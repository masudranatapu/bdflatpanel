@extends('admin.layout.master')

@section('System Settings','open')
@section('payment_account','active')

@section('title') Update payment account @endsection
@section('page-name') Update payment account @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('payment.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">@lang('payment.breadcrumb_sub_title')    </li>
@endsection

@php($tabIndex = 0)

@section('content')
<section id="basic-form-layouts">
    <div class="row match-height min-height">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-content collapse show">
                    <div class="card-body">
                        {!! Form::open([ 'route' => ['admin.payment_acc.update', $data['account']->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group {!! $errors->has('bank_name') ? 'error' : '' !!}">
                                        <label>Bank Name<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('bank_name', old('bank_name', $data['account']->BANK_NAME), [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter bank name', 'data-validation-required-message' => 'This field is required', 'tabIndex' => ++$tabIndex ]) !!}
                                            {!! $errors->first('bank_name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group {!! $errors->has('bank_acc_name') ? 'error' : '' !!}">
                                        <label>Bank Account Name<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('bank_acc_name', old('bank_acc_name', $data['account']->BANK_ACC_NAME), [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter bank account name', 'data-validation-required-message' => 'This field is required', 'tabIndex' => ++$tabIndex ]) !!}
                                            {!! $errors->first('bank_acc_name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group {!! $errors->has('bank_acc_no') ? 'error' : '' !!}">
                                        <label>Account Number<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('bank_acc_no', old('bank_acc_no', $data['account']->BANK_ACC_NO), [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter account number', 'data-validation-required-message' => 'This field is required', 'tabIndex' => ++$tabIndex ]) !!}
                                            {!! $errors->first('bank_acc_no', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group {!! $errors->has('payment_method') ? 'error' : '' !!}">
                                        <label>Payment Method<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::select('payment_method', $data['methods'] ?? [], old('payment_method', $data['account']->F_PAYMENT_METHOD_NO), [ 'class' => 'form-control mb-1', 'placeholder' => 'Select Payment Method', 'data-validation-required-message' => 'This field is required', 'tabIndex' => ++$tabIndex ]) !!}
                                            {!! $errors->first('payment_method', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group {!! $errors->has('status') ? 'error' : '' !!}">
                                        <label>Payment Method<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::select('status', $data['status'] ?? [], old('status', $data['account']->IS_ACTIVE), [ 'class' => 'form-control mb-1', 'placeholder' => 'Select Status', 'data-validation-required-message' => 'This field is required', 'tabIndex' => ++$tabIndex ]) !!}
                                            {!! $errors->first('status', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group {!! $errors->has('comment') ? 'error' : '' !!}">
                                        <label>Comment<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::textarea('comment', old('comment', $data['account']->COMMENTS), [ 'class' => 'form-control mb-1', 'placeholder' => 'Select Status', 'tabIndex' => ++$tabIndex ]) !!}
                                            {!! $errors->first('comment', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-actions text-center mt-3">
                                        <a href="{{ route('admin.payment_acc.list') }}" title="Cancel" class="btn btn-warning mr-1"><i class="ft-x"></i>@lang('form.btn_cancle') </a>
                                        <button type="submit" class="btn btn-primary" title="Save" title="Save"><i class="la la-check-square-o"></i>@lang('form.btn_save') </button>
                                    </div>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
