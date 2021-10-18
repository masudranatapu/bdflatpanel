@extends('admin.layout.master')

@section('Payment','open')
@section('view_refund','active')

@section('title') Customer Refund @endsection
@section('page-name') Customer Refund @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_title')</a></li>
    <li class="breadcrumb-item active">@lang('order.breadcrumb_sub_title')</li>
    {{-- <li class="breadcrumb-item "><a href="{{ URL::to('api/update-status') }}" class="link btn btn-sm btn-success text-white">Refresh</a> </li> --}}
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<style>
    .f12{font-size: 12px;}
    .w100{width: 100px;}
    #process_data_table td{vertical-align: middle;}
    .order-type{display: inline-block; margin-right: 10px;}
    .order-type label {cursor: pointer;}
    .badge-default{ background-color: #fff; color: blue;}
    .pulse-green {animation: pulsered 2s infinite;background: #90ee90;box-shadow: 0 0 0 #e00e3f; }
    .select2-container{width: 100% !important; }
</style>
@endpush



@php
    $roles          = userRolePermissionArray();
    $payment_note   = Config::get('static_array.refund_reason') ?? [];
@endphp


@section('content')
<div class="card card-success">
    <div class="card-content collapse show">
        <div class="card-body" style="padding: 15px 5px;">
            @include('admin.refund._header')
            <hr>
            <div class="table-responsive p-1">
                <table class="table table-striped table-bordered table-sm" id="process_data_table">
                    <thead>
                    <tr>
                        <th>SL.</th>
                        <th>Customer/Reseller ID</th>
                        <th>Customer/Reseller name</th>
                        <th>Mobile</th>
                        <th>Balance(RM)</th>
                        <th>Request</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="refundRequestModal" tabindex="-1" role="dialog" aria-labelledby="refundRequestModal" aria-hidden="true" >
    <div class="modal-dialog modal-md" role="document">

            {!! Form::open([ 'route' => 'admin.customer.refundrequeststore', 'method' => 'post', 'class' => 'form-horizontal', 'files' => false, 'id' => 'refundrequestFrm']) !!}
            @csrf
            {!! Form::hidden('customer_no',null,['id'=> 'customer_no']) !!}
            {!! Form::hidden('is_customer',1,['id'=> 'is_customer']) !!}
            <div class="modal-content">
                <div class="modal-header ">
                    <h3 class="modal-title text-center" style="margin: 0 auto;">Request for Refund</h3>
                </div>
                <div class="modal-body">
                    <table class="table p84" style="width: 80%; margin: 0 auto;">
                        <tbody><tr class="bg-yellow">
                            <th>Customer Name</th>
                            <td><strong id="c_name"></strong></td>
                        </tr>
                         <tr class="bg-gray">
                            <th>Avaliable Credit</th>
                            <td>(RM) <strong id="c_balance">0</strong></td>
                        </tr>
                    </tbody></table>
                   <br>
                   <br>
                    <div class="form-group {!! $errors->has('bank_no') ? 'error' : '' !!}">
                        <label>Bank</label>
                        <div class="controls">
                            {!! Form::select('bank_no', $data['mybank_list'] ?? [], null, [ 'class' => 'form-control  select3', 'placeholder' => 'Customer bank name', 'tabindex' => 1 , 'id' => 'bank_no']) !!}
                            {!! $errors->first('bank_no', '<label
                                class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                    <div class="form-group {!! $errors->has('cust_acc_name') ? 'error' : '' !!}">
                        <label>Account Name</label>
                        <div class="controls">
                            {!! Form::text('cust_acc_name', null,[ 'class' => 'form-control', 'placeholder' => 'Account name', 'tabindex' => 2 , 'id' => 'cust_acc_name', 'autocomplete' => 'off']) !!}
                            {!! $errors->first('cust_acc_name', '<label
                                class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                    <div class="form-group {!! $errors->has('cust_acc_no') ? 'error' : '' !!}">
                        <label>Account No</label>
                        <div class="controls">
                            {!! Form::text('cust_acc_no', null,[ 'class' => 'form-control', 'placeholder' => 'Account number', 'tabindex' => 3 , 'id' => 'cust_acc_no','autocomplete' => 'off']) !!}
                            {!! $errors->first('cust_acc_no', '<label
                                class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                    <div class="form-group {!! $errors->has('refund_note') ? 'error' : '' !!}">
                        <label>Refund Reason</label>
                        <div class="form-group  {!! $errors->has('refund_note') ? 'error' : '' !!}">
                            <div class="controls">
                                {!! Form::select('refund_note', $payment_note, null, [ 'class' => 'form-control select2', 'placeholder' => 'Refund reason','data-validation-required-message' => 'This field is required', 'tabindex' => 4, 'refund_note', 'id' => 'refund_note','autocomplete' => 'off']) !!}
                                {!! $errors->first('refund_note', '<label
                                    class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Refund Amount <small style="color:red">*</small></label>
                        <div class="controls">
                            {!! Form::number('refund_amount',null,[ 'class' => 'form-control mb-1 number-only max_val_check', 'placeholder' => 'Refund amount (RM)', 'tabindex' => 5 ,'data-min' => 1, 'data-max' => 0, 'id' => 'refund_amount', 'required', 'step' => '0.01', 'autocomplete' => 'off']) !!}
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left btn-sm" data-dismiss="modal" title="Click For Close">Close</button>
                    <button type="submit" class="btn btn-sm btn-success pull-right" title="Click For Submit" name="submit" value="request_accept" >Submit</button>

                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>

@endsection

@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script src="{{ asset('app-assets/js/common.js')}}"></script>
<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(".select2").select2({
  tags: true
});

var get_url = $('#base_url').val();
$(document).on('click','.refundRequest',function(){
    var amount = Number($(this).data('balance'));
    var customer = $(this).data('name');
    var customer_no = $(this).data('customer_no');
    $('#customer_no').val(customer_no);
    $('#c_name').text(customer);
    $('#c_balance').text(amount.toFixed(2));
    $('#refund_amount').attr('max',amount);
})


    $(document).ready(function() {

        var table   = $('#process_data_table').DataTable({
                processing: false,
                serverSide: true,
                paging: true,
                pageLength: 25,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                ajax: {
                    url: get_url+'/customer/refundlist',
                    type: 'POST',
                    data: function(d) {
                        d._token        = "{{ csrf_token() }}";
                    }
                },
                columns: [
                    {
                        data: 'CUSTOMER_PK_NO',
                        name: 'c.PK_NO',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'CUSTOMER_NO',
                        name: 'c.CUSTOMER_NO',
                        searchable: true,
                    },
                    {
                        data: 'customer_name',
                        name: 'c.NAME',
                        searchable: true
                    },
                    {
                        data: 'mobile',
                        name: 'c.MOBILE_NO',
                        searchable: true
                    },
                    {
                        data: 'balance',
                        name: 'c.CUM_BALANCE',
                        searchable: true,
                        className: 'text-right',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        className: 'text-center',
                    },

                ]
            });


    });
    </script>
@endpush

