@extends('admin.layout.master')

@section('Customer Management','open')
@section('customer_list','active')

@section('title') Customer | Update @endsection
@section('page-name') Update Customer @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Customer</li>
@endsection

<!--push from page-->
@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
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
            {!! Form::open([ 'route' => ['admin.customer.update',$customer->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                @csrf
                <div class="row">
                    <div class="col-md-6">
                            <div class="form-group {!! $errors->has('scustomer') ? 'error' : '' !!}">
                                <label>{{trans('form.select_customer')}}<span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="controls">
                                            <label>{!! Form::radio('scustomer', 'ukshop', $customer->F_RESELLER_NO == 0 ? true : '') !!} {{trans('form.agent')}}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="controls">
                                            <label>{!! Form::radio('scustomer','reseller', $customer->F_RESELLER_NO == 0 ? '' : true) !!} {{trans('form.reseller')}}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group {!! $errors->has('agent') ? 'error' : '' !!}">
                                                <div class="controls">
                                                {!! Form::select('agent', $customer->F_RESELLER_NO == 0 ? $data['agent_combo'] : $data['reseller_combo'], $customer->F_RESELLER_NO != 0 ? $customer->reseller->PK_NO : 0, ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'This field is required', 'id' => 'booking_under', 'tabindex' => 1]) !!}
                                                {!! $errors->first('agent', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('customername') ? 'error' : '' !!}">
                            <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::text('customername',  $customer->NAME, ['class'=>'form-control mb-1', 'id' => 'customername', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter customer name', 'tabindex' => 1,  ]) !!}
                                {!! $errors->first('customername', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('mobileno') ? 'error' : '' !!}">
                            <label>{{trans('form.mobile_no')}}<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::text('mobileno',  $customer->MOBILE_NO, [ 'class' => 'form-control mb-1',  'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter mobile no', 'tabindex' => 2]) !!}
                                {!! $errors->first('mobileno', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('altno') ? 'error' : '' !!}">
                            <label>{{trans('form.alternative_no')}}</label>
                            <div class="controls">
                                {!! Form::text('altno', $customer->ALTERNATE_NO, ['class'=>'form-control mb-1', 'id' => 'altno',  'placeholder' => 'Enter alternative mobile no', 'tabindex' => 3,  ]) !!}
                                {!! $errors->first('altno', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('email') ? 'error' : '' !!}">
                            <label>{{trans('form.email')}}<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::text('email',  $customer->EMAIL, ['class'=>'form-control mb-1', 'id' => 'email', 'placeholder' => 'Enter your email', 'tabindex' => 4,  ]) !!}
                                {!! $errors->first('email', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('fbid') ? 'error' : '' !!}">
                            <label>{{trans('form.fb_id')}}</label>
                            <div class="controls">
                                {!! Form::text('fbid',  $customer->FB_ID, ['class'=>'form-control mb-1', 'id' => 'fbid',  'placeholder' => 'Enter your facebook id', 'tabindex' => 5,  ]) !!}
                                {!! $errors->first('fbid', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('insid') ? 'error' : '' !!}">
                            <label>{{trans('form.ins_id')}}</label>
                            <div class="controls">
                                {!! Form::text('insid',  $customer->IG_ID, ['class'=>'form-control mb-1', 'id' => 'insid',  'placeholder' => 'Enter your instagram id', 'tabindex' => 6,  ]) !!}
                                {!! $errors->first('insid', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('ukid') ? 'error' : '' !!}">
                            <label>Azuramart Customer ID</label>
                            <div class="controls">
                                {!! Form::text('ukid', $customer->UKSHOP_ID, ['class'=>'form-control mb-1', 'id' => 'ukid',  'placeholder' => 'Enter azuramart user id', 'tabindex' => 6,  ]) !!}
                                {!! $errors->first('ukid', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('ukpass') ? 'error' : '' !!}">
                            <label>Azuramart Customer Password<span class="text-danger">*</span> <small class="">(Leave it blank if you do not want to change the password)</small></label>
                            <div class="controls">
                                {!! Form::password('ukpass', ['class'=>'form-control mb-1', 'id' => 'ukpass',  'placeholder' => 'Enter azuramart user password', 'tabindex' => 6,  ]) !!}
                                {!! $errors->first('ukpass', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-actions text-center">
                            <a href="{{route('admin.customer.list')}}" class="btn btn-warning mr-1" title="Cancel"><i class="ft-x"></i> {{ trans('form.btn_cancle') }}</a>
                            <button type="submit" onclick="return confirm('Are you sure you want to update?')" class="btn bg-primary bg-darken-1 text-white" title="Save">
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
 <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
 <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
 <script type="text/javascript" src="{{ asset('app-assets/pages/customer.js')}}"></script>
@endpush('custom_js')
