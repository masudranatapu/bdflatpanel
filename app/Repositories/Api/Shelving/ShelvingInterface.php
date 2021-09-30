<?php

namespace App\Repositories\Api\Shelving;

interface ShelvingInterface
{
    public function postShelving($request);
    public function postShelvingList($request);
    public function postAllShelveList($request);
    public function postRtsShelveCheckout($request);
}
