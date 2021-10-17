@extends('admin.layout.master')

@section('Sales Agent','open')
@section('agent_list','active')

@section('title') @lang('agent.list_page_title') @endsection
@section('page-name') @lang('agent.list_page_sub_title') @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('agent.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active">@lang('agent.breadcrumb_sub_title')</li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{asset('/custom/css/custom.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@push('custom_js')

<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush

@php
    $roles = userRolePermissionArray()
@endphp

@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            @if(hasAccessAbility('new_agent', $roles))
                                <a class="text-white btn btn-sm btn-primary" href="{{route('agent.create')}}" title="Add new"><i class="ft-plus text-white"></i>@lang('agent.create_btn')</a>
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
                                <div class="table-responsive p-1">
                                    <table class="table table-striped table-bordered alt-pagination table-sm" id="indextable">
                                        <thead>
                                        <tr class="small_table_head">
                                            <th>SL.</th>
                                            <th>Name</th>
                                            <th>Moblie No</th>
                                            <th>Email</th>
                                            <th>Total Order</th>
                                            <th>Total Balance</th>
                                            <th style="width: 100PX">@lang('tablehead.tbl_head_action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($rows as $row)
                                            <tr class="small_table_data">
                                                <td>{{$loop->index + 1}}</td>
                                                <td><span class="text-upper">{{ $row->NAME }}</span></td>
                                                <td>{{ $row->MOBILE_NO }}</td>
                                                <td>{{ $row->EMAIL }}</td>
                                                <td>{{ $row->CUM_ORDERS_QTY }}</td>
                                                <td>{{ $row->CUM_BALANCE }}</td>
                                                <td style="text-align: center">
                                                    @if(hasAccessAbility('edit_agent', $roles))
                                                        <a href="{{ route('admin.agent.edit', [$row->PK_NO]) }}" title="View & Edit Agent" class="btn btn-xs btn-info mr-1"><i class="la la-edit" ></i> </a>
                                                    @endif
                                                    @if(hasAccessAbility('delete_agent', $roles))
                                                        <a href="{{ route('admin.agent.delete', [$row->PK_NO]) }}" onclick="return confirm('Are you sure you want to delete?')" title="Delete Agent" class="btn btn-xs btn-danger mr-1"><i class="la la-trash"></i></a>
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
