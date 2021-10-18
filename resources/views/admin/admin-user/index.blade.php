@extends('admin.layout.master')
@section('admin-user','active')
@section('title')
    Admin User
@endsection
@section('page-name')
    Admin User
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Admin User
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
    $roles = userRolePermissionArray();
@endphp
@section('content')
    <!-- Alternative pagination table -->
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            @if(hasAccessAbility('add_admin_user', $roles))
                                <a class="text-white btn btn-sm btn-primary" href="{{url('admin-user/new')}}" title="Create Admin User">
                                    <i class="ft-user-plus text-white"></i> Create Admin User
                                </a>
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
                                    <table class="table display nowrap table-striped table-bordered  alt-pagination50 dataTables_scroll" id="indextable">
                                        <thead>
                                        <tr>
                                            <th>Sl.</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Designation</th>
                                            <th>Email</th>
                                            <th>Mobile no</th>
                                            <th>Group</th>
                                            <th>Role</th>
                                            <th>Can login</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($triggers as $row)
                                            <tr>
                                                <td>{{$loop->index + 1}}</td>
                                                <td>
                                                    <img align="middle" width="50" height="50"
                                                         src="{{$row->PROFILE_PIC_URL}}" alt="No image">
                                                </td>
                                                <td>{{$row->FIRST_NAME}} {{$row->LAST_NAME}}</td>
                                                <td>{{$row->USERNAME}}</td>
                                                <td>{{$row->DESIGNATION}}</td>
                                                <td>{{$row->EMAIL}}</td>
                                                <td>{{$row->MOBILE_NO}}</td>
                                                <td>{{$row->GROUP_NAME}}</td>
                                                <td>{{$row->NAME}}</td>
                                                @if($row->CAN_LOGIN == 0)
                                                    <td class="text-center"><i class='ft-crosshair text-danger'></i>
                                                    </td>
                                                @else
                                                    <td class="text-center"><i class='ft-check text-success'></i></td>
                                                @endif
                                                <td>
                                                    @if($row->STATUS == 0)
                                                        {{'Inactive'}}
                                                    @else
                                                        {{'Active'}}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(hasAccessAbility('edit_admin_user', $roles))
                                                    <a href="{{ route('admin.admin-user.edit', array($row->PK_NO)) }}" class="btn btn-xs btn-info mr-1" title="Edit"><i class="la la-edit"></i></a>
                                                    @endif
                                                    @if(hasAccessAbility('delete_admin_user', $roles))
                                                    <a href="{{ route('admin.admin-user.delete', [$row->PK_NO]) }}" onclick="return confirm('Are you sure you want to delete?')" class="btn btn-xs btn-danger mr-1" title="Delete"><i class="la la-trash"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
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
