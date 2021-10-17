<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Repositories\Admin\SubCategory\SubCategoryInterface;
use App\Http\Requests\Admin\SubCategoryRequest;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Models\Category;

use DB;

class SubCategoryController extends BaseController
{
     public function __construct(SubCategoryInterface $sub_category, Category $category)
     {
         $this->sub_category  = $sub_category;
         $this->category  = $category;
         //dd($this->category);

     }

    public function getIndex(Request $request)
    {
        $this->resp = $this->sub_category->getPaginatedList($request, 10);
        return view('admin.sub-category.index')->withRows($this->resp->data);
    }

    public function getCreate() {
        $data = array();
        $data['category_combo']     =  $this->category->getCategorCombo();

        return view('admin.sub-category.create')->withData($data);
    }

    public function postStore(SubCategoryRequest $request) {

        $this->resp = $this->sub_category->postStore($request);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id){

    $data                       = array();
    $this->resp                 = $this->sub_category->findOrThrowException($id);
    $data['category_combo']     =  $this->category->getCategorCombo();
    return view('admin.sub-category.edit')->withSubcategory($this->resp)->withData($data);

    }

    public function postUpdate(SubCategoryRequest $request, $id)
    {
        $this->resp = $this->sub_category->postUpdate($request, $id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->sub_category->delete($id);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }
}
