<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Reseller;
use App\Models\AccBankTxn;
use App\Models\PaymentExfer;
use App\Models\PaymentIxfer;
use Illuminate\Http\Request;
use App\Models\AuthUserGroup;
use App\Models\PaymentBankAcc;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\VendorRequest;
use App\Repositories\Admin\Order\OrderInterface;
use App\Repositories\Admin\Booking\BookingInterface;
use App\Repositories\Admin\Dispatch\DispatchInterface;

class DispatchController extends BaseController
{
    protected $bookingInt;
    protected $booking_model;
    protected $customer;
    protected $prd_variant;
    protected $agent;
    protected $reseller;
    protected $order;
    protected $dispatch;

    public function __construct(BookingInterface $bookingInt, Booking $booking_model, Customer $customer, ProductVariant $prd_variant, Agent $agent, Reseller $reseller,OrderInterface $order, DispatchInterface $dispatch)
    {
        $this->customer        = $customer;
        $this->bookingInt      = $bookingInt;
        $this->booking_model   = $booking_model;
        $this->prd_variant     = $prd_variant;
        $this->agent           = $agent;
        $this->reseller        = $reseller;
        $this->order           = $order;
        $this->dispatch        = $dispatch;
    }


    public function getDispatch(Request $request,$id)
    {
        $this->resp                 = $this->dispatch->getOrderForDisopatch($id);
        $data['booking']            = $this->resp->data['booking'];
        $data['payment_acc_no']     = PaymentBankAcc::where('IS_ACTIVE','1')->get();

        // dd($data['booking']->booking_details_returned);
        if(count($data['booking']->booking_details) > 0 && $data['booking']->booking_details[0]->CURRENT_IS_SM == 0){
            $data['courier']            = DB::table('SC_COURIER')->where('PK_NO','!=',9)->orderBy('ORDER_ID','ASC')->get();
        }else{
            $data['courier']            = DB::table('SC_COURIER')->orderBy('ORDER_ID','ASC')->get();
        }
        $data['SC_ORDER_DISPATCH']  = DB::table('SC_ORDER_DISPATCH')->select('IS_DISPATHED')->where('F_ORDER_NO',$id)->first();

        return view('admin.dispatch.dispatch',compact('data'));
    }

    public function getCodRtcUserStockList(Request $request,$id)
    {
        if( Auth::user()->F_AGENT_NO > 0){
            $id = Auth::user()->PK_NO;
        }
        $this->resp                 = $this->dispatch->getCodRtcUserStockList($request,$id);
        $data = $this->resp->data;
        return view('admin.collect-list.collect_list_shelved',compact('data'));
    }

    public function getCollectionList()
    {
        if( Auth::user()->F_AGENT_NO > 0){
            $data = PaymentBankAcc::where('IS_COD',1)->where('IS_ACTIVE','1')->where('F_USER_NO',Auth::user()->PK_NO)->get();
            // dd($data['payment_acc_no']);
        }else{
            $data = PaymentBankAcc::where('IS_COD',1)->where('IS_ACTIVE','1')->get();
        }

        return view('admin.dispatch.collect_list',compact('data'));
    }

