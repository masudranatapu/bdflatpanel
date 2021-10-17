<?php

namespace App\Repositories\Admin\Brand;

interface BrandInterface
{
    public function getPaginatedList($request, int $per_page = 20);
    public function postStore($request);
    public function postUpdate($request, int $id);
    public function findOrThrowException($id);
    public function delete($id);
}
