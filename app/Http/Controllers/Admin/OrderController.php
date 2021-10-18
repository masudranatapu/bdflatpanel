<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Agent;
use App\Models\Order;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Reseller;
use Illuminate\Http\Request;
use App\Models\BookingDetails;
use App\Models\PaymentBankAcc;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\VendorRequest;
use App\Repositories\Admin\Order\OrderInterface;
use App\Repositories\Admin\Booking\BookingInterface;


class OrderController extends BaseController
{
    protected $bookingInt;
    protected $booking_model;
    protected $customer;
    protected $prd_variant;
    protected $agent;
    protected $reseller;
    protected $order;


    public function __construct(BookingInterface $bookingInt, Booking $booking_model, Customer $customer, ProductVariant $prd_variant, Agent $agent, Reseller $reseller,OrderInterface $order)
    {
        $this->customer        = $customer;
        $this->bookingInt      = $bookingInt;
        $this->booking_model   = $booking_model;
        $this->prd_variant     = $prd_variant;
        $this->agent           = $agent;
        $this->reseller        = $reseller;
        $this->order           = $order;

    }


    public function getIndex(Request $request)
    {
        if( Auth::user()->F_AGENT_NO > 0){
            $data['payment_acc_no'] = PaymentBankAcc::where('IS_COD',1)->where('F_USER_NO',Auth::user()->PK_NO)->get();
        }else{
            $data['payment_acc_no'] = PaymentBankAcc::where('IS_COD',1)->get();
        }
        return view('admin.order.index',compact('data'));
    }


    public function postCancel($id, Request $request)
    {
        $this->resp = $this->order->postCancel($id,$request);
        if($this->resp->status == true){
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }else{
            return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
        }
    }

    public function getAlteredIndex()
    {
        if( Auth::user()->F_AGENT_NO > 0){
            $data['payment_acc_no'] = PaymentBankAcc::where('IS_COD',1)->where('F_USER_NO',Auth::user()->PK_NO)->get();
        }else{
            $data['payment_acc_no'] = PaymentBankAcc::where('IS_COD',1)->get();
        }
        return view('admin.order.altered',compact('data'));
    }

    public function getDefaultIndex()
    {
        if( Auth::user()->F_AGENT_NO > 0){
            $data['payment_acc_no'] = PaymentBankAcc::where('IS_COD',1)->where('F_USER_NO',Auth::user()->id)->get();
        }else{
            $data['payment_acc_no'] = PaymentBankAcc::where('IS_COD',1)->get();
        }
        return view('admin.orderDefault.index',compact('data'));
    }

    public function getDefaultActionIndex()
    {
        if( Auth::user()->F_AGENT_NO > 0){
            $data['payment_acc_no'] = PaymentBankAcc::where('IS_COD',1)->where('F_USER_NO',Auth::user()->id)->get();
        }else{
            $data['payment_acc_no'] = PaymentBankAcc::where('IS_COD',1)->get();
        }
        return view('admin.orderDefault.default_action',compact('data'));
    }

    public function getCancelOrder(Request $request)
    {
        return view('admin.orderDefault.cancel');
    }

    public function getCancelRequest(Request $request)
    {
        return view('admin.orderDefault.cancel_request');
    }

    public function getDefaultPenaltyIndex(Request $request)
    {
        return view('admin.orderDefault.penalty_list');
    }

    public function getDefaultRevert($id)
    {
        DB::beginTransaction();
        try {
            Order::where('F_BOOKING_NO',$id)->update(['DEFAULT_AT' => null, 'DEFAULT_TYPE' => 0]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('flashMessageError',$e->getMessage());
        }
        DB::commit();
        return redirect()->back()->with('flashMessageSuccess','Reverted Successfully');
    }

    public function getCreate() {
        $country            = $this->country->getCountryCombo();
        $agentCombo         = $this->agent->getAgentCombo();
        $customerCombo      = $this->customer->getCustomerCombo();

        return view('admin.order.create')->withCountry($country)->withAgent($agentCombo)->withCustomer($customerCombo);
    }

    public function postStore(VendorRequest $request) {
        $this->resp = $this->vendor->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postAdminHold(Request $request)
    {
        DB::beginTransaction();
            try {
                if($request->type == 'checked'){
                    Order::where('F_BOOKING_NO', $request->id)->update([ 'IS_ADMIN_HOLD' => 1 ]);
                    BookingDetails::where('F_BOOKING_NO', $request->id)->update([ 'IS_ADMIN_HOLD' => 1 ]);
                }elseif($request->type == 'unchecked'){
                    Order::where('F_BOOKING_NO', $request->id)->update([ 'IS_ADMIN_HOLD' => 0 ]);
                    BookingDetails::where('F_BOOKING_NO', $request->id)->update([ 'IS_ADMIN_HOLD' => 0 ]);
                }

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json('false');
            }
        DB::commit();


        return response()->json('true');
    }

    public function postSelfPickup(Request $request)
    {
        $this->resp = $this->order->postSelfPickup($request);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postSelfPickupAjax(Request $request)
    {
        $data = $this->order->postSelfPickupAjax($request);
        return $data;
    }

    public function updateReceiverAddress(Request $request,$id)
    {
        $this->resp = $this->order->updateReceiverAddress($request,$id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }
    public function updateSenderaddress(Request $request,$id)
    {
        $this->resp = $this->order->updateSenderaddress($request,$id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->order->delete($id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postReturnOrder(Request $request, $id)
    {
        $this->resp = $this->order->postReturnOrder($request, $id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }


}
