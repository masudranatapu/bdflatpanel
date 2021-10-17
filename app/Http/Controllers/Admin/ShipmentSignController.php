<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Models\ShipmentSign;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\ShipmentSignRequest;
use App\Repositories\Admin\ShipmentSign\ShipmentSignInterface;

class ShipmentSignController extends BaseController
{
    public function __construct(ShipmentSignInterface $shipment_sign)
    {
        $this->shipment_sign         = $shipment_sign;

    }

        public function getIndex(Request $request)
    {
        $this->resp = $this->shipment_sign->getPaginatedList($request, 20);
        return view('admin.shipment-signature.index')->withRows($this->resp->data);
    }

    public function getCreate() {

        return view('admin.shipment-signature.create');
    }

    public function postStore(ShipmentSignRequest $request) {

        $this->resp = $this->shipment_sign->postStore($request);


        return redirect()->route('admin.shipment-signature.list')->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit($id)
    {
        $this->resp = $this->shipment_sign->getShow($id);


        return view('admin.shipment-signature.edit')->withRow($this->resp->data);
    }

    public function postUpdate(Request $request, $id)
    {
        $this->resp = $this->shipment_sign->postUpdate($request, $id);

        return redirect()->route('admin.shipment-signature.list',$this->resp->data)->with($this->resp->redirect_class, $this->resp->msg);
    }

    // public function getDelete($id)
    // {
    //     $this->resp = $this->shipment_sign->delete($id);

    //     return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    // }

    public function getDeleteImage($id)
    {
        $this->resp = $this->shipment_sign->deleteImage($id);
        return response()->json($this->resp);
    }



}
