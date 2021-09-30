<?php

namespace App\Repositories\Admin\Datatable;

interface DatatableInterface
{
    public function getSeeker($request);

    public function getOwner($request);

    public function getAgents($request);

    public function getProperty($request);

    public function getRefundRequest($request);

    public function getRechargeRequest($request);
    /*
    public function getDatatableCustomer();
    public function getDatatableReseller();
    public function getDatatableProduct($request);
    public function getDatatableOrder($request);
    public function getCancelOrder($request);
    public function getDatatableAlteredOrder($request);
    public function getDatatableDefaultOrder($request);
    public function getDatatableUnshelved($request);
    public function getDatatableBoxed($request);
    public function getDatatableShelved($request);
    public function getDatatableNotBoxed($request);
    public function getDatatableSalesComission($request);
    public function getDatatableSalesComissionList($request);
    public function getDatatableOrderCollection($request);
    public function getDatatableItemCollection($request);
    public function getDatatableItemCollectedList($request);
    public function getDatatableDefaultOrderAction($request);
    public function getDatatableDefaultOrderPenalty($request);
    public function customerRefundlist($request);
    public function customerRefunded($request);
    public function customerRefundedRequestList($request);
    public function ajaxbankToOther();
    public function ajaxbankToBank();

    */
}
