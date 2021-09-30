<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Models\Agent;
use App\Models\Order;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Reseller;
use App\Models\NotifySms;
use Illuminate\Http\Request;
use App\Models\AuthUserGroup;
use App\Models\ProductVariant;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerAddressType;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Order\OrderInterface;
use App\Repositories\Admin\Booking\BookingInterface;

class BookingToOrderController extends BaseController
{
    protected $orderInt;
    protected $bookingInt;
    protected $booking_model;
    protected $customer;
    protected $prd_variant;
    protected $agent;
    protected $reseller;
    protected $address_type;
    protected $country;

    public function __construct(OrderInterface $orderInt, BookingInterface $bookingInt, Booking $booking_model, Customer $customer, ProductVariant $prd_variant, Agent $agent, Reseller $reseller, CustomerAddressType $address_type, Country $country)
    {
        $this->orderInt        = $orderInt;
        $this->customer        = $customer;
        $this->bookingInt      = $bookingInt;
        $this->booking_model   = $booking_model;
        $this->prd_variant     = $prd_variant;
        $this->agent           = $agent;
        $this->reseller        = $reseller;
        $this->address_type    = $address_type;
        $this->address_type    = $address_type;
        $this->country         = $country;
    }




    public function getBooking($booking_id )
    {
        $customer   = $this->customer->getCustomerCombo();
        $agent      = $this->agent->getAgentCombo();
        $reseller   = $this->reseller->getResellerCombo();
        $this->resp = $this->bookingInt->findOrThrowException($booking_id);
        $max_customer_no = $this->customer::max('CUSTOMER_NO');
        $data       = $this->resp->data;
        $bundle = array();

        try {
            $bundle = DB::table('SLS_CHECK_OFFER')
            ->select('SLS_CHECK_OFFER.F_BOOKING_NO','SLS_CHECK_OFFER.F_BUNDLE_NO',DB::RAW('sum(SLS_CHECK_OFFER.REGULAR_BUNDLE_PRICE) as TOTAL_REGULAR_BUNDLE_PRICE'), DB::RAW('sum(SLS_CHECK_OFFER.INSTALLMENT_BUNDLE_PRICE) as TOTAL_INSTALLMENT_BUNDLE_PRICE'),DB::RAW('COUNT(*) AS INV_COUNT'),'SLS_BUNDLE.BUNDLE_NAME_PUBLIC','SLS_BUNDLE.BUNDLE_NAME')
            ->leftJoin('SLS_BUNDLE','SLS_BUNDLE.PK_NO','=','SLS_CHECK_OFFER.F_BUNDLE_NO')
            ->where('SLS_CHECK_OFFER.F_BOOKING_NO',$booking_id)
            ->where('SLS_CHECK_OFFER.IS_PROCESSED',1)
            ->groupBy('SLS_CHECK_OFFER.F_BUNDLE_NO')
            ->get();
            if($bundle){
                foreach ($bundle as $key => $value) {
                    $query = DB::SELECT("select sum(t.REGULAR_PRICE) as REGULAR_PRICE,sum(t.INSTALLMENT_PRICE) as INSTALLMENT_PRICE from (SELECT SLS_BOOKING_DETAILS.F_BOOKING_NO,
                    SLS_BOOKING_DETAILS.F_INV_STOCK_NO,
                    SLS_CHECK_OFFER.F_INV_STOCK_NO as F_STOCK_NO,
                    INV_STOCK.BOOKING_STATUS,INV_STOCK.PRD_VARINAT_NAME, INV_STOCK.REGULAR_PRICE,INV_STOCK.INSTALLMENT_PRICE
                    FROM SLS_BOOKING_DETAILS
                    LEFT JOIN SLS_CHECK_OFFER ON SLS_CHECK_OFFER.F_INV_STOCK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO AND SLS_CHECK_OFFER.F_BUNDLE_NO = $value->F_BUNDLE_NO
                    LEFT JOIN INV_STOCK ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO

                    WHERE SLS_BOOKING_DETAILS.F_BOOKING_NO = $booking_id
                    HAVING  F_STOCK_NO IS NULL ) t ");

                    if(!empty($query)){
                        $value->NON_BUNDLE_REGULAR_PRICE        = $query[0]->REGULAR_PRICE;
                        $value->NON_BUNDLE_INSTALLMENT_PRICE    = $query[0]->INSTALLMENT_PRICE;
                    }
                }
            }

    } catch (\Exception $e) {
        return $this->formatResponse(false, $e->getMessage(), 'admin.booking.list');
    }
        return view('admin.order.booking_to_order')
        ->withCustomer($customer)
        ->withAgent($agent)
        ->withReseller($reseller)
        ->withData($data)
        ->withBundle($bundle)
        ->withMaxcustomerno($max_customer_no);
    }

