<?php

namespace App\Repositories\Admin\Datatable;

use App\Models\ProductRequirements;
use Carbon\Carbon;
use App\Models\Stock;
use App\Models\Product;
use App\Models\OrderRtc;
use App\Traits\CommonTrait;
use App\Models\AuthUserGroup;
use App\Models\BookingDetails;
use App\Models\PaymentBankAcc;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class DatatableAbstract implements DatatableInterface
{
    use CommonTrait;

    public function __construct()
    {

    }

    public function getSeeker($request)
    {
        $dataSet = DB::table("WEB_USER as c")->select('c.*')
        ->leftJoin('PRD_REQUIREMENTS as b', function($join){
            $join->on('c.PK_NO', '=', 'b.F_USER_NO');
            $join->on('b.IS_ACTIVE','=',DB::raw("'1'"));
        })->where('c.STATUS', '!=', 3)->where('c.USER_TYPE', 1);

        if($request->property_for != ''){
            $dataSet->where('b.PROPERTY_FOR', $request->property_for);
        }
        if($request->property_type != ''){
            $dataSet->where('b.F_PROPERTY_TYPE_NO', $request->property_type);
        }

        if($request->lead_status != ''){
            $dataSet->where('b.IS_VERIFIED', $request->lead_status);
        }

        if($request->city != ''){
            $dataSet->where('b.F_CITY_NO', $request->city);
        }
        if($request->area != ''){
            $dataSet->whereJsonContains('b.F_AREAS', $request->area);
        }

        $dataSet = $dataSet->orderBy('c.PK_NO', 'DESC')->get();

        return Datatables::of($dataSet)
            ->addColumn('status', function ($dataSet) {
                $status = '';
                if ($dataSet->STATUS == 1) {
                    $status = '<span class="t-pub">Active</span>';
                } else {
                    $status = '<span class="t-del">Inactive</span>';
                }
                return $status;

            })
            ->addColumn('leadStatus', function ($dataSet) {
                $requirement = ProductRequirements::select('IS_VERIFIED')->where('F_USER_NO', $dataSet->PK_NO)->where('IS_ACTIVE', 1)->first();
                if ($requirement) {
                    $verify = Config::get('static_array.seeker_verification_status');
                    return $verify[$requirement->IS_VERIFIED] ?? '';
                }
                return 'N/A';
            })
            ->addColumn('leadInfo', function ($dataSet) {
                $info = '';
                $requirement = ProductRequirements::where('F_USER_NO', $dataSet->PK_NO)->where('IS_ACTIVE', 1)->first();
                if ($requirement) {

                    $area_no = json_decode($requirement->F_AREAS);
                    $areas = DB::table('SS_AREA')->whereIn('PK_NO',$area_no)->pluck('AREA_NAME')->implode(', ');

                    $info .= '<div>For : '.$requirement->PROPERTY_FOR.'</div>';
                    $info .= '<div>City : '.$requirement->CITY_NAME.'</div>';
                    $info .= '<div>Area : '.$areas.'</div>';
                    $info .= '<div>Type : '.$requirement->PROPERTY_TYPE.'</div>';
                    return $info;

                }
                return 'N/A';
            })
            ->addColumn('action', function ($dataSet) {
                $roles = userRolePermissionArray();
                $edit = $view = $payment = '';
                if (hasAccessAbility('view_seeker', $roles)) {
                    $view = ' <a href="' . route("admin.seeker.view", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-info mb-05 mr-05" title="View">View</a>';
                }
                if (hasAccessAbility('edit_seeker', $roles)) {
                    $edit = ' <a href="' . route("admin.seeker.edit", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-warning mb-05 mr-05" title="Edit">Edit</a>';
                }
                if (hasAccessAbility('view_seeker_payment', $roles)) {
                    $payment = ' <a href="' . route("admin.seeker.payment", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-success mb-05 mr-05" title="View Payment">Payment</a>';
                }
                return $view . $edit . $payment;

            })
            ->rawColumns(['action', 'status','leadInfo'])
            ->make(true);

    }

    public function getOwner($request)
    {
        $userType = [2, 3, 4];
        if ($request->get('owner')) {
            $userType = [$request->get('owner')];
        }

        $dataSet = DB::table("WEB_USER as c")
            ->where('STATUS', '!=', 3)
            ->whereIn('USER_TYPE', $userType)
            ->orderBy('PK_NO', 'DESC')
            ->get();
        return Datatables::of($dataSet)
            ->addColumn('status', function ($dataSet) {
                if ($dataSet->STATUS == 1) {
                    $status = '<span class="t-pub">Active</span>';
                } else {
                    $status = '<span class="t-del">Inactive</span>';
                }
                return $status;
            })
            ->addColumn('name', function ($dataSet) {
                $name = '';
                if ($dataSet->USER_TYPE == 2) {
                    $name = 'Owner';
                } else if ($dataSet->USER_TYPE == 3) {
                    $name = 'Builder';
                } else if ($dataSet->USER_TYPE == 4) {
                    $name = 'Agency';
                }
                return $dataSet->NAME .' ('.$name.')';
            })
            ->addColumn('total_list', function ($dataSet) {
                $total_list = '';
                $total_list = '<a href="' . route("admin.product.list", ['user_id' => $dataSet->PK_NO]) . '">'.$dataSet->TOTAL_LISTING.'</a>';
                return $total_list;
            })
            ->addColumn('created_at', function ($dataSet) {
                $created_at = '';
                $created_at .= '<div>'.date('d M, y',strtotime($dataSet->CREATED_AT)).'</div>';
                $created_at .= '<div class="font-10">'.date('h:i A',strtotime($dataSet->CREATED_AT)).'</div>';
                // $created_at = ;

                return $created_at;
            })
            ->addColumn('contact', function ($dataSet) {
                $email = $mobile = $contact = '';
                if($dataSet->COUNTRY_CODE != 'bd' && $dataSet->IS_VERIFIED == 1){
                    $email = $dataSet->EMAIL.' <span class="text-success" title="Otp verified"><i class="fa fa-check" aria-hidden="true"></i></span>';
                }elseif($dataSet->COUNTRY_CODE != 'bd' && $dataSet->IS_VERIFIED == 0){
                    $email = $dataSet->EMAIL.' <span class="text-danger" title="Otp not verified"><i class="fa fa-check" aria-hidden="true"></i></span>';
                }else{
                    $email = $dataSet->EMAIL;
                }

                if($dataSet->COUNTRY_CODE == 'bd' && $dataSet->IS_VERIFIED == 1){
                    $mobile = $dataSet->MOBILE_NO.' <span class="text-success" title="Otp verified"><i class="fa fa-check" aria-hidden="true"></i></span>';
                }elseif($dataSet->COUNTRY_CODE == 'bd' && $dataSet->IS_VERIFIED == 0){
                    $mobile = $dataSet->MOBILE_NO.' <span class="text-danger" title="Otp not verified"><i class="fa fa-check" aria-hidden="true"></i></span>';
                }else{
                    $mobile = $dataSet->MOBILE_NO;
                }


                $contact .= '<div>'.$email.'</div>';
                $contact .= '<div>'.$mobile.'</div>';
                return $contact;
            })

            ->addColumn('action', function ($dataSet) {
                $roles = userRolePermissionArray();
                $edit = $cp = $view = $payment = '';
                if (hasAccessAbility('view_owner', $roles)) {
                    $view = ' <a href="' . route("admin.owner.view", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-info mb-05 mr-05" title="View">View</span>';
                }
                if (hasAccessAbility('edit_owner', $roles)) {
                    $edit = ' <a href="' . route("admin.owner.edit", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-success mb-05 mr-05" title="Edit">Edit</a>';
                }
                if (hasAccessAbility('view_owner_payment', $roles)) {
                    $payment = ' <a href="' . route("admin.owner.payment", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-success mb-05 mr-05" title="View Payment">Payment</a>';
                }
                if (hasAccessAbility('edit_owner', $roles)) {
                    $cp = ' <a href="' . route("admin.owner.password.edit", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-success mb-05 mr-05" title="Change Password">CP</a>';
                }
                return $view . $edit . $payment . $cp;
            })
            ->rawColumns(['action', 'status','total_list','contact','name','created_at'])
            ->make(true);
    }

    public function getAgents($request)
    {
        $dataSet = DB::table("WEB_USER as c")
            ->where('STATUS', '!=', 3)
            ->where('USER_TYPE', '=', 5)
            ->orderBy('PK_NO', 'DESC')
            ->get();
        return Datatables::of($dataSet)
            ->addColumn('status', function ($dataSet) {
                $status = [
                    0 => 'Pending',
                    1 => 'Active',
                    2 => 'Inactive',
                    3 => 'Deleted'
                ];
                return $status[$dataSet->STATUS] ?? '';
            })
            ->addColumn('action', function ($dataSet) {
                $roles = userRolePermissionArray();
                $edit = $payment = $areas = '';
                if (hasAccessAbility('edit_agents', $roles)) {
                    $edit = ' <a href="' . route("admin.agents.edit", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-success mb-05 mr-05" title="Edit">Edit</a>';
                }
                if (hasAccessAbility('view_agent_earnings', $roles)) {
                    $payment = ' <a href="' . route("admin.agent_earnings", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-success mb-05 mr-05" title="View Earnings">Earnings</a>';
                }

                if (hasAccessAbility('view_agent_earnings', $roles)) {
                    $areas = ' <a href="' . route("admin.agent.area", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-success mb-05 mr-05" title="View Earnings">Areas</a>';
                }

                return $edit . $payment . $areas;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function getProperty($request)
    {
        $dataSet = DB::table('PRD_LISTINGS as p')
            ->orderBy('PK_NO', 'DESC');
        if ($request->user_type != '' ) {
            $dataSet->where('p.USER_TYPE', $request->user_type);
        }
        if ($request->user_id != '' ) {
            $dataSet->where('p.F_USER_NO', $request->user_id);
        }
        if ($request->property_for != '') {
            $dataSet->where('p.PROPERTY_FOR', $request->property_for);
        }
        if ($request->listing_type != '') {
            $dataSet->where('p.F_LISTING_TYPE', $request->listing_type);
        }
        if ($request->payment_status != '') {
            $dataSet->where('p.PAYMENT_STATUS', $request->payment_status);
        }

        if ($request->property_status != '' ) {
            $dataSet->where('p.STATUS', $request->property_status);
        }
        if ($request->city != '' ) {
            $dataSet->where('p.F_CITY_NO', $request->city);
        }

        $dataSet = $dataSet->get();


        return Datatables::of($dataSet)
            ->addColumn('status', function ($dataSet) {
                $status = Config::get('static_array.property_status');
                return $status[$dataSet->STATUS] ?? '';
            })
            ->addColumn('user_id', function ($dataSet) {
                return DB::table('WEB_USER')->where('PK_NO', '=', $dataSet->F_USER_NO)
                    ->first('CODE')->CODE;
            })
            ->addColumn('user_name', function ($dataSet) {
                $user_name = '';
                $roles = userRolePermissionArray();
                $user = DB::table('WEB_USER')->where('PK_NO', '=', $dataSet->F_USER_NO)->first('NAME');
                if (hasAccessAbility('view_owner', $roles)) {
                    $user_name = '<a href="'.route('admin.owner.view', [$dataSet->F_USER_NO]).'">'.$user->NAME.'</a>';
                }else{
                    $user_name = '<a href="javascript:void(0)">'.$user->NAME.'</a>';
                }

                return $user_name;
            })
            ->addColumn('payment_status', function ($dataSet) {
                $status = [
                    0 => 'Due',
                    1 => 'Paid'
                ];
                return $status[$dataSet->PAYMENT_STATUS];
            })
            ->addColumn('user_type', function ($dataSet) {
                $status = [
                    2 => 'Owner',
                    3 => 'Builder',
                    4 => 'Agency',
                    5 => 'Agent'
                ];
                return $status[$dataSet->USER_TYPE];
            })
            ->addColumn('mobile', function ($dataSet) {
                $mobile = '';
                if ($dataSet->MOBILE1) {
                    $mobile = '<span>' . $dataSet->MOBILE1 . '</span>';
                }
                if ($dataSet->MOBILE2) {
                    $mobile .= '<br><span>' . $dataSet->MOBILE2 . '</span>';
                }
                return $mobile;
            })
            ->addColumn('action', function ($dataSet) {
                $roles = userRolePermissionArray();
                $edit = $view = $activity = '';
                if (hasAccessAbility('edit_product_activity', $roles)) {
                    $activity = ' <a href="' . route("admin.product.activity", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-success mb-05 mr-05" title="Activities">Activities</a>';
                }
                if (hasAccessAbility('edit_product', $roles)) {
                    $edit = ' <a href="' . route("admin.product.edit", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-warning mb-05 mr-05" title="Edit">Edit</a>';
                }
                if (hasAccessAbility('view_product', $roles)) {
                    $view = ' <a href="' . route("admin.product.view", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-info mb-05 mr-05" title="View">View</a>';
                }
                return $activity . $edit . $view;
            })
            ->rawColumns(['action', 'status', 'mobile','user_name'])
            ->make(true);
    }

    public function getRefundRequest($request)
    {
        $status = $request->filter;
        $dataSet = DB::table('ACC_CUSTOMER_REFUND')
            ->select('ACC_CUSTOMER_REFUND.*', 'WEB_USER.CODE as USER_CODE', 'WEB_USER.NAME as USER_NAME', 'WEB_USER.MOBILE_NO as USER_MOBILE_NO', 'ACC_CUSTOMER_TRANSACTION.CODE as TID')
            ->leftJoin('WEB_USER', 'WEB_USER.PK_NO', 'ACC_CUSTOMER_REFUND.F_USER_NO')
            ->leftJoin('ACC_CUSTOMER_TRANSACTION', 'ACC_CUSTOMER_TRANSACTION.F_LISTING_LEAD_PAYMENT_NO', 'ACC_CUSTOMER_REFUND.F_LISTING_LEAD_PAYMENT_NO');
        if ($status) {
            $dataSet = $dataSet->where('ACC_CUSTOMER_REFUND.STATUS', '=', $status);
        }
        $dataSet = $dataSet->get();

        return Datatables::of($dataSet)
            ->addColumn('status', function ($dataSet) {
                if ($dataSet->STATUS == 1) {
                    $status = '<span class="text-warning">Pending</span>';
                } else if ($dataSet->STATUS == 2) {
                    $status = '<span class="text-success">Approved</span>';
                } else {
                    $status = '<span class="text-danger">Denied</span>';
                }
                return $status;
            })
            ->addColumn('action', function ($dataSet) {
                $roles = userRolePermissionArray();
                $edit = '';
                if (hasAccessAbility('edit_refund_request', $roles)) {
                    $edit = ' <a href="' . route("admin.refund_request.edit", ['id' => $dataSet->PK_NO]) . '" class="btn btn-xs btn-success mb-05 mr-05" title="Edit">Edit</a>';
                }
                return $edit;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function getRechargeRequest($request)
    {
        $status = $request->filter;
        $dataSet = DB::table('ACC_RECHARGE_REQUEST')
            ->select('ACC_RECHARGE_REQUEST.*', 'C.NAME AS C_NAME', 'C.CODE AS C_CODE', 'C.MOBILE_NO AS C_MOBILE_NO')
            ->leftJoin('WEB_USER AS C', 'C.PK_NO', '=', 'ACC_RECHARGE_REQUEST.F_CUSTOMER_NO')
            ->orderByDesc('ACC_RECHARGE_REQUEST.PK_NO');
        if ($status) {
            if ($status == 3) $status = 0;
            $dataSet = $dataSet->where('ACC_RECHARGE_REQUEST.STATUS', '=', $status);
        }
        $dataSet = $dataSet->get();

        return Datatables::of($dataSet)
            ->addColumn('status', function ($dataSet) {
                if ($dataSet->STATUS == 0) {
                    $status = '<span class="text-warning">Pending</span>';
                } else if ($dataSet->STATUS == 1) {
                    $status = '<span class="text-success">Approved</span>';
                } else {
                    $status = '<span class="text-danger">Denied</span>';
                }
                return $status;
            })
            ->addColumn('action', function ($dataSet) {
                $roles = userRolePermissionArray();
                $edit = '';
                if (hasAccessAbility('edit_refund_request', $roles)) {
                    $edit = ' <a href="' . route('admin.recharge_request.edit', $dataSet->PK_NO) . '" class="btn btn-xs btn-success mb-05 mr-05" title="Edit">Edit</a>';
                }
                return $edit;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }


    /*
   public function getDatatableCustomer()
   {
       $dataSet = DB::table("SLS_CUSTOMERS as c")
       ->select('c.PK_NO','c.CUSTOMER_NO','c.NAME','c.EMAIL','c.MOBILE_NO','c.CUSTOMER_BALANCE_BUFFER','c.CUSTOMER_BALANCE_ACTUAL','c.CUM_BALANCE', 'r.DIAL_CODE')
       ->leftjoin('SS_COUNTRY as r', 'r.PK_NO','c.F_COUNTRY_NO')
       ->where('c.IS_ACTIVE', 1)
       ->orderBy('c.NAME', 'ASC');
       return Datatables::of($dataSet)
                   ->addColumn('mobile', function($dataSet){
                       $mobile = $dataSet->DIAL_CODE.$dataSet->MOBILE_NO;

                       return $mobile;
                   })
                   ->addColumn('action', function($dataSet){
                       $roles = userRolePermissionArray();
                       $edit = $view = $delete = $payment = $balance_trans = $add_booking = $view_booking = $view_order = $view_payment = $view_history = '';

                       if (hasAccessAbility('edit_customer', $roles)) {
                           $edit = '<a href="'.route("admin.customer.edit", [$dataSet->PK_NO]).'" class="btn btn-xs btn-info mb-05 mr-05" title="EDIT"><i class="la la-edit"></i></a>';
                       }
                       if (hasAccessAbility('view_customer', $roles)) {
                           $view = ' <a href="'.route("admin.customer.view", [$dataSet->PK_NO]). '" class="btn btn-xs btn-success mb-05 mr-05" title="VIEW"><i class="la la-eye"></i></a>';
                       }
                       if (hasAccessAbility('delete_customer', $roles)) {
                           $delete = ' <a href="'.route('admin.customer.delete', [$dataSet->PK_NO]).'" class="btn btn-xs btn-danger mb-05" onclick="return confirm('. "'" .'Are you sure you want to delete the customer ?'. "'" .')" title="DELETE"><i class="la la-trash"></i></a>';
                       }
                       if (hasAccessAbility('new_payment', $roles)) {
                           $payment = ' <a href="'.route('admin.payment.create', [$dataSet->PK_NO, 'customer' ]).'" class="btn btn-xs btn-primary mb-05 mr-05" title="Add new payment"><i class="la la-usd"></i></a>';
                       }
                       if (hasAccessAbility('edit_customer', $roles)) {
                           $balance_trans = ' <a href="#balanceTrans"  data-target="#balanceTrans" data-toggle="modal" class="btn btn-xs btn-primary mb-05 balanceTransBtn" data-name="'.$dataSet->NAME.'" data-balance_actual="'.$dataSet->CUSTOMER_BALANCE_ACTUAL.'" data-id="'.$dataSet->PK_NO.'" title="BALANCE TRANSFER"><i class="la la-exchange"></i></a>';
                       }
                       if (hasAccessAbility('new_booking', $roles)) {
                           $add_booking = ' <a href="'.route("admin.booking.create", ['id'=>$dataSet->PK_NO,'type'=>'customer']). '" class="btn btn-xs btn-primary mb-05 mr-05" title="ADD BOOKING"><i class="la la-plus"></i></a>';
                       }

                       if (hasAccessAbility('view_booking', $roles)) {
                           $view_booking = ' <a href="'.route("admin.booking.list", ['id' => $dataSet->PK_NO,'type'=>'customer']). '" class="btn btn-xs btn-success mb-05 mr-05" title="ALL BOOKING LIST">&nbsp;B&nbsp;</a>';
                       }
                       if (hasAccessAbility('view_order', $roles)) {
                           $view_order = ' <a href="'.route("admin.order.list", ['id' => $dataSet->PK_NO,'type'=>'customer']). '" class="btn btn-xs btn-info mb-05 mr-05" title="ALL ORDER LIST">&nbsp;O&nbsp;</a>';
                       }
                       if (hasAccessAbility('view_payment', $roles)) {
                           $view_payment = ' <a href="'.route("admin.payment.list", ['id' => $dataSet->PK_NO,'type'=>'customer']). '" class="btn btn-xs btn-success mb-05 mr-05" title="ALL PAYMENTS LIST">&nbsp;P&nbsp;</a>';
                       }
                       if (hasAccessAbility('view_payment', $roles)) {
                           $view_history = ' <a href="'.route("admin.customer.history", ['id' => $dataSet->PK_NO,'type'=>'customer']). '" class="btn btn-xs btn-success mb-05 mr-05" title="ALL HISTORY">&nbsp;H&nbsp;</a>';
                       }


                       return $edit.$view.$payment.$balance_trans.$add_booking.$view_booking.$view_order.$view_payment.$view_history;

                   })

                   ->addColumn('due', function($dataSet){

                       $due = '0';
                       $customer_due = DB::SELECT("SELECT
                       SLS_BOOKING.PK_NO as BOOKING_PK_NO,
                       SLS_BOOKING.F_CUSTOMER_NO AS CUSTOMER_PK_NO,
                       SUM(SLS_BOOKING.TOTAL_PRICE) AS TOTAL_PRICE,
                       IFNULL(SLS_ORDER.ORDER_BUFFER_TOPUP,0) AS ORDER_BUFFER_TOPUP,
                       SUM(IFNULL(SLS_BOOKING.TOTAL_PRICE,0) - (IFNULL(SLS_ORDER.ORDER_BUFFER_TOPUP,0))) AS DUE_PRICE

                       from SLS_BOOKING, SLS_ORDER
                       where SLS_BOOKING.F_CUSTOMER_NO = $dataSet->PK_NO
                       AND SLS_BOOKING.PK_NO = SLS_ORDER.F_BOOKING_NO");
                       if(!empty($customer_due)){
                           $due = '<span titel="Sum of unverified payments">'.number_format($customer_due[0]->DUE_PRICE,2).'</span>';
                       }
                       return $due;

                   })

                   ->addColumn('credit', function($dataSet){
                       $roles = userRolePermissionArray();
                       $credit = '';
                       if($dataSet->CUM_BALANCE > 0 ){
                           if (hasAccessAbility('new_payment', $roles)) {
                               $credit = ' <a href="'.route('admin.payment.create', [$dataSet->PK_NO, 'customer' ]).'?payfrom=credit" class="link" title="Customer actual credit balance (only verified)">'.number_format($dataSet->CUM_BALANCE,2).'</a>';
                           }
                       }else{
                           $credit = '<span>'.number_format($dataSet->CUM_BALANCE,2).'</span>';
                       }
                       return $credit;
                   })
                   ->addColumn('balance', function($dataSet){
                       $roles = userRolePermissionArray();
                       $balance = '';
                       if (hasAccessAbility('new_payment', $roles)) {
                           $buffer = $dataSet->CUSTOMER_BALANCE_BUFFER;
                           $actual = $dataSet->CUSTOMER_BALANCE_ACTUAL;
                           if($buffer == $actual){
                               $balance = '<span title="Actual balance & buffer balance is same (SUM)">'.number_format($dataSet->CUSTOMER_BALANCE_ACTUAL,2).'</span>';
                           }else{
                               $balance = '<span title="Actual balance (SUM)">'.number_format($dataSet->CUSTOMER_BALANCE_ACTUAL,2).'</span >/<span title="Buffer balance (SUM)">'. number_format($dataSet->CUSTOMER_BALANCE_BUFFER,2).'</span>';
                           }
                       }
                       return $balance;
                   })
                   ->addColumn('total_unverified', function($dataSet){
                       $roles = userRolePermissionArray();
                       $total_unverified = 0;
                       if (hasAccessAbility('new_payment', $roles)) {
                           $query = DB::table('ACC_CUSTOMER_PAYMENTS')
                           ->select(DB::raw("sum(ACC_CUSTOMER_PAYMENTS.MR_AMOUNT) as total_unverified"))
                           ->where('F_CUSTOMER_NO',$dataSet->PK_NO)
                           ->where('PAYMENT_CONFIRMED_STATUS',0)
                           ->groupBy('ACC_CUSTOMER_PAYMENTS.F_CUSTOMER_NO')
                           ->first();
                           if($query){
                               $total_unverified = $query->total_unverified;
                           }
                           $total_unverified = number_format($total_unverified,2);
                       }
                       return $total_unverified;
                   })
                   ->addColumn('customer_no', function($dataSet){

                       $customer_no = '';
                       $customer_no = '<a href="#" class="" title="Customer No">'.$dataSet->CUSTOMER_NO.'</a>';
                       return $customer_no;

                   })


                   ->rawColumns(['mobile','action','due', 'balance','customer_no','credit'])
                   ->make(true);
   }

   public function ajaxbankToOther()
   {
       $dataSet = DB::table("ACC_PAYMENT_BANK_ACC_EXFER as ex")
       ->select('ex.*','h.NARRATION as head_narration','acc.BANK_ACC_NAME','u.USERNAME')
       ->leftjoin('ACC_PAYMENT_ACC_HEAD as h', 'h.PK_NO','ex.F_ACC_PAYMENT_ACC_HEAD_NO')
       ->join('ACC_PAYMENT_BANK_ACC as acc', 'acc.PK_NO','ex.F_I_ACC_PAYMENT_BANK_ACC_NO')
       ->join('SA_USER as u', 'u.PK_NO','ex.SS_CREATED_BY');
       if (Auth::user()->F_AGENT_NO > 0) {
           $dataSet = $dataSet->where('acc.F_USER_NO',Auth::user()->PK_NO);
       }
       $dataSet = $dataSet->orderBy('ex.SS_CREATED_ON', 'DESC');
       return Datatables::of($dataSet)
       ->addColumn('is_in', function($dataSet){
           if ($dataSet->IS_IN == 0) {
               $is_in = 'Cash Out';
           }else{
               $is_in = 'Cash In';
           }
           return $is_in;
       })
       ->addColumn('status', function($dataSet){
           if ($dataSet->IS_VERIFIED == 0) {
               $status = '<div class="badge badge-danger round w-100 f-100">Not Verified</div>';
           }elseif($dataSet->IS_VERIFIED == 1){
               $status = '<div class="badge badge-success round w-100 f-100">Verified</div>';
           }else{
               $status = '<div class="badge border-info round w-100 f-100">Cancelled</div>';
           }
           return $status;
       })
       ->addColumn('action', function($dataSet){
               $action = '<a href="'.route("admin.account_to_other.view", [$dataSet->PK_NO]).'" class="btn btn-xs btn-info mb-05 mr-05" title="EDIT"><i class="la la-edit"></i></a> <a href="'.route("admin.account_to_other.details", [$dataSet->PK_NO]).'" class="btn btn-xs btn-primary mb-05 mr-05" title="VIEW"><i class="la la-eye"></i></a>';
           return $action;
       })
       ->rawColumns(['status','is_in','action'])
       ->make(true);
   }

   public function ajaxbankToBank()
   {
       $agent_pk = '0';
       if (Auth::user()->F_AGENT_NO > 0) {
           $agent_pk = PaymentBankAcc::select('PK_NO')->where('F_USER_NO',Auth::user()->PK_NO)->first();
           $agent_pk = $agent_pk->PK_NO;
       }
       $dataSet = DB::table("ACC_PAYMENT_BANK_ACC_IXFER as ix")
       ->select('ix.*','acc.BANK_ACC_NAME','acc.BANK_NAME','u.USERNAME'
       ,DB::raw('(select BANK_ACC_NAME from ACC_PAYMENT_BANK_ACC where PK_NO = F_TO_ACC_PAYMENT_BANK_ACC_NO) as to_bank_acc_name')
       ,DB::raw('(select BANK_NAME from ACC_PAYMENT_BANK_ACC where PK_NO = F_TO_ACC_PAYMENT_BANK_ACC_NO) as to_bank_name')
       ,DB::raw(''.$agent_pk.' as agent_pk')
       )
       ->join('ACC_PAYMENT_BANK_ACC as acc', 'acc.PK_NO','ix.F_FROM_ACC_PAYMENT_BANK_ACC_NO')
       ->join('SA_USER as u', 'u.PK_NO','ix.SS_CREATED_BY');
       if (Auth::user()->F_AGENT_NO > 0) {
           $dataSet = $dataSet->where('ix.F_FROM_ACC_PAYMENT_BANK_ACC_NO',$agent_pk);
           $dataSet = $dataSet->orWhere('ix.F_TO_ACC_PAYMENT_BANK_ACC_NO',$agent_pk);
       }
       $dataSet = $dataSet->orderBy('ix.SS_CREATED_ON', 'DESC');
       // ->get();
       // echo '<pre>';
       // echo '======================<br>';
       // print_r($dataSet1);
       // echo '<br>======================<br>';
       // exit();
       // $dataSet = DB::table("ACC_PAYMENT_BANK_ACC_IXFER as ix")
       // ->select('ix.*','acc.BANK_ACC_NAME as to_bank_acc_name','acc.BANK_NAME as to_bank_name','u.USERNAME'
       // ,DB::raw('(select BANK_ACC_NAME from ACC_PAYMENT_BANK_ACC where PK_NO = F_FROM_ACC_PAYMENT_BANK_ACC_NO) as BANK_ACC_NAME')
       // ,DB::raw('(select BANK_NAME from ACC_PAYMENT_BANK_ACC where PK_NO = F_FROM_ACC_PAYMENT_BANK_ACC_NO) as BANK_NAME')
       // )
       // ->join('ACC_PAYMENT_BANK_ACC as acc', 'acc.PK_NO','ix.F_TO_ACC_PAYMENT_BANK_ACC_NO')
       // ->join('SA_USER as u', 'u.PK_NO','ix.SS_CREATED_BY');
       // if (Auth::user()->F_AGENT_NO > 0) {
       //     $dataSet = $dataSet->where('acc.F_USER_NO',Auth::user()->PK_NO);
       // }
       // $dataSet = $dataSet->orderBy('ix.SS_CREATED_ON', 'DESC');

       // $dataSet = $dataSet->UNION($dataSet1)->get();
       // echo '<pre>';
       // echo '======================<br>';
       // print_r($dataSet);
       // echo '<br>======================<br>';
       // exit();
       return Datatables::of($dataSet)
       ->addColumn('status', function($dataSet){
           if ($dataSet->IS_VERIFIED == 0) {
               $status = '<div class="badge badge-danger round w-100 f-100">Not Verified</div>';
           }elseif($dataSet->IS_VERIFIED == 1){
               $status = '<div class="badge badge-success round w-100 f-100">Verified</div>';
           }else{
               $status = '<div class="badge border-info round w-100 f-100">Cancelled</div>';
           }
           return $status;
       })
       ->addColumn('action', function($dataSet){
           $action = '';
           if (Auth::user()->F_AGENT_NO > 0 && $dataSet->agent_pk == $dataSet->F_TO_ACC_PAYMENT_BANK_ACC_NO) {
               $action .= '';
           }else{
               $action .= '<a href="'.route("admin.account_to_bank.view", [$dataSet->PK_NO]).'" class="btn btn-xs btn-info mb-05 mr-05" title="EDIT"><i class="la la-edit"></i></a> ';
           }
               $action .= '<a href="'.route("admin.account_to_bank.details", [$dataSet->PK_NO]).'" class="btn btn-xs btn-primary mb-05 mr-05" title="VIEW"><i class="la la-eye"></i></a>';
           return $action;
       })
       ->rawColumns(['status','action'])
       ->make(true);
   }

   public function getDatatableReseller()
   {
       $dataSet = DB::table("SLS_RESELLERS as r")
       ->join('SS_COUNTRY as c','c.PK_NO','r.F_COUNTRY_NO')
       ->select('r.PK_NO','r.RESELLER_NO','r.NAME','r.EMAIL','r.MOBILE_NO','r.CUM_BALANCE_BUFFER','r.CUM_BALANCE_ACTUAL','r.CUM_BALANCE','c.DIAL_CODE')
       ->where('r.IS_ACTIVE', 1)
       ->orderBy('r.RESELLER_NO', 'ASC');
       return Datatables::of($dataSet)
                   ->addColumn('action', function($dataSet){
                       $roles = userRolePermissionArray();
                       $edit = '';
                       $view = '';
                       $delete = '';
                       $payment = '';
                       $balance_trans = '';
                       $add_booking = '';
                       $view_booking = '';
                       $view_order = '';
                       $view_payment = '';
                       if (hasAccessAbility('edit_reseller', $roles)) {
                           $edit = '<a href="'.route("admin.reseller.edit", [$dataSet->PK_NO]).'" class="btn btn-xs btn-info mb-05 mr-05" title="EDIT"><i class="la la-edit"></i></a>';
                       }
                       if (hasAccessAbility('view_reseller', $roles)) {
                           $view = ' <a href="'.route("admin.reseller.view", [$dataSet->PK_NO]). '" class="btn btn-xs btn-success mb-05 mr-05" title="VIEW"><i class="la la-eye"></i></a>';
                       }
                       if (hasAccessAbility('delete_reseller', $roles)) {
                           $delete = ' <a href="'.route('admin.reseller.delete', [$dataSet->PK_NO]).'" class="btn btn-xs btn-danger mb-05" onclick="return confirm('. "'" .'Are you sure you want to delete the reseller ?'. "'" .')" title="DELETE"><i class="la la-trash"></i></a>';
                       }
                       if (hasAccessAbility('new_payment', $roles)) {
                           $payment = ' <a href="'.route('admin.payment.create', [$dataSet->PK_NO, 'reseller' ]).'" class="btn btn-xs btn-primary mb-05 mr-05" title="Add new payment"><i class="la la-usd"></i></a>';
                       }
                       if (hasAccessAbility('edit_reseller', $roles)) {
                           $balance_trans = ' <a href="#balanceTrans"  data-target="#balanceTrans" data-toggle="modal" class="btn btn-xs btn-azura mb-05 balanceTransBtn" data-name="'.$dataSet->NAME.'" data-balance_actual="'.$dataSet->CUM_BALANCE_ACTUAL.'" data-id="'.$dataSet->PK_NO.'" title="BALANCE TRANSFER"><i class="la la-exchange"></i></a>';
                       }
                       if (hasAccessAbility('new_booking', $roles)) {
                           $add_booking = ' <a href="'.route("admin.booking.create", ['id'=>$dataSet->PK_NO,'type'=>'reseller']). '" class="btn btn-xs btn-primary mb-05 mr-05" title="ADD BOOKING"><i class="la la-plus"></i></a>';
                       }

                       if (hasAccessAbility('view_booking', $roles)) {
                           $view_booking = ' <a href="'.route("admin.booking.list", ['id' => $dataSet->PK_NO,'type'=>'reseller']). '" class="btn btn-xs btn-success mb-05 mr-05" title="ALL BOOKING LIST">&nbsp;B&nbsp;</a>';
                       }
                       if (hasAccessAbility('view_order', $roles)) {
                           $view_order = ' <a href="'.route("admin.order.list", ['id' => $dataSet->PK_NO,'type'=>'reseller']). '" class="btn btn-xs btn-success mb-05 mr-05" title="ALL ORDER LIST">&nbsp;O&nbsp;</a>';
                       }
                       if (hasAccessAbility('view_payment', $roles)) {
                           $view_payment = ' <a href="'.route("admin.payment.list", ['id' => $dataSet->PK_NO,'type'=>'reseller']). '" class="btn btn-xs btn-success mb-05 mr-05" title="ALL PAYMENTS LIST">&nbsp;P&nbsp;</a>';
                       }


                       return $edit.$view.$payment.$balance_trans.$add_booking.$view_booking.$view_order.$view_payment;
                   })

                   ->addColumn('due', function($dataSet){

                       $due = '0';
                       $reseller_due = DB::SELECT("SELECT
                       SLS_BOOKING.PK_NO as BOOKING_PK_NO,
                       SLS_BOOKING.F_RESELLER_NO AS RESELLER_PK_NO,
                       SUM(SLS_BOOKING.TOTAL_PRICE) AS TOTAL_PRICE,
                       IFNULL(SLS_ORDER.ORDER_BUFFER_TOPUP,0) AS ORDER_BUFFER_TOPUP,
                       SUM(IFNULL(SLS_BOOKING.TOTAL_PRICE,0) - (IFNULL(SLS_ORDER.ORDER_BUFFER_TOPUP,0))) AS DUE_PRICE

                       from SLS_BOOKING, SLS_ORDER
                       where SLS_BOOKING.F_RESELLER_NO = $dataSet->PK_NO
                       AND SLS_BOOKING.PK_NO = SLS_ORDER.F_BOOKING_NO");
                       if(!empty($reseller_due)){
                           $due = number_format($reseller_due[0]->DUE_PRICE,2);
                       }
                       return $due;

                   })
                   ->addColumn('credit', function($dataSet){
                       $roles = userRolePermissionArray();
                       $credit = '';
                       if($dataSet->CUM_BALANCE > 0 ){
                           if (hasAccessAbility('new_payment', $roles)) {
                               $credit = ' <a href="'.route('admin.payment.create', [$dataSet->PK_NO, 'reseller' ]).'?payfrom=credit" class="link" title="Reseller actual credit balance (only verified)">'.number_format($dataSet->CUM_BALANCE,2).'</a>';
                           }
                       }else{
                           $credit = '<span>'.number_format($dataSet->CUM_BALANCE,2).'</span>';
                       }
                       return $credit;
                   })

                   // ->addColumn('balance', function($dataSet){
                   //     $roles = userRolePermissionArray();
                   //     $balance = '';

                   //     if (hasAccessAbility('new_payment', $roles)) {

                   //         $buffer = $dataSet->CUM_BALANCE_BUFFER;
                   //         $actual = $dataSet->CUM_BALANCE_ACTUAL;
                   //         if($buffer == $actual){
                   //             $balance = number_format($dataSet->CUM_BALANCE_BUFFER,2);
                   //         }else{

                   //             $balance = $dataSet->CUM_BALANCE_ACTUAL .'/'. $dataSet->CUM_BALANCE_BUFFER;
                   //         }



                   //     }

                   //     return $balance;

                   // })
                   ->addColumn('balance', function($dataSet){
                       $roles = userRolePermissionArray();
                       $balance = '';
                       if (hasAccessAbility('new_payment', $roles)) {
                           $buffer = $dataSet->CUM_BALANCE_BUFFER;
                           $actual = $dataSet->CUM_BALANCE_ACTUAL;
                           if($buffer == $actual){
                               $balance = '<span title="Actual balance & buffer balance is same (SUM)">'.number_format($dataSet->CUM_BALANCE_ACTUAL,2).'</span>';
                           }else{
                               $balance = '<span title="Actual balance (SUM)">'.number_format($dataSet->CUM_BALANCE_ACTUAL,2).'</span >/<span title="Buffer balance (SUM)">'. number_format($dataSet->CUM_BALANCE_BUFFER,2).'</span>';
                           }
                       }
                       return $balance;
                   })



                   // ->addColumn('total_unverified', function($dataSet){
                   //     $roles = userRolePermissionArray();
                   //     $total_unverified = 0;

                   //     if (hasAccessAbility('new_payment', $roles)) {

                   //         $query = DB::table('ACC_RESELLER_PAYMENTS')
                   //         ->select(DB::raw("sum(ACC_RESELLER_PAYMENTS.MR_AMOUNT) as total_unverified"))
                   //         ->where('F_RESELLER_NO',$dataSet->PK_NO)
                   //         ->where('PAYMENT_CONFIRMED_STATUS',0)
                   //         ->groupBy('ACC_RESELLER_PAYMENTS.F_RESELLER_NO')
                   //         ->first();

                   //         if($query){
                   //             $total_unverified = $query->total_unverified;
                   //         }

                   //         $total_unverified = number_format($total_unverified,2);

                   //     }

                   //     return $total_unverified;

                   // })
                   ->addColumn('total_unverified', function($dataSet){
                       $roles = userRolePermissionArray();
                       $total_unverified = 0;
                       if (hasAccessAbility('new_payment', $roles)) {
                           $query = DB::table('ACC_RESELLER_PAYMENTS')
                           ->select(DB::raw("sum(ACC_RESELLER_PAYMENTS.MR_AMOUNT) as total_unverified"))
                           ->where('F_RESELLER_NO',$dataSet->PK_NO)
                           ->where('PAYMENT_CONFIRMED_STATUS',0)
                           ->groupBy('ACC_RESELLER_PAYMENTS.F_RESELLER_NO')
                           ->first();
                           if($query){
                               $total_unverified = $query->total_unverified;
                           }
                           $total_unverified = number_format($total_unverified,2);
                       }
                       return $total_unverified;
                   })
                   ->addColumn('reseller_no', function($dataSet){

                       $reseller_no = '';



                           $reseller_no = '<a href="#" class="" title="Customer No">'.$dataSet->RESELLER_NO.'</a>';



                       return $reseller_no;

                   })


                   ->rawColumns(['action','due', 'balance','reseller_no','credit'])
                   ->make(true);
   }

   public function getDatatableOrder($request)
   {
       $agent_id            = Auth::user()->F_AGENT_NO;
       $dispatch_type       = $request->dispatch ?? '0';

       $dataSet = DB::table("SLS_ORDER")
           ->select('SLS_ORDER.PK_NO','SLS_ORDER.F_BOOKING_NO','SLS_ORDER.F_CUSTOMER_NO','SLS_ORDER.F_RESELLER_NO','SLS_ORDER.CUSTOMER_NAME','SLS_ORDER.IS_READY','SLS_BOOKING.SS_CREATED_ON','SA_USER.USERNAME as CREATED_BY','SLS_BOOKING.BOOKING_SALES_AGENT_NAME','SLS_BOOKING.CONFIRM_TIME as ORDER_DATE','SLS_BOOKING.BOOKING_NO','SLS_BOOKING.RESELLER_NAME','SLS_BOOKING.TOTAL_PRICE','SLS_BOOKING.DISCOUNT','SLS_BOOKING.PK_NO as  SLS_BOOKING_PK_NO','SLS_BOOKING.IS_RESELLER','SLS_ORDER.ORDER_BUFFER_TOPUP','SLS_ORDER.ORDER_ACTUAL_TOPUP','SLS_ORDER.IS_SYSTEM_HOLD','SLS_ORDER.IS_ADMIN_HOLD','SLS_ORDER.DISPATCH_STATUS','SLS_ORDER.IS_CANCEL','SLS_BOOKING.CANCEL_REQUEST_BY','SLS_BOOKING.CANCEL_REQUEST_AT','SLS_ORDER.IS_SELF_PICKUP','SLS_ORDER.IS_ADMIN_APPROVAL','SLS_BOOKING.BOOKING_NOTES','SLS_BOOKING.IS_READ_BOOKING_NOTES','SLS_BOOKING.IS_BUNDLE_MATCHED','SLS_BOOKING.RECONFIRM_TIME','SLS_ORDER.IS_DEFAULT'
           ,DB::raw('(select "'.$dispatch_type.'" ) as dispatch_type'))
           ->leftJoin('SLS_BOOKING','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING.PK_NO')
           ->leftJoin('SA_USER','SLS_BOOKING.F_SS_CREATED_BY','SA_USER.PK_NO')
           ->whereRaw('SLS_ORDER.DISPATCH_STATUS < 40')
           ->where('DEFAULT_TYPE',0)
           ->where('SLS_ORDER.IS_CANCEL',0);

           if ($agent_id > 0) {
               $dataSet->where('SLS_BOOKING.F_BOOKING_SALES_AGENT_NO',$agent_id);
           }
           // if ($dispatch_type == 'rts' || $dispatch_type == 'cod_rtc') {
           //     $dataSet->whereIn('SLS_ORDER.DISPATCH_STATUS',[30,20]);
           // }

       // $order = DB::table('SLS_ORDER as o')
       //             ->join('SLS_BOOKING as b','o.F_BOOKING_NO','b.PK_NO')
       //             ->select('o.*','b.TOTAL_PRICE','b.BOOKING_SALES_AGENT_NAME');
       //     if ($agent_id > 0) {
       //         $order = $order->where('b.F_BOOKING_SALES_AGENT_NO',$agent_id);
       //     }
       if($request->id){
           if($request->type == 'customer'){
               $dataSet->where('SLS_ORDER.F_CUSTOMER_NO',$request->id);
           }elseif($request->type == 'reseller'){
               $dataSet->where('SLS_ORDER.F_RESELLER_NO',$request->id);
           }

       }
       if($request->dispatch){
           if($request->dispatch == 'rts'){
               $dataSet->whereIn('SLS_ORDER.DISPATCH_STATUS',[30,20])->where('SLS_ORDER.IS_SELF_PICKUP',0);
               $dataSet->where('SLS_ORDER.PICKUP_ID',0);
               $dataSet->where('SLS_ORDER.IS_ADMIN_HOLD',0);
           }
           if($request->dispatch == 'cod_rtc'){
               $dataSet->where('IS_READY','!=',0)->where('SLS_ORDER.IS_SELF_PICKUP',1)->where('SLS_ORDER.IS_ADMIN_HOLD',0);
           }
       }
       $dataSet->orderBy('SLS_ORDER.PK_NO','DESC');

       return Datatables::of($dataSet)

       ->addColumn('created_at', function($dataSet){

           $created_at = '<div class="font-11">'.date('d-m-y h:i A',strtotime($dataSet->SS_CREATED_ON)).'</div><div>'.$dataSet->CREATED_BY.'</div>';
           return $created_at;
       })
       ->addColumn('order_date', function($dataSet){
           if($dataSet->RECONFIRM_TIME){
           $order_date = '<div>'.date('d-m-y',strtotime($dataSet->RECONFIRM_TIME)).'</div>';
           }else{
               $order_date = '<div>'.date('d-m-y',strtotime($dataSet->SS_CREATED_ON)).'</div>';
           }
           return $order_date;
       })
       ->addColumn('order_id', function($dataSet){
           $order_id = '';
           $title = $dataSet->IS_BUNDLE_MATCHED == 1 ? 'The contains offer item' : '';
           $order_id .= '<a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->SLS_BOOKING_PK_NO]).'" title="'.$title.'">ORD-'.$dataSet->BOOKING_NO.'</a>';
           if($dataSet->IS_BUNDLE_MATCHED == 1){
               $order_id .= '<i class="la la-gift pull-right text-azura"><i>';
           }
           return $order_id;
       })
       ->addColumn('customer_name', function($dataSet){

           if($dataSet->IS_RESELLER == 1){
               $customer_name = '<a href="'.route("admin.reseller.edit", [$dataSet->F_RESELLER_NO]). '">'.$dataSet->RESELLER_NAME.'</a>';
           }else{
               $customer_name = '<a href="'.route('admin.customer.view', [$dataSet->F_CUSTOMER_NO]).'">'.$dataSet->CUSTOMER_NAME.'</a>';
           }
           return $customer_name;
       })
      ->addColumn('item_type', function($dataSet){
           $booking_no = $dataSet->F_BOOKING_NO;
           $item = 0;
           $item_type = '';

           $query = DB::SELECT("SELECT SLS_BOOKING_DETAILS.F_BOOKING_NO,INV_STOCK.F_PRD_VARIANT_NO, COUNT(*) AS ITEM_QTY  FROM SLS_BOOKING_DETAILS LEFT JOIN INV_STOCK ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO WHERE SLS_BOOKING_DETAILS.F_BOOKING_NO = $booking_no GROUP BY INV_STOCK.F_PRD_VARIANT_NO ");
           if(!empty($query)){
               foreach($query as $variant){
                   $item +=  $variant->ITEM_QTY;
               }
           }

           $item_type_qty = count($query) ?? 0;
           if($item_type_qty > 1){
               $item_type ='<div title="Total Quantity/Total Item">'.$item.'/'.$item_type_qty.'</div>';
           }else{
               $item_type ='<div >'.$item_type_qty.'</div>';
           }


           return $item_type;
       })
       ->addColumn('price_after_dis', function($dataSet){

           $price_after_dis = number_format($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT,2);

           return $price_after_dis;
       })
       ->addColumn('payment', function($dataSet){
           $payment = '';
           if($dataSet->ORDER_ACTUAL_TOPUP > 0 ){
               $payment .= '<div class="badge badge-success d-block" title="PAID AND VERIFIED">'.number_format($dataSet->ORDER_ACTUAL_TOPUP,2).'</div>';
           }

           if($dataSet->ORDER_BUFFER_TOPUP - $dataSet->ORDER_ACTUAL_TOPUP > 0 ){
               $payment .= '<div class="badge badge-warning d-block" title="PAID BUT NOT VERIFIED">'.number_format($dataSet->ORDER_BUFFER_TOPUP - $dataSet->ORDER_ACTUAL_TOPUP,2).'</div>';
           }
           if($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT  - $dataSet->ORDER_BUFFER_TOPUP > 0 ){
               $payment .= '<div class="badge badge-danger d-block" title="DUE" >'.number_format($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT -  $dataSet->ORDER_BUFFER_TOPUP,2).'</div>';
           }
           return $payment;
       })
       ->addColumn('avaiable', function($dataSet){
           $avaiable = '';
           $zones = '';
           $shelve_zones = DB::SELECT("SELECT GROUP_CONCAT(IFNULL(INV_STOCK.F_INV_ZONE_NO,0)) AS ZONES from SLS_BOOKING_DETAILS join INV_STOCK on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO where SLS_BOOKING_DETAILS.F_BOOKING_NO = $dataSet->F_BOOKING_NO GROUP BY SLS_BOOKING_DETAILS.F_BOOKING_NO ");

           if($dataSet->IS_READY == 0){
               $avaiable = '<div class="badge badge-primary d-block" title="NOT READY">Intransit</div>';
           }elseif($dataSet->IS_READY == 1){
               $avaiable = '<div class="badge badge-success d-block" title="READY">Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block " title="READY (Need to Shelve)">Ready</div>';
                   }
               }
           }elseif($dataSet->IS_READY == 2){
               $avaiable = '<div class="badge badge-warning d-block" title="PARTIALLY READY">Partially Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block  (Need to Shelve)" title="PARTIALLY READY">Partially</div>';
                   }
               }
           }
           return $avaiable;
       })
       ->addColumn('status', function($dataSet){

           $status = '';
           if ($dataSet->IS_ADMIN_APPROVAL == 1) {
               $status .= '<div class="badge badge-danger d-block" title="DATA IS ALTERED NEED ADMIN APPROVAL">ALTERED</div>';
           }else{
               if($dataSet->IS_CANCEL == 1){
                   $status .= '<div class="badge badge-warning d-block" title="Canceled">Canceled</div>';
               }elseif($dataSet->IS_CANCEL == 2){
                   $status .= '<div class="badge badge-warning d-block" title="Cancele Request Pending">CR</div>';
               }else{
                   if($dataSet->IS_ADMIN_HOLD == 0){

                       $assigned_user = DB::SELECT("SELECT RTS_COLLECTION_USER_ID FROM SLS_BOOKING_DETAILS WHERE F_BOOKING_NO = $dataSet->F_BOOKING_NO");
                       $assigned_user = count($assigned_user) ?? 0;
                       if ($dataSet->dispatch_type == 'rts' || $dataSet->dispatch_type == 'cod_rtc') {
                           $rts_link = '<a href="'.route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=rts">RTS</a>';
                       }else{
                           $rts_link = '<a href="javascript:void(0)">RTS</a>';
                       }
                       if($dataSet->DISPATCH_STATUS == '40'){
                           $status = '<div class="badge badge-primary d-block" title="DISPACTHED">Dispacthed</div>';
                       }elseif($dataSet->DISPATCH_STATUS == '30'){
                           $status = '<div class="badge badge-success d-block" title="READY TO SHIP">'.$rts_link.'</div>';
                       }elseif($dataSet->DISPATCH_STATUS == '20'){
                           $status = '<div class="badge badge-warning d-block" title="READY TO COLLECT (Partially)"><a href="'.route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=rts">RTS(H)</a></div>';
                       }elseif($dataSet->DISPATCH_STATUS == '10'){
                           $status = '<div class="badge badge-info d-block" title="DISPACTHED (Partially)">Dispacthed(H)</div>';
                       }


                       if($dataSet->IS_SELF_PICKUP == 1){

                           $due_amt = $dataSet->TOTAL_PRICE - $dataSet->DISCOUNT - $dataSet->ORDER_BUFFER_TOPUP;
                           if ($dataSet->dispatch_type == 'rts' || $dataSet->dispatch_type == 'cod_rtc') {
                               // $note_read = Booking::select('IS_READ_BOOKING_NOTES')->where('PK_NO',$dataSet->F_BOOKING_NO)->first();
                               $cod_link = route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=cod';

                           }else{
                               $cod_link = 'javascript:void(0)';
                           }
                           if($due_amt > 0 ){
                               if ($dataSet->IS_READ_BOOKING_NOTES == 0 && isset($dataSet->BOOKING_NOTES)) {
                                   $status = '<div style="position:relative;" class="badge badge-warning d-block" title="CASH ON DELIVERY"><a href="'.$cod_link.'" >COD<span class="badge badge-pill badge-up badge-danger badge-glow" style="position: absolute;top: -5px;right: 0px;bottom: 11px;">!</span></a></div>';
                               }else{
                                   $status = '<div class="badge badge-warning d-block" title="CASH ON DELIVERY"><a href="'.$cod_link.'" >COD</a></div>';
                               }
                           }else{
                               if ($dataSet->IS_READ_BOOKING_NOTES == 0 && isset($dataSet->BOOKING_NOTES)) {
                                   $status = '<div  style="position:relative;" class="badge badge-warning d-block" title="READY TO SELF PICKUP BY CUSTOMER"><a href="'.$cod_link.'">RTC<span class="badge badge-pill badge-up badge-danger badge-glow" style="position: absolute;top: -5px;right: 0px;bottom: 11px;">!</span></a></div>';
                               }else{
                                   $status = '<div class="badge badge-warning d-block" title="READY TO SELF PICKUP BY CUSTOMER"><a href="'.$cod_link.'">RTC</a></div>';
                               }
                           }

                       }

                   }else{

                       if($dataSet->IS_ADMIN_HOLD == 1){
                           $status = '<div class="badge badge-danger d-block" title="HOLD">HOLD</div>';
                       }

                   }


               }

           }

           if($status == ''){
               if($dataSet->IS_SYSTEM_HOLD == 1)
                   {
                       $status = '<div class="badge badge-default d-block" title="In Processing"><i class="la la-spinner spinner"></i></div>';
                   }
           }

           return $status;
       })
       ->addColumn('admin_hold', function($dataSet){
           $roles = userRolePermissionArray();
           $admin_hold = '';
           // $agent_id            = Auth::user()->F_AGENT_NO;
           if (hasAccessAbility('edit_order', $roles)) {
               if($dataSet->IS_ADMIN_HOLD == 0){
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold" data-booking_id="'.$dataSet->F_BOOKING_NO.'" /></label>';
               }elseif($dataSet->IS_ADMIN_HOLD == 1)
               {
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold"  data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked/></label>';
               }
           }else{
               if($dataSet->IS_ADMIN_HOLD == 0){
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold" data-booking_id="'.$dataSet->F_BOOKING_NO.'" disabled /></label>';
               }elseif($dataSet->IS_ADMIN_HOLD == 1)
               {
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold"  data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked disabled/></label>';
               }
           }


           // if (hasAccessAbility('view_order', $roles)) {
           //     if($dataSet->IS_ADMIN_HOLD == 0){
           //         $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold" data-booking_id="'.$dataSet->F_BOOKING_NO.'" disabled /></label>';
           //     }elseif($dataSet->IS_ADMIN_HOLD == 1)
           //     {
           //         $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold"  data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked disabled/></label>';
           //     }
           // }else{
           //     if($dataSet->IS_ADMIN_HOLD == 0){
           //         $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold" data-booking_id="'.$dataSet->F_BOOKING_NO.'" /></label>';
           //     }elseif($dataSet->IS_ADMIN_HOLD == 1)
           //     {
           //         $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold"  data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked/></label>';
           //     }
           // }

           return $admin_hold;
       })
       ->addColumn('self_pickup', function($dataSet){
           $roles = userRolePermissionArray();
           $self_pickup = '';
           // $agent_id            = Auth::user()->F_AGENT_NO;
           if($dataSet->IS_CANCEL == 0){
               if (hasAccessAbility('edit_order', $roles)) {

                   if($dataSet->IS_SELF_PICKUP == 0){
                       $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-self_pickup_booking="'.$dataSet->F_BOOKING_NO.'" data-toggle="modal" data-target="#self_pick_modal">SP</button>';

                       // $self_pickup = '<label title=""><input type="checkbox" class="is_self_pickup" data-booking_id="'.$dataSet->F_BOOKING_NO.'"/></label>';
                   }elseif($dataSet->IS_SELF_PICKUP == 1)
                   {
                       $name = '';
                       $rtc = OrderRtc::select('BANK_ACC_NAME','F_ACC_PAYMENT_BANK_NO','IS_REQUEST_PENDING')->where('F_BOOKING_NO',$dataSet->F_BOOKING_NO)->first();
                       // $self_pickup = '<label title=""><input type="checkbox" class="is_self_pickup" data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked/></label>';
                       $bank_acc_name = $rtc->BANK_ACC_NAME ?? '';
                       $bank_acc_no = $rtc->F_ACC_PAYMENT_BANK_NO ?? '';

                       if($rtc){
                           if($rtc->IS_REQUEST_PENDING == 1){
                               $btn_class = 'btn-warning';
                               $title = 'SELF PICKUP (PENDING FOR DISPATCH MANAGER APPROVAL)';
                               $name = 'COD/RTC';
                           }else{
                               $btn_class = 'btn-success';
                               $title = 'SELF PICKUP';
                               $name = $bank_acc_name;

                           }
                           if(($rtc->IS_CONFIRM_HOLDER == 0) && ($rtc->IS_REQUEST_PENDING == 0)){
                               $title = 'SELF PICKUP (PENDING FOR ORDER ITEM RECEIVED BY AGENT)';
                           }
                       }else{
                           $btn_class = "";
                           $title = "";
                       }

                       if ($dataSet->IS_READ_BOOKING_NOTES == 0 && isset($dataSet->BOOKING_NOTES) && isset($rtc->IS_REQUEST_PENDING) && $rtc->IS_REQUEST_PENDING == 0) {
                           $self_pickup = '<button type="button" title="'.$title.'" class="btn btn-xs '.$btn_class.' mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-rtc_no="'.$bank_acc_no.'" data-self_pickup_booking="'.$dataSet->F_BOOKING_NO.'" data-toggle="modal" data-target="" onclick="return confirm('. "'" .'Please read special note'. "'" .')">'.$name.'</button>';
                       }else{
                           $self_pickup = '<button type="button" title="'.$title.'" class="btn btn-xs '.$btn_class.' mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-rtc_no="'.$bank_acc_no.'" data-self_pickup_booking="'.$dataSet->F_BOOKING_NO.'" data-toggle="modal" data-target="#self_pick_modal">'.$name.'</button>';
                       }


                   }
               }else{
                   if($dataSet->IS_SELF_PICKUP == 0){
                       $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-toggle="modal" data-target="#self_pick_modal" disabled>SP</button>';
                   }elseif($dataSet->IS_SELF_PICKUP == 1)
                   {
                       $rtc = OrderRtc::select('BANK_ACC_NAME','F_ACC_PAYMENT_BANK_NO')->where('F_BOOKING_NO',$dataSet->F_BOOKING_NO)->first();
                       // $self_pickup = '<label title=""><input type="checkbox" class="is_self_pickup" data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked/></label>';
                       $bank_acc_name = $rtc->BANK_ACC_NAME ?? '';
                       $bank_acc_no = $rtc->F_ACC_PAYMENT_BANK_NO ?? '';
                       if ($dataSet->IS_READ_BOOKING_NOTES == 0 && isset($dataSet->BOOKING_NOTES)) {
                           $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-rtc_no="'.$bank_acc_no.'" data-toggle="modal" data-target="" disabled  onclick="return confirm('. "'" .'Please read special note'. "'" .')">'.$bank_acc_name.'</button>';
                       }else{
                           $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-rtc_no="'.$bank_acc_no.'" data-toggle="modal" data-target="#self_pick_modal" disabled>'.$bank_acc_name.'</button>';
                       }
                   }
               }

               // if (hasAccessAbility('edit_order', $roles)) {
               //     if($dataSet->IS_SELF_PICKUP == 0){
               //         $self_pickup = '<label title=""><input type="checkbox" class="is_self_pickup" data-booking_id="'.$dataSet->F_BOOKING_NO.'" disabled/></label>';
               //     }elseif($dataSet->IS_SELF_PICKUP == 1)
               //     {
               //         $self_pickup = '<label title=""><input type="checkbox" class="is_self_pickup" data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked disabled/></label>';
               //     }
               // }else{
               //     if($dataSet->IS_SELF_PICKUP == 0){
               //         $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-toggle="modal" data-target="#self_pick_modal">SP</button>';

               //         // $self_pickup = '<label title=""><input type="checkbox" class="is_self_pickup" data-booking_id="'.$dataSet->F_BOOKING_NO.'"/></label>';
               //     }elseif($dataSet->IS_SELF_PICKUP == 1)
               //     {
               //         $rtc = OrderRtc::select('BANK_ACC_NAME','F_ACC_PAYMENT_BANK_NO')->where('F_BOOKING_NO',$dataSet->F_BOOKING_NO)->first();
               //         // $self_pickup = '<label title=""><input type="checkbox" class="is_self_pickup" data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked/></label>';
               //         $bank_acc_name = $rtc->BANK_ACC_NAME ?? '';
               //         $bank_acc_no = $rtc->F_ACC_PAYMENT_BANK_NO ?? '';
               //         $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-rtc_no="'.$bank_acc_no.'" data-toggle="modal" data-target="#self_pick_modal">'.$bank_acc_name.'</button>';

               //     }
               // }

           }else{
               $self_pickup = '';
           }

           return $self_pickup;
       })



       ->addColumn('action', function($dataSet){
           $roles = userRolePermissionArray();
           $action = '';
           if (hasAccessAbility('view_order', $roles)) {
           $action .=' <a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->F_BOOKING_NO]).'" class="btn btn-xs btn-primary mb-05 mr-05" title="View order"><i class="la la-eye"></i></a>';
           }

           if (hasAccessAbility('edit_order', $roles)) {
           $action .=' <a href="'.route('admin.booking_to_order.book-order',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-info mb-05 mr-05" title="Edit"><i class="la la-edit"></i></a>';

               if(($dataSet->DISPATCH_STATUS == 20 || $dataSet->DISPATCH_STATUS == 30) && $dataSet->dispatch_type == 'rts'){
                   $action .= '<input type="checkbox" name="record_check" value='.$dataSet->PK_NO.' class="ml-1 record_check c-p">';
                   // $action .= $dataSet->dispatch_type;
               }
           }
           if($dataSet->IS_CANCEL == 0){
               if($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT - $dataSet->ORDER_BUFFER_TOPUP > 0 ){
                   if (hasAccessAbility('new_payment', $roles) && $dataSet->IS_ADMIN_APPROVAL == 0 && $dataSet->dispatch_type != 'cod_rtc') {
                       if($dataSet->F_CUSTOMER_NO){
                           $action .= ' <a href="'.route('admin.payment.create', [$dataSet->F_CUSTOMER_NO, 'customer' ]).'" class="btn btn-xs btn-primary mb-05 mr-05" title="Add new payment"><i class="la la-usd"></i></a>';
                       }
                       if($dataSet->F_RESELLER_NO){
                           $action .= ' <a href="'.route('admin.payment.create', [$dataSet->F_RESELLER_NO, 'reseller' ]).'" class="btn btn-xs btn-azura mb-05 mr-05" title="Add new payment"><i class="la la-usd"></i></a>';
                       }
                   }
               }
           }

           $auth_id = Auth::user()->PK_NO;
           $role_id = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                               ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                               ->select('F_ROLE_NO')->where('F_USER_NO',$auth_id)->first();
           if ($dataSet->IS_ADMIN_APPROVAL == 1 && $role_id->F_ROLE_NO == 1) {
               $action .= ' <a href="'.route('admin.booking_to_order.admin-approval',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-azura mb-05 mr-05" ><i class="ft-help-circle"></i></a>';

           }
           $price_after_dis = $dataSet->TOTAL_PRICE - $dataSet->DISCOUNT;
           $order_payment = $dataSet->ORDER_BUFFER_TOPUP;

           if ((hasAccessAbility('delete_order', $roles)) && ($price_after_dis <= 0 ) && ($order_payment <= 0 ) ) {
               $action .=' <a href="'.route('admin.order.delete',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-danger mb-05 mr-05" onclick="return confirm('. "'" .'Are you sure you want to delete the order ?'. "'" .')"  ><i class="la la-trash"></i></a>';
               }

           return $action;
       })
       ->addColumn('altered', function($dataSet){
           if ($dataSet->IS_ADMIN_APPROVAL == 1) {
               return 'Altered';
           }else{
               return '';
           }
       })

       ->rawColumns(['created_at','order_date','order_id','customer_name','item_type','price_after_dis','payment','avaiable','status','admin_hold','self_pickup','action','altered'])
       ->make(true);
   }


   public function getCancelOrder($request)
   {
       $cancel_type       = $request->type ?? 'canceled';
       $dataSet = DB::table("SLS_ORDER")
           ->select('SLS_ORDER.PK_NO','SLS_ORDER.F_BOOKING_NO','SLS_ORDER.F_CUSTOMER_NO','SLS_ORDER.F_RESELLER_NO','SLS_ORDER.CUSTOMER_NAME','SLS_ORDER.IS_READY','SLS_BOOKING.SS_CREATED_ON','SA_USER.USERNAME as CREATED_BY','SLS_BOOKING.BOOKING_SALES_AGENT_NAME','SLS_BOOKING.CONFIRM_TIME as ORDER_DATE','SLS_BOOKING.BOOKING_NO','SLS_BOOKING.RESELLER_NAME','SLS_BOOKING.TOTAL_PRICE','SLS_BOOKING.DISCOUNT','SLS_BOOKING.PK_NO as  SLS_BOOKING_PK_NO','SLS_BOOKING.IS_RESELLER','SLS_ORDER.ORDER_BUFFER_TOPUP','SLS_ORDER.ORDER_ACTUAL_TOPUP','SLS_ORDER.IS_SYSTEM_HOLD','SLS_ORDER.IS_ADMIN_HOLD','SLS_ORDER.DISPATCH_STATUS','SLS_ORDER.IS_CANCEL','SLS_BOOKING.CANCEL_REQUEST_BY','SLS_BOOKING.CANCEL_REQUEST_AT','SLS_ORDER.IS_SELF_PICKUP','SLS_ORDER.IS_ADMIN_APPROVAL','SLS_BOOKING.BOOKING_NOTES','SLS_BOOKING.IS_READ_BOOKING_NOTES','SLS_BOOKING.IS_BUNDLE_MATCHED','SLS_BOOKING.RECONFIRM_TIME',DB::raw('(select "'.$cancel_type.'" ) as cancel_type'))
           ->leftJoin('SLS_BOOKING','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING.PK_NO')
           ->leftJoin('SA_USER','SLS_BOOKING.F_SS_CREATED_BY','SA_USER.PK_NO')
           ->whereRaw('SLS_ORDER.DISPATCH_STATUS < 40')
           ->where('DEFAULT_TYPE',0);
       if($cancel_type == 'cancelrequest'){
           $dataSet->where('SLS_ORDER.IS_CANCEL',2);
       }else{
           $dataSet->where('SLS_ORDER.IS_CANCEL',1);
       }

       $dataSet->orderBy('SLS_ORDER.PK_NO','DESC');
       return Datatables::of($dataSet)

       ->addColumn('created_at', function($dataSet){
           $created_at = '<div class="font-11">'.date('d-m-y h:i A',strtotime($dataSet->SS_CREATED_ON)).'</div><div>'.$dataSet->CREATED_BY.'</div>';
           return $created_at;
       })
       ->addColumn('order_date', function($dataSet){
           if($dataSet->RECONFIRM_TIME){
           $order_date = '<div>'.date('d-m-y',strtotime($dataSet->RECONFIRM_TIME)).'</div>';
           }else{
               $order_date = '<div>'.date('d-m-y',strtotime($dataSet->SS_CREATED_ON)).'</div>';
           }
           return $order_date;
       })
       ->addColumn('order_id', function($dataSet){
           $order_id = '';
           $title = $dataSet->IS_BUNDLE_MATCHED == 1 ? 'The contains offer item' : '';
           $order_id .= '<a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->SLS_BOOKING_PK_NO]).'" title="'.$title.'">ORD-'.$dataSet->BOOKING_NO.'</a>';
           if($dataSet->IS_BUNDLE_MATCHED == 1){
               $order_id .= '<i class="la la-gift pull-right text-azura"><i>';
           }
           return $order_id;
       })
       ->addColumn('customer_name', function($dataSet){

           if($dataSet->IS_RESELLER == 1){
               $customer_name = '<a href="'.route("admin.reseller.edit", [$dataSet->F_RESELLER_NO]). '">'.$dataSet->RESELLER_NAME.'</a>';
           }else{
               $customer_name = '<a href="'.route('admin.customer.view', [$dataSet->F_CUSTOMER_NO]).'">'.$dataSet->CUSTOMER_NAME.'</a>';
           }
           return $customer_name;
       })
      ->addColumn('item_type', function($dataSet){
           $booking_no = $dataSet->F_BOOKING_NO;
           $item = 0;
           $item_type = '';
           if($dataSet->cancel_type == 'cancelrequest'){
               $query = DB::SELECT("SELECT SLS_BOOKING_DETAILS.F_BOOKING_NO,INV_STOCK.F_PRD_VARIANT_NO, COUNT(*) AS ITEM_QTY  FROM SLS_BOOKING_DETAILS LEFT JOIN INV_STOCK ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO WHERE SLS_BOOKING_DETAILS.F_BOOKING_NO = $booking_no GROUP BY INV_STOCK.F_PRD_VARIANT_NO ");
           }else{
               $query = DB::SELECT("SELECT SLS_BOOKING_DETAILS_AUD.F_BOOKING_NO,INV_STOCK.F_PRD_VARIANT_NO, COUNT(*) AS ITEM_QTY  FROM SLS_BOOKING_DETAILS_AUD LEFT JOIN INV_STOCK ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS_AUD.F_INV_STOCK_NO WHERE SLS_BOOKING_DETAILS_AUD.F_BOOKING_NO = $booking_no GROUP BY INV_STOCK.F_PRD_VARIANT_NO ");
           }


           if(!empty($query)){
               foreach($query as $variant){
                   $item +=  $variant->ITEM_QTY;
               }
           }

           $item_type_qty = count($query) ?? 0;
           if($item_type_qty > 1){
               $item_type ='<div title="Total Quantity/Total Item">'.$item.'/'.$item_type_qty.'</div>';
           }else{
               $item_type ='<div >'.$item_type_qty.'</div>';
           }
           return $item_type;
       })
       ->addColumn('price_after_dis', function($dataSet){
           if($dataSet->cancel_type == 'cancelrequest'){
               $order_val = $dataSet->TOTAL_PRICE;
           }else{
               $order_val = DB::table('SLS_BOOKING_DETAILS_AUD')->where('F_BOOKING_NO',$dataSet->F_BOOKING_NO)->where('SLS_BOOKING_DETAILS_AUD.CHANGE_TYPE','ORDER_CANCEL')->sum('LINE_PRICE');
           }
           return number_format($order_val-$dataSet->DISCOUNT,2);
       })
       ->addColumn('payment', function($dataSet){
           $payment = '';
           if($dataSet->ORDER_ACTUAL_TOPUP > 0 ){
               $payment .= '<div class="badge badge-success d-block" title="PAID AND VERIFIED">'.number_format($dataSet->ORDER_ACTUAL_TOPUP,2).'</div>';
           }

           if($dataSet->ORDER_BUFFER_TOPUP - $dataSet->ORDER_ACTUAL_TOPUP > 0 ){
               $payment .= '<div class="badge badge-warning d-block" title="PAID BUT NOT VERIFIED">'.number_format($dataSet->ORDER_BUFFER_TOPUP - $dataSet->ORDER_ACTUAL_TOPUP,2).'</div>';
           }
           if($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT  - $dataSet->ORDER_BUFFER_TOPUP > 0 ){
               $payment .= '<div class="badge badge-danger d-block" title="DUE" >'.number_format($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT -  $dataSet->ORDER_BUFFER_TOPUP,2).'</div>';
           }
           return $payment;
       })
       ->addColumn('avaiable', function($dataSet){
           $avaiable = '';
           $zones = '';
           $shelve_zones = DB::SELECT("SELECT GROUP_CONCAT(IFNULL(INV_STOCK.F_INV_ZONE_NO,0)) AS ZONES from SLS_BOOKING_DETAILS join INV_STOCK on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO where SLS_BOOKING_DETAILS.F_BOOKING_NO = $dataSet->F_BOOKING_NO GROUP BY SLS_BOOKING_DETAILS.F_BOOKING_NO ");

           if($dataSet->IS_READY == 0){
               $avaiable = '<div class="badge badge-primary d-block" title="NOT READY">Intransit</div>';
           }elseif($dataSet->IS_READY == 1){
               $avaiable = '<div class="badge badge-success d-block" title="READY">Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block " title="READY (Need to Shelve)">Ready</div>';
                   }
               }
           }elseif($dataSet->IS_READY == 2){
               $avaiable = '<div class="badge badge-warning d-block" title="PARTIALLY READY">Partially Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block  (Need to Shelve)" title="PARTIALLY READY">Partially</div>';
                   }
               }
           }
           return $avaiable;
       })
       ->addColumn('status', function($dataSet){
           if($dataSet->cancel_type == 'cancelrequest'){
               $status = 'Cancel Request';
           }else{
               $status = 'Canceled';
           }

           return $status;
       })
       ->addColumn('admin_hold', function($dataSet){
           $roles = userRolePermissionArray();
           $admin_hold = '';


           return $admin_hold;
       })
       ->addColumn('self_pickup', function($dataSet){
           $self_pickup = '';
           return $self_pickup;
       })



       ->addColumn('action', function($dataSet){
           $roles = userRolePermissionArray();
           $action = '';
           if (hasAccessAbility('view_order', $roles)) {
               $action .=' <a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->F_BOOKING_NO]).'" class="btn btn-xs btn-primary mb-05 mr-05" title="View order"><i class="la la-eye"></i></a>';
           }
           if($dataSet->cancel_type == 'cancelrequest'){
               if (hasAccessAbility('edit_order', $roles)) {
                   $action .=' <a href="'.route('admin.booking_to_order.book-order',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-info mb-05 mr-05" title="Edit"><i class="la la-edit"></i></a>';
               }
           }

           return $action;
       })
       ->addColumn('altered', function($dataSet){
           if ($dataSet->IS_ADMIN_APPROVAL == 1) {
               return 'Altered';
           }else{
               return '';
           }
       })

       ->rawColumns(['created_at','order_date','order_id','customer_name','item_type','price_after_dis','payment','avaiable','status','admin_hold','self_pickup','action','altered'])
       ->make(true);
   }


   public function getDatatableAlteredOrder($request)
   {
       $agent_id            = Auth::user()->F_AGENT_NO;
       $dispatch_type       = $request->dispatch ?? '0';
       $dataSet = DB::table("SLS_ORDER")
           ->select('SLS_ORDER.PK_NO','SLS_ORDER.F_BOOKING_NO','SLS_ORDER.F_CUSTOMER_NO','SLS_ORDER.F_RESELLER_NO','SLS_ORDER.CUSTOMER_NAME','SLS_ORDER.IS_READY','SLS_BOOKING.SS_CREATED_ON','SA_USER.USERNAME as CREATED_BY','SLS_BOOKING.BOOKING_SALES_AGENT_NAME','SLS_BOOKING.CONFIRM_TIME as ORDER_DATE','SLS_BOOKING.BOOKING_NO','SLS_BOOKING.RESELLER_NAME','SLS_BOOKING.TOTAL_PRICE','SLS_BOOKING.DISCOUNT','SLS_BOOKING.PK_NO as  SLS_BOOKING_PK_NO','SLS_BOOKING.IS_RESELLER','SLS_ORDER.ORDER_BUFFER_TOPUP','SLS_ORDER.ORDER_ACTUAL_TOPUP','SLS_ORDER.IS_SYSTEM_HOLD','SLS_ORDER.IS_ADMIN_HOLD','SLS_ORDER.DISPATCH_STATUS','SLS_ORDER.IS_CANCEL','SLS_BOOKING.CANCEL_REQUEST_BY','SLS_BOOKING.CANCEL_REQUEST_AT','SLS_ORDER.IS_SELF_PICKUP','SLS_ORDER.IS_ADMIN_APPROVAL','SLS_BOOKING.RECONFIRM_TIME','SLS_BOOKING.IS_BUNDLE_MATCHED',DB::raw('(select "'.$dispatch_type.'" ) as dispatch_type'))
           ->leftJoin('SLS_BOOKING','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING.PK_NO')
           ->leftJoin('SA_USER','SLS_BOOKING.F_SS_CREATED_BY','SA_USER.PK_NO')
          ->where('SLS_ORDER.DISPATCH_STATUS', '<', '40')
           ->where('SLS_ORDER.IS_ADMIN_APPROVAL', 1);

           if ($agent_id > 0) {
               $dataSet->where('SLS_BOOKING.F_BOOKING_SALES_AGENT_NO',$agent_id);
           }
       if($request->id){
           if($request->type == 'customer'){
               $dataSet->where('SLS_ORDER.F_CUSTOMER_NO',$request->id);
           }elseif($request->type == 'reseller'){
               $dataSet->where('SLS_ORDER.F_RESELLER_NO',$request->id);
           }

       }
       if($request->dispatch){
           if($request->dispatch == 'rts'){
               $dataSet->whereIn('SLS_ORDER.DISPATCH_STATUS',[30,20])->where('SLS_ORDER.IS_SELF_PICKUP',0);
               $dataSet->where('SLS_ORDER.PICKUP_ID',0);
           }
           if($request->dispatch == 'cod_rtc'){
               $dataSet->where('IS_READY','!=',0)->where('SLS_ORDER.IS_SELF_PICKUP',1);
           }
       }
       $dataSet->orderBy('SLS_ORDER.PK_NO','DESC');

       return Datatables::of($dataSet)

       ->addColumn('created_at', function($dataSet){
           $created_at = '<div class="font-11">'.date('d-m-y h:i A',strtotime($dataSet->SS_CREATED_ON)).'</div><div>'.$dataSet->CREATED_BY.'</div>';
           return $created_at;
       })
       ->addColumn('order_date', function($dataSet){
           if($dataSet->RECONFIRM_TIME){
           $order_date = '<div>'.date('d-m-y',strtotime($dataSet->RECONFIRM_TIME)).'</div>';
           }else{
               $order_date = '<div>'.date('d-m-y',strtotime($dataSet->SS_CREATED_ON)).'</div>';
           }
           return $order_date;
       })
       ->addColumn('order_id', function($dataSet){
           $order_id = '';
           $title = $dataSet->IS_BUNDLE_MATCHED == 1 ? 'The contains offer item' : '';
           $order_id .= '<a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->SLS_BOOKING_PK_NO]).'" title="'.$title.'">ORD-'.$dataSet->BOOKING_NO.'</a>';
           if($dataSet->IS_BUNDLE_MATCHED == 1){
               $order_id .= '<i class="la la-gift pull-right text-azura"><i>';
           }
           return $order_id;
       })
       ->addColumn('customer_name', function($dataSet){

           if($dataSet->IS_RESELLER == 1){
               $customer_name = '<a href="'.route("admin.reseller.edit", [$dataSet->F_RESELLER_NO]). '">'.$dataSet->RESELLER_NAME.'</a>';
           }else{
               $customer_name = '<a href="'.route('admin.customer.view', [$dataSet->F_CUSTOMER_NO]).'">'.$dataSet->CUSTOMER_NAME.'</a>';
           }
           return $customer_name;
       })
      ->addColumn('item_type', function($dataSet){
           $booking_no = $dataSet->F_BOOKING_NO;
           $item = 0;
           $item_type = '';

           $query = DB::SELECT("SELECT SLS_BOOKING_DETAILS.F_BOOKING_NO,INV_STOCK.F_PRD_VARIANT_NO, COUNT(*) AS ITEM_QTY  FROM SLS_BOOKING_DETAILS LEFT JOIN INV_STOCK ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO WHERE SLS_BOOKING_DETAILS.F_BOOKING_NO = $booking_no GROUP BY INV_STOCK.F_PRD_VARIANT_NO ");
           if(!empty($query)){
               foreach($query as $variant){
                   $item +=  $variant->ITEM_QTY;
               }
           }

           $item_type_qty = count($query) ?? 0;
           if($item_type_qty > 1){
               $item_type ='<div title="Total Quantity/Total Item">'.$item.'/'.$item_type_qty.'</div>';
           }else{
               $item_type ='<div >'.$item_type_qty.'</div>';
           }

           return $item_type;
       })
       ->addColumn('price_after_dis', function($dataSet){

           $price_after_dis = number_format($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT,2);

           return $price_after_dis;
       })
       ->addColumn('payment', function($dataSet){
           $payment = '';
           if($dataSet->ORDER_ACTUAL_TOPUP > 0 ){
               $payment .= '<div class="badge badge-success d-block" title="PAID AND VERIFIED">'.number_format($dataSet->ORDER_ACTUAL_TOPUP,2).'</div>';
           }

           if($dataSet->ORDER_BUFFER_TOPUP - $dataSet->ORDER_ACTUAL_TOPUP > 0 ){
               $payment .= '<div class="badge badge-warning d-block" title="PAID BUT NOT VERIFIED">'.number_format($dataSet->ORDER_BUFFER_TOPUP - $dataSet->ORDER_ACTUAL_TOPUP,2).'</div>';
           }
           if($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT  - $dataSet->ORDER_BUFFER_TOPUP > 0 ){
               $payment .= '<div class="badge badge-danger d-block" title="DUE" >'.number_format($dataSet->TOTAL_PRICE - $dataSet->ORDER_BUFFER_TOPUP,2).'</div>';
           }
           return $payment;
       })
       ->addColumn('avaiable', function($dataSet){
           $avaiable = '';
           $zones = '';
           $shelve_zones = DB::SELECT("SELECT GROUP_CONCAT(IFNULL(INV_STOCK.F_INV_ZONE_NO,0)) AS ZONES from SLS_BOOKING_DETAILS join INV_STOCK on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO where SLS_BOOKING_DETAILS.F_BOOKING_NO = $dataSet->F_BOOKING_NO GROUP BY SLS_BOOKING_DETAILS.F_BOOKING_NO ");

           if($dataSet->IS_READY == 0){
               $avaiable = '<div class="badge badge-primary d-block" title="NOT READY">Intransit</div>';
           }elseif($dataSet->IS_READY == 1){
               $avaiable = '<div class="badge badge-success d-block" title="READY">Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block " title="READY (Need to Shelve)">Ready</div>';
                   }
               }
           }elseif($dataSet->IS_READY == 2){
               $avaiable = '<div class="badge badge-warning d-block" title="PARTIALLY READY">Partially Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block  (Need to Shelve)" title="PARTIALLY READY">Partially</div>';
                   }
               }
           }
           return $avaiable;
       })
       ->addColumn('status', function($dataSet){

           $status = '';
           if($dataSet->IS_ADMIN_HOLD == 0){

               $assigned_user = DB::SELECT("SELECT RTS_COLLECTION_USER_ID FROM SLS_BOOKING_DETAILS WHERE F_BOOKING_NO = $dataSet->F_BOOKING_NO");
               $assigned_user = count($assigned_user) ?? 0;
               if ($dataSet->dispatch_type == 'rts' || $dataSet->dispatch_type == 'cod_rtc') {
                   $rts_link = '<a href="'.route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=rts">RTS</a>';
               }else{
                   $rts_link = '<a href="javascript:void(0)">RTS</a>';
               }
               if($dataSet->DISPATCH_STATUS == '40'){
                   $status = '<div class="badge badge-success d-block" title="DISPACTHED">Dispacthed</div>';
               }elseif($dataSet->DISPATCH_STATUS == '30'){
                   $status = '<div class="badge badge-success d-block" title="READY TO SHIP">'.$rts_link.'</div>';
               }elseif($dataSet->DISPATCH_STATUS == '20'){
                   $status = '<div class="badge badge-success d-block" title="READY TO COLLECT (Partially)"><a href="'.route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=rts">RTS(H)</a></div>';
               }elseif($dataSet->DISPATCH_STATUS == '10'){
                   $status = '<div class="badge badge-success d-block" title="DISPACTHED (Partially)">Dispacthed(H)</div>';
               }
           }else{

               if($dataSet->IS_ADMIN_HOLD == 1){
                   $status = '<div class="badge badge-warning d-block" title="HOLD">HOLD</div>';
               }
           }

           if($dataSet->IS_CANCEL == '1'){
               $status .= '<div class="badge badge-warning d-block" title="Canceled">Canceled</div>';
           }elseif($dataSet->IS_CANCEL == '2'){
               $status .= '<div class="badge badge-warning d-block" title="Cancele Request Pending">CR</div>';
           }

           if($dataSet->IS_SELF_PICKUP == 1){

               $due_amt = $dataSet->TOTAL_PRICE - $dataSet->DISCOUNT - $dataSet->ORDER_BUFFER_TOPUP;
               if ($dataSet->dispatch_type == 'rts' || $dataSet->dispatch_type == 'cod_rtc') {
                   $cod_link = route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=cod';
               }else{
                   $cod_link = 'javascript:void(0)';
               }
               if($due_amt > 0 ){
                   $status = '<div class="badge badge-warning d-block" title="CASH ON DELIVERY"><a href="'.$cod_link.'">COD</a></div>';
               }else{
                   $status = '<div class="badge badge-warning d-block" title="READY TO SELF PICKUP BY CUSTOMER"><a href="'.$cod_link.'">RTC</a></div>';
               }

           }
           if($status == ''){
               if($dataSet->IS_SYSTEM_HOLD == 1)
                   {
                       $status = '<div class="badge badge-default d-block" title="In Processing"><i class="la la-spinner spinner"></i></div>';
                   }
           }
           if ($dataSet->IS_ADMIN_APPROVAL == 1) {
               $status .= '<div class="badge badge-danger d-block" title="DATA IS ALTERED NEED ADMIN APPROVAL">ALTERED</div>';
           }
           return $status;
       })
       ->addColumn('admin_hold', function($dataSet){
           $roles = userRolePermissionArray();
           $admin_hold = '';
           if (hasAccessAbility('edit_order', $roles)) {
               if($dataSet->IS_ADMIN_HOLD == 0){
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold" data-booking_id="'.$dataSet->F_BOOKING_NO.'" /></label>';
               }elseif($dataSet->IS_ADMIN_HOLD == 1)
               {
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold"  data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked/></label>';
               }
           }else{
               if($dataSet->IS_ADMIN_HOLD == 0){
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold" data-booking_id="'.$dataSet->F_BOOKING_NO.'" disabled /></label>';
               }elseif($dataSet->IS_ADMIN_HOLD == 1)
               {
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold"  data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked disabled/></label>';
               }
           }
           return $admin_hold;
       })
       ->addColumn('self_pickup', function($dataSet){
           $roles = userRolePermissionArray();
           $self_pickup = '';

           if (hasAccessAbility('edit_order', $roles)) {
               if($dataSet->IS_SELF_PICKUP == 0){
                   $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-toggle="modal" data-target="#self_pick_modal">SP</button>';

               }elseif($dataSet->IS_SELF_PICKUP == 1)
               {
                   $rtc = OrderRtc::select('BANK_ACC_NAME','F_ACC_PAYMENT_BANK_NO','IS_REQUEST_PENDING')->where('F_BOOKING_NO',$dataSet->F_BOOKING_NO)->first();
                   $bank_acc_name = $rtc->BANK_ACC_NAME ?? '';
                   $bank_acc_no = $rtc->F_ACC_PAYMENT_BANK_NO ?? '';

                   if($rtc){
                       if($rtc->IS_REQUEST_PENDING == 1){
                           $btn_class = 'btn-warning';
                           $title = 'SELF PICKUP (PENDING FOR DISPATCH MANAGER APPROVAL)';
                       }else{
                           $btn_class = 'btn-success';
                           $title = 'SELF PICKUP';

                       }
                       if(($rtc->IS_CONFIRM_HOLDER == 0) && ($rtc->IS_REQUEST_PENDING == 0)){
                           $title = 'SELF PICKUP (PENDING FOR ORDER ITEM RECEIVED BY AGENT)';
                       }
                   }else{
                       $btn_class = "";
                       $title = "";
                   }

                   $self_pickup = '<button type="button" title="'.$title.'" class="btn btn-xs '.$btn_class.' mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-rtc_no="'.$bank_acc_no.'" data-toggle="modal" data-target="#self_pick_modal">'.$bank_acc_name.'</button>';

               }
           }else{
               if($dataSet->IS_SELF_PICKUP == 0){
                   $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-info mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-toggle="modal" data-target="#self_pick_modal" disabled>SP</button>';
               }elseif($dataSet->IS_SELF_PICKUP == 1)
               {
                   $rtc = OrderRtc::select('BANK_ACC_NAME','F_ACC_PAYMENT_BANK_NO')->where('F_BOOKING_NO',$dataSet->F_BOOKING_NO)->first();
                   $bank_acc_name = $rtc->BANK_ACC_NAME ?? '';
                   $bank_acc_no = $rtc->F_ACC_PAYMENT_BANK_NO ?? '';
                   $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-rtc_no="'.$bank_acc_no.'" data-toggle="modal" data-target="#self_pick_modal" disabled>'.$bank_acc_name.'</button>';
               }
           }

           return $self_pickup;
       })



       ->addColumn('action', function($dataSet){
           $roles = userRolePermissionArray();
           $action = '';
           if ($dataSet->IS_ADMIN_APPROVAL == 1) {
               $action .= ' <a href="'.route('admin.booking_to_order.admin-approval',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-warning mb-05 mr-05" ><i class="ft-help-circle"></i></a>';

           }
           return $action;
       })
       ->addColumn('altered', function($dataSet){
           if ($dataSet->IS_ADMIN_APPROVAL == 1) {
               return 'Altered';
           }else{
               return '';
           }
       })

       ->rawColumns(['created_at','order_date','order_id','customer_name','item_type','price_after_dis','payment','avaiable','status','admin_hold','self_pickup','action','altered'])
       ->make(true);
   }

   public function getDatatableDefaultOrder($request)
   {
       $agent_id       = Auth::user()->F_AGENT_NO;
       $dispatch_type  = $request->dispatch ?? '0';
       $now            = Carbon::now()->subDays(7)->toDateString();

       $dataSet = DB::table("SLS_ORDER")
           ->select('SLS_ORDER.PK_NO','SLS_ORDER.F_BOOKING_NO','SLS_ORDER.F_CUSTOMER_NO','SLS_ORDER.F_RESELLER_NO','SLS_ORDER.CUSTOMER_NAME','SLS_ORDER.IS_READY','SLS_BOOKING.SS_CREATED_ON','SA_USER.USERNAME as CREATED_BY','SLS_BOOKING.BOOKING_SALES_AGENT_NAME','SLS_BOOKING.CONFIRM_TIME as ORDER_DATE','SLS_BOOKING.BOOKING_NO','SLS_BOOKING.RESELLER_NAME','SLS_BOOKING.TOTAL_PRICE','SLS_BOOKING.DISCOUNT','SLS_BOOKING.PK_NO as  SLS_BOOKING_PK_NO','SLS_BOOKING.IS_RESELLER','SLS_ORDER.ORDER_BUFFER_TOPUP','SLS_ORDER.ORDER_ACTUAL_TOPUP','SLS_ORDER.IS_SYSTEM_HOLD','SLS_ORDER.IS_ADMIN_HOLD','SLS_ORDER.DISPATCH_STATUS','SLS_ORDER.IS_CANCEL','SLS_BOOKING.CANCEL_REQUEST_BY','SLS_BOOKING.CANCEL_REQUEST_AT','SLS_ORDER.IS_SELF_PICKUP','SLS_ORDER.IS_ADMIN_APPROVAL','SLS_BOOKING.RECONFIRM_TIME','SLS_BOOKING.IS_BUNDLE_MATCHED','DEFAULT_TYPE','SLS_NOTIFICATION.IS_SEND','SLS_NOTIFICATION.PK_NO as sms_pk'
           ,DB::raw('(select "'.$dispatch_type.'" ) as dispatch_type')
           ,DB::raw('(DEFAULT_AT + interval 7 day ) as DEFAULT_AT'))
           ->leftJoin('SLS_BOOKING','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING.PK_NO')
           ->leftJoin('SA_USER','SLS_BOOKING.F_SS_CREATED_BY','SA_USER.PK_NO')
           ->Join('SLS_NOTIFICATION','SLS_NOTIFICATION.F_BOOKING_NO','SLS_ORDER.F_BOOKING_NO')
           ->where('SLS_ORDER.DISPATCH_STATUS', '<', '40')
           // ->where('SLS_ORDER.IS_DEFAULT',0)
           ->whereNotNull('SLS_ORDER.DEFAULT_AT')
           ->whereNull('SLS_ORDER.GRACE_TIME')
           ->whereRaw('((SLS_NOTIFICATION.IS_SEND = 0 OR SLS_NOTIFICATION.SEND_AT > "'.$now.'") AND SLS_NOTIFICATION.TYPE = "Default")')
           ;
           if ($agent_id > 0) {
               $dataSet->where('SLS_BOOKING.F_BOOKING_SALES_AGENT_NO',$agent_id);
           }
       if($request->id){
           if($request->type == 'customer'){
               $dataSet->where('SLS_ORDER.F_CUSTOMER_NO',$request->id);
           }elseif($request->type == 'reseller'){
               $dataSet->where('SLS_ORDER.F_RESELLER_NO',$request->id);
           }

       }
       if($request->dispatch){
           if($request->dispatch == 'rts'){
               $dataSet->whereIn('SLS_ORDER.DISPATCH_STATUS',[30,20])->where('SLS_ORDER.IS_SELF_PICKUP',0);
               $dataSet->where('SLS_ORDER.PICKUP_ID',0);
           }
           if($request->dispatch == 'cod_rtc'){
               $dataSet->where('IS_READY','!=',0)->where('SLS_ORDER.IS_SELF_PICKUP',1);
           }
       }
       $dataSet->orderBy('SLS_ORDER.PK_NO','DESC');
       $dataSet->orderBy('SLS_ORDER.DEFAULT_AT','DESC')
               // ->groupBy('SLS_ORDER.F_BOOKING_NO')
               ;

       return Datatables::of($dataSet)

       ->addColumn('created_at', function($dataSet){
           $created_at = '<div class="font-11">'.date('d-m-y h:i A',strtotime($dataSet->SS_CREATED_ON)).'</div><div>'.$dataSet->CREATED_BY.'</div>';
           return $created_at;
       })
       ->addColumn('order_date', function($dataSet){
           if($dataSet->RECONFIRM_TIME){
           $order_date = '<div>'.date('d-m-y',strtotime($dataSet->RECONFIRM_TIME)).'</div>';
           }else{
               $order_date = '<div>'.date('d-m-y',strtotime($dataSet->SS_CREATED_ON)).'</div>';
           }
           return $order_date;
       })
       ->addColumn('order_id', function($dataSet){
           $order_id = '';
           $title = $dataSet->IS_BUNDLE_MATCHED == 1 ? 'The contains offer item' : '';
           $order_id .= '<a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->SLS_BOOKING_PK_NO]).'" title="'.$title.'">ORD-'.$dataSet->BOOKING_NO.'</a>';
           if($dataSet->IS_BUNDLE_MATCHED == 1){
               $order_id .= '<i class="la la-gift pull-right text-azura"><i>';
           }
           return $order_id;
       })
       ->addColumn('customer_name', function($dataSet){

           if($dataSet->IS_RESELLER == 1){
               $customer_name = '<a href="'.route("admin.reseller.edit", [$dataSet->F_RESELLER_NO]). '">'.$dataSet->RESELLER_NAME.'</a>';
           }else{
               $customer_name = '<a href="'.route('admin.customer.view', [$dataSet->F_CUSTOMER_NO]).'">'.$dataSet->CUSTOMER_NAME.'</a>';
           }
           return $customer_name;
       })
      ->addColumn('item_type', function($dataSet){
           $booking_no = $dataSet->F_BOOKING_NO;
           $item = 0;
           $item_type = '';

           $query = DB::SELECT("SELECT SLS_BOOKING_DETAILS.F_BOOKING_NO,INV_STOCK.F_PRD_VARIANT_NO, COUNT(*) AS ITEM_QTY  FROM SLS_BOOKING_DETAILS LEFT JOIN INV_STOCK ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO WHERE SLS_BOOKING_DETAILS.F_BOOKING_NO = $booking_no GROUP BY INV_STOCK.F_PRD_VARIANT_NO ");
           if(!empty($query)){
               foreach($query as $variant){
                   $item +=  $variant->ITEM_QTY;
               }
           }

           $item_type_qty = count($query) ?? 0;
           if($item_type_qty > 1){
               $item_type ='<div title="Total Quantity/Total Item">'.$item.'/'.$item_type_qty.'</div>';
           }else{
               $item_type ='<div >'.$item_type_qty.'</div>';
           }

           return $item_type;
       })
       ->addColumn('price_after_dis', function($dataSet){

           $price_after_dis = number_format($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT,2);

           return $price_after_dis;
       })
       ->addColumn('payment', function($dataSet){
           $payment = '';
           if($dataSet->ORDER_ACTUAL_TOPUP > 0 ){
               $payment .= '<div class="badge badge-success d-block" title="PAID AND VERIFIED">'.number_format($dataSet->ORDER_ACTUAL_TOPUP,2).'</div>';
           }

           if($dataSet->ORDER_BUFFER_TOPUP - $dataSet->ORDER_ACTUAL_TOPUP > 0 ){
               $payment .= '<div class="badge badge-warning d-block" title="PAID BUT NOT VERIFIED">'.number_format($dataSet->ORDER_BUFFER_TOPUP - $dataSet->ORDER_ACTUAL_TOPUP,2).'</div>';
           }
           if($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT  - $dataSet->ORDER_BUFFER_TOPUP > 0 ){
               $payment .= '<div class="badge badge-danger d-block" title="DUE" >'.number_format($dataSet->TOTAL_PRICE - $dataSet->ORDER_BUFFER_TOPUP,2).'</div>';
           }
           return $payment;
       })
       ->addColumn('avaiable', function($dataSet){
           $avaiable = '';
           $zones = '';
           $shelve_zones = DB::SELECT("SELECT GROUP_CONCAT(IFNULL(INV_STOCK.F_INV_ZONE_NO,0)) AS ZONES from SLS_BOOKING_DETAILS join INV_STOCK on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO where SLS_BOOKING_DETAILS.F_BOOKING_NO = $dataSet->F_BOOKING_NO GROUP BY SLS_BOOKING_DETAILS.F_BOOKING_NO ");

           if($dataSet->IS_READY == 0){
               $avaiable = '<div class="badge badge-primary d-block" title="NOT READY">Intransit</div>';
           }elseif($dataSet->IS_READY == 1){
               $avaiable = '<div class="badge badge-success d-block" title="READY">Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block " title="READY (Need to Shelve)">Ready</div>';
                   }
               }
           }elseif($dataSet->IS_READY == 2){
               $avaiable = '<div class="badge badge-warning d-block" title="PARTIALLY READY">Partially Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block  (Need to Shelve)" title="PARTIALLY READY">Partially</div>';
                   }
               }
           }
           return $avaiable;
       })
       ->addColumn('status', function($dataSet){

           $status = '';
           if($dataSet->IS_ADMIN_HOLD == 0){

               $assigned_user = DB::SELECT("SELECT RTS_COLLECTION_USER_ID FROM SLS_BOOKING_DETAILS WHERE F_BOOKING_NO = $dataSet->F_BOOKING_NO");
               $assigned_user = count($assigned_user) ?? 0;
               if ($dataSet->dispatch_type == 'rts' || $dataSet->dispatch_type == 'cod_rtc') {
                   $rts_link = '<a href="'.route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=rts">RTS</a>';
               }else{
                   $rts_link = '<a href="javascript:void(0)">RTS</a>';
               }
               if($dataSet->DISPATCH_STATUS == '40'){
                   $status = '<div class="badge badge-success d-block" title="DISPACTHED">Dispacthed</div>';
               }elseif($dataSet->DISPATCH_STATUS == '30'){
                   $status = '<div class="badge badge-success d-block" title="READY TO SHIP">'.$rts_link.'</div>';
               }elseif($dataSet->DISPATCH_STATUS == '20'){
                   $status = '<div class="badge badge-success d-block" title="READY TO COLLECT (Partially)"><a href="'.route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=rts">RTS(H)</a></div>';
               }elseif($dataSet->DISPATCH_STATUS == '10'){
                   $status = '<div class="badge badge-success d-block" title="DISPACTHED (Partially)">Dispacthed(H)</div>';
               }
           }else{

               if($dataSet->IS_ADMIN_HOLD == 1){
                   $status = '<div class="badge badge-warning d-block" title="HOLD">HOLD</div>';
               }
           }

           if($dataSet->IS_CANCEL == '1'){
               $status .= '<div class="badge badge-warning d-block" title="Canceled">Canceled</div>';
           }elseif($dataSet->IS_CANCEL == '2'){
               $status .= '<div class="badge badge-warning d-block" title="Cancele Request Pending">CR</div>';
           }

           if($dataSet->IS_SELF_PICKUP == 1){

               $due_amt = $dataSet->TOTAL_PRICE - $dataSet->DISCOUNT - $dataSet->ORDER_BUFFER_TOPUP;
               if ($dataSet->dispatch_type == 'rts' || $dataSet->dispatch_type == 'cod_rtc') {
                   $cod_link = route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=cod';
               }else{
                   $cod_link = 'javascript:void(0)';
               }
               if($due_amt > 0 ){
                   $status = '<div class="badge badge-warning d-block" title="CASH ON DELIVERY"><a href="'.$cod_link.'">COD</a></div>';
               }else{
                   $status = '<div class="badge badge-warning d-block" title="READY TO SELF PICKUP BY CUSTOMER"><a href="'.$cod_link.'">RTC</a></div>';
               }

           }
           if($status == ''){
               if($dataSet->IS_SYSTEM_HOLD == 1)
                   {
                       $status = '<div class="badge badge-default d-block" title="In Processing"><i class="la la-spinner spinner"></i></div>';
                   }
           }
           if ($dataSet->IS_ADMIN_APPROVAL == 1) {
               $status .= '<div class="badge badge-danger d-block" title="DATA IS ALTERED NEED ADMIN APPROVAL">ALTERED</div>';
           }
           return $status;
       })
       ->addColumn('admin_hold', function($dataSet){
           $roles = userRolePermissionArray();
           $admin_hold = '';
           // $agent_id            = Auth::user()->F_AGENT_NO;
           if (hasAccessAbility('edit_order', $roles)) {
               if($dataSet->IS_ADMIN_HOLD == 0){
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold" data-booking_id="'.$dataSet->F_BOOKING_NO.'" /></label>';
               }elseif($dataSet->IS_ADMIN_HOLD == 1)
               {
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold"  data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked/></label>';
               }
           }else{
               if($dataSet->IS_ADMIN_HOLD == 0){
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold" data-booking_id="'.$dataSet->F_BOOKING_NO.'" disabled /></label>';
               }elseif($dataSet->IS_ADMIN_HOLD == 1)
               {
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold"  data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked disabled/></label>';
               }
           }
           return $admin_hold;
       })
       ->addColumn('self_pickup', function($dataSet){
           $roles = userRolePermissionArray();
           $self_pickup = '';
           // $agent_id            = Auth::user()->F_AGENT_NO;

           if (hasAccessAbility('edit_order', $roles)) {
               if($dataSet->IS_SELF_PICKUP == 0){
                   $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-toggle="modal" data-target="#self_pick_modal">SP</button>';

                   // $self_pickup = '<label title=""><input type="checkbox" class="is_self_pickup" data-booking_id="'.$dataSet->F_BOOKING_NO.'"/></label>';
               }elseif($dataSet->IS_SELF_PICKUP == 1)
               {
                   $rtc = OrderRtc::select('BANK_ACC_NAME','F_ACC_PAYMENT_BANK_NO','IS_REQUEST_PENDING')->where('F_BOOKING_NO',$dataSet->F_BOOKING_NO)->first();
                   // $self_pickup = '<label title=""><input type="checkbox" class="is_self_pickup" data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked/></label>';
                   $bank_acc_name = $rtc->BANK_ACC_NAME ?? '';
                   $bank_acc_no = $rtc->F_ACC_PAYMENT_BANK_NO ?? '';

                   if($rtc){
                       if($rtc->IS_REQUEST_PENDING == 1){
                           $btn_class = 'btn-warning';
                           $title = 'SELF PICKUP (PENDING FOR DISPATCH MANAGER APPROVAL)';
                       }else{
                           $btn_class = 'btn-success';
                           $title = 'SELF PICKUP';

                       }
                       if(($rtc->IS_CONFIRM_HOLDER == 0) && ($rtc->IS_REQUEST_PENDING == 0)){
                           $title = 'SELF PICKUP (PENDING FOR ORDER ITEM RECEIVED BY AGENT)';
                       }
                   }else{
                       $btn_class = "";
                       $title = "";
                   }

                   $self_pickup = '<button type="button" title="'.$title.'" class="btn btn-xs '.$btn_class.' mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-rtc_no="'.$bank_acc_no.'" data-toggle="modal" data-target="#self_pick_modal">'.$bank_acc_name.'</button>';

               }
           }else{
               if($dataSet->IS_SELF_PICKUP == 0){
                   $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-info mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-toggle="modal" data-target="#self_pick_modal" disabled>SP</button>';
               }elseif($dataSet->IS_SELF_PICKUP == 1)
               {
                   $rtc = OrderRtc::select('BANK_ACC_NAME','F_ACC_PAYMENT_BANK_NO')->where('F_BOOKING_NO',$dataSet->F_BOOKING_NO)->first();
                   // $self_pickup = '<label title=""><input type="checkbox" class="is_self_pickup" data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked/></label>';
                   $bank_acc_name = $rtc->BANK_ACC_NAME ?? '';
                   $bank_acc_no = $rtc->F_ACC_PAYMENT_BANK_NO ?? '';
                   $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-rtc_no="'.$bank_acc_no.'" data-toggle="modal" data-target="#self_pick_modal" disabled>'.$bank_acc_name.'</button>';
               }
           }

           return $self_pickup;
       })

       ->addColumn('action', function($dataSet){
           $roles = userRolePermissionArray();
           $action = '';
           if (hasAccessAbility('view_order', $roles)) {
           $action .=' <a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->F_BOOKING_NO]).'" class="btn btn-xs btn-primary mb-05 mr-05" title="View order"><i class="la la-eye"></i></a>';
           }

           if (hasAccessAbility('edit_order', $roles)) {
           $action .=' <a href="'.route('admin.booking_to_order.book-order',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-info mb-05 mr-05" title="Edit"><i class="la la-edit"></i></a>';

           }
           $auth_id = Auth::user()->PK_NO;
           $role_id = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                               ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                               ->select('F_ROLE_NO')->where('F_USER_NO',$auth_id)->first();

           if ($dataSet->IS_ADMIN_APPROVAL == 1 && $role_id->F_ROLE_NO == 1) {
               $action .= ' <a href="'.route('admin.booking_to_order.admin-approval',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-azura mb-05 mr-05" ><i class="ft-help-circle"></i></a>';

           }
           $price_after_dis = $dataSet->TOTAL_PRICE - $dataSet->DISCOUNT;
           $order_payment = $dataSet->ORDER_BUFFER_TOPUP;

           if ((hasAccessAbility('delete_order', $roles)) && ($price_after_dis <= 0 ) && ($order_payment <= 0 ) ) {
               $action .=' <a href="'.route('admin.order.delete',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-danger mb-05 mr-05" onclick="return confirm('. "'" .'Are you sure you want to delete the order ?'. "'" .')"  ><i class="la la-trash"></i></a>';
           }
           $action .= '<a href="'.route('admin.order_revert.default',[$dataSet->F_BOOKING_NO]).'" onclick="return confirm('. "'" .'Are you sure you want to revert ?'. "'" .')" class="btn btn-xs btn-primary mb-05 mr-05" title="REVERT BACK"><i class="la la-exchange"></i></a>';

           if ($dataSet->IS_SEND == 0) {
               $action .= '<a href="'.route('admin.notify_sms.send', [$dataSet->sms_pk]).'" onclick="return confirm('. "'" .'Are you sure ?'. "'" .')" class="btn btn-xs btn-success" title="SEND SMS"><i class="la la-envelope"></i></a>';
           }elseif($dataSet->IS_SEND == 1){
               $action .= '<a href="#!" class="btn btn-xs btn-success" style="opacity: .5;" title="VIEW SMS" data-sms_pk="'.$dataSet->sms_pk.'"><i class="la la-envelope"></i></a>';
           }
           return $action;
       })
       ->addColumn('option_details', function($dataSet){
           if($dataSet->DEFAULT_TYPE == 1){
               $option = 'Air Option 1';
           }else if($dataSet->DEFAULT_TYPE == 2){
               $option = 'Air Option 2';
           }else if($dataSet->DEFAULT_TYPE == 3){
               $option = 'Sea Option 1';
           }else if($dataSet->DEFAULT_TYPE == 4){
               $option = 'Sea Option 2';
           }else if($dataSet->DEFAULT_TYPE == 5){
               $option = 'Ready Option 1';
           }else{
               $option = 'Ready Option 2';
           }
           return $option;
       })
       ->rawColumns(['created_at','order_date','order_id','customer_name','item_type','price_after_dis','payment','avaiable','status','admin_hold','self_pickup','action','option_details'])
       ->make(true);
   }

   public function getDatatableDefaultOrderAction($request)
   {
       $agent_id       = Auth::user()->F_AGENT_NO;
       $dispatch_type  = $request->dispatch ?? '0';
       $now            = Carbon::now()->subDays(7)->toDateString();
       $dataSet = DB::table("SLS_ORDER")
           ->select('SLS_ORDER.PK_NO','SLS_ORDER.F_BOOKING_NO','SLS_ORDER.F_CUSTOMER_NO','SLS_ORDER.F_RESELLER_NO','SLS_ORDER.CUSTOMER_NAME','SLS_ORDER.IS_READY','SLS_BOOKING.SS_CREATED_ON','SA_USER.USERNAME as CREATED_BY','SLS_BOOKING.BOOKING_SALES_AGENT_NAME','SLS_BOOKING.CONFIRM_TIME as ORDER_DATE','SLS_BOOKING.BOOKING_NO','SLS_BOOKING.RESELLER_NAME','SLS_BOOKING.TOTAL_PRICE','SLS_BOOKING.DISCOUNT','SLS_BOOKING.PK_NO as  SLS_BOOKING_PK_NO','SLS_BOOKING.IS_RESELLER','SLS_ORDER.ORDER_BUFFER_TOPUP','SLS_ORDER.ORDER_ACTUAL_TOPUP','SLS_ORDER.IS_SYSTEM_HOLD','SLS_ORDER.IS_ADMIN_HOLD','SLS_ORDER.DISPATCH_STATUS','SLS_ORDER.IS_CANCEL','SLS_BOOKING.CANCEL_REQUEST_BY','SLS_BOOKING.CANCEL_REQUEST_AT','SLS_ORDER.IS_SELF_PICKUP','SLS_ORDER.IS_ADMIN_APPROVAL','SLS_BOOKING.RECONFIRM_TIME','SLS_BOOKING.IS_BUNDLE_MATCHED','DEFAULT_TYPE','SLS_NOTIFICATION.IS_SEND','SLS_NOTIFICATION.PK_NO as sms_pk'
           ,DB::raw('(select "'.$dispatch_type.'" ) as dispatch_type')
           ,DB::raw('(DEFAULT_AT + interval 7 day ) as DEFAULT_AT'))
           ->leftJoin('SLS_BOOKING','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING.PK_NO')
           ->leftJoin('SA_USER','SLS_BOOKING.F_SS_CREATED_BY','SA_USER.PK_NO')
           ->Join('SLS_NOTIFICATION','SLS_NOTIFICATION.F_BOOKING_NO','SLS_ORDER.F_BOOKING_NO')
           ->where('SLS_ORDER.DISPATCH_STATUS', '<', '40')
           // ->where('SLS_ORDER.IS_DEFAULT', 0)
           ->whereNotNull('SLS_ORDER.DEFAULT_AT')
           ->whereNull('SLS_ORDER.GRACE_TIME')
           ->whereRaw('((SLS_NOTIFICATION.IS_SEND = 1 AND SLS_NOTIFICATION.SEND_AT < "'.$now.'") AND SLS_NOTIFICATION.TYPE = "Default")')
           ;
           if ($agent_id > 0) {
               $dataSet->where('SLS_BOOKING.F_BOOKING_SALES_AGENT_NO',$agent_id);
           }
       if($request->id){
           if($request->type == 'customer'){
               $dataSet->where('SLS_ORDER.F_CUSTOMER_NO',$request->id);
           }elseif($request->type == 'reseller'){
               $dataSet->where('SLS_ORDER.F_RESELLER_NO',$request->id);
           }

       }
       if($request->dispatch){
           if($request->dispatch == 'rts'){
               $dataSet->whereIn('SLS_ORDER.DISPATCH_STATUS',[30,20])->where('SLS_ORDER.IS_SELF_PICKUP',0);
               $dataSet->where('SLS_ORDER.PICKUP_ID',0);
           }
           if($request->dispatch == 'cod_rtc'){
               $dataSet->where('IS_READY','!=',0)->where('SLS_ORDER.IS_SELF_PICKUP',1);
           }
       }
       $dataSet->orderBy('SLS_ORDER.PK_NO','DESC');

       return Datatables::of($dataSet)

       ->addColumn('created_at', function($dataSet){
           $created_at = '<div class="font-11">'.date('d-m-y h:i A',strtotime($dataSet->SS_CREATED_ON)).'</div><div>'.$dataSet->CREATED_BY.'</div>';
           return $created_at;
       })
       ->addColumn('order_date', function($dataSet){
           if($dataSet->RECONFIRM_TIME){
           $order_date = '<div>'.date('d-m-y',strtotime($dataSet->RECONFIRM_TIME)).'</div>';
           }else{
               $order_date = '<div>'.date('d-m-y',strtotime($dataSet->SS_CREATED_ON)).'</div>';
           }
           return $order_date;
       })
       ->addColumn('order_id', function($dataSet){
           $order_id = '';
           $title = $dataSet->IS_BUNDLE_MATCHED == 1 ? 'The contains offer item' : '';
           $order_id .= '<a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->SLS_BOOKING_PK_NO]).'" title="'.$title.'">ORD-'.$dataSet->BOOKING_NO.'</a>';
           if($dataSet->IS_BUNDLE_MATCHED == 1){
               $order_id .= '<i class="la la-gift pull-right text-azura"><i>';
           }
           return $order_id;
       })
       ->addColumn('customer_name', function($dataSet){

           if($dataSet->IS_RESELLER == 1){
               $customer_name = '<a href="'.route("admin.reseller.edit", [$dataSet->F_RESELLER_NO]). '">'.$dataSet->RESELLER_NAME.'</a>';
           }else{
               $customer_name = '<a href="'.route('admin.customer.view', [$dataSet->F_CUSTOMER_NO]).'">'.$dataSet->CUSTOMER_NAME.'</a>';
           }
           return $customer_name;
       })
      ->addColumn('item_type', function($dataSet){
           $booking_no = $dataSet->F_BOOKING_NO;
           $item = 0;
           $item_type = '';

           $query = DB::SELECT("SELECT SLS_BOOKING_DETAILS.F_BOOKING_NO,INV_STOCK.F_PRD_VARIANT_NO, COUNT(*) AS ITEM_QTY  FROM SLS_BOOKING_DETAILS LEFT JOIN INV_STOCK ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO WHERE SLS_BOOKING_DETAILS.F_BOOKING_NO = $booking_no GROUP BY INV_STOCK.F_PRD_VARIANT_NO ");
           if(!empty($query)){
               foreach($query as $variant){
                   $item +=  $variant->ITEM_QTY;
               }
           }

           $item_type_qty = count($query) ?? 0;
           if($item_type_qty > 1){
               $item_type ='<div title="Total Quantity/Total Item">'.$item.'/'.$item_type_qty.'</div>';
           }else{
               $item_type ='<div >'.$item_type_qty.'</div>';
           }

           return $item_type;
       })
       ->addColumn('price_after_dis', function($dataSet){

           $price_after_dis = number_format($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT,2);

           return $price_after_dis;
       })
       ->addColumn('payment', function($dataSet){
           $payment = '';
           if($dataSet->ORDER_ACTUAL_TOPUP > 0 ){
               $payment .= '<div class="badge badge-success d-block" title="PAID AND VERIFIED">'.number_format($dataSet->ORDER_ACTUAL_TOPUP,2).'</div>';
           }

           if($dataSet->ORDER_BUFFER_TOPUP - $dataSet->ORDER_ACTUAL_TOPUP > 0 ){
               $payment .= '<div class="badge badge-warning d-block" title="PAID BUT NOT VERIFIED">'.number_format($dataSet->ORDER_BUFFER_TOPUP - $dataSet->ORDER_ACTUAL_TOPUP,2).'</div>';
           }
           if($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT  - $dataSet->ORDER_BUFFER_TOPUP > 0 ){
               $payment .= '<div class="badge badge-danger d-block" title="DUE" >'.number_format($dataSet->TOTAL_PRICE - $dataSet->ORDER_BUFFER_TOPUP,2).'</div>';
           }
           return $payment;
       })
       ->addColumn('avaiable', function($dataSet){
           $avaiable = '';
           $zones = '';
           $shelve_zones = DB::SELECT("SELECT GROUP_CONCAT(IFNULL(INV_STOCK.F_INV_ZONE_NO,0)) AS ZONES from SLS_BOOKING_DETAILS join INV_STOCK on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO where SLS_BOOKING_DETAILS.F_BOOKING_NO = $dataSet->F_BOOKING_NO GROUP BY SLS_BOOKING_DETAILS.F_BOOKING_NO ");

           if($dataSet->IS_READY == 0){
               $avaiable = '<div class="badge badge-primary d-block" title="NOT READY">Intransit</div>';
           }elseif($dataSet->IS_READY == 1){
               $avaiable = '<div class="badge badge-success d-block" title="READY">Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block " title="READY (Need to Shelve)">Ready</div>';
                   }
               }
           }elseif($dataSet->IS_READY == 2){
               $avaiable = '<div class="badge badge-warning d-block" title="PARTIALLY READY">Partially Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block  (Need to Shelve)" title="PARTIALLY READY">Partially</div>';
                   }
               }
           }
           return $avaiable;
       })
       ->addColumn('status', function($dataSet){

           $status = '';
           if($dataSet->IS_ADMIN_HOLD == 0){

               $assigned_user = DB::SELECT("SELECT RTS_COLLECTION_USER_ID FROM SLS_BOOKING_DETAILS WHERE F_BOOKING_NO = $dataSet->F_BOOKING_NO");
               $assigned_user = count($assigned_user) ?? 0;
               if ($dataSet->dispatch_type == 'rts' || $dataSet->dispatch_type == 'cod_rtc') {
                   $rts_link = '<a href="'.route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=rts">RTS</a>';
               }else{
                   $rts_link = '<a href="javascript:void(0)">RTS</a>';
               }
               if($dataSet->DISPATCH_STATUS == '40'){
                   $status = '<div class="badge badge-success d-block" title="DISPACTHED">Dispacthed</div>';
               }elseif($dataSet->DISPATCH_STATUS == '30'){
                   $status = '<div class="badge badge-success d-block" title="READY TO SHIP">'.$rts_link.'</div>';
               }elseif($dataSet->DISPATCH_STATUS == '20'){
                   $status = '<div class="badge badge-success d-block" title="READY TO COLLECT (Partially)"><a href="'.route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=rts">RTS(H)</a></div>';
               }elseif($dataSet->DISPATCH_STATUS == '10'){
                   $status = '<div class="badge badge-success d-block" title="DISPACTHED (Partially)">Dispacthed(H)</div>';
               }
           }else{

               if($dataSet->IS_ADMIN_HOLD == 1){
                   $status = '<div class="badge badge-warning d-block" title="HOLD">HOLD</div>';
               }
           }

           if($dataSet->IS_CANCEL == '1'){
               $status .= '<div class="badge badge-warning d-block" title="Canceled">Canceled</div>';
           }elseif($dataSet->IS_CANCEL == '2'){
               $status .= '<div class="badge badge-warning d-block" title="Cancele Request Pending">CR</div>';
           }

           if($dataSet->IS_SELF_PICKUP == 1){

               $due_amt = $dataSet->TOTAL_PRICE - $dataSet->DISCOUNT - $dataSet->ORDER_BUFFER_TOPUP;
               if ($dataSet->dispatch_type == 'rts' || $dataSet->dispatch_type == 'cod_rtc') {
                   $cod_link = route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=cod';
               }else{
                   $cod_link = 'javascript:void(0)';
               }
               if($due_amt > 0 ){
                   $status = '<div class="badge badge-warning d-block" title="CASH ON DELIVERY"><a href="'.$cod_link.'">COD</a></div>';
               }else{
                   $status = '<div class="badge badge-warning d-block" title="READY TO SELF PICKUP BY CUSTOMER"><a href="'.$cod_link.'">RTC</a></div>';
               }

           }
           if($status == ''){
               if($dataSet->IS_SYSTEM_HOLD == 1)
                   {
                       $status = '<div class="badge badge-default d-block" title="In Processing"><i class="la la-spinner spinner"></i></div>';
                   }
           }
           if ($dataSet->IS_ADMIN_APPROVAL == 1) {
               $status .= '<div class="badge badge-danger d-block" title="DATA IS ALTERED NEED ADMIN APPROVAL">ALTERED</div>';
           }
           return $status;
       })
       ->addColumn('admin_hold', function($dataSet){
           $roles = userRolePermissionArray();
           $admin_hold = '';
           if (hasAccessAbility('edit_order', $roles)) {
               if($dataSet->IS_ADMIN_HOLD == 0){
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold" data-booking_id="'.$dataSet->F_BOOKING_NO.'" /></label>';
               }elseif($dataSet->IS_ADMIN_HOLD == 1)
               {
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold"  data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked/></label>';
               }
           }else{
               if($dataSet->IS_ADMIN_HOLD == 0){
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold" data-booking_id="'.$dataSet->F_BOOKING_NO.'" disabled /></label>';
               }elseif($dataSet->IS_ADMIN_HOLD == 1)
               {
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold"  data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked disabled/></label>';
               }
           }
           return $admin_hold;
       })
       ->addColumn('self_pickup', function($dataSet){
           $roles = userRolePermissionArray();
           $self_pickup = '';

           if (hasAccessAbility('edit_order', $roles)) {
               if($dataSet->IS_SELF_PICKUP == 0){
                   $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-toggle="modal" data-target="#self_pick_modal">SP</button>';

               }elseif($dataSet->IS_SELF_PICKUP == 1)
               {
                   $rtc = OrderRtc::select('BANK_ACC_NAME','F_ACC_PAYMENT_BANK_NO','IS_REQUEST_PENDING')->where('F_BOOKING_NO',$dataSet->F_BOOKING_NO)->first();
                   $bank_acc_name = $rtc->BANK_ACC_NAME ?? '';
                   $bank_acc_no = $rtc->F_ACC_PAYMENT_BANK_NO ?? '';

                   if($rtc){
                       if($rtc->IS_REQUEST_PENDING == 1){
                           $btn_class = 'btn-warning';
                           $title = 'SELF PICKUP (PENDING FOR DISPATCH MANAGER APPROVAL)';
                       }else{
                           $btn_class = 'btn-success';
                           $title = 'SELF PICKUP';

                       }
                       if(($rtc->IS_CONFIRM_HOLDER == 0) && ($rtc->IS_REQUEST_PENDING == 0)){
                           $title = 'SELF PICKUP (PENDING FOR ORDER ITEM RECEIVED BY AGENT)';
                       }
                   }else{
                       $btn_class = "";
                       $title = "";
                   }

                   $self_pickup = '<button type="button" title="'.$title.'" class="btn btn-xs '.$btn_class.' mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-rtc_no="'.$bank_acc_no.'" data-toggle="modal" data-target="#self_pick_modal">'.$bank_acc_name.'</button>';

               }
           }else{
               if($dataSet->IS_SELF_PICKUP == 0){
                   $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-info mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-toggle="modal" data-target="#self_pick_modal" disabled>SP</button>';
               }elseif($dataSet->IS_SELF_PICKUP == 1)
               {
                   $rtc = OrderRtc::select('BANK_ACC_NAME','F_ACC_PAYMENT_BANK_NO')->where('F_BOOKING_NO',$dataSet->F_BOOKING_NO)->first();
                   $bank_acc_name = $rtc->BANK_ACC_NAME ?? '';
                   $bank_acc_no = $rtc->F_ACC_PAYMENT_BANK_NO ?? '';
                   $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-rtc_no="'.$bank_acc_no.'" data-toggle="modal" data-target="#self_pick_modal" disabled>'.$bank_acc_name.'</button>';
               }
           }

           return $self_pickup;
       })

       ->addColumn('action', function($dataSet){
           $roles = userRolePermissionArray();
           $action = '';
           if (hasAccessAbility('view_order', $roles)) {
           $action .=' <a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->F_BOOKING_NO]).'" class="btn btn-xs btn-primary mb-05 mr-05" title="View order"><i class="la la-eye"></i></a>';
           }

           if (hasAccessAbility('edit_order', $roles)) {
               $action .=' <a href="'.route('admin.booking_to_order.book-order',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-info mb-05 mr-05" title="Edit"><i class="la la-edit"></i></a>';
           }
           $auth_id = Auth::user()->PK_NO;
           $role_id = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                               ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                               ->select('F_ROLE_NO')->where('F_USER_NO',$auth_id)->first();

           if ($dataSet->IS_ADMIN_APPROVAL == 1 && $role_id->F_ROLE_NO == 1) {
               $action .= ' <a href="'.route('admin.booking_to_order.admin-approval',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-azura mb-05 mr-05" ><i class="ft-help-circle"></i></a>';
           }
           $price_after_dis = $dataSet->TOTAL_PRICE - $dataSet->DISCOUNT;
           $order_payment = $dataSet->ORDER_BUFFER_TOPUP;

           if ((hasAccessAbility('delete_order', $roles)) && ($price_after_dis <= 0 ) && ($order_payment <= 0 ) ) {
               $action .=' <a href="'.route('admin.order.delete',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-danger mb-05 mr-05" onclick="return confirm('. "'" .'Are you sure you want to delete the order ?'. "'" .')"  ><i class="la la-trash"></i></a>';
           }
           $action .= '<a href="'.route('admin.order_revert.default',[$dataSet->F_BOOKING_NO]).'" onclick="return confirm('. "'" .'Are you sure you want to revert ?'. "'" .')" class="btn btn-xs btn-primary mb-05" title="REVERT BACK"><i class="la la-exchange"></i></a>';

           if ($dataSet->IS_SEND == 0) {
               $action .= '<a href="'.route('admin.notify_sms.send', [$dataSet->sms_pk]).'" onclick="return confirm('. "'" .'Are you sure ?'. "'" .')" class="btn btn-xs btn-success" title="SEND SMS"><i class="la la-envelope"></i></a>';
           }elseif($dataSet->IS_SEND == 1){
               $action .= '<a href="#!" class="btn btn-xs btn-success" style="opacity: .5;" title="VIEW SMS" data-sms_pk="'.$dataSet->sms_pk.'"><i class="la la-envelope"></i></a>';
           }
           return $action;
       })
       ->addColumn('option_details', function($dataSet){
           if($dataSet->DEFAULT_TYPE == 1){
               $option = 'Air Option 1';
           }else if($dataSet->DEFAULT_TYPE == 2){
               $option = 'Air Option 2';
           }else if($dataSet->DEFAULT_TYPE == 3){
               $option = 'Sea Option 1';
           }else if($dataSet->DEFAULT_TYPE == 4){
               $option = 'Sea Option 2';
           }else if($dataSet->DEFAULT_TYPE == 5){
               $option = 'Ready Option 1';
           }else{
               $option = 'Ready Option 2';
           }
           return $option;
       })
       ->rawColumns(['created_at','order_date','order_id','customer_name','item_type','price_after_dis','payment','avaiable','status','admin_hold','self_pickup','action','option_details'])
       ->make(true);
   }

   public function getDatatableDefaultOrderPenalty($request)
   {
       $agent_id       = Auth::user()->F_AGENT_NO;
       $dispatch_type  = $request->dispatch ?? '0';
       // $now            = Carbon::now()->subDays(7)->toDateString();
       $dataSet = DB::table("SLS_ORDER")
           ->select('SLS_ORDER.PK_NO','SLS_ORDER.F_BOOKING_NO','SLS_ORDER.F_CUSTOMER_NO','SLS_ORDER.F_RESELLER_NO','SLS_ORDER.CUSTOMER_NAME','SLS_ORDER.IS_READY','SLS_BOOKING.SS_CREATED_ON','SA_USER.USERNAME as CREATED_BY','SLS_BOOKING.BOOKING_SALES_AGENT_NAME','SLS_BOOKING.CONFIRM_TIME as ORDER_DATE','SLS_BOOKING.BOOKING_NO','SLS_BOOKING.RESELLER_NAME','SLS_BOOKING.TOTAL_PRICE','SLS_BOOKING.DISCOUNT','SLS_BOOKING.PK_NO as  SLS_BOOKING_PK_NO','SLS_BOOKING.IS_RESELLER','SLS_ORDER.ORDER_BUFFER_TOPUP','SLS_ORDER.ORDER_ACTUAL_TOPUP','SLS_ORDER.IS_SYSTEM_HOLD','SLS_ORDER.IS_ADMIN_HOLD','SLS_ORDER.DISPATCH_STATUS','SLS_ORDER.IS_CANCEL','SLS_BOOKING.CANCEL_REQUEST_BY','SLS_BOOKING.CANCEL_REQUEST_AT','SLS_ORDER.IS_SELF_PICKUP','SLS_ORDER.IS_ADMIN_APPROVAL','SLS_BOOKING.RECONFIRM_TIME','SLS_BOOKING.IS_BUNDLE_MATCHED','DEFAULT_TYPE','PENALTY_FEE','GRACE_TIME','SLS_NOTIFICATION.IS_SEND','SLS_NOTIFICATION.PK_NO as sms_pk'
           ,DB::raw('(select "'.$dispatch_type.'" ) as dispatch_type'))
           ->leftJoin('SLS_BOOKING','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING.PK_NO')
           ->leftJoin('SA_USER','SLS_BOOKING.F_SS_CREATED_BY','SA_USER.PK_NO')
           ->Join('SLS_NOTIFICATION','SLS_NOTIFICATION.F_BOOKING_NO','SLS_ORDER.F_BOOKING_NO')
           ->where('SLS_ORDER.DISPATCH_STATUS', '<', '40')
           ->whereNotNull('SLS_ORDER.GRACE_TIME')
           ->whereRaw('(SLS_NOTIFICATION.TYPE = "Default")')
           ;
           if ($agent_id > 0) {
               $dataSet->where('SLS_BOOKING.F_BOOKING_SALES_AGENT_NO',$agent_id);
           }
       if($request->id){
           if($request->type == 'customer'){
               $dataSet->where('SLS_ORDER.F_CUSTOMER_NO',$request->id);
           }elseif($request->type == 'reseller'){
               $dataSet->where('SLS_ORDER.F_RESELLER_NO',$request->id);
           }
       }
       if($request->dispatch){
           if($request->dispatch == 'rts'){
               $dataSet->whereIn('SLS_ORDER.DISPATCH_STATUS',[30,20])->where('SLS_ORDER.IS_SELF_PICKUP',0);
               $dataSet->where('SLS_ORDER.PICKUP_ID',0);
           }
           if($request->dispatch == 'cod_rtc'){
               $dataSet->where('IS_READY','!=',0)->where('SLS_ORDER.IS_SELF_PICKUP',1);
           }
       }
       $dataSet->orderBy('SLS_ORDER.PK_NO','DESC');

       return Datatables::of($dataSet)

       ->addColumn('created_at', function($dataSet){
           $created_at = '<div class="font-11">'.date('d-m-y h:i A',strtotime($dataSet->SS_CREATED_ON)).'</div><div>'.$dataSet->CREATED_BY.'</div>';
           return $created_at;
       })
       ->addColumn('order_date', function($dataSet){
           if($dataSet->RECONFIRM_TIME){
           $order_date = '<div>'.date('d-m-y',strtotime($dataSet->RECONFIRM_TIME)).'</div>';
           }else{
               $order_date = '<div>'.date('d-m-y',strtotime($dataSet->SS_CREATED_ON)).'</div>';
           }
           return $order_date;
       })
       ->addColumn('order_id', function($dataSet){
           $order_id = '';
           $title = $dataSet->IS_BUNDLE_MATCHED == 1 ? 'The contains offer item' : '';
           $order_id .= '<a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->SLS_BOOKING_PK_NO]).'" title="'.$title.'">ORD-'.$dataSet->BOOKING_NO.'</a>';
           if($dataSet->IS_BUNDLE_MATCHED == 1){
               $order_id .= '<i class="la la-gift pull-right text-azura"><i>';
           }
           return $order_id;
       })
       ->addColumn('customer_name', function($dataSet){

           if($dataSet->IS_RESELLER == 1){
               $customer_name = '<a href="'.route("admin.reseller.edit", [$dataSet->F_RESELLER_NO]). '">'.$dataSet->RESELLER_NAME.'</a>';
           }else{
               $customer_name = '<a href="'.route('admin.customer.view', [$dataSet->F_CUSTOMER_NO]).'">'.$dataSet->CUSTOMER_NAME.'</a>';
           }
           return $customer_name;
       })
      ->addColumn('item_type', function($dataSet){
           $booking_no = $dataSet->F_BOOKING_NO;
           $item = 0;
           $item_type = '';

           $query = DB::SELECT("SELECT SLS_BOOKING_DETAILS.F_BOOKING_NO,INV_STOCK.F_PRD_VARIANT_NO, COUNT(*) AS ITEM_QTY  FROM SLS_BOOKING_DETAILS LEFT JOIN INV_STOCK ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO WHERE SLS_BOOKING_DETAILS.F_BOOKING_NO = $booking_no GROUP BY INV_STOCK.F_PRD_VARIANT_NO ");
           if(!empty($query)){
               foreach($query as $variant){
                   $item +=  $variant->ITEM_QTY;
               }
           }

           $item_type_qty = count($query) ?? 0;
           if($item_type_qty > 1){
               $item_type ='<div title="Total Quantity/Total Item">'.$item.'/'.$item_type_qty.'</div>';
           }else{
               $item_type ='<div >'.$item_type_qty.'</div>';
           }

           return $item_type;
       })
       ->addColumn('price_after_dis', function($dataSet){

           $price_after_dis = number_format($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT,2);

           return $price_after_dis;
       })
       ->addColumn('payment', function($dataSet){
           $payment = '';
           if($dataSet->ORDER_ACTUAL_TOPUP > 0 ){
               $payment .= '<div class="badge badge-success d-block" title="PAID AND VERIFIED">'.number_format($dataSet->ORDER_ACTUAL_TOPUP,2).'</div>';
           }

           if($dataSet->ORDER_BUFFER_TOPUP - $dataSet->ORDER_ACTUAL_TOPUP > 0 ){
               $payment .= '<div class="badge badge-warning d-block" title="PAID BUT NOT VERIFIED">'.number_format($dataSet->ORDER_BUFFER_TOPUP - $dataSet->ORDER_ACTUAL_TOPUP,2).'</div>';
           }
           if($dataSet->TOTAL_PRICE - $dataSet->DISCOUNT  - $dataSet->ORDER_BUFFER_TOPUP > 0 ){
               $payment .= '<div class="badge badge-danger d-block" title="DUE" >'.number_format($dataSet->TOTAL_PRICE - $dataSet->ORDER_BUFFER_TOPUP,2).'</div>';
           }
           return $payment;
       })
       ->addColumn('avaiable', function($dataSet){
           $avaiable = '';
           $zones = '';
           $shelve_zones = DB::SELECT("SELECT GROUP_CONCAT(IFNULL(INV_STOCK.F_INV_ZONE_NO,0)) AS ZONES from SLS_BOOKING_DETAILS join INV_STOCK on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO where SLS_BOOKING_DETAILS.F_BOOKING_NO = $dataSet->F_BOOKING_NO GROUP BY SLS_BOOKING_DETAILS.F_BOOKING_NO ");

           if($dataSet->IS_READY == 0){
               $avaiable = '<div class="badge badge-primary d-block" title="NOT READY">Intransit</div>';
           }elseif($dataSet->IS_READY == 1){
               $avaiable = '<div class="badge badge-success d-block" title="READY">Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block " title="READY (Need to Shelve)">Ready</div>';
                   }
               }
           }elseif($dataSet->IS_READY == 2){
               $avaiable = '<div class="badge badge-warning d-block" title="PARTIALLY READY">Partially Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block  (Need to Shelve)" title="PARTIALLY READY">Partially</div>';
                   }
               }
           }
           return $avaiable;
       })
       ->addColumn('status', function($dataSet){

           $status = '';
           if($dataSet->IS_ADMIN_HOLD == 0){

               $assigned_user = DB::SELECT("SELECT RTS_COLLECTION_USER_ID FROM SLS_BOOKING_DETAILS WHERE F_BOOKING_NO = $dataSet->F_BOOKING_NO");
               $assigned_user = count($assigned_user) ?? 0;
               if ($dataSet->dispatch_type == 'rts' || $dataSet->dispatch_type == 'cod_rtc') {
                   $rts_link = '<a href="'.route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=rts">RTS</a>';
               }else{
                   $rts_link = '<a href="javascript:void(0)">RTS</a>';
               }
               if($dataSet->DISPATCH_STATUS == '40'){
                   $status = '<div class="badge badge-success d-block" title="DISPACTHED">Dispacthed</div>';
               }elseif($dataSet->DISPATCH_STATUS == '30'){
                   $status = '<div class="badge badge-success d-block" title="READY TO SHIP">'.$rts_link.'</div>';
               }elseif($dataSet->DISPATCH_STATUS == '20'){
                   $status = '<div class="badge badge-success d-block" title="READY TO COLLECT (Partially)"><a href="'.route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=rts">RTS(H)</a></div>';
               }elseif($dataSet->DISPATCH_STATUS == '10'){
                   $status = '<div class="badge badge-success d-block" title="DISPACTHED (Partially)">Dispacthed(H)</div>';
               }
           }else{

               if($dataSet->IS_ADMIN_HOLD == 1){
                   $status = '<div class="badge badge-warning d-block" title="HOLD">HOLD</div>';
               }
           }

           if($dataSet->IS_CANCEL == '1'){
               $status .= '<div class="badge badge-warning d-block" title="Canceled">Canceled</div>';
           }elseif($dataSet->IS_CANCEL == '2'){
               $status .= '<div class="badge badge-warning d-block" title="Cancele Request Pending">CR</div>';
           }

           if($dataSet->IS_SELF_PICKUP == 1){

               $due_amt = $dataSet->TOTAL_PRICE - $dataSet->DISCOUNT - $dataSet->ORDER_BUFFER_TOPUP;
               if ($dataSet->dispatch_type == 'rts' || $dataSet->dispatch_type == 'cod_rtc') {
                   $cod_link = route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=cod';
               }else{
                   $cod_link = 'javascript:void(0)';
               }
               if($due_amt > 0 ){
                   $status = '<div class="badge badge-warning d-block" title="CASH ON DELIVERY"><a href="'.$cod_link.'">COD</a></div>';
               }else{
                   $status = '<div class="badge badge-warning d-block" title="READY TO SELF PICKUP BY CUSTOMER"><a href="'.$cod_link.'">RTC</a></div>';
               }

           }
           if($status == ''){
               if($dataSet->IS_SYSTEM_HOLD == 1)
                   {
                       $status = '<div class="badge badge-default d-block" title="In Processing"><i class="la la-spinner spinner"></i></div>';
                   }
           }
           if ($dataSet->IS_ADMIN_APPROVAL == 1) {
               $status .= '<div class="badge badge-danger d-block" title="DATA IS ALTERED NEED ADMIN APPROVAL">ALTERED</div>';
           }
           return $status;
       })
       ->addColumn('admin_hold', function($dataSet){
           $roles = userRolePermissionArray();
           $admin_hold = '';
           if (hasAccessAbility('edit_order', $roles)) {
               if($dataSet->IS_ADMIN_HOLD == 0){
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold" data-booking_id="'.$dataSet->F_BOOKING_NO.'" /></label>';
               }elseif($dataSet->IS_ADMIN_HOLD == 1)
               {
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold"  data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked/></label>';
               }
           }else{
               if($dataSet->IS_ADMIN_HOLD == 0){
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold" data-booking_id="'.$dataSet->F_BOOKING_NO.'" disabled /></label>';
               }elseif($dataSet->IS_ADMIN_HOLD == 1)
               {
                   $admin_hold = '<label title=""><input type="checkbox" class="is_admin_hold"  data-booking_id="'.$dataSet->F_BOOKING_NO.'" checked disabled/></label>';
               }
           }
           return $admin_hold;
       })
       ->addColumn('self_pickup', function($dataSet){
           $roles = userRolePermissionArray();
           $self_pickup = '';

           if (hasAccessAbility('edit_order', $roles)) {
               if($dataSet->IS_SELF_PICKUP == 0){
                   $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-toggle="modal" data-target="#self_pick_modal">SP</button>';

               }elseif($dataSet->IS_SELF_PICKUP == 1)
               {
                   $rtc = OrderRtc::select('BANK_ACC_NAME','F_ACC_PAYMENT_BANK_NO','IS_REQUEST_PENDING')->where('F_BOOKING_NO',$dataSet->F_BOOKING_NO)->first();
                   $bank_acc_name = $rtc->BANK_ACC_NAME ?? '';
                   $bank_acc_no = $rtc->F_ACC_PAYMENT_BANK_NO ?? '';

                   if($rtc){
                       if($rtc->IS_REQUEST_PENDING == 1){
                           $btn_class = 'btn-warning';
                           $title = 'SELF PICKUP (PENDING FOR DISPATCH MANAGER APPROVAL)';
                       }else{
                           $btn_class = 'btn-success';
                           $title = 'SELF PICKUP';

                       }
                       if(($rtc->IS_CONFIRM_HOLDER == 0) && ($rtc->IS_REQUEST_PENDING == 0)){
                           $title = 'SELF PICKUP (PENDING FOR ORDER ITEM RECEIVED BY AGENT)';
                       }
                   }else{
                       $btn_class = "";
                       $title = "";
                   }

                   $self_pickup = '<button type="button" title="'.$title.'" class="btn btn-xs '.$btn_class.' mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-rtc_no="'.$bank_acc_no.'" data-toggle="modal" data-target="#self_pick_modal">'.$bank_acc_name.'</button>';

               }
           }else{
               if($dataSet->IS_SELF_PICKUP == 0){
                   $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-info mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-toggle="modal" data-target="#self_pick_modal" disabled>SP</button>';
               }elseif($dataSet->IS_SELF_PICKUP == 1)
               {
                   $rtc = OrderRtc::select('BANK_ACC_NAME','F_ACC_PAYMENT_BANK_NO')->where('F_BOOKING_NO',$dataSet->F_BOOKING_NO)->first();
                   $bank_acc_name = $rtc->BANK_ACC_NAME ?? '';
                   $bank_acc_no = $rtc->F_ACC_PAYMENT_BANK_NO ?? '';
                   $self_pickup = '<button type="button" title="IS SELF PICKUP" class="btn btn-xs btn-success mb-05 mr-05 self_pick" data-booking_id="'.$dataSet->F_BOOKING_NO.'" data-rtc_no="'.$bank_acc_no.'" data-toggle="modal" data-target="#self_pick_modal" disabled>'.$bank_acc_name.'</button>';
               }
           }

           return $self_pickup;
       })

       ->addColumn('action', function($dataSet){
           $roles = userRolePermissionArray();
           $action = '';
           if (hasAccessAbility('view_order', $roles)) {
           $action .=' <a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->F_BOOKING_NO]).'" class="btn btn-xs btn-primary mb-05 mr-05" title="View order"><i class="la la-eye"></i></a>';
           }

           if (hasAccessAbility('edit_order', $roles)) {
               $action .=' <a href="'.route('admin.booking_to_order.book-order',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-info mb-05 mr-05" title="Edit"><i class="la la-edit"></i></a>';
           }
           $auth_id = Auth::user()->PK_NO;
           $role_id = AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                               ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                               ->select('F_ROLE_NO')->where('F_USER_NO',$auth_id)->first();

           if ($dataSet->IS_ADMIN_APPROVAL == 1 && $role_id->F_ROLE_NO == 1) {
               $action .= ' <a href="'.route('admin.booking_to_order.admin-approval',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-azura mb-05 mr-05" ><i class="ft-help-circle"></i></a>';
           }
           $price_after_dis = $dataSet->TOTAL_PRICE - $dataSet->DISCOUNT;
           $order_payment = $dataSet->ORDER_BUFFER_TOPUP;

           if ((hasAccessAbility('delete_order', $roles)) && ($price_after_dis <= 0 ) && ($order_payment <= 0 ) ) {
               $action .=' <a href="'.route('admin.order.delete',$dataSet->F_BOOKING_NO).'" class="btn btn-xs btn-danger mb-05 mr-05" onclick="return confirm('. "'" .'Are you sure you want to delete the order ?'. "'" .')"  ><i class="la la-trash"></i></a>';
               }

           if ($dataSet->IS_SEND == 0) {
               $action .= '<a href="'.route('admin.notify_sms.send', [$dataSet->sms_pk]).'" onclick="return confirm('. "'" .'Are you sure ?'. "'" .')" class="btn btn-xs btn-success" title="SEND SMS"><i class="la la-envelope"></i></a>';
           }elseif($dataSet->IS_SEND == 1){
               $action .= '<a href="#!" class="btn btn-xs btn-success" style="opacity: .5;" title="VIEW SMS" data-sms_pk="'.$dataSet->sms_pk.'"><i class="la la-envelope"></i></a>';
           }
           return $action;
       })
       ->addColumn('option_details', function($dataSet){
           if($dataSet->DEFAULT_TYPE == 1){
               $option = 'Air Option 1';
           }else if($dataSet->DEFAULT_TYPE == 2){
               $option = 'Air Option 2';
           }else if($dataSet->DEFAULT_TYPE == 3){
               $option = 'Sea Option 1';
           }else if($dataSet->DEFAULT_TYPE == 4){
               $option = 'Sea Option 2';
           }else if($dataSet->DEFAULT_TYPE == 5){
               $option = 'Ready Option 1';
           }else{
               $option = 'Ready Option 2';
           }
           return $option;
       })
       ->rawColumns(['created_at','order_date','order_id','customer_name','item_type','price_after_dis','payment','avaiable','status','admin_hold','self_pickup','action','option_details'])
       ->make(true);
   }

   public function getDatatableProduct($request)
   {



               $stock = DB::SELECT(" SELECT PK_NO, SKUID, IG_CODE, BARCODE, PRD_VARINAT_NAME, PRD_VARIANT_IMAGE_PATH, INV_WAREHOUSE_NAME, F_INV_WAREHOUSE_NO,F_SHIPPMENT_NO, F_BOX_NO, F_INV_ZONE_NO, PRODUCT_STATUS, BOOKING_STATUS, ORDER_STATUS FROM INV_STOCK WHERE PRODUCT_STATUS IS NULL OR PRODUCT_STATUS < 420");

               $dataSet = DB::SELECT("SELECT PK_NO, SKUID, IG_CODE, BARCODE, PRD_VARINAT_NAME, PRD_VARIANT_IMAGE_PATH, INV_WAREHOUSE_NAME, F_INV_WAREHOUSE_NO AS WAREHOUSE_NO
               FROM INV_STOCK
               WHERE PRODUCT_STATUS IS NULL OR PRODUCT_STATUS < 420
               GROUP BY SKUID, F_INV_WAREHOUSE_NO ORDER BY PK_NO DESC");

               if(!empty($dataSet) && count($dataSet)> 0){
                   foreach ($dataSet as $k => $value1) {
                       $boxed_qty              = 0;
                       $not_shelved_qty        = 0;
                       $yet_to_boxed_qty       = 0;
                       $shelved_qty            = 0;
                       $shipment_assigned_qty  = 0;
                       $ordered                = 0;
                       $dispatched             = 0;
                       $available              = 0;
                       if(!empty($stock)){
                           foreach ($stock as $l => $value2) {
                               if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->BOOKING_STATUS >= 10) && ($value2->BOOKING_STATUS <= 80) && ($value2->ORDER_STATUS < 80 OR $value2->ORDER_STATUS == null)){
                                   $ordered += 1;
                               }

                               if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->ORDER_STATUS < 80 OR $value2->ORDER_STATUS == null)){
                                   $available += 1;
                               }

                               if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_SHIPPMENT_NO == null) && ($value2->F_BOX_NO != null) && ($value2->ORDER_STATUS < 80 OR $value2->ORDER_STATUS == null)){
                                   $boxed_qty += 1;
                               }

                               if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_BOX_NO == null || $value2->F_BOX_NO == 0) && ($value2->PRODUCT_STATUS == null || $value2->PRODUCT_STATUS == 0 || $value2->PRODUCT_STATUS == 90 ) && ($value2->ORDER_STATUS < 80 OR $value2->ORDER_STATUS == null)){
                                   $yet_to_boxed_qty += 1;
                               }

                               if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_SHIPPMENT_NO != null) && ($value2->F_BOX_NO != null) && ($value2->F_INV_ZONE_NO == null) && ($value2->ORDER_STATUS < 80 OR $value2->ORDER_STATUS == null) && ($value2->PRODUCT_STATUS < 60)){
                                   $shipment_assigned_qty += 1;
                               }

                               if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_INV_ZONE_NO != null) && ($value2->ORDER_STATUS < 80 OR $value2->ORDER_STATUS == null)){
                                   $shelved_qty += 1;
                               }

                               if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->F_INV_ZONE_NO == null) && ($value2->ORDER_STATUS < 80 OR $value2->ORDER_STATUS == null) && ($value2->PRODUCT_STATUS >= 60)){
                                   $not_shelved_qty += 1;
                               }

                               if( ($value2->IG_CODE == $value1->IG_CODE) && ($value2->F_INV_WAREHOUSE_NO == $value1->WAREHOUSE_NO ) && ($value2->ORDER_STATUS >= 80)){
                                   $dispatched += 1;
                               }
                           }
                       }
                       $value1->BOXED_QTY              = $boxed_qty ;
                       $value1->NOT_SHELVED_QTY        = $not_shelved_qty ;
                       $value1->YET_TO_BOXED_QTY       = $yet_to_boxed_qty ;
                       $value1->SHELVED_QTY            = $shelved_qty ;
                       $value1->SHIPMENT_ASSIGNED_QTY  = $shipment_assigned_qty ;
                       $value1->ORDERED                = $ordered ;
                       $value1->DISPATCHED             = $dispatched ;
                       $value1->COUNTER                = $available ;
                   }
               }


               return Datatables::of($dataSet)
               ->addColumn('action', function($dataSet){
                   $roles = userRolePermissionArray();
                   $view = '';
                   if (hasAccessAbility('view_warehouse_stock_view', $roles)) {
                   $view = '<a href="'.route("admin.stock_price.view", [$dataSet->PK_NO]).'" class="btn btn-xs btn-success mb-05 mr-05" title="View Product"><i class="la la-eye"></i></a>';
                   }
                   return $view;
               })
               ->rawColumns(['action'])
               ->make(true);
   }

   public function getDatatableUnshelved($request)
   {
       $house = $request->get('columns')[4]['search']['value'];

       $count_not_shelved = Stock::selectRaw('(SELECT IFNULL(COUNT(IG_CODE),0) from INV_STOCK where IG_CODE = ig_code_ and F_INV_WAREHOUSE_NO = warehouse_no and (F_INV_ZONE_NO IS NULL and PRODUCT_STATUS = 60 ))')->limit(1)->getQuery();

       $dataSet = DB::table('INV_STOCK')
               ->select('PK_NO','SKUID','PRD_VARINAT_NAME','BARCODE','PRD_VARIANT_IMAGE_PATH','INV_WAREHOUSE_NAME','IG_CODE as ig_code_','F_INV_WAREHOUSE_NO as warehouse_no')
               ->selectSub($count_not_shelved, 'count')
               ->whereNull('F_INV_ZONE_NO')
               ->whereNotNull('F_BOX_NO')
               ->whereNotNull('F_SHIPPMENT_NO')
               // ->where('PRODUCT_STATUS', '>=', 60)
               ->groupBy('F_INV_WAREHOUSE_NO','SKUID')
               ->orderBy('PK_NO', 'DESC');

               return Datatables::of($dataSet)
               ->addColumn('action', function($dataSet){
                   return '<a href="'.route("admin.unshelved.view", [$dataSet->PK_NO]).'" class="btn btn-xs btn-success mb-05 mr-05" title="View Product"><i class="la la-eye"></i></a>';
               })
               ->rawColumns(['action'])
               ->make(true);
   }

   public function getDatatableBoxed($request)
   {
       $dataSet = DB::table('SC_BOX')
               ->join('INV_WAREHOUSE', 'INV_WAREHOUSE.PK_NO', 'SC_BOX.F_INV_WAREHOUSE_NO')
               ->leftjoin('SC_SHIPMENT_BOX', 'SC_SHIPMENT_BOX.F_BOX_NO', 'SC_BOX.PK_NO')
               ->leftjoin('SC_SHIPMENT', 'SC_SHIPMENT.PK_NO', 'SC_SHIPMENT_BOX.F_SHIPMENT_NO')
               ->select('SC_BOX.PK_NO','SC_BOX.BOX_NO','SC_BOX.USER_NAME','SC_BOX.ITEM_COUNT', 'INV_WAREHOUSE.NAME','SC_SHIPMENT.CODE','SC_BOX.BOX_STATUS','SC_BOX.WIDTH_CM','SC_BOX.LENGTH_CM','SC_BOX.HEIGHT_CM','SC_BOX.WEIGHT_KG'
               ,DB::raw('(CASE WHEN SC_SHIPMENT_BOX.BOX_SERIAL IS NULL THEN "Box not assigned" ELSE SC_SHIPMENT_BOX.BOX_SERIAL END) AS BOX_SERIAL'))
               ->orderBy('SC_BOX.BOX_NO', 'DESC');
               return Datatables::of($dataSet)
               ->addColumn('action', function($dataSet){
                   $roles = userRolePermissionArray();
                   $view = '';
                   $edit = '';
                   if (hasAccessAbility('view_box', $roles)) {
                       $view = '<a href="'.route("admin.box.view", [$dataSet->PK_NO]).'" class="btn btn-xs btn-success mb-05 mr-05" title="VIEW PRODUCTS"><i class="la la-eye"></i></a>';
                   }
                   if (hasAccessAbility('edit_box_label', $roles)) {
                       $edit = '<a href="javascript:void(0)" data-toggle="modal" id="box_label" data-target="#EditBoxLabel" title="EDIT BOX LABEL" data-url="'.route('admin.box_label.update').'" data-id="'.$dataSet->PK_NO.'" data-box_label="'.$dataSet->BOX_NO.'" class="btn btn-xs btn-info mb-05 mr-05" ><i class="la la-edit"></i></a>';
                   }
                   return $view.$edit;
               })
               ->addColumn('warehouse_status', function($dataSet){

                   $status = \Config::get('static_array.box_status');

                   return $status[$dataSet->BOX_STATUS];
               })
               ->make(true);
   }

   public function getDatatableShelved($request)
   {
   $dataSet = DB::table('INV_WAREHOUSE_ZONES')
           ->join('INV_WAREHOUSE', 'INV_WAREHOUSE.PK_NO', 'INV_WAREHOUSE_ZONES.F_INV_WAREHOUSE_NO')
           ->select('INV_WAREHOUSE_ZONES.*', 'INV_WAREHOUSE.NAME');
           if (isset($request->type) && $request->type == 'all') {
               $dataSet = $dataSet->where('ITEM_COUNT','>=',0);
           }else{
               $dataSet = $dataSet->where('ITEM_COUNT','!=',0);
           }
           $dataSet = $dataSet->groupBy('INV_WAREHOUSE_ZONES.PK_NO')
                               ->orderBy('INV_WAREHOUSE_ZONES.PK_NO', 'DESC');

           return Datatables::of($dataSet)
           ->addColumn('action', function($dataSet){
               return '<a href="'.route("admin.shelved.view", ['id' => $dataSet->PK_NO]).'" class="btn btn-xs btn-success mr-05" title="VIEW PRODUCT"><i class="la la-eye"></i></a><a href="'.route("admin.shelve.add", ['id'=>$dataSet->PK_NO]).'" class="btn btn-xs btn-info mr-05" title="EDIT SHELVE"><i class="la la-edit"></i></a>';
           })
           ->rawColumns(['action'])
           ->make(true);
   }

   public function getDatatableNotBoxed($request)
   {
       $dataSet = DB::table('INV_STOCK')
               ->select('PK_NO','SKUID','PRD_VARINAT_NAME','PRD_VARIANT_IMAGE_PATH','INV_WAREHOUSE_NAME','IG_CODE as ig_code_','BARCODE',DB::raw('IFNULL(count(SKUID),0) as count'))
               // ->selectSub($count_not_boxed, 'count')
               ->whereNull('F_BOX_NO')
               // ->orWhere('F_BOX_NO',0)
               ->groupBy('F_INV_WAREHOUSE_NO','SKUID')
               ->orderBy('PK_NO', 'DESC');

               return Datatables::of($dataSet)
               ->addColumn('action', function($dataSet){
                   return '<a href="'.route("admin.not_boxed.view", [$dataSet->PK_NO]).'" class="btn btn-xs btn-success mb-05 mr-05" title="View Product"><i class="la la-eye"></i></a>';
               })
               ->rawColumns(['action'])
               ->make(true);
   }

   public function getDatatableSalesComission($request)
   {
       $date           = $request->get('columns')[4]['search']['value'];
       $agent          = Auth::user()->F_AGENT_NO;
       $now            = Carbon::now();
       $current_year   = $now->year;
       $current_month  = $now->month;

       $dataSet = DB::table('SLS_BOOKING as b')
               ->select('a.NAME','a.EMAIL','a.MOBILE_NO','b.RECONFIRM_TIME','a.PK_NO'
               ,DB::raw('(IFNULL(SUM(bd.COMISSION),0)) as comission')
               )
               ->join('SLS_BOOKING_DETAILS as bd','bd.F_BOOKING_NO','b.PK_NO')
               ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
               ->rightjoin('SLS_AGENTS as a','a.PK_NO','b.F_BOOKING_SALES_AGENT_NO')
               ->where('a.IS_ACTIVE',1);
               if ($agent > 0) {
                   $dataSet = $dataSet->where('a.PK_NO',$agent);
               }
               if ($date != '') {
                   $current_year   = date('Y', strtotime($date));
                   $current_month  = date('n', strtotime($date));
               } else {
                   $current_year   = $now->year;
                   $current_month  = $now->month;
               }

               $dataSet = $dataSet->whereYear('b.SS_CREATED_ON',$current_year);
               $dataSet = $dataSet->whereMonth('b.SS_CREATED_ON',$current_month);

               $dataSet = $dataSet->orderBy('a.PK_NO','DESC')
               ->groupBy('b.F_BOOKING_SALES_AGENT_NO')->get();

               foreach ($dataSet as $key => $value) {

                   $data['cancelled_later'] = DB::table('SLS_BOOKING as b')
                   ->select(DB::raw('(IFNULL(SUM(bda.COMISSION),0)) as c_current_comission')
                   )
                   ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
                   ->join('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
                   ->whereYear('b.SS_CREATED_ON',$current_year)
                   ->whereMonth('b.SS_CREATED_ON',$current_month)
                   ->where('b.F_BOOKING_SALES_AGENT_NO',$value->PK_NO)
                   ->whereRaw('(bda.CHANGE_TYPE = "ORDER_CANCEL")')
                   ->groupBy('b.F_BOOKING_SALES_AGENT_NO')
                   ->first();

                   $data['cancelled_now'] = DB::table('SLS_BOOKING as b')
                   ->select(DB::raw('(IFNULL(SUM(bda.COMISSION),0)) as c_current_comission')
                   )
                   ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
                   ->join('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
                   ->whereYear('b.CANCELED_AT',$current_year)
                   ->whereMonth('b.CANCELED_AT',$current_month)
                   ->where('b.F_BOOKING_SALES_AGENT_NO',$value->PK_NO)
                   ->whereRaw('(bda.CHANGE_TYPE = "ORDER_CANCEL")')
                   ->groupBy('b.F_BOOKING_SALES_AGENT_NO')
                   ->first();

                   $data['return_later'] = DB::table('SLS_BOOKING as b')
                   ->select(DB::raw('(IFNULL(SUM(bda.COMISSION),0)) as c_current_comission')
                   )
                   ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
                   ->join('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
                   ->whereYear('b.SS_CREATED_ON',$current_year)
                   ->whereMonth('b.SS_CREATED_ON',$current_month)
                   ->where('b.F_BOOKING_SALES_AGENT_NO',$value->PK_NO)
                   ->whereRaw('(bda.CHANGE_TYPE = "ORDER_RETURN")')
                   ->whereIn('bda.RETURN_TYPE',[1,2,4,5])
                   ->groupBy('b.F_BOOKING_SALES_AGENT_NO')
                   ->first();

                   $data['return_now'] = DB::table('SLS_BOOKING as b')
                   ->select(DB::raw('(IFNULL(SUM(bda.COMISSION),0)) as c_current_comission')
                   )
                   ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
                   ->join('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
                   ->whereYear('bda.RETURN_DATE',$current_year)
                   ->whereMonth('bda.RETURN_DATE',$current_month)
                   ->where('b.F_BOOKING_SALES_AGENT_NO',$value->PK_NO)
                   ->whereRaw('(bda.CHANGE_TYPE = "ORDER_RETURN")')
                   ->whereIn('bda.RETURN_TYPE',[1,2,4,5])
                   ->groupBy('b.F_BOOKING_SALES_AGENT_NO')
                   ->first();

                   $value->comission += $data['cancelled_later']->c_current_comission ?? 0;
                   $value->comission += $data['return_later']->c_current_comission ?? 0;
                   $value->comission -= $data['cancelled_now']->c_current_comission ?? 0;
                   $value->comission -= $data['return_now']->c_current_comission ?? 0;
               }

               return Datatables::of($dataSet)
               ->addColumn('action', function($dataSet){
                   return '<a href="'.route('admin.sales_report.list-item',[$dataSet->PK_NO]).'" type="button" class="btn btn-xs btn-info mr-1 " title="VIEW">
                   <i class="la la-eye"></i></a>';
               })
               ->rawColumns(['action'])
               ->make(true);
   }

   public function getDatatableSalesComissionList($request)
   {
       $date           = $request->get('columns')[9]['search']['value'];
       $now            = Carbon::now();

       $return_later = DB::table('SLS_BOOKING as b')
       ->select('b.BOOKING_TIME','b.RECONFIRM_TIME','b.BOOKING_NO','s.PRD_VARINAT_NAME','b.CUSTOMER_NAME','b.RESELLER_NAME','bda.CURRENT_IS_REGULAR','bda.CURRENT_REGULAR_PRICE','bda.CURRENT_INSTALLMENT_PRICE','bda.F_INV_STOCK_NO','a.USERNAME','b.PK_NO','b.SS_CREATED_ON','bda.F_BUNDLE_NO','bda.BUNDLE_SEQUENC','SLS_BUNDLE.BUNDLE_NAME_PUBLIC','CHANGE_TYPE'
       ,DB::raw('CONCAT("",bda.COMISSION) AS COMISSION')
       )
       ->leftjoin('SA_USER as a','a.PK_NO','b.F_SS_CREATED_BY')
       ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
       ->leftjoin('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
       ->leftjoin('INV_STOCK as s','s.PK_NO','bda.F_INV_STOCK_NO')
       ->leftjoin('SLS_BUNDLE','SLS_BUNDLE.PK_NO','bda.F_BUNDLE_NO')
       ->where('b.F_BOOKING_SALES_AGENT_NO',$request->segment)
       ->where('bda.CHANGE_TYPE','ORDER_RETURN')
       ->whereIn('bda.RETURN_TYPE',[1,2,4,5]);
       if ($date != '') {
           $current_year   = date('Y', strtotime($date));
           $current_month  = date('n', strtotime($date));
       } else {
           $current_year   = $now->year;
           $current_month  = $now->month;
       }
       $return_later = $return_later->whereYear('b.SS_CREATED_ON',$current_year);
       $return_later = $return_later->whereMonth('b.SS_CREATED_ON',$current_month);

       $return_now = DB::table('SLS_BOOKING as b')
       ->select('b.BOOKING_TIME','b.RECONFIRM_TIME','b.BOOKING_NO','s.PRD_VARINAT_NAME','b.CUSTOMER_NAME','b.RESELLER_NAME','bda.CURRENT_IS_REGULAR','bda.CURRENT_REGULAR_PRICE','bda.CURRENT_INSTALLMENT_PRICE','bda.F_INV_STOCK_NO','a.USERNAME','b.PK_NO','b.SS_CREATED_ON','bda.F_BUNDLE_NO','bda.BUNDLE_SEQUENC','SLS_BUNDLE.BUNDLE_NAME_PUBLIC','CHANGE_TYPE'
       ,DB::raw('CONCAT("-",bda.COMISSION) AS COMISSION')
       )
       ->leftjoin('SA_USER as a','a.PK_NO','b.F_SS_CREATED_BY')
       ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
       ->leftjoin('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
       ->leftjoin('INV_STOCK as s','s.PK_NO','bda.F_INV_STOCK_NO')
       ->leftjoin('SLS_BUNDLE','SLS_BUNDLE.PK_NO','bda.F_BUNDLE_NO')
       ->where('b.F_BOOKING_SALES_AGENT_NO',$request->segment)
       ->where('bda.CHANGE_TYPE','ORDER_RETURN')
       ->whereIn('bda.RETURN_TYPE',[1,2,4,5]);
       if ($date != '') {
           $current_year   = date('Y', strtotime($date));
           $current_month  = date('n', strtotime($date));
       } else {
           $current_year   = $now->year;
           $current_month  = $now->month;
       }
       $return_now = $return_now->whereYear('bda.RETURN_DATE',$current_year);
       $return_now = $return_now->whereMonth('bda.RETURN_DATE',$current_month);

       $canceled_later = DB::table('SLS_BOOKING as b')
                   ->select('b.BOOKING_TIME','b.RECONFIRM_TIME','b.BOOKING_NO','s.PRD_VARINAT_NAME','b.CUSTOMER_NAME','b.RESELLER_NAME','bda.CURRENT_IS_REGULAR','bda.CURRENT_REGULAR_PRICE','bda.CURRENT_INSTALLMENT_PRICE','bda.F_INV_STOCK_NO','a.USERNAME','b.PK_NO','b.SS_CREATED_ON','bda.F_BUNDLE_NO','bda.BUNDLE_SEQUENC','SLS_BUNDLE.BUNDLE_NAME_PUBLIC','CHANGE_TYPE'
                   ,DB::raw('CONCAT("",bda.COMISSION) AS COMISSION')
                   )
                   ->leftjoin('SA_USER as a','a.PK_NO','b.F_SS_CREATED_BY')
                   ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
                   ->leftjoin('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
                   // ->join('SLS_BOOKING_DETAILS as bd','bd.F_BOOKING_NO','b.PK_NO')
                   ->leftjoin('INV_STOCK as s','s.PK_NO','bda.F_INV_STOCK_NO')
                   ->leftjoin('SLS_BUNDLE','SLS_BUNDLE.PK_NO','bda.F_BUNDLE_NO')
                   // ->orderBy('bda.F_BOOKING_NO','DESC')
                   // ->orderBy('bda.F_BUNDLE_NO','DESC')
                   // ->orderBy('bda.BUNDLE_SEQUENC','ASC')
                   // ->groupBy('bda.PK_NO')
                   ->where('b.F_BOOKING_SALES_AGENT_NO',$request->segment)
                   ->where('bda.CHANGE_TYPE','ORDER_CANCEL');
                   if ($date != '') {
                       $current_year   = date('Y', strtotime($date));
                       $current_month  = date('n', strtotime($date));
                   } else {
                       $current_year   = $now->year;
                       $current_month  = $now->month;
                   }
                   $canceled_later = $canceled_later->whereYear('b.SS_CREATED_ON',$current_year);
                   $canceled_later = $canceled_later->whereMonth('b.SS_CREATED_ON',$current_month);

                   // $canceled_later = $canceled_later->orderBy('o.PK_NO','DESC');


       $canceled_now = DB::table('SLS_BOOKING as b')
                   ->select('b.BOOKING_TIME','b.RECONFIRM_TIME','b.BOOKING_NO','s.PRD_VARINAT_NAME','b.CUSTOMER_NAME','b.RESELLER_NAME','bda.CURRENT_IS_REGULAR','bda.CURRENT_REGULAR_PRICE','bda.CURRENT_INSTALLMENT_PRICE','bda.F_INV_STOCK_NO','a.USERNAME','b.PK_NO','b.SS_CREATED_ON','bda.F_BUNDLE_NO','bda.BUNDLE_SEQUENC','SLS_BUNDLE.BUNDLE_NAME_PUBLIC','CHANGE_TYPE'
                   // ,DB::raw('(concat("-" , bda.COMISSION) as COMISSION) ')
                   ,DB::raw('CONCAT("-",bda.COMISSION) AS COMISSION')
                   )
                   ->leftjoin('SA_USER as a','a.PK_NO','b.F_SS_CREATED_BY')
                   ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
                   ->leftjoin('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
                   // ->join('SLS_BOOKING_DETAILS as bd','bd.F_BOOKING_NO','b.PK_NO')
                   ->leftjoin('INV_STOCK as s','s.PK_NO','bda.F_INV_STOCK_NO')
                   ->leftjoin('SLS_BUNDLE','SLS_BUNDLE.PK_NO','bda.F_BUNDLE_NO')
                   // ->orderBy('bda.F_BOOKING_NO','DESC')
                   // ->orderBy('bda.F_BUNDLE_NO','DESC')
                   // ->orderBy('bda.BUNDLE_SEQUENC','ASC')
                   // ->groupBy('bda.PK_NO')
                   ->where('b.F_BOOKING_SALES_AGENT_NO',$request->segment)
                   ->where('bda.CHANGE_TYPE','ORDER_CANCEL');
                   if ($date != '') {
                       $current_year   = date('Y', strtotime($date));
                       $current_month  = date('n', strtotime($date));
                   } else {
                       $current_year   = $now->year;
                       $current_month  = $now->month;
                   }
                   $canceled_now = $canceled_now->whereYear('b.CANCELED_AT',$current_year);
                   $canceled_now = $canceled_now->whereMonth('b.CANCELED_AT',$current_month);

                   // $canceled_now = $canceled_now->orderBy('o.PK_NO','DESC');


       $dataSet = DB::table('SLS_BOOKING as b')
                   ->select('b.BOOKING_TIME','b.RECONFIRM_TIME','b.BOOKING_NO','s.PRD_VARINAT_NAME','b.CUSTOMER_NAME','b.RESELLER_NAME','bd.CURRENT_IS_REGULAR','bd.CURRENT_REGULAR_PRICE','bd.CURRENT_INSTALLMENT_PRICE','bd.F_INV_STOCK_NO','a.USERNAME','b.PK_NO','b.SS_CREATED_ON','bd.F_BUNDLE_NO','bd.BUNDLE_SEQUENC','SLS_BUNDLE.BUNDLE_NAME_PUBLIC'
                   ,DB::raw('( 0 ) as CHANGE_TYPE')
                   ,DB::raw('CONCAT("",bd.COMISSION) AS COMISSION')
                   )
                   ->leftjoin('SA_USER as a','a.PK_NO','b.F_SS_CREATED_BY')
                   ->join('SLS_ORDER as o','o.F_BOOKING_NO','b.PK_NO')
                   // ->leftjoin('SLS_BOOKING_DETAILS_AUD as bda','bda.F_BOOKING_NO','b.PK_NO')
                   ->join('SLS_BOOKING_DETAILS as bd','bd.F_BOOKING_NO','b.PK_NO')
                   ->leftjoin('INV_STOCK as s','s.PK_NO','bd.F_INV_STOCK_NO')
                   ->leftjoin('SLS_BUNDLE','SLS_BUNDLE.PK_NO','bd.F_BUNDLE_NO')
                   // ->orderBy('bd.F_BOOKING_NO','DESC')
                   // ->orderBy('bd.F_BUNDLE_NO','DESC')
                   // ->orderBy('bd.BUNDLE_SEQUENC','ASC')
                   // ->groupBy('bd.PK_NO')
                   ->where('b.F_BOOKING_SALES_AGENT_NO',$request->segment);
                   if ($date != '') {
                       $current_year   = date('Y', strtotime($date));
                       $current_month  = date('n', strtotime($date));
                   } else {
                       $current_year   = $now->year;
                       $current_month  = $now->month;
                   }
                   $dataSet = $dataSet->whereYear('b.SS_CREATED_ON',$current_year);
                   $dataSet = $dataSet->whereMonth('b.SS_CREATED_ON',$current_month)

                   // $dataSet = $dataSet->orderBy('o.PK_NO','DESC')
                   ->union($canceled_now)
                   ->union($canceled_later)
                   ->union($return_later)
                   ->union($return_now);
                   // ->orderBy('F_BUNDLE_NO','DESC')
                   // ->orderBy('BUNDLE_SEQUENC','ASC')
                   $query = $dataSet->toSql();
                   $dataSet = DB::table(DB::raw("($query order by PK_NO desc) as a"))->mergeBindings($dataSet);
               return Datatables::of($dataSet)
               ->addColumn('order', function($dataSet){
                   return '<a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->PK_NO]).'" title="VIEW ORDER" target="_blank">ORD-'.$dataSet->BOOKING_NO.'</a>';
               })
               ->rawColumns(['order'])
               ->make(true);
   }

   public function getDatatableOrderCollection($request)
   {
       $agent_id            = Auth::user()->F_AGENT_NO;

       $dataSet = DB::table("SLS_ORDER")
           ->select('SLS_ORDER.PK_NO','SLS_ORDER.F_BOOKING_NO','SLS_ORDER.F_CUSTOMER_NO','SLS_ORDER.F_RESELLER_NO','SLS_ORDER.CUSTOMER_NAME','SLS_ORDER.IS_READY','SLS_BOOKING.SS_CREATED_ON','SA_USER.USERNAME as CREATED_BY','SLS_BOOKING.BOOKING_SALES_AGENT_NAME','SLS_BOOKING.CONFIRM_TIME as ORDER_DATE','SLS_BOOKING.BOOKING_NO','SLS_BOOKING.RESELLER_NAME','SLS_BOOKING.TOTAL_PRICE','SLS_BOOKING.DISCOUNT','SLS_BOOKING.PK_NO as  SLS_BOOKING_PK_NO','SLS_BOOKING.IS_RESELLER','SLS_ORDER.ORDER_BUFFER_TOPUP','SLS_ORDER.ORDER_ACTUAL_TOPUP','SLS_ORDER.IS_SYSTEM_HOLD','SLS_ORDER.IS_ADMIN_HOLD','SLS_ORDER.DISPATCH_STATUS','SLS_ORDER.IS_CANCEL','SLS_BOOKING.CANCEL_REQUEST_BY','SLS_BOOKING.CANCEL_REQUEST_AT','SLS_ORDER.IS_SELF_PICKUP','SLS_BATCH_LIST.RTS_BATCH_NO'
           ,DB::raw('(select ifnull(count(PK_NO),0) from SLS_BOOKING_DETAILS where F_BOOKING_NO = SLS_BOOKING_PK_NO) as total_tobe_picked')
           )
           ->leftJoin('SLS_BOOKING','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING.PK_NO')
           ->leftJoin('SA_USER','SLS_BOOKING.F_SS_CREATED_BY','SA_USER.PK_NO')
           ->join('SLS_BATCH_LIST','SLS_BATCH_LIST.PK_NO','SLS_ORDER.PICKUP_ID')
           ->where('SLS_ORDER.DISPATCH_STATUS', '<', '40')
           // ->whereIn('SLS_ORDER.DISPATCH_STATUS',[30,20])
           ->where('SLS_ORDER.IS_SELF_PICKUP',0)
           ->where('SLS_ORDER.PICKUP_ID','>',0)
           ->orderBy('SLS_ORDER.PICKUP_ID','DESC');
       if ($agent_id > 0) {
           $dataSet->where('SLS_BOOKING.F_BOOKING_SALES_AGENT_NO',$agent_id);
       }
       if ($request->id > 0) {
           $dataSet->where('SLS_BATCH_LIST.PK_NO',$request->id);
       }
       return Datatables::of($dataSet)

       ->addColumn('created_at', function($dataSet){
           $created_at = '<div class="font-11">'.date('d-m-y h:i A',strtotime($dataSet->SS_CREATED_ON)).'</div><div>'.$dataSet->CREATED_BY.'</div>';
           return $created_at;
       })
       ->addColumn('order_date', function($dataSet){
           if($dataSet->ORDER_DATE){
           $order_date = '<div>'.date('d-m-y',strtotime($dataSet->ORDER_DATE)).'</div>';
           }else{
               $order_date = '<div>'.date('d-m-y',strtotime($dataSet->SS_CREATED_ON)).'</div>';
           }
           return $order_date;
       })
       ->addColumn('order_id', function($dataSet){

           $order_id = '<a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->SLS_BOOKING_PK_NO]).'">ORD-'.$dataSet->BOOKING_NO.'</a>';

           return $order_id;
       })
       ->addColumn('customer_name', function($dataSet){

           if($dataSet->IS_RESELLER == 1){
               $customer_name = '<a href="'.route("admin.reseller.edit", [$dataSet->F_RESELLER_NO]). '">'.$dataSet->RESELLER_NAME.'</a>';
           }else{
               $customer_name = '<a href="'.route('admin.customer.view', [$dataSet->F_CUSTOMER_NO]).'">'.$dataSet->CUSTOMER_NAME.'</a>';
           }
           return $customer_name;
       })
       ->addColumn('item_type', function($dataSet){
           $booking_no = $dataSet->F_BOOKING_NO;

           $query = DB::SELECT("SELECT SLS_BOOKING_DETAILS.F_BOOKING_NO,INV_STOCK.F_PRD_VARIANT_NO  FROM SLS_BOOKING_DETAILS LEFT JOIN INV_STOCK ON INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO WHERE SLS_BOOKING_DETAILS.F_BOOKING_NO = $booking_no GROUP BY INV_STOCK.F_PRD_VARIANT_NO");

           $item_type = count($query) ?? 0;

           return $item_type;
       })
       ->addColumn('item_count', function($dataSet){

           $booking_no = $dataSet->F_BOOKING_NO;
           $item_type = BookingDetails::where('F_BOOKING_NO',$booking_no)->count();

           return $item_type;
       })
       ->addColumn('avaiable', function($dataSet){
           $avaiable = '';
           $zones = '';
           $shelve_zones = DB::SELECT("SELECT GROUP_CONCAT(IFNULL(INV_STOCK.F_INV_ZONE_NO,0)) AS ZONES from SLS_BOOKING_DETAILS join INV_STOCK on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO where SLS_BOOKING_DETAILS.F_BOOKING_NO = $dataSet->F_BOOKING_NO GROUP BY SLS_BOOKING_DETAILS.F_BOOKING_NO");


           if($dataSet->IS_READY == 0){
               $avaiable = '<div class="badge badge-primary d-block" title="NOT READY">Intransit</div>';
           }elseif($dataSet->IS_READY == 1){
               $avaiable = '<div class="badge badge-success d-block" title="READY">Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block " title="READY (Need to Shelve)">Ready</div>';
                   }
               }
           }elseif($dataSet->IS_READY == 2){
               $avaiable = '<div class="badge badge-warning d-block" title="PARTIALLY READY">Partially Ready</div>';
               if(!empty($shelve_zones)){
                   $zones  = $shelve_zones[0]->ZONES;
                   $zones_arr = explode(',', $zones);
                   if(in_array(0,$zones_arr)){
                       $avaiable = '<div class="badge badge-warning d-block  (Need to Shelve)" title="PARTIALLY READY">Partially</div>';
                   }

               }
           }
           return $avaiable;
       })
       ->addColumn('status', function($dataSet){

           $status = '';
           if($dataSet->IS_ADMIN_HOLD == 0){

               $item_picked = DB::SELECT("SELECT IFNULL(COUNT(PK_NO),0) AS PICKED from SLS_BOOKING_DETAILS where F_BOOKING_NO = $dataSet->F_BOOKING_NO and IS_COLLECTED_FOR_RTS = 1");

               if (isset($item_picked) && $item_picked[0]->PICKED == $dataSet->total_tobe_picked) {
                   $link = route("admin.order.dispatch",[$dataSet->F_BOOKING_NO]).'?type=rts';
                   $rts = $dataSet->DISPATCH_STATUS == '30' ?'RTS' : ($dataSet->DISPATCH_STATUS == '20' ? 'RTS(H)' : 'Dispacthed(H)' );
               }else{
                   $link = 'javascript:void(0)';
                   $rts = 'Please Collect';
               }

                   // $link = 'javascript:void(0)'.$item_picked[0]->PICKED ?? 0;

               if($dataSet->DISPATCH_STATUS == '30'){
                   $status = '<div class="badge badge-success d-block" title="READY TO SHIP"><a href="'.$link.'">'.$rts.'</a></div>';
               }elseif($dataSet->DISPATCH_STATUS == '20'){
                   $status = '<div class="badge badge-info d-block" title="READY TO COLLECT (Partially)"><a href="'.$link.'">'.$rts.'</a></div>';
               }elseif($dataSet->DISPATCH_STATUS == '10'){
                   $status = '<div class="badge badge-primary d-block" title="DISPACTHED (Partially)">'.$rts.'</div>';
               }

           }else{

               if($dataSet->IS_ADMIN_HOLD == 1){
                   $status = '<div class="badge badge-warning d-block" title="HOLD">HOLD</div>';
               }
           }

           if($dataSet->IS_CANCEL == '1'){
               $status .= '<div class="badge badge-warning d-block" title="Canceled">Canceled</div>';
           }elseif($dataSet->IS_CANCEL == '2'){
               $status .= '<div class="badge badge-warning d-block" title="Cancele Request Pending">CR</div>';
           }

           if($dataSet->IS_SELF_PICKUP == 1){
               $due_amt = $dataSet->TOTAL_PRICE - $dataSet->DISCOUNT - $dataSet->ORDER_BUFFER_TOPUP;
               if($due_amt > 0 ){
                   $status = '<div class="badge badge-warning d-block" title="CASH ON DELIVERY"><a href="'.$link.'?type=cod">COD</a></div>';
               }else{
                   $status = '<div class="badge badge-warning d-block" title="READY TO SELF PICKUP BY CUSTOMER"><a href="'.$link.'?type=cod">RTC</a></div>';
               }

           }

           if($status == ''){

               if($dataSet->IS_SYSTEM_HOLD == 1)
                   {
                       $status = '<div class="badge badge-default d-block" title="In Processing"><i class="la la-spinner spinner"></i></div>';
                   }
           }
           return $status;
       })
       ->addColumn('action', function($dataSet){
           $roles = userRolePermissionArray();
           $action = '';
           if (hasAccessAbility('view_order', $roles)) {

           $action .=' <a href="'.route("admin.booking_to_order.book-order-view", [$dataSet->SLS_BOOKING_PK_NO]).'" class="btn btn-xs btn-success mb-05 mr-05" title="VIEW ORDER"><i class="la la-eye"></i></a> <a href="'.route("admin.item_revert.batch", [$dataSet->SLS_BOOKING_PK_NO]).'" class="btn btn-xs btn-info" title="REVERT BACK" onclick="return confirm('. "'" .'Are you sure?'. "'" .')"><i class="la la-exchange"></i></a>';
           }
           return $action;
       })
       ->rawColumns(['created_at','order_date','order_id','customer_name','item_type','item_count','avaiable','status','action'])
       ->make(true);
   }

   public function getDatatableItemCollection($request)
   {
       $dataSet = DB::table("INV_STOCK")
           ->select('INV_STOCK.PK_NO','INV_STOCK.INV_ZONE_BARCODE','INV_STOCK.BOX_BARCODE','INV_STOCK.PRODUCT_STATUS','INV_STOCK.PRD_VARIANT_IMAGE_PATH','INV_STOCK.F_BOOKING_NO','INV_STOCK.PRD_VARINAT_NAME','SLS_BATCH_LIST.RTS_BATCH_NO','INV_STOCK.SKUID','SLS_BOOKING_DETAILS.RTS_COLLECTION_USER_ID','SLS_BATCH_LIST.RTS_BATCH_NO as batch_no'
           )
           ->leftJoin('SLS_BOOKING_DETAILS','INV_STOCK.PK_NO','SLS_BOOKING_DETAILS.F_INV_STOCK_NO')
           ->leftJoin('SLS_ORDER','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING_DETAILS.F_BOOKING_NO')
           ->join('SLS_BATCH_LIST','SLS_BATCH_LIST.PK_NO','SLS_ORDER.PICKUP_ID')
           ->where('SLS_ORDER.DISPATCH_STATUS', '<', '40')
           // ->whereIn('SLS_ORDER.DISPATCH_STATUS',[30,20])
           // ->where('SLS_ORDER.IS_SELF_PICKUP',0)
           // ->where('INV_STOCK.PRODUCT_STATUS','>=',60)
           ->where('SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS',0)
           ->where('SLS_BATCH_LIST.RTS_BATCH_NO',$request->id)
           ->groupBy('INV_STOCK.IG_CODE')
           ->orderBy('SLS_ORDER.PICKUP_ID','ASC');

       return Datatables::of($dataSet)
       ->addColumn('image', function($dataSet){
           return '<img src="'.asset($dataSet->PRD_VARIANT_IMAGE_PATH).'" class="w100" alt="Image">';
       })
       ->addColumn('assign_user', function($dataSet){
           $user = \App\Models\Auth::select('USERNAME')->where('PK_NO',$dataSet->RTS_COLLECTION_USER_ID)->first();
           if (empty($user->USERNAME)) {
               $user   = 'Unassigned';
               $class  = 'btn-warning';
           }else{
               $user = $user->USERNAME;
               $class  = 'btn-success';
           }
           $assign_user = '<button type="button" title="ASSIGN USER" id="assign_logistic" class="btn btn-xs '.$class.' mb-05 mr-05" data-batch_id="'.$dataSet->batch_no.'" data-sku_id="'.$dataSet->SKUID.'" data-user_id="'.$dataSet->RTS_COLLECTION_USER_ID.'" data-toggle="modal" data-target="#_modal">'.$user.'</button>';
           return $assign_user;
       })
       ->addColumn('position', function($dataSet){
           $return = '';
           $in_landing = DB::SELECT("SELECT INV_STOCK.PK_NO
           from INV_STOCK
           inner join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
           inner join SLS_ORDER  on SLS_ORDER.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO
           inner join SLS_BATCH_LIST  on SLS_BATCH_LIST.PK_NO = SLS_ORDER.PICKUP_ID
           where SLS_ORDER.DISPATCH_STATUS < 40
           and SLS_BATCH_LIST.RTS_BATCH_NO = $dataSet->batch_no
           and  INV_STOCK.F_INV_ZONE_NO IS NULL
           and INV_STOCK.SKUID = $dataSet->SKUID
           and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 0
           group by INV_STOCK.PK_NO");
           $in_landing = count($in_landing) ?? 0;

           // $in_shelve = DB::SELECT("SELECT INV_STOCK.INV_ZONE_BARCODE from SLS_ORDER left join SLS_BOOKING_DETAILS on SLS_ORDER.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO inner join INV_STOCK on INV_STOCK.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO where SLS_ORDER.DISPATCH_STATUS < 40 and SLS_ORDER.PICKUP_ID = $dataSet->PICKUP_ID and INV_STOCK.PRODUCT_STATUS >= 60 and  INV_STOCK.F_INV_ZONE_NO IS NOT NULL and INV_STOCK.SKUID = $dataSet->SKUID group by INV_STOCK.PK_NO");
           $in_shelve = DB::SELECT("SELECT INV_STOCK.INV_ZONE_BARCODE,INV_WAREHOUSE_ZONES.DESCRIPTION,COUNT(*) as count
           from INV_STOCK
           left join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
           left join SLS_ORDER on SLS_ORDER.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO
           inner join SLS_BATCH_LIST  on SLS_BATCH_LIST.PK_NO = SLS_ORDER.PICKUP_ID
           left join INV_WAREHOUSE_ZONES on INV_WAREHOUSE_ZONES.ZONE_BARCODE = INV_STOCK.INV_ZONE_BARCODE
           where INV_STOCK.F_INV_ZONE_NO IS NOT NULL
           and SLS_BATCH_LIST.RTS_BATCH_NO = $dataSet->batch_no
           and INV_STOCK.SKUID = $dataSet->SKUID
           and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 0
           and SLS_ORDER.DISPATCH_STATUS < 40
           group by INV_STOCK.INV_ZONE_BARCODE");
           // $in_shelve_count = count($in_shelve) ?? 0;

           if ($in_landing > 0) {
               $return .= ' Item Is In Landing Area '.' ('.$in_landing.')<br>';
           }
           if (!empty($in_shelve)) {
               foreach ($in_shelve as $key => $value) {
                   $return .= ' Item Is In Shelve - '.$value->INV_ZONE_BARCODE.' ('.$value->count.')'
                                   .'<br><strong>Description: </strong>'.$value->DESCRIPTION.'<br>';
               }
           }
           return $return;
       })
       ->addColumn('total_count', function($dataSet){
           $return = '';

           $in_landing = DB::SELECT("SELECT INV_STOCK.PK_NO
           from INV_STOCK
           inner join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
           inner join SLS_ORDER  on SLS_ORDER.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO
           inner join SLS_BATCH_LIST  on SLS_BATCH_LIST.PK_NO = SLS_ORDER.PICKUP_ID
           where SLS_ORDER.DISPATCH_STATUS < 40
           and SLS_BATCH_LIST.RTS_BATCH_NO = $dataSet->batch_no
           and  INV_STOCK.F_INV_ZONE_NO IS NULL
           and INV_STOCK.SKUID = $dataSet->SKUID
           and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 0
           group by INV_STOCK.PK_NO");
           $in_landing = count($in_landing) ?? 0;

           $in_shelve = DB::SELECT("SELECT INV_STOCK.PK_NO
           from INV_STOCK
           left join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
           left join SLS_ORDER on SLS_ORDER.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO
           inner join SLS_BATCH_LIST  on SLS_BATCH_LIST.PK_NO = SLS_ORDER.PICKUP_ID
           left join INV_WAREHOUSE_ZONES on INV_WAREHOUSE_ZONES.ZONE_BARCODE = INV_STOCK.INV_ZONE_BARCODE
           where INV_STOCK.F_INV_ZONE_NO IS NOT NULL
           and SLS_BATCH_LIST.RTS_BATCH_NO = $dataSet->batch_no
           and INV_STOCK.SKUID = $dataSet->SKUID
           and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 0
           and SLS_ORDER.DISPATCH_STATUS < 40");
           $in_shelve = count($in_shelve) ?? 0;

           return $in_landing + $in_shelve;
       })
       ->rawColumns(['image','assign_user','position','total_count'])
       ->make(true);
   }

   public function getDatatableItemCollectedList($request)
   {
       $dataSet = DB::table("INV_STOCK")
           ->select('INV_STOCK.PK_NO','INV_STOCK.BARCODE','INV_STOCK.INV_ZONE_BARCODE','INV_STOCK.BOX_BARCODE','INV_STOCK.PRODUCT_STATUS','INV_STOCK.PRD_VARIANT_IMAGE_PATH','INV_STOCK.F_BOOKING_NO','INV_STOCK.PRD_VARINAT_NAME','SLS_BATCH_LIST.RTS_BATCH_NO','INV_STOCK.SKUID','SLS_BOOKING_DETAILS.RTS_COLLECTION_USER_ID','SLS_BATCH_LIST.PK_NO as batch_pk'
           )
           ->leftJoin('SLS_BOOKING_DETAILS','INV_STOCK.PK_NO','SLS_BOOKING_DETAILS.F_INV_STOCK_NO')
           ->leftJoin('SLS_ORDER','SLS_ORDER.F_BOOKING_NO','SLS_BOOKING_DETAILS.F_BOOKING_NO')
           ->leftjoin('SLS_BATCH_LIST','SLS_BATCH_LIST.PK_NO','SLS_ORDER.PICKUP_ID')
           ->leftJoin('SC_ORDER_DISPATCH','SC_ORDER_DISPATCH.F_ORDER_NO','SLS_ORDER.PK_NO')
           ->where('SLS_ORDER.DISPATCH_STATUS', '<', '40')
           // ->where('SC_ORDER_DISPATCH.IS_DISPATHED', '!=', 1)
           ->where('SLS_ORDER.IS_SELF_PICKUP',0)
           // ->where('INV_STOCK.PRODUCT_STATUS','>=',60)
           // ->where('SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS',0)
           ->where('SLS_ORDER.PICKUP_ID',$request->id)
           ->groupBy('INV_STOCK.IG_CODE')
           ->orderBy('SLS_ORDER.PICKUP_ID','ASC');

       return Datatables::of($dataSet)
       ->addColumn('image', function($dataSet){
           return '<img src="'.asset($dataSet->PRD_VARIANT_IMAGE_PATH).'" class="w100" alt="Image">';
       })
       ->addColumn('assign_user', function($dataSet){
           $user = \App\Models\Auth::select('USERNAME')->where('PK_NO',$dataSet->RTS_COLLECTION_USER_ID)->first();
           if (empty($user->USERNAME)) {
               $user   = 'Unassigned';
               $class  = 'btn-warning';
           }else{
               $user = $user->USERNAME;
               $class  = 'btn-success';
           }
           $assign_user = '<button type="button" title="ASSIGN USER" id="assign_logistic" class="btn btn-xs '.$class.' mb-05 mr-05" data-batch_id="'.$dataSet->batch_pk.'" data-sku_id="'.$dataSet->SKUID.'" data-user_id="'.$dataSet->RTS_COLLECTION_USER_ID.'" data-toggle="modal" data-target="#_modal">'.$user.'</button>';
           return $assign_user;
       })
       ->addColumn('bulk_assign', function($dataSet){
           $bulk_assign = '<input type="checkbox" name="record_check" value='.$dataSet->SKUID.' class="mr-1 record_check c-p" style="float:right">';
           return $bulk_assign;
       })
       ->addColumn('position', function($dataSet){
           $return = '';

           $in_landing = DB::SELECT("SELECT INV_STOCK.PK_NO
           from INV_STOCK
           inner join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
           inner join SLS_ORDER  on SLS_ORDER.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO
           inner join SLS_BATCH_LIST  on SLS_BATCH_LIST.PK_NO = SLS_ORDER.PICKUP_ID
           where SLS_ORDER.DISPATCH_STATUS < 40
           and SLS_BATCH_LIST.RTS_BATCH_NO = $dataSet->RTS_BATCH_NO
           and  INV_STOCK.F_INV_ZONE_NO IS NULL
           and INV_STOCK.SKUID = $dataSet->SKUID
           group by INV_STOCK.PK_NO");
           $in_landing = count($in_landing) ?? 0;

           $in_landing_collected = DB::SELECT("SELECT INV_STOCK.PK_NO
           from INV_STOCK
           inner join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
           inner join SLS_ORDER  on SLS_ORDER.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO
           inner join SLS_BATCH_LIST  on SLS_BATCH_LIST.PK_NO = SLS_ORDER.PICKUP_ID
           where SLS_ORDER.DISPATCH_STATUS < 40
           and SLS_BATCH_LIST.RTS_BATCH_NO = $dataSet->RTS_BATCH_NO
           and  INV_STOCK.F_INV_ZONE_NO IS NULL
           and INV_STOCK.SKUID = $dataSet->SKUID
           -- and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 0
           and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 1
           group by INV_STOCK.PK_NO");
           $in_landing_collected = count($in_landing_collected) ?? 0;

           $in_shelve = DB::SELECT("SELECT INV_STOCK.INV_ZONE_BARCODE,INV_WAREHOUSE_ZONES.DESCRIPTION,F_INV_ZONE_NO as w_zone,COUNT(*) as count,
           (SELECT IFNULL(COUNT(*),0)
            from INV_STOCK
            left join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
            left join SLS_ORDER on SLS_ORDER.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO
            inner join SLS_BATCH_LIST  on SLS_BATCH_LIST.PK_NO = SLS_ORDER.PICKUP_ID
            left join INV_WAREHOUSE_ZONES on INV_WAREHOUSE_ZONES.ZONE_BARCODE = INV_STOCK.INV_ZONE_BARCODE
            where INV_STOCK.F_INV_ZONE_NO IS NOT NULL
            and SLS_BATCH_LIST.RTS_BATCH_NO = $dataSet->RTS_BATCH_NO
             and INV_STOCK.SKUID = $dataSet->SKUID
            and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 1
            and SLS_ORDER.DISPATCH_STATUS < 40
            and F_INV_ZONE_NO = w_zone
            LIMIT 1 ) as count_collected
           from INV_STOCK
           left join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
           left join SLS_ORDER on SLS_ORDER.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO
           inner join SLS_BATCH_LIST  on SLS_BATCH_LIST.PK_NO = SLS_ORDER.PICKUP_ID
           left join INV_WAREHOUSE_ZONES on INV_WAREHOUSE_ZONES.ZONE_BARCODE = INV_STOCK.INV_ZONE_BARCODE
           where INV_STOCK.F_INV_ZONE_NO IS NOT NULL
           and SLS_BATCH_LIST.RTS_BATCH_NO = $dataSet->RTS_BATCH_NO
           and INV_STOCK.SKUID = $dataSet->SKUID
           -- and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 0
           and SLS_ORDER.DISPATCH_STATUS < 40
           group by INV_STOCK.INV_ZONE_BARCODE");

           if ($in_landing > 0) {
               $return .= '<span title="COLLECTED-'.$in_landing_collected.'TOTAL-'.$in_landing.'"> Item Is In Landing Area '.' ('.$in_landing_collected.' / '.$in_landing.')</span><br>';
           }
           if (!empty($in_shelve)) {
               foreach ($in_shelve as $key => $value) {
                   $return .= ' <span title="COLLECTED-'.$value->count_collected.'TOTAL-'.$value->count.' ">Item Is In Shelve - '.$value->INV_ZONE_BARCODE.' ('.$value->count_collected.' / '.$value->count.')'
                                   .'<br><strong>Description: </strong>'.$value->DESCRIPTION.'</span><br>';
               }
           }
           return $return;
       })
       ->addColumn('total_count', function($dataSet){
           $return = '';

           $in_landing = DB::SELECT("SELECT INV_STOCK.PK_NO
           from INV_STOCK
           inner join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
           inner join SLS_ORDER  on SLS_ORDER.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO
           inner join SLS_BATCH_LIST  on SLS_BATCH_LIST.PK_NO = SLS_ORDER.PICKUP_ID
           where SLS_ORDER.DISPATCH_STATUS < 40
           and SLS_BATCH_LIST.RTS_BATCH_NO = $dataSet->RTS_BATCH_NO
           and  INV_STOCK.F_INV_ZONE_NO IS NULL
           and INV_STOCK.SKUID = $dataSet->SKUID
           -- and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 0
           group by INV_STOCK.PK_NO");
           $in_landing = count($in_landing) ?? 0;

           $in_landing_collected = DB::SELECT("SELECT INV_STOCK.PK_NO
           from INV_STOCK
           inner join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
           inner join SLS_ORDER  on SLS_ORDER.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO
           inner join SLS_BATCH_LIST  on SLS_BATCH_LIST.PK_NO = SLS_ORDER.PICKUP_ID
           where SLS_ORDER.DISPATCH_STATUS < 40
           and SLS_BATCH_LIST.RTS_BATCH_NO = $dataSet->RTS_BATCH_NO
           and  INV_STOCK.F_INV_ZONE_NO IS NULL
           and INV_STOCK.SKUID = $dataSet->SKUID
           and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 1
           group by INV_STOCK.PK_NO");
           $in_landing_collected = count($in_landing_collected) ?? 0;

           $in_shelve = DB::SELECT("SELECT INV_STOCK.PK_NO
           from INV_STOCK
           left join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
           left join SLS_ORDER on SLS_ORDER.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO
           inner join SLS_BATCH_LIST  on SLS_BATCH_LIST.PK_NO = SLS_ORDER.PICKUP_ID
           left join INV_WAREHOUSE_ZONES on INV_WAREHOUSE_ZONES.ZONE_BARCODE = INV_STOCK.INV_ZONE_BARCODE
           where INV_STOCK.F_INV_ZONE_NO IS NOT NULL
           and SLS_BATCH_LIST.RTS_BATCH_NO = $dataSet->RTS_BATCH_NO
           and INV_STOCK.SKUID = $dataSet->SKUID
           -- and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 1
           and SLS_ORDER.DISPATCH_STATUS < 40");
           $in_shelve = count($in_shelve) ?? 0;

           $in_shelve_collected = DB::SELECT("SELECT INV_STOCK.PK_NO
           from INV_STOCK
           left join SLS_BOOKING_DETAILS on INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
           left join SLS_ORDER on SLS_ORDER.F_BOOKING_NO = SLS_BOOKING_DETAILS.F_BOOKING_NO
           inner join SLS_BATCH_LIST  on SLS_BATCH_LIST.PK_NO = SLS_ORDER.PICKUP_ID
           left join INV_WAREHOUSE_ZONES on INV_WAREHOUSE_ZONES.ZONE_BARCODE = INV_STOCK.INV_ZONE_BARCODE
           where INV_STOCK.F_INV_ZONE_NO IS NOT NULL
           and SLS_BATCH_LIST.RTS_BATCH_NO = $dataSet->RTS_BATCH_NO
           and INV_STOCK.SKUID = $dataSet->SKUID
           and SLS_BOOKING_DETAILS.IS_COLLECTED_FOR_RTS = 1
           and SLS_ORDER.DISPATCH_STATUS < 40");
           $in_shelve_collected = count($in_shelve_collected) ?? 0;

           return '<span>'.($in_landing_collected+$in_shelve_collected).' / '.($in_landing+$in_shelve).'</span>';
       })
       ->rawColumns(['image','assign_user','position','bulk_assign','total_count'])
       ->make(true);
   }

   public function customerRefundlist($request)
   {
       $dataSet = DB::table("SLS_CUSTOMERS as c")
       ->select('c.PK_NO AS CUSTOMER_PK_NO','c.CUSTOMER_NO','c.NAME as CUSTOMER_NAME','c.MOBILE_NO as CUSTOMER_MOBILE_NO','c.CUM_BALANCE AS CUSTOMER_CUM_BALANCE','r.DIAL_CODE','c.F_COUNTRY_NO')
       ->leftjoin('SS_COUNTRY as r', 'r.PK_NO','c.F_COUNTRY_NO')
       ->where('c.CUM_BALANCE','>' ,0)
       ->orderBy('c.NAME', 'ASC');

       return Datatables::of($dataSet)
       ->addColumn('customer_no', function($dataSet){
           $customer_no = '';
           $customer_no = '<a href="#" class="" title="Customer No">'.$dataSet->CUSTOMER_NO.'</a>';
           return $customer_no;
       })
       ->addColumn('customer_name', function($dataSet){
           $customer_name = '';
           $customer_name = '<a href="'.route("admin.customer.view", [$dataSet->CUSTOMER_PK_NO]). '" class="" title="Customer name">'.$dataSet->CUSTOMER_NAME.'</a>';
           return $customer_name;

       })
       ->addColumn('mobile', function($dataSet){
           $mobile = $dataSet->DIAL_CODE.' '.$dataSet->CUSTOMER_MOBILE_NO;
           return $mobile;
       })
       ->addColumn('balance', function($dataSet){
           $balance = '';
           $request_amt_txt = '';
           $request_amt = DB::table('ACC_CUST_RES_REFUND_REQUEST')->where('STATUS', 0)->where('F_CUSTOMER_NO',$dataSet->CUSTOMER_PK_NO)->sum('MR_AMOUNT');
           $balance = '<p style="margin-bottom: 1px;">Credit : <small></small> <strong>'.number_format($dataSet->CUSTOMER_CUM_BALANCE,2).'</strong> </p>';
           if($request_amt > 0 ){

               $request_amt_txt = ' <p style="margin-bottom: 1px;"> Request : <small></small> <strong>'.number_format($request_amt,2).'</strong></p>';
           }
           return $balance.$request_amt_txt;

           // $balance = number_format($dataSet->CUSTOMER_CUM_BALANCE,2);
           // return $balance;
       })
       ->addColumn('action', function($dataSet){
           $role = $this->getMyRole();
           $refund = '';
           if($role->ROLE_NO == 1){
               $refund = ' <a href="'.route('admin.payment.refund', ['id' => $dataSet->CUSTOMER_PK_NO, 'type' => 'customer' ]).'" class="btn btn-xs btn-primary mb-05 mr-05" title="Direct refund by admin">R</a>';
           }


           $request = ' <button data-customer_no="'.$dataSet->CUSTOMER_PK_NO.'" data-name="'.$dataSet->CUSTOMER_NAME.'" data-balance="'.$dataSet->CUSTOMER_CUM_BALANCE.'" class="btn btn-xs btn-primary mb-05 mr-05 refundRequest" data-toggle="modal" data-target="#refundRequestModal"  title="Refud request"><i class="la la-plus"></i></button>';

           return $refund .$request;

       })
       ->rawColumns(['customer_no','customer_name','mobile', 'balance','action'])
       ->make(true);
   }

   public function customerRefunded($request)
   {
       $dataSet = DB::table("ACC_CUSTOMER_PAYMENTS as p")
       ->select('p.F_CUSTOMER_NO AS CUSTOMER_PK_NO','p.PAYMENT_NOTE','p.PAYMENT_DATE','p.CUSTOMER_NAME as CUSTOMER_NAME','p.PAYMENT_ACCOUNT_NAME','p.CUSTOMER_NO','p.ATTACHMENT_PATH','p.MR_AMOUNT','r.REQUEST_NOTE','r.REQ_BANK_NAME','r.REQ_BANK_ACC_NAME','r.REQ_BANK_ACC_NO','r.REFUNDED_BANK_NAME','r.REFUNDED_BANK_ACC_NAME','r.REFUNDED_BANK_ACC_NO')
       ->leftJoin('ACC_CUST_RES_REFUND_REQUEST as r','r.PK_NO','p.F_ACC_CUST_RES_REFUND_REQUEST_NO')
       ->where('p.PAYMENT_TYPE',2)
       ->orderBy('p.PAYMENT_DATE', 'ASC');

       return Datatables::of($dataSet)
       ->addColumn('date', function($dataSet){
           $date = '';
           $date = date('d M, Y',strtotime($dataSet->PAYMENT_DATE));
           return $date;
       })
       ->addColumn('reason', function($dataSet){
           $reason = '';
           if($dataSet->REQUEST_NOTE == $dataSet->PAYMENT_NOTE){
               $reason .= '<div class="">'.$dataSet->REQUEST_NOTE.'</div>';
           }else{
               $reason .= '<div class=""><span class="sub_lbl">Request</span>: '.$dataSet->REQUEST_NOTE.'</div>';
               $reason .= '<div class=""><span class="sub_lbl">Refund</span>: '.$dataSet->PAYMENT_NOTE.'</div>';
           }
           return $reason;
       })
       ->addColumn('account', function($dataSet){
           $account = '';
           $account = $dataSet->PAYMENT_ACCOUNT_NAME;
           return $account;
       })
       ->addColumn('customer_no', function($dataSet){
           $customer_no = '';
           $customer_no = $dataSet->CUSTOMER_NO;
           return $customer_no;
       })
       ->addColumn('customer_name', function($dataSet){
           $customer_name = '';
           $customer_name = '<a href="'.route("admin.customer.view", [$dataSet->CUSTOMER_PK_NO]). '" class="" title="Customer name">'.$dataSet->CUSTOMER_NAME.'</a>';
           return $customer_name;

       })
       ->addColumn('req_bank_name', function($dataSet){
           $req_bank_name = '';
           $req_bank_name .= '<div class=""><span class="sub_lbl">Bank </span>: '.$dataSet->REQ_BANK_NAME.'</div>';
           $req_bank_name .= '<div class=""><span class="sub_lbl">Acc Name</span>: '.$dataSet->REQ_BANK_ACC_NAME.'</div>';
           $req_bank_name .= '<div class=""><span class="sub_lbl">Acc No</span>: '.$dataSet->REQ_BANK_ACC_NO.'</div>';
           // $req_bank_name .= '<a href="" class="" title="Customer name">'.$dataSet->REQ_BANK_NAME.'</a>';
           return $req_bank_name;
       })
       ->addColumn('refunded_bank_name', function($dataSet){
           $refunded_bank_name = '';
           $refunded_bank_name .= '<div class=""><span class="sub_lbl">Bank </span>: '.$dataSet->REFUNDED_BANK_NAME.'</div>';
           $refunded_bank_name .= '<div class=""><span class="sub_lbl">Acc Name</span>: '.$dataSet->REFUNDED_BANK_ACC_NAME.'</div>';
           $refunded_bank_name .= '<div class=""><span class="sub_lbl">Acc No</span>: '.$dataSet->REFUNDED_BANK_ACC_NO.'</div>';
           return $refunded_bank_name;
       })
       ->addColumn('image', function($dataSet){
           if($dataSet->ATTACHMENT_PATH){
               $path_info = pathinfo($dataSet->ATTACHMENT_PATH);
               $extension = $path_info['extension'];
               if($extension == 'pdf'){
                   $image = '<a href="'.$dataSet->ATTACHMENT_PATH.'" target="_blank">Show PDF</a>';
               }else{
                   $image = '<img src="'.$dataSet->ATTACHMENT_PATH.'" width="50" >';
               }
           }else{
               $image = '';
           }
           return $image;
       })
       ->addColumn('amount', function($dataSet){
           $amount = number_format(abs($dataSet->MR_AMOUNT),2);
           return $amount;
       })
       ->rawColumns(['date','reason','account','customer_no','customer_name','req_bank_name','refunded_bank_name','image','amount'])
       ->make(true);

   }



   public function customerRefundedRequestList($request)
   {
       $dataSet = DB::table("ACC_CUST_RES_REFUND_REQUEST as p")
       ->select('p.PK_NO as REQUEST_PK_NO','p.F_CUSTOMER_NO','p.REQUEST_DATE','p.REQUEST_BY_NAME','c.CUSTOMER_NO','c.NAME as CUSTOMER_NAME','p.MR_AMOUNT','c.CUM_BALANCE','p.STATUS','p.REQUEST_NOTE','p.REQ_BANK_NAME','p.REQ_BANK_ACC_NAME','p.REQ_BANK_ACC_NO','p.REFUNDED_BANK_NAME','p.REFUNDED_BANK_ACC_NAME','p.REFUNDED_BANK_ACC_NO')
       ->leftJoin('SLS_CUSTOMERS AS c','c.PK_NO','p.F_CUSTOMER_NO')
       ->where('p.STATUS',0)
       ->orderBy('p.REQUEST_DATE', 'ASC');

       return Datatables::of($dataSet)
       ->addColumn('date', function($dataSet){
           $date = '';
           $date = date('d M, Y',strtotime($dataSet->REQUEST_DATE));
           return $date;
       })
       ->addColumn('request_by', function($dataSet){
           $request_by = '';
           $request_by = $dataSet->REQUEST_BY_NAME;
           return $request_by;
       })
       ->addColumn('request_note', function($dataSet){
           return $dataSet->REQUEST_NOTE;
       })
       ->addColumn('customer_no', function($dataSet){
           $customer_no = '';
           $customer_no = $dataSet->CUSTOMER_NO;
           return $customer_no;
       })
       ->addColumn('customer_name', function($dataSet){
           $customer_name = '';
           $customer_name = '<a href="'.route("admin.customer.view", [$dataSet->F_CUSTOMER_NO]). '" class="" title="Customer name">'.$dataSet->CUSTOMER_NAME.'</a>';
           return $customer_name;

       })
       ->addColumn('req_bank_name', function($dataSet){
           $req_bank_name = '';
           $req_bank_name .= '<div class=""><span class="sub_lbl">Bank </span>: '.$dataSet->REQ_BANK_NAME.'</div>';
           $req_bank_name .= '<div class=""><span class="sub_lbl">Acc Name</span>: '.$dataSet->REQ_BANK_ACC_NAME.'</div>';
           $req_bank_name .= '<div class=""><span class="sub_lbl">Acc No</span>: '.$dataSet->REQ_BANK_ACC_NO.'</div>';
           // $req_bank_name .= '<a href="" class="" title="Customer name">'.$dataSet->REQ_BANK_NAME.'</a>';
           return $req_bank_name;

       })

       ->addColumn('balance', function($dataSet){
           $balance = '';
           $request_amt = '';
           $balance = '<p style="margin-bottom: 1px;">Credit : <small>(RM)</small> <strong>'.number_format($dataSet->CUM_BALANCE,2).'</strong> </p>';
           $request_amt = ' <p style="margin-bottom: 1px;"> Request : <small>(RM)</small> <strong>'.number_format($dataSet->MR_AMOUNT,2).'</strong></p>';
           return $balance.$request_amt;
       })
       ->addColumn('status', function($dataSet){
           $role = $this->getMyRole();
           $status = '';

           if($dataSet->STATUS  == 0 ){
               if($role->ROLE_NO == 1){
                   $status = '<button class="btn btn-sm btn-warning">Pending</button>';
               }
           }else{
               $status = '';
           }
           return $status;
       })
       ->addColumn('action', function($dataSet){
           $role = $this->getMyRole();
           $accept = '';
           if($role->ROLE_NO == 1){
               $accept = '<a href="'.route('admin.payment.refund', ['id' => $dataSet->F_CUSTOMER_NO, 'type' => 'customer','request_no' => $dataSet->REQUEST_PK_NO ]).'" class="btn btn-xs btn-primary mb-05 mr-05" title="Refund request accept"><i class="la la-check"></i></a>';
           }

           $deny = ' <a href="'.route('admin.customer.refundrequest_deny', ['id' => $dataSet->REQUEST_PK_NO]).'" class="btn btn-xs btn-primary mb-05 mr-05" title="Request Deny" onclick="return confirm('. "'" .'Are you sure you want to deny the request?'. "'" .')"><i class="la la-undo"></i></a>';

           return $accept.$deny;

       })
       ->rawColumns(['date','request_by','customer_no','customer_name','req_bank_name','balance', 'status','action'])
       ->make(true);

   }

   */


}
