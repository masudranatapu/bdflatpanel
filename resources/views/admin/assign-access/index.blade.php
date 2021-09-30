@extends('admin.layout.master')

@section('assign-access','active')
@section('User Management','open')
@section('title')
    @lang('assign_access.list_page_title')
@endsection
@section('page-name')
    @lang('assign_access.list_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('assign_access.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('assign_access.breadcrumb_sub_title')
    </li>
@endsection
@php
        $roles = userRolePermissionArray()
@endphp
@section('content')
    <div class="content-body min-height">
        <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form-center">@lang('assign_access.assign_access_from_title')</h4>
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
                                <!--Start Search From -->
                                <div class="card-content collapse show">
                                    <div class="card-body">

                                        <div class="card-text">
                                            <p>&nbsp;</p>
                                        </div>
                                        <div class="row justify-content-md-center">
                                            <div class="col-lg-6 col-md-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4 class="card-title">Search user by username or email</h4>
                                                    </div>
                                                    <div class="card-content">
                                                        <div class="card-body">

                                                            {!! Form::open([ 'route' => 'admin.assign-access', 'method' => 'post', 'class' => 'form error', 'files' => true , 'novalidate']) !!}
                                                                @csrf
                                                                <div class="form-body">
                                                                    {!! Form::text('search_string', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' =>__('form.field_required'), 'placeholder' => 'Search user by username or email', 'tabindex' => 1 ]) !!}
                                                                </div>
                                                                @if ($errors->has('search_string'))
                                                                    <div class="help-block">
                                                                        <strong>{{ $errors->first('search_string') }}</strong>
                                                                    </div>
                                                                @endif
                                                                <div class="form-actions border-0 pb-0 text-right">
                                                                    <button type="submit" class="btn btn-info" title="Search"><i class="la la-search-plus"></i> @lang('form.btn_search')
                                                                    </button>
                                                                </div>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--End Search From -->
                                @if($userName)
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form-center">Showing Result For  <i>{{$userName}}</i></h4>

                                </div>
                                @endif
                                <!--Start Search Result If not Empty -->
                                @if($triggers)
                                <div class="card-content collapse show">
                                    <div class="card-body card-dashboard text-center">
                                        <div class="table-responsive">

                                            <table class="table display nowrap table-striped table-bordered alt-pagination dataTables_scroll" id="indextable">
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
                                                            <a href="#" class="btn btn-social btn-min-width mb-1 mr-1 btn-vimeo"><span class="la la-anchor font-medium-4"></span> @lang('assign_access.assign_access_btn')</a>
                                                            @endif

                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <!--End Search Result If not Empty -->
                            </div>
                        </div>
                    </div>
        </section>
    </div>
@endsection
