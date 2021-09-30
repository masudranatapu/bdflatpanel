<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AddressRequest;
use App\Repositories\Admin\Address\AddressInterface;

class AddressController extends BaseController
{
    protected $address;
    protected $state;

    public function __construct(AddressInterface $address, State $state)
    {
        $this->address  = $address;
        $this->state    = $state;
    }

    public function getIndex(Request $request)
    {
        $this->address_resp = $this->address->getPaginatedList($request, 20);
        return view('admin.address-type.index')->withRows($this->address_resp->data);

    }

    public function getCreate() {

        return view('admin.address-type.create');
    }

    public function postStore(AddressRequest $request) {

        $this->resp = $this->address->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id){

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

    public function getCityAddress($id=null){

        $this->resp = $this->address->getCityAddress($id);
        return view('admin.customer-address.city_address')->withData($this->resp->data);
    }

    public function getPostageAddress($id=null){

        $this->resp = $this->address->getPostageAddress($id);
        return view('admin.customer-address.postage_address')->withData($this->resp->data);
    }

    public function ajaxStateByCountry($country)
    {
        $state = $this->state->getStateByCountry($country);
        return $state;
    }

    public function postCityAddress(Request $request,$id)
    {
        $this->resp = $this->address->postCityAddress($request,$id);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postPostageAddress(Request $request,$id)
    {
        $this->resp = $this->address->postPostageAddress($request,$id);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getCityList()
    {
        //$this->address_resp = $this->address->getCityList();
        //return view('admin.customer-address.city_index')->withRows($this->address_resp->data);
        return view('admin.customer-address.form');
    }

    public function getPostageList()
    {
        $this->address_resp = $this->address->getPostageList();
        return view('admin.customer-address.postcode_index')->withRows($this->address_resp->data);
    }
}
