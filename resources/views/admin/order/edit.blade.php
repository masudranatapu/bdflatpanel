@extends('admin.layout.master')

@section('list_order','active')

@section('title') Order | Edit @endsection
@section('page-name') Order | Edit @endsection

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
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}">
<link rel="stylesheet" href="{{ asset('app-assets/lightgallery/dist/css/lightgallery.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">

<style>
    #scrollable-dropdown-menu .tt-menu {max-height: 260px;overflow-y: auto; width: 100%; border: 1px solid #333;border-radius: 5px; }
    #scrollable-dropdown-menu2 .tt-menu {max-height: 260px;overflow-y: auto;width: 100%;border: 1px solid #333;border-radius: 5px;}
    .twitter-typeahead{display: block !important;}
    #warehouse th, #availble_qty th {border: none;border-bottom: 1px solid #333;font-size: 12px;font-weight: normal;padding-bottom: 11px;}
    #book_qty th {border: none;font-size: 12px;font-weight: normal; padding-bottom: 5px; padding-top: 0;}
    .tt-hint { color: #999 !important;}
    #append_cus td{padding: 2px 5px;}
    #append_cus tr{width: 70%;}
    hr {margin-top: 1rem;margin-bottom: 1rem;border: 0;border-top-color: currentcolor; border-top-style: none;border-top-width: 0px;border-top: 1px solid #f2dade;}
    .icheckbox_square-red, .iradio_square-red {margin-top: 3px;}
    .bg-bundle{ background-color: #f9f0f2 !important;}
    .bg-bundle-item{ background-color: #f5edee !important;}
    #non-bundle-1{ border-top: 2px solid red !important;}
    #invoicetable tr td {vertical-align: middle;}
    .pc_in{font-weight: normal;font-size: 12px; display: inline-block;text-align:right; width: 40px;}
    #from_address_noneditable thead tr th,#delivery_address_noneditable thead tr th,#from_address_editable thead tr th,#delivery_address_editable thead tr th{padding: 0px 5px !important;}

    #delivery_address_noneditable thead tr th a,#from_address_noneditable thead tr th a,#from_address_editable thead tr th a,#delivery_address_editable thead tr th a {margin: 2px 0px !important; float: right;}
    #delivery_address_noneditable thead tr th span,#from_address_noneditable thead tr th span,#from_address_editable thead tr th span,#delivery_address_editable thead tr th span {margin: 8px 0px !important; float: left;}
    #sender_td_inline>div,#receiver_td_inline>div{min-height: 100px;}
    .modal-footer{display: block;}
</style>
@endpush('custom_css')
<?php
    use Carbon\Carbon;
    $booking_validity   = Config::get('static_array.booking_validity') ?? array();
    $booking_details    = $data['booking_details'];
    // $count_freight      = $data['count_freight'];
    $order              = $data['order'];
    $arrived_at         = $data['arrived_at'];
    $default_msg_sent_at = $data['default_msg_sent_at'];
    $data               = $data['booking'];
    $type               = request()->get('type') ?? '';
    $customer_id        = $data->getCustomer->PK_NO ?? $data->getReseller->PK_NO;
    $agent              = \Illuminate\Support\Facades\Auth::user()->F_AGENT_NO;
?>
@section('content')
@if ($agent == 0 || $agent == $data->F_BOOKING_SALES_AGENT_NO)

    @if( isset($order->DISPATCH_STATUS) && (($order->DISPATCH_STATUS == 40) || ($order->DISPATCH_STATUS == 35) || ($order->IS_ADMIN_HOLD == 1)))
        <div class="card card-success">
            <div class="card-header pb-0">
                <div class="row">
                    <div class="col-md-12">
                        @if($order->DISPATCH_STATUS >= 35)
                            <div class="alert bg-danger mb-2 text-center" role="alert" style="background: linear-gradient(to right, #2193b0 0%, #6dd5ed 100%);">
                                <div class="row" style="">
                                <div class="col-md-12" style="">
                                <h4 style="color:#fff;"><i class="icon la la-ban"></i> Alert!</h4>
                                @if (isset($order->dispatch[0]) && $order->dispatch[0]->IS_DISPATHED == 0)
                                <span style="font-size: 16px;">This order has been scheduled for App <strong>Dispatch @if( $order->DISPATCH_STATUS == 35) (Partial) @endif </strong>.</span>
                                @elseif($order->PICKUP_ID > 0)
                                <span style="font-size: 16px;">This order has been <strong>Dispatched (App)</strong>.</span>
                                @else
                                <span style="font-size: 16px;">This order has <strong>Dispatched @if( $order->DISPATCH_STATUS == 35) (Partial) @endif @if (isset($order->dispatch[0]) && $order->dispatch[0]->IS_DISPATHED == 2) (Partial) @endif</strong>.</span>
                                @endif
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
                @if ($order->DEFAULT_TYPE == 0)
                <a href="javascript:void(0)" id="edit_address{{ $address2[0]->PK_NO ?? '' }}" class="btn btn-xs btn-info mr-1" data-toggle="modal" data-target="#UpdateCustomerAddress" data-post_code="{{ $address2[0]->POST_CODE ?? '' }}" data-customeraddress="{{ $address2[0]->NAME ?? '' }}" data-address_no="{{ $address2[0]->PK_NO ?? '' }}" data-pk_no="{{ $data['pk_no'] ?? '' }}" data-addresstype="{{ $address2[0]->F_ADDRESS_TYPE_NO ?? '' }}" data-mobilenoadd="{{ $address2[0]->TEL_NO ?? '' }}" data-ad_1="{{ $address2[0]->ADDRESS_LINE_1 ?? '' }}" data-ad_2="{{ $address2[0]->ADDRESS_LINE_2 ?? '' }}" data-ad_3="{{ $address2[0]->ADDRESS_LINE_3 ?? '' }}" data-ad_4="{{ $address2[0]->ADDRESS_LINE_4 ?? '' }}" data-location="{{ $address2[0]->LOCATION ?? '' }}" data-country="{{ $address2[0]->country->PK_NO ?? '' }}" data-state="{{ $address2[0]->STATE ?? '' }}" data-city="{{ $address2[0]->CITY ?? '' }}" data-is_reseller="{{ $data->IS_RESELLER }}" style="" title="EDIT BILLING ADDRESS"><i class="la la-edit"></i>
                </a>
                @endif
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
                <h4 style="display: inline-block;width: 125px;">ORDER DATE:</h4> <input type='text' style="display: inline-block;width: 200px;" class="form-control pickadate" placeholder="Order Date" value="{{isset($data->RECONFIRM_TIME) ? date('d-m-Y',strtotime($data->RECONFIRM_TIME)) : date('d-m-Y')}}" name="order_date_" id="order_date_"/>
            </div>
        </div>
    </div>
    {!! Form::open([ 'route' => ['admin.bookingtoorder.update', $data->PK_NO], 'id'=>'booktoorderform', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}

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
                                                            @if ($order->DEFAULT_TYPE == 0)
                                                            <a href="javascript:void(0)" id="address_change_sender" class="btn btn-xs btn-primary ml-2" style="font-size: 12px">Edit Address</a>
                                                            @endif
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
                                                {{-- {!! Form::open([ 'route' => ['admin.senderaddress.update', $order->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!} --}}
                                                <thead>
                                                    <tr>
                                                        <th colspan="4" style="background: aliceblue;">
                                                            <span>Sender</span>
                                                            @if ($order->DEFAULT_TYPE == 0)
                                                            <a href="javascript:void(0)" id="update_btn{{ $data->PK_NO }}" class="btn btn-xs btn-primary ml-2" data-toggle="modal" data-target="#UpdateCustomerAddress" data-customer_address_id="{{$data->f_customer_address ?? 0}}" data-pk_no="{{ $data->PK_NO }}" data-order_status="{{ $data->ORDER_STATUS }}" data-type="sender" style="font-size: 12px" title="{{ $data->customer_name ?? 'NO ADDRESS ASSIGNED' }}">Address Book</a>
                                                            @endif
                                                        </th>

                                                    </tr>
                                                </thead>
                                                <tbody id="append_cus">
                                                        <tr id="from_name">
                                                            <td>
                                                                {!! Form::hidden('f_from_address', $order->F_FROM_ADDRESS ) !!}
                                                                <input style="width: 100%" class="form-control input-sm" name="from_name" type="text" value="{{ $order->FROM_NAME ?? '' }}" placeholder="Name"></td>
                                                        </tr>
                                                        <tr id="from_add_1">
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_add_1" type="text" value="{{ $order->FROM_ADDRESS_LINE_1 ?? '' }}" placeholder="Address line 1"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_add_2" type="text" value="{{ $order->FROM_ADDRESS_LINE_2 ?? '' }}" placeholder="Address line 2"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_add_3" type="text" value="{{ $order->FROM_ADDRESS_LINE_3 ?? '' }}" placeholder="Address line 3"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_add_4" type="text" value="{{ $order->FROM_ADDRESS_LINE_4 ?? '' }}" placeholder="Address line 4"></td>
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
                                                            <td><input style="width: 100%" class="form-control input-sm" name="from_mobile" type="text" value="{{ $order->FROM_MOBILE ?? '' }}" readonly></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan=2 style="border-top: 1px solid #e3ebf3">
                                                                {{-- <a href="javascript:void(0)" id="address_done_sender" class="btn btn-xs btn-info mr-2" style="font-size: 12px;float:right;">Done Editing</a> --}}
                                                                <button type="submit" class="btn btn-xs btn-info mr-2" style="font-size: 12px;float:right;" >Done Editing</button>
                                                            </td>
                                                        </tr>
                                                </tbody>
                                                {{ Form::close() }}
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
                                                            @if ($order->DEFAULT_TYPE == 0)
                                                            <a href="javascript:void(0)" id="address_change_receiver" class="btn btn-xs btn-primary ml-2" style="font-size: 12px">Edit Address</a>
                                                            @endif
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td id="receiver_td_inline">
                                                            <address>
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
                                                            </address>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            {{-- {!! Form::open([ 'route' => ['admin.receiveraddress.update', $order->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!} --}}
                                            {{-- {!! Form::open([ 'route' => ['admin.bookingtoorder.update', $data->PK_NO], 'id'=>'booktoorderform', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!} --}}

                                            <table class="table mb-0" id="delivery_address_editable"  style="display: none;">
                                                <thead>
                                                    <tr>
                                                        <th colspan="4" style="background: aliceblue;"><span>Receiver</span>
                                                            @if ($order->DEFAULT_TYPE == 0)
                                                            <a href="javascript:void(0)" id="update_btn{{ $data->PK_NO }}" class="btn btn-xs btn-primary ml-2" data-toggle="modal" data-target="#UpdateCustomerAddress" data-customer_address_id="{{$data->f_customer_address ?? 0}}" data-pk_no="{{ $data->PK_NO }}" data-order_status="{{ $data->ORDER_STATUS }}" data-type="receiver" style="font-size: 12px" title="{{ $data->customer_name ?? 'NO ADDRESS ASSIGNED' }}">Address Book</a>
                                                            @endif
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="append_cus">
                                                    <tr>
                                                        {!! Form::hidden('f_to_address',$order->F_TO_ADDRESS ) !!}
                                                        <td><input style="width: 100%" class="form-control input-sm" name="delivery_name" type="text" value="{{ $order->DELIVERY_NAME ?? '' }}"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input style="width: 100%" class="form-control input-sm" name="delivery_add_1" type="text" value="{{ $order->DELIVERY_ADDRESS_LINE_1 ?? '' }}" placeholder="Address line 1"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input style="width: 100%" class="form-control input-sm" name="delivery_add_2" type="text" value="{{ $order->DELIVERY_ADDRESS_LINE_2 ?? '' }}" placeholder="Address line 2"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input style="width: 100%" class="form-control input-sm" name="delivery_add_3" type="text" value="{{ $order->DELIVERY_ADDRESS_LINE_3 ?? '' }}" placeholder="Address line 3"></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input style="width: 100%" class="form-control input-sm" name="delivery_add_4" type="text" value="{{ $order->DELIVERY_ADDRESS_LINE_4 ?? '' }}" placeholder="Address line 4"></td>
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

                                                        <td><input style="width: 100%" class="form-control input-sm" name="delivery_mobile" type="text" value="{{ $order->DELIVERY_MOBILE ?? '' }}" readonly></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan=2 style="border-top: 1px solid #e3ebf3">
                                                            {{-- <a href="javascript:void(0)" id="address_done_receiver" class="btn btn-xs btn-info mr-2" style="font-size: 12px;float:right;">Done Editing</a> --}}
                                                            <button type="submit" class="btn btn-xs btn-info mr-2" style="font-size: 12px;float:right;" name="delievery_address">Done Editing</button>
                                                        </td>
                                                    </tr>
                                                </tbody>

                                            </table>
                                             {{-- {{ Form::close() }} --}}

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
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {{-- {!! Form::open([ 'route' => ['admin.bookingtoorder.update', $data->PK_NO], 'id'=>'booktoorderform', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!} --}}
                        {!! Form::hidden('order_date_',$data->RECONFIRM_TIME ? date('d-m-Y',strtotime($data->RECONFIRM_TIME)) : date('d-m-Y'), ['id'=>'new_order_date_']) !!}
                        {!! Form::hidden('booking_id',$data->PK_NO ?? null, ['id'=>'booking_id']) !!}
                        {!! Form::hidden('',$order->PK_NO ?? null, ['id'=>'order_id']) !!}
                        {!! Form::hidden('customer_id',$customer_id ?? null, ['id'=>'customer_id']) !!}
                        {!! Form::hidden('post_code',$customer_postcode->POST_CODE ?? 0, ['id'=>'post_code']) !!}
                        {!! Form::hidden('is_reseller',$data->IS_RESELLER ?? 0, ['id'=>'is_reseller']) !!}
                        {!! Form::hidden('',$type == 'view' ? 0 : 1,['id'=>'page_is_view']) !!}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-borde table-sm" id="invoicetable">
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
                                                <th class="checkBox" style="width: 10px;" title="PAY FOR THIS ITEM">Paid?</th>
                                                <th class="checkBox" style="width: 10px;" title="SELF PICKUP COD/RTC">Self?</th>
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
                                                <td id="final_qty"></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td id="ss_amount_final"></td>
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
                                                <td colspan="3">
                                                </td>
                                                <td id="freight_cost_total"></td>
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
                                                <td colspan="3">
                                                </td>
                                                <td id="postage_cost_final"></td>
                                                @if ($type == 'view')
                                                @else
                                                <td colspan="3"></td>
                                                @endif
                                            </tr>
                                            <tr style="text-align: center">
                                                <td></td>
                                                <td></td>
                                                <th class="text-right">Total</th>
                                                <td id="final_qty"></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                {!! Form::hidden('grand_total', 0, ['id' => 'grand_total']) !!}
                                                <th id="total_with_extra_costs"></th>
                                                @if ($type == 'view')
                                                @else
                                                <td colspan="3"></td>
                                                @endif
                                            </tr>
                                            <tr style="text-align: center">
                                                <td></td>
                                                <td></td>
                                                <td class="text-right">Discount</td>
                                                <td colspan="4"></td>
                                                <td><input type="number" name="discount_amount" style="width: 60px;text-align: center;" class="form-control input-sm ml-1" id="discount_amount" value="{{ number_format($data->DISCOUNT,2, '.', '') }}"></td>

                                                @if ($type == 'view')
                                                @else
                                                <td colspan="3"></td>
                                                @endif
                                            </tr>
                                            <tr style="text-align: center;color: red" id='penalty_fee_tr'>
                                                <td></td>
                                                <td></td>
                                                <th class="text-right">Penalty Fee</th>
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
                                                <th id="grand_final_qty"></th>
                                                <th></th>
                                                <th></th>
                                                {!! Form::hidden('final_qty_', 0, ['id' => 'final_qty_']) !!}
                                                <th></th>
                                                <th id="grand_total_ss"></th>

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
                        {{-- {!! Form::open([ 'route' => ['admin.default.order.penalty', $data->PK_NO], 'id'=>'penalty_order', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!} --}}
                        {{-- {{-- safsdfsdfsdfsdfsdf --}}
                        {{-- {{ Form::close() }}
                        {!! Form::open([ 'route' => ['admin.default.order.penalty', $data->PK_NO], 'id'=>'penalty_order', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!} --}}
                        <div class="card card-success">
                            <div class="card-header pb-0">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="bg-danger mb-2 text-center p-1" role="alert" style="background: linear-gradient(to right, #2193b0 0%, #6dd5ed 100%);">
                                            <div class="row" style="">
                                                <div class="col-md-12" style="">
                                                    <h4 style="color:#fff;"><i class="icon la la-ban"></i> Warning!</h4>
                                                    {{-- @if ($order->IS_DEFAULT == 1)
                                                    <span style="font-size: 16px;">ORDER PENALTY AMOUNT IS {{ number_format($data->PENALTY_FEE,2) ?? "0.00" }}
                                                    @else --}}
                                                    <span style="font-size: 16px;color:#fff;">Please Insert Penalty Amount
                                                    {{-- @endif --}}
                                                    </span>
                                                    <hr style="margin-bottom: 5px; border-top:2px soild #f2dade;">
                                                </div>
                                            </div>
                                            {{-- @if ($order->IS_DEFAULT == 0) --}}
                                            <div class="row mt-1">
                                                <div class="offset-md-4 col-md-4 mb-1">
                                                    <input type='text' style="display: inline-block;" class="form-control pickadate pickadate_grace" placeholder="Grace Time" value="{{ isset($order->GRACE_TIME) ? date('d-m-Y',strtotime($order->GRACE_TIME)) : '' }}" name="grace_time" id="grace_time"/>
                                                </div>
                                                <div class="offset-md-4 col-md-4 mb-1">
                                                    <div class="controls">
                                                        <select id="change_option" class="select2" name="change_option">
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
                                                {{-- <div class="offset-md-7 col-md-1">
                                                    <button name="panelty_btn" style="float: left;padding: 0.37rem 1rem; width: 100%" type="submit" onclick="return confirm('Are you sure ?')" class="btn btn-dark" value="" title="SUBMIT PENALTY"><i class="la la-check-square-o"></i> Submit</button>
                                                </div> --}}
                                            </div>
                                            {{-- @endif --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- {{ Form::close() }} --}}
                        @endif
                        {!! Form::hidden('', number_format($data->PENALTY_FEE,2) ?? "0.00", ['id'=>'penalty_amount']) !!}

                        @if( $order->DISPATCH_STATUS < 35 )
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-actions mt-10 text-center">
                                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1 pull-left"><i class="la la-backward" ></i> Back</a>

                                            @if($order->IS_CANCEL == 0)
                                            {{-- @if ($order->DEFAULT_TYPE == 0) --}}
                                            <button name="save_btn" type="submit" class="btn btn-primary save-inv-details mr-1" value="proceed_to_order" title="{{ $order != null ? 'Update Order' : 'Proceed to Order' }}"><i class="la la-check-square-o"></i> {{ $order != null ? 'Update Order' : 'Proceed to Order' }}</button>
                                            {{-- @endif --}}
                                                <button name="save_btn" type="submit" class="btn btn-success save-inv-details" value="proceed_to_order_make_payment" title=" Order and make payment"><i class="la la-check-square-o"></i> {{ $order != null ? 'Update Order & Take Payment' : 'Proceed to Order & Take Payment' }}</button>
                                            @endif

                                            @if($role == 1 && $order->IS_CANCEL == 2)
                                            <button type="button" class="btn btn-danger mr-1 pull-right" title="Request accept" data-toggle="modal" data-target="#orderCancelRequest">Order cancel request accept</button>
                                            @else
                                                {{-- <a href="{{ route('admin.order.cancel',['id' => $data->PK_NO,'type' => 'request'])}}" class="btn btn-danger mr-1 pull-right" title="Request for order cancel" onclick="return confirm('Are you sure?')"><i class="ft-x"></i> Cancel Order</a> --}}
                                                @if($order->IS_CANCEL == 0)
                                                    <button type="button" class="btn btn-danger mr-1 pull-right" title="Request for order cancel" data-toggle="modal" data-target="#orderCancelRequest">@if($role == 1) Order Cancel @else Request Cancel @endif</button>
                                                @endif

                                            @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    {{-- {!! Form::close() !!} --}}
                </div>
            </div>
        </div>
    </div>
</div>
<!--Order Cancel request-->
<?php
    $order_value    = $data->TOTAL_PRICE - $data->DISCOUNT ;
    $freight_cost   = $data->FREIGHT_COST;
    $postage_cost   = $data->POSTAGE_COST;
    $paid_amt       = $order->ORDER_ACTUAL_TOPUP;
    $penalty_fee    = $data->PENALTY_FEE;
    $cancel_fee     = $data->CANCEL_FEE;
    $due_amt        = $data->TOTAL_PRICE - $data->DISCOUNT - $order->ORDER_ACTUAL_TOPUP;
    $creadit        = $order->ORDER_ACTUAL_TOPUP - $penalty_fee - $cancel_fee;
?>
<div class="modal fade text-left" id="orderCancelRequest" tabindex="-1" role="dialog" aria-labelledby="orderCancelRequest" aria-hidden="true" >
    <div class="modal-dialog modal-md" role="document">

        <form method="POST" action="{{ route('admin.order.cancel',$data->PK_NO) }}" id="cancelOrderFrm">
            @csrf
            <div class="modal-content">
                <div class="modal-header ">
                    <h3 class="modal-title text-center" style="margin: 0 auto;">Cancel Order</h3>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="m-t-0 f16"><small>{{ $order->IS_RESELLER == 1 ? 'Reseller' : 'Customer' }} </small><b>{{ $data->getCustomer->NAME ?? $data->getReseller->NAME }} </b></h4>
                                <h4 class="m-t-0 f16"><small>Sales Agent : </small><b> {{ $data->BOOKING_SALES_AGENT_NAME }}</b></h4>
                                <h4 class="m-t-0 f16"><small>Order ID : </small><b>{{ $data->BOOKING_NO }}</b></h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <h4 class="m-t-0 f16"><small>Create By : </small> <b>{{ $data->createdBy->USERNAME ?? '' }}</b></h4>
                                <h4 class="m-t-0 f16"><small>Create At : </small><b>{{ date('d-m-Y h:i A',strtotime($data->SS_CREATED_ON)) }}</b></h4>
                                <h4 class="m-t-0 f16"><small>Order Date : </small><b>{{ date('d-m-Y',strtotime($data->RECONFIRM_TIME)) }}</b></h4>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped table-hover cost_summary2" style="width: 350px; margin: 0 auto;">
                                    <tbody>
                                    <tr>
                                        <td class="text-right">Freight : </td>
                                        <td class="text-right" style="width: 60%;"><small>(RM) </small> <span class="af">{{ number_format($freight_cost,2) }}</span> </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">Local Postage : </td>
                                        <td class="text-right" style="width: 60%;"><small>(RM) </small> <span class="lp">{{ number_format($postage_cost,2) }}</span></td>
                                    </tr>
                                    <tr class="bg-blue">
                                        <td class="text-right"><b>Total : </b></td>
                                        <th class="text-right" style="width: 60%;"><small>(RM) </small> <span class="tp">{{ number_format($order_value,2) }}</span></th>
                                    </tr>
                                    <tr class="bg-gray">
                                        <td class="text-right">Paid : </td>
                                        <td class="text-right" style="width: 60%;"><small>(RM) </small> <span class="paid" id="paid_amount">{{ number_format($paid_amt,2) }}</span></td>
                                    </tr>
                                    <tr class="bg-green">
                                        <td class="text-right text-white"><b>Balance : </b></td>
                                        <th class="text-right text-white" style="width: 60%;"><small>(RM) </small> <span class="balance">{{ number_format($due_amt,2) }}</span></th>

                                    </tr>
                                    <tr class="bg-red">
                                        <td class="text-right text-white"><b>Penalty : </b></td>
                                        <th class="text-right text-white" style="width: 60%;"><small>(RM) </small> <span class="pen">{{ number_format($penalty_fee,2)  }}</span></th>
                                    </tr>
                                    <tr class="bg-yellow">
                                        <td class="text-right text-white"><b>Cancellation Fee : </b></td>
                                        <th class="text-right text-white" style="width: 60%;"><small>(RM) </small> <span class="can_fee">{{ number_format($cancel_fee,2) }}</span></th>
                                    </tr>
                                    <tr class="bg-purple">
                                        <td class="text-right"><b>Credit : </b></td>
                                        <th class="text-right" style="width: 60%;"><small>(RM) </small> <span class="refund_ampunt">{{ number_format($creadit,2) }}</span></th>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12 text-center">
                                <a href="#" title="Click For Add Penalty Amount" data-toggle="collapse" data-target="#add_penalty" class="text-red" @if($role == 1)aria-expanded="true" @endif><b>Add Cancellation Fee</b></a>
                                <div id="add_penalty" class="collapse {{ $role == 1 ? 'show' : ''}}" style="margin-top: 20px;">
                                    <div class="form-group">
                                        <input type="number" class="form-control max_val_check" name="amount" placeholder="Cancellation Fee" id="cancellation_amount" required="" max="{{ $paid_amt }}" min="0" step="0.1" autocomplete="off" value="{{ $data->CANCEL_FEE }}">
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" name="note" placeholder="Note..." id="cancel_note">{{ $data->CANCEL_NOTE }}</textarea>
                                    </div>
                                </div>
                                <small class="err err-cancellation_amount" style="display: block;"></small>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left btn-sm" data-dismiss="modal" title="Click For Close">Close</button>
                    @if($order->IS_CANCEL == 0)
                        @if($role == 1)
                            <button type="submit" class="btn btn-sm btn-success pull-right" title="Click For Submit" data-request_type="cancel" name="submit" value="request_accept">Order Cancel Directly</button>
                        @else
                            <button type="submit" class="btn btn-sm btn-success pull-right" title="Click For Submit" data-request_type="cancel" name="submit" value="request">Submit Cancel Request</button>
                        @endif
                    @elseif($order->IS_CANCEL == 2)
                        <button type="submit" class="btn btn-sm btn-info pull-right" title="Click For Submit" data-request_type="cancel" name="submit" value="request_deny" >Cancel Request Deny</button>
                        <button type="submit" class="btn btn-sm btn-success pull-right" title="Click For Submit" data-request_type="cancel" name="submit" value="request_accept" >Cancel Request Accept</button>
                    @endif
                </div>
            </div>
        {!! Form::close() !!}
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
<script type="text/javascript" src="{{ asset('app-assets/pages/order_booking.js?v=1.7')}}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/country.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{ asset('app-assets/lightgallery/dist/js/lightgallery.min.js')}}"></script>
<script>
    $(document).on('submit',"#cancelOrderFrm",function(e){
   // e.preventDefault();
    return confirm('Are you sure?');

    });


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
