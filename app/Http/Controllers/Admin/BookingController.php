<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agent;
use App\Models\Stock;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Reseller;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Booking\BookingInterface;

class BookingController extends BaseController
{
    protected $bookingInt;
    protected $booking_model;
    protected $customer;
    protected $prd_variant;
    protected $agent;
    protected $reseller;
    protected $country;

    public function __construct(BookingInterface $bookingInt, Booking $booking_model, Customer $customer, ProductVariant $prd_variant, Agent $agent, Reseller $reseller, Country $country)
    {
        $this->customer        = $customer;
        $this->bookingInt      = $bookingInt;
        $this->booking_model   = $booking_model;
        $this->prd_variant     = $prd_variant;
        $this->agent           = $agent;
        $this->reseller        = $reseller;
        $this->country         = $country;
    }

    public function getIndex(Request $request, $id = null, $type = null)
    {
        $this->resp = $this->bookingInt->getPaginatedList($request, $id, $type);
        $info['type'] = '';

        if ($id != null && $type != null) {
            if ($type=='customer') {
                $info = $this->customer->select('PK_NO','NAME','MOBILE_NO','EMAIL','CUSTOMER_NO')->where('PK_NO',$id)->first();
                $info['type'] = $type;
            }else{
                $info = $this->reseller->select('PK_NO','NAME','MOBILE_NO','EMAIL','POST_CODE','RESELLER_NO')->where('PK_NO',$id)->first();
                $info['type'] = $type;
            }
        }
        return view('admin.booking.index')->withRows($this->resp->data)->withInfo($info);
    }

    public function search(Request $request)
    {
        $result = Stock::select('PRD_VARINAT_NAME','IG_CODE','PRD_VARIANT_IMAGE_PATH')
        ->whereRaw('( (BOOKING_STATUS IS NULL OR BOOKING_STATUS = 0 OR BOOKING_STATUS = 90) and (PRODUCT_STATUS IS NULL OR PRODUCT_STATUS < 420) ) ');

        if($request->get('q')){
            $pieces = explode(" ", $request->get('q'));
            if($pieces){
                foreach ($pieces as $key => $piece) {
                    $result->where('PRD_VARINAT_NAME', 'LIKE', '%' . $piece . '%');
                    // $result->Where('PRD_VARIANT_SETUP.IG_CODE', 'LIKE', '%' . $piece . '%');
                }
            }
            $result->orWhere('IG_CODE',$request->get('q'));
            $result->orWhere('BARCODE',$request->get('q'));
        }

        $result =  $result->groupBy('IG_CODE')->get();
        return $result;
    }

    public function getCusInfo(Request $request)
    {
        $table = 'SLS_CUSTOMERS';
        if ($request->type == 'reseller') {
            $table = 'SLS_RESELLERS';
        }
        $this->resp = $this->bookingInt->getCusInfo($table,$request->customer);
        return json_encode($this->resp);
    }

    public function getCustomer(Request $request)
    {
        $table = 'SLS_CUSTOMERS';
        if ($request->get('type') == 'reseller') {
            $table = 'SLS_RESELLERS';
        }
        $result = DB::table($table)->select('NAME','PK_NO')->where('NAME', 'LIKE', '%'. $request->get('q'). '%')->orWhere('MOBILE_NO', 'LIKE', '%'. $request->get('q'). '%')->orwhere('CUSTOMER_NO', 'LIKE', '%'. $request->get('q'). '%')->get();
        return $result;
    }

    public function getAllInfo(Request $request)
    {
        $this->resp = $this->bookingInt->getProductINV($request->product);
        return $this->resp;
    }

    public function postStore(Request $request)
    {
        $this->resp = $this->bookingInt->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }



