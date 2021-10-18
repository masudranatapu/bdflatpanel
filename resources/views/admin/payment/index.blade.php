@extends('admin.layout.master')

@section('Payment','open')
@section('payment_list','active')

@section('title') Payment @endsection
@section('page-name') @lang('paymentc.list_page_sub_title') @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.invoice') }}"> Payment </a></li>
    <li class="breadcrumb-item active">Payment List</li>
@endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <style>
        #scrollable-dropdown-menu .tt-menu {max-height: 260px; overflow-y: auto; width: 100%; border: 1px solid #333; border-radius: 5px;}
        .twitter-typeahead {display: block !important;}
        #indextable td{vertical-align: middle}
    </style>
@endpush

<?php
    $key = 0;
    $roles = userRolePermissionArray();
?>

@section('content')
<div class="card card-success min-height">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-eye text-primary" style="padding-right: 5px; "></i>Payment</h4>
        <br/>
        @if(isset($data['customer_info']))
            for <i>{{ $data['customer_info']->NAME }} </i>
        @else
        <div class="btn-group mr-1 mb-1" style="position: absolute; top: 15px; left: 10%; ">
            <button type="button" class="btn btn-sm btn-success btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="la la-camera"></i>
                {{ $data['selected'] ?? '' }}</button>
            <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 40px, 0px);">
                <a class="dropdown-item" href="{{ route('admin.payment.list') }}">All Payment (Not Verified)</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('admin.payment.list',['type' => 'reseller']) }}">Reseller (Not Verified) </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('admin.payment.list',['type' => 'customer']) }}">Customer (Not Verified)</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('admin.payment.list',['type' => 'verified']) }}">All Payment (Verified)</a>

            </div>

            <a class="btn btn-sm btn-success all_cod {{ request()->get('type') == 'cod' ? 'active' : '' }}" href="{{ route('admin.payment.list',['type' => 'cod']) }}">ALL COD</a>
        </div>

        @endif

        @if($errors->any())
        {{ implode($errors->all(':message')) }}
        @endif
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
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-striped table-bordered alt-pagination50 table-sm" id="indextable">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">Sl.</th>
                        <th class="" style="width: 80px;">Entry Date</th>
                        <th class="" style="width:100px;">Entry By</th>
                        <th style="width: 70px;">Pay Date</th>
                        <th >Customer Name</th>
                        <th >Paid By</th>
                        <th style="width: 100px;" >Ref</th>
                        <th style="width: 100px;" class="text-center">Image</th>
                        <th style="width: 80px;">Via</th>
                        <th style="width: 80px;">Amount(RM)</th>
                        <th style="width: 60px;" class="text-center">Active</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($data['rows']) && count($data['rows']) > 0 )
                        @foreach($data['rows'] as $key => $row )
                            <tr @if($row->PAYMENT_CONFIRMED_STATUS == 0 )  @endif>
                                <td class="text-center">{{ $key+1 }}</td>
                                <td style="width: 80px;" class="text-center">
                                   <div style="font-size:12px; font-weight: 600;">
                                    {{ date('d-m-Y',strtotime($row->SS_CREATED_ON)) }}
                                    </div>

                                    <div style="font-size:12px;">
                                    {{ date('h:i A',strtotime($row->SS_CREATED_ON)) }}
                                   </div>
                                   @if( ($row->IS_MATCHED == 1) && ($row->MATCHED_ON))
                                   <div style="font-size:12px; font-weight: 600; border-top: 1px solid #000;">
                                    {{ date('d-m-Y',strtotime($row->MATCHED_ON)) }}
                                    </div>

                                    <div style="font-size:12px;">
                                    {{ date('h:i A',strtotime($row->MATCHED_ON)) }}
                                   </div>
                                   @endif
                                </td>
                                <td style="width:100px; text-transform: uppercase;">{{ $row->entryBy->USERNAME ?? '' }}</td>
                                <td>{{ date('d-m-Y', strtotime($row->PAYMENT_DATE) ) }}</td>
                                <td class="text-uppercase">
                                    <a href="" class="font-bold">
                                        {{ $row->CUSTOMER_NAME ?? ''}}
                                    </a>
                                </td>
                                <td class="text-uppercase">
                                     {{ $row->PAID_BY  ?? $row->CUSTOMER_NAME }} </td>
                                <td style="width:100px;">
                                    {{ $row->SLIP_NUMBER }} <br>
                                    <a href="{{ route('admin.payment.details',['id' =>  $row->PK_NO ]) }}" class="font-bold">{{ 'PAYID-'.$row->CODE ?? '' }}</a>
                                </td>
                                <td class="text-center" >
                                    @if($row->ATTACHMENT_PATH)
                                    <a href="{{ asset($row->ATTACHMENT_PATH) }}" target="_blank"  >
                                        <img src="{{ asset($row->ATTACHMENT_PATH) }}"  style="width: 60px;" >
                                    </a>
                                    @endif
                                </td>
                                <td >
                                    {{ $row->PAYMENT_BANK_NAME }}
                                    <br>
                                    <span style="font-size:10px;">{{ $row->PAYMENT_ACCOUNT_NAME }}</span>
                                </td>
                                <td class="text-right">{{ number_format($row->MR_AMOUNT,2) }}</td>

                                <td class="text-center" style="width: 5%;">
                                    @if(hasAccessAbility('view_payment', $roles))
                                    <a href="{{ route('admin.payment.details',['id' =>  $row->PK_NO ]) }}" class="btn btn-xs btn-success mr-05" title="VIEW PAYMENT"><i class="la la-eye"></i></a>
                                    @endif
                                    @if(hasAccessAbility('delete_payment', $roles))
                                    @if($row->IS_COD == 0)
                                    <a href="{{ route('admin.payment.delete',['id' =>  $row->PK_NO]) }}" class="btn btn-xs btn-danger mr-05" title="DELETE PAYMENT" onclick="return confirm('Are you sure want to delete this?');"><i class="la la-trash"></i></a>
                                    @endif
                                    @endif

                                </td>
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

    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
    <script type="text/javascript" src="{{ asset('app-assets/file_upload/image-uploader.min.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>

    <script>
        $('.pickadate').pickadate({
            format: 'dd-mm-yyyy',
            formatSubmit: 'dd-mm-yyyy',
        });
    </script>

@endpush('custom_js')
