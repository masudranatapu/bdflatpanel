<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Traits\RepoResponse;
use Illuminate\Http\Request;
use App\Models\WarehouseZone;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Shelve\ShelveInterface;

class ShelveController extends BaseController
{
    use RepoResponse;

    private $shelve;
    private $stock;
    private $warehouse;
    private $warehoueszone;

    function __construct(ShelveInterface $shelve, Stock $stock, WarehouseZone $warehoueszone, Warehouse $warehouse)
    {
        $this->shelve        = $shelve;
        $this->stock         = $stock;
        $this->warehouse     = $warehouse;
        $this->warehoueszone = $warehoueszone;
    }

    public function getUnshelved(Request $request)
    {
        return view('admin.shelve.unshelved');
    }

    public function getUnshelvedItem($id)
    {
        $item = $this->shelve->getUnshelvedItem($id);
        return view('admin.shelve.unshelve_view')->withItems($item->data);
    }

    public function getShelvedItem($zone_id)
    {   $item = $this->shelve->getShelvedItem($zone_id);
        return view('admin.shelve.shelve_view')->withItems($item->data);
    }

    public function getStockPriceInfo($id)
    {
        $item = $this->shelve->getStockPriceInfo($id);
        return view('admin.shelve.stock_price_info')->withItems($item->data);
    }

    public function getShelveList()
    {
        return view('admin.shelve.shelved');
    }

    public function getShelveStore($id = null)
    {
        $warehouse = $this->warehouse->getWarehpuseCombo();
        $data = null;
        if ($id != null) {
            $data = $this->warehoueszone->where('PK_NO',$id)->first();
        }
        return view('admin.shelve.addShelve')->withWarehouse($warehouse)->withData($data);
    }

