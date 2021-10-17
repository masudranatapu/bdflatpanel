@extends('admin.layout.master')

@section('Sales Report','open')
@section('sales_report','active')

@section('title')
    Sales Comission  Report
@endsection
@section('page-name')
    Sales Comission  Report
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('shipping.breadcrumb_title')</a>
    </li>
    <li class="breadcrumb-item active">Sales Comission  Report
    </li>
@endsection
@php
    $roles = userRolePermissionArray();
    use Carbon\Carbon;
    // echo '<pre>';
    // echo '======================<br>';
    // print_r($report);
    // echo '<br>======================<br>';
    // exit();
@endphp
@push('custom_css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

@endpush('custom_css')
@section('content')
<div class="content-body min-height">
    <section id="pagination">
        <div class="row">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3>Sales Report of <span style="color: #FFA702">{{ $report['total_comission'][0]->BOOKING_SALES_AGENT_NAME ?? '' }}</span> for the month of <span style="color: #FFA702" id="current_date">{{ date('F Y', strtotime(Carbon::now())) }}</span></h3>
                        {!! Form::hidden('', Request::segment(2), ['id'=>'agent_id']) !!}

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
                            <div class="row">
                                <div class="offset-md-4 col-md-3">
                                    <div class="form-group {!! $errors->has('payment_date') ? 'error' : '' !!}">
                                        <label>Select Month</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <span class="la la-calendar-o"></span>
                                                </span>
                                            </div>
                                            <input type='text' class="form-control pickadate datepicker" placeholder="ISelect Date"
                                                value="{{date('Y-m')}}" name="date" id="datepicker" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped table-hover p84">
                                        <thead>
                                            <tr>
                                                <th style="width: 25%;" class="text-center" title="After Adjusting Cancel Orders">Total Order</th>
                                                <th style="width: 25%;" class="text-center" title="After Adjusting Cancel Orders">Total Commission (RM)</th>
                                                <th style="width: 25%;" class="text-center" title="After Adjusting Cancel Orders">Total Order (<span id="current_date">{{ date('F Y', strtotime(Carbon::now())) }}</span>)</th>
                                                <th style="width: 25%;" class="text-center" title="After Adjusting Cancel Orders">Total Commission (<span id="current_date">{{ date('F Y', strtotime(Carbon::now())) }}</span>)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">{{ $report['total_order'] ?? 0 }}</td>
                                                <td class="text-center">{{ isset($report['total_comission'][0]->total_comission) ? number_format($report['total_comission'][0]->total_comission,2) : 0.00 }}</td>
                                                <td class="text-center" id="current_order">{{ $report['data']->current_order ?? 0 }}</td>
                                                <td class="text-center" id="current_comission">{{ isset($report['current_comission']) ? number_format($report['current_comission'],2) : 0.00 }}</td>
                                            </tr>
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="table-responsive text-center p-1">
                                    <table class="table table-striped table-bordered table-sm" id="process_data_table_">
                                        <thead>
                                        <tr>
                                            <th>SL.</th>
                                            <th style="width: 12%">Order Date</th>
                                            <th>Created By</th>
                                            <th>Order Code</th>
                                            <th>Product</th>
                                            <th>Bundle</th>
                                            <th>Customer</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <th>Total Com.</th>
                                            <th>ENTRY DATE</th>
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
    </section>
</div>
@endsection
@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
<script>
    $("#datepicker").datepicker({
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months",
        orientation: 'bottom auto',
    });
    $(document).ready(function() {
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
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "columnDefs": [
                    { "visible": false, "targets": [10,11] }
                  ],
                ajax: {
                    url: `{{ URL::to('sales_comission_report_list') }}`,
                    type: 'POST',
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.segment = "{{ \Request::segment(2) }}";
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
                            data: 'SS_CREATED_ON',
                            name: 'b.SS_CREATED_ON',
                            order: 'b.RECONFIRM_TIME',
                            searchable: false,
                            render: function(data, type, row) {
                                return '<style="display:inline-block;">Entry: '+moment(row.SS_CREATED_ON).format("MMM DD, YY")+'</style=><br><span style="display:inline-block;">Order: '+moment(row.RECONFIRM_TIME).format("MMM DD, YY")+'</span>';
                            }
                        },
                        {
                            data: 'USERNAME',
                            name: 'a.USERNAME',
                            searchable: true
                        },
                        {
                            data: 'order',
                            name: 'b.BOOKING_NO',
                            searchable: true
                        },
                        {
                            data: 'PRD_VARINAT_NAME',
                            name: 's.PRD_VARINAT_NAME',
                            searchable: true,
                        },
                        {
                            data: 'BUNDLE_NAME_PUBLIC',
                            name: 'SLS_BUNDLE.BUNDLE_NAME_PUBLIC',
                            seq: 'bd.BUNDLE_SEQUENC',
                            searchable: false,
                            render: function(data, type, row) {
                                if (data !== null) {
                                    return data+' / '+row.BUNDLE_SEQUENC;
                                }else{
                                    return '';
                                }
                            }
                        },
                        {
                            data: 'CUSTOMER_NAME',
                            name: 'b.CUSTOMER_NAME',
                            reseller: 'b.RESELLER_NAME',
                            searchable: false,
                            render: function(data, type, row) {
                                return row.RESELLER_NAME ?? row.CUSTOMER_NAME;
                            }
                        },
                        {
                            data: 'PK_NO',
                            name: 'PK_NO',
                            searchable: false,
                            render: function(data, type, row) {
                                return 1;
                            }
                        },
                        {
                            data: 'CURRENT_IS_REGULAR',
                            name: 'bd.CURRENT_IS_REGULAR',
                            regular: 'bd.CURRENT_REGULAR_PRICE',
                            ins: 'bd.CURRENT_INSTALLMENT_PRICE',
                            searchable: false,
                            render: function(data, type, row) {
                                if (row.CURRENT_IS_REGULAR == 1) {
                                    return row.CURRENT_REGULAR_PRICE.toFixed(2);
                                }else{
                                    return row.CURRENT_INSTALLMENT_PRICE.toFixed(2);
                                }
                            }
                        },
                        {
                            data: 'COMISSION',
                            name: 'COMISSION',
                            searchable: false,
                            render: function(data, type, row) {
                                return parseFloat(row.COMISSION).toFixed(2);
                            }
                        },
                        {
                            data: 'SS_CREATED_ON',
                            name: 'b.SS_CREATED_ON',
                            searchable: false,
                            render: function(data, type, row) {
                                return moment(row.SS_CREATED_ON).format("MMM DD, YY");
                            }
                        },
                    {
                        data: 'CHANGE_TYPE',
                        name: 'CHANGE_TYPE',
                        searchable: false
                    },
                ],
                rowCallback: function (row, data, index) {
                    if (data['CHANGE_TYPE'] != 0) {
                        $(row).css('color','red')
                    }
                },
            });
            $('#datepicker').on('change', function() {
            var datepicker = $(this).val();
            if (datepicker != "") {
                table.columns(9).search(datepicker).draw();
                //FETCH MONTHLY DATA
                var get_url         = $('#base_url').val();
                var agent_id        = $('#agent_id').val();
                $.ajax({
                    type:'get',
                    url:get_url+'/sales-comission-list-view/'+agent_id+'/'+datepicker,
                    async :true,
                    dataType: 'json',
                    beforeSend: function () {
                        $("body").css("cursor", "progress");
                    },
                    success: function (data) {
                        console.log(data);
                        $('#current_order').text(data.current_order);
                        $('#current_comission').text((data.current_comission).toFixed(2));
                        $('[id*=current_date]').text(moment(datepicker).format("MMMM YYYY"))
                    },
                    complete: function (data) {
                        $("body").css("cursor", "default");
                    }
                });
            }
        })
    });
</script>
@endpush
