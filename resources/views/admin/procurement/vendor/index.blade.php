@extends('admin.layout.master')

@section('vendor','active')
@section('Procurement','open')

@section('title')Admin|Vendor @endsection
@section('page-name') @lang('vendor.list_page_sub_title') @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('vendor.breadcrumb_title')</a></li>
    <li class="breadcrumb-item active">@lang('vendor.breadcrumb_sub_title')</li>
@endsection

@php
    $roles = userRolePermissionArray()
@endphp

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@push('custom_js')
    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
@endpush

@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            @if(hasAccessAbility('new_vendor', $roles))
                                <a class="text-white btn btn-round btn-sm btn-primary" href="{{route('admin.vendor.new')}}" title="Add new vandor"> <i class="ft-plus text-white"></i> @lang('vendor.role_create_btn')</a>
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
                                    <table class="table table-striped table-bordered alt-pagination50 table-sm" id="indextable">
                                        <thead>
                                        <tr>
                                            <th class="text-center">SL.</th>
                                            <th>Name</th>
                                            <th>Address</th>
                                            <th>Phone</th>
                                            <th>Country</th>
                                            <th>Has Loyalty</th>
                                            <th style="width: 80px;" class="text-center">@lang('tablehead.tbl_head_action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($rows as $row)
                                            <tr>
                                                <td class="text-center">{{$loop->index + 1}}</td>
                                                <td>{{ $row->NAME }}</td>
                                                <td>{{ $row->ADDRESS }}</td>
                                                <td>{{ $row->PHONE }}</td>
                                                <td>{{ $row->COUNTRY }}</td>
                                                <td>
                                                    @if($row->HAS_LOYALITY == 1)
                                                        Yes
                                                    @else
                                                        No
                                                    @endif
                                                </td>
                                                <td style="width: 100px;" class="text-center">
                                                    @if(hasAccessAbility('edit_vendor', $roles))
                                                        <a href="{{ route('admin.vendor.edit', [$row->PK_NO]) }}" class="btn btn-xs btn-info" title="Cancel"><i class="la la-edit"></i></a>
                                                    @endif
                                                    @if(hasAccessAbility('delete_vendor', $roles))
                                                        <a href="{{ route('admin.vendor.delete', [$row->PK_NO]) }}" onclick="return confirm('Are You Sure?')" class="btn btn-xs btn-danger" title="Delete"><i class="la la-trash"></i></a>
                                                    @endif
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
