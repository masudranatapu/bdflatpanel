@extends('admin.layout.master')
@section('Address Type','active')
@section('title')
Customer Address Type
@endsection
@section('page-name')
Customer Address Type
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('address_type.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">@lang('address_type.breadcrumb_sub_title')    </li>
@endsection

@section('content')

<section id="basic-form-layouts" class="min-height">
    <div class="row match-height">
        <div class="col-md-12">
            <div class="card">
                <div class="card-content card-success">
                    <div class="card-body">
                        {!! Form::open([ 'route' => 'admin.address_type.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                            <div class="row">
                            <div class="col-md-4 offset-4">
                            <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                                    <label>@lang('form.product_form_field_name')<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::text('name', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter address type', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                                        {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                </div>
                                </div>
                                <div class="form-actions text-center mt-3">
                                    <a href="{{ route('admin.address_type.list') }}" class="btn btn-warning mr-1"><i class="ft-x"></i>@lang('form.btn_cancle')</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="la la-check-square-o"></i>@lang('form.btn_save')
                                    </button>
                                </div>


                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
