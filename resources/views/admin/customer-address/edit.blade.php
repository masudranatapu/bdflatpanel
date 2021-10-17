@extends('admin.layout.master')

@section('Customer Management','open')
@section('customer_list','active')

@section('title') Customer Address | Update @endsection
@section('page-name') Customer Address Update @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Customer</li>
@endsection
<!--push from page-->
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<style>
    #scrollable-dropdown-menu2 .tt-menu {max-height: 260px;overflow-y: auto;width: 100%;border: 1px solid #333;border-radius: 5px;}
    .twitter-typeahead{ display: block !important;}
    .tt-hint {color: #999 !important;}
</style>

@endpush('custom_css')

@section('content')
<div class="card card-success min-height">
    <div class="card-header">

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
            {!! Form::open([ 'route' => ['admin.customer-address.update', $address->PK_NO], 'method' => 'post', 'class'
            => 'form-horizontal', 'files' => true , 'novalidate']) !!}
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group {!! $errors->has('addresstype') ? 'error' : '' !!}">
                        <label>{{trans('form.address_type')}}</label>
                        <div class="controls">
                        {!! Form::select('addresstype', $data['address_type_combo'], $address->F_ADDRESS_TYPE_NO ?? null, ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'This field is required', 'id' => 'addressCombo', 'tabindex' => 1]) !!}
                        {!! $errors->first('addresstype', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group {!! $errors->has('customeraddress') ? 'error' : '' !!}">
                        <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('customeraddress', $address->NAME ?? null, ['class'=>'form-control mb-1', 'id' => 'customeraddress', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter name', 'tabindex' => 2, 'required' ]) !!}
                            {!! $errors->first('customeraddress', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>
                <?php
                $fetched_country = isset($address->F_COUNTRY_NO) ? $address->F_COUNTRY_NO : 2;
                ?>
                <div class="col-md-4">
                    <div class="form-group {!! $errors->has('country') ? 'error' : '' !!}">
                        <label>{{trans('form.country')}}</label>
                        <div class="controls">
                            <select name="country" id="country" class="form-control mb-1 select2" tabindex="3">
                                @foreach ($data['country'] as $item)
                                    <option value="{{ $item->PK_NO }}" data-dial_code="{{ $item->DIAL_CODE }}" {{ $item->PK_NO == $fetched_country ? "selected='selected'" : '' }}>{{ $item->NAME }} ({{ $item->DIAL_CODE }})</option>
                                @endforeach
                            </select>
                            {!! $errors->first('country', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label>{{trans('form.mobile_no')}}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="phonecode2">+60</span>
                        </div>
                        {!! Form::text('mobilenoadd',$address->TEL_NO ?? null,[ 'class' => 'form-control', 'placeholder' => 'Enter mobile no.', 'id' => 'mobilenoadd', 'tabindex' => 4]) !!}
                        {!! $errors->first('mobilenoadd', '<label class="help-block text-danger">:message</label>') !!}
                        {{-- <input type="text" class="form-control" placeholder="Addon to Left" aria-describedby="basic-addon1"> --}}
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="form-group {!! $errors->has('post_code') ? 'error' : '' !!}">
                        <label>{{trans('form.post_code')}}</label>
                        <div class="controls" id="scrollable-dropdown-menu2">
                            <input type="search" name="post_code" id="post_code_" class="form-control search-input4" placeholder="Post code" autocomplete="off" value="{{ $address->POST_CODE ?? '' }}" required tabindex="5">
                        </div>
                        <div id="post_code_appended_div">
                            {!! Form::hidden('post_code', $address->POST_CODE ?? 0, ['id'=>'post_code_hidden']) !!}
                        </div>
                        {!! $errors->first('post_code', '<label class="help-block text-danger">:message</label>') !!}
                        {{-- <div class="controls">
                            {!! Form::select('post_code', array(),  null, ['class'=>'form-control mb-1', 'id' => 'post_c',  'placeholder' => 'Select Post Code', 'tabindex' => 8,  ]) !!}
                            {!! $errors->first('post_code', '<label class="help-block text-danger">:message</label>') !!}
                        </div> --}}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group {!! $errors->has('city') ? 'error' : '' !!}">
                        <label>{{trans('form.city')}}</label>
                        <div class="controls">
                            {!! Form::select('city', $data['city'] ?? array(), $address->CITY ?? null, ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'Select city', 'id' => 'city','tabindex' => 6, $address->CITY ?? 'placeholder' => 'Select city', 'data-url' => URL::to('customer_pCode') ]) !!}
                            {!! $errors->first('city', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>
                {{-- STATE 1 --}}
                <div class="col-md-4">
                    <div class="form-group {!! $errors->has('state') ? 'error' : '' !!}">
                        <label>{{trans('form.state')}}</label>
                        <div class="controls">
                            {!! Form::select('state',  $data['state'] ?? array(), $address->STATE ?? null, ['class'=>'form-control mb-1 select2','data-validation-required-message' => 'Select State', $address->STATE ?? 'placeholder' => 'Select state', 'id' => 'state','tabindex' =>7,'data-url' => URL::to('customer_city') ]) !!}
                            {!! $errors->first('state', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group {!! $errors->has('ad_1') ? 'error' : '' !!}">
                        <label>{{trans('form.address_1')}}</label>
                        <div class="controls">
                            {!! Form::text('ad_1', $address->ADDRESS_LINE_1 ?? null, ['class'=>'form-control mb-1', 'id' => 'ad1', 'placeholder' => 'Enter address', 'tabindex' => 8 ]) !!}
                            {!! $errors->first('ad_1', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group {!! $errors->has('ad_2') ? 'error' : '' !!}">
                        <label>{{trans('form.address_2')}}</label>
                        <div class="controls">
                            {!! Form::text('ad_2', $address->ADDRESS_LINE_2 ?? null, ['class'=>'form-control mb-1', 'id' => 'ad2',  'placeholder' => 'Enter address', 'tabindex' => 9  ]) !!}
                            {!! $errors->first('ad_2', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group {!! $errors->has('ad_3') ? 'error' : '' !!}">
                        <label>{{trans('form.address_3')}}</label>
                        <div class="controls">
                            {!! Form::text('ad_3', $address->ADDRESS_LINE_3 ?? null, ['class'=>'form-control mb-1', 'id' => 'ad3',  'placeholder' => 'Enter address', 'tabindex' => 10 ]) !!}
                            {!! $errors->first('ad_3', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group {!! $errors->has('ad_4') ? 'error' : '' !!}">
                        <label>{{trans('form.address_4')}}</label>
                        <div class="controls">
                            {!! Form::text('ad_4', $address->ADDRESS_LINE_4 ?? null, ['class'=>'form-control mb-1', 'id' => 'ad4',  'placeholder' => 'Enter address', 'tabindex' => 11,  ]) !!}
                            {!! $errors->first('ad_4', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group {!! $errors->has('location') ? 'error' : '' !!}">
                        <label>{{trans('form.location')}}</label>
                        <div class="controls">
                            {!! Form::text('location', $address->LOCATION ?? null, ['class'=>'form-control mb-1', 'id' => 'location',  'placeholder' => 'Enter location', 'tabindex' => 12,  ]) !!}
                            {!! $errors->first('location', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>
            </div>

                <div class="col-md-12 mt-2 mb-2">
                    <div class="form-actions text-center">
                        <a href="{{route('admin.customer.list')}}" class="btn btn-warning mr-1" title="Cancel"><i class="ft-x"></i>
                            {{ trans('form.btn_cancle') }}</a>
                        <button type="submit" class="btn bg-primary bg-darken-1 text-white" title="Save">
                            <i class="la la-check-square-o"></i> {{ trans('form.btn_edit') }} </button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
<!--push from page-->
@push('custom_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/country.js') }}"></script>

@endpush('custom_js')
