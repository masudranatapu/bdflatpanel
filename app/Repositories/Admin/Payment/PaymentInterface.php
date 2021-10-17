<?php

namespace App\Repositories\Admin\Payment;

interface PaymentInterface
{
    public function getPaginatedList($request, int $per_page = 5);
    // public function getList();
    public function postStore($request);
    public function postStoreReseller($request);
    // public function paymentVrify($id,$type);
    public function getDetails($id);
    public function getOrderPaymentDelete($id);
    // public function postUpdate($request, int $id);
    // public function getShow(int $id);
    public function getDelete(int $id);
    public function postUpdatePartial($request);
    public function getPaymentProcessing($request);
    public function getBankToOther(int $id);
    public function getBankToBank(int $id);
    public function getBankToBankDetails(int $id);
    public function getBankToOtherDetails(int $id);
    public function postNewPaymentType($request);
    public function postbankToOther($request);
    public function postbankToBank($request);
    public function postAccountBalanceInfo($request);
    public function postRefund($request);
}
