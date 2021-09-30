@extends('admin.layout.master')

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
@endpush


@section('Account Name','active')
@section('title')
    Create Bank Account Name
@endsection
@section('page-name')
    Create Bank Account Name
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('payment.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">@lang('form.bankAccount')    </li>
@endsection
<?php
    $account_combo = $data['all_source'] ?? [];
?>

@section('content')

<section id="basic-form-layouts">
                    <div class="row match-height min-height">
                        <div class="col-md-12">
                            <div class="card card-success">
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        {!! Form::open([ 'route' => 'account.bank.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}

                                        <div class="row">
                                            <div class="col-md-6 offset-3">
                                                <div class="form-group {!! $errors->has('bank_name') ? 'error' : '' !!}">
                                                    <label>{{trans('form.accountSource')}}<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::select('bank_name', $account_combo, null, ['class'=>'form-control mb-1 select2', 'id' => 'bank_name',  'placeholder' => 'Select Account Source', 'tabindex' => 2]) !!}
                                                        {!! $errors->first('bank_name', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 offset-3">
                                                <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                                    <label>@lang('form.bankAccount')<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::text('name', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter bank name', 'tabindex' => 2 ]) !!}
                                                        {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                            <div class="form-actions text-center mt-3">
                                                <a href="{{route('admin.sub_category.list')}}">
                                                    <button type="button" class="btn btn-warning mr-1">
                                                        <i class="ft-x"></i>@lang('form.btn_cancle')
                                                    </button>
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="la la-check-square-o"></i>@lang('form.btn_save')
                                                </button>
                                        {!! Form::close() !!}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
@endsection

@push('custom_js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>\
    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
@endpush
