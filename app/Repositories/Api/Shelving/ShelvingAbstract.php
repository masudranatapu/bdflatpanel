<?php
namespace App\Repositories\Api\Shelving;

use DB;
use App\Models\Auth;
use App\Models\Stock;
use App\Traits\ApiResponse;
use App\Models\WarehouseZone;
use App\Models\BookingDetails;

class ShelvingAbstract implements ShelvingInterface
{
    use ApiResponse;

    function __construct() {
    }

    public function get_available_qty($sku_id, $qty,$house)
    {
        $info = Stock::where('SKUID', $sku_id)->where('F_INV_WAREHOUSE_NO', $house)->whereRaw('( F_INV_ZONE_NO IS NULL) ')->count();
        if ($info < $qty) {
            return 'exeeded';
        }else{
            return 'allow';
        }
    }

    public function get_available_qty_update($shelve_label,$sku_id, $qty,$house)
    {
        $info = Stock::where('INV_ZONE_BARCODE', $shelve_label)->where('SKUID', $sku_id)->where('F_INV_WAREHOUSE_NO', $house)->count();
        if ($info < $qty) {
            return 'exeeded';
        }else{
            return 'allow';
        }
    }

    public function postShelving($request)
    {
        $request = $request->json()->all();
        $string     = '';
        $response_code      = 200;
        $response_msg       = 'Shelving unsuccessfull !';
        $response_data      = null;
        $response_status    = 0;
        $col_separatior     = '~' ;
        $row_separatior     = '|' ;
        $shelve_label       = $request['shelve_label'];

        $shelve_no = DB::table('INV_WAREHOUSE_ZONES')->where('ZONE_BARCODE', $shelve_label)->first();
        if (empty($shelve_no)) {
            return $this->successResponse(200, 'Shelve not found !', null, 0);
        }

        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request['user_id'])->first();
        if ($user_map->F_INV_WAREHOUSE_NO != $shelve_no->F_INV_WAREHOUSE_NO) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }

        for ($loop=0; $loop < count($request['data']); $loop++) {

            if ($request['is_update'] == 0) {
                $status = $this->get_available_qty($request['data'][$loop]['sku_id'], $request['data'][$loop]['qty'],$user_map->F_INV_WAREHOUSE_NO);
            }else if($request['is_update'] == 1){
                $status = $this->get_available_qty_update($shelve_label,$request['data'][$loop]['sku_id'], $request['data'][$loop]['qty'],$user_map->F_INV_WAREHOUSE_NO);
            }
            if ($status == 'allow') {
                $string .= $request['data'][$loop]['sku_id'].'~'.$user_map->F_INV_WAREHOUSE_NO.'~'.$request['data'][$loop]['qty'].'|';
            }else{
                return $this->successResponse(200, 'Quantity exeeded!', null, 0);
            }
        }

        $count      = count($request['data']);
        $user_id    = $request['user_id'];
        $is_update  = $request['is_update'];
        $col_parameters = 3;
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($string);
        // echo '<br>======================<br>';
        // exit();
        DB::beginTransaction();
        try {

            if ($is_update == 0) {
                DB::statement('CALL PROC_SHELVING_INV_STOCK(:shelve_label, :string, :count, :col_parameters, :col_separatior, :row_separatior, :user_id, :is_update, @OUT_STATUS);',
                    array(
                        $shelve_label
                        ,$string
                        ,$count
                        ,$col_parameters
                        ,$col_separatior
                        ,$row_separatior
                        ,$user_id
                        ,$is_update
                    )
                );
            }else if($is_update == 1){
                DB::statement('CALL PROC_SHELVING_UPDATE_INV_STOCK(:shelve_label, :string, :count, :col_parameters, :col_separatior, :row_separatior, :user_id, :is_update, @OUT_STATUS);',
                    array(
                        $shelve_label
                        ,$string
                        ,$count
                        ,$col_parameters
                        ,$col_separatior
                        ,$row_separatior
                        ,$user_id
                        ,$is_update
                    )
                );
            }

            $prc = DB::select('select @OUT_STATUS as OUT_STATUS');

            // $prc = DB::select('CALL PROC_SC_BOX_INV_STOCK(?,?,?,?,?,?,?,?,?)', [ $box_label, $string, $count, 3, $column_separatior, $row_separatior, $user_id, $is_update, '@OUT_STATUS as tt']);
            if ($prc[0]->OUT_STATUS == 'success') {

               $response_code     = 200;
               $response_msg       = $is_update == 0 ? 'Shelving successfull !' : 'Unshelving successfull !';
               $response_data      = null;
               $response_status    = 1;

            }elseif ($prc[0]->OUT_STATUS == 'exeeded') {

               $response_code      = 200;
               $response_msg       = 'Item exeeded !';
               $response_data      = null;
               $response_status    = 0;

            }else{
                $response_code      = 200;
                $response_msg       = $is_update == 0 ? 'Shelving unsuccessfull !' : 'Unshelving unsuccessfull !';
                $response_data      = null;
                $response_status    = 1;
            }

        } catch (\Exeption $e) {
           DB::rollback();
           return $this->successResponse(200, $e->getMessage(), null, 0);
        }

        DB::commit();
        return $this->successResponse($response_code, $response_msg, $response_data, $response_status);
    }

    public function postShelvingList($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }

        $count_not_shelved = Stock::selectRaw('(SELECT IFNULL(COUNT(SKUID),0) from INV_STOCK where SKUID = sku_id and F_INV_WAREHOUSE_NO = '.$user_map->F_INV_WAREHOUSE_NO.' and (F_INV_ZONE_NO IS NULL))')->limit(1)->getQuery();

        $data = DB::table('INV_STOCK as s')
                ->select('v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','s.INV_WAREHOUSE_NAME as warehouse')
                ->selectSub($count_not_shelved, 'available_qty')
                ->join('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE')
                ->where('s.F_INV_WAREHOUSE_NO', $user_map->F_INV_WAREHOUSE_NO)
                ->where('s.SKUID', $request->sku_id)
                ->whereRaw('s.F_INV_ZONE_NO IS NULL')
                // ->whereRaw('s.F_INV_ZONE_NO IS NULL and s.F_BOX_NO IS NOT NULL and s.F_SHIPPMENT_NO IS NOT NULL and s.PRODUCT_STATUS = 60')
                ->groupBy('s.IG_CODE', 's.F_INV_WAREHOUSE_NO')->get();

        if ( count($data) > 0 ) {
            return $this->successResponse(200, 'Product found !', $data, 1);
        }
        return $this->successResponse(200, 'Product not found !', null, 0);
    }

    public function postAllShelveList($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }

        $data = DB::table('INV_STOCK as s')
                ->select('v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.REGULAR_PRICE as price','v.INSTALLMENT_PRICE as ins_price','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','s.INV_WAREHOUSE_NAME as warehouse',DB::raw('IFNULL(count(s.PK_NO),0) as available_qty'))
                ->join('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE')
                ->where('s.F_INV_WAREHOUSE_NO', $user_map->F_INV_WAREHOUSE_NO)
                ->where('s.INV_ZONE_BARCODE', $request->shelve_label);
                if ($request->sku_id != '') {
                    $data = $data->where('s.SKUID', $request->sku_id);
                }
                $data = $data->groupBy('s.IG_CODE', 's.F_INV_WAREHOUSE_NO')->get();

            if ( count($data) > 0 ) {
                return $this->successResponse(200, 'Product found !', $data, 1);
            }
            return $this->successResponse(200, 'Product not found !', null, 0);
    }

    public function postRtsShelveCheckout($request)
    {
        $request = $request->json()->all();
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request['user_id'])->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        $username = Auth::select('USERNAME')->where('PK_NO',$request['user_id'])->first();

        DB::beginTransaction();

        try {
            if ($request['is_shelve'] == 1) {
                $warehouse  = WarehouseZone::select('ITEM_COUNT')->where('ZONE_BARCODE',$request['shelve_label'])->first();
                $count      = $warehouse->ITEM_COUNT - $request['data'][0]['qty'];
                WarehouseZone::where('ZONE_BARCODE',$request['shelve_label'])->update(['ITEM_COUNT'=>$count]);

                $data = Stock::select('INV_STOCK.PK_NO')
                        ->join('SLS_BOOKING_DETAILS','SLS_BOOKING_DETAILS.F_INV_STOCK_NO','INV_STOCK.PK_NO')
                        ->join('SLS_ORDER','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING_DETAILS.F_BOOKING_NO')
                        ->leftjoin('SLS_BATCH_LIST','SLS_BATCH_LIST.PK_NO','SLS_ORDER.PICKUP_ID')
                        ->where('INV_STOCK.INV_ZONE_BARCODE',$request['shelve_label'])
                        ->where('INV_STOCK.SKUID',$request['data'][0]['sku_id']);
                        if (isset($request['data'][0]['batch_no']) && $request['data'][0]['batch_no'] != '') {
                            $data = $data->where('SLS_BATCH_LIST.RTS_BATCH_NO',$request['data'][0]['batch_no'])
                            ->where('SLS_BOOKING_DETAILS.RTS_COLLECTION_USER_ID',$request['user_id']);
                        }
                        $data = $data->where('SLS_ORDER.PK_NO',$request['data'][0]['order_id'])
                        ->where('SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS',0)
                        ->limit($request['data'][0]['qty'])
                        ->get();
                Stock::join('SLS_BOOKING_DETAILS','SLS_BOOKING_DETAILS.F_INV_STOCK_NO','INV_STOCK.PK_NO')
                        ->whereIn('SLS_BOOKING_DETAILS.F_INV_STOCK_NO',$data)
                        ->update(['INV_STOCK.F_INV_ZONE_NO'=>null
                        ,'INV_STOCK.INV_ZONE_BARCODE'=>null
                        ,'INV_STOCK.ZONE_CHECK_OUT_BY_NAME'=>$username->USERNAME
                        ,'INV_STOCK.ZONE_CHECK_OUT_BY'=>$request['user_id']
                        ,'SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS'=>1]);
                DB::table('INV_WAREHOUSE_ZONE_STOCK_ITEM')->whereIn('F_INV_STOCK_NO',$data)->delete();
            }else{
                $data = Stock::select('INV_STOCK.PK_NO')
                ->join('SLS_BOOKING_DETAILS','SLS_BOOKING_DETAILS.F_INV_STOCK_NO','INV_STOCK.PK_NO')
                ->join('SLS_ORDER','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING_DETAILS.F_BOOKING_NO')
                ->leftjoin('SLS_BATCH_LIST','SLS_BATCH_LIST.PK_NO','SLS_ORDER.PICKUP_ID')
                ->whereNull('INV_STOCK.INV_ZONE_BARCODE')
                ->where('INV_STOCK.SKUID',$request['data'][0]['sku_id']);
                if (isset($request['data'][0]['batch_no']) && $request['data'][0]['batch_no'] != '') {
                    $data = $data->where('SLS_BATCH_LIST.RTS_BATCH_NO',$request['data'][0]['batch_no'])
                    ->where('SLS_BOOKING_DETAILS.RTS_COLLECTION_USER_ID',$request['user_id']);
                }
                $data=  $data->where('SLS_ORDER.PK_NO',$request['data'][0]['order_id'])
                ->where('SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS',0)
                ->limit($request['data'][0]['qty'])
                ->get();
                BookingDetails::whereIn('F_INV_STOCK_NO',$data)
                               ->update(['IS_COLLECTED_FOR_RTS'=>1]);
            }
        } catch (\Exeption $e) {
            DB::rollback();
            return $this->successResponse(200, $e->getMessage(), null, 0);
        }
        DB::commit();
        if ($data->isEmpty()) {
            return $this->successResponse(200, 'You do not have this product !', null, 0);
        }
        return $this->successResponse(200, 'RTS Dispatch Successful !', $data, 1);
    }
}
