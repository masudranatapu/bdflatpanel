<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Area;
use App\Models\City;
use App\Models\PropertyCondition;
use App\Models\User;
use App\Models\Customer;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use App\Models\PaymentBankAcc;
use App\Models\PaymentMethod;
use App\Models\ProductRequirements;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\RechargeRequest;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Customer\CustomerInterface;

class SeekerController extends BaseController
{
    protected $customer;
    protected $resp;

    public function __construct(CustomerInterface $customer)
    {
        parent::__construct();
        $this->customer = $customer;

    }

    public function getIndex(Request $request)
    {


        $data = [];
        // $this->resp = $this->customer->getPaginatedList($request);
        // $data['rows'] = $this->resp->data;
        $data['city'] = City::where('IS_ACTIVE',1)->pluck('CITY_NAME','PK_NO');
        if($request->city != '' ){
            $data['area'] = Area::where('IS_ACTIVE',1)
            ->where('F_CITY_NO',$request->city)
            ->whereNull('F_PARENT_AREA_NO')
            ->pluck('AREA_NAME','PK_NO');
        }

        $data['property_type'] = PropertyType::where('IS_ACTIVE',1)->pluck('PROPERTY_TYPE', 'PK_NO');
        return view('admin.seeker.index', compact('data'));
    }

    public function getView($id)
    {
        $this->resp = $this->customer->getEdit($id);
        $data = $this->resp->data;
        $city = City::all(['CITY_NAME', 'PK_NO'])->pluck('CITY_NAME', 'PK_NO');
        $row = ProductRequirements::where('CREATED_BY', $id)->orderByDesc('PK_NO')->first();
        $areas = Area::whereIn('PK_NO', json_decode($row->F_AREAS ?? "[]"))->pluck('AREA_NAME', 'PK_NO');
        $property_types = PropertyType::pluck('PROPERTY_TYPE', 'PK_NO');
        return view('admin.seeker.view', compact('data', 'city', 'areas', 'row', 'property_types'));
    }

    public function getEdit($id)
    {
        $this->resp = $this->customer->getEdit($id);
        $data['user'] = $this->resp->data;
        $data['city'] = City::all(['CITY_NAME', 'PK_NO'])->pluck('CITY_NAME', 'PK_NO');
        $data['row'] = ProductRequirements::where('F_USER_NO', $id)->where('IS_ACTIVE',1)->orderByDesc('PK_NO')->first();
        $data['cond'] = PropertyCondition::where('IS_ACTIVE', '=', 1)
            ->orderByDesc('ORDER_ID')
            ->pluck('PROD_CONDITION', 'PK_NO');
        if($data['row'] && $data['row']->F_CITY_NO){
            $data['areas'] = Area::where('F_CITY_NO', 1)->pluck('AREA_NAME', 'PK_NO');
        }
        $data['property_types'] = PropertyType::pluck('PROPERTY_TYPE', 'PK_NO');

        return view('admin.seeker.edit', compact('data'));
    }

