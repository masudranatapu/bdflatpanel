@extends('admin.layout.master')

@section('view_bank_collection','active')
@section('title')
   Order Item List
@endsection

@section('page-name')
Order Item List
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
<?php

?>

@section('content')
<div class="card card-success min-height">
    <div class="card-content collapse show">
        <div class="card-body" style="padding: 15px 5px;">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <a href="{{ route('admin.cod_user.stock_list',[ 'id'=>Request::segment(2),'acknowlwdge' => 'yes' ]) }}" class="btn btn-xs btn-success  c-btn {{ request()->get('acknowlwdge') == 'yes' ? 'active' : ''}} " style="min-width:90px;">Acknowledged</a>
                    <a href="{{ route('admin.cod_user.stock_list',[ 'id'=>Request::segment(2),'acknowlwdge' => 'no' ]) }}" class="btn btn-xs btn-success c-btn {{ request()->get('acknowlwdge') == 'no' ? 'active' : ''}} " style="min-width:90px;">Not Acknowledged</a>
                    <a href="{{ route('admin.cod_user.stock_list',[ 'id'=>Request::segment(2) ]) }}" class="btn btn-xs btn-info c-btn {{ request()->get('acknowlwdge') === null ? 'active' : ''}} " style="min-width:90px;">Show All</a>
                  </div>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table table-striped table-bordered alt-pagination table-sm" id="process_data_table">
                    <thead>
                    <tr>
                        <th>SL.</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th style="width:50px;">Total Count</th>
                        {{-- <th class=" text-center" title="SELF PICKUP/ COD or RTC">SP</th> --}}
                        {{-- <th class=" text-center" style="width:100px;">Action --}}
                        {{-- </th> --}}
                    </tr>
                    </thead>
                    <tbody>
                        @if (!empty($data) && isset($data))
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ $loop->index+1 }}</td>
                                <td class="w100"><img src="{{ asset("/") }}{{ $item->variant_primary_image }}" alt="" class="w100"></td>
                                <td>
                                    <p>{{ $item->product_variant_name }}</p>
                                    <p><strong>IG CODE : </strong>{{ $item->IG_CODE }}</p>
                                    <p><strong>BARCODE : </strong>{{ $item->barcode }}</p>
                                    <p><strong>SKIUD : </strong>{{ $item->sku_id }}</p>
                                </td>
                                <td>{{ $item->location }}</td>
                                <td>{{ $item->is_acknowledge == 1 ? 'Acknowledged' : 'Not acknowledged' }}</td>
                                <td>{{ $item->qty }}</td>
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
{{-- @include('admin.dispatch.item_assign_modal') --}}
@endsection
@push('custom_js')
{{-- <script src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/js/scripts/forms/checkbox-radio.min.js"></script> --}}
@endpush

