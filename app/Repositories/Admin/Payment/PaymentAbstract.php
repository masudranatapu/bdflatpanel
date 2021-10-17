<?php
namespace App\Repositories\Admin\Payment;

use DB;
use Auth;
use App\User;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\BankList;
use App\Models\Customer;
use App\Models\Reseller;
use App\Models\AccBankTxn;
use App\Models\PaymentBank;
use App\Models\OrderPayment;
use App\Models\PaymentExfer;
use App\Models\PaymentIxfer;
use App\Traits\RepoResponse;
use App\Models\AuthUserGroup;
use App\Models\RefundRequest;
use App\Models\BookingDetails;
use App\Models\PaymentBankAcc;
use App\Models\PaymentCustomer;
use App\Models\PaymentReseller;
use App\Models\PartyPaymentMethod;
use App\Models\PartyPaymentMethodHead;

class PaymentAbstract implements PaymentInterface
{
    use RepoResponse;
    protected $payment;
    protected $paymentReseller;

    public function __construct(PaymentCustomer $payment, PaymentReseller $paymentReseller)
    {
        $this->payment   = $payment;
        $this->paymentReseller   = $paymentReseller;

    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        $data = array();
        $query1 =  PaymentCustomer::select('ACC_BANK_TXN.PK_NO','ACC_CUSTOMER_PAYMENTS.SS_CREATED_ON','ACC_BANK_TXN.TXN_DATE','ACC_BANK_TXN.MATCHED_ON','ACC_BANK_TXN.IS_MATCHED','ACC_CUSTOMER_PAYMENTS.F_SS_CREATED_BY','ACC_BANK_TXN.CODE','ACC_CUSTOMER_PAYMENTS.PK_NO AS PAYMENT_PK_NO','ACC_CUSTOMER_PAYMENTS.CUSTOMER_NAME','ACC_CUSTOMER_PAYMENTS.CUSTOMER_NO','ACC_CUSTOMER_PAYMENTS.PAID_BY','ACC_CUSTOMER_PAYMENTS.SLIP_NUMBER','ACC_CUSTOMER_PAYMENTS.ATTACHMENT_PATH','ACC_CUSTOMER_PAYMENTS.F_PAYMENT_ACC_NO','ACC_CUSTOMER_PAYMENTS.PAYMENT_BANK_NAME','ACC_CUSTOMER_PAYMENTS.PAYMENT_ACCOUNT_NAME','ACC_CUSTOMER_PAYMENTS.MR_AMOUNT', 'ACC_CUSTOMER_PAYMENTS.PAYMENT_CONFIRMED_STATUS','ACC_CUSTOMER_PAYMENTS.IS_COD', 'ACC_CUSTOMER_PAYMENTS.PAYMENT_DATE', DB::raw("'CUSTOMER' as TYPE") )
        ->leftJoin('ACC_BANK_TXN','ACC_BANK_TXN.F_CUSTOMER_PAYMENT_NO', '=', 'ACC_CUSTOMER_PAYMENTS.PK_NO')
        //->where('ACC_CUSTOMER_PAYMENTS.PAYMENT_CONFIRMED_STATUS',0)
       // ->where('ACC_BANK_TXN.IS_CUS_RESELLER_BANK_RECONCILATION',1)
       // ->where('ACC_CUSTOMER_PAYMENTS.IS_COD',1)
        ->orderBy('ACC_CUSTOMER_PAYMENTS.PAYMENT_DATE', 'DESC');

        $query2 =  PaymentReseller::select('ACC_BANK_TXN.PK_NO','ACC_RESELLER_PAYMENTS.SS_CREATED_ON','ACC_BANK_TXN.TXN_DATE','ACC_BANK_TXN.MATCHED_ON','ACC_BANK_TXN.IS_MATCHED','ACC_RESELLER_PAYMENTS.F_SS_CREATED_BY','ACC_BANK_TXN.CODE','ACC_RESELLER_PAYMENTS.PK_NO AS PAYMENT_PK_NO','ACC_RESELLER_PAYMENTS.RESELLER_NAME','ACC_RESELLER_PAYMENTS.RESELLER_NO','ACC_RESELLER_PAYMENTS.PAID_BY','ACC_RESELLER_PAYMENTS.SLIP_NUMBER','ACC_RESELLER_PAYMENTS.ATTACHMENT_PATH','ACC_RESELLER_PAYMENTS.F_PAYMENT_ACC_NO','ACC_RESELLER_PAYMENTS.PAYMENT_BANK_NAME','ACC_RESELLER_PAYMENTS.PAYMENT_ACCOUNT_NAME','ACC_RESELLER_PAYMENTS.MR_AMOUNT','ACC_RESELLER_PAYMENTS.PAYMENT_CONFIRMED_STATUS', 'ACC_RESELLER_PAYMENTS.IS_COD','ACC_RESELLER_PAYMENTS.PAYMENT_DATE', DB::raw("'RESELLER' as TYPE") )
        ->leftJoin('ACC_BANK_TXN','ACC_BANK_TXN.F_RESELLER_PAYMENT_NO', '=', 'ACC_RESELLER_PAYMENTS.PK_NO')
       // ->where('ACC_RESELLER_PAYMENTS.PAYMENT_CONFIRMED_STATUS',0)
        //->where('ACC_BANK_TXN.IS_CUS_RESELLER_BANK_RECONCILATION',1)
        ->orderBy('ACC_RESELLER_PAYMENTS.PAYMENT_DATE', 'DESC');

        if($request->type == 'customer')
        {
            if($request->id){
                $query = $query1->where('ACC_CUSTOMER_PAYMENTS.F_CUSTOMER_NO',$request->id);
                $data['customer_info'] = Customer::find($request->id);
            }else{
                $query = $query1->where('ACC_CUSTOMER_PAYMENTS.PAYMENT_CONFIRMED_STATUS',0);
            }

            $data['customer'] = $query->get();
            $data['selected'] = 'Customer (Not Verified)';

        }elseif($request->type == 'reseller'){
            if($request->id){
                $query = $query2->where('ACC_RESELLER_PAYMENTS.F_RESELLER_NO',$request->id);
                $data['customer_info'] = Reseller::find($request->id);
            }else{
                $query = $query2->where('ACC_RESELLER_PAYMENTS.PAYMENT_CONFIRMED_STATUS',0);
            }

            $data['customer'] = $query->get();
            $data['selected'] = 'Reseller (Not Verified)';

        }elseif($request->type == 'verified'){

            $data1 = $query1->where('ACC_CUSTOMER_PAYMENTS.PAYMENT_CONFIRMED_STATUS',1);
            $data2 = $query2->where('ACC_RESELLER_PAYMENTS.PAYMENT_CONFIRMED_STATUS',1);

            $data['customer'] = $data1->UNION($data2)->get();
            $data['selected'] = 'All Payment (Verified)';

        }elseif($request->type == 'cod'){
            $data1 = $query1->where('ACC_CUSTOMER_PAYMENTS.IS_COD',1);
            $data2 = $query2->where('ACC_RESELLER_PAYMENTS.IS_COD',1);

            $data['customer'] = $data1->UNION($data2)->get();
            $data['selected'] = 'all_cod';
        }
        else{

            $data1 = $query1->where('ACC_CUSTOMER_PAYMENTS.PAYMENT_CONFIRMED_STATUS',0);
            $data2 = $query2->where('ACC_RESELLER_PAYMENTS.PAYMENT_CONFIRMED_STATUS',0);

            $data['customer'] = $data1->UNION($data2)->get();
            $data['selected'] = 'All Payment (Not Verified)';

        }
        return $this->formatResponse(true, '', 'admin.payment.list', $data);
    }
/*
    public function paymentVrify($id,$type)
    {

        DB::beginTransaction();
            try {
                if($type == 'customer'){
                    $payment = PaymentCustomer::find($id);
                    if($payment->PAYMENT_CONFIRMED_STATUS == 0){
                        PaymentCustomer::where('PK_NO',$id)->update(['PAYMENT_CONFIRMED_STATUS' => 1]);
                        $order_payment = OrderPayment::where('F_ACC_CUSTOMER_PAYMENT_NO',$id)->where('IS_PAYMENT_FROM_BALANCE',0)->get();
                        if(!empty($order_payment)){
                            foreach($order_payment as $key => $row ) {
                                if( $row->PAYMENT_AMOUNT > 0 ){
                                    Order::where('PK_NO', $row->ORDER_NO)->increment('ORDER_ACTUAL_TOPUP',$row->PAYMENT_AMOUNT);
                                }
                            }
                        }
                    AccBankTxn::where('F_CUSTOMER_PAYMENT_NO',$id)->increment('AMOUNT_ACTUAL', $payment->MR_AMOUNT);
                    PaymentBankAcc::where('PK_NO',$payment->F_PAYMENT_ACC_NO)->increment('BALANCE_ACTUAL', $payment->MR_AMOUNT);
                    Customer::where('PK_NO',$payment->F_CUSTOMER_NO)->increment('CUSTOMER_BALANCE_ACTUAL', $payment->PAYMENT_REMAINING_MR);
                    }
                }
                if($type == 'reseller'){
                    $payment = PaymentReseller::find($id);
                    if($payment->PAYMENT_CONFIRMED_STATUS == 0){
                        PaymentReseller::where('PK_NO',$id)->update(['PAYMENT_CONFIRMED_STATUS' => 1]);
                        $order_payment = OrderPayment::where('F_ACC_RESELLER_PAYMENT_NO',$id)->where('IS_PAYMENT_FROM_BALANCE',0)->get();
                        if(!empty($order_payment)){
                            foreach($order_payment as $key => $row ) {
                                if( $row->PAYMENT_AMOUNT > 0 ){
                                    Order::where('PK_NO', $row->ORDER_NO)->increment('ORDER_ACTUAL_TOPUP',$row->PAYMENT_AMOUNT);
                                }
                            }
                        }

                    AccBankTxn::where('F_RESELLER_PAYMENT_NO',$id)->increment('AMOUNT_ACTUAL', $payment->MR_AMOUNT);
                    PaymentBankAcc::where('PK_NO',$payment->F_PAYMENT_ACC_NO)->increment('BALANCE_ACTUAL', $payment->MR_AMOUNT);
                    Reseller::where('PK_NO',$payment->F_RESELLER_NO)->increment('CUM_BALANCE_ACTUAL', $payment->PAYMENT_REMAINING_MR);

                    }
                }

            } catch (\Exception $e) {
                DB::rollback();
                return $this->formatResponse(false, $e->getMessage(), 'admin.payment.list');
            }
        DB::commit();
        return $this->formatResponse(true, 'Payment verification successfull !', 'admin.payment.list');
    } */


