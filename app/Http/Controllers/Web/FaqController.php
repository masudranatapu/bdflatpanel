<?php

namespace App\Http\Controllers\Web;
use Auth;
use App\Models\Web\Faq;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Http\Requests\Web\faqRequest;

class FaqController extends Controller
{

    protected $faq;

    public function __construct(Faq $faq)
    {
        $this->faq     = $faq;
    }

    public function getAllFaq(Request $request){
        $this->resp = $this->faq->getPaginatedList($request);
        $data['faqs'] = $this->resp->data;
        return view('admin.web.faq.index')->withData($data);
    }

     public function getCreate(){
        return view('admin.web.faq.create');
    }

    public function postStore(Request $request)
    {
        $this->resp = $this->faq->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id)
    {
        $data[] = '';
        $this->resp = $this->faq->getShow($id);
        if (!$this->resp->status) {
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }
        $data['faq'] = $this->resp->data;
         return view('admin.web.faq.edit')->withData($data);
    }

    public function postUpdate(Request $request, $id)
    {
        $this->resp = $this->faq->postUpdate($request, $id);
        if (!$this->resp->status) {
            return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
        }
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->article->getDelete($id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
    public function changeActiveStatus(Request $request){

        $id                     = $request->id;
        $faq                = Faq::findOrFail($id);
        $faq->IS_ACTIVE     = !$faq->IS_ACTIVE;
        $faq->MODIFIED_BY   = Auth::user()->PK_NO;
        $faq->update();
        return response()->json($faq);
    }

    public function changeFeatureStatus(Request $request){

        $id                     = $request->id;
        $faq                = Faq::findOrFail($id);
        $faq->IS_FEATURE    = !$faq->IS_FEATURE;
        $faq->MODIFIED_BY   = Auth::user()->PK_NO;
        $faq->update();
        return response()->json($faq);
    }

}
