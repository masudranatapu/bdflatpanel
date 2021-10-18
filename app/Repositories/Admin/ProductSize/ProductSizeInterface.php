<?php

namespace App\Repositories\Admin\ProductSize;

interface ProductSizeInterface
{
    public function postStore($request);
    public function getPaginatedList($request);
    public function getList();
    public function delete(int $id);
    public function getShow(int $id);
    public function postUpdate($request, $id);
}
