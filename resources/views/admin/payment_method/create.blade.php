@extends('admin.layout.master')
@section('Web','Open')
@section('payment_method','active')
@section('title')
    Create Payment Method
@endsection
@section('page-name')
    Create Payment Method
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Create Payment Method</li>
@endsection

<!--push from page-->
@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
@endpush

@php($tabIndex = 0)

@section('content')
    <div class="row">
        <div class="col-md-6">
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
                        {!! Form::open([ 'route' => 'admin.payment_method.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                        @csrf
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group {!! $errors->has('payment_method_name') ? 'error' : '' !!}">
                                        <label>Payment Method Name<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('payment_method_name', null,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Payment Method Name', 'tabIndex' => ++$tabIndex ]) !!}
                                            {!! $errors->first('payment_method_name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group {!! $errors->has('status') ? 'status' : '' !!}">
                                        <label>Status<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::select('status',['1'=>'Active','0'=>'Inactive'],null,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Select Status', 'tabIndex' => ++$tabIndex ]) !!}
                                            {!! $errors->first('status', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions mt-10 mb-3">
                            <a href="{{route('admin.payment_method.list')}}">
                                <button type="button" class="btn btn-warning mr-1">
                                    <i class="ft-x"></i> Cancel
                                </button>
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="la la-check-square-o"></i> Save
                            </button>
                        </div>
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
