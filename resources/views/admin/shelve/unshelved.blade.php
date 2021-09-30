@extends('admin.layout.master')

@section('Warehouse Operation','open')
@section('unshelved_list','active')

@section('title')
    @lang('unshelve.list_page_title')
@endsection
@section('page-name')
    @lang('unshelve.list_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('unshelve.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('unshelve.breadcrumb_sub_title')
    </li>
@endsection
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush
@php
    $roles = userRolePermissionArray();
@endphp
@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
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
                                            <th>Image</th>
                                            <th>Product Name</th>
                                            <th>SKU Id</th>
                                            <th>Warehouse </th>
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

@push('custom_js')
{{-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> --}}

<script type="text/javascript">

    function datatable_() {
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
                    url: `{{URL::to('unshelved_product_list')}}`,
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
                            data: 'PRC_IN_IMAGE_PATH',
                            name: 'PRC_IN_IMAGE_PATH',
                            searchable: false,
                            render: function(data, type, row) {
                                return '<a href="{{URL::to('')}}'+row.PRD_VARIANT_IMAGE_PATH+'" target="_blank"><img src="{{URL::to('')}}'+row.PRD_VARIANT_IMAGE_PATH+'" class="img-responsive img-sm"></a>';
                            }


                        },

                        {
                            data: 'PRD_VARINAT_NAME',
                            name: 'PRD_VARINAT_NAME',
                            searchable: true
                        },
                        {
                            data: 'SKUID',
                            name: 'SKUID',
                            ig_code: 'ig_code_',
                            barcode: 'BARCODE',
                            searchable: true,
                            render: function(data, type, row) {
                                return '<div style="display:inline-block;"><span style="width:70px;display: inline-block;">IG</span>:'+row.ig_code_+'</div><br>'
                                        +'<div style="display:inline-block;"><span style="width:70px;display: inline-block;">BC</span>:'+row.BARCODE+'</div><br>'
                                        +'<div style="display:inline-block;"><span style="width:70px;display: inline-block;">SKU</span>:'+row.SKUID+'</div>';

                                // return '<span>IG: <span style="text-align:center;">'+row.ig_code_+'</span></span><br>'
                                // +'<span>BARCODE: <span style="text-align:center;">'+row.BARCODE+'</span></span><br>'
                                // +'<span>SKU: <span style="text-align:center;">'+row.SKUID+'</span></span>';
                            }
                        },
                        {
                            data: 'INV_WAREHOUSE_NAME',
                            name: 'INV_WAREHOUSE_NAME',
                            searchable: true
                        },
                        {
                            data: 'count',
                            name: 'count',
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
                        table.columns(4).search(warehouse_type).draw();
                    }
                });
            },
            complete: function (data) {
                $("body").css("cursor", "default");
            }
        });
    }
</script>

<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush

