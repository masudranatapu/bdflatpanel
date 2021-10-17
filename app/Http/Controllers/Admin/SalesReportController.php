<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Booking;
use App\Traits\RepoResponse;
use Illuminate\Http\Request;
use App\Models\BookingDetails;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\SalesReport\SalesReportInterface;

class SalesReportController extends BaseController
{
    use RepoResponse;

    private $salesreport_int;
    private $booking;
    private $booking_details;

    function __construct(SalesReportInterface $salesreport_int, Booking $booking, BookingDetails $booking_details)
    {
        $this->salesreport_int    = $salesreport_int;
        $this->booking           = $booking;
        $this->booking_details   = $booking_details;
    }

    public function getIndex()
    {
        return view('admin.salesReport.index');
    }

    public function getComissionReport($id)
    {
        $this->resp = $this->salesreport_int->getComissionReport($id);
        return view('admin.salesReport.sales_comission_view')->withReport($this->resp->data);
    }

    public function getYetToShip(Request $request)
    {
        $this->resp = $this->salesreport_int->getYetToShip($request);
        return view('admin.salesReport.yet_to_ship')->withReport($this->resp->data);
    }

    public function ajaxComissionReport($agent_id,$date)
    {
        $this->resp = $this->salesreport_int->ajaxComissionReport($agent_id,$date);
        return response()->json($this->resp);
    }
}

