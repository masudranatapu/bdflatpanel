<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Api\Shipment\ShipmentInterface;

class ShipmentApiController extends Controller
{
    protected $user;
    protected $shipment;

    public function __construct(ShipmentInterface $shipment)
    {
        $this->shipment = $shipment;
    }

    public function ShipmentPost(Request $request){

        $response = $this->shipment->ShipmentPost($request);
        return response()->json($response, $response->code);
    }

    public function shipmentReceived(Request $request){

        $response = $this->shipment->shipmentReceived($request);
        return response()->json($response, $response->code);
    }

    public function shipmentList(Request $request){

        $response = $this->shipment->shipmentList($request);
        return response()->json($response, $response->code);
    }
}
