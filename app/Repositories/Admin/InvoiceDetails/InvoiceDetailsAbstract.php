<?php
namespace App\Repositories\Admin\InvoiceDetails;

use DB;
use App\Models\Stock;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Currency;
use App\Models\VatClass;
use App\Traits\RepoResponse;
use App\Models\InvoiceDetails;
use App\Models\ProductVariant;

class InvoiceDetailsAbstract implements InvoiceDetailsInterface
{
    use RepoResponse;

    protected $innvoice_details;
    protected $product;
    protected $vat_class;

    public function __construct(InvoiceDetails $innvoice_details, Product $product, VatClass $vat_class)
    {
        $this->innvoice_details = $innvoice_details;
        $this->product          = $product;
        $this->vat_class        = $vat_class;
    }

    public function getPaginatedList($request, int $per_page = 20, $id)
    {
        $data = $this->innvoice_details->select('*')
            ->where('F_PRC_STOCK_IN', $id)
            ->where('IS_ACTIVE', 1)
            ->orderBy('PK_NO', 'ASC')
            ->get();

        return $this->formatResponse(true, '', 'admin.invoice-details', $data);
    }

    public function getProductBySubCategory($id){

        $products = Product::where('F_PRD_SUB_CATEGORY_ID', $id)->pluck('DEFAULT_NAME', 'PK_NO');
        $response = '';

        if (count($products) > 0) {
            $response .= '<option value=""> -- Please select product -- </option>';
            foreach ($products as $key => $value) {
                $response .= '<option value="'.$key.'">'. $value.'</option>';
            }
        }else{
            $response .= '<option value=""> Product not found </option>';
        }

        return $response;
    }

    public function getInvoiceData($id){
        $data = Invoice::where('PK_NO', $id)->first();
        return $this->formatResponse(true, '', 'admin.invoice-details.new', $data);
    }

    public function getVariantListById($data){
        $variants = ProductVariant::whereIn('PK_NO', $data)->get();
        $remove_item = 'PK_NO';
        return $this->generateVariantResponse($variants, $remove_item);
    }

    public function getVariantListByBarCode($bar_code){

        $bar_code = explode(",",$bar_code);
        $variants = ProductVariant::whereIn('BARCODE', $bar_code)->get();
        $remove_item = 'BARCODE';
        return $this->generateVariantResponse($variants, $remove_item);
    }

    private function generateVariantResponse($variants, $remove_item){
        $response = '';
        if (count($variants) > 0) {
            foreach ($variants as $item) {
                $item->remove_item = $remove_item;
                //$vat_class = $this->getVatClassCombo();
                $response .= $this->generateInputField($item);
            }
        }
        return $response;
    }

    private function getVatClassCombo(){
        return VatClass::pluck('NAME', 'RATE');
    }

    private function generateInputField($item){
        return view('admin.procurement.invoice-details._variant_tr')->withItem($item)->render();
    }

