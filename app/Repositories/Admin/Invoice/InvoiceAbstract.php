<?php
namespace App\Repositories\Admin\Invoice;

use App\Models\Stock;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\InvoiceImgLib;
use App\Models\InvoiceRequest;
use App\Models\Currency;
use App\Models\AccountSource;
use App\Models\BankAccount;
use App\Models\AccountMethod;
use App\Models\StockGeneration;
use App\Traits\RepoResponse;
use DB;

class InvoiceAbstract implements InvoiceInterface
{
    use RepoResponse;

    protected $vendor;
    protected $invoice;

    public function __construct(Vendor $vendor, Invoice $invoice)
    {
        $this->vendor   = $vendor;
        $this->invoice  = $invoice;
    }

    public function getPaginatedList($request, int $per_page = 20)
    {
        $data = $this->invoice->where('IS_ACTIVE',1)
            ->orderBy('INVOICE_DATE', 'desc')
            ->orderBy('PK_NO', 'desc')
            // ->where('PRC_STOCK_IN.INV_STOCK_RECORD_GENERATED','!=',1)
            ->get();

        return $this->formatResponse(true, '', 'admin.invoice', $data);
    }

    public function getVatProcessing()
    {
        $data = $this->invoice->where('IS_ACTIVE',1)
            ->where('F_SS_CURRENCY_NO',1)
            ->orderBy('INVOICE_DATE', 'desc')
            ->orderBy('PK_NO', 'desc')
            ->get();
        return $this->formatResponse(true, '', 'admin.invoice', $data);
    }

    public function getPaginatedListForProcess($request, int $per_page = 20)
    {
        $data =  $this->invoice->select(['PRC_STOCK_IN.*',DB::raw('count(1) AS total_child')])
                ->join('PRC_STOCK_IN_DETAILS', 'PRC_STOCK_IN_DETAILS.F_PRC_STOCK_IN', '=', 'PRC_STOCK_IN.PK_NO')
                ->groupBy('PRC_STOCK_IN.PK_NO')
                ->havingRaw('total_child > 0')
                ->where('PRC_STOCK_IN.IS_ACTIVE',1)
                ->orderBy('PRC_STOCK_IN.INVOICE_DATE', 'desc')
                ->orderBy('PRC_STOCK_IN.PK_NO', 'desc')
                ->get();

        return $this->formatResponse(true, '', 'admin.invoice', $data);
    }




