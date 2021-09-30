@extends('admin.layout.master')

@section('Property Owner','open')
@section('owner_list','active')

@section('title') Property Owner Payment @endsection
@section('page-name') Property Owner Payment @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('invoice.breadcrumb_title')</a></li>
    <li class="breadcrumb-item active">Payment</li>
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
    $roles = userRolePermissionArray();
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
                                    <h3>Transaction</h3>
                                    <p><strong class="font-weight-bold">Payment Date: </strong>{{ $data['payment']->TRANSACTION_DATE }}</p>

                                    <p><strong class="font-weight-bold">Payment Type: </strong>
                                        @if( $data['payment']->TRANSACTION_TYPE == '1')
                                        {{ $data['payment']->payment->PAYMENT_TYPE == 1 ? 'Customer Payment' : 'Bonus Payment by BDFLAT' }}
                                        @elseif( $data['payment']->TRANSACTION_TYPE == '2')
                                        Listing Payment
                                        @endif
                                    </p>
                                    <p><strong class="font-weight-bold">Customer Name: </strong>{{ $data['payment']->customer->NAME }}</p>
                                    @if( $data['payment']->TRANSACTION_TYPE == '1')
                                    <p><strong class="font-weight-bold">Bank Name: </strong>{{ $data['payment']->payment->PAYMENT_BANK_NAME }}</p>

                                    <p><strong class="font-weight-bold">Account Name: </strong>{{ $data['payment']->payment->PAYMENT_ACCOUNT_NAME }}</p>
                                    <p><strong class="font-weight-bold">Account No.: </strong>{{ $data['payment']->payment->PAYMENT_BANK_ACC_NO }}</p>
                                    <p><strong class="font-weight-bold">Slip Number: </strong>{{ $data['payment']->payment->SLIP_NUMBER }}</p>
                                    <p><strong class="font-weight-bold">Attachment: </strong>@if($data['payment']->payment->ATTACHMENT_PATH)<a href="{{ asset($data['payment']->payment->ATTACHMENT_PATH) }}" target="_blank">Click to View</a>@else N/A @endif</p>
                                    <p><strong class="font-weight-bold">Note: </strong>{{ $data['payment']->payment->PAYMENT_NOTE ?? 'N/A' }}</p>
                                    <p><strong class="font-weight-bold">Payment Status: </strong>{{ $data['payment']->payment->PAYMENT_CONFIRMED_STATUS == 1 ? 'Confirmed' : 'Pending' }}</p>
                                    @endif
                                    <p><strong class="font-weight-bold">Amount: </strong>{{ number_format($data['payment']->AMOUNT, 2) }}</p>
                                </div>
                                <div class="col-12">
                                    <a href="{{ route('admin.owner.payment', $data['payment']->F_CUSTOMER_NO) }}" class="btn btn-info">Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="recharge" tabindex="-1" role="dialog" aria-labelledby="category_amount"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="category_amount">Recharge Balance</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['method' => 'admin.owner.payment.update', 'class' => 'form-horizontal', 'files' => true , 'novalidate' , 'id' => 'subcat_add_edit_frm' ]) !!}
                <div class="modal-body p-5">
                    <div class="form-group {!! $errors->has('amount') ? 'error' : '' !!}">
                        <label>Amount<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::number('amount', null, [ 'class' => 'form-control mb-1 subcat_amount', 'data-validation-required-message' => 'This field is required', 'placeholder' => '0.00', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('amount', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>

                    <div class="form-group {!! $errors->has('amount') ? 'error' : '' !!}">
                        <label>Note<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::textarea('note', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Type Note', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('note', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>

                    <div class="form-group text-center">
                        <input type="submit" class="btn btn-success submit-btn" value="Save" title="Save">
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
