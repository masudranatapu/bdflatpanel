@extends('admin.layout.master')

@section('Product Management','open')
@section('city_list','active')

@section('title') City / Division | Update @endsection
@section('page-name') City / Division | Update @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">City / Division</li>
@endsection

@php
    $roles = userRolePermissionArray();
$status = [
    1 => 'Active',
    0 => 'Inactive'
];
@endphp
@push('custom_css')
    <link rel="stylesheet" type="text/css"
          href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@push('custom_js')
    <!-- BEGIN: Data Table-->
    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
    <!-- END: Data Table-->
@endpush

@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-6">
                    <div class="card card-sm card-success">
                        <div class="card-header pl-2">
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
                            <div class="card-body card-dashboard">
                                {!! Form::open([ 'route' => ['admin.city.update', $data['city']->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('city_name', 'City Name *', ['class' => 'label-title']) !!}
                                            <div class="controls">
                                                {!! Form::text('city_name', old('city_name', $data['city']->CITY_NAME), ['class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'City Name']) !!}
                                                {!! $errors->first('city_name', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('latitude', 'Latitude *', ['class' => 'label-title']) !!}
                                            <div class="controls">
                                                {!! Form::text('latitude', old('latitude', $data['city']->LAT), ['class' => 'form-control', 'placeholder' => 'Latitude']) !!}
                                                {!! $errors->first('latitude', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('longitude', 'Longitude *', ['class' => 'label-title']) !!}
                                            <div class="controls">
                                                {!! Form::text('longitude', old('longitude', $data['city']->LON), ['class' => 'form-control', 'placeholder' => 'Longitude']) !!}
                                                {!! $errors->first('longitude', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('order', 'Order *', ['class' => 'label-title']) !!}
                                            <div class="controls">
                                                {!! Form::number('order', old('order', $data['city']->ORDER_ID), ['class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Order']) !!}
                                                {!! $errors->first('order', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('status', 'Status *', ['class' => 'label-title']) !!}
                                            <div class="controls">
                                                {!! Form::select('status', $status ?? [], old('status', $data['city']->IS_ACTIVE), ['class' => 'form-control', 'data-validation-required-message' => 'This field is required']) !!}
                                                {!! $errors->first('status', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('populate', 'Is Populate *', ['class' => 'label-title']) !!}
                                            <div class="controls">
                                                {!! Form::select('populate', [1 => 'Populate', 0 => 'Common'], old('populate', $data['city']->IS_POPULATED), ['class' => 'form-control', 'data-validation-required-message' => 'This field is required']) !!}
                                                {!! $errors->first('populate', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <a href="{{ route('admin.city.list')}}">
                                            <button type="button" class="btn btn-warning mr-1">
                                                <i class="ft-x"></i> Cancel
                                            </button>
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="la la-check-square-o"></i> Save
                                        </button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection


@push('custom_js')

    <!--script only for brand page-->
    <script type="text/javascript" src="{{ asset('app-assets/pages/category.js')}}"></script>


@endpush('custom_js')
