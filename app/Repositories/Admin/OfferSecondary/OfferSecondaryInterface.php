<?php

namespace App\Repositories\Admin\OfferSecondary;

interface OfferSecondaryInterface
{
    public function getPaginatedList($request, int $per_page = 500);
    public function postStore($request);
    public function postUpdate($request, int $id);
    public function findOrThrowException(int $id);
    public function delete($id);
    public function getVariantList($request);
    public function postStoreProduct($request);
    public function getDeleteProduct($id);
}
