@extends('admin.layout.master')
@section('user-group','active')
@section('User Management','open')
@section('title')
    @lang('user_group.edit_page_title')
@endsection
@section('page-name')
    @lang('user_group.list_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">@lang('user_group.breadcrumb_title')</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('admin.permission-group') }}">@lang('user_group.breadcrumb_sub_title')</a>
    </li>
    <li class="breadcrumb-item active">@lang('user_group.breadcrumb_title_active_2')
    </li>
@endsection
@section('content')
    <div class="col-md-12">
        <div class="card card-success min-height">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-colored-form-control">@lang('form.edit_group_form_title')</h4>
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
                    {!! Form::open([ 'route' => ['admin.user-group.update', $userGroup->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                    @csrf
                    <div class="form-body">
                        <div class="col-md-6 offset-3">
                            <div class="form-group">
                                <label>@lang('form.edit_group_form_filed_name')</label>
                                <div class="controls">
                                    {!! Form::text('user_group_name',$userGroup->GROUP_NAME, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => __('form.field_required'), 'placeholder' => __('form.edit_user_form_placeholder'), 'tabindex' => 1 ]) !!}
                                </div>
                                @if ($errors->has('user_group_name'))
                                    <div class="alert alert-danger">
                                        <strong>{{ $errors->first('user_group_name') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 offset-3">
                            <div class="form-group">
                                <label>@lang('form.new_group_form_filed_role')</label>
                                <div class="controls">
                                    {!! Form::select('role', $role, $userGroup->F_ROLE_NO, [ 'class' => 'form-control mb-1', 'placeholder' => 'Select role name', 'data-validation-required-message' => __('form.field_required')]) !!}
                                </div>
                                @if ($errors->has('role'))
                                    <span class="alert alert-danger">
                                            <strong>{{ $errors->first('role') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-actions text-center mt-3">
                            <a href="{{ route('admin.user-group') }}" title="Cancel" class="btn btn-warning mr-1"> <i class="ft-x"></i> @lang('form.btn_cancle') </a>
                            <button type="submit" class="btn btn-primary" title="Save"> <i class="la la-check-square-o"></i> @lang('form.btn_save')</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
