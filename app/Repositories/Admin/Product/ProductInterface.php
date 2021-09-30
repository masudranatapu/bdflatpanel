<?php

namespace App\Repositories\Admin\Product;


interface ProductInterface
{
    public function getPaginatedList($request);
    public function postUpdate($request, int $id);
    /*
    public function postStoreProductVariant($request);
    public function postUpdateProductVariant($request, int $id);
    public function getDeleteProductVariant(int $id);
    public function getShow(int $id);
    public function delete(int $id);
    public function deleteImage(int $id);
    public function getProductSearchList($request);
    */
}
