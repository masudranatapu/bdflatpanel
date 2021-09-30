@extends('admin.layout.master')
@section('user-group','active')
@section('User Management','open')
@section('title')
    @lang('user_group.list_page_title')
@endsection
@section('page-name')
    @lang('user_group.list_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">@lang('user_group.breadcrumb_title')</a>
    </li>
    <li class="breadcrumb-item active">@lang('user_group.breadcrumb_sub_title')
    </li>
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
        $roles = userRolePermissionArray()
@endphp
@section('content')
    <!-- Alternative pagination table -->
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            @if(hasAccessAbility('new_user_group', $roles))
                                <a class="text-white btn btn-round btn-sm btn-primary" href="{{route('admin.user-group.new')}}" title="Add new user group"><i class="ft-plus text-white"></i> @lang('user_group.btn_menu_create')</a>
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
                            <div class="card-body card-dashboard text-center">
                                <div class="table-responsive p-1">
                                    <table class="table table-striped table-bordered alt-pagination" id="indextable">
                                        <thead>
                                        <tr>
                                            <th>@lang('tablehead.tbl_head_sl')</th>
                                            <th>@lang('tablehead.tbl_head_name')</th>
                                            <th>@lang('tablehead.tbl_head_role_assigned')</th>
                                            <th>@lang('tablehead.tbl_head_created_at')</th>
                                            <th>@lang('tablehead.tbl_head_action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($rows as $row)
                                            <tr>
                                                <td>{{$loop->index + 1}}</td>
                                                <td>{{$row->GROUP_NAME}}</td>
                                                <td>
                                                    <a href="{{ route('admin.role.edit', [$row->role_pk]) }}" target="_blank">
                                                        {{$row->NAME}}
                                                    </a>
                                                </td>
                                                <td>{{$row->CREATED_AT}}</td>
                                                <td>
                                                    @if(hasAccessAbility('edit_user_group', $roles))
                                                        <a class="text-white btn btn-xs btn-primary mr-1" href="{{ route('admin.user-group.edit', [$row->PK_NO]) }}" title="Edit"><i class="la la-edit"></i></a>
                                                    @endif
                                                    @if(hasAccessAbility('delete_user_group', $roles))
                                                        <a class="text-white btn btn-xs btn-danger mr-1" href="{{ route('admin.user-group.delete', [$row->PK_NO]) }}" title="Delete"><i class="la la-trash"></i></a>
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

    <!--/ Alternative pagination table -->
@endsection
