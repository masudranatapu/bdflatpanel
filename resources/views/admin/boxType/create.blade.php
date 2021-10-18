@extends('admin.layout.master')
@section('box_type_list','active')
@section('title')
    @lang('admin_action.new_page_title')
@endsection
@section('page-name')
    @lang('admin_action.new_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ url('dashboard') }}">@lang('admin_action.breadcrumb_title')</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ url('permission') }}">@lang('admin_action.breadcrumb_sub_title')</a>
    </li>
    <li class="breadcrumb-item active">
        @lang('admin_action.breadcrumb_title_active_1')
    </li>
@endsection
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
@endpush
@section('content')
    <div class="card card-success min-height">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-success"></i>@lang('form.new_action_form_title')</h4>
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
                {!! Form::open([ 'route' => 'admin.box_type.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                {!! Form::hidden('box_pk', $data->PK_NO ?? 0) !!}
                @csrf
                <div class="form-body">
                    <div class="col-md-6 offset-3">
                        <div class="form-group">
                            <label>Enter Type</label>
                            <div class="controls">
                                {!! Form::text('type', $data->TYPE ?? null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => __('form.field_required'), 'placeholder' => 'Enter Type', 'tabindex' => 1 ]) !!}
                            </div>
                            @if ($errors->has('type'))
                                <div class="alert alert-danger">
                                    <strong>{{ $errors->first('type') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 offset-3">
                        <div class="form-group">
                            <label>Width</label>
                            <div class="controls">
                                {!! Form::number('width', $data->WIDTH_CM ??  null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => __('form.field_required'), 'placeholder' => 'Enter width (cm)', 'tabindex' => 2 ]) !!}
                            </div>
                            @if ($errors->has('width'))
                                <div class="alert alert-danger">
                                    <strong>{{ $errors->first('width') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 offset-3">
                        <div class="form-group">
                            <label>Length</label>
                            <div class="controls">
                                {!! Form::number('length', $data->LENGTH_CM ??  null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => __('form.field_required'), 'placeholder' => 'Enter length (cm)', 'tabindex' => 2 ]) !!}
                            </div>
                            @if ($errors->has('length'))
                                <div class="alert alert-danger">
                                    <strong>{{ $errors->first('length') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 offset-3">
                        <div class="form-group">
                            <label>Height</label>
                            <div class="controls">
                                {!! Form::number('height', $data->HEIGHT_CM ??  null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => __('form.field_required'), 'placeholder' => 'Enter height (cm)', 'tabindex' => 2 ]) !!}
                            </div>
                            @if ($errors->has('height'))
                                <div class="alert alert-danger">
                                    <strong>{{ $errors->first('height') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-actions text-center mt-3">
                        <a href="{{ route('admin.permission') }} " class="btn btn-warning mr-1" title="Cancel"><i class="ft-x"></i> @lang('form.btn_cancle')</a>
                        <button type="submit" class="btn btn-primary"><i class="la la-check-square-o"></i>{{ isset($data->PK_NO) ? 'Update' : 'Save' }} </button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
@endpush