    public function postStore($request)
    {
        $currency    = Currency::select('NAME', 'CODE')->where('PK_NO', '=', $request->currency)->first();
        $vendor      = Vendor::select('NAME', 'HAS_LOYALITY')->where('PK_NO', '=', $request->vendor)->first();
        $pay_source  = AccountSource::select('NAME')->where('PK_NO',$request->payment_source)->first();
        $acc_bank    = BankAccount::select('NAME')->where('PK_NO',$request->acc_bank)->first();
        $pay_method  = AccountMethod::select('NAME')->where('PK_NO',$request->payment_methods)->first();

        DB::beginTransaction();

        try {
            $invoice = new Invoice();
            $invoice->INVOICE_NO            = $request->invoice_no;
            $invoice->F_PAYMENT_SOURCE_NO   = $request->payment_source;
            $invoice->PAYMENT_SOURCE_NAME   = $pay_source->NAME;
            $invoice->F_PAYMENT_ACC_NO      = $request->acc_bank;
            $invoice->PAYMENT_ACC_NAME      = $acc_bank->NAME;
            $invoice->F_PAYMENT_METHOD_NO   = $request->payment_methods;
            $invoice->PAYMENT_METHOD_NAME   = $pay_method->NAME;
            $invoice->INVOICE_DATE          = date('Y-m-d',strtotime($request->invoice_date));
            $invoice->F_VENDOR_NO           = $request->vendor;
            $invoice->VENDOR_NAME           = $vendor->NAME;
            $invoice->F_PURCHASER_USER_NO   = $request->purchaser;
            $invoice->F_SS_CURRENCY_NO      = $request->currency;
            $invoice->INVOICE_CURRENCY      = $currency->CODE;
            $invoice->DISCOUNT_PERCENTAGE   = $request->discount_percentage ?? 0;
            $invoice->DISCOUNT_AMOUNT       = $request->discount_amount ?? 0;
            $invoice->DISCOUNT2_PERCENTAGE  = $request->discount_percentage2 ?? 0;
            $invoice->DISCOUNT2_AMOUNT      = $request->discount_amount2 ?? 0;
            $invoice->TOTAL_QTY             = $request->total_qty;
            $invoice->RECIEVED_QTY          = $request->recieved_qty;
            $invoice->HAS_LOYALTY           = $vendor->HAS_LOYALITY;
            $invoice->HAS_VAT_REFUND        = $request->has_vat_refund;
            $invoice->GBP_TO_MR_RATE        = $request->gbp_to_mr;
            $invoice->GBP_TO_AC_RATE        = $request->gbp_to_ac;
            $invoice->INVOICE_EXACT_VALUE   = $request->invoice_exact_value ?? 0;
            $invoice->INVOICE_EXACT_VAT     = $request->exact_vat ?? 0;
            $invoice->SETTLEMENT_AMT        = $request->settlement_amt;
            $invoice->INVOICE_EXACT_POSTAGE = $request->postage ?? 0;
            $invoice->F_PARENT_PRC_STOCK_IN = $request->parent_invoice ?? null;
            $invoice->INV_STOCK_RECORD_GENERATED  = 0;
            $invoice->DESCRIPTION           = $request->description;

            if($currency->CODE == 'GBP'){
                if(isset($request->gbp_to_mr) && $request->gbp_to_mr != ''){
                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_MR           = $request->gbp_to_mr*$request->total_vat;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_MR            = $request->gbp_to_mr*$request->total_accept_vat;
                    // $invoice->INVOICE_TOTAL_ACTUAL_MR               = $request->gbp_to_mr*$request->total_amount;
                    // $invoice->INVOICE_POSTAGE_ACTUAL_MR             = $request->gbp_to_mr*$request->postage;

                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_GBP                 = $request->total_vat;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_GBP                  = $request->total_accept_vat;
                    // $invoice->INVOICE_TOTAL_ACTUAL_GBP                     = $request->total_amount;
                    $invoice->INVOICE_POSTAGE_ACTUAL_GBP                   = $request->postage;
                }

            } elseif ($currency->CODE == 'RM'){
                if(isset($request->gbp_to_mr) && $request->gbp_to_mr != ''){
                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_MR           = $request->total_vat;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_MR            = $request->total_accept_vat;
                    // $invoice->INVOICE_TOTAL_ACTUAL_MR               = $request->total_amount;
                    $invoice->INVOICE_POSTAGE_ACTUAL_MR             = $request->postage;

                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_GBP         = $request->total_vat/$request->gbp_to_mr;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_GBP          = $request->total_accept_vat/$request->gbp_to_mr;
                    // $invoice->INVOICE_TOTAL_ACTUAL_GBP             = $request->total_amount/$request->gbp_to_mr;
                    $invoice->INVOICE_POSTAGE_ACTUAL_GBP           = $request->postage/$request->gbp_to_mr;
                }
            } else {
                if(isset($request->gbp_to_ac) && $request->gbp_to_ac != ''){

                    // $total_vat_gbp          = $request->total_vat/$request->gbp_to_ac;
                    // $total_except_vat_gbp   = $request->total_accept_vat/$request->gbp_to_ac;
                    // $total_gbp              = $request->total_amount/$request->gbp_to_ac;
                    $total_postage          = $request->postage/$request->gbp_to_ac;

                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_GBP                 = $total_vat_gbp;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_GBP                  = $total_except_vat_gbp;
                    // $invoice->INVOICE_TOTAL_ACTUAL_GBP                     = $total_gbp;
                    $invoice->INVOICE_POSTAGE_ACTUAL_GBP                   = $total_postage;

                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_AC                  = $request->total_vat;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_AC                   = $request->total_accept_vat;
                    // $invoice->INVOICE_TOTAL_ACTUAL_AC                      = $request->total_amount;
                    $invoice->INVOICE_POSTAGE_ACTUAL_AC                    = $request->postage;

                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_MR           = $request->gbp_to_mr*$total_vat_gbp;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_MR            = $request->gbp_to_mr*$total_except_vat_gbp;
                    // $invoice->INVOICE_TOTAL_ACTUAL_MR               = $request->gbp_to_mr*$total_gbp;
                    $invoice->INVOICE_POSTAGE_ACTUAL_MR             = $request->gbp_to_mr*$request->postage;
                }
            }

            $invoice->save();

            if ($request->parent_invoice) {
                Invoice::where('PK_NO', $request->parent_invoice)->update(['F_CHILD_PRC_STOCK_IN' => $invoice->PK_NO]);
            }

            if ($request->file('images')) {
                $i = 0;
                foreach($request->file('images') as $key => $image)
                    {
                        $file_name = 'invoice_'. date('dmY'). '_' .uniqid(). '.' . $image->getClientOriginalExtension();
                        $img_lib                    = new InvoiceImgLib();
                        $img_lib->F_PRC_STOCK_IN_NO = $invoice->PK_NO;
                        $img_lib->F_FILE_TYPE_NO    = 1;
                        $img_lib->FILE_EXT          = $image->getClientOriginalExtension();
                        $img_lib->RELATIVE_PATH     = '/media/images/invoices/'.$invoice->PK_NO.'/'.$file_name;
                        $img_lib->SERIAL_NO         = $i;
                        $img_lib->save();
                        if($i == 0){
                            $def_relative_path      = '/media/images/invoices/'.$invoice->PK_NO.'/'.$file_name;
                        }
                        $image->move(public_path().'/media/images/invoices/'.$invoice->PK_NO.'/', $file_name);
                        $i++;
                    }

                    $invoice = Invoice::find($invoice->PK_NO);
                    if ($invoice->MASTER_INVOICE_RELATIVE_PATH == null) {
                        $invoice->MASTER_INVOICE_RELATIVE_PATH = $def_relative_path ?? null;
                        $invoice->update();
                    }
            }

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Unable to create invoice !', 'admin.invoice');
        }

        DB::commit();
        return $this->formatResponse(true, 'Invoice has been created successfully !', 'admin.invoice',$invoice->PK_NO);
    }