    public function getAllProduct()
    {
        /*
        $stock = DB::SELECT(" SELECT PK_NO, SKUID, IG_CODE, BARCODE, PRD_VARINAT_NAME, PRD_VARIANT_IMAGE_PATH, INV_WAREHOUSE_NAME, F_INV_WAREHOUSE_NO,F_SHIPPMENT_NO, F_BOX_NO, F_INV_ZONE_NO, PRODUCT_STATUS, BOOKING_STATUS FROM INV_STOCK");
        $dataSet = DB::SELECT("SELECT PK_NO, SKUID, IG_CODE AS IG_CODE_, BARCODE, PRD_VARINAT_NAME, PRD_VARIANT_IMAGE_PATH, INV_WAREHOUSE_NAME, F_INV_WAREHOUSE_NO AS WAREHOUSE_NO, IFNULL(COUNT(SKUID),0) AS COUNT
        -- ,(SELECT IFNULL(COUNT(IG_CODE),0) FROM INV_STOCK WHERE IG_CODE = IG_CODE_ AND F_INV_WAREHOUSE_NO = WAREHOUSE_NO AND F_SHIPPMENT_NO IS NULL AND F_BOX_NO IS NOT NULL) AS BOXED_QTY
        -- ,(SELECT IFNULL(COUNT(IG_CODE),0) FROM INV_STOCK WHERE IG_CODE = IG_CODE_ AND F_INV_WAREHOUSE_NO = WAREHOUSE_NO AND (F_INV_ZONE_NO IS NULL AND F_BOX_NO IS NOT NULL AND F_SHIPPMENT_NO IS NOT NULL AND PRODUCT_STATUS = 60)) AS NOT_SHELVED_QTY
        -- ,(SELECT IFNULL(COUNT(IG_CODE),0) FROM INV_STOCK WHERE IG_CODE = IG_CODE_ AND F_INV_WAREHOUSE_NO = WAREHOUSE_NO AND (F_BOX_NO IS NULL OR F_BOX_NO = 0) AND (PRODUCT_STATUS IS NULL OR PRODUCT_STATUS = 0 OR PRODUCT_STATUS = 90)) AS YET_TO_BOXED_QTY
        -- ,(SELECT IFNULL(COUNT(IG_CODE),0) FROM INV_STOCK WHERE IG_CODE = IG_CODE_ AND F_INV_WAREHOUSE_NO = WAREHOUSE_NO AND (F_INV_ZONE_NO IS NOT NULL)) AS SHELVED_QTY
        -- ,(SELECT IFNULL(COUNT(IG_CODE),0) FROM INV_STOCK WHERE IG_CODE = IG_CODE_ AND F_INV_WAREHOUSE_NO = WAREHOUSE_NO AND (F_SHIPPMENT_NO IS NOT NULL AND F_BOX_NO IS NOT NULL AND F_INV_ZONE_NO IS NULL)) AS SHIPMENT_ASSIGNED_QTY
        -- ,(SELECT IFNULL(COUNT(IG_CODE),0) FROM INV_STOCK WHERE IG_CODE = IG_CODE_ AND F_INV_WAREHOUSE_NO = WAREHOUSE_NO AND BOOKING_STATUS BETWEEN 10 AND 79) AS ORDERED
        FROM INV_STOCK GROUP BY SKUID, F_INV_WAREHOUSE_NO ORDER BY PK_NO DESC ");
        if(!empty($dataSet) && count($dataSet)> 0){
            foreach ($dataSet as $k => $value1) {
                $boxed_qty              = 0;
                $not_shelved_qty        = 0;
                $yet_to_boxed_qty       = 0;
                $shelved_qty            = 0;
                $shipment_assigned_qty  = 0;
                $ordered                = 0;
                if(!empty($stock)){
                    foreach ($stock as $l => $value2) {
                        if( ($value2->IG_CODE == $value1->IG_CODE_) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_SHIPPMENT_NO == null) && ($value2->F_BOX_NO != null) ){
                            $boxed_qty += 1;
                        }

                        if( ($value2->IG_CODE == $value1->IG_CODE_) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_INV_ZONE_NO == null) && ($value2->F_BOX_NO != null) && ($value2->F_SHIPPMENT_NO != null) && ($value2->PRODUCT_STATUS == 60) ){
                            $not_shelved_qty += 1;
                        }

                        if( ($value2->IG_CODE == $value1->IG_CODE_) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_BOX_NO == null || $value2->F_BOX_NO == 0) && ($value2->PRODUCT_STATUS == null || $value2->PRODUCT_STATUS == 0 || $value2->PRODUCT_STATUS == 90 ) ){
                            $yet_to_boxed_qty += 1;
                        }

                        if( ($value2->IG_CODE == $value1->IG_CODE_) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_INV_ZONE_NO != null) ){
                            $shelved_qty += 1;
                        }

                        if( ($value2->IG_CODE == $value1->IG_CODE_) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_SHIPPMENT_NO != null) && ($value2->F_BOX_NO != null) && ($value2->F_INV_ZONE_NO == null)){
                            $shipment_assigned_qty += 1;
                        }
                        if( ($value2->IG_CODE == $value1->IG_CODE_) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->BOOKING_STATUS >= 10) && ($value2->BOOKING_STATUS <= 80) ){
                            $ordered += 1;
                        }

                    }
                }


                $value1->BOXED_QTY = $boxed_qty ;
                $value1->NOT_SHELVED_QTY = $not_shelved_qty ;
                $value1->YET_TO_BOXED_QTY = $yet_to_boxed_qty ;
                $value1->SHELVED_QTY = $shelved_qty ;
                $value1->SHIPMENT_ASSIGNED_QTY = $shipment_assigned_qty ;
                $value1->ORDERED = $ordered ;
            }
        }

                dd($dataSet);
                */

        // $this->resp = $this->shelve->getAllProduct();
        return view('admin.shelve.product-list');
    }

    public function getProductModal(Request $request)
    {
        $this->resp = $this->shelve->getProductModal($request);
        return json_encode($this->resp);
    }

    public function getInvoiceProductModal(Request $request)
    {
        $this->resp = $this->shelve->getInvoiceProductModal($request);
        return json_encode($this->resp);
    }

    public function getWarehouseDropdown(Request $request)
    {
        $this->resp = $this->shelve->getWarehouseDropdown($request);
        return json_encode($this->resp);
    }

    public function postStore(Request $request)
    {
        $this->resp = $this->shelve->postStore($request);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
}

