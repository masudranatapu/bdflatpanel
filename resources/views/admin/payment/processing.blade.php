@extends('admin.layout.master')

@section('Procurement','open')
@section('payment_processing','active')

@section('title') Payment Details @endsection
@section('page-name') Payment Details @endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin_role.breadcrumb_title') </a></li>
<li class="breadcrumb-item active">Payment Details </li>
@endsection


@push('custom_css')
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">


<style>
    .shipment_box{
    border-bottom: 1px solid #E3EBF3;
    padding: 12px;
    }
    /* .box_2{
    border-bottom: 1px solid #E3EBF3;
    }
    .box_2:last-child {
        border-bottom: none;
    } */
    .box_{
        display: inline-block;
        /* margin-left: 45px; */
        text-align: left;
    }
    .shipment_{
        /* display: inline-block;
        vertical-align: top; */
        /* float: left; */
    }
    .border-right-custom{
        border-right: 2px solid;
    }
    .shipment_box:last-child {
        border-bottom: none;
    }
    .shipment_box2:first-child {
        border-top: none;
    }
    .inner-div1 {
        position: absolute;
        height: 100%;
        width: 47%;
        top: 0;
        border-right: 2px solid #ccc;
    }
    .inner-div2 {
        position: absolute;
        height: 100%;
        width: 50%;
        top: 0;
        right: 0;
    }
    .inner-div1 span, .inner-div2 span{
        position: relative;
        top: 49%;
    }
    #col-14, #col-15, #col-16, #col-17{
        padding : 0px !important;
    }
    .position-relative{
        position: relative;
    }
    .shipment_box2 {
        border-top: 1px solid #E3EBF3;
        padding: 9px 14px 0px;
    }