    public function postStore($request)
    {
        $invoice = Invoice::where('PK_NO', $request->invoice_id)->where('INV_STOCK_RECORD_GENERATED', 0)->first();
        if(empty($invoice)){
            return $this->formatResponse(false, 'Unable to add new product in this invoice !', 'admin.invoice');
        }

        $currency = Currency::find($invoice->F_SS_CURRENCY_NO);
        $variants = $request->variant_id;
        if($variants == null ){
            return $this->formatResponse(false, 'Item not found in this invoice !', 'admin.invoice');
        }

        if(count(array_unique($request->variant_id))<count($request->variant_id)){
            return $this->formatResponse(false, 'Unable to add new product in this invoice, it has may duplicate item !', 'admin.invoice');
        }

        DB::beginTransaction();
            try {
                if(!empty($variants)){
                foreach ($variants as $key => $value) {
                    $variant_id = $value;

                    $variant = ProductVariant::find($variant_id);
                    $variant->VARIANT_CUSTOMS_NAME = $request->invoice_name[$key];
                    $variant->update();

                    $inv = new InvoiceDetails();
                    $inv->F_PRC_STOCK_IN    = $invoice->PK_NO;
                    $inv->F_PRD_VARIANT_NO  = $variant_id;
                    $inv->PRD_VARIANT_NAME  = $request->variant_name[$key];
                    $inv->INVOICE_NAME      = $request->invoice_name[$key];
                    $inv->BAR_CODE          = $request->barcode[$key];
                    $inv->HS_CODE           = $request->hs_code[$key];
                    $inv->QTY               = $request->total_line_qty[$key] ?? 0;
                    $inv->RECIEVED_QTY      = $request->recieved_qty[$key] ?? 0;
                    $inv->FAULTY_QTY        = $request->faulty_qty[$key] ?? 0;
                    $inv->CURRENCY          = $currency->NAME;
                    $inv->VAT_RATE          = $request->vat_class_rate[$key];

                    $gbp_to_mr_rate         = $invoice->GBP_TO_MR_RATE;
                    $gbp_to_ac_rate         = $invoice->GBP_TO_AC_RATE;

                    if($currency->CODE == 'GBP')
                    {
                        $inv->UNIT_PRICE_GBP_EV         = $request->unit_price_ev2[$key];
                        $inv->UNIT_VAT_GBP              = $request->unit_vat2[$key];
                        $inv->LINE_TOTAL_VAT_GBP        = $request->line_vat_actual2[$key];
                        $inv->SUB_TOTAL_GBP_RECEIPT     = $request->line_total[$key];
                        $inv->SUB_TOTAL_GBP_EV          = $request->line_total_exvat_actual2[$key];
                        $inv->REC_TOTAL_GBP_WITH_VAT    = ($request->unit_price_ev2[$key]+$request->unit_vat2[$key])*$request->recieved_qty[$key];
                        $inv->REC_TOTAL_GBP_ONLY_VAT     = $request->unit_vat2[$key]*$request->recieved_qty[$key];

                        $inv->UNIT_PRICE_MR_EV          = $request->unit_price_ev2[$key]*$gbp_to_mr_rate;
                        $inv->UNIT_VAT_MR               = $request->unit_vat2[$key]*$gbp_to_mr_rate;
                        $inv->LINE_TOTAL_VAT_MR         = $request->line_vat_actual2[$key]*$gbp_to_mr_rate;
                        $inv->SUB_TOTAL_MR_RECEIPT      = $request->line_total[$key]*$gbp_to_mr_rate;
                        $inv->SUB_TOTAL_MR_EV           = $request->line_total_exvat_actual2[$key]*$gbp_to_mr_rate;
                        $inv->REC_TOTAL_MR_WITH_VAT     = (($request->unit_price_ev2[$key]+$request->unit_vat2[$key])*$request->recieved_qty[$key])*$gbp_to_mr_rate;
                        $inv->REC_TOTAL_MR_ONLY_VAT     = ($request->unit_vat2[$key]*$request->recieved_qty[$key])*$gbp_to_mr_rate;

                    } elseif ($currency->CODE == 'RM') {

                        $inv->UNIT_PRICE_GBP_EV         = $request->unit_price_ev2[$key]/$gbp_to_mr_rate;
                        $inv->UNIT_VAT_GBP              = $request->unit_vat2[$key]/$gbp_to_mr_rate;
                        $inv->LINE_TOTAL_VAT_GBP        = $request->line_vat_actual2[$key]/$gbp_to_mr_rate;
                        $inv->SUB_TOTAL_GBP_RECEIPT     = $request->line_total[$key]/$gbp_to_mr_rate;
                        $inv->SUB_TOTAL_GBP_EV          = $request->line_total_exvat_actual2[$key]/$gbp_to_mr_rate;
                        $inv->REC_TOTAL_GBP_WITH_VAT    = (($request->unit_price_ev2[$key]+$request->unit_vat2[$key])*$request->recieved_qty[$key])/$gbp_to_mr_rate;
                        $inv->REC_TOTAL_GBP_ONLY_VAT     = ($request->unit_vat2[$key]*$request->recieved_qty[$key])/$gbp_to_mr_rate;



                        $inv->UNIT_PRICE_MR_EV          = $request->unit_price_ev2[$key];
                        $inv->UNIT_VAT_MR               = $request->unit_vat2[$key];
                        $inv->LINE_TOTAL_VAT_MR         = $request->line_vat_actual2[$key];
                        $inv->SUB_TOTAL_MR_RECEIPT      = $request->line_total[$key];
                        $inv->SUB_TOTAL_MR_EV           = $request->line_total_exvat_actual2[$key];
                        $inv->REC_TOTAL_MR_WITH_VAT     = (($request->unit_price_ev2[$key]+$request->unit_vat2[$key])*$request->recieved_qty[$key]);
                        $inv->REC_TOTAL_MR_ONLY_VAT     = ($request->unit_vat2[$key]*$request->recieved_qty[$key]);

                    } else {

                        $inv->UNIT_PRICE_AC_EV          = $request->unit_price_ev2[$key];
                        $inv->UNIT_VAT_AC               = $request->unit_vat2[$key];
                        $inv->LINE_TOTAL_VAT_AC         = $request->line_vat_actual2[$key];
                        $inv->SUB_TOTAL_AC_RECEIPT      = $request->line_total[$key];
                        $inv->SUB_TOTAL_AC_EV           = $request->line_total_exvat_actual2[$key];
                        $inv->REC_TOTAL_AC_WITH_VAT     = ($request->unit_price_ev2[$key]+$request->unit_vat2[$key])*$request->recieved_qty[$key];
                        $inv->REC_TOTAL_AC_ONLY_VAT     = $request->unit_vat2[$key]*$request->recieved_qty[$key];

                        $unit_price_gbp_ev              = $request->unit_price_ev2[$key]/$gbp_to_ac_rate;
                        $unit_vat_gbp                   = $request->unit_vat2[$key]/$gbp_to_ac_rate;
                        $unit_total_vat_gbp             = $request->line_vat_actual2[$key]/$gbp_to_ac_rate;
                        $sub_total_gbp                  = $request->line_total[$key]/$gbp_to_ac_rate;
                        $sub_total_gbp_actual           = $request->line_total_exvat_actual2[$key]/$gbp_to_ac_rate;
                        $rec_total_gbp_with_vat         = (($request->unit_price_ev2[$key]+$request->unit_vat2[$key])*$request->recieved_qty[$key])/$gbp_to_ac_rate;
                        $rec_total_gbp_only_vat         = ($request->unit_vat2[$key]*$request->recieved_qty[$key])/$gbp_to_ac_rate;

                        $inv->UNIT_PRICE_GBP_EV         = $unit_price_gbp_ev;
                        $inv->UNIT_VAT_GBP              = $unit_vat_gbp;
                        $inv->LINE_TOTAL_VAT_GBP        = $unit_total_vat_gbp;
                        $inv->SUB_TOTAL_GBP_RECEIPT     = $sub_total_gbp;
                        $inv->SUB_TOTAL_GBP_EV          = $sub_total_gbp_actual;
                        $inv->REC_TOTAL_GBP_WITH_VAT    = $rec_total_gbp_with_vat;
                        $inv->REC_TOTAL_GBP_ONLY_VAT    = $rec_total_gbp_only_vat;

                        $inv->UNIT_PRICE_MR_EV          = $request->unit_price_ev2[$key]*$gbp_to_mr_rate/$gbp_to_ac_rate;
                        $inv->UNIT_VAT_MR               = $request->unit_vat2[$key]*$gbp_to_mr_rate/$gbp_to_ac_rate;
                        $inv->LINE_TOTAL_VAT_MR         = $request->line_vat_actual2[$key]*$gbp_to_mr_rate/$gbp_to_ac_rate;
                        $inv->SUB_TOTAL_MR_RECEIPT      = $request->line_total[$key]*$gbp_to_mr_rate/$gbp_to_ac_rate;
                        $inv->SUB_TOTAL_MR_EV           = $request->line_total_exvat_actual2[$key]*$gbp_to_mr_rate/$gbp_to_ac_rate;
                        $inv->REC_TOTAL_MR_WITH_VAT     = (($request->unit_price_ev2[$key]+$request->unit_vat2[$key])*$request->recieved_qty[$key])*$gbp_to_mr_rate/$gbp_to_ac_rate;
                        $inv->REC_TOTAL_MR_ONLY_VAT     = ($request->unit_vat2[$key]*$request->recieved_qty[$key])*$gbp_to_mr_rate/$gbp_to_ac_rate;


                    }

                    $inv->save();
                }
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to create invoice !', 'admin.invoice');
        }

        DB::commit();
        return $this->formatResponse(true, 'Invoice has been created successfully !', 'admin.invoice');
    }

    public function getVariantListByQueryString($request, $queryString){
        $data = ProductVariant::select('VARIANT_NAME', 'COMPOSITE_CODE', 'VARIANT_CUSTOMS_NAME', 'SIZE_NAME', 'REGULAR_PRICE', 'COLOR')
            ->where('VARIANT_NAME', 'like', '%'.$queryString.'%')
            ->orderBy('PK_NO')
            ->limit(10)
            ->get();

        return $this->formatResponse(true, '', 'Data found', $data);
    }

    // private function genarateRow($data)
    // {
    //     foreach ($data as $key => $value) {
    //         return '<tr> <td>'.$key.'</td></tr>';
    //     }
    // }
/*
    public function findOrThrowException($id)
    {
        $data = $this->vendor->where('PK_NO', '=', $id)->first();

        if (!empty($data)) {
            return $this->formatResponse(true, '', 'admin.vendor.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.vendor', null);
    }

    public function postUpdate($request, $id)
    {
        $country = DB::table('SS_COUNTRY')->select('NAME')->where('PK_NO', '=', $request->country)->first();

        DB::beginTransaction();

        try {

            $vendor = $this->vendor->where('PK_NO', $id)->first();

            $vendor->where('PK_NO', $id)->update(
                [
                    'CODE'          => (!empty($request['code']) ?  $request['code'] : $id),
                    'NAME'          => $request['name'],
                    'ADDRESS'       => $request['address'],
                    'F_COUNTRY'     => $request['country'],
                    'COUNTRY'       => $country->NAME,
                    'PHONE'         => $request['phone'],
                    'HAS_LOYALITY'  => $request['has_loyality'],
                ]
            );

        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to update vendor !', 'admin.vendor');
        }

        DB::commit();

        return $this->formatResponse(true, 'Vendor has been updated successfully !', 'admin.vendor');
    }
    */

    public function delete($id)
    {
        $innvoice_details   = InvoiceDetails::find($id);
        $invoice            = $innvoice_details->invoice->INV_STOCK_RECORD_GENERATED;
        if ($invoice == 1) {
            return $this->formatResponse(false, 'Unable to delete this action because stock already generated !', 'admin.invoice');
        }

        DB::begintransaction();
        try {
            InvoiceDetails::where('PK_NO', $id)->delete();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete this action !', 'admin.invoice');
        }
        DB::commit();
        return $this->formatResponse(true, 'Successfully delete this action !', 'admin.invoice');
    }

    public function getProductByInvoice($id,$type)
    {
        // $data = Stock::select('INV_ZONE_BARCODE','PRD_VARINAT_NAME','SKUID','IG_CODE','F_PRC_STOCK_IN_DETAILS_NO as inv_details',DB::raw('(select IFNULL(COUNT(F_PRC_STOCK_IN_DETAILS_NO),0)) as product_count')
        // ,DB::raw('GROUP_CONCAT(SHIPMENT_NAME) as SHIPMENT_NAME')
        // ,DB::raw('(SELECT GROUP_CONCAT(F_BOX_NO) from INV_STOCK where F_PRC_STOCK_IN_NO = '.$id.' and F_INV_WAREHOUSE_NO = 1 and (F_SHIPPMENT_NO IS NULL or F_SHIPPMENT_NO = 0)) as F_BOX_NO')
        // ,DB::raw('(SELECT IFNULL(COUNT(F_PRC_STOCK_IN_DETAILS_NO),0) from INV_STOCK where F_PRC_STOCK_IN_DETAILS_NO = inv_details and F_INV_WAREHOUSE_NO = 1 and F_PRC_STOCK_IN_NO = '.$id.' and (F_BOX_NO IS NULL OR F_BOX_NO = 0)) as yet_to_box')
        // )
        // ->where('F_PRC_STOCK_IN_NO',$id)
        // // ->where('F_INV_WAREHOUSE_NO',1)
        // ->groupBy('F_PRC_STOCK_IN_DETAILS_NO')
        // ->get();
        if ($type == 'stock-processing') {
        $stock = DB::SELECT(" SELECT PK_NO, SKUID, IG_CODE, BARCODE, PRD_VARINAT_NAME, PRD_VARIANT_IMAGE_PATH, INV_WAREHOUSE_NAME, F_INV_WAREHOUSE_NO,F_SHIPPMENT_NO, F_BOX_NO, F_INV_ZONE_NO, PRODUCT_STATUS, BOOKING_STATUS, ORDER_STATUS FROM INV_STOCK WHERE F_PRC_STOCK_IN_NO = $id");

        $dataSet = DB::SELECT("SELECT PK_NO, SKUID, IG_CODE, BARCODE, PRD_VARINAT_NAME, PRD_VARIANT_IMAGE_PATH, INV_WAREHOUSE_NAME, F_INV_WAREHOUSE_NO AS WAREHOUSE_NO

        FROM INV_STOCK
        WHERE F_PRC_STOCK_IN_NO = $id
        GROUP BY SKUID, F_INV_WAREHOUSE_NO ORDER BY PK_NO DESC ");

        if(!empty($dataSet) && count($dataSet)> 0){
            foreach ($dataSet as $k => $value1) {
                $boxed_qty              = 0;
                $not_shelved_qty        = 0;
                $yet_to_boxed_qty       = 0;
                $shelved_qty            = 0;
                $shipment_assigned_qty  = 0;
                $ordered                = 0;
                $dispatched             = 0;
                $available              = 0;
                if(!empty($stock)){
                    foreach ($stock as $l => $value2) {
                        if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->BOOKING_STATUS >= 10) && ($value2->BOOKING_STATUS <= 80) && ($value2->ORDER_STATUS < 80 OR $value2->ORDER_STATUS == null)){
                            $ordered += 1;
                        }

                        if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO )){
                            $available += 1;
                        }

                        if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_SHIPPMENT_NO == null) && ($value2->F_BOX_NO != null) && ($value2->ORDER_STATUS < 80 OR $value2->ORDER_STATUS == null)){
                            $boxed_qty += 1;
                        }

                        if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_BOX_NO == null || $value2->F_BOX_NO == 0) && ($value2->PRODUCT_STATUS == null || $value2->PRODUCT_STATUS == 0 || $value2->PRODUCT_STATUS == 90 ) && ($value2->ORDER_STATUS < 80 OR $value2->ORDER_STATUS == null)){
                            $yet_to_boxed_qty += 1;
                        }

