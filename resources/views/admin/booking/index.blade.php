@extends('admin.layout.master')

@section('Order Management','open')
@section('booking_list','active')

@section('title') @lang('booking.list_page_title') @endsection
@section('page-name') @lang('booking.list_page_sub_title') @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('booking.breadcrumb_title')</a></li>
    <li class="breadcrumb-item active">@lang('booking.breadcrumb_sub_title')</li>
@endsection
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush

@php
    $roles = userRolePermissionArray();

@endphp

@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            @if(hasAccessAbility('new_booking', $roles))
                                @if(isset($info['type']) && ($info['type'] == 'customer'))
                                    <a href="{{route('admin.booking.create',['id' => $info['PK_NO'], 'type' => 'customer' ])}}" class="btn btn-round btn-sm btn-primary text-white" title="Add booking"><i class="ft-plus text-white"></i> @lang('booking.role_create_btn')</a>
                                @elseif(isset($info['type']) && ($info['type'] == 'reseller'))
                                    <a href="{{route('admin.booking.create',['id' => $info['PK_NO'], 'type' => 'reseller' ])}}" class="btn btn-round btn-sm btn-primary text-white" title="Add booking"><i class="ft-plus text-white"></i> @lang('booking.role_create_btn')</a>

                                @else
                                    <a href="{{route('admin.booking.search_create')}}" class="btn btn-sm btn-primary text-white" title="Add booking"><i class="ft-plus text-white"></i> @lang('booking.role_create_btn')</a>

                                @endif
                            @endif
                            <a class="heading-elements-toggle" href="javascript:void(0)"><i class="la la-ellipsis-v font-medium-3"></i></a>
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
                                <div class="table-responsive p-1">
                                    <table class="table table-striped table-bordered alt-pagination table-sm" id="indextable">
                                        <thead>
                                        <tr>
                                            <th class="text-center">@lang('tablehead.sl')</th>
                                            <th>Booking ID</th>
                                            <th>@lang('tablehead.customer')</th>
                                            <th>@lang('tablehead.sales_agent')</th>
                                            <th>@lang('tablehead.quantity')</th>
                                            <th>@lang('tablehead.booking_time')</th>
                                            <th>@lang('tablehead.expired_time')</th>
                                            <th class="text-center">@lang('tablehead.tbl_head_action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                use Carbon\Carbon;
                                                $current_time = Carbon::now(); ?>
                                            @foreach ($rows as $row)

                                            @if (empty($row->getOrder->F_BOOKING_NO))
                                            <tr>
                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                <td class="text-center">{{ 'BKNG-'.$row->BOOKING_NO }}</td>
                                                <td>
                                                    <span class="text-upercase">{{ $row->CUSTOMER_NAME ?? $row->RESELLER_NAME }}</span>
                                                </td>
                                                <td><span class="text-upercase">{{ $row->BOOKING_SALES_AGENT_NAME }}</span></td>
                                                <td>{{ $row->booking_details->count() ?? 0 }}</td>
                                                <td>{{ date('d-m-Y h:i a', strtotime($row->BOOKING_TIME)) }}</td>
                                                <td>
                                                    <div class=" {{ (date($current_time) > date($row->EXPIERY_DATE_TIME)) ? 'text-danger' : '' }} ">
                                                        <span>{{ date('d-m-Y h:i a', strtotime($row->EXPIERY_DATE_TIME)) }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($row->BOOKING_STATUS < 50)
                                                    @if(hasAccessAbility('edit_booking', $roles))
                                                        <a href="{{ route('admin.booking.edit',['id' => $row->PK_NO, 'checkoffer' => 1]) }}" class="btn btn-xs btn-info" title="Edit"><i class="la la-edit"></i></a>
                                                    @endif
                                                    @endif

                                                    @if(hasAccessAbility('view_booking', $roles))
                                                    <a href="{{ route('admin.booking.view',$row->PK_NO) }}"  class="btn btn-xs btn-success" title="View"><i class="la la-eye"></i></a>
                                                    @endif
                                                    @if($row->BOOKING_STATUS < 50)
                                                    @if(hasAccessAbility('delete_booking', $roles))
                                                        <a href="{{ route('admin.booking.delete', $row->PK_NO) }}" onclick="return confirm('Are you sure you want to delete ?')" class="btn btn-xs btn-danger" title="Delete"><i class="la la-trash"></i></a>
                                                    @endif
                                                    @endif

                                                </td>
                                            </tr>
                                            @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1" title="Back"><i class="la la-backward" ></i> Back</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
