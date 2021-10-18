<?php

namespace App\Repositories\Admin\SearchBooking;

interface SearchBookingInterface
{
    public function getPaginatedList($request, int $id = null, $type = null);
    public function getProductINV($product);
    public function postStore($request);
    public function findOrThrowException($id, int $checkoffer = null);
    public function postUpdate($request, int $id,string $type);
    public function getCusInfo($type,$customer);
    public function delete($id);
    public function postOfferApply($request);
}
