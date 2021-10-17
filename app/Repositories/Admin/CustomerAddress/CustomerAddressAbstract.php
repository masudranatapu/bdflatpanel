<?php
namespace App\Repositories\Admin\CustomerAddress;

use DB;
use App\Models\CustomerAddress;
use App\Traits\RepoResponse;

class CustomerAddressAbstract implements CustomerAddressInterface
{
    use RepoResponse;



    public function __construct(CustomerAddress $customer_add)
    {
        $this->customer_add = $customer_add;
    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        $data = $this->customer_add->where('IS_ACTIVE',1)->orderBy('NAME', 'ASC')->get();
        //dd($data);
        return $this->formatResponse(true, '', 'admin.customer.index', $data);
    }

    public function getShow(int $id)
    {
        $data =  CustomerAddress::find($id);

        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.customer-address.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.customer.view', null);
    }

    public function postStore($request)
    {
        //dd($request);
        // $check_dup = Agent::where('NAME',$request->name)->first();
        // if ($check_dup !== null) {
        //     return $this->formatResponse(false, 'Duplicate entry for Payment Source !', 'admin.account.list');
        // }
        DB::beginTransaction();

        try {
            $customer_add                           = new CustomerAddress();
            $customer_add->NAME                     = $request->customername;
            $customer_add->TEL_NO                   = (int)$request->mobilenoadd;
            $customer_add->ADDRESS_LINE_1           = $request->ad_1;
            $customer_add->ADDRESS_LINE_2           = $request->ad_2;
            $customer_add->ADDRESS_LINE_3           = $request->ad_3;
            $customer_add->ADDRESS_LINE_4           = $request->ad_4;
            $customer_add->LOCATION                 = $request->location;
            $customer_add->F_COUNTRY_NO             = $request->country;
            $customer_add->STATE                    = $request->state;
            $customer_add->CITY                     = $request->city;
            $customer_add->POST_CODE                = $request->post_code;
            $customer_add->F_ADDRESS_TYPE_NO        = $request->is_modal == 0 ? $request->addresstype : 1;
            $customer_add->F_CUSTOMER_NO            = $request->customer_id;
            $customer_add->IS_ACTIVE                = 1;
            $customer_add->save();

        } catch (\Exception $e) {

            DB::rollback();
            if ($request->is_modal == 0) {
                return $this->formatResponse(false, $e->getMessage(), 'admin.customer.list');
            }else{
                return 0;
            }
        }
        DB::commit();

        if ($request->is_modal == 0) {
            return $this->formatResponse(true, 'Customer Address has been created successfully !', 'admin.customer.view');
        }else{
            return 1;
        }
    }

    public function postUpdate($request, $PK_NO)
    {
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($request->all());
        // echo '<br>======================<br>';
        // exit();
            $customer_add = CustomerAddress::where('PK_NO', $PK_NO)->first();
            $customer_add->NAME                     = $request->customeraddress;
            $customer_add->TEL_NO                   = (int)$request->mobilenoadd;
            $customer_add->ADDRESS_LINE_1           = $request->ad_1;
            $customer_add->ADDRESS_LINE_2           = $request->ad_2;
            $customer_add->ADDRESS_LINE_3           = $request->ad_3;
            $customer_add->ADDRESS_LINE_4           = $request->ad_4;
            $customer_add->LOCATION                 = $request->location;
            $customer_add->F_COUNTRY_NO             = $request->country;
            $customer_add->STATE                    = $request->state;
            $customer_add->CITY                     = $request->city;
            $customer_add->POST_CODE                = $request->post_code;
            $customer_add->F_ADDRESS_TYPE_NO        = $request->addresstype;
            $data = $customer_add->F_CUSTOMER_NO;

            if ($customer_add->update()) {
                // $customer = CustomerAddress::where('PK_NO', $PK_NO)->first();
                return $this->formatResponse(true, 'Customer Address Information has been Updated successfully', 'admin.customer.view',$data);
            }

            return $this->formatResponse(false, 'Unable to update Customer Address Information !', 'admin.customer.list');
    }

    public function delete($PK_NO)
    {
        $customer = CustomerAddress::where('PK_NO',$PK_NO)->first();
        $customer->IS_ACTIVE = 0;
        if ($customer->update()) {
            return $this->formatResponse(true, 'Successfully deleted Customer Address', 'admin.customer.list');
        }
        return $this->formatResponse(false,'Unable to delete Customer Address','admin.customer.list');
    }
}
