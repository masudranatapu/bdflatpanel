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
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('shipping.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">Sales Comission  Report
    </li>
@endsection
@php
    $roles = userRolePermissionArray();
    use Carbon\Carbon;
@endphp
@push('custom_css')
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}"> --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

{{-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/> --}}
@endpush('custom_css')
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
                                <div class="table-responsive text-center p-1">
                                    <table class="table table-striped table-bordered table-sm" id="process_data_table_">
                                        <thead>
                                        <tr>
                                            <th>SL.</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Contact</th>
                                            <th>Month</th>
                                            {{-- <th>Open Account</th> --}}
                                            <th>Amount</th>
                                            {{-- <th>Total Amount</th> --}}
                                            {{-- <th>Status</th> --}}
                                            <th style="width: 11%">@lang('tablehead.tbl_head_action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @foreach ($salesReport as $row)
                                            <tr>
                                                <td>{{ $loop->index+1 }}</td>
                                                <td>{{ $row->NAME }}</td>
                                                <td>{{ $row->EMAIL }}</td>
                                                <td>{{ $row->MOBILE_NO }}</td>
                                                <td>{{ $row->SENDER_BOX_COUNT }}</td>
                                                <td>{{ Carbon::parse($row->RECONFIRM_TIME)->format('Y-m') }}</td>
                                                <td><small>(RM)</small> {{ number_format($row->comission,2) }}</td>
                                                @if(hasAccessAbility('view_sales_report', $roles))
                                                <td>
                                                    <a href="#!" type="button" class="btn btn-xs btn-success mr-1 " title="Vi=IEW">
                                                        <i class="la la-eye"></i>
                                                    </a>
                                                </td>
                                                @endif
                                            </tr>
                                            @endforeach --}}
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
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
{{-- <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script> --}}
<script>
    // $('.pickadate').pickadate({
    //     format: 'yyyy-mm',
    //     formatSubmit: 'yyyy-mm',
    //     viewMode: "months",
    //     minViewMode: "months"
    // });
    $("#datepicker").datepicker({
        format: "yyyy-mm",
        viewMode: "months",
        minViewMode: "months"
    });
    $(document).ready(function() {

        var table =
            $('#process_data_table_').DataTable({
                processing: false,
                serverSide: true,
                paging: true,
                pageLength: 10,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ajax: {
                    url: `{{ URL::to('sales_comission_report') }}`,
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
                            data: 'NAME',
                            name: 'NAME',
                            searchable: true
                        },
                        {
                            data: 'EMAIL',
                            name: 'EMAIL',
                            searchable: true,
                        },
                        {
                            data: 'MOBILE_NO',
                            name: 'MOBILE_NO',
                            searchable: true
                        },
                        {
                            data: 'RECONFIRM_TIME',
                            name: 'RECONFIRM_TIME',
                            searchable: false,
                            render: function(data, type, row) {
                                return moment(data).format("YYYY-MM");
                            }
                        },
                        {
                            data: 'comission',
                            name: 'comission',
                            searchable: true,
                            render: function(data, type, row) {
                                return '<small>(RM)</small> '+(parseFloat(data)).toFixed(2);
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            searchable: false
                        }
                ]
            });
            $('#datepicker').on('change', function() {
            var datepicker = $(this).val();
            if (datepicker != "") {
                table.columns(4).search(datepicker).draw();
            }
        })
    });
</script>
@endpush
