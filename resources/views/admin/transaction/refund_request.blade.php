@extends('admin.layout.master')

@section('Payment','open')
@section('refund_request','active')

@section('title') Refund Request @endsection
@section('page-name') Refund Request @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('agent.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active">Refund Request</li>
@endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{asset('/custom/css/custom.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
@endpush

@push('custom_js')

    <!-- BEGIN: Data Table-->
    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
    <!-- END: Data Table-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>

@endpush

@php
    $roles = userRolePermissionArray();
    $refund_status = Config('static_array.refund_status');
@endphp

@section('content')
    <div class="content-body min-height">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-block mb-1">
                                        <div class="controls">
                                            {!! Form::radio('type','all', !request()->query('filter'),[ 'id' => 'all', 'class' => 'type']) !!}
                                            {{ Form::label('all','All') }}
                                            &emsp;
                                            {!! Form::radio('type','1', request()->query('filter') == '1',[ 'id' => 'pending', 'class' => 'type']) !!}
                                            {{ Form::label('pending','Pending') }}
                                            &emsp;
                                            {!! Form::radio('type','2', request()->query('filter') == '2',[ 'id' => 'approved', 'class' => 'type']) !!}
                                            {{ Form::label('approved','Approved') }}
                                            &emsp;
                                            {!! Form::radio('type','3', request()->query('filter') == '3',[ 'id' => 'rejected', 'class' => 'type']) !!}
                                            {{ Form::label('rejected','Rejected') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <table class="table table-striped table-bordered text-center" id="dtable">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>USER ID</th>
                                            <th>Refund ID</th>
                                            <th>PID/LID</th>
                                            <th>Request At</th>
                                            <th>Property owner/seeker Name</th>
                                            <th>Property owner/seeker No.</th>
                                            <th>Reason</th>
                                            <th>Comment</th>
                                            <th>Amount</th>
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
@endsection


@push('custom_js')
    <script type="text/javascript">
        $('.type').click(function () {
            let type = $(this).val();
            let url = '{{ route('admin.refund_request') }}'

            if (type !== 'all') {
                url += '?filter=' + type;
            }
            window.location = url;
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let get_url = $('#base_url').val();


        $(document).ready(function () {
            let value = getCookie('refund_request_list');

            if (value !== null) {
                let value = (value - 1) * 25;
                // table.fnPageChange(value,true);
            } else {
                let value = 0;
            }
            let table = callDatatable(value);

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
                        url: '{{ route('ajax.refund-request.list') }}',
                        type: 'POST',
                        data: function (d) {
                            d._token = "{{ csrf_token() }}";
                            d.filter = {{ request()->query('filter') ?? 'null' }};
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
                            data: 'USER_CODE',
                            name: 'USER_CODE',
                            className: 'text-center',
                            searchable: true
                        },
                        {
                            data: 'CODE',
                            name: 'CODE',
                            searchable: true
                        },
                        {
                            data: 'TID',
                            name: 'TID',
                            searchable: true,
                            render: function (data) {
                                return 'PID ' + data;
                            }
                        },
                        {
                            data: 'REQUEST_AT',
                            name: 'REQUEST_AT',
                            searchable: true,
                        },
                        {
                            data: 'USER_NAME',
                            name: 'USER_NAME',
                            searchable: true,
                        },
                        {
                            data: 'USER_MOBILE_NO',
                            name: 'USER_MOBILE_NO',
                            searchable: true,
                        },
                        {
                            data: 'REQUEST_REASON',
                            name: 'REQUEST_REASON',
                            searchable: true
                        },

                        {
                            data: 'COMMENT',
                            name: 'COMMENT',
                            searchable: false,
                        },

                        {
                            data: 'REQUEST_AMOUNT',
                            name: 'REQUEST_AMOUNT',
                            className: 'text-center',
                            searchable: true
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
