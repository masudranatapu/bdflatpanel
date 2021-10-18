<?php
namespace App\Repositories\Api\Product;
use DB;
use App\Models\Stock;
use App\Models\Product;
use App\Models\ProdImgLib;
use App\Traits\ApiResponse;
use App\Models\ProductVariant;

class ProductAbstract implements ProductInterface
{
    use ApiResponse;

    function __construct() {}

    public function getProductList(){
        $data = Product::select('PK_NO as mp_id','DEFAULT_NAME as mp_name','PRIMARY_IMG_RELATIVE_PATH as mp_image','MODEL_NAME as mp_model', 'BRAND_NAME as mp_brand')->get();

        if (!empty($data)) {
            return $this->successResponse(200, 'Product list is available !', $data, 1);
        }
        return $this->successResponse(200, 'Data Not Found !', null, 0);
    }

    public function getVariantList($id){

        $data = DB::table('PRD_VARIANT_SETUP as v')
                    ->select('v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','m.DEFAULT_NAME as product_name','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.SEA_FREIGHT_CHARGE as sea_price', 'v.AIR_FREIGHT_CHARGE as air_price', 'v.PREFERRED_SHIPPING_METHOD as preferred_shipping_method', 'v.VAT_AMOUNT_PERCENT as vat', 'v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','m.PRIMARY_IMG_RELATIVE_PATH as primary_image','v.INTER_DISTRICT_POSTAGE as ss_price','v.LOCAL_POSTAGE as sm_price'
                    ,DB::raw('(CASE WHEN v.PREFERRED_SHIPPING_METHOD = "AIR" THEN 1 ELSE 0 END) AS is_air')
                    ,DB::raw('(select ifnull(count(PK_NO),0) from PRD_VARIANT_SETUP where PREFERRED_SHIPPING_METHOD = "AIR" and F_PRD_MASTER_SETUP_NO = '.$id.') as is_air_count')
                    ,DB::raw('(select ifnull(count(PK_NO),0) from PRD_VARIANT_SETUP where PREFERRED_SHIPPING_METHOD = "SEA" and F_PRD_MASTER_SETUP_NO = '.$id.') as is_sea_count')
                    )
                    ->leftJoin('PRD_MASTER_SETUP as m', 'm.PK_NO', 'v.F_PRD_MASTER_SETUP_NO')
                    ->where('v.F_PRD_MASTER_SETUP_NO',$id)
                    ->get();

        if (count($data)>0) {
            return $this->successResponse(200, 'Product variant data is available !', $data, 1);
        }
        return $this->successResponse(200, 'Data Not Found !', null, 0);
    }