</style>
@endpush('custom_css')
@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush
@section('content')
<div class="card card-success min-height">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> Payment Details</h4>
        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                <li><a data-action="close"><i class="ft-x"></i></a></li>
            </ul>
        </div>

        <h3 class="pt-2">Report generated from <span style="color: #FFA702">{{ date('jS, F Y', strtotime($data['from_date'])) }}</span> to <span style="color: #FFA702" id="current_date">{{ date('jS, F Y', strtotime($data['to_date'])) }}</span></h3>
    </div>
    <div class="card-content collapse show">
        <div class="card-body">
                <div class="row">
                    <form class="form-inline" action="{{ route('admin.payment_processing.list') }}" method="get" id="form_post" style="display: none">
                        <div class="form-group">
                            <input type="text" class="form-control form-control-sm pickadate" id="from_date" placeholder="From Date" name="from_date">
                        </div>
                        <div class="form-group">
                            &nbsp;&nbsp;
                        <input type="text" class="form-control form-control-sm pickadate" id="to_date" placeholder="To date" name="to_date">
                        </div>
                    </form>
                    <div class="col-md-12">
                        <div class="table-responsive p-1">
                            <table class="table table-striped table-bordered table-sm" id="invoicetable_">
                                <thead>
                                    <tr>
                                        <th style="width: 3%" rowspan="2" colspan="1">SL</th>
                                        <th style="width: 25%" class="text-center" rowspan="1" colspan="2">Payment Source</th>
                                        <th style="width: 50%" class="text-center" rowspan="1" colspan="4">Account Name - Payment Method - Amount</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 10%" class="text-center">Payment Source</th>
                                        <th style="width: 10%" class="text-center">Amount</th>
                                        <th style="width: 20%" class="text-center">Account Name</th>
                                        <th style="width: 20%" class="text-center">Amount</th>
                                        <th style="width: 20%" class="text-center">Account Name</th>
                                        <th style="width: 20%" class="text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($data['data'] && count($data['data']) > 0 )
                                    @foreach ($data['data'] as $item)
                                    <tr>
                                        <td>{{ $loop->index+1 }}</td>
                                        <td>{{ $item->PAYMENT_SOURCE_NAME ?? '' }}</td>
                                        <td>{{ $item->INVOICE_CURRENCY }} {{ number_format($item->total,2) }}</td>
                                        {{-- <td colspan="2" style="position: relative;">
                                           <div class="inner-div1">
                                                <span>{{ $item->PAYMENT_SOURCE_NAME ?? '' }}</span>
                                           </div>
                                           <div class="inner-div2">
                                                <span>{{ $item->INVOICE_CURRENCY }} {{ number_format($item->total,2) }}</span>
                                           </div>
                                        </td> --}}
                                        {{-- <td colspan="4" class="text-center" style="width: 10%;padding: 0">
                                            @if(!empty($item->F_PAYMENT_ACC_NO))
                                            @foreach ($item->get_account_name($item->source_no,$data['from_date'],$data['to_date']) as $account_name)
                                            <div class="shipment_box">
                                                <div class="shipment_">
                                                    <span class="text-center">{{ $account_name->PAYMENT_ACC_NAME }}</span> - <span class="danger">{{ $account_name->INVOICE_CURRENCY }} {{ number_format($account_name->sub_total,2) }}</span> &nbsp;
                                                </div>
                                                <div class="box_">
                                                @if(!empty($item->F_PAYMENT_METHOD_NO))
                                                @foreach ($item->get_payment_method($item->source_no,$account_name->account_no,$data['from_date'],$data['to_date']) as $method)
                                                    <span class="text-center">{{ $method->PAYMENT_METHOD_NAME }} - {{ $account_name->INVOICE_CURRENCY }} {{ number_format($method->amount,2) }} </span><br><br>
                                                @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                            @endif
                                        </td> --}}
                                        <td id="col-14">
                                            @if(!empty($item->F_PAYMENT_ACC_NO))
                                            @foreach ($item->get_account_name($item->source_no,$data['from_date'],$data['to_date']) as $account_name)
                                                <div class="shipment_box2">
                                                    <div class="shipment_">
                                                        <span>{{ $account_name->PAYMENT_ACC_NAME }}</span>
                                                    </div>
                                                </div>
                                                <div class="box_2">
                                                    @if(!empty($item->F_PAYMENT_METHOD_NO))
                                                    @foreach ($item->get_payment_method($item->source_no,$account_name->account_no,$data['from_date'],$data['to_date']) as $method)
                                                        <span class="text-center"> </span><br><br>
                                                    @endforeach
                                                    @endif
                                                </div>
                                            @endforeach
                                            @endif
                                        </td>
                                        <td id="col-15">
                                            @if(!empty($item->F_PAYMENT_ACC_NO))
                                            @foreach ($item->get_account_name($item->source_no,$data['from_date'],$data['to_date']) as $account_name)
                                            <div class="shipment_box2">
                                                <div class="shipment_">
                                                    <span class="danger">{{ $account_name->INVOICE_CURRENCY }} {{ number_format($account_name->sub_total,2) }}</span>
                                                </div>
                                            </div>
                                            <div class="box_2">
                                                @if(!empty($item->F_PAYMENT_METHOD_NO))
                                                @foreach ($item->get_payment_method($item->source_no,$account_name->account_no,$data['from_date'],$data['to_date']) as $method)
                                                    <span class="text-center"> </span><br><br>
                                                @endforeach
                                                @endif
                                            </div>
                                            @endforeach
                                            @endif
                                        </td>
                                        <td id="col-16">
                                            @foreach ($item->get_account_name($item->source_no,$data['from_date'],$data['to_date']) as $account_name)
                                            <div class="shipment_box">
                                                <div class="box_">
                                                    @if(!empty($item->F_PAYMENT_METHOD_NO))
                                                    @foreach ($item->get_payment_method($item->source_no,$account_name->account_no,$data['from_date'],$data['to_date']) as $method)
                                                        <span>{{ $method->PAYMENT_METHOD_NAME }} </span><br><br>
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                        </td>
                                        <td id="col-17">
                                            @foreach ($item->get_account_name($item->source_no,$data['from_date'],$data['to_date']) as $account_name)
                                            <div class="shipment_box">
                                                <div class="box_">
                                                    @if(!empty($item->F_PAYMENT_METHOD_NO))
                                                    @foreach ($item->get_payment_method($item->source_no,$account_name->account_no,$data['from_date'],$data['to_date']) as $method)
                                                        <span> {{ $account_name->INVOICE_CURRENCY }} {{ number_format($method->amount,2) }} </span><br><br>
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('custom_js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript"  src="{{asset('/app-assets/js/scripts/bootstrap-datetimepicker.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(document).ready(function() {
        var table =
        $('#invoicetable_').DataTable( {
            processing: false,
            serverSide: false,
            paging: true,
            pageLength: 10,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            dom: 'l<"#date-filter">frtip',
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            // buttons: [
                // 'copy', 'csv', 'excel', 'print',
                // {
                // extend: 'pdfHtml5',
                // title: 'PDF ',
                // text: 'PDF',
                // orientation: 'landscape',
                // pageSize: 'A4',
                // exportOptions: {
                // columns: [ 0, 1, 2 ]
                // },
                // customize: function ( doc ) {
                // doc.content[1].table.widths = [
                // '3%',
                // '25%',
                // '50%'
                // ]
                // }
                // }
            // ]
        } );
        $('#date-filter').addClass('dataTables_length offset-md-3 col-lg-4 col-md-4 col-sm-12');

        var date_picker_html = '<div id="date_range" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;display: inline-block;"> <i class="fa fa-calendar"> </i>&nbsp; <span> </span> <i class="fa fa-caret-down"></i></div>&nbsp;<a href="{{ route("admin.payment_processing.list") }}" style="margin-top: -2px;" class="btn btn-info btn-sm">Reset</a>';
            $('#date-filter').append(date_picker_html);
            $(function() {
                var start = moment().startOf('month');
                var end = moment().endOf('month');
                function cb(start, end) {
                    $('#date_range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    // var range = start.format("YYYY-MM-DD") + "~" + end.format("YYYY-MM-DD");
                    // table.columns(3).search(start.format("YYYY-MM-DD")).draw();
                    // alert(range);

                    //   $.fn.dataTable.ext.search.push(
                    //     function(settings, data, dataIndex) {
                    //       var min = start;
                    //       var max = end;
                    //       var startDate = new Date(data[3]);
                    //     //   startDate.format("YYYY-MM-DD");
                    //         console.log(min);
                    //         console.log(startDate);
                    //       if (min == null && max == null) {
                    //         return true;
                    //       }
                    //       if (min == null && startDate <= max) {
                    //         return true;
                    //       }
                    //       if (max == null && startDate >= min) {
                    //         return true;
                    //       }
                    //       if (startDate <= max && startDate >= min) {
                    //         return true;
                    //       }
                    //       return false;
                    //     }
                    //   );
                    //   table.draw();
                    //   $.fn.dataTable.ext.search.pop();
                    $('#from_date').val(start.format('DD-MM-Y'));
                    $('#to_date').val(end.format('DD-MM-Y'));
                    $('#form_post').submit();
                }
                $('#date_range').daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                }, cb);
                $('#date_range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            });
    } );
</script>
@endpush('custom_js')
