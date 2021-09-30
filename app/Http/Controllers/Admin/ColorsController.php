<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Color\ColorInterface;
use App\Http\Requests\Admin\ColorRequest;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Http\Models\CustomUser as User;

class ColorsController extends BaseController
{

    protected $color;
    protected $brand;

    public function __construct(ColorInterface $color, Brand $brand)
    {
        $this->color   = $color;
        $this->brand  = $brand;
    }



    public function getIndex(Request $request)

    {
        $this->resp = $this->color->getPaginatedList($request, 20);

        return view('admin.product-color.index')->withRows($this->resp->data);


    }



    public function getCreate()
    {
        $brand = $this->brand->getBrandCombo();
        return view('admin.product-color.create')->withBrand($brand);
    }


    public function postStore(ColorRequest $request)
    {

        $this->resp = $this->color->postStore($request);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);


    }
    public function getEdit(Request $request, $id){

        $this->resp = $this->color->findOrThrowException($id);
        $brand    = $this->brand->getBrandCombo();
        //dd($this->resp->data);
        //dd($brand);
        return view('admin.product-color.edit')->withColor($this->resp->data)->withBrand($brand);

    }

    public function postUpdate(ColorRequest $request, $id){
        
        $this->resp = $this->color->postUpdate($request, $id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);

    }

    public function getDelete($id)
    {

        $this->resp = $this->color->delete($id);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }
}
