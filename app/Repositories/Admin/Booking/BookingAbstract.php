<?php
namespace App\Repositories\Admin\Booking;

use DB;
use Carbon\Carbon;
use App\Models\Agent;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Reseller;
use App\Models\BookingTemp;
use App\Traits\RepoResponse;
use App\Models\BookingDetails;
use App\Models\CustomerAddress;
use App\Models\EmailNotification;
use App\Models\BookingDetailsTemp;
use Illuminate\Support\Facades\Auth;

class BookingAbstract implements BookingInterface
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
        return $customer_info;
    }

    public function getProductINV($ig_code, $f_booking_no = null,$price_type = null)
    {
        if ($f_booking_no != null) {
            $count_my_booked = 0;

            $count_my_warehouse = Stock::selectRaw('(SELECT IFNULL(COUNT(PK_NO),0) from INV_STOCK where F_INV_WAREHOUSE_NO != 1 and IG_CODE = '. '"' .$ig_code. '"' .' and (BOOKING_STATUS IS NULL OR BOOKING_STATUS = 0 OR BOOKING_STATUS = 90 OR F_BOOKING_NO = '.$f_booking_no.'))')->limit(1)->getQuery();

            $info = Stock::select('INV_STOCK.BOX_TYPE','INV_STOCK.F_BOX_NO','INV_STOCK.PRD_VARINAT_NAME','INV_STOCK.IG_CODE','INV_STOCK.INV_WAREHOUSE_NAME','INV_STOCK.F_INV_WAREHOUSE_NO','INV_STOCK.SKUID','INV_STOCK.SHIPMENT_TYPE','INV_STOCK.PREFERRED_SHIPPING_METHOD','INV_STOCK.CUSTOMER_PREFFERED_SHIPPING_METHOD','s.SCH_ARRIVAL_DATE','s.SHIPMENT_STATUS','INV_STOCK.FINAL_PREFFERED_SHIPPING_METHOD','INV_STOCK.F_SHIPPMENT_NO','p.REGULAR_PRICE','p.INSTALLMENT_PRICE','p.SEA_FREIGHT_CHARGE as SEA_FREIGHT_COST','p.AIR_FREIGHT_CHARGE as AIR_FREIGHT_COST','p.LOCAL_POSTAGE as SM_COST','p.INTER_DISTRICT_POSTAGE as SS_COST','INV_STOCK.BARCODE','INV_STOCK.F_PRD_VARIANT_NO','p.PRIMARY_IMG_RELATIVE_PATH'
            ,DB::raw('count(INV_STOCK.PK_NO) as total')
            )
            ->selectSub($count_my_warehouse, 'count_my_warehouse')
            // ->selectSub($prodct_image, 'PRIMARY_IMG_RELATIVE_PATH')
            ->leftJoin('SC_SHIPMENT as s','s.PK_NO','INV_STOCK.F_SHIPPMENT_NO')
            ->leftJoin('PRD_VARIANT_SETUP as p','p.MRK_ID_COMPOSITE_CODE','INV_STOCK.IG_CODE')
            ->where('INV_STOCK.IG_CODE', $ig_code)
            // ->where('INV_STOCK.F_INV_WAREHOUSE_NO', 1)
            ->whereRaw('( INV_STOCK.BOOKING_STATUS IS NULL OR INV_STOCK.BOOKING_STATUS = 0 OR INV_STOCK.BOOKING_STATUS = 90 OR INV_STOCK.F_BOOKING_NO = '.$f_booking_no.' ) ')
            ->groupBy('INV_STOCK.IG_CODE','INV_STOCK.F_INV_WAREHOUSE_NO','INV_STOCK.BOX_TYPE','INV_STOCK.SHIPMENT_TYPE','INV_STOCK.F_SHIPPMENT_NO')
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
        }else{
            $my_warehouse_name = Stock::select('INV_WAREHOUSE_NAME')->where('F_INV_WAREHOUSE_NO',2)->first();

            $count_my_warehouse = Stock::selectRaw('(SELECT IFNULL(COUNT(PK_NO),0) from INV_STOCK where F_INV_WAREHOUSE_NO != 1 and IG_CODE = '. '"' .$ig_code. '"' .' and (BOOKING_STATUS IS NULL OR BOOKING_STATUS = 0 OR BOOKING_STATUS = 90))')->limit(1)->getQuery();

            $data['info'] = Stock::select('INV_STOCK.BOX_TYPE','INV_STOCK.PRD_VARINAT_NAME','INV_STOCK.IG_CODE','INV_STOCK.INV_WAREHOUSE_NAME','INV_STOCK.F_INV_WAREHOUSE_NO','INV_STOCK.SKUID','INV_STOCK.PREFERRED_SHIPPING_METHOD','INV_STOCK.SHIPMENT_TYPE','INV_STOCK.F_SHIPPMENT_NO','INV_STOCK.CUSTOMER_PREFFERED_SHIPPING_METHOD','s.SCH_ARRIVAL_DATE','s.SHIPMENT_STATUS','INV_STOCK.F_BOX_NO','INV_STOCK.FINAL_PREFFERED_SHIPPING_METHOD','p.REGULAR_PRICE','p.INSTALLMENT_PRICE','p.SEA_FREIGHT_CHARGE as SEA_FREIGHT_COST','p.AIR_FREIGHT_CHARGE as AIR_FREIGHT_COST','p.LOCAL_POSTAGE as SM_COST','p.INTER_DISTRICT_POSTAGE as SS_COST','INV_STOCK.BARCODE','INV_STOCK.F_PRD_VARIANT_NO','p.PRIMARY_IMG_RELATIVE_PATH'
            ,DB::raw('count(INV_STOCK.PK_NO) as total')
            )
            ->selectSub($count_my_warehouse, 'count_my_warehouse')
            // ->selectSub($prodct_image, 'PRIMARY_IMG_RELATIVE_PATH')
            ->leftJoin('SC_SHIPMENT as s','s.PK_NO','INV_STOCK.F_SHIPPMENT_NO')
            ->leftJoin('PRD_VARIANT_SETUP as p','p.MRK_ID_COMPOSITE_CODE','INV_STOCK.IG_CODE')
            ->where('INV_STOCK.IG_CODE', $ig_code)
            // ->where('INV_STOCK.F_INV_WAREHOUSE_NO', 1)
            ->whereRaw('( BOOKING_STATUS IS NULL OR BOOKING_STATUS = 0 OR BOOKING_STATUS = 90 ) ')
            ->groupBy('INV_STOCK.IG_CODE','INV_STOCK.F_INV_WAREHOUSE_NO','INV_STOCK.BOX_TYPE','INV_STOCK.SHIPMENT_TYPE','INV_STOCK.F_SHIPPMENT_NO')
            ->get();
            $data['count_my_booked'] =  0 ;
        }

        $data['my_warehouse_name']  = $my_warehouse_name->INV_WAREHOUSE_NAME ?? null ;

        // $prd_variant_id = Stock::select('F_PRD_VARIANT_NO')->where('IG_CODE', $ig_code)->first();

        if (!empty($data['info'])) {
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

    }

    public function findOrThrowException($PK_NO, $checkoffer=null)
    {
        $data       = array();
        $booking    = $this->booking->find($PK_NO);
        $start      = Carbon::parse($booking->BOOKING_TIME);
        $end        = Carbon::parse($booking->EXPIERY_DATE_TIME);
        $booking->EXPIERY_DATE_TIME_DIF = $end->diffInHours($start) - 12;

        $booking_details = BookingDetails::select('INV_STOCK.*','SLS_BOOKING_DETAILS.IS_REGULAR', 'SLS_BOOKING_DETAILS.IS_FREIGHT','SLS_BOOKING_DETAILS.IS_SM','SLS_BOOKING_DETAILS.IS_REGULAR',DB::raw('ifnull(count(INV_STOCK.PK_NO),0) as total_book_qty') )
        ->leftJoin('INV_STOCK','INV_STOCK.PK_NO','SLS_BOOKING_DETAILS.F_INV_STOCK_NO')
        ->where('SLS_BOOKING_DETAILS.F_BOOKING_NO', $booking->PK_NO)
        ->groupBy('INV_STOCK.IG_CODE')->get();

        if ($booking_details && count($booking_details) > 0 ) {
            foreach ($booking_details as $key => $value) {
                $value->book_info = $this->getProductINV($value->IG_CODE, $value->F_BOOKING_NO,$value->IS_REGULAR);
            }
        }
        $data['booking']            = $booking;
        $data['booking_details']    = $booking_details ?? null ;

        if($checkoffer == '1'){
           DB::statement('CALL PROC_SLS_CHECK_OFFER(:booking_no );',array($PK_NO));
           DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO', $PK_NO)->where('IS_PROCESSED',0)->where('IS_TEMP',0)->delete();
            $bundle = DB::table('SLS_CHECK_OFFER')
            ->where('SLS_CHECK_OFFER.F_BOOKING_NO',$PK_NO)
            ->where('IS_TEMP',0)
            ->groupBy('SLS_CHECK_OFFER.F_VARIANT_NO')
            ->get();

            if(count($bundle) > 0 ){
                foreach ($bundle as $key => $value) {
                    $check_duplicat = DB::table('SLS_CHECK_OFFER')
                    ->select('F_BUNDLE_NO', DB::RAW('sum(SLS_CHECK_OFFER.REGULAR_BUNDLE_PRICE) as TOTAL_REGULAR_BUNDLE_PRICE'))
                    ->where('SLS_CHECK_OFFER.F_BOOKING_NO',$PK_NO)
                    ->where('SLS_CHECK_OFFER.IS_TEMP',0)
                    ->where('SLS_CHECK_OFFER.F_VARIANT_NO',$value->F_VARIANT_NO)
                    ->groupBy('SLS_CHECK_OFFER.F_BUNDLE_NO')
                    ->orderBy('TOTAL_REGULAR_BUNDLE_PRICE','ASC')
                    ->get();

                    if(count($check_duplicat) > 1){
                        foreach ($check_duplicat as $l => $recod) {
                            if($l != 0 ){
                                DB::table('SLS_CHECK_OFFER')->where('F_BUNDLE_NO', $recod->F_BUNDLE_NO)->where('IS_TEMP',0)->delete();
                            }
                        }
                    }
                }
            }

            $bundle = array();
            $bundle = DB::table('SLS_CHECK_OFFER')
            ->select('SLS_CHECK_OFFER.F_BOOKING_NO','SLS_CHECK_OFFER.F_BUNDLE_NO',DB::RAW('sum(SLS_CHECK_OFFER.REGULAR_BUNDLE_PRICE) as TOTAL_REGULAR_BUNDLE_PRICE'), DB::RAW('sum(SLS_CHECK_OFFER.INSTALLMENT_BUNDLE_PRICE) as TOTAL_INSTALLMENT_BUNDLE_PRICE'),DB::RAW('COUNT(*) AS MATCHED_QTY'),DB::RAW('MAX(SLS_CHECK_OFFER.SEQUENC) AS BUNDLE_QTY'),'SLS_BUNDLE.BUNDLE_NAME_PUBLIC','SLS_BUNDLE.BUNDLE_NAME','SLS_BUNDLE.P_SS','SLS_BUNDLE.P_SM','SLS_BUNDLE.P_AIR','SLS_BUNDLE.P_SEA','SLS_BUNDLE.R_SS','SLS_BUNDLE.R_SM','SLS_BUNDLE.R_AIR','SLS_BUNDLE.R_SEA','SLS_BUNDLE.IMAGE as IMAGE_PATH')
            ->leftJoin('SLS_BUNDLE','SLS_BUNDLE.PK_NO','=','SLS_CHECK_OFFER.F_BUNDLE_NO')
            ->where('SLS_CHECK_OFFER.F_BOOKING_NO',$PK_NO)
            ->where('SLS_CHECK_OFFER.IS_TEMP',0)
            ->where('SLS_CHECK_OFFER.IS_PROCESSED',1)
            ->groupBy('SLS_CHECK_OFFER.F_BUNDLE_NO')
            // ->groupBy('SLS_CHECK_OFFER.F_VARIANT_NO')
            ->get();
            // dd($bundle);
            if($bundle){
                foreach ($bundle as $key => $value) {
                    $query = DB::SELECT("SELECT SUM(SLS_BOOKING_DETAILS.CURRENT_REGULAR_PRICE) AS TOTAL_REGULAR_PRICE ,
                    SUM(SLS_BOOKING_DETAILS.CURRENT_INSTALLMENT_PRICE) AS TOTAL_INSTALLMENT_PRICE FROM SLS_BOOKING_DETAILS LEFT JOIN SLS_CHECK_OFFER ON SLS_CHECK_OFFER.F_INV_STOCK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO WHERE SLS_BOOKING_DETAILS.F_BOOKING_NO = $PK_NO AND ( SLS_CHECK_OFFER.IS_PROCESSED IS NULL OR SLS_CHECK_OFFER.IS_PROCESSED = 0 AND SLS_CHECK_OFFER.IS_TEMP = 0 AND SLS_CHECK_OFFER.F_BUNDLE_NO = $value->F_BUNDLE_NO ) ");
                    if(!empty($query)){
                        $value->NON_BUNDLE_REGULAR_PRICE        = $query[0]->TOTAL_REGULAR_PRICE;
                        $value->NON_BUNDLE_INSTALLMENT_PRICE    = $query[0]->TOTAL_INSTALLMENT_PRICE;
                    }
                }
            }

            $data['bundle'] = $bundle;
            $data['non_bundle'] = DB::SELECT(" SELECT A.F_BOOKING_NO, A.F_INV_STOCK_NO, A.CURRENT_REGULAR_PRICE, A.CURRENT_INSTALLMENT_PRICE, A.AIR_FREIGHT, A.SEA_FREIGHT, A.IS_FREIGHT, A.SS_COST, A.SM_COST, A.IS_SM, A.IS_REGULAR, C.PRD_VARINAT_NAME, COUNT(C.F_PRD_VARIANT_NO ) AS ITEM_QTY, C.PRD_VARIANT_IMAGE_PATH
            FROM SLS_BOOKING_DETAILS AS A
            LEFT JOIN SLS_CHECK_OFFER AS B ON B.F_INV_STOCK_NO = A.F_INV_STOCK_NO AND B.F_BOOKING_NO = $PK_NO AND B.IS_TEMP = 0
            LEFT JOIN INV_STOCK AS C ON C.PK_NO = A.F_INV_STOCK_NO
            WHERE A.F_BOOKING_NO = $PK_NO
            AND ( B.IS_PROCESSED IS NULL OR B.IS_PROCESSED = 0  ) GROUP BY C.F_PRD_VARIANT_NO ");
        }
        return $this->formatResponse(true, 'Data found successfully !', 'admin.booking.list', $data);
    }

    public function postBookAndOrderWithOffer($request, $PK_NO, $type = null)
    {
        $booking_type = 'production';
        $j          = 0;
        $string     = '';
        $house      = '';
        $ship       = '';
        $ship_1     = '';
        $box_1      = '';
        $box        = '';
        if($request->products ==  null){
            return $this->formatResponse(false, 'You did not add any item in your cart !', 'admin.booking.list');
        }
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
                        }else{

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
                                    }else{
                                        $preferred_method = 'SEA';
                                    }
                                    $sku_id = Stock::select('SKUID')->where('IG_CODE', $request->products[$loop])->first();
                                    // $string .= $sku_id->SKUID.','.$house.','.$value.','.$ship.','.$box.','.$preferred_method.','.$price_type.','.$price.';';
                                    $string .= $sku_id->SKUID.','.$house.','.$value.','.$ship.','.$box.','.$preferred_method.','.$request->price_type_all.','.$request->customer_address.','.$ship_no.';';
                                }else if($status == 'zero'){
                                    echo '';
                                }else{
                                    return $this->formatResponse(false, 'Booking quantity exeeded !', 'admin.booking.list');
                                }
                            }
                        }

                    }
                }

            }
            $j++;
        }

        if ($string != '') {
            $agent_name = Agent::select('NAME')->where('PK_NO', $request->agent)->first();
            if ($request->booking_radio == 'customer') {
                $customer_no = Customer::select('NAME')->where('PK_NO', $request->customer_id)->first();
                                $is_reseller = 0;
                }elseif($request->booking_radio == 'reseller'){
                    $customer_no = Reseller::select('NAME')->where('PK_NO', $request->customer_id)->first();
                    $is_reseller = 1;
                }

                $count = substr_count($string,';');
                $row_separator    = ';';
                $column_separator = ',';
                $col_parameters = 5;

                DB::beginTransaction();
                    try {
                        $booking = new Booking();
                        $booking->BOOKING_TIME              = date('Y-m-d h:i:s');
                        $booking->EXPIERY_DATE_TIME         = Carbon::now()->addHours($request->booking_validity ?? 24 );

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
                        $booking->save();

                        $booking_id = $PK_NO = $request->booking_pk_no = $booking->PK_NO;

                        DB::statement('CALL PROC_SLS_BOOKING(:booking_id, :string, :count, :col_parameters, :column_separator, :row_separator, :booking_type, @OUT_STATUS);',
                            array(
                                $booking_id
                               ,$string
                               ,$count
                               ,$col_parameters
                               ,$column_separator
                               ,$row_separator
                               ,$booking_type
                            ));

                        $prc = DB::select('select @OUT_STATUS as OUT_STATUS');

                        if ($prc[0]->OUT_STATUS == 'success') {

                            BookingDetails::where('F_BOOKING_NO',$booking_id)->update(['IS_SM'=>$request->is_sm,'CURRENT_IS_SM'=>$request->is_sm]);

                            DB::statement('CALL PROC_SLS_CHECK_OFFER(:booking_no );',array($PK_NO));
                            DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO', $PK_NO)->where('IS_PROCESSED',0)->where('IS_TEMP',0)->delete();

                            $bundle = DB::table('SLS_CHECK_OFFER')
                             ->where('SLS_CHECK_OFFER.F_BOOKING_NO',$PK_NO)
                             ->where('IS_TEMP',0)
                             ->groupBy('SLS_CHECK_OFFER.F_VARIANT_NO')
                             ->get();

                            if(count($bundle) > 0 ){
                                 foreach ($bundle as $key => $value) {
                                    $check_duplicat = DB::table('SLS_CHECK_OFFER')
                                    ->select('F_BUNDLE_NO', DB::RAW('sum(SLS_CHECK_OFFER.REGULAR_BUNDLE_PRICE) as TOTAL_REGULAR_BUNDLE_PRICE'),DB::RAW('count(SLS_CHECK_OFFER.F_BUNDLE_NO) as TOTAL_BUNDLE_ITEM_QTY'))
                                    ->where('SLS_CHECK_OFFER.F_BOOKING_NO',$PK_NO)
                                    ->where('SLS_CHECK_OFFER.IS_TEMP',0)
                                    ->where('SLS_CHECK_OFFER.F_VARIANT_NO',$value->F_VARIANT_NO)
                                    ->groupBy('SLS_CHECK_OFFER.F_BUNDLE_NO')
                                    ->orderBy('TOTAL_BUNDLE_ITEM_QTY','DESC')
                                    ->orderBy('TOTAL_REGULAR_BUNDLE_PRICE','ASC')
                                    ->get();

                                     if(count($check_duplicat) > 1){
                                         foreach ($check_duplicat as $l => $recod) {
                                             if($l != 0 ){
                                                 DB::table('SLS_CHECK_OFFER')->where('F_BUNDLE_NO', $recod->F_BUNDLE_NO)->where('IS_TEMP',0)->delete();
                                             }
                                         }
                                     }
                                 }
                            }

                            $bundle = DB::table('SLS_CHECK_OFFER')
                             ->select('SLS_CHECK_OFFER.F_BOOKING_NO','SLS_CHECK_OFFER.F_BUNDLE_NO',DB::RAW('sum(SLS_CHECK_OFFER.REGULAR_BUNDLE_PRICE) as TOTAL_REGULAR_BUNDLE_PRICE'), DB::RAW('sum(SLS_CHECK_OFFER.INSTALLMENT_BUNDLE_PRICE) as TOTAL_INSTALLMENT_BUNDLE_PRICE'),DB::RAW('COUNT(*) AS MATCHED_QTY'),DB::RAW('MAX(SLS_CHECK_OFFER.SEQUENC) AS BUNDLE_QTY'),'SLS_BUNDLE.BUNDLE_NAME_PUBLIC','SLS_BUNDLE.BUNDLE_NAME','SLS_BUNDLE.P_SS','SLS_BUNDLE.P_SM','SLS_BUNDLE.P_AIR','SLS_BUNDLE.P_SEA','SLS_BUNDLE.R_SS','SLS_BUNDLE.R_SM','SLS_BUNDLE.R_AIR','SLS_BUNDLE.R_SEA','SLS_BUNDLE.IMAGE as IMAGE_PATH')
                             ->leftJoin('SLS_BUNDLE','SLS_BUNDLE.PK_NO','=','SLS_CHECK_OFFER.F_BUNDLE_NO')
                             ->where('SLS_CHECK_OFFER.F_BOOKING_NO',$PK_NO)
                             ->where('SLS_CHECK_OFFER.IS_TEMP',0)
                             ->where('SLS_CHECK_OFFER.IS_PROCESSED',1)
                             ->groupBy('SLS_CHECK_OFFER.F_BUNDLE_NO')
                             ->get();

                            if($bundle && count($bundle) > 0 ){

                                $request->bundle_no = $bundle[0]->F_BUNDLE_NO;
                                $offer = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_TEMP',0)->where('IS_PROCESSED',1)->get();
                                $offer_a = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_TEMP',0)->where('IS_PROCESSED',1)->where('LIST_TYPE','A')->count();
                                $offer_max_a = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_TEMP',0)->where('IS_PROCESSED',1)->where('LIST_TYPE','A')->max('SEQUENC');
                                $offer_b = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_TEMP',0)->where('IS_PROCESSED',1)->where('LIST_TYPE','B')->count();
                                $offer_max_b = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_TEMP',0)->where('IS_PROCESSED',1)->where('LIST_TYPE','B')->max('SEQUENC');
                                $bundle  = Offer::find($request->bundle_no);

                                $air_cost_a = $offer_a > 0 ? ($offer_max_a*$bundle->P_AIR)/$offer_a : 0;
                                $sea_cost_a = $offer_a > 0 ? ($offer_max_a*$bundle->P_SEA)/$offer_a : 0;
                                $ss_cost_a  = $offer_a > 0 ? ($offer_max_a*$bundle->P_SS)/$offer_a : 0;
                                $sm_cost_a  = $offer_a > 0 ? ($offer_max_a*$bundle->P_SM)/$offer_a : 0;

                                $air_cost_b = $offer_b > 0 ? ($offer_max_b*$bundle->R_AIR)/$offer_b : 0;
                                $sea_cost_b = $offer_b > 0 ? ($offer_max_b*$bundle->R_SEA)/$offer_b : 0;
                                $ss_cost_b  = $offer_b > 0 ? ($offer_max_b*$bundle->R_SS)/$offer_b : 0;
                                $sm_cost_b  = $offer_b > 0 ? ($offer_max_b*$bundle->R_SM)/$offer_b : 0;

                                if($offer){
                                    foreach ($offer as $key => $value) {
                                        if($value->LIST_TYPE == 'A'){
                                            $avg_air_cost = $air_cost_a;
                                            $avg_sea_cost = $sea_cost_a;
                                            $avg_ss_cost = $ss_cost_a;
                                            $avg_sm_cost = $sm_cost_a;
                                        }else{
                                            $avg_air_cost = $air_cost_b;
                                            $avg_sea_cost = $sea_cost_b;
                                            $avg_ss_cost = $ss_cost_b;
                                            $avg_sm_cost =  $sm_cost_b;
                                        }

                                    BookingDetails::where('F_BOOKING_NO',$request->booking_pk_no)->where('F_INV_STOCK_NO', $value->F_INV_STOCK_NO)->update([
                                    'F_BUNDLE_NO'                       => $value->F_BUNDLE_NO,
                                    'REGULAR_PRICE'                     => $value->REGULAR_BUNDLE_PRICE ,
                                    'INSTALLMENT_PRICE'                 => $value->INSTALLMENT_BUNDLE_PRICE,
                                    'AIR_FREIGHT'                       => $avg_air_cost,
                                    'SEA_FREIGHT'                       => $avg_sea_cost,
                                    'SS_COST'                           => $avg_ss_cost,
                                    'SM_COST'                           => $avg_sm_cost,
                                    'CURRENT_REGULAR_PRICE'             => $value->REGULAR_BUNDLE_PRICE ,
                                    'CURRENT_INSTALLMENT_PRICE'         => $value->INSTALLMENT_BUNDLE_PRICE,
                                    'BUNDLE_SEQUENC'                    => $value->SEQUENC,
                                    'CURRENT_AIR_FREIGHT'               => $avg_air_cost,
                                    'CURRENT_SEA_FREIGHT'               => $avg_sea_cost,
                                    'CURRENT_SS_COST'                   => $avg_ss_cost,
                                    'CURRENT_SM_COST'                   => $avg_sm_cost,
                                    ]);
                                }

                                $data = BookingDetails::select('CURRENT_IS_REGULAR','F_BUNDLE_NO as bundle_no',DB::raw('(IFNULL(SUM(CURRENT_REGULAR_PRICE),0)) as regular_price'),DB::raw('(IFNULL(SUM(CURRENT_INSTALLMENT_PRICE),0)) as instalment_price'),DB::raw('(IFNULL(COUNT(PK_NO),0)) as bundle_item_count'))->where('F_BOOKING_NO',$request->booking_pk_no)->whereNotNull('F_BUNDLE_NO')->groupBy('F_BUNDLE_NO','BUNDLE_SEQUENC')->get();

                                foreach ($data as $key => $value) {
                                    if($value->CURRENT_IS_REGULAR == 1){
                                        $price = $value->regular_price;
                                    }else{
                                        $price = $value->instalment_price;
                                    }
                                    $comission = DB::SELECT("SELECT AMOUNT FROM SLS_COMMISION WHERE $price BETWEEN FROM_PRICE AND TO_PRICE");
                                    $line_comission = $comission[0]->AMOUNT/$value->bundle_item_count;

                                    BookingDetails::where('F_BOOKING_NO',$request->booking_pk_no)->where('F_BUNDLE_NO', $value->bundle_no)->update(['COMISSION' => $line_comission]);
                                }

                                    $total_comission = BookingDetails::where('F_BOOKING_NO',$request->booking_pk_no)->sum('COMISSION');

                                    Booking::where('PK_NO',$request->booking_pk_no)->update(['TOTAL_COMISSION' => $total_comission]);

                                }

                                $booking            = Booking::find($request->booking_pk_no);
                                $booking_details    = BookingDetails::select('F_INV_STOCK_NO')->where('F_BOOKING_NO',$request->booking_pk_no)->get();
                                $order = new Order();
                                $order->F_CUSTOMER_NO       = $booking->F_CUSTOMER_NO;
                                $order->CUSTOMER_NAME       = $booking->CUSTOMER_NAME;
                                $order->IS_RESELLER         = $booking->IS_RESELLER;
                                $order->F_RESELLER_NO       = $booking->F_RESELLER_NO;
                                $order->RESELLER_NAME       = $booking->RESELLER_NAME;
                                $order->F_BOOKING_NO        = $booking->PK_NO;

                                if ($booking->IS_RESELLER == '0') {
                                    $to_address     = CustomerAddress::where('F_CUSTOMER_NO',$booking->F_CUSTOMER_NO)->where('F_ADDRESS_TYPE_NO',1)->where('IS_DEFAULT',1)->first();
                                    $from_address   = \Config::get('static_array.order_from');

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

                                    $order->F_TO_ADDRESS            = $to_address->PK_NO;
                                    $order->DELIVERY_NAME           = $to_address->NAME ?? '';
                                    $order->DELIVERY_MOBILE         = $to_address->TEL_NO ?? '';
                                    $order->DELIVERY_ADDRESS_LINE_1 = $to_address->ADDRESS_LINE_1 ?? '';
                                    $order->DELIVERY_ADDRESS_LINE_2 = $to_address->ADDRESS_LINE_2 ?? '';
                                    $order->DELIVERY_ADDRESS_LINE_3 = $to_address->ADDRESS_LINE_3 ?? '';
                                    $order->DELIVERY_ADDRESS_LINE_4 = $to_address->ADDRESS_LINE_4 ?? '';
                                    $order->DELIVERY_CITY           = $to_address->city->CITY_NAME ?? '';
                                    $order->DELIVERY_STATE          = $to_address->state->STATE_NAME ?? '';
                                    $order->DELIVERY_POSTCODE       = $to_address->POST_CODE ?? '';
                                    $order->DELIVERY_COUNTRY        = $to_address->country->NAME ?? '';
                                    $order->DELIVERY_F_COUNTRY_NO   = $to_address->F_COUNTRY_NO ?? '';

                                }else{
                                    $from_address                   = Reseller::where('PK_NO',$booking->F_RESELLER_NO)->first();
                                    $to_address                     = CustomerAddress::select('PK_NO')->where('F_RESELLER_NO',$booking->F_RESELLER_NO)->where('F_ADDRESS_TYPE_NO',1)->where('IS_DEFAULT',1)->first();

                                    $order->F_TO_ADDRESS            = $to_address->PK_NO;
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
                                $booking->RECONFIRM_TIME        = date('Y-m-d h:i:s');
                                $booking->BOOKING_TIME          = date('Y-m-d h:i:s');
                                $booking->BOOKING_STATUS        = 80;
                                $booking->IS_BUNDLE_MATCHED     = 1;
                                $booking->save();

                                Stock::whereIn('PK_NO',$booking_details)->update(['F_ORDER_NO' => $order->PK_NO,'ORDER_STATUS' => 10]);

                                $email = new EmailNotification();
                                $email->TYPE = 'Order Create';
                                $email->F_BOOKING_NO = $booking->PK_NO;
                                $email->F_SS_CREATED_BY = Auth::user()->PK_NO;
                                if($booking->IS_RESELLER == 0){
                                    $email->CUSTOMER_NO = $booking->F_CUSTOMER_NO;
                                    $email->IS_RESELLER = 0;
                                }else{
                                    $email->RESELLER_NO = $booking->F_RESELLER_NO;
                                    $email->IS_RESELLER = 1;
                                }
                                $email->save();

                                $response_status    = true;
                                $response_msg       = 'Booking successfull with offer !';
                                $response_route     = 'admin.booking_to_order.book-order';

                            }
                        }else{
                            $response_status    = true;
                            $response_msg       = 'Booking successfull without offer !';
                            $response_route     = 'admin.booking_to_order.book-order';
                        }


                    } catch (\Exception $e) {
                        dd( $e);
                        DB::rollback();
                        return $this->formatResponse(false, $e->getMessage(), 'admin.booking.list');
                    }

                DB::commit();

            return $this->formatResponse($response_status, $response_msg, $response_route,$booking_id);

        }

    }

    public function postUpdate($request, $PK_NO, $type = null)
    {
        $booking_type = 'production';
        if($request->offer_status == 'order_with_offer'){
            $order = Order::where('F_BOOKING_NO',$request->booking_pk_no)->first();
        if($order){
            return $this->formatResponse(false, 'This booking already transfered to order list, Offer apply not successfull !', 'admin.booking.list');
        }

        DB::beginTransaction();
            try {
                $offer = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_TEMP',0)->where('IS_PROCESSED',1)->get();
                $offer_a = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_TEMP',0)->where('IS_PROCESSED',1)->where('LIST_TYPE','A')->count();
                $offer_max_a = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_TEMP',0)->where('IS_PROCESSED',1)->where('LIST_TYPE','A')->max('SEQUENC');
                $offer_b = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_TEMP',0)->where('IS_PROCESSED',1)->where('LIST_TYPE','B')->count();
                $offer_max_b = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_TEMP',0)->where('IS_PROCESSED',1)->where('LIST_TYPE','B')->max('SEQUENC');
                $bundle  = Offer::find($request->bundle_no);

                $air_cost_a = $offer_a > 0 ? ($offer_max_a*$bundle->P_AIR)/$offer_a : 0;
                $sea_cost_a = $offer_a > 0 ? ($offer_max_a*$bundle->P_SEA)/$offer_a : 0;
                $ss_cost_a  = $offer_a > 0 ? ($offer_max_a*$bundle->P_SS)/$offer_a : 0;
                $sm_cost_a  = $offer_a > 0 ? ($offer_max_a*$bundle->P_SM)/$offer_a : 0;

                $air_cost_b = $offer_b > 0 ? ($offer_max_b*$bundle->R_AIR)/$offer_b : 0;
                $sea_cost_b = $offer_b > 0 ? ($offer_max_b*$bundle->R_SEA)/$offer_b : 0;
                $ss_cost_b  = $offer_b > 0 ? ($offer_max_b*$bundle->R_SS)/$offer_b : 0;
                $sm_cost_b  = $offer_b > 0 ? ($offer_max_b*$bundle->R_SM)/$offer_b : 0;


                if($offer){
                    foreach ($offer as $key => $value) {
                        if($value->LIST_TYPE == 'A'){
                            $avg_air_cost = $air_cost_a;
                            $avg_sea_cost = $sea_cost_a;
                            $avg_ss_cost = $ss_cost_a;
                            $avg_sm_cost = $sm_cost_a;
                        }else{
                            $avg_air_cost = $air_cost_b;
                            $avg_sea_cost = $sea_cost_b;
                            $avg_ss_cost = $ss_cost_b;
                            $avg_sm_cost =  $sm_cost_b;
                        }
                        BookingDetails::where('F_BOOKING_NO',$request->booking_pk_no)->where('F_INV_STOCK_NO', $value->F_INV_STOCK_NO)->update([
                            'F_BUNDLE_NO'                       => $value->F_BUNDLE_NO,
                            'REGULAR_PRICE'                     => $value->REGULAR_BUNDLE_PRICE ,
                            'INSTALLMENT_PRICE'                 => $value->INSTALLMENT_BUNDLE_PRICE,
                            'AIR_FREIGHT'                       => $avg_air_cost,
                            'SEA_FREIGHT'                       => $avg_sea_cost,
                            'SS_COST'                           => $avg_ss_cost,
                            'SM_COST'                           => $avg_sm_cost,
                            'CURRENT_REGULAR_PRICE'             => $value->REGULAR_BUNDLE_PRICE ,
                            'CURRENT_INSTALLMENT_PRICE'         => $value->INSTALLMENT_BUNDLE_PRICE,
                            'BUNDLE_SEQUENC'                    => $value->SEQUENC,
                            'CURRENT_AIR_FREIGHT'               => $avg_air_cost,
                            'CURRENT_SEA_FREIGHT'               => $avg_sea_cost,
                            'CURRENT_SS_COST'                   => $avg_ss_cost,
                            'CURRENT_SM_COST'                   => $avg_sm_cost,
                        ]);
                    }
                    $data = BookingDetails::select('CURRENT_IS_REGULAR','F_BUNDLE_NO as bundle_no'
                                            ,DB::raw('(IFNULL(SUM(CURRENT_REGULAR_PRICE),0)) as regular_price')
                                            ,DB::raw('(IFNULL(SUM(CURRENT_INSTALLMENT_PRICE),0)) as instalment_price')
                                            ,DB::raw('(IFNULL(COUNT(PK_NO),0)) as bundle_item_count')
                                            )
                                            ->where('F_BOOKING_NO',$request->booking_pk_no)
                                            ->whereNotNull('F_BUNDLE_NO')
                                            ->groupBy('F_BUNDLE_NO','BUNDLE_SEQUENC')
                                            ->get();

                    foreach ($data as $key => $value) {
                        if($value->CURRENT_IS_REGULAR == 1){
                            $price = $value->regular_price;
                        }else{
                            $price = $value->instalment_price;
                        }
                        $comission = DB::SELECT("SELECT AMOUNT FROM SLS_COMMISION WHERE $price BETWEEN FROM_PRICE AND TO_PRICE");
                        $line_comission = $comission[0]->AMOUNT/$value->bundle_item_count;
                        BookingDetails::where('F_BOOKING_NO',$request->booking_pk_no)->where('F_BUNDLE_NO', $value->bundle_no)->update(['COMISSION' => $line_comission]);
                    }

                    $total_comission = BookingDetails::where('F_BOOKING_NO',$request->booking_pk_no)->sum('COMISSION');
                    Booking::where('PK_NO',$request->booking_pk_no)->update(['TOTAL_COMISSION' => $total_comission]);
                }

                $booking            = Booking::find($request->booking_pk_no);
                $booking_details    = BookingDetails::select('F_INV_STOCK_NO')->where('F_BOOKING_NO',$request->booking_pk_no)->get();
                $order = new Order();
                $order->F_CUSTOMER_NO       = $booking->F_CUSTOMER_NO;
                $order->CUSTOMER_NAME       = $booking->CUSTOMER_NAME;
                $order->IS_RESELLER         = $booking->IS_RESELLER;
                $order->F_RESELLER_NO       = $booking->F_RESELLER_NO;
                $order->RESELLER_NAME       = $booking->RESELLER_NAME;
                $order->F_BOOKING_NO        = $booking->PK_NO;

                if ($booking->IS_RESELLER == '0') {
                    $to_address     = CustomerAddress::where('F_CUSTOMER_NO',$booking->F_CUSTOMER_NO)->where('F_ADDRESS_TYPE_NO',1)->where('IS_DEFAULT',1)->first();
                    $from_address   = \Config::get('static_array.order_from');

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

                    $order->F_TO_ADDRESS            = $to_address->PK_NO;
                    $order->DELIVERY_NAME           = $to_address->NAME ?? '';
                    $order->DELIVERY_MOBILE         = $to_address->TEL_NO ?? '';
                    $order->DELIVERY_ADDRESS_LINE_1 = $to_address->ADDRESS_LINE_1 ?? '';
                    $order->DELIVERY_ADDRESS_LINE_2 = $to_address->ADDRESS_LINE_2 ?? '';
                    $order->DELIVERY_ADDRESS_LINE_3 = $to_address->ADDRESS_LINE_3 ?? '';
                    $order->DELIVERY_ADDRESS_LINE_4 = $to_address->ADDRESS_LINE_4 ?? '';
                    $order->DELIVERY_CITY           = $to_address->city->CITY_NAME ?? '';
                    $order->DELIVERY_STATE          = $to_address->state->STATE_NAME ?? '';
                    $order->DELIVERY_POSTCODE       = $to_address->POST_CODE ?? '';
                    $order->DELIVERY_COUNTRY        = $to_address->country->NAME ?? '';
                    $order->DELIVERY_F_COUNTRY_NO   = $to_address->F_COUNTRY_NO ?? '';

                }else{
                    $from_address                   = Reseller::where('PK_NO',$booking->F_RESELLER_NO)->first();
                    $to_address                     = CustomerAddress::select('PK_NO')->where('F_RESELLER_NO',$booking->F_RESELLER_NO)->where('F_ADDRESS_TYPE_NO',1)->where('IS_DEFAULT',1)->first();

                    $order->F_TO_ADDRESS            = $to_address->PK_NO;
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
                $booking->RECONFIRM_TIME        = date('Y-m-d h:i:s');
                $booking->BOOKING_STATUS        = 80;
                $booking->IS_BUNDLE_MATCHED     = 1;
                // $booking->TOTAL_PRICE           = $request->total_price;
                $booking->save();

                $email = new EmailNotification();
                $email->TYPE = 'Order Create';
                $email->F_BOOKING_NO = $booking->PK_NO;
                $email->F_SS_CREATED_BY = Auth::user()->PK_NO;
                if($booking->IS_RESELLER == 0){
                    $email->CUSTOMER_NO = $booking->F_CUSTOMER_NO;
                    $email->IS_RESELLER = 0;
                }else{
                    $email->RESELLER_NO = $booking->F_RESELLER_NO;
                    $email->IS_RESELLER = 1;
                }
                $email->save();

                Stock::whereIn('PK_NO',$booking_details)->update(['F_ORDER_NO' => $order->PK_NO,'ORDER_STATUS' => 10]);

            } catch (\Exception $e) {
                DB::rollback();
                return $this->formatResponse(false, $e->getMessage(), 'admin.booking.list');
            }
            DB::commit();
            return $this->formatResponse(true, 'Successfully offer applyed !', 'admin.booking.list');

        }else {
            if ($PK_NO != 0) {
                $book = Booking::where('PK_NO',$PK_NO)->first();
            }
            $j          = 0;
            $string     = '';
            $house      = '';
            $ship       = '';
            $ship_1     = '';
            $box_1      = '';
            $box        = '';
            $response_status    = false;
            $response_msg       = 'Booking unsuccessfull !';
            $response_route      = 'admin.booking.list';
            $eq = 0;
            if($request->products ==  null){
                return $this->formatResponse(false, 'You did not add any item in your cart !', 'admin.booking.list');
            }
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
                                    }else{

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
                                                }else{
                                                    $preferred_method = 'SEA';
                                                }
                                                $sku_id = Stock::select('SKUID')->where('IG_CODE', $request->products[$loop])->first();
                                                // $string .= $sku_id->SKUID.','.$house.','.$value.','.$ship.','.$box.','.$preferred_method.','.$price_type.','.$price.';';
                                                $string .= $sku_id->SKUID.','.$house.','.$value.','.$ship.','.$box.','.$preferred_method.','.$request->price_type_all.','.$request->customer_address.','.$ship_no.';';
                                            }else if($status == 'zero'){
                                                echo '';
                                            }else{
                                                return $this->formatResponse(false, 'Booking quantity exeeded !', 'admin.booking.list');
                                            }
                                        }
                                    }

                                }
                        }

                }
               $j++;
            }

                    if ($string != 0) {
                        if($type == 'checkoffer'){
                            $agent_name = Agent::select('NAME')->where('PK_NO', 1)->first();
                        }else{
                            $agent_name = Agent::select('NAME')->where('PK_NO', $request->agent)->first();
                        }

                        if($type == 'checkoffer'){
                            $customer_no = Customer::select('NAME')->where('PK_NO', 1)->first();
                            $is_reseller = 0;
                        }else{
                            if ($request->booking_radio == 'customer') {
                                $customer_no = Customer::select('NAME')->where('PK_NO', $request->customer_id)->first();
                                $is_reseller = 0;
                            }elseif($request->booking_radio == 'reseller'){
                                $customer_no = Reseller::select('NAME')->where('PK_NO', $request->customer_id)->first();
                                $is_reseller = 1;
                            }
                        }

                        $count = substr_count($string,';');
                        $row_separator    = ';';
                        $column_separator = ',';
                        $col_parameters = 5;

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
                               $booking = Booking::find($PK_NO);
                                ###End Delete old booking ###
                            }else{
                                if($type == 'checkoffer'){
                                    $booking = new BookingTemp();
                                    $booking_type = 'temp';
                                }else{
                                    $booking = new Booking();
                                }
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
                            // $booking->TOTAL_PRICE               = $request->grand_total;

                            $booking->save();

                           $booking_id = $booking->PK_NO;

                            DB::statement('CALL PROC_SLS_BOOKING(:booking_id, :string, :count, :col_parameters, :column_separator, :row_separator, :booking_type, @OUT_STATUS);',
                            array(
                                $booking_id
                               ,$string
                               ,$count
                               ,$col_parameters
                               ,$column_separator
                               ,$row_separator
                               ,$booking_type
                            )
                            );

                            $prc = DB::select('select @OUT_STATUS as OUT_STATUS');
                            if ($prc[0]->OUT_STATUS == 'success') {
                                $response_status   = true;
                                $response_msg      = 'Booking successfull !';
                                $response_route    = 'admin.booking.list';
                                if($type == 'checkoffer'){
                                    $booking_details   = BookingDetailsTemp::select('F_INV_STOCK_NO')->where('F_BOOKING_NO',$booking->PK_NO)->get();
                                }else{
                                    $booking_details   = BookingDetails::select('F_INV_STOCK_NO')->where('F_BOOKING_NO',$booking->PK_NO)->get();
                                }


                                if($type != 'checkoffer'){
                                    foreach ($booking_details as $key => $value) {
                                        BookingDetails::where('F_INV_STOCK_NO',$value->F_INV_STOCK_NO)->update(['IS_SM'=>$request->is_sm,'CURRENT_IS_SM'=>$request->is_sm]);
                                    }
                                }

                                if ($type == 'order') {
                                    $booking            = Booking::find($booking->PK_NO);
                                    $order = new Order();
                                    $order->F_CUSTOMER_NO       = $booking->F_CUSTOMER_NO;
                                    $order->CUSTOMER_NAME       = $booking->CUSTOMER_NAME;
                                    $order->IS_RESELLER         = $booking->IS_RESELLER;
                                    $order->F_RESELLER_NO       = $booking->F_RESELLER_NO;
                                    $order->RESELLER_NAME       = $booking->RESELLER_NAME;
                                    $order->F_BOOKING_NO        = $booking->PK_NO;

                                    if ($request->booking_radio == 'customer') {
                                        $to_address     = CustomerAddress::where('F_CUSTOMER_NO',$request->customer_id)->where('F_ADDRESS_TYPE_NO',1)->where('IS_DEFAULT',1)->first();

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

                                        $order->F_TO_ADDRESS            = $to_address->PK_NO;
                                        $order->DELIVERY_NAME           = $to_address->NAME;
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
                                        $to_address                     = CustomerAddress::select('PK_NO')->where('F_RESELLER_NO',$booking->F_RESELLER_NO)->where('F_ADDRESS_TYPE_NO',1)->where('IS_DEFAULT',1)->first();

                                        $order->F_TO_ADDRESS            = $to_address->PK_NO;
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

                                    $email = new EmailNotification();
                                    $email->TYPE = 'Order Create';
                                    $email->F_BOOKING_NO = $booking->PK_NO;
                                    $email->F_SS_CREATED_BY = Auth::user()->PK_NO;
                                    if($booking->IS_RESELLER == 0){
                                        $email->CUSTOMER_NO = $booking->F_CUSTOMER_NO;
                                        $email->IS_RESELLER = 0;
                                    }else{
                                        $email->RESELLER_NO = $booking->F_RESELLER_NO;
                                        $email->IS_RESELLER = 1;
                                    }
                                    $email->save();

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
                        if ($type == 'checkoffer') {
                            return $this->formatResponse(true, 'Successfully check offer !', 'admin.booking_to_order.book-order',$booking_id);
                        }

                        if ($request->booking_type == 'book_and_check_offer') {
                            return $this->formatResponse(true, 'Successfully booked and check offer !', 'admin.booking.edit',$booking_id);
                        }

               return $this->formatResponse($response_status, $response_msg, $response_route);
            }

           return $this->formatResponse(false, 'Something went wrong, please try again !', 'admin.booking.list');

        }
    }

    public function postCheckOffer($PK_NO)
    {
        DB::statement('CALL PROC_SLS_CHECK_OFFER_TEMP(:booking_no );',array($PK_NO));
           DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO', $PK_NO)->where('IS_PROCESSED',0)->where('IS_TEMP',1)->delete();
            $bundle = DB::table('SLS_CHECK_OFFER')
            ->where('SLS_CHECK_OFFER.F_BOOKING_NO',$PK_NO)
            ->where('SLS_CHECK_OFFER.IS_TEMP',1)
            ->groupBy('SLS_CHECK_OFFER.F_VARIANT_NO')
            ->get();

            if(count($bundle) > 0 ){
                foreach ($bundle as $key => $value) {
                    $check_duplicat = DB::table('SLS_CHECK_OFFER')
                    ->select('F_BUNDLE_NO', DB::RAW('sum(SLS_CHECK_OFFER.REGULAR_BUNDLE_PRICE) as TOTAL_REGULAR_BUNDLE_PRICE'),DB::RAW('count(SLS_CHECK_OFFER.F_BUNDLE_NO) as TOTAL_BUNDLE_ITEM_QTY'))
                    ->where('SLS_CHECK_OFFER.F_BOOKING_NO',$PK_NO)
                    ->where('SLS_CHECK_OFFER.IS_TEMP',1)
                    ->where('SLS_CHECK_OFFER.F_VARIANT_NO',$value->F_VARIANT_NO)
                    ->groupBy('SLS_CHECK_OFFER.F_BUNDLE_NO')
                    ->orderBy('TOTAL_BUNDLE_ITEM_QTY','DESC')
                    ->orderBy('TOTAL_REGULAR_BUNDLE_PRICE','ASC')
                    ->get();

                    if(count($check_duplicat) > 1){
                        foreach ($check_duplicat as $l => $recod) {
                            if($l != 0 ){
                                DB::table('SLS_CHECK_OFFER')->where('F_BUNDLE_NO', $recod->F_BUNDLE_NO)->where('IS_TEMP',1)->delete();
                            }
                        }
                    }
                }
            }

            $bundle = array();
            $bundle = DB::table('SLS_CHECK_OFFER')
            ->select('SLS_CHECK_OFFER.F_BOOKING_NO','SLS_CHECK_OFFER.F_BUNDLE_NO',DB::RAW('sum(SLS_CHECK_OFFER.REGULAR_BUNDLE_PRICE) as TOTAL_REGULAR_BUNDLE_PRICE'), DB::RAW('sum(SLS_CHECK_OFFER.INSTALLMENT_BUNDLE_PRICE) as TOTAL_INSTALLMENT_BUNDLE_PRICE'),DB::RAW('COUNT(*) AS MATCHED_QTY'),DB::RAW('MAX(SLS_CHECK_OFFER.SEQUENC) AS BUNDLE_QTY'),'SLS_BUNDLE.BUNDLE_NAME_PUBLIC','SLS_BUNDLE.BUNDLE_NAME','SLS_BUNDLE.P_SS','SLS_BUNDLE.P_SM','SLS_BUNDLE.P_AIR','SLS_BUNDLE.P_SEA','SLS_BUNDLE.R_SS','SLS_BUNDLE.R_SM','SLS_BUNDLE.R_AIR','SLS_BUNDLE.R_SEA','SLS_BUNDLE.IMAGE as IMAGE_PATH')
            ->leftJoin('SLS_BUNDLE','SLS_BUNDLE.PK_NO','=','SLS_CHECK_OFFER.F_BUNDLE_NO')
            ->where('SLS_CHECK_OFFER.F_BOOKING_NO',$PK_NO)
            ->where('SLS_CHECK_OFFER.IS_TEMP',1)
            ->where('SLS_CHECK_OFFER.IS_PROCESSED',1)
            ->groupBy('SLS_CHECK_OFFER.F_BUNDLE_NO')
            // ->groupBy('SLS_CHECK_OFFER.F_VARIANT_NO')
            ->get();
            // dd($bundle);
            if($bundle){
                foreach ($bundle as $key => $value) {
                    $query = DB::SELECT("SELECT SUM(SLS_BOOKING_DETAILS_TEMP.CURRENT_REGULAR_PRICE) AS TOTAL_REGULAR_PRICE ,
                    SUM(SLS_BOOKING_DETAILS_TEMP.CURRENT_INSTALLMENT_PRICE) AS TOTAL_INSTALLMENT_PRICE FROM SLS_BOOKING_DETAILS_TEMP LEFT JOIN SLS_CHECK_OFFER ON SLS_CHECK_OFFER.F_INV_STOCK_NO = SLS_BOOKING_DETAILS_TEMP.F_INV_STOCK_NO WHERE SLS_BOOKING_DETAILS_TEMP.F_BOOKING_NO = $PK_NO AND ( SLS_CHECK_OFFER.IS_PROCESSED IS NULL OR SLS_CHECK_OFFER.IS_PROCESSED = 0 AND SLS_CHECK_OFFER.IS_TEMP = 1 AND SLS_CHECK_OFFER.F_BUNDLE_NO = $value->F_BUNDLE_NO ) ");
                    if(!empty($query)){
                        $value->NON_BUNDLE_REGULAR_PRICE        = $query[0]->TOTAL_REGULAR_PRICE;
                        $value->NON_BUNDLE_INSTALLMENT_PRICE    = $query[0]->TOTAL_INSTALLMENT_PRICE;
                    }
                }
            }

        $data['non_bundle'] = DB::SELECT(" SELECT A.F_BOOKING_NO, A.F_INV_STOCK_NO, A.CURRENT_REGULAR_PRICE, A.CURRENT_INSTALLMENT_PRICE, A.AIR_FREIGHT, A.SEA_FREIGHT, A.IS_FREIGHT, A.SS_COST, A.SM_COST, A.IS_SM, A.IS_REGULAR, C.PRD_VARINAT_NAME, COUNT(C.F_PRD_VARIANT_NO ) AS ITEM_QTY, C.PRD_VARIANT_IMAGE_PATH
        FROM SLS_BOOKING_DETAILS_TEMP AS A
            LEFT JOIN SLS_CHECK_OFFER AS B ON B.F_INV_STOCK_NO = A.F_INV_STOCK_NO AND B.F_BOOKING_NO = $PK_NO AND B.IS_TEMP = 1
            LEFT JOIN INV_STOCK AS C ON C.PK_NO = A.F_INV_STOCK_NO
            WHERE A.F_BOOKING_NO = $PK_NO
            AND ( B.IS_PROCESSED IS NULL OR B.IS_PROCESSED = 0  ) GROUP BY C.F_PRD_VARIANT_NO ");

        $data['bundle'] = $bundle;
        $data['bookingDetails'] = BookingDetailsTemp::where('F_BOOKING_NO',$PK_NO)->get();

        return view('admin.booking._check_offer')->withData($data)->render();

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

    /*
    public function postOfferApply($request)
    {
        $order = Order::where('F_BOOKING_NO',$request->booking_pk_no)->first();
        if($order){
            return $this->formatResponse(false, 'This booking already transfered to order list, Offer apply not successfull !', 'admin.booking.list');
        }

        DB::beginTransaction();
            try {
                $offer = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_PROCESSED',1)->get();
                $offer_a = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_PROCESSED',1)->where('LIST_TYPE','A')->count();
                $offer_max_a = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_PROCESSED',1)->where('LIST_TYPE','A')->max('SEQUENC');
                $offer_b = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_PROCESSED',1)->where('LIST_TYPE','B')->count();
                $offer_max_b = DB::table('SLS_CHECK_OFFER')->where('F_BOOKING_NO',$request->booking_pk_no)->where('IS_PROCESSED',1)->where('LIST_TYPE','B')->max('SEQUENC');
                $bundle  = Offer::find($request->bundle_no);

                $air_cost_a = $offer_a > 0 ? ($offer_max_a*$bundle->P_AIR)/$offer_a : 0;
                $sea_cost_a = $offer_a > 0 ? ($offer_max_a*$bundle->P_SEA)/$offer_a : 0;
                $ss_cost_a  = $offer_a > 0 ? ($offer_max_a*$bundle->P_SS)/$offer_a : 0;
                $sm_cost_a  = $offer_a > 0 ? ($offer_max_a*$bundle->P_SM)/$offer_a : 0;

                $air_cost_b = $offer_b > 0 ? ($offer_max_b*$bundle->R_AIR)/$offer_b : 0;
                $sea_cost_b = $offer_b > 0 ? ($offer_max_b*$bundle->R_SEA)/$offer_b : 0;
                $ss_cost_b  = $offer_b > 0 ? ($offer_max_b*$bundle->R_SS)/$offer_b : 0;
                $sm_cost_b  = $offer_b > 0 ? ($offer_max_b*$bundle->R_SM)/$offer_b : 0;


                if($offer){
                    foreach ($offer as $key => $value) {
                        if($value->LIST_TYPE == 'A'){
                            $avg_air_cost = $air_cost_a;
                            $avg_sea_cost = $sea_cost_a;
                            $avg_ss_cost = $ss_cost_a;
                            $avg_sm_cost = $sm_cost_a;
                        }else{
                            $avg_air_cost = $air_cost_b;
                            $avg_sea_cost = $sea_cost_b;
                            $avg_ss_cost = $ss_cost_b;
                            $avg_sm_cost =  $sm_cost_b;
                        }
                        BookingDetails::where('F_BOOKING_NO',$request->booking_pk_no)->where('F_INV_STOCK_NO', $value->F_INV_STOCK_NO)->update([
                            'F_BUNDLE_NO'                       => $value->F_BUNDLE_NO,
                            'CURRENT_REGULAR_PRICE'             => $value->REGULAR_BUNDLE_PRICE ,
                            'CURRENT_INSTALLMENT_PRICE'         => $value->INSTALLMENT_BUNDLE_PRICE,
                            'BUNDLE_SEQUENC'                    => $value->SEQUENC,
                            'CURRENT_AIR_FREIGHT'               => $avg_air_cost,
                            'CURRENT_SEA_FREIGHT'               => $avg_sea_cost,
                            'CURRENT_SS_COST'                   => $avg_ss_cost,
                            'CURRENT_SM_COST'                   => $avg_sm_cost,
                        ]);
                    }
                }

                $booking            = Booking::find($request->booking_pk_no);
                $booking_details    = BookingDetails::select('F_INV_STOCK_NO')->where('F_BOOKING_NO',$request->booking_pk_no)->get();
                $order = new Order();
                $order->F_CUSTOMER_NO       = $booking->F_CUSTOMER_NO;
                $order->CUSTOMER_NAME       = $booking->CUSTOMER_NAME;
                $order->IS_RESELLER         = $booking->IS_RESELLER;
                $order->F_RESELLER_NO       = $booking->F_RESELLER_NO;
                $order->RESELLER_NAME       = $booking->RESELLER_NAME;
                $order->F_BOOKING_NO        = $booking->PK_NO;

                if ($booking->IS_RESELLER == '0') {
                    $to_address     = CustomerAddress::where('F_CUSTOMER_NO',$booking->F_CUSTOMER_NO)->where('F_ADDRESS_TYPE_NO',1)->first();
                    $from_address   = \Config::get('static_array.order_from');

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

                    $order->DELIVERY_NAME           = $to_address->NAME ?? '';
                    $order->DELIVERY_MOBILE         = $to_address->TEL_NO ?? '';
                    $order->DELIVERY_ADDRESS_LINE_1 = $to_address->ADDRESS_LINE_1 ?? '';
                    $order->DELIVERY_ADDRESS_LINE_2 = $to_address->ADDRESS_LINE_2 ?? '';
                    $order->DELIVERY_ADDRESS_LINE_3 = $to_address->ADDRESS_LINE_3 ?? '';
                    $order->DELIVERY_ADDRESS_LINE_4 = $to_address->ADDRESS_LINE_4 ?? '';
                    $order->DELIVERY_CITY           = $to_address->city->CITY_NAME ?? '';
                    $order->DELIVERY_STATE          = $to_address->state->STATE_NAME ?? '';
                    $order->DELIVERY_POSTCODE       = $to_address->POST_CODE ?? '';
                    $order->DELIVERY_COUNTRY        = $to_address->country->NAME ?? '';
                    $order->DELIVERY_F_COUNTRY_NO   = $to_address->F_COUNTRY_NO ?? '';

                }else{
                    $from_address                   = Reseller::where('PK_NO',$booking->F_RESELLER_NO)->first();
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
                $booking->RECONFIRM_TIME        = date('Y-m-d h:i:s');
                $booking->BOOKING_STATUS        = 80;
                $booking->IS_BUNDLE_MATCHED     = 1;
                $booking->TOTAL_PRICE           = $request->total_price;
                $booking->save();
                Stock::whereIn('PK_NO',$booking_details)->update(['F_ORDER_NO' => $order->PK_NO,'ORDER_STATUS' => 10]);

            } catch (\Exception $e) {
                DB::rollback();
                return $this->formatResponse(false, $e->getMessage(), 'admin.booking.list');
            }
            DB::commit();
        return $this->formatResponse(true, 'Successfully offer applyed !', 'admin.booking.list');
    }

    */

}
