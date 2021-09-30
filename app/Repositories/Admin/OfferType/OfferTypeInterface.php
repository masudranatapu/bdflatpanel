<?php

namespace App\Repositories\Admin\OfferType;

interface OfferTypeInterface
{
    public function getPaginatedList($request, int $per_page = 5);
    public function postStore($request);
    public function postUpdate($request, int $id);
    public function findOrThrowException(int $id);
    public function delete($id);
}
