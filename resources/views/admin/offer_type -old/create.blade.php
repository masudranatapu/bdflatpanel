@extends('admin.layout.master')
@section('offer_type','active')
@section('offer_management','open')
@section('title')
    Offer type
@endsection
@section('page-name')
    Offer type
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('payment.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">@lang('payment.breadcrumb_sub_title')    </li>
@endsection

@section('content')

<section id="basic-form-layouts">
    <div class="row match-height min-height">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-content collapse show">
                    <div class="card-body">
                        {!! Form::open([ 'route' => 'admin.offer_type.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                        <label>@lang('form.product_form_field_name')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('name', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter offer type name', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('public_name') ? 'error' : '' !!}">
                                        <label>@lang('form.public_name')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('public_name', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter offer type public name', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('public_name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('p_amount') ? 'error' : '' !!}">
                                        <label>@lang('form.p_amount')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p_amount', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter P amount', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('p_amount', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('p2_amount') ? 'error' : '' !!}">
                                        <label>@lang('form.p2_amount')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p2_amount', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter P amount', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('p2_amount', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('p_ss') ? 'error' : '' !!}">
                                        <label>@lang('form.p_ss')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p_ss', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter SS postage for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('p_ss', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('p_sm') ? 'error' : '' !!}">
                                        <label>@lang('form.p_sm')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p_sm', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter SM postage for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('p_sm', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('p_air') ? 'error' : '' !!}">
                                        <label>@lang('form.p_air')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p_air', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter AIR cost for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('p_air', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('p_sea') ? 'error' : '' !!}">
                                        <label>@lang('form.p_sea')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p_sea', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter SEA cost for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('p_sea', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('x1_qty') ? 'error' : '' !!}">
                                        <label>@lang('form.x1_qty')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('x1_qty', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter X1 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('x1_qty', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('x2_qty') ? 'error' : '' !!}">
                                        <label>@lang('form.x2_qty')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('x2_qty', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter X2 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('x2_qty', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('za1') ? 'error' : '' !!}">
                                        <label>@lang('form.za1%')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('za1', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter XA1 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('za1', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('za2') ? 'error' : '' !!}">
                                        <label>@lang('form.za2%')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('za2', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZA2 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('za2', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('za3') ? 'error' : '' !!}">
                                        <label>@lang('form.za3%')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('za3', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZA3 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('za3', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('r_amount') ? 'error' : '' !!}">
                                        <label>@lang('form.r_amount')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('r_amount', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter R amount', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('r_amount', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('r2_amount') ? 'error' : '' !!}">
                                        <label>@lang('form.r2_amount')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('r2_amount', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter R amount', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('r2_amount', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('r_ss') ? 'error' : '' !!}">
                                        <label>@lang('form.r_ss')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('r_ss', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter SS cost for B list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('r_ss', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('r_sm') ? 'error' : '' !!}">
                                        <label>@lang('form.r_sm')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('r_sm', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter SM cost for B list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('r_sm', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('r_air') ? 'error' : '' !!}">
                                        <label>@lang('form.r_air')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('r_air', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter AIR cost for B list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('r_air', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('r_sea') ? 'error' : '' !!}">
                                        <label>@lang('form.r_sea')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('r_sea', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter SEA cost for B list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('r_sea', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('y1_qty') ? 'error' : '' !!}">
                                        <label>@lang('form.y1_qty')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('y1_qty', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter Y1 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('y1_qty', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('y2_qty') ? 'error' : '' !!}">
                                        <label>@lang('form.y2_qty')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('y2_qty', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter Y2 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('y2_qty', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('zb1') ? 'error' : '' !!}">
                                        <label>@lang('form.zb1%')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('zb1', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZB1 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('zb1', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('zb2') ? 'error' : '' !!}">
                                        <label>@lang('form.zb2%')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('zb2', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZB2 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('zb2', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group {!! $errors->has('zb3') ? 'error' : '' !!}">
                                        <label>@lang('form.zb3%')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('zb3', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZB3 % quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('zb3', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-actions text-center mt-3">
                                        <a href="{{ route('admin.offer_type.list') }}">
                                        <button type="button" class="btn btn-warning mr-1">
                                        <i class="ft-x"></i>@lang('form.btn_cancle')
                                        </button>
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                        <i class="la la-check-square-o"></i>@lang('form.btn_save')
                                        </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </section>
@endsection
