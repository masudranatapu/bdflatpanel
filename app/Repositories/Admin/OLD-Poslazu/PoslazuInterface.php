<?php

namespace App\Repositories\Admin\Poslazu;
interface PoslazuInterface
{
    public function getConsignmentNote($request);
    public function getTrackingId(int $id);
    public function getPaginatedList($request, int $per_page = 5);
    public function createShipment($request);
    public function getCartList($request);
    public function cartCheckout($key);
    public function getShipmentStatus();
    public function getOrderForDisopatch($id);

}
