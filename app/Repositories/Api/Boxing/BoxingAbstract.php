<?php
namespace App\Repositories\Api\Boxing;

use DB;
use App\Models\Box;
use App\Models\Stock;
use App\Models\Shipment;
use App\Models\Shipmentbox;
use App\Traits\ApiResponse;
use App\Models\BookingDetails;
use App\Models\BoxType;
use App\Models\SmsNotification;
use App\Models\EmailNotification;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;

class BoxingAbstract implements BoxingInterface
{
    use ApiResponse;

    function __construct() {}

    public function getProductBox($request)
    {
        $data               = $request->json()->all();
        $string             = '';
        $response_code      = 200;
        $response_msg       = 'Boxing unsuccessfull !';
        $response_data      = null;
        $response_status    = 0;
        $col_separatior     = '~' ;
        $row_separatior     = '|' ;
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $data['user_id'])->first();

        for ($loop = 0; $loop < count($data['data']); $loop++) {
           $string .= $data['data'][$loop]['sku_id'].'~'.$user_map->F_INV_WAREHOUSE_NO.'~'.$data['data'][$loop]['qty'].'|';
        }

        $box_label  = $data['box_label'];
        $count      = count($data['data']);
        $user_id    = $data['user_id'];
        $is_update  = $data['is_update'];
        $width      = $data['width'] ?? null;
        $length     = $data['length'] ?? null;
        $height     = $data['height'] ?? null;
        $weight     = $data['weight'] ?? null;
        $col_parameters = 3;

        DB::beginTransaction();

        try {
            //$is_update = 0 => Add Item Into New Box
            //$is_update = 1 => Add Item Into Existing Box
            //$is_update = 2 => Remove Item From Existing Box
            // echo '<pre>';
            // echo '======================<br>';
            // print_r($string);
            // echo '<br>======================<br>';
            // exit();
            if ($is_update == 0) {

                DB::statement('CALL PROC_SC_BOX_INV_STOCK(:box_label, :string, :count, :col_parameters, :col_separatior, :row_separatior, :user_id, :is_update, :width, :length, :height, :weight, @OUT_STATUS);',
                    array(
                        $box_label
                        ,$string
                        ,$count
                        ,$col_parameters
                        ,$col_separatior
                        ,$row_separatior
                        ,$user_id
                        ,$is_update
                        ,$width
                        ,$length
                        ,$height
                        ,$weight
                    )
                );
            }else if ($is_update == 1){

                DB::statement('CALL PROC_SC_BOX_INV_STOCK_ITEM_ADD(:box_label, :string, :count, :col_parameters, :col_separatior, :row_separatior, :user_id, :is_update, :width, :length, :height, :weight, @OUT_STATUS);',
                    array(
                        $box_label
                        ,$string
                        ,$count
                        ,$col_parameters
                        ,$col_separatior
                        ,$row_separatior
                        ,$user_id
                        ,$is_update
                        ,$width
                        ,$length
                        ,$height
                        ,$weight
                    )
                );
            }else if ($is_update == 2){
                DB::statement('CALL PROC_SC_BOX_INV_STOCK_ITEM_REMOVE(:box_label, :string, :count, :col_parameters, :col_separatior, :row_separatior, :user_id, :is_update, :width, :length, :height, :weight, @OUT_STATUS);',
                    array(
                        $box_label
                        ,$string
                        ,$count
                        ,$col_parameters
                        ,$col_separatior
                        ,$row_separatior
                        ,$user_id
                        ,$is_update
                        ,$width
                        ,$length
                        ,$height
                        ,$weight
                    )
                );
            }
            $prc = DB::select('select @OUT_STATUS as OUT_STATUS');
            if ($prc[0]->OUT_STATUS == 'success') {

                $response_code      = 200;
                $response_msg       = $is_update == 0 ? 'Boxing successfull !' : ($is_update == 1 ? 'Item Added successfully !' : 'Item Removed successfully !');
                $response_data      = null;
                $response_status    = 1;

            }elseif ($prc[0]->OUT_STATUS == 'duplicate-box') {

               $response_code      = 200;
               $response_msg       = 'Duplicate Box ! Try Unbox';
               $response_data      = null;
               $response_status    = 0;

            }elseif ($prc[0]->OUT_STATUS == 'failed-partial') {

                $response_code      = 200;
                $response_msg       = 'Partial Product Quantity Exeeded !';
                $response_data      = null;
                $response_status    = 0;

            }elseif($prc[0]->OUT_STATUS == 'failed'){
               $response_code      = 200;
               $response_msg       = 'Boxing failed !';
               $response_data      = null;
               $response_status    = 0;
            }elseif($prc[0]->OUT_STATUS == 'box-not-found'){
                $response_code      = 200;
                $response_msg       = 'Box Not Found !';
                $response_data      = null;
                $response_status    = 0;
             }else{
               $response_code      = 200;
               $response_msg       = 'Boxing unsuccessfull !';
               $response_data      = null;
               $response_status    = 0;
            }

        } catch (\Throwable $e) {

           return $this->successResponse(200, $e->getMessage(), null, 0);
        }

