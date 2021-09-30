@extends('admin.layout.master')

@section('Payment','open')
@section('recharge_request','active')

@section('title') Update Recharge Request @endsection
@section('page-name') Update Recharge Request @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('agent.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active">Update Recharge Request</li>
@endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{asset('/custom/css/custom.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@push('custom_js')

    <!-- BEGIN: Data Table-->
    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
    <!-- END: Data Table-->
@endpush

@php
    $roles = userRolePermissionArray()
@endphp

@section('content')
    <div class="content-body min-height">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row  mb-2">
                                <div class="col-12">
                                    <p><strong>User ID: </strong>{{ $data['recharge']->C_CODE }}</p>
                                    <p><strong>Property {{ $data['recharge']->USER_TYPE == 1 ? 'Seeker' : 'Owner' }}
                                            Name: </strong>{{ $data['recharge']->C_NAME }}</p>
                                    <p><strong>Property {{ $data['recharge']->USER_TYPE == 1 ? 'Seeker' : 'Owner' }}
                                            Mobile: </strong>{{ $data['recharge']->C_MOBILE_NO }}</p>
                                    <p><strong>Payment
                                            Date: </strong>{{ date('M d, Y', strtotime($data['recharge']->PAYMENT_DATE)) }}
                                    </p>
                                    <p><strong>Payment Note: </strong>{{ $data['recharge']->PAYMENT_NOTE }}</p>
                                    <p><strong>Amount: </strong>BDT {{ number_format($data['recharge']->AMOUNT, 2) }}
                                    </p>
                                    <p><strong>Slip Number: </strong>{{ $data['recharge']->SLIP_NUMBER ?? 'N/A' }}</p>
                                    <p>
                                        <strong>Attachment: </strong>{{ $data['recharge']->ATTACHMENT_PATH ? asset($data['recharge']->ATTACHMENT_PATH) : 'N/A' }}
                                    </p>
                                    @if($data['recharge']->STATUS == 1)
                                        <p>
                                            <strong>Status: </strong> Approved
                                        </p>
                                    @else
                                        <hr>
                                        {!! Form::open([ 'route' => ['admin.recharge_request.update', $data['recharge']->PK_NO], 'method' => 'post', 'id' => 'recharge_form', 'class' => 'form-horizontal', 'files' => false , 'novalidate']) !!}
                                        <div class="row">
                                            <div class="col-md-6 col-lg-3">
                                                <div class="form-group">
                                                    <label for="status"><strong>Status</strong></label>
                                                    <div class="controls">
                                                        {!! Form::select('status', [0 => 'Pending', 1 => 'Approved', 2 => 'Denied'], $data['recharge']->STATUS, ['class'=>'form-control mb-1 select2', 'id' => 'status', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1]) !!}
                                                        {!! $errors->first('status', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 col-lg-6">
                                                <div class="form-group">
                                                    <a href="{{ route('admin.recharge_request')}}">
                                                        <button type="button" class="btn btn-warning mr-1">
                                                            <i class="ft-x"></i> Cancel
                                                        </button>
                                                    </a>
                                                    <button type="submit" class="btn btn-primary"
                                                            onclick="return confirm('Are you sure?')">
                                                        <i class="la la-check-square-o"></i> Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
