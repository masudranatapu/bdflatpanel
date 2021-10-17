<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\State;
use App\Models\WebInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\WebInfoRequest;
use App\Repositories\Admin\WebInfo\WebInfoInterface;

class WebInfoController extends BaseController
{
    protected $generalInfo;
    protected $webInfo;
    protected $resp;

    public function __construct(WebInfoInterface $generalInfo, WebInfo $webInfo)
    {
        parent::__construct();

        $this->generalInfo = $generalInfo;
        $this->webInfo = $webInfo;
    }

    public function getIndex(Request $request)
    {
        //$this->address_resp = $this->address->getPaginatedList($request, 20);
        return view('admin.general-info.index')->withRows($this->address_resp->data);

    }

    public function getCreate()
    {
        $data['webInfo'] = $this->webInfo->where('PK_NO', 1)->first();
        return view('admin.general-info.create', compact('data'));
    }

    public function postStore(WebInfoRequest $request): RedirectResponse
    {
//        dd($request->file('images'));
        $this->resp = $this->generalInfo->postStore($request);
        //dd($this->resp);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id)
    {

        $this->resp = $this->address->findOrThrowException($id);
        return view('admin.address-type.edit')->withAddress($this->resp->data);

    }

    public function postUpdate(AddressRequest $request, $id)
    {
        $this->resp = $this->address->postUpdate($request, $id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->address->delete($id);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getCityAddress($id = null)
    {

        $this->resp = $this->address->getCityAddress($id);
        return view('admin.customer-address.city_address')->withData($this->resp->data);
    }

    public function getPostageAddress($id = null)
    {

        $this->resp = $this->address->getPostageAddress($id);
        return view('admin.customer-address.postage_address')->withData($this->resp->data);
    }

    public function ajaxStateByCountry($country)
    {
        $state = $this->state->getStateByCountry($country);
        return $state;
    }

    public function postCityAddress(Request $request, $id)
    {
        $this->resp = $this->address->postCityAddress($request, $id);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postPostageAddress(Request $request, $id)
    {
        $this->resp = $this->address->postPostageAddress($request, $id);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getCityList()
    {
        $this->address_resp = $this->address->getCityList();
        return view('admin.customer-address.city_index')->withRows($this->address_resp->data);
    }

    public function getPostageList()
    {
        $this->address_resp = $this->address->getPostageList();
        return view('admin.customer-address.postcode_index')->withRows($this->address_resp->data);
    }
}
