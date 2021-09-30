@extends('admin.layout.master')
@section('city_list','active')
{{-- @section('Role Management','open') --}}
@section('title')
    @lang('admin_action.edit_page_title')
@endsection
@section('page-name')
    @lang('admin_action.edit_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">@lang('admin_action.breadcrumb_title')</a>
    </li>
    <li class="breadcrumb-item active">City
    </li>
@endsection
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">

@section('content')
    <div class="col-md-12">
        <div class="card card-success min-height">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-colored-form-control">City</h4>
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

                    {!! Form::open([ 'route' => [ 'admin.customer_address_city.put', $data['city_details']->PK_NO ?? 0], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                    @csrf
                    <div class="form-body">
                        <div class="col-md-6 offset-3">
                            <div class="form-group">
                                <label>Country</label>
                                <div class="controls">
                                    {!! Form::select('country', $data['countryCombo'], $data->PK_NO ?? 2, [ 'class' => 'form-control mb-1 select2 ', 'id'=>'country' ,'data-validation-required-message' => 'This Filed Is Required']) !!}
                                </div>
                                @if ($errors->has('country'))
                                    <div class="alert alert-danger">
                                        <strong>{{ $errors->first('country') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 offset-3">
                            <div class="form-group">
                                <label>State</label>
                                <div class="controls">
                                    {!! Form::select('state', $data['stateCombo'], $data['city_details']->F_STATE_NO ?? null, [ 'class' => 'form-control mb-1 select2', 'id'=>'state','data-validation-required-message' => 'This Filed Is Required']) !!}
                                </div>
                                @if ($errors->has('state'))
                                    <div class="alert alert-danger">
                                        <strong>{{ $errors->first('state') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 offset-3">
                            <div class="form-group">
                                <label>City</label>
                                <div class="controls">
                                    {!! Form::text('city', $data['city_details']->CITY_NAME ?? null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This Filed Is Required', 'placeholder' => 'Enter City', 'tabindex' => 2 ]) !!}
                                </div>
                                @if ($errors->has('city'))
                                    <div class="alert alert-danger">
                                        <strong>{{ $errors->first('city') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-actions text-center">
                            <a title="Cancel" href="{{ route('admin.address_type.city_list_') }} " class="btn btn-warning mr-1"> <i class="ft-x"></i> @lang('form.btn_cancle')</a>
                            <button title="Update" type="submit" class="btn btn-primary"><i class="la la-check-square-o"></i> {{ isset($data['city_details']->PK_NO) != 0 ? 'Update' : 'Add'}}</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/customer.js') }}"></script>
@endpush
