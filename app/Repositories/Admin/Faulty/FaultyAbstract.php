<?php
namespace App\Repositories\Admin\Faulty;

use DB;
use Carbon\Carbon;
use App\Models\Box;
use App\Models\Agent;
use App\Models\Stock;
use App\Models\Faulty;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\Shipmentbox;
use App\Traits\RepoResponse;
use App\Models\BookingDetails;
use App\Models\ProductVariant;
use App\Models\CustomerAddress;
use App\Models\CustomerAddressType;

class FaultyAbstract implements FaultyInterface
{
    use RepoResponse;

    protected $inv_stock;
    protected $box;

    public function __construct(Stock $inv_stock,Box $box)
    {
        $this->inv_stock = $inv_stock;
        $this->box       = $box;
    }

    public function getProductINV($ig_code,$type,$id)
    {
        // $regular_price = Stock::selectRaw('(SELECT IFNULL(AVG(REGULAR_PRICE),0) from INV_STOCK where IG_CODE = '. '"' .$ig_code. '"' .')')->limit(1)->getQuery();
        // $install_price = Stock::selectRaw('(SELECT IFNULL(AVG(INSTALLMENT_PRICE),0) from INV_STOCK where IG_CODE = '. '"' .$ig_code. '"' .')')->limit(1)->getQuery();
        // $prodct_image = Stock::selectRaw('(SELECT PRIMARY_IMG_RELATIVE_PATH from PRD_VARIANT_SETUP where MRK_ID_COMPOSITE_CODE = '. '"' .$ig_code. '"' .')')->limit(1)->getQuery();

        $info = DB::table('INV_STOCK as v')->select('v.PK_NO','v.BOX_TYPE','v.F_BOX_NO','v.PRD_VARINAT_NAME','v.IG_CODE','v.INV_WAREHOUSE_NAME','v.F_INV_WAREHOUSE_NO','v.SKUID','v.SHIPMENT_TYPE','v.PREFERRED_SHIPPING_METHOD','v.CUSTOMER_PREFFERED_SHIPPING_METHOD','s.SCH_ARRIVAL_DATE','v.FINAL_PREFFERED_SHIPPING_METHOD','v.PRODUCT_STATUS','v.BOOKING_STATUS','v.SS_COST','v.SM_COST','v.AIR_FREIGHT_COST','v.SEA_FREIGHT_COST','v.ORDER_STATUS','v.PRC_IN_IMAGE_PATH','v.F_BOOKING_NO','v.REGULAR_PRICE','v.INSTALLMENT_PRICE','v.PRD_VARIANT_IMAGE_PATH','v.F_ORDER_NO'
        );
        // $info = $info->selectSub($regular_price, 'REGULAR_PRICE');
        // $info = $info->selectSub($install_price, 'INSTALLMENT_PRICE');
        // $info = $info->selectSub($prodct_image, 'PRIMARY_IMG_RELATIVE_PATH');
        $info = $info->leftJoin('SC_SHIPMENT as s','s.PK_NO','v.F_SHIPPMENT_NO');
        $info = $info->where('v.IG_CODE', $ig_code);
        if ($type == 'box') {
            $info = $info->where('v.F_BOX_NO', $id);
        }else if($type == 'product'){
            $info = $info->where('v.F_PRD_VARIANT_NO', $id);
        }
        $info = $info->orderBy('v.F_BOX_NO','ASC');
        $info = $info->get();
        $data['info'] = $info;

        if (!empty($data['info'])) {
            // echo '<pre>';
            // echo '======================<br>';
            // print_r($data['info']);
            // echo '<br>======================<br>';
            // exit();
            return $this->generateInputField($data);
        }

        return $data;
    }

    private function generateInputField($item){
        return view('admin.faulty.faulty_variant_tr')->withItem($item)->render();
    }

    public function findOrThrowException($type,$id)
    {
        $product_details = $this->inv_stock->select('IG_CODE'
        );
        if ($type == 'box') {
            $product_details = $product_details->where('F_BOX_NO',$id);
        }else if ($type == 'product') {
            $product_details = $product_details->where('F_PRD_VARIANT_NO',$id);
        }
        $product_details = $product_details->groupBy('IG_CODE');
        $product_details = $product_details->get();


        if ($product_details && count($product_details) > 0 ) {
            foreach ($product_details as $key => $value) {
                $value->product_info = $this->getProductINV($value->IG_CODE,$type,$id);
            }
        }

        return $this->formatResponse(true, 'Data found successfully !', 'admin.booking.list', $product_details);
    }

    public function ajaxFaultyChecker($id)
    {
        DB::beginTransaction();
        try {
            $box = Stock::select('F_BOX_NO')->where('PK_NO',$id)->first();

            if (!empty($box)) {

                $shipment_no = Shipmentbox::select('F_SHIPMENT_NO')->where('F_BOX_NO', $box->F_BOX_NO)->first();
                if (!empty($shipment_no)) {
                    $shipment_status = Shipment::select('PK_NO','SHIPMENT_STATUS','F_TO_INV_WAREHOUSE_NO')->where('PK_NO', $shipment_no->F_SHIPMENT_NO)->first();
                    $product_count = Stock::where('F_BOX_NO',$box->F_BOX_NO)->where('PRODUCT_STATUS','<=',50)->where('F_SHIPPMENT_NO',$shipment_no->F_SHIPMENT_NO)->count();
                    if ($product_count == 0) {
                        Box::where('PK_NO', $box->F_BOX_NO)->update(['BOX_STATUS' => 60, 'F_INV_WAREHOUSE_NO' => $shipment_status->F_TO_INV_WAREHOUSE_NO]);
                    }
                }

                Stock::where('PK_NO', $id)
                ->update(['PRODUCT_STATUS' => 420
                        ,'F_INV_WAREHOUSE_NO' => $shipment_status->F_TO_INV_WAREHOUSE_NO ?? 1
                        ,'INV_WAREHOUSE_NAME' => $shipment_status->to_warehouse->NAME ?? 'UK WAREHOUSE 01']);

                DB::table('SC_BOX_INV_STOCK')->where('F_INV_STOCK_NO',$id)->delete();

            }else{
                Stock::where('PK_NO', $id)->update(['PRODUCT_STATUS' => 420]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        DB::commit();
        return 1;
    }
}
