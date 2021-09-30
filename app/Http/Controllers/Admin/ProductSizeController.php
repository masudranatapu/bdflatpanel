<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Admin\ProductSize\ProductSizeInterface;
use App\Traits;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\ProductSizeRequest;
use App\Models\ProductSize;
use App\Models\Brand;

class ProductSizeController extends BaseController
{
    protected $productSizeInt;

    public function __construct(
        ProductSizeInterface       $productSizeInt,
        Brand                       $brand
    )
    {
        $this->productSizeInt      = $productSizeInt;
        $this->brand               = $brand;
    }

    public function getIndex(Request $request) {

        $this->resp = $this->productSizeInt->getPaginatedList($request);

        return view('admin.product-size.index')
        ->withSize($this->resp->data);

    }

    public function getCreate() {
        $data[] = '';
        $data['brand_combo']        =  $this->brand->getBrandCombo();

        return view('admin.product-size.create')
            ->withData($data);

    }

    public function postStore(ProductSizeRequest $request) {

        $this->resp = $this->productSizeInt->postStore($request);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);

    }

    public function putUpdate(ProductSizeRequest $request, $id) {

        
        
        $this->resp = $this->productSizeInt->postUpdate($request, $id);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id) {


        $brand_combo        =  $this->brand->getBrandCombo();
        $this->resp = $this->productSizeInt->getShow($id);

        //dd($brand_combo);      
        
        if (!$this->resp->status) {
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }

        return view('admin.product-size.edit')->withSize($this->resp->data)->withBrand($brand_combo);
    }

    public function getDelete($PK_NO) {

        $this->resp = $this->productSizeInt->delete($PK_NO);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

}

