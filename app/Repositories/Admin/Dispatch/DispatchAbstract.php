<?php
namespace App\Repositories\Admin\Dispatch;

use DB;
use Auth;
use App\Models\Agent;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Dispatch;
use App\Models\PaymentBank;
use App\Traits\RepoResponse;
use App\Models\WarehouseZone;
use App\Models\BookingDetails;
use App\Models\DispatchDetails;
use App\Models\PaymentCustomer;
use App\Models\PaymentReseller;
use App\Models\SmsNotification;
use App\Models\OrderConsignment;
use App\Models\EmailNotification;
use App\Models\CustomerAddressType;

class DispatchAbstract implements DispatchInterface
{
    use RepoResponse;

    protected $agent;
    protected $order;
    protected $booking;
    protected $address_type;
    protected $country;
    protected $dispatch;

    public function __construct(Agent $agent,Booking $booking, Order $order,CustomerAddressType $address_type, Country $country, Dispatch $dispatch)
    {
        $this->agent   = $agent;
        $this->booking = $booking;
        $this->order   = $order;
        $this->address_type = $address_type;
        $this->country = $country;
        $this->dispatch = $dispatch;
    }

    public function getPaginatedList($request, int $per_page = 50)
    {
        $query = $this->dispatch->select('SC_ORDER_DISPATCH.*','o.DISPATCH_STATUS','bd.IS_COLLECTED_FOR_RTS')
                    ->leftjoin('SLS_ORDER as o','o.PK_NO','SC_ORDER_DISPATCH.F_ORDER_NO')
                    ->leftjoin('SLS_BOOKING as b','b.PK_NO','o.F_BOOKING_NO')
                    ->leftjoin('SLS_BOOKING_DETAILS as bd','o.F_BOOKING_NO','bd.F_BOOKING_NO')
                    ->where('o.DISPATCH_STATUS','>=',40)
                    ->where('SC_ORDER_DISPATCH.IS_DISPATHED',1);
                    if ($request->get('type') == 'returned') {
                        $query = $query->where('b.IS_RETURN','!=',0);
                    }
                    $query = $query->groupBy('SC_ORDER_DISPATCH.PK_NO')
                    ->orderBy('SC_ORDER_DISPATCH.DISPATCH_DATE', 'DESC');

                if($request->keywords != ''){
                    $pieces = explode(" ", $request->keywords);
                    if($pieces){
                        foreach ($pieces as $key => $piece) {
                            $query->where('o.CUSTOMER_NAME', 'LIKE', '%' . $piece . '%');
                            $query->Where('o.CUSTOMER_NAME', 'LIKE', '%' . $piece . '%');
                        }
                        foreach ($pieces as $key => $piece) {
                            $query->orWhere('o.RESELLER_NAME', 'LIKE', '%' . $piece . '%');
                            $query->orWhere('o.RESELLER_NAME', 'LIKE', '%' . $piece . '%');
                        }
                        foreach ($pieces as $key => $piece) {
                            $query->orWhere('b.BOOKING_NO', 'LIKE', '%' . $piece . '%');
                            $query->orWhere('b.BOOKING_NO', 'LIKE', '%' . $piece . '%');
                        }
                        foreach ($pieces as $key => $piece) {
                            $query->orWhere('SC_ORDER_DISPATCH.COURIER_TRACKING_NO', 'LIKE', '%' . $piece . '%');
                            $query->orWhere('SC_ORDER_DISPATCH.COURIER_TRACKING_NO', 'LIKE', '%' . $piece . '%');
                        }
                    }
                }
                if($request->from_date && $request->to_date){
                    $query->where('SC_ORDER_DISPATCH.DISPATCH_DATE','>=', date('Y-m-d',strtotime($request->from_date)));
                    $query->where('SC_ORDER_DISPATCH.DISPATCH_DATE','<=', date('Y-m-d',strtotime($request->to_date)));
                }

        $data = $query->paginate($per_page);
        return $this->formatResponse(true, 'Data found successfully !', 'admin.dispatched.list', $data);
    }

