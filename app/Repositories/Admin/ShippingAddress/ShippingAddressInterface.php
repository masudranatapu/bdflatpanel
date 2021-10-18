<?php

namespace App\Repositories\Admin\ShippingAddress;

interface ShippingAddressInterface
{
    public function getPaginatedList($request, int $per_page = 20);
    public function postStore($request);
    public function postUpdate($request, int $id);
    public function getShow(int $id);

}
