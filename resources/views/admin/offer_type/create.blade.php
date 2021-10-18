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
    <li class="breadcrumb-item active">@lang('payment.breadcrumb_sub_title')</li>
@endsection
@push('custom_css')
<style>
    .text-normal{ font-weight: normal;}
</style>
@endpush

@section('content')
<section id="basic-form-layouts">
    <div class="row match-height min-height">
        <div class="col-md-8">
            <div class="card card-success">
                <div class="card-content collapse show">
                    <div class="card-body">
                        {!! Form::open([ 'route' => 'admin.offer_type.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                        <label >Offer Template Name<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('name', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter offer type name', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('public_name') ? 'error' : '' !!}">
                                        <label >Offer Type Name<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('public_name', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter offer type public name', 'data-validation-required-message' => 'This field is required', 'tabindex' => 2 ]) !!}
                                            {!! $errors->first('public_name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row card p-1">
                                <div class="col-md-12">
                                    <h4>A List</h4>
                                    <hr/>

                            <div class="row">
                                <div class="col-md-2">Value</div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('p_amount') ? 'error' : '' !!}">
                                        <label class="text-normal">Option1 Price (P)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p_amount', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter option 1 value', 'data-validation-required-message' => 'This field is required', 'tabindex' => 3 ]) !!}
                                            {!! $errors->first('p_amount', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('p2_amount') ? 'error' : '' !!}">
                                        <label class="text-normal">Option 2 Price (P2)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p2_amount', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter option 2 value', 'data-validation-required-message' => 'This field is required', 'tabindex' => 4 ]) !!}
                                            {!! $errors->first('p2_amount', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">Postage</div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('p_sm') ? 'error' : '' !!}">
                                        <label class="text-normal">Postage Cost - SM (P SM)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p_sm', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter sm postage for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 4 ]) !!}
                                            {!! $errors->first('p_sm', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('p_ss') ? 'error' : '' !!}">
                                        <label class="text-normal">Postage Cost - SM (P SS)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p_ss', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ss postage for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 5 ]) !!}
                                            {!! $errors->first('p_ss', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">Freight</div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('p_air') ? 'error' : '' !!}">
                                        <label class="text-normal">Air (P AIR) <span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p_air', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter air fright for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 6 ]) !!}
                                            {!! $errors->first('p_air', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('p_sea') ? 'error' : '' !!}">
                                        <label class="text-normal">Air (P AIR)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p_sea', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter sea freight for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 7 ]) !!}
                                            {!! $errors->first('p_sea', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">First Slab</div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('x1_qty') ? 'error' : '' !!}">
                                        <label class="text-normal">Quantity (X1)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('x1_qty', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter x1 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 8 ]) !!}
                                            {!! $errors->first('x1_qty', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('za1') ? 'error' : '' !!}">
                                        <label class="text-normal">Discount % (ZA1)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('za1', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter xa1 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 9 ]) !!}
                                            {!! $errors->first('za1', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">Second Slab</div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('x2_qty') ? 'error' : '' !!}">
                                    <label class="text-normal">Quantity (X2)<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::number('x2_qty', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter x2 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 10 ]) !!}
                                        {!! $errors->first('x2_qty', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('za2') ? 'error' : '' !!}">
                                        <label class="text-normal">Discount% (ZA2)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('za2', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter za2 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 11 ]) !!}
                                            {!! $errors->first('za2', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">Remaining Slab</div>
                                <div class="col-md-5">All Remaining</div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('za3') ? 'error' : '' !!}">
                                        <label class="text-normal">Discount% (ZA3)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('za3', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter za3 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 12 ]) !!}
                                            {!! $errors->first('za3', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            </div>


                                <div class="row card p-2">
                                    <div class="col-md-12">
                                        <h4>B List</h4>
                                        <div class="row">
                                            <div class="col-md-2">Value</div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('r_amount') ? 'error' : '' !!}">
                                                    <label class="text-normal">Option1 Price (P)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('r_amount', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter option 1 value for B list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 13 ]) !!}
                                                        {!! $errors->first('r_amount', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('r2_amount') ? 'error' : '' !!}">
                                                    <label class="text-normal">Option2 Price (P)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('r2_amount', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter option 2 value for B list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 14 ]) !!}
                                                        {!! $errors->first('r2_amount', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">First Slab</div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('y1_qty') ? 'error' : '' !!}">
                                                    <label class="text-normal">Quantity (Y1)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('y1_qty', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter y1 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 15 ]) !!}
                                                        {!! $errors->first('y1_qty', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('zb1') ? 'error' : '' !!}">
                                                    <label class="text-normal">Discount % (ZB1)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('zb1', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter zb1 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 16 ]) !!}
                                                        {!! $errors->first('zb1', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">Second Slab</div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('y2_qty') ? 'error' : '' !!}">
                                                    <label class="text-normal">Quantity (Y2)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('y2_qty', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter y2 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 17 ]) !!}
                                                        {!! $errors->first('y2_qty', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('zb2') ? 'error' : '' !!}">
                                                    <label class="text-normal">Discount % (ZB2)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('zb2', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter zb2 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 18 ]) !!}
                                                        {!! $errors->first('zb2', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">Remaing Slab</div>
                                            <div class="col-md-5">All Remaining </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('zb3') ? 'error' : '' !!}">
                                                    <label class="text-normal">Discount % (ZB3)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('zb3', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter zb3 % quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 19 ]) !!}
                                                        {!! $errors->first('zb3', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row card p-2">
                                    <div class="col-md-12">
                                        <h4>C List</h4>
                                        <div class="row">
                                            <div class="col-md-2">Value</div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('t_amount') ? 'error' : '' !!}">
                                                    <label class="text-normal">Option1 Price (T)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('t_amount', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter option 1 value for C list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 20 ]) !!}
                                                        {!! $errors->first('t_amount', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('t2_amount') ? 'error' : '' !!}">
                                                    <label class="text-normal">Option2 Price (T)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('t2_amount', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter option 2 value for C list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 21 ]) !!}
                                                        {!! $errors->first('t2_amount', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">First Slab</div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('z1_qty') ? 'error' : '' !!}">
                                                    <label class="text-normal">Quantity (Z1<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('z1_qty', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter z1 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 22 ]) !!}
                                                        {!! $errors->first('z1_qty', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('zc1') ? 'error' : '' !!}">
                                                    <label class="text-normal">Discount % (ZC1)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('zc1', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter zc1 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 23 ]) !!}
                                                        {!! $errors->first('zc1', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">Second Slab</div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('z2_qty') ? 'error' : '' !!}">
                                                    <label class="text-normal">Quantity (ZC2)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('z2_qty', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter z2 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 24 ]) !!}
                                                        {!! $errors->first('z2_qty', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('zc2') ? 'error' : '' !!}">
                                                    <label class="text-normal">Discount % (ZC2)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('zc2', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter zc2 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 25 ]) !!}
                                                        {!! $errors->first('zc2', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">Remaing Slab</div>
                                            <div class="col-md-5">All Remaining </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('zc3') ? 'error' : '' !!}">
                                                    <label class="text-normal">Discount % (ZC3)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('zc3', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter zc3 % quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 26 ]) !!}
                                                        {!! $errors->first('zc3', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
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
