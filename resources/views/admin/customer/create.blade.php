@extends('admin.layout.master')

@section('Customer Management','open')
@section('customer_list','active')

@section('title') @lang('customer.add_new_customer') @endsection
@section('page-name') @lang('customer.add_new_customer') @endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">@lang('admin_role.breadcrumb_title')  </a></li>
<li class="breadcrumb-item active">@lang('customer.breadcrumb_sub_title')    </li>
@endsection

<?php

    $roles = userRolePermissionArray();
    $method_name = request()->route()->getActionMethod();

?>

<!--push from page-->
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('app-assets/file_upload/image-uploader.min.css')}}">
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endpush('custom_css')

<style>
    #scrollable-dropdown-menu2 .tt-menu {max-height: 260px;overflow-y: auto; width: 100%; border: 1px solid #333;border-radius: 5px;}
    #scrollable-dropdown-menu4 .tt-menu {max-height: 260px; overflow-y: auto; width: 100%; border: 1px solid #333;border-radius: 5px;}
    .twitter-typeahead{display: block !important;}
</style>

@section('content')
<div class="content-body min-height">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-sm card-success" >
                <!--?php vError($errors) ?-->
                <div class="card-content">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-top-border no-hover-bg nav-justified no-border">
                            <li class="nav-item">
                                <a class="nav-link active" id="productBasic-tab1" data-toggle="tab" href="#productBasic" aria-controls="productBasic" aria-expanded="true">@lang('customer.customer_info')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="linkIcon1-tab1" data-toggle="tab" href="#linkIcon1" aria-controls="linkIcon1" aria-expanded="false" >@lang('customer.customer_address')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="linkIconOpt1-tab1" data-toggle="tab" href="#linkIconOpt1" aria-controls="linkIconOpt1">@lang('customer.order_info')</a>
                            </li>
                        </ul>

                                    <!-- Customer Info -->

                        <div class="tab-content border-tab-content">
                            <div role="tabpanel" class="tab-pane active" id="productBasic" aria-labelledby="productBasic-tab1" aria-expanded="true">
                                {!! Form::open([ 'route' => 'admin.customer.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}


                                <div class="row">
                                    <div class="col-md-6">
                                            <div class="form-group {!! $errors->has('scustomer') ? 'error' : '' !!}">
                                                <label>{{trans('form.select_customer')}}<span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="controls">
                                                            <label>{!! Form::radio('scustomer', 'ukshop', true) !!} AZURAMART</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="controls">
                                                            <label>{!! Form::radio('scustomer','reseller') !!} {{trans('form.reseller')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group {!! $errors->has('agent') ? 'error' : '' !!}">
                                                            <div class="controls">
                                                                {!! Form::select('agent', $agent, null, ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'This field is required', 'id' => 'booking_under']) !!}
                                                                {!! $errors->first('agent', '<label class="help-block text-danger">:message</label>') !!}
                                                                {{-- <select name="agent" class="form-control mb-1 select2">
                                                                    <option value="0">AZURAMART</option>
                                                                </select> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('customername') ? 'error' : '' !!}">
                                            <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::text('customername',  null, ['class'=>'form-control mb-1', 'id' => 'customername', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter name', 'tabindex' => 1,  ]) !!}
                                                {!! $errors->first('customername', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('country3') ? 'error' : '' !!}">
                                            <label>{{trans('form.country')}}</label>
                                            <div class="controls">
                                                <select name="country3" id="country3" class="form-control mb-1 select2">
                                                    @foreach ($data['country'] as $item)
                                                        <option value="{{ $item->PK_NO }}" data-dial_code="{{ $item->DIAL_CODE }}" {{ $item->PK_NO == 2 ? "selected='selected'" : '' }}>{{ $item->NAME }} ({{ $item->DIAL_CODE }})</option>
                                                    @endforeach
                                                </select>
                                                {!! $errors->first('country3', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('mobileno') ? 'error' : '' !!}">
                                            <label>{{trans('form.mobile_no')}}<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::text('mobileno', null, [ 'class' => 'form-control mb-1',  'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Mobile No', 'tabindex' => 2]) !!}
                                                {!! $errors->first('mobileno', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-4">
                                        <label>{{trans('form.mobile_no')}}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="phonecode4">+60</span>
                                            </div>
                                            {!! Form::text('mobileno',null,[ 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter mobile No', 'tabindex' => 2]) !!}
                                            {!! $errors->first('mobileno', '<label class="help-block text-danger">:message</label>') !!}
                                            {{-- <input type="text" class="form-control" placeholder="Addon to Left" aria-describedby="basic-addon1"> --}}
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('altno') ? 'error' : '' !!}">
                                            <label>{{trans('form.alternative_no')}}</label>
                                            <div class="controls">
                                                {!! Form::text('altno',  null, ['class'=>'form-control mb-1', 'id' => 'altno',  'placeholder' => 'Enter Alternative mobile no', 'tabindex' => 3,  ]) !!}
                                                {!! $errors->first('altno', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('email') ? 'error' : '' !!}">
                                            <label>{{trans('form.email')}}</label>
                                            <div class="controls">
                                                {!! Form::email('email',  null, ['class'=>'form-control mb-1', 'id' => 'email', 'placeholder' => 'Enter email', 'tabindex' => 4,  ]) !!}
                                                {!! $errors->first('email', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('fbid') ? 'error' : '' !!}">
                                            <label>{{trans('form.fb_id')}}</label>
                                            <div class="controls">
                                                {!! Form::text('fbid',  null, ['class'=>'form-control mb-1', 'id' => 'fbid',  'placeholder' => 'Enter facebook id', 'tabindex' => 5,  ]) !!}
                                                {!! $errors->first('fbid', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('insid') ? 'error' : '' !!}">
                                            <label>{{trans('form.ins_id')}}</label>
                                            <div class="controls">
                                                {!! Form::text('insid',  null, ['class'=>'form-control mb-1', 'id' => 'insid',  'placeholder' => 'Enter instagram id', 'tabindex' => 6,  ]) !!}
                                                {!! $errors->first('insid', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('ukid') ? 'error' : '' !!}">
                                            <label>Azuramart Customer ID</label>
                                            <div class="controls">
                                                {!! Form::text('ukid',  null, ['class'=>'form-control mb-1', 'id' => 'ukid',  'placeholder' => 'Enter azuramart id', 'tabindex' => 6,  ]) !!}
                                                {!! $errors->first('ukid', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('ukpass') ? 'error' : '' !!}">
                                            <label>Azuramart Customer Password</label>
                                            <div class="controls">
                                                {!! Form::password('ukpass', ['class'=>'form-control mb-1', 'id' => 'ukpass',  'placeholder' => 'Enter azuramart pass', 'tabindex' => 6,  ]) !!}
                                                {!! $errors->first('ukpass', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Address -->

                             <div class="tab-pane" id="linkIcon1" role="tabpanel" aria-labelledby="linkIcon1-tab1" aria-expanded="false">
                             {{-- {!! Form::open([ 'route' => 'admin.customer-address.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!} --}}
                                @csrf

                                <div class="row">
                                    <div class="col-md-12">
                                        <h3><strong>Address</strong></h3>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('addresstype') ? 'error' : '' !!}">
                                            <label>{{trans('form.address_type')}}</label>
                                            <div class="controls">
                                            {!! Form::select('addresstype', $address, null, ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'This field is required', 'id' => 'addressCombo']) !!}
                                            {!! $errors->first('addresstype', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('customeraddress') ? 'error' : '' !!}">
                                            <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::text('customeraddress',  null, ['class'=>'form-control mb-1', 'id' => 'customeraddress', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter name', 'tabindex' => 3, 'required' ]) !!}
                                                {!! $errors->first('customeraddress', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                            <label>{{trans('form.mobile_no')}}</label>
                                            <div class="controls">
                                                {!! Form::text('mobilenoadd', null, [ 'class' => 'form-control mb-1','placeholder' => 'Enter Mobile No', 'tabindex' => 2]) !!}
                                                {!! $errors->first('mobilenoadd', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-4">
                                        <label>{{trans('form.mobile_no')}}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="phonecode2">+60</span>
                                            </div>
                                            {!! Form::text('mobilenoadd',null,[ 'class' => 'form-control', 'placeholder' => 'Enter mobile no.', 'id' => 'mobilenoadd']) !!}
                                            {!! $errors->first('mobilenoadd', '<label class="help-block text-danger">:message</label>') !!}
                                            {{-- <input type="text" class="form-control" placeholder="Addon to Left" aria-describedby="basic-addon1"> --}}
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('country') ? 'error' : '' !!}">
                                            <label>{{trans('form.country')}}</label>
                                            <div class="controls">
                                                {!! Form::select('country', $data['country'], 2, ['class'=>'form-control mb-1 select2',
                                                'data-validation-required-message' => 'Select Country', 'placeholder' => 'Select Country', 'id' => 'country','tabindex' =>
                                                1, 'data-url' => URL::to('customer_state' )]) !!}
                                                {!! $errors->first('country', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('country') ? 'error' : '' !!}">
                                            <label>{{trans('form.country')}}</label>
                                            <div class="controls">
                                                <select name="country" id="country" class="form-control mb-1 select2">
                                                    @foreach ($data['country'] as $item)
                                                        <option value="{{ $item->PK_NO }}" data-dial_code="{{ $item->DIAL_CODE }}" {{ $item->PK_NO == 2 ? "selected='selected'" : '' }}>{{ $item->NAME }} ({{ $item->DIAL_CODE }})</option>
                                                    @endforeach
                                                </select>
                                                {!! $errors->first('country', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('post_code') ? 'error' : '' !!}">
                                            <label>{{trans('form.post_code')}}</label>
                                            <div class="controls" id="scrollable-dropdown-menu2">
                                                <input type="search" name="post_code" id="post_code_" class="form-control search-input4" placeholder="Post code" autocomplete="off" required>
                                            </div>
                                            <div id="post_code_appended_div">
                                                {!! Form::hidden('post_code', 0, ['id'=>'post_code_hidden']) !!}
                                            </div>
                                            {!! $errors->first('post_code', '<label class="help-block text-danger">:message</label>') !!}
                                            {{-- <div class="controls">
                                                {!! Form::select('post_code', array(),  null, ['class'=>'form-control mb-1', 'id' => 'post_c',  'placeholder' => 'Select Post Code', 'tabindex' => 8,  ]) !!}
                                                {!! $errors->first('post_code', '<label class="help-block text-danger">:message</label>') !!}
                                            </div> --}}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('city') ? 'error' : '' !!}">
                                            <label>{{trans('form.city')}}</label>
                                            <div class="controls">
                                                {!! Form::select('city', array(), null, ['class'=>'form-control mb-1 select2',
                                                'data-validation-required-message' => 'Select City', 'id' => 'city','tabindex' =>
                                                1, 'placeholder' => 'Select city', 'data-url' => URL::to('customer_pCode') ]) !!}
                                                {!! $errors->first('city', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- STATE 1 --}}
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('state') ? 'error' : '' !!}">
                                            <label>{{trans('form.state')}}</label>
                                            <div class="controls">
                                                {!! Form::select('state', array(), null, ['class'=>'form-control mb-1 select2',
                                                'data-validation-required-message' => 'Select State', 'placeholder' => 'Select state', 'id' => 'state','tabindex' =>
                                                1, 'data-url' => URL::to('customer_city') ]) !!}
                                                {!! $errors->first('state', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('ad_1') ? 'error' : '' !!}">
                                            <label>{{trans('form.address_1')}}</label>
                                            <div class="controls">
                                                {!! Form::text('ad_1',  null, ['class'=>'form-control mb-1', 'id' => 'ad1',  'placeholder' => 'Enter address 1', 'tabindex' => 4,  ]) !!}
                                                {!! $errors->first('ad_1', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('ad_2') ? 'error' : '' !!}">
                                            <label>{{trans('form.address_2')}}</label>
                                            <div class="controls">
                                                {!! Form::text('ad_2',  null, ['class'=>'form-control mb-1', 'id' => 'ad3',  'placeholder' => 'Enter address 2', 'tabindex' => 5,  ]) !!}
                                                {!! $errors->first('ad_2', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('ad_3') ? 'error' : '' !!}">
                                            <label>{{trans('form.address_3')}}</label>
                                            <div class="controls">
                                                {!! Form::text('ad_3',  null, ['class'=>'form-control mb-1', 'id' => 'ad3',  'placeholder' => 'Enter address 3', 'tabindex' => 6,  ]) !!}
                                                {!! $errors->first('ad_3', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('ad_4') ? 'error' : '' !!}">
                                            <label>{{trans('form.address_4')}}</label>
                                            <div class="controls">
                                                {!! Form::text('ad_4',  null, ['class'=>'form-control mb-1', 'id' => 'ad3',  'placeholder' => 'Enter Address 4', 'tabindex' => 7,  ]) !!}
                                                {!! $errors->first('ad_4', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('location') ? 'error' : '' !!}">
                                            <label>{{trans('form.location')}}</label>
                                            <div class="controls">
                                                {!! Form::text('location',  null, ['class'=>'form-control mb-1', 'id' => 'location',  'placeholder' => 'Enter location', 'tabindex' => 11,  ]) !!}
                                                {!! $errors->first('location', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <br>
                                            <div class="controls">
                                                {{Form::hidden('same_as_add',0)}}
                                                <label style="float: right;"><input type="checkbox" name="same_as_add" id="checkbox1">  {{ trans('form.same_as_add') }}</label>
                                                {!! $errors->first('same_as_add', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Billing Address --}}

                                <div class="row" id="display_none" style="display: none">
                                    <div class="col-md-12">
                                        <h3><strong>Billing Address</strong></h3>
                                        <br>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('addresstype2') ? 'error' : '' !!}">
                                            <label>{{trans('form.address_type')}}</label>
                                            <div class="controls">
                                            {!! Form::select('addresstype2', $address, 2, ['class'=>'form-control mb-1 select2', 'id' => 'addresstype2']) !!}
                                            {!! $errors->first('addresstype2', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('customeraddress2') ? 'error' : '' !!}">
                                            <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::text('customeraddress2',  null, ['class'=>'form-control mb-1', 'id' => 'customeraddress2', 'placeholder' => 'Enter name', 'tabindex' => 3, '' ]) !!}
                                                {!! $errors->first('customeraddress2', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('mobilenoadd2') ? 'error' : '' !!}">
                                            <label>{{trans('form.mobile_no')}}</label>
                                            <div class="controls">
                                                {!! Form::text('mobilenoadd2', null, [ 'class' => 'form-control mb-1','placeholder' => 'Enter Mobile No', 'tabindex' => 2]) !!}
                                                {!! $errors->first('mobilenoadd2', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-4">
                                        <label>{{trans('form.mobile_no')}}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="phonecode3">+60</span>
                                            </div>
                                            {!! Form::text('mobilenoadd2',null,[ 'class' => 'form-control', 'placeholder' => 'Enter mobile no.', 'id' => 'mobilenoadd2']) !!}
                                            {!! $errors->first('mobilenoadd2', '<label class="help-block text-danger">:message</label>') !!}
                                            {{-- <input type="text" class="form-control" placeholder="Addon to Left" aria-describedby="basic-addon1"> --}}
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('country2') ? 'error' : '' !!}">
                                            <label>{{trans('form.country')}}</label>
                                            <div class="controls">
                                                {!! Form::select('country2', $data['country'], 2, ['class'=>'form-control mb-1 select2', 'id' => 'country2', 'placeholder' => 'Select Country', 'data-url' => URL::to('customer_state' ), 'tabindex' => 1 ]) !!}
                                                {!! $errors->first('country2', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('country2') ? 'error' : '' !!}">
                                            <label>{{trans('form.country')}}</label>
                                            <div class="controls">
                                                <select name="country2" id="country2" class="form-control mb-1 select2">
                                                    @foreach ($data['country'] as $item)
                                                        <option value="{{ $item->PK_NO }}" data-dial_code="{{ $item->DIAL_CODE }}" {{ $item->PK_NO == 2 ? "selected='selected'" : '' }}>{{ $item->NAME }} ({{ $item->DIAL_CODE }})</option>
                                                    @endforeach
                                                </select>
                                                {!! $errors->first('country2', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('post_code2') ? 'error' : '' !!}">
                                            <label>{{trans('form.post_code')}}</label>
                                            {{-- <div class="controls">
                                                {!! Form::select('post_code2', array(), null, ['class'=>'form-control mb-1', 'id' => 'post_c2',  'placeholder' => 'Select Post Code', 'tabindex' => 8,  ]) !!}
                                                {!! $errors->first('post_code2', '<label class="help-block text-danger">:message</label>') !!}
                                            </div> --}}
                                            <div class="controls" id="scrollable-dropdown-menu4">
                                                <input type="search" name="post_code2" id="post_code_2" class="form-control search-input8" placeholder="Post code" autocomplete="off" required>
                                                {!! $errors->first('post_code2', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                            <div id="post_code2_appended_div">
                                                {!! Form::hidden('post_code2', 0, ['id'=>'post_code2']) !!}
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('city2') ? 'error' : '' !!}">
                                            <label>{{trans('form.city')}}</label>
                                            <div class="controls">
                                                {!! Form::select('city2', array(), null, ['class'=>'form-control mb-1 select2',
                                                'data-validation-required-message' => 'Select City', 'id' => 'city2','tabindex' =>
                                                1, 'placeholder' => 'Select city', 'data-url' => URL::to('customer_pCode') ]) !!}
                                                {!! $errors->first('city', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('state2') ? 'error' : '' !!}">
                                            <label>{{trans('form.state')}}</label>
                                            <div class="controls">
                                                {!! Form::select('state2', array(), null, ['class'=>'form-control mb-1 select2',
                                                'data-validation-required-message' => 'Select City', 'placeholder' => 'Select state', 'id' => 'state2','tabindex' => 1, 'data-url' => URL::to('customer_city') ]) !!}
                                                {!! $errors->first('state2', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('ad_12') ? 'error' : '' !!}">
                                            <label>{{trans('form.address_1')}}</label>
                                            <div class="controls">
                                                {!! Form::text('ad_12',  null, ['class'=>'form-control mb-1', 'id' => 'ad1',  'placeholder' => 'Enter address 1', 'tabindex' => 4,  ]) !!}
                                                {!! $errors->first('ad_12', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('ad_22') ? 'error' : '' !!}">
                                            <label>{{trans('form.address_2')}}</label>
                                            <div class="controls">
                                                {!! Form::text('ad_22',  null, ['class'=>'form-control mb-1', 'id' => 'ad2',  'placeholder' => 'Enter address 2', 'tabindex' => 5,  ]) !!}
                                                {!! $errors->first('ad_22', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('ad_32') ? 'error' : '' !!}">
                                            <label>{{trans('form.address_3')}}</label>
                                            <div class="controls">
                                                {!! Form::text('ad_32',  null, ['class'=>'form-control mb-1', 'id' => 'ad3',  'placeholder' => 'Enter Address 3', 'tabindex' => 6,  ]) !!}
                                                {!! $errors->first('ad_32', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('ad_42') ? 'error' : '' !!}">
                                            <label>{{trans('form.address_4')}}</label>
                                            <div class="controls">
                                                {!! Form::text('ad_42',  null, ['class'=>'form-control mb-1', 'id' => 'ad4',  'placeholder' => 'Enter Address 4', 'tabindex' => 7,  ]) !!}
                                                {!! $errors->first('ad_42', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {!! $errors->has('location2') ? 'error' : '' !!}">
                                            <label>{{trans('form.location')}}</label>
                                            <div class="controls">
                                                {!! Form::text('location2',  null, ['class'=>'form-control mb-1', 'id' => 'location',  'placeholder' => 'Enter location', 'tabindex' => 11,  ]) !!}
                                                {!! $errors->first('location2', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-2 mb-2">
                                    <div class="form-actions text-center">
                                        <a href="{{route('admin.customer.list')}}" class="btn btn-warning mr-1" title="Cancel"><i class="ft-x"></i> {{ trans('form.btn_cancle') }}</a>
                                        <button type="submit" class="btn bg-primary bg-darken-1 text-white" title="Save">
                                         <i class="la la-check-square-o"></i> {{ trans('form.btn_save') }} </button>
                                     </div>
                                 </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="tab-pane" id="linkIconOpt1" role="tabpanel" aria-labelledby="linkIconOpt1-tab1" aria-expanded="false">
                                <p>Cookie icing tootsie roll cupcake jelly-o sesame snaps. Gummies cookie dragée cake jelly marzipan
                                    donut pie macaroon. Gingerbread powder chocolate cake icing. Cheesecake gummi bears ice cream
                                    marzipan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Recent Transactions -->
</div>
@endsection
<!--push from page-->
@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/customer.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/country.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script>
    $(document).on('change','#country3', function(){
        changeDialCodePopups4();
    });
    function changeDialCodePopups4() {
        var selected_country_dial = $('#country3').find(":selected").data('dial_code');
        console.log(selected_country_dial);
        $('#phonecode4').text(selected_country_dial);
        $('#mobileno').val('');
    }
</script>
 @endpush('custom_js')