    public function getAppPendingDispatchList()
    {
        $data =  $this->dispatch->select('SC_ORDER_DISPATCH.*','o.DISPATCH_STATUS','bd.IS_COLLECTED_FOR_RTS','bd.F_BOOKING_NO')
                    ->leftjoin('SLS_ORDER as o','o.PK_NO','SC_ORDER_DISPATCH.F_ORDER_NO')
                    ->leftjoin('SLS_BOOKING_DETAILS as bd','o.F_BOOKING_NO','bd.F_BOOKING_NO')
                    // ->where('o.DISPATCH_STATUS','>=',40)
                    ->whereRaw('(SC_ORDER_DISPATCH.IS_DISPATHED = 0 OR SC_ORDER_DISPATCH.IS_DISPATHED = 2)')
                    ->groupBy('SC_ORDER_DISPATCH.PK_NO')
                    ->orderBy('SC_ORDER_DISPATCH.DISPATCH_DATE', 'DESC')
                    ->get();
                    // echo '<pre>';
                    // echo '======================<br>';
                    // print_r($data);
                    // echo '<br>======================<br>';
                    // exit();

        return $this->formatResponse(true, 'Data found successfully !', 'admin.dispatched.list', $data);
    }

    public function getOrderForDisopatch($PK_NO)
    {
        $data       = array();
        $booking    = $this->booking->where('PK_NO',$PK_NO)
        // ->where('SLS_ORDER.IS_ADMIN_HOLD',0)
        // ->where('SLS_ORDER.IS_SYSTEM_HOLD',0)
        ->first();
        $data['booking']            = $booking;
        return $this->formatResponse(true, 'Data found successfully !', 'admin.booking.list', $data);
    }