    public function postUpdate($request, $id)
    {
        $currency    = Currency::select('NAME', 'CODE')->where('PK_NO', '=', $request->currency)->first();
        $vendor      = Vendor::select('NAME', 'HAS_LOYALITY')->where('PK_NO', '=', $request->vendor)->first();
        $pay_source  = AccountSource::select('NAME')->where('PK_NO',$request->payment_source)->first();
        $acc_bank    = BankAccount::select('NAME')->where('PK_NO',$request->acc_bank)->first();
        $pay_method  = AccountMethod::select('NAME')->where('PK_NO',$request->payment_methods)->first();

        DB::beginTransaction();

        try {
            $invoice                        = Invoice::find($id);
            $invoice->INVOICE_NO            = $request->invoice_no;
            $invoice->F_PAYMENT_SOURCE_NO   = $request->payment_source;
            $invoice->PAYMENT_SOURCE_NAME   = $pay_source->NAME;
            $invoice->F_PAYMENT_ACC_NO      = $request->acc_bank;
            $invoice->PAYMENT_ACC_NAME      = $acc_bank->NAME;
            $invoice->F_PAYMENT_METHOD_NO   = $request->payment_methods;
            $invoice->PAYMENT_METHOD_NAME   = $pay_method->NAME;
            $invoice->INVOICE_DATE          = date('Y-m-d',strtotime($request->invoice_date));
            $invoice->F_VENDOR_NO           = $request->vendor;
            $invoice->VENDOR_NAME           = $vendor->NAME;
            $invoice->F_PURCHASER_USER_NO   = $request->purchaser;
            $invoice->F_SS_CURRENCY_NO      = $request->currency;
            $invoice->INVOICE_CURRENCY      = $currency->CODE;
            $invoice->DISCOUNT_PERCENTAGE   = $request->discount_percentage;
            $invoice->DISCOUNT_AMOUNT       = $request->discount_amount;
            $invoice->DISCOUNT2_PERCENTAGE  = $request->discount_percentage2;
            $invoice->DISCOUNT2_AMOUNT      = $request->discount_amount2;
           // $invoice->TOTAL_QTY             = $request->total_qty;
            //$invoice->RECIEVED_QTY          = $request->recieved_qty;
            $invoice->HAS_LOYALTY           = $vendor->HAS_LOYALITY;
            $invoice->HAS_VAT_REFUND        = $request->has_vat_refund;
            $invoice->GBP_TO_MR_RATE        = $request->gbp_to_mr;
            $invoice->GBP_TO_AC_RATE        = $request->gbp_to_ac;
            $invoice->INVOICE_EXACT_VALUE   = $request->invoice_exact_value;
            $invoice->INVOICE_EXACT_VAT     = $request->exact_vat;
            //$invoice->SETTLEMENT_AMT        = $request->settlement_amt;
            $invoice->F_PARENT_PRC_STOCK_IN = $request->parent_invoice;
            $invoice->INVOICE_EXACT_POSTAGE = $request->postage;
            //$invoice->INV_STOCK_RECORD_GENERATED  = 0;
            $invoice->DESCRIPTION           = $request->description;

            if($currency->CODE == 'GBP'){
                if(isset($request->gbp_to_mr) && $request->gbp_to_mr != ''){
                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_MR           = $request->gbp_to_mr*$request->total_vat;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_MR            = $request->gbp_to_mr*$request->total_accept_vat;
                    // $invoice->INVOICE_TOTAL_ACTUAL_MR               = $request->gbp_to_mr*$request->total_amount;
                    $invoice->INVOICE_POSTAGE_ACTUAL_MR             = $request->gbp_to_mr*$request->postage;

                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_GBP                 = $request->total_vat;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_GBP                  = $request->total_accept_vat;
                    // $invoice->INVOICE_TOTAL_ACTUAL_GBP                     = $request->total_amount;
                    $invoice->INVOICE_POSTAGE_ACTUAL_GBP                   = $request->postage;
                }

            } elseif ($currency->CODE == 'RM'){
                if(isset($request->gbp_to_mr) && $request->gbp_to_mr != ''){
                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_MR           = $request->total_vat;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_MR            = $request->total_accept_vat;
                    // $invoice->INVOICE_TOTAL_ACTUAL_MR               = $request->total_amount;
                    $invoice->INVOICE_POSTAGE_ACTUAL_MR             = $request->postage;

                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_GBP         = $request->total_vat/$request->gbp_to_mr;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_GBP          = $request->total_accept_vat/$request->gbp_to_mr;
                    // $invoice->INVOICE_TOTAL_ACTUAL_GBP             = $request->total_amount/$request->gbp_to_mr;
                    $invoice->INVOICE_POSTAGE_ACTUAL_GBP           = $request->postage/$request->gbp_to_mr;
                }
            } else {
                if(isset($request->gbp_to_ac) && $request->gbp_to_ac != ''){

                    // $total_vat_gbp          = $request->total_vat/$request->gbp_to_ac;
                    // $total_except_vat_gbp   = $request->total_accept_vat/$request->gbp_to_ac;
                    // $total_gbp              = $request->total_amount/$request->gbp_to_ac;
                    $total_postage          = $request->postage/$request->gbp_to_ac;

                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_GBP                 = $total_vat_gbp;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_GBP                  = $total_except_vat_gbp;
                    // $invoice->INVOICE_TOTAL_ACTUAL_GBP                     = $total_gbp;
                    $invoice->INVOICE_POSTAGE_ACTUAL_GBP                   = $total_postage;

                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_AC                  = $request->total_vat;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_AC                   = $request->total_accept_vat;
                    // $invoice->INVOICE_TOTAL_ACTUAL_AC                      = $request->total_amount;
                    $invoice->INVOICE_POSTAGE_ACTUAL_AC                    = $request->postage;

                    // $invoice->INVOICE_TOTAL_VAT_ACTUAL_MR           = $request->gbp_to_mr*$total_vat_gbp;
                    // $invoice->INVOICE_TOTAL_EV_ACTUAL_MR            = $request->gbp_to_mr*$total_except_vat_gbp;
                    // $invoice->INVOICE_TOTAL_ACTUAL_MR               = $request->gbp_to_mr*$total_gbp;
                    $invoice->INVOICE_POSTAGE_ACTUAL_MR             = $request->gbp_to_mr*$request->postage;
                }
            }

            $invoice->update();

            if ($request->file('images')) {
                $i = 0;

                foreach($request->file('images') as $key => $image)
                    {
                        $file_name = 'invoice_'. date('dmY'). '_' .uniqid(). '.' . $image->getClientOriginalExtension();

                        $img_lib                    = new InvoiceImgLib();
                        $img_lib->F_PRC_STOCK_IN_NO = $invoice->PK_NO;
                        $img_lib->F_FILE_TYPE_NO    = 1;
                        $img_lib->FILE_EXT          = $image->getClientOriginalExtension();
                        $img_lib->RELATIVE_PATH     = '/media/images/invoices/'.$invoice->PK_NO.'/'.$file_name;
                        $img_lib->SERIAL_NO         = $i;

                        $img_lib->save();

                        if($i == 0){
                            $def_relative_path      = '/media/images/invoices/'.$invoice->PK_NO.'/'.$file_name;
                        }

                        $image->move(public_path().'/media/images/invoices/'.$invoice->PK_NO.'/', $file_name);

                        $i++;
                    }

                $invoice = Invoice::find($invoice->PK_NO);

                if ($invoice->MASTER_INVOICE_RELATIVE_PATH == null) {
                    $invoice->MASTER_INVOICE_RELATIVE_PATH = $def_relative_path ?? null;
                    $invoice->update();
                }
            }

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return $this->formatResponse(false, 'Unable to update invoice !', 'admin.invoice');
        }

        DB::commit();

        return $this->formatResponse(true, 'Invoice has been updated successfully !', 'admin.invoice',$invoice->PK_NO);
    }


