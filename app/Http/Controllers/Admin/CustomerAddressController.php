<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\State;
use App\Models\PoCode;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Reseller;
use Illuminate\Http\Request;
use App\Models\BookingDetails;
use App\Models\CustomerAddress;
use App\Models\CustomerAddressType;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\CustomerAddressRequest;
use App\Repositories\Admin\CustomerAddress\CustomerAddressInterface;

class CustomerAddressController extends BaseController
{
    protected $customer_add;
    protected $pCode;
    protected $city;
    protected $state;

    public function __construct(CustomerAddressInterface $customer_add, CustomerAddress $customer_add_model, CustomerAddressType $address_type, Country $country, State $state, City $city, PoCode $pCode)
    {
        $this->customer_add         = $customer_add;
        $this->customer_add_model   = $customer_add_model;
        $this->address_type         = $address_type;
        $this->country              = $country;
        $this->state                = $state;
        $this->city                 = $city;
        $this->pCode                = $pCode;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->customer_add->getPaginatedList($request, 20);
        return view('admin.customer.index')->withRows($this->resp->data);
    }

    public function postStore(CustomerAddressRequest $request) {

        $this->resp = $this->customer_add->postStore($request);

        if ($request->is_modal == 0) {
            return redirect()->route('admin.customer.view',$request->customer_id)->with($this->resp->redirect_class, $this->resp->msg);
        }else{
            return 1;
        }
    }

    public function getEdit($id)
    {
        $customer_address = $this->customer_add->getShow($id);
        $data['address_type_combo'] = $this->address_type->getAddTypeCombo();
        $data['country'] = $this->country->getCountryComboWithCode();
        $data['city']       = PoCode::where('PO_CODE',$customer_address->data->POST_CODE)->groupBy('F_CITY_NO')->pluck('CITY_NAME','F_CITY_NO');
        $data['state']      = City::where('PK_NO',$customer_address->data->CITY)->groupBy('F_STATE_NO')->pluck('STATE_NAME','F_STATE_NO');

        // if (!$this->resp->status) {

        //     return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        // }

        return view('admin.customer-address.edit')->withAddress($customer_address->data)->withData($data);
    }

    public function postUpdate(Request $request, $id)
    {
        $this->resp = $this->customer_add->postUpdate($request, $id);

        return redirect()->route('admin.customer.view',$this->resp->data)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->customer_add->delete($id);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getCreate() {

        $addTypeCombo    = $this->address_type->getAddTypeCombo();
        $data['country'] = $this->country->getCountryComboWithCode();

        return view('admin.customer-address.create')->withAddress($addTypeCombo)->withData($data);
    }

    public function getState($city_id)
    {
        $state_id = $this->state->getStateByCity($city_id);
        return response()->json($state_id);
    }

    public function getCity($post_code)
    {
        $city_id = $this->city->getCityByPostcode($post_code);
        return response()->json($city_id);
    }

    public function getCitybyState($state_id)
    {
        $city_id = $this->city->getCitybyState($state_id);
        return response()->json($city_id);
    }

    public function getPostC($city_id,$state_id)
    {
        $post_c = $this->pCode->getPcodeByCity($city_id,$state_id);
        return response()->json($post_c);
    }

    public function getPostagebyCity($city_id)
    {
        $post_c = $this->pCode->getPostagebyCity($city_id);
        return response()->json($post_c);
    }

    // public function getCityState($post_code)
    // {
    //     $citystate = $this->pCode->getCityState($post_code);
    //     return response()->json($citystate);
    // }

    public function search(Request $request)
    {
        $result = PoCode::select('PO_CODE','PK_NO')->where('PO_CODE', 'LIKE', '%'. $request->get('post_code'). '%')->where('F_COUNTRY_NO',$request->get('country'))->groupBy('PO_CODE')->get();
          return $result;
    }

    public function getCustomerAddressEdit($customer_id,$address_id,$is_reseller=null)
    {
        $customer_address   = CustomerAddress::where('IS_ACTIVE',1)
                                            ->where('F_ADDRESS_TYPE_NO',1);
                                            if ($is_reseller > 0) {
                                                $customer_address = $customer_address->where('F_RESELLER_NO',$customer_id);
                                            }else{
                                                $customer_address = $customer_address->where('F_CUSTOMER_NO',$customer_id);
                                            }
                                            $customer_address = $customer_address->get();
        // if ($is_reseller == 0) {
            $customer_address2  = CustomerAddress::where('PK_NO',$address_id)->where('IS_ACTIVE',1)->first();
        // }else{
        //     $customer_address2  = Reseller::where('PK_NO',$address_id)->where('IS_ACTIVE',1)->first();
        // }
        $data['city']       = PoCode::where('PO_CODE',$customer_address2->POST_CODE)->groupBy('F_CITY_NO')->pluck('CITY_NAME','F_CITY_NO');
        $data['state']      = City::where('PK_NO',$customer_address2->CITY)->groupBy('F_STATE_NO')->pluck('STATE_NAME','F_STATE_NO');
        $data['pk_no']      = $customer_address2->F_INV_STOCK_NO;
        $data['address_id'] = $customer_address2->PK_NO;
        $data['country']    = $this->country->getCountryComboWithCode();
        $addTypeCombo       = $this->address_type->getAddTypeCombo();
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($data);
        // echo '<br>======================<br>';
        // exit();
        $html = view('admin.order.customer_address_modal')->withAddress($addTypeCombo)->withRows($customer_address)->withData($data)->withEditdata($customer_address2)->render();

        $data['html'] = $html;
        return response()->json($data);
    }

    public function getCustomerByName($customer_name, $type = null)
    {
        if($type == 'reseller'){
            $customer = Reseller::where('NAME', 'LIKE', '%'. $customer_name. '%')->count();
        }else{
            $customer = Customer::where('NAME', 'LIKE', '%'. $customer_name. '%')->count();
        }
        return $customer;
    }
}
