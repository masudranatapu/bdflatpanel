@extends('admin.layout.master')

@section('Sales Agent','open')
@section('agent_list','active')

@section('title') @lang('agent.list_page_title') @endsection
@section('page-name') @lang('agent.list_page_sub_title') @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('agent.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active">@lang('agent.breadcrumb_sub_title')</li>
@endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{asset('/custom/css/custom.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@push('custom_js')

    <!-- BEGIN: Data Table-->
    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
    <!-- END: Data Table-->
@endpush

@php
    $roles = userRolePermissionArray()
@endphp

@section('content')
    <div class="content-body min-height">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row  mb-2">
                                <div class="col-12">
                                    <div class="row mb-1">
                                        {{-- <div class="col-2">
                                            <form action="">
                                                <div style="position: relative">
                                                    <i class="fa fa-search"
                                                       style="position: absolute;top: 9px;left: 10px"></i>
                                                    <input type="text" class="form-control" name="search"
                                                           placeholder="Search"
                                                           style="border-radius: 25px !important;padding-left: 28px;">
                                                </div>
                                            </form>
                                        </div> --}}
                                        <div class="col-12  text-right" style="padding-top: 10px">
                                            <a href="{{route('admin.agents.create')}}"
                                               class="text-warning font-weight-bold"><i class="fa fa-plus"></i> Add New</a>
                                        </div>
                                    </div>
                                    <div class="table-responsive ">
                                        <table
                                            id="dtable"
                                            class="table table-striped table-bordered table-sm text-center">
                                            <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>ID</th>
                                                <th>Create Date</th>
                                                <th>Agent Name</th>
                                                <th>Mobile</th>
                                                <th>Email</th>
                                                <th>Is Feature</th>
                                                <th>Properties</th>
                                                <th>Earning</th>
                                                <th>Status</th>
                                                <th>Actions</th>
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
            </div>
        </div>
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
            var value = getCookie('agents_list');

            if (value !== null) {
                value = (value - 1) * 25;
                // table.fnPageChange(value,true);
            } else {
                value = 0;
            }
            var table = callDatatable(value);

        });

        function callDatatable(value) {
            return $('#dtable').dataTable({
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
                    url: get_url + '/agents_list',
                    type: 'POST',
                    data: function (d) {
                        d._token = "{{ csrf_token() }}";
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
                        data: 'CREATED_AT',
                        name: 'CREATED_AT',
                        searchable: true
                    },
                    {
                        data: 'NAME',
                        name: 'NAME',
                        searchable: true,
                    },
                    {
                        data: 'MOBILE_NO',
                        name: 'MOBILE_NO',
                        searchable: true,
                    },
                    {
                        data: 'EMAIL',
                        name: 'EMAIL',
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
                        data: 'total_list',
                        name: 'TOTAL_LISTING',
                        className: 'text-center',
                        searchable: true
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
                        data: 'status',
                        name: 'status',
                        searchable: true,
                        className: 'text-center'

                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'text-center',
                        searchable: false
                    },

                ]
            });
        }

        let formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'BDT'
        });
    </script>

    <script>
        $(document).on('click', '.page-link', function () {
            let pageNum = $(this).text();
            setCookie('agents_list', pageNum);
        });

        function setCookie(agents_list, pageNum) {
            let today = new Date();
            let name = agents_list;
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
