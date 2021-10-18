<?php
namespace App\Repositories\Admin\SearchBooking;

use DB;
use Carbon\Carbon;
use App\Models\Agent;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Reseller;
use App\Traits\RepoResponse;
use App\Models\AccountSource;
use App\Models\BookingDetails;
use App\Models\ProductVariant;
use App\Models\CustomerAddress;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;

class SearchBookingAbstract implements SearchBookingInterface
{
    use RepoResponse;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function getPaginatedList($request, $id = null, $type = null)
    {
        $agent_id            = Auth::user()->F_AGENT_NO;
        $query = $this->booking
                ->where('BOOKING_STATUS',10);
                if(($id != null) && ($type =! null)){
                    if($type == 'customer'){
                        $query->where('F_CUSTOMER_NO', $id);
                    }elseif($type == 'reseller'){
                        $query->where('F_RESELLER_NO', $id);
                    }
                }
                if ($agent_id > 0) {
                    $query->where('F_BOOKING_SALES_AGENT_NO',$agent_id);
                }
        $data = $query->orderBy('PK_NO','DESC')->get();
        return $this->formatResponse(true, '', 'admin.booking.list', $data);
    }

    public function getCusInfo($table,$customer)
    {
        $customer_info['info'] = DB::table($table)
                        ->where('NAME', $customer)
                        // ->where('IS_ACTIVE',1)
                        ->first();
        $customer_info['address_pk'] = $customer_info['info']->PK_NO ?? 0;
        $customer_info['billing_address']   = $customer_info['info'];
        $customer_info['delivery_address']  = '';
        if ($table == 'SLS_CUSTOMERS') {
            $address = CustomerAddress::select('SLS_CUSTOMERS_ADDRESS.*','c.NAME as COUNTRY','s.STATE_NAME as STATE','city.CITY_NAME as CITY')
                    ->leftjoin('SS_COUNTRY as c','c.PK_NO','SLS_CUSTOMERS_ADDRESS.F_COUNTRY_NO')
                    ->leftjoin('SS_STATE as s','s.PK_NO','SLS_CUSTOMERS_ADDRESS.STATE')
                    ->leftjoin('SS_CITY as city','city.PK_NO','SLS_CUSTOMERS_ADDRESS.CITY')
                    ->where('F_ADDRESS_TYPE_NO',1)->where('F_CUSTOMER_NO',$customer_info['info']->PK_NO)->first();
            $customer_info['info']->POST_CODE = $address->POST_CODE ?? '';
            $customer_info['address_pk'] = $address->PK_NO ?? 0;
            $customer_info['billing_address']   = CustomerAddress::select('SLS_CUSTOMERS_ADDRESS.*','c.NAME as COUNTRY','s.STATE_NAME as STATE','city.CITY_NAME as CITY')
                        ->leftjoin('SS_COUNTRY as c','c.PK_NO','SLS_CUSTOMERS_ADDRESS.F_COUNTRY_NO')
                        ->leftjoin('SS_STATE as s','s.PK_NO','SLS_CUSTOMERS_ADDRESS.STATE')
                        ->leftjoin('SS_CITY as city','city.PK_NO','SLS_CUSTOMERS_ADDRESS.CITY')
                        ->where('F_ADDRESS_TYPE_NO',2)->where('F_CUSTOMER_NO',$customer_info['info']->PK_NO)->first();
            $customer_info['delivery_address']  = $address;
        }
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($customer_info);
        // echo '<br>======================<br>';
        // exit();
        return $customer_info;
    }

