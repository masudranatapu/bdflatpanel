@extends('admin.layout.master')
@section('vendor','active')
@section('title')
    Vendor | Edit
@endsection
@section('page-name')
    Vendor Edit
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('admin.vendor') }}"> Vendor </a>
    </li>
    <li class="breadcrumb-item active">Vendor Edit
    </li>
@endsection
<!--push from page-->
@push('custom_css')
 <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
@endpush('custom_css')
@section('content')
    <div class="card card-success min-height">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-colored-form-control"> Edit Vendor</h4>
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
                {!! Form::open([ 'route' => ['admin.vendor.update', $vendor->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                @csrf
                <div class="form-body">
                    <h4 class="form-section"><i class="la la-eye"></i> Edit Vendor</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {!! $errors->has('code') ? 'error' : '' !!}">
                                <label>@lang('vendor.code')</label>
                                <div class="controls">
                                    {!! Form::text('code', $vendor->CODE,[ 'class' => 'form-control mb-1', 'placeholder' => 'Enter code', 'tabindex' => 1 ]) !!}
                                    {!! $errors->first('code', '<label class="help-block text-danger">:message</label>') !!}
                                </div>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                <label>@lang('vendor.name')<span class="text-danger">*</span></label>
                                <div class="controls">
                                    {!! Form::text('name', $vendor->NAME,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter name', 'tabindex' => 1 ]) !!}
                                    {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {!! $errors->has('phone') ? 'error' : '' !!}">
                                <label>@lang('vendor.phone')<span class="text-danger">*</span></label>
                                <div class="controls">
                                    {!! Form::text('phone', $vendor->PHONE,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter phone number', 'tabindex' => 1 ]) !!}
                                    {!! $errors->first('phone', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group {!! $errors->has('address') ? 'error' : '' !!}">
                                <label>@lang('vendor.address')<span class="text-danger">*</span></label>
                                <div class="controls">
                                    {!! Form::text('address', $vendor->ADDRESS,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter address', 'tabindex' => 1 ]) !!}
                                    {!! $errors->first('address', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {!! $errors->has('country') ? 'error' : '' !!}">
                                <label>@lang('vendor.country')<span class="text-danger">*</span></label>
                                <div class="controls">
                                    {!! Form::select('country', $country, $vendor->F_COUNTRY, [ 'class' => 'form-control mb-1 select2', 'placeholder' => 'Please select country', 'data-validation-required-message' => 'This field is required']) !!}
                                    {!! $errors->first('country', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group {!! $errors->has('has_loyality') ? 'error' : '' !!}">
                                <label>@lang('vendor.has_loyality')<span class="text-danger">*</span></label>
                                <div class="controls">
                                    {!! Form::select('has_loyality', ['1' => 'Yes', '0' => 'No'], $vendor->HAS_LOYALITY, [ 'class' => 'form-control mb-1 select2', 'placeholder' => 'Please select', 'data-validation-required-message' => 'This field is required']) !!}
                                    {!! $errors->first('has_loyality', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions text-center">
                        <a href="{{ route('admin.vendor')}}">
                            <button type="button" class="btn btn-warning btn-min-width box-shadow-5 mr-1 mb-1">
                                Cancel
                            </button>
                        </a>
                        <button type="submit" class="btn btn-success btn-min-width box-shadow-5 mr-1 mb-1">
                            Save changes
                        </button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
<!--push from page-->
@push('custom_js')
 <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
 <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
@endpush('custom_js')
