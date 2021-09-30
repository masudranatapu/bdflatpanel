@extends('admin.layout.master')

@section('dispatched_list','active')

@section('title') Order | Returned @endsection
@section('page-name') Returned Order @endsection

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
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

<style>
    .f12{font-size: 12px;}
    .w100{width: 100px;}
    /* #process_data_table td{vertical-align: middle;} */
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
    <div class="card-header">
        <a href="{{route('admin.dispatched.list',['type' => 'returned'])}}" class="btn btn-sm btn-primary text-white" title="VIEW RETURNED ORDERS"><i class="ft-eye text-white"></i> Returned Orders</a>
    </div>
    <div class="card-content collapse show">
        <div class="card-body" style="padding: 15px 5px;">
            <div class="row">
                <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered alt-pagination50 table-sm ">
                    <thead>
                        <tr>
                            <th>SL.</th>
                            <th style="width:100px;">Date</th>
                            <th style="width:100px;">Dispatch By</th>
                            <th style="width:100px;">Sales Agent</th>
                            <th>Order No.</th>
                            <th>Customer</th>
                            <th style="width:100px;">Dispatched Via</th>
                            <th style="width:100px;">Tracking No</th>
                            <th style="width:100px;">Carrier</th>
                            <th style="width:50px;" class="text-center">Qty</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($rows) && count($rows) > 0 )
                            @foreach($rows as $key => $row )
                                {{-- <tr class="{{ $row->order->booking->IS_RETURN != 0 ? 'bg-warning' : '' }}"> --}}
                                <tr>
                                    <td>{{ $rows->firstItem() + $key }}</td>
                                    <td>{{ date('d-m-Y',strtotime($row->DISPATCH_DATE)) }}</td>
                                    <td>{{ $row->DISPATCH_USER_NAME }}</td>
                                    <td>{{ $row->order->booking->BOOKING_SALES_AGENT_NAME ?? '' }}</td>
                                    <td>
                                        <a href="{{ route('admin.booking_to_order.book-order-view', ['id' => $row->order->F_BOOKING_NO ?? 0 ]) }}">
                                        {{ '#ORD-'.$row->order->booking->BOOKING_NO ?? '' }}
                                        </a>
                                        <br>
                                        @if($row->order->booking->IS_RETURN == 1)
                                            Partial Returned
                                        @elseif($row->order->booking->IS_RETURN == 2)
                                            Full Order Returned
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->order->IS_RESELLER == 1)
                                            <a href="{{ route('admin.reseller.edit', [$row->order->F_RESELLER_NO]) }}" title="VIEW" class="link">
                                            {{ $row->order->RESELLER_NAME  }}
                                            </a>
                                        @else
                                            <a href="{{ route("admin.customer.history", ['id' => $row->order->F_CUSTOMER_NO,'type'=>'customer']) }} " class="link" title="VIEW">{{  $row->order->CUSTOMER_NAME  }}</a>

                                        @endif
                                    </td>
                                    <td class="text-center">{{ $row->IS_COLLECTED_FOR_RTS > 0 ? 'App' : 'Direct' }}</td>
                                    <td>{{ $row->COURIER_TRACKING_NO ?? $row->COLLECTED_BY }}</td>
                                    <td><a href="{{ $row->courier->URLS ?? '' }}" target="_blank">{{ $row->COURIER_NAME ?? 'Self' }}</a></td>
                                    <td class="text-center">{{ $row->allChild->count() ?? 0 }}</td>
                                    <td class="text-center">
                                        @if($row->order->booking->IS_RETURN == 0)
                                        <a href="{{ route('admin.revert_dispatch.dispatch',[$row->order->F_BOOKING_NO]) }}" onclick="return confirm('Are you sure?')" class="btn btn-xs btn-primary mb-05" title="REVERT BACK"><i class="la la-exchange"></i></a>
                                        @endif

                                        @if ($row->order->IS_SELF_PICKUP == 1)
                                        <a href="{{ route('admin.order.dispatch',['id'=>$row->order->F_BOOKING_NO,'type' => 'cod_rtc']) }}" class="btn btn-xs btn-info mb-05" title="VIEW"><i class="la la-eye"></i></a>
                                        @else
                                        <a href="{{ route('admin.order.dispatch',['id'=>$row->order->F_BOOKING_NO,'type' => 'rts']) }}" class="btn btn-xs btn-info mb-05" title="VIEW"><i class="la la-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="pagination">
                         {{ $rows->appends(request()->query())->links() }}
                    </div>
                </div>
                </div>

            <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
        </div>
    </div>
</div>
@endsection

@push('custom_js')
@endpush

