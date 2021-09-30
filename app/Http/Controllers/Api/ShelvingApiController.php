<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Api\Shelving\ShelvingInterface;

class ShelvingApiController extends Controller
{
    protected $user;
    protected $shelve;

    public function __construct(ShelvingInterface $shelve)
    {
        $this->shelve = $shelve;
    }

    public function postShelving(Request $request)
    {
        $response = $this->shelve->postShelving($request);
        return response()->json($response, $response->code);
    }

    public function postShelvingList(Request $request)
    {
        $response = $this->shelve->postShelvingList($request);
        return response()->json($response, $response->code);
    }

    public function postAllShelveList(Request $request)
    {
        $response = $this->shelve->postAllShelveList($request);
        return response()->json($response, $response->code);
    }

    public function postRtsShelveCheckout(Request $request)
    {
        $response = $this->shelve->postRtsShelveCheckout($request);
        return response()->json($response, $response->code);
    }
}
