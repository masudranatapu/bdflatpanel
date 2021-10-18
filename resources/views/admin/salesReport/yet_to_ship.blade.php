@extends('admin.layout.master')

@section('Sales Report','open')
@section('yet_to_ship','active')

@section('title')
    Yet to Ship Report
@endsection
@section('page-name')
    Yet to Ship Report
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('shipping.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">Yet to Ship Report
    </li>
@endsection
@php
    $roles = userRolePermissionArray();
    use Carbon\Carbon;
@endphp
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush('custom_css')
@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            {{-- <h3>Report generated from <span style="color: #FFA702">{{ $report['from_date'] }}</span> to <span style="color: #FFA702" id="current_date">{{ $report['to_date'] }}</span></h3> --}}
                            <h3>Report generated from <span style="color: #FFA702">{{ date('jS, F Y', strtotime($report['from_date'])) }}</span> to <span style="color: #FFA702" id="current_date">{{ date('jS, F Y', strtotime($report['to_date'])) }}</span></h3>
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
                                    <div class="col-md-12 text-center">
                                        <form class="form-inline" action="{{ route('admin.yet_to_ship.list') }}" method="get">
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-sm pickadate" id="from_date" placeholder="From Date" name="from_date">
                                            </div>
                                            <div class="form-group">
                                                &nbsp;&nbsp;
                                            <input type="text" class="form-control form-control-sm pickadate" id="to_date" placeholder="To date" name="to_date">
                                            </div> &nbsp; &nbsp;

                                            <button type="submit" class="btn btn-info btn-sm">Search</button> &nbsp; &nbsp;
                                            <a href="{{ route('admin.yet_to_ship.list') }}" class="btn btn-info btn-sm">Reset</a>
                                        </form>
                                    </div>
                                </div>
                                <hr>
                                <div class="table-responsive text-center p-1">
                                    <table class="table table-striped table-bordered table-sm" id="process_data_table_">
                                        <thead>
                                        <tr>
                                            <th>SL.</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Product that is not generated</td>
                                                <td>{{ number_format($report['invoice_actual_ev'],2) }} (Exact Value {{ number_format($report['invoice_exact'],2) }})</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Product that is not in shipment</td>
                                                <td>{{ number_format($report['not_in_ship'],2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Product that is in shipment</td>
                                                <td>{{ number_format($report['in_ship'],2) }}</td>
                                            </tr>
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
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script>
    $('.pickadate').pickadate({
        format: 'dd-mm-yyyy',
        // formatSubmit: 'dd-mm-yyyy',
    });
</script>
@endpush
