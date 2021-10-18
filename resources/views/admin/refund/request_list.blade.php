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
    .sub_lbl{min-width: 80px; display: inline-block}
</style>
@endpush



@php
    $roles = userRolePermissionArray();
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
                        <th>Request Date</th>
                        <th>Request By</th>
                        <th>Request Reason</th>
                        <th>Customer ID</th>
                        <th>Customer Name</th>
                        <th>Request Bank</th>
                        <th>Balance(RM)</th>
                        <th>Status</th>
                        <th>Action</th>
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
                    <div class="form-group">
                        <label>Refund Amount <small style="color:red">*</small></label>
                        <div class="controls">
                            {!! Form::number('refund_amount', 0,[ 'class' => 'form-control mb-1 number-only max_val_check', 'placeholder' => 'Refund amount (RM)', 'tabindex' => 1 ,'data-min' => 1, 'data-max' => 0, 'id' => 'refund_amount', 'required', 'step' => '0.01']) !!}
                        </div>
                    </div>
                     <div class="form-group">
                        <label>Note </label>
                        <div class="controls">
                            <textarea class="form-control" name="refund_note" id="refund_note"></textarea>
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
                    url: get_url+'/customer/refundrequestlist',
                    type: 'POST',
                    data: function(d) {
                        d._token        = "{{ csrf_token() }}";
                    }
                },
                columns: [
                    {
                        data: 'F_CUSTOMER_NO',
                        name: 'p.F_CUSTOMER_NO',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'date',
                        name: 'p.REQUEST_DATE',
                        searchable: true,
                    },
                    {
                        data: 'request_by',
                        name: 'p.REQUEST_BY_NAME',
                        searchable: true
                    },
                    {
                        data: 'request_note',
                        name: 'p.REQUEST_NOTE',
                        searchable: true
                    },
                    {
                        data: 'customer_no',
                        name: 'p.CUSTOMER_NO',
                        searchable: true
                    },
                    {
                        data: 'customer_name',
                        name: 'c.NAME',
                        searchable: true
                    },
                    {
                        data: 'req_bank_name',
                        name: 'c.REQ_BANK_NAME',
                        searchable: true
                    },
                    {
                        data: 'balance',
                        name: 'c.CUM_BALANCE',
                        searchable: false,
                        className: 'text-right'
                    },
                    {
                        data: 'status',
                        name: 'p.STATUS',
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        className: 'text-center'
                    },

                ]
            });


    });
    </script>
@endpush

