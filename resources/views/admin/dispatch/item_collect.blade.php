@extends('admin.layout.master')

@section('Dispatch Management','open')
@section('item_collect','active')
@section('title')
   Order Item for Dispatch
@endsection

@section('page-name')
Order Item for Dispatch
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('order.breadcrumb_sub_title')
    </li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

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
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<!-- END: Data Table-->
@endpush

@php
    $roles = userRolePermissionArray();
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
                        <th>Image</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Assign</th>
                        <th style="width:50px;">Total Count</th>
                        {{-- <th class=" text-center" title="SELF PICKUP/ COD or RTC">SP</th> --}}
                        {{-- <th class=" text-center" style="width:100px;">Action --}}
                        {{-- </th> --}}
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
@include('admin.dispatch.item_assign_modal')
@endsection
@push('custom_js')
{{-- <script src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/js/scripts/forms/checkbox-radio.min.js"></script> --}}
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).on("click", "#assign_logistic", function(event){
    var batch_id  = $(this).data("batch_id");
    var sku_id    = $(this).data("sku_id");
    var user_id   = $(this).data("user_id");

    $('#batch_id').val(batch_id);
    $('#sku_id').val(sku_id);
    // $('#logistic_user option[value='+user_id+']').attr('selected','selected');
    // $("#logistic_user").selectmenu("refresh");
    if (user_id > 0) {
        $("#logistic_user").val(user_id);
        $("#logistic_user").trigger('change');
    }else{
        $("#logistic_user").val("").change();
        // $("#logistic_user").trigger('change');
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
                    url: `{{ URL::to('collect-item-datatable') }}`,
                    type: 'POST',
                    data: function(d) {
                        d._token    = "{{ csrf_token() }}";
                        d.id        = id;
                        // d.type      = type;
                        // d.dispatch  = dispatch;
                    }
                },
                // "columnDefs": [
                // { "orderable": false, "targets": 12 }
                // ],
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
                        data: 'image',
                        name: 'image',
                        searchable: false,
                        className:'w100'
                    },
                    {
                        data: 'PRD_VARINAT_NAME',
                        name: 'INV_STOCK.PRD_VARINAT_NAME',
                        searchable: true,
                    },
                    {
                        data: 'position',
                        name: 'position',
                        searchable: false,
                    },
                    {
                        data: 'assign_user',
                        name: 'assign_user',
                        searchable: false,
                    },
                    {
                        data: 'total_count',
                        name: 'total_count',
                        searchable: false,
                    },
                ]
            });
        });
    </script>

@endpush

