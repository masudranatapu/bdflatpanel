@extends('admin.layout.master')

@section('Dispatck Management','open')
@section('list_dispatch','active')

@section('title') Order for Dispatch @endsection
@section('page-name') Order for Dispatch @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_title')</a></li>
    <li class="breadcrumb-item active">@lang('order.breadcrumb_sub_title')</li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
<style>
    .f12{font-size: 12px;}
    .w100{width: 100px;}
    #process_data_table td{vertical-align: middle;}
    .order-type{display: inline-block; margin-right: 10px;}
    .order-type label {cursor: pointer;}
    a:not([href]):not([tabindex]) {color: #fff; }
    .c-btn.active{color: #fff !important;}
</style>
@endpush

@push('custom_js')
    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
@endpush

@php
    $roles = userRolePermissionArray();
    $order_type = 'all';
@endphp

@section('content')
<div class="card card-success min-height">
    <div class="card-content collapse show">
        <div class="card-body" style="padding: 15px 5px;">
            <div class="row">

                <div class="col-md-6 col-sm-6">
                    <a href="{{ route('admin.dispatch.list',[ 'dispatch' => 'rts' ]) }}" class="btn btn-xs btn-success  c-btn {{ request()->get('dispatch') == 'rts' ? 'active' : ''}} " style="min-width:90px;">RTS</a>
                    <a href="{{ route('admin.dispatch.list',[ 'dispatch' => 'cod_rtc' ]) }}" class="btn btn-xs btn-success c-btn {{ request()->get('dispatch') == 'cod_rtc' ? 'active' : ''}} " style="min-width:90px;">COD & RTC</a>
                    @if (request()->get('dispatch') == 'rts')
                    <a href="javascript:void(0)" class="btn btn-xs btn-info c-btn" id="mark_pickup" style="min-width:90px;">Collect</a>
                    @endif
                  </div>
            </div>
            <hr>
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
                        <th style="width:50px;" class=" text-right">Order value</th>
                        <th style="width:50px;">Payment</th>
                        <th style="width:50px;">Ready?</th>
                        <th class=" text-center">Status</th>
                        {{-- <th class=" text-center" title="IS HOLD BY ADMIN">Hold</th> --}}
                        <th class=" text-center" title="SELF PICKUP/ COD or RTC">SP</th>
                        <th class=" text-center" style="width:100px;">Action
                        @if (request()->get('dispatch') == 'rts')
                        <label class="c-p">
                            <input type="checkbox" id="bulk_check" class="c-p ml-1">
                        </label>
                        @endif
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

$(document).on("click", "#bulk_check", function(e){
    $('#process_data_table tbody :checkbox').prop('checked', $(this).is(':checked'));
});
$(document).on("click", "#mark_pickup", function(event){
    var pickup_array = [];
    $("input:checkbox[name=record_check]:checked").each(function(){
        pickup_array.push($(this).val());
    });
    var url = get_url + '/mark-pickup-list';
    if (pickup_array != '') {
        if(confirm('Are you sure?')) {
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: {'pickup_array' : pickup_array},
            success: function(data) {
                if(data == 1){
                    location.reload();
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
var get_url = $('#base_url').val();

    $("#process_data_table").on("change", ".is_admin_hold", function () {
        var id = $(this).data("booking_id");
        var type = null;

        if ($(this).is(':checked')) {
            var type = 'checked';
        }else{
            var type = 'unchecked';
        }
        var is_admin_hold = get_url + '/order_admin_hold';

        if(confirm('Are you sure?')) {
        $.ajax({
            type: "post",
            data:{ type:type, id:id},
            url: is_admin_hold,
            beforeSend:function(){},
            success: function (data) {

                if (data == 'true') {
                    if( type == 'unchecked'){
                        toastr.success('Unhold the order successfully','Success');
                    }else{
                        toastr.success('Successfully hold the order','Success');
                        }
                }else{
                    toastr.info('Order status not change successfully', 'Error');
                }
            },
            complete: function (data){}
        });
        }else{
            if( type == 'unchecked'){
                $(this).prop('checked', true);

            }else{
                $(this).prop('checked', false);
            }


        }

    });
    
    /*
    $("#process_data_table").on("change", ".is_self_pickup", function () {
        var id = $(this).data("booking_id");
        var type = null;

        if ($(this).is(':checked')) {
            var type = 'checked';
        }else{
            var type = 'unchecked';
        }
        var is_self_pickup = get_url + '/order_self_pickup';

        if(confirm('Are you sure?')) {
        $.ajax({
            type: "post",
            data:{ type:type, id:id},
            url: is_self_pickup,
            beforeSend:function(){},
            success: function (data) {

                if (data == 'true') {
                    if( type == 'unchecked'){
                        toastr.success('Self pickup unchecked successfully','Success');
                    }else{
                        toastr.success('Self pickup checked successfully','Success');
                        }
                }else{
                    toastr.info('Order status not change successfully', 'Error');
                }
            },
            complete: function (data){}
        });
        }else{
            if( type == 'unchecked'){
                $(this).prop('checked', true);

            }else{
                $(this).prop('checked', false);
            }


        }

    });

*/




    $(document).ready(function() {
        var id      =  `{{ request()->get('id') }}`;
        var type    =  `{{ request()->get('type') }}`;
        var dispatch    =  `{{ request()->get('dispatch') }}`;
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
                    url: 'order/all_order',
                    type: 'POST',
                    data: function(d) {
                        d._token    = "{{ csrf_token() }}";
                        d.id        = id;
                        d.type      = type;
                        d.dispatch  = dispatch;
                    }
                },
                "columnDefs": [
                { "orderable": false, "targets": 12 }
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
                        data: 'price_after_dis',
                        name: 'price_after_dis',
                        searchable: false,
                        className: 'text-right'

                    },
                    {
                        data: 'payment',
                        name: 'payment',
                        searchable: false
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
                    /* {
                        data: 'admin_hold',
                        name: 'admin_hold',
                        className: 'text-center',
                        searchable: false
                    }, */
                    {
                        data: 'self_pickup',
                        name: 'self_pickup',
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

