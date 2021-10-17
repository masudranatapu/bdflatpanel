@extends('admin.layout.master')

@section('Payment','open')
@section('payment_list','active')

@section('title') Add Payment @endsection
@section('page-name')Add Payment @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Add Payment</li>
@endsection

@push('custom_css')

    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">
    <style>
        #scrollable-dropdown-menu .tt-menu {max-height: 260px;overflow-y: auto;width: 100%;border: 1px solid #333;border-radius: 5px;}
        .twitter-typeahead {display: block !important;}
    </style>
@endpush('custom_css')

@section('content')
<div class="card card-success min-height">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control" style="text-transform: capitalize;">{{ $data['type'] ?? 'Payment Entry' }} &nbsp;</h4>

        @if($errors->any())
        {{ implode($errors->all(':message')) }}
        @endif
        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            @if( request()->get('payfrom') == 'credit' )
                <a href="{{ route('admin.payment.create',[ 'id' => $data['customer']->PK_NO ?? '', 'type' => $data['type'] ?? '', 'payfrom' => 'new' ]) }}">New payment entry</a>

            @else
                <a href="{{ route('admin.payment.create',[ 'id' => $data['customer']->PK_NO ?? '', 'type' => $data['type'] ?? '', 'payfrom' => 'credit' ]) }}" title="Click for payment from customer balance">Payment from customer balance</a>
            @endif

        </div>
    </div>
    <div class="card-content collapse show">
        <div class="card-body">
            {!! Form::open([ 'route' => 'admin.payment.store', 'method' => 'post', 'class' => 'form-horizontal paymentEntryFrm prev_duplicat_frm', 'files'
            => true , 'novalidate']) !!}
            <input type="hidden" name="customer_id" value="{{ $data['customer']->PK_NO ?? '' }}" />
            <input type="hidden" name="type" value="{{ $data['type'] ?? '' }}" />
            <input type="hidden" name="payfrom" value="{{ request()->get('payfrom') ?? '' }}" />

            <div class="form-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group {!! $errors->has('customer') ? 'error' : '' !!}">
                            <div class="controls">
                                <label>
                                    @if ($data['type'] == 'customer')
                                    @lang('order.customer')
                                    @else
                                    @lang('order.reseller')
                                    @endif
                                    <span class="text-danger">*</span></label>
                                <div class="controls" id="scrollable-dropdown-menu">
                                    <input type="search" name="customer" id="customer" class="form-control"
                                        placeholder="Enter customer name" autocomplete="off" value="{{ $data['customer']->NAME ?? '' }}" readonly>
                                    {!! $errors->first('customer', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group {!! $errors->has('payment_currency_no') ? 'error' : '' !!}">
                            <label>Payment Currency<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::select('payment_currency_no',  $data['currency'] ?? [], 2, [ 'class' => 'form-control mb-1
                                ', 'placeholder' => 'Please select', 'data-validation-required-message' => 'This field is required', 'tabindex' => 2, 'id' => 'payment_currency_no' ]) !!}

                                {!! $errors->first('payment_currency_no', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        @if( request()->get('payfrom')  == 'credit' )
                        <div class="form-group {!! $errors->has('payment_acc_no') ? 'error' : '' !!}">
                            <label>Paymet From<span class="text-danger">*</span></label>
                            <div class="controls">
                                <select class="form-control mb-1" name="pay_pk_no" required id="pay_pk_no">
                                    <option value="">- select one -</option>
                                    @if(isset($data['remaining_balance']) && count($data['remaining_balance']) > 0 )
                                        @foreach($data['remaining_balance'] as $key => $balance )
                                            <option value="{{ $balance->PK_NO }}"
                                                data-currency="{{ $balance->F_PAYMENT_CURRENCY_NO }}"
                                                data-paydate="{{ date('d-m-Y',strtotime($balance->PAYMENT_DATE)) }}"
                                                data-slipno="{{ $balance->SLIP_NUMBER }}"
                                                data-paidby="{{ $balance->PAID_BY }}"
                                                data-paynote="{{ $balance->PAYMENT_NOTE }}"
                                                data-amount="{{ $balance->PAYMENT_REMAINING_MR }}"
                                                > {{ 'PID-'.$balance->bankTxn->CODE }} (RM {{ number_format($balance->PAYMENT_REMAINING_MR,2) }} )
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                {!! $errors->first('payment_acc_no', '<label class="help-block text-danger">:message</label>') !!}

                            </div>
                        </div>
                        @else
                        <div class="form-group {!! $errors->has('payment_acc_no') ? 'error' : '' !!}">
                            <label>Payment Account<span class="text-danger">*</span></label>
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
                        @endif
                    </div>
                    <div class="col-md-3">
                        <div class="form-group {!! $errors->has('payment_date') ? 'error' : '' !!}">
                            <label>Payment Date<span class="text-danger">*</span></label>
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
                        <div class="form-group {!! $errors->has('payment_amount') ? 'error' : '' !!}">
                            <label>Payment Amount (RM)<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::number('payment_amount', null,[ 'class' => 'form-control mb-1',
                                'data-validation-required-message' => 'This field is required','placeholder' => 'Payment amount (RM)', 'tabindex' => 6 ,'min' => 0, 'id' => 'payment_amount', 'step' => '0.01',  request()->get('payfrom')  == 'credit' ? 'readonly' : '']) !!}
                                {!! $errors->first('payment_amount', '<label
                                    class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group {!! $errors->has('ref_number') ? 'error' : '' !!}">
                            <label>Ref. Number/Slip Number<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::text('ref_number', null,[ 'class' => 'form-control mb-1',
                                'data-validation-required-message' => 'This field is required','placeholder' => 'Ref. number/slip number', 'tabindex' => 7 , 'id' => 'ref_number']) !!}
                                {!! $errors->first('ref_number', '<label
                                    class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group {!! $errors->has('paid_by') ? 'error' : '' !!}">
                            <label>Paid By</label>
                            <div class="controls">
                                {!! Form::text('paid_by', null,[ 'class' => 'form-control mb-1', 'placeholder' => 'Paid by', 'tabindex' => 8, 'id' => 'paid_by' ]) !!}
                                {!! $errors->first('paid_by', '<label class="help-block text-danger">:message</label>')
                                !!}
                            </div>
                        </div>
                    </div>



                    <div class="col-md-3">
                        <div class="form-group {!! $errors->has('payment_note') ? 'error' : '' !!}">
                            <label>Paymet Note</label>
                            <div class="controls">
                                {!! Form::text('payment_note', null,[ 'class' => 'form-control mb-1', 'placeholder' =>
                                'Paymet note', 'tabindex' => 9, 'payment_note', 'id' => 'payment_note']) !!}
                                {!! $errors->first('payment_note', '<label
                                    class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>


                </div>

                @if( request()->get('payfrom')  != 'credit' )
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="input-field">
                                <input type="file" name="payment_photo" class="form-control"/>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered  table-sm" >
                            <thead>
                                <tr>
                                    <th style="width: 50px; " class="text-center">SL#</th>
                                    <th style="width: 100px; ">Order ID</th>
                                    <th>Product Name</th>
                                    <th style="width: 150px; ">Order Date</th>
                                    <th style="width: 150px; ">Original amount (RM)</th>
                                    <th style="width: 126px; ">Due Amount (RM)</th>
                                    <th style="width: 80px; ">Payment (RM)</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if(isset($data['due_orders']) && count($data['due_orders']) > 0 )
                                @foreach($data['due_orders'] as $key =>  $order )
                                <?php
                                if($order->BOOKING_PK_NO){
                                     $variants  = getVariantName($order->BOOKING_PK_NO);
                                }else{
                                    $variants = [];
                                }
                                ?>
                                <tr class="row_class">
                                    <td class="text-center">
                                        {{ $key+1 }}
                                        <input type="hidden" name="order_id[]" value="{{ $order->ORDER_PK_NO }}" />
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.booking_to_order.book-order',['id' => $order->BOOKING_PK_NO]) }}?type=view" class="link" target="_blank">ORD-{{ $order->BOOKING_NO }}</a>
                                    </td>

                                    <td class="text-left">
                                        <ul class="pl-0 list-unstyled">
                                            @if( (!empty($variants) ) && (count($variants) > 0 ) )
                                                @foreach($variants as $row)
                                                    <li>{{ $row->PRD_VARINAT_NAME }} ({{ $row->ORD_QTY }})</li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </td>
                                    <td class="text-center">

                                        @if($order->CONFIRM_TIME)
                                        <span title="Confirm time">{{ date('Y-m-d',strtotime($order->CONFIRM_TIME)) }}</span>
                                        @else
                                        <span title="Booking time">{{ date('Y-m-d',strtotime($order->BOOKING_TIME)) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right"><span>{{ number_format($order->TOTAL_PRICE,2) }}</span></td>

                                    <td class="text-right">
                                        <span>{{ number_format($order->TOTAL_PRICE - $order->ORDER_BUFFER_TOPUP - $order->DISCOUNT,2)  }}</span>
                                    </td>
                                    <td style="width:80px;">
                                        <input type="number" class="form-control  text-right number-only due_amt max_limit" value="" name="split_pay[]" data-max_amount="{{ $order->TOTAL_PRICE - $order->ORDER_BUFFER_TOPUP - $order->DISCOUNT }}" style="width:80px; display: inline; float: right;" min="0">
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center text-warning" colspan="7">No order for payment </td>
                                </tr>
                                @endif
                            </tbody>

                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-actions text-center">
                            <a href="{{route('admin.customer.list')}}" class="btn btn-warning mr-1"><i class="ft-x"></i> {{ trans('form.btn_cancle') }}</a>
                            <button type="submit" class="btn bg-primary bg-darken-1 text-white save_btn prev_duplicat">
                             <i class="la la-check-square-o"></i> {{ trans('form.btn_save') }} </button>
                         </div>
                     </div>
                </div>
            </div>
            {!! Form::close() !!}
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
        </div>
    </div>
</div>
@endsection


@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script>
    $('.pickadate').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
    });

    $('.paymentEntryFrm').submit(function(event){
        var sum_due_amt = 0;
        $('.due_amt').each(function() {
            sum_due_amt += Number($(this).val());
        });
        var payment_amount  = Number($('#payment_amount').val());
            if(payment_amount != sum_due_amt){
                if(!confirm("Are you sure you want to submit the payment without assigning it to order ?")){
                    event.preventDefault();
                }

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

    $(document).on('input','.due_amt',function(){
        var payment_amount  = Number($('#payment_amount').val());
        var split_amt       = $(this).val();
        var max_amt         = $(this).data('max_amount');
        var row_sum         = 0;

        $(".due_amt").each(function(){
            row_sum += Number($(this).val());
        });

        if(payment_amount < row_sum ) {
            $(this).val('');
        }
    })

    $(document).on('input','.max_limit',function(){
        var max_val   = Number($(this).data('max_amount'));
        var this_val  = Number($(this).val());
        if(this_val > max_val){
            $(this).val(max_val);
        }
    })
/*
    jQuery(document).ready(function (jQuery) {
        var engine = new Bloodhound({
            remote: {
                url: '/autocomplete_booking?q=%QUERY%&type=SLS_CUSTOMERS',
                wildcard: '%QUERY%'
            },
            datumTokenizer: Bloodhound.tokenizers.whitespace('q'),
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });

        $(".search-input").typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            source: engine.ttAdapter(),
            display: 'NAME',
            limit: 20,
            templates: {
                empty: [
                    '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                ],
                header: [
                    '<div class="list-group search-results-dropdown">'
                ],
                suggestion: function (data) {
                    return '<span class="list-group-item" style="cursor: pointer;">' + data.NAME +
                        '</span>'
                }
            }
        });
    });
    */

</script>
@endpush('custom_js')
