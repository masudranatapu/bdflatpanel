@extends('admin.layout.master')

@section('shelve_list','active')
@section('title')
    {{ $data != null ? 'Edit Shelve' : 'Add Shelve' }}
@endsection
@section('page-name')
    {{ $data != null ? 'Edit Shelve' : 'Add Shelve' }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('shelve.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">{{ $data != null ? 'Edit Shelve' : 'Add Shelve' }}
    </li>
@endsection
@php
    $roles = userRolePermissionArray();
@endphp
@section('content')
    <div class="content-body">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> {{ $data != null ? 'Edit Shelve' : 'Add Shelve' }}</h4>

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
                                {!! Form::open([ 'route' => 'admin.shelve.post', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                                {!! Form::hidden('pk_no', Request::segment(2) ?? 0) !!}
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group {!! $errors->has('zone_barcode') ? 'error' : '' !!}">
                                                    <label>Zone Barcode<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::text('zone_barcode', $data->ZONE_BARCODE ?? '',[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Zone Barcode', 'tabindex' => 1 ]) !!}
                                                        {!! $errors->first('zone_barcode', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group {!! $errors->has('warehouse') ? 'error' : '' !!}">
                                                    <label>Select Warehouse<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::select('warehouse', $warehouse, $data->F_INV_WAREHOUSE_NO ?? 2, ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'This field is required', 'id' => 'addressCombo']) !!}
                                                        {!! $errors->first('warehouse', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group {!! $errors->has('description') ? 'error' : '' !!}">
                                                    <label>Description</label>
                                                    <div class="controls">
                                                        {!! Form::textarea('description', $data->DESCRIPTION ?? '', [ 'class' => 'form-control mb-1 summernote', 'placeholder' => 'Enter Description', 'tabindex' => 1, 'rows' => 3,'data-validation-required-message' => 'This field is required' ]) !!}
                                                        {!! $errors->first('description', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions mt-10 text-center">
                                        <a href="{{ route('admin.shelve.list')}}">
                                            <button type="button" class="btn btn-warning mr-1">
                                                <i class="ft-x"></i> Cancel
                                            </button>
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="la la-check-square-o"></i>
                                            {{ $data != null ? 'Update' : 'Save' }}
                                        </button>
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
