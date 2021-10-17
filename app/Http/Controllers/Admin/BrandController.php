<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Brand\BrandInterface;
use App\Http\Requests\Admin\BrandRequest;
use Illuminate\Http\Request;
use DB;

class BrandController extends BaseController
{
    protected $userGroup;

    public function __construct(BrandInterface $brand)
    {
        $this->brand   = $brand;
    }

    public function getIndex(Request $request)
    {
        $this->brand_resp = $this->brand->getPaginatedList($request, 500);
        return view('admin.product-brand.index')->withRows($this->brand_resp->data);

    }

    public function getCreate() {

        return view('admin.product-brand.create');
    }

    public function postStore(BrandRequest $request) {

        $this->resp = $this->brand->postStore($request);
        //dd($this->resp);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postEdit(Request $request, $id){

        $this->resp = $this->brand->findOrThrowException($id);
        //dd($this->resp->data);
        return view('admin.product-brand.edit')->withBrand($this->resp->data);

    }

    public function postUpdate(BrandRequest $request, $id)
    {
        // dd($id);
        $this->resp = $this->brand->postUpdate($request, $id);
        //dd($this->resp->data);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->brand->delete($id);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }


}