    public function getProductINV($ig_code, $f_booking_no = null,$price_type = null)
    {
        $prodct_image = Stock::selectRaw('(SELECT PRIMARY_IMG_RELATIVE_PATH from PRD_VARIANT_SETUP where MRK_ID_COMPOSITE_CODE = '. '"' .$ig_code. '"' .')')->limit(1)->getQuery();
        if ($f_booking_no != null) {
            $count_my_booked = 0;

            $count_my_warehouse = Stock::selectRaw('(SELECT IFNULL(COUNT(PK_NO),0) from INV_STOCK where F_INV_WAREHOUSE_NO != 1 and IG_CODE = '. '"' .$ig_code. '"' .' and (BOOKING_STATUS IS NULL OR BOOKING_STATUS = 0 OR BOOKING_STATUS = 90 OR F_BOOKING_NO = '.$f_booking_no.'))')->limit(1)->getQuery();

            $info = DB::table('INV_STOCK as v')->select('v.BOX_TYPE','v.F_BOX_NO','v.PRD_VARINAT_NAME','v.IG_CODE','v.INV_WAREHOUSE_NAME','v.F_INV_WAREHOUSE_NO','v.SKUID','v.SHIPMENT_TYPE','v.PREFERRED_SHIPPING_METHOD','v.CUSTOMER_PREFFERED_SHIPPING_METHOD','s.SCH_ARRIVAL_DATE','s.SHIPMENT_STATUS','v.FINAL_PREFFERED_SHIPPING_METHOD','v.F_SHIPPMENT_NO','p.REGULAR_PRICE','p.INSTALLMENT_PRICE','p.SEA_FREIGHT_CHARGE as SEA_FREIGHT_COST','p.AIR_FREIGHT_CHARGE as AIR_FREIGHT_COST','p.LOCAL_POSTAGE as SM_COST','p.INTER_DISTRICT_POSTAGE as SS_COST'
            ,DB::raw('count(v.PK_NO) as total')
            )
            ->selectSub($count_my_warehouse, 'count_my_warehouse')
            ->selectSub($prodct_image, 'PRIMARY_IMG_RELATIVE_PATH')
            ->leftJoin('SC_SHIPMENT as s','s.PK_NO','v.F_SHIPPMENT_NO')
            ->leftJoin('PRD_VARIANT_SETUP as p','p.MRK_ID_COMPOSITE_CODE','v.IG_CODE')
            ->where('v.IG_CODE', $ig_code)
            // ->where('v.F_INV_WAREHOUSE_NO', 1)
            ->whereRaw('( v.BOOKING_STATUS IS NULL OR v.BOOKING_STATUS = 0 OR v.BOOKING_STATUS = 90 OR v.F_BOOKING_NO = '.$f_booking_no.' ) ')
            ->groupBy('v.IG_CODE','v.F_INV_WAREHOUSE_NO','v.BOX_TYPE','v.SHIPMENT_TYPE','v.F_SHIPPMENT_NO')
            ->get();
            ### COUNT BOOKED QTY FOR EACH GROUP BY ###
            if ($info && count($info) > 0 ) {
                foreach ($info as $key => $value) {
                    $inv_info = DB::table('INV_STOCK as v')->select(DB::raw('(SELECT IFNULL(COUNT(v.PK_NO),0)) as book_qty')
                    , DB::raw('(SELECT IFNULL(COUNT(PK_NO),0) from INV_STOCK where F_INV_WAREHOUSE_NO != 1 and F_BOOKING_NO='.$f_booking_no.' and BOOKING_STATUS=10 and IG_CODE='. '"' .$value->IG_CODE. '"' .') as count_my_booked'))
                    // ->leftJoin('SC_SHIPMENT as s','s.PK_NO','v.F_SHIPPMENT_NO')
                    ->where(['v.F_BOOKING_NO' => $f_booking_no, 'v.BOOKING_STATUS' => 10,'v.IG_CODE' => $value->IG_CODE,'BOX_TYPE'=>$value->BOX_TYPE,'SHIPMENT_TYPE'=>$value->SHIPMENT_TYPE,'F_SHIPPMENT_NO'=>$value->F_SHIPPMENT_NO,'F_INV_WAREHOUSE_NO'=>$value->F_INV_WAREHOUSE_NO])
                    ->groupBy('v.IG_CODE','v.F_INV_WAREHOUSE_NO','v.SHIPMENT_TYPE','v.BOX_TYPE','v.F_SHIPPMENT_NO')
                    ->first();
                    $value->book_qty = $inv_info->book_qty ?? 0 ;
                    // $value->PK_NO = $inv_info[0]->PK_NO ?? 0 ;
                    // $value->count_my_booked = $inv_info->count_my_booked ?? 0 ;
                    $count_my_booked = $inv_info->count_my_booked ?? 0 ;
                    // if ($inv_info[0]->F_INV_WAREHOUSE_NO == 2) {
                    //     $count_my_booked++;
                    // }
                }
            }
            $my_warehouse_name = Stock::select('INV_WAREHOUSE_NAME')->where('F_INV_WAREHOUSE_NO',2)->first();
            $data['count_my_booked'] = $count_my_booked;
            $data['price_type'] = $price_type;
            // $data['count_my_warehouse'] = $count_my_warehouse;
            $data['info'] = $info;
            // echo '<pre>';
            // echo '======================<br>';
            // print_r($data['info']);
            // echo '<br>======================<br>';
        }else{
            $my_warehouse_name = Stock::select('INV_WAREHOUSE_NAME')->where('F_INV_WAREHOUSE_NO',2)->first();

            $count_my_warehouse = Stock::selectRaw('(SELECT IFNULL(COUNT(PK_NO),0) from INV_STOCK where F_INV_WAREHOUSE_NO != 1 and IG_CODE = '. '"' .$ig_code. '"' .' and (BOOKING_STATUS IS NULL OR BOOKING_STATUS = 0 OR BOOKING_STATUS = 90))')->limit(1)->getQuery();

            $data['info'] = DB::table('INV_STOCK as v')->select('v.BOX_TYPE','v.PRD_VARINAT_NAME','v.IG_CODE','v.INV_WAREHOUSE_NAME','v.F_INV_WAREHOUSE_NO','v.SKUID','v.PREFERRED_SHIPPING_METHOD','v.SHIPMENT_TYPE','v.F_SHIPPMENT_NO','v.CUSTOMER_PREFFERED_SHIPPING_METHOD','s.SCH_ARRIVAL_DATE','s.SHIPMENT_STATUS','v.F_BOX_NO','v.FINAL_PREFFERED_SHIPPING_METHOD','p.REGULAR_PRICE','p.INSTALLMENT_PRICE','p.SEA_FREIGHT_CHARGE as SEA_FREIGHT_COST','p.AIR_FREIGHT_CHARGE as AIR_FREIGHT_COST','p.LOCAL_POSTAGE as SM_COST','p.INTER_DISTRICT_POSTAGE as SS_COST'
            ,DB::raw('count(v.PK_NO) as total')
            )
            ->selectSub($count_my_warehouse, 'count_my_warehouse')
            ->selectSub($prodct_image, 'PRIMARY_IMG_RELATIVE_PATH')
            ->leftJoin('SC_SHIPMENT as s','s.PK_NO','v.F_SHIPPMENT_NO')
            ->leftJoin('PRD_VARIANT_SETUP as p','p.MRK_ID_COMPOSITE_CODE','v.IG_CODE')
            ->where('v.IG_CODE', $ig_code)
            // ->where('v.F_INV_WAREHOUSE_NO', 1)
            ->whereRaw('( BOOKING_STATUS IS NULL OR BOOKING_STATUS = 0 OR BOOKING_STATUS = 90 ) ')
            ->groupBy('v.IG_CODE','v.F_INV_WAREHOUSE_NO','v.BOX_TYPE','v.SHIPMENT_TYPE','v.F_SHIPPMENT_NO')
            ->get();
            $data['count_my_booked'] =  0 ;
        }

        $data['my_warehouse_name']  = $my_warehouse_name->INV_WAREHOUSE_NAME ?? null ;

        // $prd_variant_id = Stock::select('F_PRD_VARIANT_NO')->where('IG_CODE', $ig_code)->first();

        if (!empty($data['info'])) {
            // $data['img']  =ProductVariant::select('PRIMARY_IMG_RELATIVE_PATH')->where('PK_NO', $prd_variant_id->F_PRD_VARIANT_NO)->first();
            // // $data['info'] = $data['info']->toArray();
            // $data['img']  = $data['img']->toArray();

            // echo '<pre>';
            // echo '======================<br>';
            // print_r($data['info']);
            // echo '<br>======================<br>';
            // exit();
            return $this->generateInputField($data, $f_booking_no);
        }

        return $data;
    }

    private function generateInputField($item, $book_qty = 0){
        return view('admin.booking._variant_tr')->withItem($item)->withBookqty($book_qty)->render();
    }

