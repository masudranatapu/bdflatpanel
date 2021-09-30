@extends('admin.layout.master')
@section('permission','active')
@section('Role Management','open')
@section('title')
    @lang('admin_action.edit_page_title')
@endsection
@section('page-name')
    @lang('admin_action.edit_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">@lang('admin_action.breadcrumb_title')</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('admin.permission') }}">@lang('admin_action.breadcrumb_sub_title')</a>
    </li>
    <li class="breadcrumb-item active">@lang('admin_action.breadcrumb_title_active_2')
    </li>
@endsection
@section('content')
    <div class="col-md-12">
        <div class="card card-success min-height">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-colored-form-control">@lang('form.edit_action_form_title')</h4>
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
                    {!! Form::open([ 'route' => [ 'admin.permission.update', $permission->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                    @csrf
                    <div class="form-body">
                        <div class="col-md-6 offset-3">
                            <div class="form-group">
                                <label>@lang('form.new_action_form_field_menuslug')</label>
                                <div class="controls">
                                    {!! Form::text('permission_slug', $permission->NAME, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => __('form.field_required'), 'placeholder' => __('form.new_action_form_placeholder_menuslug'), 'tabindex' => 1 ]) !!}
                                </div>
                                @if ($errors->has('permission_slug'))
                                    <div class="alert alert-danger">
                                        <strong>{{ $errors->first('permission_slug') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 offset-3">
                            <div class="form-group">
                                <label>Display Name</label>
                                <div class="controls">
                                    {!! Form::text('display_name', $permission->DISPLAY_NAME, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => __('form.field_required'), 'placeholder' => __('form.new_action_form_placeholder_name'), 'tabindex' => 2 ]) !!}
                                </div>
                                @if ($errors->has('display_name'))
                                    <div class="alert alert-danger">
                                        <strong>{{ $errors->first('display_name') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 offset-3">
                            <div class="form-group">
                                <label>Permission Group</label>
                                <div class="controls">
                                    {!! Form::select('permission_group', $group, $permission->F_PERMISSION_GROUP_NO, [ 'class' => 'form-control mb-1', 'placeholder' => __('form.new_action_form_placeholder_menu'), 'data-validation-required-message' => __('form.field_required')]) !!}
                                </div>
                                @if ($errors->has('permission_group'))
                                    <div class="alert alert-danger">
                                        <strong>{{ $errors->first('permission_group') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-actions text-center">
                            <a href="{{ route('admin.permission') }}" title="Cancel" class="btn btn-warning mr-1"><i class="ft-x"></i> @lang('form.btn_cancle')</a>
                            <button type="submit" class="btn btn-primary" title="Update"> <i class="la la-check-square-o"></i> @lang('form.btn_edit')</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
