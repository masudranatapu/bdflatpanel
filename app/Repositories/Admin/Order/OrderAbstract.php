<?php
namespace App\Repositories\Admin\Order;

use DB;
use Carbon\Carbon;
use App\Models\Agent;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Customer;
use App\Models\OrderRtc;
use App\Models\Reseller;
use App\Models\NotifySms;
use App\Models\AccBankTxn;
use App\Models\OrderPayment;
use App\Traits\RepoResponse;
use App\Models\AuthUserGroup;
use App\Models\BookingDetails;
use App\Models\PaymentBankAcc;
use App\Models\CustomerAddress;
use App\Models\PaymentCustomer;
use App\Models\PaymentReseller;
use App\Models\EmailNotification;
use App\Models\CustomerAddressType;
use Illuminate\Support\Facades\Auth;

class OrderAbstract implements OrderInterface
{
    use RepoResponse;
    protected $agent;
    protected $order;
    protected $booking;
    protected $address_type;
    protected $country;

    public function __construct(Agent $agent,Booking $booking, Order $order,CustomerAddressType $address_type, Country $country)
    {
        $this->agent   = $agent;
        $this->booking = $booking;
        $this->order   = $order;
        $this->address_type = $address_type;
        $this->country = $country;
    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        $data = $this->agent->where('IS_ACTIVE',1)->orderBy('NAME', 'ASC')->get();
        return $this->formatResponse(true, '', 'admin.agent.index', $data);
    }

