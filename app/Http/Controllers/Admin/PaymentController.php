<?php

namespace App\Http\Controllers\Admin;
use Auth;
use App\Models\Vendor;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Reseller;
use Illuminate\Http\Request;
use App\Models\AccountSource;
use App\Models\RefundRequest;
use App\Models\PaymentBankAcc;
use App\Models\PaymentCustomer;
use App\Models\PaymentReseller;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\PaymentRequest;
use App\Http\Requests\Admin\PattyCashRequest;
use App\Repositories\Admin\Order\OrderInterface;
use App\Repositories\Admin\Payment\PaymentInterface;

class PaymentController extends BaseController
{
    protected $paymentInt;
    protected $customer;
    protected $vendor;
    protected $orderInt;
    protected $account_source;
    protected $currency;

    function __construct(PaymentInterface $paymentInt, AccountSource $account_source, Customer $customer, Vendor $vendor, OrderInterface $orderInt, Currency $currency)
    {
        $this->paymentInt         = $paymentInt;
        $this->customer           = $customer;
        $this->vendor 		      = $vendor;
        $this->orderInt 		  = $orderInt;
        $this->account_source     = $account_source;
        $this->currency           = $currency;
    }

    public function getIndex(Request $request)
    {
        $this->resp             = $this->paymentInt->getPaginatedList($request, 20);
        $data['rows']           = $this->resp->data['customer'];
        $data['customer_info']  = $this->resp->data['customer_info'] ?? null;
        $data['selected']       = $this->resp->data['selected'];
        return view('admin.payment.index',compact('data'));
    }

    public function getVrify($id,$type)
    {
        //$this->resp = $this->paymentInt->paymentVrify($id,$type);
        // return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);

    }

    public function getCreate($customer_id = null, $type = null )
    {

        $data = array();
        $acc_source_id         = null;
        if($type == 'customer'){
            $data['customer']      = Customer::where('PK_NO',$customer_id)->where('IS_ACTIVE', 1)->first();
            $data['due_orders']    = $this->orderInt->getDueOrdersCustomer($customer_id);
            $data['remaining_balance'] = PaymentCustomer::where('F_CUSTOMER_NO',$customer_id)
                                        ->where('PAYMENT_REMAINING_MR', '>', 0)
                                        // ->where('PAYMENT_CONFIRMED_STATUS', 1)
                                        ->get();
        }

        if($type == 'reseller'){
            $data['customer']      = Reseller::where('PK_NO',$customer_id)->where('IS_ACTIVE', 1)->first();
            $data['due_orders']    = $this->orderInt->getDueOrdersReseller($customer_id);
            $data['remaining_balance'] = PaymentReseller::where('F_RESELLER_NO',$customer_id)
                                        ->where('PAYMENT_REMAINING_MR', '>', 0)
                                        // ->where('PAYMENT_CONFIRMED_STATUS', 1)
                                        ->get();
        }

        $data['currency']       = $this->currency->pluck('NAME', 'PK_NO');
        $data['payment_acc_no'] = PaymentBankAcc::where('IS_ACTIVE','1')->get();
        $data['type']           = $type;
        return view('admin.payment.create',compact('data'));
    }


    public function getRefund(Request $request, $id, $type)
    {

        $data = array();
        $acc_source_id         = null;
        $data['refund_request'] = null;

        if($request->request_no){
            $data['refund_request'] = RefundRequest::find($request->request_no);
        }
        if($type == 'customer'){
            $data['customer']      = Customer::where('PK_NO',$id)->where('IS_ACTIVE', 1)->first();
            $data['due_orders']    = $this->orderInt->getDueOrdersCustomer($id);
            $data['remaining_balance'] = PaymentCustomer::where('F_CUSTOMER_NO',$id)
                                        ->where('PAYMENT_REMAINING_MR', '>', 0)
                                        // ->where('PAYMENT_CONFIRMED_STATUS', 1)
                                        ->get();
        }

        if($type == 'reseller'){
            $data['customer']      = Reseller::where('PK_NO',$id)->where('IS_ACTIVE', 1)->first();
            $data['due_orders']    = $this->orderInt->getDueOrdersReseller($id);
            $data['remaining_balance'] = PaymentReseller::where('F_RESELLER_NO',$id)
                                        ->where('PAYMENT_REMAINING_MR', '>', 0)
                                        // ->where('PAYMENT_CONFIRMED_STATUS', 1)
                                        ->get();
        }

    }

