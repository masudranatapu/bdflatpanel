@extends('admin.layout.master')
@section('dashboard','active')
@section('title')
Color
@endsection
@section('page-name')
Product Color Page
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('color.breadcrumb_title')  </a></li>
<li class="breadcrumb-item active">@lang('color.breadcrumb_sub_title')</li>
@endsection
@php
$brand_combo = $brand ?? [];
@endphp

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
@endpush

@section('content')
        <div class="card card-success min-height">
            <div class="card-header">

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
                    {!! Form::open([ 'route' => 'admin.product.color.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                        @csrf
                        <div class="form-body">
                                    <div class="row">


                                <div class="col-md-6 offset-3">
                                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                        <label>@lang('form.name')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('name', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter product model name', 'tabindex' => 2 ]) !!}
                                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 offset-3">
                                    <div class="form-group {!! $errors->has('code') ? 'error' : '' !!}">
                                        <label>@lang('form.code')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('code', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter product model name', 'tabindex' => 2 ]) !!}
                                            {!! $errors->first('code', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 offset-3">
                                    <div class="form-group {!! $errors->has('brand') ? 'error' : '' !!}">
                                        <label>{{trans('form.brand')}}<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::select('brand', $brand_combo, null, ['class'=>'form-control mb-1 select2', 'id' => 'brand','data-validation-required-message' => 'This field is required', 'placeholder' => 'Select brand', 'tabindex' => 2, 'data-url' => URL::to('prod_model')]) !!}
                                            {!! $errors->first('brand', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="form-actions text-center">


                                <a type="button" href="{{ route('admin.product.color.list')}}" class="btn btn-warning mr-1">
                                    <i class="ft-x"></i>@lang('form.btn_cancle')
                                </a>

                                <button type="submit" class="btn btn-primary">

                                    <i class="la la-check-square-o"></i>@lang('form.btn_save')


                                </button>
                            </div>
                    {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('custom_js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>\
    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
@endpush
