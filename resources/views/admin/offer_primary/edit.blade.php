@extends('admin.layout.master')

@section('offer_primary_list','active')
@section('offer_management','open')

@section('title')
Edit primary list
@endsection
@section('page-name')
Edit primary list
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('payment.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">Edit primary list </li>
@endsection

@php
$roles = userRolePermissionArray();
@endphp

@section('content')

<section id="basic-form-layouts">
    <div class="row match-height min-height">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-content collapse show">
                    <div class="card-body">
                        {!! Form::open([ 'route' => ['admin.offer_primary.update',$row->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                        <label>@lang('form.name')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('name', $row->PRIMARY_SET_NAME, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter name', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group {!! $errors->has('comment') ? 'error' : '' !!}">
                                        <label>@lang('form.description')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('comment', $row->COMMENTS, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter description', 'data-validation-required-message' => 'This field is required', 'tabindex' => 2, 'rows' => 3, ]) !!}
                                            {!! $errors->first('comment', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions text-center mt-3">
                                <a href="{{ route('admin.offer_primary.list') }}" class="btn btn-warning mr-1"><i class="ft-x" title="Cancel"></i> @lang('form.btn_cancle')</a>
                                <button type="submit" class="btn btn-primary" title="Save"><i class="la la-check-square-o"></i> @lang('form.btn_save')</button>


                            </div>
                            {!! Form::close() !!}
                            @if($row->primaryDetails && count($row->primaryDetails) > 0 )
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-sm" >
                                        <thead>
                                        <tr>
                                            <th style="width: 40px;">Sl.</th>
                                            <th class="text-left" style="">Photo</th>
                                            <th class="text-left" style="">Product Name</th>
                                            <th class="text-left">Price (Option 1) </th>
                                            <th class="text-left">Price (Option 2) </th>
                                            <th class="text-left" style="">SKU ID</th>
                                            <th class="text-center" style="">Action</th>

                                        </tr>
                                        </thead>
                                        <tbody>

                                            @foreach($row->primaryDetails as $key => $crow)
                                                <tr>
                                                    <td>{{ $key+1 }}</td>
                                                    <td>
                                                        <img src="{{ asset($crow->variant->PRIMARY_IMG_RELATIVE_PATH) }}" style="width : 50px; " />
                                                    </td>
                                                    <td>{{ $crow->PRD_VARIANT_NAME }}</td>
                                                    <td>{{ number_format($crow->variant->REGULAR_PRICE,2) }}</td>
                                                    <td>{{ number_format($crow->variant->INSTALLMENT_PRICE,2) }}</td>
                                                    <td>{{ $crow->SKUID }}</td>
                                                    <td class="text-center">
                                                        @if(hasAccessAbility('delete_product', $roles))
                                                        <a href="{{ route('admin.offer_primary.deleteproduct', [$crow->PK_NO]) }}" class="btn btn-xs btn-danger mr-1" onclick="return confirm('Are you sure you want to delete the product with it\'s variant product ?')" title="DELETE"><i class="la la-trash"></i></a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
@endsection
