<?php
namespace App\Repositories\Admin\Shipment;

use DB;
use App\Models\Box;
use App\Models\Stock;
use App\Models\Invoice;
use App\Models\Carrier;
use App\Models\Shipment;
use App\Models\Warehouse;
use App\Models\Shipmentbox;
use App\Traits\RepoResponse;
use App\Models\ShippingAddressSet;

class ShipmentAbstract implements ShipmentInterface
{
    use RepoResponse;
    protected $shipment;
    protected $warehouse;

    public function __construct(Shipment $shipment, Warehouse $warehouse)
    {
        $this->shipment = $shipment;
        $this->warehouse = $warehouse;
    }
    public function postStore($request)
    {
        if ($request->is_update == 0) {
            DB::beginTransaction();
            try {

                $signature = null;
                $sign = DB::table('SC_SIGNATURE')->where('PK_NO',$request->signature)->first();
                $logistics_carrier = Carrier::find($request->logistics_carrier);
                if(!empty($sign)){
                    $signature = $sign->IMG_PATH;
                }
                $shipment                           = new Shipment();
                $shipment->WAYBILL                  = $request->waybill;
                $shipment->F_FROM_INV_WAREHOUSE_NO  = $request->from_warehouse;
                $shipment->F_TO_INV_WAREHOUSE_NO    = $request->to_warehouse;
                $shipment->FREIGHT_GBP              = $request->freight_gbp;
                $shipment->FREIGHT_RM               = $request->freight_rm;
                if($request->shipping_date){
                    $shipment->SCH_DEPARTING_DATE   = date('Y-m-d',strtotime($request->shipping_date));
                }
                if($request->delivery_date){
                    $shipment->SCH_ARRIVAL_DATE     = date('Y-m-d',strtotime($request->delivery_date));
                }
                if($request->packing_process_date){
                    $shipment->PACKING_PROCESS_DATE     = date('Y-m-d',strtotime($request->packing_process_date));
                }

                $shipment->F_SHIPPING_AGENT         = $request->shipping_agent;
                $shipment->F_RECIEVING_AGENT        = $request->receiving_agent;

               // $shipment->F_SENT_BY              = $request->delivery_address;
                //$shipment->F_RECEVIED_BY          = $request->receiving_agent;

                $shipment->F_FROM_ADDRESS           = $request->form_address;
                $shipment->F_SHIP_TO_ADDRESS        = $request->ship_to;
                $shipment->F_BILL_TO_ADDRESS        = $request->bill_to;
                $shipment->F_SIGNATURE              = $request->signature;
                $shipment->SIGNATURE_PATH           = $signature;

                $shipment->IS_AIR_SHIPMENT          = $request->transport == 'air_freight' ? 1 : 0;
                $shipment->SHIPMENT_STATUS          = 10;
                // $shipment->SENDER_BOX_COUNT         = $request->box_count == '' ? 0 : $request->box_count;
                $shipment->F_LOGISTICS_CARRIER      = $request->logistics_carrier;
                $shipment->LOGISTICS_CARRIER        = $logistics_carrier->NAME;
                // dd($shipment);
                $shipment->save();

            } catch (\Exception $e) {

                DB::rollback();
                return $this->formatResponse(false, $e->getMessage(), 'admin.shipment.list');
            }
            DB::commit();
            return $this->formatResponse(true, 'Shipment created successfully !', 'admin.shipment.list');
        }else{
            DB::beginTransaction();
            try {
                $signature = null;

                $sign = DB::table('SC_SIGNATURE')->where('PK_NO',$request->signature)->first();
                $logistics_carrier = Carrier::find($request->logistics_carrier);
                if(!empty($sign)){
                    $signature = $sign->IMG_PATH;
                }

                $shipment                           = Shipment::find($request->is_update);
                $shipment->WAYBILL                  = $request->waybill;
                $shipment->F_FROM_INV_WAREHOUSE_NO  = $request->from_warehouse;
                $shipment->F_TO_INV_WAREHOUSE_NO    = $request->to_warehouse;
                $shipment->FREIGHT_GBP              = $request->freight_gbp;
                $shipment->FREIGHT_RM               = $request->freight_rm;
                if($request->shipping_date){
                    $shipment->SCH_DEPARTING_DATE       = date('Y-m-d',strtotime($request->shipping_date));
                }
                if($request->delivery_date){
                    $shipment->SCH_ARRIVAL_DATE       = date('Y-m-d',strtotime($request->delivery_date));
                }
                if($request->packing_process_date){
                    $shipment->PACKING_PROCESS_DATE     = date('Y-m-d',strtotime($request->packing_process_date));
                }

                $shipment->F_SHIPPING_AGENT         = $request->shipping_agent;
                $shipment->F_RECIEVING_AGENT        = $request->receiving_agent;

               // $shipment->F_SENT_BY              = $request->delivery_address;
                //$shipment->F_RECEVIED_BY          = $request->receiving_agent;

                $shipment->F_FROM_ADDRESS           = $request->form_address;
                $shipment->F_SHIP_TO_ADDRESS        = $request->ship_to;
                $shipment->F_BILL_TO_ADDRESS        = $request->bill_to;
                $shipment->F_SIGNATURE              = $request->signature;
                $shipment->SIGNATURE_PATH           = $signature;

                $shipment->IS_AIR_SHIPMENT          = $request->transport == 'air_freight' ? 1 : 0;
                // $shipment->SHIPMENT_STATUS          = 10;
                // $shipment->SENDER_BOX_COUNT         = $request->box_count == '' ? 0 : $request->box_count;
                $shipment->F_LOGISTICS_CARRIER      = $request->logistics_carrier;
                $shipment->LOGISTICS_CARRIER        = $logistics_carrier->NAME;

                $shipment->update();

                // if ($shipment->SHIPMENT_STATUS == 10) {
                //     Stock::where('F_SHIPPMENT_NO',$shipment->PK_NO)->update(['SHIPMENT_TYPE'=>$request->transport == 'air_freight' ? 'AIR' : 'SEA']);
                // }

            } catch (\Exception $e) {

                DB::rollback();
                return $this->formatResponse(false, $e->getMessage(), 'admin.shipment.list');
            }
            DB::commit();
            return $this->formatResponse(true, 'Shipment updated successfully !', 'admin.shipment.list');
        }

    }