    public function postStore($request)
    {

        DB::beginTransaction();
        try {
            $payment                            = new PaymentCustomer();
            $payment->F_CUSTOMER_NO             = $request->customer_id;
            $payment->F_PAYMENT_CURRENCY_NO     = $request->payment_currency_no ?? 2;
            $payment->PAYMENT_DATE              = date('Y-m-d',strtotime($request->payment_date));
            $payment->MR_AMOUNT                 = $request->payment_amount;
            $payment->F_PAYMENT_ACC_NO          = $request->payment_acc_no;
            $payment->PAYMENT_NOTE              = $request->payment_note;
            $payment->PAID_BY                   = $request->paid_by ?? null;
            $payment->SLIP_NUMBER               = $request->ref_number;
            $payment->PAYMENT_CONFIRMED_STATUS  = 0;

            $total_split = 0;
            if ($request->split_pay){
                foreach($request->split_pay as $key => $pay ){
                    if($pay > 0 ){
                        $total_split += $pay;
                    }
                }
            }

            $payment->PAYMENT_REMAINING_MR      = round($request->payment_amount - $total_split,2) ;
            if($request->payfrom == 'credit'){
                $pay_pk_no = $request->pay_pk_no;
                $PaymentCustomer = PaymentCustomer::find($pay_pk_no);
                $is_payment_from_balance = 1;
                if($total_split > 0 ){
                    PaymentCustomer::where('PK_NO',$pay_pk_no)->decrement('PAYMENT_REMAINING_MR',$total_split);
                }
            }elseif($request->payfrom == 'cod'){
                $payment->IS_COD = 1;
                $payment->PAYMENT_CONFIRMED_STATUS  = 1;
                $payment->save();

                $pay_pk_no              = $payment->PK_NO;
                $order_pay              = new OrderPayment();
                $order_pay->ORDER_NO    = $request->order_id;
                $order_pay->CUSTOMER_NO = $request->customer_id;
                $order_pay->IS_CUSTOMER = 1;
                $order_pay->F_ACC_CUSTOMER_PAYMENT_NO = $pay_pk_no;
                $order_pay->PAYMENT_AMOUNT = $request->payment_amount;
                $order_pay->IS_PAYMENT_FROM_BALANCE = 0;
                $order_pay->save();

                $txn = new AccBankTxn();
                $txn->TXN_TYPE_IN_OUT   = 1;
                $txn->TXN_DATE          = date('Y-m-d',strtotime($request->payment_date));
                $txn->AMOUNT_ACTUAL     = $request->payment_amount;
                $txn->AMOUNT_BUFFER     = $request->payment_amount;
                $txn->IS_CUS_RESELLER_BANK_RECONCILATION = 1;
                $txn->F_ACC_PAYMENT_BANK_NO = $request->payment_acc_no;;
                $txn->F_CUSTOMER_NO     = $request->customer_id;;
                $txn->F_CUSTOMER_PAYMENT_NO = $pay_pk_no;
                $txn->IS_MATCHED        = 1;
                $txn->MATCHED_ON        = date('Y-m-d H:i:s');;
                $txn->IS_COD            = 1;
                $txn->save();

            }else{
                $payment->save();
                $pay_pk_no                  = $payment->PK_NO;
                $is_payment_from_balance    = 0;
                $type                       = 'customer';
                DB::statement('CALL PROC_CUSTOMER_PAYMENT(:pay_pk_no, :type);',array( $pay_pk_no,$type));
            }
            if ($request->split_pay){
                foreach($request->split_pay as $key => $pay ){
                    if($pay > 0 ){
                        $order_pk_no = $request->order_id[$key];
                        $order_pay              = new OrderPayment();
                        $order_pay->ORDER_NO    = $order_pk_no;
                        $order_pay->CUSTOMER_NO = $request->customer_id;
                        $order_pay->IS_CUSTOMER = 1;
                        $order_pay->F_ACC_CUSTOMER_PAYMENT_NO = $pay_pk_no;
                        $order_pay->PAYMENT_AMOUNT = $pay;
                        $order_pay->IS_PAYMENT_FROM_BALANCE = $is_payment_from_balance;
                        $order_pay->save();

                        if($request->payfrom == 'credit'){
                            /* This block for Order payment by customer balance (verified or non verified) */
                            if($PaymentCustomer->PAYMENT_CONFIRMED_STATUS == 1){
                                $order = Order::find($order_pk_no);
                                $booking        = Booking::find($order->F_BOOKING_NO);
                                $order_value    = $booking->TOTAL_PRICE - $booking->DISCOUNT;
                                if($order->ORDER_ACTUAL_TOPUP >= $order_value ){
                                   $inv = BookingDetails::where('F_BOOKING_NO',$booking->PK_NO)->get();
                                   if($inv){
                                       foreach ($inv as $key => $value) {
                                           Stock::where('PK_NO',$value->F_INV_STOCK_NO)->update(['ORDER_STATUS' => 60]);
                                       }
                                   }
                                }
                            }else{
                                Order::where('PK_NO', $order_pk_no)->increment('ORDER_BUFFER_TOPUP',$pay);
                            }
                        }
                    }
                }
            }

            if ($request->file('payment_photo')) {
                $image      = $request->file('payment_photo');
                $file_name  = 'pay_'. date('dmY'). '_' .uniqid(). '.' . $image->getClientOriginalExtension();
                $file_path  = '/media/images/payment/customer/'.$pay_pk_no.'/'.$file_name;
                $image->move(public_path().'/media/images/payment/customer/'.$pay_pk_no.'/', $file_name);
                $payment_photo = PaymentCustomer::find($pay_pk_no);
                $payment_photo->ATTACHMENT_PATH   = $file_path;
                $payment_photo->update();

            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.order.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Payment entry successfully !', 'admin.order.list');
    }

    public function postStoreReseller($request)
    {

        DB::beginTransaction();
        try {
            $payment                            = new PaymentReseller();
            $payment->F_RESELLER_NO             = $request->customer_id;
            $payment->F_PAYMENT_CURRENCY_NO     = $request->payment_currency_no ?? 2;
            $payment->PAYMENT_DATE              = date('Y-m-d',strtotime($request->payment_date));
            $payment->MR_AMOUNT                 = $request->payment_amount;
            $payment->F_PAYMENT_ACC_NO          = $request->payment_acc_no;
            $payment->PAYMENT_NOTE              = $request->payment_note;
            $payment->PAID_BY                   = $request->paid_by;
            $payment->SLIP_NUMBER               = $request->ref_number;
            $payment->PAYMENT_CONFIRMED_STATUS  = 0;
            $total_split = 0;
            if ($request->split_pay){
                foreach($request->split_pay as $key => $pay ){
                    if($pay > 0 ){
                        $total_split += $pay;
                    }
                }
            }
            $payment->PAYMENT_REMAINING_MR  = $request->payment_amount - $total_split ;
            if($request->payfrom == 'credit'){
                $pay_pk_no = $request->pay_pk_no;
                $is_payment_from_balance = 1;
                $PaymentCustomer = PaymentReseller::find($pay_pk_no);
                if($total_split > 0 ){
                    PaymentReseller::where('PK_NO',$pay_pk_no)->decrement('PAYMENT_REMAINING_MR',$total_split);
                }
            }elseif($request->payfrom == 'cod'){
                $payment->IS_COD = 1;
                $payment->PAYMENT_CONFIRMED_STATUS  = 1;
                $payment->save();

                $pay_pk_no = $payment->PK_NO;
                $order_pay              = new OrderPayment();
                $order_pay->ORDER_NO    = $request->order_id;
                $order_pay->RESELLER_NO = $request->customer_id;
                $order_pay->IS_CUSTOMER = 0;
                $order_pay->F_ACC_RESELLER_PAYMENT_NO = $pay_pk_no;
                $order_pay->PAYMENT_AMOUNT = $request->payment_amount;
                $order_pay->IS_PAYMENT_FROM_BALANCE = 0;
                $order_pay->save();

                $txn = new AccBankTxn();
                $txn->TXN_TYPE_IN_OUT = 1;
                $txn->TXN_DATE = date('Y-m-d',strtotime($request->payment_date));
                $txn->AMOUNT_ACTUAL = $request->payment_amount;
                $txn->AMOUNT_BUFFER = $request->payment_amount;
                $txn->IS_CUS_RESELLER_BANK_RECONCILATION = 2;
                $txn->F_ACC_PAYMENT_BANK_NO = $request->payment_acc_no;;
                $txn->F_RESELLER_NO = $request->customer_id;;
                $txn->F_RESELLER_PAYMENT_NO = $pay_pk_no;
                $txn->IS_MATCHED = 1;
                $txn->MATCHED_ON = date('Y-m-d H:i:s');;
                $txn->IS_COD = 1;
                $txn->save();

            }else{
                $payment->save();
                $pay_pk_no = $payment->PK_NO;
                $is_payment_from_balance = 0;
                $type = 'reseller';
                DB::statement('CALL PROC_CUSTOMER_PAYMENT(:pay_pk_no, :type);',array( $pay_pk_no,$type));
            }

            if ($request->split_pay){
                foreach($request->split_pay as $key => $pay ){
                    $order_pk_no = $request->order_id[$key];
                    if($pay > 0 ){
                        $order_pay              = new OrderPayment();
                        $order_pay->ORDER_NO    = $order_pk_no;
                        $order_pay->RESELLER_NO = $request->customer_id;
                        $order_pay->IS_CUSTOMER = 0;
                        $order_pay->F_ACC_RESELLER_PAYMENT_NO = $pay_pk_no;
                        $order_pay->PAYMENT_AMOUNT = $pay;
                        $order_pay->IS_PAYMENT_FROM_BALANCE = $is_payment_from_balance;
                        $order_pay->save();
                        if($request->payfrom == 'credit'){
                            /* This block for Order payment by customer balance (verified or non verified) */
                            if($PaymentCustomer->PAYMENT_CONFIRMED_STATUS == 1){
                                $order = Order::find($order_pk_no);
                                $booking        = Booking::find($order->F_BOOKING_NO);
                                $order_value    = $booking->TOTAL_PRICE - $booking->DISCOUNT;
                                if($order->ORDER_ACTUAL_TOPUP >= $order_value ){
                                   $inv = BookingDetails::where('F_BOOKING_NO',$booking->PK_NO)->get();
                                   if($inv){
                                       foreach ($inv as $key => $value) {
                                           Stock::where('PK_NO',$value->F_INV_STOCK_NO)->update(['ORDER_STATUS' => 60]);
                                       }
                                   }
                                }
                            }else{
                                Order::where('PK_NO', $order_pk_no)->increment('ORDER_BUFFER_TOPUP',$pay);
                            }
                        }
                    }
                }
            }
            if ($request->file('payment_photo')) {
                $image = $request->file('payment_photo');
                $file_name = 'pay_'. date('dmY'). '_' .uniqid(). '.' . $image->getClientOriginalExtension();
                $file_path = '/media/images/payment/reseller/'.$pay_pk_no.'/'.$file_name;
                $image->move(public_path().'/media/images/payment/reseller/'.$pay_pk_no.'/', $file_name);
                $payment_photo = PaymentReseller::find($pay_pk_no);
                $payment_photo->ATTACHMENT_PATH   = $file_path;
                $payment_photo->update();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.reseller.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Payment entry successfully !', 'admin.reseller.list');
    }

    public function postRefund($request)
    {
       DB::beginTransaction();
        try {
            $bank = BankList::find($request->bank_no);

            if($request->refund_request_no){
                $ref_req = RefundRequest::find($request->refund_request_no);
                $ref_req->STATUS = 1;
                if($bank){
                    $ref_req->F_ACC_BANK_LIST_NO_REFUNDED   = $request->bank_no;
                    $ref_req->REFUNDED_BANK_NAME            = $bank->BANK_NAME;
                }
                $ref_req->REFUNDED_BANK_ACC_NAME    = $request->cust_acc_name;
                $ref_req->REFUNDED_BANK_ACC_NO      = $request->cust_acc_no;
                $ref_req->update();

                $refund_no = $request->refund_request_no;
            }else{
                $refund =  new RefundRequest();
                if($request->type == 'customer'){
                    $refund->F_CUSTOMER_NO  = $request->customer_no;
                    $refund->IS_CUSTOMER    = 1;
                }elseif($request->type == 'reseller'){
                    $refund->F_RESELLER_NO  = $request->reseller_no;
                    $refund->IS_CUSTOMER    = 0;
                }
                if($bank){
                    $refund->F_ACC_BANK_LIST_NO             = $request->bank_no;
                    $refund->REQ_BANK_NAME                  = $bank->BANK_NAME;
                    $refund->F_ACC_BANK_LIST_NO_REFUNDED    = $request->bank_no;
                    $refund->REFUNDED_BANK_NAME             = $bank->BANK_NAME;
                }
                $refund->REQ_BANK_ACC_NAME          = $request->cust_acc_name;
                $refund->REQ_BANK_ACC_NO            = $request->cust_acc_no;
                $refund->REFUNDED_BANK_ACC_NAME     = $request->cust_acc_name;
                $refund->REFUNDED_BANK_ACC_NO       = $request->cust_acc_no;
                $refund->MR_AMOUNT                  = $request->payment_amount;
                $refund->REQUEST_NOTE               = $request->payment_note;
                $refund->REQUEST_BY                 = Auth::user()->PK_NO;
                $refund->REQUEST_BY_NAME            = Auth::user()->USERNAME;
                $refund->REQUEST_DATE               = date('Y-m-d');
                $refund->STATUS                     = 1;
                $refund->save();
                $refund_no = $refund->PK_NO;
            }
            $payment                            = new PaymentCustomer();
            $payment->F_CUSTOMER_NO             = $request->customer_id;
            $payment->F_PAYMENT_CURRENCY_NO     = $request->payment_currency_no ?? 2;
            $payment->PAYMENT_DATE              = date('Y-m-d',strtotime($request->payment_date));
            $payment->MR_AMOUNT                 = -$request->payment_amount;
            $payment->PAYMENT_REMAINING_MR      = 0;
            $payment->F_PAYMENT_ACC_NO          = $request->payment_acc_no;
            $payment->PAYMENT_NOTE              = $request->payment_note;
            $payment->PAID_BY                   = $request->paid_by ?? null;
            $payment->SLIP_NUMBER               = $request->ref_number;
            $payment->PAYMENT_TYPE              = 2;
            $payment->PAYMENT_CONFIRMED_STATUS  = 0;
            $payment->F_ACC_CUST_RES_REFUND_REQUEST_NO  = $refund_no;
            $payment->save();

            $pay_pk_no                  = $payment->PK_NO;
            $type                       = 'customer';

            //reduce old payment remaining balance by refund amount
            $all_remainings = PaymentCustomer::where('F_CUSTOMER_NO',$request->customer_id)->where('PAYMENT_REMAINING_MR','>',0)->where('PAYMENT_TYPE',1)->get();

            if($all_remainings){
                $refund_mr = $request->payment_amount;
                foreach($all_remainings as $key => $pay_remaining){
                    if($refund_mr > 0 ){
                        if($pay_remaining->PAYMENT_REMAINING_MR >= $refund_mr){

                            $new_remain_mr = $pay_remaining->PAYMENT_REMAINING_MR - $refund_mr;
                            $refund_maping = $pay_remaining->REFUND_MAPING.$pay_pk_no.','.$refund_mr.'|';
                            $new_refund_mr = $pay_remaining->REFUND_MR + $refund_mr;
                            // dd($refund_maping);
                            PaymentCustomer::where('PK_NO',$pay_remaining->PK_NO)->update(['PAYMENT_REMAINING_MR' => $new_remain_mr, 'REFUND_MR' => $new_refund_mr, 'REFUND_MAPING' => $refund_maping]);
                            $refund_mr = 0;
                        }else{
                            //$new_remain_mr = $pay_remaining->PAYMENT_REMAINING_MR - $request->payment_amount;
                            $refund_mr -= $pay_remaining->PAYMENT_REMAINING_MR;
                            $refund_maping = $pay_remaining->REFUND_MAPING.$pay_pk_no.','.$pay_remaining->PAYMENT_REMAINING_MR.'|';
                            $new_refund_mr = $pay_remaining->REFUND_MR + $pay_remaining->PAYMENT_REMAINING_MR;

                            PaymentCustomer::where('PK_NO',$pay_remaining->PK_NO)->update(['PAYMENT_REMAINING_MR' => 0,'REFUND_MR' => $new_refund_mr, 'REFUND_MAPING' => $refund_maping]);
                        }
                    }
                }
            }



            DB::statement('CALL PROC_CUSTOMER_PAYMENT(:pay_pk_no, :type);',array( $pay_pk_no,$type));

            if ($request->file('payment_photo')) {
                $image      = $request->file('payment_photo');
                $file_name  = 'pay_'. date('dmY'). '_' .uniqid(). '.' . $image->getClientOriginalExtension();
                $file_path  = '/media/images/payment/customer/'.$pay_pk_no.'/'.$file_name;
                $image->move(public_path().'/media/images/payment/customer/'.$pay_pk_no.'/', $file_name);
                $payment_photo = PaymentCustomer::find($pay_pk_no);
                $payment_photo->ATTACHMENT_PATH   = $file_path;
                $payment_photo->update();

            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.customer.refund');
        }
        DB::commit();
        return $this->formatResponse(true, 'Refund entry successfully !', 'admin.customer.refund');
    }


    public function getDetails($txnId)
    {
        $data = array();
        DB::beginTransaction();
        try{
            $txn = AccBankTxn::find($txnId);
            if($txn->F_CUSTOMER_PAYMENT_NO){
                $data['order_payments'] = OrderPayment::where('F_ACC_CUSTOMER_PAYMENT_NO',$txn->F_CUSTOMER_PAYMENT_NO)->get();
            }
            if($txn->F_RESELLER_PAYMENT_NO){
                $data['order_payments'] = OrderPayment::where('F_ACC_RESELLER_PAYMENT_NO',$txn->F_RESELLER_PAYMENT_NO)->get();
            }
            $data['txn'] = $txn;

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.payment.details',$data);
        }
        DB::commit();
        return $this->formatResponse(true, 'Payment entry successfully !', 'admin.payment.details',$data);
    }

    public function getOrderPaymentDelete($id)
    {
        /*NOT POSSIBLE IF PAYMENT IS USED ANY AMOUNT IN THE ORDER*/
        $orders =  OrderPayment::select('SLS_ORDER.ORDER_BALANCE_USED')
        ->where('ACC_ORDER_PAYMENT.PK_NO',$id)
        ->leftJoin('SLS_ORDER', 'SLS_ORDER.PK_NO', 'ACC_ORDER_PAYMENT.ORDER_NO')
        ->first();

        if($orders->ORDER_BALANCE_USED > 0){
            return $this->formatResponse(false, 'Payment already used in order item, please free the payment at first', 'admin.payment.details');
        }else{
            DB::beginTransaction();
            $data = array();
            try{
                OrderPayment::find($id)->delete();
            } catch (\Exception $e) {
                DB::rollback();
                return $this->formatResponse(false, $e->getMessage(), 'admin.payment.details',$data);
            }
            DB::commit();
            return $this->formatResponse(true, 'Payment entry successfully !', 'admin.payment.details',$data);
        }
    }

    public function getDelete($id)
    {
        /* NOT POSSIBLE IF PAYMENT IS ASSIGNED TO THE ORDER */
        $orders =  AccBankTxn::find($id);
        if($orders->IS_CUS_RESELLER_BANK_RECONCILATION == 1){
            $order_payment = PaymentCustomer::where('PK_NO',$orders->F_CUSTOMER_PAYMENT_NO)->first();
            if($order_payment->allOrderPayments->count() > 0 ){
                return $this->formatResponse(false, 'Payment already used in order, please free the payment at first', 'admin.payment.list');
           }else{
            $type = 'customer';
            $pay_pk_no = $order_payment->PK_NO;
            }
        }elseif($orders->IS_CUS_RESELLER_BANK_RECONCILATION == 2){
            $order_payment = PaymentReseller::where('PK_NO', $orders->F_RESELLER_PAYMENT_NO)->first();
            if($order_payment->allOrderPayments->count() > 0 ){
                return $this->formatResponse(false, 'Payment already used in order, please free the payment at first', 'admin.payment.list');
            }else{
                $type = 'reseller';
                $pay_pk_no = $order_payment->PK_NO;
            }
        }
        DB::beginTransaction();
            try{
                    DB::statement('CALL PROC_CUSTOMER_PAYMENT_DELETE(:pay_pk_no, :type);',array( $pay_pk_no,$type));
                } catch (\Exception $e) {
                    DB::rollback();
                    return $this->formatResponse(false, 'Payment not delete successfully !', 'admin.payment.list');
                }
        DB::commit();
        return $this->formatResponse(true, 'Payment delete successfully !', 'admin.payment.list');
    }

    public function postUpdatePartial($request)
    {
        DB::beginTransaction();
        try{
            $txn = AccBankTxn::find($request->txn_pk_no);
            if($txn->IS_CUS_RESELLER_BANK_RECONCILATION == 1){
                $payment_date  = date('Y-m-d',strtotime($request->payment_date));
                PaymentCustomer::where('PK_NO',$txn->F_CUSTOMER_PAYMENT_NO)->update(['PAYMENT_DATE' => $payment_date ]);
            }

            if($txn->IS_CUS_RESELLER_BANK_RECONCILATION == 2){
                PaymentReseller::where('PK_NO',$txn->F_RESELLER_PAYMENT_NO)->update(['PAYMENT_DATE' => $payment_date ]);
            }
            AccBankTxn::where('PK_NO',$request->txn_pk_no)->update(['TXN_DATE' => $payment_date]);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.payment.details');
        }
        DB::commit();
        return $this->formatResponse(true, 'Payment entry successfully !', 'admin.payment.details');
    }

    public function getPaymentProcessing($request)
    {
        if ($request->get('from_date') === null || ($request->get('to_date')) === null) {
            // $from_date = new Carbon('last day of last month');
            // $from_date = $from_date->toDateString();
            $from_date  = Carbon::now()->firstOfMonth()->toDateString();
            $to_date    = Carbon::now()->endOfMonth()->toDateString();
        }else{
            $from_date   = date('Y-m-d', strtotime($request->get('from_date')));
            $to_date     = date('Y-m-d', strtotime($request->get('to_date')));
        }

        $count_total = Invoice::selectRaw('(SELECT IFNULL(SUM(INVOICE_EXACT_VALUE),0) from PRC_STOCK_IN where F_PAYMENT_SOURCE_NO = source_no
         and INVOICE_DATE BETWEEN '. '"' .$from_date. '"' .' and '. '"' .$to_date. '"' .'
         )')->limit(1)->getQuery();

        $data['data'] = Invoice::select('PAYMENT_SOURCE_NAME','F_PAYMENT_SOURCE_NO as source_no','F_PAYMENT_ACC_NO','F_PAYMENT_METHOD_NO','INVOICE_DATE','INVOICE_CURRENCY')
        ->selectSub($count_total, 'total')
        ->whereBetween('INVOICE_DATE',[$from_date,$to_date])
        ->groupBy('F_PAYMENT_SOURCE_NO')
        ->orderBy('INVOICE_DATE','DESC')
        ->get();
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($data);
        // echo '<br>======================<br>';
        // exit();

        return $this->formatResponse(true, 'Data found !', ' ',$data);
    }

    public function getBankToBank($id)
    {
        $data['payment_bank']           = PaymentBankAcc::getFilterPaymentBanks();
        $data['all_payment_bank']       = PaymentBankAcc::getAllPaymentBanks();
        $data['party_payment_method']   = PartyPaymentMethod::pluck('METHOD_NAME','PK_NO');
        if ($id != null) {
            $data['edit_data']          = PaymentIxfer::find($id);
            $data['from_balance']       = PaymentBankAcc::find($data['edit_data']->F_FROM_ACC_PAYMENT_BANK_ACC_NO);
            $data['to_balance']         = PaymentBankAcc::find($data['edit_data']->F_TO_ACC_PAYMENT_BANK_ACC_NO);
        }
        $data['user'] = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                            ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                            ->select('F_ROLE_NO')->where('F_USER_NO',Auth::user()->PK_NO)->first();
        return $this->formatResponse(true, 'Payment entry successfully !', '', $data);
    }

    public function getBankToOther($id)
    {
        $data['payment_bank']           = PaymentBankAcc::where('IS_COD',1);
                                            if (Auth::user()->F_AGENT_NO > 0) {
                                                $data['payment_bank'] = $data['payment_bank']->where('F_USER_NO',Auth::user()->PK_NO);
                                            }
                                            $data['payment_bank'] = $data['payment_bank']->pluck('BANK_ACC_NAME','PK_NO');
        $data['party_payment_type']     = PartyPaymentMethodHead::pluck('NARRATION','PK_NO');
        $data['party_payment_method']   = PartyPaymentMethod::pluck('METHOD_NAME','PK_NO');
        if ($id != null) {
            $data['edit_data']          = PaymentExfer::find($id);
            $data['from_balance']       = PaymentBankAcc::find($data['edit_data']->F_I_ACC_PAYMENT_BANK_ACC_NO);
        }
        $data['user'] = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                            ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                            ->select('F_ROLE_NO')->where('F_USER_NO',Auth::user()->PK_NO)->first();
        return $this->formatResponse(true, 'Payment entry successfully !', '', $data);
    }

    public function postNewPaymentType($request)
    {
        DB::beginTransaction();
        try{
            $check              = PartyPaymentMethodHead::where('NARRATION',$request->name)->first();
            if (!empty($check)) {
                return $this->formatResponse(false, 'Already exists !', 'admin.account_to_other.view');
            }
            $data               = new PartyPaymentMethodHead();
            $data->NARRATION    = $request->name;
            $data->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.payment.details');
        }
        DB::commit();
        return $this->formatResponse(true, 'Entry successfull !', 'admin.account_to_other.view');
    }

    public function postbankToOther($request)
    {
        DB::beginTransaction();
        try{
            // if ($request->is_cash_in == 0 && $request->submit == 'accept') {
            //     $available_amount = PaymentBankAcc::select('BALANCE_ACTUAL')->where('PK_NO',$request->payment_acc_no)->first();
            //     if ($available_amount->BALANCE_ACTUAL < $request->payment_amount) {
            //         return $this->formatResponse(false, 'Insufficiant Amount !', 'admin.account_to_other.view');
            //     }
            // }
            $party_pay_method = DB::table('ACC_PARTY_PAYMENT_METHOD')->select('METHOD_NAME')->where('PK_NO',$request->payment_method)->first();
            $role_id = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                        ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                        ->select('F_ROLE_NO')->where('F_USER_NO',Auth::user()->PK_NO)->first();
            if ($request->submit == 'decline' && $role_id->F_ROLE_NO == 1) {
                $external_xfer                                  = PaymentExfer::find($request->ixfer_id);
                $external_xfer->IS_VERIFIED                     = 2;
                $external_xfer->NARRATION                       = $request->payment_note;

                $txn                                            = AccBankTxn::find($external_xfer->F_ACC_BANK_TXN);
                $acc_payment_acc                                = PaymentBank::select('BALACNE_BUFFER')->where('PK_NO',$external_xfer->F_I_ACC_PAYMENT_BANK_ACC_NO)->first();

                $updated_buffer                                 = $acc_payment_acc->BALACNE_BUFFER - $txn->AMOUNT_BUFFER;
                PaymentBank::where('PK_NO',$external_xfer->F_I_ACC_PAYMENT_BANK_ACC_NO)->update(['BALACNE_BUFFER' => $updated_buffer]);
                AccBankTxn::where('PK_NO',$external_xfer->F_ACC_BANK_TXN)->delete();
                $msg                                            = 'Request is declined !';
            }else{
                if ($request->submit == 'update') {
                    $amount                                     = $request->is_cash_in == 0 ? '-'.$request->payment_amount : $request->payment_amount;

                    $external_xfer                              = PaymentExfer::find($request->ixfer_id);
                    $txn                                        = AccBankTxn::find($external_xfer->F_ACC_BANK_TXN);

                    $acc_payment_acc                            = PaymentBank::find($external_xfer->F_I_ACC_PAYMENT_BANK_ACC_NO);
                    $acc_payment_acc->BALACNE_BUFFER            = ($acc_payment_acc->BALACNE_BUFFER - $txn->AMOUNT_BUFFER) + $amount;
                    $acc_payment_acc->save();

                    $txn->AMOUNT_BUFFER                         = $amount;
                    // $txn->F_ACC_PAYMENT_BANK_NO                 = $request->payment_acc_no;
                    $txn->save();

                    $msg                                        = 'Request is updated !';
                }elseif($request->submit == 'accept' && $role_id->F_ROLE_NO == 1){
                    $amount                                     = $request->is_cash_in == 0 ? '-'.$request->payment_amount : $request->payment_amount;

                    $external_xfer                              = PaymentExfer::find($request->ixfer_id);
                    $external_xfer->ACK_MR_AMOUNT               = $request->payment_amount;
                    $external_xfer->IS_VERIFIED                 = 1;
                    $external_xfer->F_VERIFIED_BY_SA_USER_NO    = Auth::user()->PK_NO;

                    $txn                                        = AccBankTxn::find($external_xfer->F_ACC_BANK_TXN);
                    $acc_payment_acc                            = PaymentBank::find($external_xfer->F_I_ACC_PAYMENT_BANK_ACC_NO);

                    if ($request->is_cash_in == 0 && $acc_payment_acc->BALANCE_ACTUAL < $request->payment_amount) {
                        return $this->formatResponse(false, 'Insufficiant Amount !', 'admin.account_to_other.view');
                    }
                    $acc_payment_acc->BALANCE_ACTUAL            = $acc_payment_acc->BALANCE_ACTUAL + $amount;
                    $acc_payment_acc->BALACNE_BUFFER            = ($acc_payment_acc->BALACNE_BUFFER - $txn->AMOUNT_BUFFER) + $amount;
                    $acc_payment_acc->save();

                    $txn->AMOUNT_ACTUAL                         = $amount;
                    $txn->AMOUNT_BUFFER                         = $amount;
                    // $txn->F_ACC_PAYMENT_BANK_NO                 = $request->payment_acc_no;
                    $txn->IS_MATCHED                            = 1;
                    $txn->MATCHED_ON                            = date('Y-m-d H:i:s');
                    $txn->save();

                    $msg                                        = 'Request accepted successfully !';
                }elseif($request->submit == 'save'){
                    $amount                                     = $request->is_cash_in == 0 ? '-'.$request->payment_amount : $request->payment_amount;

                    $txn                                        = new AccBankTxn();
                    $txn->TXN_TYPE_IN_OUT                       = 1;
                    $txn->TXN_DATE                              = date('Y-m-d');
                    $txn->AMOUNT_ACTUAL                         = 0;
                    $txn->AMOUNT_BUFFER                         = $amount;
                    $txn->IS_CUS_RESELLER_BANK_RECONCILATION    = 4;
                    $txn->F_ACC_PAYMENT_BANK_NO                 = $request->payment_acc_no;
                    $txn->IS_MATCHED                            = 0;
                    $txn->IS_COD                                = 0;
                    $txn->save();
                    $new_txn_Pk                                 = $txn->PK_NO;

                    $external_xfer                              = new PaymentExfer();
                    $external_xfer->F_ACC_BANK_TXN              = $new_txn_Pk;
                    $external_xfer->F_I_ACC_PAYMENT_BANK_ACC_NO = $request->payment_acc_no;

                    $acc_payment_acc                            = PaymentBank::find($request->payment_acc_no);
                    $acc_payment_acc->BALACNE_BUFFER            = $acc_payment_acc->BALACNE_BUFFER + $amount;
                    $acc_payment_acc->save();
                    $msg                                        = 'Request successfull !';
                }
                $external_xfer->ENTERED_MR_AMOUNT               = $request->payment_amount;
                $external_xfer->IS_IN                           = $request->is_cash_in;
                $external_xfer->F_ACC_PARTY_PAYMENT_METHOD_NO   = $request->payment_method;
                $external_xfer->ACC_PARTY_PAYMENT_METHOD        = $party_pay_method->METHOD_NAME;
                $external_xfer->NARRATION                       = $request->payment_note;
                $external_xfer->F_ACC_PAYMENT_ACC_HEAD_NO       = $request->payment_type;
                if ($request->file != '') {
                    $file_name = 'payment_exfer_'. date('dmY'). '_' .time(). '.' . $request->file->getClientOriginalExtension();
                    $request->file->move(public_path('media/images/payment_xfer/'), $file_name);
                    $external_xfer->ATTACHMENT_PATH = 'media/images/payment_xfer/'.$file_name;
                }
            }

            $external_xfer->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.account_to_other_list.view');
        }
        DB::commit();
        return $this->formatResponse(true, $msg, 'admin.account_to_other_list.view');
    }

    public function postbankToBank($request)
    {
        DB::beginTransaction();
        try{
            $party_pay_method = DB::table('ACC_PARTY_PAYMENT_METHOD')->select('METHOD_NAME')->where('PK_NO',$request->payment_method)->first();

            $role_id = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                        ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                        ->select('F_ROLE_NO')->where('F_USER_NO',Auth::user()->PK_NO)->first();

            if ($request->submit == 'decline' && $role_id->F_ROLE_NO == 1) {
                $inter_xfer                                     = PaymentIxfer::find($request->ixfer_id);
                $inter_xfer->IS_VERIFIED                        = 2;
                $inter_xfer->NARRATION                          = $request->payment_note;

                $from_txn                                       = AccBankTxn::find($inter_xfer->F_FROM_ACC_BANK_TXN);
                $to_txn                                         = AccBankTxn::find($inter_xfer->F_TO_ACC_BANK_TXN);
                // $to_txn                                         = AccBankTxn::where('PK_NO', '>', $from_txn->PK_NO)->orderby('PK_NO','ASC')->first();

                $from_acc_payment_acc                           = PaymentBank::select('BALACNE_BUFFER')->where('PK_NO',$inter_xfer->F_FROM_ACC_PAYMENT_BANK_ACC_NO)->first();
                $to_acc_payment_acc                             = PaymentBank::select('BALACNE_BUFFER')->where('PK_NO',$inter_xfer->F_TO_ACC_PAYMENT_BANK_ACC_NO)->first();

                $updated_buffer_from                            = $from_acc_payment_acc->BALACNE_BUFFER - $from_txn->AMOUNT_BUFFER;
                PaymentBank::where('PK_NO',$inter_xfer->F_FROM_ACC_PAYMENT_BANK_ACC_NO)->update(['BALACNE_BUFFER' => $updated_buffer_from]);
                AccBankTxn::where('PK_NO',$inter_xfer->F_FROM_ACC_BANK_TXN)->delete();

                $updated_buffer_to                              = $to_acc_payment_acc->BALACNE_BUFFER - $to_txn->AMOUNT_BUFFER;
                PaymentBank::where('PK_NO',$inter_xfer->F_TO_ACC_PAYMENT_BANK_ACC_NO)->update(['BALACNE_BUFFER' => $updated_buffer_to]);
                AccBankTxn::where('PK_NO',$inter_xfer->F_TO_ACC_BANK_TXN)->delete();

                $msg                                            = 'Request is declined !';
            }else{
                if ($request->submit == 'update') {

                    $inter_xfer                                 = PaymentIxfer::find($request->ixfer_id);
                    $from_txn                                   = AccBankTxn::find($inter_xfer->F_FROM_ACC_BANK_TXN);
                    $to_txn                                     = AccBankTxn::find($inter_xfer->F_TO_ACC_BANK_TXN);

                    $from_acc_payment_acc                       = PaymentBank::find($inter_xfer->F_FROM_ACC_PAYMENT_BANK_ACC_NO);
                    $from_acc_payment_acc->BALACNE_BUFFER       = ($from_acc_payment_acc->BALACNE_BUFFER - $from_txn->AMOUNT_BUFFER) - $request->payment_amount;
                    $from_acc_payment_acc->save();

                    $from_txn->AMOUNT_BUFFER                    = '-'.$request->payment_amount;
                    // $from_txn->F_ACC_PAYMENT_BANK_NO            = $request->from_payment_acc_no;
                    $from_txn->save();

                    $to_acc_payment_acc                         = PaymentBank::find($inter_xfer->F_TO_ACC_PAYMENT_BANK_ACC_NO);
                    $to_acc_payment_acc->BALACNE_BUFFER         = ($to_acc_payment_acc->BALACNE_BUFFER - $to_txn->AMOUNT_BUFFER) + $request->payment_amount;
                    $to_acc_payment_acc->save();

                    $to_txn->AMOUNT_BUFFER                      = $request->payment_amount;
                    // $to_txn->F_ACC_PAYMENT_BANK_NO              = $request->to_payment_acc_no;
                    $to_txn->save();

                    $msg                                        = 'Request is updated !';
                }elseif($request->submit == 'accept' && $role_id->F_ROLE_NO == 1){

                    $inter_xfer                                 = PaymentIxfer::find($request->ixfer_id);

                    $inter_xfer->ACK_MR_AMOUNT                  = $request->payment_amount;
                    $inter_xfer->IS_VERIFIED                    = 1;
                    $inter_xfer->F_VERIFIED_BY_SA_USER_NO       = Auth::user()->PK_NO;

                    $from_txn                                   = AccBankTxn::find($inter_xfer->F_FROM_ACC_BANK_TXN);
                    $to_txn                                     = AccBankTxn::find($inter_xfer->F_TO_ACC_BANK_TXN);

                    $from_acc_payment_acc                       = PaymentBank::find($inter_xfer->F_FROM_ACC_PAYMENT_BANK_ACC_NO);
                    if ($from_acc_payment_acc->BALANCE_ACTUAL < $request->payment_amount) {
                        return $this->formatResponse(false, 'Insufficiant Amount !', 'admin.account_to_bank_list.view');
                    }
                    $from_acc_payment_acc->BALANCE_ACTUAL       = $from_acc_payment_acc->BALANCE_ACTUAL - $request->payment_amount;
                    $from_acc_payment_acc->BALACNE_BUFFER       = ($from_acc_payment_acc->BALACNE_BUFFER - $from_txn->AMOUNT_BUFFER) - $request->payment_amount;
                    $from_acc_payment_acc->save();

                    $from_txn->AMOUNT_ACTUAL                    = '-'.$request->payment_amount;
                    $from_txn->AMOUNT_BUFFER                    = '-'.$request->payment_amount;
                    // $from_txn->F_ACC_PAYMENT_BANK_NO            = $request->from_payment_acc_no;
                    $from_txn->IS_MATCHED                       = 1;
                    $from_txn->MATCHED_ON                       = date('Y-m-d H:i:s');
                    $from_txn->save();

                    $to_acc_payment_acc                         = PaymentBank::find($inter_xfer->F_TO_ACC_PAYMENT_BANK_ACC_NO);
                    $to_acc_payment_acc->BALANCE_ACTUAL         = $to_acc_payment_acc->BALANCE_ACTUAL + $request->payment_amount;
                    $to_acc_payment_acc->BALACNE_BUFFER         = ($to_acc_payment_acc->BALACNE_BUFFER - $to_txn->AMOUNT_BUFFER) + $request->payment_amount;
                    $to_acc_payment_acc->save();

                    $to_txn->AMOUNT_ACTUAL                      = $request->payment_amount;
                    $to_txn->AMOUNT_BUFFER                      = $request->payment_amount;
                    // $to_txn->F_ACC_PAYMENT_BANK_NO              = $request->to_payment_acc_no;
                    $to_txn->IS_MATCHED                         = 1;
                    $to_txn->MATCHED_ON                         = date('Y-m-d H:i:s');
                    $to_txn->save();

                    $msg                                        = 'Request accepted successfully !';
                }elseif($request->submit == 'save'){
                    if ($request->from_payment_acc_no == $request->to_payment_acc_no) {
                        return $this->formatResponse(false, 'Both accounts are same !', 'admin.account_to_bank.view');
                    }
                    $txn                                        = new AccBankTxn();
                    $txn->TXN_TYPE_IN_OUT                       = 1;
                    $txn->TXN_DATE                              = date('Y-m-d');
                    $txn->AMOUNT_ACTUAL                         = 0;
                    $txn->AMOUNT_BUFFER                         = '-'.$request->payment_amount;
                    $txn->IS_CUS_RESELLER_BANK_RECONCILATION    = 4;
                    $txn->F_ACC_PAYMENT_BANK_NO                 = $request->from_payment_acc_no;
                    $txn->IS_MATCHED                            = 0;
                    $txn->IS_COD                                = 0;
                    $txn->save();
                    $new_from_txn_Pk                            = $txn->PK_NO;

                    $txn                                        = new AccBankTxn();
                    $txn->TXN_TYPE_IN_OUT                       = 1;
                    $txn->TXN_DATE                              = date('Y-m-d');
                    $txn->AMOUNT_ACTUAL                         = 0;
                    $txn->AMOUNT_BUFFER                         = $request->payment_amount;
                    $txn->IS_CUS_RESELLER_BANK_RECONCILATION    = 4;
                    $txn->F_ACC_PAYMENT_BANK_NO                 = $request->to_payment_acc_no;
                    $txn->IS_MATCHED                            = 0;
                    $txn->IS_COD                                = 0;
                    $txn->save();
                    $new_to_txn_Pk                              = $txn->PK_NO;

                    $inter_xfer                                 = new PaymentIxfer();
                    $inter_xfer->F_FROM_ACC_BANK_TXN            = $new_from_txn_Pk;
                    $inter_xfer->F_TO_ACC_BANK_TXN              = $new_to_txn_Pk;
                    $inter_xfer->F_FROM_ACC_PAYMENT_BANK_ACC_NO = $request->from_payment_acc_no;
                    $inter_xfer->F_TO_ACC_PAYMENT_BANK_ACC_NO   = $request->to_payment_acc_no;

                    $from_acc_payment_acc                       = PaymentBank::find($request->from_payment_acc_no);
                    $from_acc_payment_acc->BALACNE_BUFFER       = $from_acc_payment_acc->BALACNE_BUFFER - $request->payment_amount;
                    $from_acc_payment_acc->save();

                    $to_acc_payment_acc                         = PaymentBank::find($request->to_payment_acc_no);
                    $to_acc_payment_acc->BALACNE_BUFFER         = $to_acc_payment_acc->BALACNE_BUFFER + $request->payment_amount;
                    $to_acc_payment_acc->save();

                    $msg                                        = 'Request successfull !';
                }

                $inter_xfer->ENTERED_MR_AMOUNT                  = $request->payment_amount;
                $inter_xfer->F_ACC_CUSTOMER_PAYMENT_METHOD_NO   = $request->payment_method;
                $inter_xfer->ACC_CUSTOMER_PAYMENT_METHOD        = $party_pay_method->METHOD_NAME;
                $inter_xfer->NARRATION                          = $request->payment_note;
                if ($request->file != '') {
                    $file_name = 'payment_ixfer_'. date('dmY'). '_' .time(). '.' . $request->file->getClientOriginalExtension();
                    $request->file->move(public_path('media/images/payment_xfer/'), $file_name);
                    $inter_xfer->ATTACHMENT_PATH = 'media/images/payment_xfer/'.$file_name;
                }
            }
            $inter_xfer->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.account_to_bank_list.view');
        }
        DB::commit();
        return $this->formatResponse(true, $msg, 'admin.account_to_bank.view');
    }

    public function getBankToBankDetails($id)
    {
        $data['data']       = PaymentIxfer::find($id);
        if (empty($data['data'])) {
           return $this->formatResponse(false, 'Information not found !', 'admin.account_to_bank_list.view');
        }
        return $this->formatResponse(true, '', 'admin.account_to_bank.details',$data);
    }

    public function getBankToOtherDetails($id)
    {
        $data['data']       = PaymentExfer::find($id);
        if (empty($data['data'])) {
              return $this->formatResponse(false, 'Information not found !', 'admin.account_to_bank_list.view');
        }
        return $this->formatResponse(true, '', 'admin.account_to_bank.details',$data);
    }

    public function postAccountBalanceInfo($request)
    {
        $user = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
        ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
        ->select('F_ROLE_NO')->where('F_USER_NO',Auth::user()->PK_NO)->first();

        if($user->F_ROLE_NO == 1 || $request['type'] == 'from_ix'){
            $data = PaymentBankAcc::select('BALANCE_ACTUAL','BALACNE_BUFFER')->where('PK_NO',$request['id'])->first();
        }
        if($user->F_ROLE_NO == 1 || $request['type'] == 'from_ex'){
            $data = PaymentBankAcc::select('BALANCE_ACTUAL','BALACNE_BUFFER')->where('PK_NO',$request['id'])->first();
        }
        return $data ?? 0;
    }
}