    public function revert_rts_rtc($booking_id)
    {
        DB::beginTransaction();
        try {
            /* FOR DISPATCH STATUS */
            $query = DB::SELECT("SELECT
            BC.PK_NO AS BOOKING_DETAILS_PK_NO
            ,BC.F_BOOKING_NO
            ,BC.F_INV_STOCK_NO
            ,BC.DISPATCH_STATUS
            ,BC.IS_ADMIN_HOLD
            ,BC.IS_SYSTEM_HOLD
            ,S.ORDER_STATUS
            ,S.F_INV_WAREHOUSE_NO
            ,BC.IS_READY
            FROM
            SLS_BOOKING_DETAILS AS BC
            JOIN INV_STOCK AS S
            ON S.PK_NO = BC.F_INV_STOCK_NO
            WHERE BC.DISPATCH_STATUS != 40
            AND BC.IS_ADMIN_APPROVAL != 1
            AND S.F_INV_WAREHOUSE_NO = 2
            AND BC.F_BOOKING_NO = $booking_id
            ");

            if($query){
                foreach ($query as $key => $value) {
                    if( ($value->F_INV_WAREHOUSE_NO == 2) && ($value->ORDER_STATUS == 60) ){
                        DB::SELECT("UPDATE SLS_BOOKING_DETAILS SET IS_SYSTEM_HOLD = 1, DISPATCH_STATUS = 0  WHERE PK_NO = $value->BOOKING_DETAILS_PK_NO");
                    }
                    if($value->F_INV_WAREHOUSE_NO == 2){
                        DB::SELECT("UPDATE SLS_BOOKING_DETAILS SET IS_READY = 1  WHERE PK_NO = $value->BOOKING_DETAILS_PK_NO");
                    }
                }
            }
            $query2 = DB::SELECT("SELECT
            O.PK_NO AS ORDER_PK_NO
            ,O.F_BOOKING_NO
            ,O.IS_ADMIN_HOLD
            ,O.IS_SYSTEM_HOLD
            ,O.DISPATCH_STATUS
            ,O.IS_READY
            ,O.ORDER_ACTUAL_TOPUP
            ,B.TOTAL_PRICE
            ,B.DISCOUNT
            ,C.F_INV_STOCK_NO
            ,GROUP_CONCAT(C.IS_ADMIN_HOLD) AS ALL_C_IS_ADMIN_HOLD
            ,COUNT(C.IS_ADMIN_HOLD) AS COUNT_C_IS_ADMIN_HOLD

            ,GROUP_CONCAT(C.IS_SYSTEM_HOLD) AS  ALL_C_IS_SYSTEM_HOLD
            ,COUNT(C.IS_SYSTEM_HOLD) AS C_IS_SYSTEM_HOLD

            ,GROUP_CONCAT(C.IS_READY) AS ALL_C_IS_READY
            ,COUNT(C.IS_READY) AS COUNT_C_IS_READY

            FROM SLS_ORDER AS O
            LEFT JOIN SLS_BOOKING_DETAILS AS C
            ON C.F_BOOKING_NO = O.F_BOOKING_NO
            LEFT JOIN SLS_BOOKING AS B
            ON B.PK_NO = O.F_BOOKING_NO
            WHERE O.DISPATCH_STATUS <> 40
            AND B.PK_NO = $booking_id
            ");
            if($query2){
                foreach($query2 as $key => $val){
                    $ORDER_ID = $val->ORDER_PK_NO;
                    $all_c_is_ready             = $val->ALL_C_IS_READY;
                    $all_c_is_ready_arr         = explode(',',$all_c_is_ready);
                    $all_c_is_ready_arr_count           = array_count_values($all_c_is_ready_arr);

                    DB::SELECT("UPDATE SLS_ORDER SET DISPATCH_STATUS = 0,IS_SYSTEM_HOLD = 1  WHERE PK_NO = $ORDER_ID AND IS_ADMIN_APPROVAL <> 1 ");

                    /*update is_ready status*/
                    if( (isset($all_c_is_ready_arr_count[0])) && (isset($all_c_is_ready_arr_count[1])) ){
                        DB::SELECT("UPDATE SLS_ORDER SET IS_READY = 2 WHERE PK_NO = $ORDER_ID AND IS_ADMIN_APPROVAL <> 1 ");
                    }elseif(isset($all_c_is_ready_arr_count[1])){
                        if($all_c_is_ready_arr_count[1] == $val->COUNT_C_IS_READY ){
                            DB::SELECT("UPDATE SLS_ORDER SET IS_READY = 1 WHERE PK_NO = $ORDER_ID AND IS_ADMIN_APPROVAL <> 1 ");
                        }
                    }
                    /*UPDATE INV_STOCK ORDER_STATUS IF PAYMENT FULL AND VERIFIED */
                    $total_value = $val->TOTAL_PRICE - $val->DISCOUNT;
                    if( ($val->F_INV_STOCK_NO != null) && ($val->ORDER_ACTUAL_TOPUP >= $total_value)){
                        DB::SELECT("UPDATE INV_STOCK SET ORDER_STATUS = 60 WHERE PK_NO = $val->F_INV_STOCK_NO ");
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
        DB::commit();
        return true;
    }

    public function postStore($request)
    {
         DB::beginTransaction();

        try {
            $agent                  = new Agent();
            $agent->NAME            = $request->name;
            $agent->MOBILE_NO       = $request->phone;
            $agent->ALTERNATE_NO    = $request->alt_phone;
            $agent->EMAIL           = $request->email;
            $agent->FB_ID           = $request->fb_id;
            $agent->IG_ID           = $request->ig_id;
            $agent->UKSHOP_ID       = $request->uk_id;
            $agent->UKSHOP_PASS     = bcrypt($request->uk_pass);
            $agent->IS_ACTIVE       = 1;
            $agent->save();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.agent.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Agent has been created successfully !', 'admin.agent.list');
    }

    public function postUpdate($request, $id)
    {
        $agent = Agent::where('PK_NO', $id)->first();
            $agent->NAME            = $request->name;
            $agent->MOBILE_NO       = $request->phone;
            $agent->ALTERNATE_NO    = $request->alt_phone;
            $agent->EMAIL           = $request->email;
            $agent->FB_ID           = $request->fb_id;
            $agent->IG_ID           = $request->ig_id;
            $agent->UKSHOP_ID       = $request->uk_id;
            $agent->UKSHOP_PASS     = bcrypt($request->uk_pass);
            if ($agent->update()) {
                $agent = Agent::where('PK_NO', $id)->first();
                return $this->formatResponse(true, 'Agent Information has been Updated successfully', 'admin.agent.list');
            }
            return $this->formatResponse(false, 'Unable to update Agent Information !', 'admin.agent.list');
    }

    public function updateSenderaddress($request, $id)
    {

        DB::beginTransaction();
        try {
            $from_address  = CustomerAddress::find($request->f_from_address);
            $order = Order::find($id);
            $order->FROM_NAME               = $request->from_name;
            $order->FROM_ADDRESS_LINE_1     = $request->from_add_1;
            $order->FROM_ADDRESS_LINE_2     = $request->from_add_2;
            $order->FROM_ADDRESS_LINE_3     = $request->from_add_3;
            $order->FROM_ADDRESS_LINE_4     = $request->from_add_4;
            $order->FROM_MOBILE             = $request->from_mobile;
            $order->FROM_CITY               = $request->from_city;
            $order->FROM_POSTCODE           = $request->from_post_code;
            $order->FROM_STATE              = $request->from_state;
            $order->FROM_COUNTRY            = $request->from_country;
            $order->FROM_F_COUNTRY_NO       = $from_address->F_COUNTRY_NO;
            $order->F_FROM_ADDRESS          = $request->f_from_address;
            $order->update();
        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Order Sender address not updated successfully', 'admin.order.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Order Sender address updated successfully', 'admin.order.list');

    }

    public function updateReceiverAddress($request, $id)
    {
        DB::beginTransaction();
        try {
            $to_address  = CustomerAddress::find($request->f_to_address);

            $order = Order::find($id);
            $order->DELIVERY_NAME               = $request->delivery_name;
            $order->DELIVERY_ADDRESS_LINE_1     = $request->delivery_add_1;
            $order->DELIVERY_ADDRESS_LINE_2     = $request->delivery_add_2;
            $order->DELIVERY_ADDRESS_LINE_3     = $request->delivery_add_3;
            $order->DELIVERY_ADDRESS_LINE_4     = $request->delivery_add_4;
            $order->DELIVERY_MOBILE             = $request->delivery_mobile;
            $order->DELIVERY_CITY               = $request->delivery_city;
            $order->DELIVERY_POSTCODE           = $request->delivery_post_code;
            $order->DELIVERY_STATE              = $request->delivery_state;
            $order->DELIVERY_COUNTRY            = $request->delivery_country;
            $order->DELIVERY_F_COUNTRY_NO       = $to_address->F_COUNTRY_NO;
            $order->F_TO_ADDRESS                = $request->f_to_address;
            $order->update();

        } catch (\Exception $e) {

            DB::rollback();
            // return $this->formatResponse(false, 'Order delivery address not updated !', 'admin.order.list');
            return $this->formatResponse(false, $e->getMessage(), 'admin.order.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Order delivery address updated successfully !', 'admin.order.list');

    }


    public function delete($id)
    {

        DB::beginTransaction();
        try {
            $order = Order::where('F_BOOKING_NO', $id)->first();
            if( (($order->TOTAL_PRICE - $order->DISCOUNT) == 0) && ($order->ORDER_BUFFER_TOPUP == 0) ){
                Order::where('F_BOOKING_NO', $id)->delete();
                Booking::where('PK_NO', $id)->delete();
                NotifySms::where('F_BOOKING_NO',$id)->delete();
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Order not deleted successfully', 'admin.order.list');
        }
        DB::commit();
        return $this->formatResponse(true,'Order has been deleted successfully','admin.order.list');
    }

    public function postSelfPickup($request)
    {
        DB::beginTransaction();
        try {
            $order      = Order::where('F_BOOKING_NO', $request->booking_id)->first();
            if($request->approval){

                $bank = PaymentBankAcc::find($request->payment_acc_no);
                OrderRtc::where('F_BOOKING_NO', $request->booking_id)->delete();
                if($request->payment_acc_no == 0 ){
                    Order::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 0 ]);
                    BookingDetails::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 0 ]);

                }else{
                    if($order->IS_SELF_PICKUP != 1){
                        Order::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 1 ]);
                        BookingDetails::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 1 ]);
                    }
                    $rtc                         = new OrderRtc();
                    $rtc->F_ORDER_NO             = $order->PK_NO;
                    $rtc->F_BOOKING_NO           = $request->booking_id;
                    $rtc->F_ACC_PAYMENT_BANK_NO  = $request->payment_acc_no;
                    $rtc->BANK_NAME              = $bank->BANK_NAME;
                    $rtc->BANK_ACC_NAME          = $bank->BANK_ACC_NAME;
                    $rtc->REQUEST_APPROVED_TIME  = date('Y-m-d H:i:s');
                    $rtc->IS_REQUEST_PENDING     = 0;
                    $rtc->F_USER_NO              = $bank->F_USER_NO;
                    $rtc->save();
                }
            }else{
                $bank = PaymentBankAcc::find($request->payment_acc_no);
                if($request->payment_acc_no == 0 ){
                    Order::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 0 ]);
                    BookingDetails::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 0 ]);
                    OrderRtc::where('F_BOOKING_NO', $request->booking_id)->delete();
                }else{
                    if($order->IS_SELF_PICKUP != 1){
                        Order::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 1 ]);
                        BookingDetails::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 1 ]);
                    }
                    $rtc                         = new OrderRtc();
                    $rtc->F_ORDER_NO             = $order->PK_NO;
                    $rtc->F_BOOKING_NO           = $request->booking_id;
                    $rtc->F_ACC_PAYMENT_BANK_NO  = $request->payment_acc_no;
                    $rtc->BANK_NAME              = $bank->BANK_NAME;
                    $rtc->BANK_ACC_NAME          = $bank->BANK_ACC_NAME;
                    $rtc->REQUEST_TIME           = date('Y-m-d H:i:s');
                    $rtc->IS_REQUEST_PENDING     = 1;
                    $rtc->F_USER_NO              = $bank->F_USER_NO;
                    $rtc->save();
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Order not transfered successfully', 'admin.order.list');
        }
        DB::commit();
        return $this->formatResponse(true,'Order has been transfered successfully','admin.order.list');
    }

    public function postSelfPickupAjax($request)
    {
        DB::beginTransaction();
        try {
            $order      = Order::where('F_BOOKING_NO', $request->booking_id)->first();
            if($request->approval){

                $bank = PaymentBankAcc::find($request->payment_acc_no);
                OrderRtc::where('F_BOOKING_NO', $request->booking_id)->delete();
                if($request->payment_acc_no == 0 ){
                    Order::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 0 ]);
                    BookingDetails::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 0 ]);

                }else{
                    if($order->IS_SELF_PICKUP != 1){
                        Order::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 1 ]);
                        BookingDetails::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 1 ]);
                    }
                    $rtc                         = new OrderRtc();
                    $rtc->F_ORDER_NO             = $order->PK_NO;
                    $rtc->F_BOOKING_NO           = $request->booking_id;
                    $rtc->F_ACC_PAYMENT_BANK_NO  = $request->payment_acc_no;
                    $rtc->BANK_NAME              = $bank->BANK_NAME;
                    $rtc->BANK_ACC_NAME          = $bank->BANK_ACC_NAME;
                    $rtc->REQUEST_APPROVED_TIME  = date('Y-m-d H:i:s');
                    $rtc->IS_REQUEST_PENDING     = 0;
                    $rtc->F_USER_NO              = $bank->F_USER_NO;
                    $rtc->save();
                }
                $is_approval = 1;
            }else{
                $bank = PaymentBankAcc::find($request->payment_acc_no);
                if($request->payment_acc_no == 0 ){
                    Order::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 0 ]);
                    BookingDetails::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 0 ]);
                    OrderRtc::where('F_BOOKING_NO', $request->booking_id)->delete();
                }else{
                    if($order->IS_SELF_PICKUP != 1){
                        Order::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 1 ]);
                        BookingDetails::where('F_BOOKING_NO', $request->booking_id)->update([ 'IS_SELF_PICKUP' => 1 ]);
                    }
                    $rtc                         = new OrderRtc();
                    $rtc->F_ORDER_NO             = $order->PK_NO;
                    $rtc->F_BOOKING_NO           = $request->booking_id;
                    $rtc->F_ACC_PAYMENT_BANK_NO  = $request->payment_acc_no;
                    $rtc->BANK_NAME              = $bank->BANK_NAME;
                    $rtc->BANK_ACC_NAME          = $bank->BANK_ACC_NAME;
                    $rtc->REQUEST_TIME           = date('Y-m-d H:i:s');
                    $rtc->IS_REQUEST_PENDING     = 1;
                    $rtc->F_USER_NO              = $bank->F_USER_NO;
                    $rtc->save();
                }
                $is_approval = 0;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['status'=>0,'name'=>''];
        }
        DB::commit();
        $username = DB::table('ACC_PAYMENT_BANK_ACC')
                    ->select('BANK_ACC_NAME')
                    ->where('PK_NO',$request->payment_acc_no)
                    ->first();
        $user_assigned = isset($username->BANK_ACC_NAME) ? 1 : 0;
        return ['status'=>1,'username'=>$username->BANK_ACC_NAME ?? 'SP','rtc_no'=>$request->payment_acc_no,'booking_id'=>$request->booking_id,'user_assigned'=>$user_assigned,'approval'=>$is_approval];
    }

    public function getDueOrdersCustomer($customer_id)
    {
        return $this->order
        ->select('SLS_ORDER.PK_NO as ORDER_PK_NO','SLS_ORDER.F_CUSTOMER_NO as CUSTOMER_PK_NO','SLS_ORDER.CUSTOMER_NAME','SLS_ORDER.ORDER_ACTUAL_TOPUP','SLS_ORDER.ORDER_BUFFER_TOPUP','SLS_ORDER.ORDER_BALANCE_USED','SLS_ORDER.ORDER_BALANCE_RETURN','SLS_BOOKING.TOTAL_PRICE','SLS_BOOKING.PK_NO as BOOKING_PK_NO','SLS_BOOKING.CONFIRM_TIME','.SLS_BOOKING.BOOKING_TIME','SLS_BOOKING.DISCOUNT','SLS_BOOKING.BOOKING_NO')
        ->leftJoin('SLS_BOOKING','SLS_BOOKING.PK_NO','=','SLS_ORDER.F_BOOKING_NO')
        ->where('SLS_ORDER.F_CUSTOMER_NO',$customer_id)
        ->havingRaw(" IFNULL(SLS_ORDER.ORDER_BUFFER_TOPUP,0)  < IFNULL(SLS_BOOKING.TOTAL_PRICE,0) - SLS_BOOKING.DISCOUNT ")
        ->get();
    }

    public function getDueOrdersReseller($reseller_id)
    {
        return $this->order
        ->select('SLS_ORDER.PK_NO as ORDER_PK_NO','SLS_ORDER.F_CUSTOMER_NO as CUSTOMER_PK_NO','SLS_ORDER.CUSTOMER_NAME','SLS_ORDER.ORDER_ACTUAL_TOPUP','SLS_ORDER.ORDER_BUFFER_TOPUP','SLS_ORDER.ORDER_BALANCE_USED','SLS_ORDER.ORDER_BALANCE_RETURN','SLS_BOOKING.TOTAL_PRICE','SLS_BOOKING.PK_NO as BOOKING_PK_NO','SLS_BOOKING.CONFIRM_TIME','SLS_BOOKING.BOOKING_TIME','SLS_BOOKING.DISCOUNT','SLS_BOOKING.BOOKING_NO')
        ->leftJoin('SLS_BOOKING','SLS_BOOKING.PK_NO','=','SLS_ORDER.F_BOOKING_NO')
        ->where('SLS_ORDER.F_RESELLER_NO',$reseller_id)
        ->havingRaw(" IFNULL(SLS_ORDER.ORDER_BUFFER_TOPUP,0)  < IFNULL(SLS_BOOKING.TOTAL_PRICE,0) - SLS_BOOKING.DISCOUNT ")
        ->get();
    }

    public function getProductINV($ig_code, $f_booking_no = null,$customer_id = null,$reseller_id= null,$is_reseller,$delivery_postage,$type)
    {
            $prodct_image = Stock::selectRaw('(SELECT PRIMARY_IMG_RELATIVE_PATH from PRD_VARIANT_SETUP where MRK_ID_COMPOSITE_CODE = '. '"' .$ig_code. '"' .')')->limit(1)->getQuery();

            if ($is_reseller == 0) {
                $customer_post_code= Stock::selectRaw('(SELECT IFNULL(POST_CODE,0) from SLS_CUSTOMERS_ADDRESS where PK_NO = f_customer_address)')->limit(1)->getQuery();
                $customer_name= Stock::selectRaw('(SELECT NAME from SLS_CUSTOMERS_ADDRESS where PK_NO = f_customer_address)')->limit(1)->getQuery();
            }else{
                $customer_post_code= Stock::selectRaw('(SELECT IFNULL(POST_CODE,0) from SLS_RESELLERS where PK_NO = f_customer_address)')->limit(1)->getQuery();
                $customer_name= Stock::selectRaw('(SELECT NAME from SLS_RESELLERS where PK_NO = f_customer_address)')->limit(1)->getQuery();
            }

            $info = Stock::select('INV_STOCK.PK_NO','INV_STOCK.BOX_TYPE','INV_STOCK.F_BOX_NO','INV_STOCK.PRD_VARINAT_NAME','INV_STOCK.IG_CODE','INV_STOCK.INV_WAREHOUSE_NAME','INV_STOCK.F_INV_WAREHOUSE_NO','INV_STOCK.SKUID','INV_STOCK.SHIPMENT_TYPE','INV_STOCK.PREFERRED_SHIPPING_METHOD','INV_STOCK.CUSTOMER_PREFFERED_SHIPPING_METHOD','s.SCH_ARRIVAL_DATE','INV_STOCK.FINAL_PREFFERED_SHIPPING_METHOD','INV_STOCK.PRODUCT_STATUS','INV_STOCK.BOOKING_STATUS','b.SS_COST','b.SM_COST','b.CURRENT_F_DELIVERY_ADDRESS as f_customer_address','b.CURRENT_SS_COST','b.CURRENT_SM_COST','b.CURRENT_IS_FREIGHT','b.CURRENT_AIR_FREIGHT','b.CURRENT_SEA_FREIGHT','b.AIR_FREIGHT','b.SEA_FREIGHT','b.CURRENT_REGULAR_PRICE','b.CURRENT_INSTALLMENT_PRICE','b.F_INV_STOCK_NO','b.CURRENT_IS_SM','CURRENT_IS_REGULAR','b.ORDER_STATUS','b.IS_SELF_PICKUP','b.F_BUNDLE_NO', 'b.F_BUNDLE_NO','s.SHIPMENT_STATUS','o.BUNDLE_NAME','INV_STOCK.SS_COST as INV_SS_COST','INV_STOCK.SM_COST as INV_SM_COST','INV_STOCK.BARCODE','INV_STOCK.F_PRD_VARIANT_NO','INV_STOCK.REGULAR_PRICE','INV_STOCK.INSTALLMENT_PRICE'
            )
            ->selectSub($prodct_image, 'PRIMARY_IMG_RELATIVE_PATH')
            ->selectSub($customer_name, 'customer_name')
            ->selectSub($customer_post_code, 'customer_post_code')
            ->leftJoin('SC_SHIPMENT as s','s.PK_NO','INV_STOCK.F_SHIPPMENT_NO')
            ->leftJoin('SLS_BOOKING_DETAILS as b','INV_STOCK.PK_NO','b.F_INV_STOCK_NO')
            ->leftJoin('SLS_BUNDLE as o','o.PK_NO','b.F_BUNDLE_NO')
            ->where('INV_STOCK.IG_CODE', $ig_code)
            // ->where('b.CURRENT_F_DELIVERY_ADDRESS', $delivery_address)
            ->whereRaw('( INV_STOCK.F_BOOKING_NO = '.$f_booking_no.' ) ')
            ->orderBy('INV_STOCK.F_BOX_NO','ASC')
            ->get();

            $data['info']               = $info;
            $data['customer_id']        = $customer_id;
            $data['reseller_id']        = $reseller_id;
            $data['delivery_postage']   = $delivery_postage;
        if (!empty($data['info'])) {
            return $this->generateInputField($data, $f_booking_no,$type);
        }
        return $data;
    }

    public function getProductINVReturn($ig_code, $f_booking_no = null,$customer_id = null,$reseller_id= null,$is_reseller,$delivery_postage,$type)
    {
        $prodct_image = Stock::selectRaw('(SELECT PRIMARY_IMG_RELATIVE_PATH from PRD_VARIANT_SETUP where MRK_ID_COMPOSITE_CODE = '. '"' .$ig_code. '"' .')')->limit(1)->getQuery();

        if ($is_reseller == 0) {
            $customer_post_code= Stock::selectRaw('(SELECT IFNULL(POST_CODE,0) from SLS_CUSTOMERS_ADDRESS where PK_NO = f_customer_address)')->limit(1)->getQuery();
            $customer_name= Stock::selectRaw('(SELECT NAME from SLS_CUSTOMERS_ADDRESS where PK_NO = f_customer_address)')->limit(1)->getQuery();
        }else{
            $customer_post_code= Stock::selectRaw('(SELECT IFNULL(POST_CODE,0) from SLS_RESELLERS where PK_NO = f_customer_address)')->limit(1)->getQuery();
            $customer_name= Stock::selectRaw('(SELECT NAME from SLS_RESELLERS where PK_NO = f_customer_address)')->limit(1)->getQuery();
        }

        $info = Stock::select('INV_STOCK.PK_NO','INV_STOCK.BOX_TYPE','INV_STOCK.F_BOX_NO','INV_STOCK.PRD_VARINAT_NAME','INV_STOCK.IG_CODE','INV_STOCK.INV_WAREHOUSE_NAME','INV_STOCK.F_INV_WAREHOUSE_NO','INV_STOCK.SKUID','INV_STOCK.SHIPMENT_TYPE','INV_STOCK.PREFERRED_SHIPPING_METHOD','INV_STOCK.CUSTOMER_PREFFERED_SHIPPING_METHOD','s.SCH_ARRIVAL_DATE','INV_STOCK.FINAL_PREFFERED_SHIPPING_METHOD','INV_STOCK.PRODUCT_STATUS','INV_STOCK.BOOKING_STATUS','ba.SS_COST','ba.SM_COST','ba.CURRENT_F_DELIVERY_ADDRESS as f_customer_address','ba.CURRENT_SS_COST','ba.CURRENT_SM_COST','ba.CURRENT_IS_FREIGHT','ba.CURRENT_AIR_FREIGHT','ba.CURRENT_SEA_FREIGHT','ba.AIR_FREIGHT','ba.SEA_FREIGHT','ba.CURRENT_REGULAR_PRICE','ba.CURRENT_INSTALLMENT_PRICE','ba.F_INV_STOCK_NO','ba.CURRENT_IS_SM','ba.CURRENT_IS_REGULAR','ba.ORDER_STATUS','ba.IS_SELF_PICKUP','ba.F_BUNDLE_NO', 'ba.F_BUNDLE_NO','s.SHIPMENT_STATUS','o.BUNDLE_NAME','INV_STOCK.SS_COST as INV_SS_COST','INV_STOCK.SM_COST as INV_SM_COST','INV_STOCK.BARCODE','INV_STOCK.F_PRD_VARIANT_NO','ba.ID','INV_STOCK.REGULAR_PRICE','INV_STOCK.INSTALLMENT_PRICE'
        )
        ->selectSub($prodct_image, 'PRIMARY_IMG_RELATIVE_PATH')
        ->selectSub($customer_name, 'customer_name')
        ->selectSub($customer_post_code, 'customer_post_code')
        ->leftJoin('SC_SHIPMENT as s','s.PK_NO','INV_STOCK.F_SHIPPMENT_NO')
        // ->leftJoin('SLS_BOOKING_DETAILS as b','INV_STOCK.PK_NO','b.F_INV_STOCK_NO')
        ->leftJoin('SLS_BOOKING_DETAILS_AUD as ba','INV_STOCK.PK_NO','ba.F_INV_STOCK_NO')
        ->leftJoin('SLS_BUNDLE as o','o.PK_NO','ba.F_BUNDLE_NO','ba.F_BUNDLE_NO')
        ->where('INV_STOCK.IG_CODE', $ig_code)
        ->whereIn('ba.RETURN_TYPE',[1,2])
        ->where('ba.CHANGE_TYPE','ORDER_RETURN')
        ->whereRaw('( ba.F_BOOKING_NO = '.$f_booking_no.' ) ')
        ->orderBy('INV_STOCK.F_BOX_NO','ASC')
        ->get();

        $data['info']               = $info;
        $data['customer_id']        = $customer_id;
        $data['reseller_id']        = $reseller_id;
        $data['delivery_postage']   = $delivery_postage;

        if (!empty($data['info']) && !$data['info']->isEmpty()) {
        //     echo '<pre>';
        // echo '======================<br>';
        // print_r($info);
        // echo '<br>======================<br>';
        // exit();
            return $this->generateInputField($data, $f_booking_no,$type);
        }
        return false;
    }

    private function generateInputField($item, $book_qty = 0,$type){
        if ($type == 'edit') {
            return view('admin.order.book_order_variant_tr')->withItem($item)->withBookqty($book_qty)->render();
        }else{
            return view('admin.order.book_order_view_variant_tr')->withItem($item)->withBookqty($book_qty)->render();
        }
    }

    public function getProductINVAdminApproval($ig_code, $f_booking_no = null,$customer_id = null,$reseller_id= null,$is_reseller,$delivery_postage)
    {
            $prodct_image = Stock::selectRaw('(SELECT PRIMARY_IMG_RELATIVE_PATH from PRD_VARIANT_SETUP where MRK_ID_COMPOSITE_CODE = '. '"' .$ig_code. '"' .')')->limit(1)->getQuery();

            if ($is_reseller == 0) {
                $customer_post_code= Stock::selectRaw('(SELECT IFNULL(POST_CODE,0) from SLS_CUSTOMERS_ADDRESS where PK_NO = f_customer_address)')->limit(1)->getQuery();
                $customer_name= Stock::selectRaw('(SELECT NAME from SLS_CUSTOMERS_ADDRESS where PK_NO = f_customer_address)')->limit(1)->getQuery();
            }else{
                $customer_post_code= Stock::selectRaw('(SELECT IFNULL(POST_CODE,0) from SLS_RESELLERS where PK_NO = f_customer_address)')->limit(1)->getQuery();
                $customer_name= Stock::selectRaw('(SELECT NAME from SLS_RESELLERS where PK_NO = f_customer_address)')->limit(1)->getQuery();
            }

            $info = Stock::select('INV_STOCK.PK_NO','INV_STOCK.BOX_TYPE','INV_STOCK.F_BOX_NO','INV_STOCK.PRD_VARINAT_NAME','INV_STOCK.IG_CODE','INV_STOCK.INV_WAREHOUSE_NAME','INV_STOCK.F_INV_WAREHOUSE_NO','INV_STOCK.SKUID','INV_STOCK.SHIPMENT_TYPE','INV_STOCK.PREFERRED_SHIPPING_METHOD','INV_STOCK.CUSTOMER_PREFFERED_SHIPPING_METHOD','s.SCH_ARRIVAL_DATE','INV_STOCK.FINAL_PREFFERED_SHIPPING_METHOD','INV_STOCK.PRODUCT_STATUS','INV_STOCK.BOOKING_STATUS','b.CURRENT_F_DELIVERY_ADDRESS as f_customer_address','b.CURRENT_SS_COST','b.CURRENT_SM_COST','b.CURRENT_IS_FREIGHT','b.CURRENT_AIR_FREIGHT','b.CURRENT_SEA_FREIGHT','b.CURRENT_REGULAR_PRICE','b.CURRENT_INSTALLMENT_PRICE','b.F_INV_STOCK_NO','b.CURRENT_IS_SM','CURRENT_IS_REGULAR','b.ORDER_STATUS','b.IS_SELF_PICKUP','s.SHIPMENT_STATUS','b.SS_COST','b.SM_COST','b.IS_FREIGHT','b.AIR_FREIGHT','b.SEA_FREIGHT','b.REGULAR_PRICE as BOOK_REGULAR_PRICE','b.INSTALLMENT_PRICE as BOOK_INSTALLMENT_PRICE','b.F_INV_STOCK_NO','b.IS_SM','IS_REGULAR','b.IS_ADMIN_APPROVAL','INV_STOCK.SS_COST as INV_SS_COST','INV_STOCK.SM_COST as INV_SM_COST','INV_STOCK.BARCODE','INV_STOCK.F_PRD_VARIANT_NO','INV_STOCK.REGULAR_PRICE','INV_STOCK.INSTALLMENT_PRICE'
            )
            ->selectSub($prodct_image, 'PRIMARY_IMG_RELATIVE_PATH')
            ->selectSub($customer_name, 'customer_name')
            ->selectSub($customer_post_code, 'customer_post_code')
            ->leftJoin('SC_SHIPMENT as s','s.PK_NO','INV_STOCK.F_SHIPPMENT_NO')
            ->leftJoin('SLS_BOOKING_DETAILS as b','INV_STOCK.PK_NO','b.F_INV_STOCK_NO')
            ->where('INV_STOCK.IG_CODE', $ig_code)
            // ->where('b.CURRENT_F_DELIVERY_ADDRESS', $delivery_address)
            ->whereRaw('( INV_STOCK.F_BOOKING_NO = '.$f_booking_no.' ) ')
            ->orderBy('INV_STOCK.F_BOX_NO','ASC')
            ->get();

            $data['info']               = $info;
            $data['customer_id']        = $customer_id;
            $data['reseller_id']        = $reseller_id;
            $data['delivery_postage']   = $delivery_postage;
            // $data['delivery_address'] = $delivery_address;
        if (!empty($data['info'])) {
            return view('admin.order.adminApproval.book_order_variant_tr')->withItem($data)->render();
        }
        return $data;
    }

    public function findOrThrowException($PK_NO,$type)
    {
        $data       = array();
        $booking    = $this->booking->find($PK_NO);
        $start      = Carbon::parse($booking->BOOKING_TIME);
        $end        = Carbon::parse($booking->EXPIERY_DATE_TIME);

        $booking->EXPIERY_DATE_TIME_DIF = $end->diffInHours($start) - 12;
        $data['order'] = Order::where('F_BOOKING_NO',$booking->PK_NO)->first();
        if($booking->IS_RETURN == 1 || $booking->IS_RETURN == 2){
            $booking_details_aud = DB::table('SLS_BOOKING_DETAILS_AUD')->select('INV_STOCK.IG_CODE','SLS_BOOKING_DETAILS_AUD.F_BOOKING_NO','SLS_BOOKING_DETAILS_AUD.CURRENT_IS_REGULAR', 'SLS_BOOKING_DETAILS_AUD.CURRENT_F_DELIVERY_ADDRESS', DB::raw('ifnull(count(INV_STOCK.PK_NO),0) as total_book_qty')
            )
            ->leftJoin('INV_STOCK','INV_STOCK.PK_NO','SLS_BOOKING_DETAILS_AUD.F_INV_STOCK_NO')
            ->where('SLS_BOOKING_DETAILS_AUD.F_BOOKING_NO', $booking->PK_NO)
            ->where('SLS_BOOKING_DETAILS_AUD.RETURN_TYPE',[1,2])
            ->where('SLS_BOOKING_DETAILS_AUD.CHANGE_TYPE','ORDER_RETURN')
            ->groupBy('INV_STOCK.IG_CODE')
            ->get();
        }
        $booking_details = BookingDetails::select('INV_STOCK.IG_CODE','INV_STOCK.F_BOOKING_NO','SLS_BOOKING_DETAILS.CURRENT_IS_REGULAR', 'SLS_BOOKING_DETAILS.CURRENT_F_DELIVERY_ADDRESS', DB::raw('ifnull(count(INV_STOCK.PK_NO),0) as total_book_qty')
        )
        ->leftJoin('INV_STOCK','INV_STOCK.PK_NO','SLS_BOOKING_DETAILS.F_INV_STOCK_NO')
        ->where('SLS_BOOKING_DETAILS.F_BOOKING_NO', $booking->PK_NO)
        ->groupBy('INV_STOCK.IG_CODE')
        ->get();

        $data['booking_details'] = $booking_details ?? null ;

        if( $booking->IS_BUNDLE_MATCHED == 1 ){
           $respone = $this->getProductInvWithBundle($booking->PK_NO);
           $booking->bundleInfo = $respone;
        }else{
            if ($booking_details && count($booking_details) > 0 ) {
                foreach ($booking_details as $key => $value) {
                    $value->book_info = $this->getProductINV($value->IG_CODE, $value->F_BOOKING_NO,$booking->F_CUSTOMER_NO,$booking->F_RESELLER_NO,$booking->IS_RESELLER,$data['order']->DELIVERY_POSTCODE ?? '',$type);
                }
            }
        }
        if($booking->IS_RETURN == 1 || $booking->IS_RETURN == 2){
            if ($booking_details_aud && count($booking_details_aud) > 0 ) {

                foreach ($booking_details_aud as $key => $value) {
                    $value->book_info2 = $this->getProductINVReturn($value->IG_CODE, $value->F_BOOKING_NO,$booking->F_CUSTOMER_NO,$booking->F_RESELLER_NO,$booking->IS_RESELLER,$data['order']->DELIVERY_POSTCODE ?? '',$type);
                }
            }
        }
        $data['booking_details_aud'] = $booking_details_aud ?? null ;

        if($data['order']->IS_CANCEL == 1){
            $data['booking_details_canceled'] = DB::table('SLS_BOOKING_DETAILS_AUD')->select('SLS_BOOKING_DETAILS_AUD.*','INV_STOCK.PRD_VARINAT_NAME','INV_STOCK.INV_WAREHOUSE_NAME','INV_STOCK.PRD_VARIANT_IMAGE_PATH')->join('INV_STOCK','INV_STOCK.PK_NO','SLS_BOOKING_DETAILS_AUD.F_INV_STOCK_NO')->where('SLS_BOOKING_DETAILS_AUD.F_BOOKING_NO',$PK_NO)->where('SLS_BOOKING_DETAILS_AUD.CHANGE_TYPE','ORDER_CANCEL')->get();
        }else{
            $data['booking_details_canceled'] = [];
        }

        $data['booking']    = $booking;
        $data['arrived_at'] = NotifySms::select('SEND_AT')->where('IS_SEND', 1)->where('F_BOOKING_NO',$PK_NO)->where('TYPE','Arrival')->orderBy('SEND_AT', 'DESC')
        ->first();
        $data['default_msg_sent_at'] = NotifySms::select('SEND_AT')->where('IS_SEND', 1)->where('F_BOOKING_NO',$PK_NO)->where('TYPE','Default')->orderBy('SEND_AT', 'DESC')
        ->first();

        return $this->formatResponse(true, 'Data found successfully !', 'admin.booking.list', $data);
    }

    public function getProductInvWithBundle($book_no){
        $bundle           = BookingDetails::where('F_BOOKING_NO',$book_no)->whereNotNull('F_BUNDLE_NO')->groupBy('F_BUNDLE_NO')->groupBy('BUNDLE_SEQUENC')->get();
        $book_details_all = BookingDetails::where('F_BOOKING_NO',$book_no)->get();
        $book             = Booking::where('PK_NO',$book_no)->first();
        return view('admin.order._book_bundle_variant_tr')->withBundle($bundle)->withBookingDetails($book_details_all)->withBook($book)->render();
    }


    public function findOrThrowExceptionAdminApproval($PK_NO)
    {
        $data       = array();
        $booking    = $this->booking->find($PK_NO);
        $start      = Carbon::parse($booking->BOOKING_TIME);
        $end        = Carbon::parse($booking->EXPIERY_DATE_TIME);

        $booking->EXPIERY_DATE_TIME_DIF = $end->diffInHours($start) - 12;
        $data['order'] = Order::where('F_BOOKING_NO',$booking->PK_NO)->first();
        $booking_details = BookingDetails::select('INV_STOCK.IG_CODE','INV_STOCK.F_BOOKING_NO','SLS_BOOKING_DETAILS.CURRENT_IS_REGULAR','SLS_BOOKING_DETAILS.CURRENT_F_DELIVERY_ADDRESS'
        ,DB::raw('ifnull(count(INV_STOCK.PK_NO),0) as total_book_qty')
        )
        ->leftJoin('INV_STOCK','INV_STOCK.PK_NO','SLS_BOOKING_DETAILS.F_INV_STOCK_NO')
        ->where('SLS_BOOKING_DETAILS.F_BOOKING_NO', $booking->PK_NO)
        ->groupBy('INV_STOCK.IG_CODE')
        ->get();

        $data['booking']            = $booking;
        $data['booking_details']    = $booking_details ?? null ;

        if ($booking_details && count($booking_details) > 0 ) {
            foreach ($booking_details as $key => $value) {
                $value->book_info = $this->getProductINVAdminApproval($value->IG_CODE, $value->F_BOOKING_NO,$booking->F_CUSTOMER_NO,$booking->F_RESELLER_NO,$booking->IS_RESELLER,$data['order']->DELIVERY_POSTCODE ?? '');
            }
        }

        return $this->formatResponse(true, 'Data found successfully !', 'admin.booking.list', $data);
    }

    public function ajaxDelete($id,$type,$booking_no)
    {
        DB::beginTransaction();
        try {
            if($type == 'bundle'){
                $max_bundle_sequenc = BookingDetails::where('F_BOOKING_NO',$booking_no)->where('F_BUNDLE_NO',$id)->max('BUNDLE_SEQUENC');
                $booking_details    = BookingDetails::select('F_BOOKING_NO','COMISSION')->where('F_BOOKING_NO',$booking_no)->where('F_BUNDLE_NO',$id)->where('BUNDLE_SEQUENC',$max_bundle_sequenc)->get();
                $total_comm = 0;
                if($booking_details && count($booking_details) > 0 ){
                    foreach($booking_details as $key => $value ){
                        Stock::where('PK_NO',$value->F_INV_STOCK_NO)->update(['BOOKING_STATUS' => null, 'F_BOOKING_NO' => null,'F_ORDER_NO' => null,'ORDER_STATUS' => null,'ORDER_PRICE' => null]);
                        $total_comm += $value->COMISSION;
                    }
                }
                $booking            = Booking::select('TOTAL_COMISSION')->where('PK_NO',$booking_no)->first();
                $new_comission      = $booking->TOTAL_COMISSION - $total_comm;
                Booking::where('PK_NO',$booking_no)->update(['TOTAL_COMISSION' => $new_comission]);
                BookingDetails::where('F_BOOKING_NO',$booking_no)->where('F_BUNDLE_NO',$id)->where('BUNDLE_SEQUENC',$max_bundle_sequenc)->delete();

            }else{
                $booking_details    = BookingDetails::select('F_BOOKING_NO','COMISSION')->where('F_INV_STOCK_NO',$id)->first();
                $booking            = Booking::select('TOTAL_COMISSION')->where('PK_NO',$booking_details->F_BOOKING_NO)->first();
                $new_comission      = $booking->TOTAL_COMISSION - $booking_details->COMISSION;
                Booking::where('PK_NO',$booking_details->F_BOOKING_NO)->update(['TOTAL_COMISSION' => $new_comission]);
                BookingDetails::where('F_INV_STOCK_NO',$id)->delete();
                Stock::where('PK_NO',$id)->update(['BOOKING_STATUS' => null, 'F_BOOKING_NO' => null,'F_ORDER_NO' => null,'ORDER_STATUS' => null,'ORDER_PRICE' => null]);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        DB::commit();
        return 1;
    }

    public function ajaxPayment($request)
    {
        DB::beginTransaction();
        try {
            $order_no = Stock::select('F_ORDER_NO')->where('PK_NO',$request->pk_no)->first();
            Order::where('PK_NO',$order_no->F_ORDER_NO)->update(['ORDER_BALANCE_USED' => $request->order_balance_used]);
            if ($request->order_price_update == 'add') {
                Stock::where('PK_NO',$request->pk_no)->update(['ORDER_STATUS' => 60]);
            }else{
                Stock::where('PK_NO',$request->pk_no)->update(['ORDER_STATUS' => 10]);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        DB::commit();
        return 1;
    }

    public function updateBooktoOrder($request,$id)
    {
        DB::beginTransaction();
        try {
            $booking = Booking::find($id);
            $order = Order::where('F_BOOKING_NO',$id)->first();

            $auth_id = Auth::user()->PK_NO;
            $role_id = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                                ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                                ->select('F_ROLE_NO')->where('F_USER_NO',$auth_id)->first();

            if (empty($order)) {
                $booking_details = BookingDetails::select('F_INV_STOCK_NO')->where('F_BOOKING_NO',$id)->get();

                $order_new                      = new Order();
                $order_new->F_CUSTOMER_NO       = $booking->F_CUSTOMER_NO;
                $order_new->CUSTOMER_NAME       = $booking->CUSTOMER_NAME;
                $order_new->IS_RESELLER         = $booking->IS_RESELLER;
                $order_new->F_RESELLER_NO       = $booking->F_RESELLER_NO;
                $order_new->RESELLER_NAME       = $booking->RESELLER_NAME;
                $order_new->F_BOOKING_NO        = $booking->PK_NO;
                // $from_address           = ShippingAddress::where('PK_NO',8)->first();
                if ($booking->IS_RESELLER == 0) {
                    $to_address         = CustomerAddress::where('F_CUSTOMER_NO',$booking->F_CUSTOMER_NO)->where('F_ADDRESS_TYPE_NO',1)->where('IS_DEFAULT',1)->first();

                    $from_address = \Config::get('static_array.order_from');
                    $order_new->FROM_NAME               = $from_address['FROM_NAME'];
                    $order_new->FROM_MOBILE             = $from_address['FROM_MOBILE'];
                    $order_new->FROM_ADDRESS_LINE_1     = $from_address['FROM_ADDRESS_LINE_1'];
                    $order_new->FROM_ADDRESS_LINE_2     = $from_address['FROM_ADDRESS_LINE_2'];
                    $order_new->FROM_ADDRESS_LINE_3     = $from_address['FROM_ADDRESS_LINE_3'];
                    $order_new->FROM_ADDRESS_LINE_4     = $from_address['FROM_ADDRESS_LINE_4'];
                    $order_new->FROM_CITY               = $from_address['FROM_CITY'];
                    $order_new->FROM_STATE              = $from_address['FROM_STATE'];
                    $order_new->FROM_POSTCODE           = $from_address['FROM_POSTCODE'];
                    $order_new->FROM_COUNTRY            = $from_address['FROM_COUNTRY'];
                    $order_new->FROM_F_COUNTRY_NO       = $from_address['FROM_F_COUNTRY_NO'];

                    $order_new->DELIVERY_NAME           = $to_address->NAME;
                    $order_new->DELIVERY_MOBILE         = $to_address->TEL_NO;
                    $order_new->DELIVERY_ADDRESS_LINE_1 = $to_address->ADDRESS_LINE_1;
                    $order_new->DELIVERY_ADDRESS_LINE_2 = $to_address->ADDRESS_LINE_2;
                    $order_new->DELIVERY_ADDRESS_LINE_3 = $to_address->ADDRESS_LINE_3;
                    $order_new->DELIVERY_ADDRESS_LINE_4 = $to_address->ADDRESS_LINE_4;
                    $order_new->DELIVERY_CITY           = $to_address->city->CITY_NAME ?? '';
                    $order_new->DELIVERY_STATE          = $to_address->state->STATE_NAME ?? '';
                    $order_new->DELIVERY_POSTCODE       = $to_address->POST_CODE;
                    $order_new->DELIVERY_COUNTRY        = $to_address->country->NAME ?? '';
                    $order_new->DELIVERY_F_COUNTRY_NO   = $to_address->F_COUNTRY_NO;
                    $order_new->F_TO_ADDRESS            = $to_address->PK_NO;

                    $order_new->PREV_DELIVERY_NAME           = $to_address->NAME;
                    $order_new->PREV_DELIVERY_MOBILE         = $to_address->TEL_NO;
                    $order_new->PREV_DELIVERY_ADDRESS_LINE_1 = $to_address->ADDRESS_LINE_1;
                    $order_new->PREV_DELIVERY_ADDRESS_LINE_2 = $to_address->ADDRESS_LINE_2;
                    $order_new->PREV_DELIVERY_ADDRESS_LINE_3 = $to_address->ADDRESS_LINE_3;
                    $order_new->PREV_DELIVERY_ADDRESS_LINE_4 = $to_address->ADDRESS_LINE_4;
                    $order_new->PREV_DELIVERY_CITY           = $to_address->city->CITY_NAME ?? '';
                    $order_new->PREV_DELIVERY_STATE          = $to_address->state->STATE_NAME ?? '';
                    $order_new->PREV_DELIVERY_POSTCODE       = $to_address->POST_CODE;
                    $order_new->PREV_DELIVERY_COUNTRY        = $to_address->country->NAME ?? '';
                    $order_new->PREV_DELIVERY_F_COUNTRY_NO   = $to_address->F_COUNTRY_NO;
                }else{
                    $from_address                   = Reseller::where('PK_NO',$booking->F_RESELLER_NO)->first();
                    $to_address                     = CustomerAddress::select('PK_NO')->where('F_RESELLER_NO',$booking->F_RESELLER_NO)->where('F_ADDRESS_TYPE_NO',1)->where('IS_DEFAULT',1)->first();

                    $order->F_TO_ADDRESS            = $to_address->PK_NO;
                    $order_new->FROM_NAME           = $from_address->NAME;
                    $order_new->FROM_MOBILE         = $from_address->MOBILE_NO;
                    $order_new->FROM_ADDRESS_LINE_1 = $from_address->ADDRESS_LINE_1;
                    $order_new->FROM_ADDRESS_LINE_2 = $from_address->ADDRESS_LINE_2;
                    $order_new->FROM_ADDRESS_LINE_3 = $from_address->ADDRESS_LINE_3;
                    $order_new->FROM_ADDRESS_LINE_4 = $from_address->ADDRESS_LINE_4;
                    $order_new->FROM_CITY           = $from_address->city->CITY_NAME ?? '';
                    $order_new->FROM_STATE          = $from_address->state->STATE_NAME ?? '';
                    $order_new->FROM_POSTCODE       = $from_address->POST_CODE;
                    $order_new->FROM_COUNTRY        = $from_address->country->NAME ?? '';
                    $order_new->FROM_F_COUNTRY_NO   = $from_address->F_COUNTRY_NO;
                }
                $order_new->save();

                Stock::whereIn('PK_NO',$booking_details)
                ->update(['F_ORDER_NO' => $order->PK_NO,'ORDER_STATUS' => 10]);
                // $booking->RECONFIRM_TIME = date('Y-m-d h:i:s');
                // if (isset($request->order_date_) && $request->order_date_ != 0) {
                //     $booking->RECONFIRM_TIME        = $request->order_date_;
                // }
                $booking->BOOKING_STATUS = 80;
                $booking->save();
            }

            //SENDER ADDRESS UPDATE
            $to_address  = CustomerAddress::find($request->f_to_address);
            $order->DELIVERY_NAME               = $request->delivery_name;
            $order->DELIVERY_ADDRESS_LINE_1     = $request->delivery_add_1;
            $order->DELIVERY_ADDRESS_LINE_2     = $request->delivery_add_2;
            $order->DELIVERY_ADDRESS_LINE_3     = $request->delivery_add_3;
            $order->DELIVERY_ADDRESS_LINE_4     = $request->delivery_add_4;
            $order->DELIVERY_MOBILE             = $request->delivery_mobile;
            $order->DELIVERY_CITY               = $request->delivery_city;
            $order->DELIVERY_POSTCODE           = $request->delivery_post_code;
            $order->DELIVERY_STATE              = $request->delivery_state;
            $order->DELIVERY_COUNTRY            = $request->delivery_country;
            $order->DELIVERY_F_COUNTRY_NO       = $to_address->F_COUNTRY_NO;
            $order->F_TO_ADDRESS                = $request->f_to_address;

            //RECEIVER ADDRESS UPDATE
            $from_address  = CustomerAddress::find($request->f_from_address);
            $order->FROM_NAME               = $request->from_name;
            $order->FROM_ADDRESS_LINE_1     = $request->from_add_1;
            $order->FROM_ADDRESS_LINE_2     = $request->from_add_2;
            $order->FROM_ADDRESS_LINE_3     = $request->from_add_3;
            $order->FROM_ADDRESS_LINE_4     = $request->from_add_4;
            $order->FROM_MOBILE             = $request->from_mobile;
            $order->FROM_CITY               = $request->from_city;
            $order->FROM_POSTCODE           = $request->from_post_code;
            $order->FROM_STATE              = $request->from_state;
            $order->FROM_COUNTRY            = $request->from_country;
            $order->FROM_F_COUNTRY_NO       = $from_address->F_COUNTRY_NO;
            $order->F_FROM_ADDRESS          = $request->f_from_address;
            $order->update();

            // $order->update();
            $paid_count = 0;
            $self_pickup_count = 0;
            $updated = 0;
            // $order->IS_DEFAULT  = 0;
            if (isset($request['INV_PK_NO']) && !empty($request['INV_PK_NO'])) {

                foreach ($request['INV_PK_NO'] as $key => $value) {
                    $inv_stock = Stock::select('PK_NO','ORDER_PRICE','ORDER_STATUS')->where('PK_NO',$value)->first();
                    $update_book_details = BookingDetails::where('F_INV_STOCK_NO',$value)->first();
                    $selfpickup = 'selfpickup_value_'.$value;
                    $update_book_details->IS_SELF_PICKUP = isset($request->$selfpickup) ? 1 : 0;

                    if (isset($request->$selfpickup)) {
                        $self_pickup_count++;
                    }
                    if ($request['is_freight'][$key] == 1) {
                        if (!isset($request->$selfpickup) && $update_book_details->AIR_FREIGHT != $request['freight_costs'][$key]) {
                            $updated++;
                        }
                        $update_book_details->CURRENT_AIR_FREIGHT = $request['freight_costs'][$key] ?? 0;
                    }
                    if ($request['is_freight'][$key] == 2) {
                        if (!isset($request->$selfpickup) && $update_book_details->SEA_FREIGHT != $request['freight_costs'][$key]) {
                            $updated++;
                        }
                        $update_book_details->CURRENT_SEA_FREIGHT = $request['freight_costs'][$key] ?? 0;
                    }
                    $update_book_details->CURRENT_IS_FREIGHT = $request['is_freight'][$key] ?? 0;

                    if ($request['is_sm'][$key] == 1) {
                        if (!isset($request->$selfpickup) && $update_book_details->SM_COST != $request['postage_costs'][$key]) {
                            $updated++;
                        }
                        $update_book_details->CURRENT_SM_COST = $request['postage_costs'][$key] ?? 0;
                    }
                    if ($request['is_sm'][$key] == 0) {
                        if (!isset($request->$selfpickup) && $update_book_details->SS_COST != $request['postage_costs'][$key]) {
                            $updated++;
                        }
                        $update_book_details->CURRENT_SS_COST = $request['postage_costs'][$key] ?? 0;
                    }
                    $update_book_details->CURRENT_IS_SM = $request['is_sm'][$key];

                    if ($request['is_regular'][$key] == 1) {
                        if ($update_book_details->REGULAR_PRICE != $request['unit_costs'][$key]) {
                            $updated++;
                        }
                        $update_book_details->CURRENT_REGULAR_PRICE = $request['unit_costs'][$key] ?? 0;
                    }
                    if ($request['is_regular'][$key] == 0) {
                        if ($update_book_details->INSTALLMENT_PRICE != $request['unit_costs'][$key]) {
                            $updated++;
                        }
                        $update_book_details->CURRENT_INSTALLMENT_PRICE = $request['unit_costs'][$key] ?? 0;
                    }
                    $update_book_details->CURRENT_IS_REGULAR = $request['is_regular'][$key] ?? 0;
                    $update_book_details->CURRENT_F_DELIVERY_ADDRESS = $request['customer_address'][$key] ?? 0;

                    $checkbox = 'checkbox_value_'.$value;
                    if ($request->$checkbox !=  null) {

                        // $paid_count++;
                        if ($update_book_details->ORDER_STATUS <= 60) {
                            $update_book_details->ORDER_STATUS = 60;
                            $inv_stock->ORDER_STATUS = 60;
                        }else if($update_book_details->ORDER_STATUS <= 70){
                            $update_book_details->ORDER_STATUS = 70;
                            $inv_stock->ORDER_STATUS = 70;
                        }
                    }else{
                        $update_book_details->ORDER_STATUS = 10;
                        $inv_stock->ORDER_STATUS = 10;
                    }

                    $update_book_details->IS_ADMIN_APPROVAL = 0;
                    if ($updated > 0 && $role_id->F_ROLE_NO != 1) {
                        $update_book_details->IS_ADMIN_APPROVAL = 1;
                    }

                    $inv_stock->ORDER_PRICE = $request['line_total_costs'][$key] ?? 0;
                    $update_book_details->update();
                    $inv_stock->save();
                }
            }
            $order_selfpickup = 0;
            if (isset($request['INV_PK_NO']) && !empty($request['INV_PK_NO'])) {
                $order_item_count = count($request['INV_PK_NO']);
                if ($order_item_count == $self_pickup_count) {
                    $order_selfpickup = 1;
                }else if($order_item_count > $self_pickup_count && $self_pickup_count != 0){
                    $order_selfpickup = 2;
                }
            }
            //$order->ORDER_BALANCE_USED      = $request->balance_used;
            if ($updated > 0 && $role_id->F_ROLE_NO != 1) {
                $order->IS_ADMIN_APPROVAL   = 1;
            }
            $order->IS_SELF_PICKUP          = $order_selfpickup;
            $order->UPDATED_BY              = $auth_id;

            $penalty_fee    = 0;
            $penalty_note   = '';

            if (($request->penalty_amount && $request->penalty_amount > 0) || $request->penalty_note) {
                $penalty_fee        = $request->penalty_amount;
                $penalty_note       = $request->penalty_note;
                $order->IS_DEFAULT  = 1;
            }
            if (isset($request->change_option)) {
                if ($request->change_option == 0) { //option 2
                    $order->DEFAULT_TYPE = $order->DEFAULT_TYPE == 1 ? 2 : ($order->DEFAULT_TYPE == 3 ? 4 : ($order->DEFAULT_TYPE == 5 ? 6 : $order->DEFAULT_TYPE));
                    if (Carbon::parse($request->order_date_)->gt(Carbon::now()->subWeeks(13))) { // need to undefault
                            $order->DEFAULT_AT      = null;
                            $order->DEFAULT_TYPE    = 0;
                    }
                }else{
                    $order->DEFAULT_TYPE = $order->DEFAULT_TYPE == 2 ? 1 : ($order->DEFAULT_TYPE == 4 ? 3 : ($order->DEFAULT_TYPE == 6 ? 5 : $order->DEFAULT_TYPE));
                    if (Carbon::parse($request->order_date_)->gt(Carbon::now()->subWeeks(5))) { // need to undefault
                        $order->DEFAULT_AT      = null;
                        $order->DEFAULT_TYPE    = 0;
                    }
                }
            }
            if (isset($request->grace_time)) {
                if($request->grace_time > date('d-m-Y')){
                    $order->GRACE_TIME = date('Y-m-d',strtotime($request->grace_time));
                    $order->IS_DEFAULT  = 1;
                }elseif($request->grace_time == date('d-m-Y')){
                    $order->GRACE_TIME = null;
                }
            }
            $order->save();

            Booking::where('PK_NO',$id)->update(['RECONFIRM_TIME'=>date('Y-m-d',strtotime($request->order_date_)),'BOOKING_NOTES' => $request->booking_note,'DISCOUNT' => $request->discount_amount,'PENALTY_FEE' => $penalty_fee, 'PENALTY_NOTE' => $penalty_note]);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.booking.list', 0);
        }
        DB::commit();

        //REVERT RTS/RTC
        // $booking = Booking::select('TOTAL_PRICE')->where('PK_NO',$id)->first();
        // $order = Order::select('ORDER_ACTUAL_TOPUP')->where('F_BOOKING_NO',$id)->first();

        if($booking->TOTAL_PRICE > $order->ORDER_ACTUAL_TOPUP){
            $this->revert_rts_rtc($id);
        }
        return $this->formatResponse(true, 'Ordered successfully !', 'admin.booking.list', 1);
    }

    public function updateBooktoOrderAdminApproved($request,$id)
    {
        if ($request->save_btn == 'discard_alter_order') {
            DB::beginTransaction();
            try {
                $order = Order::where('F_BOOKING_NO',$id)->first();
                $auth_id = Auth::user()->PK_NO;
                $role_id = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                                    ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                                    ->select('F_ROLE_NO')->where('F_USER_NO',$auth_id)->first();

                if ($role_id->F_ROLE_NO == 1) {
                    DB::STATEMENT("UPDATE SLS_BOOKING_DETAILS SET
                    CURRENT_AIR_FREIGHT         = AIR_FREIGHT,
                    CURRENT_SEA_FREIGHT         = SEA_FREIGHT,
                    CURRENT_IS_FREIGHT          = IS_FREIGHT,
                    CURRENT_SM_COST             = SM_COST,
                    CURRENT_SS_COST             = SS_COST,
                    CURRENT_IS_SM               = IS_SM,
                    CURRENT_REGULAR_PRICE       = REGULAR_PRICE,
                    CURRENT_INSTALLMENT_PRICE   = INSTALLMENT_PRICE,
                    CURRENT_IS_REGULAR          = IS_REGULAR,
                    CURRENT_F_DELIVERY_ADDRESS  = F_DELIVERY_ADDRESS
                    where F_BOOKING_NO = '$id'");

                    $order->IS_ADMIN_APPROVAL             = 0;
                    $order->DELIVERY_NAME           =  $order->PREV_DELIVERY_NAME;
                    $order->DELIVERY_MOBILE         =  $order->PREV_DELIVERY_MOBILE;
                    $order->DELIVERY_ADDRESS_LINE_1 =  $order->PREV_DELIVERY_ADDRESS_LINE_1;
                    $order->DELIVERY_ADDRESS_LINE_2 =  $order->PREV_DELIVERY_ADDRESS_LINE_2;
                    $order->DELIVERY_ADDRESS_LINE_3 =  $order->PREV_DELIVERY_ADDRESS_LINE_3;
                    $order->DELIVERY_ADDRESS_LINE_4 =  $order->PREV_DELIVERY_ADDRESS_LINE_4;
                    $order->DELIVERY_CITY           =  $order->PREV_DELIVERY_CITY;
                    $order->DELIVERY_STATE          =  $order->PREV_DELIVERY_STATE;
                    $order->DELIVERY_POSTCODE       =  $order->PREV_DELIVERY_POSTCODE;
                    $order->DELIVERY_COUNTRY        =  $order->PREV_DELIVERY_COUNTRY;
                    $order->DELIVERY_F_COUNTRY_NO   =  $order->PREV_DELIVERY_F_COUNTRY_NO;
                    $order->UPDATED_BY              =  $auth_id;
                    $order->save();
                    Booking::where('PK_NO',$id)->update(['RECONFIRM_TIME'=>date('Y-m-d',strtotime($request->order_date_)),'BOOKING_NOTES' => $request->booking_note,'DISCOUNT' => $request->discount_amount,'TOTAL_PRICE'=>$request->grand_total]);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return $this->formatResponse(false, $e->getMessage(), 'admin.order.list', 0);
            }
            DB::commit();
            return $this->formatResponse(true, 'Order Discarded !', 'admin.order.list', 1);

        }else{

            DB::beginTransaction();
            try {
                $order = Order::where('F_BOOKING_NO',$id)->first();
                $auth_id = Auth::user()->PK_NO;
                $role_id = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                                    ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                                    ->select('F_ROLE_NO')->where('F_USER_NO',$auth_id)->first();

                $self_pickup_count = 0;
                if (isset($request['INV_PK_NO']) && !empty($request['INV_PK_NO'])) {
                    foreach ($request['INV_PK_NO'] as $key => $value) {
                        $inv_stock = Stock::select('PK_NO','ORDER_PRICE','ORDER_STATUS')->where('PK_NO',$value)->first();
                        $update_book_details = BookingDetails::where('F_INV_STOCK_NO',$value)->first();
                        $selfpickup = 'selfpickup_value_'.$value;
                        $update_book_details->IS_SELF_PICKUP = isset($request->$selfpickup) ? 1 : 0;

                        if (isset($request->$selfpickup)) {
                            $self_pickup_count++;
                        }
                        if ($request['is_freight'][$key] == 1) {
                            $update_book_details->CURRENT_AIR_FREIGHT = $request['freight_costs'][$key];
                            $update_book_details->AIR_FREIGHT = $request['freight_costs'][$key];
                        }
                        if ($request['is_freight'][$key] == 2) {
                            $update_book_details->CURRENT_SEA_FREIGHT = $request['freight_costs'][$key];
                            $update_book_details->SEA_FREIGHT = $request['freight_costs'][$key];
                        }
                        $update_book_details->CURRENT_IS_FREIGHT = $request['is_freight'][$key];
                        $update_book_details->IS_FREIGHT = $request['is_freight'][$key];

                        if ($request['is_sm'][$key] == 1) {
                            $update_book_details->CURRENT_SM_COST = $request['postage_costs'][$key];
                            $update_book_details->SM_COST = $request['postage_costs'][$key];
                        }
                        if ($request['is_sm'][$key] == 0) {
                            $update_book_details->CURRENT_SS_COST = $request['postage_costs'][$key];
                            $update_book_details->SS_COST = $request['postage_costs'][$key];
                        }
                        $update_book_details->CURRENT_IS_SM = $request['is_sm'][$key];

                        if ($request['is_regular'][$key] == 1) {
                            $update_book_details->CURRENT_REGULAR_PRICE = $request['unit_costs'][$key];
                            $update_book_details->REGULAR_PRICE = $request['unit_costs'][$key];
                        }
                        if ($request['is_regular'][$key] == 0) {
                            $update_book_details->CURRENT_INSTALLMENT_PRICE = $request['unit_costs'][$key];
                            $update_book_details->INSTALLMENT_PRICE = $request['unit_costs'][$key];
                        }
                        $update_book_details->CURRENT_IS_REGULAR = $request['is_regular'][$key];
                        $update_book_details->CURRENT_F_DELIVERY_ADDRESS = $request['customer_address'][$key];
                        $update_book_details->F_DELIVERY_ADDRESS = $request['customer_address'][$key];

                        $checkbox = 'checkbox_value_'.$value;
                        if (isset($request->$checkbox)) {
                            if ($update_book_details->ORDER_STATUS <= 60) {
                                $update_book_details->ORDER_STATUS = 60;
                                $inv_stock->ORDER_STATUS = 60;
                            }else if($update_book_details->ORDER_STATUS <= 70){
                                $update_book_details->ORDER_STATUS = 70;
                                $inv_stock->ORDER_STATUS = 70;
                            }
                        }else{
                            $update_book_details->ORDER_STATUS = 10;
                            $inv_stock->ORDER_STATUS = 10;
                        }
                        if ($role_id->F_ROLE_NO == 1) {
                            $update_book_details->IS_ADMIN_APPROVAL = 0;
                        }
                        $inv_stock->ORDER_PRICE = $request['line_total_costs'][$key];
                        $update_book_details->save();
                        $inv_stock->save();
                    }
                }
                $order_selfpickup = 0;
                if (isset($request['INV_PK_NO']) && !empty($request['INV_PK_NO'])) {
                    $order_item_count = count($request['INV_PK_NO']);
                    if ($order_item_count == $self_pickup_count) {
                        $order_selfpickup = 1;
                    }else if($order_item_count > $self_pickup_count && $self_pickup_count != 0){
                        $order_selfpickup = 2;
                    }
                }
                // $order->ORDER_BALANCE_USED      = $request->balance_used;
                if ($role_id->F_ROLE_NO == 1) {
                    $order->IS_ADMIN_APPROVAL   = 0;
                    $order->PREV_DELIVERY_NAME            = $request->delivery_name;
                    $order->PREV_DELIVERY_MOBILE          = $request->delivery_mobile;
                    $order->PREV_DELIVERY_ADDRESS_LINE_1  = $request->delivery_add_1;
                    $order->PREV_DELIVERY_ADDRESS_LINE_2  = $request->delivery_add_2;
                    $order->PREV_DELIVERY_ADDRESS_LINE_3  = $request->delivery_add_3;
                    $order->PREV_DELIVERY_ADDRESS_LINE_4  = $request->delivery_add_4;
                    $order->PREV_DELIVERY_CITY            = $request->delivery_city;
                    $order->PREV_DELIVERY_STATE           = $request->delivery_state;
                    $order->PREV_DELIVERY_POSTCODE        = $request->delivery_post_code;
                    $order->PREV_DELIVERY_COUNTRY         = $request->delivery_country;
                    $order->PREV_DELIVERY_F_COUNTRY_NO    = $request->receiver_f_country;
                }
                $order->IS_SELF_PICKUP          = $order_selfpickup;
                $order->UPDATED_BY              = $auth_id;
                $order->save();
                Booking::where('PK_NO',$id)->update(['RECONFIRM_TIME'=>date('Y-m-d',strtotime($request->order_date_)),'BOOKING_NOTES' => $request->booking_note,'DISCOUNT' => $request->discount_amount]);

            } catch (\Exception $e) {
                DB::rollback();
                return $this->formatResponse(false, $e->getMessage(), 'admin.order.list', 0);
            }
            DB::commit();
            return $this->formatResponse(true, 'Order Approved !', 'admin.order.list', 1);
        }
    }

    public function getIndex()
    {
        $order = DB::table('SLS_ORDER as o')
                    ->join('SLS_BOOKING as b','o.F_BOOKING_NO','b.PK_NO')
                    ->select('o.*','b.TOTAL_PRICE','b.BOOKING_SALES_AGENT_NAME')
                    ->get();

        return $this->formatResponse(true, 'Data found successfully !', 'admin.order.list', $order);
    }

    public function getOrderForDisopatch($PK_NO)
    {
        $data       = array();
        $booking    = $this->booking->find($PK_NO);
        $start      = Carbon::parse($booking->BOOKING_TIME);
        $end        = Carbon::parse($booking->EXPIERY_DATE_TIME);

        $booking->EXPIERY_DATE_TIME_DIF = $end->diffInHours($start) - 12;

        $booking_details = BookingDetails::select('INV_STOCK.*','SLS_BOOKING_DETAILS.IS_REGULAR'
        ,DB::raw('ifnull(count(INV_STOCK.PK_NO),0) as total_book_qty'), 'SLS_BOOKING_DETAILS.CURRENT_F_DELIVERY_ADDRESS' )
        ->leftJoin('INV_STOCK','INV_STOCK.PK_NO','SLS_BOOKING_DETAILS.F_INV_STOCK_NO')
        ->where('SLS_BOOKING_DETAILS.F_BOOKING_NO', $booking->PK_NO)
       ->where('SLS_BOOKING_DETAILS.DISPATCH_STATUS', '<>',40)
      ->whereRaw(('(case WHEN SLS_BOOKING_DETAILS.IS_ADMIN_HOLD <> 2  THEN SLS_BOOKING_DETAILS.IS_SYSTEM_HOLD = 0 AND SLS_BOOKING_DETAILS.IS_ADMIN_HOLD = 0 ELSE SLS_BOOKING_DETAILS.IS_ADMIN_HOLD = 2 END)'))

      //  ->where('SLS_BOOKING_DETAILS.IS_ADMIN_HOLD', '=', 2)
        // ->where('SLS_BOOKING_DETAILS.IS_SYSTEM_HOLD', 0)

        // ->when($published, function ($q) use ($published) {
        //     return $q->where('published', 1);
        // })
        ->groupBy('INV_STOCK.IG_CODE')
        ->groupBy('SLS_BOOKING_DETAILS.CURRENT_F_DELIVERY_ADDRESS')
        ->get();
        $data['dispatch_section'] = BookingDetails::select('SLS_BOOKING_DETAILS.PK_NO'
        ,DB::raw('ifnull(count(SLS_BOOKING_DETAILS.PK_NO),0) as total_book_qty'),'SLS_BOOKING_DETAILS.CURRENT_F_DELIVERY_ADDRESS' )
        ->where('SLS_BOOKING_DETAILS.F_BOOKING_NO', $booking->PK_NO)
        ->groupBy('SLS_BOOKING_DETAILS.CURRENT_F_DELIVERY_ADDRESS')
        ->get();


        $data['booking']            = $booking;
        $data['booking_details']    = $booking_details ?? null ;


        return $this->formatResponse(true, 'Data found successfully !', 'admin.booking.list', $data);
    }

    public function getCustomerAddress($id,$pk_no,$address_id,$is_reseller)
    {
        $customer_address = CustomerAddress::where('F_ADDRESS_TYPE_NO','!=',2)
                                            ->where('IS_ACTIVE',1);
            if ($is_reseller > 0) {
                $customer_address = $customer_address->where('F_RESELLER_NO',$id);
            }else{
                $customer_address = $customer_address->where('F_CUSTOMER_NO',$id);
            }
            $customer_address     = $customer_address->get();

        $data['country'] = $this->country->getCountryComboWithCode();
        $addTypeCombo    = $this->address_type->getAddTypeCombo();
        $data['pk_no'] = $pk_no;
        $data['address_id'] = $address_id;
        $data['order_status'] = BookingDetails::select('ORDER_STATUS')->where('F_INV_STOCK_NO',$pk_no)->first();

        $html = view('admin.order.customer_address_modal')->withRows($customer_address)->withData($data)->withAddress($addTypeCombo)->render();

        $data['html'] = $html;
        return response()->json($data);
    }

    public function postCustomerAddress($request)
    {

        DB::beginTransaction();
        try {
            /*Update address*/
            if($request->address_pk_ > 0 ){

                $addr =  CustomerAddress::find($request->address_pk_);

                $addr->NAME                     = $request->customeraddress;
                $addr->TEL_NO                   = (int)$request->mobilenoadd;
                $addr->ADDRESS_LINE_1           = $request->ad_1;
                $addr->ADDRESS_LINE_2           = $request->ad_2;
                $addr->ADDRESS_LINE_3           = $request->ad_3;
                $addr->ADDRESS_LINE_4           = $request->ad_4;
                $addr->LOCATION                 = $request->location;
                $addr->F_COUNTRY_NO             = $request->country;
                $addr->STATE                    = $request->state;
                $addr->CITY                     = $request->city;
                $addr->POST_CODE                = $request->post_code;
               // $addr->F_ADDRESS_TYPE_NO        = 1;
               // $addr->F_RESELLER_NO            = $request->customer_id_;
                $addr->IS_ACTIVE                = 1;
                $addr->update();

            }else{
                /*Insert new address*/
                if($request->customer_id_){
                    $addr =  new CustomerAddress();
                    $addr->NAME                     = $request->customeraddress;
                    $addr->TEL_NO                   = (int)$request->mobilenoadd;
                    $addr->ADDRESS_LINE_1           = $request->ad_1;
                    $addr->ADDRESS_LINE_2           = $request->ad_2;
                    $addr->ADDRESS_LINE_3           = $request->ad_3;
                    $addr->ADDRESS_LINE_4           = $request->ad_4;
                    $addr->LOCATION                 = $request->location;
                    $addr->F_COUNTRY_NO             = $request->country;
                    $addr->STATE                    = $request->state;
                    $addr->CITY                     = $request->city;
                    $addr->POST_CODE                = $request->post_code;
                    $addr->F_ADDRESS_TYPE_NO        = $request->addresstype;
                    if ($request->is_reseller == 1) {
                        $addr->F_RESELLER_NO        = $request->customer_id_;
                    }else{
                        $addr->F_CUSTOMER_NO        = $request->customer_id_;
                    }
                    $addr->IS_ACTIVE                = 1;
                    $addr->save();
                    $address_pk = $addr->PK_NO;
                }
            }
/*
            if ($request->is_reseller == 1) {
                if (isset($request->address_pk_) && $request->address_pk_ > 0) {
                    $reseller_add                       = CustomerAddress::find($request->address_pk_);
                }else{
                    $reseller_add                       = new CustomerAddress();
                }
                $reseller_add->NAME                     = $request->customeraddress;
                $reseller_add->TEL_NO                   = (int)$request->mobilenoadd;
                $reseller_add->ADDRESS_LINE_1           = $request->ad_1;
                $reseller_add->ADDRESS_LINE_2           = $request->ad_2;
                $reseller_add->ADDRESS_LINE_3           = $request->ad_3;
                $reseller_add->ADDRESS_LINE_4           = $request->ad_4;
                $reseller_add->LOCATION                 = $request->location;
                $reseller_add->F_COUNTRY_NO             = $request->country;
                $reseller_add->STATE                    = $request->state;
                $reseller_add->CITY                     = $request->city;
                $reseller_add->POST_CODE                = $request->post_code;
                $reseller_add->F_ADDRESS_TYPE_NO        = 1;
                $reseller_add->F_RESELLER_NO            = $request->customer_id_;
                $reseller_add->IS_ACTIVE                = 1;
                $reseller_add->save();

            }else if (isset($request->same_as_add) && $request->same_as_add == 0 && $request->same_as_add != 'on') {

                $customer_add                           = new CustomerAddress();
                $customer_add->NAME                     = $request->customeraddress;
                $customer_add->TEL_NO                   = (int)$request->mobilenoadd;
                $customer_add->ADDRESS_LINE_1           = $request->ad_1;
                $customer_add->ADDRESS_LINE_2           = $request->ad_2;
                $customer_add->ADDRESS_LINE_3           = $request->ad_3;
                $customer_add->ADDRESS_LINE_4           = $request->ad_4;
                $customer_add->LOCATION                 = $request->location;
                $customer_add->F_COUNTRY_NO             = $request->country;
                $customer_add->STATE                    = $request->state;
                $customer_add->CITY                     = $request->city;
                $customer_add->POST_CODE                = $request->post_code;
                $customer_add->F_ADDRESS_TYPE_NO        = 1;
                $customer_add->F_CUSTOMER_NO            = $request->customer_id_;
                $customer_add->IS_ACTIVE                = 1;
                $customer_add->save();
                $address_pk                             = $customer_add->PK_NO;

                $customer_add                           = new CustomerAddress();
                $customer_add->NAME                     = $request->customeraddress;
                $customer_add->TEL_NO                   = (int)$request->mobilenoadd;
                $customer_add->ADDRESS_LINE_1           = $request->ad_1;
                $customer_add->ADDRESS_LINE_2           = $request->ad_2;
                $customer_add->ADDRESS_LINE_3           = $request->ad_3;
                $customer_add->ADDRESS_LINE_4           = $request->ad_4;
                $customer_add->LOCATION                 = $request->location;
                $customer_add->F_COUNTRY_NO             = $request->country;
                $customer_add->STATE                    = $request->state;
                $customer_add->CITY                     = $request->city;
                $customer_add->POST_CODE                = $request->post_code;
                $customer_add->F_ADDRESS_TYPE_NO        = 2;
                $customer_add->F_CUSTOMER_NO            = $request->customer_id_;
                $customer_add->IS_ACTIVE                = 1;
                $customer_add->save();

            }else if (isset($request->same_as_add) && $request->same_as_add == 'on') {

                if (isset($request->address_pk_) && $request->address_pk_ > 0) {
                    $customer_add                        = CustomerAddress::find($request->address_pk_);
                }else{
                    $customer_add                       = new CustomerAddress();
                }
                $customer_add->NAME                     = $request->customeraddress;
                $customer_add->TEL_NO                   = (int)$request->mobilenoadd;
                $customer_add->ADDRESS_LINE_1           = $request->ad_1;
                $customer_add->ADDRESS_LINE_2           = $request->ad_2;
                $customer_add->ADDRESS_LINE_3           = $request->ad_3;
                $customer_add->ADDRESS_LINE_4           = $request->ad_4;
                $customer_add->LOCATION                 = $request->location;
                $customer_add->F_COUNTRY_NO             = $request->country;
                $customer_add->STATE                    = $request->state;
                $customer_add->CITY                     = $request->city;
                $customer_add->POST_CODE                = $request->post_code;
                $customer_add->F_ADDRESS_TYPE_NO        = $request->addresstype;
                $customer_add->F_CUSTOMER_NO            = $request->customer_id_;
                $customer_add->IS_ACTIVE                = 1;
                $customer_add->save();
                $address_pk                             = $customer_add->PK_NO;

                if (isset($request->booking_create) && $request->booking_create == 1) {
                    $customer_add                           = new CustomerAddress();
                    $customer_add->NAME                     = $request->customeraddress2;
                    $customer_add->TEL_NO                   = (int)$request->mobilenoadd2;
                    $customer_add->ADDRESS_LINE_1           = $request->ad_12;
                    $customer_add->ADDRESS_LINE_2           = $request->ad_22;
                    $customer_add->ADDRESS_LINE_3           = $request->ad_32;
                    $customer_add->ADDRESS_LINE_4           = $request->ad_42;
                    $customer_add->LOCATION                 = $request->location2;
                    $customer_add->F_COUNTRY_NO             = $request->country2;
                    $customer_add->STATE                    = $request->state2;
                    $customer_add->CITY                     = $request->city2;
                    $customer_add->POST_CODE                = $request->post_code2;
                    $customer_add->F_ADDRESS_TYPE_NO        = $request->addresstype2;
                    $customer_add->F_CUSTOMER_NO            = $request->customer_id_;
                    $customer_add->IS_ACTIVE                = 1;
                    $customer_add->save();
                }
            }
        */

        } catch (\Exception $e) {
            DB::rollback();
            return ['status'=>0,'address_pk'=>$e->getMessage()];
        }
        DB::commit();
        return ['status'=>1,'address_pk'=>$request->address_pk_ ?? 0,'post_code'=>$request->post_code,'final_address'=>$address_pk ?? 0,'is_reseller'=>$request->is_reseller];
    }


    public function postCustomerAddress2($request)
    {
        DB::beginTransaction();
        try {
             if ( $request->same_as_add == 'on') {

                $customer_add                           = new CustomerAddress();
                $customer_add->NAME                     = $request->customeraddress;
                $customer_add->TEL_NO                   = (int)$request->mobilenoadd;
                $customer_add->ADDRESS_LINE_1           = $request->ad_1;
                $customer_add->ADDRESS_LINE_2           = $request->ad_2;
                $customer_add->ADDRESS_LINE_3           = $request->ad_3;
                $customer_add->ADDRESS_LINE_4           = $request->ad_4;
                $customer_add->LOCATION                 = $request->location;
                $customer_add->F_COUNTRY_NO             = $request->country;
                $customer_add->STATE                    = $request->state;
                $customer_add->CITY                     = $request->city;
                $customer_add->POST_CODE                = $request->post_code;
                $customer_add->F_ADDRESS_TYPE_NO        = $request->addresstype;
                $customer_add->F_CUSTOMER_NO            = $request->customer_id_;
                $customer_add->IS_ACTIVE                = 1;
                $customer_add->IS_DEFAULT               = 1;
                $customer_add->save();
                $address_pk                             = $customer_add->PK_NO;

                $customer_add                           = new CustomerAddress();
                $customer_add->NAME                     = $request->customeraddress2;
                $customer_add->TEL_NO                   = (int)$request->mobilenoadd2;
                $customer_add->ADDRESS_LINE_1           = $request->ad_12;
                $customer_add->ADDRESS_LINE_2           = $request->ad_22;
                $customer_add->ADDRESS_LINE_3           = $request->ad_32;
                $customer_add->ADDRESS_LINE_4           = $request->ad_42;
                $customer_add->LOCATION                 = $request->location2;
                $customer_add->F_COUNTRY_NO             = $request->country2;
                $customer_add->STATE                    = $request->state2;
                $customer_add->CITY                     = $request->city2;
                $customer_add->POST_CODE                = $request->post_code2;
                $customer_add->F_ADDRESS_TYPE_NO        = $request->addresstype2;
                $customer_add->F_CUSTOMER_NO            = $request->customer_id_;
                $customer_add->IS_ACTIVE                = 1;
                $customer_add->save();

            }else{
                $customer_add                           = new CustomerAddress();
                $customer_add->NAME                     = $request->customeraddress;
                $customer_add->TEL_NO                   = (int)$request->mobilenoadd;
                $customer_add->ADDRESS_LINE_1           = $request->ad_1;
                $customer_add->ADDRESS_LINE_2           = $request->ad_2;
                $customer_add->ADDRESS_LINE_3           = $request->ad_3;
                $customer_add->ADDRESS_LINE_4           = $request->ad_4;
                $customer_add->LOCATION                 = $request->location;
                $customer_add->F_COUNTRY_NO             = $request->country;
                $customer_add->STATE                    = $request->state;
                $customer_add->CITY                     = $request->city;
                $customer_add->POST_CODE                = $request->post_code;
                $customer_add->F_ADDRESS_TYPE_NO        = 1;
                $customer_add->F_CUSTOMER_NO            = $request->customer_id_;
                $customer_add->IS_ACTIVE                = 1;
                $customer_add->IS_DEFAULT               = 1;
                $customer_add->save();
                $address_pk                             = $customer_add->PK_NO;

                $customer_add                           = new CustomerAddress();
                $customer_add->NAME                     = $request->customeraddress;
                $customer_add->TEL_NO                   = (int)$request->mobilenoadd;
                $customer_add->ADDRESS_LINE_1           = $request->ad_1;
                $customer_add->ADDRESS_LINE_2           = $request->ad_2;
                $customer_add->ADDRESS_LINE_3           = $request->ad_3;
                $customer_add->ADDRESS_LINE_4           = $request->ad_4;
                $customer_add->LOCATION                 = $request->location;
                $customer_add->F_COUNTRY_NO             = $request->country;
                $customer_add->STATE                    = $request->state;
                $customer_add->CITY                     = $request->city;
                $customer_add->POST_CODE                = $request->post_code;
                $customer_add->F_ADDRESS_TYPE_NO        = 2;
                $customer_add->F_CUSTOMER_NO            = $request->customer_id_;
                $customer_add->IS_ACTIVE                = 1;
                $customer_add->save();
                // if (isset($request->booking_create) && $request->booking_create == 1) {
                //     $customer_add                           = new CustomerAddress();
                //     $customer_add->NAME                     = $request->customeraddress2;
                //     $customer_add->TEL_NO                   = (int)$request->mobilenoadd2;
                //     $customer_add->ADDRESS_LINE_1           = $request->ad_12;
                //     $customer_add->ADDRESS_LINE_2           = $request->ad_22;
                //     $customer_add->ADDRESS_LINE_3           = $request->ad_32;
                //     $customer_add->ADDRESS_LINE_4           = $request->ad_42;
                //     $customer_add->LOCATION                 = $request->location2;
                //     $customer_add->F_COUNTRY_NO             = $request->country2;
                //     $customer_add->STATE                    = $request->state2;
                //     $customer_add->CITY                     = $request->city2;
                //     $customer_add->POST_CODE                = $request->post_code2;
                //     $customer_add->F_ADDRESS_TYPE_NO        = $request->addresstype2;
                //     $customer_add->F_CUSTOMER_NO            = $request->customer_id_;
                //     $customer_add->IS_ACTIVE                = 1;
                //     $customer_add->save();
                // }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['status'=>0, 'address_pk' => $e->getMessage() ];
        }
        DB::commit();
        return ['status'=>1,'address_pk'=>$request->address_pk_ ?? 0, 'post_code'=> $request->post_code,'final_address'=>$address_pk ?? 0, 'is_reseller'=>$request->is_reseller];
    }

    public function getPayInfo($order_id,$is_reseller)
    {
        if ($is_reseller == 0) {
            $order_payment = DB::table('ACC_ORDER_PAYMENT as acco')->select('acco.F_ACC_CUSTOMER_PAYMENT_NO','acco.PAYMENT_AMOUNT','accb.PK_NO','accb.CODE')
            ->join('ACC_BANK_TXN as accb','accb.F_CUSTOMER_PAYMENT_NO','acco.F_ACC_CUSTOMER_PAYMENT_NO')
            ->where('acco.ORDER_NO',$order_id)
            ->get();
        }else{
            $order_payment = DB::table('ACC_ORDER_PAYMENT as acco')->select('acco.F_ACC_RESELLER_PAYMENT_NO','acco.PAYMENT_AMOUNT','accb.PK_NO','accb.CODE')
            ->join('ACC_BANK_TXN as accb','accb.F_RESELLER_PAYMENT_NO','acco.F_ACC_RESELLER_PAYMENT_NO')
            ->where('acco.ORDER_NO',$order_id)
            ->get();
        }

        $html = view('admin.order._payment_details_modal')->withData($order_payment)->render();

        $data['html'] = $html;
        return response()->json($data);
    }

    public function postUpdatedAddress($request, $order_id,$type)
    {
        DB::beginTransaction();
        $auth_id = Auth::user()->PK_NO;
        $role_id = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                            ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                            ->select('F_ROLE_NO')->where('F_USER_NO',$auth_id)->first();
        try {
            $order = Order::find($order_id);
            if ($type == 'sender') {
                $order->FROM_NAME                = $request->from_name;
                $order->FROM_MOBILE              = $request->from_mobile;
                $order->FROM_ADDRESS_LINE_1      = $request->from_add_1;
                $order->FROM_ADDRESS_LINE_2      = $request->from_add_2;
                $order->FROM_ADDRESS_LINE_3      = $request->from_add_3;
                $order->FROM_ADDRESS_LINE_4      = $request->from_add_4;
                $order->FROM_CITY                = $request->from_city;
                $order->FROM_STATE               = $request->from_state;
                $order->FROM_POSTCODE            = $request->from_post_code;
                $order->FROM_COUNTRY             = $request->from_country;
                $order->FROM_F_COUNTRY_NO        = $request->sender_f_country;
            }else{
                $order->DELIVERY_NAME            = $request->delivery_name;
                $order->DELIVERY_MOBILE          = $request->delivery_mobile;
                $order->DELIVERY_ADDRESS_LINE_1  = $request->delivery_add_1;
                $order->DELIVERY_ADDRESS_LINE_2  = $request->delivery_add_2;
                $order->DELIVERY_ADDRESS_LINE_3  = $request->delivery_add_3;
                $order->DELIVERY_ADDRESS_LINE_4  = $request->delivery_add_4;
                $order->DELIVERY_CITY            = $request->delivery_city;
                $order->DELIVERY_STATE           = $request->delivery_state;
                $order->DELIVERY_POSTCODE        = $request->delivery_post_code;
                $order->DELIVERY_COUNTRY         = $request->delivery_country;
                $order->DELIVERY_F_COUNTRY_NO    = $request->receiver_f_country;

                if (($order->PREV_DELIVERY_NAME          != $request->delivery_name
                || $order->PREV_DELIVERY_MOBILE         != $request->delivery_mobile
                || $order->PREV_DELIVERY_ADDRESS_LINE_1 != $request->delivery_add_1
                || $order->PREV_DELIVERY_ADDRESS_LINE_2 != $request->delivery_add_2
                || $order->PREV_DELIVERY_ADDRESS_LINE_3 != $request->delivery_add_3
                || $order->PREV_DELIVERY_ADDRESS_LINE_4 != $request->delivery_add_4
                || $order->PREV_DELIVERY_CITY           != $request->delivery_city
                || $order->PREV_DELIVERY_STATE          != $request->delivery_state
                || $order->PREV_DELIVERY_POSTCODE       != $request->delivery_post_code
                || $order->PREV_DELIVERY_COUNTRY        != $request->delivery_country
                || $order->PREV_DELIVERY_F_COUNTRY_NO   != $request->receiver_f_country)
                && $role_id->F_ROLE_NO != 1) {
                   $order->IS_ADMIN_APPROVAL = 1;
                }

                if ($request->delivery_post_code >= 87000) {
                    BookingDetails::where('F_BOOKING_NO',$order->F_BOOKING_NO)->update(['CURRENT_IS_SM'=>0]);
                }else{
                    BookingDetails::where('F_BOOKING_NO',$order->F_BOOKING_NO)->update(['CURRENT_IS_SM'=>1]);
                }
            }
            $order->save();
        } catch (\Exception $e) {
            DB::rollback();
            return ['status'=>0,'request'=>$e->getMessage()];
        }
        DB::commit();
        return ['status'=>1,'request'=>$order];
    }

    public function postPaymentUncheck($request)
    {
        DB::beginTransaction();
        try {
            if (isset($request['INV_PK_NO']) && !empty($request['INV_PK_NO'])) {
                foreach ($request['INV_PK_NO'] as $key => $value) {
                    $inv_stock = Stock::select('ORDER_PRICE','ORDER_STATUS')->where('PK_NO',$value)->first();
                    $update_book_details = BookingDetails::where('F_INV_STOCK_NO',$value)->first();

                    $checkbox = 'checkbox_value_'.$value;
                    if (empty($request->$checkbox)) {
                        $update_book_details->ORDER_STATUS = 10;
                        $inv_stock->ORDER_STATUS = 10;
                    }
                    $inv_stock->ORDER_PRICE = $request['line_total_costs'][$key];
                    $update_book_details->save();
                    $inv_stock->save();
                }
                Order::where('F_BOOKING_NO',$request->booking_id)->update([
                    'ORDER_BALANCE_USED' => $request->balance_used
                ]);

            }

        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        DB::commit();
        return 1;
    }

    public function ajaxExchangeStock($inv_id)
    {
        $info = Stock::select('F_BOOKING_NO','SKUID')->where('PK_NO',$inv_id)->first();
        $free_item = DB::table('INV_STOCK as i')
                        ->leftJoin('SC_SHIPMENT as s','s.PK_NO','i.F_SHIPPMENT_NO')
                        ->select('i.PK_NO','i.INV_WAREHOUSE_NAME','s.SCH_ARRIVAL_DATE','s.SHIPMENT_STATUS','i.BOX_TYPE','i.SHIPMENT_TYPE','i.F_INV_WAREHOUSE_NO','i.ORDER_STATUS','i.F_SHIPPMENT_NO','i.SKUID')
                        ->where('i.SKUID',$info->SKUID)
                        ->groupBy('i.IG_CODE','i.BOX_TYPE','i.SHIPMENT_TYPE','i.F_SHIPPMENT_NO','i.F_INV_WAREHOUSE_NO')
                        ->whereNull('i.F_BOOKING_NO')
                        ->get();
        $html = view('admin.order.stock_exchange')->withRows($free_item)->withInvpk($inv_id)->render();
        $data['html']       = $html;
        return response()->json($data);
    }

    public function ajaxExchangeStockAction($request)
    {
        DB::beginTransaction();
        try {
            $inv_pk = Stock::select('PK_NO')
                        ->whereNull('F_BOOKING_NO')
                        ->where('F_INV_WAREHOUSE_NO',$request->warehouse)
                        ->where('SKUID',$request->skuid)
                        ->whereRaw('(PRODUCT_STATUS < 420 OR PRODUCT_STATUS IS NULL)');
            if ($request->warehouse == 1) {
                if ($request->box_type != '') {
                    $inv_pk = $inv_pk->where('BOX_TYPE',$request->box_type);
                }else{
                    $inv_pk = $inv_pk->whereNull('BOX_TYPE');
                }
                if ($request->shipment_type != '') {
                    $inv_pk = $inv_pk->where('SHIPMENT_TYPE',$request->shipment_type);
                }else{
                    $inv_pk = $inv_pk->whereNull('SHIPMENT_TYPE');
                }
                if ($request->shipment_no != '') {
                    $inv_pk = $inv_pk->where('F_SHIPPMENT_NO',$request->shipment_no);
                }else{
                    $inv_pk = $inv_pk->whereNull('F_SHIPPMENT_NO');
                }
            }
            $inv_pk = $inv_pk->first();
            //UPDATE NEW INV
            $old_inv_value = Stock::select('PK_NO','F_BOOKING_NO','F_ORDER_NO','F_BOOKING_DETAILS_NO','F_ORDER_DETAILS_NO','PRODUCT_STATUS','BOOKING_STATUS','ORDER_STATUS','ORDER_PRICE')
                                ->where('PK_NO',$request->inv_pk)
                                ->first();

            Stock::where('PK_NO',$inv_pk->PK_NO)
            ->update(['F_BOOKING_NO'=>$old_inv_value->F_BOOKING_NO,'F_ORDER_NO'=>$old_inv_value->F_ORDER_NO,'F_BOOKING_DETAILS_NO'=>$old_inv_value->F_BOOKING_DETAILS_NO,'F_ORDER_DETAILS_NO'=>$old_inv_value->F_ORDER_DETAILS_NO,'PRODUCT_STATUS'=>$old_inv_value->PRODUCT_STATUS,'BOOKING_STATUS'=>$old_inv_value->BOOKING_STATUS,'ORDER_STATUS'=>$old_inv_value->ORDER_STATUS,'ORDER_PRICE'=>$old_inv_value->ORDER_PRICE]);
            //FREE OLD STOCK
            Stock::where('PK_NO',$request->inv_pk)
            ->update(['F_BOOKING_NO'=>null,'F_ORDER_NO'=>null,'F_BOOKING_DETAILS_NO'=>null,'F_ORDER_DETAILS_NO'=>null,'BOOKING_STATUS'=>null,'ORDER_STATUS'=>null,'ORDER_PRICE'=>null]);
            BookingDetails::where('F_INV_STOCK_NO',$request->inv_pk)->update(['F_INV_STOCK_NO'=>$inv_pk->PK_NO]);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        DB::commit();
        return 1;
    }

    public function postDefaultOrderPenalty($request,$id)
    {
        DB::beginTransaction();
        try{
            $role_id = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')->select('F_ROLE_NO')
                ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                ->where('F_USER_NO',Auth::user()->PK_NO)
                ->first();

            if($role_id->F_ROLE_NO == 1){
                /*delete booking child*/
                $booking_details    = BookingDetails::where('F_BOOKING_NO',$id)->get();
                if($booking_details){
                    foreach($booking_details as $child){
                        DB::SELECT("INSERT INTO SLS_BOOKING_DETAILS_AUD (PK_NO, F_BOOKING_NO, F_INV_STOCK_NO, COMMENTS, IS_ACTIVE, F_SS_CREATED_BY, SS_CREATED_ON, F_DELIVERY_ADDRESS, F_SS_COMPANY_NO, IS_SYSTEM_HOLD, IS_ADMIN_HOLD, DISPATCH_STATUS, AIR_FREIGHT, SEA_FREIGHT, IS_FREIGHT, SS_COST, SM_COST, IS_SM, REGULAR_PRICE, INSTALLMENT_PRICE, IS_REGULAR, CURRENT_AIR_FREIGHT, CURRENT_SEA_FREIGHT, CURRENT_IS_FREIGHT, CURRENT_SS_COST, CURRENT_SM_COST, CURRENT_IS_SM, CURRENT_REGULAR_PRICE, CURRENT_INSTALLMENT_PRICE, CURRENT_IS_REGULAR, CURRENT_F_DELIVERY_ADDRESS, ORDER_STATUS, IS_SELF_PICKUP, IS_ADMIN_APPROVAL, IS_READY, ARRIVAL_NOTIFICATION_FLAG, DISPATCH_NOTIFICATION_FLAG, IS_COD_SHELVE_TRANSFER, COMISSION, RTS_COLLECTION_USER_ID, IS_COLLECTED_FOR_RTS, F_BUNDLE_NO, BUNDLE_SEQUENC, COD_RTC_ACK, LINE_PRICE, CHANGE_TYPE) VALUES ('$child->PK_NO', '$child->F_BOOKING_NO', '$child->F_INV_STOCK_NO', '$child->COMMENTS', '$child->IS_ACTIVE', '$child->F_SS_CREATED_BY', '$child->SS_CREATED_ON', '$child->F_DELIVERY_ADDRESS', '$child->F_SS_COMPANY_NO', '$child->IS_SYSTEM_HOLD', '$child->IS_ADMIN_HOLD', '$child->DISPATCH_STATUS', '$child->AIR_FREIGHT', '$child->SEA_FREIGHT', '$child->IS_FREIGHT', '$child->SS_COST', '$child->SM_COST', '$child->IS_SM', '$child->REGULAR_PRICE', '$child->INSTALLMENT_PRICE', '$child->IS_REGULAR', '$child->CURRENT_AIR_FREIGHT', '$child->CURRENT_SEA_FREIGHT', '$child->CURRENT_IS_FREIGHT', '$child->CURRENT_SS_COST', '$child->CURRENT_SM_COST', '$child->CURRENT_IS_SM', '$child->CURRENT_REGULAR_PRICE', '$child->CURRENT_INSTALLMENT_PRICE', '$child->CURRENT_IS_REGULAR', '$child->CURRENT_F_DELIVERY_ADDRESS', '$child->ORDER_STATUS', '$child->IS_SELF_PICKUP', '$child->IS_ADMIN_APPROVAL', '$child->IS_READY', '$child->ARRIVAL_NOTIFICATION_FLAG', '$child->DISPATCH_NOTIFICATION_FLAG', '$child->IS_COD_SHELVE_TRANSFER', '$child->COMISSION', '$child->RTS_COLLECTION_USER_ID', '$child->IS_COLLECTED_FOR_RTS', '$child->F_BUNDLE_NO', '$child->BUNDLE_SEQUENC', '$child->COD_RTC_ACK', '$child->LINE_PRICE', 'ORDER_PENALTY' )");
                    }
                }
                // $booking            = Booking::find($id);
                $order              = Order::select('PK_NO','IS_RESELLER','ORDER_ACTUAL_TOPUP','ORDER_BUFFER_TOPUP')->where('F_BOOKING_NO',$id)->first();
                $penalty_fee        = $request->penalty_amount;

                if($order->ORDER_ACTUAL_TOPUP < $penalty_fee){
                    $msg = 'Penalty amount can not be more than verified amount !';
                    return $this->formatResponse(false, $msg, 'admin.order.list');
                }
                if($order->ORDER_ACTUAL_TOPUP != $order->ORDER_BUFFER_TOPUP){
                    $msg = 'Order payment need to verify first !';
                    return $this->formatResponse(false, $msg, 'admin.order.list');
                }

                BookingDetails::where('F_BOOKING_NO',$id)->delete();
                Stock::where('F_BOOKING_NO',$id)->update(['BOOKING_STATUS' => null, 'F_BOOKING_NO' => null,'F_ORDER_NO' => null,'ORDER_STATUS' => null,'ORDER_PRICE' => null]);
                $order_payment = OrderPayment::where('ORDER_NO',$order->PK_NO)->get();

                if($penalty_fee > 0 ){
                    if($order_payment){
                        foreach($order_payment as $payment) {
                            if($penalty_fee > 0){
                                if( $penalty_fee >= $payment->PAYMENT_AMOUNT  ){
                                    $penalty_fee = $penalty_fee - $payment->PAYMENT_AMOUNT;
                                }else{
                                    OrderPayment::where('PK_NO',$payment->PK_NO)->update(['PAYMENT_AMOUNT' => $penalty_fee]);
                                    if($payment->IS_CUSTOMER == 1){
                                        // $sum_used_payment =  OrderPayment::where('F_ACC_CUSTOMER_PAYMENT_NO',$payment->F_ACC_CUSTOMER_PAYMENT_NO)->sum('PAYMENT_AMOUNT');
                                        $customer_pay = PaymentCustomer::find($payment->F_ACC_CUSTOMER_PAYMENT_NO);
                                        // $new_remaining = $customer_pay->MR_AMOUNT - $sum_used_payment;
                                        $new_remaining = $customer_pay->MR_AMOUNT - $penalty_fee;

                                        PaymentCustomer::where('PK_NO',$payment->F_ACC_CUSTOMER_PAYMENT_NO)->update(['PAYMENT_REMAINING_MR' => $new_remaining ]);
                                    }else{
                                        // $sum_used_payment =  OrderPayment::where('F_ACC_RESELLER_PAYMENT_NO',$payment->F_ACC_RESELLER_PAYMENT_NO)->sum('PAYMENT_AMOUNT');
                                        $reseller_pay = PaymentReseller::find($payment->F_ACC_RESELLER_PAYMENT_NO);
                                        // $new_remaining = $reseller_pay->MR_AMOUNT - $sum_used_payment;
                                        $new_remaining = $reseller_pay->MR_AMOUNT - $penalty_fee;
                                        PaymentReseller::where('PK_NO',$payment->F_ACC_RESELLER_PAYMENT_NO)->update(['PAYMENT_REMAINING_MR' => $new_remaining ]);
                                    }
                                    $penalty_fee = 0;
                                }
                            }else{
                               OrderPayment::where('PK_NO',$payment->PK_NO)->delete();
                                if($payment->IS_CUSTOMER == 1){
                                    $sum_used_payment =  OrderPayment::where('F_ACC_CUSTOMER_PAYMENT_NO',$payment->F_ACC_CUSTOMER_PAYMENT_NO)->sum('PAYMENT_AMOUNT');
                                    $customer_pay = PaymentCustomer::find($payment->F_ACC_CUSTOMER_PAYMENT_NO);
                                    $new_remaining = $customer_pay->MR_AMOUNT - $sum_used_payment;
                                    PaymentCustomer::where('PK_NO',$payment->F_ACC_CUSTOMER_PAYMENT_NO)->update(['PAYMENT_REMAINING_MR' => $new_remaining ]);
                                }else{
                                    $sum_used_payment =  OrderPayment::where('F_ACC_RESELLER_PAYMENT_NO',$payment->F_ACC_RESELLER_PAYMENT_NO)->sum('PAYMENT_AMOUNT');
                                    $reseller_pay = PaymentReseller::find($payment->F_ACC_RESELLER_PAYMENT_NO);
                                    $new_remaining = $reseller_pay->MR_AMOUNT - $sum_used_payment;
                                    PaymentReseller::where('PK_NO',$payment->F_ACC_RESELLER_PAYMENT_NO)->update(['PAYMENT_REMAINING_MR' => $new_remaining ]);
                                }
                            }
                        }
                    }
                }else{
                   OrderPayment::where('ORDER_NO',$order->PK_NO)->delete();
                    foreach($order_payment as $payment) {
                        if($payment->IS_CUSTOMER == 1){
                            $sum_used_payment =  OrderPayment::where('F_ACC_CUSTOMER_PAYMENT_NO',$payment->F_ACC_CUSTOMER_PAYMENT_NO)->sum('PAYMENT_AMOUNT');
                            $customer_pay = PaymentCustomer::find($payment->F_ACC_CUSTOMER_PAYMENT_NO);
                            $new_remaining = $customer_pay->MR_AMOUNT - $sum_used_payment;
                            PaymentCustomer::where('PK_NO',$payment->F_ACC_CUSTOMER_PAYMENT_NO)->update(['PAYMENT_REMAINING_MR' => $new_remaining ]);
                        }else{
                            $sum_used_payment =  OrderPayment::where('F_ACC_RESELLER_PAYMENT_NO',$payment->F_ACC_RESELLER_PAYMENT_NO)->sum('PAYMENT_AMOUNT');
                            $reseller_pay = PaymentReseller::find($payment->F_ACC_RESELLER_PAYMENT_NO);
                            $new_remaining = $reseller_pay->MR_AMOUNT - $sum_used_payment;
                            PaymentReseller::where('PK_NO',$payment->F_ACC_RESELLER_PAYMENT_NO)->update(['PAYMENT_REMAINING_MR' => $new_remaining ]);
                        }
                    }
                }
                Order::where('F_BOOKING_NO',$id)->update(['IS_DEFAULT' => 1, 'ORDER_ACTUAL_TOPUP' => $request->penalty_amount, 'ORDER_BUFFER_TOPUP' => $request->penalty_amount, 'ORDER_BALANCE_USED' => $request->penalty_amount ]);
                Booking::where('PK_NO',$id)->update(['PENALTY_FEE' => $request->penalty_amount]);

                if($order->IS_RESELLER == 0){
                    $payment_remaining_mr = PaymentCustomer::where('F_CUSTOMER_NO',$order->F_CUSTOMER_NO)->sum('PAYMENT_REMAINING_MR');
                    Customer::where('PK_NO',$order->F_CUSTOMER_NO)->update(['CUM_BALANCE' => $payment_remaining_mr]);
                }else{
                    $payment_remaining_mr = PaymentReseller::where('F_RESELLER_NO',$order->F_RESELLER_NO)->sum('PAYMENT_REMAINING_MR');
                    Reseller::where('PK_NO',$order->F_RESELLER_NO)->update(['CUM_BALANCE' => $payment_remaining_mr]);
                }
                $msg = 'Penalty successfully done !';
                // $order_payment = OrderPayment::where('ORDER_NO',$order->PK_NO)->get();
                // echo '<pre>';
                // echo '======================<br>';
                // print_r($order_payment);
                // echo '<br>======================<br>';
                // exit();
                /*if($orders->ORDER_BALANCE_USED > 0){
                    BookingDetails::where('F_BOOKING_NO',$id)->update(['ORDER_STATUS' => 10]);
                    Stock::where('F_BOOKING_NO',$id)->update(['ORDER_STATUS' => 10]);
                    Order::where('F_BOOKING_NO',$id)->update(['ORDER_BALANCE_USED' => null]);
                }
                if($request->penalty_amount < 0){
                    return $this->formatResponse(false, 'Amount must be greater than 0 !', 'admin.order.list', 0);
                }
                if($orders->IS_RESELLER == 0){

                    if ($orders->ORDER_ACTUAL_TOPUP == 0) {
                        return $this->formatResponse(false, 'Please made payment against this order !', 'admin.order.list', 0);
                    }elseif ($orders->ORDER_ACTUAL_TOPUP < $request->penalty_amount) {
                        return $this->formatResponse(false, 'Varified payment is less than penalty amount !', 'admin.order.list', 0);
                    }else{
                        $customer_payment = OrderPayment::where('ORDER_NO',$orders->PK_NO)->pluck('F_ACC_CUSTOMER_PAYMENT_NO');
                        OrderPayment::where('ORDER_NO',$orders->PK_NO)->delete();
                        $customer_payment = PaymentCustomer::whereIn('PK_NO',$customer_payment)->pluck('PAYMENT_REMAINING_MR','PK_NO');
                        $customer_payment = json_decode(json_encode($customer_payment), true);
                        arsort($customer_payment);
                        $key_of_max       = key($customer_payment);
                        $max_val          = max($customer_payment);

                        if ($max_val >= $request->penalty_amount) {
                            PaymentCustomer::where('PK_NO',$key_of_max)->update(['PAYMENT_REMAINING_MR' => $max_val-$request->penalty_amount]);
                        }else{
                            $remaining_penalty = $request->penalty_amount;
                            foreach ($customer_payment as $key => $value) {
                                $txn = AccBankTxn::select('IS_MATCHED')->where('F_CUSTOMER_PAYMENT_NO',$key)->first();

                                if ($value > 0 && $value <= $remaining_penalty && $txn->IS_MATCHED == 1) {
                                    $remaining_penalty = $remaining_penalty - $value;

                                    PaymentCustomer::where('PK_NO',$key)->update(['PAYMENT_REMAINING_MR' => 0]);
                                }elseif($value > 0 && $value > $remaining_penalty && $txn->IS_MATCHED == 1){
                                    PaymentCustomer::where('PK_NO',$key)->update(['PAYMENT_REMAINING_MR' => $value - $remaining_penalty]);
                                    $remaining_penalty = 0;

                                }elseif($remaining_penalty > 0){
                                    //DO NOT ALLOW WTHOUT VERIFIED PAYMENT
                                    return $this->formatResponse(false, 'Varified payment is less than penalty amount !', 'admin.order.list', 0);
                                }
                            }
                        }
                    }
                    Booking::where('PK_NO',$id)->update(['PENALTY_FEE' => DB::raw('PENALTY_FEE +'. $request->penalty_amount)]);
                }else{
                    $reseller_payment = OrderPayment::where('ORDER_NO',$orders->PK_NO)->pluck('F_ACC_RESELLER_PAYMENT_NO');
                    if ($orders->ORDER_ACTUAL_TOPUP == 0) {
                        return $this->formatResponse(false, 'Please made payment against this order !', 'admin.order.list', 0);
                    }elseif ($orders->ORDER_ACTUAL_TOPUP < $request->penalty_amount) {
                        return $this->formatResponse(false, 'Varified payment is less than penalty amount !', 'admin.order.list', 0);
                    }else{
                        OrderPayment::where('ORDER_NO',$orders->PK_NO)->delete();
                        $reseller_payment = PaymentReseller::whereIn('PK_NO',$reseller_payment)->pluck('PAYMENT_REMAINING_MR','PK_NO');

                        $reseller_payment = json_decode(json_encode($reseller_payment), true);
                        arsort($reseller_payment);
                        $key_of_max       = key($reseller_payment);
                        $max_val          = max($reseller_payment);

                        if ($max_val >= $request->penalty_amount) {
                            PaymentReseller::where('PK_NO',$key_of_max)->update(['PAYMENT_REMAINING_MR' => $max_val-$request->penalty_amount]);
                        }else{
                            $remaining_penalty = $request->penalty_amount;
                            foreach ($reseller_payment as $key => $value) {
                                $txn = AccBankTxn::select('IS_MATCHED')->where('F_CUSTOMER_PAYMENT_NO',$key)->first();

                                if ($value > 0 && $value <= $request->penalty_amount && $txn->IS_MATCHED == 1) {
                                    $remaining_penalty = $remaining_penalty - $value;
                                    PaymentReseller::where('PK_NO',$key)->update(['PAYMENT_REMAINING_MR' => 0]);
                                }elseif($value > 0 && $value > $request->penalty_amount && $txn->IS_MATCHED == 1){
                                    PaymentReseller::where('PK_NO',$key)->update(['PAYMENT_REMAINING_MR' => $value - $remaining_penalty]);
                                    $remaining_penalty = 0;
                                }elseif($remaining_penalty > 0){
                                    //DO NOT ALLOW WTHOUT VERIFIED PAYMENT
                                    return $this->formatResponse(false, 'Varified payment is less than penalty amount !', 'admin.order.list', 0);
                                }
                            }
                        }
                    }
                    Booking::where('PK_NO',$id)->update(['PENALTY_FEE' => DB::raw('PENALTY_FEE +'. $request->penalty_amount)]);

                }
            */
            }else{
                abort(404);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.order_default_action.list','');
        }
        DB::commit();
        return $this->formatResponse(true, $msg, 'admin.order_default_action.list','');
    }


    public function postCancel($id,$request)
    {
        $msg = '';
        $route = 'admin.order.list';
        DB::beginTransaction();
        try{
            $role_id = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')->select('F_ROLE_NO')
                ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                ->where('F_USER_NO',Auth::user()->PK_NO)
                ->first();
            if($request->submit == 'request_accept'){
                if($role_id->F_ROLE_NO == 1){
                    $cancel_fee         = $request->amount;
                    $booking            = Booking::find($id);
                    $order              = Order::where('F_BOOKING_NO',$id)->first();
                    if($cancel_fee > 0 ){
                        if($order->ORDER_ACTUAL_TOPUP != $order->ORDER_BUFFER_TOPUP){
                            $msg = 'Order payment need to verify first';
                            return $this->formatResponse(false, $msg, 'admin.order.list');
                        }
                    }
                    $booking->CANCEL_FEE    = $cancel_fee;
                    $booking->CANCEL_NOTE   = $request->note;
                    $booking->CANCELED_AT   = date('Y-m-d H:i:s');
                    $booking->update();
                    //$booking_child = BookingDetails::where('F_BOOKING_NO',$id)->get();
                    /*delete booking child*/
                    $booking_details    = BookingDetails::where('F_BOOKING_NO',$id)->get();
                    if($booking_details){
                        foreach($booking_details as $child){
                            DB::SELECT("INSERT INTO SLS_BOOKING_DETAILS_AUD (PK_NO, F_BOOKING_NO, F_INV_STOCK_NO, COMMENTS, IS_ACTIVE, F_SS_CREATED_BY, SS_CREATED_ON, F_DELIVERY_ADDRESS, F_SS_COMPANY_NO, IS_SYSTEM_HOLD, IS_ADMIN_HOLD, DISPATCH_STATUS, AIR_FREIGHT, SEA_FREIGHT, IS_FREIGHT, SS_COST, SM_COST, IS_SM, REGULAR_PRICE, INSTALLMENT_PRICE, IS_REGULAR, CURRENT_AIR_FREIGHT, CURRENT_SEA_FREIGHT, CURRENT_IS_FREIGHT, CURRENT_SS_COST, CURRENT_SM_COST, CURRENT_IS_SM, CURRENT_REGULAR_PRICE, CURRENT_INSTALLMENT_PRICE, CURRENT_IS_REGULAR, CURRENT_F_DELIVERY_ADDRESS, ORDER_STATUS, IS_SELF_PICKUP, IS_ADMIN_APPROVAL, IS_READY, ARRIVAL_NOTIFICATION_FLAG, DISPATCH_NOTIFICATION_FLAG, IS_COD_SHELVE_TRANSFER, COMISSION, RTS_COLLECTION_USER_ID, IS_COLLECTED_FOR_RTS, F_BUNDLE_NO, BUNDLE_SEQUENC, COD_RTC_ACK, LINE_PRICE, CHANGE_TYPE) VALUES ('$child->PK_NO', '$child->F_BOOKING_NO', '$child->F_INV_STOCK_NO', '$child->COMMENTS', '$child->IS_ACTIVE', '$child->F_SS_CREATED_BY', '$child->SS_CREATED_ON', '$child->F_DELIVERY_ADDRESS', '$child->F_SS_COMPANY_NO', '$child->IS_SYSTEM_HOLD', '$child->IS_ADMIN_HOLD', '$child->DISPATCH_STATUS', '$child->AIR_FREIGHT', '$child->SEA_FREIGHT', '$child->IS_FREIGHT', '$child->SS_COST', '$child->SM_COST', '$child->IS_SM', '$child->REGULAR_PRICE', '$child->INSTALLMENT_PRICE', '$child->IS_REGULAR', '$child->CURRENT_AIR_FREIGHT', '$child->CURRENT_SEA_FREIGHT', '$child->CURRENT_IS_FREIGHT', '$child->CURRENT_SS_COST', '$child->CURRENT_SM_COST', '$child->CURRENT_IS_SM', '$child->CURRENT_REGULAR_PRICE', '$child->CURRENT_INSTALLMENT_PRICE', '$child->CURRENT_IS_REGULAR', '$child->CURRENT_F_DELIVERY_ADDRESS', '$child->ORDER_STATUS', '$child->IS_SELF_PICKUP', '$child->IS_ADMIN_APPROVAL', '$child->IS_READY', '$child->ARRIVAL_NOTIFICATION_FLAG', '$child->DISPATCH_NOTIFICATION_FLAG', '$child->IS_COD_SHELVE_TRANSFER', '$child->COMISSION', '$child->RTS_COLLECTION_USER_ID', '$child->IS_COLLECTED_FOR_RTS', '$child->F_BUNDLE_NO', '$child->BUNDLE_SEQUENC', '$child->COD_RTC_ACK', '$child->LINE_PRICE', 'ORDER_CANCEL' )");

                        }
                    }

                    BookingDetails::where('F_BOOKING_NO',$id)->delete();
                    Stock::where('F_BOOKING_NO',$id)->update(['BOOKING_STATUS' => null, 'F_BOOKING_NO' => null,'F_ORDER_NO' => null,'ORDER_STATUS' => null,'ORDER_PRICE' => null]);
                    $order_payment = OrderPayment::where('ORDER_NO',$order->PK_NO)->get();
                    //dd($order_payment);
                    if($cancel_fee > 0 ){
                        if($order_payment){
                            foreach($order_payment as $payment) {
                                if($cancel_fee > 0){
                                    if( $cancel_fee >= $payment->PAYMENT_AMOUNT  ){
                                        $cancel_fee = $cancel_fee - $payment->PAYMENT_AMOUNT;
                                    }else{
                                        OrderPayment::where('PK_NO',$payment->PK_NO)->update(['PAYMENT_AMOUNT' => $cancel_fee]);
                                        $cancel_fee = 0;
                                        if($payment->IS_CUSTOMER == 1){
                                            $sum_used_payment =  OrderPayment::where('F_ACC_CUSTOMER_PAYMENT_NO',$payment->F_ACC_CUSTOMER_PAYMENT_NO)->sum('PAYMENT_AMOUNT');
                                            $customer_pay = PaymentCustomer::find($payment->F_ACC_CUSTOMER_PAYMENT_NO);
                                            $new_remaining = $customer_pay->MR_AMOUNT - $sum_used_payment;

                                            PaymentCustomer::where('PK_NO',$payment->F_ACC_CUSTOMER_PAYMENT_NO)->update(['PAYMENT_REMAINING_MR' => $new_remaining ]);
                                        }else{
                                            $sum_used_payment =  OrderPayment::where('F_ACC_RESELLER_PAYMENT_NO',$payment->F_ACC_RESELLER_PAYMENT_NO)->sum('PAYMENT_AMOUNT');
                                            $reseller_pay = PaymentReseller::find($payment->F_ACC_RESELLER_PAYMENT_NO);
                                            $new_remaining = $reseller_pay->MR_AMOUNT - $sum_used_payment;
                                            PaymentReseller::where('PK_NO',$payment->F_ACC_RESELLER_PAYMENT_NO)->update(['PAYMENT_REMAINING_MR' => $new_remaining ]);
                                        }
                                    }
                                }else{
                                   OrderPayment::where('PK_NO',$payment->PK_NO)->delete();
                                    if($payment->IS_CUSTOMER == 1){
                                        $sum_used_payment =  OrderPayment::where('F_ACC_CUSTOMER_PAYMENT_NO',$payment->F_ACC_CUSTOMER_PAYMENT_NO)->sum('PAYMENT_AMOUNT');
                                        $customer_pay = PaymentCustomer::find($payment->F_ACC_CUSTOMER_PAYMENT_NO);
                                        $new_remaining = $customer_pay->MR_AMOUNT - $sum_used_payment;
                                        PaymentCustomer::where('PK_NO',$payment->F_ACC_CUSTOMER_PAYMENT_NO)->update(['PAYMENT_REMAINING_MR' => $new_remaining ]);
                                    }else{
                                        $sum_used_payment =  OrderPayment::where('F_ACC_RESELLER_PAYMENT_NO',$payment->F_ACC_RESELLER_PAYMENT_NO)->sum('PAYMENT_AMOUNT');
                                        $reseller_pay = PaymentReseller::find($payment->F_ACC_RESELLER_PAYMENT_NO);
                                        $new_remaining = $reseller_pay->MR_AMOUNT - $sum_used_payment;
                                        PaymentReseller::where('PK_NO',$payment->F_ACC_RESELLER_PAYMENT_NO)->update(['PAYMENT_REMAINING_MR' => $new_remaining ]);
                                    }
                                }
                            }
                        }
                    }else{
                       OrderPayment::where('ORDER_NO',$order->PK_NO)->delete();
                        foreach($order_payment as $payment) {
                            if($payment->IS_CUSTOMER == 1){
                                $sum_used_payment =  OrderPayment::where('F_ACC_CUSTOMER_PAYMENT_NO',$payment->F_ACC_CUSTOMER_PAYMENT_NO)->sum('PAYMENT_AMOUNT');
                                $customer_pay = PaymentCustomer::find($payment->F_ACC_CUSTOMER_PAYMENT_NO);
                                $new_remaining = $customer_pay->MR_AMOUNT - $sum_used_payment;
                                PaymentCustomer::where('PK_NO',$payment->F_ACC_CUSTOMER_PAYMENT_NO)->update(['PAYMENT_REMAINING_MR' => $new_remaining ]);
                            }else{
                                $sum_used_payment =  OrderPayment::where('F_ACC_RESELLER_PAYMENT_NO',$payment->F_ACC_RESELLER_PAYMENT_NO)->sum('PAYMENT_AMOUNT');
                                $reseller_pay = PaymentReseller::find($payment->F_ACC_RESELLER_PAYMENT_NO);
                                $new_remaining = $reseller_pay->MR_AMOUNT - $sum_used_payment;
                                PaymentReseller::where('PK_NO',$payment->F_ACC_RESELLER_PAYMENT_NO)->update(['PAYMENT_REMAINING_MR' => $new_remaining ]);
                            }
                        }
                    }

                    Order::where('F_BOOKING_NO',$id)->update(['IS_CANCEL' => 1]);
                    // Order::where('F_BOOKING_NO',$id)->update(['IS_CANCEL' => 1, 'ORDER_ACTUAL_TOPUP' => $booking->CANCEL_FEE, 'ORDER_BUFFER_TOPUP' => $booking->CANCEL_FEE, 'ORDER_BALANCE_USED' => $booking->CANCEL_FEE ]);
                    if($booking->IS_RESELLER == 0){
                        $payment_remaining_mr = PaymentCustomer::where('F_CUSTOMER_NO',$booking->F_CUSTOMER_NO)->sum('PAYMENT_REMAINING_MR');

                        Customer::where('PK_NO',$booking->F_CUSTOMER_NO)->update(['CUM_BALANCE' => $payment_remaining_mr]);
                    }else{
                        $payment_remaining_mr = PaymentReseller::where('F_RESELLER_NO',$booking->F_RESELLER_NO)->sum('PAYMENT_REMAINING_MR');
                        Reseller::where('PK_NO',$booking->F_RESELLER_NO)->update(['CUM_BALANCE' => $payment_remaining_mr]);
                    }
                    $email = new EmailNotification();
                    $email->TYPE = 'Cancel';
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
                    $msg = 'Cacnel request accepted successfully !';
                    $route = 'admin.order.canceled';
                }else{
                    abort(404);
                }

            }elseif($request->submit == 'request_deny'){
                if($role_id->F_ROLE_NO == 1){
                    $booking                = Booking::find($id);
                    $booking->CANCEL_FEE    = null;
                    $booking->update();
                    Order::where('F_BOOKING_NO',$id)->update(['IS_CANCEL' => 0]);
                    $msg = 'Cacnel request denied successfully !';
                    $route = 'admin.order.list';
                }else{
                    abort(404);
                }
            }elseif($request->submit == 'request'){
                $booking                = Booking::find($id);
                $booking->CANCEL_FEE    = $request->amount;
                $booking->CANCEL_NOTE   = $request->note;
                $booking->CANCEL_REQUEST_BY = Auth::user()->PK_NO;
                $booking->CANCEL_REQUEST_AT = date('Y-m-d H:i:s');
                $booking->update();
                Order::where('F_BOOKING_NO',$id)->update(['IS_CANCEL' => 2]);
                $msg = 'Cacnel request successfully done !';
                $route = 'admin.order.cancelrequest';
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.order.list');
        }
        DB::commit();
        return $this->formatResponse(true, $msg, $route);

    }

    public function postReturnOrder($request, $id)
    {
        $curr_date = date('Y-m-d');

        DB::beginTransaction();
        try{
            $child      = BookingDetails::find($request->booking_details_id);
            $order      = Order::where('F_BOOKING_NO',$child->F_BOOKING_NO)->first();
            $booking    = Booking::where('PK_NO',$child->F_BOOKING_NO)->first();
            $child_price = $child->LINE_PRICE;

            $check_exist_child = BookingDetails::where('F_BOOKING_NO',$child->F_BOOKING_NO)->where('PK_NO','!=',$child->PK_NO)->count();
            if($check_exist_child > 0 ){
                //(Partial return)
                $IS_RETURN = 1;
            }else{
                //(Full return)
                $IS_RETURN = 2;
            }

            //Good Condition ( Wrong Product ) -> Full Value + Return Postage
            //Good Condition ( Right Product )
            if($request->stock_condition == 1 || $request->stock_condition == 4){
                if($child){
                    $penalty            = $request->credit_amount ?? 0;
                    $postage            = $request->discount_amount ?? 0 ;
                    $paid_amount        = $request->paid_amount ;
                    $amount_to_credit   = $paid_amount - $penalty + $postage;
                    if($child->DISPATCH_STATUS  == 40){
                        DB::table('SLS_BOOKING')->where('PK_NO', $child->F_BOOKING_NO)->update([
                            'CUSTOMER_POSTAGE' => DB::raw('CUSTOMER_POSTAGE + '.$postage.''),
                            'PENALTY_FEE' => DB::raw('PENALTY_FEE + '.$penalty.''),
                            'TOTAL_PRICE_BEFORE_RETURN' => DB::raw('TOTAL_PRICE_BEFORE_RETURN + '.$child->LINE_PRICE.''),
                            'IS_RETURN' => $IS_RETURN]);

                        Stock::where('PK_NO',$child->F_INV_STOCK_NO)->update(['BOOKING_STATUS' => null, 'F_BOOKING_NO' => null,'F_ORDER_NO' => null,'ORDER_STATUS' => null,'ORDER_PRICE' => null]);
                        BookingDetails::where('PK_NO',$request->booking_details_id)->delete();

                        if($order->IS_RESELLER == 0){
                            $payment                            = new PaymentCustomer();
                            $payment->F_CUSTOMER_NO             = $order->F_CUSTOMER_NO;
                            $payment->F_PAYMENT_CURRENCY_NO     = 2;
                            $payment->PAYMENT_DATE              = date('Y-m-d');
                            $payment->MR_AMOUNT                 = $amount_to_credit;
                            $payment->PAYMENT_REMAINING_MR      = $amount_to_credit;
                            $payment->F_PAYMENT_ACC_NO          = 14;
                            $payment->PAYMENT_NOTE              = $request->return_note;
                            $payment->PAID_BY                   = Auth::user()->PK_NO;
                            $payment->SLIP_NUMBER               = $order->PK_NO.$child->PK_NO;
                            $payment->PAYMENT_CONFIRMED_STATUS  = 1;
                            $payment->PAYMENT_TYPE              = 3;
                            $payment->F_BOOKING_NO_FOR_PAYMENT_TYPE3  = $child->F_BOOKING_NO;
                            $payment->save();
                            $pay_pk_no                  = $payment->PK_NO;
                            $type                       = 'customer';
                            DB::statement('CALL PROC_CUSTOMER_PAYMENT(:pay_pk_no, :type);',array( $pay_pk_no,$type));

                        }
                    }
                }
            }

            //Bad Condition ( Item Returned - broken / out of order )
            if($request->stock_condition == 2){
                if($child){
                    $item_value   = $request->credit_amount ?? 0;
                    $postage   = $request->discount_amount ?? 0 ;
                   // $paid_amount   = $request->paid_amount ;
                    $amount_to_credit   =  $item_value + $postage;

                    if($child->DISPATCH_STATUS  == 40){

                        DB::table('SLS_BOOKING')->where('PK_NO', $child->F_BOOKING_NO)->update([
                            'CUSTOMER_POSTAGE' => DB::raw('CUSTOMER_POSTAGE + '.$postage.''),
                            'TOTAL_PRICE_BEFORE_RETURN' => DB::raw('TOTAL_PRICE_BEFORE_RETURN + '.$child->LINE_PRICE.''),
                            'IS_RETURN' => $IS_RETURN]);

                        Stock::where('PK_NO',$child->F_INV_STOCK_NO)->update(['BOOKING_STATUS' => null, 'F_BOOKING_NO' => null,'F_ORDER_NO' => null,'ORDER_STATUS' => null,'ORDER_PRICE' => null,'PRODUCT_STATUS' => 420]);
                        BookingDetails::where('PK_NO',$request->booking_details_id)->delete();

                        if($order->IS_RESELLER == 0){
                            $payment                            = new PaymentCustomer();
                            $payment->F_CUSTOMER_NO             = $order->F_CUSTOMER_NO;
                            $payment->F_PAYMENT_CURRENCY_NO     = 2;
                            $payment->PAYMENT_DATE              = date('Y-m-d');
                            $payment->MR_AMOUNT                 = $amount_to_credit;
                            $payment->PAYMENT_REMAINING_MR      = $amount_to_credit;
                            $payment->F_PAYMENT_ACC_NO          = 14;
                            $payment->PAYMENT_NOTE              = $request->return_note;
                            $payment->PAID_BY                   = Auth::user()->PK_NO;
                            $payment->SLIP_NUMBER               = $order->PK_NO.$child->PK_NO;
                            $payment->PAYMENT_CONFIRMED_STATUS  = 1;
                            $payment->PAYMENT_TYPE              = 3;
                            $payment->F_BOOKING_NO_FOR_PAYMENT_TYPE3  = $child->F_BOOKING_NO;
                            $payment->save();
                            $pay_pk_no                  = $payment->PK_NO;
                            $type                       = 'customer';
                            DB::statement('CALL PROC_CUSTOMER_PAYMENT(:pay_pk_no, :type);',array( $pay_pk_no,$type));
                        }
                    }
                }
            }


            //Bad Condition ( Item Not Returned - faulty / broken / out of order )
            if($request->stock_condition == 3){
                if($child){
                    $discount_amount   = $request->discount_amount ?? 0 ;
                   // $credit_amount      = $request->credit_amount;
                    if($child->DISPATCH_STATUS  == 40){

                       Booking::where('PK_NO',$child->F_BOOKING_NO)->update(['IS_RETURN' => $IS_RETURN]);

                        if($order->IS_RESELLER == 0){
                            $payment                            = new PaymentCustomer();
                            $payment->F_CUSTOMER_NO             = $order->F_CUSTOMER_NO;
                            $payment->F_PAYMENT_CURRENCY_NO     = 2;
                            $payment->PAYMENT_DATE              = date('Y-m-d',strtotime($request->payment_date));
                            $payment->MR_AMOUNT                 = $discount_amount;
                            $payment->PAYMENT_REMAINING_MR      = $discount_amount;
                            $payment->F_PAYMENT_ACC_NO          = 14;
                            $payment->PAYMENT_NOTE              = $request->return_note;
                            $payment->PAID_BY                   = Auth::user()->PK_NO;
                            $payment->SLIP_NUMBER               = $order->PK_NO.$child->PK_NO;
                            $payment->PAYMENT_CONFIRMED_STATUS  = 1;
                            $payment->PAYMENT_TYPE              = 3;
                            $payment->F_BOOKING_NO_FOR_PAYMENT_TYPE3  = $child->F_BOOKING_NO;
                            $payment->save();
                            $pay_pk_no                  = $payment->PK_NO;
                            $type                       = 'customer';
                            DB::statement('CALL PROC_CUSTOMER_PAYMENT(:pay_pk_no, :type);',array( $pay_pk_no,$type));
                        }
                    }
                }
            }

            //Bad Condition ( Item Returned - defect )
            if($request->stock_condition == 5){
                if($child){
                    $item_value   = $request->credit_amount ?? 0;
                    $postage   = $request->discount_amount ?? 0 ;
                   // $paid_amount   = $request->paid_amount ;
                    $amount_to_credit   =  $item_value + $postage;

                    if($child->DISPATCH_STATUS  == 40){

                        DB::table('SLS_BOOKING')->where('PK_NO', $child->F_BOOKING_NO)->update([
                            'CUSTOMER_POSTAGE' => DB::raw('CUSTOMER_POSTAGE + '.$postage.''),
                            'TOTAL_PRICE_BEFORE_RETURN' => DB::raw('TOTAL_PRICE_BEFORE_RETURN + '.$child->LINE_PRICE.''),
                            'IS_RETURN' => $IS_RETURN]);

                        Stock::where('PK_NO',$child->F_INV_STOCK_NO)->update(['BOOKING_STATUS' => null, 'F_BOOKING_NO' => null,'F_ORDER_NO' => null,'ORDER_STATUS' => null,'ORDER_PRICE' => null,'PRODUCT_STATUS' => 90]);
                        BookingDetails::where('PK_NO',$request->booking_details_id)->delete();

                        if($order->IS_RESELLER == 0){
                            $payment                            = new PaymentCustomer();
                            $payment->F_CUSTOMER_NO             = $order->F_CUSTOMER_NO;
                            $payment->F_PAYMENT_CURRENCY_NO     = 2;
                            $payment->PAYMENT_DATE              = date('Y-m-d');
                            $payment->MR_AMOUNT                 = $amount_to_credit;
                            $payment->PAYMENT_REMAINING_MR      = $amount_to_credit;
                            $payment->F_PAYMENT_ACC_NO          = 14;
                            $payment->PAYMENT_NOTE              = $request->return_note;
                            $payment->PAID_BY                   = Auth::user()->PK_NO;
                            $payment->SLIP_NUMBER               = $order->PK_NO.$child->PK_NO;
                            $payment->PAYMENT_CONFIRMED_STATUS  = 1;
                            $payment->PAYMENT_TYPE              = 3;
                            $payment->F_BOOKING_NO_FOR_PAYMENT_TYPE3  = $child->F_BOOKING_NO;
                            $payment->save();
                            $pay_pk_no                  = $payment->PK_NO;
                            $type                       = 'customer';
                            DB::statement('CALL PROC_CUSTOMER_PAYMENT(:pay_pk_no, :type);',array( $pay_pk_no,$type));
                        }
                    }
                }
            }
            $email = new EmailNotification();
            $email->TYPE = 'Return';
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

            DB::SELECT("INSERT INTO SLS_BOOKING_DETAILS_AUD (PK_NO, F_BOOKING_NO, F_INV_STOCK_NO, COMMENTS, IS_ACTIVE, F_SS_CREATED_BY, SS_CREATED_ON, F_DELIVERY_ADDRESS, F_SS_COMPANY_NO, IS_SYSTEM_HOLD, IS_ADMIN_HOLD, DISPATCH_STATUS, AIR_FREIGHT, SEA_FREIGHT, IS_FREIGHT, SS_COST, SM_COST, IS_SM, REGULAR_PRICE, INSTALLMENT_PRICE, IS_REGULAR, CURRENT_AIR_FREIGHT, CURRENT_SEA_FREIGHT, CURRENT_IS_FREIGHT, CURRENT_SS_COST, CURRENT_SM_COST, CURRENT_IS_SM, CURRENT_REGULAR_PRICE, CURRENT_INSTALLMENT_PRICE, CURRENT_IS_REGULAR, CURRENT_F_DELIVERY_ADDRESS, ORDER_STATUS, IS_SELF_PICKUP, IS_ADMIN_APPROVAL, IS_READY, ARRIVAL_NOTIFICATION_FLAG, DISPATCH_NOTIFICATION_FLAG, IS_COD_SHELVE_TRANSFER, COMISSION, RTS_COLLECTION_USER_ID, IS_COLLECTED_FOR_RTS, F_BUNDLE_NO, BUNDLE_SEQUENC, COD_RTC_ACK, LINE_PRICE, CHANGE_TYPE,RETURN_TYPE,RETURN_DATE) VALUES ('$child->PK_NO', '$child->F_BOOKING_NO', '$child->F_INV_STOCK_NO', '$child->COMMENTS', '$child->IS_ACTIVE', '$child->F_SS_CREATED_BY', '$child->SS_CREATED_ON', '$child->F_DELIVERY_ADDRESS', '$child->F_SS_COMPANY_NO', '$child->IS_SYSTEM_HOLD', '$child->IS_ADMIN_HOLD', '$child->DISPATCH_STATUS', '$child->AIR_FREIGHT', '$child->SEA_FREIGHT', '$child->IS_FREIGHT', '$child->SS_COST', '$child->SM_COST', '$child->IS_SM', '$child->REGULAR_PRICE', '$child->INSTALLMENT_PRICE', '$child->IS_REGULAR', '$child->CURRENT_AIR_FREIGHT', '$child->CURRENT_SEA_FREIGHT', '$child->CURRENT_IS_FREIGHT', '$child->CURRENT_SS_COST', '$child->CURRENT_SM_COST', '$child->CURRENT_IS_SM', '$child->CURRENT_REGULAR_PRICE', '$child->CURRENT_INSTALLMENT_PRICE', '$child->CURRENT_IS_REGULAR', '$child->CURRENT_F_DELIVERY_ADDRESS', '$child->ORDER_STATUS', '$child->IS_SELF_PICKUP', '$child->IS_ADMIN_APPROVAL', '$child->IS_READY', '$child->ARRIVAL_NOTIFICATION_FLAG', '$child->DISPATCH_NOTIFICATION_FLAG', '$child->IS_COD_SHELVE_TRANSFER', '$child->COMISSION', '$child->RTS_COLLECTION_USER_ID', '$child->IS_COLLECTED_FOR_RTS', '$child->F_BUNDLE_NO', '$child->BUNDLE_SEQUENC', '$child->COD_RTC_ACK', '$child->LINE_PRICE', 'ORDER_RETURN', '$request->stock_condition', '$curr_date' )");

        }catch(\Exception $e){
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.dispatched.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Order Item return entry successfull !', 'admin.dispatched.list');
    }
}
