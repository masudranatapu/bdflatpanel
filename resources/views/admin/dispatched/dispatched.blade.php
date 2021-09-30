@extends('admin.layout.master')

@section('Order Management','open')
@section('dispatched_list','active')

@section('title') Order | Dispatched @endsection
@section('page-name') Dispatched Order @endsection

@section('page-name')
    @lang('order.list_page_sub_title')
@endsection



@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('order.breadcrumb_sub_title')
    </li>
@endsection



@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

<style>
    .f12{font-size: 12px;}
    .w100{width: 100px;}
    #process_data_table td{vertical-align: middle;}
    .order-type{display: inline-block; margin-right: 10px;}
    .order-type label {cursor: pointer;}

    a:not([href]):not([tabindex]) {
        color: #fff;
    }
</style>
@endpush
@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush

@php
    $roles = userRolePermissionArray();
    $order_type = 'all';

@endphp


@section('content')

<div class="card card-success min-height">
    <div class="card-content collapse show">
        <div class="card-body" style="padding: 15px 5px;">
            <div class="row">
                <div class="col-md-12 text-center">
                    <form class="form-inline" action="">
                        <div class="form-group">
                            <input type="text" class="form-control pickadate" id="from_date" placeholder="From Date">
                        </div>
                        <div class="form-group">
                            &nbsp;&nbsp;
                        <input type="text" class="form-control pickadate" id="to_date" placeholder="To date">
                        </div>

                        <button type="submit" class="btn btn-info btn-sm">Search</button> &nbsp; &nbsp; &nbsp;
                        <a href="" class="btn btn-info btn-sm">Reset</a>
                    </form>
                </div>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm alt-pagination50" id="process_data_table">
                    <thead>
                    <tr>
                        <th>SL.</th>
                        <th style="width:100px;">Date</th>
                        <th style="width:100px;">Dispatch By</th>
                        <th style="width:100px;">Sales Agent</th>
                        <th>Order No.</th>
                        <th>Customer</th>
                        <th style="width:100px;">Tracking No</th>
                        <th style="width:100px;">Carrier</th>
                        <th style="width:50px;" class="text-center">Qty</th>
                        <th style="width:50px;">Action</th>

                    </tr>
                    </thead>
                    <tbody>
                        @if(isset($rows) && count($rows) > 0 )
                            @foreach($rows as $key => $row )
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ date('d-m-Y',strtotime($row->DISPATCH_DATE)) }}</td>
                                    <td>{{ $row->DISPATCH_USER_NAME }}</td>
                                    <td>{{ $row->order->booking->BOOKING_SALES_AGENT_NAME ?? '' }}</td>
                                    <td>
                                        <a href="{{ route('admin.booking_to_order.book-order-view', ['id' => $row->order->F_BOOKING_NO ?? 0 ]) }}">
                                        {{ '#ORD-'.$row->order->booking->BOOKING_NO ?? '' }}
                                        </a>
                                    </td>
                                    <td>

                                        @if($row->order->IS_RESELLER == 1)
                                            <a href="{{ route('admin.reseller.edit', [$row->order->F_RESELLER_NO]) }}" title="VIEW" class="link">
                                            {{ $row->order->RESELLER_NAME  }}
                                            </a>

                                        @else
                                            <a href="{{ route('admin.customer.view',[$row->order->F_CUSTOMER_NO]) }}" class="link" title="VIEW">{{  $row->order->CUSTOMER_NAME  }}</a>

                                        @endif



                                    </td>
                                    <td>{{ $row->COURIER_TRACKING_NO }}</td>
                                    <td><a href="{{ $row->courier->URLS ?? '' }}" target="_blank">{{ $row->COURIER_NAME }}</a></td>
                                    <td class="text-center">{{ $row->allChild->count() ?? 0 }}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

            </div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
        </div>

    </div>



</div>


@endsection




@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var get_url = $('#base_url').val();

$('.pickadate').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
    });



    </script>

@endpush

