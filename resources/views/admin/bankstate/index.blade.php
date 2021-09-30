@extends('admin.layout.master')

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
<style>
    .bs_verified_for_payment {
    background-color: #0c693e !important;
    color: #FFF !important;
}
tr td{vertical-align: middle !important;}
.payid{color:#fff70a; text-decoration: underline;}
.payid:hover{color:#fff70a; }
</style>
@endpush
@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush

@section('PaymentPayment','open')
@section('bankstatement','active')

@section('title')
   UKSHOP | Bank statement
@endsection
@section('page-name')
Bank statement
@endsection
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
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <div class="form-group">
                                <!-- button group with icons and text. -->
                                <div class="btn-group btn-style" role="group" aria-label="Basic example">
                                    <button type="button" class="shadow btn btn-info btn-sm {{ $status == '' ? 'active' : '' }}"><a class="text-white" href="{{ route('admin.bankstate.list') }}"><i class="la la-th-list"></i> Statement(Unused)</a></button>
                                    <button type="button" class="shadow btn btn-info btn-sm {{ $status == 'draft' ? 'active' : '' }}"><a class="text-white" href="{{ route('admin.bankstate.list') }}?status=draft"><i class="la la-list-ol"></i> Draft</a></button>
                                    <button type="button" class="shadow btn btn-info btn-sm {{ $status == 'used' ? 'active' : '' }}"><a class="text-white" href="{{ route('admin.bankstate.list') }}?status=used"><i class="la la-list-alt"></i> Used</a></button>

                                    @if($status == 'draft')
                                        <button type="button" class="shadow btn btn-info btn-sm" id="draft_recor_save"><i class="la la-user"></i>Save record from draft</button>
                                        <button type="button" class="shadow btn btn-info btn-sm"><a class="text-white" href="javascript:void(0)" class="editsourceModal" data-toggle="modal" data-target="#addEditSourceModal" title="Upload bank statement"><i class="text-white la la-cloud-upload"></i> Upload bank statement</a></button>
                                    @else
                                    <button type="button" class="shadow text-white btn btn-info btn-sm" id="mark_as_used"><i class="la la-user"></i>Mark as used</button>

                                    @endif

                                    @if($status != 'used')
                                    <button type="button" class="shadow text-white btn btn-danger btn-sm" id="delete"><i class="la la-delete"></i>Delete</button>
                                    @endif




                                </div>

                            </div>
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
                            <div class="card-body card-dashboard">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered alt-pagination50 table-sm" >
                                        <thead>
                                            <tr>
                                                @if( $status != 'used' )
                                                    <th style="width: 40px;" class="text-center">
                                                        <label class="c-p">
                                                            <input type="checkbox" id="bulk_check" class="c-p">
                                                        </label>
                                                    </th>
                                                @endif
                                                <th style="width: 40px;" >Sl.</th>
                                                <th class="text-left" style="width: 100px;">Tran Date</th>
                                                <th class="text-left">Description</th>
                                                <th class="text-left" style="width: 100px;">Debit</th>
                                                <th class="text-left" style="width: 100px;">Credit</th>
                                                <th class="text-center" style="width: 140px;">Bank</th>
                                                <th style="width: 100px;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($data['rows']) && count($data['rows']) > 0 )
                                                @foreach($data['rows'] as $key => $row )
                                                    <tr class="{{ $row->IS_MATCHED == 1 ? 'bs_verified_for_payment' : '' }}" >
                                                        @if( $status != 'used' )
                                                            <td class="text-center">
                                                                <label class="c-p">
                                                                    <input type="checkbox" name="record_check" value="{{ $row->PK_NO }}" class="record_check c-p">
                                                                </label>
                                                            </td>
                                                        @endif

                                                        <td  class="text-center">{{ $key+1 }}</td>
                                                        <td  class="text-center">{{ date('d-M-Y',strtotime( $row->TXN_DATE )) }}</td>
                                                        <td>
                                                            {{ $row->NARRATION }}
                                                            @if($row->IS_MATCHED == 1)
                                                            <a href="{{ route('admin.payment.details',['id' => $row->payment->PK_NO ?? '' ]) }}" class="payid" title="@if($row->payment->IS_CUS_RESELLER_BANK_RECONCILATION == 1)Customer : {{ $row->payment->customer->NAME ?? '' }}, Customer ID : {{ $row->payment->customer->CUSTOMER_NO ?? '' }} @elseif($row->payment->IS_CUS_RESELLER_BANK_RECONCILATION == 2)Reseller : {{ $row->payment->reseller->NAME ?? '' }}, Reseller ID :{{ $row->payment->reseller->RESELLER_NO ?? '' }} @endif, Amount : (RM) {{ number_format($row->payment->AMOUNT_ACTUAL ?? 0,2) }}" target="_blank">{{ 'PAYID-'.$row->payment->CODE ?? '' }}</a>
                                                            @endif
                                                        </td>
                                                        <td class="text-right"> {{  number_format($row->DR_AMOUNT,2) }}</td>
                                                        <td class="text-right"> {{  number_format($row->CR_AMOUNT,2) }}</td>
                                                        <td class="text-center">
                                                            {{ $row->bank->BANK_NAME }}
                                                            <br>
                                                            <span style="font-size:10px;">{{  $row->bank->BANK_ACC_NAME.'('.$row->bank->BANK_ACC_NO.')' }}</span>

                                                        </td>
                                                        <td class="text-center">
                                                            @if( $status != 'used' )
                                                                @if(hasAccessAbility('delete_bank_state', $roles))
                                                                    <a href="{{route('admin.bankstate.delete',$row->PK_NO)}}" onclick="return confirm('Are you sure you want to delete?')" class="btn btn-xs btn-danger mr-1" title="DELETE"><i class="la la-trash"></i>
                                                                    </a>
                                                                @endif
                                                            @endif

                                                            @if( $status == 'used' )
                                                            @if(hasAccessAbility('edit_bank_state', $roles))
                                                                <a href="{{route('admin.bankstate.unverify',$row->PK_NO)}}" onclick="return confirm('Are you sure you want to unverify?')" class="btn btn-xs btn-danger mr-1 shadow" title="UNVERIFY"><i class="la la-undo"></i>
                                                                </a>
                                                            @endif
                                                        @endif

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
            </div>
        </section>
    </div>

    <div class="modal fade text-left" id="addEditSourceModal" tabindex="-1" role="dialog" aria-labelledby="brand_name" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Upload Bank Statement</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    {!! Form::open([ 'route' => 'admin.bankstate.store', 'method' => 'post',  'class' => 'form-horizontal', 'files' => true , 'novalidate'  ]) !!}
                        @csrf

                    <div class="modal-body">
                        <div class="form-group {!! $errors->has('payment_acc_no') ? 'error' : '' !!}">
                            <label>Payment Account<span class="text-danger">*</span></label>
                            <div class="controls">
                                <select class="form-control" name="payment_acc_no" id="payment_acc_no" data-validation-required-message="This field is required" tabindex="1">
                                    <option value="">--select bank--</option>
                                    @if(isset($data['payment_acc_no']) && count($data['payment_acc_no']) > 0 )
                                    @foreach($data['payment_acc_no'] as $k => $bank)
                                    <option value="{{ $bank->PK_NO }}" >{{ $bank->BANK_NAME .' ('.$bank->BANK_ACC_NAME.') ('.$bank->BANK_ACC_NO.')' }}</option>
                                    @endforeach
                                    @endif

                                </select>

                                {!! $errors->first('payment_acc_no', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputFile">Bank Statement</label>
                            <br>
                            <input type="file" id="statementInputFile" data-validation-required-message="This field is required" name="statement_file" accept=".csv" tabindex="2">
                            <br>
                            <small class="err err_statementInputFile"></small>
                            <small class="text-green">Upload CSV file</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary btn-sm" data-dismiss="modal" value="Close">
                        <input type="submit" class="btn btn-primary btn-sm submit-btn" value="Save">
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="reason_for_used" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Reason for use the record</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                 <div class="modal-body">
                    {!! Form::open(['method' => 'post', 'class' => 'form-horizontal', 'files' => false , 'novalidate' ]) !!}
                        @csrf

                    <div class="form-group {!! $errors->has('reason') ? 'error' : '' !!}">
                        <label>Reason</label>
                        <div class="controls">
                            {!! Form::text('reason', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Reason for use the record', 'tabindex' => 6 , 'id' => 'reason']) !!}
                            {!! $errors->first('reason', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary btn-sm" data-dismiss="modal" value="Close">
                        <input type="button" class="btn btn-primary btn-sm submit-btn-reason" value="Save">
                    </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>


@endsection
@push('custom_js')

<!--script only for brand page-->
<script type="text/javascript" src="{{ asset('app-assets/pages/account.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script  type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $(document).on("click", "#bulk_check", function(event){
        $(".record_check").prop('checked', $(this).prop("checked"));

    });
    var get_url = $('#base_url').val();

//Bank Statement record save from draft
$(document).on("click", "#draft_recor_save", function(event){
        var draft = [];
        $("input:checkbox[name=record_check]:checked").each(function(){
            draft.push($(this).val());
        });
        var url = get_url + '/bank-state/draft-to-save';

        if (draft != '') {

            if(confirm('Are you sure?')) {
            $.ajax({

                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: {'draft' : draft},
                success: function(data) {
                    // console.log(data);
                    if(data.status == true){
                        // location.reload();
                        var re_url =  get_url + '/bank-state';
                        window.location.replace(re_url);
                    }else{
                        toastr.info('Please try again','Info');
                    }

                },

                error: function (xhr, ajaxOptions, thrownError) {}
            });
            }else{
                $("input:checkbox[name=record_check]:checked").prop('checked', false);
            }

        }else{

            toastr.info('Please check at list single record','Info');
        }

    });
    $(document).on("click", ".submit-btn-reason", function(event){
        var draft = [];
        $("input:checkbox[name=record_check]:checked").each(function(){
            draft.push($(this).val());
        });

        var reason = $('#reason').val();

        if (reason != '') {
        var url = get_url + '/bank-state/mark-as-used';
            if(confirm('Are you sure?')) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {'draft' : draft, 'reason' : reason},
                    success: function(data) {
                        // console.log(data);
                        if(data.status == true){
                            // location.reload();
                            var re_url =  get_url + '/bank-state';
                            window.location.replace(re_url);
                        }else{
                            toastr.info('Please try again','Info');
                        }

                    },

                    error: function (xhr, ajaxOptions, thrownError) {}
                });

            }else{
                $("input:checkbox[name=record_check]:checked").prop('checked', false);
            }

        }else{
            toastr.info('Please write a reason in the input field','Info');
        }
    })



    //Bank Statement mark as used
    $(document).on("click", "#mark_as_used", function(event){

        var draft = [];
        $("input:checkbox[name=record_check]:checked").each(function(){
                draft.push($(this).val());
        });

        if (draft != '') {
            $('#reason_for_used').modal('show');
        }else{
            toastr.info('Please check at list single record','Info');
        }

    });

//Bank Statement mark as used
$(document).on("click", "#delete", function(event){
        var pk_no = [];
        $("input:checkbox[name=record_check]:checked").each(function(){
            pk_no.push($(this).val());
        });
        var url = get_url + '/bank-state/delete_bulk';

        if (pk_no != '') {
            if(confirm('Are you sure?')) {
            $.ajax({

                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: {'pk_no' : pk_no},
                success: function(data) {
                    // console.log(data);
                    if(data.status == true){
                        // location.reload();
                        toastr.success('Bank statement deleted successfully','Success');
                        var re_url =  get_url + '/bank-state';
                        window.location.replace(re_url);
                    }else{
                        toastr.info('Please try again','Info');
                    }

                },

                error: function (xhr, ajaxOptions, thrownError) {}
            });

            }else{
                    $("input:checkbox[name=record_check]:checked").prop('checked', false);
                }

            }else{

                toastr.info('Please check at list single record','Info');
            }

    });


</script>

@endpush('custom_js')
