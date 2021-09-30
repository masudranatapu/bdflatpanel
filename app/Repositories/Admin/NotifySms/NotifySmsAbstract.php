<?php
namespace App\Repositories\Admin\NotifySms;

use DB;
use Carbon\Carbon;
use App\Traits\SMS;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Booking;
use App\Models\NotifySms;
use App\Traits\RepoResponse;
use App\Models\SmsNotification;
use App\Models\EmailNotification;

class NotifySmsAbstract implements NotifySmsInterface
{
    use RepoResponse;
    use SMS;

    protected $notifySms;
    protected $notifyEmail;

    public function __construct(NotifySms $notifySms,EmailNotification $notifyEmail)
    {
        $this->notifySms    = $notifySms;
        $this->notifyEmail  = $notifyEmail;
    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        if($request->type == 'success'){
            $data = $this->notifySms->orderBy('F_BOOKING_NO', 'DESC')->where('IS_SEND',1)->get();
        }else{
            $data = $this->notifySms
            ->where('IS_SEND',0)
            ->orderBy('PK_NO', 'DESC')
            ->get();
            // ->orderBy('F_BOOKING_NO', 'ASC')
            // dd($data);
        }

        return $this->formatResponse(true, '', 'admin.notify_sms.list', $data);
    }

    public function getEmailIndex($request)
    {
        if($request->type == 'success'){
            $data = $this->notifyEmail->orderBy('F_BOOKING_NO', 'DESC')->where('IS_SEND',1)->get();
        }else{
            $data = $this->notifyEmail
            ->where('IS_SEND',0)
            ->orderBy('PK_NO', 'DESC')
            ->get();
        }
        return $this->formatResponse(true, '', 'admin.notify_sms.list', $data);
    }

