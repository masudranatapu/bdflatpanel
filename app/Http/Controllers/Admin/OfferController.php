<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AccountSource;
use App\Models\Offer;
use App\Models\OfferPrimary;
use App\Models\OfferSecondary;
use App\Models\OfferType;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\OfferRequest;
use App\Repositories\Admin\Offer\OfferInterface;

class OfferController extends BaseController
{
    protected $offerInt;
    protected $offerPrimary;
    protected $offerSecondary;
    protected $offerType;

    public function __construct(OfferInterface $offerInt, AccountSource $accountSource, OfferPrimary $offerPrimary , OfferSecondary $offerSecondary, OfferType $offerType)
    {
        $this->offerInt         = $offerInt;
        $this->accountSource    = $accountSource;
        $this->offerPrimary     = $offerPrimary;
        $this->offerSecondary   = $offerSecondary;
        $this->offerType        = $offerType;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->offerInt->getPaginatedList($request);
        return view('admin.offer.index')->withRows($this->resp->data);
    }

    public function getCreate()
    {
        $data                       = array();
        $data['list_a_combo']       = $this->offerPrimary->pluck('PRIMARY_SET_NAME','PK_NO');
        $data['list_b_combo']       = $this->offerSecondary->pluck('SECONDARY_SET_NAME', 'PK_NO');
        $data['offer_type']         = $this->offerType->get();
        // dd($data);
        return view('admin.offer.create',compact('data'));
    }

    public function getEdit($id)
    {
        $data                       = array();
        $data['list_a_combo']       = $this->offerPrimary->pluck('PRIMARY_SET_NAME','PK_NO');
        $data['list_b_combo']       = $this->offerSecondary->pluck('SECONDARY_SET_NAME', 'PK_NO');
        $data['offer_type']         = $this->offerType->get();
        $data['row']                = Offer::where('PK_NO',$id)->first();
        // dd($data);
        return view('admin.offer.edit',compact('data'));
    }


    public function postStore(OfferRequest $request)
    {
        $this->resp = $this->offerInt->postStore($request);
        //dd($this->resp);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function putUpdate(OfferRequest $request, $pk_no) {

        $this->resp = $this->offerInt->postUpdate($request, $pk_no);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($pk_no) {

        $this->resp = $this->offerInt->delete($pk_no);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }


}
