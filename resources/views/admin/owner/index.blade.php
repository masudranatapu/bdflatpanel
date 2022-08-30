@extends('admin.layout.master')

@section('Property Owner','open')
@section('owner_list','active')

@section('title') Property Owner @endsection
@section('page-name') Property Owner @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('invoice.breadcrumb_title')</a></li>
    <li class="breadcrumb-item active">Property Owner</li>
@endsection

@php
    $roles          = userRolePermissionArray();
    $user_type      = Config::get('static_array.user_type');
    $user_status    = Config::get('static_array.user_status');
    $owner_active   = request()->get('owner') ?? 'all';

@endphp

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <style>
        .owner_active{background-color: #0808de;}
    </style>
@endpush


@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-sm card-success">
                        <div class="card-header">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <a href="{{ route('admin.owner.list',['owner' => 2]) }}"
                                       class="btn btn-info btn-sm {{ $owner_active == 2 ? 'owner_active' : '' }}">Owner</a>
                                </div>

                                <div class="form-check form-check-inline">
                                    <a href="{{ route('admin.owner.list',['owner' => 3]) }}"
                                       class="btn btn-info btn-sm {{ $owner_active == 3 ? 'owner_active' : '' }}">Builder</a>
                                </div>

                                <div class="form-check form-check-inline">
                                    <a href="{{ route('admin.owner.list',['owner' => 4]) }}"
                                       class="btn btn-info btn-sm {{ $owner_active == 4 ? 'owner_active' : '' }}">Agency</a>
                                </div>
                                <div class="form-check form-check-inline">
                                    <a href="{{ route('admin.owner.list') }}" class="btn btn-info btn-sm {{ $owner_active == 'all' ? 'owner_active' : '' }}">All</a>
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


                                <div class="table-responsive ">
                                    <table class="table table-striped table-bordered table-sm" id="dtable">
                                        <thead>
                                        <tr>
                                            <th class="text-center">SL</th>
                                            <th class="text-center">User ID</th>
                                            <th>Create Date</th>
                                            <th>Name</th>
                                            <th>Contact</th>
                                            <th>Is Feature</th>
                                            <th>Balance</th>
                                            <th>Properties</th>
                                            <th style="width: 17%" class="text-center">Action</th>
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
    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let get_url = $('#base_url').val();


        $(document).ready(function () {
            var value = getCookie('owner_list');

            if (value !== null) {
                value = (value - 1) * 25;
                // table.fnPageChange(value,true);
            } else {
                value = 0;
            }
            var table = callDatatable(value);

        });

        function callDatatable(value) {
            let table =
                $('#dtable').dataTable({
                    processing: false,
                    serverSide: true,
                    paging: true,
                    pageLength: 25,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    iDisplayStart: value,
                    ajax: {
                        url: get_url + '/owner_list',
                        type: 'POST',
                        data: function (d) {
                            d._token = "{{ csrf_token() }}";
                            d.owner = {{ request()->query('owner') ?? 'null' }};
                        }
                    },
                    columns: [
                        {
                            data: 'PK_NO',
                            name: 'PK_NO',
                            searchable: false,
                            sortable: false,
                            className: 'text-center',
                            render: function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },

                        {
                            data: 'CODE',
                            name: 'CODE',
                            className: 'text-center',
                            searchable: true
                        },
                        {
                            data: 'created_at',
                            name: 'CREATED_AT',
                            searchable: true
                        },

                        {
                            data: 'name',
                            name: 'NAME',
                            searchable: true,
                        },
                        {
                            data: 'contact',
                            name: 'contact',
                            searchable: true,
                        },
                        {
                            data: 'IS_FEATURE',
                            name: 'IS_FEATURE',
                            searchable: true,
                            render: function (data) {
                                return data === 1 ? 'Feature' : 'General';
                            }
                        },

                        {
                            data: 'UNUSED_TOPUP',
                            name: 'UNUSED_TOPUP',
                            searchable: false,
                            className: 'text-right',
                            render: function (data) {
                                return formatter.format(data);
                            }
                        },

                        {
                            data: 'total_list',
                            name: 'total_list',
                            className: 'text-center',
                            searchable: true
                        },
                        {
                            data: 'action',
                            name: 'action',
                            className: 'text-center',
                            searchable: false
                        },

                    ]
                });
            return table;
        }
        let formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'BDT'
        });
    </script>

    <script>
        $(document).on('click', '.page-link', function () {
            let pageNum = $(this).text();
            setCookie('owner_list', pageNum);
        });

        function setCookie(owner_list, pageNum) {
            let today = new Date();
            let name = owner_list;
            let elementValue = pageNum;
            let expiry = new Date(today.getTime() + 30 * 24 * 3600 * 1000); // plus 30 days

            document.cookie = name + "=" + elementValue + "; path=/; expires=" + expiry.toGMTString();
        }

        function getCookie(name) {
            let re = new RegExp(name + "=([^;]+)");
            let value = re.exec(document.cookie);
            return (value != null) ? unescape(value[1]) : null;
        }
    </script>
@endpush
