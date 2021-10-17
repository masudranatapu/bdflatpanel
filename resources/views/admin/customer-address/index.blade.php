@extends('admin.layout.master')
@section('Address Type','active')
@section('title')
Customer Address Type
@endsection
@section('page-name')
Customer Address Type
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('customer_address.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">@lang('customer_address.breadcrumb_sub_title')    </li>
@endsection

@section('content')

<section id="basic-form-layouts">
    <div class="row match-height min-height">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-content collapse show">
                    <div class="card-body">
                        {!! Form::open([ 'route' => 'admin.customer-address.list', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                            <div class="row">
                                <div class="col-md-4 offset-4">
                                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                        <label>@lang('form.name')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('name', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter Customer Address Type', 'tabindex' => 2 ]) !!}
                                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                </div>
                                <div class="form-actions text-center mt-3">
                                    <a href="#" class="btn btn-warning mr-1"><i class="ft-x"></i>@lang('form.btn_cancle')</a>
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