    public function get_available_qty($ig_code, $qty, $house, $ship, $box, $id, $ship_no)
    {
        $info = Stock::where('IG_CODE', $ig_code)
                    ->where('F_INV_WAREHOUSE_NO',$house);
                    if ($house == 1) {
                        if ($ship != "0") {
                            $info = $info->where('SHIPMENT_TYPE',$ship);
                        }else{
                            $info = $info->whereNull('SHIPMENT_TYPE');
                        }
                        if ($box != "0") {
                            $info = $info->where('BOX_TYPE',$box);
                        }else{
                            $info = $info->whereNull('BOX_TYPE');
                        }
                        if ($ship_no != "0") {
                            $info = $info->where('F_SHIPPMENT_NO',$ship_no);
                        }else{
                            $info = $info->whereNull('F_SHIPPMENT_NO');
                        }
                    }
                    $info = $info->whereRaw('( BOOKING_STATUS IS NULL OR BOOKING_STATUS = 0 OR BOOKING_STATUS = 90 ) ')
                    ->count();

        if ($info < $qty) {
            return 'exeeded';
        }else if($qty == 0){
            return 'zero';
        }else{
            return 'allow';
        }
    }

    public function get_available_qty_update($ig_code, $qty, $house, $ship, $box, $id, $ship_no)
    {
        $info = Stock::where('IG_CODE', $ig_code)
                    ->where('F_INV_WAREHOUSE_NO',$house);
                    if ($house == 1) {
                        if ($ship != "0") {
                            $info = $info->where('SHIPMENT_TYPE',$ship);
                        }else{
                            $info = $info->whereNull('SHIPMENT_TYPE');
                        }
                        if ($box != "0") {
                            $info = $info->where('BOX_TYPE',$box);
                        }else{
                            $info = $info->whereNull('BOX_TYPE');
                        }
                        if ($ship_no != "0") {
                            $info = $info->where('F_SHIPPMENT_NO',$ship_no);
                        }else{
                            $info = $info->whereNull('F_SHIPPMENT_NO');
                        }
                    }
                    $info = $info->whereRaw('( BOOKING_STATUS IS NULL OR BOOKING_STATUS = 0 OR BOOKING_STATUS = 90 OR F_BOOKING_NO = '.$id.' ) ')->count();
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($ig_code);
        // echo '<br>======================<br>';
        // print_r($house);
        // echo '<br>======================<br>';
        // print_r($qty);
        // echo '<br>======================<br>';
        // print_r($ship);
        // echo '<br>======================<br>';
        // print_r($box);
        // echo '<br>======================<br>';
        // print_r($info);
        // echo '<br>======================<br>';
        if ($info < $qty) {
            return 'exeeded';
        }else if($qty == 0){
            return 'zero';
        }else{
            return 'allow';
        }
    }

    public function postStore($request)
    {
        // $j = 0;
        // $string = '';
        // foreach ($request->all() as $key => $value) {
        //     if ($j > 4) {
        //         for ($loop=0; $loop < count($request->products); $loop++) {
        //             if(strpos($key, $request->products[$loop]) !== false){
        //                 $cut = explode("book-".$request->products[$loop]."-house-", $key);
        //                 $status = $this->get_available_qty($request->products[$loop], $value,$cut[1]);
        //                 if ($status == 'allow') {
        //                   $sku_id = Stock::select('SKUID')->where('IG_CODE', $request->products[$loop])->first();

        //                 $string .= $sku_id->SKUID.','.$cut[1].','.$value.';';
        //                 }else{
        //                     return $this->formatResponse(false, 'Booking quantity exeeded !', 'admin.booking.create');
        //                 }
        //             }
        //         }
        //     }
        //     $j++;
        // }
        // if ($string != null) {
        //     $agent_name = Agent::select('NAME')->where('PK_NO', $request->agent)->first();
        //     if ($request->booking_radio == 'customer') {
        //         $customer_no = Customer::select('PK_NO')->where('NAME', $request->q)->first();
        //         $is_reseller = 0;
        //     }elseif($request->booking_radio == 'reseller'){
        //         $customer_no = Reseller::select('PK_NO')->where('NAME', $request->q)->first();
        //         $is_reseller = 1;
        //     }
        //     $count = substr_count($string,';');
        //     $row_separator    = ';';
        //     $column_separator = ',';

        //             DB::beginTransaction();

        //             try {

        //                 $booking = new Booking();
        //                 $booking->BOOKING_TIME              = date('Y-m-d h:i:s');
        //                 $booking->EXPIERY_DATE_TIME         = Carbon::now()->addHours($request->booking_validity ?? 24 );
        //                 $booking->F_BOOKING_SALES_AGENT_NO  = $request->agent;
        //                 $booking->BOOKING_SALES_AGENT_NAME  = $agent_name->NAME;
        //                 $booking->BOOKING_STATUS            = 10;
        //                 $booking->BOOKING_NOTES             = $request->booking_note;
        //                 $booking->F_CUSTOMER_NO             = $request->booking_radio == 'customer' ? $customer_no->PK_NO : null;
        //                 $booking->CUSTOMER_NAME             = $request->booking_radio == 'customer' ? $request->q : null;
        //                 $booking->IS_RESELLER               = $is_reseller;
        //                 $booking->F_RESELLER_NO             = $request->booking_radio == 'reseller' ? $customer_no->PK_NO : null;
        //                 $booking->RESELLER_NAME             = $request->booking_radio == 'reseller' ? $request->q : null;
        //                 $booking->save();


        //                 DB::select('CALL PROC_SLS_BOOKING(?,?,?,?,?,?)', [ $booking->PK_NO, $string, $count, 3, $column_separator, $row_separator]);

        //             } catch (\Exception $e) {
        //                 // dd($e);
        //                 DB::rollback();
        //                 return $this->formatResponse(false, 'Booking not Successfully !', 'admin.booking.list');
        //             }

        //             DB::commit();


        //     return $this->formatResponse(true, 'Booking Done Successfully !', 'admin.booking.list');


        // }
        // return $this->formatResponse(false, 'Something went wrong, please try again !', 'admin.booking.list');
    }