    public function getDetails($id)
    {
        $this->resp = $this->paymentInt->getDetails($id);
        $data['txn'] = $this->resp->data['txn'];
        $data['order_payments'] = $this->resp->data['order_payments'] ?? null;
        return view('admin.payment.details',compact('data'));
    }


    public function postStore(PaymentRequest $request)
    {
        if($request->payfrom == 'credit'){
            if($request->split_pay == null){
                return redirect()->back()->with('flashMessageError', 'You did not select any order');
            }
        }
        if ($request->type == 'customer') {
            $this->resp = $this->paymentInt->postStore($request);
        }
        if ($request->type == 'reseller') {
            $this->resp = $this->paymentInt->postStoreReseller($request);
        }
        if($request->payfrom == 'cod'){
            return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
        }else{
            return redirect()->route('admin.order.list')->with($this->resp->redirect_class, $this->resp->msg);
        }

    }


    public function getPaymentProcessing(Request $request)
    {
        $data = $this->paymentInt->getPaymentProcessing($request);
        return view('admin.payment.processing')->withData($data->data);
    }

    public function getBankToOther($id = null)
    {
        $data = $this->paymentInt->getBankToOther($id);
        return view('admin.PayToCash.bank_to_other')->withData($data->data);
    }

    public function getBankToOtherList()
    {
        return view('admin.PayToCash.bank_to_other_index');
    }

    public function getBankToBankList()
    {
        return view('admin.PayToCash.bank_to_bank_index');
    }

    public function getBankToBank($id = null)
    {
        $data = $this->paymentInt->getBankToBank($id);

        if (Auth::user()->F_PARENT_USER_ID > 0 && isset($data->data['edit_data'])) {
            $acc_bank_acc = PaymentBankAcc::select('PK_NO')->where('F_USER_NO',Auth::user()->F_PARENT_USER_ID)->first();
            if ($data->data['edit_data']->F_FROM_ACC_PAYMENT_BANK_ACC_NO != $acc_bank_acc->PK_NO) {
                return redirect()->route('admin.account_to_bank_list.view')->with('flashMessageError','You can not edit !');
            }
        }
        return view('admin.PayToCash.bank_to_bank')->withData($data->data);
    }

    public function getBankToBankDetails($id)
    {
        $data = $this->paymentInt->getBankToBankDetails($id);
        return view('admin.PayToCash.internal_details')->withData($data->data);
    }

    public function getBankToOtherDetails($id)
    {
        $data = $this->paymentInt->getBankToOtherDetails($id);
        return view('admin.PayToCash.external_details')->withData($data->data);
    }

    public function postNewPaymentType(Request $request)
    {
        $this->resp = $this->paymentInt->postNewPaymentType($request);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postbankToOther(PattyCashRequest $request)
    {
        $this->resp = $this->paymentInt->postbankToOther($request);
        if ($request->submit == 'update') {
            return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
        }else{
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }
    }

    public function postbankToBank(PattyCashRequest $request)
    {
        $this->resp = $this->paymentInt->postbankToBank($request);
        if ($request->submit == 'update') {
            return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
        }else{
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }
    }

    public function postAccountBalanceInfo(Request $request)
    {
        $data = $this->paymentInt->postAccountBalanceInfo($request);

        return response()->json($data);
    }

    public function getOrderPaymentDelete($id)
    {
        $this->resp = $this->paymentInt->getOrderPaymentDelete($id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->paymentInt->getDelete($id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);

    }

    public function postUpdatePartial(Request $request)
    {
        $this->resp = $this->paymentInt->postUpdatePartial($request);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

}

