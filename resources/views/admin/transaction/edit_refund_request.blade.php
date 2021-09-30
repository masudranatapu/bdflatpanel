@extends('admin.layout.master')

@section('Payment','open')
@section('refund_request','active')

@section('title') Update Refund Request @endsection
@section('page-name') Update Refund Request @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('agent.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active">Update Refund Request</li>
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
                                    <h2>Refund Request For {{ $data['refund']->refundType() }}</h2>
                                    <p><strong>Property: </strong>{{ $data['refund']->TITLE }}</p>
                                    <p><strong>Property ID: </strong>{{ $data['refund']->PROPERTY_ID }}</p>
                                    <p><strong>Property Owner: </strong>{{ $data['refund']->OWNER_NAME }}</p>
                                    <p><strong>Requested
                                            At: </strong>{{ date('M d, Y h:i a', strtotime($data['refund']->REQUEST_AT)) }}
                                    </p>
                                    <p><strong>Purchase
                                            At: </strong>{{ date('M d, Y h:i a', strtotime($data['refund']->PURCHASE_DATE)) }}
                                    </p>
                                    <p><strong>Refund Request Reason: </strong>{{ $data['refund']->REQUEST_REASON }}</p>
                                    <p><strong>Comments: </strong>{{ $data['refund']->COMMENT }}</p>
                                    <p><strong>Request
                                            Amount: </strong>BDT {{ number_format($data['refund']->REQUEST_AMOUNT, 2) }}
                                    </p>
                                    @if($data['refund']->STATUS == 2)
                                        <p><strong>Status: </strong>Approved</p>
                                        <p><strong>Note: </strong>{{ $data['refund']->ADMIN_NOTE }}</p>
                                    @else
                                        <hr>
                                        {!! Form::open([ 'route' => ['admin.refund_request.update', $data['refund']->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="status"><strong>Status</strong></label>
                                                    <div class="controls">
                                                        {!! Form::select('status', [1 => 'Pending', 2 => 'Approved', 3 => 'Denied'], $data['refund']->STATUS, ['class'=>'form-control mb-1 select2', 'id' => 'status', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1]) !!}
                                                        {!! $errors->first('status', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 col-lg-6">
                                                <div class="form-group">
                                                    <label for="status"><strong>Note</strong></label>
                                                    <div class="controls">
                                                        {!! Form::textarea('note', $data['refund']->ADMIN_NOTE, ['class'=>'form-control mb-1', 'tabindex' => 2]) !!}
                                                        {!! $errors->first('status', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 col-lg-6">
                                                <div class="form-group">
                                                    <a href="{{ route('admin.refund_request')}}">
                                                        <button type="button" class="btn btn-warning mr-1">
                                                            <i class="ft-x"></i> Cancel
                                                        </button>
                                                    </a>
                                                    <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure?')">
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
