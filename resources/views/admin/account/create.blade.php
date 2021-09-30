@extends('admin.layout.master')
@section('Payment Management','active')
@section('title')
    Create Account Source
@endsection
@section('page-name')
    Create Account Source
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('payment.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">@lang('payment.breadcrumb_sub_title')    </li>
@endsection

@section('content')

<section id="basic-form-layouts" class="min-height">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-content collapse show card-success">
                                    <div class="card-body">
                                        {!! Form::open([ 'route' => 'account.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                                            <div class="row">


                                            <div class="col-md-4 offset-4">
                                                <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                                    <label>@lang('form.product_form_field_name')<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::text('name', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter account source', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                                        {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                            <div class="form-actions text-center mt-3">
                                                <a href="{{ route('admin.account.list') }}">
                                                    <button type="button" class="btn btn-warning mr-1">
                                                        <i class="ft-x"></i>@lang('form.btn_cancle')
                                                    </button>
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="la la-check-square-o"></i>@lang('form.btn_save')
                                                </button>
                                        {!! Form::close() !!}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </section>
@endsection
