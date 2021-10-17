@extends('admin.layout.master')

@section('Order Management','open')
@section('new_search_booking','active')

@section('title') Search & Book @endsection
@section('page-name') Search & Book @endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin_role.breadcrumb_title') </a></li>
<li class="breadcrumb-item active">Search & Book </li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">
<link rel="stylesheet" href="{{ asset('app-assets/lightgallery/dist/css/lightgallery.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">

<style>
    #scrollable-dropdown-menu .tt-menu {max-height: 260px;Overflow-y: auto;width: 100%;border: 1px solid #333;border-radius: 5px;}
    #scrollable-dropdown-menu2 .tt-menu {max-height: 260px;overflow-y: auto;width: 100%;Border: 1px solid #333;border-radius: 5px;}
    .twitter-typeahead{display: block !important;}
    #warehouse th, #availble_qty th {border: none;border-bottom: 1px solid #333;font-size: 12px;font-weight: normal;Padding-bottom: 7px;padding-bottom: 11px;}
    #book_qty th {border: none;font-size: 12px;font-weight: normal;Padding-bottom: 5px;padding-top: 0;}
    .tt-hint {color: #999 !important;}
    .d-none{display: none;}
    .bg-bundle{ background-color: #f9f0f2 !important;}
    .bg-bundle-item{ background-color: #f5edee !important;}
    .bundle-summary{ border-bottom: 2px solid red !important;}
</style>

@endpush('custom_css')

<?php
    $booking_validity = Config::get('static_array.booking_validity') ?? array();
?>
@section('content')
<div class="card card-success min-height">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> New
        Search & Book</h4>
        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                <li><a data-action="close"><i class="ft-x"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse show">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    {!! Form::open([ 'route' => ['admin.booking.put',0], 'method' => 'post', 'id' => 'post_form', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                    {!! Form::hidden('customer_id',$info->PK_NO ?? null, ['id'=>'customer_id']) !!}
                    {!! Form::hidden('post_code', $info['POST_CODE'] ?? 0, ['id'=>'post_code']) !!}
                    {!! Form::hidden('customer_address',0,['id'=>'customer_address']) !!}
                    {!! Form::hidden('order_date_',0,['id'=>'order_date_']) !!}
                    {!! Form::hidden('booking_type','book',['id'=>'booking_type']) !!}

                    <div class="form-body">

                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group {!! $errors->has('ig_code') ? 'error' : '' !!}">
                                    <label>Product Keyword<span class="text-danger">*</span></label>
                                    <div class="controls" id="scrollable-dropdown-menu">
                                        <input type="search" name="q" id="product" class="form-control search-input" placeholder="Enter Product Keyword" autocomplete="off">
                                        {!! $errors->first('ig_code', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
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
                                                <th class="text-center" style="width: 70px;">@lang('tablehead.available_qty')</th>
                                                <th class="text-center" style="width: 70px;">@lang('tablehead.booking_qty')</th>
                                                <th class="text-center" style="width: 70px;">Price</th>
                                                <th class="text-center" style="width: 70px;">@lang('tablehead.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_tr">
                                        </tbody>
                                        <tfoot id="append_tfoot">
                                            <tr style="text-align: center">
                                                <td></td>
                                                <td>Sub Total</td>
                                                <td colspan="1">
                                                    <label>{!! Form::radio('price_type_all',1,true,['id'=>'regular_price_all']) !!} Regular</label>&nbsp;
                                                    <label>{!! Form::radio('price_type_all',0,false,['id'=>'installmnt_price_all']) !!} Installment</label>
                                                </td>
                                                <td></td>
                                                <td id="final_qty"></td>
                                                <td id="ss_amount_final"></td>
                                                <td></td>
                                            </tr>
                                            <tr style="text-align: center">
                                                <td></td>
                                                <td>Total Freight Cost</td>
                                                <td>
                                                    <div class="controls">
                                                        <select name="customer_preferred_all" id="customer_preferred_all" class="select2">
                                                            <option value="0">Select Air/Sea</option>
                                                            <option value="air">AIR</option>
                                                            <option value="sea">SEA</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td><input type="number" name="freight_cost_total" style="width: 60px;text-align: center;" class="form-control input-sm ml-2" id="amount_freight"></td>
                                                <td></td>
                                            </tr>
                                            <tr style="text-align: center">
                                                <td></td>
                                                <td>Postage Cost</td>
                                                <td style="pointer-events: none;display: none">
                                                    {!! Form::hidden('is_sm', 1, ['id'=>'is_sm']) !!}
                                                    <label>{!! Form::radio('postage',1,true,['id'=>'is_sm_cost1']) !!} SM COST</label>&nbsp;
                                                    <label>{!! Form::radio('postage',0,false,['id'=>'is_sm_cost2']) !!} SS COST</label>
                                                </td>
                                                <td colspan="3"></td>
                                                <td colspan="2" align="left" title="Postage cost will be set based on delivery address">
                                                    <input type="number" name="postage_regular_cost_final" style="width: 60px;text-align: center;display: none" class="form-control input-sm ml-2" id="postage_cost">
                                                    <div class="badge badge-pill  border-info info">To Be Confirmed</div>
                                                </td>
                                            </tr>
                                            <tr style="text-align: center">
                                                <th></th>
                                                <th>Grand Total</th>
                                                <th></th>
                                                <th></th>
                                                <th id="final_qty"></th>
                                                <th id="grand_total_ss"></th>
                                                {!! Form::hidden('grand_total', 0, ['id' => 'grand_total']) !!}
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="customer_section" style="display: none">
                            <div class="row">
                                <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group {!! $errors->has('agent') ? 'error' : '' !!}">
                                                    <div class="controls">
                                                        <label>{{trans('form.sales_agent')}}<span class="text-danger">*</span></label>
                                                        @if (Auth::user()->F_AGENT_NO == 0)
                                                        {!! Form::select('agent', $agent, null, ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'This field is required', 'id' => 'booking_under', 'placeholder' => 'Select Agent', 'required']) !!}
                                                        {!! $errors->first('agent', '<label class="help-block text-danger">:message</label>') !!}
                                                        @else
                                                        {!! Form::select('agent', $agent, Auth::user()->F_AGENT_NO ?? '', ['class'=>'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'id' => 'booking_under','style'=>'pointer-events:none']) !!}
                                                        {!! $errors->first('agent', '<label class="help-block text-danger">:message</label>') !!}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <br>
                                                <label>{{trans('form.customer_type')}}<span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>{!! Form::radio('booking_radio', 'customer', isset($info['type']) && $info['type'] == 'reseller' ? false : true , [ 'id' => 'radio_btn']) !!} {{trans('form.customer')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>{!! Form::radio('booking_radio','reseller', isset($info['type']) && $info['type'] == 'reseller' ? true : false ,[ 'id' => 'radio_btn2']) !!} {{trans('form.reseller')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="cus_details" style="{{ isset($info->PK_NO) ? '' : 'display: none;' }}border: 1px solid #c4c4c4;border-radius: 5px;">
                                        <div class="form-group mb-0 {!! $errors->has('book_customer') ? 'error' : '' !!}">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="4" style="background: aliceblue;">Customer Information</th>
                                                        </tr>
                                                        <tr>
                                                            <th><small><strong>Name</strong></small></th>
                                                            <th><small><strong>Phone</strong></small></th>
                                                            <th><small><strong>Email</strong></small></th>
                                                            <th><small><strong>Post Code</strong></small></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="append_cus">
                                                        @if (isset($info->PK_NO))
                                                        <td><small>{{ $info->NAME }}</small></td>
                                                        <td><small>{{ $info->MOBILE_NO }}</small></td>
                                                        <td><small>{{ $info->EMAIL }}</small></td>
                                                        <td><small>{{ $info->POST_CODE }}</small></td>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group {!! $errors->has('customer') ? 'error' : '' !!}">
                                                <label><span id="cusorresellername"></span> Name</label>
                                                <div class="controls" id="scrollable-dropdown-menu2">
                                                    <input type="search" name="q" id="book_customer" class="form-control search-input2" placeholder="Enter Customer Name" autocomplete="off" value="{{ $info->NAME ?? '' }}" required>
                                                    {!! $errors->first('book_customer', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="cus_country" class="col-md-2">
                                    <div class="form-group {!! $errors->has('country') ? 'error' : '' !!}">
                                        <label>{{trans('form.country')}}</label>
                                        <div class="controls">
                                            <select name="country" id="country_single" class="form-control mb-1 select2">
                                                @foreach ($country as $item)
                                                    <option value="{{ $item->PK_NO }}" data-dial_code="{{ $item->DIAL_CODE }}" {{ $item->PK_NO == 2 ? "selected='selected'" : '' }}>{{ $item->NAME }} ({{ $item->DIAL_CODE }})</option>
                                                @endforeach
                                            </select>
                                            {!! $errors->first('country', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div id="cus_mobile" class="col-md-2">
                                    <label>Mobile No.</label>
                                    <div class="{!! $errors->has('custom_mobile') ? 'error' : '' !!}" style="">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="phonecode">+60</span>
                                            </div>
                                            {!! Form::text('custom_mobile',null,[ 'class' => 'form-control', 'placeholder' => 'Mobile No.', 'id' => 'cus_mobile_input']) !!}
                                            {!! $errors->first('custom_mobile', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div id="cus_email" class="col-md-2">
                                    <label>Email</label>
                                    <div class="{!! $errors->has('custom_email') ? 'error' : '' !!}" style="">
                                        <div class="input-group">
                                            {!! Form::email('custom_email',null,[ 'class' => 'form-control', 'placeholder' => 'Email', 'id' => 'custom_email']) !!}
                                            {!! $errors->first('custom_email', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div id="cus_no_section" class="col-md-2">
                                    <div class="row">
                                        <div id="cus_add" class="col-md-3">
                                            <button type="button" title="ADD CUSTOMER" class="btn btn-primary btn-sm  mt-2" id="cus_no_input" data-url={{ route('admin.customer.store.booking')}}>
                                                <i class="la la-plus"></i>
                                            </button>
                                        </div>
                                        <div id="cus_no" class="col-md-7">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <div class="form-group {!! $errors->has('booking_note') ? 'error' : '' !!}">
                                        <label>{{trans('form.booking_note')}}</label>
                                        <div class="controls">

                                            {!! Form::textarea('booking_note', null, [ 'class' => 'form-control mb-1 summernote', 'placeholder' => 'Enter special note', 'tabindex' => 16, 'rows' => 3 ]) !!}
                                            {!! $errors->first('booking_note', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="controls">
                                            <label>{{trans('form.booking_validity')}}<span class="text-danger">*</span></label>
                                            {!! Form::select('booking_validity', $booking_validity, 24, ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'This field is required', 'id' => 'booking_validity']) !!}
                                            {!! $errors->first('booking_validity', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                    </div>
                    <div id="checkOfferSection"></div>
                    <div class="form-actions mt-10 text-center">
                        <a href="{{ url()->previous() }}" class="btn btn-success btn-min-width mr-1 "><i class="la la-backward" ></i> Back</a>
                        <button type="button" class="btn btn-primary save-inv-details mr-1 submit_button_book2"  value="book"><i class="la la-check-square-o"></i> Proceed to Booking </button>

                        <button type="button" class="btn btn-warning save-inv-details" id="book_2_order2"><i class="la la-check-square-o"></i> Book & Make Order </button>
                        <button type="button" class="btn btn-danger save-inv-details" id="check_offer"><i class="la la-check-square-o"></i> <span id="check_offer_txt">Check Offer</span> </button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>
</div>
@include('admin.booking._modal_html')
@endsection
@push('custom_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/country.js?v-1.01')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/cus_pro_search.js?v-1.04')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/book_order.js?v-1.66')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/js/common.js?v-1.04')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{ asset('app-assets/lightgallery/dist/js/lightgallery.min.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
@endpush('custom_js')
