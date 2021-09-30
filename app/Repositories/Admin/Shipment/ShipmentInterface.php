<?php

namespace App\Repositories\Admin\Shipment;

interface ShipmentInterface
{
    // public function getPaginatedList($request, int $per_page = 5);
    // public function getProductINV($product);
    public function postStore($request);
    public function postCarrier($request);
    public function deleteShipmentBox($request);
    public function addShipmentBox($request);
    public function postShipmentPackaging($id,$is_update);
    public function getShipment($id);
    public function getShipmentInvoice($id);
    public function getCreate(int $id);
    public function updateShipmentInfo($request, int $id);
    // public function delete($id);
}
