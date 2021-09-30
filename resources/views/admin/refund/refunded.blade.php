@extends('admin.layout.master')

@section('Payment','open')
@section('view_refund','active')

@section('title') Customer Refunded @endsection
@section('page-name') Customer Refunded @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_title')</a></li>
    <li class="breadcrumb-item active">@lang('order.breadcrumb_sub_title')</li>
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
                        <th>Date</th>
                        <th>Reason</th>
                        <th>Refunded from Acc</th>
                        <th>Customer/Reseller ID</th>
                        <th>Customer/Reseller name</th>
                        <th>Requested Bank</th>
                        <th>Refunded Bank</th>
                        <th>Image</th>
                        <th>Amount(RM)</th>
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
@endsection




@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var get_url = $('#base_url').val();

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
                    url: get_url+'/customer/refundedList',
                    type: 'POST',
                    data: function(d) {
                        d._token        = "{{ csrf_token() }}";
                    }
                },
                columns: [
                    {
                        data: 'CUSTOMER_PK_NO',
                        name: 'p.PK_NO',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'date',
                        name: 'p.PAYMENT_DATE',
                        searchable: true,
                    },
                    {
                        data: 'reason',
                        name: 'r.PAYMENT_NOTE',
                        searchable: false,
                    },
                    {
                        data: 'account',
                        name: 'p.PAYMENT_ACCOUNT_NAME',
                        searchable: true,
                    },

                    {
                        data: 'customer_no',
                        name: 'p.CUSTOMER_NO',
                        searchable: true
                    },
                    {
                        data: 'customer_name',
                        name: 'p.CUSTOMER_NAME',
                        searchable: true
                    },
                    {
                        data: 'req_bank_name',
                        name: 'r.REQ_BANK_NAME',
                        searchable: true
                    },
                    {
                        data: 'refunded_bank_name',
                        name: 'r.REFUNDED_BANK_NAME',
                        searchable: true
                    },
                    {
                        data: 'image',
                        name: 'p.ATTACHMENT_PATH',
                        searchable: false
                    },
                    {
                        data: 'amount',
                        name: 'p.MR_AMOUNT',
                        searchable: false,
                        className: 'text-center'
                    },

                ]
            });


    });
    </script>
@endpush

