<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Category\CategoryInterface;
use App\Http\Requests\Admin\CategoryRequest;
use Illuminate\Http\Request;
use DB;

class CategoryController extends BaseController
{
    protected $category;

    public function __construct(CategoryInterface $category)
    {
        $this->category  = $category;
    }

    public function getIndex(Request $request)
    {
        $this->category_resp = $this->category->getPaginatedList($request, 20);
        return view('admin.category.index')->withRows($this->category_resp->data);

    }

    public function getCreate() {

        return view('admin.category.create');
    }

    public function postStore(CategoryRequest $request) {

        $this->resp = $this->category->postStore($request);
        //dd($this->resp);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id){

        $this->resp = $this->category->findOrThrowException($id);
        //dd($this->resp->data);
        return view('admin.category.edit')->withCategory($this->resp->data);

    }

    public function postUpdate(CategoryRequest $request, $id)
    {
        //dd($id);
        $this->resp = $this->category->postUpdate($request, $id);
        //dd($this->resp->data);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->category->delete($id);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }


}
