@extends('admin.layout.master')
@section('Address Type','active')
@section('title')
    Update Customer Address Type
@endsection
@section('page-name')
    Update Customer Address Type
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin_role.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">@lang('customer_address.breadcrumb_sub_title')    </li>
@endsection
<!--push from page-->
@push('custom_css')
 <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
@endpush('custom_css')


@section('content')

<section class=" min-height">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card card-success">
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        {!! Form::open([ 'route' => ['admin.address_type.update', $address->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}


                                        <div class="row">
                                            <div class="col-md-4 offset-4">
                                                <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                                    <label>@lang('form.name')<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::text('name', $address->NAME, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter product category name', 'tabindex' => 2 ]) !!}
                                                        {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                            <div class="form-actions text-center mt-3">
                                                <a href="{{ route('admin.address_type.list') }}" class="btn btn-warning mr-1" title="Cancel"> <i class="ft-x"></i>@lang('form.btn_cancle')</a>
                                                <button type="submit" title="Update" class="btn btn-primary"><i class="la la-check-square-o"></i>@lang('form.btn_update')</button>
                                            </div>

                                        {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
@endsection
