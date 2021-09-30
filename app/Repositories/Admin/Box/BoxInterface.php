<?php

namespace App\Repositories\Admin\Box;

interface BoxInterface
{
    // public function getPaginatedList($request, int $per_page = 5);
    // public function getProductINV($product);
    // public function postStore($request);
    // public function deleteShipmentBox($request);
    // public function addShipmentBox($request);
    public function postUpdate($request);
    public function postBoxTypeStore($request);
    // public function getCusInfo($type,$customer);
    // public function delete($id);
     public function getBox($id);
     public function getBoxTypeAdd($id);
     public function getBoxTypeDelete($id);
     public function getBoxTypeList();
}
