@extends('admin.layout.master')

@section('Customer Management','open')
@section('customer_list','active')

<!--push from page-->
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('app-assets/file_upload/image-uploader.min.css')}}">
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endpush('custom_css')



@section('title') @lang('customer.update_customer') @endsection

@section('page-name') @lang('customer.update_customer') @endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">@lang('admin_role.breadcrumb_title')  </a></li>
<li class="breadcrumb-item active">@lang('customer.breadcrumb_sub_title')    </li>
@endsection

@section('content')
<div class="content-body min-height">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-sm card-success" >
                <!--?php vError($errors) ?-->
                <div class="card-content">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-top-border no-hover-bg nav-justified no-border">
                            <li class="nav-item">
                                <a class="nav-link active" id="productBasic-tab1" data-toggle="tab" href="#productBasic" aria-controls="productBasic" aria-expanded="true">@lang('customer.customer_info')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="linkIcon1-tab1" data-toggle="tab" href="#linkIcon1" aria-controls="linkIcon1" aria-expanded="false" >@lang('customer.customer_address')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="linkIconOpt1-tab1" data-toggle="tab" href="#linkIconOpt1" aria-controls="linkIconOpt1">@lang('product.stock_info')</a>
                            </li>
                        </ul>
                        <div class="tab-content border-tab-content">
                            <div role="tabpanel" class="tab-pane active" id="productBasic" aria-labelledby="productBasic-tab1" aria-expanded="true">
                                {!! Form::open([ 'route' => ['admin.customer.update',$customer->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                            <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                                                <label>{{trans('form.select_customer')}}<span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="controls">
                                                            {!! Form::radio('scustomer', 'agent', $customer->F_SALES_AGENT_NO == NULL ? '' : true) !!}
                                                            <label for="scustomer">{{trans('form.agent')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="controls">
                                                            {!! Form::radio('scustomer','reseller', $customer->F_RESELLER_NO == NULL ? '' : true) !!}
                                                            <label for="scustomer">{{trans('form.reseller')}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group {!! $errors->has('agent') ? 'error' : '' !!}">
                                                                <div class="controls">
                                                                {!! Form::select('agent', $customer->F_RESELLER_NO == NULL ? $data['agent_combo'] : $data['reseller_combo'], $customer->F_RESELLER_NO == NULL ? $customer->agent->PK_NO : $customer->reseller->PK_NO, ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'This field is required', 'id' => 'booking_under', 'tabindex' => 1]) !!}
                                                                {!! $errors->first('agent', '<label class="help-block text-danger">:message</label>') !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                                            <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::text('customername',  $customer->NAME, ['class'=>'form-control mb-1', 'id' => 'customername', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Your Name', 'tabindex' => 1,  ]) !!}
                                                {!! $errors->first('customername', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                            <label>{{trans('form.mobile_no')}}<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::text('mobileno',  $customer->MOBILE_NO, [ 'class' => 'form-control mb-1',  'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Mobile No', 'tabindex' => 2]) !!}
                                                {!! $errors->first('mobileno', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                                            <label>{{trans('form.alternative_no')}}</label>
                                            <div class="controls">
                                                {!! Form::text('altno', $customer->ALTERNATE_NO, ['class'=>'form-control mb-1', 'id' => 'altno',  'placeholder' => 'Enter Alternative Mobile No', 'tabindex' => 3,  ]) !!}
                                                {!! $errors->first('altno', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                                            <label>{{trans('form.email')}}<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::text('email',  $customer->EMAIL, ['class'=>'form-control mb-1', 'id' => 'email', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Your Email', 'tabindex' => 4,  ]) !!}
                                                {!! $errors->first('email', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                                            <label>{{trans('form.fb_id')}}</label>
                                            <div class="controls">
                                                {!! Form::text('fbid',  $customer->FB_ID, ['class'=>'form-control mb-1', 'id' => 'fbid',  'placeholder' => 'Enter Your Facebook Id', 'tabindex' => 5,  ]) !!}
                                                {!! $errors->first('fbid', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                                            <label>{{trans('form.ins_id')}}</label>
                                            <div class="controls">
                                                {!! Form::text('insid',  $customer->IG_ID, ['class'=>'form-control mb-1', 'id' => 'insid',  'placeholder' => 'Enter Your Instagram Id', 'tabindex' => 6,  ]) !!}
                                                {!! $errors->first('insid', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                                            <label>{{trans('form.uk_id')}}</label>
                                            <div class="controls">
                                                {!! Form::text('ukid', $customer->UKSHOP_ID, ['class'=>'form-control mb-1', 'id' => 'ukid',  'placeholder' => 'Enter Your UKshop Id', 'tabindex' => 6,  ]) !!}
                                                {!! $errors->first('ukid', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                                            <label>{{trans('form.uk_pass')}}<span class="text-danger">*</span> <small class="">(Leave it blank if you do not want to change the password)</small></label>
                                            <div class="controls">
                                                {!! Form::password('ukpass', ['class'=>'form-control mb-1', 'id' => 'ukpass',  'placeholder' => 'Enter Your Ukshop Pass', 'tabindex' => 6,  ]) !!}
                                                {!! $errors->first('ukpass', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-3">
                                        <div class="form-group {!! $errors->has('customs_name') ? 'error' : '' !!}">
                                            <label>{{trans('form.customs_name')}}</label>
                                            <div class="controls">
                                                {!! Form::text('customs_name', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter product customs name', 'tabindex' => 6 ]) !!}
                                                {!! $errors->first('customs_name', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div> --}}





                                    {{-- <div class="col-md-3">
                                        <div class="form-group {!! $errors->has('def_shipping_method') ? 'error' : '' !!}">
                                            <label>{{trans('form.default_shipping_method')}}</label>
                                            <div class="controls">
                                                {!! Form::select('def_shipping_method', $shipping_method_combo, 'AIR', ['class'=>'form-control mb-1', 'placeholder' => 'Select def_shipping_method', 'tabindex' => 9]) !!}
                                                {!! $errors->first('def_shipping_method', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>--}}
                                    {{--<div class="col-md-3">
                                        <div class="form-group {!! $errors->has('def_air_freight') ? 'error' : '' !!}">
                                            <label>{{trans('form.default_air_freight')}}</label>
                                            <div class="controls">
                                                {!! Form::number('def_air_freight', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter air freight cost', 'tabindex' => 10,'step' => '0.01', 'data-validation-number-message' => 'Please enter max 2 decimal point' ]) !!}
                                                {!! $errors->first('def_air_freight', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>--}}
                                    {{--<div class="col-md-3">
                                        <div class="form-group {!! $errors->has('def_sea_freight') ? 'error' : '' !!}">
                                            <label>{{trans('form.default_sea_freight')}}</label>
                                            <div class="controls">
                                                {!! Form::number('def_sea_freight', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter sea freight cost', 'tabindex' => 11,'step' => '0.01', 'data-validation-number-message' => 'Please enter max 2 decimal point' ]) !!}
                                                {!! $errors->first('def_sea_freight', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div> --}}



                                    {{--<div class="col-md-3">
                                        <div class="form-group  {!! $errors->has('price') ? 'error' : '' !!}">
                                            <label>{{trans('form.default_price')}}</label>
                                            <div class="controls">
                                                {!! Form::number('def_price', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter price for fixed', 'tabindex' => 12, 'step' => '0.01', 'data-validation-number-message' => 'Please enter max 2 decimal point' ]) !!}
                                                {!! $errors->first('def_price', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>--}}
                                    {{--<div class="col-md-3">
                                        <div class="form-group {!! $errors->has('price_ins') ? 'error' : '' !!}">
                                            <label>{{trans('form.default_price_ins')}}</label>
                                            <div class="controls">
                                                {!! Form::number('def_price_ins', null, ['class' => 'form-control mb-1', 'placeholder' => 'Enter price for installment', 'tabindex' => 13, 'step' => '0.01', 'data-validation-number-message' => 'Please enter max 2 decimal point']) !!}
                                                {!! $errors->first('def_price_ins', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>--}}
                                    {{--<div class="col-md-3">
                                        <div class="form-group {!! $errors->has('def_local_postage') ? 'error' : '' !!}">
                                            <label>{{trans('form.default_local_postage')}}</label>
                                            <div class="controls">
                                                {!! Form::number('def_local_postage', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter postage cost for local', 'tabindex' => 14, 'step' => '0.01', 'data-validation-number-message' => 'Please enter max 2 decimal point']) !!}
                                                {!! $errors->first('def_local_postage', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>--}}
                                    {{--<div class="col-md-3">
                                        <div class="form-group {!! $errors->has('def_local_postage') ? 'error' : '' !!}">
                                            <label>{{trans('form.default_interdistric_postage')}}</label>
                                            <div class="controls">
                                                {!! Form::number('def_int_postage', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter postage cost for interdistrict', 'tabindex' => 15, 'step' => '0.01', 'data-validation-number-message' => 'Please enter max 2 decimal point']) !!}
                                                {!! $errors->first('def_int_postage', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>--}}


                                    <div class="col-md-12">
                                        <div class="form-actions text-center">
                                            <a href="{{route('admin.customer.list')}}" class="btn btn-warning mr-1"><i class="ft-x"></i> {{ trans('form.btn_cancle') }}</a>
                                            <button type="submit" class="btn bg-primary bg-darken-1 text-white">
                                             <i class="la la-check-square-o"></i> {{ trans('form.btn_edit') }} </button>
                                         </div>
                                     </div>
                                 </div>
                                 {!! Form::close() !!}
                             </div>
                             <div class="tab-pane" id="linkIcon1" role="tabpanel" aria-labelledby="linkIcon1-tab1" aria-expanded="false">
                                <p>Chocolate bar gummies sesame snaps. Liquorice cake sesame snaps cotton candy cake sweet brownie.
                                    Cotton candy candy canes brownie. Biscuit pudding sesame snaps pudding pudding sesame snaps biscuit
                                    tiramisu.
                                </p>
                            </div>
                            <div class="tab-pane" id="linkIconOpt1" role="tabpanel" aria-labelledby="linkIconOpt1-tab1" aria-expanded="false">
                                <p>Cookie icing tootsie roll cupcake jelly-o sesame snaps. Gummies cookie dragée cake jelly marzipan
                                    donut pie macaroon. Gingerbread powder chocolate cake icing. Cheesecake gummi bears ice cream
                                    marzipan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Recent Transactions -->
</div>
@endsection
<!--push from page-->
@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/customer.js')}}"></script>

 @endpush('custom_js')
