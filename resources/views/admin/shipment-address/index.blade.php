@extends('admin.layout.master')

@section('Shipping','open')
@section('shipping_address','active')

@section('title') Shipping Address @endsection
@section('page-name') Shipping Address List @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard  </a></li>
    <li class="breadcrumb-item active">Shipping Address </li>
@endsection

@php
    $roles = userRolePermissionArray();
    $shippment_address_type_arr   =  Config::get('static_array.shippment_address_type') ?? array();

@endphp
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush
@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            @if(hasAccessAbility('new_role', $roles))
                            <a class="text-white btn btn-round btn-sm btn-primary" href="{{ route('admin.shipping-address.create') }}" title="Add new">  <i class="ft-plus text-white"></i> New Address</a>
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
                            <div class="card-body card-dashboard">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered alt-pagination table-sm" id="indextable">
                                        <thead>
                                        <tr>
                                            <th class="text-center">SL.</th>
                                            <th style="width: 12%">Address Type</th>
                                            <th style="width: 25%">Personal Information</th>
                                            <th style="width: 10%">VAT EORI NO</th>
                                            <th>Attention</th>
                                            <th style="width: 5%">Status</th>
                                            <th style="width: 80px;" class="text-center">@lang('tablehead.tbl_head_action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($rows as $row)
                                            <tr>
                                                <td class="text-center">{{$loop->index + 1}}</td>
                                                <td class="text-center">
                                                    {{ $shippment_address_type_arr[$row->ADDRESS_TYPE] ?? '' }}

                                                </td>

                                                <td>
                                                    <p><b>Name:</b> &nbsp;{{ $row->NAME }}</p>
                                                    <p><b>Address 1:</b> {{ $row->ADDRESS_LINE_1 }}</p>
                                                    <p><b>Address 2:</b> {{ $row->ADDRESS_LINE_2 }}</p>
                                                    <p><b>Address 3:</b> {{ $row->ADDRESS_LINE_3 }}</p>
                                                    <p><b>Address 4:</b> {{ $row->ADDRESS_LINE_4 }}</p>
                                                    <p><b>Post Code:</b> {{ $row->POST_CODE }}</p>
                                                    <p><b>City:</b> {{ $row->CITY }}</p>
                                                    <p><b>State:</b> {{ $row->STATE }}</p>
                                                    <p><b>Country:</b> {{ $row->COUNTRY }}</p>

                                                </td>
                                                <td class="text-center"> {{ $row->VAT_EORI_NO }}</td>
                                                <td>
                                                    <p>{{ $row->ATTENTION }}</p>
                                                    <p><b>Phone No:</b> {{ $row->TEL_NO }}</p>
                                                </td>
                                                <td class="text-center">

                                                    @if( $row->IS_ACTIVE == 1 )
                                                    <p class="btn btn-success btn-xs">Active</p>
                                                    @else
                                                    <p class="btn btn-danger btn-xs">Inactive</p>
                                                    @endif
                                                </td>
                                                <td style="width: 80px;" class="text-center">
                                                    <a href="{{ route('admin.shipping-address.edit',$row->PK_NO) }}" class="btn btn-xs btn-info mr-1" title="Edit"><i class="la la-edit"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach()
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
@endsection
