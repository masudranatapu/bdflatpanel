@extends('admin.layout.master')

@section('System Settings','open')
@section('property_category','active')

@section('title') Create Property Category @endsection
@section('page-name') Create Property Category @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin_role.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">Create Property Category</li>
@endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.css') }}">
@endpush

@php($tabIndex = 0)

@section('content')
<section id="basic-form-layouts" class="min-height">
    <div class="row match-height">
        <div class="col-md-8">
         <div class="card card-success min-height">
            <div class="card-content collapse show">
               <div class="card-body">
                  {!! Form::open([ 'route' => 'admin.property.category.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                  <div class="row">
                    <div class="col-md-12">
                        <div class="form-group {!! $errors->has('category_name') ? 'error' : '' !!}">
                           <label>CATEGORY NAME<span class="text-danger">*</span></label>
                           <div class="controls">
                              {!! Form::text('category_name', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter category name', 'tabIndex' => ++$tabIndex]) !!}
                              {!! $errors->first('category_name', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="form-group {!! $errors->has('meta_title') ? 'error' : '' !!}">
                           <label>META TITLE<span class="text-danger">*</span></label>
                           <div class="controls">
                              {!! Form::text('meta_title', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter meta title', 'tabIndex' => ++$tabIndex]) !!}
                              {!! $errors->first('meta_title', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="form-group {!! $errors->has('meta_description') ? 'error' : '' !!}">
                           <label>META DESCRIPTION<span class="text-danger">*</span></label>
                           <div class="controls">
                              {!! Form::textarea('meta_description', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter meta description', 'tabIndex' => ++$tabIndex,'rows'=>'4','cols'=>'10' ]) !!}
                              {!! $errors->first('meta_description', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="form-group {!! $errors->has('body_description') ? 'error' : '' !!}">
                           <label>BODY DESCRIPTION</label>
                           <div class="controls">
                              {!! Form::textarea('body_description', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter body description', 'tabIndex' => ++$tabIndex,'rows'=>'4','cols'=>'10' ]) !!}
                              {!! $errors->first('body_description', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="form-group {!! $errors->has('null') ? 'error' : '' !!}">
                           <label>URL</label>
                           <div class="controls">
                              {!! Form::text('url', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter url', 'tabIndex' => ++$tabIndex]) !!}
                              {!! $errors->first('null', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="form-group {!! $errors->has('order') ? 'error' : '' !!}">
                           <label>ORDER<span class="text-danger">*</span></label>
                           <div class="controls">
                              {!! Form::text('order', null, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter order', 'tabIndex' => ++$tabIndex]) !!}
                              {!! $errors->first('order', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group {!! $errors->has('is_active') ? 'error' : '' !!}">
                           <label class="active">Upload Image</label>
                           <div class="controls">
                              <div class="fileupload fileupload-new" data-provides="fileupload" >
                                 <span class="fileupload-preview fileupload-exists thumbnail" style="max-width: 150px; max-height: 120px;">
                                 </span>
                                 <span>
                                 <label class="btn btn-primary btn-rounded btn-file btn-sm">
                                 <span class="fileupload-new">
                                 <i class="la la-file-image-o"></i> Select Image
                                 </span>
                                 <span class="fileupload-exists">
                                 <i class="la la-reply"></i> Change
                                 </span>
                                 {!! Form::file('image', Null,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'IS ACTIVE', 'tabIndex' => ++$tabIndex]) !!}
                                 </label>
                                 <a href="#" class="btn fileupload-exists btn-default btn-rounded  btn-sm" data-dismiss="fileupload" id="remove-thumbnail">
                                 <i class="la la-times"></i> Remove
                                 </a>
                                 </span>
                                 <br>
                                 <span class="MainToUpload edit-3-color" style="font-size: 12px; color: #bf4c4c;">File types jpg, png.</span>
                              </div>
                              {!! $errors->first('image', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group {!! $errors->has('is_active') ? 'error' : '' !!}">
                           <label class="active">Upload Icon</label>
                           <div class="controls">
                              <div class="fileupload fileupload-new" data-provides="fileupload" >
                                 <span class="fileupload-preview fileupload-exists thumbnail" style="max-width: 150px; max-height: 120px;">
                                 </span>
                                 <span>
                                 <label class="btn btn-primary btn-rounded btn-file btn-sm">
                                 <span class="fileupload-new">
                                 <i class="la la-file-image-o"></i> Select Image
                                 </span>
                                 <span class="fileupload-exists">
                                 <i class="la la-reply"></i> Change
                                 </span>
                                 {!! Form::file('icon', Null,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'IS ACTIVE', 'tabIndex' => ++$tabIndex]) !!}
                                 </label>
                                 <a href="#" class="btn fileupload-exists btn-default btn-rounded  btn-sm" data-dismiss="fileupload" id="remove-thumbnail">
                                 <i class="la la-times"></i> Remove
                                 </a>
                                 </span>
                                 <br>
                                 <span class="MainToUpload edit-3-color" style="font-size: 12px; color: #bf4c4c;">File types jpg, png.</span>
                              </div>
                              {!! $errors->first('icon', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-actions text-center mt-3">
                     <a href="{{ route('admin.property.category') }}">
                     <button type="button" class="btn btn-warning mr-1" title="Cancel">
                     <i class="ft-x"></i> @lang('form.btn_cancle')
                     </button>
                     </a>
                     <button type="submit" class="btn btn-primary" title="Save">
                     <i class="la la-check-square-o"></i> @lang('form.btn_save')
                     </button>
                  </div>
                  {!! Form::close() !!}
               </div>
            </div>
         </div>
      </div>
    </div>
    </div>
</section>
@endsection
@push('custom_js')
<script type="text/javascript" src="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.min.js') }}"></script>
@endpush('custom_js')
