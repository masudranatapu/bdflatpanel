@extends('admin.layout.master')

@section('Product Management','open')
@section('features','active')

@section('title') Property Features | Create @endsection
@section('page-name') Property Features | Create @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Property Features</li>
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
                                {!! Form::open([ 'route' => 'admin.property.features.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('title', 'Title *', ['class' => 'label-title']) !!}
                                            <div class="controls">
                                                {!! Form::text('title', old('title'), ['class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Property Features']) !!}
                                                {!! $errors->first('title', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('order_id', 'Order ID *', ['class' => 'label-title']) !!}
                                            <div class="controls">
                                                {!! Form::text('order_id', old('order_id'), ['class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Order ID']) !!}
                                                {!! $errors->first('order_id', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('icon', 'Icon *', ['class' => 'label-title']) !!}
                                            <div class="controls">
                                                {!! Form::file('icon', old('icon'), ['class' => 'form-control', 'data-validation-required-message' => 'This field is required']) !!}
                                                {!! $errors->first('icon', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('status', 'Status *', ['class' => 'label-title']) !!}
                                            <div class="controls">
                                                {!! Form::select('status', $status ?? [], old('status'), ['class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Select Status']) !!}
                                                {!! $errors->first('status', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <a href="{{ route('admin.property.features')}}">
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
