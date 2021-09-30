<?php
namespace App\Repositories\Api\Dispatch;

use DB;
use App\Models\Box;
use App\Models\Auth;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Booking;
use App\Models\Dispatch;
use App\Models\OrderRtc;
use App\Models\Shipment;
use App\Models\Warehouse;
use App\Models\AccBankTxn;
use App\Models\PaymentBank;
use App\Models\Shipmentbox;
use App\Traits\ApiResponse;
use App\Models\OrderPayment;
use App\Models\WarehouseZone;
use App\Models\BookingDetails;
use App\Models\DispatchDetails;
use App\Models\PaymentCustomer;
use App\Models\PaymentReseller;
use App\Models\SmsNotification;
use App\Models\OrderConsignment;
use App\Models\EmailNotification;
use App\Models\WarehouseZoneItem;
// use Illuminate\Support\Facades\Config;

class DispatchAbstract implements DispatchInterface
{
    use ApiResponse;

    function __construct() {
    }

    public function postCodRtcDispatchList($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        $data = DB::table('INV_STOCK as s')
            ->select(
            'v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image',
            'wz.ZONE_BARCODE as label','wz.DESCRIPTION as location','od.BANK_ACC_NAME as person','od.F_ACC_PAYMENT_BANK_NO as person_id','o.PK_NO as order_id','s.PRODUCT_STATUS','s.BOX_BARCODE'
            ,DB::raw('(select ifnull(count(s.PK_NO),0)) as qty')
            ,DB::raw('(select ifnull(wz.DESCRIPTION,"Product Is In Landing Area")) as location')
            ,DB::raw('(select ifnull(wz.ZONE_BARCODE,"Landing Area")) as label')
            )
            ->leftjoin('SLS_BOOKING_DETAILS as bd', 's.PK_NO', 'bd.F_INV_STOCK_NO')
            ->join('SLS_ORDER_RTC as od', 'od.F_ORDER_NO', 's.F_ORDER_NO')
            ->join('SLS_ORDER as o', 'o.PK_NO', 's.F_ORDER_NO')
            ->leftjoin('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 's.IG_CODE')
            ->leftjoin('INV_WAREHOUSE_ZONES as wz', 'wz.PK_NO', 's.F_INV_ZONE_NO')
            // ->whereNotNull('s.INV_ZONE_BARCODE')
            // ->where('od.IS_CONFIRM_HOLDER',0)
            ->where('od.IS_REQUEST_PENDING',0)
            ->where('bd.IS_COD_SHELVE_TRANSFER',0)
            // ->whereRaw('o.IS_READY = 1 OR o.IS_READY = 2')
            ->where('bd.IS_READY',1)
            ->where('bd.DISPATCH_STATUS','<',40)
            // ->where('s.PRODUCT_STATUS','>=',60)
            ->groupBy('s.IG_CODE','wz.PK_NO','od.F_ACC_PAYMENT_BANK_NO','s.PRODUCT_STATUS')
            ->get();

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if ($value->PRODUCT_STATUS == 50) {
                    $value->location = 'Yet to unbox - '.$value->BOX_BARCODE;
                    $value->label = $value->BOX_BARCODE;
                }
            }
        }
        if ( count($data) > 0 ) {
            return $this->successResponse(200, 'Data found !', $data, 1);
        }
        return $this->successResponse(200, 'Data not found !', null, 0);
    }

    public function postCodRtcDispatchTransfer($request)
    {
        $request    = $request->json()->all();

        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request["user_id"])->first();
        // $config  = \Config::get('static_array.COS_RTC_zone');
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', $user_map, 0);
        }
        $agent_zone = PaymentBank::select('BANK_ACC_NAME','F_INV_ZONE_NO')->where('PK_NO',$request["data"][0]["person_id"])->first();
        $user_name  = Auth::select('USERNAME')->where('PK_NO',$request["user_id"])->first();
        // $info       = $config[$request["data"][0]["person_id"]];
        if ($request["is_shelve"] == 1) {
            $old_shelve   = WarehouseZone::select('ITEM_COUNT','PK_NO')->where('ZONE_BARCODE',$request["shelve_label"])->first();
            $decrease_qty = $old_shelve->ITEM_COUNT - $request["data"][0]["qty"];
        }
        $new_shelve   = WarehouseZone::where('PK_NO',$agent_zone->F_INV_ZONE_NO)->first();
        $increase_qty = $new_shelve->ITEM_COUNT + $request["data"][0]["qty"];

        DB::beginTransaction();
        try {
            $booking_id = Order::select('F_BOOKING_NO')->where('PK_NO',$request["data"][0]["order_id"])->first();
            $inv_pks    = Stock::select('INV_STOCK.PK_NO')
                                ->join('SLS_BOOKING_DETAILS as b','INV_STOCK.PK_NO','b.F_INV_STOCK_NO')
                                ->leftjoin('SLS_ORDER_RTC as rtc','rtc.F_BOOKING_NO','INV_STOCK.F_BOOKING_NO')
                                ->where('INV_STOCK.SKUID',$request["data"][0]["sku_id"])
                                ->where('INV_STOCK.F_BOOKING_NO',$booking_id->F_BOOKING_NO);
                                if ($request["is_shelve"] == 1) {
                                    $inv_pks = $inv_pks->where('INV_STOCK.INV_ZONE_BARCODE',$request["shelve_label"]);
                                }
                                $inv_pks = $inv_pks->where('rtc.F_ACC_PAYMENT_BANK_NO',$request["data"][0]["person_id"])
                                ->where('b.IS_COD_SHELVE_TRANSFER',0)
                                ->take($request["data"][0]["qty"])
                                ->get();

            if ($request["is_shelve"] == 1) {
                Stock::whereIn('PK_NO',$inv_pks)
                        // ->select('PK_NO')
                    ->update([
                         'F_INV_ZONE_NO'        => $new_shelve->PK_NO
                        ,'INV_ZONE_BARCODE'     => $new_shelve->ZONE_BARCODE
                        ,'ZONE_CHECK_OUT_BY'    => $request["user_id"]
                        ,'ZONE_CHECK_OUT_BY_NAME'=> $user_name->USERNAME
                    ]);
                    // ->get();
            }else{
                Stock::whereIn('PK_NO',$inv_pks)
                    ->update([
                         'F_INV_ZONE_NO'        => $new_shelve->PK_NO
                        ,'INV_ZONE_BARCODE'     => $new_shelve->ZONE_BARCODE
                        ,'ZONE_CHECK_IN_BY'     => $request["user_id"]
                        ,'ZONE_CHECK_IN_BY_NAME'=> $user_name->USERNAME
                    ]);
            }
            // echo '<pre>';
            // echo '======================<br>';
            // print_r($stocked);
            // print_r($increase_qty);
            // echo '<br>======================<br>';
            // exit();
            if ($request["is_shelve"] == 1) {
                WarehouseZone::where('ZONE_BARCODE',$request["shelve_label"])->update(['ITEM_COUNT' => $decrease_qty]);
            }
            WarehouseZone::where('ZONE_BARCODE',$new_shelve->ZONE_BARCODE)->update(['ITEM_COUNT' => $increase_qty]);
            BookingDetails::whereIn('F_INV_STOCK_NO',$inv_pks)->update(['IS_COD_SHELVE_TRANSFER' => 1]);
            if ($request["is_shelve"] == 1) {
                DB::table('INV_WAREHOUSE_ZONE_STOCK_ITEM')
                ->whereIn('F_INV_STOCK_NO',$inv_pks)
                ->update(['F_INV_WAREHOUSE_ZONE_NO'=> $new_shelve->PK_NO]);
            }else{
                $zone_items[]=array();
                foreach ($inv_pks as $key => $value) {
                    $zone_items[0]['F_INV_STOCK_NO'] = $value->PK_NO;
                    $zone_items[0]['F_INV_WAREHOUSE_ZONE_NO'] = $new_shelve->PK_NO;
                    // $zone_items[0][$value->PK_NO] = $new_shelve->PK_NO;
                }
                WarehouseZoneItem::insert($zone_items);
            }
            // $insert_data[] = array(
            //     'F_INV_STOCK_NO'                  => 5,
            //     'F_INV_WAREHOUSE_ZONE_NO'                 => 2,
            // );
            // echo '<pre>';
            // echo '======================<br>';
            // print_r($zone_items);
            // echo '<br>======================<br>';
            // print_r($data);
            // echo '<br>======================<br>';
            // print_r($insert_data);
            // echo '<br>======================<br>';
            // exit();
        } catch (\Exeption $e) {
            DB::rollback();
            return $this->successResponse(200, $e->getMessage(), null, 0);
        }
        DB::commit();
        return $this->successResponse(200, 'Transfer Successfull !', null, 1);
    }

    public function postRtsDispatchList($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', $user_map, 0);
        }
        $dataSet = DB::table("INV_STOCK")
            ->select('v.PK_NO','INV_STOCK.SKUID as sku_id','INV_STOCK.PRD_VARINAT_NAME as product_name','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','INV_STOCK.BARCODE as barcode','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','v.PRIMARY_IMG_RELATIVE_PATH as primary_image','SLS_BATCH_LIST.RTS_BATCH_NO as batch_no','SLS_ORDER.PK_NO as order_id','SLS_BOOKING_DETAILS.RTS_COLLECTION_USER_ID'
            ,DB::raw('IFNULL(w.DESCRIPTION,"Product is in landing area") as location')
            ,DB::raw('IFNULL(INV_STOCK.INV_ZONE_BARCODE,"label") as label')
            ,DB::raw('IFNULL(COUNT(INV_STOCK.PK_NO),0) as qty')
            )
            ->leftJoin('SLS_BOOKING_DETAILS','INV_STOCK.PK_NO','SLS_BOOKING_DETAILS.F_INV_STOCK_NO')
            ->leftJoin('SLS_ORDER','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING_DETAILS.F_BOOKING_NO')
            ->leftjoin('SLS_BATCH_LIST','SLS_BATCH_LIST.PK_NO','SLS_ORDER.PICKUP_ID')
            ->leftjoin('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 'INV_STOCK.IG_CODE')
            ->leftjoin('INV_WAREHOUSE_ZONES as w', 'w.PK_NO', 'INV_STOCK.F_INV_ZONE_NO')
            // ->whereIn('SLS_ORDER.DISPATCH_STATUS',[30,20])
            // ->where('SLS_ORDER.IS_SELF_PICKUP',0)
            ->where('SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS',0)
            ->where('SLS_BOOKING_DETAILS.RTS_COLLECTION_USER_ID',$request->user_id)
            ->where('SLS_BATCH_LIST.RTS_BATCH_NO',$request->batch_no)
            // ->where('INV_STOCK.PRODUCT_STATUS','>=',60)
            ->where('SLS_ORDER.DISPATCH_STATUS', '<', '40')
            ->groupBy('INV_STOCK.IG_CODE','INV_STOCK.INV_ZONE_BARCODE')
            ->orderBy('INV_STOCK.PRD_VARINAT_NAME','ASC')
            ->get();
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($dataSet);
        // echo '<br>======================<br>';
        // exit();
        if (count($dataSet)>0) {
            return $this->successResponse(200, 'Data found !', $dataSet, 1);
        }

        return $this->successResponse(200, 'Data not found !', null, 0);
    }

    public function postRtsDispatchedItemList($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', $user_map, 0);
        }
        $dataSet = DB::table("INV_STOCK")
            ->select('INV_STOCK.SKUID as sku_id','v.PK_NO','INV_STOCK.PRD_VARINAT_NAME as product_name','v.VARIANT_NAME as product_variant_name','v.SIZE_NAME as size','v.COLOR as color','INV_STOCK.BARCODE as barcode','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','v.PRIMARY_IMG_RELATIVE_PATH as primary_image','SLS_BATCH_LIST.RTS_BATCH_NO as batch_no','SLS_ORDER.PK_NO as order_id','SLS_BOOKING_DETAILS.RTS_COLLECTION_USER_ID'
            ,DB::raw('IFNULL(w.DESCRIPTION,"Product is in landing area") as location')
            ,DB::raw('IFNULL(INV_STOCK.INV_ZONE_BARCODE,"label") as label')
            ,DB::raw('IFNULL(COUNT(INV_STOCK.PK_NO),0) as available_qty')
            ,DB::raw('(select
            IFNULL(COUNT(INV_STOCK.PK_NO),0) as qty from `INV_STOCK`
            left join `SLS_BOOKING_DETAILS` on `INV_STOCK`.`PK_NO` = `SLS_BOOKING_DETAILS`.`F_INV_STOCK_NO`
            left join `SLS_ORDER` on `SLS_ORDER`.`F_BOOKING_NO` = `SLS_BOOKING_DETAILS`.`F_BOOKING_NO`
            LEFT JOIN SLS_BATCH_LIST ON SLS_ORDER.PICKUP_ID = SLS_BATCH_LIST.PK_NO
            left join `PRD_VARIANT_SETUP` as `v` on `v`.`MRK_ID_COMPOSITE_CODE` = `INV_STOCK`.`IG_CODE`
            left join `INV_WAREHOUSE_ZONES` as `w` on `w`.`PK_NO` = `INV_STOCK`.`F_INV_ZONE_NO`
            and SLS_BATCH_LIST.RTS_BATCH_NO = '.$request->batch_no.'
            and `SLS_ORDER`.`DISPATCH_STATUS` < 40 ) as total_qty')
            )
            ->leftJoin('SLS_BOOKING_DETAILS','INV_STOCK.PK_NO','SLS_BOOKING_DETAILS.F_INV_STOCK_NO')
            ->leftJoin('SLS_ORDER','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING_DETAILS.F_BOOKING_NO')
            ->leftjoin('SLS_BATCH_LIST','SLS_BATCH_LIST.PK_NO','SLS_ORDER.PICKUP_ID')
            ->leftjoin('PRD_VARIANT_SETUP as v', 'v.MRK_ID_COMPOSITE_CODE', 'INV_STOCK.IG_CODE')
            ->leftjoin('INV_WAREHOUSE_ZONES as w', 'w.PK_NO', 'INV_STOCK.F_INV_ZONE_NO')
            // ->whereIn('SLS_ORDER.DISPATCH_STATUS',[30,20])
            // ->where('SLS_ORDER.IS_SELF_PICKUP',0)
            ->where('SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS',1)
            // ->where('SLS_BOOKING_DETAILS.RTS_COLLECTION_USER_ID',$request->user_id)
            ->where('SLS_BATCH_LIST.RTS_BATCH_NO',$request->batch_no)
            // ->where('INV_STOCK.PRODUCT_STATUS','>=',60)
            ->where('SLS_ORDER.DISPATCH_STATUS', '<', '40')
            ->groupBy('INV_STOCK.IG_CODE','INV_STOCK.INV_ZONE_BARCODE')
            ->orderBy('SLS_ORDER.PICKUP_ID','ASC')
            ->get();
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($dataSet);
        // echo '<br>======================<br>';
        // exit();
        if (count($dataSet)>0) {
            return $this->successResponse(200, 'Data found !', $dataSet, 1);
        }

        return $this->successResponse(200, 'Data not found !', null, 0);
    }

    public function postRtsBatchList($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', $user_map, 0);
        }
        $data = DB::table('SLS_ORDER as o')
        ->select('bl.RTS_BATCH_NO as batch_no'
        ,DB::raw('(
            SELECT IFNULL(COUNT(INV_STOCK.PK_NO),0)
            FROM INV_STOCK
            LEFT JOIN SLS_BOOKING_DETAILS ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
            LEFT JOIN SLS_ORDER ON SLS_BOOKING_DETAILS.F_BOOKING_NO = SLS_ORDER.F_BOOKING_NO
            LEFT JOIN SLS_BATCH_LIST ON SLS_ORDER.PICKUP_ID = SLS_BATCH_LIST.PK_NO
            WHERE SLS_BATCH_LIST.RTS_BATCH_NO = batch_no
            and SLS_ORDER.DISPATCH_STATUS < 40
            and SLS_BOOKING_DETAILS.RTS_COLLECTION_USER_ID = '.$request->user_id.'
            and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 0) as product_count')
        // ,DB::raw('(SELECT IFNULL(COUNT(SLS_ORDER.PK_NO),0)
        // FROM SLS_ORDER WHERE SLS_BATCH_LIST.RTS_BATCH_NO = batch_no and SLS_ORDER.DISPATCH_STATUS < 40 ) as order_count')
        )
        ->join('SLS_BOOKING_DETAILS as bd','bd.F_BOOKING_NO','o.F_BOOKING_NO')
        ->join('SLS_BATCH_LIST as bl','bl.PK_NO','o.PICKUP_ID')
        ->where('bd.RTS_COLLECTION_USER_ID',$request->user_id)
        ->where('bd.IS_COLLECTED_FOR_RTS',0)
        ->where('o.DISPATCH_STATUS','<',40)
        ->where('o.PICKUP_ID','>',0)
        ->groupBy('batch_no')
        ->get();
        if (count($data)>0) {
            return $this->successResponse(200, 'Data found !', $data, 1);
        }
        return $this->successResponse(200, 'Data not found !', null, 0);
    }

    public function postRtsDispatchedList($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', $user_map, 0);
        }
        $data = DB::table('SLS_ORDER as o')
        ->select('bl.RTS_BATCH_NO as batch_no'
        ,DB::raw('(
            SELECT IFNULL(COUNT(INV_STOCK.PK_NO),0)
            FROM INV_STOCK
            LEFT JOIN SLS_BOOKING_DETAILS ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
            LEFT JOIN SLS_ORDER ON SLS_BOOKING_DETAILS.F_BOOKING_NO = SLS_ORDER.F_BOOKING_NO
            LEFT JOIN SLS_BATCH_LIST ON SLS_ORDER.PICKUP_ID = SLS_BATCH_LIST.PK_NO
            WHERE SLS_BATCH_LIST.RTS_BATCH_NO = batch_no
            and SLS_ORDER.DISPATCH_STATUS < 40
            and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 1) as product_count')
        ,DB::raw('(
            SELECT IFNULL(COUNT(INV_STOCK.PK_NO),0)
            FROM INV_STOCK
            LEFT JOIN SLS_BOOKING_DETAILS ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
            LEFT JOIN SLS_ORDER ON SLS_BOOKING_DETAILS.F_BOOKING_NO = SLS_ORDER.F_BOOKING_NO
            LEFT JOIN SLS_BATCH_LIST ON SLS_ORDER.PICKUP_ID = SLS_BATCH_LIST.PK_NO
            WHERE SLS_BATCH_LIST.RTS_BATCH_NO = batch_no
            and SLS_ORDER.DISPATCH_STATUS < 40
            ) as total_count')
        // ,DB::raw('(SELECT IFNULL(COUNT(SLS_ORDER.PK_NO),0)
        // FROM SLS_ORDER WHERE SLS_BATCH_LIST.RTS_BATCH_NO = batch_no and SLS_ORDER.DISPATCH_STATUS < 40 ) as order_count')
        )
        ->join('SLS_BOOKING_DETAILS as bd','bd.F_BOOKING_NO','o.F_BOOKING_NO')
        ->join('SLS_BATCH_LIST as bl','bl.PK_NO','o.PICKUP_ID')
        // ->where('bd.RTS_COLLECTION_USER_ID',$request->user_id)
        // ->where('bd.IS_COLLECTED_FOR_RTS',0)
        ->where('o.DISPATCH_STATUS','<',40)
        ->where('o.PICKUP_ID','>',0)
        ->groupBy('batch_no')
        ->get();
        if (count($data)>0) {
            return $this->successResponse(200, 'Data found !', $data, 1);
        }
        return $this->successResponse(200, 'Data not found !', null, 0);
    }

    public function postProductOfTrackingNo($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', $user_map, 0);
        }
        $data2 = DB::table('SC_ORDER_CONSIGNMENT as oc')
        ->select('v.PK_NO'
        ,'v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','i.PRD_VARINAT_NAME as product_name','v.SIZE_NAME as size','v.COLOR as color','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','i.PRD_VARIANT_IMAGE_PATH as primary_image','SLS_BOOKING.BOOKING_NOTES','i.F_ORDER_NO as order_id','bd.IS_COLLECTED_FOR_RTS'
        ,DB::raw('IFNULL(w.DESCRIPTION,"Product is in landing area") as location')
        ,DB::raw('IFNULL(i.INV_ZONE_BARCODE,"land") as label')
        ,DB::raw('(IFNULL(COUNT(odd.PK_NO),0)) as qty')
        ,DB::raw('(SELECT IFNULL(COUNT(SLS_BOOKING_DETAILS.PK_NO),0)
        FROM INV_STOCK
        inner join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
        inner join SC_ORDER_DISPATCH_DETAILS on SC_ORDER_DISPATCH_DETAILS.F_BOOKING_DETAILS_NO = SLS_BOOKING_DETAILS.PK_NO
        where SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 1
        and INV_STOCK.SKUID = sku_id
        and SC_ORDER_DISPATCH_DETAILS.COURIER_TRACKING_NO = "'.$request->consignment_label.'"
        group by SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS,INV_STOCK.F_INV_ZONE_NO,INV_STOCK.SKUID) as picked_qty')
        )
        ->join('SC_ORDER_DISPATCH_DETAILS as odd','oc.COURIER_TRACKING_NO','odd.COURIER_TRACKING_NO')
        ->join('SC_ORDER_DISPATCH as od','odd.F_SC_ORDER_DISPATCH_NO','od.PK_NO')
        ->join('SLS_BOOKING_DETAILS as bd','bd.PK_NO','odd.F_BOOKING_DETAILS_NO')
        ->join('INV_STOCK as i','bd.F_INV_STOCK_NO','i.PK_NO')
        ->leftjoin('INV_WAREHOUSE_ZONES as w', 'w.PK_NO', 'i.F_INV_ZONE_NO')
        ->join('PRD_VARIANT_SETUP as v', 'v.PK_NO', 'i.F_PRD_VARIANT_NO')
        ->join('SLS_BOOKING', 'SLS_BOOKING.PK_NO', 'bd.F_BOOKING_NO')
        // ->where('od.IS_DISPATHED','!=',1)
        ->where('odd.IS_DISPATHED',0)
        ->where('bd.IS_COLLECTED_FOR_RTS',1)
        ->where('odd.COURIER_TRACKING_NO',$request->consignment_label)
        ->groupBy('sku_id');
        // ->get();

        $data = DB::table('SC_ORDER_CONSIGNMENT as oc')
        ->select('v.PK_NO'
        ,'v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','i.PRD_VARINAT_NAME as product_name','v.SIZE_NAME as size','v.COLOR as color','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','i.PRD_VARIANT_IMAGE_PATH as primary_image','SLS_BOOKING.BOOKING_NOTES','i.F_ORDER_NO as order_id','bd.IS_COLLECTED_FOR_RTS'
        ,DB::raw('IFNULL(w.DESCRIPTION,"Product is in landing area") as location')
        ,DB::raw('IFNULL(i.INV_ZONE_BARCODE,"land") as label')
        ,DB::raw('(IFNULL(COUNT(odd.PK_NO),0)) as qty')
        ,DB::raw('0 as picked_qty')
        // ,DB::raw('(SELECT IFNULL(COUNT(SLS_BOOKING_DETAILS.PK_NO),0)
        // FROM INV_STOCK
        // inner join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
        // inner join SC_ORDER_DISPATCH_DETAILS on SC_ORDER_DISPATCH_DETAILS.F_BOOKING_DETAILS_NO = SLS_BOOKING_DETAILS.PK_NO
        // where SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 0
        // and INV_STOCK.SKUID = sku_id
        // and SC_ORDER_DISPATCH_DETAILS.COURIER_TRACKING_NO = "'.$request->consignment_label.'" ) as picked_qty')
        )
        ->join('SC_ORDER_DISPATCH_DETAILS as odd','oc.COURIER_TRACKING_NO','odd.COURIER_TRACKING_NO')
        ->join('SC_ORDER_DISPATCH as od','odd.F_SC_ORDER_DISPATCH_NO','od.PK_NO')
        ->join('SLS_BOOKING_DETAILS as bd','bd.PK_NO','odd.F_BOOKING_DETAILS_NO')
        ->join('INV_STOCK as i','bd.F_INV_STOCK_NO','i.PK_NO')
        ->leftjoin('INV_WAREHOUSE_ZONES as w', 'w.PK_NO', 'i.F_INV_ZONE_NO')
        ->join('PRD_VARIANT_SETUP as v', 'v.PK_NO', 'i.F_PRD_VARIANT_NO')
        ->join('SLS_BOOKING', 'SLS_BOOKING.PK_NO', 'bd.F_BOOKING_NO')
        // ->where('od.IS_DISPATHED','!=',1)
        ->where('odd.IS_DISPATHED',0)
        ->where('bd.IS_COLLECTED_FOR_RTS',0)
        ->where('odd.COURIER_TRACKING_NO',$request->consignment_label)
        ->groupBy('sku_id','i.F_INV_ZONE_NO','bd.IS_COLLECTED_FOR_RTS')
        ->UNION($data2)
        ->get();
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($data);
        // echo '<br>======================<br>';
        // exit();
        foreach ($data as $key => $value) {
            if ($value->qty == $value->picked_qty && $value->IS_COLLECTED_FOR_RTS == 1) {
                $value->isPicked = 1;
            }else{
                $value->isPicked = 0;
            }
        }
        if (isset($data) && !empty($data) && count($data)>0) {
            return $this->successResponse(200, 'Data found !', $data, 1,$data[0]->BOOKING_NOTES ?? '');
        }
        return $this->successResponse(200, 'Data not found !', null, 0);
    }

    public function postDispatch($request)
    {
        $request = $request->json()->all();

        // return $this->successResponse(200, 'Work in progress !', null, 1);

        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request['user_id'])->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', $user_map, 0);
        }
        $data = DB::table('SC_ORDER_CONSIGNMENT as oc')
        ->select('odd.PK_NO','i.BARCODE as barcode'
        )
        ->join('SC_ORDER_DISPATCH_DETAILS as odd','oc.COURIER_TRACKING_NO','odd.COURIER_TRACKING_NO')
        ->join('SC_ORDER_DISPATCH as od','odd.F_SC_ORDER_DISPATCH_NO','od.PK_NO')
        ->join('SLS_BOOKING_DETAILS as bd','bd.PK_NO','odd.F_BOOKING_DETAILS_NO')
        ->join('INV_STOCK as i','bd.F_INV_STOCK_NO','i.PK_NO')
        ->where('bd.IS_COLLECTED_FOR_RTS',1)
        // ->where('od.IS_DISPATHED','!=',1)
        ->where('odd.IS_DISPATHED',0)
        ->where('odd.COURIER_TRACKING_NO',$request['consignment_label'])
        ->groupBy('barcode')
        ->get();
        DB::beginTransaction();
        try {
            if (count($data) == count($request['data'])) {
                $dispatch_pk = DispatchDetails::select('F_SC_ORDER_DISPATCH_NO')->where('COURIER_TRACKING_NO',$request['consignment_label'])->where('IS_DISPATHED',0)->first();

                $consignment_order = Dispatch::select('F_ORDER_NO')->whereRaw('(IS_DISPATHED = 0 OR IS_DISPATHED = 2)')->where('PK_NO',$dispatch_pk->F_SC_ORDER_DISPATCH_NO)->first();

                DispatchDetails::where('COURIER_TRACKING_NO',$request['consignment_label'])->where('IS_DISPATHED',0)->update(['IS_DISPATHED'=>1]);

                $dispatch_details = DispatchDetails::where('IS_DISPATHED',0)->where('F_SC_ORDER_DISPATCH_NO',$dispatch_pk->F_SC_ORDER_DISPATCH_NO)->count();

                $username = Auth::select('USERNAME')->where('PK_NO',$request['user_id'])->first();

                if ($dispatch_details > 0) {
                    Dispatch::where('PK_NO',$dispatch_pk->F_SC_ORDER_DISPATCH_NO)->whereRaw('(IS_DISPATHED = 0 OR IS_DISPATHED = 2)')->update(['IS_DISPATHED'=>2,'DISPATCH_USER_NAME'=>$username->USERNAME,'F_DISPATCH_BY_USER_NO'=>$request['user_id']]);
                }else{
                    Dispatch::where('PK_NO',$dispatch_pk->F_SC_ORDER_DISPATCH_NO)->whereRaw('(IS_DISPATHED = 0 OR IS_DISPATHED = 2)')->update(['IS_DISPATHED'=>1,'DISPATCH_USER_NAME'=>$username->USERNAME,'F_DISPATCH_BY_USER_NO'=>$request['user_id']]);
                }
            }elseif(count($data) > count($request['data'])){
                return $this->successResponse(200, 'Please collect remaining product !', null, 0);
            }else{
                return $this->successResponse(200, 'Please try again !', null, 0);
            }

            $order = Order::select('F_BOOKING_NO','IS_SELF_PICKUP','IS_RESELLER','F_CUSTOMER_NO','F_RESELLER_NO')
                            ->where('PK_NO',$consignment_order->F_ORDER_NO)
                            ->first();

            if($order->IS_SELF_PICKUP == 0){
                $tracking_info = $request['consignment_label'];
                $sms_body = "RM0.00 AZURAMART: Order #ORD-".$order->booking->BOOKING_NO." has dispatched, ".$tracking_info.", for more info please Whatsapp http://linktr.ee/azuramart";
            }else{
                $sms_body = "RM0.00 AZURAMART: Order #ORD-".$order->booking->BOOKING_NO." has dispatched, for more info please Whatsapp http://linktr.ee/azuramart";
            }

            $noti = new SmsNotification();
            $noti->TYPE = 'Dispatch';
            $noti->F_BOOKING_NO = $order->F_BOOKING_NO;
            //$noti->F_BOOKING_DETAIL_NO = $value->PK_NO;
            $noti->BODY = $sms_body;
            $noti->F_SS_CREATED_BY = $request['user_id'];
            if($order->IS_RESELLER == 0){
                $noti->CUSTOMER_NO = $order->F_CUSTOMER_NO;
                $noti->IS_RESELLER = 0;
            }else{
                $noti->RESELLER_NO = $order->F_RESELLER_NO;
                $noti->IS_RESELLER = 1;
            }
            $noti->save();

            $email = new EmailNotification();
            $email->TYPE = 'Dispatch';
            $email->F_BOOKING_NO = $order->F_BOOKING_NO;
            $email->F_SS_CREATED_BY = $request['user_id'];
            if($order->IS_RESELLER == 0){
                $email->CUSTOMER_NO = $order->F_CUSTOMER_NO;
                $email->IS_RESELLER = 0;
            }else{
                $email->RESELLER_NO = $order->F_RESELLER_NO;
                $email->IS_RESELLER = 1;
            }
            $email->save();

        } catch (\Exeption $e) {
            DB::rollback();
            return $this->successResponse(200, $e->getMessage(), null, 0);
        }
        DB::commit();
        return $this->successResponse(200, 'Dispatch Successfull !', null, 1);
    }

    public function postCOnsignmentList($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', $user_map, 0);
        }
        DB::beginTransaction();
        try {
            $data = DB::table('SC_ORDER_CONSIGNMENT as c')
                        ->select('c.F_ORDER_NO','c.COURIER_TRACKING_NO as consignment_label'
                        ,DB::raw('(IFNULL(COUNT(c.COURIER_TRACKING_NO),0)) as product_count')
                        // ,DB::raw('(IFNULL(COUNT(c.COURIER_TRACKING_NO),0)) as picked_count')
                        ,DB::raw('(SELECT IFNULL(COUNT(SLS_BOOKING_DETAILS.PK_NO),0) FROM SLS_BOOKING_DETAILS inner join SC_ORDER_DISPATCH_DETAILS on SC_ORDER_DISPATCH_DETAILS.F_BOOKING_DETAILS_NO = SLS_BOOKING_DETAILS.PK_NO where SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 1 and SC_ORDER_DISPATCH_DETAILS.COURIER_TRACKING_NO = consignment_label) as picked_count')
                        )
                        ->join('SC_ORDER_DISPATCH_DETAILS as dd','dd.COURIER_TRACKING_NO','c.COURIER_TRACKING_NO')
                        ->join('SC_ORDER_DISPATCH as od','od.PK_NO','dd.F_SC_ORDER_DISPATCH_NO')
                        ->where('od.IS_DISPATHED','!=',1)
                        ->where('dd.IS_DISPATHED','!=',1)
                        ->groupBy('c.COURIER_TRACKING_NO')
                        ->get();
        } catch (\Exeption $e) {
            DB::rollback();
            return $this->successResponse(200, $e->getMessage(), null, 0);
        }
        DB::commit();
        if (isset($data) && !empty($data) && count($data)>0) {
            return $this->successResponse(200, 'Data found !', $data, 1);
        }
        return $this->successResponse(200, 'Data Not Found !', null, 0);
    }

    public function postCodRtsZone($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', $user_map, 0);
        }
        DB::beginTransaction();
        try {
            $secondarty_user = Auth::select('F_PARENT_USER_ID')->where('PK_NO',$request->user_id)->first();
            $agent_zone = PaymentBank::select('BANK_ACC_NAME','F_INV_ZONE_NO')->where('F_USER_NO',$secondarty_user->F_PARENT_USER_ID)->first();
            if (!empty($agent_zone)) {
                $data = PaymentBank::join('INV_STOCK as i','i.F_INV_ZONE_NO','ACC_PAYMENT_BANK_ACC.F_INV_ZONE_NO')
                                    ->join('PRD_VARIANT_SETUP as v', 'v.PK_NO', 'i.F_PRD_VARIANT_NO')
                                    ->join('SLS_BOOKING_DETAILS as bd', 'i.PK_NO', 'bd.F_INV_STOCK_NO')
                                    ->select('v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','i.PRD_VARINAT_NAME as product_name','v.SIZE_NAME as size','v.COLOR as color','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','i.PRD_VARIANT_IMAGE_PATH as primary_image','bd.COD_RTC_ACK as is_acknowledge','i.F_ORDER_NO as order_id'
                                    ,DB::raw('(IFNULL(COUNT(i.PK_NO),0)) as qty'))
                                    ->where('bd.DISPATCH_STATUS','<',40)
                                    ->where('ACC_PAYMENT_BANK_ACC.F_USER_NO',$secondarty_user->F_PARENT_USER_ID)
                                    ->groupBy('i.SKUID','is_acknowledge')
                                    ->orderBy('is_acknowledge','ASC')
                                    ->get();
            }else{
                return $this->successResponse(200, 'Agent not found !', null, 0);
            }
        } catch (\Exeption $e) {
            DB::rollback();
            return $this->successResponse(200, $e->getMessage(), null, 0);
        }
        DB::commit();
        if (isset($data) && !empty($data) && count($data)>0) {
            return $this->successResponse(200, 'Data found !', $data, 1);
        }
        return $this->successResponse(200, 'Data Not Found !', null, 0);
    }

    public function postCodRtcDispatchItem($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', $user_map, 0);
        }

        DB::beginTransaction();
        try {
            $secondarty_user = Auth::select('F_PARENT_USER_ID')->where('PK_NO',$request->user_id)->first();
            $agent_zone = PaymentBank::select('BANK_ACC_NAME','F_INV_ZONE_NO')->where('F_USER_NO',$secondarty_user->F_PARENT_USER_ID)->first();

            if (!empty($agent_zone)) {
                $data = PaymentBank::select('v.PK_NO','v.COMPOSITE_CODE as sku_id','v.BARCODE as barcode','v.MRK_ID_COMPOSITE_CODE as mkt_id','v.VARIANT_NAME as product_variant_name','i.PRD_VARINAT_NAME as product_name','v.SIZE_NAME as size','v.COLOR as color','v.PRIMARY_IMG_RELATIVE_PATH as variant_primary_image','i.PRD_VARIANT_IMAGE_PATH as primary_image','bd.COD_RTC_ACK as acknowledge','wz.DESCRIPTION','i.INV_ZONE_BARCODE','i.PRODUCT_STATUS','i.BOX_BARCODE'
                                    ,DB::raw('(IFNULL(COUNT(i.PK_NO),0)) as qty')
                                    ,DB::raw('IFNULL(wz.DESCRIPTION,"Product is in MY Warehouse landing area") as location')
                                    ,DB::raw('IFNULL(i.INV_ZONE_BARCODE,"label") as label')
                                    ,DB::raw('(CASE
                                        WHEN ACC_PAYMENT_BANK_ACC.F_INV_ZONE_NO = i.F_INV_ZONE_NO THEN 0
                                        ELSE 1
                                        END) AS is_my')
                                    )
                                    ->join('SLS_ORDER_RTC as rtc','rtc.F_ACC_PAYMENT_BANK_NO','ACC_PAYMENT_BANK_ACC.PK_NO')
                                    ->join('SLS_ORDER as o', 'o.PK_NO', 'rtc.F_ORDER_NO')
                                    ->join('SLS_BOOKING_DETAILS as bd', 'bd.F_BOOKING_NO', 'o.F_BOOKING_NO')
                                    ->join('INV_STOCK as i','i.PK_NO','bd.F_INV_STOCK_NO')
                                    ->join('PRD_VARIANT_SETUP as v', 'v.PK_NO', 'i.F_PRD_VARIANT_NO')
                                    ->leftjoin('INV_WAREHOUSE_ZONES as wz', 'wz.PK_NO', 'i.F_INV_ZONE_NO')
                                    ->where('bd.DISPATCH_STATUS','<',40)
                                    // ->where('bd.IS_READY',1)
                                    ->where('ACC_PAYMENT_BANK_ACC.F_USER_NO',$secondarty_user->F_PARENT_USER_ID)
                                    ->where('o.PK_NO',$request->order_id)
                                    ->groupBy('i.SKUID','i.PRODUCT_STATUS','i.F_INV_ZONE_NO')
                                    ->get();
                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        if ($value->PRODUCT_STATUS == 50) {
                            $value->location = 'Yet to unbox - '.$value->BOX_BARCODE;
                        }
                    }
                }
                $order = Order::select('SLS_ORDER.PK_NO as order_no','SLS_ORDER.DELIVERY_NAME as customer_name','SLS_ORDER.DELIVERY_MOBILE as customer_mobile','SLS_BOOKING.TOTAL_PRICE as total_amount','SLS_BOOKING.DISCOUNT','SLS_ORDER.ORDER_BUFFER_TOPUP','SLS_ORDER.ORDER_BALANCE_RETURN','SLS_ORDER.ORDER_BALANCE_USED as paid_amount','SLS_ORDER.ORDER_ACTUAL_TOPUP','SLS_BOOKING.BOOKING_NO')
                                ->join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')
                                ->where('SLS_ORDER.PK_NO',$request->order_id)
                                ->first();

                $due_amount = ($order->total_amount ?? 0) - ($order->DISCOUNT ?? 0) - (($order->ORDER_BUFFER_TOPUP ?? 0) - ($order->ORDER_BALANCE_RETURN ?? 0));
                $amount_paid = ($order->ORDER_ACTUAL_TOPUP ?? 0) - ($order->ORDER_BALANCE_RETURN ?? 0);
                // $customer_mobile_dial = $order->to_country->DIAL_CODE;
            }else{
                return $this->successResponse(200, 'Data not found !', null, 0);
            }
        } catch (\Exeption $e) {
            DB::rollback();
            return $this->successResponse(200, $e->getMessage(), null, 0);
        }
        DB::commit();
        if (isset($data) && !empty($data) && count($data)>0) {
            return (object) array(
                'status'            => 1,
                'success'           => true,
                'code'              => 200,
                'message'           => 'Data found !',
                'description'       => '',
                'customer_name'     => $order->customer_name,
                'customer_mobile'   => $order->customer_mobile,
                'order_no'          => $order->order_no,
                'total_amount'      => $order->total_amount,
                'paid_amount'       => $amount_paid,
                'due_amount'        => $due_amount,
                'slip_no'           => 'CASH-'.$order->BOOKING_NO,
                'data'              => $data,
                'errors'            => null,
                'api'               => ['version' => '1.0']
            );
        }
        return $this->successResponse(200, 'Data Not Found !', null, 0);
    }

    public function postCodRtcOrderList($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        DB::beginTransaction();
        try {
            $secondarty_user = Auth::select('F_PARENT_USER_ID')->where('PK_NO',$request->user_id)->first();
            $agent_zone = PaymentBank::select('BANK_ACC_NAME','F_INV_ZONE_NO')->where('F_USER_NO',$secondarty_user->F_PARENT_USER_ID)->first();

            if (!empty($agent_zone)) {
                $data = PaymentBank::select('o.PK_NO as order_id','o.DELIVERY_NAME as customer_name','o.DELIVERY_MOBILE as customer_mobile','o.DELIVERY_F_COUNTRY_NO'
                                    ,DB::raw('(SELECT IFNULL(COUNT(bd.PK_NO),0)
                                    from `ACC_PAYMENT_BANK_ACC`
                                    inner join `SLS_ORDER_RTC` as `rtc` on `rtc`.`F_ACC_PAYMENT_BANK_NO` = `ACC_PAYMENT_BANK_ACC`.`PK_NO`
                                    inner join `SLS_ORDER` as `o` on `o`.`PK_NO` = `rtc`.`F_ORDER_NO`
                                    inner join `SLS_BOOKING_DETAILS` as `bd` on `rtc`.`F_BOOKING_NO` = `bd`.`F_BOOKING_NO`
                                    where `o`.`DISPATCH_STATUS` < 40
                                    and `ACC_PAYMENT_BANK_ACC`.`F_USER_NO` = '.$secondarty_user->F_PARENT_USER_ID.'
                                    and o.PK_NO = order_id) as product_count')
                                    )
                                    ->join('SLS_ORDER_RTC as rtc','rtc.F_ACC_PAYMENT_BANK_NO','ACC_PAYMENT_BANK_ACC.PK_NO')
                                    ->join('SLS_ORDER as o', 'o.PK_NO', 'rtc.F_ORDER_NO')
                                    ->join('SLS_BOOKING_DETAILS as bd', 'rtc.F_BOOKING_NO', 'bd.F_BOOKING_NO')
                                    ->where('o.DISPATCH_STATUS','<',40)
                                    ->where('o.IS_READY',1)
                                    ->where('ACC_PAYMENT_BANK_ACC.F_USER_NO',$secondarty_user->F_PARENT_USER_ID)
                                    ->groupBy('o.PK_NO')
                                    ->get();
                // echo '<pre>';
                // echo '======================<br>';
                // print_r($data);
                // echo '<br>======================<br>';
                // exit();
            }else{
                return $this->successResponse(200, 'Data not found !', null, 0);
            }
        } catch (\Exeption $e) {
            DB::rollback();
            return $this->successResponse(200, $e->getMessage(), null, 0);
        }
        DB::commit();
        if (isset($data) && !empty($data) && count($data)>0) {

            return $this->successResponse(200, 'Data found !', $data, 1);
        }
        return $this->successResponse(200, 'Data Not Found !', null, 0);
    }

    public function postCodRtcDispatch($request)
    {
        $request = $request->json()->all();

        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request['user_id'])->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        DB::beginTransaction();
        try {
            //PAYMENT COLLECT
            $order = Order::select('SLS_ORDER.*','SLS_BOOKING.TOTAL_PRICE','SLS_BOOKING.DISCOUNT','SLS_BOOKING.BOOKING_NO')
                            ->join('SLS_BOOKING','SLS_BOOKING.PK_NO','SLS_ORDER.F_BOOKING_NO')
                            ->where('SLS_ORDER.PK_NO',$request['order_id'])->first();

            $due_amount = ($order->TOTAL_PRICE ?? 0) - ($order->DISCOUNT ?? 0) - (($order->ORDER_BUFFER_TOPUP ?? 0) - ($order->ORDER_BALANCE_RETURN ?? 0));
            $payment_acc_no = OrderRtc::select('F_ACC_PAYMENT_BANK_NO','BANK_ACC_NAME')->where('F_ORDER_NO',$request['order_id'])->first();

            if (isset($order->F_CUSTOMER_NO)) {
                $payment                            = new PaymentCustomer();
                $payment->F_CUSTOMER_NO             = $order->F_CUSTOMER_NO;
                $payment->F_PAYMENT_CURRENCY_NO     = $request['payment_currency_no'] ?? 2;
                $payment->PAYMENT_DATE              = date('Y-m-d',strtotime($request['collect_date']));
                $payment->MR_AMOUNT                 = $due_amount;
                $payment->F_PAYMENT_ACC_NO          = $payment_acc_no->F_ACC_PAYMENT_BANK_NO;
                // $payment->PAYMENT_NOTE              = $request['payment_note'];
                $payment->PAID_BY                   = $request['collected_by'] ?? null;
                $payment->SLIP_NUMBER               = 'CASH-'.$order->BOOKING_NO;
            }else{
                $payment                            = new PaymentReseller();
                $payment->F_RESELLER_NO             = $order->F_RESELLER_NO;
                $payment->F_PAYMENT_CURRENCY_NO     = $request['payment_currency_no'] ?? 2;
                $payment->PAYMENT_DATE              = date('Y-m-d',strtotime($request['collect_date']));
                $payment->MR_AMOUNT                 = $due_amount;
                $payment->F_PAYMENT_ACC_NO          = $payment_acc_no->F_ACC_PAYMENT_BANK_NO;
                // $payment->PAYMENT_NOTE              = $request['payment_note'];
                $payment->PAID_BY                   = $request['collected_by'] ?? null;
                $payment->SLIP_NUMBER               = 'CASH-'.$order->BOOKING_NO;
            }
            $payment->IS_COD = 1;
            $payment->PAYMENT_CONFIRMED_STATUS  = 1;
            $payment->setApiAuthId($request['user_id']);
            $payment->save();

            $pay_pk_no = $payment->PK_NO;

            $order_pay              = new OrderPayment();
            $order_pay->ORDER_NO    = $request['order_id'];
            $order_pay->CUSTOMER_NO = $order->F_CUSTOMER_NO ?? $order->F_RESELLER_NO;
            $order_pay->IS_CUSTOMER = $order->IS_RESELLER == 0 ? 1 : 0;
            $order_pay->F_ACC_CUSTOMER_PAYMENT_NO = $pay_pk_no;
            $order_pay->PAYMENT_AMOUNT = $due_amount;
            $order_pay->IS_PAYMENT_FROM_BALANCE = 0;
            $order_pay->save();

            $txn = new AccBankTxn();
            $txn->TXN_TYPE_IN_OUT = 1;
            $txn->TXN_DATE = date('Y-m-d',strtotime($request['collect_date']));
            $txn->AMOUNT_ACTUAL = $due_amount;
            $txn->AMOUNT_BUFFER = $due_amount;
            $txn->IS_CUS_RESELLER_BANK_RECONCILATION = 1;
            $txn->F_ACC_PAYMENT_BANK_NO = $payment_acc_no->F_ACC_PAYMENT_BANK_NO;
            $txn->F_CUSTOMER_NO = $order->F_CUSTOMER_NO ?? $order->F_RESELLER_NO;
            $txn->F_CUSTOMER_PAYMENT_NO = $pay_pk_no;
            $txn->IS_MATCHED = 1;
            $txn->MATCHED_ON = date('Y-m-d H:i:s');
            $txn->IS_COD = 1;
            $txn->save();

            $username = Auth::select('USERNAME')->where('PK_NO',$request['user_id'])->first();
            //COD RTC ORDER DISPATCH
            $dispatch                       = new Dispatch();
            $dispatch->F_ORDER_NO           = $order->PK_NO;
            $dispatch->F_DISPATCH_BY_USER_NO= $request['user_id'];
            $dispatch->DISPATCH_USER_NAME   = $username->USERNAME;
            $dispatch->DISPATCH_DATE        = date('Y-m-d',strtotime($request['collect_date']));
            $dispatch->FROM_NAME            = $order->FROM_NAME;
            $dispatch->FROM_TEL_NO          = $order->FROM_MOBILE;
            $dispatch->FROM_ADDRESS_LINE_1  = $order->FROM_ADDRESS_LINE_1;
            $dispatch->FROM_ADDRESS_LINE_2  = $order->FROM_ADDRESS_LINE_2;
            $dispatch->FROM_ADDRESS_LINE_3  = $order->FROM_ADDRESS_LINE_3;
            $dispatch->FROM_ADDRESS_LINE_4  = $order->FROM_ADDRESS_LINE_4;
            $dispatch->FROM_STATE           = $order->FROM_STATE;
            $dispatch->FROM_CITY            = $order->FROM_CITY;
            $dispatch->FROM_POST_CODE       = $order->FROM_POSTCODE;
            $dispatch->FROM_F_COUNTRY_NO    = $order->FROM_F_COUNTRY_NO;
            $dispatch->FROM_COUNTRY         = $order->FROM_COUNTRY;

            $dispatch->TO_NAME              = $order->DELIVERY_NAME;
            $dispatch->TO_TEL_NO            = $order->DELIVERY_MOBILE;
            $dispatch->TO_ADDRESS_LINE_1    = $order->DELIVERY_ADDRESS_LINE_1;
            $dispatch->TO_ADDRESS_LINE_2    = $order->DELIVERY_ADDRESS_LINE_2;
            $dispatch->TO_ADDRESS_LINE_3    = $order->DELIVERY_ADDRESS_LINE_3;
            $dispatch->TO_ADDRESS_LINE_4    = $order->DELIVERY_ADDRESS_LINE_4;
            $dispatch->TO_STATE             = $order->DELIVERY_STATE;
            $dispatch->TO_CITY              = $order->DELIVERY_CITY;
            $dispatch->TO_POST_CODE         = $order->DELIVERY_POSTCODE;
            $dispatch->TO_F_COUNTRY_NO      = $order->DELIVERY_F_COUNTRY_NO;
            $dispatch->TO_COUNTRY           = $order->DELIVERY_COUNTRY;
            $dispatch->COLLECTED_BY         = $request['collected_by'] ?? $order->CUSTOMER_NAME ?? $order->RESELLER_NAME ?? '';

            $dispatch->save();
            $booking_details_no = BookingDetails::join('SLS_ORDER','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING_DETAILS.F_BOOKING_NO')
                                                ->select('SLS_BOOKING_DETAILS.PK_NO')
                                                ->where('SLS_ORDER.PK_NO',$request['order_id'])
                                                ->get();

            if($booking_details_no){

                foreach ($booking_details_no as $key => $value) {
                    $child = new DispatchDetails();
                    $child->F_SC_ORDER_DISPATCH_NO  = $dispatch->PK_NO;
                    $child->F_BOOKING_DETAILS_NO    = $value->PK_NO;
                    $child->save();
                    $booking_details = BookingDetails::select('F_INV_STOCK_NO')->where('PK_NO',$value->PK_NO)->first();

                    BookingDetails::where('PK_NO',$value->PK_NO)->update(['DISPATCH_STATUS' => 40]);

                    Stock::where('PK_NO', $booking_details->F_INV_STOCK_NO)->update(['ORDER_STATUS' => 80]);
                    //IF PRODUCT IS ON SHELVE THEN CHECKOUT
                    $shelve = Stock::select('INV_WAREHOUSE_ZONES.ITEM_COUNT','INV_WAREHOUSE_ZONES.PK_NO','INV_STOCK.PK_NO as inv_pk')
                                    ->join('INV_WAREHOUSE_ZONES','INV_WAREHOUSE_ZONES.PK_NO','INV_STOCK.F_INV_ZONE_NO')
                                    ->where('INV_STOCK.PK_NO',$booking_details->F_INV_STOCK_NO)
                                    ->first();

                    if (!empty($shelve)) {
                        Stock::where('PK_NO',$shelve->inv_pk)->update(['F_INV_ZONE_NO'=>null,'INV_ZONE_BARCODE'=>null,'ZONE_CHECK_OUT_BY'=>$request['user_id'],'ZONE_CHECK_OUT_BY_NAME'=>$username->USERNAME]);
                        WarehouseZone::where('PK_NO',$shelve->PK_NO)->update(['ITEM_COUNT'=>$shelve->ITEM_COUNT-1]);
                        DB::table('INV_WAREHOUSE_ZONE_STOCK_ITEM')->where('F_INV_STOCK_NO',$booking_details->F_INV_STOCK_NO)->delete();
                    }
                }
            }
            $data = DB::table('SLS_BOOKING_DETAILS')
            ->select(DB::raw("GROUP_CONCAT(DISPATCH_STATUS) as DISPATCH_STATUS"), DB::raw("COUNT(*) AS COUNTER"))
            ->groupBy('F_BOOKING_NO')
            ->where('F_BOOKING_NO',$order->F_BOOKING_NO)
            ->first();

            $dispatch_status = $data->DISPATCH_STATUS;
            $dispatch_status_arr = explode(',',$dispatch_status);
            $dispatch_status_arr_count = array_count_values($dispatch_status_arr);
            if(isset($dispatch_status_arr_count[40])){
                Order::where('F_BOOKING_NO',$order->F_BOOKING_NO)->update(['DISPATCH_STATUS' => 40]);
            }
            $sms_body = "RM0.00 AZURAMART: Order #ORD-".$order->BOOKING_NO." has dispatched, for more info please Whatsapp http://linktr.ee/azuramart";

            $noti = new SmsNotification();
            $noti->TYPE = 'Dispatch';
            $noti->F_BOOKING_NO = $order->F_BOOKING_NO;
            //$noti->F_BOOKING_DETAIL_NO = $value->PK_NO;
            $noti->BODY = $sms_body;
            $noti->F_SS_CREATED_BY = $request['user_id'];
            if($order->IS_RESELLER == 0){
                $noti->CUSTOMER_NO = $order->F_CUSTOMER_NO;
                $noti->IS_RESELLER = 0;
            }else{
                $noti->RESELLER_NO = $order->F_RESELLER_NO;
                $noti->IS_RESELLER = 1;
            }
            $noti->save();

            $email = new EmailNotification();
            $email->TYPE = 'Dispatch';
            $email->F_BOOKING_NO = $order->F_BOOKING_NO;
            $email->BODY = $sms_body;
            $email->F_SS_CREATED_BY = $request['user_id'];
            if($order->IS_RESELLER == 0){
                $email->CUSTOMER_NO = $order->F_CUSTOMER_NO;
                $email->IS_RESELLER = 0;
            }else{
                $email->RESELLER_NO = $order->F_RESELLER_NO;
                $email->IS_RESELLER = 1;
            }
            $email->save();

            //SPECIAL NOTE IS READ OR NOT
            Booking::where('PK_NO',$order->F_BOOKING_NO)->update(['IS_READ_BOOKING_NOTES'=>1,'READ_BY_BOOKING_NOTES'=>$request['user_id']]);
        } catch (\Exeption $e) {
            DB::rollback();
            return $this->successResponse(200, $e->getMessage(), null, 0);
        }
        DB::commit();
        return $this->successResponse(200, 'Payment Accepted Successfully !', null, 1);
    }

    public function postCodRtcAcknowledge($request)
    {
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request->user_id)->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        DB::beginTransaction();
        try {
            $secondarty_user = Auth::select('F_PARENT_USER_ID')->where('PK_NO',$request->user_id)->first();
            $agent_zone = PaymentBank::select('BANK_ACC_NAME','F_INV_ZONE_NO')->where('F_USER_NO',$secondarty_user->F_PARENT_USER_ID)->first();

            if (!empty($agent_zone)) {
                $details = BookingDetails::join('INV_STOCK','INV_STOCK.PK_NO','SLS_BOOKING_DETAILS.F_INV_STOCK_NO')
                                ->where('INV_STOCK.SKUID',$request->sku_id)
                                // ->where('SLS_BOOKING_DETAILS.F_BOOKING_NO',$order->F_BOOKING_NO)
                                ->where('INV_STOCK.F_INV_ZONE_NO',$agent_zone->F_INV_ZONE_NO)
                                ->limit($request->qty)
                                ->update(['SLS_BOOKING_DETAILS.COD_RTC_ACK'=>$request->is_acknowledge]);

            }else{
                return $this->successResponse(200, 'Please try again !', null, 0);
            }
        } catch (\Exeption $e) {
            DB::rollback();
            return $this->successResponse(200, $e->getMessage(), null, 0);
        }
        DB::commit();
        return $this->successResponse(200, 'Acknowledge successfull !', null, 1);
    }

    public function postCodRtcBoxItemTransfer($request)
    {
        $request = $request->json()->all();
        $user_map = DB::table('SS_INV_USER_MAP')->select('F_INV_WAREHOUSE_NO')->where('F_USER_NO', $request['user_id'])->first();
        if (empty($user_map)) {
            return $this->successResponse(200, 'Unauthorized Location!', null, 0);
        }
        DB::beginTransaction();
        try {
            $agent_zone = PaymentBank::select('BANK_ACC_NAME','F_INV_ZONE_NO')->where('PK_NO',$request["data"][0]["person_id"])->first();

            if (!empty($agent_zone)) {

                //-----------------------------UNBOX BOX ITEM----------------------
                $box_no = Box::select('PK_NO','F_INV_WAREHOUSE_NO','BOX_STATUS')->where('BOX_NO',$request['box_label'])->first();
                if (empty($box_no)) {
                    return $this->successResponse(200, 'Box not found !', null, 0);
                }
                $shipment_no = Shipmentbox::select('F_SHIPMENT_NO')->where('F_BOX_NO', $box_no->PK_NO)->first();
                if (empty($shipment_no)) {
                    return $this->successResponse(200, 'Shipment not found !', null, 0);
                }

                $shipment_status = Shipment::select('PK_NO','SHIPMENT_STATUS','F_TO_INV_WAREHOUSE_NO')->where('PK_NO', $shipment_no->F_SHIPMENT_NO)->first();

                if($box_no->BOX_STATUS < 50){
                    return $this->successResponse(200, 'Box not ready to unbox !', null, 0);
                }
                // Stock::where('F_BOX_NO', $box_no->PK_NO)
                // ->where('SKUID',$request['data'][0]['sku_id'])
                // ->where('PRODUCT_STATUS',50)
                // ->where('F_SHIPPMENT_NO',$shipment_no->F_SHIPMENT_NO)
                // ->orderBy('F_BOOKING_NO','DESC')
                // ->limit($request['data'][0]['qty'])
                // ->update(['PRODUCT_STATUS' => 60
                //         ,'F_INV_WAREHOUSE_NO' => $shipment_status->F_TO_INV_WAREHOUSE_NO
                //         ,'INV_WAREHOUSE_NAME' => $shipment_status->to_warehouse->NAME]);

                $product_count = Stock::where('F_BOX_NO',$box_no->PK_NO)->where('PRODUCT_STATUS',50)->where('F_SHIPPMENT_NO',$shipment_no->F_SHIPMENT_NO)->count();
                if ($product_count == 0) {
                    Box::where('PK_NO', $box_no->PK_NO)->update(['BOX_STATUS' => 60, 'F_INV_WAREHOUSE_NO' => $shipment_status->F_TO_INV_WAREHOUSE_NO,'F_BOX_USER_NO' => $request->user_id]);
                }
                // if ($product_count == 0) {
                //     Box::where('PK_NO', $box_no->PK_NO)->update(['BOX_STATUS' => 60]);
                // }
                $booking_id = Order::select('F_BOOKING_NO')->where('PK_NO',$request["data"][0]["order_id"])->first();

                // $inv_pks = Stock::join('SLS_BOOKING_DETAILS','SLS_BOOKING_DETAILS.F_INV_STOCK_NO','=','INV_STOCK.PK_NO')
                // ->where('INV_STOCK.F_BOX_NO', $box_no->PK_NO)
                // ->where('INV_STOCK.SKUID',$request['data'][0]['sku_id'])
                // ->where('INV_STOCK.PRODUCT_STATUS',60)
                // ->where('INV_STOCK.F_SHIPPMENT_NO',$shipment_no->F_SHIPMENT_NO)
                // ->orderBy('INV_STOCK.F_BOOKING_NO','DESC')
                // ->limit($request['data'][0]['qty'])
                // ->pluck('INV_STOCK.PK_NO');

                $inv_pks    = Stock::join('SLS_BOOKING_DETAILS as b','INV_STOCK.PK_NO','b.F_INV_STOCK_NO')
                ->leftjoin('SLS_ORDER_RTC as rtc','rtc.F_BOOKING_NO','INV_STOCK.F_BOOKING_NO')
                ->where('INV_STOCK.F_BOX_NO', $box_no->PK_NO)
                ->where('INV_STOCK.PRODUCT_STATUS',50)
                ->where('INV_STOCK.SKUID',$request["data"][0]["sku_id"])
                ->where('INV_STOCK.F_BOOKING_NO',$booking_id->F_BOOKING_NO)
                ->where('rtc.F_ACC_PAYMENT_BANK_NO',$request["data"][0]["person_id"])
                ->where('b.IS_COD_SHELVE_TRANSFER',0)
                ->orderBy('INV_STOCK.F_BOOKING_NO','DESC')
                ->limit($request["data"][0]["qty"])
                ->pluck('INV_STOCK.PK_NO');

                    if(!empty($inv_pks)){
                        $booking_details = BookingDetails::whereIn('F_INV_STOCK_NO',$inv_pks)->get();

                        if(!empty($booking_details)){
                            foreach ($booking_details as $key => $value) {

                                $query = BookingDetails::where('F_BOOKING_NO',$value->F_BOOKING_NO)->sum('ARRIVAL_NOTIFICATION_FLAG');

                                if($query > 1){
                                    $sms_body = "RM0.00 AZURAMART: Order #ORD-".$value->booking->BOOKING_NO." has arrived(partially) at Malaysia & ready to post, for more info please WhatsApp http://linktr.ee/azuramart";
                                }else{
                                    $sms_body = "RM0.00 AZURAMART: Order #ORD-".$value->booking->BOOKING_NO." has arrived at Malaysia & ready to post, for more info please WhatsApp http://linktr.ee/azuramart";
                                }
                                $check =  SmsNotification::where('F_BOOKING_NO', $value->F_BOOKING_NO)->where('IS_SEND', 0)->first();
                                $check2 =  EmailNotification::where('F_BOOKING_NO', $value->F_BOOKING_NO)->where('IS_SEND', 0)->first();

                                if(!empty($check)){

                                    SmsNotification::where('F_BOOKING_NO', $value->F_BOOKING_NO)->where('IS_SEND', 0)->update(['IS_SEND' => 2]);
                                }
                                if(!empty($check2)){
                                    EmailNotification::where('F_BOOKING_NO', $value->F_BOOKING_NO)->where('IS_SEND', 0)->update(['IS_SEND' => 2]);
                                }

                                $noti = new SmsNotification();
                                $noti->TYPE = 'Arrival';
                                $noti->F_BOOKING_NO = $value->F_BOOKING_NO;
                                $noti->F_BOOKING_DETAIL_NO = $value->PK_NO;
                                $noti->BODY = $sms_body;
                                $noti->F_SS_CREATED_BY = $request['user_id'];
                                if($value->booking->IS_RESELLER == 0){
                                    $noti->CUSTOMER_NO = $value->booking->F_CUSTOMER_NO;
                                    $noti->IS_RESELLER = 0;
                                }else{
                                    $noti->RESELLER_NO = $value->booking->F_RESELLER_NO;
                                    $noti->IS_RESELLER = 1;
                                }
                                $noti->save();

                                $email = new EmailNotification();
                                $email->TYPE = 'Arrival';
                                $email->F_BOOKING_NO = $value->F_BOOKING_NO;
                                $email->F_BOOKING_DETAIL_NO = $value->PK_NO;
                                $email->F_SS_CREATED_BY = $request['user_id'];
                                if($value->booking->IS_RESELLER == 0){
                                    $email->CUSTOMER_NO = $value->booking->F_CUSTOMER_NO;
                                    $email->IS_RESELLER = 0;
                                }else{
                                    $email->RESELLER_NO = $value->booking->F_RESELLER_NO;
                                    $email->IS_RESELLER = 1;
                                }
                                $email->save();
                                BookingDetails::where('PK_NO',$value->PK_NO)->update(['ARRIVAL_NOTIFICATION_FLAG' => 0]);
                            }
                        }
                    }

                    //--------------------------------TRANSFER BOX ITEM------------------------

                    $user_name  = Auth::select('USERNAME')->where('PK_NO',$request["user_id"])->first();
                    $new_shelve   = WarehouseZone::where('PK_NO',$agent_zone->F_INV_ZONE_NO)->first();
                    $increase_qty = $new_shelve->ITEM_COUNT + $request["data"][0]["qty"];

                    // $inv_pks    = Stock::select('INV_STOCK.PK_NO')
                    //                     ->join('SLS_BOOKING_DETAILS as b','INV_STOCK.PK_NO','b.F_INV_STOCK_NO')
                    //                     ->leftjoin('SLS_ORDER_RTC as rtc','rtc.F_BOOKING_NO','INV_STOCK.F_BOOKING_NO')
                    //                     ->where('INV_STOCK.SKUID',$request["data"][0]["sku_id"])
                    //                     ->where('INV_STOCK.F_BOOKING_NO',$booking_id->F_BOOKING_NO)
                    //                     ->where('rtc.F_ACC_PAYMENT_BANK_NO',$request["data"][0]["person_id"])
                    //                     ->where('b.IS_COD_SHELVE_TRANSFER',0)
                    //                     ->take($request["data"][0]["qty"])
                    //                     ->get();

                    // Stock::where('F_BOX_NO', $box_no->PK_NO)
                    // ->where('SKUID',$request['data'][0]['sku_id'])
                    // ->where('PRODUCT_STATUS',50)
                    // ->where('F_SHIPPMENT_NO',$shipment_no->F_SHIPMENT_NO)
                    // ->orderBy('F_BOOKING_NO','DESC')
                    // ->limit($request['data'][0]['qty'])
                    // ->update(['PRODUCT_STATUS' => 60
                    //         ,'F_INV_WAREHOUSE_NO' => $shipment_status->F_TO_INV_WAREHOUSE_NO
                    //         ,'INV_WAREHOUSE_NAME' => $shipment_status->to_warehouse->NAME]);

                    Stock::whereIn('PK_NO',$inv_pks)
                        ->update([
                                'F_INV_ZONE_NO'         => $new_shelve->PK_NO
                            ,'INV_ZONE_BARCODE'         => $new_shelve->ZONE_BARCODE
                            ,'ZONE_CHECK_IN_BY'         => $request["user_id"]
                            ,'ZONE_CHECK_IN_BY_NAME'    => $user_name->USERNAME
                            ,'F_INV_WAREHOUSE_NO'       => $shipment_status->F_TO_INV_WAREHOUSE_NO
                            ,'INV_WAREHOUSE_NAME'       => $shipment_status->to_warehouse->NAME
                            ,'PRODUCT_STATUS' => 60
                        ]);
                    WarehouseZone::where('ZONE_BARCODE',$new_shelve->ZONE_BARCODE)->update(['ITEM_COUNT' => $increase_qty]);
                    BookingDetails::whereIn('F_INV_STOCK_NO',$inv_pks)->update(['IS_COD_SHELVE_TRANSFER' => 1]);

                    $zone_items[]=array();
                    foreach ($inv_pks as $key => $value) {
                        $zone_items[0]['F_INV_STOCK_NO'] = $value;
                        $zone_items[0]['F_INV_WAREHOUSE_ZONE_NO'] = $new_shelve->PK_NO;
                        // $zone_items[0][$value->PK_NO] = $new_shelve->PK_NO;
                    }
                    WarehouseZoneItem::insert($zone_items);
            }else{
                return $this->successResponse(200, 'Please try again !', null, 0);
            }
        } catch (\Exeption $e) {
            DB::rollback();
            return $this->successResponse(200, $e->getMessage(), null, 0);
        }
        DB::commit();
        return $this->successResponse(200, 'Transfer successfull !', null, 1);
    }
}
