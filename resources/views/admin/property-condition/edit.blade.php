@extends('admin.layout.master')

@section('Product Management','open')
@section('property_condition','active')

@section('title') Property Condition | Update @endsection
@section('page-name') Property Condition | Update @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Property Condition</li>
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
                                {!! Form::open([ 'route' => ['admin.property.condition.update', $data['condition']->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('property_condition', 'Property Condition *', ['class' => 'label-title']) !!}
                                            <div class="controls">
                                                {!! Form::text('property_condition', old('property_condition', $data['condition']->PROD_CONDITION), ['class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Property Condition']) !!}
                                                {!! $errors->first('property_condition', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('order_id', 'Order ID *', ['class' => 'label-title']) !!}
                                            <div class="controls">
                                                {!! Form::text('order_id', old('order_id', $data['condition']->ORDER_ID), ['class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Order ID']) !!}
                                                {!! $errors->first('order_id', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('status', 'Status *', ['class' => 'label-title']) !!}
                                            <div class="controls">
                                                {!! Form::select('status', $status ?? [], old('status', $data['condition']->IS_ACTIVE), ['class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Select Status']) !!}
                                                {!! $errors->first('status', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <a href="{{ route('admin.property.condition')}}">
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
