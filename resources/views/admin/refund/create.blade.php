@extends('admin.layout.master')

@section('Payment','open')
@section('view_refund','active')

@section('title') Refund to Customer @endsection
@section('page-name')Refund to Customer @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Refund to Customer</li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">
    <style>
        #scrollable-dropdown-menu .tt-menu {max-height: 260px;overflow-y: auto;width: 100%;border: 1px solid #333;border-radius: 5px;}
        .twitter-typeahead {display: block !important;}
        .select2-container{width: 100% !important;}
    </style>
@endpush('custom_css')

<?php
    $request_amt = $data['customer']->CUM_BALANCE;
    if($data['refund_request']){
        $request_amt = $data['refund_request']->MR_AMOUNT;
    }
    $payment_note = Config::get('static_array.refund_reason') ?? [];

?>

@section('content')
<div class="card card-success min-height">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control" style="text-transform: capitalize;">Refund to Customer</h4>
        @if($errors->any())
            {{ implode($errors->all(':message')) }}
        @endif
        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
        {{-- <div class="heading-elements text-warning text-bold">
            <p>Credit balance : RM {{ number_format($data['customer']->CUM_BALANCE,2) }} </p>
        </div> --}}

    </div>
    <div class="card-content collapse show">
        <div class="card-body">
            @if($data['refund_request'])
                {{-- <div class="row">
                    <div class="col-md-12">
                        <table class="table">
                            <tr>
                                <td>Requested Amount :</td>
                                <td> RM {{ number_format($data['refund_request']->MR_AMOUNT,2) }}</td>
                                <td>Request Date :</td>
                                <td>{{ date('F d, Y',strtotime($data['refund_request']->REQUEST_DATE)) }}</td>
                                <td> Requested By : </td>
                                <td class="text-uppercase">{{ $data['refund_request']->REQUEST_BY_NAME }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <br>
                <br> --}}
            @endif
            {!! Form::open([ 'route' => 'admin.paymentrefund.store', 'method' => 'post', 'class' => 'form-horizontal paymentEntryFrm prev_duplicat_frm', 'files'
            => true , 'novalidate']) !!}
                <input type="hidden" name="customer_id" value="{{ $data['customer']->PK_NO ?? '' }}" />
                <input type="hidden" name="type" value="{{ $data['type'] ?? '' }}" />
                <input type="hidden" name="refund_request_no" value="{{ request()->get('request_no') ?? '' }}" />

                <div class="form-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Customer Name :</th>
                                        <th>{{ $data['customer']->NAME ?? '' }}</th>
                                        <th>Credit balance :</th>
                                        <th>RM {{ number_format($data['customer']->CUM_BALANCE,2) }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="row">
                        @if($data['refund_request'])
                            <div class="col-md-6">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Refund Request Information</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Customer Bank Name</td>
                                            <td>
                                                <div class="form-group mb-0 {!! $errors->has('cust_bank_name1') ? 'error' : '' !!}">
                                                    <div class="controls">
                                                        {!! Form::select('cust_bank_name1', $data['mybank_list'] ?? [], $data['refund_request']->F_ACC_BANK_LIST_NO, [ 'class' => 'form-control','placeholder' => 'Customer bank name', 'tabindex' => 0 , 'disabled']) !!}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Account Name</td>
                                            <td>
                                                <div class="form-group mb-0 {!! $errors->has('cust_acc_name1') ? 'error' : '' !!}">
                                                    <div class="controls">
                                                        {!! Form::text('cust_acc_name1', $data['refund_request']->REQ_BANK_ACC_NAME,[ 'class' => 'form-control', 'placeholder' => 'Account name', 'tabindex' => 0 ,'disabled']) !!}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Account No</td>
                                            <td>
                                                <div class="form-group mb-0 {!! $errors->has('cust_acc_no1') ? 'error' : '' !!}">
                                                    <div class="controls">
                                                        {!! Form::text('cust_acc_no1', $data['refund_request']->REQ_BANK_ACC_NO,[ 'class' => 'form-control','placeholder' => 'Account number', 'tabindex' => 0 , 'disabled']) !!}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Request Amount</td>
                                            <td>
                                                <div class="form-group mb-0 {!! $errors->has('payment_note1') ? 'error' : '' !!}">
                                                    <div class="controls">
                                                        {!! Form::number('payment_amount1', $data['refund_request']->MR_AMOUNT ?? 0,[ 'class' => 'form-control ', 'placeholder' => 'Request amount (RM)', 'step' => '0.01', 'tabindex' => 0,'disabled']) !!}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Refund Reason</td>
                                            <td>
                                                <div class="form-group mb-0 {!! $errors->has('payment_note1') ? 'error' : '' !!}">
                                                    <div class="controls">
                                                        {!! Form::select('payment_note1', $payment_note, $data['refund_request']->REQUEST_NOTE, [ 'class' => 'form-control', 'placeholder' => 'Refund reason', 'tabindex' => 0,'disabled']) !!}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Request By</td>
                                            <td>
                                                <div class="form-group mb-0 {!! $errors->has('request_by1') ? 'error' : '' !!}">
                                                    <div class="controls">
                                                        {!! Form::text('request_by1', $data['refund_request']->REQUEST_BY_NAME ?? '',[ 'class' => 'form-control ', 'placeholder' => 'Request By', 'tabindex' => 0,'disabled']) !!}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Request At</td>
                                            <td>
                                                <div class="form-group mb-0 {!! $errors->has('request_date1') ? 'error' : '' !!}">
                                                    <div class="controls">
                                                        {!! Form::text('request_date1',date('d-m-Y',strtotime($data['refund_request']->REQUEST_DATE)),[ 'class' => 'form-control ', 'placeholder' =>'Request At', 'tabindex' => 0, 'disabled']) !!}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th colspan="2">Refund Information</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Bank</td>
                                        <td>
                                            <div class="form-group mb-0 {!! $errors->has('bank_no') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::select('bank_no', $data['mybank_list'] ?? [], $data['refund_request']->F_ACC_BANK_LIST_NO ?? null, [ 'class' => 'form-control  select3', 'data-validation-required-message' => 'This field is required','placeholder' => 'Customer bank name', 'tabindex' => 1 , 'id' => 'bank_no']) !!}
                                                    {!! $errors->first('bank_no', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Account Name</td>
                                        <td>
                                            <div class="form-group mb-0 {!! $errors->has('cust_acc_name') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::text('cust_acc_name', $data['refund_request']->REQ_BANK_ACC_NAME ?? null,[ 'class' => 'form-control', 'data-validation-required-message' => 'This field is required','placeholder' => 'Account name', 'tabindex' => 2 , 'id' => 'cust_acc_name']) !!}
                                                    {!! $errors->first('cust_acc_name', '<label  class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Account No</td>
                                        <td>
                                            <div class="form-group mb-0 {!! $errors->has('cust_acc_no') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::text('cust_acc_no', $data['refund_request']->REQ_BANK_ACC_NO ?? null,[ 'class' => 'form-control', 'data-validation-required-message' => 'This field is required','placeholder' => 'Account number', 'tabindex' => 3 , 'id' => 'cust_acc_no']) !!}
                                                    {!! $errors->first('cust_acc_no', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Refund Amount</td>
                                        <td>
                                            <div class="form-group mb-0 {!! $errors->has('payment_amount') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::number('payment_amount', $data['refund_request']->MR_AMOUNT ?? 0,[ 'class' => 'form-control ', 'data-validation-required-message' => 'This field is required', 'data-validation-max-message' => 'Invalid data', 'placeholder' => 'Request amount (RM)', 'tabindex' => 4 ,'min' => 1, 'max' => $data['customer']->CUM_BALANCE, 'id' => 'payment_amount', 'required', 'step' => '0.01']) !!}
                                                    {!! $errors->first('payment_amount', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Refund Reason</td>
                                        <td>
                                            <div class="form-group mb-0 {!! $errors->has('payment_note') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::select('payment_note', $payment_note, $data['refund_request']->REQUEST_NOTE ?? null, [ 'class' => 'form-control select2', 'placeholder' => 'Refund reason', 'tabindex' => 5, 'payment_note', 'id' => 'payment_note']) !!}
                                                    {!! $errors->first('payment_note', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Refund Currency</td>
                                        <td>
                                            <div class="form-group mb-0 {!! $errors->has('payment_currency_no') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::select('payment_currency_no',  $data['currency'] ?? [], 2, [ 'class' => 'form-control', 'placeholder' => 'Please select', 'data-validation-required-message' => 'This field is required', 'tabindex' => 6, 'id' => 'payment_currency_no' ]) !!}
                                                    {!! $errors->first('payment_currency_no', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Refund Account</td>
                                        <td>
                                            <div class="form-group mb-0 {!! $errors->has('payment_acc_no') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    <select class="form-control" name="payment_acc_no" id="payment_acc_no" data-validation-required-message="This field is required" tabindex="7">
                                                        <option value="">--select bank--</option>
                                                        @if(isset($data['payment_acc_no']) && count($data['payment_acc_no']) > 0 )
                                                            @foreach($data['payment_acc_no'] as $k => $bank)
                                                                @if( $bank->IS_COD == 0)
                                                                    <option value="{{ $bank->PK_NO }}" >{{ $bank->BANK_NAME .' ('.$bank->BANK_ACC_NAME.') ('.$bank->BANK_ACC_NO.')' }}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    {!! $errors->first('payment_acc_no', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Refund Dat</td>
                                        <td>
                                            <div class="form-group mb-0 {!! $errors->has('payment_date') ? 'error' : '' !!}">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <span class="la la-calendar-o"></span>
                                                        </span>
                                                    </div>
                                                    <input type='text' class="form-control pickadate datepicker" placeholder="Invoice Date"
                                                        value="{{date('d-m-Y')}}" name="payment_date" id="payment_date" tabindex="8" />
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Ref. Number/Slip Number</td>
                                        <td>
                                            <div class="form-group mb-0 {!! $errors->has('ref_number') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::text('ref_number', null,[ 'class' => 'form-control ',
                                                    'data-validation-required-message' => 'This field is required','placeholder' => 'Ref. number/slip number', 'tabindex' => 9 , 'id' => 'ref_number']) !!}
                                                    {!! $errors->first('ref_number', '<label
                                                        class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Refund By</td>
                                        <td>
                                            <div class="controls">
                                                {!! Form::text('paid_by', Auth::user()->USERNAME,[ 'class' => 'form-control ', 'placeholder' => 'Paid by', 'tabindex' => 10, 'id' => 'paid_by' ]) !!}
                                                {!! $errors->first('paid_by', '<label class="help-block text-danger">:message</label>')
                                                !!}
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{--

                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-group mb-0 {!! $errors->has('payment_currency_no') ? 'error' : '' !!}">
                                <label>Refund Currency<span class="text-danger">*</span></label>
                                <div class="controls">
                                    {!! Form::select('payment_currency_no',  $data['currency'] ?? [], 2, [ 'class' => 'form-control
                                    ', 'placeholder' => 'Please select', 'data-validation-required-message' => 'This field is required', 'tabindex' => 2, 'id' => 'payment_currency_no' ]) !!}

                                    {!! $errors->first('payment_currency_no', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">

                            <div class="form-group mb-0 {!! $errors->has('payment_acc_no') ? 'error' : '' !!}">
                                <label>Refund Account<span class="text-danger">*</span></label>
                                <div class="controls">
                                    <select class="form-control" name="payment_acc_no" id="payment_acc_no" data-validation-required-message="This field is required" tabindex="4">
                                        <option value="">--select bank--</option>
                                        @if(isset($data['payment_acc_no']) && count($data['payment_acc_no']) > 0 )
                                            @foreach($data['payment_acc_no'] as $k => $bank)
                                                @if( $bank->IS_COD == 0)
                                                    <option value="{{ $bank->PK_NO }}" >{{ $bank->BANK_NAME .' ('.$bank->BANK_ACC_NAME.') ('.$bank->BANK_ACC_NO.')' }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                    {!! $errors->first('payment_acc_no', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0 {!! $errors->has('payment_date') ? 'error' : '' !!}">
                                <label>Refund Date<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <span class="la la-calendar-o"></span>
                                        </span>
                                    </div>
                                    <input type='text' class="form-control pickadate datepicker" placeholder="Invoice Date"
                                        value="{{date('d-m-Y')}}" name="payment_date" id="payment_date" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0 {!! $errors->has('payment_amount') ? 'error' : '' !!}">
                                <label>@if($data['refund_request']) Request Amount @else Refund Amount @endif (RM)<span class="text-danger">*</span></label>
                                <div class="controls">
                                    {!! Form::number('payment_amount', $request_amt,[ 'class' => 'form-control ',
                                    'data-validation-required-message' => 'This field is required', 'data-validation-max-message' => 'Invalid data', 'placeholder' => 'Payment amount (RM)', 'tabindex' => 6 ,'min' => 1, 'max' => $data['customer']->CUM_BALANCE, 'id' => 'payment_amount', 'required', 'step' => '0.01']) !!}
                                    {!! $errors->first('payment_amount', '<label
                                        class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0 {!! $errors->has('ref_number') ? 'error' : '' !!}">
                                <label>Ref. Number/Slip Number<span class="text-danger">*</span></label>
                                <div class="controls">
                                    {!! Form::text('ref_number', null,[ 'class' => 'form-control ',
                                    'data-validation-required-message' => 'This field is required','placeholder' => 'Ref. number/slip number', 'tabindex' => 7 , 'id' => 'ref_number']) !!}
                                    {!! $errors->first('ref_number', '<label
                                        class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0 {!! $errors->has('paid_by') ? 'error' : '' !!}">
                                <label>Refund By</label>
                                <div class="controls">
                                    {!! Form::text('paid_by', null,[ 'class' => 'form-control ', 'placeholder' => 'Paid by', 'tabindex' => 8, 'id' => 'paid_by' ]) !!}
                                    {!! $errors->first('paid_by', '<label class="help-block text-danger">:message</label>')
                                    !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0 {!! $errors->has('payment_note') ? 'error' : '' !!}">
                                <label>Refund Note</label>
                                <div class="controls">
                                    {!! Form::text('payment_note', null,[ 'class' => 'form-control ', 'placeholder' =>
                                    'Paymet note', 'tabindex' => 9, 'payment_note', 'id' => 'payment_note']) !!}
                                    {!! $errors->first('payment_note', '<label
                                        class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group mb-0 {!! $errors->has('ref_number') ? 'error' : '' !!}">
                                <label>Ref. Number/Slip Number<span class="text-danger">*</span></label>
                                <div class="controls">
                                    {!! Form::text('ref_number', null,[ 'class' => 'form-control ',
                                    'data-validation-required-message' => 'This field is required','placeholder' => 'Ref. number/slip number', 'tabindex' => 7 , 'id' => 'ref_number']) !!}
                                    {!! $errors->first('ref_number', '<label
                                        class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>

                    </div>

                    --}}

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="input-field">
                                    <input type="file" name="payment_photo" class="form-control"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-actions text-center">

                                <a href="{{route('admin.customer.refund')}}" class="btn btn-warning mr-1"><i class="ft-x"></i> {{ trans('form.btn_cancle') }}</a>
                                <button type="submit" class="btn bg-primary bg-darken-1 text-white save_btn prev_duplicat">
                                <i class="la la-check-square-o"></i> {{ trans('form.btn_save') }} </button>
                            </div>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection


@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script>



$(".select2").select2({
  tags: true
});
    $('.pickadate').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
    });

    $('.paymentEntryFrm').submit(function(event){
        if(!confirm("Are you sure?")){
            event.preventDefault();
        }
    });
</script>

<script>
    $(document).on('change','#pay_pk_no', function(e){
        var pay_pk_no   = $(this).val();
        var currency    = $(this).find("option:selected").attr('data-currency');
        var paydate     = $(this).find("option:selected").attr('data-paydate');
        var slipno      = $(this).find("option:selected").attr('data-slipno');
        var paidby      = $(this).find("option:selected").attr('data-paidby');
        var paynote     = $(this).find("option:selected").attr('data-paynote');
        var amount      = $(this).find("option:selected").attr('data-amount');

        $('#payment_currency_no').val(currency);
        $('#payment_date').val(paydate);
        $('#ref_number').val(slipno);
        $('#paid_by').val(paidby);
        $('#payment_note').val(paynote);
        $('#payment_amount').val(amount);
    })




</script>
@endpush('custom_js')