    public function getSendSms($id){

        try {
            $noti       = NotifySms::find($id);
            $send_to    = ltrim($noti->MOBILE_NO,'+');
            $sms_body   = $noti->BODY;
            $smsRes = $this->sendsms($send_to, $sms_body);

            if($smsRes){
                NotifySms::where('PK_NO',$id)->update(['IS_SEND' => 1, 'SEND_AT' => date('Y-m-d H:i:s')]);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.notify_sms.list');
        }
        return $this->formatResponse(true, 'SMS has been send successfully !', 'admin.notify_sms.list');
    }

    public function getSendEmail($id){

        // try {
        //     $noti       = NotifySms::find($id);
        //     $send_to    = ltrim($noti->MOBILE_NO,'+');
        //     $sms_body   = $noti->BODY;
        //     $smsRes = $this->sendsms($send_to, $sms_body);

        //     if($smsRes){
        //         NotifySms::where('PK_NO',$id)->update(['IS_SEND' => 1, 'SEND_AT' => date('Y-m-d H:i:s')]);
        //     }

        // } catch (\Exception $e) {
        //     DB::rollback();
        //     return $this->formatResponse(false, $e->getMessage(), 'admin.notify_sms.list');
        // }
        // return $this->formatResponse(true, 'SMS has been send successfully !', 'admin.notify_sms.list');
    }

    public function getSendAllSms($request){

        try {
            $notis   = NotifySms::where('IS_SEND',0)->get();
            if($notis){
                foreach ($notis as $key => $value) {
                    $send_to    = ltrim($value->MOBILE_NO,'+');
                    $sms_body   = $value->BODY;
                    $smsRes     = $this->sendsms($send_to, $sms_body);
                    if($smsRes){
                        NotifySms::where('PK_NO',$value->PK_NO)->update(['IS_SEND' => 1, 'SEND_AT' => date('Y-m-d H:i:s')]);

                }
                }
            }
        } catch (\Exception $e) {
        DB::rollback();
        return $this->formatResponse(false, $e->getMessage(), 'admin.notify_sms.list');
        }
        return $this->formatResponse(true, 'SMS has been send successfully !', 'admin.notify_sms.list');

    }

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

    public function getOrderDefault($request){

        try {
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
            if ($data) {
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
                        Order::where('F_BOOKING_NO',$value1->F_BOOKING_NO)->update(['DEFAULT_AT'=>date('Y-m-d H:i:s'),'DEFAULT_TYPE' => 1, 'IS_DEFAULT' => 1]);
                        $this->generateDefaultSms($value1->F_BOOKING_NO, $last_msg_noti, $daysAdd7);
                    }
                    if (($total_order_item_count == $air_option_2_count) && ($total_order_item_count > 0)) {
                        Order::where('F_BOOKING_NO',$value1->F_BOOKING_NO)->update(['DEFAULT_AT'=>date('Y-m-d H:i:s'),'DEFAULT_TYPE' => 2, 'IS_DEFAULT' => 1]);
                        $this->generateDefaultSms($value1->F_BOOKING_NO, $last_msg_noti, $daysAdd7);
                    }
                    if (($total_order_item_count == $sea_option_1_count) && ($total_order_item_count > 0)) {
                        Order::where('F_BOOKING_NO',$value1->F_BOOKING_NO)->update(['DEFAULT_AT'=>date('Y-m-d H:i:s'),'DEFAULT_TYPE' => 3, 'IS_DEFAULT' => 1]);
                        $this->generateDefaultSms($value1->F_BOOKING_NO, $last_msg_noti, $daysAdd7);
                    }
                    if (($total_order_item_count == $sea_option_2_count) && ($total_order_item_count > 0)) {
                        Order::where('F_BOOKING_NO',$value1->F_BOOKING_NO)->update(['DEFAULT_AT'=>date('Y-m-d H:i:s'),'DEFAULT_TYPE' => 4, 'IS_DEFAULT' => 1]);
                        $this->generateDefaultSms($value1->F_BOOKING_NO, $last_msg_noti, $daysAdd7);
                    }
                    if (($total_order_item_count == $ready_option_1_count) && ($total_order_item_count > 0)) {
                        Order::where('F_BOOKING_NO',$value1->F_BOOKING_NO)->update(['DEFAULT_AT'=>date('Y-m-d H:i:s'),'DEFAULT_TYPE' => 5, 'IS_DEFAULT' => 1]);
                        $this->generateDefaultSms($value1->F_BOOKING_NO, $last_msg_noti, $daysAdd7);
                    }
                    if (($total_order_item_count == $ready_option_2_count) && ($total_order_item_count > 0)) {
                        Order::where('F_BOOKING_NO',$value1->F_BOOKING_NO)->update(['DEFAULT_AT'=>date('Y-m-d H:i:s'),'DEFAULT_TYPE' => 6, 'IS_DEFAULT' => 1]);
                        $this->generateDefaultSms($value1->F_BOOKING_NO, $last_msg_noti, $daysAdd7);
                    }
                }
            }
        } catch (\Exception $e) {
        DB::rollback();
        return $this->formatResponse(false, $e->getMessage(), 'admin.notify_sms.list');
        }
        return $this->formatResponse(true, 'SMS has been send successfully !', 'admin.notify_sms.list');

    }

    public function getEmailBody($id)
    {
        $data = EmailNotification::find($id);

        if ($data->TYPE == 'Order Create') {
            $data['order_info'] = Booking::select('SLS_BOOKING.DISCOUNT','SLS_ORDER.DELIVERY_NAME','SLS_ORDER.DELIVERY_MOBILE','SLS_ORDER.DELIVERY_ADDRESS_LINE_1','SLS_ORDER.DELIVERY_ADDRESS_LINE_2','SLS_ORDER.DELIVERY_ADDRESS_LINE_3','SLS_ORDER.DELIVERY_ADDRESS_LINE_4','SLS_ORDER.DELIVERY_CITY','SLS_ORDER.DELIVERY_POSTCODE','SLS_ORDER.DELIVERY_STATE','SLS_ORDER.DELIVERY_COUNTRY','SLS_BOOKING.BOOKING_SALES_AGENT_NAME','SLS_BOOKING.RECONFIRM_TIME','SLS_BOOKING.BOOKING_NO','SLS_BOOKING.F_CUSTOMER_NO','SLS_BOOKING.F_RESELLER_NO')
            ->join('SLS_ORDER','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')
            ->where('SLS_ORDER.F_BOOKING_NO', $data->F_BOOKING_NO)
            ->first();

            $data['stock_info'] = Stock::select('INV_STOCK.PRD_VARINAT_NAME','INV_STOCK.IG_CODE as igcode','INV_STOCK.PRD_VARIANT_IMAGE_PATH','SLS_BOOKING_DETAILS.*',DB::raw('(select ifnull(count(PK_NO),0) from INV_STOCK where IG_CODE = igcode and F_BOOKING_NO = '.$data->F_BOOKING_NO.') as qty'))
            ->join('SLS_BOOKING_DETAILS','INV_STOCK.PK_NO','SLS_BOOKING_DETAILS.F_INV_STOCK_NO')
            ->where('INV_STOCK.F_BOOKING_NO', $data->F_BOOKING_NO)
            ->groupBy('INV_STOCK.IG_CODE')
            ->get();
            foreach ($data['stock_info'] as $key => $value) {
                if($value->CURRENT_IS_REGULAR == 1){
                    $value->unit_price = $value->CURRENT_REGULAR_PRICE;
                }else{
                    $value->unit_price = $value->CURRENT_INSTALLMENT_PRICE;
                }
                if($value->CURRENT_IS_SM == 1){
                    $value->postage = $value->CURRENT_SM_COST;
                }else{
                    $value->postage = $value->CURRENT_SS_COST;
                }
                if($value->CURRENT_IS_FREIGHT == 1){
                    $value->freight = $value->CURRENT_AIR_FREIGHT;
                }elseif($value->CURRENT_IS_FREIGHT == 2){
                    $value->freight = $value->CURRENT_SEA_FREIGHT;
                }else{
                    $value->freight = 0;
                }
            }
            return $data;
        }elseif($data->TYPE == 'Arrival'){
            $order_info = Order::join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')->where('SLS_ORDER.F_BOOKING_NO', $data->F_BOOKING_NO)->first();
            return $order_info;
        }elseif($data->TYPE == 'Default'){
            $order_info = Order::join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')->where('SLS_ORDER.F_BOOKING_NO', $data->F_BOOKING_NO)->first();
            return $order_info;
        }elseif($data->TYPE == 'Dispatch'){
            $order_info = Order::join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')->where('SLS_ORDER.F_BOOKING_NO', $data->F_BOOKING_NO)->first();
            return $order_info;
        }elseif($data->TYPE == 'Cancel'){
            $order_info = Order::join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')->where('SLS_ORDER.F_BOOKING_NO', $data->F_BOOKING_NO)->first();
            return $order_info;
        }elseif($data->TYPE == 'Return'){
            $order_info = Order::join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')->where('SLS_ORDER.F_BOOKING_NO', $data->F_BOOKING_NO)->first();
            return $order_info;
        }elseif($data->TYPE == 'greeting'){
            $order_info = Order::join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')->where('SLS_ORDER.F_BOOKING_NO', $data->F_BOOKING_NO)->first();
            return $order_info;
        }
    }
}
