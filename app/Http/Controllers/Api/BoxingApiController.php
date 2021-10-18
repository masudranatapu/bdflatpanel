<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Api\Boxing\BoxingInterface;

class BoxingApiController extends Controller
{
    protected $user;
    protected $box;

    public function __construct(BoxingInterface $box)
    {
        $this->box = $box;
    }

    public function getProductBox(Request $request){

        $response = $this->box->getProductBox($request);
        return response()->json($response, $response->code);
    }

    public function getRebox(Request $request){

        $response = $this->box->getRebox($request);
        return response()->json($response, $response->code);
    }

    public function getUnboxList(Request $request){

        $response = $this->box->getUnboxList($request);
        return response()->json($response, $response->code);
    }

    public function getUnbox(Request $request){

        $response = $this->box->getUnbox($request);
        return response()->json($response, $response->code);
    }

    public function postBoxList(Request $request)
    {
        $response = $this->box->postBoxList($request);
        return response()->json($response, $response->code);
    }

    public function getPriorityUnboxList(Request $request)
    {
        $response = $this->box->getPriorityUnboxList($request);
        return response()->json($response, $response->code);
    }

    public function priorityUnboxListItem(Request $request)
    {
        $response = $this->box->priorityUnboxListItem($request);
        return response()->json($response, $response->code);
    }

    public function postBoxLabelExists(Request $request)
    {
        $response = $this->box->postBoxLabelExists($request);
        return response()->json($response, $response->code);
    }

    public function postBoxListDetails(Request $request)
    {
        $response = $this->box->postBoxListDetails($request);
        return response()->json($response, $response->code);
    }

    public function postYetToBox(Request $request)
    {
        $response = $this->box->postYetToBox($request);
        return response()->json($response, $response->code);
    }

    public function postUnboxListItem(Request $request){

        $response = $this->box->postUnboxListItem($request);
        return response()->json($response, $response->code);
    }

    public function postBoxLabelUpdate(Request $request){

        $response = $this->box->postBoxLabelUpdate($request);
        return response()->json($response, $response->code);
    }

    public function getUnboxingBoxList(Request $request){

        $response = $this->box->getUnboxingBoxList($request);
        return response()->json($response, $response->code);
    }

    public function getBoxDimention(Request $request){

        $response = $this->box->getBoxDimention($request);
        return response()->json($response, $response->code);
    }
}
