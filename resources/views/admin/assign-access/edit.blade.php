@extends('admin.layout.master')
@section('role','active')
@section('Role Management','open')
@section('title')
    @lang('admin_role.edit_page_title')
@endsection
@section('page-name')
    @lang('admin_role.list_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{url('/admin.admin-user')}}">@lang('admin_role.edit_page_breadcrumb_title_1')</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('admin.role') }}">@lang('admin_role.edit_page_breadcrumb_title_2')</a>
    </li>
    <li class="breadcrumb-item active">@lang('admin_role.edit_page_breadcrumb_title_active')
    </li>
@endsection
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}">
@endpush
@section('content')
    <div class="col-md-12">
        <div class="card card-success min-height">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-colored-form-control">User Profile</h4>
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
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div>{{$error}}</div>
                        @endforeach
                    @endif
                    {!! Form::open([ 'route' => ['admin.role.update', $role->id] , 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                    @csrf
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Role Name :</strong></label>
                                    <div class="controls">
                                        {!! Form::text('role_name',$role->role_name, [ 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter group name', 'tabindex' => 1 ]) !!}
                                    </div>
                                    @if ($errors->has('role_name'))
                                        <div class="alert alert-danger">
                                            <strong>{{ $errors->first('role_name') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $assigned_perms = explode(",",$role->permission->permissions);
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-primary bg-darken-1 text-white text-center">
                            <tr>
                                <th>Menu</th>
                                <th>Action</th>

                            </tr>
                            </thead>
                            <tbody class="text-center">
                            @foreach($groups as $group)
                                <tr>
                                    <td class="text-left">{{ $group->group_name }}</td>
                                    <td class="row skin skin-flat">
                                        @foreach($group->permissions as $i => $permissions)
                                            <fieldset style="margin-left: 68px;">
                                                @if(in_array($group->permissions[$i]->name, $assigned_perms) && $group->permissions[$i]->name != '')
                                                    <label class="m-checkbox" for="input-15">
                                                        <input name="permission[]" type="checkbox" checked="checked"
                                                               class="icheckbox_flat-pink checked"
                                                               value="{{ $group->permissions[$i]->name }}"
                                                               id="input-15">
                                                        <span>{{ $group->permissions[$i]->display_name }}</span>
                                                    </label>
                                                @else
                                                    <label class="m-checkbox" for="input-15">
                                                        <input name="permission[]" type="checkbox"
                                                               class="icheckbox_flat-pink"
                                                               value="{{ $group->permissions[$i]->name }}"
                                                               id="input-15">
                                                        <span>{{ $group->permissions[$i]->display_name }}</span>
                                                    </label>
                                                @endif
                                            </fieldset>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="form-actions text-center">
                        <a href="{{ route('admin.role') }}">
                            <button type="button" class="btn btn-warning mr-1">
                                <i class="ft-x"></i> Cancel
                            </button>
                        </a>
                        <button type="submit" class="btn bg-primary bg-darken-1 text-white">
                            <i class="la la-check-square-o"></i> Save changes
                        </button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