    public function addShipmentBox($request)
    {
        $flag = 0;
        $shipment_label = $request->bar_code1/1000;
        $shipment_label = floor($shipment_label);
        $box_serial     = $request->bar_code1%1000;
        $box_label      = $request->bar_code2;

        $shipment_master = Shipment::select('PK_NO','F_FROM_INV_WAREHOUSE_NO','CODE','SHIPMENT_STATUS','IS_AIR_SHIPMENT')->where('CODE', $shipment_label)->whereRaw('( SHIPMENT_STATUS IS NULL OR SHIPMENT_STATUS = 10 )')->first();
        if (empty($shipment_master)) {
            return $response = $flag+1;
        }

        if ($shipment_master->IS_AIR_SHIPMENT == 1) {
            $ship_type = 'AIR';
        }else{
            $ship_type = 'SEA';
        }

        $box_type = substr($box_label, 0, 1);
        if ($box_type == 1) {
            $box_type = 'AIR';
        }else{
            $box_type = 'SEA';
        }

        // if ($ship_type != $box_type) {
        //     return $response = $flag+7;
        // }

        if ($ship_type == 'SEA' && $box_type == 'AIR') {
            return $response = $flag+7;
        }

        $box_master = Box::select('PK_NO','CODE','BOX_NO','ITEM_COUNT')->where('BOX_NO', $box_label)->whereRaw('BOX_STATUS = 10')->first();
        if (empty($box_master)) {
            return $response = $flag+2;
        }

        $shipment_box = Shipmentbox::select('PK_NO')->where('F_SHIPMENT_NO',$shipment_master->PK_NO)->where('BOX_SERIAL',$box_serial)->first();
        $shipment_box_label = Shipmentbox::select('PK_NO')->where('F_BOX_NO',$box_master->PK_NO)->first();
        if (!empty($shipment_box)) {
            return $response = $flag+3;
        }
        if (!empty($shipment_box_label)) {
            return $response = $flag+4;
        }

        $inv_stock = Stock::select('PREFERRED_SHIPPING_METHOD','FINAL_PREFFERED_SHIPPING_METHOD')->where('F_BOX_NO', $box_master->PK_NO)->get();

        if (count($inv_stock) == 0) {
        return $response = $flag+5;
        }
        if (count($inv_stock) != $box_master->ITEM_COUNT) {
            return $response = $flag+6;
        }

        foreach ($inv_stock as $key => $value) {
            // if ($ship_type != $value->CUSTOMER_PREFFERED_SHIPPING_METHOD || $value->CUSTOMER_PREFFERED_SHIPPING_METHOD != $value->PREFERRED_SHIPPING_METHOD) {
            //     return $response = $flag+8;
            // }
            if ($ship_type == 'SEA' && ($value->FINAL_PREFFERED_SHIPPING_METHOD == 'AIR')) {
                return $response = $flag+8;
            }
        }

        DB::beginTransaction();
        try {
            Stock::where('F_BOX_NO', $box_master->PK_NO)
            ->update([
                    'F_SHIPPMENT_NO'    => $shipment_master->PK_NO,
                    'SHIPMENT_NAME'     => $shipment_master->CODE,
                    'SHIPMENT_TYPE'     => $shipment_master->IS_AIR_SHIPMENT == 1 ? 'AIR' : 'SEA'
                    ]);

            $shipment_box = new Shipmentbox();
            $shipment_box->F_SHIPMENT_NO = $shipment_master->PK_NO;
            $shipment_box->BOX_SERIAL    = $box_serial;
            $shipment_box->F_BOX_NO      = $box_master->PK_NO;
            $shipment_box->PRODUCT_COUNT = $box_master->ITEM_COUNT;
            $shipment_box->save();

            $count = Shipmentbox::where('F_SHIPMENT_NO', $shipment_master->PK_NO)->count();
            // Shipment::where('PK_NO', $shipment_master->PK_NO)->update(['SENDER_BOX_COUNT' => $count]);
            Box::where('BOX_NO', $box_label)->update(['BOX_STATUS' => 20]);

        } catch (\Exception $e) {

            DB::rollback();
            // return $response = $flag+5;
            return $e->getMessage();
        }
        DB::commit();
        return view('admin.shipment._variant_tr')->withItem($box_master)->withSerial($box_serial)->withBoxid($shipment_box->PK_NO)->render();
    }

