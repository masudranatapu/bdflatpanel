<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Web\BlogCategory;
use App\Http\Requests\Web\BlogCategoryRequest;
use Auth;
class BlogCategoryController extends Controller
{

    protected $category;

    public function __construct(BlogCategory $category)
    {
        $this->category     = $category;
    }

    public function getAllCategory(Request $request){
        $this->resp = $this->category->getPaginatedList($request);
        $data['category'] = $this->resp->data;
        return view('admin.web.category.index')->withData($data);
    }

     public function getCreate(){
        return view('admin.web.category.create');
    }

    public function postStore(BlogCategoryRequest $request)
    {
        $this->resp = $this->category->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id)
    {
        $data[] = '';
        $this->resp = $this->category->getShow($id);
        if (!$this->resp->status) {
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }
        return view('admin.web.category.edit')->withData($this->resp->data);
    }

    public function postUpdate(BlogCategoryRequest $request, $id)
    {
        $this->resp = $this->category->postUpdate($request, $id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }


    public function getDelete($id)
    {
        $this->resp = $this->category->getDelete($id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function changeFeatureStatus(Request $request){
        $id                     = $request->id;
        $category               = BlogCategory::findOrFail($id);
        $category->IS_FEATURE   = !$category->IS_FEATURE;
        $category->MODIFIED_BY  = Auth::user()->PK_NO;
        $category->update();
        return response()->json($category);
    }

}
