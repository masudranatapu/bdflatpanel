<?php

namespace App\Repositories\Admin\Order;

interface OrderInterface
{
    public function getPaginatedList($request, int $per_page = 5);
    public function postStore($request);
    public function postSelfPickup($request);
    public function postSelfPickupAjax($request);
    public function postUpdate($request, int $id);
    public function updateSenderaddress($request, int $id);
    public function updateReceiverAddress($request, int $id);
    public function postReturnOrder($request, int $id);
    public function delete($id);
    public function getDueOrdersCustomer(int $id);
    public function getDueOrdersReseller(int $id);
    public function findOrThrowException($id,$type);
    public function findOrThrowExceptionAdminApproval($id);
    public function ajaxDelete($id,$type,$booking_no);
    public function ajaxExchangeStock($inv_id);
    public function ajaxExchangeStockAction($request);
    public function ajaxPayment($request);
    public function updateBooktoOrder($request,$id);
    public function updateBooktoOrderAdminApproved($request,$id);
    public function getIndex();
    public function getOrderForDisopatch($id);
    public function getCustomerAddress($id,$pk_no,$address_id,$is_reseller);
    public function postCustomerAddress($request);
    public function postCustomerAddress2($request);
    public function postUpdatedAddress($request, $order_id,$type);
    public function postPaymentUncheck($request);
    public function postDefaultOrderPenalty($request, $id);
    public function getPayInfo($order_id,$is_reseller);
    public function postCancel(int $id, $request);
}
