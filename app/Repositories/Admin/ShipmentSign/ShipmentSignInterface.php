<?php

namespace App\Repositories\Admin\ShipmentSign;

interface ShipmentSignInterface
{
    public function getPaginatedList($request, int $per_page = 20);
    public function postStore($request);
    // public function postUpdate($request, int $id);
    public function getShow(int $id);

}
