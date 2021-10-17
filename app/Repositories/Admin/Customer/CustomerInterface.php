<?php

namespace App\Repositories\Admin\Customer;

interface CustomerInterface
{
    public function getPaginatedList($request);
    public function getPayment($id);
    public function getCustomerTxn($id);
    public function getEdit(int $id);
    public function postUpdate($request);

    public function postRecharge($request, int $id);
    // public function postStore($request);
    // public function postBlanceTransfer($request);
    // public function getRemainingBalance($id);
    // public function addNewCustomer($request);

    // public function getCusAdd(int $id);
    // public function getCustomerHistory(int $id);
    // public function postUpdate($request, int $id);
    // public function findOrThrowException($id);
    // public function delete($id);
    // public function postRefundRequest($request);
    // public function getRefundedRequestDeny($request, int $id);

}