    public function findOrThrowException($PK_NO, $checkoffer=null)
    {
        $data       = array();
        $booking    = $this->booking->find($PK_NO);
        $start      = Carbon::parse($booking->BOOKING_TIME);
        $end        = Carbon::parse($booking->EXPIERY_DATE_TIME);
        $booking->EXPIERY_DATE_TIME_DIF = $end->diffInHours($start) - 12;

        $booking_details = BookingDetails::select('INV_STOCK.*','SLS_BOOKING_DETAILS.IS_REGULAR', 'SLS_BOOKING_DETAILS.IS_FREIGHT','SLS_BOOKING_DETAILS.IS_SM','SLS_BOOKING_DETAILS.IS_REGULAR'
        ,DB::raw('ifnull(count(INV_STOCK.PK_NO),0) as total_book_qty') )
        ->leftJoin('INV_STOCK','INV_STOCK.PK_NO','SLS_BOOKING_DETAILS.F_INV_STOCK_NO')
        ->where('SLS_BOOKING_DETAILS.F_BOOKING_NO', $booking->PK_NO)
        ->groupBy('INV_STOCK.IG_CODE')->get();

        // echo '<pre>';
        // echo '=======sdfsd===============<br>';
        // print_r($booking_details);
        // echo '<br>======================<br>';
        // exit();

        if ($booking_details && count($booking_details) > 0 ) {
            foreach ($booking_details as $key => $value) {
                $value->book_info = $this->getProductINV($value->IG_CODE, $value->F_BOOKING_NO,$value->IS_REGULAR);
            }
        }
        $data['booking']            = $booking;
        $data['booking_details']    = $booking_details ?? null ;

        if($checkoffer == '1'){
           DB::statement('CALL PROC_SLS_CHECK_OFFER(:booking_no );',array($PK_NO));
        $bundle = array();
        $bundle = DB::table('SLS_CHECK_OFFER')
        ->select('SLS_CHECK_OFFER.F_BOOKING_NO','SLS_CHECK_OFFER.F_BUNDLE_NO',DB::RAW('sum(SLS_CHECK_OFFER.REGULAR_BUNDLE_PRICE) as TOTAL_REGULAR_BUNDLE_PRICE'), DB::RAW('sum(SLS_CHECK_OFFER.INSTALLMENT_BUNDLE_PRICE) as TOTAL_INSTALLMENT_BUNDLE_PRICE'),DB::RAW('COUNT(*) AS MATCHED_QTY'),DB::RAW('MAX(SLS_CHECK_OFFER.SEQUENC) AS BUNDLE_QTY'),'SLS_BUNDLE.BUNDLE_NAME_PUBLIC','SLS_BUNDLE.BUNDLE_NAME','SLS_BUNDLE.P_SS','SLS_BUNDLE.P_SM','SLS_BUNDLE.P_AIR','SLS_BUNDLE.P_SEA','SLS_BUNDLE.R_SS','SLS_BUNDLE.R_SM','SLS_BUNDLE.R_AIR','SLS_BUNDLE.R_SEA')
        ->leftJoin('SLS_BUNDLE','SLS_BUNDLE.PK_NO','=','SLS_CHECK_OFFER.F_BUNDLE_NO')
        ->where('SLS_CHECK_OFFER.F_BOOKING_NO',$PK_NO)
        ->where('SLS_CHECK_OFFER.IS_PROCESSED',1)
        ->groupBy('SLS_CHECK_OFFER.F_BUNDLE_NO')
        ->get();
        // dd($bundle);
        if($bundle){
            foreach ($bundle as $key => $value) {
                // $query = DB::SELECT("SELECT SUM(REGULAR_PRICE) AS TOTAL_REGULAR_PRICE,SUM(INSTALLMENT_PRICE) AS TOTAL_INSTALLMENT_PRICE FROM SLS_CHECK_OFFER WHERE F_BOOKING_NO = $PK_NO AND IS_PROCESSED = 0 AND F_BUNDLE_NO = $value->F_BUNDLE_NO ");
                $query = DB::SELECT("SELECT SUM(SLS_BOOKING_DETAILS.CURRENT_REGULAR_PRICE) AS TOTAL_REGULAR_PRICE ,
                SUM(SLS_BOOKING_DETAILS.CURRENT_INSTALLMENT_PRICE) AS TOTAL_INSTALLMENT_PRICE FROM SLS_BOOKING_DETAILS LEFT JOIN SLS_CHECK_OFFER ON SLS_CHECK_OFFER.F_INV_STOCK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO WHERE SLS_BOOKING_DETAILS.F_BOOKING_NO = $PK_NO AND ( SLS_CHECK_OFFER.IS_PROCESSED IS NULL OR SLS_CHECK_OFFER.IS_PROCESSED = 0 AND SLS_CHECK_OFFER.F_BUNDLE_NO = $value->F_BUNDLE_NO ) ");

                if(!empty($query)){

                    $value->NON_BUNDLE_REGULAR_PRICE        = $query[0]->TOTAL_REGULAR_PRICE;
                    $value->NON_BUNDLE_INSTALLMENT_PRICE    = $query[0]->TOTAL_INSTALLMENT_PRICE;
                }

            }
        }

        $data['bundle'] = $bundle;
    }

// dd($data['bundle']);
$data['non_bundle'] = DB::SELECT(" SELECT A.F_BOOKING_NO, A.F_INV_STOCK_NO, A.CURRENT_REGULAR_PRICE, A.CURRENT_INSTALLMENT_PRICE, A.AIR_FREIGHT, A.SEA_FREIGHT, A.IS_FREIGHT, A.SS_COST, A.SM_COST, A.IS_SM, A.IS_REGULAR, C.PRD_VARINAT_NAME, COUNT(C.F_PRD_VARIANT_NO ) AS ITEM_QTY, C.PRC_IN_IMAGE_PATH
FROM SLS_BOOKING_DETAILS AS A
LEFT JOIN SLS_CHECK_OFFER AS B ON B.F_INV_STOCK_NO = A.F_INV_STOCK_NO
LEFT JOIN INV_STOCK AS C ON C.PK_NO = A.F_INV_STOCK_NO
WHERE A.F_BOOKING_NO = $PK_NO
AND ( B.IS_PROCESSED IS NULL OR B.IS_PROCESSED = 0  ) GROUP BY C.F_PRD_VARIANT_NO ");


        return $this->formatResponse(true, 'Data found successfully !', 'admin.booking.list', $data);
    }

    public function postUpdate($request, $PK_NO, $type = null)
    {
        if ($PK_NO != 0) {
            $book = Booking::where('PK_NO',$PK_NO)->first();
        }
        $j = 0;
        $string = '';
        $house = '';
        $ship = '';
        $ship_1 = '';
        $box_1 = '';
        $box = '';
        $response_status    = false;
        $response_msg       = 'Booking unsuccessfull !';
        $response_route      = 'admin.booking.list';
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($request->all());
        // echo '<br>======================<br>';
        // exit();

        foreach ($request->all() as $key => $value) {
            ### LOOP ONLY IN BOOKING DATA ###
            if ($j > 8) {
                ### LOOP IN PRODUCTS ARRAY TO CHECK IF ANY PRODUCT MATCHES BOOKING DATA ###
                    for ($loop=0; $loop < count($request->products); $loop++) {
                            ###IF ANY PRODUCT MATCHES DATA ###
                            // $price_type = 1;
                            if(strpos($key, $request->products[$loop]) !== false ){
                                // $preferred_method = 'SEA';
                                if (strpos($key, 'product_freight_type-') !== false) {
                                    $preferred_method = $value;
                                    // echo '<pre>';
                                    // echo '====================================================<br>';
                                    // print_r($preferred_method);
                                    // echo '<br>=========fgdfgdfgdf=============<br>';
                                    // exit();
                                }
                                // else if (strpos($key, 'price_type_') !== false) {
                                //     $price_type = $value;
                                // }
                                else{

                                    $house = explode("book-".$request->products[$loop]."-house-", $key);
                                    $house = substr($house[1], 0, 1);

                                    $ship = explode("book-".$request->products[$loop]."-house-".$house[0]."-ship-", $key);
                                    $ship_1 = substr($ship[1], 0, 1);
                                    if ($ship_1 != "0") {
                                        $ship = substr($ship[1], 0, 3);
                                    }else if($ship_1 == "0"){
                                        $ship = 0;
                                    }

                                    $box = explode("book-".$request->products[$loop]."-house-".$house[0]."-ship-".$ship."-box-", $key);
                                    $box_1 = substr($box[1], 0, 1);
                                    if ($box_1 != "0") {
                                        $box = substr($box[1], 0,3);
                                    }else if($box_1 == "0"){
                                        $box = 0;
                                    }

                                    $ship_no = explode("book-".$request->products[$loop]."-house-".$house[0]."-ship-".$ship."-box-".$box."-ship_no-", $key);
                                    $ship_no_1 = substr($ship_no[1], 0);
                                    if ($ship_no_1 != "0") {
                                        $ship_no = substr($ship_no[1], 0);
                                    }else if($ship_no_1 == "0"){
                                        $ship_no = 0;
                                    }

                                    if (isset($house[0])) {
                                        if ($PK_NO != 0) {
                                            $status = $this->get_available_qty_update($request->products[$loop], $value,$house,$ship,$box,$PK_NO,$ship_no);
                                        }else{
                                            $status = $this->get_available_qty($request->products[$loop], $value,$house,$ship,$box,$PK_NO,$ship_no);
                                        }
                                        if ($status == 'allow') {
                                            if (isset($preferred_method) && $preferred_method != '0') {
                                                $preferred_method = $preferred_method;
                                            }
                                            // else if(isset($price) && $price != 0){
                                            //     $price = $price;
                                            // }
                                            // else if(isset($price_type) && $price_type != 0){
                                            //     $price_type = 1;
                                            // }
                                            else{
                                                $preferred_method = 'SEA';
                                                // $price_type = 0;
                                            }
                                            $sku_id = Stock::select('SKUID')->where('IG_CODE', $request->products[$loop])->first();
                                            // $string .= $sku_id->SKUID.','.$house.','.$value.','.$ship.','.$box.','.$preferred_method.','.$price_type.','.$price.';';
                                            $string .= $sku_id->SKUID.','.$house.','.$value.','.$ship.','.$box.','.$preferred_method.','.$request->price_type_all.','.$request->customer_address.','.$ship_no.';';
                                            // $preferred_method = '0';
                                            // echo "<br>";
                                            // print_r($string);
                                            // print_r($status);
                                            // $string = '';
                                        }else if($status == 'zero'){
                                            echo '';
                                        }else{
                                            echo "<br>";
                                            print_r($status);
                                            exit();
                                            return $this->formatResponse(false, 'Booking quantity exeeded !', 'admin.booking.list');
                                        }
                                    }
                                }

                            }
                    }
                    // if (isset($house[0])) {

                    //    echo '<pre>';
                    //    echo '<br>==========STRING============<br>';
                    //    print_r($string);
                    //    echo '==========HOUSE============<br>';
                    //    print_r($house);
                    //    echo '<br>======SHIP================<br>';
                    //    print_r($ship);
                    //    echo '<br>======BOX================<br>';
                    //    print_r($box);
                    //    $string = '';
                    //    echo '<br>======END================<br>';
                    // }
            }
           $j++;
    }
    // echo '<pre>';
    // echo '======================<br>';
    // print_r($string);
    // echo '<br>======================<br>';
    // exit();
         if ($string != 0) {
            $agent_name = Agent::select('NAME')->where('PK_NO', $request->agent)->first();
            if ($request->booking_radio == 'customer') {
                // $customer_no = Customer::select('PK_NO')->where('NAME', $request->q)->first();
                $customer_no = Customer::select('NAME')->where('PK_NO', $request->customer_id)->first();
                $is_reseller = 0;
            }elseif($request->booking_radio == 'reseller'){
                // $customer_no = Reseller::select('PK_NO')->where('NAME', $request->q)->first();
                $customer_no = Reseller::select('NAME')->where('PK_NO', $request->customer_id)->first();
                $is_reseller = 1;
            }
            $count = substr_count($string,';');
            $row_separator    = ';';
            $column_separator = ',';
            $col_parameters = 5;

            // echo '<pre>';
            // echo '======================<br>';
            // print_r($string);
            // echo '<br>======================<br>';
            // exit();
                    DB::beginTransaction();
                    try {

                        if ($PK_NO != 0) {
                            ###Delete old booking ###
                            $stock = Stock::select('PK_NO','PREFERRED_SHIPPING_METHOD','F_BOX_NO')->where('F_BOOKING_NO',$book->PK_NO)->get();
                            $book_details = BookingDetails::where('F_BOOKING_NO',$book->PK_NO)->get();

                            if ($book_details && count($book_details) > 0 ) {
                                foreach ($stock as $key => $value) {
                                    Stock::where('PK_NO',$value->PK_NO)->update(['BOOKING_STATUS' => null, 'F_BOOKING_NO' => null,'CUSTOMER_PREFFERED_SHIPPING_METHOD' => null]);
                                    if ($value->F_BOX_NO == null || $value->F_BOX_NO == 0) {
                                        Stock::where('PK_NO',$value->PK_NO)->update(['FINAL_PREFFERED_SHIPPING_METHOD' => $value->PREFERRED_SHIPPING_METHOD]);
                                    }
                                }
                            }

                            BookingDetails::where('F_BOOKING_NO',$book->PK_NO)->delete();
                           // Booking::where('PK_NO',$PK_NO)->delete();
                           $booking = Booking::find($PK_NO);
                            ###End Delete old booking ###
                        }else{
                            $booking = new Booking();
                        }

                        if ($PK_NO != 0) {
                            // $booking->PK_NO                     = $PK_NO;
                            $booking->REBOOKING_TIME            = date('Y-m-d h:i:s');
                            $booking->BOOKING_TIME              = $book->BOOKING_TIME;
                            $booking->EXPIERY_DATE_TIME         = $request->booking_validity != '' ? Carbon::now()->addHours($request->booking_validity) : $book->EXPIERY_DATE_TIME;
                        }else{
                            $booking->BOOKING_TIME              = date('Y-m-d h:i:s');
                            $booking->EXPIERY_DATE_TIME         = Carbon::now()->addHours($request->booking_validity ?? 24 );
                        }
                        $booking->F_BOOKING_SALES_AGENT_NO  = $request->agent;
                        $booking->BOOKING_SALES_AGENT_NAME  = $agent_name->NAME;
                        $booking->BOOKING_STATUS            = 10;
                        $booking->BOOKING_NOTES             = $request->booking_note;
                        $booking->F_CUSTOMER_NO             = $request->booking_radio == 'customer' ? $request->customer_id : null;
                        $booking->CUSTOMER_NAME             = $request->booking_radio == 'customer' ? $customer_no->NAME : null;
                        $booking->IS_RESELLER               = $is_reseller;
                        $booking->F_RESELLER_NO             = $request->booking_radio == 'reseller' ? $request->customer_id : null;
                        $booking->RESELLER_NAME             = $request->booking_radio == 'reseller' ? $customer_no->NAME : null;
                        $booking->FREIGHT_COST              = $request->freight_cost_total;
                        $booking->POSTAGE_COST              = $request->postage_regular_cost_final;
                        $booking->TOTAL_PRICE               = $request->grand_total;

                        $booking->save();

                       $booking_id = $booking->PK_NO;

                        DB::statement('CALL PROC_SLS_BOOKING(:booking_id, :string, :count, :col_parameters, :column_separator, :row_separator, @OUT_STATUS);',
                        array(
                            $booking_id
                           ,$string
                           ,$count
                           ,$col_parameters
                           ,$column_separator
                           ,$row_separator
                        )
                        );

                        $prc = DB::select('select @OUT_STATUS as OUT_STATUS');

                        if ($prc[0]->OUT_STATUS == 'success') {

                            $response_status    = true;
                            $response_msg       = 'Booking successfull !';
                            $response_route     = 'admin.booking.list';

                            $booking_details = BookingDetails::select('SLS_BOOKING_DETAILS.*')->where('F_BOOKING_NO',$booking->PK_NO)->get();

                            foreach ($booking_details as $key => $value) {
                                BookingDetails::where('F_INV_STOCK_NO',$value->F_INV_STOCK_NO)->update(['IS_SM'=>$request->postage,'CURRENT_IS_SM'=>$request->postage]);
                            }

                            if ($type == 'order') {
                                $booking            = Booking::find($booking->PK_NO);
                                $booking_details    = BookingDetails::select('F_INV_STOCK_NO')->where('F_BOOKING_NO',$booking->PK_NO)->get();
                                // $from_address           = ShippingAddress::where('PK_NO',8)->first();
                                $order = new Order();
                                $order->F_CUSTOMER_NO       = $booking->F_CUSTOMER_NO;
                                $order->CUSTOMER_NAME       = $booking->CUSTOMER_NAME;
                                $order->IS_RESELLER         = $booking->IS_RESELLER;
                                $order->F_RESELLER_NO       = $booking->F_RESELLER_NO;
                                $order->RESELLER_NAME       = $booking->RESELLER_NAME;
                                $order->F_BOOKING_NO        = $booking->PK_NO;

                                if ($request->booking_radio == 'customer') {
                                    $to_address     = CustomerAddress::where('F_CUSTOMER_NO',$request->customer_id)->where('F_ADDRESS_TYPE_NO',1)->first();
                                    $from_address = \Config::get('static_array.order_from');

                                    $order->FROM_NAME               = $from_address['FROM_NAME'];
                                    $order->FROM_MOBILE             = $from_address['FROM_MOBILE'];
                                    $order->FROM_ADDRESS_LINE_1     = $from_address['FROM_ADDRESS_LINE_1'];
                                    $order->FROM_ADDRESS_LINE_2     = $from_address['FROM_ADDRESS_LINE_2'];
                                    $order->FROM_ADDRESS_LINE_3     = $from_address['FROM_ADDRESS_LINE_3'];
                                    $order->FROM_ADDRESS_LINE_4     = $from_address['FROM_ADDRESS_LINE_4'];
                                    $order->FROM_CITY               = $from_address['FROM_CITY'];
                                    $order->FROM_STATE              = $from_address['FROM_STATE'];
                                    $order->FROM_POSTCODE           = $from_address['FROM_POSTCODE'];
                                    $order->FROM_COUNTRY            = $from_address['FROM_COUNTRY'];
                                    $order->FROM_F_COUNTRY_NO       = $from_address['FROM_F_COUNTRY_NO'];

                                    $order->PREV_DELIVERY_NAME           = $to_address->NAME;
                                    $order->PREV_DELIVERY_MOBILE         = $to_address->TEL_NO;
                                    $order->PREV_DELIVERY_ADDRESS_LINE_1 = $to_address->ADDRESS_LINE_1;
                                    $order->PREV_DELIVERY_ADDRESS_LINE_2 = $to_address->ADDRESS_LINE_2;
                                    $order->PREV_DELIVERY_ADDRESS_LINE_3 = $to_address->ADDRESS_LINE_3;
                                    $order->PREV_DELIVERY_ADDRESS_LINE_4 = $to_address->ADDRESS_LINE_4;
                                    $order->PREV_DELIVERY_CITY           = $to_address->city->CITY_NAME ?? '';
                                    $order->PREV_DELIVERY_STATE          = $to_address->state->STATE_NAME ?? '';
                                    $order->PREV_DELIVERY_POSTCODE       = $to_address->POST_CODE;
                                    $order->PREV_DELIVERY_COUNTRY        = $to_address->country->NAME ?? '';
                                    $order->PREV_DELIVERY_F_COUNTRY_NO   = $to_address->F_COUNTRY_NO;

                                    $order->DELIVERY_NAME           = $to_address->NAME;
                                    $order->DELIVERY_MOBILE         = $to_address->TEL_NO;
                                    $order->DELIVERY_ADDRESS_LINE_1 = $to_address->ADDRESS_LINE_1;
                                    $order->DELIVERY_ADDRESS_LINE_2 = $to_address->ADDRESS_LINE_2;
                                    $order->DELIVERY_ADDRESS_LINE_3 = $to_address->ADDRESS_LINE_3;
                                    $order->DELIVERY_ADDRESS_LINE_4 = $to_address->ADDRESS_LINE_4;
                                    $order->DELIVERY_CITY           = $to_address->city->CITY_NAME ?? '';
                                    $order->DELIVERY_STATE          = $to_address->state->STATE_NAME ?? '';
                                    $order->DELIVERY_POSTCODE       = $to_address->POST_CODE;
                                    $order->DELIVERY_COUNTRY        = $to_address->country->NAME ?? '';
                                    $order->DELIVERY_F_COUNTRY_NO   = $to_address->F_COUNTRY_NO;

                                }else{
                                    $from_address                   = Reseller::where('PK_NO',$request->customer_id)->first();
                                    // echo '<pre>';
                                    // echo '======================<br>';
                                    // print_r($from_address);
                                    // echo '<br>======================<br>';
                                    // exit();
                                    $order->FROM_NAME               = $from_address->NAME;
                                    $order->FROM_MOBILE             = $from_address->MOBILE_NO;
                                    $order->FROM_ADDRESS_LINE_1     = $from_address->ADDRESS_LINE_1;
                                    $order->FROM_ADDRESS_LINE_2     = $from_address->ADDRESS_LINE_2;
                                    $order->FROM_ADDRESS_LINE_3     = $from_address->ADDRESS_LINE_3;
                                    $order->FROM_ADDRESS_LINE_4     = $from_address->ADDRESS_LINE_4;
                                    $order->FROM_CITY               = $from_address->city->CITY_NAME ?? '';
                                    $order->FROM_STATE              = $from_address->state->STATE_NAME ?? '';
                                    $order->FROM_POSTCODE           = $from_address->POST_CODE;
                                    $order->FROM_COUNTRY            = $from_address->country->NAME ?? '';
                                    $order->FROM_F_COUNTRY_NO       = $from_address->F_COUNTRY_NO;
                                }

                                $order->save();
                                // echo '<pre>';
                                // echo '======================<br>';
                                // print_r($order);
                                // echo '<br>======================<br>';
                                // exit();
                                $booking->RECONFIRM_TIME = date('Y-m-d h:i:s');
                                if (isset($request->order_date_) && $request->order_date_ != 0) {
                                    $booking->RECONFIRM_TIME        = date('Y-m-d',strtotime($request->order_date_));
                                }
                                $booking->BOOKING_STATUS = 80;
                                $booking->save();

                                Stock::whereIn('PK_NO',$booking_details)
                                ->update(['F_ORDER_NO' => $order->PK_NO,'ORDER_STATUS' => 10,'ORDER_PRICE' => $booking->TOTAL_PRICE]);
                            }
                        }else{
                            $response_status    = false;
                            $response_msg       = 'Booking unsuccessfull !';
                            $response_route     = 'admin.booking.list';
                        }

                    } catch (\Exception $e) {
                        dd( $e);
                        DB::rollback();
                        return $this->formatResponse(false, $e->getMessage(), 'admin.booking.list');
                    }
                    DB::commit();
                    if ($type == 'order') {
                        return $this->formatResponse(true, 'Successfully booked and ordered !', 'admin.booking_to_order.book-order',$booking_id);
                    }

           return $this->formatResponse($response_status, $response_msg, $response_route);
        }
        echo '<pre>';
        echo '======================<br>';
        print_r(':(');
        echo '<br>======================<br>';
        exit();
       return $this->formatResponse(false, 'Something went wrong, please try again !', 'admin.booking.list');
    }

    public function delete($PK_NO)
    {
      $book = Booking::where('PK_NO',$PK_NO)->first();
      if($book->BOOKING_STATUS >= 50 ){
        return $this->formatResponse(false, 'Can Not Delete Booking At This Moment!', 'admin.booking.list');
      }
        DB::beginTransaction();
        try {
            $stock = Stock::select('PK_NO','PREFERRED_SHIPPING_METHOD','F_BOX_NO')->where('F_BOOKING_NO',$PK_NO)->get();
           // $book_details = BookingDetails::where('F_BOOKING_NO',$PK_NO)->get();

            // if ($book_details && count($book_details) > 0 ) {
                foreach ($stock as $key => $value) {
                    Stock::where('PK_NO',$value->PK_NO)->update(['BOOKING_STATUS' => null, 'F_BOOKING_NO' => null,'CUSTOMER_PREFFERED_SHIPPING_METHOD' => null]);
                    if ($value->F_BOX_NO == null || $value->F_BOX_NO == 0) {
                        Stock::where('PK_NO',$value->PK_NO)->update(['FINAL_PREFFERED_SHIPPING_METHOD' => $value->PREFERRED_SHIPPING_METHOD]);
                    }
                }
            // }
            BookingDetails::where('F_BOOKING_NO',$PK_NO)->delete();
            Booking::where('PK_NO',$PK_NO)->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.booking.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Successfully deleted booking with details !', 'admin.booking.list');
    }

    public function postOfferApply($request)
    {
        $order = Order::where('F_BOOKING_NO',$request->booking_pk_no)->first();
        if($order){
            return $this->formatResponse(false, 'This booking already transfered to order list, Offer apply not successfull !', 'admin.booking.list');
        }

        DB::beginTransaction();
            try {
                $book = Booking::where('PK_NO',$request->booking_pk_no)->first();
                $details = BookingDetails::where('F_BOOKING_NO',$request->booking_pk_no)->get();
                $bundle  = Offer::find();

                $offer = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_PROCESSED',1)->get();



                $air_cost_a = $bundle->P_AIR;
                $sea_cost_a = $bundle->P_SEA;
                $ss_cost_a = $bundle->P_SS;
                $sm_cost_a = $bundle->P_SM;

                $air_cost_b = $bundle->R_AIR;
                $sea_cost_b = $bundle->R_SEA;
                $ss_cost_b = $bundle->R_SS;
                $sm_cost_b = $bundle->R_SM;


                if($offer){
                    foreach ($offer as $key => $value) {
                        BookingDetails::where('F_BOOKING_NO',$request->booking_pk_no)->where('F_INV_STOCK_NO', $value->F_INV_STOCK_NO)->update([
                            'REGULAR_PRICE'             => $value->REGULAR_BUNDLE_PRICE ,
                            'INSTALLMENT_PRICE'         => $value->INSTALLMENT_BUNDLE_PRICE,
                            'CURRENT_REGULAR_PRICE'     => $value->REGULAR_BUNDLE_PRICE ,
                            'CURRENT_INSTALLMENT_PRICE' => $value->INSTALLMENT_BUNDLE_PRICE,
                            'F_BUNDLE_NO'               => $value->F_BUNDLE_NO,
                            'AIR_FREIGHT'               => $air_cost_a,
                            'SEA_FREIGHT'               => $sea_cost_a,
                            'SS_COST'                   => $ss_cost_a,
                            'SM_COST'                   => $sm_cost_a,
                        ]);
                    }
                    Booking::where('PK_NO',$request->booking_pk_no)->update(['IS_BUNDLE_MATCHED' => 1]);
                }

                // booking to order
                /*

                $booking            = Booking::find($booking->PK_NO);
                $booking_details    = BookingDetails::select('F_INV_STOCK_NO')->where('F_BOOKING_NO',$booking->PK_NO)->get();
                $order = new Order();
                $order->F_CUSTOMER_NO       = $booking->F_CUSTOMER_NO;
                $order->CUSTOMER_NAME       = $booking->CUSTOMER_NAME;
                $order->IS_RESELLER         = $booking->IS_RESELLER;
                $order->F_RESELLER_NO       = $booking->F_RESELLER_NO;
                $order->RESELLER_NAME       = $booking->RESELLER_NAME;
                $order->F_BOOKING_NO        = $booking->PK_NO;

                if ($request->booking_radio == 'customer') {
                    $to_address     = CustomerAddress::where('F_CUSTOMER_NO',$request->customer_id)->where('F_ADDRESS_TYPE_NO',1)->first();
                    $from_address = \Config::get('static_array.order_from');

                    $order->FROM_NAME               = $from_address['FROM_NAME'];
                    $order->FROM_MOBILE             = $from_address['FROM_MOBILE'];
                    $order->FROM_ADDRESS_LINE_1     = $from_address['FROM_ADDRESS_LINE_1'];
                    $order->FROM_ADDRESS_LINE_2     = $from_address['FROM_ADDRESS_LINE_2'];
                    $order->FROM_ADDRESS_LINE_3     = $from_address['FROM_ADDRESS_LINE_3'];
                    $order->FROM_ADDRESS_LINE_4     = $from_address['FROM_ADDRESS_LINE_4'];
                    $order->FROM_CITY               = $from_address['FROM_CITY'];
                    $order->FROM_STATE              = $from_address['FROM_STATE'];
                    $order->FROM_POSTCODE           = $from_address['FROM_POSTCODE'];
                    $order->FROM_COUNTRY            = $from_address['FROM_COUNTRY'];
                    $order->FROM_F_COUNTRY_NO       = $from_address['FROM_F_COUNTRY_NO'];

                    $order->DELIVERY_NAME           = $to_address->NAME;
                    $order->DELIVERY_MOBILE         = $to_address->TEL_NO;
                    $order->DELIVERY_ADDRESS_LINE_1 = $to_address->ADDRESS_LINE_1;
                    $order->DELIVERY_ADDRESS_LINE_2 = $to_address->ADDRESS_LINE_2;
                    $order->DELIVERY_ADDRESS_LINE_3 = $to_address->ADDRESS_LINE_3;
                    $order->DELIVERY_ADDRESS_LINE_4 = $to_address->ADDRESS_LINE_4;
                    $order->DELIVERY_CITY           = $to_address->city->CITY_NAME ?? '';
                    $order->DELIVERY_STATE          = $to_address->state->STATE_NAME ?? '';
                    $order->DELIVERY_POSTCODE       = $to_address->POST_CODE;
                    $order->DELIVERY_COUNTRY        = $to_address->country->NAME ?? '';
                    $order->DELIVERY_F_COUNTRY_NO   = $to_address->F_COUNTRY_NO;

                }else{
                    $from_address                   = Reseller::where('PK_NO',$request->customer_id)->first();
                    $order->FROM_NAME               = $from_address->NAME;
                    $order->FROM_MOBILE             = $from_address->MOBILE_NO;
                    $order->FROM_ADDRESS_LINE_1     = $from_address->ADDRESS_LINE_1;
                    $order->FROM_ADDRESS_LINE_2     = $from_address->ADDRESS_LINE_2;
                    $order->FROM_ADDRESS_LINE_3     = $from_address->ADDRESS_LINE_3;
                    $order->FROM_ADDRESS_LINE_4     = $from_address->ADDRESS_LINE_4;
                    $order->FROM_CITY               = $from_address->city->CITY_NAME ?? '';
                    $order->FROM_STATE              = $from_address->state->STATE_NAME ?? '';
                    $order->FROM_POSTCODE           = $from_address->POST_CODE;
                    $order->FROM_COUNTRY            = $from_address->country->NAME ?? '';
                    $order->FROM_F_COUNTRY_NO       = $from_address->F_COUNTRY_NO;
                }

                $order->save();
                $booking->RECONFIRM_TIME = date('Y-m-d h:i:s');
                if (isset($request->order_date_) && $request->order_date_ != 0) {
                    $booking->RECONFIRM_TIME        = date('Y-m-d',strtotime($request->order_date_));
                }
                $booking->BOOKING_STATUS = 80;
                $booking->save();

                Stock::whereIn('PK_NO',$booking_details)
                ->update(['F_ORDER_NO' => $order->PK_NO,'ORDER_STATUS' => 10,'ORDER_PRICE' => $booking->TOTAL_PRICE]);

*/




            } catch (\Exception $e) {
                DB::rollback();
                return $this->formatResponse(false, $e->getMessage(), 'admin.booking.list');
            }
        DB::commit();
        return $this->formatResponse(true, 'Successfully deleted booking with details !', 'admin.booking.list');
    }


}
