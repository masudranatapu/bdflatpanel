<?php

namespace App\Http\Controllers\Admin;

use Session;
use App\Models\Currency;
use App\Models\SubCategory;
use App\Models\ProductVariant;
use App\Models\Invoice;
use App\Http\Controllers\BaseController;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use App\Repositories\Admin\InvoiceDetails\InvoiceDetailsInterface;

class InvoiceDetailsController extends BaseController
{
	protected $invoice_details;
	protected $currency;
	protected $subcategory;
    protected $invoice;
    protected $productVariant;
    use CommonTrait;

	function __construct(
        InvoiceDetailsInterface $invoice_details
        , Currency $currency
        , SubCategory $subcategory
        , Invoice $invoice
        , ProductVariant $productVariant
    )
	{
		$this->invoice_details    = $invoice_details;
		$this->currency 	      = $currency;
		$this->subcategory 	      = $subcategory;
        $this->invoice            = $invoice;
        $this->productVariant     = $productVariant;
	}

	public function getIndex(Request $request, $id)
    {
        $data               = array();
        $this->resp         = $this->invoice_details->getPaginatedList($request, 200, $id);
        $data['rows']       = $this->resp->data;
        $invoice            = $this->invoice_details->getInvoiceData($id);
        $data['invoice']    = $invoice->data;
        return view('admin.procurement.invoice-details.index', compact('data'));
    }

    public function getCreate(Request $request, $id)
    {
        $data           = array();
        $variant_info   = null;
        $variant_pk_arr = $this->getVariantNo($request);
        if ($variant_pk_arr) {
           $variant_info = $this->productVariant->getProductVariantInfo($variant_pk_arr);
        }
        $this->resp     = $this->invoice_details->getInvoiceData($id);
        $this->resp->old_data       = $this->invoice_details->getPaginatedList($request, 200, $id);
        $data['variant_info']   = $variant_info;
        $data['invoice_info']   = $this->resp->data;
        $data['old_data']       = $this->resp->old_data->data;
        Session::put('list_type', '');
        return view('admin.procurement.invoice-details.create', compact('data'));
    }

    public function getVariantListById(Request $request)
    {
        $this->resp = $this->invoice_details->getVariantListById($request->data);
        return response()->json($this->resp);
    }

    public function getVariantListByBarCode($bar_code)
    {
        $this->resp = $this->invoice_details->getVariantListByBarCode($bar_code);
        return response()->json($this->resp);
    }

    public function getProductBySubCategory($id)
    {
        $product 	= $this->invoice_details->getProductBySubCategory($id);
        return response()->json($product);
    }

    public function postStore(Request $request)
    {
        $this->resp = $this->invoice_details->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
    public function getDelete($id)
    {
        $this->resp = $this->invoice_details->delete($id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }


    public function getVariantListByQueryString(Request $request, $queryString)
    {
        $products = $this->invoice_details->getVariantListByQueryString($request, $queryString);
        return response()->json($products->data);
    }

    public function getProductByInvoice($id,$type)
    {
        $this->resp = $this->invoice_details->getProductByInvoice($id,$type);
        return view($this->resp->redirect_to)->withData($this->resp->data)->withInvoiceid($id)->withPagetype($type);
    }

}
