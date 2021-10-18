<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\ShippingAddress;
use App\Models\ShipmentSign;
use App\Models\Carrier;
use App\Models\Vendor;
use App\Models\Shipment;
use App\Models\Warehouse;
use App\Models\Shipmentbox;
use App\Traits\RepoResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Box;
use App\Repositories\Admin\Shipment\ShipmentInterface;

class ShipmentController extends BaseController
{
    use RepoResponse;

    private $shipment;
    private $warehouse;
    private $shipmodel;
    private $shipmentbox;
    private $ShippingAddress;
    private $shipmentSign;

    function __construct(ShipmentInterface $shipment, Warehouse $warehouse, Shipment $shipmodel, Shipmentbox $shipmentbox, Vendor $vendor,ShipmentSign $shipmentSign, ShippingAddress $ShippingAddress)
    {
        $this->shipment           = $shipment;
        $this->warehouse          = $warehouse;
        $this->shipmodel          = $shipmodel;
        $this->shipmentbox        = $shipmentbox;
        $this->shipmentbox        = $shipmentbox;
        $this->ShippingAddress     = $ShippingAddress;
        $this->shipmentSign       = $shipmentSign;
    }

    public function getIndex(Request $request)
    {
        $data = $this->shipmodel->orderBy('PK_NO','DESC')->get();
        foreach ($data as $key => $value) {
            if ($value->SHIPMENT_STATUS > 20) {
                $shipment_box       = Shipmentbox::select('F_BOX_NO')->where('F_SHIPMENT_NO',$value->PK_NO)->get();
                $received_count     = Box::whereIn('PK_NO',$shipment_box)->where('BOX_STATUS','>=',50)->count();
                $value->received    = $received_count;
            }else{
                $value->received    = 0;
            }
        }
        return view('admin.shipment.index')->withShipment($data);
    }

    public function getProcessingIndex()
    {
        $data = Shipment::orderBy('PK_NO','DESC')->get();

        foreach ($data as $key => $value) {
            if ($value->SHIPMENT_STATUS > 20) {
                $shipment_box       = Shipmentbox::select('F_BOX_NO')->where('F_SHIPMENT_NO',$value->PK_NO)->get();
                $received_count     = Box::whereIn('PK_NO',$shipment_box)->where('BOX_STATUS','>=',50)->count();
                $value->received    = $received_count;
            }else{
                $value->received    = 0;
            }
        }
        return view('admin.shipment.processing')->withShipment($data);
    }

    public function getShipment($id)
    {
        $this->resp = $this->shipment->getShipment($id);
        return view($this->resp->redirect_to)->withShipment($this->resp->data);
    }

    public function getShipmentInvoice($id)
    {
        $this->resp = $this->shipment->getShipmentInvoice($id);
        return view($this->resp->redirect_to)->withShipment($this->resp->data);
    }

    public function getCreate($id = null) {

        $this->resp = $this->shipment->getCreate($id);
        $shipment_address   = $this->ShippingAddress->where('IS_ACTIVE',1)->get();
        $shipment_sign      = $this->shipmentSign->get();
        $carrier            = Carrier::where('IS_ACTIVE',1)->pluck('NAME','PK_NO');
        // dd( $carrier );

        return view($this->resp->redirect_to)
        ->withData($this->resp->data)
        ->withSignature($shipment_sign)
        ->withAddress($shipment_address)
        ->withCarrier($carrier);
    }

    public function postStore(Request $request) {

        $this->resp = $this->shipment->postStore($request);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postCarrier(Request $request) {

        $this->resp = $this->shipment->postCarrier($request);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getShipmentAdd($id)
    {
        $data = $this->shipmodel->where('PK_NO', $id)->first();
        // if($data->SHIPMENT_STATUS > 20){
        //     return redirect()->route('admin.shipment.list')->with('flashMessageError', 'Can not add boxes to shipment !');
        // }
        $shipments  = $this->shipmentbox->where('F_SHIPMENT_NO', $id)->orderBy('BOX_SERIAL', 'ASC')->get();
        return view('admin.shipment.shipmentCreate')->withShipmentInfo($data)->withShipments($shipments);
    }

    public function addShipmentBox(Request $request)
    {
        $this->resp = $this->shipment->addShipmentBox($request);
        return $this->resp;
    }

    public function deleteShipmentBox(Request $request)
    {
        $this->resp = $this->shipment->deleteShipmentBox($request);
        return $this->resp;
    }

    public function updateShipmentStatus(Request $request)
    {
        $this->resp = $this->shipment->updateShipmentStatus($request);
        return $this->resp;
    }

    public function postShipmentPackaging($id,$is_update)
    {
        $this->resp = $this->shipment->postShipmentPackaging($id,$is_update);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function updateShipmentInfo(Request $request, $id)
    {
        $this->resp = $this->shipment->updateShipmentInfo($request,$id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

}

