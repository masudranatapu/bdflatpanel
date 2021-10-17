<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\OfferPrimary\OfferPrimaryInterface;

class OfferPrimaryController extends BaseController
{
    protected $offerPrimary;

    public function __construct(OfferPrimaryInterface $offerPrimary)
    {
        $this->offerPrimary        = $offerPrimary;

    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->offerPrimary->getPaginatedList($request);
        return view('admin.offer_primary.index')->withRows($this->resp->data);
    }

    public function getCreate()
    {
        return view('admin.offer_primary.create');
    }

    public function postStore(Request $request)
    {
        $this->resp = $this->offerPrimary->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
    public function getEdit($id)
    {
        $this->resp = $this->offerPrimary->findOrThrowException($id);
        return view('admin.offer_primary.edit')->withRow($this->resp->data);
    }

    public function getView($id)
    {
        $this->resp = $this->offerPrimary->findOrThrowException($id);
        return view('admin.offer_primary.view')->withRow($this->resp->data);
    }


    public function putUpdate(Request $request, $PK_NO) {

        $this->resp = $this->offerPrimary->postUpdate($request, $PK_NO);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id) {

        $this->resp = $this->offerInt->delete($id);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }
    public function getAddProduct($id)
    {
        $this->resp = $this->offerPrimary->findOrThrowException($id);
        \Session::put('list_type', '');
        return view('admin.offer_primary.add_product')->withRow($this->resp->data);
    }

    public function getVariantList(Request $request)
    {
        $this->resp = $this->offerPrimary->getVariantList($request);
        return response()->json($this->resp);
    }
    public function postStoreProduct(Request $request)
    {
        $this->resp = $this->offerPrimary->postStoreProduct($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);

    }

    public function getDeleteProduct($id) {

        $this->resp = $this->offerPrimary->getDeleteProduct($id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }



}
