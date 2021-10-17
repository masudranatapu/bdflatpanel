@extends('admin.layout.master')

@section('Warehouse Operation','open')
@section('shelve_list','active')


@section('title')
    @lang('shelve.list_page_title')
@endsection

@section('page-name')
    @lang('shelve.list_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('shelve.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('shelve.breadcrumb_sub_title')
    </li>
@endsection
@php
    $roles = userRolePermissionArray();
@endphp
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush
@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            {{-- <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> View
                                Shelve</h4> --}}
                            <a class="btn btn-round btn-sm btn-primary text-white" href="{{route('admin.shelve.add')}}" title="ADD NEW SHELVE"><i class="ft-plus text-white"></i> Add Shelve</a>
                            <a class="btn btn-round btn-sm btn-info text-white" href="{{route('admin.shelve.list',['type'=>'all'])}}" title="VIEW ALL SHELVES"><i class="ft-eye text-white"></i> View All Shelves</a>
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
                                <div class="table-responsive text-center p-1">
                                    <table class="table table-striped table-bordered table-sm" id="process_data_table_">
                                        <thead>
                                        <tr>
                                            <th>SL.</th>
                                            <th>Zone Code</th>
                                            <th>Warehouse Name</th>
                                            <th>Description</th>
                                            <th>Product Count</th>
                                            <th>@lang('tablehead.tbl_head_action')</th>
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
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

<script type="text/javascript">

    function datatable_() {
        var type  =  `{{ request()->get('type') }}`;
        var table =
            $('#process_data_table_').DataTable({
                processing: false,
                serverSide: true,
                paging: true,
                pageLength: 25,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                dom: 'l<"#warehouse-filter">frtip',
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ajax: {
                    url: `{{ URL::to('shelved_product_list') }}`,
                    type: 'POST',
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.type = type;
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
                            data: 'ZONE_BARCODE',
                            name: 'ZONE_BARCODE',
                            pk_no: 'PK_NO',
                            searchable: true,
                            render: function(data, type, row) {
                                if (row.PK_NO == 262 || row.PK_NO == 263 || row.PK_NO == 264) {
                                    return '<span style="color:#D70022">'+data+'</span>';
                                }else{
                                    return data;
                                }
                            }
                        },
                        {
                            data: 'NAME',
                            name: 'INV_WAREHOUSE.NAME',
                            searchable: true
                        },
                        {
                            data: 'DESCRIPTION',
                            name: 'DESCRIPTION',
                            pk_no: 'PK_NO',
                            searchable: true,
                            render: function(data, type, row) {
                                if (row.PK_NO == 262 || row.PK_NO == 263 || row.PK_NO == 264) {
                                    return '<span style="color:#D70022">'+data+'</span>';
                                }else{
                                    return data;
                                }
                            }
                        },
                        {
                            data: 'ITEM_COUNT',
                            name: 'ITEM_COUNT',
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            searchable: false
                        }
                ]
            });
            warehouse_dropdown();
            $('#warehouse-filter').addClass('dataTables_length offset-md-6 col-lg-2 col-md-2 col-sm-12');

            return table;
    }

    $(document).ready(function() {
            table = datatable_();
    });
    function destroy_table() {
        $('#process_data_table_').DataTable().clear().destroy();
        table = datatable_();
    }

    function warehouse_dropdown() {
        var pageurl = `{{ URL::to('get-warehouse-dropdown') }}`;
        $.ajax({
            type:'post',
            url:pageurl,
            dataType: "json",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $("body").css("cursor", "progress");
            },
            success: function (data) {
                $('#warehouse-filter').html('');
                $('#warehouse-filter').append(data);
                $('#warehouse_type').on('change', function() {
                    var warehouse_type = $(this).val();
                    if (warehouse_type == 0) {
                        destroy_table();
                    }else{
                        table.columns(2).search(warehouse_type).draw();
                    }
                });
            },
            complete: function (data) {
                $("body").css("cursor", "default");
            }
        });
    }
</script>
