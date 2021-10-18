<?php

namespace App\Models;

use App\Models\Box;
use App\Models\InvoiceDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'INV_STOCK';

    public $timestamps 		= false;
    protected $primaryKey 	= 'PK_NO';
    protected $fillable 	= ['PK_NO'];
    const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';

    // public function count_not_boxed($sku_id,$house)
    // {
    //     $data = Stock::where('SKUID',$sku_id)->where('INV_WAREHOUSE_NAME', $house)->whereRaw('(F_BOX_NO IS NULL OR F_BOX_NO = 0 )')->count();
    //     return $data;
    // }

    // public function count_boxed($sku_id,$house)
    // {
    //     $data = Stock::where('SKUID',$sku_id)->where('INV_WAREHOUSE_NAME', $house)->whereRaw('(F_BOX_NO IS NOT NULL)')->count();
    //     return $data;
    // }

    // public function count_not_shipped($sku_id,$house)
    // {
    //     $data = Stock::where('SKUID',$sku_id)->where('INV_WAREHOUSE_NAME', $house)->whereRaw('(F_SHIPPMENT_NO IS NULL OR F_SHIPPMENT_NO = 0 )')->count();
    //     return $data;
    // }

    // public function count_shipped($sku_id,$house)
    // {
    //     $data = Stock::where('SKUID',$sku_id)->where('INV_WAREHOUSE_NAME', $house)->whereRaw('(F_SHIPPMENT_NO IS NOT NULL)')->count();
    //     return $data;
    // }

    // public function count_not_shelved($sku_id,$house)
    // {
    //     $data = Stock::where('SKUID',$sku_id)->where('INV_WAREHOUSE_NAME', $house)->whereRaw('(F_INV_ZONE_NO IS NULL and F_BOX_NO IS NOT NULL and F_SHIPPMENT_NO IS NOT NULL and PRODUCT_STATUS >= 60) OR F_INV_ZONE_NO = 0')->count();
    //     return $data;
    // }

    // public function count_shelved($sku_id,$house)
    // {
    //     $data = Stock::where('SKUID',$sku_id)->where('INV_WAREHOUSE_NAME', $house)->whereRaw('(F_INV_ZONE_NO IS NOT NULL)')->count();
    //     return $data;
    // }

    public function productVariant() {
        return $this->belongsTo('App\Models\ProductVariant', 'F_PRD_VARIANT_NO', 'PK_NO');
    }

    public function warehouse() {
        return $this->belongsTo('App\Models\Warehouse', 'F_INV_WAREHOUSE_NO', 'PK_NO');
    }


    public function image() {
        return $this->hasOne('App\Models\ProductVariant', 'MRK_ID_COMPOSITE_CODE', 'IG_CODE');
    }

    public function shippment() {
        return $this->belongsTo('App\Models\Shipment', 'F_SHIPPMENT_NO', 'PK_NO');
    }

    public function box_list_per_ship($id,$inv)
    {
        $box_label = Box::selectRaw('(SELECT BOX_NO FROM SC_BOX WHERE PK_NO = F_BOX_NO)')->limit(1)->getQuery();

        $data = Stock::select('F_BOX_NO',DB::raw('(select IFNULL(COUNT(F_BOX_NO),0)) as boxed'))
        ->selectSub($box_label, 'box_label')
        ->where('SHIPMENT_NAME',$id)
        ->where('F_PRC_STOCK_IN_DETAILS_NO',$inv)
        ->where('F_INV_WAREHOUSE_NO',1)
        ->groupBy('F_BOX_NO')
        ->get();
        return json_decode($data);
    }

    public function box_list_($id,$inv)
    {
        $box_label = Box::selectRaw('(SELECT BOX_NO FROM SC_BOX WHERE PK_NO = F_BOX_NO)')->limit(1)->getQuery();

        $data = Stock::select('F_BOX_NO',DB::raw('(select IFNULL(COUNT(F_BOX_NO),0)) as boxed'))
        ->selectSub($box_label, 'box_label')
        ->where('F_BOX_NO',$id)
        ->where('F_PRC_STOCK_IN_DETAILS_NO',$inv)
        ->where('F_INV_WAREHOUSE_NO',1)
        ->groupBy('F_BOX_NO')
        ->first();

        return json_decode($data);
    }

    public function get_box_details($shipment_no,$skuid,$warehouse=null,$invoiceid,$invoicetype)
    {
        $box_label = Shipmentbox::selectRaw('(SELECT BOX_SERIAL FROM SC_SHIPMENT_BOX WHERE F_BOX_NO = f_box_no1)')->limit(1)->getQuery();

        $data = Stock::select('F_BOX_NO as f_box_no1','BOX_BARCODE'
        ,DB::raw('(select IFNULL(COUNT(F_BOX_NO),0)) as boxed'))
        ->selectSub($box_label, 'box_serial')
        ->where('F_SHIPPMENT_NO',$shipment_no)
        ->where('SKUID',$skuid);
        // if (isset($warehouse) && $warehouse != '' && isset($invoice_type) && $invoice_type != 'vat-processing') {
        // }
        if (isset($invoicetype) && $invoicetype == 'vat-processing') {
            $data = $data->where('F_INV_WAREHOUSE_NO','>',0);
        }else{
            $data = $data->where('F_INV_WAREHOUSE_NO',$warehouse);
        }
        if (isset($invoiceid) && $invoiceid != '') {
            $data = $data->where('F_PRC_STOCK_IN_NO',$invoiceid);
        }
        $data = $data->groupBy('F_BOX_NO')
        ->orderBy('box_serial','ASC')
        ->get();

        return $data;
    }

    public function getPrcStockInDetails($invoice_id,$sku_id)
    {
        $data['info'] = Stock::join('PRC_STOCK_IN','PRC_STOCK_IN.PK_NO','INV_STOCK.F_PRC_STOCK_IN_NO')
                                ->select('PRC_STOCK_IN.MASTER_INVOICE_RELATIVE_PATH','PRC_STOCK_IN.INVOICE_NO','PRC_STOCK_IN.INVOICE_DATE','INV_STOCK.PRODUCT_PURCHASE_PRICE')
                                ->where('PRC_STOCK_IN.PK_NO',$invoice_id)
                                ->where('INV_STOCK.SKUID',$sku_id)
                                ->groupBy('PRC_STOCK_IN.PK_NO')
                                // ->orderBy('PRC_STOCK_IN.INVOICE_DATE','ASC')
                                ->first();
        $data['total'] = Stock::where('F_PRC_STOCK_IN_NO',$invoice_id)->where('SKUID',$sku_id)->count();
        $data['dispatched'] = Stock::where('F_PRC_STOCK_IN_NO',$invoice_id)->where('SKUID',$sku_id)->where('ORDER_STATUS','80')->count();
        $data['vat'] = ProductVariant::select('VAT_AMOUNT_PERCENT')->where('COMPOSITE_CODE',$sku_id)->first();
        return $data;
        // return $this->hasOne('App\Models\Invoice', 'PK_NO', 'F_PRC_STOCK_IN_NO');
    }
}
