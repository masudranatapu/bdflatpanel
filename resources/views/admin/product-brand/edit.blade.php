@extends('admin.layout.master')

@section('Product Management','open')
@section('product brand','active')

@section('title') Update Product Brand @endsection
@section('page-name') Update Product Brand @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin_role.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">@lang('brand.breadcrumb_sub_title')    </li>
@endsection
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.css') }}">
@endpush('custom_css')

@section('content')

<section id="basic-form-layouts">
                    <div class="row match-height min-height">
                        <div class="col-md-12">
                            <div class="card card-success">
                                <div class="card-content collapse show">
                                    <div class="card-body">

                                        {!! Form::open([ 'route' => ['product.brand.update', $brand->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                                        @csrf

                                           <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="code">@lang('form.name')<span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                    {!! Form::text('name', $brand->NAME,[ 'class' => 'form-control mb-1', 'placeholder' => 'Enter name', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                                        <div class="help-block"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label for="bname">@lang('form.code')<span class="text-danger">*</span></label>
                                                        {!! Form::text('code', $brand->CODE,[ 'class' => 'form-control mb-1', 'placeholder' => 'Enter name',  'data-validation-required-message' => 'This field is required', 'tabindex' => 2 ]) !!}
                                                        <div class="help-block"></div>
                                                    </div>
                                                </div>
                                            </div> --}}
                                            <div class="col-md-4">
                                                <div class="form-group {!! $errors->has('is_active') ? 'error' : '' !!}">
                                                    <label>IS_ACTIVE <span class="text-danger">*</span></label>
                                                    <div class="controls">
                                                        {!! Form::select('is_active', ['1'=> 'YES','0'=> 'NO'], $brand->IS_ACTIVE,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'tabindex' => 6]) !!}
                                                        {!! $errors->first('is_active', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                             <div class="form-group {!! $errors->has('is_feature') ? 'error' : '' !!}">
                                                 <label>IS FEATURE <span class="text-danger">*</span></label>
                                                 <div class="controls">
                                                     {!! Form::select('is_feature', ['1'=> 'YES','0'=> 'NO'], $brand->IS_FEATURE,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'tabindex' => 6]) !!}
                                                     {!! $errors->first('is_feature', '<label class="help-block text-danger">:message</label>') !!}
                                                 </div>
                                             </div>
                                             </div>
                                            <div class="col-md-4">
                                                <div class="form-group {!! $errors->has('logo') ? 'error' : '' !!}">
                                                   <label class="active" for="logo">Logo</label>
                                                   <div class="controls">
                                                      <div class="fileupload @if(!empty($brand->BRAND_LOGO))  {{'fileupload-exists'}} @else {{'fileupload-new'}} @endif " data-provides="fileupload" >
                                                         <span class="fileupload-preview fileupload-exists thumbnail" style="max-width: 150px; max-height: 120px;">
                                                         @if(!empty($brand->BRAND_LOGO))
                                                         <img src="{{asset($brand->BRAND_LOGO)}}" alt="Photo" class="img-fluid" height="150px" width="120px"/>
                                                         @endif
                                                         </span>
                                                         <span>
                                                         <label class="btn btn-success btn-file btn-sm text-white">
                                                         <span class="fileupload-new">
                                                         <i class="la la-file-image-o"></i> Select Image
                                                         </span>
                                                         <span class="fileupload-exists" title="Change">
                                                         <i class="la la-reply"></i> Change
                                                         </span>
                                                         {!! Form::file('logo', Null,[ 'class' => 'form-control mb-1','tabindex' => 3]) !!}
                                                         </label>
                                                         <a href="#" class="btn fileupload-exists btn-danger  btn-sm" data-dismiss="fileupload" id="remove-thumbnail" title="Remove">
                                                         <i class="la la-times"></i> Remove
                                                         </a>
                                                         </span>
                                                         <br>
                                                         <span class="MainToUpload edit-3-color" style="font-size: 12px; color: #bf4c4c;">File types jpg, png.</span>
                                                      </div>
                                                      {!! $errors->first('logo', '<label class="help-block text-danger">:message</label>') !!}
                                                   </div>
                                                </div>
                                             </div>

                                            </div>

                                            <div class="form-actions text-center mt-3">
                                                <a href="{{route('product.brand.list')}}"  type="button" class="btn btn-warning mr-1" title="Cancel">
                                                    <i class="ft-x"></i> @lang('form.btn_cancle')
                                                </a>
                                            <button type="submit" class="btn btn-primary" title="Update">
                                                <i class="la la-check-square-o"></i> @lang('form.btn_update')
                                            </button>
                                            </div>

                                        {!! Form::close() !!}


                                </div>
                            </div>
                        </div>
                    </div>
                </section>
@endsection
@push('custom_js')
<script type="text/javascript" src="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.min.js') }}"></script>
@endpush('custom_js')
