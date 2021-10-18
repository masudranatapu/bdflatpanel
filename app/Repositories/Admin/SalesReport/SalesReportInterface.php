<?php

namespace App\Repositories\Admin\SalesReport;

interface SalesReportInterface
{
    public function getComissionReport($id);
    public function getYetToShip($request);
    public function ajaxComissionReport($agent_id,$date);
}
