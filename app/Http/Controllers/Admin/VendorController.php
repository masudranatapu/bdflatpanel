<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Vendor\VendorInterface;
use App\Http\Requests\Admin\VendorRequest;
use App\Models\Country;
use Illuminate\Http\Request;

class VendorController extends BaseController
{
    protected $vendor;
    protected $country;

    public function __construct(VendorInterface $vendor, Country $country)
    {
        $this->vendor   = $vendor;
        $this->country  = $country;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->vendor->getPaginatedList($request, 20);
        return view('admin.procurement.vendor.index')
            ->withRows($this->resp->data);
    }

    public function getCreate() {
        $country = $this->country->getCountryCombo();
        return view('admin.procurement.vendor.create')
            ->withCountry($country);
    }

    public function postStore(VendorRequest $request) {

        $this->resp = $this->vendor->postStore($request);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id)
    {
        $this->resp = $this->vendor->findOrThrowException($id);
        $country    = $this->country->getCountryCombo();

        if (!$this->resp->status) {
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }

        return view('admin.procurement.vendor.edit')
            ->withVendor($this->resp->data)
            ->withCountry($country);
    }

    public function postUpdate(VendorRequest $request, $id)
    {
        $this->resp = $this->vendor->postUpdate($request, $id);
        
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->vendor->delete($id);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

}
