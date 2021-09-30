@extends('admin.layout.master')
@section('Inventory','active')
@section('title')
    Create Inventory
@endsection
@section('page-name')
    Create Inventory
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('inventory.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">@lang('inventory.breadcrumb_sub_title')    </li>
@endsection

@section('content')

<section id="basic-form-layouts">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card card-success min-height">
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        {!! Form::open([ 'route' => 'product.inventory.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>@lang('form.inventory_form_field_name') </label>
                                                        <div class="controls">
                                                        {!! Form::text('inventory_name', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' =>__('form.inventory_required'), 'placeholder' => __('form.inventory_form_placeholder_name'), 'tabindex' => 1 ]) !!}
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('inventory_name'))
                                                         <span class="alert alert-danger">
                                                            <strong>{{ $errors->first('inventory_name') }}</strong>
                                                         </span>
                                                    @endif
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label for="bname">@lang('form.inventory_form_field_name_code') </label>
                                                            {!! Form::number('inventory_code', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' =>__('form.inventory_code_required'), 'placeholder' => __('form.inventory_form_placeholder_name_code'), 'tabindex' => 2 ]) !!}
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('inventory_code'))
                                                         <span class="alert alert-danger">
                                                            <strong>{{ $errors->first('inventory_code') }}</strong>
                                                         </span>
                                                    @endif
                                                    <div class="form-group">
                                                        <label>@lang('form.inventory_form_field_name_location') </label>
                                                        <div class="controls">
                                                        {!! Form::text('inventory_location', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' =>__('form.inventory_location_required'), 'placeholder' => __('form.inventory_form_field_name_location'), 'tabindex' => 3 ]) !!}
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('inventory_location'))
                                                         <span class="alert alert-danger">
                                                            <strong>{{ $errors->first('inventory_location') }}</strong>
                                                         </span>
                                                    @endif
                                                    <div class="form-group">
                                                        <label>@lang('form.inventory_form_field_name_address') </label>
                                                        <div class="controls">
                                                        {!! Form::text('inventory_address', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' =>__('form.inventory_address_required'), 'placeholder' => __('form.inventory_form_field_name_address'), 'tabindex' => 4 ]) !!}
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('inventory_address'))
                                                         <span class="alert alert-danger">
                                                            <strong>{{ $errors->first('inventory_address') }}</strong>
                                                         </span>
                                                    @endif

                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>@lang('form.inventory_form_field_name_Manager') </label>
                                                        <div class="controls">
                                                        {!! Form::text('inventory_manager', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' =>__('form.inventory_manager_required'), 'placeholder' => __('form.inventory_form_placeholder_name_Manager'), 'tabindex' => 5 ]) !!}
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('inventory_manager'))
                                                         <span class="alert alert-danger">
                                                            <strong>{{ $errors->first('inventory_manager') }}</strong>
                                                         </span>
                                                    @endif
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label for="bname">@lang('form.inventory_form_field_name_Contact') </label>
                                                            {!! Form::number('inventory_contact', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' =>__('form.inventory_contact_required'), 'placeholder' => __('form.inventory_form_placeholder_name_Contact'), 'tabindex' => 6 ]) !!}
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('inventory_contact'))
                                                         <span class="alert alert-danger">
                                                            <strong>{{ $errors->first('inventory_contact') }}</strong>
                                                         </span>
                                                    @endif
                                                    <div class="form-group">
                                                        <label>@lang('form.inventory_form_field_name_Country') </label>
                                                        <div class="controls">
                                                        {!! Form::text('inventory_country', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' =>__('form.inventory_country_required'), 'placeholder' => __('form.inventory_form_placeholder_name_Country'), 'tabindex' => 7 ]) !!}
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('inventory_country'))
                                                         <span class="alert alert-danger">
                                                            <strong>{{ $errors->first('inventory_country') }}</strong>
                                                         </span>
                                                    @endif
                                                    <div class="form-group">
                                                        <label>@lang('form.inventory_form_field_name_Country_No') </label>
                                                        <div class="controls">
                                                        {!! Form::text('inventory_country_no', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' =>__('form.inventory_country_no_required'), 'placeholder' => __('form.inventory_form_placeholder_name_Country_No'), 'tabindex' => 8 ]) !!}
                                                            <div class="help-block"></div>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('inventory_country_no'))
                                                         <span class="alert alert-danger">
                                                            <strong>{{ $errors->first('inventory_country_no') }}</strong>
                                                         </span>
                                                    @endif

                                                </div>


                                            </div>


                                            <div class="form-actions text-center mt-3">
                                                <a href="{{route('product.inventory.list')}}">
                                                    <button type="button" class="btn btn-warning mr-1">
                                                        <i class="ft-x"></i>@lang('form.btn_cancle')
                                                    </button>
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="la la-check-square-o"></i>@lang('form.btn_save')
                                                </button>
                                        {!! Form::close() !!}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
@endsection