    public function getCollectionListBreakdown($id)
    {
        if( Auth::user()->F_AGENT_NO > 0){
            $id = Auth::user()->PK_NO;
        }
        $this->resp                 = $this->dispatch->getCollectionBreakdown($id);
        $data['rows']               = $this->resp->data['customer'];
        $data['customer_info']      = $this->resp->data['customer_info'] ?? null;
        $data['name']               = $this->resp->data['name'] ?? null;

        $data['inter_from_ixfer']   = PaymentIxfer::where('F_FROM_ACC_PAYMENT_BANK_ACC_NO',$id)
                                        ->orderBy('SS_CREATED_ON','DESC')
                                        ->get();
        // $data['inter_to_ixfer']     = AccBankTxn::join('ACC_PAYMENT_BANK_ACC_IXFER as ix','ix.F_TO_ACC_PAYMENT_BANK_ACC_NO','ACC_BANK_TXN.F_ACC_PAYMENT_BANK_NO')
        //                                 ->select('ix.ENTERED_MR_AMOUNT','ix.PK_NO','ACC_BANK_TXN.PK_NO as asd')
        //                                 ->where('ix.F_TO_ACC_PAYMENT_BANK_ACC_NO',$id)
        //                                 ->where('ACC_BANK_TXN.IS_CUS_RESELLER_BANK_RECONCILATION',4)
        //                                 ->groupBy('ACC_BANK_TXN.PK_NO')
        //                                 ->get();
        $data['inter_to_ixfer']     = PaymentIxfer::where('F_TO_ACC_PAYMENT_BANK_ACC_NO',$id)
                                        ->where('IS_VERIFIED','!=',2)
                                        ->orderBy('SS_CREATED_ON','DESC')
                                        ->get();

        $data['party_exfer']        = PaymentExfer::where('F_I_ACC_PAYMENT_BANK_ACC_NO',$id)
                                        ->where('IS_VERIFIED','!=',2)
                                        ->orderBy('SS_CREATED_ON','DESC')
                                        ->get();

        $result = DB::SELECT("SELECT result.* FROM ( SELECT NULL as ORDER_PK_NO, NULL AS ORDER_ACTUAL_TOPUP, NULL AS ORDER_BUFFER_TOPUP, NULL AS ORDER_BALANCE_RETURN, NULL AS DISPATCH_STATUS, NULL AS BOOKING_PK_NO, NULL AS BOOKING_NO, NULL AS ORDER_PRICE, NULL AS ORDER_DISCOUNT, NULL AS BOOKING_STATUS, ix.PK_NO as XFER_PK, t.CODE AS PAYMENT_NO, u.USERNAME as ENTRY_BY_NAME, ix.SS_CREATED_ON as DATE_AT, t.PK_NO as TX_PK_NO, t.IS_MATCHED AS PAYMENT_VERIFY, ix.ENTERED_MR_AMOUNT as PAY_AMOUNT, NULL as IS_IN, t.MATCHED_ON, 'from_inernal' AS TYPE, ix.ACC_CUSTOMER_PAYMENT_METHOD as PAYMENT_METHOD, NULL as PAID_BY, NULL as PARTY_HEAD, NULL as CUSTOMER_NAME, NULL as CUS_PK, acc.BANK_NAME, acc.BANK_ACC_NAME FROM ACC_PAYMENT_BANK_ACC_IXFER as ix JOIN ACC_PAYMENT_BANK_ACC AS acc ON ix.F_TO_ACC_PAYMENT_BANK_ACC_NO = acc.PK_NO JOIN SA_USER as u ON u.PK_NO = ix.SS_CREATED_BY JOIN ACC_BANK_TXN as t ON t.PK_NO = ix.F_FROM_ACC_BANK_TXN WHERE ix.F_FROM_ACC_PAYMENT_BANK_ACC_NO = $id

        UNION

        SELECT NULL as ORDER_PK_NO, NULL AS ORDER_ACTUAL_TOPUP, NULL AS ORDER_BUFFER_TOPUP, NULL AS ORDER_BALANCE_RETURN, NULL AS DISPATCH_STATUS, NULL AS BOOKING_PK_NO, NULL AS BOOKING_NO, NULL AS ORDER_PRICE, NULL AS ORDER_DISCOUNT, NULL AS BOOKING_STATUS, ix.PK_NO as XFER_PK, t.CODE AS PAYMENT_NO, u.USERNAME as ENTRY_BY_NAME, ix.SS_CREATED_ON as DATE_AT, t.PK_NO as TX_PK_NO, t.IS_MATCHED AS PAYMENT_VERIFY, ix.ENTERED_MR_AMOUNT as PAY_AMOUNT, NULL as IS_IN, t.MATCHED_ON, 'to_inernal' AS TYPE, ix.ACC_CUSTOMER_PAYMENT_METHOD as PAYMENT_METHOD, NULL as PAID_BY, NULL as PARTY_HEAD, NULL as CUSTOMER_NAME, NULL as CUS_PK, acc.BANK_NAME, acc.BANK_ACC_NAME FROM ACC_PAYMENT_BANK_ACC_IXFER as ix JOIN ACC_PAYMENT_BANK_ACC AS acc ON ix.F_FROM_ACC_PAYMENT_BANK_ACC_NO = acc.PK_NO JOIN SA_USER as u ON u.PK_NO = ix.SS_CREATED_BY JOIN ACC_BANK_TXN as t ON t.PK_NO = ix.F_TO_ACC_BANK_TXN WHERE ix.F_TO_ACC_PAYMENT_BANK_ACC_NO = $id

        UNION

        SELECT NULL as ORDER_PK_NO, NULL AS ORDER_ACTUAL_TOPUP, NULL AS ORDER_BUFFER_TOPUP, NULL AS ORDER_BALANCE_RETURN, NULL AS DISPATCH_STATUS, NULL AS BOOKING_PK_NO, NULL AS BOOKING_NO, NULL AS ORDER_PRICE, NULL AS ORDER_DISCOUNT, NULL AS BOOKING_STATUS, ex.PK_NO as XFER_PK, t.CODE AS PAYMENT_NO, u.USERNAME as ENTRY_BY_NAME, ex.SS_CREATED_ON as DATE_AT, t.PK_NO as TX_PK_NO, t.IS_MATCHED AS PAYMENT_VERIFY, ex.ENTERED_MR_AMOUNT as PAY_AMOUNT, ex.IS_IN as IS_IN, t.MATCHED_ON, 'party_payment' AS TYPE, ex.ACC_PARTY_PAYMENT_METHOD as PAYMENT_METHOD, NULL as PAID_BY, h.NARRATION as PARTY_HEAD, NULL as CUSTOMER_NAME, NULL as CUS_PK, NULL as BANK_NAME, NULL as BANK_ACC_NAME FROM ACC_PAYMENT_BANK_ACC_EXFER as ex JOIN ACC_PAYMENT_BANK_ACC AS acc ON ex.F_I_ACC_PAYMENT_BANK_ACC_NO = acc.PK_NO JOIN SA_USER as u ON u.PK_NO = ex.SS_CREATED_BY JOIN ACC_BANK_TXN as t ON t.PK_NO = ex.F_ACC_BANK_TXN JOIN ACC_PAYMENT_ACC_HEAD as h on ex.F_ACC_PAYMENT_ACC_HEAD_NO = h.PK_NO WHERE ex.F_I_ACC_PAYMENT_BANK_ACC_NO = $id

        UNION

        SELECT o.PK_NO AS ORDER_PK_NO, o.ORDER_ACTUAL_TOPUP, o.ORDER_BUFFER_TOPUP, o.ORDER_BALANCE_RETURN, o.DISPATCH_STATUS, b.PK_NO AS BOOKING_PK_NO, b.BOOKING_NO
        , b.TOTAL_PRICE AS ORDER_PRICE, b.DISCOUNT AS ORDER_DISCOUNT, b.BOOKING_STATUS,NULL AS XFER_PK, t.CODE AS PAYMENT_NO, u.USERNAME AS ENTRY_BY_NAME, cp.SS_CREATED_ON AS DATE_AT, t.PK_NO AS TX_PK_NO, NULL AS PAYMENT_VERIFY, t.AMOUNT_BUFFER AS PAY_AMOUNT, NULL AS IS_IN, t.MATCHED_ON, 'Customer' AS TYPE, NULL AS PAYMENT_METHOD, cp.PAID_BY, NULL AS PARTY_HEAD, cp.CUSTOMER_NAME, cp.F_CUSTOMER_NO AS CUS_PK, NULL AS BANK_NAME, NULL AS BANK_ACC_NAME
        FROM ACC_BANK_TXN AS t
        JOIN ACC_CUSTOMER_PAYMENTS AS cp ON cp.PK_NO = t.F_CUSTOMER_PAYMENT_NO
        LEFT JOIN ACC_ORDER_PAYMENT AS od ON od.F_ACC_CUSTOMER_PAYMENT_NO = cp.PK_NO
        JOIN SLS_ORDER AS o ON
        CASE
            WHEN od.ORDER_NO IS NULL
                THEN o.F_CUSTOMER_NO = cp.F_CUSTOMER_NO
            WHEN od.ORDER_NO IS NOT NULL
                THEN o.PK_NO = od.ORDER_NO
        END
        JOIN SLS_BOOKING AS b ON b.PK_NO = o.F_BOOKING_NO
        JOIN SA_USER AS u ON u.PK_NO = b.F_SS_CREATED_BY
        WHERE t.F_ACC_PAYMENT_BANK_NO = $id
        AND t.IS_COD = 1

        UNION

        SELECT o.PK_NO AS ORDER_PK_NO, o.ORDER_ACTUAL_TOPUP, o.ORDER_BUFFER_TOPUP, o.ORDER_BALANCE_RETURN, o.DISPATCH_STATUS, b.PK_NO AS BOOKING_PK_NO, b.BOOKING_NO
        , b.TOTAL_PRICE AS ORDER_PRICE, b.DISCOUNT AS ORDER_DISCOUNT, b.BOOKING_STATUS,NULL as XFER_PK, t.CODE AS PAYMENT_NO, u.USERNAME AS ENTRY_BY_NAME, rp.SS_CREATED_ON AS DATE_AT, t.PK_NO AS TX_PK_NO, NULL AS PAYMENT_VERIFY, t.AMOUNT_BUFFER AS PAY_AMOUNT, NULL as IS_IN, t.MATCHED_ON, 'Reseller' AS TYPE, NULL as PAYMENT_METHOD, rp.PAID_BY, NULL as PARTY_HEAD, rp.RESELLER_NAME as CUSTOMER_NAME, rp.F_RESELLER_NO as CUS_PK, NULL as BANK_NAME, NULL as BANK_ACC_NAME
        FROM ACC_BANK_TXN AS t
        JOIN ACC_RESELLER_PAYMENTS AS rp ON rp.PK_NO = t.F_RESELLER_PAYMENT_NO
        LEFT JOIN ACC_ORDER_PAYMENT AS od ON od.F_ACC_RESELLER_PAYMENT_NO = rp.PK_NO
        JOIN SLS_ORDER AS o ON
        CASE
            WHEN od.ORDER_NO IS NULL
                THEN o.F_RESELLER_NO = rp.F_RESELLER_NO
            WHEN od.ORDER_NO IS NOT NULL
                THEN o.PK_NO = od.ORDER_NO
        END
        JOIN SLS_BOOKING AS b ON b.PK_NO = o.F_BOOKING_NO
        JOIN SA_USER AS u ON u.PK_NO = b.F_SS_CREATED_BY
        WHERE t.F_ACC_PAYMENT_BANK_NO = $id
        AND t.IS_COD = 1
        -- SELECT NULL as ORDER_PK_NO, NULL AS ORDER_ACTUAL_TOPUP, NULL AS ORDER_BUFFER_TOPUP, NULL AS ORDER_BALANCE_RETURN, NULL AS DISPATCH_STATUS, NULL AS BOOKING_PK_NO, NULL AS BOOKING_NO, NULL AS ORDER_PRICE, NULL AS ORDER_DISCOUNT, NULL AS BOOKING_STATUS, cp.PAYMENT_DATE as DATE_AT, t.CODE AS PAYMENT_NO, cp.MR_AMOUNT AS PAY_AMOUNT, t.PK_NO as TX_PK_NO, t.IS_MATCHED AS PAYMENT_VERIFY, u.USERNAME as ENTRY_BY_NAME, t.MATCHED_ON, 'Payment' AS TYPE FROM ACC_CUSTOMER_PAYMENTS AS cp LEFT JOIN SA_USER as u ON u.PK_NO = cp.F_SS_CREATED_BY LEFT JOIN ACC_BANK_TXN AS t ON t.F_CUSTOMER_PAYMENT_NO = cp.PK_NO  WHERE cp.F_PAYMENT_ACC_NO = $id

        ) result ORDER BY result.DATE_AT ASC

        ");
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($result);
        // echo '<br>======================<br>';
        // exit();
        return view('admin.collect-list.collect_list_breakdown',compact('data','result'));
    }

