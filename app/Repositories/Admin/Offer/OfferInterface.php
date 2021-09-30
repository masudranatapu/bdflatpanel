<?php

namespace App\Repositories\Admin\Offer;

interface OfferInterface
{
    public function getPaginatedList($request, int $per_page = 5);
    public function postStore($request);
    public function postUpdate($request, int $id);
    public function delete($id);
}
