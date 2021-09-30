<?php


namespace App\Repositories\Admin\PropertyFeatures;


interface PropertyFeaturesInterface
{
    public function getFeatures($limit = 2000);

    public function getFeature(int $id);

    public function postStore($request);

    public function postUpdate($request, int $id);

    public function getFacings($limit = 2000);

    public function getFacing(int $id);

    public function storeFacing($request);

    public function updateFacing($request, int $id);

    public function getFloors($limit = 2000);

    public function getFloor(int $id);

    public function storeFloor($request);

    public function updateFloor($request, int $id);
}
