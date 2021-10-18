<?php
namespace App\Repositories\Admin\SalesReport;

use DB;
use Carbon\Carbon;
use App\Models\Agent;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Booking;
use App\Models\Invoice;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\Auth;

class SalesReportAbstract implements SalesReportInterface
{
    use RepoResponse;

    // protected $shipment;
    // protected $warehouse;

    public function __construct()
    {
        // $this->shipment = $shipment;
        // $this->warehouse = $warehouse;
    }

    public function getComissionReport($id)
    {
        $agent          = Agent::select('NAME')->where('PK_NO',$id)->first();
        $now            = Carbon::now();
        $current_year   = $now->year;
        $current_month  = $now->month;

        $data['data'] = DB::table('SLS_BOOKING as b')
        ->select('b.BOOKING_SALES_AGENT_NAME','b.F_BOOKING_SALES_AGENT_NO as agent_no'
        ,DB::raw('(IFNULL(SUM(bd.COMISSION),0)) as current_comission')
        // ,DB::raw('(IFNULL(COUNT(o.PK_NO),0)) as current_order')
        ,DB::raw('(select (IFNULL(COUNT(o.PK_NO),0)) from SLS_BOOKING as b
        inner join SLS_ORDER as o on o.F_BOOKING_NO = b.PK_NO
        where year(b.SS_CREATED_ON) = '.$current_year.'
        and month(b.SS_CREATED_ON) = '.$current_month.'
        and b.F_BOOKING_SALES_AGENT_NO = agent_no ) as current_order')
        )
        ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
        ->join('SLS_BOOKING_DETAILS as bd','bd.F_BOOKING_NO','b.PK_NO')
        ->whereYear('b.SS_CREATED_ON',$current_year)
        ->whereMonth('b.SS_CREATED_ON',$current_month)
        ->where('b.F_BOOKING_SALES_AGENT_NO',$id)
        ->groupBy('b.F_BOOKING_SALES_AGENT_NO')
        ->first();

        $data['cancelled_later'] = DB::table('SLS_BOOKING as b')
        ->select('b.F_BOOKING_SALES_AGENT_NO as agent_no'
        ,DB::raw('(IFNULL(SUM(bda.COMISSION),0)) as c_current_comission')
        )
        ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
        ->join('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
        ->whereYear('b.SS_CREATED_ON',$current_year)
        ->whereMonth('b.SS_CREATED_ON',$current_month)
        ->where('b.F_BOOKING_SALES_AGENT_NO',$id)
        ->whereRaw('(bda.CHANGE_TYPE = "ORDER_CANCEL")')
        ->groupBy('b.F_BOOKING_SALES_AGENT_NO')
        ->first();

        $data['cancelled_now'] = DB::table('SLS_BOOKING as b')
        ->select('b.F_BOOKING_SALES_AGENT_NO as agent_no'
        ,DB::raw('(IFNULL(SUM(bda.COMISSION),0)) as c_current_comission')
        )
        ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
        ->join('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
        ->whereYear('b.CANCELED_AT',$current_year)
        ->whereMonth('b.CANCELED_AT',$current_month)
        ->where('b.F_BOOKING_SALES_AGENT_NO',$id)
        ->whereRaw('(bda.CHANGE_TYPE = "ORDER_CANCEL")')
        ->groupBy('b.F_BOOKING_SALES_AGENT_NO')
        ->first();

        $data['return_later'] = DB::table('SLS_BOOKING as b')
        ->select('b.F_BOOKING_SALES_AGENT_NO as agent_no'
        ,DB::raw('(IFNULL(SUM(bda.COMISSION),0)) as c_current_comission')
        )
        ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
        ->join('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
        ->whereYear('b.SS_CREATED_ON',$current_year)
        ->whereMonth('b.SS_CREATED_ON',$current_month)
        ->where('b.F_BOOKING_SALES_AGENT_NO',$id)
        ->whereRaw('(bda.CHANGE_TYPE = "ORDER_RETURN")')
        ->whereIn('bda.RETURN_TYPE',[1,2,4,5])
        ->groupBy('b.F_BOOKING_SALES_AGENT_NO')
        ->first();

        $data['return_now'] = DB::table('SLS_BOOKING as b')
        ->select('b.F_BOOKING_SALES_AGENT_NO as agent_no'
        ,DB::raw('(IFNULL(SUM(bda.COMISSION),0)) as c_current_comission')
        )
        ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
        ->join('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
        ->whereYear('bda.RETURN_DATE',$current_year)
        ->whereMonth('bda.RETURN_DATE',$current_month)
        ->where('b.F_BOOKING_SALES_AGENT_NO',$id)
        ->whereRaw('(bda.CHANGE_TYPE = "ORDER_RETURN")')
        ->whereIn('bda.RETURN_TYPE',[1,2,4,5])
        ->groupBy('b.F_BOOKING_SALES_AGENT_NO')
        ->first();

        // if (isset($data['data'])) {
            $current_comission = $data['data']->current_comission ?? 0;
            $current_comission += $data['cancelled_later']->c_current_comission ?? 0;
            $current_comission += $data['return_later']->c_current_comission ?? 0;
            $current_comission -= $data['cancelled_now']->c_current_comission ?? 0;
            $current_comission -= $data['return_now']->c_current_comission ?? 0;
            $data['current_comission'] = $current_comission;
        // }

        $data['total_comission'] = DB::SELECT("SELECT IFNULL(SUM(bd.COMISSION),0) as total_comission, BOOKING_SALES_AGENT_NAME FROM SLS_BOOKING as b inner join SLS_ORDER as o on o.F_BOOKING_NO = b.PK_NO inner join SLS_BOOKING_DETAILS as bd on bd.F_BOOKING_NO = b.PK_NO where b.F_BOOKING_SALES_AGENT_NO = $id");

        $data['total_order'] = Booking::join('SLS_ORDER','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING.PK_NO')->where('SLS_BOOKING.F_BOOKING_SALES_AGENT_NO',$id)->count();

        return $this->formatResponse(true, 'Data Found', 'admin.shipment.shipmentInvoiceView', $data);
    }

    public function ajaxComissionReport($agent_id,$date)
    {
        $current_year   = date('Y', strtotime($date));
        $current_month  = date('n', strtotime($date));

        $count_monthly = DB::table('SLS_BOOKING as b')
        ->select(DB::raw('(IFNULL(SUM(bd.COMISSION),0)) as current_comission')
        // ,DB::raw('(IFNULL(COUNT(o.PK_NO),0)) as current_order')
        ,DB::raw('(select (IFNULL(COUNT(o.PK_NO),0)) from SLS_BOOKING as b
        inner join SLS_ORDER as o on o.F_BOOKING_NO = b.PK_NO
        where year(b.SS_CREATED_ON) = '.$current_year.'
        and month(b.SS_CREATED_ON) = '.$current_month.'
        and b.F_BOOKING_SALES_AGENT_NO = '.$agent_id.' ) as current_order')
        )
        ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
        ->join('SLS_BOOKING_DETAILS as bd','bd.F_BOOKING_NO','b.PK_NO')
        // ->leftjoin('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
        ->whereYear('b.SS_CREATED_ON',$current_year)
        ->whereMonth('b.SS_CREATED_ON',$current_month)
        ->where('b.F_BOOKING_SALES_AGENT_NO',$agent_id)
        // ->whereRaw('(bda.CHANGE_TYPE = "ORDER_CANCEL" OR bda.CHANGE_TYPE IS NULL)')
        ->first();

        $data['cancelled_later'] = DB::table('SLS_BOOKING as b')
        ->select('b.F_BOOKING_SALES_AGENT_NO as agent_no'
        ,DB::raw('(IFNULL(SUM(bda.COMISSION),0)) as c_current_comission')
        )
        ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
        ->join('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
        ->whereYear('b.SS_CREATED_ON',$current_year)
        ->whereMonth('b.SS_CREATED_ON',$current_month)
        ->where('b.F_BOOKING_SALES_AGENT_NO',$agent_id)
        ->whereRaw('(bda.CHANGE_TYPE = "ORDER_CANCEL")')
        ->groupBy('b.F_BOOKING_SALES_AGENT_NO')
        ->first();

        $data['cancelled_now'] = DB::table('SLS_BOOKING as b')
        ->select('b.F_BOOKING_SALES_AGENT_NO as agent_no'
        ,DB::raw('(IFNULL(SUM(bda.COMISSION),0)) as c_current_comission')
        )
        ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
        ->join('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
        ->whereYear('b.CANCELED_AT',$current_year)
        ->whereMonth('b.CANCELED_AT',$current_month)
        ->where('b.F_BOOKING_SALES_AGENT_NO',$agent_id)
        ->whereRaw('(bda.CHANGE_TYPE = "ORDER_CANCEL")')
        ->groupBy('b.F_BOOKING_SALES_AGENT_NO')
        ->first();

        $data['return_later'] = DB::table('SLS_BOOKING as b')
        ->select('b.F_BOOKING_SALES_AGENT_NO as agent_no'
        ,DB::raw('(IFNULL(SUM(bda.COMISSION),0)) as c_current_comission')
        )
        ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
        ->join('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
        ->whereYear('b.SS_CREATED_ON',$current_year)
        ->whereMonth('b.SS_CREATED_ON',$current_month)
        ->where('b.F_BOOKING_SALES_AGENT_NO',$agent_id)
        ->whereRaw('(bda.CHANGE_TYPE = "ORDER_RETURN")')
        ->whereIn('bda.RETURN_TYPE',[1,2,4,5])
        ->groupBy('b.F_BOOKING_SALES_AGENT_NO')
        ->first();

        $data['return_now'] = DB::table('SLS_BOOKING as b')
        ->select('b.F_BOOKING_SALES_AGENT_NO as agent_no'
        ,DB::raw('(IFNULL(SUM(bda.COMISSION),0)) as c_current_comission')
        )
        ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
        ->join('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
        ->whereYear('bda.RETURN_DATE',$current_year)
        ->whereMonth('bda.RETURN_DATE',$current_month)
        ->where('b.F_BOOKING_SALES_AGENT_NO',$agent_id)
        ->whereRaw('(bda.CHANGE_TYPE = "ORDER_RETURN")')
        ->whereIn('bda.RETURN_TYPE',[1,2,4,5])
        ->groupBy('b.F_BOOKING_SALES_AGENT_NO')
        ->first();

        $count_monthly->current_comission += $data['cancelled_later']->c_current_comission ?? 0;
        $count_monthly->current_comission += $data['return_later']->c_current_comission ?? 0;
        $count_monthly->current_comission -= $data['cancelled_now']->c_current_comission ?? 0;
        $count_monthly->current_comission -= $data['return_now']->c_current_comission ?? 0;

        return $count_monthly;
    }

    public function getYetToShip($request)
    {
        if ($request->get('from_date') === null || ($request->get('to_date')) === null) {
            $from_date = new Carbon('last day of last year');
            $from_date = $from_date->startOfMonth()->subSeconds(1)->endOfMonth()->toDateString();

            // $from_date = new Carbon('last year');
            // $from_date = date('F Y', strtotime($from_date));
            // $last_month = Carbon::now()->subMonth()->format('F');
            // $last_year = new Carbon('last year');
            // $last_year = date('Y', strtotime($last_year));
            // $from_date = new Carbon('last day of '.$last_month.' '.$last_year.'');
            // $from_date = $from_date->toDateString();
            // $from_date = $from_date->startOfMonth()->subSeconds(1)->endOfMonth()->toDateString();

            $to_date    = Carbon::now()->firstOfMonth()->toDateString();
        }else{
            $from_date   = date('Y-m-d', strtotime($request->get('from_date')));
            $to_date     = date('Y-m-d', strtotime($request->get('to_date')));
        }
        // $data['invoice_exact'] = DB::table('PRC_STOCK_IN as stock')
        //         // ->leftjoin('PRC_STOCK_IN_DETAILS as details','details.F_PRC_STOCK_IN','stock.PK_NO')
        //         ->where('stock.F_SS_CURRENCY_NO',1)
        //         ->where('stock.INV_STOCK_RECORD_GENERATED',0)
        //         // ->whereBetween('details.SS_CREATED_ON',[$from_date,$to_date])
        //         ->whereBetween('stock.INVOICE_DATE',[$from_date,$to_date])
        //         ->sum('stock.INVOICE_EXACT_VALUE');


        $data['invoice_exact'] = DB::SELECT("SELECT sum(stock.INVOICE_EXACT_VALUE) as invoice_exact
                                    from PRC_STOCK_IN as stock
                                    where stock.F_SS_CURRENCY_NO = 1
                                    and stock.INV_STOCK_RECORD_GENERATED = 0
                                    and stock.INVOICE_DATE between '$from_date' and '$to_date'");
        $data['invoice_exact'] = $data['invoice_exact'][0]->invoice_exact;

        // $data['invoice_actual_ev'] = DB::table('PRC_STOCK_IN as stock')
        //         // ->leftjoin('PRC_STOCK_IN_DETAILS as details','details.F_PRC_STOCK_IN','stock.PK_NO')
        //         ->where('stock.F_SS_CURRENCY_NO',1)
        //         ->where('stock.INV_STOCK_RECORD_GENERATED',0)
        //         // ->whereBetween('details.SS_CREATED_ON',[$from_date,$to_date])
        //         ->whereBetween('stock.INVOICE_DATE',[$from_date,$to_date])
        //         ->sum('stock.INVOICE_TOTAL_EV_ACTUAL_GBP')

        $data['invoice_actual_ev'] = DB::SELECT("SELECT sum(stock.INVOICE_TOTAL_EV_ACTUAL_GBP) as invoice_actual_ev
                                    from PRC_STOCK_IN as stock
                                    where stock.F_SS_CURRENCY_NO = 1
                                    and stock.INV_STOCK_RECORD_GENERATED = 0
                                    and stock.INVOICE_DATE between '$from_date' and '$to_date'");
        $data['invoice_actual_ev'] = $data['invoice_actual_ev'][0]->invoice_actual_ev;

        // $data['in_ship'] = DB::table('INV_STOCK as i')
        //             ->join('SC_SHIPMENT as s','s.PK_NO','i.F_SHIPPMENT_NO')
        //             ->leftjoin('PRC_STOCK_IN as stock','i.F_PRC_STOCK_IN_NO','stock.PK_NO')
        //             ->whereNotNull('i.F_SHIPPMENT_NO')
        //             ->where('stock.F_SS_CURRENCY_NO',1)
        //             ->where('s.SCH_DEPARTING_DATE','<=',$to_date)
        //             ->whereBetween('stock.INVOICE_DATE',[$from_date,$to_date])
        //             ->sum('i.PRODUCT_PURCHASE_PRICE_GBP');

        $data['in_ship'] = DB::SELECT("SELECT sum(i.PRODUCT_PURCHASE_PRICE_GBP) as in_ship
                                from INV_STOCK as i
                                inner join SC_SHIPMENT as s on s.PK_NO = i.F_SHIPPMENT_NO
                                left join PRC_STOCK_IN as stock on i.F_PRC_STOCK_IN_NO = stock.PK_NO
                                where i.F_SHIPPMENT_NO is not null
                                and stock.F_SS_CURRENCY_NO = 1
                                and s.SCH_DEPARTING_DATE <= '$to_date'
                                and stock.INVOICE_DATE between '$from_date' and '$to_date'");
        $data['in_ship'] = $data['in_ship'][0]->in_ship;

        // $data['not_in_ship'] = DB::table('INV_STOCK as i')
        //             ->leftjoin('PRC_STOCK_IN as stock','i.F_PRC_STOCK_IN_NO','stock.PK_NO')
        //             ->leftjoin('SC_SHIPMENT as s','s.PK_NO','i.F_SHIPPMENT_NO')
        //             ->whereRaw('(i.F_SHIPPMENT_NO IS NULL OR s.SCH_DEPARTING_DATE > '.$to_date.')')
        //             ->where('stock.F_SS_CURRENCY_NO',1)
        //             ->whereBetween('stock.INVOICE_DATE',[$from_date,$to_date])
        //             ->sum('i.PRODUCT_PURCHASE_PRICE_GBP');

        $data['not_in_ship'] = DB::SELECT("SELECT sum(i.PRODUCT_PURCHASE_PRICE_GBP) as not_in_ship
                                from INV_STOCK as i
                                left join PRC_STOCK_IN as stock on i.F_PRC_STOCK_IN_NO = stock.PK_NO
                                left join SC_SHIPMENT as s on s.PK_NO = i.F_SHIPPMENT_NO
                                where (i.F_SHIPPMENT_NO IS NULL OR s.SCH_DEPARTING_DATE > '$to_date')
                                and stock.F_SS_CURRENCY_NO = 1
                                and stock.INVOICE_DATE between '$from_date' and '$to_date'");
        $data['not_in_ship'] = $data['not_in_ship'][0]->not_in_ship;


        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;

        return $this->formatResponse(true, 'Data Found', 'admin.shipment.shipmentInvoiceView', $data);
    }
}
