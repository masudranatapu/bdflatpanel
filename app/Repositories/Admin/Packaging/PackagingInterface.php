<?php

namespace App\Repositories\Admin\Packaging;

interface PackagingInterface
{

    public function findOrThrowException(int $id);
    public function gePackagingListInfo($key,$type);
    public function postPackingItemStore($request);
    public function postPackingItemDelete($request);
    public function postPackingItemUpdate($request);
    public function postPackagingboxStore($request);
    public function getPackaginglistPrint(int $id);

}
