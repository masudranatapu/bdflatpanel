@extends('admin.layout.master')

@section('offer_list','active')
@section('offer_management','open')

@section('title')
   Create New offer
@endsection
@section('page-name')
   Create New offer
@endsection

<!--push from page-->
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">

<style>
    .text-normal{ font-weight: normal;}
</style>


@endpush('custom_css')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('payment.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">@lang('form.offer_edit')    </li>
@endsection


@section('content')

<section id="basic-form-layouts">
    <div class="row match-height min-height">
        <div class="col-md-10 offset-md-1">
            <div class="card card-success">
                <div class="card-content collapse show">
                    <div class="card-body">
                        {!! Form::open([ 'route' => ['admin.offer.store'], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('offer_type') ? 'error' : '' !!}">
                                        <label>@lang('form.offer_type')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            <select class="form-control mb-1" name="offer_type" id="offer_type" data-validation-required-message="This field is required" tabindex="1">
                                                <option value="" > - select offer type - </option>
                                                @if(isset($data['offer_type']) && count($data['offer_type']) > 0 )
                                                @foreach($data['offer_type'] as $key => $type )
                                                <option value="{{ $type->PK_NO }}"
                                                data-public_name="{{ $type->PUBLIC_NAME }}"
                                                data-p_amount="{{ $type->P_AMOUNT }}"
                                                data-p2_amount="{{ $type->P2_AMOUNT }}"
                                                data-p_ss="{{ $type->P_SS }}"
                                                data-p_sm="{{ $type->P_SM }}"
                                                data-p_air="{{ $type->P_AIR }}"
                                                data-p_sea="{{ $type->P_SEA }}"
                                                data-x1_qty="{{ $type->X1_QTY }}"
                                                data-x2_qty="{{ $type->X2_QTY }}"
                                                data-za1="{{ $type->ZA1 }}"
                                                data-za2="{{ $type->ZA2 }}"
                                                data-za3="{{ $type->ZA2 }}"
                                                data-r_amount="{{ $type->R_AMOUNT }}"
                                                data-r2_amount="{{ $type->R2_AMOUNT }}"
                                                data-r_ss="{{ $type->R_SS }}"
                                                data-r_sm="{{ $type->R_SM }}"
                                                data-r_air="{{ $type->R_AIR }}"
                                                data-r_sea="{{ $type->R_SEA }}"
                                                data-y1_qty="{{ $type->Y1_QTY }}"
                                                data-y2_qty="{{ $type->Y2_QTY }}"
                                                data-zb1="{{ $type->ZB1 }}"
                                                data-zb2="{{ $type->ZB2 }}"
                                                data-zb3="{{ $type->ZB3 }}"
                                                > {{ $type->NAME }} </option>
                                                @endforeach
                                                @endif
                                            </select>

                                            {!! $errors->first('offer_type', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('image') ? 'error' : '' !!}">
                                        <label>Photo<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::file('image', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter photo','data-validation-required-message' => 'This field is required','tabindex' => 2 ]) !!}
                                            {!! $errors->first('image', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                        <label>@lang('form.name')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('name',null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter offer name', 'data-validation-required-message' => 'This field is required', 'tabindex' => 3 ]) !!}
                                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('public_name') ? 'error' : '' !!}">
                                        <label>@lang('form.public_name')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('public_name',null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter offer name', 'data-validation-required-message' => 'This field is required', 'tabindex' => 4 ]) !!}
                                            {!! $errors->first('public_name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 ">
                                    <div class="form-group {!! $errors->has('coupon_code') ? 'error' : '' !!}">
                                        <label>@lang('form.coupon_code')</label>
                                        <div class="controls">
                                            {!! Form::text('coupon_code', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter coupon code', 'tabindex' => 5 ]) !!}
                                            {!! $errors->first('coupon_code', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group {!! $errors->has('validity_from') ? 'error' : '' !!}">
                                        <label>@lang('form.validity_from')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('validity_from', null, [ 'class' => 'form-control mb-1 pickadate', 'placeholder' => 'Enter date', 'data-validation-required-message' => 'This field is required', 'tabindex' => 6 ]) !!}
                                            {!! $errors->first('validity_from', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group {!! $errors->has('validity_to') ? 'error' : '' !!}">
                                        <label>@lang('form.validity_to')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('validity_to', null,  [ 'class' => 'form-control mb-1 pickadate', 'placeholder' => 'Enter date', 'data-validation-required-message' => 'This field is required', 'tabindex' => 7 ]) !!}
                                            {!! $errors->first('validity_to', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row card p-1">
                                <div class="col-md-12">

                                    <div class="row">
                                        <div class="col-md-2"><h4>A List</h4></div>
                                        <div class="col-md-10">
                                            <div class="form-group {!! $errors->has('lista') ? 'error' : '' !!} mb-0">
                                                <div class="controls">
                                                    {!! Form::select('lista', $data['list_a_combo'] ?? array() , null, [ 'class' => 'form-control mb-1 select2', 'placeholder' => 'Select A list','data-validation-required-message' => 'This field is required',  'tabindex' => 8 ]) !!}
                                                    {!! $errors->first('lista', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr/>
                                    <div class="row">
                                        <div class="col-md-4">
                                            Value
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {!! $errors->has('p_amount') ? 'error' : '' !!}">
                                                <label class="text-normal">Option1 Price (P)<span class="text-danger">*</span></label>
                                                <div class="controls">
                                                    {!! Form::number('p_amount', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter P amount','data-validation-required-message' => 'This field is required', 'tabindex' => 9 ]) !!}
                                                    {!! $errors->first('p_amount', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {!! $errors->has('p2_amount') ? 'error' : '' !!}">
                                                <label class="text-normal">Option2 Price (P)<span class="text-danger">*</span></label>
                                                <div class="controls">
                                                    {!! Form::number('p2_amount',null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter P2 amount','data-validation-required-message' => 'This field is required', 'tabindex' => 10 ]) !!}
                                                    {!! $errors->first('p2_amount', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">Postage</div>
                                        <div class="col-md-4">
                                            <div class="form-group {!! $errors->has('p_sm') ? 'error' : '' !!}">
                                                <label class="text-normal">Postage Cost - SM (P SM)<span class="text-danger">*</span></label>
                                                <div class="controls">
                                                    {!! Form::number('p_sm', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter SM postage for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 11 ]) !!}
                                                    {!! $errors->first('p_sm', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {!! $errors->has('p_ss') ? 'error' : '' !!}">
                                                <label class="text-normal">Postage Cost - SS (P SS)<span class="text-danger">*</span></label>
                                                <div class="controls">
                                                    {!! Form::number('p_ss', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter SS postage for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 12 ]) !!}
                                                    {!! $errors->first('p_ss', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">Freight</div>
                                        <div class="col-md-4">
                                            <div class="form-group {!! $errors->has('p_air') ? 'error' : '' !!}">
                                                <label class="text-normal">Air (P AIR)<span class="text-danger">*</span></label>
                                                <div class="controls">
                                                    {!! Form::number('p_air', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter AIR cost for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 13 ]) !!}
                                                    {!! $errors->first('p_air', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {!! $errors->has('p_sea') ? 'error' : '' !!}">
                                                <label class="text-normal">SEA (P SEA)<span class="text-danger">*</span></label>
                                                <div class="controls">
                                                    {!! Form::number('p_sea', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter SEA cost for A list', 'data-validation-required-message' => 'This field is required', 'tabindex' => 14 ]) !!}
                                                    {!! $errors->first('p_sea', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">First Slab</div>
                                        <div class="col-md-4">
                                            <div class="form-group {!! $errors->has('x1_qty') ? 'error' : '' !!}">
                                                <label class="text-normal">Quantity (X1)<span class="text-danger">*</span></label>
                                                <div class="controls">
                                                    {!! Form::number('x1_qty',  null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter X1 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 15 ]) !!}
                                                    {!! $errors->first('x1_qty', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {!! $errors->has('za1') ? 'error' : '' !!}">
                                                <label class="text-normal">Discount % (ZA1)<span class="text-danger">*</span></label>
                                                <div class="controls">
                                                    {!! Form::number('za1', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter XA1 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 16 ]) !!}
                                                    {!! $errors->first('za1', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">Second Slab</div>
                                        <div class="col-md-4">
                                            <div class="form-group {!! $errors->has('x2_qty') ? 'error' : '' !!}">
                                                <label class="text-normal">Quantity (X2)<span class="text-danger">*</span></label>
                                                <div class="controls">
                                                    {!! Form::number('x2_qty', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter X2 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 17 ]) !!}
                                                    {!! $errors->first('x2_qty', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group {!! $errors->has('za2') ? 'error' : '' !!}">
                                                <label class="text-normal">Discount% (ZA2)<span class="text-danger">*</span></label>
                                                <div class="controls">
                                                    {!! Form::number('za2', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZA2 %','data-validation-required-message' => 'This field is required',  'tabindex' => 18 ]) !!}
                                                    {!! $errors->first('za2', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">Remaining Slab</div>
                                        <div class="col-md-4">All Remaining</div>
                                        <div class="col-md-4">
                                            <div class="form-group {!! $errors->has('za3') ? 'error' : '' !!}">
                                                <label class="text-normal">Discount% (ZA3)<span class="text-danger">*</span></label>
                                                <div class="controls">
                                                    {!! Form::number('za3', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZA3 %', 'data-validation-required-message' => 'This field is required', 'tabindex' => 19 ]) !!}
                                                    {!! $errors->first('za3', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                                <div class="row card p-1">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>B List</label>
                                            </div>
                                            <div class="col-md-10 ">
                                                <div class="form-group {!! $errors->has('listb') ? 'error' : '' !!}">
                                                    <div class="controls">
                                                        {!! Form::select('listb', $data['list_b_combo'] ?? array(), null, [ 'class' => 'form-control mb-1 select2', 'placeholder' => 'Select B list', 'tabindex' => 20 ]) !!}
                                                        {!! $errors->first('listb', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="row">
                                            <div class="col-md-4">Value</div>
                                            <div class="col-md-4">
                                                <div class="form-group {!! $errors->has('r_amount') ? 'error' : '' !!}">
                                                    <label class="text-normal">Option1 Price (P)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('r_amount', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter R amount', 'data-validation-required-message' => 'This field is required', 'tabindex' => 21 ]) !!}
                                                        {!! $errors->first('r_amount', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group {!! $errors->has('r2_amount') ? 'error' : '' !!}">
                                                    <label class="text-normal">Option2 Price (P)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('r2_amount', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter R2 amount', 'data-validation-required-message' => 'This field is required', 'tabindex' => 22 ]) !!}
                                                        {!! $errors->first('r2_amount', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">First Slab</div>
                                            <div class="col-md-4">
                                                <div class="form-group {!! $errors->has('y1_qty') ? 'error' : '' !!}">
                                                    <label class="text-normal">Quantity (Y1)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('y1_qty', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter X1 quantity', 'data-validation-required-message' => 'This field is required', 'tabindex' => 23 ]) !!}
                                                        {!! $errors->first('y1_qty', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group {!! $errors->has('zb1') ? 'error' : '' !!}">
                                                    <label class="text-normal">Discount % (ZB1)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('zb1', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZB1 %','data-validation-required-message' => 'This field is required',  'tabindex' => 24 ]) !!}
                                                        {!! $errors->first('zb1', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">Second Slab</div>
                                            <div class="col-md-4">
                                                <div class="form-group {!! $errors->has('y2_qty') ? 'error' : '' !!}">
                                                    <label class="text-normal">Quantity (Y2)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('y2_qty', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter Y2 quantity','data-validation-required-message' => 'This field is required',  'tabindex' => 25 ]) !!}
                                                        {!! $errors->first('y2_qty', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group {!! $errors->has('zb2') ? 'error' : '' !!}">
                                                    <label class="text-normal">Discount % (ZB2)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('zb2', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZB2 %','data-validation-required-message' => 'This field is required',  'tabindex' => 26 ]) !!}
                                                        {!! $errors->first('zb2', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">Remaining Slab</div>
                                            <div class="col-md-4">All Remaining</div>
                                            <div class="col-md-4">
                                                <div class="form-group {!! $errors->has('zb3') ? 'error' : '' !!}">
                                                    <label class="text-normal">Discount % (ZB3)<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::number('zb3', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter ZB3 % quantity','data-validation-required-message' => 'This field is required','tabindex' => 27 ]) !!}
                                                        {!! $errors->first('zb3', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions text-center mt-3">
                                    <a href="{{ route('admin.offer.list') }}">
                                        <button type="button" class="btn btn-warning mr-1">
                                        <i class="ft-x"></i>@lang('form.btn_cancle')
                                        </button>
                                    </a>
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
@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script>

    $(document).on('change','#offer_type', function(e){
        var element     = $(this).find('option:selected');
        var p_amount    = element.data('p_amount');
        var p2_amount   = element.data('p2_amount');
        var p_ss        = element.data('p_ss');
        var p_sm        = element.data('p_sm');
        var p_air       = element.data('p_air');
        var p_sea       = element.data('p_sea');
        var x1_qty      = element.data('x1_qty');
        var x2_qty      = element.data('x2_qty');
        var za1         = element.data('za1');
        var za2         = element.data('za2');
        var za3         = element.data('za3');
        var r_amount    = element.data('r_amount');
        var r2_amount   = element.data('r2_amount');
        var r_ss        = element.data('r_ss');
        var r_sm        = element.data('r_sm');
        var r_air       = element.data('r_air');
        var r_sea       = element.data('r_sea');
        var y1_qty      = element.data('y1_qty');
        var y2_qty      = element.data('y2_qty');
        var zb1         = element.data('zb1');
        var zb2         = element.data('zb2');
        var zb3         = element.data('zb3');
        $("input[name='p_amount']").val(p_amount);
        $("input[name='p2_amount']").val(p2_amount);
        $("input[name='p_ss']").val(p_ss);
        $("input[name='p_sm']").val(p_sm);
        $("input[name='p_air']").val(p_air);
        $("input[name='p_sea']").val(p_sea);
        $("input[name='x1_qty']").val(x1_qty);
        $("input[name='x2_qty']").val(x2_qty);
        $("input[name='za1']").val(za1);
        $("input[name='za2']").val(za2);
        $("input[name='za3']").val(za3);
        $("input[name='r_amount']").val(r_amount);
        $("input[name='r2_amount']").val(r2_amount);
        $("input[name='r_ss']").val(r_ss);
        $("input[name='r_sm']").val(r_sm);
        $("input[name='r_air']").val(r_air);
        $("input[name='r_sea']").val(r_sea);
        $("input[name='y1_qty']").val(y1_qty);
        $("input[name='y2_qty']").val(y2_qty);
        $("input[name='zb1']").val(zb1);
        $("input[name='zb2']").val(zb2);
        $("input[name='zb3']").val(zb3);


    })

$(document).ready(function () {
    $('.pickadate').pickadate({
        format: 'dd-mm-yyyy',
       // formatSubmit: 'dd-mm-yyyy',
    // max:!0,
    });

})
</script>

@endpush()