                        if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_SHIPPMENT_NO != null) && ($value2->F_BOX_NO != null) && ($value2->F_INV_ZONE_NO == null) && ($value2->ORDER_STATUS < 80 OR $value2->ORDER_STATUS == null)){
                            $shipment_assigned_qty += 1;
                        }

                        if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_INV_ZONE_NO != null) && ($value2->ORDER_STATUS < 80 OR $value2->ORDER_STATUS == null)){
                            $shelved_qty += 1;
                        }

                        if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_INV_ZONE_NO == null) && ($value2->ORDER_STATUS < 80 OR $value2->ORDER_STATUS == null) && ($value2->PRODUCT_STATUS == 60)){
                            $not_shelved_qty += 1;
                        }

                        if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->ORDER_STATUS >= 80)){
                            $dispatched += 1;
                        }
                    }
                }
                $value1->BOXED_QTY              = $boxed_qty ;
                $value1->NOT_SHELVED_QTY        = $not_shelved_qty ;
                $value1->YET_TO_BOXED_QTY       = $yet_to_boxed_qty ;
                $value1->SHELVED_QTY            = $shelved_qty ;
                $value1->SHIPMENT_ASSIGNED_QTY  = $shipment_assigned_qty ;
                $value1->ORDERED                = $ordered ;
                $value1->DISPATCHED             = $dispatched ;
                $value1->COUNTER                = $available ;
            }
        }
        }else{
            $stock = DB::SELECT(" SELECT PK_NO, SKUID, IG_CODE, BARCODE, PRD_VARINAT_NAME, PRD_VARIANT_IMAGE_PATH, INV_WAREHOUSE_NAME, F_INV_WAREHOUSE_NO,F_SHIPPMENT_NO, F_BOX_NO, F_INV_ZONE_NO, PRODUCT_STATUS, BOOKING_STATUS, ORDER_STATUS FROM INV_STOCK WHERE F_PRC_STOCK_IN_NO = $id");

            $dataSet = DB::SELECT("SELECT PK_NO, SKUID, IG_CODE, BARCODE, PRD_VARINAT_NAME, PRD_VARIANT_IMAGE_PATH, INV_WAREHOUSE_NAME, F_INV_WAREHOUSE_NO AS WAREHOUSE_NO

            FROM INV_STOCK
            WHERE F_PRC_STOCK_IN_NO = $id
            GROUP BY SKUID ORDER BY PK_NO DESC ");
            if(!empty($dataSet) && count($dataSet)> 0){
                foreach ($dataSet as $k => $value1) {
                    $boxed_qty              = 0;
                    $not_shelved_qty        = 0;
                    $yet_to_boxed_qty       = 0;
                    $shelved_qty            = 0;
                    $shipment_assigned_qty  = 0;
                    $ordered                = 0;
                    $dispatched             = 0;
                    $available              = 0;
                    if(!empty($stock)){
                        foreach ($stock as $l => $value2) {
                            if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->BOOKING_STATUS >= 10) && ($value2->BOOKING_STATUS <= 80)){
                                $ordered += 1;
                            }

                            if( ($value2->IG_CODE == $value1->IG_CODE)){
                                $available += 1;
                            }

                            if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_SHIPPMENT_NO == null) && ($value2->F_BOX_NO != null)){
                                $boxed_qty += 1;
                            }

                            if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_BOX_NO == null || $value2->F_BOX_NO == 0) && ($value2->PRODUCT_STATUS == null || $value2->PRODUCT_STATUS == 0 || $value2->PRODUCT_STATUS == 90 )){
                                $yet_to_boxed_qty += 1;
                            }

                            if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_SHIPPMENT_NO != null)){
                                $shipment_assigned_qty += 1;
                            }

                            if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_ZONE_NO != null)){
                                $shelved_qty += 1;
                            }

                            if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_ZONE_NO == null) && ($value2->F_BOX_NO != null) && ($value2->F_SHIPPMENT_NO != null)){
                                $not_shelved_qty += 1;
                            }

                            if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->ORDER_STATUS >= 80)){
                                $dispatched += 1;
                            }
                        }
                    }
                    $value1->BOXED_QTY              = $boxed_qty ;
                    $value1->NOT_SHELVED_QTY        = $not_shelved_qty ;
                    $value1->YET_TO_BOXED_QTY       = $yet_to_boxed_qty ;
                    $value1->SHELVED_QTY            = $shelved_qty ;
                    $value1->SHIPMENT_ASSIGNED_QTY  = $shipment_assigned_qty ;
                    $value1->ORDERED                = $ordered ;
                    $value1->DISPATCHED             = $dispatched ;
                    $value1->COUNTER                = $available ;
                }
            }
        }
        if (!empty($dataSet) && count($dataSet) > 0) {
            return $this->formatResponse(true, 'Data found !', 'admin.procurement.invoice-details.details',$dataSet);
        }
        return $this->formatResponse(false, 'Please try again !', 'admin.procurement.invoice-details.details');
    }
}
