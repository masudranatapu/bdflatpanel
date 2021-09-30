<?php
namespace App\Repositories\Admin\ShipmentSign;

use DB;
use App\Models\ShipmentSign;
use App\Traits\RepoResponse;

class ShipmentSignAbstract implements ShipmentSignInterface
{
    use RepoResponse;

    public function __construct(ShipmentSign $shipment_sign)
    {
        $this->shipment_sign = $shipment_sign;
    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        $data = $this->shipment_sign->orderBy('NAME', 'ASC')->get();

        return $this->formatResponse(true, '', 'admin.shipment-signature.index', $data);
    }

    public function getShow(int $id)
    {
        $data =  ShipmentSign::find($id);

        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.shipment-signature.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.shipment-signature.list', null);
    }

    public function postStore($request)
    {
        DB::beginTransaction();

        try {
            $shipment_sign        = new ShipmentSign();
            $shipment_sign->NAME  = $request->name;




            if ($request->file('images')) {

                foreach ($request->file('images') as $key => $image) {
                    $file_name = 'sign_' . time() . '_' . '.' . $image->getClientOriginalExtension();


                       $path = '/media/images/signature/'. $file_name;


                       $image->move(public_path() . '/media/images/signature/',$file_name);
                       $shipment_sign->IMG_PATH = $path;
                       $shipment_sign->save();

                   }
               }

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.shipment-signature.index');
        }
        DB::commit();


        return $this->formatResponse(true, 'Signature has been created successfully !', 'admin.shipment-signature.index');
    }

    public function postUpdate($request, $PK_NO)
    {
        DB::beginTransaction();

        try {
            $shipment_sign        = ShipmentSign::where('PK_NO', $PK_NO)->first();
            $shipment_sign->NAME  = $request->name;

            if ($request->file('images')) {

                foreach ($request->file('images') as $key => $image) {
                       $file_name = 'sign_' . time() . '_' . '.' . $image->getClientOriginalExtension();
                       $path = '/media/images/signature/'. $file_name;

                       $image->move(public_path() . '/media/images/signature/', $file_name);
                       $shipment_sign->IMG_PATH = $path;
                       $shipment_sign->save();

                   }
               }

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.shipment-signature.index');
        }
        DB::commit();


        return $this->formatResponse(true, 'Signature has been Update successfully !', 'admin.shipment-signature.index');
    }


    public function deleteImage(int $id)
    {
        DB::begintransaction();

        try {

            $shipment_sign = ShipmentSign::find($id);
            $shipment_sign->IMG_PATH    = null;

            $shipment_sign->save();


        } catch (\Exception $e) {

            DB::rollback();

            return $this->formatResponse(false, 'Unable to delete Signature photo !', 'admin.shipment-signature.index');
        }

        DB::commit();

        if ($shipment_sign) {
            return ['status' => 'true'];
        } else {
            return false;
        }

        return $this->formatResponse(true, 'Successfully delete Signature photo !', 'admin.shipment-signature.index');
    }


}
