<?php
namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatusApiController extends Controller
{

    public function __construct()
    {

    }

    public function checkStatus(Request $request){

        /*
        F_INV_WAREHOUSE_NO = 1 (UK)
        F_INV_WAREHOUSE_NO = 2 (MY)
        SLS_BOOKING_DETAILS.DISPATCH_STATUS = 40 (dispatched)
        SLS_BOOKING_DETAILS.DISPATCH_STATUS = 30 (All Remaining Dispatchable)
        INV_STOCK.ORDER_STATUS = 60 (payment done by actual topup from sls_order)


        SLS_ORDER.DISPATCH_STATUS = 40 (dispatched)
        SLS_ORDER.DISPATCH_STATUS = 30 (All Remaining Dispatchable )
        SLS_ORDER.DISPATCH_STATUS = 20 (Partially Dispatchable)
        SLS_ORDER.IS_ADMIN_HOLD ( 0=NOT HOLD, 1=HOLD, 2=COD, 3=RTC ),

        */

        DB::beginTransaction();

        try{
            /* FOR DISPATCH STATUS */

        $query = DB::SELECT("SELECT
        BC.PK_NO AS BOOKING_DETAILS_PK_NO
        ,BC.F_BOOKING_NO
        ,BC.F_INV_STOCK_NO
        ,BC.DISPATCH_STATUS
        ,BC.IS_ADMIN_HOLD
        ,BC.IS_SYSTEM_HOLD
        ,S.ORDER_STATUS
        ,S.F_INV_WAREHOUSE_NO
        ,BC.IS_READY
        FROM
        SLS_BOOKING_DETAILS AS BC
        JOIN INV_STOCK AS S
        ON S.PK_NO = BC.F_INV_STOCK_NO
        WHERE BC.DISPATCH_STATUS != 40
        AND BC.IS_ADMIN_APPROVAL != 1
        AND S.F_INV_WAREHOUSE_NO = 2
        ");
        // dd($query);
        if($query){
            foreach ($query as $key => $value) {
                if( ($value->F_INV_WAREHOUSE_NO == 2) && ($value->ORDER_STATUS == 60) ){
                    DB::SELECT("UPDATE SLS_BOOKING_DETAILS SET IS_SYSTEM_HOLD = 0, DISPATCH_STATUS = 30  WHERE PK_NO = $value->BOOKING_DETAILS_PK_NO");
                }
                if($value->F_INV_WAREHOUSE_NO == 2){
                    DB::SELECT("UPDATE SLS_BOOKING_DETAILS SET IS_READY = 1  WHERE PK_NO = $value->BOOKING_DETAILS_PK_NO");
                }
            }
        }

        $query2 = DB::SELECT("SELECT
        O.PK_NO AS ORDER_PK_NO
        ,O.F_BOOKING_NO
        ,O.IS_ADMIN_HOLD
        ,O.IS_SYSTEM_HOLD
        ,O.DISPATCH_STATUS
        ,O.IS_READY
        ,O.ORDER_ACTUAL_TOPUP
        ,B.TOTAL_PRICE
        ,B.DISCOUNT
        ,C.F_INV_STOCK_NO
        ,GROUP_CONCAT(C.IS_ADMIN_HOLD) AS ALL_C_IS_ADMIN_HOLD
        ,COUNT(C.IS_ADMIN_HOLD) AS COUNT_C_IS_ADMIN_HOLD

        ,GROUP_CONCAT(C.IS_SYSTEM_HOLD) AS  ALL_C_IS_SYSTEM_HOLD
        ,COUNT(C.IS_SYSTEM_HOLD) AS C_IS_SYSTEM_HOLD

        ,GROUP_CONCAT(C.DISPATCH_STATUS) AS ALL_C_DISPATCH_STATUS
        ,COUNT(C.DISPATCH_STATUS) AS COUNT_C_DISPATCH_STATUS

        ,GROUP_CONCAT(C.IS_READY) AS ALL_C_IS_READY
        ,COUNT(C.IS_READY) AS COUNT_C_IS_READY

        FROM SLS_ORDER AS O
        LEFT JOIN SLS_BOOKING_DETAILS AS C
        ON C.F_BOOKING_NO = O.F_BOOKING_NO
        LEFT JOIN SLS_BOOKING AS B
        ON B.PK_NO = O.F_BOOKING_NO
        WHERE O.DISPATCH_STATUS <> 40
        GROUP BY F_BOOKING_NO
        ");

        if($query2){
            foreach($query2 as $key => $val){
                $ORDER_ID = $val->ORDER_PK_NO;
                $all_c_dispatch_status      = $val->ALL_C_DISPATCH_STATUS;
                $all_c_is_system_hold       = $val->ALL_C_IS_SYSTEM_HOLD;
                $all_c_is_ready             = $val->ALL_C_IS_READY;
                $all_c_dispatch_status_arr  = explode(',',$all_c_dispatch_status);
                $all_c_is_system_hold_arr   = explode(',',$all_c_is_system_hold);
                $all_c_is_ready_arr         = explode(',',$all_c_is_ready);
                $all_c_dispatch_status_arr_count    = array_count_values($all_c_dispatch_status_arr);
                $all_c_is_system_hold_arr_count     = array_count_values($all_c_is_system_hold_arr);
                $all_c_is_ready_arr_count           = array_count_values($all_c_is_ready_arr);

                if(isset($all_c_dispatch_status_arr_count[40])){
                    if($all_c_dispatch_status_arr_count[40] == $val->COUNT_C_DISPATCH_STATUS){
                        DB::SELECT("UPDATE SLS_ORDER SET DISPATCH_STATUS = 40 WHERE PK_NO = $ORDER_ID AND IS_ADMIN_APPROVAL <> 1 ");
                    }
                }

                if(isset($all_c_dispatch_status_arr_count[30])){
                    if($all_c_dispatch_status_arr_count[30] == $val->COUNT_C_DISPATCH_STATUS){
                        DB::SELECT("UPDATE SLS_ORDER SET DISPATCH_STATUS = 30 WHERE PK_NO = $ORDER_ID AND IS_ADMIN_APPROVAL <> 1 ");
                    }elseif($all_c_dispatch_status_arr_count[30] != $val->COUNT_C_DISPATCH_STATUS){
                        DB::SELECT("UPDATE SLS_ORDER SET DISPATCH_STATUS = 20 WHERE PK_NO = $ORDER_ID AND IS_ADMIN_APPROVAL <> 1 ");
                    }
                }


                if(isset($all_c_dispatch_status_arr_count[0])){
                    if($all_c_dispatch_status_arr_count[0] == $val->COUNT_C_DISPATCH_STATUS){
                        DB::SELECT("UPDATE SLS_ORDER SET DISPATCH_STATUS = 0 WHERE PK_NO = $ORDER_ID AND IS_ADMIN_APPROVAL <> 1 ");
                    }

                }

                /* all_c_is_system_hold_arr */
                if( (isset($all_c_is_system_hold_arr_count[0])) && (isset($all_c_is_system_hold_arr_count[1]) ) ){
                    /* partially unhold by system */
                    DB::SELECT("UPDATE SLS_ORDER SET IS_SYSTEM_HOLD = 2 WHERE PK_NO = $ORDER_ID AND IS_ADMIN_APPROVAL <> 1 ");
                }elseif(isset($all_c_is_system_hold_arr_count[0])){
                    if($all_c_is_system_hold_arr_count[0] == $val->C_IS_SYSTEM_HOLD){
                        /* fully unhold by system */
                        DB::SELECT("UPDATE SLS_ORDER SET IS_SYSTEM_HOLD = 0 WHERE PK_NO = $ORDER_ID AND IS_ADMIN_APPROVAL <> 1 ");
                    }
                }elseif(isset($all_c_is_system_hold_arr_count[1])){
                    if($all_c_is_system_hold_arr_count[1] == $val->C_IS_SYSTEM_HOLD){
                        /* fully hold by system */
                    }
                }

                /*update is_ready status*/
                if( (isset($all_c_is_ready_arr_count[0])) && (isset($all_c_is_ready_arr_count[1])) ){
                    DB::SELECT("UPDATE SLS_ORDER SET IS_READY = 2 WHERE PK_NO = $ORDER_ID AND IS_ADMIN_APPROVAL <> 1 ");
                }elseif(isset($all_c_is_ready_arr_count[1])){
                    if($all_c_is_ready_arr_count[1] == $val->COUNT_C_IS_READY ){
                        DB::SELECT("UPDATE SLS_ORDER SET IS_READY = 1 WHERE PK_NO = $ORDER_ID AND IS_ADMIN_APPROVAL <> 1 ");
                    }
                }

                /*UPDATE INV_STOCK ORDER_STATUS IF PAYMENT FULL AND VERIFIED */
                $total_value = $val->TOTAL_PRICE - $val->DISCOUNT;
                // dd($val->F_INV_STOCK_NO);
                if( ($val->F_INV_STOCK_NO != null) && ($val->ORDER_ACTUAL_TOPUP >= $total_value)){
                    DB::SELECT("UPDATE INV_STOCK SET ORDER_STATUS = 60 WHERE PK_NO = $val->F_INV_STOCK_NO ");
                }



            }
        }

        } catch (\Exception $e) {

            DB::rollback();
            return 0;
        }

            DB::commit();

        return 1;



    }


}
