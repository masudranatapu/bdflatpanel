@extends('admin.layout.master')
@section('role','active')
@section('Role Management','open')
@section('title')
    Role
@endsection
@section('page-name')
    Role Management
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('admin.role') }}">Role</a>
    </li>
    <li class="breadcrumb-item active">New Role
    </li>
@endsection
@section('content')
    <div class="card card-success min-height">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> Add New
                Role</h4>
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
                {!! Form::open([ 'route' => 'admin.role.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                @csrf
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Role Name :</strong></label>
                                <div class="controls">
                                    {!! Form::text('role_name', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter group name', 'tabindex' => 1 ]) !!}
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
                                    <td>
                                        {{ $group->group_name }}
                                    </td>
                                    @for($i = 0; $i < 5; $i++)
                                        <td style="vertical-align: middle">
                                            @if(isset($group->permissions[$i]->name) && $group->permissions[$i]->name != '')
                                            <label class="m-checkbox">
                                                <input name="permission[]" type="checkbox" value="{{ $group->permissions[$i]->name }}">
                                                <span></span>
                                            </label>
                                            @else {{'--' }}
                                            @endif
                                        </td>
                                    @endfor
                                    <td>
                                        @for($i = 5; $i < count($group->permissions); $i++)
                                            @if(isset($group->permissions[$i]->name) && $group->permissions[$i]->name != '')
                                                <label class="checkbox-inline">
                                                    <input name="permission[]" type="checkbox" value="{{ $group->permissions[$i]->name }}">
                                                     {{ $group->permissions[$i]->display_name }}
                                                </label> <br/>
                                            @else {{'--' }}
                                            @endif
                                        @endfor
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="form-actions text-center">
                    <a href="{{ route('admin.role') }}">
                        <button type="button" class="btn btn-warning mr-1">
                            <i class="ft-x"></i>  @lang('form.btn_cancle')
                        </button>
                    </a>
                    <button type="submit" class="btn bg-primary bg-darken-1 text-white">
                        <i class="la la-check-square-o"></i> @lang('form.btn_save')
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    </div>
@endsection