    public function getEdit($PK_NO)
    {
        $customer   = $this->customer->getCustomerCombo();
        $agent      = $this->agent->getAgentCombo();
        $reseller   = $this->reseller->getResellerCombo();
        $this->resp = $this->orderInt->findOrThrowException($PK_NO,'edit');
        $max_customer_no = $this->customer::max('CUSTOMER_NO');
        $data       = $this->resp->data;

        $role_id = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')->select('F_ROLE_NO')
                            ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                            ->where('F_USER_NO',Auth::user()->PK_NO)
                            ->first();

        if($data['booking']->IS_BUNDLE_MATCHED == 1){
            $view = 'admin.order.bundle';
        }else{
            $view = 'admin.order.edit';
        }
        return view($view)->withCustomer($customer)
        ->withAgent($agent)
        ->withReseller($reseller)
        ->withData($data)
        ->withRole($role_id->F_ROLE_NO)
        ->withMaxcustomerno($max_customer_no);
    }

    public function getBookOrderAdminApproval($PK_NO)
    {
        $customer   = $this->customer->getCustomerCombo();
        $agent      = $this->agent->getAgentCombo();
        $reseller   = $this->reseller->getResellerCombo();
        $this->resp = $this->orderInt->findOrThrowExceptionAdminApproval($PK_NO);
        $max_customer_no = $this->customer::max('CUSTOMER_NO');
        $data       = $this->resp->data;

        return view('admin.order.adminApproval.book_order')->withCustomer($customer)->withAgent($agent)->withReseller($reseller)->withData($data)->withMaxcustomerno($max_customer_no);
    }

    public function getView($PK_NO)
    {
        $customer   = $this->customer->getCustomerCombo();
        $agent      = $this->agent->getAgentCombo();
        $reseller   = $this->reseller->getResellerCombo();
        $this->resp = $this->orderInt->findOrThrowException($PK_NO,'view');
        $max_customer_no = $this->customer::max('CUSTOMER_NO');
        $data       = $this->resp->data;
        if (isset($this->resp->data['order']->DEFAULT_AT)) {
            $data['full_order_arrive'] = NotifySms::select('SEND_AT')->where('F_BOOKING_NO',$PK_NO)->where('IS_SEND', 1)->where('TYPE','Arrival')->orderBy('SEND_AT','DESC')->first();
        }
        if($this->resp->data['order']->IS_CANCEL == 1){
            $view = 'admin.order.canceled';
        }else{
            if($data['booking']->IS_BUNDLE_MATCHED == 1){
                $view = 'admin.order.bundle';
            }else{
                $view = 'admin.order.view';
            }
        }


        return view($view)->withCustomer($customer)->withAgent($agent)->withReseller($reseller)->withData($data)->withMaxcustomerno($max_customer_no);
    }

    public function ajaxDelete($id, $type=null,$booking_no=null)
    {
        $data = $this->orderInt->ajaxDelete($id,$type,$booking_no);
        return $data;
    }

    public function ajaxExchangeStock($inv_id)
    {
        $data = $this->orderInt->ajaxExchangeStock($inv_id);
        return $data;
    }

    public function ajaxExchangeStockAction(Request $request)
    {
        $data = $this->orderInt->ajaxExchangeStockAction($request);
        return $data;
    }

    public function ajaxPayment(Request $request)
    {
        $data = $this->orderInt->ajaxPayment($request);
        return $data;
    }