    public function getDispatchList(Request $request)
    {
        // $this->resp = $this->order->getIndex($request);
        // return view('admin.dispatch.index')->withOrder($this->resp->data);
        if( Auth::user()->F_AGENT_NO > 0){
            $data['payment_acc_no'] = PaymentBankAcc::where('IS_COD',1)->where('F_USER_NO',Auth::user()->PK_NO)->get();
            // dd($data['payment_acc_no']);
        }else{
            $data['payment_acc_no'] = PaymentBankAcc::where('IS_COD',1)->get();
        }
        return view('admin.dispatch.index',compact('data'));
    }

    public function getOrderCollectList()
    {
        return view('admin.dispatch.order_collect');
    }

    public function getItemCollectList()
    {
        return view('admin.dispatch.item_collect');
    }

    public function getItemCollectedList()
    {
        $dropdown = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                    ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                    ->select('SA_USER.PK_NO','USERNAME')
                    ->where('F_ROLE_NO',20)
                    ->get();
        return view('admin.dispatched.item_collect',compact('dropdown'));
    }

    public function getBatchCollectList()
    {
        $this->resp = $this->dispatch->getBatchCollectList();
        return view('admin.dispatch.batch_list')->withRows($this->resp->data);
    }

    public function getBatchCollectedList()
    {
        $this->resp = $this->dispatch->getBatchCollectedList();
        return view('admin.dispatched.batch_list')->withRows($this->resp->data);
    }

