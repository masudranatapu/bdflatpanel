<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\OfferTypeRequest;
use App\Repositories\Admin\OfferType\OfferTypeInterface;

class OfferTypeController extends BaseController
{
    protected $offerTypeInt;

    public function __construct(OfferTypeInterface $offerTypeInt)
    {
        $this->offerTypeInt        = $offerTypeInt;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->offerTypeInt->getPaginatedList($request);
        return view('admin.offer_type.index')->withRows($this->resp->data);
    }

    public function getCreate()
    {
        $data = array();
        return view('admin.offer_type.create',compact('data'));
    }

    public function postStore(OfferTypeRequest $request)
    {
        $this->resp = $this->offerTypeInt->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit($id)
    {
        $this->resp = $this->offerTypeInt->findOrThrowException($id);
        return view('admin.offer_type.edit')->withRow($this->resp->data);
    }

    public function putUpdate(OfferTypeRequest $request, $PK_NO)
    {
        $this->resp = $this->offerTypeInt->postUpdate($request, $PK_NO);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    // public function getDelete($PK_NO) {

    //     $this->resp = $this->offerInt->delete($PK_NO);

    //     return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    // }
}
