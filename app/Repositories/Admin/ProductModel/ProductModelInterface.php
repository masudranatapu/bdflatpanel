<?php
namespace App\Repositories\Admin\ProductModel;

interface ProductModelInterface {
    public function postStore($request);
    public function getPaginatedList($request);
    public function getList();
    public function delete(int $PK_NO);
    public function getShow(int $PK_NO);
    public function postUpdate($request, $PK_NO);
}
