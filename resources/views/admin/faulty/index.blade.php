@extends('admin.layout.master')

@section('list_order','active')
@section('title')
    @lang('order.list_page_title')
@endsection
@section('page-name')
    @lang('order.list_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('order.breadcrumb_sub_title')
    </li>
@endsection
@php
    $roles = userRolePermissionArray()
@endphp
@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            @if(hasAccessAbility('new_order', $roles))
                            {{-- <a class="text-white" href="{{route('admin.vendor.new')}}">
                                <button type="button" class="btn btn-round btn-sm btn-primary">
                                    <i class="ft-plus text-white"></i> @lang('order.role_create_btn')
                                </button>
                            </a> --}}
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
                            <div class="card-body card-dashboard" style="padding: 0">
                                <div class="table-responsive p-1">
                                    <table class="table table-striped table-bordered table-sm" id="process_data_table">
                                        <thead>
                                        <tr>
                                            <th>SL.</th>
                                            <th>Create At</th>
                                            <th>Create By</th>
                                            <th>Sales Agent</th>
                                            <th>Ord Date</th>
                                            <th>Ord ID</th>
                                            <th>Customer Name</th>
                                            <th style="width:50px;">Type</th>
                                            <th style="width:50px;" class=" text-right">Total Price</th>
                                            <th style="width:50px;">Pay</th>
                                            <th style="width:50px;">Avail</th>
                                            <th class=" text-center">Satus</th>
                                            <th class=" text-center">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>

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
@endsection




@push('custom_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>

<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var get_url = $('#base_url').val();



    $(document).ready(function() {
        var table =
            $('#process_data_table').DataTable({
                processing: false,
                serverSide: true,
                paging: true,
                pageLength: 10,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                ajax: {
                    url: 'order/all_order',
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
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false
                    },
                    {
                        data: 'CREATED_BY',
                        name: 'SA_USER.USERNAME',
                        searchable: true
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
                        searchable: false
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

