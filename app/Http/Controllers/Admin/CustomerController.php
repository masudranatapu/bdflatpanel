<?php
namespace App\Http\Controllers\Admin;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Reseller;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use App\Models\PaymentCustomer;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerAddressType;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\CustomerRequest;
use App\Repositories\Admin\Customer\CustomerInterface;

class CustomerController extends BaseController
{
    protected $customer;
    protected $country;

    public function __construct(CustomerInterface $customer, Customer $customermodel, Reseller $reseller, Agent $agent, CustomerAddress $cusAdd, CustomerAddressType $cusaddtype, Country $country)
    {
        $this->customer        = $customer;
        $this->customermodel   = $customermodel;
        $this->agent           = $agent;
        $this->reseller        = $reseller;
        $this->cusaddtype      = $cusaddtype;
        $this->cusAdd          = $cusAdd;
        $this->country         = $country;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->customer->getPaginatedList($request, 20);
        return view('admin.customer.index')->withRows($this->resp->data);
    }

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
        // $result = DB::table($table)->select('NAME','MOBILE_NO')->where('NAME', 'LIKE', '%'. $request->get('q'). '%')->orWhere('MOBILE_NO', 'LIKE', '%'. $request->get('q'). '%')->get();

        if ($table == 'SLS_CUSTOMERS') {
            // $post_code = CustomerAddress::selectRaw('(SELECT IFNULL(POST_CODE,0) from SLS_CUSTOMERS_ADDRESS where F_CUSTOMER_NO = pk_no1 and F_ADDRESS_TYPE_NO = 1)')->limit(1)->getQuery();

            $customer_info = DB::table($table)
            ->select('NAME','MOBILE_NO','PK_NO as pk_no1','CUSTOMER_NO'
            ,DB::raw('(select IFNULL(POST_CODE,0)  from SLS_CUSTOMERS_ADDRESS where (F_CUSTOMER_NO=pk_no1 and F_ADDRESS_TYPE_NO=1) limit 1) as POST_CODE')
            ,DB::raw('(select CITY  from SLS_CUSTOMERS_ADDRESS where (F_CUSTOMER_NO=pk_no1 and F_ADDRESS_TYPE_NO=1) limit 1) as CITY')
            )
            // ->selectSub($post_code, 'POST_CODE')
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



        // $orders = DB::table("SLS_ORDER as o")->select('o.F_CUSTOMER_NO','o.F_BOOKING_NO','o.CUSTOMER_NAME','o.IS_RESELLER','o.ORDER_ACTUAL_TOPUP','o.ORDER_BUFFER_TOPUP', 'o.ORDER_BALANCE_RETURN','o.DISPATCH_STATUS','b.BOOKING_NO', 'b.RECONFIRM_TIME as DATE','b.TOTAL_PRICE', 'b.DISCOUNT','b.BOOKING_STATUS','b.F_SS_CREATED_BY','u.username as ENTRY_BY')
        // ->join('SLS_BOOKING as b','b.PK_NO','=','o.F_BOOKING_NO')
        // ->leftJoin('auths as u','u.id','=','b.F_SS_CREATED_BY')
        // ->where('o.F_CUSTOMER_NO',$id)
        // ->orderBy('b.RECONFIRM_TIME','DESC')
        // ->get();





        return view('admin.customer.history',compact('data','rows','balance_history'));
    }
}

