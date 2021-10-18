<?php

namespace App\Repositories\Admin\Owner;

interface OwnerInterface
{
    public function getPaginatedList($request);

    public function getShow(int $id);

    public function postUpdate($request, int $id);

    public function updatePassword($request, int $id);

    public function getPayments(int $id);

    public function getCustomerTxn(int $id);

    public function storePayment($request, int $id);

    public function postRecharge($request, int $id);

    public function getTransaction($id);
    /*
    public function getShow(int $id);
    public function postStore($request);
    public function postUpdate($request, int $id);
    public function delete($id);
    */
}
