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
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}">
<link rel="stylesheet" href="{{ asset('app-assets/lightgallery/dist/css/lightgallery.min.css') }}">

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
    $agent              = \Illuminate\Support\Facades\Auth::user()->F_AGENT_NO;

?>

@section('content')

{!! Form::open([ 'route' => ['admin.senderaddress.update', $order->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true ,]) !!}
<input style="width: 100%" class="form-control input-sm" name="from_add_3" type="text" value="{{ $order->FROM_ADDRESS_LINE_2 ?? '' }}" placeholder="Address line 2">
<input type="submit" value="Save" name="Save"/>
 {{ Form::close() }}

 <br>
 <br>
 <br>
 <br>
 <br>
 <br>
 <h1>tttttttttttttttttttttttttttt</h1>

 {!! Form::open([ 'route' => ['admin.receiveraddress.update', $order->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true ,]) !!}
 <input style="width: 100%" class="form-control input-sm" name="from_add_2" type="text" value="{{ $order->FROM_ADDRESS_LINE_2 ?? '' }}" placeholder="Address line 2">
 <input type="submit" value="Update" name="Update"/>
{{ Form::close() }}

@endsection

