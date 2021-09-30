@extends('admin.layout.master')

@section('Dispatck Management','open')
@section('order_collect_list','active')
@section('title')
   Collect Order for Dispatch
@endsection

@section('page-name')
Collect Order for Dispatch
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('order.breadcrumb_sub_title')
    </li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
<style>
    .f12{font-size: 12px;}
    .w100{width: 100px;}
    #process_data_table td{vertical-align: middle;}
    .order-type{display: inline-block; margin-right: 10px;}
    .order-type label {cursor: pointer;}

    a:not([href]):not([tabindex]) {
        color: #fff;
    }
    .c-btn.active{color: #fff !important;}
</style>
@endpush
@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush

@php
    $roles = userRolePermissionArray();
    $order_type = 'all';
@endphp


@section('content')

<div class="card min-height">
    <div class="card-content collapse show">
        <div class="card-body" style="padding: 15px 5px;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm" id="process_data_table">
                    <thead>
                    <tr>
                        <th>SL.</th>
                        <th style="width:100px;">Created</th>
                        <th>Agent</th>
                        <th>Date</th>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th style="width:50px;">Variations</th>
                        <th style="width:50px;" class=" text-right">Items</th>
                        <th style="width:50px;">Ready?</th>
                        <th class=" text-center">Status</th>
                        <th class=" text-center" title="BATCH NO.">Batch</th>
                        <th class=" text-center" style="width:100px;">Action
                        </th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
{{-- <script src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/js/scripts/forms/checkbox-radio.min.js"></script> --}}
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
var get_url = $('#base_url').val();
    $(document).ready(function() {
        var id      =  `{{ Request::segment(2) }}`;
        var table   =
            $('#process_data_table').DataTable({
                processing: false,
                serverSide: true,
                paging: true,
                pageLength: 25,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ajax: {
                    url: `{{  URL::to('collect-order-datatable') }}`,
                    type: 'POST',
                    data: function(d) {
                        d._token    = "{{ csrf_token() }}";
                        d.id        = id;
                    }
                },
                "columnDefs": [
                { "orderable": false, "targets": 11 }
                ],
                columns: [
                    {
                        data: 'PK_NO',
                        name: 'PK_NO',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false,
                        className:'w100'
                    },
                    {
                        data: 'BOOKING_SALES_AGENT_NAME',
                        name: 'SLS_BOOKING.BOOKING_SALES_AGENT_NAME',
                        searchable: true,
                    },
                    {
                        data: 'order_date',
                        name: 'order_date',
                        searchable: false
                    },
                    {
                        data: 'order_id',
                        name: 'SLS_ORDER.F_BOOKING_NO',
                        searchable: true,

                    },
                    {
                        data: 'customer_name',
                        name: 'SLS_BOOKING.CUSTOMER_NAME',
                        searchable: true,
                        className:'text-uppercase'
                    },
                    {
                        data: 'item_type',
                        name: 'item_type',
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'item_count',
                        name: 'item_count',
                        searchable: false,
                        className: 'text-center'

                    },
                    {
                        data: 'avaiable',
                        name: 'avaiable',
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        searchable: false
                    },
                    {
                        data: 'RTS_BATCH_NO',
                        name: 'RTS_BATCH_NO',
                        className: 'text-center',
                        searchable: false
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

