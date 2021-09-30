@extends('admin.layout.master')

@section('Property Management','open')
@section('property_list','active')

@section('title') Properties @endsection
@section('page-name') Properties @endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush



@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('product.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">Properties
    </li>
@endsection

@php
    $roles = userRolePermissionArray();
    $rows = $data['listings'] ?? null;
    $user_type_combo = $data['user_type'] ?? [];
    $listing_type_combo = $data['listing_type'] ?? [];
    $city_combo = $data['cities'] ?? [];
    $property_for_combo = Config::get('static_array.property_for');
    $property_status_combo = Config::get('static_array.property_status');
    $payment_status_combo = Config::get('static_array.payment_status');

@endphp

@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-sm card-success">

                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard">
                                <form action="" class="my-2">
                                    <div class="row mb-1">

                                        <div class="col">
                                            <div class="form-group {!! $errors->has('user_type') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::select('user_type', $user_type_combo, request()->query->get('user_type'), ['class'=>'form-control mb-1 ', 'placeholder' => 'Select user type', 'tabindex' => 6]) !!}
                                                    {!! $errors->first('user_type', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col">
                                            <div class="form-group {!! $errors->has('property_for') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::select('property_for', $property_for_combo, request()->query->get('property_for'), ['class'=>'form-control mb-1 ', 'placeholder' => 'Select property for', 'tabindex' => 6]) !!}
                                                    {!! $errors->first('property_for', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="form-group {!! $errors->has('listing_type') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::select('listing_type', $listing_type_combo, request()->query->get('listing_type'), ['class'=>'form-control mb-1 ', 'placeholder' => 'Select listing type', 'tabindex' => 6]) !!}
                                                    {!! $errors->first('listing_type', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div
                                                class="form-group {!! $errors->has('payment_status') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::select('payment_status', $payment_status_combo, request()->query->get('payment_status'), ['class'=>'form-control mb-1 ', 'placeholder' => 'Select payment status', 'tabindex' => 6]) !!}
                                                    {!! $errors->first('payment_status', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div
                                                class="form-group {!! $errors->has('property_status') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::select('property_status', $property_status_combo, request()->query->get('property_status'), ['class'=>'form-control mb-1 ', 'placeholder' => 'Select property status', 'tabindex' => 6]) !!}
                                                    {!! $errors->first('property_status', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col">
                                            <div class="form-group {!! $errors->has('city') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    {!! Form::select('city', $city_combo, request()->query->get('city'), ['class'=>'form-control mb-1 ', 'placeholder' => 'Select city', 'tabindex' => 6]) !!}
                                                    {!! $errors->first('city', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col">
                                            <input type="submit" class="btn btn-info btn-sm px-2" value="Search"
                                                   style="border-radius: 0px">
                                            <a class="btn btn-sm btn-primary text-white"
                                               href="{{ route('admin.product.list') }}" title="ADD NEW LISTING"
                                               style="color: #FC611F;margin-left: 10px;">Reset</a>
                                        </div>
                                    </div>
                                </form>

                                <div class="table-responsive ">
                                    <table class="table table-striped table-bordered table-sm" id="dtable">
                                        <thead>
                                        <tr>
                                            <th class="text-center">SL</th>
                                            <th class="text-center">User ID</th>
                                            <th class="text-center">User Type</th>
                                            <th>User Name</th>
                                            <th class="text-center" title="Property ID">Prty ID</th>
                                            <th title="Property For">Prty For</th>
                                            <th>Listing Type</th>
                                            <th>City</th>
                                            <th>Area</th>
                                            <th>Title</th>
                                            <th>Mobile</th>
                                            <th>Create Date</th>
                                            <th>Status</th>
                                            <th>Pay Status</th>
                                            <th style="width: 135px;">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
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
            let value = getCookie('property_list');

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
                        url: get_url + '/property_list',
                        type: 'POST',
                        data: function (d) {
                            d._token = "{{ csrf_token() }}";
                            d.user_type = {{ request()->query('user_type') ?? 'null' }};
                            d.property_for = {!! request()->query('property_for') ? '"' . request()->query('property_for') . '"' : 'null' !!};
                            d.listing_type = {{ request()->query('listing_type') ?? 'null' }};
                            d.property_status = {{ request()->query('property_status') ?? 'null' }};
                            d.payment_status = {{ request()->query('payment_status') ?? 'null' }};
                            d.city = {{ request()->query('city') ?? 'null' }};
                        }
                    },
                    columns: [
                        {
                            data: 'PK_NO',
                            name: 'PK_NO',
                            searchable: false,
                            sortable: false,
                            render: function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },

                        {
                            data: 'user_id',
                            name: 'user_id',
                            searchable: true
                        },
                        {
                            data: 'user_type',
                            name: 'user_type',
                            searchable: true
                        },
                        {
                            data: 'user_name',
                            name: 'user_name',
                            searchable: true,
                            className: 'text-left'
                        },
                        {
                            data: 'CODE',
                            name: 'CODE',
                            searchable: true
                        },
                        {
                            data: 'PROPERTY_FOR',
                            name: 'PROPERTY_FOR',
                            searchable: true
                        },                        
                        {
                            data: 'LISTING_TYPE',
                            name: 'LISTING_TYPE',
                            searchable: true,
                            className: 'text-left'

                        },
                         {
                            data: 'CITY_NAME',
                            name: 'CITY_NAME',
                            searchable: true
                        },
                        {
                            data: 'AREA_NAME',
                            name: 'AREA_NAME',
                            searchable: true
                        },                       
                        {
                            data: 'TITLE',
                            name: 'TITLE',
                            searchable: true
                        },
                        {
                            data: 'mobile',
                            name: 'mobile',
                            searchable: true
                        },
                        {
                            data: 'CREATED_AT',
                            name: 'CREATED_AT',
                            searchable: true,
                        },
                        {
                            data: 'status',
                            name: 'status',
                            searchable: false,
                            className: 'text-right',
                        },
                        {
                            data: 'payment_status',
                            name: 'payment_status',
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
            return table;
        }
    </script>

    <script>
        $(document).on('click', '.page-link', function () {
            let pageNum = $(this).text();
            setCookie('property_list', pageNum);
        });

        function setCookie(property_list, pageNum) {
            let today = new Date();
            let name = property_list;
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
