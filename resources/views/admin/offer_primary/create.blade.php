@extends('admin.layout.master')

@section('offer_primary_list','active')
@section('offer_management','open')

@section('title') Create Offer primary list @endsection
@section('page-name') Create Offer primary list @endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('form.dashboard')  </a></li>
    <li class="breadcrumb-item active">@lang('form.offer_primary_list_add')    </li>
@endsection

@section('content')

<section id="basic-form-layouts">
    <div class="row match-height min-height">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-content collapse show">
                        <div class="card-body">
                            {!! Form::open([ 'route' => 'admin.offer_primary.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                                <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                        <label>@lang('form.name')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('name', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter name', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group {!! $errors->has('comment') ? 'error' : '' !!}">
                                        <label>@lang('form.description')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::textarea('comment', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter description', 'data-validation-required-message' => 'This field is required', 'tabindex' => 2, 'rows' => 3 ]) !!}
                                            {!! $errors->first('comment', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>

                            </div>
                                <div class="form-actions text-center mt-3">
                                    <a href="{{ route('admin.offer_primary.list') }}" class="btn btn-warning mr-1" title="Cancel"><i class="ft-x"></i> @lang('form.btn_cancle')</a>
                                    <button type="submit" class="btn btn-primary" title="Save"><i class="la la-check-square-o"></i> @lang('form.btn_save') </button>


                        </div>
                    {!! Form::close() !!}


                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
