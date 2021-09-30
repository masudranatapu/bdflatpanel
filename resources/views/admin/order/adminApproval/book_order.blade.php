@extends('admin.layout.master')

@section('list_altered_order','active')


@section('title') @lang('order.edit_page_title') @endsection
@section('page-name') @lang('order.edit_page_title') @endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_dashboard_title') </a></li>
<li class="breadcrumb-item active">@lang('order.edit_page_breadcrumb_title_active') </li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('app-assets/file_upload/image-uploader.min.css')}}">
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/summernote.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/css/core/colors/palette-callout.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">
<link rel="stylesheet" href="{{ asset('app-assets/lightgallery/dist/css/lightgallery.min.css') }}">

<style>

    #scrollable-dropdown-menu .tt-menu {
      max-height: 260px;
      overflow-y: auto;
      width: 100%;
      border: 1px solid #333;
      border-radius: 5px;
    }
    #scrollable-dropdown-menu2 .tt-menu {
        max-height: 260px;
        overflow-y: auto;
        width: 100%;
        border: 1px solid #333;
        border-radius: 5px;

    }
    .twitter-typeahead{
        display: block !important;
    }
    #warehouse th, #availble_qty th {
        border: none;
        border-bottom: 1px solid #333;
        font-size: 12px;
        font-weight: normal;
        padding-bottom: 7px;
        padding-bottom: 11px;
    }
    #book_qty th {
        border: none;
        /* border-bottom: 1px solid #333; */
        font-size: 12px;
        font-weight: normal;
        padding-bottom: 5px;
        padding-top: 0;
    }
    .tt-hint {
        color: #999 !important;
    }
    #append_cus td{
        padding: 2px 5px;
    }
    #append_cus tr{
        width: 70%;
    }
    hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
        border-top-color: currentcolor;
        border-top-style: none;
        border-top-width: 0px;
    border-top: 1px solid #f2dade;
}
</style>

@endpush('custom_css')

<?php
$booking_validity   = Config::get('static_array.booking_validity') ?? array();
$booking_details    = $data['booking_details'];
// $count_freight      = $data['count_freight'];
$order              = $data['order'];
$data               = $data['booking'];
$type               = request()->get('type') ?? '';
$customer_id        = $data->getCustomer->PK_NO ?? $data->getReseller->PK_NO;
$auth_id = \Illuminate\Support\Facades\Auth::user()->PK_NO;
$role_id = \App\Models\AuthUserGroup::join('SA_USER','SA_USER.PK_NO','SA_USER_GROUP_USERS.F_USER_NO')
                    ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                    ->select('F_ROLE_NO')->where('F_USER_NO',$auth_id)->first();
?>

@section('content')
{{-- @if ($agent == 0 || $agent == $data->F_BOOKING_SALES_AGENT_NO) --}}
@if( isset($order->DISPATCH_STATUS) && (($order->DISPATCH_STATUS == 40) || ($order->DISPATCH_STATUS == 35) || ($order->IS_ADMIN_HOLD == 1)))
<div class="card card-success min-height">
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

