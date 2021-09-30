<?php
namespace App\Repositories\Admin\Vendor;

use DB;
use App\Models\Vendor;
use App\Traits\RepoResponse;

class VendorAbstract implements VendorInterface
{
    use RepoResponse;
    protected $vendor;

    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }

    public function getPaginatedList($request, int $per_page = 20)
    {
        $data = $this->vendor->where('IS_ACTIVE',1)->select('PK_NO','CODE','NAME', 'ADDRESS', 'PHONE', 'COUNTRY', 'HAS_LOYALITY')->orderBy('NAME', 'ASC')->get();
        return $this->formatResponse(true, '', 'admin.vendor', $data);
    }

    public function postStore($request)
    {
        $country = DB::table('SS_COUNTRY')->select('NAME')->where('PK_NO', '=', $request->country)->first();
        DB::beginTransaction();
        try {
            $vendor = new Vendor();
            $vendor->NAME           = $request->name;
            $vendor->ADDRESS        = $request->address;
            $vendor->F_COUNTRY      = $request->country;
            $vendor->COUNTRY        = $country->NAME;
            $vendor->PHONE          = $request->phone;
            $vendor->HAS_LOYALITY   = $request->has_loyality;
            $vendor->save();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to create vendor !', 'admin.vendor');
        }
        DB::commit();
        return $this->formatResponse(true, 'Vendor has been created successfully !', 'admin.vendor');
    }

    public function findOrThrowException($id)
    {
        $data = $this->vendor->where('PK_NO', '=', $id)->first();
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.vendor.edit', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.vendor', null);
    }

    public function postUpdate($request, $id)
    {
        $country = DB::table('SS_COUNTRY')->select('NAME')->where('PK_NO', '=', $request->country)->first();
        DB::beginTransaction();
        try {
            $vendor = $this->vendor->find($id);
            $vendor->NAME           = $request->name;
            $vendor->ADDRESS        = $request->address;
            $vendor->F_COUNTRY      = $request->country;
            $vendor->COUNTRY        = $country->NAME;
            $vendor->PHONE          = $request->phone;
            $vendor->HAS_LOYALITY   = $request->has_loyality;
            $vendor->update();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to update vendor !', 'admin.vendor');
        }

        DB::commit();
        return $this->formatResponse(true, 'Vendor has been updated successfully !', 'admin.vendor');
    }

    public function delete($id)
    {
        DB::begintransaction();
        try {
            $vendor = $this->vendor->find($id)->delete();
            // $vendor->IS_ACTIVE = 0;
            // $vendor->update();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete the vendor !', 'admin.vendor');
        }
        DB::commit();

        return $this->formatResponse(true, 'Successfully delete the vendor !', 'admin.vendor');
    }
}
