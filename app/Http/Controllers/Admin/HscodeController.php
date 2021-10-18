<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Hscode\HscodeInterface;
use App\Http\Requests\Admin\HscodeRequest;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Models\Category;

use DB;

class HscodeController extends BaseController
{
    

     
     public function __construct(HscodeInterface $hscode, SubCategory $subcategory)
     {
         $this->hscode  = $hscode;
         $this->subcategory  = $subcategory;
         //dd($this->category);

     }

    public function getIndex(Request $request)
    {
        $this->resp = $this->hscode->getPaginatedList($request, 10);
        return view('admin.hscode.index')->withRows($this->resp->data);
       

    }

    public function getCreate() {
        // $data = array();   
        // return view('admin.hscode.create')->withData($data);
    }

     public function postStore(HscodeRequest $request) {

        $this->resp = $this->hscode->postStore($request);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
     }

     public function getEdit(Request $request, $id){

       // $data                       = array();
        //$this->resp                 = $this->hscode->findOrThrowException($id);

        //return view('admin.hscode.edit')->withData($data);

     }

    public function postUpdate(HscodeRequest $request, $id)
    {
        $this->resp = $this->hscode->postUpdate($request, $id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->hscode->delete($id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }


}
