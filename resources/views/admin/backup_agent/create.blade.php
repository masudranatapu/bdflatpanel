@extends('admin.layout.master')

@section('Sales Agent','open')
@section('add_agent','active')

@section('title') Agent | Create @endsection
@section('page-name') Create Agent @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Create Agent</li>
@endsection

<!--push from page-->
@push('custom_css')
 <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
@endpush('custom_css')

@section('content')
    <div class="card card-success min-height">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> Add New
            Agent</h4>
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
                {!! Form::open([ 'route' => 'admin.agent.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                    @csrf
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                    <label>@lang('agent.name')<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::text('name', null,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter name', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {!! $errors->has('phone') ? 'error' : '' !!}">
                                    <label>@lang('agent.phone')<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::text('phone', null,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter phone number', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('phone', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {!! $errors->has('alt_phone') ? 'error' : '' !!}">
                                    <label>@lang('agent.alt_phone')</label>
                                    <div class="controls">
                                        {!! Form::text('alt_phone', null,[ 'class' => 'form-control mb-1', 'placeholder' => 'Enter alternate phone number', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('alt_phone', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group {!! $errors->has('fb_id') ? 'error' : '' !!}">
                                    <label>@lang('agent.fb_id')</label>
                                    <div class="controls">
                                        {!! Form::text('fb_id', null,[ 'class' => 'form-control mb-1', 'placeholder' => 'Enter Facebook ID', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('fb_id', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {!! $errors->has('ig_id') ? 'error' : '' !!}">
                                    <label>@lang('agent.ig_id')</label>
                                    <div class="controls">
                                        {!! Form::text('ig_id', null,[ 'class' => 'form-control mb-1', 'placeholder' => 'Enter Instagram ID', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('ig_id', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {!! $errors->has('uk_id') ? 'error' : '' !!}">
                                    <label>@lang('agent.uk_id')</label>
                                    <div class="controls">
                                        {!! Form::text('uk_id', null,[ 'class' => 'form-control mb-1', 'placeholder' => 'Enter UKShop ID', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('uk_id', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {!! $errors->has('email') ? 'error' : '' !!}">
                                    <label>@lang('agent.email')<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::text('email', null,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Email', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('email', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group {!! $errors->has('uk_pass') ? 'error' : '' !!}">
                                    <label>@lang('agent.password')<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::password('uk_pass',[ 'class' => 'form-control mb-1', 'placeholder' => 'Enter UKShop Password', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('uk_pass', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>
                    <div class="form-actions mt-10 text-center">
                        <a href="{{ route('admin.agent.list')}}">
                            <button type="button" class="btn btn-warning mr-1">
                                <i class="ft-x"></i> Cancel
                            </button>
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="la la-check-square-o"></i> Save
                        </button>
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
@endpush('custom_js')
