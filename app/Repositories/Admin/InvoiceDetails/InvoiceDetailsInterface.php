<?php

namespace App\Repositories\Admin\InvoiceDetails;

interface InvoiceDetailsInterface
{
    public function getPaginatedList($request, int $per_page = 20, $id);
    public function getProductBySubCategory($id);
    public function getInvoiceData($id);
    public function getVariantListById($data);
    public function getVariantListByBarCode($bar_code);
    public function postStore($request);
    public function getVariantListByQueryString($request, $queryString);
    /*public function postUpdate($request, int $id);
    public function findOrThrowException($id);*/
    public function delete($id);
    public function getProductByInvoice($id,$type);
}
