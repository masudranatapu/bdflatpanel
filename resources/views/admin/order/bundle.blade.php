@extends('admin.layout.master')

@section('Product Management','open')
@section('list_order','active')

@if((request()->route()->getName() != 'admin.booking_to_order.book-order-view'))
@section('title') Order | View @endsection
@section('page-name') Order | View @endsection
@else

@section('title') Order | Edit @endsection
@section('page-name') Order | Edit @endsection

@endif


@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_dashboard_title') </a></li>
<li class="breadcrumb-item active">@lang('order.edit_page_breadcrumb_title_active') </li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('app-assets/file_upload/image-uploader.min.css')}}">
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/css/core/colors/palette-callout.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">
<link rel="stylesheet" href="{{ asset('app-assets/lightgallery/dist/css/lightgallery.min.css') }}">
<style>
    #scrollable-dropdown-menu .tt-menu {max-height: 260px; overflow-y: auto; width: 100%; border: 1px solid #333;    border-radius: 5px; }
    #scrollable-dropdown-menu2 .tt-menu {max-height: 260px;overflow-y: auto; width: 100%; border: 1px solid #333;        border-radius: 5px;  }
    .twitter-typeahead{display: block !important;}
    #warehouse th, #availble_qty th { border: none; border-bottom: 1px solid #333;font-size: 12px;font-weight: normal;padding-bottom: 11px; }
    #book_qty th {border: none;font-size: 12px;font-weight: normal;padding-bottom: 5px;padding-top: 0;}
    .tt-hint {color: #999 !important;}
    #append_cus td{ padding: 2px 5px; }
    #append_cus tr{width: 70%;}
    hr {margin-top: 1rem;margin-bottom: 1rem;border: 0;border-top-color: currentcolor;border-top-style: none; border-top-width: 0px;border-top: 1px solid #f2dade;}
    .icheckbox_square-red, .iradio_square-red {margin-top: 3px;}
    .bg-bundle{ background-color: #f9f0f2 !important;}
    .bg-bundle-item{ background-color: #f5edee !important;}
    .bundle-summary{ border-bottom: 2px solid red !important;}
    .pc_in{font-weight: normal;font-size: 12px; display: inline-block;text-align:right; width: 40px;}
    #from_address_noneditable thead tr th,#delivery_address_noneditable thead tr th,#from_address_editable thead tr th,#delivery_address_editable thead tr th{padding: 0px 5px !important;}
    #delivery_address_noneditable thead tr th a,#from_address_noneditable thead tr th a,#from_address_editable thead tr th a,#delivery_address_editable thead tr th a {margin: 2px 0px !important; float: right;}
    #delivery_address_noneditable thead tr th span,#from_address_noneditable thead tr th span,#from_address_editable thead tr th span,#delivery_address_editable thead tr th span {margin: 8px 0px !important; float: left;}
    #sender_td_inline>div,#receiver_td_inline>div{min-height: 100px;}
    .single_line_value{font-weight: normal;font-size: 12px;text-align:right;}
    .single_unit_value{font-weight: normal;font-size: 12px;text-align:right; width: 80px;}
    .single_freight_value{text-align:right; width: 60px;}
    .single_postage_value{text-align:right; width: 60px;}
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 5px 5px!important;font-size: 13px!important; vertical-align: middle;}
    .f-11{font-size: 11px;}
    input.form-control.c_check{height: 22px !important;}
    .htext{font-size: 14px;}
    .readonly-check{ pointer-events: none;}

</style>

@endpush('custom_css')

<?php
use Carbon\Carbon;
$booking_validity   = Config::get('static_array.booking_validity') ?? array();
$booking_details    = $data['booking_details'];
$order              = $data['order'];
$arrived_at         = $data['arrived_at'];
$default_msg_sent_at= $data['default_msg_sent_at'];
$data               = $data['booking'];
$type               = request()->get('type') ?? '';
$customer_id        = $data->getCustomer->PK_NO ?? $data->getReseller->PK_NO;
$agent              = \Illuminate\Support\Facades\Auth::user()->F_AGENT_NO;
$due                = $data->TOTAL_PRICE - $data->DISCOUNT - $order->ORDER_BUFFER_TOPUP;
?>

@section('content')
@if ($agent == 0 || $agent == $data->F_BOOKING_SALES_AGENT_NO)
@if( isset($order->DISPATCH_STATUS) && (($order->DISPATCH_STATUS == 40) || ($order->DISPATCH_STATUS == 35) || ($order->IS_ADMIN_HOLD == 1)))
<div class="card card-success ">
    <div class="card-header pb-0">
        <div class="row">
            <div class="col-md-12">
                @if($order->DISPATCH_STATUS >= 35)
                    <div class="alert bg-danger mb-2 text-center" role="alert" style="background: linear-gradient(to right, #2193b0 0%, #6dd5ed 100%);">
                        <div class="row" style="">
                        <div class="col-md-12" style="">
                        <h4 style="color:#fff;"><i class="icon la la-ban"></i> Alert!</h4>
                        <span style="font-size: 16px;">This order has <strong>Dispatched @if( $order->DISPATCH_STATUS == 35) (Partial) @endif </strong>.</span>
                        <hr style="margin-bottom: 5px; border-top:2px soild #f2dade;">
                        </div>
                        </div>
                            <div class="row" style="">
                                @if($order->dispatch && count($order->dispatch) > 0 )
                                @foreach($order->dispatch as $k => $dispatch)
                                <div class="col-md-6" style="text-align: left;">
                                    <p style="margin-bottom: 2px;">Dispatch By : <strong class="text-uppercase">{{ $dispatch->DISPATCH_USER_NAME }}</strong></p>
                                    <p style="margin-bottom: 2px;">Dispatch Qty : <strong>{{ $dispatch->allChild->count() ?? 0 }}</strong></p>
                                    <p style="margin-bottom: 2px;">Dispatch At : <strong>{{ date('M d,Y',strtotime($dispatch->DISPATCH_DATE)) }}</strong></p>
                                    <p style="margin-bottom: 2px;">Tracking No./Collect By : <strong>{{ $dispatch->COURIER_TRACKING_NO }}</strong></p>
                                    <p style="margin-bottom: 2px; ">Carrier : <a href="{{ $dispatch->courier->URLS ?? '' }}" target="_blank" class="link" style="color: #ebf21e;">{{ $dispatch->COURIER_NAME }}</a></p>

                                </div>
                                @endforeach
                                @endif
                            </div>
                    </div>
                @endif

                @if($order->IS_ADMIN_HOLD == 1)
                    <div class="alert bg-danger alert-dismissible mb-2 text-center" role="alert">
                        <strong>Hold! </strong> Order has been hold by admin.
                    </div>
                @endif



            </div>
        </div>
    </div>
</div>
@endif
@if($order->IS_CANCEL != 0)
        <div class="alert bg-danger alert-dismissible mb-2 text-center" role="alert">
            <div>
                @if($order->IS_CANCEL == 2 )
                <strong>Cancel Request </strong>The order is pending for cancel <br>
                @elseif($order->IS_CANCEL == 1)
                <strong>Order Canceled </strong>The order has been canceled by admin <br>
                @endif
                @if($data->CANCEL_NOTE)
                    <div>Note : {{ $data->CANCEL_NOTE }}</div>
                @endif
                <span style="font-size:12px;" class="pull-right" title="Request At : {{ date('d M, Y h:i A',strtotime($data->CANCEL_REQUEST_AT)) }}">-- Request By : {{ $data->cancelBy->USERNAME ?? '' }}</span>
                <br>
            </div>
        </div>
@endif
@if($order->IS_ADMIN_APPROVAL == 1)
    <div class="alert bg-danger alert-dismissible mb-2 text-center" role="alert">
        <strong>Alert! </strong> Order has been Altered.
    </div>
@endif
@if( isset($order->DEFAULT_AT) && ($order->DEFAULT_TYPE > 0) )
<div class="card card-success">
    <div class="card-header pb-0">
        <div class="row">
            <div class="col-md-12">
                <div class="alert bg-danger mb-2 text-center" role="alert" style="background: #e91e63 ">
                    <div class="row" style="">
                        <div class="col-md-12" style="">
                            <h4 style="color:#fff;"><i class="icon la la-ban"></i> Alert!</h4>
                            <span style="font-size: 16px;">This order has been <strong>Default</strong>.
                            </span>
                            <hr style="margin-bottom: 5px; border-top:2px soild #f2dade;">
                        </div>
                    </div>
                    <?php
                    $default_diff     = Carbon::parse($data->RECONFIRM_TIME)->diffInDays($order->DEFAULT_AT,false);
                    ?>
                    <div class="row" style="">
                        <div class="col-md-6" style="text-align: left;">
                            <p style="margin-bottom: 2px;"><span style="width: 267px;display: inline-block;">ORDER DATE </span>: <strong>{{ date('M d,Y',strtotime($data->RECONFIRM_TIME)) }}</strong></p>
                            @if (isset($arrived_at))
                            <?php  $arrival_diff     = Carbon::parse($data->RECONFIRM_TIME)->diffInDays($arrived_at->SEND_AT,false); ?>
                            <p style="margin-bottom: 2px;"><span style="width: 261px;display: inline-block;">ARRIVED AT </span> : <strong>{{ date('M d,Y',strtotime($arrived_at->SEND_AT)) }} ({{ $arrival_diff }} Days)</strong></p>
                            @endif
                            <p style="margin-bottom: 2px;"><span style="width: 261px;display: inline-block;">DEFAULT AT </span> : <strong>{{ date('M d,Y',strtotime($order->DEFAULT_AT)) }} ({{ $default_diff }} Days)</strong></p>
                            @if (isset($default_msg_sent_at->SEND_AT))
                            <?php
                            $arrive_diff     = Carbon::parse($data->RECONFIRM_TIME)->diffInDays($default_msg_sent_at->SEND_AT,false);
                            ?>
                            <p style="margin-bottom: 2px;"><span style="width: 261px;display: inline-block;">DEFAULT MSG SENT AT </span> : <strong>{{ date('M d,Y',strtotime($default_msg_sent_at->SEND_AT)) }} ({{ $arrive_diff }} Days)</strong></p>
                            @endif
                            <p style="margin-bottom: 2px;"><span style="width: 261px;display: inline-block;">DEFAULT OPTION </span> : <strong>
                                @if ($order->DEFAULT_TYPE == 1)Air / option 1
                                @elseif($order->DEFAULT_TYPE == 2)Air / option 2
                                @elseif($order->DEFAULT_TYPE == 3)Sea / option 1
                                @elseif($order->DEFAULT_TYPE == 4)Sea / option 2
                                @elseif($order->DEFAULT_TYPE == 5)Ready / option 1
                                @elseif($order->DEFAULT_TYPE == 6)Ready / option 2
                                @endif
                            </strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<div class="card">
    {!! Form::open([ 'route' => ['admin.bookingtoorder.update', $data->PK_NO], 'id'=>'booktoorderform', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
    <input type="hidden" name="is_bundle_matched" value="{{ $data->IS_BUNDLE_MATCHED }}" />
    <div class="card-header pb-0">
        <div class="customer_info" style="text-align:center; z-index:1;position:relative;">
                <?php
                if ($data->IS_RESELLER == 0) {
                    $address2 = $data->getCustomerAddress($customer_id,2);
                }else{
                    $address2 = $data->getResellerAddress($customer_id);
                }
                ?>
            <h2>
                <strong><a href="{{ isset($data->getCustomer->PK_NO) ? route('admin.customer.view',$data->getCustomer->PK_NO) : route('admin.reseller.edit',$data->getReseller->PK_NO) }}" target="_blank"><span id="book_customer">{{ $data->getCustomer->NAME ?? $data->getReseller->NAME }}</span> ({{ $data->getCustomer->CUSTOMER_NO ?? $data->getReseller->RESELLER_NO }})</a>

                <a href="javascript:void(0)" id="edit_address{{ $address2[0]->PK_NO ?? '' }}" class="btn btn-xs btn-info mr-1" data-toggle="modal" data-target="#UpdateCustomerAddress" data-post_code="{{ $address2[0]->POST_CODE ?? '' }}" data-customeraddress="{{ $address2[0]->NAME ?? '' }}" data-address_no="{{ $address2[0]->PK_NO ?? '' }}" data-pk_no="{{ $data['pk_no'] ?? '' }}" data-addresstype="{{ $address2[0]->F_ADDRESS_TYPE_NO ?? '' }}" data-mobilenoadd="{{ $address2[0]->TEL_NO ?? '' }}" data-ad_1="{{ $address2[0]->ADDRESS_LINE_1 ?? '' }}" data-ad_2="{{ $address2[0]->ADDRESS_LINE_2 ?? '' }}" data-ad_3="{{ $address2[0]->ADDRESS_LINE_3 ?? '' }}" data-ad_4="{{ $address2[0]->ADDRESS_LINE_4 ?? '' }}" data-location="{{ $address2[0]->LOCATION ?? '' }}" data-country="{{ $address2[0]->country->PK_NO ?? '' }}" data-state="{{ $address2[0]->STATE ?? '' }}" data-city="{{ $address2[0]->CITY ?? '' }}" data-is_reseller="{{ $data->IS_RESELLER }}" style="" title="EDIT BILLING ADDRESS"><i class="la la-edit"></i>
                </a>
            </strong>
            </h2>
            <h5>
                <strong><a href="{{ isset($data->getCustomer->PK_NO) ? route('admin.customer.view',$data->getCustomer->PK_NO) : route('admin.reseller.edit',$data->getReseller->PK_NO) }}" target="_blank">Phone : {{ $data->getCustomer->country->DIAL_CODE ?? $data->getReseller->country->DIAL_CODE ?? '' }}
                <span id="mobile_no_">
                    <?php
                    $mob1 = '';
                    $mob2 = '';
                    $mob3 = '';
                    if (isset($data->getCustomer->MOBILE_NO)) {
                        $mob1 = substr($data->getCustomer->MOBILE_NO, 0, 2);
                        $mob2 = substr($data->getCustomer->MOBILE_NO, 2, 3);
                        $mob3 = substr($data->getCustomer->MOBILE_NO, 5, 4);
                    }else if(isset($data->getReseller->MOBILE_NO)){
                        $mob1 = substr($data->getReseller->MOBILE_NO, 0, 2) ;
                        $mob2 = substr($data->getReseller->MOBILE_NO, 2, 3) ;
                        $mob3 = substr($data->getReseller->MOBILE_NO, 5, 4) ;
                    }
                    ?>
                    {{ $mob1.' '.$mob2.' '.$mob3 }}
                </span>
            </a>
        </strong>
            </h5>
        </div>
        <div class="heading-elements" style="z-index: 2;">
            <div style="display: inline-block" class="mr-1">
                <h4 style="">ORDER NO: <a href="javascript:void(0)">ORD-{{ $data->BOOKING_NO }}</a></h4>
                <h4 style="display: inline-block;width: 125px;">ORDER DATE:</h4> <input type='text' style="display: inline-block;width: 200px;" class="form-control pickadate" placeholder="Order Date" value="{{isset($data->RECONFIRM_TIME) ? date('d-m-Y',strtotime($data->RECONFIRM_TIME)) : date('d-m-Y')}}" name="order_date_" id="order_date_"/>
            </div>
        </div>
    </div>
    <div class="card-content collapse show">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div style="margin-top: -80px;width: 30%;z-index: 1;position: relative;">
                        <h4 style="width: 120px;display: inline-block"><strong>Sales Agent : </strong></h4><h5 style="display: inline-block"><a href="{{ route('admin.agent.edit',$data->F_BOOKING_SALES_AGENT_NO) }}" target="_blank">{{ $data->BOOKING_SALES_AGENT_NAME }}</h5></a><br>
                        <h4 style="width: 120px;display: inline-block"><strong>Created By : </strong></h4><h5 style="display: inline-block"><a href="javascript:void(0)">{{ $data->bookingCreatedBy->USERNAME }}</h5></a>
                    </div>
                    <div style="">
                    </div>
                    <?php
                        $customer_postcode = $data->getCustomerPostCode($data->F_CUSTOMER_NO,$data->F_RESELLER_NO,$data->IS_RESELLER);
                        ?>
                    {!! Form::hidden('booking_id',$data->PK_NO ?? null, ['id'=>'booking_id']) !!}
                    {!! Form::hidden('',$order->PK_NO ?? null, ['id'=>'order_id']) !!}
                    {!! Form::hidden('customer_id',$customer_id ?? null, ['id'=>'customer_id']) !!}
                    {!! Form::hidden('post_code',$customer_postcode->POST_CODE ?? 0, ['id'=>'post_code']) !!}
                    {!! Form::hidden('is_reseller',$data->IS_RESELLER ?? 0, ['id'=>'is_reseller']) !!}
                    {!! Form::hidden('',$type == 'view' ? 0 : 1,['id'=>'page_is_view']) !!}

                    <div class="form-body" id="order_form">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div id="cus_details" style="border: 1px solid #c4c4c4;border-radius: 5px;margin-top: 30px;">
                                    <div class="form-group {!! $errors->has('book_customer') ? 'error' : '' !!} mb-0">
                                        <div class="table-responsive">
                                            <table class="table mb-0" id="from_address_noneditable">
                                                <thead>
                                                    <tr>
                                                        <th colspan="4" style="background: aliceblue;">
                                                            <span>Sender</span>
                                                            <a href="javascript:void(0)" id="address_change_sender" class="btn btn-xs btn-primary ml-2" style="font-size: 12px">Edit Address</a>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td id="sender_td_inline">
                                                            <div>
                                                                {!! !empty($order->FROM_NAME) ? $order->FROM_NAME."<br>" : '' !!}
                                                                {!! !empty($order->FROM_ADDRESS_LINE_1) ? $order->FROM_ADDRESS_LINE_1."<br>" : '' !!}
                                                                {!! !empty($order->FROM_ADDRESS_LINE_2) ? $order->FROM_ADDRESS_LINE_2."<br>" : '' !!}
                                                                {!! !empty($order->FROM_ADDRESS_LINE_3) ? $order->FROM_ADDRESS_LINE_3."<br>" : '' !!}
                                                                {!! !empty($order->FROM_ADDRESS_LINE_4) ? $order->FROM_ADDRESS_LINE_4."<br>" : '' !!}
                                                                {!! !empty($order->FROM_CITY) ? $order->FROM_CITY." " : '' !!}
                                                                {!! !empty($order->FROM_POSTCODE) ? $order->FROM_POSTCODE."<br>" : '' !!}
                                                                {!! !empty($order->FROM_STATE) ? $order->FROM_STATE : '' !!}{!! !empty($order->FROM_COUNTRY) ? ', '.$order->FROM_COUNTRY."<br>" : '' !!}
                                                                <?php
                                                                if (!empty($order->FROM_MOBILE)) {
                                                                    $from_mob1 = substr($order->FROM_MOBILE, 0, 2);
                                                                    $from_mob2 = substr($order->FROM_MOBILE, 2, 3);
                                                                    $from_mob3 = substr($order->FROM_MOBILE, 5,4);
                                                                }
                                                                ?>
                                                                {{ !empty($order->FROM_MOBILE) ? ($order->from_country->DIAL_CODE ?? '').' '.$from_mob1.' '.$from_mob2.' '.$from_mob3 : '' }}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="table mb-0" id="from_address_editable" style="display: none">
                                                <thead>
                                                    <tr>
                                                        <th colspan="4" style="background: aliceblue;">
                                                            <span>Sender</span>
                                                            <a href="javascript:void(0)" id="update_btn{{ $data->PK_NO }}" class="btn btn-xs btn-primary ml-2" data-toggle="modal" data-target="#UpdateCustomerAddress" data-customer_address_id="{{$data->f_customer_address ?? 0}}" data-pk_no="{{ $data->PK_NO }}" data-order_status="{{ $data->ORDER_STATUS }}" data-type="sender" style="font-size: 12px" title="{{ $data->customer_name ?? 'NO ADDRESS ASSIGNED' }}">Address Book</a>
                                                        </th>
                                                        {!! Form::hidden('sender_f_country',$order->FROM_F_COUNTRY_NO ?? '' , ['id'=>'sender_f_country']) !!}
                                                    </tr>
                                                </thead>
                                                <tbody id="append_cus">
                                                        <tr id="from_name">
                                                            <td>
                                                                {!! Form::hidden('f_from_address', $order->F_FROM_ADDRESS ) !!}
                                                                <input style="width: 100%" class="form-control input-sm" name="from_name" type="text" value="{{ $order->FROM_NAME ?? '' }}"></td>

                                                        </tr>
                                                        <tr id="from_add_1">
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_add_1" type="text" value="{{ $order->FROM_ADDRESS_LINE_1 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_add_2" type="text" value="{{ $order->FROM_ADDRESS_LINE_2 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_add_3" type="text" value="{{ $order->FROM_ADDRESS_LINE_3 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_add_4" type="text" value="{{ $order->FROM_ADDRESS_LINE_4 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>

                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_city" type="text" value="{{ $order->FROM_CITY ?? '' }}" readonly></td>

                                                            <td style="border-top: 1px solid #e3ebf3;"><input style="width: 100%" class="form-control input-sm" name="from_post_code" type="text" value="{{ $order->FROM_POSTCODE ?? '' }}" readonly></td>
                                                        </tr>
                                                        <tr>

                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_state" type="text" value="{{ $order->FROM_STATE ?? '' }}" readonly></td>

                                                            <td style="border-top: 1px solid #e3ebf3"><input style="width: 100%" class="form-control input-sm" name="from_country" type="text" value="{{ $order->FROM_COUNTRY ?? '' }}" readonly></td>
                                                        </tr>
                                                        <tr>

                                                            {!! Form::hidden('', $order->from_country->DIAL_CODE ?? '', ['id'=>'sender_dial_code']) !!}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_mobile" type="text" value="{{ $order->FROM_MOBILE ?? '' }}"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan=2 style="border-top: 1px solid #e3ebf3">
                                                                <a href="javascript:void(0)" id="address_done_sender" class="btn btn-xs btn-info mr-2" style="font-size: 12px;float:right;">Done Editing</a>
                                                            </td>
                                                        </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="cus_details" style="border: 1px solid #c4c4c4;border-radius: 5px;margin-top: 30px;">
                                    <div class="form-group {!! $errors->has('book_customer') ? 'error' : '' !!} mb-0">
                                        <div class="table-responsive">
                                            <table class="table mb-0" id="delivery_address_noneditable">
                                                <thead>
                                                    <tr>
                                                        <th colspan="4" style="background: aliceblue;">
                                                            <span>Receiver</span>
                                                            <a href="javascript:void(0)" id="address_change_receiver" class="btn btn-xs btn-primary ml-2" style="font-size: 12px">Edit Address</a>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td id="receiver_td_inline">
                                                            <div>
                                                                {!! !empty($order->DELIVERY_NAME) ? $order->DELIVERY_NAME."<br>" : '' !!}
                                                                {!! !empty($order->DELIVERY_ADDRESS_LINE_1) ? $order->DELIVERY_ADDRESS_LINE_1."<br>" : '' !!}
                                                                {!! !empty($order->DELIVERY_ADDRESS_LINE_2) ? $order->DELIVERY_ADDRESS_LINE_2."<br>" : '' !!}
                                                                {!! !empty($order->DELIVERY_ADDRESS_LINE_3) ? $order->DELIVERY_ADDRESS_LINE_3."<br>" : '' !!}
                                                                {!! !empty($order->DELIVERY_ADDRESS_LINE_4) ? $order->DELIVERY_ADDRESS_LINE_4."<br>" : '' !!}
                                                                {!! !empty($order->DELIVERY_CITY) ? $order->DELIVERY_CITY." " : '' !!}
                                                                {!! !empty($order->DELIVERY_POSTCODE) ? $order->DELIVERY_POSTCODE."<br>" : '' !!}
                                                                {!! !empty($order->DELIVERY_STATE) ? $order->DELIVERY_STATE : '' !!}{!! !empty($order->DELIVERY_COUNTRY) ? ', '.$order->DELIVERY_COUNTRY."<br>" : '' !!}
                                                                <?php
                                                                if (!empty($order->DELIVERY_MOBILE)) {
                                                                    $delivery_mob1 = substr($order->DELIVERY_MOBILE, 0, 2);
                                                                    $delivery_mob2 = substr($order->DELIVERY_MOBILE, 2, 3);
                                                                    $delivery_mob3 = substr($order->DELIVERY_MOBILE, 5,4);
                                                                }
                                                                ?>
                                                                {{ !empty($order->DELIVERY_MOBILE) ? ($order->to_country->DIAL_CODE ?? '').' '.$delivery_mob1.' '.$delivery_mob2.' '.$delivery_mob3 : '' }}
                                                            <div>
                                                        </td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="table mb-0" id="delivery_address_editable" style="display: none">
                                                <thead>
                                                    <tr>
                                                        <th colspan="4" style="background: aliceblue;"><span>Receiver</span>
                                                            <a href="javascript:void(0)" id="update_btn{{ $data->PK_NO }}" class="btn btn-xs btn-primary ml-2" data-toggle="modal" data-target="#UpdateCustomerAddress" data-customer_address_id="{{$data->f_customer_address ?? 0}}" data-pk_no="{{ $data->PK_NO }}" data-order_status="{{ $data->ORDER_STATUS }}" data-type="receiver" style="font-size: 12px" title="{{ $data->customer_name ?? 'NO ADDRESS ASSIGNED' }}">Address Book</a>

                                                        </th>
                                                        {!! Form::hidden('receiver_f_country',$order->DELIVERY_F_COUNTRY_NO ?? '' , ['id'=>'receiver_f_country']) !!}
                                                    </tr>
                                                </thead>
                                                <tbody id="append_cus">
                                                        <tr>

                                                            <td>
                                                                {!! Form::hidden('f_to_address',$order->F_TO_ADDRESS ) !!}
                                                                <input style="width: 100%" class="form-control input-sm" name="delivery_name" type="text" value="{{ $order->DELIVERY_NAME ?? '' }}"></td>

                                                        </tr>
                                                        <tr>

                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_add_1" type="text" value="{{ $order->DELIVERY_ADDRESS_LINE_1 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>

                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_add_2" type="text" value="{{ $order->DELIVERY_ADDRESS_LINE_2 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>

                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_add_3" type="text" value="{{ $order->DELIVERY_ADDRESS_LINE_3 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>

                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_add_4" type="text" value="{{ $order->DELIVERY_ADDRESS_LINE_4 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>

                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_city" type="text" value="{{ $order->DELIVERY_CITY ?? '' }}" readonly></td>

                                                            <td style="border-top: 1px solid #e3ebf3;"><input style="width: 100%" class="form-control input-sm" name="delivery_post_code" type="text" value="{{ $order->DELIVERY_POSTCODE ?? '' }}" readonly></td>
                                                        </tr>
                                                        <tr>

                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_state" type="text" value="{{ $order->DELIVERY_STATE ?? '' }}" readonly></td>

                                                            <td style="border-top: 1px solid #e3ebf3;"><input style="width: 100%" class="form-control input-sm" name="delivery_country" type="text" value="{{ $order->DELIVERY_COUNTRY ?? '' }}" readonly></td>
                                                        </tr>
                                                        <tr>

                                                            {!! Form::hidden('', $order->to_country->DIAL_CODE ?? '', ['id'=>'receiver_dial_code']) !!}

                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_mobile" type="text" value="{{ $order->DELIVERY_MOBILE ?? '' }}"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan=2 style="border-top: 1px solid #e3ebf3">
                                                                <a href="javascript:void(0)" id="address_done_receiver" class="btn btn-xs btn-info mr-2" style="font-size: 12px;float:right;">Done Editing</a>
                                                            </td>
                                                        </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md col1">
                                <div class="bs-callout-danger callout-border-left mt-1 p-1">
                                    <strong>Order Value</strong>
                                    <p class="mt-1" id="order_value1">{{ isset($data->TOTAL_PRICE) ? number_format($data->TOTAL_PRICE - $data->DISCOUNT,2) : 0 }}</p>
                                </div>
                            </div>
                            <div class="col-md col1 col-half-offset">
                                <div class="bs-callout-success callout-border-left mt-1 p-1">
                                    <strong>Amount Paid</strong>
                                    <p class="mt-1">
                                        <?php
                                            $amount_paid = ($order->ORDER_ACTUAL_TOPUP ?? 0) - ($order->ORDER_BALANCE_RETURN ?? 0);
                                        ?>
                                        <span title="ACTUAL AMOUNNT" id="order_balance">
                                            @if ($amount_paid > 0)
                                            <a href="javascript:void(0)" style="text-decoration: underline;" id="payidbookorder" data-toggle="modal" data-target="#PayIdModalBooktoorder" data-order_id="{{ $order->PK_NO }}" data-is_reseller="{{ $data->IS_RESELLER }}" data-type="dispatched">
                                                {{ number_format($amount_paid,2) }}
                                            </a>
                                            @else
                                            {{ number_format($amount_paid,2) }}
                                            @endif
                                        <span>
                                        @if ($amount_paid != ($order->ORDER_BUFFER_TOPUP ?? 0))
                                        /
                                        <a href="javascript:void(0)" style="text-decoration: underline;" id="payidbookorder" data-toggle="modal" data-target="#PayIdModalBooktoorder" data-order_id="{{ $order->PK_NO }}" data-is_reseller="{{ $data->IS_RESELLER }}" data-type="dispatched">
                                            <span style="color: #f00;" title="BUFFER AMOUNT">{{ isset($order->ORDER_BUFFER_TOPUP) ? number_format($order->ORDER_BUFFER_TOPUP,2) : 0 }}</span>
                                        </a>
                                        @endif
                                        {!! Form::hidden('', $order->ORDER_BUFFER_TOPUP ?? 0, ['id'=>'buffer_amount']) !!}
                                        {!! Form::hidden('', $amount_paid ?? 0, ['id'=>'topup_amount']) !!}
                                        {!! Form::hidden('', $order->ORDER_BALANCE_RETURN ?? 0, ['id'=>'balance_return']) !!}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md col1 col-half-offset">
                                <div class="bs-callout-info callout-border-left mt-1 p-1">
                                    <strong>Used Amount</strong>
                                    {!! Form::hidden('balance_used', $order->ORDER_BALANCE_USED ?? 0, ['id'=>'balance_used']) !!}
                                    <p class="mt-1" id="order_balance_used">
                                        {{ isset($order->ORDER_BALANCE_USED) ? number_format($order->ORDER_BALANCE_USED,2) : 0 }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md col1 col-half-offset">
                                <div class="bs-callout-primary callout-border-left mt-1 p-1">
                                    <strong>Available Amount</strong>
                                    <?php
                                        $available_amount = ($order->ORDER_ACTUAL_TOPUP ?? 0) - ($order->ORDER_BALANCE_RETURN ?? 0) - ($order->ORDER_BALANCE_USED ?? 0);
                                    ?>
                                    {!! Form::hidden('', $available_amount ?? 0, ['id'=>'order_outstanding_hidden']) !!}
                                    <p class="mt-1" id="order_outstanding">
                                        {{ number_format($available_amount,2) }}</p>
                                </div>
                            </div>
                            <div class="col-md col1 col-half-offset">
                                <div class="bs-callout-pink callout-border-left mt-1 p-1">
                                    <strong>Due Amount</strong>
                                        <?php
                                            $due_amount = ($data->TOTAL_PRICE ?? 0) - ($data->DISCOUNT ?? 0) - (($order->ORDER_BUFFER_TOPUP ?? 0) - ($order->ORDER_BALANCE_RETURN ?? 0))
                                        ?>
                                    <p class="mt-1" id="due_amount1">
                                        {{ number_format($due_amount,2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-borde table-sm" >
                                <thead>
                                    <tr>
                                        <th style="width: 120px">@lang('tablehead.image')</th>
                                        <th style="width: 200px">@lang('tablehead.product_name')</th>
                                        <th class="" style="width: 150px;">@lang('tablehead.warehouse')</th>
                                        <th class="" style="width: 20px;">Qty1</th>
                                        <th class="" style="width: 50px;">Postage</th>
                                        <th class="" style="width: 50px;text-align: center">Freight</th>
                                        <th class="" style="width: 10px;">Unit Price</th>
                                        <th class="" style="width: 10px;">Line Total</th>
                                        <th class="checkBox" style="width: 50px;" title="PAY FOR THIS ITEM">Paid?</th>
                                        <th class="checkBox" style="width: 50px;" title="SELF PICKUP COD/RTC">Self?</th>

                                        <th class="Action" style="width: 70px;">@lang('tablehead.action')</th>
                                    </tr>
                                </thead>
                                <tbody id="append_tr">
                                    @if($data->IS_BUNDLE_MATCHED == 1)
                                        <?= $data->bundleInfo ?>
                                    @else
                                        @if($booking_details && count($booking_details) > 0 )
                                            @foreach($booking_details as $key => $val)
                                                <?= $val->book_info ?>
                                            @endforeach
                                        @endif
                                    @endif
                                </tbody>
                                <tfoot id="append_tfoot">
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">Sub Total</td>
                                        <td ></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td id="total_single_unit_value" class="text-right"></td>
                                        @if ($type == 'view')
                                        @else
                                        <td colspan="3"></td>
                                        @endif
                                    </tr>
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">Total Freight Cost</td>
                                        <td></td>
                                        <td colspan="3"></td>
                                        <td id="freight_cost_total"class="text-right"></td>
                                        @if ($type == 'view')
                                        @else
                                        <td colspan="3"></td>
                                        @endif
                                    </tr>
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">Postage Cost</td>
                                        <td></td>
                                        <td colspan="3"> </td>
                                        <td id="postage_cost_final" class="text-right"></td>
                                        @if ($type == 'view')
                                        @else
                                        <td colspan="3"></td>
                                        @endif
                                    </tr>
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td></td>
                                        <th class="text-right">Total</th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <th id="total_single_line_value" class="text-right">{{ number_format($data->TOTAL_PRICE,2) }}</th>

                                        @if ($type == 'view')
                                        @else
                                        <td colspan="3"></td>
                                        @endif
                                    </tr>
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">Discount</td>
                                        <td colspan="4" ></td>
                                        <td class="text-right">
                                            <input type="number" name="discount_amount" style="width: 60px;height: 27px!important; float: right; text-align: right;" class="form-control input-sm " id="discount_amount" value="{{ number_format($data->DISCOUNT,2, '.', '') }}" step="0.00">
                                        </td>

                                        @if ($type == 'view')
                                        @else
                                        <td colspan="3"></td>
                                        @endif
                                    </tr>
                                    <tr class="text-right" style="color: red" id='penalty_fee_tr'>
                                        <td></td>
                                        <td></td>
                                        <th>Penalty Fee</th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <th id="penalty_fee">{{ number_format($data->PENALTY_FEE,2) }}</th>
                                        @if ($type == 'view')
                                        @else
                                        <td colspan="3"></td>
                                        @endif
                                    </tr>
                                    <tr style="text-align: center">
                                        <th></th>
                                        <th></th>
                                        <th class="text-right">Grand Total</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>
                                            {!! Form::hidden('grand_total', $data->TOTAL_PRICE, ['id' => 'grand_total1']) !!}
                                        </th>
                                        <th id="grand_total_ss1" class="text-right">
                                            {{ number_format($data->TOTAL_PRICE -$data->DISCOUNT,2) }}</th>
                                        @if ($type == 'view')
                                        @else
                                        <td colspan="3"></td>
                                        @endif
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group {!! $errors->has('booking_note') ? 'error' : '' !!}">
                            <label>Special Note</label>
                            <div class="controls">
                                {!! Form::textarea('booking_note', $data->BOOKING_NOTES, [ 'class' => 'form-control mb-1 summernote', 'placeholder' => 'Enter special note', 'tabindex' => 16, 'rows' => 3 ]) !!}
                                {!! $errors->first('booking_note', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                </div>
                @if ($order->IS_DEFAULT == 1)
                <div class="card card-success">
                    <div class="card-header pb-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="bg-danger mb-2 text-center p-1" role="alert" style="background: linear-gradient(to right, #2193b0 0%, #6dd5ed 100%);">
                                    <div class="row" style="">
                                        <div class="col-md-12" style="">
                                            <h4 style="color:#fff;"><i class="icon la la-ban"></i> Warning!</h4>
                                            <span style="font-size: 16px;color:#fff;">Please Insert Penalty Amount
                                            </span>
                                            <hr style="margin-bottom: 5px; border-top:2px soild #f2dade;">
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="offset-md-4 col-md-4 mb-1">
                                            <input type='text' style="display: inline-block;" class="form-control pickadate pickadate_grace" placeholder="Grace Time" value="{{ isset($order->GRACE_TIME) ? date('d-m-Y',strtotime($order->GRACE_TIME)) : '' }}" name="grace_time" id="grace_time"/>
                                        </div>
                                        <div class="offset-md-4 col-md-4 mb-1">
                                            <div class="controls">
                                                <select id="change_option_bundle" class="select2" name="change_option">
                                                    <option value="1">OPTION 1</option>
                                                    <option value="0">OPTION 2</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="offset-md-4 col-md-4">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <input type="number" name="penalty_amount" id="penalty_amount" class="form-control form-control-sm input-sm" step="0.01" value="{{ number_format($data->PENALTY_FEE,2) ?? "0.00" }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="offset-md-4 col-md-4">
                                            <div class="form-group {!! $errors->has('penalty_note') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::textarea('penalty_note', $data->PENALTY_NOTE, [ 'class' => 'form-control mb-1 summernote', 'placeholder' => 'Enter Penalty note', 'tabindex' => 16, 'rows' => 3 ]) !!}
                                                    {!! $errors->first('penalty_note', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                {!! Form::hidden('', number_format($data->PENALTY_FEE,2) ?? "0.00", ['id'=>'penalty_amount']) !!}
            </div>
            <div class="form-actions mt-10 text-center">
                <a href="{{ route('admin.order.list')}}" class="btn btn-warning mr-1"><i class="ft-x"></i> @lang('form.btn_cancle')</a>
                @if((request()->route()->getName() != 'admin.booking_to_order.book-order-view'))
                    <button name="save_btn" type="submit" class="btn btn-primary save-inv-details mr-1" value="proceed_to_order"><i class="la la-check-square-o"></i> {{ $order != null ? 'Update Order' : 'Proceed to Order' }}</button>
                    @if($due > 0)
                    <button name="save_btn" type="submit" class="btn btn-success save-inv-details" value="proceed_to_order_make_payment"><i class="la la-check-square-o"></i> {{ $order != null ? 'Update Order & Take Payment' : 'Proceed to Order & Take Payment' }}</button>
                    @endif
                @endif

            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
</div>
</div>
</div>
@include('admin.order._modal_html')
@endif
@endsection
<!--push from page-->
@push('custom_js')

<!-- Typeahead.js Bundle -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script type="text/javascript" src="{{ asset('app-assets/js/common.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/order_booking.js?v=1.1')}}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/country.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{ asset('app-assets/lightgallery/dist/js/lightgallery.min.js')}}"></script>
<script>
     $(".lightgallery").lightGallery();
    $('#order_date_').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
        max:"<?php echo date('d-m-Y'); ?>",
    });
    $('.pickadate_grace').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
        min:"<?php echo date('d-m-Y'); ?>",
    });
    $(document).on('input','.single_postage_value, .single_freight_value, .single_unit_value', function(e){
        var tr = $(this).closest('tr');
        trSum(tr);
    })
    $(document).on('input','#discount_amount', function(e){
        totalPostage();
        totalFreight();
        totalSingleUnitValue();
        totalSingleLineValue();
        grandTotalValue();
    })
    $(document).on('input','#penalty_amount', function(e){
        grandTotalValue();
    })
    function trSum(tr){
        var single_postage_value = Number(tr.find('.single_postage_value').val());
        var single_freight_value = Number(tr.find('.single_freight_value').val());
        var single_unit_value = Number(tr.find('.single_unit_value').val());
        var single_line_value = single_postage_value+single_freight_value+single_unit_value;
        tr.find('.single_line_value').val(single_line_value);
        totalPostage();
        totalFreight();
        totalSingleUnitValue();
        totalSingleLineValue();
        grandTotalValue();
    }
    function totalPostage() {
        var postage_cost_final = 0;
        $('.single_postage_value').each(function(){postage_cost_final += parseFloat(this.value);});
        $("#postage_cost_final").text(postage_cost_final.toFixed(2));
    }
    function totalFreight() {
        var freight_cost_total = 0;
        $('.single_freight_value').each(function(){freight_cost_total += parseFloat(this.value);});
        $("#freight_cost_total").text(freight_cost_total.toFixed(2));
    }
    function totalSingleUnitValue() {
        var total_single_unit_value = 0;
        $('.single_unit_value').each(function(){total_single_unit_value += parseFloat(this.value);});
        $("#total_single_unit_value").text(total_single_unit_value.toFixed(2));
    }
    function totalSingleLineValue() {
        var total_single_line_value = 0;
        $('.single_line_value').each(function(){total_single_line_value += parseFloat(this.value);});
        $('#total_single_line_value').text(total_single_line_value.toFixed(2));
    }
    function grandTotalValue() {
        var postage_cost_final      = Number($('#postage_cost_final').text());
        var freight_cost_total      = Number($('#freight_cost_total').text());
        var total_single_line_value = Number($('#total_single_unit_value').text());
        var discount_amount         = Number($('#discount_amount').val());
        var penalty_amount          = Number($('#penalty_amount').val());
        var grand_total_ss1         = postage_cost_final+freight_cost_total+total_single_line_value+penalty_amount-discount_amount;
        var grand_total             = postage_cost_final+freight_cost_total+total_single_line_value+penalty_amount;

        $('#grand_total_ss1').text(grand_total_ss1.toFixed(2));
        $('#grand_total1').val(grand_total.toFixed(2));
    }
    $(document).on('change','.payment_status_bundle',function(e) {
        var data_bundle_sequenc = $(this).attr('data-bundle-sequenc');
        var data_bundle = $(this).attr('data-bundle');
        var child_class = 'bundle_payment_status_'+data_bundle+data_bundle_sequenc;
        if ($(this).is(":checked")){ $('.'+child_class).attr("checked", "checked");}else{$('.'+child_class).removeAttr("checked");}
    })
    $(document).on('change','.selfpickup_status_bundle',function(e) {
        var data_bundle_sequenc = $(this).attr('data-bundle-sequenc');
        var data_bundle = $(this).attr('data-bundle');
        var child_class = 'bundle_selfpickup_'+data_bundle+data_bundle_sequenc;
        if ($(this).is(":checked")){ $('.'+child_class).attr("checked", "checked");}else{$('.'+child_class).removeAttr("checked");}
    })
    $(document).on('click','.c_check',function(e){
        var top_up = Number($('#topup_amount').val());
        var totalPrice = 0;
        $('.payment_status').each(function() {
            if( $(this).is(':checked') ) {
                totalPrice += parseInt($(this).attr('data-line_price'));
            }
        });
        if(top_up < totalPrice ){
            return false;
        }else{
            var outstanding = top_up - totalPrice;
            $('#order_outstanding').text(outstanding.toFixed(2));
            $('#order_balance_used').text(totalPrice.toFixed(2));
        }
    })
    $(document).ready(function(){
        totalPostage();totalFreight();totalSingleUnitValue();totalSingleLineValue();
        var option_type = $('#is_regular').val();
        if (option_type == 1) {
            $('#change_option_bundle option[value=1]').attr('selected','selected').change();
        }else{
            $('#change_option_bundle option[value=0]').attr('selected','selected').change();
        }
    })
    $(document).on('change','#change_option_bundle',function(){
        $('[id*=is_regular]').val($(this).val());
        $('#append_tr > tr').each(function (i, row) {
            var rows = $(row);
            // rows.find('#per_product_costs_th #per_product_costs').each(function (i, row2) {
                // var rows2 = $(row2);
                var price_type = rows.find("#is_regular").val();
                if (price_type == 1) {
                    var regular_price       = rows.find('#regular_price').val();
                    regular_price           = parseFloat(regular_price);
                    regular_price           = regular_price.toFixed(3);
                    rows.find('.single_unit_value').val(regular_price);
                }else{
                    var installment_price   = rows.find('#installment_price').val();
                    installment_price       = parseFloat(installment_price);
                    installment_price       = installment_price.toFixed(3);
                    rows.find('.single_unit_value').val(installment_price);
                }
            // });
        })
    })
    /*Change Customer Address For Rach ROw*/
    $(document).on('click','[id*=address_no_]', function(){
        var customer_post_code  = $(this).data('customer_post_code');

        $('#append_tr > tr').each(function (i, row) {
            var rows = $(row);
            var order_status = rows.find('[id=order_status]').val();
            if (order_status <= 70) {
                if (customer_post_code >= 87000) {
                    var is_sm = 0;
                    var single_ss_cost = rows.find('#ss_price').val();
                    rows.find('.single_postage_value').val(single_ss_cost);
                }else{
                    var is_sm = 1;
                    var single_sm_cost = rows.find('#sm_price').val();
                    rows.find('.single_postage_value').val(single_sm_cost);
                }
            }
        });
    })
</script>
@if ($data->IS_RESELLER == 1)
    <script>
        $('[id*=address_btn]').remove();
    </script>
@endif
@if ($type == 'view')
    <script>
        $(":input").attr('disabled',true);
        $('.checkBox').remove();
        $('.Action').remove();
        $('[id*=checkbox_th]').remove();
        $('[id*=selfpickup_th]').remove();
        $('[id*=action_col]').remove();
        $('[id*=view_mode_add_pk]').remove();
        $('[id*=edit_address]').remove();
        $('[id=view_mode_add_pk]').fadeIn();
        $('[id=address_change_sender]').remove();
        $('[id=address_change_receiver]').remove();
    </script>
@endif
@endpush('custom_js')