    public function getCreate($id=null,$type=null)
    {
        $info = '';
        if ($id != null && $type != null) {
            if ($type=='customer') {
                $info = $this->customer->select('PK_NO','NAME','MOBILE_NO','EMAIL','CUSTOMER_NO')->where('PK_NO',$id)->first();
                $info['POST_CODE'] = CustomerAddress::select('POST_CODE')->where('F_CUSTOMER_NO',$id)->where('F_ADDRESS_TYPE_NO',1)->first();
                $info['POST_CODE'] = $info['POST_CODE']->POST_CODE ?? 0;
                $info['type'] = $type;

            }else{
                $info = $this->reseller->select('PK_NO','NAME','MOBILE_NO','EMAIL','POST_CODE','RESELLER_NO')->where('PK_NO',$id)->first();
                $info['POST_CODE'] = $info->POST_CODE ?? 0;
                $info['type'] = $type;
            }
        }
        $customer   = $this->customer->getCustomerCombo();
        $agent      = $this->agent->getAgentCombo();
        $reseller   = $this->reseller->getResellerCombo();
        $country    = $this->country->getCountryComboWithCode();

        return view('admin.booking.create')
        ->withCustomer($customer)
        ->withAgent($agent)
        ->withReseller($reseller)
        ->withCountry($country)
        ->withInfo($info);
    }

    public function getEdit(Request $request, $PK_NO)
    {
        $checkoffer     = $request->checkoffer ?? null ;
        $customer       = $this->customer->getCustomerCombo();
        $agent          = $this->agent->getAgentCombo();
        $reseller       = $this->reseller->getResellerCombo();
        $this->resp     = $this->bookingInt->findOrThrowException($PK_NO, $checkoffer);
        $maxcustomerno  = $this->customer::max('CUSTOMER_NO');
        $data           = $this->resp->data;
        return view('admin.booking.edit',compact('customer','agent','reseller','data','maxcustomerno'));
    }

    public function getView(Request $request, $PK_NO)
    {
        $customer   = $this->customer->getCustomerCombo();
        $agent      = $this->agent->getAgentCombo();
        $reseller   = $this->reseller->getResellerCombo();
        $this->resp = $this->bookingInt->findOrThrowException($PK_NO);
        $data       = $this->resp->data;
        return view('admin.booking.view')->withCustomer($customer)->withAgent($agent)->withReseller($reseller)->withData($data);
    }

    public function postUpdate(Request $request, $PK_NO,$type = null)
    {
        if ($request->booking_type == 'book_and_order_with_offer') {
         
            $this->resp = $this->bookingInt->postBookAndOrderWithOffer($request, $PK_NO,$type);
            return redirect()->route('admin.booking_to_order.book-order',['id' => $this->resp->data])->with($this->resp->redirect_class, $this->resp->msg);
        }else{
            $this->resp = $this->bookingInt->postUpdate($request, $PK_NO,$type);
        }

        if ($request->booking_type == 'book_and_check_offer') {
            return redirect()->route('admin.booking.edit',['id' => $this->resp->data, 'checkoffer' => 1])->with($this->resp->redirect_class, $this->resp->msg);

        }elseif ($type == 'order') {
            if ($request->offer_status == 'order_with_offer'){
                return redirect()->route('admin.booking_to_order.book-order',['id' => $PK_NO])->with($this->resp->redirect_class, $this->resp->msg);
            }else{
                return redirect()->route($this->resp->redirect_to,[$this->resp->data])->with($this->resp->redirect_class, $this->resp->msg);
            }
        }
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postCheckOffer(Request $request, $PK_NO = 0, $type = 'checkoffer')
    {
        $this->resp = $this->bookingInt->postUpdate($request, $PK_NO, $type);
        $data = $this->bookingInt->postCheckOffer($this->resp->data);
        $this->resp->html =  $data;
        return response()->json($this->resp);
    }


    public function getDelete($PK_NO)
    {
        $this->resp = $this->bookingInt->delete($PK_NO);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    /*
    public function postOfferApply(Request $request)
    {
        $this->resp = $this->bookingInt->postOfferApply($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
    */



    public function callProcedure()
    {

        DB::beginTransaction();
        try {

            DB::table('R')->insert([
                'R' => '1'
            ]);
            DB::commit();
        } catch(\Exception $e){

        DB::rollback();
        }
        echo 'ok';
        exit();
        DB::beginTransaction();
        try {
            DB::statement('CALL PROC_SLS_BOOKING_CHECK_EXPIRE(@OUT_STATUS);');
            $prc = DB::select('select @OUT_STATUS as OUT_STATUS');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        echo 'ok';
    }
}
