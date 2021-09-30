<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Api\Product\ProductInterface;

class ProductApiController extends Controller
{
    protected $user;
    protected $product;

    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function getProductList(){

        $response = $this->product->getProductList();
        return response()->json($response, $response->code);
    }

    public function getVariantList(Request $request){

        $response = $this->product->getVariantList($request->pk_id);
        return response()->json($response, $response->code);
    }

    public function getAllVariantList(Request $request){

        $response = $this->product->getAllVariantList($request);
        return response()->json($response, $response->code);
    }

    public function getVariantImg(Request $request){

        $response = $this->product->getVariantImg($request->PK_ID);
        return response()->json($response, $response->code);
    }

    public function getStockSearchList(Request $request){

        $response = $this->product->getStockSearchList($request);
        return response()->json($response, $response->code);
    }

    public function getProductBox(Request $request){

        $response = $this->product->getProductBox($request);
        return response()->json($response, $response->code);
    }

    public function getRebox(Request $request){

        $response = $this->product->getRebox($request);
        return response()->json($response, $response->code);
    }

    public function getUnboxList(Request $request){

        $response = $this->product->getUnboxList($request);
        return response()->json($response, $response->code);
    }

    public function getUnbox(Request $request){

        $response = $this->product->getUnbox($request);
        return response()->json($response, $response->code);
    }

    public function postProductDetailsList(Request $request){

        $response = $this->product->postProductDetailsList($request);
        return response()->json($response, $response->code);
    }

    public function postProductSearchList(Request $request){

        $response = $this->product->postProductSearchList($request);
        return response()->json($response, $response->code);
    }

    public function postProductSearchListDetailsMy(Request $request){

        $response = $this->product->postProductSearchListDetailsMy($request);
        return response()->json($response, $response->code);
    }

    public function postProductSearchListMy(Request $request){

        $response = $this->product->postProductSearchListMy($request);
        return response()->json($response, $response->code);
    }

    public function postProductBoxLocation(Request $request){

        $response = $this->product->postProductBoxLocation($request);
        return response()->json($response, $response->code);
    }
}
