@extends('admin.layout.master')

@section('PaymentPayment','open')
@section('payment_verification','active')

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <style>
        .f12{font-size: 12px !important;}
        tr.active_row, tr.active_row .active_txt {color: yellow !important;}
        .active_row {background-color: red !important; color: #FFF !important;}
        .card-header-sm{padding: 1rem 1.5rem;}
        .card-header-sm .heading-elements, .card-header .heading-elements-toggle{top: 12px;}
        .fix-table{ height: 450px;overflow-x: hidden; overflow-y: auto;}
        .active_txt {color: red;}
        .col-sm-12{padding: 5px;}
        .table.table-sm th, .table.table-sm td {padding: 0.5rem .2rem;}
    </style>
@endpush

@section('title') UKSHOP | Payment Verification by Bank statement @endsection
@section('page-name') Payment Verification @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('payment.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active">Bank statement </li>
@endsection

@php
    $roles = userRolePermissionArray();
    $status = request()->get('status') ?? '';
@endphp
@section('content')
<!-- Alternative pagination table -->
    <div class="content-body min-height">
        <div class="card card-success">
            <div class="card-body">
                <div class="row">
                    <div class="col-6" style="padding: 5px;">
                        <div class="card box-shadow-0 border-success">
                            <div class="card-header card-head-inverse bg-success card-header-sm">
                                <h4 class="card-title text-center text-white">Customer Payments</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body card-dashboard" style="padding: 10px 0px;">
                                    <div class="table-responsive fix-table">
                                        <table class="table table-striped table-bordered cust50 table-sm " id="customer_payment_table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 100px;" class="text-center nosort"  >Date</th>
                                                    <th class="text-left" style="width: 120px;">Customer Name</th>
                                                    <th class="text-left">Tx. Ref</th>
                                                    <th style="width: 80px;" class="text-left" >Amt(RM)</th>
                                                    <th style="width: 150px;" class="text-center">Bank</th>

                                                </tr>
                                            </thead>
                                            <tbody>

                                                @if(isset($data['payments']) && count($data['payments']) )
                                                    @foreach($data['payments'] as $i => $pay )
                                                    <?php
                                                    $slip_number    = $pay->RE_SLIP_NUMBER ?? $pay->CU_SLIP_NUMBER;
                                                    $payment_note   = $pay->RE_PAYMENT_NOTE ?? $pay->CU_PAYMENT_NOTE;
                                                    $paid_by        = $pay->RE_PAID_BY ?? $pay->CU_PAID_BY;
                                                    ?>

                                                        <tr class="cp_row c-p ">
                                                            <td  style="width: 100px;" class="cp_pay_date text-center">
                                                                {{ date('d-M-Y',strtotime($pay->TXN_DATE)) }}
                                                                <input type="hidden" class="acc_bank_txn_no" value="{{ $pay->PK_NO }}" name="acc_bank_txn_no" />
                                                            </td>
                                                            <td style="width: 120px;" class="cp_cust_name" >{{ $pay->CUSTOMER_NAME ?? $pay->RESELLER_NAME }}</td>
                                                            <td class="cp_paid_by">
                                                                @if($payment_note)
                                                                    {{ $payment_note }}
                                                                @endif

                                                                @if($paid_by)
                                                                    {{ $paid_by }}
                                                                @endif
                                                            </td>
                                                            <td  style="width: 80px;" class="text-right cp_pay_amount">
                                                                <span class="cp_amount" style="display:none;">{{ $pay->AMOUNT_BUFFER }}</span>
                                                                {{ number_format($pay->AMOUNT_BUFFER,2) }}
                                                            </td>
                                                            <td class="cp_source_name text-center" style="width: 150px;" >
                                                                <span class="cp_bank_id" style="display:none;">{{ $pay->F_ACC_PAYMENT_BANK_NO }}</span>
                                                                <span class="cp_pk_no" style="display:none;">{{ $pay->PK_NO }}</span>

                                                                {{ $pay->BANK_NAME }} <span style="display: block; font-size:10px;">({{ $pay->BANK_ACC_NAME }})</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6" style="padding-left: 5px;">
                        <div class="row">
                            <div class="col-1" style="margin-top: 30%;">
                                <button class="btn btn-success btn-sm" id="match_btn" style="mrgin-top:40%;">M<br>A<br>T<br>C<br>H<br></button>
                            </div>
                            <div class="col-11" style="padding-left: 5px;">
                                <div class="card box-shadow-0 border-success">
                                    <div class="card-header card-head-inverse bg-success card-header-sm">
                                        <h4 class="card-title text-center text-white">Bank Statement</h4>
                                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                        <div class="heading-elements">
                                            <ul class="list-inline mb-0">
                                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-content collapse show">
                                        <div class="card-body card-dashboard" style="padding: 10px 0px;">
                                            <div class="table-responsive fix-table">
                                                <table class="table table-striped table-bordered  table-sm" id="bank_statement_table">
                                                    <thead>
                                                        <tr>

                                                            <th class="text-center" style="width: 100px;">Date</th>
                                                            <th class="text-left" >Description</th>
                                                            <th class="text-left" style="width: 100px;">Debit</th>
                                                            <th class="text-left" style="width: 100px;">Credit </th>
                                                            <th class="text-center" style="width: 100px;">Bank</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12"></div>
                </div>
            </div>
        </div>
        <div class="card card-success">
            <div class="card-body">
            <div class="row">
                            <div class="col-sm-12">
                                <table id="varified_payment_table" class="table table-striped table-bordered table-hover p82 dataTable no-footer" >
                            <thead>
                                 <tr role="row">
                                     <th colspan="14" class="text-center f-20 text-danger" style="background-color: #B4C6E7;" rowspan="1"><span style="color: red;">Verified</span></th>
                                </tr>
                                <tr role="row">
                                    <th class="text-center " style="width:75px;">Date</th>
                                    <th class="text-center " style="width: 150px;">Customer Name</th>
                                    <th class="text-center " style="width: 100px;">Paid By</th>
                                    <th class="text-right " style="width:90px;">Amount</th>
                                    <th class="text-center " style="width: 100px;">Bank</th>
                                    <th class="text-center " style="width: 60px">Cancel</th>
                                    <th class="text-center " style="width:75px;">Date</th>
                                    <th class="text-center ">Description</th>
                                    <th style="width: 90px;" class="">Credit (RM)</th>
                                    <th class="text-center " style="width: 100px;">BANK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data['verified']) && count($data['verified']) > 0 )
                                @foreach($data['verified'] as $row)
                                <tr role="row" class="odd">
                                    <td class="text-center">{{ $row->payment->TXN_DATE }}</td>
                                    <td class="text-center">
                                        @if( $row->payment->IS_CUS_RESELLER_BANK_RECONCILATION == 1)
                                        {{ $row->payment->customer->NAME ?? '' }}
                                        @elseif($row->payment->IS_CUS_RESELLER_BANK_RECONCILATION == 2)
                                        {{ $row->payment->customer->RESELLER ?? '' }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if( $row->payment->IS_CUS_RESELLER_BANK_RECONCILATION == 1)
                                        {{ $row->payment->customerPayment->PAID_BY ?? '' }}
                                        @elseif($row->payment->IS_CUS_RESELLER_BANK_RECONCILATION == 2)
                                        {{ $row->payment->resellerPayment->PAID_BY ?? '' }}
                                        @endif
                                    </td>
                                    <td class="text-right"><strong>{{ number_format($row->payment->AMOUNT_ACTUAL,2) }}</strong> </td>
                                    <td class="text-center">
                                        {{ $row->payment->bank->BANK_NAME ?? '' }}
                                        <span style="display: block; font-size:10px;">
                                        ({{ $row->payment->bank->BANK_ACC_NAME ?? '' }})
                                        </span>
                                    </td>
                                    <td class="text-center text-danger">
                                        <a href="{{ route('admin.bankstate.unverify',$row->PK_NO) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Undo</a>
                                    </td>
                                    <td class="text-center">{{ date('d-M-y',strtotime($row->TXN_DATE)) }}</td>
                                    <td class="text-center">{{ $row->NARRATION }}</td>
                                    <td class="text-right"><strong>{{ number_format($row->CR_AMOUNT,2) }}</strong></td>
                                    <td class="text-center">
                                        {{ $row->bank->BANK_NAME ?? '' }}
                                        <span style="display: block; font-size:10px;">
                                        ({{ $row->bank->BANK_ACC_NAME ?? '' }})
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                    </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
    </div>






@endsection
@push('custom_js')
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/account.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script  type="text/javascript">

    $('.cust50').DataTable( {
    "pagingType": "full_numbers",
    "pageLength": 50,
    "bSort" : false,

    } );

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

     $(document).on("click", "#bulk_check", function(event){
        $(".record_check").prop('checked', $(this).prop("checked"));

    });

    var get_url = $('#base_url').val();

    $(document).on("click", ".cp_row", function(e) {
        $("#customer_payment_table .cp_row").removeClass("active_row");
        $(this).toggleClass("active_row");
        var pay_date = $(this).find('td.cp_pay_date').text();
        // var cust_name = $(this).find('td.cp_cust_name').text();
        // var paid_by = $(this).find('td.cp_paid_by').text();
        // var pay_amount = $(this).find('td.cp_pay_amount .cp_amount').text();
        // var bank_id = $(this).find('td.cp_source_name .cp_bank_id').text();
        var acc_bank_txn_no = $(this).find('td .acc_bank_txn_no').val();
       getBankStatement(acc_bank_txn_no);
    });

    $(document).on("click", ".bs_row", function(e) {
        $("#bank_statement_table").find('.bs_row').removeClass("active_row");
        $(this).toggleClass("active_row");

    });

    function  getBankStatement(acc_bank_txn_no){
        var initHtml = '<tr><td colspan="5" class="text-center text-danger" title="Loading"> <i class="fa fa-spinner fa-spin" style="font-size:24px"></i></td></tr>';
        $('#bank_statement_table > tbody').empty().append(initHtml);
        // bank_statement_table.clear();
        var url = get_url + '/get-bank-state';
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'JSON',
            data: {
                'acc_bank_txn_no' : acc_bank_txn_no
            },
            success: function(data) {
                if (data.status == 'success'){
                    $('#bank_statement_table > tbody').empty().append(data.html);
                } else {
                    $('#bank_statement_table > tbody').empty().append('<tr><td colspan="5" class="text-center text-danger"><i>Data not found</i></td></tr>');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {}
        });
    }




$(document).on("click", "#match_btn", function(e) {
    if ($("#customer_payment_table").find('.active_row').length < 1 ) {
        alert('Please select record from customer payment table');
    } else {
        if ($("#bank_statement_table").find('.active_row').length == 0 ) {
        alert('Please select record from bankstatement table');
        } else {
            $(this).attr('disabled', true);
            $("body").css("cursor", "progress");
            var cp_pk_no = $("#customer_payment_table").find('.active_row').find('td .acc_bank_txn_no').val();
            var bs_pk_no = $("#bank_statement_table").find('.active_row').find('td .bs_pk_no').text();
            var url = get_url + '/bank-state/verify';
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'cp_pk_no' : cp_pk_no,
                    'bs_pk_no' : bs_pk_no,
                },
                success: function(data) {
                    if (data.status === true){
                       $("#customer_payment_table").find('.active_row').remove();
                        $("#bank_statement_table").find('.active_row').remove();
                        toastr.success('Payment verified successfull', 'successfull');
                        $('#match_btn').removeAttr('disabled');
                    }
                    else{
                        toastr.warning('Payment verified not successfull, please reload and try again', 'Warning');

                    }

                   window.location.reload(true);
                    $("body").css("cursor", "default");
                },
                error: function (xhr, ajaxOptions, thrownError) {}
            });

        }
    }
});

</script>

@endpush('custom_js')