    public function getAllVariantList($request){
        $data = null;
        $barcode       = trim($request->barcode);
        $product_name  = trim($request->product_name);
        $mkt_id        = trim($request->mkt_id);
        $sku_id        = trim($request->sku_id);
        $data = DB::table('PRD_VARIANT_SETUP as v')
                ->select('v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','m.DEFAULT_NAME as product_name','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.SEA_FREIGHT_CHARGE as sea_price', 'v.AIR_FREIGHT_CHARGE as air_price', 'v.PREFERRED_SHIPPING_METHOD as preferred_shipping_method', 'v.VAT_AMOUNT_PERCENT as vat', 'v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','m.PRIMARY_IMG_RELATIVE_PATH as primary_image','v.INTER_DISTRICT_POSTAGE as ss_price','v.LOCAL_POSTAGE as sm_price',
                DB::raw('(CASE WHEN v.PREFERRED_SHIPPING_METHOD = "AIR" THEN 1 ELSE 0 END) AS is_air'))
                ->leftJoin('PRD_MASTER_SETUP as m', 'm.PK_NO', 'v.F_PRD_MASTER_SETUP_NO');
                if (!empty($barcode) && $barcode != '') {
                    $data = $data->selectRaw('(select ifnull(count(PK_NO),0) from PRD_VARIANT_SETUP where PREFERRED_SHIPPING_METHOD = "AIR" and BARCODE = "'.$barcode.'") as is_air_count');
                    $data = $data->selectRaw('(select ifnull(count(PK_NO),0) from PRD_VARIANT_SETUP where PREFERRED_SHIPPING_METHOD = "SEA" and BARCODE = "'.$barcode.'") as is_sea_count');

                    $data = $data->where('v.BARCODE', $barcode);
                }else{
                    $pieces = explode(" ", $product_name);
                    if($pieces){
                        foreach ($pieces as $key => $piece) {
                            $data->where('v.VARIANT_NAME', 'LIKE', '%' . $piece . '%');
                            $data->where('v.KEYWORD_SEARCH', 'LIKE', '%' . $piece . '%');
                        }
                    }
                    $data = $data->selectRaw('(select ifnull(count(PK_NO),0) from PRD_VARIANT_SETUP where PREFERRED_SHIPPING_METHOD = "AIR" and VARIANT_NAME LIKE "%'.$product_name.'%" and COMPOSITE_CODE LIKE "%'.$sku_id.'%" and MRK_ID_COMPOSITE_CODE LIKE "%'.$mkt_id.'%") as is_air_count');

                    $data = $data->selectRaw('(select ifnull(count(PK_NO),0) from PRD_VARIANT_SETUP where PREFERRED_SHIPPING_METHOD = "SEA" and VARIANT_NAME LIKE "%'.$product_name.'%" and COMPOSITE_CODE LIKE "%'.$sku_id.'%" and MRK_ID_COMPOSITE_CODE LIKE "%'.$mkt_id.'%") as is_sea_count');

                    // $data = $data->where('v.VARIANT_NAME', 'like', '%' . $product_name . '%');
                    $data = $data->where('v.COMPOSITE_CODE', 'like', '%' . $sku_id . '%');
                    $data = $data->where('v.MRK_ID_COMPOSITE_CODE', 'like', '%' . $mkt_id . '%');
                }
                $data = $data->get();
                // echo '<pre>';
                // echo '======================<br>';
                // print_r($pieces);
                // echo '<br>======================<br>';
                // exit();
        if (count($data)>0) {
            return $this->successResponse(200, 'Variant is available !', $data, 1);
        }

        return $this->successResponse(200, 'Data Not Found !', null, 0);
    }

    public function getVariantImg($id){
        $data = ProdImgLib::select('RELATIVE_PATH as mp_image')->where('F_PRD_VARIANT_NO', $id)->get();
        if ($data->isEmpty()) {
            $data = ProductVariant::select('PRIMARY_IMG_RELATIVE_PATH as mp_image')->where('PK_NO', $id)->get();
        }

        if (count($data)>0) {
            return $this->successResponse(200, 'Variant Image is available !', $data, 1);
        }
        return $this->successResponse(200, 'Data Not Found !', null, 0);
    }