    public function getCustomerAddress($id,$pk_no,$address_id=null,$is_reseller=null)
    {
        $data = $this->orderInt->getCustomerAddress($id,$pk_no,$address_id,$is_reseller);
        return $data;
    }

    public function getPayInfo($order_id,$is_reseller)
    {
        $data = $this->orderInt->getPayInfo($order_id,$is_reseller);
        return $data;
    }

    public function postCustomerAddress(Request $request)
    {
        $data = $this->orderInt->postCustomerAddress($request);
        return $data;
    }

    public function postCustomerAddress2(Request $request)
    {
        $data = $this->orderInt->postCustomerAddress2($request);
        return $data;
    }



    public function postUpdatedAddress(Request $request, $order_id,$type)
    {
        $data = $this->orderInt->postUpdatedAddress($request, $order_id,$type);
        return $data;
    }

    public function postPaymentUncheck(Request $request)
    {
        $data = $this->orderInt->postPaymentUncheck($request);
        return $data;
    }

    public function updateBooktoOrder(Request $request, $id)
    {
        $this->resp = $this->orderInt->updateBooktoOrder($request,$id);
        if($request->save_btn == 'proceed_to_order_make_payment'){
            if ($request->is_reseller == 0) {
                return redirect()->route('admin.payment.create',['id' => $request->customer_id, 'type' => 'customer'])->with($this->resp->redirect_class, $this->resp->msg);
            }else{
                return redirect()->route('admin.payment.create',['id' => $request->customer_id, 'type' => 'reseller'])->with($this->resp->redirect_class, $this->resp->msg);
            }
        }else{
            return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
        }
    }

    public function updateBooktoOrderAdminApproved(Request $request, $id)
    {
        $this->resp = $this->orderInt->updateBooktoOrderAdminApproved($request,$id);
        if($request->save_btn == 'proceed_to_order_make_payment'){
            if ($request->is_reseller == 0) {
                return redirect()->route('admin.payment.create',['id' => $request->customer_id, 'type' => 'customer'])->with($this->resp->redirect_class, $this->resp->msg);
            }else{
                return redirect()->route('admin.payment.create',['id' => $request->customer_id, 'type' => 'reseller'])->with($this->resp->redirect_class, $this->resp->msg);
            }
        }else{
            return redirect()->route('admin.order_alter.list')->with($this->resp->redirect_class, $this->resp->msg);
        }
    }

    public function postDefaultOrderPenalty(Request $request,$id)
    {
        $this->resp = $this->orderInt->postDefaultOrderPenalty($request,$id);
        if ($this->resp->data == 0) {
            return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
        }
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function checkifCustomerAddressexists($customer_id, $type,$booking_id=null)
    {
        $country = $this->country->getCountryComboWithCode();
        $addTypeCombo    = $this->address_type->getAddTypeCombo();

        if ($type === 'customer') {

            $data1 = CustomerAddress::where('F_CUSTOMER_NO',$customer_id)->where('F_ADDRESS_TYPE_NO',1)->first();
            $data2 = CustomerAddress::where('F_CUSTOMER_NO',$customer_id)->where('F_ADDRESS_TYPE_NO',2)->first();

            if (empty($data1) || empty($data2)) {
                $html = view('admin.booking.customer_address_modal')->withAddress($addTypeCombo)->withCountry($country)->render();

                $data['delivery']   = $data1;
                $data['billing']    = $data2;
                $data['response']   = 1;
                $data['html']       = $html;

                return response()->json($data);
            }else{
                $data['response'] = 0;

                return $data;
            }

        }else{
            $data3 = Order::select('DELIVERY_NAME')->where('F_BOOKING_NO',$booking_id)->first();
            if (empty($data3->DELIVERY_NAME)) {
                $html = view('admin.booking.customer_address_modal')->withAddress($addTypeCombo)->withCountry($country)->withBooking($booking_id)->withIsreseller($type)->render();

                $data['html']       = $html;
                $data['delivery']   = $data3->DELIVERY_NAME;
                $data['billing']    = 1;
                $data['response']   = 1;
                return response()->json($data);
            }else{
                $data['response'] = 0;
                return $data;
            }
        }
        $data['response'] = 0;
        return $data;
    }



}
