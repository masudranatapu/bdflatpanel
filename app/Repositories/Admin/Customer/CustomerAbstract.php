<?php

namespace App\Repositories\Admin\Customer;

use App\Models\Booking;
use App\Models\Product;
use App\Models\BankList;
use App\Models\Customer;
use App\Models\Reseller;
use App\Models\Transaction;
use App\Models\OrderPayment;
use App\Traits\RepoResponse;
use App\Models\RefundRequest;
use App\Models\CustomerAddress;
use App\Models\PaymentCustomer;
use Illuminate\Support\Facades\DB;
use App\Models\ProductRequirements;
use Illuminate\Support\Facades\Auth;
use App\Models\AccResellerCustomerTnx;

class CustomerAbstract implements CustomerInterface
{
    use RepoResponse;

    protected $customer;

    public function __construct(Customer $customer, CustomerAddress $cusAdd)
    {
        $this->customer = $customer;
        $this->cusAdd = $cusAdd;
    }

    public function getPaginatedList($request): object
    {
        $data = $this->customer->with(['propertyRequirement'])
            ->where('STATUS', '!=', 3)
            ->where('USER_TYPE', 1)
            ->orderBy('NAME', 'ASC')
            ->get();
        return $this->formatResponse(true, '', 'admin.customer.index', $data);
    }

    public function getPayment($id)
    {

    }