    public function getDispatchedList(Request $request)
    {
        $this->resp = $this->dispatch->getPaginatedList($request);
        if ($request->get('type') == 'returned') {
            return view('admin.dispatch.returned')->withRows($this->resp->data);
        }
        return view('admin.dispatch.dispatched')->withRows($this->resp->data);

    }

    public function getPendingAppDispatch()
    {
        $this->resp = $this->dispatch->getAppPendingDispatchList();
        return view('admin.dispatch.pending_app_dispatch')->withRows($this->resp->data);
    }

    public function getRevertDispatch($id)
    {
        $this->resp = $this->dispatch->getRevertDispatch($id);
        return redirect()->route('admin.dispatch.list',['dispatch=rts'])->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postDispatch(Request $request,$id)
    {
        $dispatch = $request->dispatch;
        $this->resp = $this->dispatch->postStore($request);
        if($request->submit == 'app_dispatch'){
            return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
        }else{
            return redirect()->route('admin.dispatch.list',['dispatch' => $dispatch])->with($this->resp->redirect_class, $this->resp->msg);
        }
    }

    public function getRevertbatch($id)
    {
        $this->resp = $this->dispatch->getRevertbatch($id);
        return redirect()->route('admin.dispatch.list',['dispatch=rts'])->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postAssignOrderItem(Request $request)
    {
        $data = $this->dispatch->postAssignOrderItem($request);
        // return redirect()->route('admin.item_collected.list',$request->batch_id)->with($this->resp->redirect_class, $this->resp->msg);
        return $data;
    }

    public function postMarkPickup(Request $request)
    {
        $this->resp = $this->dispatch->postMarkPickup($request);
        return response()->json($this->resp);
    }

    public function postAssignOrderBulkItem(Request $request)
    {
        $this->resp = $this->dispatch->postAssignOrderBulkItem($request);
        return response()->json($this->resp);
    }

    public function postSpecialNoteStatus(Request $request)
    {
        $this->resp = $this->dispatch->postSpecialNoteStatus($request);
        return response()->json($this->resp);
    }
}
