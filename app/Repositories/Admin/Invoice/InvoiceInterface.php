<?php

namespace App\Repositories\Admin\Invoice;

interface InvoiceInterface
{
    public function getPaginatedList($request, int $per_page = 20);
    public function postStore($request);
    public function getPaginatedListForProcess($request, int $per_page = 20);
    public function delete(int $id);
    public function postStoreInvoiceProcessing($request);
    public function invoiceQBentry(int $id);
    public function invoiceLoyaltyClaime(int $id);
    public function invoiceVatClaime(int $id);
    public function findOrThrowException(int $id);
    public function postUpdate($request, int $id);
    public function deleteImage(int $id);
    public function getDeleteGeneratedStock(int $id);
    public function getVatProcessing();
}
