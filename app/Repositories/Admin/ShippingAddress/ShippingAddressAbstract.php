<?php
namespace App\Repositories\Admin\ShippingAddress;

use DB;
use App\Models\ShippingAddress;
use App\Traits\RepoResponse;

class ShippingAddressAbstract implements ShippingAddressInterface
{
    use RepoResponse;

    public function __construct(ShippingAddress $shipping_add)
    {
        $this->shipping_add = $shipping_add;
    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        $data = $this->shipping_add->orderBy('NAME', 'ASC')->get();

        return $this->formatResponse(true, '', 'admin.shipment-address.index', $data);
    }

    public function getShow(int $id)
    {
        $data =  ShippingAddress::find($id);

        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.shipping-address.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.shipping-address.list', null);
    }

    public function postStore($request)
    {
        DB::beginTransaction();

        try {
            $shipping_add                           = new ShippingAddress();
            $shipping_add->NAME                     = $request->name;
            $shipping_add->TEL_NO                   = $request->mobilenoadd;
            $shipping_add->ADDRESS_LINE_1           = $request->ad_1;
            $shipping_add->ADDRESS_LINE_2           = $request->ad_2;
            $shipping_add->ADDRESS_LINE_3           = $request->ad_3;
            $shipping_add->ADDRESS_LINE_4           = $request->ad_4;
            $shipping_add->LOCATION                 = $request->location;
            $shipping_add->COUNTRY                  = $request->country;
            $shipping_add->STATE                    = $request->state;
            $shipping_add->CITY                     = $request->city;
            $shipping_add->POST_CODE                = $request->post_code;
            $shipping_add->ADDRESS_TYPE             = $request->address_type;
            $shipping_add->VAT_EORI_NO              = $request->vat_eori_no;
            $shipping_add->ATTENTION                = $request->attention;
            $shipping_add->IS_ACTIVE                = $request->status;
            $shipping_add->save();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.shipment-address.index');
        }
        DB::commit();


        return $this->formatResponse(true, 'Shipping Address has been created successfully !', 'admin.shipment-address.index');
    }

    public function postUpdate($request, $PK_NO)
    {
            
            $shipping_add                           = ShippingAddress::where('PK_NO', $PK_NO)->first();
            $shipping_add->NAME                     = $request->name;
            $shipping_add->TEL_NO                   = $request->mobilenoadd;
            $shipping_add->ADDRESS_LINE_1           = $request->ad_1;
            $shipping_add->ADDRESS_LINE_2           = $request->ad_2;
            $shipping_add->ADDRESS_LINE_3           = $request->ad_3;
            $shipping_add->ADDRESS_LINE_4           = $request->ad_4;
            $shipping_add->LOCATION                 = $request->location;
            $shipping_add->COUNTRY                  = $request->country;
            $shipping_add->STATE                    = $request->state;
            $shipping_add->CITY                     = $request->city;
            $shipping_add->POST_CODE                = $request->post_code;
            $shipping_add->ADDRESS_TYPE             = $request->address_type;
            $shipping_add->VAT_EORI_NO              = $request->vat_eori_no;
            $shipping_add->ATTENTION                = $request->attention;
            $shipping_add->IS_ACTIVE                = $request->status;

            if ($shipping_add->update()) {
                return $this->formatResponse(true, 'Shipping Address Information has been Updated successfully', 'admin.shipping-address.list');
            }

            return $this->formatResponse(false, 'Unable to update Shipping Address Information !', 'admin.shipping-address.list');
    }


}
