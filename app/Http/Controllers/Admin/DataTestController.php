<?php

namespace App\Http\Controllers\Admin;
use Str;
use Session;
use App\User;
use Carbon\Carbon;
use App\Models\Auth;
use App\Traits\MAIL;
use App\Models\Order;
use App\Models\Stock;
use App\Http\Requests;
use App\Models\Booking;
use App\Models\NotifySms;
use App\Models\ProdImgLib;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\CustomerAddress;
use App\Models\SmsNotification;
use App\Models\EmailNotification;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\Admin\LoginRequest;

class DataTestController extends Controller
{
    /**
     * the model instance
     * @var User
     */
    protected $user;
    /**
     * The Guard implementation.
     *
     * @var Authenticator
     */
    protected $auth;

    /**
     * Create a new authentication controller instance.
     *
     * @param  Authenticator  $auth
     * @return void
     */

    public function __construct(Guard $auth, User $user)
    {
        $this->user = $user;
        $this->auth = $auth;
    }
    use MAIL;

    public function generateDefaultSms($booking_id,$last_msg_noti,$daysAdd7)
    {
        try{
            $booking = Booking::select('BOOKING_NO','IS_RESELLER','F_CUSTOMER_NO','F_RESELLER_NO')->where('PK_NO',$booking_id)->first();
            if ($last_msg_noti) {
                $sms_body = "RM0.00 AZURAMART:#ORD-".$booking->BOOKING_NO." Arrival SMS sent on ".$last_msg_noti.", Please pay rest by ".$daysAdd7." to avoid default. For more please Whatsapp http://linktr.ee/azuramart";
            }else{
                $sms_body = "RM0.00 AZURAMART:#ORD-".$booking->BOOKING_NO." Please pay rest by ".$daysAdd7." to avoid default. For more please Whatsapp http://linktr.ee/azuramart";
            }

            $noti = new SmsNotification();
            $noti->TYPE = 'Default';
            $noti->F_BOOKING_NO = $booking_id;
            //$noti->F_BOOKING_DETAIL_NO = $value->PK_NO;
            $noti->BODY = $sms_body;
            $noti->F_SS_CREATED_BY = 1;
            if($booking->IS_RESELLER == 0){
                $noti->CUSTOMER_NO = $booking->F_CUSTOMER_NO;
                $noti->IS_RESELLER = 0;
            }else{
                $noti->RESELLER_NO = $booking->F_RESELLER_NO;
                $noti->IS_RESELLER = 1;
            }
            $noti->save();
        } catch (\Exception $e) {
            DB::rollback();
            return 0;
        }
        DB::commit();
        return 1;
    }

