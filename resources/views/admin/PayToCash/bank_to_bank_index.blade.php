@extends('admin.layout.master')

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@section('bank_to_bank_xfer','active')

@section('title') Internal Transfer List @endsection

@section('page-name')Internal Transfer List @endsection

@push('custom_js')
    <!-- BEGIN: Data Table-->
    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
    <!-- END: Data Table-->
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">@lang('customer.breadcrumb_title')</a></li>
    <li class="breadcrumb-item active">Internal Transfer List</li>
@endsection

@php
    $roles = userRolePermissionArray();
@endphp


@push('custom_css')

    <style>
        #scrollable-dropdown-menu .tt-menu {max-height: 260px;overflow-y: auto;width: 100%;border: 1px solid #333;border-radius: 5px;}
        #scrollable-dropdown-menu2 .tt-menu {max-height: 260px;overflow-y: auto;width: 100%;border: 1px solid #333;border-radius: 5px;}
        .twitter-typeahead{display: block !important;}
        #warehouse th, #availble_qty th {border: none;border-bottom: 1px solid #333;font-size: 12px;font-weight: normal;padding-bottom: 7px;    padding-bottom: 11px;}
        #book_qty th { border: none;font-size: 12px;font-weight: normal;padding-bottom: 5px;padding-top: 0;}
        .tt-hint {color: #999 !important;}
        .f-100{
            font-size: 95%;
        }
    </style>

@endpush('custom_css')

@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-sm card-success">
                        <div class="card-header">

                            @if(hasAccessAbility('new_bank_to_bank', $roles))
                            <a class="btn btn-sm btn-primary" href="{{route('admin.account_to_bank.view')}}" title="REQUEST"><i class="ft-plus text-white"></i> New Request</a>
                            @endif

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
                                <div class="table-responsive ">
                                    <table class="table table-striped table-bordered table-sm" id="process_data_table">
                                        <thead>
                                        <tr>
                                            <th class="text-center">@lang('tablehead.sl')</th>
                                            <th>From Account</th>
                                            <th>To Account</th>
                                            <th>Payment Method</th>
                                            <th>Amount(RM)</th>
                                            <th>Status</th>
                                            <th>Request By</th>
                                            <th style="wis_inidth: 15%" class="text-center">@lang('tablehead.action')</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('custom_js')
<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
var get_url = $('#base_url').val();
$(document).ready(function() {

    var table =
    $('#process_data_table').dataTable({
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
            url: `{{ URL::TO('bank-to-bank-list-ajax') }}`,
            type: 'POST',
            data: function(d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            {
                data: 'PK_NO',
                name: 'PK_NO',
                searchable: false,
                sortable:false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'BANK_ACC_NAME',
                name: 'acc.BANK_ACC_NAME',
                bank: 'acc.BANK_NAME',
                searchable: true,
                render: function(data, type, row) {
                    return row.BANK_ACC_NAME+' ('+row.BANK_NAME+')';
                }
            },
            // {
            //     data: 'is_in',
            //     name: 'is_in',
            //     searchable: false,
            //     className: 'text-center',
            //     render: function(data, type, row) {
            //         if (row.is_in == 0) {
            //             return 'Cash from 1 ro 2';
            //         }else{
            //             return 'Cash from 2 to 1';
            //         }
            //     }
            // },
            {
                data: 'to_bank_acc_name',
                name: 'to_bank_acc_name',
                bank: 'to_bank_name',
                searchable: true,
                render: function(data, type, row) {
                    return row.to_bank_acc_name+' ('+row.to_bank_name+')';
                }
            },
            {
                data: 'ACC_CUSTOMER_PAYMENT_METHOD',
                name: 'ix.ACC_CUSTOMER_PAYMENT_METHOD',
                searchable: true,
                className: 'text-center'
            },
            {
                data: 'ENTERED_MR_AMOUNT',
                name: 'ix.ENTERED_MR_AMOUNT_NO',
                searchable: false,
            },
            {
                data: 'status',
                name: 'status',
                searchable: false,
                className: 'text-center'
            },
            {
                data: 'USERNAME',
                name: 'u.USERNAME',
                searchable: true,
                className: 'text-center'
            },
            {
                data: 'action',
                name: 'action',
                searchable: false
            },
        ]
    });
});
</script>
@endpush
