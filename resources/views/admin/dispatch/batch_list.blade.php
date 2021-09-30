@extends('admin.layout.master')

@section('Dispatck Management','open')
@section('view_batch_list','active')
@section('title')
   Batch List
@endsection

@section('page-name')
Batch List
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
    #process_data_table td{vertical-align: middle;}
    .order-type{display: inline-block; margin-right: 10px;}
    .order-type label {cursor: pointer;}

    a:not([href]):not([tabindex]) {
        color: #fff;
    }
    .c-btn.active{color: #fff !important;}
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
        <div class="card-body" style="padding: 15px 5px;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered alt-pagination table-sm">
                    <thead>
                    <tr>
                        <th class="text-center">SL.</th>
                        <th class="text-center">Batch No.</th>
                        <th class="text-center">Total Order Item</th>
                        <th class="text-center">Total Item Count</th>
                        {{-- <th class=" text-center" title="SELF PICKUP/ COD or RTC">SP</th> --}}
                        {{-- <th class=" text-center" style="width:100px;">Action --}}
                        {{-- </th> --}}
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                        <tr>
                            <td class="text-center">{{ $loop->index + 1 }}</td>
                            <td class="text-center">{{ $row->batch_no }}</td>
                            <td class="text-center"><a href="{{ route('admin.order_collect.list', [$row->batch_no]) }}" class="btn btn-xs btn-info" title="VIEW ORDER LIST">{{ $row->order_count }}</a></td>
                            <td class="text-center"><a href="{{ route('admin.item_collect.list', [$row->batch_no]) }}" class="btn btn-xs btn-info" title="VIEW ORDER ITEM LIST">{{ $row->item_count }}</a></td>
                            <td class="text-center">
                                <a href="{{ route('admin.item_collect.list', [$row->batch_no]) }}" class="btn btn-xs btn-success" title="VIEW BREAKDOWN"><i class="la la-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
        </div>
    </div>
</div>
@include('admin.dispatch.item_assign_modal')
@endsection
@push('custom_js')
{{-- <script src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/js/scripts/forms/checkbox-radio.min.js"></script> --}}
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
@endpush

