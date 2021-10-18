@extends('admin.layout.master')
<!--push from page-->
@push('custom_css')
{{-- <link rel="stylesheet" href="{{ asset('app-assets/file_upload/image-uploader.min.css')}}"> --}}
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.css') }}">
@endpush('custom_css')
@section('blog-category','active')
@section('title') Edit Category @endsection
@section('page-name') Edit category @endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">category</a></li>
<li class="breadcrumb-item active">Edit category</li>
@endsection
<?php
$row = $data ?? [];
?>
@section('content')
<div class="content-body min-height">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-sm card-success" >
                <div class="card-content">
                    <div class="card-body">
                                {!! Form::open([ 'route' => ['web.blog.category.update',$row->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                                @csrf
                                {!! Form::hidden('id', $row->PK_NO) !!}

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group {!! $errors->has('title') ? 'error' : '' !!}">
                                            <label>Title<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::text('name', $row->NAME, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Title', 'tabindex' => 5]) !!}
                                                {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="form-group {!! $errors->has('url_slug') ? 'error' : '' !!}">
                                            <label>Url Slug</label>
                                            <div class="controls">
                                                {!! Form::text('url_slug', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter url slug', 'tabindex' => 2]) !!}
                                                {!! $errors->first('url_slug', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group {!! $errors->has('is_active') ? 'error' : '' !!}">
                                            <label>IS_ACTIVE <span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::select('is_active', ['1'=> 'YES','0'=> 'NO'], $row->IS_ACTIVE,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'IS ACTIVE', 'tabindex' => 5]) !!}
                                                {!! $errors->first('is_active', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group {!! $errors->has('banner') ? 'error' : '' !!}">
                                            <label class="active">Banner Image <span class="text-danger">*</span></label>
                                            <div class="controls">
                                                <div class="fileupload @if(!empty($row->BANNER))  {{'fileupload-exists'}} @else {{'fileupload-new'}} @endif " data-provides="fileupload" >
                                                <span class="fileupload-preview fileupload-exists thumbnail" style="max-width: 150px; max-height: 120px;">
                                                @if(!empty($row->BANNER))
                                                <img src="{{asset($row->BANNER)}}" alt="Photo" class="img-fluid" height="150px" width="120px"/>
                                                @endif
                                                </span>
                                                <span>
                                                <label class="btn btn-primary btn-rounded btn-file btn-sm">
                                                <span class="fileupload-new">
                                                <i class="la la-file-image-o"></i> Select Image
                                                </span>
                                                <span class="fileupload-exists">
                                                <i class="la la-reply"></i> Change
                                                </span>
                                                {!! Form::file('banner', Null,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required','tabindex' => 5]) !!}
                                                </label>
                                                <a href="#" class="btn fileupload-exists btn-default btn-rounded  btn-sm" data-dismiss="fileupload" id="remove-thumbnail">
                                                <i class="la la-times"></i> Remove
                                                </a>
                                                </span>
                                                <br>
                                                <span class="MainToUpload edit-3-color" style="font-size: 12px; color: #bf4c4c;">File types jpg, png.</span>
                                             </div>
                                                 {!! $errors->first('feature_image', '<label class="help-block text-danger">:message</label>') !!}
                                                        </div>

                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-actions text-center">
                                            <a href="{{route('web.blog.category')}}" class="btn btn-warning mr-1"><i class="ft-x"></i> {{ trans('form.btn_cancle') }}</a>
                                            <button type="submit" class="btn bg-primary bg-darken-1 text-white">
                                             <i class="la la-check-square-o"></i> {{ trans('form.btn_save') }} </button>
                                         </div>
                                     </div>
                                 </div>
                                 {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Recent Transactions -->
</div>
@endsection
<!--push from page-->
@push('custom_js')
<script type="text/javascript" src="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.min.js') }}"></script>
{{-- <script type="text/javascript" src="{{ asset('app-assets/file_upload/image-uploader.min.js')}}"></script>
<script>
   $(function () {
     $('.prod_def_photo_upload').imageUploader();
     });
 </script> --}}
 @endpush('custom_js')