    public function postUpdate(Request $request): RedirectResponse
    {
        $this->resp = $this->customer->postUpdate($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getPayment(Request $request, $id)
    {
        $this->resp = $this->customer->getCustomerTxn($id);
        $data['rows'] = $this->resp->data;
        $data['seeker'] = Customer::find($id);
        return view('admin.seeker.payment', compact('data'));
    }

    public function getRecharge($id)
    {
        $data['paymentMethods'] = PaymentMethod::all()
            ->whereNotIn('PK_NO', [1, 5, 7])
            ->where('IS_ACTIVE', '=', 1)
            ->pluck('NAME', 'PK_NO');
        $data['seeker'] = User::find($id);
        return view('admin.seeker.recharge', compact('data'));
    }

    public function postRecharge(RechargeRequest $request, $id): RedirectResponse
    {
        $this->resp = $this->customer->postRecharge($request, $id);
        return redirect()->route($this->resp->redirect_to, $id)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getArea($id)
    {
        $html = Area::where('F_CITY_NO', $id)->pluck('AREA_NAME', 'PK_NO');
        if ($html) {
            return response()->json(['html' => $html, 'status' => 'success']);
        } else {
            return response()->json(['html' => [], 'status' => 'faild']);
        }

    }

    public function paymentAccount(Request $request)
    {
        $method = $request->query->get('query');
        $accounts = PaymentBankAcc::where('F_PAYMENT_METHOD_NO', '=', $method)
            ->get(['PK_NO', 'BANK_ACC_NAME', 'BANK_ACC_NO']);

        $response = '';
        if (!count($accounts)) {
            $response = '<option selected disabled>No Account</option>';
        }

        foreach ($accounts as $account) {
            $response .= '<option value="' . $account->PK_NO . '">' . $account->BANK_ACC_NAME . '(' . $account->BANK_ACC_NO . ')' . '</option>';
        }
        return $response;
    }


    /*


        public function getCombo($type)
        {
            if ($type == 'ukshop') {
                $combo = $this->agent->getUKComboCustomer();
            }elseif ($type == 'agent') {
                $combo    = $this->agent->getAgentComboCustomer();
            }elseif ($type == 'reseller') {
                $combo    = $this->reseller->getResellerComboCustomer();
            }else{
                $combo = [];
            }
            return response()->json($combo);

        }

        public function getCreate() {
            $agentCombo      = $this->agent->getUKCombo();
            $resellerCombo   = $this->reseller->getResellerCombo();
            $addTypeCombo    = $this->cusaddtype->getAddTypeCombo();
            $data['country'] = $this->country->getCountryComboWithCode();

            return view('admin.customer.create')->withAgent($agentCombo)->withReseller($resellerCombo)->withAddress($addTypeCombo)->withData($data);
        }

        public function postStore(CustomerRequest $request)
        {
            $this->resp = $this->customer->postStore($request);
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }

        public function addNewCustomer(Request $request)
        {
            $this->resp = $this->customer->addNewCustomer($request);
            return response()->json($this->resp);
        }

        public function getEdit()
        {
            return view('admin.property-seeker.index');
        }

        public function getEdit($id)
        {
            $this->resp = $this->customer->getShow($id);
            $data['agent_combo'] = $this->agent->getUKCombo();
            $data['reseller_combo'] = $this->reseller->getResellerCombo();
            $data['country'] = $this->country->getCountryComboWithCode();

            if (!$this->resp->status) {
                return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
            }

            return view('admin.customer.edit_customer')->withCustomer($this->resp->data)->withData($data);
        }

        public function postUpdate(Request $request, $id)
        {
            $this->resp = $this->customer->postUpdate($request, $id);

            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }

        public function getDelete($id)
        {
            $this->resp = $this->customer->delete($id);

            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }

        public function getView($id)
        {
            $this->resp = $this->customer->getCusAdd($id);
            $data['cus_info'] = $this->customer->getShow($id);


            $data['address'] = $this->resp->data[0];
            $customer = $this->customer->getShow($id);
            $data['customer'] = $customer->data;

            $result = $this->customer->getCustomerHistory($id);
            if($result->status){
                $rows = $result->data;
            }else{
                $rows = null;
            }


            return view('admin.customer.view')->withAddress($this->resp->data)->withCustomer($data['cus_info']->data)->withRows($rows)->withData($data);
        }


        public function getCustomer(Request $request)
        {
            $table = 'SLS_CUSTOMERS';
            if ($request->get('type') == 'reseller') {
                $table = 'SLS_RESELLERS';
            }


            if ($table == 'SLS_CUSTOMERS') {


                $customer_info = DB::table($table)
                ->select('NAME','MOBILE_NO','PK_NO as pk_no1','CUSTOMER_NO'
                ,DB::raw('(select IFNULL(POST_CODE,0)  from SLS_CUSTOMERS_ADDRESS where (F_CUSTOMER_NO=pk_no1 and F_ADDRESS_TYPE_NO=1) limit 1) as POST_CODE')
                ,DB::raw('(select CITY  from SLS_CUSTOMERS_ADDRESS where (F_CUSTOMER_NO=pk_no1 and F_ADDRESS_TYPE_NO=1) limit 1) as CITY')
                )

                ->where('IS_ACTIVE',1)
                ->where('NAME', 'LIKE', '%'. $request->get('q'). '%')
                ->orWhere('MOBILE_NO', 'LIKE', '%'. $request->get('q'). '%')
                ->get();
            }else{
                $customer_info = DB::table($table)
                ->select('NAME','PK_NO as pk_no1','MOBILE_NO','POST_CODE','CITY')
                ->where('IS_ACTIVE',1)
                ->where('NAME', 'LIKE', '%'. $request->get('q'). '%')
                ->orWhere('MOBILE_NO', 'LIKE', '%'. $request->get('q'). '%')
                ->get();
            }
            return $customer_info;
        }


        public function postBlanceTransfer(Request $request)
        {
            $this->resp = $this->customer->postBlanceTransfer($request);
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }

        public function getRemainingBalance($id)
        {
            $data = $this->customer->getRemainingBalance($id);
            return response()->json($data);
        }


        public function getHistory($id)
        {

            $this->resp = $this->customer->getCusAdd($id);

            $data['address'] = $this->resp->data != null ? $this->resp->data[0] : null;
            $customer = $this->customer->getShow($id);

            $data['customer'] = $customer->data;

            $result = $this->customer->getCustomerHistory($id);
            if($result->status){
                $rows = $result->data;
            }else{
                $rows = null;
            }




            $balance_history = PaymentCustomer::where('F_CUSTOMER_NO', $id)->where('PAYMENT_REMAINING_MR','>', 0)->where('PAYMENT_CONFIRMED_STATUS',1)->get();




            return view('admin.customer.history',compact('data','rows','balance_history'));
        }
    */


}