    public function findOrThrowException(int $id)
    {
        $data = $this->invoice->where('PK_NO', '=', $id)->first();
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.invoice.edit', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.invoice.list', null);
    }

    public function delete(int $id)
    {
        DB::begintransaction();

        try {

            $invoice = Invoice::find($id);

            if ($invoice->F_CHILD_PRC_STOCK_IN > 0) {
                return $this->formatResponse(false, 'Unable to delete invoice because it has a child invoice !', 'admin.invoice');
            }elseif($invoice->INV_STOCK_RECORD_GENERATED == 1){
                return $this->formatResponse(false, 'Unable to delete invoice because it has generated stock !', 'admin.invoice');
            }else{

                Invoice::where('PK_NO',$invoice->F_PARENT_PRC_STOCK_IN)->update(['F_CHILD_PRC_STOCK_IN' => NULL]);
                $invoice = Invoice::find($id)->delete();
            }


        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete invoice !', 'admin.invoice');
        }

        DB::commit();

        return $this->formatResponse(true, 'Successfully delete invoice!', 'admin.invoice');
    }

    public function deleteImage(int $id)
    {
        DB::begintransaction();
        try {
            $inv_img = InvoiceImgLib::find($id);
            if ($inv_img->SERIAL_NO == 0) {
                InvoiceImgLib::where('PK_NO', $id)->delete();
                $inv = Invoice::find($inv_img->F_PRC_STOCK_IN_NO);
                $inv_img = InvoiceImgLib::where('F_PRC_STOCK_IN_NO', $inv->PK_NO)->orderBy('SERIAL_NO','ASC')->first();
                if (!empty($inv_img)) {
                    $inv->MASTER_INVOICE_RELATIVE_PATH = $inv_img->RELATIVE_PATH;
                    $inv->update();
                    $inv_img->SERIAL_NO = 0;
                    $inv_img->update();
                }else{
                    $inv->MASTER_INVOICE_RELATIVE_PATH = null;
                    $inv->update();
                }
            }else{
                InvoiceImgLib::where('PK_NO', $id)->delete();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete invoice photo !', 'admin.invoice');
        }

        DB::commit();

        return $this->formatResponse(true, 'Successfully delete invoice photo !', 'admin.invoice');
    }


    public function invoiceQBentry($id)
    {
        DB::begintransaction();
        try {
            $invoice = $this->invoice->find($id);
            $invoice->IS_QUICK_BOOK_ENTERED = 1;
            $invoice->update();


        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to complete this action !', 'admin.invoice_processing');
        }

         DB::commit();

        return $this->formatResponse(true, 'Successfully quick books entry !', 'admin.invoice_processing');
    }

    public function invoiceLoyaltyClaime($id)
    {
        DB::begintransaction();
        try {
            $invoice = $this->invoice->find($id);
            $invoice->LOYALTY_CLAIMED = 1;
            $invoice->update();


        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to complete this action !', 'admin.invoice_processing');
        }

         DB::commit();

        return $this->formatResponse(true, 'Successfully loyalty claimed entry !', 'admin.invoice_processing');
    }


    public function invoiceVatClaime($id)
    {
        DB::begintransaction();
        try {
            $invoice = $this->invoice->find($id);
            $invoice->VAT_CLAIMED = 1;
            $invoice->update();


        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to complete this action !', 'admin.vat_processing');
        }

         DB::commit();

        return $this->formatResponse(true, 'Successfully vat claimed entry or QB entry !', 'admin.vat_processing');
    }

    public function postStoreInvoiceProcessing($request)
    {
        $invoice = Invoice::where(['PK_NO' => $request->invoice_pk_no, 'INV_STOCK_RECORD_GENERATED' => 1, 'IS_ACTIVE' => 1])->first();
        if ($invoice) {
            return $this->formatResponse(false, 'Stock already generated', 'admin.invoice_processing');
        }

        DB::begintransaction();
        try {
            $stock_gen                      = new StockGeneration();
            $stock_gen->F_PRC_STOCK_IN_NO   = $request->invoice_pk_no;
            $stock_gen->F_INV_WAREHOUSE_NO  = $request->warehouse;
            $stock_gen->save();
            $PK_NO = $stock_gen->PK_NO;

            DB::SELECT("UPDATE INV_STOCK_PRC_STOCK_IN_MAP SET PROCESS_COMPLETE_TIME = NOW() WHERE PK_NO = $PK_NO");


        } catch (\Exception $e) {

            DB::rollback();
dd($e);
            return $this->formatResponse(false, 'Unable to complete this action !', 'admin.invoice_processing');
        }

         DB::commit();

        return $this->formatResponse(true, 'Successfully invoice to stock generation !', 'admin.invoice_processing');

    }

    public function getDeleteGeneratedStock($invoice_id)
    {




        $invoice = Invoice::find($invoice_id);
        if ($invoice->INV_STOCK_RECORD_GENERATED == 0 ) {
            return $this->formatResponse(false, 'It has no stock !', 'admin.invoice_processing');
        }

        // $stock = Stock::whereNotNull('F_BOX_NO')->whereNotNull('F_ORDER_NO')->whereNotNull('F_BOOKING_NO')->whereNotNull('PRODUCT_STATUS')->where('F_PRC_STOCK_IN_NO', $invoice_id)->toSql();
        $stock = DB::SELECT("select * from INV_STOCK where (F_BOX_NO is not null or F_ORDER_NO is not null or F_BOOKING_NO is not null or PRODUCT_STATUS is not null ) and F_PRC_STOCK_IN_NO = '$invoice_id' ");



        if (!empty($stock)) {

             return $this->formatResponse(false, 'Not Possible to delete Stock !', 'admin.invoice_processing');
        }


        DB::begintransaction();

        try {

            DB::SELECT("DELETE from INV_STOCK where  F_PRC_STOCK_IN_NO = '$invoice_id' ");

            StockGeneration::where('F_PRC_STOCK_IN_NO', $invoice_id)->delete();
            Invoice::where('PK_NO', $invoice_id)->update(['INV_STOCK_RECORD_GENERATED' => 0 ]);




        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete this action !', 'admin.invoice_processing');
        }

        DB::commit();
        return $this->formatResponse(true, 'Successfully stock deleted !', 'admin.invoice_processing');


    }




}