    public function getEdit(int $id)
    {
        $data = Customer::where('PK_NO', $id)->first();
        if (!empty($data)) {

            return $this->formatResponse(true, '', 'admin.seeker.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.seeker.list', null);
    }

    public function postUpdate($request): object
    {
        DB::beginTransaction();
        try {
            if ($request->pk_no) {
                $list = ProductRequirements::find($request->pk_no);
            } else {
                $list = new ProductRequirements();
                $list->CREATED_BY = Auth::id();
            }

            $rc = $request->condition;
            $cond = [];
            $condF = [];
            if (!$rc) {
                $rc = [];
            }
            foreach ($rc as $item) {
                $item = explode(',', $item);
        //      $condF[] = intval($item[0]);
                $condF[] = "$item[0]";
                $cond[] = $item[1];
            }

            $list->PROPERTY_FOR         = $request->itemCon;
            $list->F_CITY_NO            = $request->city;
            $list->F_AREAS              = json_encode($request->area);
            $list->F_PROPERTY_TYPE_NO   = $request->property_type;
            $list->MIN_SIZE             = $request->minimum_size;
            $list->MAX_SIZE             = $request->maximum_size;
            $list->MIN_BUDGET           = $request->minimum_budget;
            $list->MAX_BUDGET           = $request->maximum_budget;
            $list->BEDROOM              = json_encode($request->rooms);
            $list->PROPERTY_CONDITION   = json_encode($cond);
            $list->F_PROPERTY_CONDITION = json_encode($condF);
            $list->REQUIREMENT_DETAILS  = $request->requirement_details;
            $list->PREP_CONT_TIME       = $request->time;
            $list->MAX_SHARING_PERMISSION = $request->max_sharing_permission;
            $list->EMAIL_ALERT          = $request->alert;
            $list->IS_VERIFIED          = $request->v_status;
            $list->MODIFIED_BY          = Auth::id();
            $list->F_USER_NO            = $request->user_id;
            $list->LEAD_PRICE    = $request->lead_price;

            if ($request->v_status == 1 && $request->acc_status == 1) {
                $list->F_VERIFIED_BY    = Auth::id();
                $list->VERIFIED_AT      = date('Y-m-d H:i:s');
            }

            $list->save();

            $user = Customer::where('PK_NO', $request->user_id)->first();
            $user->NAME         = $request->name;
            $user->EMAIL        = $request->email;
            $user->ADDRESS      = $request->address;
            $user->MOBILE_NO    = $request->mobile;
            $user->STATUS       = $request->acc_status;
            $user->update();

             //share to developer company
            if ($request->v_status == 1 && $request->acc_status == 1) {
                $req = ProductRequirements::find($list->PK_NO);
                $property_for       = $req->PROPERTY_FOR;
                $property_type      = $req->F_PROPERTY_TYPE_NO;
                $size_min           = $req->MIN_SIZE-1000;
                $size_max           = $req->MAX_SIZE+1000;
                $property_condition = json_decode($req->F_PROPERTY_CONDITION);
                $area_nos = json_decode($req->F_AREAS);


                $listings =  Product::select('PRD_LISTINGS.PK_NO','PRD_LISTINGS.F_USER_NO','PRD_LISTING_VARIANTS.PROPERTY_SIZE')
                ->join('PRD_LISTING_VARIANTS', 'PRD_LISTING_VARIANTS.F_LISTING_NO', 'PRD_LISTINGS.PK_NO')
                ->join('WEB_USER', 'WEB_USER.PK_NO', 'PRD_LISTINGS.F_USER_NO')
                ->where('PRD_LISTINGS.STATUS',10)
                ->where('PRD_LISTINGS.PAYMENT_STATUS',1)
                ->where('PRD_LISTINGS.PROPERTY_FOR',$property_for)
                ->where('PRD_LISTINGS.F_PROPERTY_TYPE_NO',$property_type)
                ->whereIn('PRD_LISTINGS.F_AREA_NO',$area_nos)
                ->whereBetween('PRD_LISTING_VARIANTS.PROPERTY_SIZE', [$size_min,$size_max]);
                if($property_condition){
                    $listings->whereIn('PRD_LISTINGS.F_PROPERTY_CONDITION',$property_condition);
                }
                $listings = $listings->where('WEB_USER.STATUS',1)
                ->groupBy('PRD_LISTINGS.PK_NO')
                ->orderBy('PRD_LISTINGS.MODIFIED_AT', 'DESC')
                ->get();
                if($listings && count($listings) > 0 ){
                    $max_share = 0;
                    foreach ($listings as $key => $value) {
                        DB::table('PRD_LEAD_SHARE_MAP')->where('F_USER_NO',$list->F_USER_NO)->where('F_COMPANY_NO',$value->F_USER_NO)->where('STATUS',0)->where('LEAD_TYPE',0)->delete();
                        $check_old = DB::table('PRD_LEAD_SHARE_MAP')->where('F_USER_NO',$list->F_USER_NO)->where('F_COMPANY_NO',$value->F_USER_NO)->first();
                        if($check_old == null){
                            $order_id = 1+$key;
                            $max_share++;
                            DB::table('PRD_LEAD_SHARE_MAP')->insert([
                                'F_REQUIREMENT_NO'  => $req->PK_NO,
                                'F_USER_NO'         => $req->F_USER_NO,
                                'CREATED_AT'        => date('Y-m-d H:i:s'),
                                'CREATED_BY'        => Auth::id(),
                                'F_COMPANY_NO'      => $value->F_USER_NO,
                                'F_LISTING_NO'      => $value->PK_NO,
                                'STATUS'            => 0,
                                'ORDER_ID'          => $order_id
                            ]);

                            if($max_share > $list->MAX_SHARING_PERMISSION ){ break;}

                        }

                    }
                }

            }
            //END share to developer company


        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            if ($request->pk_no) {
                return $this->formatResponse(false, 'Property Seeker not updated successfully !', 'admin.seeker.list');
            } else {
                return $this->formatResponse(false, 'Property Seeker not created successfully !', 'admin.seeker.list');
            }
        }

        DB::commit();
        if ($request->pk_no) {
            return $this->formatResponse(true, 'Property Seeker Updated successfully !', 'admin.seeker.list');
        } else {
            return $this->formatResponse(true, 'Property Seeker Created successfully !', 'admin.seeker.list');
        }
    }

    public function getCustomerPayment($id)
    {
        try {
            $data = Transaction::where('F_CUSTOMER_NO', $id)->get();
        } catch (\Throwable $th) {
            return $this->formatResponse(fasle, 'Data not found', 'admin.seeker.list');
        }
        return $this->formatResponse(true, 'Payment list found successfully !', 'admin.seeker.list', $data);
    }

    public function postRecharge($request, int $id): object
    {
        $status = false;
        $msg = 'Recharge not successful!';

        DB::beginTransaction();
        try {
            $payment = new PaymentCustomer();
            $payment->F_CUSTOMER_NO = $id;
            $payment->AMOUNT = $request->amount;
            $payment->F_ACC_PAYMENT_BANK_NO = $request->payment_account ?? 4;
            $payment->PAYMENT_CONFIRMED_STATUS = 1;
            $payment->PAYMENT_NOTE = $request->note;
            $payment->PAYMENT_DATE = date('Y-m-d', strtotime($request->payment_date));
            $payment->PAYMENT_TYPE = $request->payment_type;
            $payment->SLIP_NUMBER = $request->slip_number;

            if ($request->hasFile('images')) {
                $file = $request->file('images')[0];
                $file_name = uniqid() . '.' . $file->getClientOriginalExtension();
                $file_path = 'uploads/attachments/' . $id . '/';
                $file->move(public_path($file_path), $file_name);

                $payment->ATTACHMENT_PATH = $file_path . $file_name;
            }
            $payment->save();

            $status = true;
            $msg = 'Recharge successful!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($status, $msg, 'admin.seeker.payment');
    }

    public function getCustomerTxn($id): object
    {
        try {
            $data = Transaction::with(['payment'])->where('F_CUSTOMER_NO', $id)->get();
        } catch (\Throwable $th) {
            return $this->formatResponse(false, 'Data not found', 'admin.seeker.list');
        }
        return $this->formatResponse(true, 'Payment list found successfully !', 'admin.seeker.list', $data);

    }

    /*


    public function getCusAdd(int $id)
    {
        $data =  CustomerAddress::join('SS_COUNTRY','SS_COUNTRY.PK_NO','SLS_CUSTOMERS_ADDRESS.F_COUNTRY_NO')
                        ->select('SLS_CUSTOMERS_ADDRESS.*','SS_COUNTRY.DIAL_CODE')
                        ->where('F_CUSTOMER_NO', $id)->where('IS_ACTIVE', 1)->get();

        if ($data && count($data) > 0) {
            return $this->formatResponse(true, 'Data found', 'admin.customer.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.customer.list', null);
    }

    public function postStore($request)
    {
        DB::beginTransaction();

        try {
            $mobile = (int)$request->mobileno;
            $check_customer = Customer::where('MOBILE_NO',$mobile)->first();
            $check_reseller = Reseller::where('MOBILE_NO',$mobile)->first();
            if($check_customer){
                return $this->formatResponse(false, 'This mobile no exists in customer table', 'admin.customer.create');
            }
            if($check_reseller){
                return $this->formatResponse(false, 'This mobile no exists in reseller table', 'admin.customer.create');
            }

            $customer                  = new Customer();
            $customer->NAME            = str_replace("’","'",$request->customername);
            $customer->F_COUNTRY_NO    = $request->country3;
            $customer->MOBILE_NO       = $mobile;
            $customer->ALTERNATE_NO    = $request->altno;
            $customer->EMAIL           = $request->email;
            $customer->FB_ID           = $request->fbid;
            $customer->IG_ID           = $request->insid;
            $customer->UKSHOP_ID       = $request->ukid;
            $customer->CUSTOMER_NO     = $request->customer_no;
            $customer->UKSHOP_PASS     = bcrypt($request->ukpass);
            $customer->F_RESELLER_NO   = $request->scustomer == 'reseller' ? $request->agent : null;
            $customer->IS_ACTIVE       = 1;
            $customer->save();
            $customer->PK_NO;

            if (isset($request->same_as_add) && $request->same_as_add == 0 && $request->same_as_add != 'on') {

                $customer_add                           = new CustomerAddress();
                $customer_add->NAME                     = str_replace("’","'",$request->customeraddress);
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
                $customer_add->F_ADDRESS_TYPE_NO        = 1;
                $customer_add->F_CUSTOMER_NO            = $customer->PK_NO;
                $customer_add->IS_ACTIVE                = 1;
                $customer_add->IS_DEFAULT               = 1;
                $customer_add->save();

                $customer_add                           = new CustomerAddress();
                $customer_add->NAME                     = str_replace("’","'",$request->customeraddress);
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
                $customer_add->F_ADDRESS_TYPE_NO        = 2;
                $customer_add->F_CUSTOMER_NO            = $customer->PK_NO;
                $customer_add->IS_ACTIVE                = 1;
                $customer_add->save();

            }else if (isset($request->same_as_add) && $request->same_as_add == 'on') {

                $customer_add                           = new CustomerAddress();
                $customer_add->NAME                     = str_replace("’","'",$request->customeraddress);
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
                $customer_add->F_CUSTOMER_NO            = $customer->PK_NO;
                $customer_add->IS_ACTIVE                = 1;
                $customer_add->IS_DEFAULT               = 1;
                $customer_add->save();

                $customer_add                           = new CustomerAddress();
                $customer_add->NAME                     = str_replace("’","'",$request->customeraddress2);
                $customer_add->TEL_NO                   = (int)$request->mobilenoadd2;
                $customer_add->ADDRESS_LINE_1           = $request->ad_12;
                $customer_add->ADDRESS_LINE_2           = $request->ad_22;
                $customer_add->ADDRESS_LINE_3           = $request->ad_32;
                $customer_add->ADDRESS_LINE_4           = $request->ad_42;
                $customer_add->LOCATION                 = $request->location2;
                $customer_add->F_COUNTRY_NO             = $request->country2;
                $customer_add->STATE                    = $request->state2;
                $customer_add->CITY                     = $request->city2;
                $customer_add->POST_CODE                = $request->post_code2;
                $customer_add->F_ADDRESS_TYPE_NO        = $request->addresstype2;
                $customer_add->F_CUSTOMER_NO            = $customer->PK_NO;
                $customer_add->IS_ACTIVE                = 1;
                $customer_add->save();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.customer.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Customer has been created successfully !', 'admin.customer.list');
    }

    public function postUpdate($request, $PK_NO)
    {
        // $check_dup = AccountSource::where('NAME',$request->name)->first();
        // if ($check_dup !== null) {
        //     return $this->formatResponse(false, 'Duplicate entry for Payment Source !', 'admin.account.list');
        // }
        $customer = Customer::where('PK_NO', $PK_NO)->first();
        $customer->NAME            = str_replace("’","'",$request->customername);
        $customer->MOBILE_NO       = (int)$request->mobileno;
        $customer->ALTERNATE_NO    = $request->altno;
        $customer->EMAIL           = $request->email;
        $customer->FB_ID           = $request->fbid;
        $customer->IG_ID           = $request->insid;
        $customer->UKSHOP_ID       = $request->ukid;
        $customer->UKSHOP_PASS     = bcrypt($request->ukpass);
        $customer->F_RESELLER_NO   = $request->scustomer == 'reseller' ? $request->agent : null;

            if ($customer->update()) {
                $customer = Customer::where('PK_NO', $PK_NO)->first();
                return $this->formatResponse(true, 'Customer Information has been Updated successfully', 'admin.customer.list');
            }

            return $this->formatResponse(false, 'Unable to update Customer Information !', 'admin.customer.list');
    }

    public function delete($PK_NO)
    {
        $customer = Customer::where('PK_NO',$PK_NO)->first();
        $customer->IS_ACTIVE = 0;
        if ($customer->update()) {
            return $this->formatResponse(true, 'Successfully deleted Customer Information', 'admin.customer.list');
        }
        return $this->formatResponse(false,'Unable to delete Customer Information','admin.customer.list');
    }

    public function addNewCustomer($request)
    {

        DB::beginTransaction();
        try {
            $mobile = (int)$request->customer_mobile;
            $checkReseller = Reseller::where('MOBILE_NO',$mobile)->first();
            if($checkReseller){
                return ['customer_no'=>null,'customer_id'=>0, 'duplicate' => '1' ];
            }else{
                $customer                  = new Customer();
                $customer->NAME            = str_replace("’","'",$request->customer_name);
                $customer->MOBILE_NO       = $mobile;
                $customer->F_COUNTRY_NO    = $request->country;
                $customer->EMAIL           = $request->custom_email;
                $customer->F_RESELLER_NO   = null;
                $customer->IS_ACTIVE       = 1;
                $customer->save();
            }

            // $customer_add                           = new CustomerAddress();
            // $customer_add->NAME                     = $request->customer_name;
            // $customer_add->TEL_NO                   = $request->customer_mobile;
            // $customer_add->F_ADDRESS_TYPE_NO        = 1;
            // $customer_add->F_CUSTOMER_NO            = $customer->PK_NO;
            // $customer_add->IS_ACTIVE                = 1;
            // $customer_add->F_COUNTRY_NO             = $request->country;
            // $customer_add->save();
        } catch (\Exception $e) {
            DB::rollback();
            return ['customer_no'=>null,'customer_id'=>0,'duplicate' => '0'];
        }
        DB::commit();
        $customer_info = $this->customer::find($customer->PK_NO);

        return ['customer_no'=>$customer_info->CUSTOMER_NO,'customer_id'=>$customer_info->PK_NO
        // ,'cus_add_pk'=>$customer_add->PK_NO
        ];
    }

    public function postBlanceTransfer($request)
    {
        DB::beginTransaction();
        try {
        //$from_customer = Customer::find($request->from_customer);
        //$to_customer = Customer::find($request->to_customer_hidden);

        $balance =  PaymentCustomer::find($request->payment_no);

        if( ( $balance->PAYMENT_REMAINING_MR >= $request->amount_to_trans ) && ($request->from_customer != $request->to_customer_hidden) ){

            $trn = new AccResellerCustomerTnx();
            $trn->F_FROM_CUSTOMER_NO = $request->from_customer;
            $trn->F_FROM_CUSTOMER_PAYMENT_NO = $request->payment_no;
            $trn->F_TO_CUSTOMER = $request->to_customer_hidden;
            $trn->AMOUNT = $request->amount_to_trans;
            $trn->save();


        }else{
            return $this->formatResponse(false,'Balance transfer not successfull 1','admin.customer.list');
        }

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false,'Balance transfer not successfull 2','admin.customer.list');
        }
        DB::commit();
        return $this->formatResponse(true,'Balance transfer successfull ! 3','admin.customer.list');

    }

    public function getRemainingBalance($id)
    {
        $data = PaymentCustomer::where('F_CUSTOMER_NO',$id)->where('PAYMENT_REMAINING_MR','>',0)->get();

        $response = '<option value="">- select one -</option>';

            if ($data) {
                foreach ($data as $value) {
                    $response .= '<option value="'.$value->PK_NO.'">PAYID'.$value->PK_NO.' - '.$value->PAYMENT_REMAINING_MR.'</option>';
                }
            }else{
                $response .= '<option value="">No data found</option>';
            }


        return $response;
    }

    public function postRefundRequest($request)
    {
        DB::beginTransaction();
        try {
            $bank = BankList::find($request->bank_no);
            $refund =  new RefundRequest();
            if($request->is_customer == 1){
                $refund->F_CUSTOMER_NO  = $request->customer_no;
                $refund->IS_CUSTOMER    = $request->is_customer;
            }else{
                $refund->F_RESELLER_NO  = $request->reseller_no;
                $refund->IS_CUSTOMER    = $request->is_customer;
            }
            if($bank){
                $refund->F_ACC_BANK_LIST_NO = $request->bank_no;
                $refund->REQ_BANK_NAME  = $bank->BANK_NAME;
            }
            $refund->REQ_BANK_ACC_NAME  = $request->cust_acc_name;
            $refund->REQ_BANK_ACC_NO     = $request->cust_acc_no;
            $refund->MR_AMOUNT          = $request->refund_amount;
            $refund->REQUEST_NOTE       = $request->refund_note;
            $refund->REQUEST_BY         = Auth::user()->PK_NO;
            $refund->REQUEST_BY_NAME    = Auth::user()->USERNAME;
            $refund->REQUEST_DATE       = date('Y-m-d');
            $refund->STATUS             = 0;
            $refund->save();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false,'Refund request not successfull','admin.customer.refund');
        }
        DB::commit();
        return $this->formatResponse(true,'Refund request successfull','admin.customer.refund');

    }
    public function getRefundedRequestDeny($request, $id){


        DB::beginTransaction();
        try {
            $check =  PaymentCustomer::where('F_ACC_CUST_RES_REFUND_REQUEST_NO',$id)->first();
            if($check){
                dd($check);
                $msg = 'This request already accepted';
            }else{
                RefundRequest::where('PK_NO',$id)->update(['STATUS' => 2]);
            }
            $msg = 'Refund request deny successfull';

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false,'Refund request deny not successfull','admin.customer.refundrequest');
        }
        DB::commit();
        return $this->formatResponse(true,$msg,'admin.customer.refundrequest');

    }

    public function getCustomerHistory($id)
    {
        try {

        $result = DB::SELECT("SELECT result.* FROM (

            SELECT o.PK_NO as ORDER_PK_NO, o.F_CUSTOMER_NO, o.CUSTOMER_NAME, o.ORDER_ACTUAL_TOPUP, o.ORDER_BUFFER_TOPUP, o.ORDER_BALANCE_RETURN, o.DISPATCH_STATUS, b.PK_NO AS BOOKING_PK_NO, b.BOOKING_NO, DATE(b.RECONFIRM_TIME) as ORDER_DATE, SUM(IFNULL(b.TOTAL_PRICE,0) - b.DISCOUNT + IFNULL(b.PENALTY_FEE,0) - IFNULL(b.CUSTOMER_POSTAGE,0)) AS ORDER_PRICE, b.DISCOUNT AS ORDER_DISCOUNT, SUM(IFNULL(b.TOTAL_PRICE,0) - b.DISCOUNT - o.ORDER_ACTUAL_TOPUP + IFNULL(b.PENALTY_FEE,0)  - IFNULL(b.CUSTOMER_POSTAGE,0) ) AS ORDER_DUE, b.PENALTY_FEE, b.BOOKING_STATUS, o.IS_CANCEL,DATE(b.RECONFIRM_TIME) as DATE_AT, NULL AS PAY_PK_NO, NULL AS PAYMENT_NO, NULL AS PAY_AMOUNT, NULL AS REFUND_MAPING, NULL AS PAYMENT_REMAINING_MR, NULL as TX_PK_NO, NULL AS PAYMENT_VERIFY, NULL AS F_BOOKING_NO_FOR_PAYMENT_TYPE3, NULL AS RETURN_PRICE, b.F_SS_CREATED_BY AS ENTRY_BY_NO, u.USERNAME as ENTRY_BY_NAME, b.SS_CREATED_ON as ENTRY_AT, 1 AS  ORDER_ID, 'Order Placed' AS TYPE  FROM SLS_ORDER as o JOIN SLS_BOOKING AS b ON b.PK_NO = o.F_BOOKING_NO LEFT JOIN SA_USER as u ON u.PK_NO = b.F_SS_CREATED_BY WHERE o.F_CUSTOMER_NO = $id GROUP BY o.PK_NO

            UNION


            SELECT NULL as ORDER_PK_NO, cp.F_CUSTOMER_NO, cp.CUSTOMER_NAME, NULL AS ORDER_ACTUAL_TOPUP, NULL AS ORDER_BUFFER_TOPUP, NULL AS ORDER_BALANCE_RETURN, NULL AS DISPATCH_STATUS, NULL AS BOOKING_PK_NO, NULL AS BOOKING_NO, NULL AS ORDER_DATE, NULL AS ORDER_PRICE, NULL AS ORDER_DISCOUNT, NULL AS ORDER_DUE, NULL AS PENALTY_FEE, NULL AS BOOKING_STATUS, NULL AS IS_CANCEL, cp.PAYMENT_DATE as DATE_AT, cp.PK_NO AS PAY_PK_NO, t.CODE AS PAYMENT_NO, cp.MR_AMOUNT AS PAY_AMOUNT, cp.REFUND_MAPING, cp.PAYMENT_REMAINING_MR, t.PK_NO as TX_PK_NO, t.IS_MATCHED AS PAYMENT_VERIFY,cp.F_BOOKING_NO_FOR_PAYMENT_TYPE3, NULL AS RETURN_PRICE, cp.F_SS_CREATED_BY AS ENTRY_BY_NO, u.USERNAME as ENTRY_BY_NAME, cp.SS_CREATED_ON as ENTRY_AT, 3 AS ORDER_ID, 'Payment' AS TYPE FROM ACC_CUSTOMER_PAYMENTS AS cp LEFT JOIN SA_USER as u ON u.PK_NO = cp.F_SS_CREATED_BY LEFT JOIN ACC_BANK_TXN AS t ON t.F_CUSTOMER_PAYMENT_NO = cp.PK_NO  WHERE cp.F_CUSTOMER_NO = $id AND cp.PAYMENT_TYPE = 1



            UNION

            SELECT NULL as ORDER_PK_NO, cp.F_CUSTOMER_NO, cp.CUSTOMER_NAME, NULL AS ORDER_ACTUAL_TOPUP, NULL AS ORDER_BUFFER_TOPUP, NULL AS ORDER_BALANCE_RETURN, NULL AS DISPATCH_STATUS, NULL AS BOOKING_PK_NO, NULL AS BOOKING_NO, NULL AS ORDER_DATE, NULL AS ORDER_PRICE, NULL AS ORDER_DISCOUNT,NULL AS ORDER_DUE, NULL AS PENALTY_FEE, NULL AS BOOKING_STATUS, NULL AS IS_CANCEL, cp.PAYMENT_DATE as DATE_AT, cp.PK_NO AS PAY_PK_NO, t.CODE AS PAYMENT_NO, cp.MR_AMOUNT AS PAY_AMOUNT, NULL AS REFUND_MAPING, cp.PAYMENT_REMAINING_MR, t.PK_NO as TX_PK_NO, t.IS_MATCHED AS PAYMENT_VERIFY, cp.F_BOOKING_NO_FOR_PAYMENT_TYPE3, NULL AS RETURN_PRICE, cp.F_SS_CREATED_BY AS ENTRY_BY_NO, u.USERNAME as ENTRY_BY_NAME, cp.SS_CREATED_ON as ENTRY_AT, 6 AS ORDER_ID, 'Refund' AS TYPE FROM ACC_CUSTOMER_PAYMENTS AS cp LEFT JOIN SA_USER as u ON u.PK_NO = cp.F_SS_CREATED_BY LEFT JOIN ACC_BANK_TXN AS t ON t.F_CUSTOMER_PAYMENT_NO = cp.PK_NO  WHERE cp.F_CUSTOMER_NO = $id AND cp.PAYMENT_TYPE = 2

            ) result ORDER BY result.DATE_AT ASC, result.ORDER_ID ASC

            ");

            if($result){
                foreach ($result as $key => $value) {
                    if($value->TYPE == 'Payment'){
                        $value->allOrderPayments =  OrderPayment::where('F_ACC_CUSTOMER_PAYMENT_NO',$value->PAY_PK_NO)->where('IS_CUSTOMER',1)->get();
                        if($value->REFUND_MAPING){
                            $refund_pk = array();
                            $refunds = explode('|',$value->REFUND_MAPING);
                            foreach ($refunds as $key2 => $value2) {
                                if($value2 != ''){
                                    $refund = explode(',',$value2);

                                    array_push($refund_pk,$refund[0]);
                                }
                            }
                            if($refund_pk){
                                $value->allRefunds = PaymentCustomer::whereIn('PK_NO',$refund_pk)->get();

                            }

                        }
                    }elseif($value->TYPE == 'Order Placed'){
                        $value->allPaymentForTheOrder = OrderPayment::where('ORDER_NO', $value->ORDER_PK_NO)->where('IS_CUSTOMER',1)->get();

                    }elseif($value->TYPE == 'AM payment'){
                        $value->amPaymentForOrder = Booking::find($value->F_BOOKING_NO_FOR_PAYMENT_TYPE3);

                    }

                }
            }
        } catch (\Exception $e) {
            return $this->formatResponse(false,'data not found','admin.customer.list');
        }
        return $this->formatResponse(true,'data found','admin.customer.list', $result);


    }

    */


}
