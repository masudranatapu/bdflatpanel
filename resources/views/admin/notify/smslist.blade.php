@extends('admin.layout.master')
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush
@section('notify_sms','active')

@section('title')Notification SMS @endsection
@section('page-name')Notification SMS @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('form.dashboard') </a></li>
    <li class="breadcrumb-item active">Notification SMS</li>
@endsection
@php
    $roles = userRolePermissionArray();
@endphp

@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush

@section('content')
    <!-- Alternative pagination table -->
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <div class="form-group">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="button" class="btn btn-success btn-sm  {{ request()->get('type') != 'success' ? 'active' : '' }}">
                                        <a href="{{ route('admin.notify_sms.list') }}"><i class="la la-th-list"></i> Pending</a>
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm {{ request()->get('type') == 'success' ? 'active' : '' }}"><a href="{{ route('admin.notify_sms.list',['type' => 'success']) }}"><i class="la la-list-ol"></i> Success</a></button>
                                    {{-- <a href="{{ URL::to('api/notification/all_notify_sms/send') }}" class="btn btn-sm btn-info">Send All Pending SMS</a> --}}
                                </div>
                            </div>
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
                            <div class="card-body card-dashboard ">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered alt-pagination50 table-sm" id="indextable">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;">Sl.</th>
                                                <th style="width: 40px;">Type</th>
                                                <th class="" style="width: 100px;">Name</th>
                                                <th class="" style="width: 100px;">Order ID</th>
                                                <th> SMS</th>
                                                <th style="width: 150px;" class="text-center">Mobile</th>
                                                <th style="width: 50px;" class="text-center">IS Send</th>
                                                <th style="width: 140px;" class="text-center">Time</th>
                                                <th style="width: 80px;">Active</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($rows && count($rows) > 0 )
                                                @foreach($rows as $key => $row )
                                                    <tr>
                                                        <td style="width: 40px;">{{ $key+1 }}</td>
                                                        <td style="width: 40px;">{{ $row->TYPE }}</td>
                                                        <td style="width: 100px;">
                                                            @if(isset($row->customer->NAME))
                                                            <a href="{{ route('admin.customer.view',[$row->customer->PK_NO]) }}" class="link" title="VIEW">{{  $row->customer->NAME  }}</a>
                                                            @endif
                                                            @if(isset($row->reseller->NAME))
                                                            <a href="{{ route('admin.reseller.edit', [$row->reseller->PK_NO]) }}" title="VIEW" class="link">
                                                            {{ $row->reseller->NAME  }}
                                                            </a>
                                                            @endif


                                                        </td>
                                                        <td style="width: 100px;">

                                                            <a href="{{ route("admin.booking_to_order.book-order-view", [$row->booking->PK_NO ?? 0 ]) }}" target="_blank" class="link" title="VIEW ORDER">@if($row->booking){{ '#ORD-'.$row->booking->BOOKING_NO ?? 0 }}@endif</a>

                                                        </td>
                                                        <td > {{ $row->BODY ?? ''}}</td>
                                                        <td style="width: 150px;" class="text-center">{{ $row->MOBILE_NO }}</td>
                                                        <td style="width: 50px;" class="text-center">{{ $row->IS_SEND == 1 ? 'Yes' : 'No' }}</td>
                                                        <td style="width: 140px;" class="text-center">
                                                            @if($row->SS_CREATED_ON)
                                                                <div title="Generated">
                                                                    {{ date('d-m-y h:i A',strtotime($row->SS_CREATED_ON)) }}
                                                                </div>
                                                            @endif
                                                            @if($row->SEND_AT)
                                                                <div style="border-top: 1px solid #eee;" title="Sent At">{{ date('d-m-y h:i A',strtotime($row->SEND_AT)) }}</div>
                                                            @endif
                                                        </td>
                                                        <td style="width: 80px;" class="text-center">
                                                            @if($row->IS_SEND == 0)
                                                                @if(hasAccessAbility('send_notify_sms', $roles))
                                                                    <a href="{{ route('admin.notify_sms.send', [$row->PK_NO]) }}" title="SEND SMS" class="btn btn-xs btn-primary mr-05"><i class="la la-send"></i></a>
                                                                @endif
                                                            @else
                                                                Sent
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
                    </div>
                </div>
            </div>
        </section>
</div>


      @include('admin.account._account_edit_modal')

    <!--/ Alternative pagination table -->
@endsection
@push('custom_js')

<!--script only for brand page-->
<script type="text/javascript" src="{{ asset('app-assets/pages/account.js')}}"></script>


@endpush('custom_js')
