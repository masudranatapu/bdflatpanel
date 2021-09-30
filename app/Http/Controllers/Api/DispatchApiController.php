<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Api\Dispatch\DispatchInterface;

class DispatchApiController extends Controller
{
    protected $dispatch;

    public function __construct(DispatchInterface $dispatch)
    {
        $this->dispatch = $dispatch;
    }

    public function postCodRtcDispatchList(Request $request)
    {
        $response = $this->dispatch->postCodRtcDispatchList($request);
        return response()->json($response, $response->code);
    }

    public function postCodRtcDispatchTransfer(Request $request)
    {
        $response = $this->dispatch->postCodRtcDispatchTransfer($request);
        return response()->json($response, $response->code);
    }

    public function postCodRtcBoxItemTransfer(Request $request)
    {
        $response = $this->dispatch->postCodRtcBoxItemTransfer($request);
        return response()->json($response, $response->code);
    }

    public function postCodRtcAcknowledge(Request $request)
    {
        $response = $this->dispatch->postCodRtcAcknowledge($request);
        return response()->json($response, $response->code);
    }

    public function postCodRtcDispatch(Request $request)
    {
        $response = $this->dispatch->postCodRtcDispatch($request);
        return response()->json($response, $response->code);
    }

    public function postCodRtcOrderList(Request $request)
    {
        $response = $this->dispatch->postCodRtcOrderList($request);
        return response()->json($response, $response->code);
    }

    public function postCodRtcDispatchItem(Request $request)
    {
        $response = $this->dispatch->postCodRtcDispatchItem($request);
        return response()->json($response, $response->code);
    }

    public function postRtsDispatchList(Request $request)
    {
        $response = $this->dispatch->postRtsDispatchList($request);
        return response()->json($response, $response->code);
    }

    public function postRtsDispatchedItemList(Request $request)
    {
        $response = $this->dispatch->postRtsDispatchedItemList($request);
        return response()->json($response, $response->code);
    }

    public function postRtsDispatchedList(Request $request)
    {
        $response = $this->dispatch->postRtsDispatchedList($request);
        return response()->json($response, $response->code);
    }

    public function postCOnsignmentList(Request $request)
    {
        $response = $this->dispatch->postCOnsignmentList($request);
        return response()->json($response, $response->code);
    }

    public function postRtsBatchList(Request $request)
    {
        $response = $this->dispatch->postRtsBatchList($request);
        return response()->json($response, $response->code);
    }

    public function postDispatch(Request $request)
    {
        $response = $this->dispatch->postDispatch($request);
        return response()->json($response, $response->code);
    }

    public function postProductOfTrackingNo(Request $request)
    {
        $response = $this->dispatch->postProductOfTrackingNo($request);
        return response()->json($response, $response->code);
    }

    public function postCodRtsZone(Request $request)
    {
        $response = $this->dispatch->postCodRtsZone($request);
        return response()->json($response, $response->code);
    }
}
