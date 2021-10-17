<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\OfferSecondary\OfferSecondaryInterface;

class OfferSecondaryController extends BaseController
{
    protected $offerSecondary;

    public function __construct(OfferSecondaryInterface $offerSecondary)
    {
        $this->offerSecondary        = $offerSecondary;

    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->offerSecondary->getPaginatedList($request);
        return view('admin.offer_secondary.index')->withRows($this->resp->data);
    }

    public function getCreate()
    {
        return view('admin.offer_secondary.create');
    }

    public function postStore(Request $request)
    {
        $this->resp = $this->offerSecondary->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
    public function getEdit($id)
    {
        $this->resp = $this->offerSecondary->findOrThrowException($id);
        return view('admin.offer_secondary.edit')->withRow($this->resp->data);
    }
    public function getView($id)
    {
        $this->resp = $this->offerSecondary->findOrThrowException($id);
        return view('admin.offer_secondary.view')->withRow($this->resp->data);
    }

    public function putUpdate(Request $request, $PK_NO) {

        $this->resp = $this->offerSecondary->postUpdate($request, $PK_NO);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id) {

        $this->resp = $this->offerInt->delete($id);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }
    public function getAddProduct($id)
    {
        $this->resp = $this->offerSecondary->findOrThrowException($id);
        return view('admin.offer_secondary.add_product')->withRow($this->resp->data);
    }

    public function getVariantList(Request $request)
    {
        $this->resp = $this->offerSecondary->getVariantList($request);
        return response()->json($this->resp);
    }
    public function postStoreProduct(Request $request)
    {
        $this->resp = $this->offerSecondary->postStoreProduct($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);

    }

    public function getDeleteProduct($id) {

        $this->resp = $this->offerSecondary->getDeleteProduct($id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }



}