    public function postStore($request)
    {
        DB::beginTransaction();
        try {

        if($request->dispatch_type == 'rts'){
            $first_cnsignment = OrderConsignment::find($request->consignment_note[0]);
            $courier = DB::table('SC_COURIER')->where('PK_NO',$first_cnsignment->F_COURIER_NO)->first();
        }
        $booking = Booking::find($request->booking_no);
        $order =  $booking->getOrder;

        $dispatch                       = new Dispatch();
        $dispatch->F_ORDER_NO           = $order->PK_NO;
        if($request->dispatch_type == 'rts'){
            $dispatch->F_COURIER_NO     = $courier->PK_NO ?? null;
            $dispatch->COURIER_NAME     = $courier->COURIER_NAME ?? null;
            $dispatch->COURIER_TRACKING_NO  = $first_cnsignment->COURIER_TRACKING_NO;
        }
        $dispatch->F_DISPATCH_BY_USER_NO = Auth::user()->PK_NO;
        $dispatch->DISPATCH_USER_NAME    = Auth::user()->USERNAME;
        $dispatch->DISPATCH_DATE        = date('Y-m-d',strtotime($request->dispatch_date));
        $dispatch->FROM_NAME            = $order->FROM_NAME;
        $dispatch->FROM_TEL_NO          = $order->FROM_MOBILE;
        $dispatch->FROM_ADDRESS_LINE_1  = $order->FROM_ADDRESS_LINE_1;
        $dispatch->FROM_ADDRESS_LINE_2  = $order->FROM_ADDRESS_LINE_2;
        $dispatch->FROM_ADDRESS_LINE_3  = $order->FROM_ADDRESS_LINE_3;
        $dispatch->FROM_ADDRESS_LINE_4  = $order->FROM_ADDRESS_LINE_4;
        $dispatch->FROM_STATE           = $order->FROM_STATE;
        $dispatch->FROM_CITY            = $order->FROM_CITY;
        $dispatch->FROM_POST_CODE       = $order->FROM_POSTCODE;
        $dispatch->FROM_F_COUNTRY_NO    = $order->FROM_F_COUNTRY_NO;
        $dispatch->FROM_COUNTRY         = $order->FROM_COUNTRY;

        $dispatch->TO_NAME              = $order->DELIVERY_NAME;
        $dispatch->TO_TEL_NO            = $order->DELIVERY_MOBILE;
        $dispatch->TO_ADDRESS_LINE_1    = $order->DELIVERY_ADDRESS_LINE_1;
        $dispatch->TO_ADDRESS_LINE_2    = $order->DELIVERY_ADDRESS_LINE_2;
        $dispatch->TO_ADDRESS_LINE_3    = $order->DELIVERY_ADDRESS_LINE_3;
        $dispatch->TO_ADDRESS_LINE_4    = $order->DELIVERY_ADDRESS_LINE_4;
        $dispatch->TO_STATE             = $order->DELIVERY_STATE;
        $dispatch->TO_CITY              = $order->DELIVERY_CITY;
        $dispatch->TO_POST_CODE         = $order->DELIVERY_POSTCODE;
        $dispatch->TO_F_COUNTRY_NO      = $order->DELIVERY_F_COUNTRY_NO;
        $dispatch->TO_COUNTRY           = $order->DELIVERY_COUNTRY;
        $dispatch->COLLECTED_BY         = $request->collected_by;
        $dispatch->CREATED_AT           = date('Y-m-d h:i:s');
        if($request->submit == 'app_dispatch'){
            $dispatch->IS_DISPATHED         = 0;
            $msg = 'Assign for App Dispatch';
        }else{
            $msg = 'Dispatch successfull !';
        }

        $dispatch->save();

        if($request->booking_details_no){

            foreach ($request->booking_details_no as $key => $value) {
                if($request->dispatch_type == 'rts'){
                    $consignment = OrderConsignment::find($request->consignment_note[$key]);
                }
                if($request->dispatch_qty[$key] > 0 ){
                    $child = new DispatchDetails();
                    $child->F_SC_ORDER_DISPATCH_NO  = $dispatch->PK_NO;
                    $child->F_BOOKING_DETAILS_NO    = $value;
                    if($request->dispatch_type == 'rts'){
                        $child->COURIER_TRACKING_NO     = $consignment->COURIER_TRACKING_NO;
                    }
                    if($request->submit == 'app_dispatch'){
                        $child->IS_DISPATHED         = 0;
                    }
                    $child->save();
                    $booking_details = BookingDetails::select('F_INV_STOCK_NO')->where('PK_NO',$value)->first();
                    BookingDetails::where('PK_NO',$value)->update(['DISPATCH_STATUS' => 40]);

                    Stock::where('PK_NO', $booking_details->F_INV_STOCK_NO)->update(['ORDER_STATUS' => 80]);

                    //IF PRODUCT IS ON SHELVE THEN CHECKOUT
                    $shelve = Stock::select('INV_WAREHOUSE_ZONES.ITEM_COUNT','INV_WAREHOUSE_ZONES.PK_NO')
                                    ->join('INV_WAREHOUSE_ZONES','INV_WAREHOUSE_ZONES.PK_NO','INV_STOCK.F_INV_ZONE_NO')
                                    ->where('INV_STOCK.PK_NO',$booking_details->F_INV_STOCK_NO)
                                    ->first();
                    if (!empty($shelve)) {
                        Stock::where('PK_NO',$booking_details->F_INV_STOCK_NO)->update(['INV_ZONE_BARCODE'=>null,'F_INV_ZONE_NO'=>null,'ZONE_CHECK_OUT_BY'=>Auth::user()->PK_NO,'ZONE_CHECK_OUT_BY_NAME'=>Auth::user()->USERNAME]);
                        WarehouseZone::where('PK_NO',$shelve->PK_NO)->update(['ITEM_COUNT'=>$shelve->ITEM_COUNT-1]);
                        DB::table('INV_WAREHOUSE_ZONE_STOCK_ITEM')->where('F_INV_STOCK_NO',$booking_details->F_INV_STOCK_NO)->delete();
                    }
                }
            }
        }

        $data = DB::table('SLS_BOOKING_DETAILS')
        ->select(DB::raw("GROUP_CONCAT(DISPATCH_STATUS) as DISPATCH_STATUS"), DB::raw("COUNT(*) AS COUNTER"))
        ->groupBy('F_BOOKING_NO')
        ->where('F_BOOKING_NO',$request->booking_no)
        ->first();


        $dispatch_status = $data->DISPATCH_STATUS;
        $dispatch_status_arr = explode(',',$dispatch_status);
        $dispatch_status_arr_count = array_count_values($dispatch_status_arr);
        if(isset($dispatch_status_arr_count[40])){
            if($dispatch_status_arr_count[40] == $data->COUNTER){

                Order::where('F_BOOKING_NO',$request->booking_no)->update(['DISPATCH_STATUS' => 40]);

            }else{
                /*Partially dispatched*/
                Order::where('F_BOOKING_NO',$request->booking_no)->update(['DISPATCH_STATUS' => 35]);
            }
        }
        if($request->submit != 'app_dispatch'){
            if($request->dispatch_type == 'rts'){
                $tracking_info = $first_cnsignment->COURIER_TRACKING_NO;
                $sms_body = "RM0.00 AZURAMART: Order #ORD-".$booking->BOOKING_NO." has dispatched, ".$tracking_info.", for more info please Whatsapp http://linktr.ee/azuramart";
            }else{
                $sms_body = "RM0.00 AZURAMART: Order #ORD-".$booking->BOOKING_NO." has dispatched, for more info please Whatsapp http://linktr.ee/azuramart";
            }

            $noti = new SmsNotification();
            $noti->TYPE = 'Dispatch';
            $noti->F_BOOKING_NO = $booking->PK_NO;
            //$noti->F_BOOKING_DETAIL_NO = $value->PK_NO;
            $noti->BODY = $sms_body;
            $noti->F_SS_CREATED_BY = Auth::user()->PK_NO;
            if($booking->IS_RESELLER == 0){
                $noti->CUSTOMER_NO = $booking->F_CUSTOMER_NO;
                $noti->IS_RESELLER = 0;
            }else{
                $noti->RESELLER_NO = $booking->F_RESELLER_NO;
                $noti->IS_RESELLER = 1;
            }
            $noti->save();

            $email = new EmailNotification();
            $email->TYPE = 'Dispatch';
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

            //SPECIAL NOTE IS READ OR NOT
            if($request->m_note){
                $booking->IS_READ_BOOKING_NOTES = 1;
                $booking->READ_BY_BOOKING_NOTES = Auth::user()->PK_NO;
                $booking->update();
            }

        }


    } catch (\Exception $e) {
    dd($e);
        DB::rollback();
        return $this->formatResponse(false,'Dispatch not successfull ','admin.order.list');
    }
    DB::commit();
    return $this->formatResponse(true,$msg,'admin.order.list',1,$order->PK_NO);
        // $details->POSTAGE_COST = '';
        // $details->IS_POSTAGE_USED = '';
        // $details->F_AC_COURIER_BILL_NO = '';
    }

