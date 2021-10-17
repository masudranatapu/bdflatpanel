@extends('admin.layout.master')

@section('view_bank_collection','active')
@section('title')
   Collection List
@endsection

@section('page-name')
Collection List
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('order.breadcrumb_sub_title')
    </li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
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
    .c-btn.active{color: #fff !important;}
</style>
@endpush
@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<!-- END: Data Table-->
@endpush

@php
    $roles = userRolePermissionArray();
@endphp


@section('content')

<div class="card card-success min-height">
    <div class="card-content collapse show">
        <div class="card-body" style="padding: 15px 5px;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered alt-pagination table-sm" id="process_data_table">
                    <thead>
                    <tr>
                        <th>SL.</th>
                        <th>Name</th>
                        <th>Bank Name</th>
                        <th>Actual balance</th>
                        <th>Buffer Balance</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>
                                    {{ $loop->index+1 }}
                                </td>
                                <td>
                                    {{ $item->BANK_ACC_NAME }}
                                </td>
                                <td>
                                    {{ $item->BANK_NAME }}
                                </td>
                                <td>
                                    {{ number_format($item->BALANCE_ACTUAL,2) }}
                                </td>
                                <td>
                                    {{ number_format($item->BALACNE_BUFFER,2) }}
                                </td>
                                <td>
                                    <a href="{{ ROUTE('admin.collection.list.breakdown',[$item->PK_NO]) }}" type="button" class="btn btn-xs btn-info mr-1 " title="VIEW BREAKDOWN"><i class="la la-eye"></i>
                                    </a>
                                    <a href="{{ ROUTE('admin.cod_user.stock_list',[$item->PK_NO]) }}" type="button" class="btn btn-xs btn-warning mr-1 " title="VIEW STOCK ITEM">
                                        <i class="la la-eye"></i>
                                    </a>
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
@endsection
@push('custom_js')
{{-- <script src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/js/scripts/forms/checkbox-radio.min.js"></script> --}}
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
@endpush