    public function deleteShipmentBox($request)
    {
        $flag = 0;
        $shipment_box = Shipmentbox::select('F_BOX_NO')->where('PK_NO', $request->id)->first();
        if (empty($shipment_box)) {
            return $response = $flag+1;
        }

        DB::beginTransaction();
        try {
            Stock::where('F_BOX_NO', $shipment_box->F_BOX_NO)->update(['F_SHIPPMENT_NO' => null,'SHIPMENT_NAME' => null,'SHIPMENT_TYPE' => NULL]);
            Box::where('PK_NO', $shipment_box->F_BOX_NO)->update(['BOX_STATUS' => 10]);
            Shipmentbox::where('PK_NO', $request->id)->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return $response = $e->getMessage();
        }
        DB::commit();
        return $response = $flag;
    }

    public function updateShipmentStatus($request)
    {
        $flag = 0;
        $shipment = Shipment::select('SHIPMENT_STATUS')->where('PK_NO', $request->shipment_id)->first();
        $box_id = Shipmentbox::select('F_BOX_NO')->where('F_SHIPMENT_NO',$request->shipment_id)->get();
        $inv_stock = Stock::select('PRODUCT_STATUS', 'F_SHIPPMENT_NO')->where('F_SHIPPMENT_NO', $request->shipment_id)->get();
        if (empty($shipment)) {
            return $response = $flag+1;
        }

        DB::beginTransaction();
        try {
            Shipment::where('PK_NO', $request->shipment_id)->update(['SHIPMENT_STATUS' => $request->status]);

            if ($request->status == 20) {
                    Box::whereIn('PK_NO', $box_id)->update(['BOX_STATUS' => 30]);
                    Stock::where('F_SHIPPMENT_NO', $request->shipment_id)->whereIn('F_BOX_NO', $box_id)->update(['PRODUCT_STATUS' => 30]);
            }
            if ($request->status == 30) {
                DB::statement('CALL PROC_SC_SHIPMENT_CANCELLED(:IN_SHIPMENT_PK_NO, @OUT_STATUS);',
                    array(
                        $request->shipment_id
                    )
                );
                $prc = DB::select('select @OUT_STATUS as OUT_STATUS');
                if ($prc[0]->OUT_STATUS != 'success') {
                    $flag+2;
                }
            }
            if ($request->status == 40) {
                Stock::where('F_SHIPPMENT_NO', $request->shipment_id)->update(['PRODUCT_STATUS' => 40]);
            }
            if ($request->status == 70) {
                    Box::whereIn('PK_NO', $box_id)->update(['BOX_STATUS' => 40]);
                    Stock::where('F_SHIPPMENT_NO', $request->shipment_id)->update(['PRODUCT_STATUS' => 45]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $response = $e->getMessage();
        }
        DB::commit();
        return $response = $flag;
    }

    public function postShipmentPackaging($id,$is_update)
    {
        $response_msg = 'Packaging unsuccessfull !';
        $status = false;

        DB::beginTransaction();
        try {
            DB::statement('CALL PROC_SC_PACKAGING_LIST_INV_STOCK(:shipment_no, :is_update, @OUT_STATUS);',
                array(
                    $id
                    ,$is_update
                )
            );
            $prc = DB::select('select @OUT_STATUS as OUT_STATUS');

            if ($prc[0]->OUT_STATUS == 'success') {

                // $shipment->F_SHIPPING_AGENT         = $request->shipping_agent;
                // $shipment->F_RECIEVING_AGENT        = $request->receiving_agent;

                // $shipment->F_FROM_ADDRESS           = $request->form_address;
                // $shipment->F_SHIP_TO_ADDRESS        = $request->ship_to;
                // $shipment->F_BILL_TO_ADDRESS        = $request->bill_to;

                // $shipment->F_SIGNATURE              = $request->signature;
                // $shipment->SIGNATURE_PATH           = $signature;

                $shipment = $this->shipment->find($id);

                ShippingAddressSet::where('F_SHIPPMENT_NO',$id)->delete();

                $from_agent = $shipment->shippingAddress;
                if(!empty($from_agent)){
                    $f_agent = new ShippingAddressSet();
                    $f_agent->NAME                     = $from_agent->NAME;
                    $f_agent->TEL_NO                   = $from_agent->TEL_NO;
                    $f_agent->ADDRESS_LINE_1           = $from_agent->ADDRESS_LINE_1;
                    $f_agent->ADDRESS_LINE_2           = $from_agent->ADDRESS_LINE_2;
                    $f_agent->ADDRESS_LINE_3           = $from_agent->ADDRESS_LINE_3;
                    $f_agent->ADDRESS_LINE_4           = $from_agent->ADDRESS_LINE_4;
                    $f_agent->LOCATION                 = $from_agent->LOCATION;
                    $f_agent->COUNTRY                  = $from_agent->COUNTRY;
                    $f_agent->STATE                    = $from_agent->STATE;
                    $f_agent->CITY                     = $from_agent->CITY;
                    $f_agent->POST_CODE                = $from_agent->POST_CODE;
                    $f_agent->ADDRESS_TYPE             = $from_agent->ADDRESS_TYPE;
                    $f_agent->VAT_EORI_NO              = $from_agent->VAT_EORI_NO;
                    $f_agent->ATTENTION                = $from_agent->ATTENTION;
                    $f_agent->IS_ACTIVE                = $from_agent->IS_ACTIVE;
                    $f_agent->F_SHIPPMENT_NO           = $id;
                    $f_agent->F_ADDRESS_NO             = $shipment->F_SHIPPING_AGENT;
                    $f_agent->save();
                }
                $to_agent = $shipment->receivingAddress;
                if(!empty($to_agent)){
                    $t_agent = new ShippingAddressSet();
                    $t_agent->NAME                     = $to_agent->NAME;
                    $t_agent->TEL_NO                   = $to_agent->TEL_NO;
                    $t_agent->ADDRESS_LINE_1           = $to_agent->ADDRESS_LINE_1;
                    $t_agent->ADDRESS_LINE_2           = $to_agent->ADDRESS_LINE_2;
                    $t_agent->ADDRESS_LINE_3           = $to_agent->ADDRESS_LINE_3;
                    $t_agent->ADDRESS_LINE_4           = $to_agent->ADDRESS_LINE_4;
                    $t_agent->LOCATION                 = $to_agent->LOCATION;
                    $t_agent->COUNTRY                  = $to_agent->COUNTRY;
                    $t_agent->STATE                    = $to_agent->STATE;
                    $t_agent->CITY                     = $to_agent->CITY;
                    $t_agent->POST_CODE                = $to_agent->POST_CODE;
                    $t_agent->ADDRESS_TYPE             = $to_agent->ADDRESS_TYPE;
                    $t_agent->VAT_EORI_NO              = $to_agent->VAT_EORI_NO;
                    $t_agent->ATTENTION                = $to_agent->ATTENTION;
                    $t_agent->IS_ACTIVE                = $to_agent->IS_ACTIVE;
                    $t_agent->F_SHIPPMENT_NO           = $id;
                    $t_agent->F_ADDRESS_NO             = $shipment->F_RECIEVING_AGENT;
                    $t_agent->save();
                }
                $from_address = $shipment->fromAddress;
                if(!empty($from_address)){
                    $f_add = new ShippingAddressSet();
                    $f_add->NAME                     = $from_address->NAME;
                    $f_add->TEL_NO                   = $from_address->TEL_NO;
                    $f_add->ADDRESS_LINE_1           = $from_address->ADDRESS_LINE_1;
                    $f_add->ADDRESS_LINE_2           = $from_address->ADDRESS_LINE_2;
                    $f_add->ADDRESS_LINE_3           = $from_address->ADDRESS_LINE_3;
                    $f_add->ADDRESS_LINE_4           = $from_address->ADDRESS_LINE_4;
                    $f_add->LOCATION                 = $from_address->LOCATION;
                    $f_add->COUNTRY                  = $from_address->COUNTRY;
                    $f_add->STATE                    = $from_address->STATE;
                    $f_add->CITY                     = $from_address->CITY;
                    $f_add->POST_CODE                = $from_address->POST_CODE;
                    $f_add->ADDRESS_TYPE             = $from_address->ADDRESS_TYPE;
                    $f_add->VAT_EORI_NO              = $from_address->VAT_EORI_NO;
                    $f_add->ATTENTION                = $from_address->ATTENTION;
                    $f_add->IS_ACTIVE                = $from_address->IS_ACTIVE;
                    $f_add->F_SHIPPMENT_NO           = $id;
                    $f_add->F_ADDRESS_NO             = $shipment->F_FROM_ADDRESS;
                    $f_add->save();
                }
                $to_address = $shipment->toAddress;
                if(!empty($to_address)){
                    $t_add = new ShippingAddressSet();
                    $t_add->NAME                     = $to_address->NAME;
                    $t_add->TEL_NO                   = $to_address->TEL_NO;
                    $t_add->ADDRESS_LINE_1           = $to_address->ADDRESS_LINE_1;
                    $t_add->ADDRESS_LINE_2           = $to_address->ADDRESS_LINE_2;
                    $t_add->ADDRESS_LINE_3           = $to_address->ADDRESS_LINE_3;
                    $t_add->ADDRESS_LINE_4           = $to_address->ADDRESS_LINE_4;
                    $t_add->LOCATION                 = $to_address->LOCATION;
                    $t_add->COUNTRY                  = $to_address->COUNTRY;
                    $t_add->STATE                    = $to_address->STATE;
                    $t_add->CITY                     = $to_address->CITY;
                    $t_add->POST_CODE                = $to_address->POST_CODE;
                    $t_add->ADDRESS_TYPE             = $to_address->ADDRESS_TYPE;
                    $t_add->VAT_EORI_NO              = $to_address->VAT_EORI_NO;
                    $t_add->ATTENTION                = $to_address->ATTENTION;
                    $t_add->IS_ACTIVE                = $to_address->IS_ACTIVE;
                    $t_add->F_SHIPPMENT_NO           = $id;
                    $t_add->F_ADDRESS_NO             = $shipment->F_SHIP_TO_ADDRESS;
                    $t_add->save();
                }
                $bill_address = $shipment->billAddress;

                if(!empty($bill_address)){
                    $b_add = new ShippingAddressSet();
                    $b_add->NAME                     = $bill_address->NAME;
                    $b_add->TEL_NO                   = $bill_address->TEL_NO;
                    $b_add->ADDRESS_LINE_1           = $bill_address->ADDRESS_LINE_1;
                    $b_add->ADDRESS_LINE_2           = $bill_address->ADDRESS_LINE_2;
                    $b_add->ADDRESS_LINE_3           = $bill_address->ADDRESS_LINE_3;
                    $b_add->ADDRESS_LINE_4           = $bill_address->ADDRESS_LINE_4;
                    $b_add->LOCATION                 = $bill_address->LOCATION;
                    $b_add->COUNTRY                  = $bill_address->COUNTRY;
                    $b_add->STATE                    = $bill_address->STATE;
                    $b_add->CITY                     = $bill_address->CITY;
                    $b_add->POST_CODE                = $bill_address->POST_CODE;
                    $b_add->ADDRESS_TYPE             = $bill_address->ADDRESS_TYPE;
                    $b_add->VAT_EORI_NO              = $bill_address->VAT_EORI_NO;
                    $b_add->ATTENTION                = $bill_address->ATTENTION;
                    $b_add->IS_ACTIVE                = $bill_address->IS_ACTIVE;
                    $b_add->F_SHIPPMENT_NO           = $id;
                    $b_add->F_ADDRESS_NO             = $shipment->F_BILL_TO_ADDRESS;
                    $b_add->save();
                }

               $response_msg = $is_update == 0 ? 'Packaging successfull !' : 'Packaging successfull !';
               $status = true;
            }elseif ($prc[0]->OUT_STATUS == 'failed') {
               $response_msg = 'Packaging Failed !';
               $status = false;
            }elseif ($prc[0]->OUT_STATUS == 'duplicate-shipment') {
               $response_msg = 'Package already generated !';
               $status = false;
            }else{
                $response_msg = $is_update == 0 ? 'Packaging unsuccessfull !' : 'Unackaging unsuccessfull !';
                $status = false;
            }
        } catch (\Exeption $e) {
            dd($e);
            DB::rollback();
            $response_msg = $e->getMessage();
            $status = false;
            return $this->formatResponse($status, $response_msg, 'admin.shipment.processing');
         }
         DB::commit();
         return $this->formatResponse($status, $response_msg, 'admin.shipment.processing');
    }

    public function getShipment($id)
    {
        $data['data'] = Shipment::find($id);
        // $box_no = Shipmentbox::select('F_BOX_NO')->where('F_SHIPMENT_NO', $id)->get();
        $data['box'] = Box::join('SC_SHIPMENT_BOX','SC_SHIPMENT_BOX.F_BOX_NO','SC_BOX.PK_NO')
                        ->select('SC_BOX.*')
                        ->where('SC_SHIPMENT_BOX.F_SHIPMENT_NO', $id)
                        ->orderBy('SC_SHIPMENT_BOX.BOX_SERIAL')
                        ->get();
        if (!$data['box']->isEmpty()) {
            $data['count'] = $data['box']->count();
            foreach ($data['box'] as $key => $value) {
                $value->unboxed         = Stock::where('PRODUCT_STATUS', 60)->where('F_BOX_NO',$value->PK_NO)->count();
                $value->faulty_count    = Stock::where('PRODUCT_STATUS', 420)->where('F_BOX_NO',$value->PK_NO)->count();
            }
        }else{
            $data['count'] = 0;
            $data['box'] = null;
            $data['unboxed'] = 0 ;
            $data['faulty_count'] = 0 ;
        }
        return $this->formatResponse(true, 'View data', 'admin.shipment.shipmentView', $data);
    }

    public function getShipmentInvoice($id)
    {
        $data['data'] = Shipment::find($id);
        $data['invoice'] = Invoice::select('s.F_PRC_STOCK_IN_NO','INVOICE_NAME','PRC_STOCK_IN.INVOICE_NO','VENDOR_NAME','INVOICE_DATE','INVOICE_EXACT_VALUE','MASTER_INVOICE_RELATIVE_PATH','PRC_STOCK_IN.PK_NO as pkno'
        ,DB::raw('IFNULL(COUNT(s.PK_NO),0) as qty')
        ,DB::raw('(SELECT IFNULL(COUNT(PK_NO),0) FROM INV_STOCK WHERE F_PRC_STOCK_IN_NO = pkno) as total')
        )
        // ->selectSub($total_count, 'total')
        ->join('INV_STOCK as s','PRC_STOCK_IN.PK_NO','s.F_PRC_STOCK_IN_NO')
        ->where('s.F_SHIPPMENT_NO',$id)->groupBy('s.F_PRC_STOCK_IN_NO')->get();
        return $this->formatResponse(true, 'View data', 'admin.shipment.shipmentInvoiceView', $data);
    }

    public function getCreate($id = null)
    {
        $data['data'] = '';
        if ($id != null) {
            $data['data'] = Shipment::where('PK_NO', $id)->first();
            // if (empty($data['data'])) {
            //     return redirect()->route('admin.shipment.list')->with('flashMessageError', 'Shipment data not found !');
            // }else if($data['data']->SHIPMENT_STATUS > 20){
            //     return redirect()->route('admin.shipment.list')->with('flashMessageError', 'Can not edit shipment at this moment!');
            // }
        }
        $data['warehouse'] = $this->warehouse->getWarehpuseCombo();
        return $this->formatResponse(true, 'View data', 'admin.shipment.create', $data);
    }

    public function updateShipmentInfo($request,$id)
    {
        $signature = null;
        DB::beginTransaction();
        try {
        $shipment = Shipment::findOrFail($id);

        $sign = DB::table('SC_SIGNATURE')->where('PK_NO',$request->signature)->first();
        if(!empty($sign)){
            $signature = $sign->IMG_PATH;
        }

        $shipment->F_FROM_ADDRESS           = $request->form_address;
        $shipment->F_SHIP_TO_ADDRESS        = $request->ship_to;
        $shipment->F_BILL_TO_ADDRESS        = $request->bill_to;
        $shipment->F_SHIPPING_AGENT         = $request->shipping_agent;
        $shipment->F_DESTINATION_ADDRESS    = $request->destination;
        $shipment->F_SIGNATURE              = $request->signature;
        $shipment->SIGNATURE_PATH           = $signature;
        $shipment->SCH_DEPARTING_DATE       = date('d-m-Y',strtotime($request->date));
        $shipment->WAYBILL                  = $request->awb;

        $shipment->update();
        }catch (\Exeption $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Shipment information not updated successfull !', 'admin.shipment.processing');

        }
        DB::commit();
        return $this->formatResponse(true, 'Shipment information updated successfull !', 'admin.shipment.processing');

    }

    public function postCarrier($request)
    {
        DB::beginTransaction();
        try {
            if($request->logistic_pk == 0){
                $carrier = new Carrier();
                $carrier->IS_ACTIVE = 1;
            }else{
                $carrier = Carrier::findOrFail($request->logistic_pk);
            }
            $carrier->NAME = $request->logistic_name;
            $carrier->save();
        }catch (\Exeption $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.shipment.create');
        }
        DB::commit();
        return $this->formatResponse(true, 'Logistics Carrier action successful !', 'admin.shipment.create');

    }
}
