<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Admin\ProductModel\ProductModelInterface;
use App\Traits;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\ProductModelRequest;
use App\Models\ProductModel;
use App\Models\Brand;

class ProductModelController extends BaseController
{
    protected $productModelInt;

    public function __construct(
        ProductModelInterface       $productModelInt,
        Brand                       $brand
    )
    {
        $this->productModelInt      = $productModelInt;
        $this->brand               = $brand;
    }

    public function getIndex(Request $request) {

        $this->resp = $this->productModelInt->getPaginatedList($request);

        return view('admin.product-model.index')
        ->withModel($this->resp->data);

    }

    public function getCreate() {
        $data[] = '';
        $data['brand_combo']        =  $this->brand->getBrandCombo();

        return view('admin.product-model.create')
            ->withData($data);

    }

    public function postStore(ProductModelRequest $request) {

        $this->resp = $this->productModelInt->postStore($request);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);

    }

    public function putUpdate(ProductModelRequest $request, $PK_NO) {

        $this->resp = $this->productModelInt->postUpdate($request, $PK_NO);
        
        if($request->row_no){

            $previousUrl = app('url')->previous();
            $previousUrl = $this->shapeSpace_add_var($previousUrl, array('row_no' => $request->row_no));
            
            return redirect()->to($previousUrl)->with($this->resp->redirect_class, $this->resp->msg);
        }
        
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function shapeSpace_add_var($url, $array ) 
    {
    
        $url_decomposition = parse_url ($url);
    $cut_url = explode('?', $url);
    $queries = array_key_exists('query',$url_decomposition)?$url_decomposition['query']:false;
    $queries_array = array ();
    if ($queries) {
        $cut_queries   = explode('&', $queries);
        foreach ($cut_queries as $k => $v) {
            if ($v)
            {
                $tmp = explode('=', $v);
                if (sizeof($tmp ) < 2) $tmp[1] = true;
                $queries_array[$tmp[0]] = urldecode($tmp[1]);
            }
        }
    }
    $newQueries = array_merge($queries_array,$array);
    return $cut_url[0].'?'.http_build_query($newQueries);

    }

    public function getEdit(Request $request, $PK_NO) {

        $brand_combo        =  $this->brand->getBrandCombo();
        $this->resp = $this->productModelInt->getShow($PK_NO);

        if (!$this->resp->status) {
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }

        return view('admin.product-model.edit')
        ->withModel($this->resp->data)
        ->withBrand($brand_combo);
    }

    public function getDelete($PK_NO) {

        $this->resp = $this->productModelInt->delete($PK_NO);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

}

