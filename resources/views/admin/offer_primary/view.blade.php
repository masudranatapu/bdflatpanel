@extends('admin.layout.master')

@section('offer_primary_list','active')
@section('offer_management','open')

@section('title') Primary list view @endsection
@section('page-name') Primary list view @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('payment.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">View primary list </li>
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

                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                        <label>@lang('form.name')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('name', $row->PRIMARY_SET_NAME, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter account source', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1, 'readonly' ]) !!}
                                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group {!! $errors->has('comment') ? 'error' : '' !!}">
                                        <label>@lang('form.description')<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('comment', $row->COMMENTS, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter comments', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1, 'rows' => 3, 'readonly']) !!}
                                            {!! $errors->first('comment', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>


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
                                            <th class="text-left" style="">Code</th>


                                        </tr>
                                        </thead>
                                        <tbody>

                                            @foreach($row->primaryDetails as $key => $crow)
                                                <tr title="PK : {{ $crow->F_PRD_VARIANT_NO }}">
                                                    <td>{{ $key+1 }}</td>
                                                    <td>
                                                        <img src="{{ asset($crow->variant->PRIMARY_IMG_RELATIVE_PATH) }}" style="width : 50px; " />
                                                    </td>
                                                    <td>
                                                       <div>

                                                        {{ $crow->PRD_VARIANT_NAME }}
                                                        {{-- <p>IG CODE : {{ $crow->SKUID }}</p> --}}
                                                       </div>
                                                    </td>
                                                    <td>{{ number_format($crow->variant->REGULAR_PRICE,2) }}</td>
                                                    <td>{{ number_format($crow->variant->INSTALLMENT_PRICE,2) }}</td>
                                                    <td>
                                                        <p>IG CODE : {{ $crow->variant->MRK_ID_COMPOSITE_CODE }}</p>
                                                        <p>BARCODE : {{ $crow->variant->BARCODE }}</p>
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
