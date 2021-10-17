@extends('admin.layout.master')
@section('offer_type','active')
@section('offer_management','open')
@section('title')
    Edit Offer type
@endsection
@section('page-name')
    Edit Offer type
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
        <div class="col-md-8 offset-md-2">
            <div class="card card-success">
                <div class="card-content collapse show">
                    <div class="card-body">
                        {!! Form::open([ 'route' => 'admin.offer_type.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                        <label >Offer Template Name<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('name', $row->NAME, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter Edit offer type name', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('public_name') ? 'error' : '' !!}">
                                        <label >Edit Offer Type Name<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('public_name', $row->PUBLIC_NAME, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter Edit offer type public name', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
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
                                            {!! Form::number('p_amount', $row->P_AMOUNT, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter P amount', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('p_amount', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('p2_amount') ? 'error' : '' !!}">
                                        <label class="text-normal">Option 2 Price (P2)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p2_amount', $row->P2_AMOUNT, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter P amount', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
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
                                            {!! Form::number('p_sm', $row->P_SM, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter SM postage for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('p_sm', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('p_ss') ? 'error' : '' !!}">
                                        <label class="text-normal">Postage Cost - SS (P SS)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p_ss', $row->P_SS, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter SS postage for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
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
                                            {!! Form::number('p_air', $row->P_AIR, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter AIR cost for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('p_air', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('p_sea') ? 'error' : '' !!}">
                                        <label class="text-normal">SEA (P SEA)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('p_sea', $row->P_SEA, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter SEA cost for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
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
                                            {!! Form::number('x1_qty', $row->X1_QTY, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter X1 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('x1_qty', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('za1') ? 'error' : '' !!}">
                                        <label class="text-normal">Discount % (ZA1)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('za1', $row->ZA1, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter XA1 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
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
                                        {!! Form::number('x2_qty', $row->X2_QTY, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter X2 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('x2_qty', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group {!! $errors->has('za2') ? 'error' : '' !!}">
                                        <label class="text-normal">Discount% (ZA2)<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::number('za2', $row->ZA2, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZA2 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
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
                                            {!! Form::number('za3', $row->ZA3, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZA3 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
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
                                                        {!! Form::number('r_amount', $row->R_AMOUNT, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter R amount', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                                        {!! $errors->first('r_amount', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('r2_amount') ? 'error' : '' !!}">
                                                    <label class="text-normal">Option2 Price (P)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('r2_amount', $row->R2_AMOUNT, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter R amount', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
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
                                                        {!! Form::number('y1_qty', $row->Y1_QTY, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter Y1 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                                        {!! $errors->first('y1_qty', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('zb1') ? 'error' : '' !!}">
                                                    <label class="text-normal">Discount % (ZB1)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('zb1', $row->ZB1, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZB1 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
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
                                                        {!! Form::number('y2_qty', $row->Y2_QTY, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter Y2 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                                        {!! $errors->first('y2_qty', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('zb2') ? 'error' : '' !!}">
                                                    <label class="text-normal">Discount % (ZB2)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('zb2', $row->ZB2, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZB2 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
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
                                                        {!! Form::number('zb3',$row->ZB3, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZB3 % quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
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
                                                        {!! Form::number('t_amount', $row->T_AMOUNT, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter T2 amount', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                                        {!! $errors->first('t_amount', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('t2_amount') ? 'error' : '' !!}">
                                                    <label class="text-normal">Option2 Price (T)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('t2_amount', $row->T2_AMOUNT, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter T2 amount', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                                        {!! $errors->first('t2_amount', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">First Slab</div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('z1_qty') ? 'error' : '' !!}">
                                                    <label class="text-normal">Quantity (Z1)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('z1_qty', $row->Z1_QTY, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter z1 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                                        {!! $errors->first('z1_qty', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('zc1') ? 'error' : '' !!}">
                                                    <label class="text-normal">Discount % (ZC1)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('zc1', $row->ZC1, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter Zc1 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                                        {!! $errors->first('zc1', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">Second Slab</div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('zc2_qty') ? 'error' : '' !!}">
                                                    <label class="text-normal">Quantity (Z2)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('z2_qty', $row->Z2_QTY, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter C2 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                                        {!! $errors->first('z2_qty', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group {!! $errors->has('zc2') ? 'error' : '' !!}">
                                                    <label class="text-normal">Discount % (ZC2)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('zc2', $row->ZC2, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter Zc2 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
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
                                                        {!! Form::number('zc3', $row->ZC3, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter Zc3 % quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
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
                                        <a href="{{ route('admin.offer_type.list') }}" class="btn btn-warning mr-1" title="Cancel">
                                        <i class="ft-x"></i> @lang('form.btn_cancle')
                                        </button>
                                        </a>
                                        <button type="submit" class="btn btn-primary" title="Save">
                                        <i class="la la-check-square-o"></i> @lang('form.btn_save')
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