    public function getStockSearchList($request){
        $data = null;
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'User Not Found !', null, 0);
        }
        $barcode       = trim($request->barcode);
        $product_name  = trim($request->product_name);
        $mkt_id        = trim($request->mkt_id);
        $sku_id        = trim($request->sku_id);
        $is_air = Stock::selectRaw('(select ifnull(count(PK_NO),0) from INV_STOCK where FINAL_PREFFERED_SHIPPING_METHOD = "AIR" and IG_CODE = mkt_id and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and (PRODUCT_STATUS IS NULL OR PRODUCT_STATUS = 0 OR PRODUCT_STATUS = 90 OR PRODUCT_STATUS < 20))')->limit(1)->getQuery();

        $is_sea = Stock::selectRaw('(select ifnull(count(PK_NO),0) from INV_STOCK where FINAL_PREFFERED_SHIPPING_METHOD = "SEA" and IG_CODE = mkt_id and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and (PRODUCT_STATUS IS NULL OR PRODUCT_STATUS = 0 OR PRODUCT_STATUS = 90 OR PRODUCT_STATUS < 20))')->limit(1)->getQuery();

        // if (!empty($request->barcode) && $request->barcode != '') {
            $data = DB::table('INV_STOCK as s')
                    ->select('s.INV_WAREHOUSE_NAME','v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price', 'v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image', 'v.INTER_DISTRICT_POSTAGE as ss_price','v.LOCAL_POSTAGE as sm_price','s.FINAL_PREFFERED_SHIPPING_METHOD'
                    , DB::raw('count(s.PK_NO) as available_qty')
                    , DB::raw('(CASE WHEN s.FINAL_PREFFERED_SHIPPING_METHOD = "AIR" THEN 1 ELSE 0 END) AS is_air'))
                    ->join('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE');
                    if (!empty($barcode) && $barcode != '') {
                        $data = $data->selectRaw('(select ifnull(count(PK_NO),0) from INV_STOCK where FINAL_PREFFERED_SHIPPING_METHOD = "AIR" and BARCODE = "'.$barcode.'" and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and (PRODUCT_STATUS IS NULL OR PRODUCT_STATUS = 0 OR PRODUCT_STATUS = 90 OR PRODUCT_STATUS < 20)) as is_air_count');

                        $data = $data->selectRaw('(select ifnull(count(PK_NO),0) from INV_STOCK where FINAL_PREFFERED_SHIPPING_METHOD = "SEA" and BARCODE = "'.$barcode.'" and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and (PRODUCT_STATUS IS NULL OR PRODUCT_STATUS = 0 OR PRODUCT_STATUS = 90 OR PRODUCT_STATUS < 20)) as is_sea_count');

                        $data = $data->where('s.BARCODE', $barcode);
                    }else{
                        $data = $data->selectSub($is_air, 'is_air_count');
                        $data = $data->selectSub($is_sea, 'is_sea_count');
                        $pieces = explode(" ", $product_name);
                        if($pieces){
                            foreach ($pieces as $key => $piece) {
                                $data->where('v.VARIANT_NAME', 'LIKE', '%' . $piece . '%');
                                $data->where('v.KEYWORD_SEARCH', 'LIKE', '%' . $piece . '%');
                            }
                        }
                        $data = $data->where('v.COMPOSITE_CODE', 'like', '%' . $mkt_id . '%');
                        $data = $data->where('v.MRK_ID_COMPOSITE_CODE', 'like', '%' . $sku_id . '%');
                    }
                    $data = $data->where('s.F_INV_WAREHOUSE_NO', $user_map->F_INV_WAREHOUSE_NO);

                    $data = $data->whereRaw('( s.PRODUCT_STATUS IS NULL OR s.PRODUCT_STATUS = 0 OR s.PRODUCT_STATUS = 90 OR s.PRODUCT_STATUS < 20 ) ');
                    $data = $data->groupBy('s.IG_CODE')->get();

        if (count($data)>0) {
            return $this->successResponse(200, 'Variant is available !', $data, 1);
        }

        return $this->successResponse(200, 'Data Not Found !', null, 0);
    }

    public function postProductDetailsList($request)
    {
        $box_no = DB::table('SC_BOX')->select('PK_NO','F_INV_WAREHOUSE_NO')->where('PK_NO',$request->PK_NO)->first();
        if (empty($box_no)) {
            return $this->successResponse(200, 'Box not found !', null, 0);
        }
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();

        if ($user_map->F_INV_WAREHOUSE_NO != $box_no->F_INV_WAREHOUSE_NO) {
            return $this->successResponse(200, 'Box not found !', null, 0);
        }

        $data = DB::table('INV_STOCK as s')
        ->select('v.PK_NO','s.INV_WAREHOUSE_NAME','v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image', DB::raw('IFNULL(count(s.PK_NO),0) as given_qty'))
        ->join('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE')
        ->where('F_INV_WAREHOUSE_NO',$box_no->F_INV_WAREHOUSE_NO)
        ->where('F_BOX_NO',$box_no->PK_NO)
        ->orderBy('s.PK_NO','DESC')
        ->groupBy('s.IG_CODE')->get();

        if (count($data)>0) {
            return $this->successResponse(200, 'Product List Found !', $data, 1);
        }
        return $this->successResponse(200, 'Box not found !', null, 0);
    }

    public function postProductSearchList($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        $barcode       = trim($request->barcode);
        $product_name  = trim($request->product_name);
        $mkt_id        = trim($request->mkt_id);
        $sku_id        = trim($request->sku_id);
        $count_not_boxed = Stock::selectRaw('(SELECT IFNULL(COUNT(SKUID),0) from INV_STOCK where SKUID = sku_id and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and (F_BOX_NO IS NULL OR F_BOX_NO = 0) and (PRODUCT_STATUS IS NULL OR PRODUCT_STATUS = 0 OR PRODUCT_STATUS = 90 OR PRODUCT_STATUS < 420))')->limit(1)->getQuery();

        $count_boxed = Stock::selectRaw('(SELECT IFNULL(COUNT(SKUID),0) from INV_STOCK where SKUID = sku_id and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and PRODUCT_STATUS <= 50 and F_BOX_NO IS NOT NULL and (ORDER_STATUS IS NULL OR ORDER_STATUS < 80))')->limit(1)->getQuery();

        $count_shipped = Stock::selectRaw('(SELECT IFNULL(COUNT(SKUID),0) from INV_STOCK where SKUID = sku_id and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and (F_SHIPPMENT_NO IS NOT NULL and F_BOX_NO IS NOT NULL) and (ORDER_STATUS IS NULL OR ORDER_STATUS < 80))')->limit(1)->getQuery();

        $count_shelved = Stock::selectRaw('(SELECT IFNULL(COUNT(SKUID),0) from INV_STOCK where SKUID = sku_id and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and (F_INV_ZONE_NO IS NOT NULL))')->limit(1)->getQuery();

        $count_not_shelved = Stock::selectRaw('(SELECT IFNULL(COUNT(SKUID),0) from INV_STOCK where SKUID = sku_id and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and (F_INV_ZONE_NO IS NULL and F_BOX_NO IS NOT NULL and F_SHIPPMENT_NO IS NOT NULL and PRODUCT_STATUS = 60))')->limit(1)->getQuery();

        $data = DB::table('INV_STOCK as s')
                ->select('v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','s.INV_WAREHOUSE_NAME as warehouse'
                ,DB::raw('IFNULL(COUNT(SKUID),0) as available_qty')
                ,DB::raw('(CASE WHEN s.FINAL_PREFFERED_SHIPPING_METHOD = "AIR" THEN 1 ELSE 0 END) AS is_air')
                )
                ->selectSub($count_boxed, 'boxed_qty')
                ->selectSub($count_not_boxed, 'yet_to_boxed_qty')
                ->selectSub($count_shipped, 'shipment_assigned_qty')
                // ->selectSub($count_shelved, 'shelved_qty')
                // ->selectSub($count_not_shelved, 'not_shelved_qty')
                ->join('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE')
                ->where('s.F_INV_WAREHOUSE_NO', $user_map->F_INV_WAREHOUSE_NO)
                ->whereRaw('(s.ORDER_STATUS IS NULL OR s.ORDER_STATUS < 80)');
                if (!empty($barcode) && $barcode != ''){
                    $data = $data->where('s.BARCODE', $barcode);
                }else{
                    $pieces = explode(" ", $product_name);
                    if($pieces){
                        foreach ($pieces as $key => $piece) {
                            $data->where('v.VARIANT_NAME', 'LIKE', '%' . $piece . '%');
                            $data->where('v.KEYWORD_SEARCH', 'LIKE', '%' . $piece . '%');
                        }
                    }
                    // $data = $data->where('v.VARIANT_NAME', 'like', '%' . $product_name . '%');
                    $data = $data->where('v.COMPOSITE_CODE', 'like', '%' . $sku_id . '%');
                    $data = $data->where('v.MRK_ID_COMPOSITE_CODE', 'like', '%' . $mkt_id . '%');
                }
                $data = $data->groupBy('s.IG_CODE', 's.F_INV_WAREHOUSE_NO')
                ->take(150)
                ->get();

        if (count($data)>0) {
            return $this->successResponse(200, 'Product found !', $data, 1);
        }

        return $this->successResponse(200, 'Product not found !', null, 0);
    }

    public function postProductSearchListMy($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        $barcode       = trim($request->barcode);
        $product_name  = trim($request->product_name);
        $mkt_id        = trim($request->mkt_id);
        $sku_id        = trim($request->sku_id);
        $count_boxed = Stock::selectRaw('(SELECT IFNULL(COUNT(SKUID),0) from INV_STOCK where SKUID = sku_id and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and PRODUCT_STATUS <= 50 and (ORDER_STATUS IS NULL OR ORDER_STATUS < 80))')->limit(1)->getQuery();

        $count_shelved = Stock::selectRaw('(SELECT IFNULL(COUNT(SKUID),0) from INV_STOCK where SKUID = sku_id and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and F_INV_ZONE_NO IS NOT NULL and (ORDER_STATUS IS NULL OR ORDER_STATUS < 80))')->limit(1)->getQuery();

        $count_not_shelved = Stock::selectRaw('(SELECT IFNULL(COUNT(SKUID),0) from INV_STOCK where SKUID = sku_id and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and F_INV_ZONE_NO IS NULL and (ORDER_STATUS IS NULL OR ORDER_STATUS < 80))')->limit(1)->getQuery();

        $data = DB::table('INV_STOCK as s')
                ->select('v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','s.INV_WAREHOUSE_NAME as warehouse'
                ,DB::raw('IFNULL(COUNT(SKUID),0) as available_qty'),
                DB::raw('(CASE WHEN s.FINAL_PREFFERED_SHIPPING_METHOD = "AIR" THEN 1 ELSE 0 END) AS is_air')
                )
                ->selectSub($count_boxed, 'box_qty')
                ->selectSub($count_not_shelved, 'land_area_qty')
                ->selectSub($count_shelved, 'shelved_qty')
                ->join('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE')
                ->where('s.F_INV_WAREHOUSE_NO', $user_map->F_INV_WAREHOUSE_NO)
                ->whereRaw('(s.ORDER_STATUS IS NULL OR s.ORDER_STATUS < 80)');
                if (!empty($barcode) && $barcode != ''){
                    $data = $data->where('s.BARCODE', $barcode);
                }else{
                    $pieces = explode(" ", $product_name);
                    if($pieces){
                        foreach ($pieces as $key => $piece) {
                            $data->where('v.VARIANT_NAME', 'LIKE', '%' . $piece . '%');
                            $data->where('v.KEYWORD_SEARCH', 'LIKE', '%' . $piece . '%');
                        }
                    }
                    // $data = $data->where('v.VARIANT_NAME', 'like', '%' . $product_name . '%');
                    $data = $data->where('v.COMPOSITE_CODE', 'like', '%' . $sku_id . '%');
                    $data = $data->where('v.MRK_ID_COMPOSITE_CODE', 'like', '%' . $mkt_id . '%');
                }
                $data = $data->groupBy('s.IG_CODE', 's.F_INV_WAREHOUSE_NO')
                ->take(150)
                ->get();

        if (count($data)>0) {
            return $this->successResponse(200, 'Product found !', $data, 1);
        }

        return $this->successResponse(200, 'Product not found !', null, 0);
    }

    public function postProductBoxLocation($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        $data = DB::table('INV_STOCK as s')
                ->select('b.PK_NO','b.BOX_NO as box_label','b.BOX_STATUS as status','s.INV_WAREHOUSE_NAME as warehouse','s.F_BOX_NO as box_id'
                ,DB::raw('(SELECT IFNULL(COUNT(F_BOX_NO),0) from INV_STOCK where SKUID = '.$request->sku_id.' and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and F_BOX_NO = box_id and F_BOX_NO IS NOT NULL) as product_count'))
                ->join('SC_BOX as b', 'b.PK_NO', 's.F_BOX_NO')
                ->where('s.SKUID', $request->sku_id)
                ->where('s.F_INV_WAREHOUSE_NO',$user_map->F_INV_WAREHOUSE_NO)
                ->whereNotNull('s.F_BOX_NO')
                ->groupBy('s.F_BOX_NO')
                ->get();

                if (count($data)>0) {
                    return $this->successResponse(200, 'Product details found !', $data, 1);
                }
                return $this->successResponse(200, 'Product details not found !', null, 0);
    }

    public function postProductSearchListDetailsMy($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        $count_boxed = Stock::selectRaw('(SELECT IFNULL(COUNT(F_BOX_NO),0) from INV_STOCK where SKUID = '.$request->sku_id.' and F_INV_WAREHOUSE_NO = 2 and PRODUCT_STATUS <= 50 and F_BOX_NO = box_id and F_BOX_NO IS NOT NULL)')->limit(1)->getQuery();
        $count_land = Stock::selectRaw('(SELECT IFNULL(COUNT(PK_NO),0) from INV_STOCK where SKUID = '.$request->sku_id.' and F_INV_WAREHOUSE_NO = 2 and (PRODUCT_STATUS < 420 OR PRODUCT_STATUS = 60 OR PRODUCT_STATUS IS NULL) and F_INV_ZONE_NO IS NULL)')->limit(1)->getQuery();
        $count_shelved = Stock::selectRaw('(SELECT IFNULL(COUNT(PK_NO),0) from INV_STOCK where SKUID = '.$request->sku_id.' and F_INV_WAREHOUSE_NO = 2 and F_INV_ZONE_NO = zone_no and F_INV_ZONE_NO IS NOT NULL)')->limit(1)->getQuery();

        $data = DB::table('INV_STOCK as s')
                ->select('s.PK_NO','s.INV_WAREHOUSE_NAME as warehouse','s.F_BOX_NO as box_id','s.BOX_BARCODE','s.INV_ZONE_BARCODE','s.F_INV_ZONE_NO as zone_no'
                ,DB::raw('(select ifnull(wz.DESCRIPTION,"Product Is In Landing Area")) as description')
                )
                ->leftjoin('INV_WAREHOUSE_ZONES as wz', 'wz.PK_NO', 's.F_INV_ZONE_NO')
                ->selectSub($count_boxed, 'qty1')
                ->selectSub($count_land, 'qty2')
                ->selectSub($count_shelved, 'qty3')
                ->where('s.SKUID', $request->sku_id)
                ->where('s.F_INV_WAREHOUSE_NO',2)
                ->groupBy('s.F_INV_ZONE_NO')
                ->get();

        foreach ($data as $key => $value) {
            if ($value->qty1 > 0) {
                $value->type    = 1;
                $value->label   = $value->BOX_BARCODE;
                $value->qty     = $value->qty1;
            }
            if ($value->qty2 > 0 && $value->INV_ZONE_BARCODE == '') {
                $value->type    = 2;
                $value->label   = 'land';
                $value->qty     = $value->qty2;
            }else {
                $value->type    = 3;
                $value->label   = $value->INV_ZONE_BARCODE;
                $value->qty     = $value->qty3;
            }
        }
        if (count($data)>0) {
            return $this->successResponse(200, 'Product details found !', $data, 1);
        }
        return $this->successResponse(200, 'Product details not found !', null, 0);
    }
}