    public function dataTest(Request $request)
    {
        try{
            $notis   = EmailNotification::where('IS_SEND',0)->whereNotNull('EMAIL')->get();
            if($notis){
                foreach ($notis as $key => $value) {
                    if($value->TYPE == 'Arrival' && isset($value->EMAIL)){
                        $emailRes   = $this->orderArrivalEmail($value->F_BOOKING_NO,$value->EMAIL);
                        if($emailRes){
                            EmailNotification::where('PK_NO',$value->PK_NO)->update(['IS_SEND' => 1, 'SEND_AT' => date('Y-m-d H:i:s')]);
                        }
                    }elseif($value->TYPE == 'Default' && isset($value->EMAIL)){
                        $emailRes   = $this->orderDefaultEmail($value->F_BOOKING_NO,$value->EMAIL);
                        if($emailRes){
                            EmailNotification::where('PK_NO',$value->PK_NO)->update(['IS_SEND' => 1, 'SEND_AT' => date('Y-m-d H:i:s')]);
                        }
                    }elseif($value->TYPE == 'Dispatch' && isset($value->EMAIL)){
                        $emailRes   = $this->orderDispatchEmail($value->F_BOOKING_NO,$value->EMAIL);
                        if($emailRes){
                            EmailNotification::where('PK_NO',$value->PK_NO)->update(['IS_SEND' => 1, 'SEND_AT' => date('Y-m-d H:i:s')]);
                        }
                    }elseif($value->TYPE == 'Cancel' && isset($value->EMAIL)){
                        $emailRes   = $this->orderCancelEmail($value->F_BOOKING_NO,$value->EMAIL);
                        if($emailRes){
                            EmailNotification::where('PK_NO',$value->PK_NO)->update(['IS_SEND' => 1, 'SEND_AT' => date('Y-m-d H:i:s')]);
                        }
                    }elseif($value->TYPE == 'Order Create' && isset($value->EMAIL)){
                        $emailRes   = $this->orderCreateEndEmail($value->F_BOOKING_NO,$value->EMAIL);
                        if($emailRes){
                            EmailNotification::where('PK_NO',$value->PK_NO)->update(['IS_SEND' => 1, 'SEND_AT' => date('Y-m-d H:i:s')]);
                        }
                    }elseif($value->TYPE == 'Return' && isset($value->EMAIL)){
                        $emailRes   = $this->orderReturntEmail($value->F_BOOKING_NO,$value->EMAIL);
                        if($emailRes){
                            EmailNotification::where('PK_NO',$value->PK_NO)->update(['IS_SEND' => 1, 'SEND_AT' => date('Y-m-d H:i:s')]);
                        }
                    }elseif($value->TYPE == 'greeting' && isset($value->EMAIL)){
                        $emailRes   = $this->greetingEmail($value->F_BOOKING_NO,$value->EMAIL);
                        if($emailRes){
                            EmailNotification::where('PK_NO',$value->PK_NO)->update(['IS_SEND' => 1, 'SEND_AT' => date('Y-m-d H:i:s')]);
                        }
                    }
                }
            }
            // $data = ProductVariant::whereNull('URL_SLUG')->get();
            // if ($data) {
            //     foreach ($data as $key => $value) {
            //         $str = strtolower($value->VARIANT_NAME);
            //         $prod = Str::slug($str);
            //         ProductVariant::where('PK_NO',$value->PK_NO)->update(['URL_SLUG' => $prod]);
            //     }
            // }
            // echo '<pre>';
            // echo '======================<br>';
            // print_r($data);
            // echo '<br>======================<br>';
            // exit();
            // $box = DB::table('SC_BOX_INV_STOCK')->get();
            // foreach ($box as $key => $value) {
            //     // $shipment = DB::table('');
            //     $box = DB::table('SC_BOX')->where('PK_NO',$value->F_BOX_NO)->first();
            //     $stock = DB::table('INV_STOCK')->where('PK_NO', $value->F_INV_STOCK_NO)->update(['F_BOX_NO' => $value->F_BOX_NO, 'BOX_BARCODE' =>$box->BOX_NO ,'BOX_TYPE'=>'SEA']);
            // }
            // $stock = Stock::select('PK_NO','BOX_BARCODE')->whereNotNull('F_BOX_NO')->get();
            // foreach ($stock as $key => $value) {
            //     $box_type = substr($value->BOX_BARCODE, 0, 1);
            //     if ($box_type == 1) {
            //         $box_type = 'AIR';
            //     }else{
            //         $box_type = 'SEA';
            //     }
            //     Stock::where('PK_NO', $value->PK_NO)->update(['BOX_TYPE'=>$box_type]);
        // }
            //update IG_CODE in INV_STOCK table
            /*
            $data = DB::table('PRD_VARIANT_SETUP')->select('PK_NO','MRK_ID_COMPOSITE_CODE')->get();
            foreach ($data as $key => $value) {
                DB::table('INV_STOCK')->where('F_PRD_VARIANT_NO', $value->PK_NO)->update(['IG_CODE' => $value->MRK_ID_COMPOSITE_CODE]);
            }*/


            //Update CODE in PRD_MASTER_SETUP table

            /* $prod =  DB::table('PRD_MASTER_SETUP')->orderBy('PK_NO')->get();
            $code = 1001;
            foreach ($prod as $key => $value) {
                $pcode = $code+$key;
                DB::table('PRD_MASTER_SETUP')->where('PK_NO',$value->PK_NO)->update(['MKT_CODE' => $pcode]);
            }*/




            /*$customer = CustomerAddress::select('F_CUSTOMER_NO','F_RESELLER_NO')->where('F_ADDRESS_TYPE_NO',1)->groupBy('F_CUSTOMER_NO','F_RESELLER_NO')->get();

            foreach ($customer as $key => $value) {
                if ($value->F_CUSTOMER_NO) {
                    $first_cus = CustomerAddress::select('PK_NO')->where('F_CUSTOMER_NO',$value->F_CUSTOMER_NO)->where('F_ADDRESS_TYPE_NO',1)->first();
                    CustomerAddress::where('PK_NO',$first_cus->PK_NO)->update(['IS_DEFAULT' => 1]);
                }else{
                    $first_res = CustomerAddress::select('PK_NO')->where('F_RESELLER_NO',$value->F_RESELLER_NO)->where('F_ADDRESS_TYPE_NO',1)->first();
                    CustomerAddress::where('PK_NO',$first_res->PK_NO)->update(['IS_DEFAULT' => 1]);
                }
            }
            */

           /*$data = DB::SELECT("SELECT PK_NO, F_CUSTOMER_NO, DELIVERY_POSTCODE FROM SLS_ORDER WHERE F_RESELLER_NO IS NULL");

            if($data){
                foreach ($data as $key => $value) {
                    $pk = DB::table('SLS_CUSTOMERS_ADDRESS')->where('F_CUSTOMER_NO',$value->F_CUSTOMER_NO)->where('POST_CODE',$value->DELIVERY_POSTCODE)->where('F_ADDRESS_TYPE_NO',1)->first();
                        if($pk){
                            DB::table('SLS_ORDER')->where('PK_NO',$value->PK_NO)->update(['F_TO_ADDRESS' => $pk->PK_NO]);

                        }

                }
            }
           */
          /*
            $data1 = DB::SELECT("SELECT PK_NO, F_RESELLER_NO, DELIVERY_POSTCODE FROM SLS_ORDER WHERE F_CUSTOMER_NO IS NULL ");

            if($data1){
                foreach ($data1 as $key => $value1) {
                    //   $pk = DB::table('SLS_CUSTOMERS_ADDRESS')->where('F_RESELLER_NO',$value1->F_RESELLER_NO)->where('POST_CODE',$value1->DELIVERY_POSTCODE)->where('F_ADDRESS_TYPE_NO',1)->first();
                    $pk = DB::table('SLS_CUSTOMERS_ADDRESS')->where('F_RESELLER_NO',$value1->F_RESELLER_NO)->where('F_ADDRESS_TYPE_NO',1)->first();
                        if($pk){
                            DB::table('SLS_ORDER')->where('PK_NO',$value1->PK_NO)->update(['F_TO_ADDRESS' => $pk->PK_NO]);

                        }
                }
            }
            */
            /*
            $daysAdd7      = Carbon::now()->addDays(7)->format('d/m/Y');
            $weeksSub5     = Carbon::now()->subWeeks(5);
            $weeksSub13    = Carbon::now()->subWeeks(13);
            $weeksSub26    = Carbon::now()->subWeeks(26);
            $now           = Carbon::now();

            $data = Order::with('bookingDetails')
                        ->join('SLS_BOOKING as b','b.PK_NO','SLS_ORDER.F_BOOKING_NO')
                        ->select('F_BOOKING_NO','b.RECONFIRM_TIME','ORDER_ACTUAL_TOPUP','ORDER_BUFFER_TOPUP')
                        ->where('DISPATCH_STATUS','<',40)
                        ->whereNull('DEFAULT_AT')
                        ->get();

            foreach ($data as $key1 => $value1) {
                $total_order_item_count = count($value1['bookingDetails']);
                $air_option_1_count     = 0;
                $air_option_2_count     = 0;
                $sea_option_1_count     = 0;
                $sea_option_2_count     = 0;
                $ready_option_1_count   = 0;
                $ready_option_2_count   = 0;

                $last_msg = NotifySms::select('SEND_AT')
                            ->where('F_BOOKING_NO',$value1->F_BOOKING_NO)
                            ->whereRaw('(IS_SEND = 1)')
                            ->where('TYPE','Arrival')
                            ->orderBy('SEND_AT', 'DESC')
                            ->first();
                $total_sms_sent = NotifySms::where('F_BOOKING_NO',$value1->F_BOOKING_NO)
                            ->whereRaw('(IS_SEND = 1 OR IS_SEND = 2)')
                            ->where('TYPE','Arrival')
                            ->count();

                $last_msg_noti  = isset($last_msg) ? Carbon::parse($last_msg->SEND_AT)->format('d/m/Y') : null;
                $last_msg       = isset($last_msg) ? Carbon::parse($last_msg->SEND_AT)->addWeeks(2) : null;

                foreach ($value1['bookingDetails'] as $key2 => $value2) {

                    //AIR-OPTION1
                    if (($value1->ORDER_ACTUAL_TOPUP != $value1->ORDER_BUFFER_TOPUP || ($value1->ORDER_ACTUAL_TOPUP == 0)) && ($value1->RECONFIRM_TIME < $weeksSub5) && isset($last_msg) && ($last_msg < $now) && ($total_sms_sent >= $total_order_item_count) && $value2->CURRENT_IS_REGULAR == 1 && ($value2->ARRIVAL_NOTIFICATION_FLAG == 0 || $value2->ARRIVAL_NOTIFICATION_FLAG == 1) && $value2->stock->FINAL_PREFFERED_SHIPPING_METHOD == 'AIR') {

                        $air_option_1_count++;
                    }

                    //AIR-OPTION2
                    if (($value1->ORDER_ACTUAL_TOPUP != $value1->ORDER_BUFFER_TOPUP || ($value1->ORDER_ACTUAL_TOPUP == 0)) && ($value1->RECONFIRM_TIME < $weeksSub13) && isset($last_msg) && ($last_msg < $now) && ($total_sms_sent >= $total_order_item_count) && $value2->CURRENT_IS_REGULAR == 0 && ($value2->ARRIVAL_NOTIFICATION_FLAG == 0 || $value2->ARRIVAL_NOTIFICATION_FLAG == 1) && $value2->stock->FINAL_PREFFERED_SHIPPING_METHOD == 'AIR') {

                        $air_option_2_count++;
                    }

                    //SEA-OPTION1
                    if (($value1->ORDER_ACTUAL_TOPUP != $value1->ORDER_BUFFER_TOPUP || ($value1->ORDER_ACTUAL_TOPUP == 0)) && ($value1->RECONFIRM_TIME < $weeksSub13) && isset($last_msg) && ($last_msg < $now) && ($total_sms_sent >= $total_order_item_count) && $value2->CURRENT_IS_REGULAR == 1 && ($value2->ARRIVAL_NOTIFICATION_FLAG == 0 || $value2->ARRIVAL_NOTIFICATION_FLAG == 1) && $value2->stock->FINAL_PREFFERED_SHIPPING_METHOD == 'SEA') {

                        $sea_option_1_count++;
                    }

                    //SEA-OPTION2
                    if (($value1->ORDER_ACTUAL_TOPUP != $value1->ORDER_BUFFER_TOPUP || ($value1->ORDER_ACTUAL_TOPUP == 0)) && ($value1->RECONFIRM_TIME < $weeksSub26) && isset($last_msg) && ($last_msg < $now) && ($total_sms_sent >= $total_order_item_count) && $value2->CURRENT_IS_REGULAR == 0 && ($value2->ARRIVAL_NOTIFICATION_FLAG == 0 || $value2->ARRIVAL_NOTIFICATION_FLAG == 1) && $value2->stock->FINAL_PREFFERED_SHIPPING_METHOD == 'SEA') {

                        $sea_option_2_count++;
                    }

                    //READY-OPTION1
                    if (($value1->ORDER_ACTUAL_TOPUP != $value1->ORDER_BUFFER_TOPUP || ($value1->ORDER_ACTUAL_TOPUP == 0)) && ($value1->RECONFIRM_TIME < $weeksSub5) && ($value2->ARRIVAL_NOTIFICATION_FLAG == 2) && ($value2->CURRENT_IS_REGULAR == 1)) {

                        $ready_option_1_count++;
                    }

                    //READY-OPTION2
                    if (($value1->ORDER_ACTUAL_TOPUP != $value1->ORDER_BUFFER_TOPUP || ($value1->ORDER_ACTUAL_TOPUP == 0)) && ($value1->RECONFIRM_TIME < $weeksSub13) && ($value2->ARRIVAL_NOTIFICATION_FLAG == 2) && ($value2->CURRENT_IS_REGULAR == 0)) {

                        $ready_option_2_count++;
                    }
                }

                //ORDER IS DEFAULT
                if (($total_order_item_count == $air_option_1_count) && ($total_order_item_count > 0)) {
                    Order::where('F_BOOKING_NO',$value1->F_BOOKING_NO)->update(['DEFAULT_AT'=>date('Y-m-d H:i:s'),'DEFAULT_TYPE' => 1]);
                    $this->generateDefaultSms($value1->F_BOOKING_NO, $last_msg_noti, $daysAdd7);
                }
                if (($total_order_item_count == $air_option_2_count) && ($total_order_item_count > 0)) {
                    Order::where('F_BOOKING_NO',$value1->F_BOOKING_NO)->update(['DEFAULT_AT'=>date('Y-m-d H:i:s'),'DEFAULT_TYPE' => 2]);
                    $this->generateDefaultSms($value1->F_BOOKING_NO, $last_msg_noti, $daysAdd7);
                }
                if (($total_order_item_count == $sea_option_1_count) && ($total_order_item_count > 0)) {
                    Order::where('F_BOOKING_NO',$value1->F_BOOKING_NO)->update(['DEFAULT_AT'=>date('Y-m-d H:i:s'),'DEFAULT_TYPE' => 3]);
                    $this->generateDefaultSms($value1->F_BOOKING_NO, $last_msg_noti, $daysAdd7);
                }
                if (($total_order_item_count == $sea_option_2_count) && ($total_order_item_count > 0)) {
                    Order::where('F_BOOKING_NO',$value1->F_BOOKING_NO)->update(['DEFAULT_AT'=>date('Y-m-d H:i:s'),'DEFAULT_TYPE' => 4]);
                    $this->generateDefaultSms($value1->F_BOOKING_NO, $last_msg_noti, $daysAdd7);
                }
                if (($total_order_item_count == $ready_option_1_count) && ($total_order_item_count > 0)) {
                    Order::where('F_BOOKING_NO',$value1->F_BOOKING_NO)->update(['DEFAULT_AT'=>date('Y-m-d H:i:s'),'DEFAULT_TYPE' => 5]);
                    $this->generateDefaultSms($value1->F_BOOKING_NO, $last_msg_noti, $daysAdd7);
                }
                if (($total_order_item_count == $ready_option_2_count) && ($total_order_item_count > 0)) {
                    Order::where('F_BOOKING_NO',$value1->F_BOOKING_NO)->update(['DEFAULT_AT'=>date('Y-m-d H:i:s'),'DEFAULT_TYPE' => 6]);
                    $this->generateDefaultSms($value1->F_BOOKING_NO, $last_msg_noti, $daysAdd7);
                }
            } */



           /* $auths = DB::table('auths')->join('admin_users','admin_users.auth_id','auths.id')
                            ->select('admin_users.*','auths.*','auths.id as auth_p_id','auths.created_at as auth_created_at','auths.updated_at as auths_updated_at')->get();

                foreach ($auths as $key => $value) {
                    DB::table('SA_USER')->insert(
                        [
                            'PK_NO' => $value->auth_p_id,
                            'USERNAME' => $value->username,
                            'FIRST_NAME' => $value->first_name,
                            'LAST_NAME' => $value->last_name,
                            'DESIGNATION' => $value->designation,
                            'EMAIL' => $value->email,
                            'MOBILE_NO' => $value->mobile_no,
                            'PASSWORD' => $value->password,
                            'GENDER' => $value->gender,
                            'PROFILE_PIC' => $value->profile_pic,
                            'PROFILE_PIC_URL' => $value->profile_pic_url,
                            'IS_FIRST_LOGIN' => 1,
                            'USER_TYPE' => 0,
                            'CAN_LOGIN' => 1,
                            'STATUS' => 1,
                            'F_AGENT_NO' => $value->F_AGENT_NO,
                            'F_PARENT_USER_ID' => $value->f_parent_user_id,
                            'IS_SECONDARY_USER' => $value->is_secondary_user,
                            'CREATED_AT' => $value->auth_created_at,
                            'UPDATED_AT' => $value->auths_updated_at,
                        ]

                    );

                }
                $user_groups = DB::table('user_groups')->get();

                foreach ($user_groups as $key => $value) {
                    DB::table('SA_USER_GROUP')->insert(
                        [
                            'PK_NO' => $value->id,
                            'GROUP_NAME' => $value->group_name,
                            'STATUS' => 1,
                            'CREATED_AT' => $value->created_at,
                            'UPDATED_AT' => $value->updated_at
                        ]
                    );
                }
                $user_groups = DB::table('user_groups')->get();

                foreach ($user_groups as $key => $value) {
                    DB::table('SA_USER_GROUP_ROLE')->insert(
                        [
                            'F_USER_GROUP_NO' => $value->id,
                            'F_ROLE_NO' => $value->ROLE_ID,
                            'STATUS' => 1,
                            'CREATED_AT' => $value->created_at,
                            'UPDATED_AT' => $value->updated_at
                        ]
                    );
                }

                $auth_role = DB::table('auth_role')->get();

                foreach ($auth_role as $key => $value) {
                    DB::table('SA_USER_GROUP_USERS')->insert(
                        [
                            'F_GROUP_NO' => $value->USER_GROUP_ID,
                            'F_USER_NO' => $value->auth_id,
                            'STATUS' => 1,
                            'CREATED_AT' => $value->created_at,
                            'UPDATED_AT' => $value->updated_at
                        ]
                    );
                }

                $permission_groups = DB::table('permission_groups')->get();

                foreach ($permission_groups as $key => $value) {
                    DB::table('SA_PERMISSION_GROUP')->insert(
                        [
                            'PK_NO' => $value->id,
                            'NAME' => $value->group_name,
                            'STATUS' => 1,
                            'CREATED_AT' => $value->created_at,
                            'UPDATED_AT' => $value->updated_at
                        ]
                    );
                }

                $permissions = DB::table('permissions')->get();

                foreach ($permissions as $key => $value) {
                    if ($value->permission_group_id != 40) {
                        DB::table('SA_PERMISSION_GROUP_DTL')->insert(
                            [
                                'PK_NO' => $value->id,
                                'NAME' => $value->name,
                                'DISPLAY_NAME' => $value->display_name,
                                'F_PERMISSION_GROUP_NO' => $value->permission_group_id,
                                'STATUS' => 1,
                                'CREATED_AT' => $value->created_at,
                                'UPDATED_AT' => $value->updated_at
                            ]
                        );
                    }
                }
                $roles = DB::table('roles')->get();

                foreach ($roles as $key => $value) {
                    DB::table('SA_ROLE')->insert(
                        [
                            'PK_NO' => $value->id,
                            'NAME' => $value->role_name,
                            'STATUS' => 1,
                            'CREATED_AT' => $value->created_at,
                            'UPDATED_AT' => $value->updated_at
                        ]
                    );
                }

                $role_permission = DB::table('role_permission')->get();

                foreach ($role_permission as $key => $value) {
                    DB::table('SA_ROLE_DTL')->insert(
                        [
                            'PK_NO' => $value->id,
                            'PERMISSIONS' => $value->permissions,
                            'F_ROLE_NO' => $value->role_id,
                            'CREATED_AT' => $value->created_at,
                            'UPDATED_AT' => $value->updated_at
                        ]
                    );
                }

                */

        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        DB::commit();
        return 1;
        // ALTER TABLE SS_PO_CODE AUTO_INCREMENT = 1;
    }

}
