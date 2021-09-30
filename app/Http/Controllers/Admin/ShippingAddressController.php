<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Models\ShippingAddress;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\ShippingAddressRequest;
use App\Repositories\Admin\ShippingAddress\ShippingAddressInterface;

class ShippingAddressController extends BaseController
{
    public function __construct(ShippingAddressInterface $shipping_add)
    {
        $this->shipping_add         = $shipping_add;

    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->shipping_add->getPaginatedList($request, 20);
        return view('admin.shipment-address.index')
            ->withRows($this->resp->data);
    }

    public function getCreate() {

        return view('admin.shipment-address.create');
    }

    public function postStore(ShippingAddressRequest $request) {

        $this->resp = $this->shipping_add->postStore($request);


        return redirect()->route('admin.shipping-address.list')->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit($id)
    {
        $this->resp = $this->shipping_add->getShow($id);


        return view('admin.shipment-address.edit')->withRow($this->resp->data);
    }

    public function postUpdate(Request $request, $id)
    {
        $this->resp = $this->shipping_add->postUpdate($request, $id);

        return redirect()->route('admin.shipping-address.list',$this->resp->data)->with($this->resp->redirect_class, $this->resp->msg);
    }



}
