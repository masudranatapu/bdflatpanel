@extends('admin.layout.master')
@section('permission-group','active')
@section('Role Management','open')
@section('title')
    @lang('admin_menu.new_page_title')
@endsection
@section('page-name')
    @lang('admin_menu.new_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ url('dashboard') }}">@lang('admin_menu.breadcrumb_title')</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.permission-group') }}">@lang('admin_menu.breadcrumb_sub_title')</a>
    </li>
    <li class="breadcrumb-item active">
        @lang('admin_menu.breadcrumb_title_active_1')
    </li>
@endsection
@section('content')
    <div class="card card-success min-height">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> @lang('form.new_menu_form_title')</h4>
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
                {!! Form::open([ 'route' => 'admin.permission-group.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                @csrf
                <div class="form-body">
                    <div class="col-md-6 offset-3">
                        <div class="form-group">
                            <label>@lang('form.new_menu_form_filed_name')</label>
                            <div class="controls">
                                {!! Form::text('permission_group_name', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' =>__('form.field_required'), 'placeholder' => __('form.new_menu_form_placeholder'), 'tabindex' => 1 ]) !!}
                            </div>
                            @if ($errors->has('permission_group_name'))
                                <div class="alert alert-danger">
                                    <strong>{{ $errors->first('permission_group_name') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-actions text-center mt-3">
                        <a href="{{ route('admin.permission-group') }}" class="btn btn-warning mr-1" title="Cancel"><i class="ft-x"></i>  @lang('form.btn_cancle')</a>
                        <button type="submit" class="btn btn-primary" title="Save"><i class="la la-check-square-o"></i> @lang('form.btn_save')</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    </div>
@endsection
