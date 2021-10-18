<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\Agent;
use App\Models\PoCode;
use App\Models\Country;
use App\Models\ShopCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\ShopCategoryRequest;
use App\Repositories\Admin\Shopcategory\ShopcategoryInterface;


class ShopCategoryController extends BaseController
{

    protected $shopcategory;
    protected $agent;
    protected $country;
    public function __construct(ShopcategoryInterface $shopcategory, Agent $agent, Country $country)
    {
        $this->shopcategory         = $shopcategory;
        $this->agent                = $agent;
        $this->country              = $country;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->shopcategory->getPaginatedList($request, 20);
        $data['rows'] = $this->resp->data;
        return view('admin.shop-category.index',compact('data'));
    }

    public function getCreate()
    {
        return view('admin.shop-category.create');
    }

    public function postStore(ShopCategoryRequest $request) {

        $this->resp = $this->shopcategory->postStore($request);
        //dd($this->resp);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id){

        $this->resp = $this->shopcategory->findOrThrowException($id);
        //dd($this->resp->data);
        return view('admin.shop-category.edit')->withCategory($this->resp->data);

    }

    public function postUpdate(ShopCategoryRequest $request, $id)
    {
        //dd($id);
        $this->resp = $this->shopcategory->postUpdate($request, $id);
        //dd($this->resp->data);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->shopcategory->delete($id);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

   

}
