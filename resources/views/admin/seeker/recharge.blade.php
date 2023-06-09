@extends('admin.layout.master')

@section('Sales Agent','open')
@section('seeker_list','active')


@section('title') Recharge Balance @endsection
@section('page-name') Recharge Balance @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('agent.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active">Recharge Balance</li>
@endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{asset('/custom/css/custom.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/assets/css/image_upload/image-uploader.min.css')}}">
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" type="text/css"
          href="{{asset('/assets/css/forms/datepicker/bootstrap-datetimepicker.min.css')}}">
    <style>
        .ui-datepicker .ui-widget-content {
            background: #999 none;
        }
    </style>
@endpush

@push('custom_js')

    <!-- BEGIN: Data Table-->
    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
    <!-- END: Data Table-->
    <script src="{{asset('/assets/css/image_upload/image-uploader.min.js')}}"></script>
    <script src="{{asset('/assets/js/forms/datepicker/moment.min.js')}}"></script>
    <script src="{{asset('/assets/js/forms/datepicker/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        $('#imageFile').imageUploader();
        $('.datepicker').datetimepicker({
            icons:
                {
                    next: 'fa fa-angle-right',
                    previous: 'fa fa-angle-left'
                },
            format: 'DD-MM-YYYY'
        });
    </script>
@endpush

@php
    $roles = userRolePermissionArray();
    $payment_methods = $data['paymentMethods'] ?? [];
    $payment_type = [1 => 'Customer Payment', 2 => 'Bonus Payment'];
    $tabIndex = 0;
@endphp

@section('content')
    <div class="content-body min-height">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <p class="font-weight-bold">Credit Balance</p>
                                        <h3 class="font-weight-bold">
                                            BDT {{ number_format($data['seeker']->UNUSED_TOPUP ?? 0, 2) }}</h3>
                                    </div>
                                    {!! Form::open(['route' => ['admin.seeker.recharge', $data['seeker']->PK_NO],'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate', 'autocomplete' => 'off']) !!}
                                    <div class="form-group">
                                        {{ Form::label('amount','Recharge Amount') }}
                                        <div class="controls">
                                            {!! Form::number('amount', old('amount'), ['class'=>'form-control', 'placeholder'=>'0.00','data-validation-required-message' => 'This field is required', 'tabIndex' => ++$tabIndex]) !!}
                                            {!! $errors->first('amount', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('note','Payment Type') }}
                                        <div class="controls">
                                            {!! Form::select('payment_type', $payment_type ?? [], old('payment_type', 1), ['id' => 'paymentType', 'class'=>'form-control', 'placeholder'=>'Payment Type', 'rows' => 4, 'tabIndex' => ++$tabIndex]) !!}
                                            {!! $errors->first('payment_type', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                    <div class="bonus form-group">
                                        {{ Form::label('method','Payment Method') }}
                                        <div class="controls">
                                            {!! Form::select('method', $payment_methods ?? [], old('method'), ['id' => 'method', 'class'=>'form-control', 'placeholder'=>'Select Method', 'tabIndex' => ++$tabIndex]) !!}
                                            {!! $errors->first('method', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>

                                    <div class="bonus form-group">
                                        {{ Form::label('payment_account','Payment Account') }}
                                        <div class="controls">
                                            {!! Form::select('payment_account', [], old('payment_account'), ['id' => 'payment_account', 'class'=>'form-control', 'placeholder'=>'Select Method', 'tabIndex' => ++$tabIndex]) !!}
                                            {!! $errors->first('payment_account', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                    <div
                                        class=" form-group ">
                                        {{ Form::label('slip_number','Slip Number (optional)') }}
                                        <div class="controls">
                                            {!! Form::text('slip_number', old('slip_number'), ['class'=>'form-control', 'placeholder'=>'Slip Number', 'tabIndex' => ++$tabIndex]) !!}
                                            {!! $errors->first('slip_number', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                    <div class="bonus form-group">
                                        {{ Form::label('attachment','Attachment (optional)') }}
                                        <div class="controls">
                                            <div id="imageFile" style="padding-top: .5rem;"></div>
                                        </div>
                                        {!! $errors->first('images', '<label class="help-block text-danger">:message</label>') !!}
                                        {!! $errors->first('images.0', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('note','Note') }}
                                        <div class="controls">
                                            {!! Form::textarea('note', old('note'), ['class'=>'form-control', 'placeholder'=>'Note', 'rows' => 4, 'tabIndex' => ++$tabIndex]) !!}
                                            {!! $errors->first('note', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{ Form::label('payment_date','Payment Date') }}
                                        <div class="controls">
                                            {!! Form::text('payment_date', old('payment_date', date('Y-m-d')), ['class'=>'form-control datepicker', 'placeholder'=>'Payment Date', 'tabIndex' => ++$tabIndex, 'data-validation-required-message' => 'This field is required']) !!}
                                            {!! $errors->first('payment_date', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-6">
                                                <a href="{{ route('admin.seeker.payment', request()->route('id'))}}">
                                                    <button type="button" class="btn btn-warning mr-1">
                                                        <i class="ft-x"></i> Cancel
                                                    </button>
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="la la-check-square-o"></i> Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom_js')
    <script src="{{asset('/assets/js/forms/validation/jqBootstrapValidation.js')}}"></script>
    <script src="{{asset('/assets/js/forms/validation/form-validation.js')}}"></script>

    <script>
        $(document).ready(function () {
            let paymentType = $('#paymentType');
            let bonus = $('.bonus');
            let method = $('#method');
            let bkash = $('.bkash');
            let bank = $('.bank');

            setTimeout(() => {
                if (parseInt(paymentType.val()) === 4) {
                    bonus.hide();
                } else {
                    bonus.show();
                }

                if (parseInt(method.val()) === 4) {
                    getPaymentAccounts(4);
                    bank.show();
                    bkash.hide();
                } else if (parseInt(method.val()) !== 6) {
                    bkash.show();
                    bank.hide();
                }
            }, 100);

            paymentType.change(function () {
                if (parseInt($(this).val()) === 2) {
                    bonus.hide(100);
                } else {
                    bonus.show(100);
                }
                bank.hide();
                bkash.hide();
            });

            method.change(function () {
                getPaymentAccounts(parseInt($(this).val()));
                if (parseInt($(this).val()) === 4) {
                    bank.show(100);
                    bkash.hide(100);
                } else {
                    bank.hide(100);
                    bkash.show(100);
                }
            });

            function getPaymentAccounts(method_id) {
                $.ajax('{{ route('ajax.payment-account.list') . '?query=' }}' + method_id)
                    .done(res => {
                        $('#payment_account').html(res);
                    })
                    .fail(err => {
                        console.log(err)
                    });
            }
        });
    </script>
@endpush
