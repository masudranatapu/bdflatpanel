@extends('admin.layout.master')
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
<style>
    .customer-info{color: blue !important; font-size: 18px;}
    .customer-info p{ text-align: right;}
    .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
    border: 1px solid #ddd!important;
    vertical-align: top!important;
}
</style>
@endpush

@section('Customer Management','open')
@section('customer_list','active')


@section('title') Customer History @endsection

@section('page-name') Customer History @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">@lang('customer.breadcrumb_title')</a></li>
    <li class="breadcrumb-item active">Customer History</li>
@endsection

@php
    $roles          = userRolePermissionArray();
    $balance        = 0;
    $html           = array();
    $cum_balance    = 0;
    $cum_order_due  = 0;
@endphp

@section('content')
    <div class="content-body min-height">
        @include('admin.customer._customerhistory')
    </div>

    <div class="modal fade text-left" id="balanceHistoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"  aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="myModalLabel1">Customer Balance</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="cust_balance_details_html"><div class="table-responsive">
                        <table class="table table-bordered thead-light">
                          <thead>
                            <tr>
                              <th style="width: 10%" class="text-center">SL</th>
                              <th style="width: 15%" class="text-center">Date</th>
                              <th>Description</th>
                              <th style="width: 12%;" class="text-center">IN <small>(RM)</small></th>
                              {{-- <th style="width: 12%;" class="text-center">OUT <small>(RM)</small></th> --}}
                              <th style="width: 15%;" class="text-center">Balance <small>(RM)</small></th>
                            </tr>
                          </thead>
                            <tbody>
                              @if(isset($balance_history) && count($balance_history) > 0 )
                              @foreach($balance_history as $ke =>  $value)
                              @php $cum_balance += $value->PAYMENT_REMAINING_MR @endphp
                                <tr>
                                <td class="text-center">{{ $ke+1 }}</td>
                                <td class="text-center">{{ date('d M, y',strtotime($value->PAYMENT_DATE)) }}</td>
                                <td>Over payment <a href="{{ route('admin.payment.details',['id' => $value->bankTxn->PK_NO ]) }}" class="">PAYID - {{ $value->bankTxn->CODE }}</a></td>
                                <td class="text-right"> {{ number_format($value->PAYMENT_REMAINING_MR,2) }} </td>
                                {{-- <td class="text-center"></td> --}}
                                <td class="text-right">{{ number_format($cum_balance,2) }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="text-center text-warning" colspan="5">NO DATA</td>
                                </tr>
                                @endif

                                </tbody>
                            </table>
                        </div>
                      </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

