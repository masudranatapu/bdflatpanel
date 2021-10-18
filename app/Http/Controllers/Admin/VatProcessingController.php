<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\User;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\Currency;
use App\Models\AdminUser;
use App\Models\Warehouse;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Models\AccountMethod;
use App\Models\AccountSource;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\InvoiceRequest;
use App\Repositories\Admin\Invoice\InvoiceInterface;
use App\Http\Requests\Admin\InvoiceProcessingRequest;
use App\Repositories\Admin\InvoiceDetails\InvoiceDetailsInterface;

class VatProcessingController extends BaseController
{
	protected $invoice;
	protected $vendor;
	protected $currency;
	protected $subcategory;
	protected $admin_user;
    protected $account_source;
    protected $payment_method;
    protected $bank_acc;
    protected $invoice_details;

	function __construct(
        InvoiceInterface $invoice
        , Vendor $vendor
        , Currency $currency
        , AdminUser $admin_user
        , AccountSource $account_source
        , BankAccount $bank_acc
        , AccountMethod $payment_method
        , Warehouse $warehouse
        , InvoiceDetailsInterface $invoice_details
    )
	{
		$this->invoice 	          = $invoice;
		$this->vendor 		      = $vendor;
		$this->currency 	      = $currency;
		$this->admin_user 	      = $admin_user;
        $this->account_source     = $account_source;
        $this->payment_method     = $payment_method;
        $this->bank_acc           = $bank_acc;
        $this->warehouse          = $warehouse;
        $this->invoice_details    = $invoice_details;

	}

	public function getIndex(Request $request)
    {
        $data = array();
        $this->resp = $this->invoice->getVatProcessing();
        $data['warehouse_combo'] = $this->warehouse->getWarehpuseCombo();
        $data['rows'] = $this->resp->data;

        return view('admin.procurement.vat_processing.index', compact('data'));
    }

    public function getCreate(Request $request)
    {
        if(request()->get('parent') && (request()->get('parent') != '' )){
            $parent_invoice = Invoice::find(request()->get('parent'));
        }else{
            $parent_invoice = null;
        }


        $acc_source_id         = null;
        $vendors 	           = $this->vendor->get();
        $currency 	           = Currency::get();
        $user 		           = User::select(DB::raw("CONCAT(FIRST_NAME,' ',LAST_NAME) AS USERNAME"),'PK_NO')->pluck('USERNAME','PK_NO');
        $acc_source            = $this->account_source->getAllSource();
        $payment_method        = $this->payment_method->getAllPaymentMethod($acc_source_id);
        $bank_acc              = $this->bank_acc->getAllBankAcc($acc_source_id);
        $gbp_to_mr_rate        = Currency::where('CODE','RM')->first();

        return view('admin.procurement.invoice.create')
            ->withVendors($vendors)
            ->withParentInvoice($parent_invoice)
            ->withCurrency($currency)
            ->withUser($user)
            ->withAccSource($acc_source)
            ->withBankAcc($bank_acc)
            ->withPaymentMethod($payment_method)
            ->withGbpToMrRate($gbp_to_mr_rate);
    }

    public function getBankAcc($acc_source_id){
        $data['payment_method'] = $this->payment_method->getAllPaymentMethod($acc_source_id,'combo');
        $data['bank_acc'] = $this->bank_acc->getAllBankAcc($acc_source_id,'combo');
        return response()->json($data);
    }

    public function postStore(InvoiceRequest $request)
    {
        $this->resp = $this->invoice->postStore($request);
        if ($this->resp->status) {
            $pk_no = $this->resp->id;
            return redirect()->route('admin.invoice-details.new',['id' => $pk_no])->with($this->resp->redirect_class, $this->resp->msg);
        }else{
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }

    }

    public function getEdit(Request $request, $id)
    {
        $acc_source_id         = null;
        $vendors               = $this->vendor->get();
        $currency              = Currency::get();
        $user                  = User::select(DB::raw("CONCAT(FIRST_NAME,' ',LAST_NAME) AS USERNAME"),'PK_NO')->pluck('USERNAME','PK_NO');
        $acc_source            = $this->account_source->getAllSource();
        $payment_method        = $this->payment_method->getAllPaymentMethod($acc_source_id);
        $bank_acc              = $this->bank_acc->getAllBankAcc($acc_source_id);
        $gbp_to_mr_rate        = Currency::where('CODE','RM')->first();
        $invoice               = $this->invoice->findOrThrowException($id);
        $invoice_details       = $this->invoice_details->getPaginatedList($request, 200, $id);
        return view('admin.procurement.invoice.edit')
            ->withInvoice($invoice->data)
            ->withItems($invoice_details->data)
            ->withVendors($vendors)
            ->withCurrency($currency)
            ->withUser($user)
            ->withAccSource($acc_source)
            ->withBankAcc($bank_acc)
            ->withPaymentMethod($payment_method)
            ->withGbpToMrRate($gbp_to_mr_rate);
    }

    public function postUpdate(InvoiceRequest $request, $id)
    {
        $this->resp = $this->invoice->postUpdate($request, $id);
        if ($this->resp->status) {
            $pk_no = $this->resp->id;
            return redirect()->route('admin.invoice-details.new',['id' => $pk_no])->with($this->resp->redirect_class, $this->resp->msg);
        }else{
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }
    }


    public function getDelete($id)
    {
        $this->resp = $this->invoice->delete($id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getImgDelete($id)
    {
        $this->resp = $this->invoice->deleteImage($id);
        return response()->json($this->resp);
    }


    public function invoiceProcessing(Request $request)
    {
        $data = array();
        $this->resp = $this->invoice->getPaginatedListForProcess($request);
        $data['warehouse_combo'] = $this->warehouse->getWarehpuseCombo();
        $data['rows'] = $this->resp->data;

        return view('admin.procurement.invoice_processing.index', compact('data'));
    }

    public function getStockDelete($invoice_id)
    {
        $data = array();
        $this->resp = $this->invoice->getDeleteGeneratedStock($invoice_id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }


    public function invoiceQBentry($invoice_id)
    {
        $this->resp = $this->invoice->invoiceQBentry($invoice_id);
       return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function invoiceLoyaltyClaime($invoice_id)
    {
        $this->resp = $this->invoice->invoiceLoyaltyClaime($invoice_id);
       return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function invoiceVatClaime($invoice_id)
    {
        $this->resp = $this->invoice->invoiceVatClaime($invoice_id);
       return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postStoreInvoiceProcessing(InvoiceProcessingRequest $request)
    {
        $this->resp = $this->invoice->postStoreInvoiceProcessing($request);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);

    }






}
?>