<div class="card">
    {!! Form::open([ 'route' => ['admin.bookingtoorder.admin-approved', $data->PK_NO], 'id'=>'booktoorderform', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
    <div class="card-header pb-0">
        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> Order Details</h4>
        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>

        <div class="customer_info" style="text-align:center;margin-top:-24px;z-index:1;position:relative;">
            <h3 style="">
                <strong>{{ isset($data->getCustomer->PK_NO) ? 'Customer' : 'Reseller' }} Info</strong>
            </h3>
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
                <strong><a href="{{ isset($data->getCustomer->PK_NO) ? route('admin.customer.view',$data->getCustomer->PK_NO) : route('admin.reseller.edit',$data->getReseller->PK_NO) }}" target="_blank">Mob : {{ $data->getCustomer->country->DIAL_CODE ?? $data->getReseller->country->DIAL_CODE ?? '' }} <span id="mobile_no_">
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
                </span></a></strong>
            </h5>
        </div>
        <div class="heading-elements" style="z-index: 2;">
            <div style="display: inline-block" class="mr-1">
                <h4 style="">ORDER NO: <a href="javascript:void(0)">ORD-{{ $data->BOOKING_NO }}</a></h4>
                <h4 style="display: inline-block;width: 125px;">ORDER DATE:</h4> <input type='text' style="display: inline-block;width: 200px;" class="form-control pickadate" placeholder="Order Date" value="{{isset($data->RECONFIRM_TIME) ? date('d-m-Y',strtotime($data->RECONFIRM_TIME)) : date('d-m-Y')}}" name="order_date_" />
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
                        // echo '<pre>';
                        // echo '======================<br>';
                        // print_r($count_freight);
                        // echo '<br>======================<br>';
                        // exit();
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
                                                        <th colspan="4" style="background: aliceblue;">Sender
                                                            <a href="javascript:void(0)" id="address_change_sender" class="btn btn-xs btn-primary ml-2" style="font-size: 12px">Edit Address</a>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td id="sender_td_inline">
                                                            {!! !empty($order->FROM_NAME) ? $order->FROM_NAME."<br>" : '' !!}
                                                            {!! !empty($order->FROM_ADDRESS_LINE_1) ? $order->FROM_ADDRESS_LINE_1."<br>" : '' !!}
                                                            {!! !empty($order->FROM_ADDRESS_LINE_2) ? $order->FROM_ADDRESS_LINE_2."<br>" : '' !!}
                                                            {!! !empty($order->FROM_ADDRESS_LINE_3) ? $order->FROM_ADDRESS_LINE_3."<br>" : '' !!}
                                                            {!! !empty($order->FROM_ADDRESS_LINE_4) ? $order->FROM_ADDRESS_LINE_4."<br>" : '' !!}
                                                            {!! !empty($order->FROM_CITY) ? $order->FROM_CITY." " : '' !!}
                                                            {!! !empty($order->FROM_POSTCODE) ? $order->FROM_POSTCODE."<br>" : '' !!}
                                                            {!! !empty($order->FROM_STATE) ? $order->FROM_STATE : '' !!}
                                                            {!! !empty($order->FROM_COUNTRY) ? ', '.$order->FROM_COUNTRY."<br>" : '' !!}
                                                            <?php
                                                            if (!empty($order->FROM_MOBILE)) {
                                                                $from_mob1 = substr($order->FROM_MOBILE, 0, 2);
                                                                $from_mob2 = substr($order->FROM_MOBILE, 2, 3);
                                                                $from_mob3 = substr($order->FROM_MOBILE, 5,4);
                                                            }
                                                            ?>
                                                            {{ !empty($order->FROM_MOBILE) ? ($order->from_country->DIAL_CODE ?? '').' '.$from_mob1.' '.$from_mob2.' '.$from_mob3 : '' }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="table mb-0" id="from_address_editable" style="display: none">
                                                <thead>
                                                    <tr>
                                                        <th colspan="4" style="background: aliceblue;">Sender
                                                            {{-- @if ($data->IS_RESELLER == 0) --}}
                                                            <a href="javascript:void(0)" id="update_btn{{ $data->PK_NO }}" class="btn btn-xs btn-primary ml-2" data-toggle="modal" data-target="#UpdateCustomerAddress" data-customer_address_id="{{$data->f_customer_address ?? 0}}" data-pk_no="{{ $data->PK_NO }}" data-order_status="{{ $data->ORDER_STATUS }}" data-type="sender" style="font-size: 12px" title="{{ $data->customer_name ?? 'NO ADDRESS ASSIGNED' }}">Address Book</a>
                                                            {{-- @endif --}}
                                                        </th>
                                                        {!! Form::hidden('sender_f_country',$order->FROM_F_COUNTRY_NO ?? '' , ['id'=>'sender_f_country']) !!}
                                                    </tr>
                                                </thead>
                                                <tbody id="append_cus">
                                                        <tr id="from_name">
                                                            {{-- <td><small><strong>Name</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_name" type="text" value="{{ $order->FROM_NAME ?? '' }}"></td>

                                                        </tr>
                                                        <tr id="from_add_1">
                                                            {{-- <td><small><strong>Address 1</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_add_1" type="text" value="{{ $order->FROM_ADDRESS_LINE_1 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>
                                                            {{-- <td><small><strong>Address 2</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_add_2" type="text" value="{{ $order->FROM_ADDRESS_LINE_2 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>
                                                            {{-- <td><small><strong>Address 3</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_add_3" type="text" value="{{ $order->FROM_ADDRESS_LINE_3 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>
                                                            {{-- <td><small><strong>Address 4</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_add_4" type="text" value="{{ $order->FROM_ADDRESS_LINE_4 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>
                                                            {{-- <td><small><strong>City</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_city" type="text" value="{{ $order->FROM_CITY ?? '' }}" readonly></td>
                                                                {{-- <td><small><strong>Post Code</strong></small></td> --}}
                                                            <td style="border-top: 1px solid #e3ebf3;"><input style="width: 100%" class="form-control input-sm" name="from_post_code" type="text" value="{{ $order->FROM_POSTCODE ?? '' }}" readonly></td>
                                                        </tr>
                                                        <tr>
                                                            {{-- <td><small><strong>State</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_state" type="text" value="{{ $order->FROM_STATE ?? '' }}" readonly></td>
                                                            {{-- <td><small><strong>Country</strong></small></td> --}}
                                                            <td style="border-top: 1px solid #e3ebf3"><input style="width: 100%" class="form-control input-sm" name="from_country" type="text" value="{{ $order->FROM_COUNTRY ?? '' }}" readonly></td>
                                                        </tr>
                                                        <tr>
                                                            {{-- <td><small><strong>Mobile</strong></small></td> --}}
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
                                                        <th colspan="4" style="background: aliceblue;">Receiver
                                                            <a href="javascript:void(0)" id="address_change_receiver" class="btn btn-xs btn-primary ml-2" style="font-size: 12px">Edit Address</a>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td id="receiver_td_inline">
                                                            {!! !empty($order->DELIVERY_NAME) ? $order->DELIVERY_NAME : '' !!}
                                                            <span style="text-decoration-line: line-through">{!! $order->DELIVERY_NAME != $order->PREV_DELIVERY_NAME ? $order->PREV_DELIVERY_NAME : '' !!}</span><br>
                                                            {!! !empty($order->DELIVERY_ADDRESS_LINE_1) ? $order->DELIVERY_ADDRESS_LINE_1 : '' !!}
                                                            <span style="text-decoration-line: line-through">{!! $order->DELIVERY_ADDRESS_LINE_1 != $order->PREV_DELIVERY_ADDRESS_LINE_1 ? $order->PREV_DELIVERY_ADDRESS_LINE_1 ?? 'null' : '' !!}</span><br>
                                                            {!! !empty($order->DELIVERY_ADDRESS_LINE_2) ? $order->DELIVERY_ADDRESS_LINE_2 : '' !!}
                                                            <span style="text-decoration-line: line-through">{!! $order->DELIVERY_ADDRESS_LINE_2 != $order->PREV_DELIVERY_ADDRESS_LINE_2 ? $order->PREV_DELIVERY_ADDRESS_LINE_2 ?? 'null' : '' !!}</span><br>
                                                            {!! !empty($order->DELIVERY_ADDRESS_LINE_3) ? $order->DELIVERY_ADDRESS_LINE_3 : '' !!}
                                                            <span style="text-decoration-line: line-through">{!! $order->DELIVERY_ADDRESS_LINE_3 != $order->PREV_DELIVERY_ADDRESS_LINE_3 ? $order->PREV_DELIVERY_ADDRESS_LINE_3 ?? 'null' : '' !!}</span><br>
                                                            {!! !empty($order->DELIVERY_ADDRESS_LINE_4) ? $order->DELIVERY_ADDRESS_LINE_4 : '' !!}
                                                            <span style="text-decoration-line: line-through">{!! $order->DELIVERY_ADDRESS_LINE_4 != $order->PREV_DELIVERY_ADDRESS_LINE_4 ? $order->PREV_DELIVERY_ADDRESS_LINE_4 ?? 'null' : '' !!}</span><br>
                                                            {!! !empty($order->DELIVERY_CITY) ? $order->DELIVERY_CITY." " : '' !!}
                                                            {!! !empty($order->DELIVERY_POSTCODE) ? $order->DELIVERY_POSTCODE : '' !!}
                                                            <span style="text-decoration-line: line-through">{!! $order->DELIVERY_CITY != $order->PREV_DELIVERY_CITY ? $order->PREV_DELIVERY_CITY ?? 'null' : '' !!}</span>
                                                            <span style="text-decoration-line: line-through">{!! $order->DELIVERY_POSTCODE != $order->PREV_DELIVERY_POSTCODE ? $order->PREV_DELIVERY_POSTCODE ?? 'null' : '' !!}</span><br>
                                                            {!! !empty($order->DELIVERY_STATE) ? $order->DELIVERY_STATE : '' !!}
                                                            {!! !empty($order->DELIVERY_COUNTRY) ? ', '.$order->DELIVERY_COUNTRY : '' !!}
                                                            <span style="text-decoration-line: line-through">{!! $order->DELIVERY_STATE != $order->PREV_DELIVERY_STATE ? $order->PREV_DELIVERY_STATE ?? 'null' : '' !!}</span>
                                                            <span style="text-decoration-line: line-through">{!! $order->DELIVERY_COUNTRY != $order->PREV_DELIVERY_COUNTRY ? $order->PREV_DELIVERY_COUNTRY ?? 'null' : '' !!}</span><br>
                                                            <?php
                                                            if (!empty($order->DELIVERY_MOBILE)) {
                                                                $delivery_mob1 = substr($order->DELIVERY_MOBILE, 0, 2);
                                                                $delivery_mob2 = substr($order->DELIVERY_MOBILE, 2, 3);
                                                                $delivery_mob3 = substr($order->DELIVERY_MOBILE, 5,4);
                                                                $prev_delivery_mob1 = substr($order->PREV_DELIVERY_MOBILE, 0, 2);
                                                                $prev_delivery_mob2 = substr($order->PREV_DELIVERY_MOBILE, 2, 3);
                                                                $prev_delivery_mob3 = substr($order->PREV_DELIVERY_MOBILE, 5,4);
                                                            }
                                                            ?>
                                                            {{ !empty($order->DELIVERY_MOBILE) ? ($order->to_country->DIAL_CODE ?? '').' '.$delivery_mob1.' '.$delivery_mob2.' '.$delivery_mob3 : '' }}
                                                            @if ($order->DELIVERY_MOBILE != $order->PREV_DELIVERY_MOBILE)
                                                            <span style="text-decoration-line: line-through">{{ !empty($order->PREV_DELIVERY_MOBILE) ? ($order->to_country->DIAL_CODE ?? '').' '.$prev_delivery_mob1.' '.$prev_delivery_mob2.' '.$prev_delivery_mob3 : '' }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="table mb-0" id="delivery_address_editable" style="display: none">
                                                <thead>
                                                    <tr>
                                                        <th colspan="4" style="background: aliceblue;">Receiver
                                                            {{-- @if ($data->IS_RESELLER == 0) --}}
                                                            <a href="javascript:void(0)" id="update_btn{{ $data->PK_NO }}" class="btn btn-xs btn-primary ml-2" data-toggle="modal" data-target="#UpdateCustomerAddress" data-customer_address_id="{{$data->f_customer_address ?? 0}}" data-pk_no="{{ $data->PK_NO }}" data-order_status="{{ $data->ORDER_STATUS }}" data-type="receiver" style="font-size: 12px" title="{{ $data->customer_name ?? 'NO ADDRESS ASSIGNED' }}">Address Book</a>
                                                            {{-- @endif --}}
                                                        </th>
                                                        {!! Form::hidden('receiver_f_country',$order->DELIVERY_F_COUNTRY_NO ?? '' , ['id'=>'receiver_f_country']) !!}
                                                    </tr>
                                                </thead>
                                                <tbody id="append_cus">
                                                        <tr>
                                                            {{-- <td><small><strong>Name</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_name" type="text" value="{{ $order->DELIVERY_NAME ?? '' }}"></td>

                                                        </tr>
                                                        <tr">
                                                            {{-- <td><small><strong>Address 1</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_add_1" type="text" value="{{ $order->DELIVERY_ADDRESS_LINE_1 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>
                                                            {{-- <td><small><strong>Address 2</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_add_2" type="text" value="{{ $order->DELIVERY_ADDRESS_LINE_2 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>
                                                            {{-- <td><small><strong>Address 3</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_add_3" type="text" value="{{ $order->DELIVERY_ADDRESS_LINE_3 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>
                                                            {{-- <td><small><strong>Address 4</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_add_4" type="text" value="{{ $order->DELIVERY_ADDRESS_LINE_4 ?? '' }}"></td>
                                                        </tr>
                                                        <tr>
                                                            {{-- <td><small><strong>City</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_city" type="text" value="{{ $order->DELIVERY_CITY ?? '' }}" readonly></td>
                                                                {{-- <td><small><strong>Post Code</strong></small></td> --}}
                                                            <td style="border-top: 1px solid #e3ebf3;"><input style="width: 100%" class="form-control input-sm" name="delivery_post_code" type="text" value="{{ $order->DELIVERY_POSTCODE ?? '' }}" readonly></td>
                                                        </tr>
                                                        <tr>
                                                            {{-- <td><small><strong>State</strong></small></td> --}}
                                                            <td><input style="width: 100%" class="form-control input-sm" name="delivery_state" type="text" value="{{ $order->DELIVERY_STATE ?? '' }}" readonly></td>
                                                            {{-- <td><small><strong>Country</strong></small></td> --}}
                                                            <td style="border-top: 1px solid #e3ebf3;"><input style="width: 100%" class="form-control input-sm" name="delivery_country" type="text" value="{{ $order->DELIVERY_COUNTRY ?? '' }}" readonly></td>
                                                        </tr>
                                                        <tr>
                                                            {{-- <td><small><strong>Mobile</strong></small></td> --}}
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
                                    <p class="mt-1" id="order_value">{{ isset($data->TOTAL_PRICE) ? number_format($data->TOTAL_PRICE,2) : 0 }}</p>
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
                                    <p class="mt-1" id="due_amount">
                                        {{ number_format($due_amount,2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-borde table-sm" id="invoicetable">
                                <thead>
                                    <tr>
                                        <th style="width: 200px">@lang('tablehead.image')</th>
                                        <th style="width: 200px">@lang('tablehead.product_name')</th>
                                        <th class="" style="width: 70px;">@lang('tablehead.warehouse')</th>
                                        <th class="">Qty</th>
                                        <th class="" style="width: 70px;">Postage</th>
                                        <th class="" style="width: 30px;text-align: center">Freight</th>
                                        <th class="" style="width: 10px;">Unit Price</th>
                                        <th class="" style="width: 10px;">Line Total</th>
                                        <th class="checkBox" style="width: 10px;" title="PAY FOR THIS ITEM">Paid?</th>
                                        <th class="checkBox" style="width: 10px;" title="SELF PICKUP COD/RTC">Self?</th>
                                        {{-- <th class="" style="width: 70px;">Per Item</th> --}}
                                        <th class="Action" style="width: 70px;">@lang('tablehead.action')</th>
                                    </tr>
                                </thead>
                                <tbody id="append_tr">
                                    @if($booking_details && count($booking_details) > 0 )
                                    @foreach($booking_details as $key => $val)

                                        <?= $val->book_info ?>

                                    @endforeach
                                    @endif
                                </tbody>
                                <tfoot id="append_tfoot">
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td></td>
                                        <td>Sub Total</td>
                                        <td id="final_qty"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td id="ss_amount_final"></td>
                                        {{-- <td id="sm_amount_final"></td> --}}
                                        @if ($type == 'view')
                                        @else
                                        <td colspan="3"></td>
                                        @endif
                                    </tr>
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td></td>
                                        <td>Total Freight Cost</td>
                                        <td></td>
                                        <td colspan="3">
                                            {{-- <span id="given_freight_td">Given Freight: <span id="given_freight">{{ number_format($data->FREIGHT_COST,2, '.', '') }}</span></span> --}}
                                        </td>
                                        {{-- <td></td> --}}
                                        <td id="freight_cost_total"></td>
                                        {{-- <td><input type="number" name="freight_cost_total_ins" style="width: 60px;text-align: center;" class="form-control input-sm ml-1" id="amount_freight2"></td> --}}
                                        @if ($type == 'view')
                                        @else
                                        <td colspan="3"></td>
                                        @endif
                                    </tr>
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td></td>
                                        <td>Postage Cost</td>
                                        <td></td>
                                        <td colspan="3">
                                            {{-- <span id="given_postage_td">Given Postage: <span id="given_postage">{{ number_format($data->POSTAGE_COST,2, '.', '') }}</span></span> --}}
                                        </td>
                                        <td id="postage_cost_final"></td>
                                        {{-- <td><input type="number" name="postage_ins_cost_final" style="width: 60px;text-align: center;" class="form-control input-sm ml-1" id="postage_cost2"></td> --}}
                                        @if ($type == 'view')
                                        @else
                                        <td colspan="3"></td>
                                        @endif
                                    </tr>
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td></td>
                                        <th>Total</th>
                                        <td id="final_qty"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        {!! Form::hidden('grand_total', 0, ['id' => 'grand_total']) !!}
                                        <th id="total_with_extra_costs"></th>
                                        {{-- <td id="sm_amount_final"></td> --}}
                                        @if ($type == 'view')
                                        @else
                                        <td colspan="3"></td>
                                        @endif
                                    </tr>
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td></td>
                                        <td>Discount</td>
                                        <td colspan="4"></td>
                                        <td><input type="number" name="discount_amount" style="width: 60px;text-align: center;" class="form-control input-sm ml-1" id="discount_amount" value="{{ number_format($data->DISCOUNT,2, '.', '') }}"></td>
                                        {{-- <td><input type="number" name="postage_ins_cost_final" style="width: 60px;text-align: center;" class="form-control input-sm ml-1" id="postage_cost2"></td> --}}
                                        @if ($type == 'view')
                                        @else
                                        <td colspan="3"></td>
                                        @endif
                                    </tr>
                                    <tr style="text-align: center">
                                        <th></th>
                                        <th></th>
                                        <th>Grand Total</th>
                                        <th id="grand_final_qty"></th>
                                        <th></th>
                                        <th></th>
                                        {!! Form::hidden('final_qty_', 0, ['id' => 'final_qty_']) !!}
                                        <th></th>
                                        <th id="grand_total_ss"></th>
                                        {{-- <th id="grand_total_sm"></th> --}}
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
                    {!! Form::hidden('', number_format($data->PENALTY_FEE,2) ?? "0.00", ['id'=>'penalty_amount']) !!}
                </div>
            </div>
            <div class="form-actions mt-10 text-center">
                <a href="{{ route('admin.order.list')}}" class="btn btn-warning mr-1"><i class="ft-x"></i> @lang('form.btn_cancle')</a>

                @if ($role_id->F_ROLE_NO == 1)
                <button name="save_btn" type="submit" class="btn btn-danger save-inv-details mr-1" value="discard_alter_order"><i class="ft-alert-octagon"></i> Discard Changes</button>
                <button name="save_btn" type="submit" class="btn btn-primary save-inv-details mr-1" value="proceed_to_order"><i class="la la-check-square-o"></i> {{ $order != null ? 'Approve Order' : 'Proceed to Order' }}</button>
                @endif

                <button name="save_btn" type="submit" class="btn btn-success save-inv-details" value="proceed_to_order_make_payment"><i class="la la-check-square-o"></i> {{ $order != null ? 'Update Order & Take Payment' : 'Proceed to Order & Take Payment' }}</button>

            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
</div>
</div>
</div>
@include('admin.order._modal_html')
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
    $('.pickadate').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
        max:"<?php echo date('d-m-Y'); ?>",
    });
</script>
@if ($data->IS_RESELLER == 1)
    <script>
        $('[id*=address_btn]').remove();
    </script>
@endif
@endpush('custom_js')