    public function getRevertDispatch($id)
    {
        DB::beginTransaction();
        try {
            $booking        = Booking::find($id);
            $order          = $booking->getOrder;
            $dispatch_pk    = Dispatch::select('PK_NO')->where('F_ORDER_NO',$order->PK_NO)->first();
            OrderConsignment::where('F_ORDER_NO',$order->PK_NO)->delete();
            DispatchDetails::where('F_SC_ORDER_DISPATCH_NO',$dispatch_pk->PK_NO)->delete();
            Dispatch::where('F_ORDER_NO',$order->PK_NO)->delete();
            BookingDetails::where('F_BOOKING_NO',$id)->update(['DISPATCH_STATUS' => 30]);
            Stock::where('F_BOOKING_NO', $id)->update(['ORDER_STATUS' => 60]);
            Order::where('F_BOOKING_NO',$id)->update(['DISPATCH_STATUS' => 30]);
            SmsNotification::where('F_BOOKING_NO',$id)->where('TYPE','Dispatch')->where('IS_SEND',0)->delete();
            EmailNotification::where('F_BOOKING_NO',$id)->where('TYPE','Dispatch')->where('IS_SEND',0)->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false,$e->getMessage(),'admin.dispatch.list',0);
        }
        DB::commit();
        return $this->formatResponse(true,'Order now ready for dispatch','admin.dispatch.list',1);
    }

    public function getRevertbatch($id)
    {
        DB::beginTransaction();
        try {
            Order::where('F_BOOKING_NO',$id)->update(['PICKUP_ID' => 0]);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false,$e->getMessage(),'admin.dispatch.list',0);
        }
        DB::commit();
        return $this->formatResponse(true,'Order is free from batch','admin.dispatch.list',1);
    }

    public function postMarkPickup($request)
    {
        DB::beginTransaction();
        try {
            $string             = '';
            $col_separatior     = '~' ;
            $response_status    = 0;
            foreach ($request->pickup_array as $key => $value) {
                $string .= $value.'~';
            }
            $count = count($request->pickup_array);

            DB::statement('CALL PROC_ORDER_RTS_COLLECT(:string, :count, :col_separatior, @OUT_STATUS);',
                array(
                    $string
                    ,$count
                    ,$col_separatior
                )
            );

            $prc = DB::select('select @OUT_STATUS as OUT_STATUS');

            // $prc = DB::select('CALL PROC_SC_BOX_INV_STOCK(?,?,?,?,?,?,?,?,?)', [ $box_label, $string, $count, 3, $column_separatior, $row_separatior, $user_id, $is_update, '@OUT_STATUS as tt']);
            if ($prc[0]->OUT_STATUS == 'success') {
               $response_status    = 1;
            }else{
                $response_status   = 0;
            }


            // $pickup_array   = $request->pickup_array;
            // $pickup_id      = Order::max('PICKUP_ID');
            // $pickup_id      = $pickup_id + 1;
           // Order::whereIn('PK_NO', $pickup_array)->update(['PICKUP_ID' => $pickup_id]);
        } catch (\Exception $e) {
            DB::rollback();
            return $response_status;
        }
        DB::commit();
        return $response_status;
    }

    public function postAssignOrderBulkItem($request)
    {
        DB::beginTransaction();
        try {
            $data = DB::table('SLS_BOOKING_DETAILS as bd')
            ->join('INV_STOCK as i','bd.F_INV_STOCK_NO','i.PK_NO')
            ->leftjoin('SLS_ORDER as o','o.F_BOOKING_NO','bd.F_BOOKING_NO')
            ->leftjoin('SLS_BATCH_LIST as bl','bl.PK_NO','o.PICKUP_ID')
            ->where('bl.PK_NO',$request['batch_id'])
            ->whereIn('i.SKUID',$request['bulk_product_array'])
            ->pluck('bd.PK_NO');

            BookingDetails::whereIn('PK_NO',$data)->update(['RTS_COLLECTION_USER_ID' => $request['user_id']]);

        } catch (\Exception $e) {
            DB::rollback();
            return 0;
        }
        DB::commit();
        return 1;
    }

    public function postSpecialNoteStatus($request)
    {
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($request->all());
        // echo '<br>======================<br>';
        // exit();
        DB::beginTransaction();
        try {
            if ($request->is_checked == 1) {
                $is_checked = 1;
            }else{
                $is_checked = 0;
            }
           Booking::where('PK_NO',$request->booking_no)->update(['IS_READ_BOOKING_NOTES'=>$is_checked,'READ_BY_BOOKING_NOTES'=>Auth::user()->PK_NO]);
        } catch (\Exception $e) {
            DB::rollback();
            return 0;
        }
        DB::commit();
        return 1;
    }

    public function getBatchCollectList()
    {
        $data = DB::table('SLS_ORDER as o')
                    ->select('bl.RTS_BATCH_NO as batch_no','o.F_BOOKING_NO as booking_no'
                    ,DB::raw('(
                        SELECT IFNULL(COUNT(INV_STOCK.PK_NO),0)
                        FROM INV_STOCK
                        LEFT JOIN SLS_BOOKING_DETAILS ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
                        LEFT JOIN SLS_ORDER ON SLS_BOOKING_DETAILS.F_BOOKING_NO = SLS_ORDER.F_BOOKING_NO
                        LEFT JOIN SLS_BATCH_LIST ON SLS_ORDER.PICKUP_ID = SLS_BATCH_LIST.PK_NO
                        WHERE SLS_BATCH_LIST.RTS_BATCH_NO = batch_no
                        and SLS_ORDER.DISPATCH_STATUS < 40
                        and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 0) as item_count')
                    ,DB::raw('(SELECT IFNULL(COUNT(SLS_ORDER.PK_NO),0)
                    FROM SLS_ORDER
                    LEFT JOIN SLS_BATCH_LIST ON SLS_ORDER.PICKUP_ID = SLS_BATCH_LIST.PK_NO
                    WHERE SLS_BATCH_LIST.RTS_BATCH_NO = batch_no and SLS_ORDER.DISPATCH_STATUS < 40
                    ) as order_count')
                    // ,DB::raw('(SELECT IFNULL(COUNT(INV_STOCK.F_PRD_VARIANT_NO),0) FROM SLS_BOOKING_DETAILS LEFT JOIN INV_STOCK ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO WHERE SLS_BOOKING_DETAILS.F_BOOKING_NO = booking_no GROUP BY INV_STOCK.F_PRD_VARIANT_NO) as variant')
                    )
                    ->join('SLS_BOOKING_DETAILS as bd','bd.F_BOOKING_NO','o.F_BOOKING_NO')
                    ->join('SLS_BATCH_LIST as bl','bl.PK_NO','o.PICKUP_ID')
                    ->where('bd.IS_COLLECTED_FOR_RTS',0)
                    ->where('o.DISPATCH_STATUS','<',40)
                    ->where('o.PICKUP_ID','>',0)
                    ->groupBy('batch_no')
                    ->get();
        return $this->formatResponse(true,'Dispatch List Found !','',$data);
    }

    public function getBatchCollectedList()
    {
        $data = DB::table('SLS_ORDER as o')
                    ->select('bl.RTS_BATCH_NO as batch_no','o.PICKUP_ID as batch_pk'
                    ,DB::raw('(
                        SELECT IFNULL(COUNT(INV_STOCK.PK_NO),0)
                        FROM INV_STOCK
                        LEFT JOIN SLS_BOOKING_DETAILS ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
                        LEFT JOIN SLS_ORDER ON SLS_BOOKING_DETAILS.F_BOOKING_NO = SLS_ORDER.F_BOOKING_NO
                        WHERE SLS_ORDER.PICKUP_ID = batch_pk) as item_count')
                    ,DB::raw('(
                        SELECT IFNULL(COUNT(INV_STOCK.PK_NO),0)
                        FROM INV_STOCK
                        LEFT JOIN SLS_BOOKING_DETAILS ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
                        LEFT JOIN SLS_ORDER ON SLS_BOOKING_DETAILS.F_BOOKING_NO = SLS_ORDER.F_BOOKING_NO
                        WHERE SLS_ORDER.PICKUP_ID = batch_pk
                        and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 1) as item_count_collected')
                    ,DB::raw('(SELECT IFNULL(COUNT(SLS_ORDER.PK_NO),0)
                    FROM SLS_ORDER WHERE SLS_ORDER.PICKUP_ID = batch_pk ) as order_count')
                    ,DB::raw('(SELECT IFNULL(COUNT(SLS_ORDER.PK_NO),0)
                    FROM SLS_ORDER WHERE SLS_ORDER.PICKUP_ID = batch_pk and SLS_ORDER.DISPATCH_STATUS >= 40 ) as order_dispatched')
                    // ,DB::raw('(SELECT IFNULL(COUNT(INV_STOCK.F_PRD_VARIANT_NO),0) FROM SLS_BOOKING_DETAILS LEFT JOIN INV_STOCK ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO WHERE SLS_BOOKING_DETAILS.F_BOOKING_NO = booking_no GROUP BY INV_STOCK.F_PRD_VARIANT_NO) as variant')
                    )
                    ->join('SLS_BOOKING_DETAILS as bd','bd.F_BOOKING_NO','o.F_BOOKING_NO')
                    ->join('SLS_BATCH_LIST as bl','bl.PK_NO','o.PICKUP_ID')
                    // ->where('bd.IS_COLLECTED_FOR_RTS',0)
                    ->where('o.DISPATCH_STATUS','<',40)
                    ->where('o.PICKUP_ID','>',0)
                    ->groupBy('batch_no')
                    ->orderBy('batch_no','DESC')
                    ->get();
        return $this->formatResponse(true,'Dispatch List Found !','',$data);
    }

    public function postAssignOrderItem($request)
    {
        DB::beginTransaction();
        try {
            $data = DB::table('SLS_BOOKING_DETAILS as bd')
            ->join('INV_STOCK as i','bd.F_INV_STOCK_NO','i.PK_NO')
            ->leftjoin('SLS_ORDER as o','o.F_BOOKING_NO','bd.F_BOOKING_NO')
            ->leftjoin('SLS_BATCH_LIST as bl','bl.PK_NO','o.PICKUP_ID')
            ->where('bl.PK_NO',$request->batch_id)
            ->where('i.SKUID',$request->sku_id)
            // ->update(['bd.RTS_COLLECTION_USER_ID'=>$request->logistic_user]);
            ->pluck('bd.PK_NO');

            BookingDetails::whereIn('PK_NO',$data)->update(['RTS_COLLECTION_USER_ID' => $request->logistic_user]);
        } catch (\Exception $e) {
            DB::rollback();
            return ['status'=>0,'name'=>''];
        }
        DB::commit();
            $username =\DB::table('SA_USER')
                        ->select('USERNAME')
                        ->where('PK_NO',$request->logistic_user)
                        ->first();
            $user_assigned = isset($username->USERNAME) ? 1 : 0;
            return ['status'=>1,'name'=>$username->USERNAME ?? 'Unassigned','id'=>$request->logistic_user,'skuid'=>$request->sku_id,'user_assigned'=>$user_assigned];
    }

    public function getCollectionBreakdown($id)
    {
        $data = array();
        $query1 =  PaymentCustomer::select('ACC_BANK_TXN.PK_NO','ACC_CUSTOMER_PAYMENTS.SS_CREATED_ON','ACC_BANK_TXN.TXN_DATE','ACC_BANK_TXN.MATCHED_ON','ACC_BANK_TXN.IS_MATCHED','ACC_CUSTOMER_PAYMENTS.F_SS_CREATED_BY','ACC_BANK_TXN.CODE','ACC_CUSTOMER_PAYMENTS.PK_NO AS PAYMENT_PK_NO','ACC_CUSTOMER_PAYMENTS.CUSTOMER_NAME','ACC_CUSTOMER_PAYMENTS.CUSTOMER_NO','ACC_CUSTOMER_PAYMENTS.PAID_BY','ACC_CUSTOMER_PAYMENTS.SLIP_NUMBER','ACC_CUSTOMER_PAYMENTS.ATTACHMENT_PATH','ACC_CUSTOMER_PAYMENTS.F_PAYMENT_ACC_NO','ACC_CUSTOMER_PAYMENTS.PAYMENT_BANK_NAME','ACC_CUSTOMER_PAYMENTS.PAYMENT_ACCOUNT_NAME','ACC_CUSTOMER_PAYMENTS.MR_AMOUNT', 'ACC_CUSTOMER_PAYMENTS.PAYMENT_CONFIRMED_STATUS','ACC_CUSTOMER_PAYMENTS.IS_COD', 'ACC_CUSTOMER_PAYMENTS.PAYMENT_DATE', DB::raw("'CUSTOMER' as TYPE") )
        ->leftJoin('ACC_BANK_TXN','ACC_BANK_TXN.F_CUSTOMER_PAYMENT_NO', '=', 'ACC_CUSTOMER_PAYMENTS.PK_NO')
        ->where('ACC_BANK_TXN.F_ACC_PAYMENT_BANK_NO',$id)
        ->orderBy('ACC_CUSTOMER_PAYMENTS.PAYMENT_DATE', 'DESC');

        $query2 =  PaymentReseller::select('ACC_BANK_TXN.PK_NO','ACC_RESELLER_PAYMENTS.SS_CREATED_ON','ACC_BANK_TXN.TXN_DATE','ACC_BANK_TXN.MATCHED_ON','ACC_BANK_TXN.IS_MATCHED','ACC_RESELLER_PAYMENTS.F_SS_CREATED_BY','ACC_BANK_TXN.CODE','ACC_RESELLER_PAYMENTS.PK_NO AS PAYMENT_PK_NO','ACC_RESELLER_PAYMENTS.RESELLER_NAME','ACC_RESELLER_PAYMENTS.RESELLER_NO','ACC_RESELLER_PAYMENTS.PAID_BY','ACC_RESELLER_PAYMENTS.SLIP_NUMBER','ACC_RESELLER_PAYMENTS.ATTACHMENT_PATH','ACC_RESELLER_PAYMENTS.F_PAYMENT_ACC_NO','ACC_RESELLER_PAYMENTS.PAYMENT_BANK_NAME','ACC_RESELLER_PAYMENTS.PAYMENT_ACCOUNT_NAME','ACC_RESELLER_PAYMENTS.MR_AMOUNT','ACC_RESELLER_PAYMENTS.PAYMENT_CONFIRMED_STATUS', 'ACC_RESELLER_PAYMENTS.IS_COD','ACC_RESELLER_PAYMENTS.PAYMENT_DATE', DB::raw("'RESELLER' as TYPE") )
        ->leftJoin('ACC_BANK_TXN','ACC_BANK_TXN.F_RESELLER_PAYMENT_NO', '=', 'ACC_RESELLER_PAYMENTS.PK_NO')
        ->where('ACC_BANK_TXN.F_ACC_PAYMENT_BANK_NO',$id)
        ->orderBy('ACC_RESELLER_PAYMENTS.PAYMENT_DATE', 'DESC');

        $data1 = $query1->where('ACC_CUSTOMER_PAYMENTS.IS_COD',1);
        $data2 = $query2->where('ACC_RESELLER_PAYMENTS.IS_COD',1);

        $data['customer'] = $data1->UNION($data2)->get();
        $agent_zone = PaymentBank::select('BANK_ACC_NAME')->where('PK_NO',$id)->first();
        $data['name']     = $agent_zone->BANK_ACC_NAME ?? '';

        return $this->formatResponse(true, '', 'admin.payment.list', $data);
    }

    public function getCodRtcUserStockList($request,$id)
    {
        $data = PaymentBank::join('INV_STOCK as i','i.F_INV_ZONE_NO','ACC_PAYMENT_BANK_ACC.F_INV_ZONE_NO')
                            ->join('PRD_VARIANT_SETUP as v', 'v.PK_NO', 'i.F_PRD_VARIANT_NO')
                            ->join('SLS_BOOKING_DETAILS as bd', 'i.PK_NO', 'bd.F_INV_STOCK_NO')
                            ->leftjoin('INV_WAREHOUSE_ZONES as wz', 'wz.PK_NO', 'i.F_INV_ZONE_NO')
                            ->select('v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.VARIANT_NAME as product_variant_name','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','bd.COD_RTC_ACK as is_acknowledge','i.F_ORDER_NO as order_id','i.IG_CODE'
                            ,DB::raw('(IFNULL(COUNT(i.PK_NO),0)) as qty')
                            ,DB::raw('IFNULL(wz.DESCRIPTION,"Product is in MY Warehouse landing area") as location')
                            ,DB::raw('IFNULL(i.INV_ZONE_BARCODE,"label") as label'))
                            ->where('bd.DISPATCH_STATUS','<',40)
                            ->where('ACC_PAYMENT_BANK_ACC.PK_NO',$id);
                            if ($request->acknowlwdge == 'yes') {
                                $data= $data->where('bd.COD_RTC_ACK',1);
                            }
                            if ($request->acknowlwdge == 'no') {
                                $data= $data->where('bd.COD_RTC_ACK',0);
                            }
                            $data = $data->groupBy('i.SKUID','is_acknowledge','wz.PK_NO')
                            ->orderBy('is_acknowledge','ASC')
                            ->get();
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($data);
        // echo '<br>======================<br>';
        // exit();
        if (isset($data) && !empty($data) && count($data)>0) {
            return $this->formatResponse(true,'List Found !','',$data);
        }
        return $this->formatResponse(false,'Data not Found !','',0);
    }
}
