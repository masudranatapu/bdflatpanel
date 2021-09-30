<?php

namespace App\Repositories\Admin\CustomerAddress;

interface CustomerAddressInterface
{
    public function getPaginatedList($request, int $per_page = 20);
    public function postStore($request);
    public function postUpdate($request, int $id);
    public function getShow(int $id);
    // public function findOrThrowException($id);
    public function delete($id);
}