        DB::commit();

        return $this->successResponse($response_code, $response_msg, $response_data, $response_status);
    }


    public function getRebox($request)
    {
        $box = DB::table('SC_BOX')->select('PK_NO','F_INV_WAREHOUSE_NO','BOX_STATUS','WIDTH_CM as width','LENGTH_CM as length','HEIGHT_CM as height','WEIGHT_KG as weight')->where('BOX_NO',$request->box_label)->first();
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();

        if (empty($box)) {
            return $this->successResponse(200, 'Box not found !', null, 0);
        }else if ($user_map->F_INV_WAREHOUSE_NO != $box->F_INV_WAREHOUSE_NO) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }else if ($user_map->F_INV_WAREHOUSE_NO == $box->F_INV_WAREHOUSE_NO && $box->BOX_STATUS > 20) {
            return $this->successResponse(200, 'Can not rebox at this moment!', null, 0);
        }

        $get_ig = Stock::selectRaw('(SELECT IFNULL(COUNT(IG_CODE),0) from INV_STOCK where IG_CODE = mkt_id and F_INV_WAREHOUSE_NO = '.$box->F_INV_WAREHOUSE_NO.' and (F_BOX_NO IS NULL OR F_BOX_NO = 0))')->limit(1)->getQuery();

        $data = DB::table('INV_STOCK as s')
                ->select('v.PK_NO'
                ,'s.INV_WAREHOUSE_NAME','v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image'
                , DB::raw('IFNULL(count(s.PK_NO),0) as given_qty')
                , DB::raw('(CASE WHEN s.FINAL_PREFFERED_SHIPPING_METHOD = "AIR" THEN 1 ELSE 0 END) AS is_air')
                )
                ->selectSub($get_ig, 'available_qty')
                ->join('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE')
                ->leftjoin('SC_BOX as b', 'b.PK_NO', 's.F_BOX_NO')
                ->where('b.F_INV_WAREHOUSE_NO','=', $user_map->F_INV_WAREHOUSE_NO)
                ->where('s.F_BOX_NO', $box->PK_NO)
                ->whereRaw('( s.PRODUCT_STATUS = 20) ')
                // ->whereRaw('( '.$user_map->F_INV_WAREHOUSE_NO.' = '.$box->F_INV_WAREHOUSE_NO.') ')
                ->groupBy('s.IG_CODE')
                ->get();

        if (count($data)>0) {
            $response = $this->successResponse(200, 'Product found !', $data, 1);
            // $response .= (object)array('width' => $box->width);
            $response->width = $box->width;
            $response->length = $box->length;
            $response->height = $box->height;
            $response->weight = $box->weight;
            return $response;
        }

        return $this->successResponse(200, 'Data Not Found !', null, 0);
    }

    /*################## UNBOX LIST FOR FIRST PAGE LOAD #######################*/
    public function getUnboxList($request)
    {
        $box_no = Box::select('PK_NO','F_INV_WAREHOUSE_NO','BOX_STATUS')->where('BOX_NO',$request->box_label)->first();
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($box_no)) {
            return $this->successResponse(200, 'Box not found !', null, 0);
        }

        $shipment_no = Shipmentbox::select('F_SHIPMENT_NO')->where('F_BOX_NO', $box_no->PK_NO)->first();
        if (empty($shipment_no)) {
            return $this->successResponse(200, 'Shipment not found !', null, 0);
        }

        $shipment_status = Shipment::select('SHIPMENT_STATUS','F_TO_INV_WAREHOUSE_NO')->where('PK_NO', $shipment_no->F_SHIPMENT_NO)->first();

        if ($user_map->F_INV_WAREHOUSE_NO != $shipment_status->F_TO_INV_WAREHOUSE_NO) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        // else if($shipment_status->SHIPMENT_STATUS < 80){
        //     return $this->successResponse(200, 'Box not arrived at destination !', null, 0);
        // }
        else if($box_no->BOX_STATUS < 50){
                return $this->successResponse(200, 'Box not ready to unbox !', null, 0);
        }
        $data = DB::table('INV_STOCK as s')
                    ->select('v.PK_NO','s.INV_WAREHOUSE_NAME','v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image', DB::raw('IFNULL(count(s.PK_NO),0) as given_qty'))
                    ->join('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE')
                    ->leftjoin('SC_BOX as b', 'b.PK_NO', 's.F_BOX_NO')
                    ->where('b.F_INV_WAREHOUSE_NO','=', $user_map->F_INV_WAREHOUSE_NO)
                    ->where('s.F_BOX_NO', $box_no->PK_NO)
                    ->whereRaw('(s.PRODUCT_STATUS = 50)')
                    ->groupBy('s.IG_CODE')->get();

        if (count($data)>0) {
            return $this->successResponse(200, 'Product found !', $data, 1);
        }

        return $this->successResponse(200, 'Data not found !', null, 0);
    }

    /*############### UNBOXING #####################*/
    public function getUnbox($request)
    {
        $box_no = Box::select('PK_NO','F_INV_WAREHOUSE_NO','BOX_STATUS')->where('BOX_NO',$request->box_label)->first();
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($box_no)) {
            return $this->successResponse(200, 'Box not found !', null, 0);
        }

        $shipment_no = Shipmentbox::select('F_SHIPMENT_NO')->where('F_BOX_NO', $box_no->PK_NO)->first();
        if (empty($shipment_no)) {
            return $this->successResponse(200, 'Shipment not found !', null, 0);
        }

        $shipment_status = Shipment::select('PK_NO','SHIPMENT_STATUS','F_TO_INV_WAREHOUSE_NO')->where('PK_NO', $shipment_no->F_SHIPMENT_NO)->first();

        if ($user_map->F_INV_WAREHOUSE_NO != $shipment_status->F_TO_INV_WAREHOUSE_NO) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        // else if($shipment_status->SHIPMENT_STATUS < 80){
        //     return $this->successResponse(200, 'Box not arrived at destination warehouse !', null, 0);
        // }
        else if($box_no->BOX_STATUS < 50){
                return $this->successResponse(200, 'Box not ready to unbox !', null, 0);
        }
        DB::beginTransaction();
        try {
            $stock = Stock::where('F_BOX_NO', $box_no->PK_NO)
            ->where('SKUID',$request->sku_id)
            ->where('PRODUCT_STATUS',50)
            ->where('F_SHIPPMENT_NO',$shipment_no->F_SHIPMENT_NO)
            ->orderBy('F_BOOKING_NO','DESC')
            ->limit($request->qty)
            ->pluck('PK_NO');

            Stock::whereIn('PK_NO',$stock)->update(['PRODUCT_STATUS' => 60
            ,'F_INV_WAREHOUSE_NO' => $shipment_status->F_TO_INV_WAREHOUSE_NO
            ,'INV_WAREHOUSE_NAME' => $shipment_status->to_warehouse->NAME]);

            $product_count = Stock::where('F_BOX_NO',$box_no->PK_NO)->where('PRODUCT_STATUS',50)->where('F_SHIPPMENT_NO',$shipment_no->F_SHIPMENT_NO)->count();
            if ($product_count == 0) {
                Box::where('PK_NO', $box_no->PK_NO)->update(['BOX_STATUS' => 60, 'F_INV_WAREHOUSE_NO' => $shipment_status->F_TO_INV_WAREHOUSE_NO,'F_BOX_USER_NO' => $request->user_id]);
            }

            $booking_details_ids = Stock::join('SLS_BOOKING_DETAILS','SLS_BOOKING_DETAILS.F_INV_STOCK_NO','=','INV_STOCK.PK_NO')
            ->where('INV_STOCK.F_BOX_NO', $box_no->PK_NO)
            ->where('INV_STOCK.SKUID',$request->sku_id)
            ->where('INV_STOCK.PRODUCT_STATUS',60)
            ->where('INV_STOCK.F_SHIPPMENT_NO',$shipment_no->F_SHIPMENT_NO)
            ->orderBy('INV_STOCK.F_BOOKING_NO','DESC')
            ->limit($request->qty)
            ->pluck('SLS_BOOKING_DETAILS.PK_NO');
            // echo '<pre>';
            // echo '======================<br>';
            // print_r($booking_details_ids);
            // echo '<br>======================<br>';
            // exit();
                if(!empty($booking_details_ids)){
                    $booking_details = BookingDetails::whereIn('PK_NO',$booking_details_ids)->get();

                    if(!empty($booking_details)){
                        foreach ($booking_details as $key => $value) {

                            $query = BookingDetails::where('F_BOOKING_NO',$value->F_BOOKING_NO)->sum('ARRIVAL_NOTIFICATION_FLAG');

                            if($query > 1){
                                $sms_body = "RM0.00 AZURAMART: Order #ORD-".$value->booking->BOOKING_NO." has arrived(partially) at Malaysia & ready to post, for more info please WhatsApp http://linktr.ee/azuramart";
                            }else{
                                $sms_body = "RM0.00 AZURAMART: Order #ORD-".$value->booking->BOOKING_NO." has arrived at Malaysia & ready to post, for more info please WhatsApp http://linktr.ee/azuramart";
                            }
                            $check =  SmsNotification::where('F_BOOKING_NO', $value->F_BOOKING_NO)->where('IS_SEND', 0)->first();
                            $email =  EmailNotification::where('F_BOOKING_NO', $value->F_BOOKING_NO)->where('IS_SEND', 0)->first();

                            if(!empty($check)){
                                SmsNotification::where('F_BOOKING_NO', $value->F_BOOKING_NO)->where('IS_SEND', 0)->update(['IS_SEND' => 2]);
                            }
                            if(!empty($email)){
                                EmailNotification::where('F_BOOKING_NO', $value->F_BOOKING_NO)->where('IS_SEND', 0)->update(['IS_SEND' => 2]);
                            }
                            $noti = new SmsNotification();
                            $noti->TYPE = 'Arrival';
                            $noti->F_BOOKING_NO = $value->F_BOOKING_NO;
                            $noti->F_BOOKING_DETAIL_NO = $value->PK_NO;
                            $noti->BODY = $sms_body;
                            $noti->F_SS_CREATED_BY = $request->user_id;
                            if($value->booking->IS_RESELLER == 0){
                                $noti->CUSTOMER_NO = $value->booking->F_CUSTOMER_NO;
                                $noti->IS_RESELLER = 0;
                            }else{
                                $noti->RESELLER_NO = $value->booking->F_RESELLER_NO;
                                $noti->IS_RESELLER = 1;
                            }
                            $noti->save();

                            $email = new EmailNotification();
                            $email->TYPE = 'Arrival';
                            $email->F_BOOKING_NO = $value->F_BOOKING_NO;
                            $email->F_BOOKING_DETAIL_NO = $value->PK_NO;
                            $email->F_SS_CREATED_BY = $request->user_id;
                            if($value->booking->IS_RESELLER == 0){
                                $email->CUSTOMER_NO = $value->booking->F_CUSTOMER_NO;
                                $email->IS_RESELLER = 0;
                            }else{
                                $email->RESELLER_NO = $value->booking->F_RESELLER_NO;
                                $email->IS_RESELLER = 1;
                            }
                            $email->save();
                            
                            BookingDetails::where('PK_NO',$value->PK_NO)->update(['ARRIVAL_NOTIFICATION_FLAG' => 0]);
                        }
                    }
                }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->successResponse(200, $e->getMessage(), null, 0);
        }
        DB::commit();
        /*#################### RETURNING LIST AFTER SINGLE UNBOXING ################*/
        $data = DB::table('INV_STOCK as s')
                ->select('v.PK_NO','s.INV_WAREHOUSE_NAME','v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image', DB::raw('IFNULL(count(s.PK_NO),0) as given_qty'))
                ->join('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE')
                ->leftjoin('SC_BOX as b', 'b.PK_NO', 's.F_BOX_NO')
                ->where('b.F_INV_WAREHOUSE_NO','=', $user_map->F_INV_WAREHOUSE_NO)
                ->where('s.F_BOX_NO', $box_no->PK_NO)
                ->whereRaw('( s.PRODUCT_STATUS = 50) ')
                ->groupBy('s.IG_CODE')->get();

        if ( !$data->isEmpty() || $product_count == 0) {
            return $this->successResponse(200, 'Unboxing successfull !', $data, 1);
        }
        return $this->successResponse(200, 'Data not found !', null, 0);
    }

    public function getPriorityUnboxList($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if ($user_map->F_INV_WAREHOUSE_NO != 2) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        $data = DB::table('INV_STOCK as s')
                ->select('b.PK_NO as box_no_','b.BOX_NO as box_label','b.ITEM_COUNT as total_item_count'
                , DB::raw('IFNULL(count(s.PK_NO),0) as priority_item_count')
                , DB::raw('(SELECT IFNULL(count(PK_NO),0) from INV_STOCK where PRODUCT_STATUS = 60 and PRODUCT_STATUS < 420 and F_BOX_NO = box_no_ ) as unboxed_qty')
                )
                ->join('SC_BOX as b', 'b.PK_NO', 's.F_BOX_NO')
                ->where('b.F_INV_WAREHOUSE_NO',2)
                ->whereRaw('( s.PRODUCT_STATUS = 50 )')
                ->whereRaw('( s.F_ORDER_NO IS NOT NULL )')
                ->groupBy('s.F_BOX_NO')
                ->get();

        if ( !$data->isEmpty()) {
            return $this->successResponse(200, 'Priority Unbox list !', $data, 1);
        }
        return $this->successResponse(200, 'Data not found !', null, 0);
    }


    public function postBoxList($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if(empty($user_map)){
            return $this->successResponse(200, 'Box Not Found !', null, 0);
        }

        $data = DB::table('SC_BOX AS b')
        ->leftjoin('SC_SHIPMENT_BOX as sb', 'sb.F_BOX_NO', 'b.PK_NO')
        ->select('b.PK_NO','b.CODE as box_no', 'b.BOX_NO as box_label','b.ITEM_COUNT as product_count','b.USER_NAME as user_name','b.BOX_STATUS as status','sb.F_SHIPMENT_NO as shipment_no_P','sb.BOX_SERIAL as box_serial','sb.F_BOX_NO as box_no_pk'
        ,DB::raw('(select NAME from INV_WAREHOUSE where INV_WAREHOUSE.PK_NO = b.F_INV_WAREHOUSE_NO) as warehouse')
        ,DB::raw('(select PK_NO from SC_SHIPMENT where shipment_no_P=PK_NO) as shipment_no')
        ,DB::raw('(select CODE from SC_SHIPMENT where shipment_no_P=PK_NO) as shipment_label')
        ,DB::raw('(select IFNULL(COUNT(PK_NO),0) from INV_STOCK where PRODUCT_STATUS = 60 and F_BOX_NO = box_no_pk) as unboxed_qty')
    );
        // ->where('b.F_INV_WAREHOUSE_NO',$user_map->F_INV_WAREHOUSE_NO)->where('b.BOX_STATUS', '<', 30);
        if($request->shipment_no && $request->shipment_no != 0) {
            $data = $data->where('sb.F_SHIPMENT_NO', $request->shipment_no);
        }else{
            $data = $data->where('b.BOX_STATUS','<',50);
        }
        $data = $data->orderBy('box_label','DESC')->get();
        $max_air = Box::where('BOX_NO','<',20000000)->max('BOX_NO');
        $max_sea = Box::max('BOX_NO');
        if (count($data)>0) {
            return $this->successResponseBoxList(200, 'Box is available !', $data,$max_air ?? 0,$max_sea ?? 0,1);
        }
        return $this->successResponseBoxList(200, 'Box Not Found !', null, 0);
    }

    public function postBoxListDetails($request)
    {
        $box_no = DB::table('SC_BOX')->select('PK_NO','F_INV_WAREHOUSE_NO')->where('PK_NO',$request->PK_NO)->first();
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($box_no)) {
            return $this->successResponse(200, 'Box not found !', null, 0);
        }
        // if ($user_map->F_INV_WAREHOUSE_NO != $box_no->F_INV_WAREHOUSE_NO) {
        //     return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        // }

        $get_ig = Stock::selectRaw('(SELECT IFNULL(COUNT(IG_CODE),0) from INV_STOCK where IG_CODE = mkt_id and F_INV_WAREHOUSE_NO = '.$box_no->F_INV_WAREHOUSE_NO.' and (F_BOX_NO IS NULL OR F_BOX_NO = 0))')->limit(1)->getQuery();

        $data = DB::table('INV_STOCK as s')
                ->select('v.PK_NO','s.INV_WAREHOUSE_NAME','v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image', DB::raw('IFNULL(count(s.PK_NO),0) as given_qty'),
                DB::raw('(CASE WHEN s.FINAL_PREFFERED_SHIPPING_METHOD = "AIR" THEN 1 ELSE 0 END) AS is_air'))
                ->selectSub($get_ig, 'available_qty')
                ->join('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE')
                ->where('s.F_BOX_NO', $box_no->PK_NO)
                // ->where('s.F_INV_WAREHOUSE_NO', $box_no->F_INV_WAREHOUSE_NO)
                ->groupBy('s.IG_CODE')->get();

        if (count($data)>0) {
            return $this->successResponse(200, 'Product found !', $data, 1);
        }
        return $this->successResponse(200, 'Data Not Found !', null, 0);


    }

    public function postYetToBox($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        $count_not_boxed = Stock::selectRaw('(SELECT IFNULL(COUNT(SKUID),0) from INV_STOCK where SKUID = sku_id and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and (F_BOX_NO IS NULL OR F_BOX_NO = 0 OR PRODUCT_STATUS IS NULL OR PRODUCT_STATUS = 0 OR PRODUCT_STATUS = 90 OR PRODUCT_STATUS < 420))')->limit(1)->getQuery();

        $data = DB::table('INV_STOCK as s')
                ->select('v.PK_NO','v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','s.INV_WAREHOUSE_NAME as warehouse')
                ->selectSub($count_not_boxed, 'available_qty')
                ->join('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE')
                ->where('s.F_INV_WAREHOUSE_NO', $user_map->F_INV_WAREHOUSE_NO)
                ->whereRaw('( s.PRODUCT_STATUS IS NULL OR s.PRODUCT_STATUS = 0 OR s.PRODUCT_STATUS = 90 OR s.PRODUCT_STATUS < 420) ')
                ->groupBy('s.IG_CODE', 's.F_INV_WAREHOUSE_NO')->get();

        if (count($data)>0) {
           return $this->successResponse(200, 'Product found !', $data, 1);
        }
        return $this->successResponse(200, 'Data Not Found !', null, 0);
    }

    public function postUnboxListItem($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        $data = DB::table('INV_STOCK as s')
                    ->select('v.PK_NO','s.INV_WAREHOUSE_NAME','v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','v.LOCAL_POSTAGE as sm_price','v.INTER_DISTRICT_POSTAGE as ss_price'
                    , DB::raw('IFNULL(count(s.PK_NO),0) as available_qty'), 'b.BOX_NO')
                    ->join('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE')
                    ->join('SC_BOX as b', 'b.PK_NO', 's.F_BOX_NO')
                    ->where('s.F_INV_WAREHOUSE_NO', $user_map->F_INV_WAREHOUSE_NO)
                    ->whereRaw('(s.F_INV_ZONE_NO IS NULL and s.F_BOX_NO IS NOT NULL and s.F_SHIPPMENT_NO IS NOT NULL and s.PRODUCT_STATUS = 60)')
                    ->groupBy('s.IG_CODE')->get();

        if (count($data)>0) {
            return $this->successResponse(200, 'Product found !', $data, 1);
        }

        return $this->successResponse(200, 'Data not found !', null, 0);
    }

    public function postBoxLabelUpdate($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        $dup_box = Box::where('BOX_NO',$request->box_label)->count();
        if ($dup_box > 0) {
            return $this->successResponse(200, 'Duplicate Box Label !', null, 0);
        }
        $box_type = substr($request->box_label, 0, 1);
        if ($box_type == 1) {
            $box_type = 'AIR';
        }else{
            $box_type = 'SEA';
        }
        DB::beginTransaction();

        try {
            Box::where('PK_NO',$request->PK_NO)->update(['BOX_NO'=> $request->box_label]);
            Stock::where('F_BOX_NO',$request->PK_NO)->update(['BOX_BARCODE'=> $request->box_label,'BOX_TYPE'=>$box_type]);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->successResponse(200, $e->getMessage(), null, 0);
        }

        DB::commit();

        return $this->successResponse(200, 'Box Label Updated !', null, 1);
    }

    public function postBoxLabelExists($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }

        $box = Box::select('PK_NO','F_INV_WAREHOUSE_NO','BOX_STATUS','WIDTH_CM as width','LENGTH_CM as length','HEIGHT_CM as height','WEIGHT_KG as weight')->where('BOX_NO',$request->box_label)->first();
        if (empty($box)) {
            return $this->successResponse(200, 'Box not found !', null, 0);
        }else if($box->BOX_STATUS >= 20){
            return $this->successResponse(200, 'Box already assigned to shipment !', null, 0);
        }

        $box_type = substr($request->box_label, 0, 1);
        if ($box_type == 1) {
            $box_type = 1;
        }else{
            $box_type = 0;
        }
        $data = (object)array('is_air' => $box_type,'width' => $box->width,'length' => $box->length,'height' => $box->height,'weight' => $box->weight);
        return $this->successResponse(200, 'Box is ready !', $data, 1);
    }

    /*############### UNBOXING PRIORITY LIST #####################*/
    public function priorityUnboxListItem($request)
    {
        $box_no = Box::select('PK_NO','F_INV_WAREHOUSE_NO','BOX_STATUS')->where('BOX_NO',$request->box_label)->first();
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($box_no)) {
            return $this->successResponse(200, 'Box not found !', null, 0);
        }

        $shipment_no = Shipmentbox::select('F_SHIPMENT_NO')->where('F_BOX_NO', $box_no->PK_NO)->first();
        if (empty($shipment_no)) {
            return $this->successResponse(200, 'Shipment not found !', null, 0);
        }

        $shipment_status = Shipment::select('SHIPMENT_STATUS','F_TO_INV_WAREHOUSE_NO')->where('PK_NO', $shipment_no->F_SHIPMENT_NO)->first();

        if ($user_map->F_INV_WAREHOUSE_NO != $shipment_status->F_TO_INV_WAREHOUSE_NO) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        // else if($shipment_status->SHIPMENT_STATUS < 80){
        //     return $this->successResponse(200, 'Box not arrived at destination !', null, 0);
        // }
        else if($box_no->BOX_STATUS < 50){
                return $this->successResponse(200, 'Box not ready to unbox !', null, 0);
        }
        $data = DB::table('INV_STOCK as s')
                    ->select('v.PK_NO','s.INV_WAREHOUSE_NAME','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image'
                    , DB::raw('IFNULL(count(s.PK_NO),0) as given_qty'))
                    ->join('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE')
                    ->leftjoin('SC_BOX as b', 'b.PK_NO', 's.F_BOX_NO')
                    ->where('b.F_INV_WAREHOUSE_NO',2)
                    ->where('s.F_BOX_NO', $box_no->PK_NO)
                    ->whereRaw('(s.PRODUCT_STATUS = 50)')
                    ->whereRaw('( s.F_ORDER_NO IS NOT NULL )')
                    ->groupBy('s.IG_CODE')->get();

        if (count($data)>0) {
            return $this->successResponse(200, 'Product found !', $data, 1);
        }
        return $this->successResponse(200, 'Data not found !', null, 0);
    }

    public function getUnboxingBoxList($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if ($user_map->F_INV_WAREHOUSE_NO != 2) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        $data = DB::table('INV_STOCK as s')
                ->select('b.PK_NO as box_no_','b.BOX_NO as box_label','b.ITEM_COUNT as total_item_count'
                ,DB::raw('(SELECT IFNULL(count(PK_NO),0) from INV_STOCK where F_INV_WAREHOUSE_NO = 1 and PRODUCT_STATUS = 50 and F_BOX_NO = box_no_ and F_ORDER_NO IS NOT NULL ) as priority_item_count')
                ,DB::raw('(SELECT IFNULL(count(PK_NO),0) from INV_STOCK where PRODUCT_STATUS = 60 and PRODUCT_STATUS < 420 and F_BOX_NO = box_no_ ) as unboxed_qty')
                )
                ->join('SC_BOX as b', 'b.PK_NO', 's.F_BOX_NO')
                ->where('b.F_INV_WAREHOUSE_NO',2)
                ->whereRaw('( b.BOX_STATUS = 50 )')
                // ->whereRaw('( s.PRODUCT_STATUS = 50 )')
                ->groupBy('s.F_BOX_NO')->get();

        if (count($data)>0) {
            return $this->successResponse(200, 'Unbox list !', $data, 1);
        }
        return $this->successResponse(200, 'Data not found !', null, 0);
    }

    public function getBoxDimention($request)
    {
        $data = DB::table('SC_BOX_TYPE as b')
                ->select('b.WIDTH_CM as width','b.LENGTH_CM as length','b.HEIGHT_CM as height','b.TYPE')
                ->where('b.IS_ACTIVE',1)
                ->get();
        foreach ($data as $key => $value) {
            $value->dimension_text = 'Size '.$value->TYPE.' = '.$value->width.' X '.$value->length.' X '.$value->height;
        }
        if (count($data)>0) {
            return $this->successResponse(200, 'Data found !', $data, 1);
        }
        return $this->successResponse(200, 'Data not found !', null, 0);
    }
}
