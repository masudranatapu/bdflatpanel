<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Repositories\Admin\PropertyCategory\PropertyCategoryInterface;
use App\Http\Requests\Admin\PropertyCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyCategoryController extends BaseController
{
    protected $category;
    protected $resp;

    public function __construct(PropertyCategoryInterface $category)
    {
        parent::__construct();
        $this->category = $category;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->category->getPaginatedList($request, 20);
        return view('admin.property-category.index')->withRows($this->resp->data);
    }

    public function getCreate()
    {
        return view('admin.property-category.create');
    }

    public function postStore(PropertyCategoryRequest $request)
    {
        //dd($request->All());
        $this->resp = $this->category->postStore($request);
        //dd($this->resp);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id)
    {

        $this->resp = $this->category->findOrThrowException($id);
        //dd($this->resp->data);
        return view('admin.property-category.edit')->withCategory($this->resp->data);

    }

    public function postUpdate(PropertyCategoryRequest $request, $id)
    {
        $this->resp = $this->category->postUpdate($request, $id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
}
