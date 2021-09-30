@extends('admin.layout.master')
<!--push from page-->
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/summernote.css')}}">
@endpush('custom_css')
@section('article','open')
@section('blog-article','active')
@section('title') article @endsection
@section('page-name') Create article @endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">article</a></li>
<li class="breadcrumb-item active">Create article</li>
@endsection
<?php
   $row        = $data['article'] ?? [];
   $category   = $data['category'] ?? [];

   ?>
@section('content')
<div class="content-body min-height">
   <div class="row">
      <div class="col-md-12">
         <div class="card card-sm card-success" >
            <div class="card-content">
               <div class="card-body">
                  {!! Form::open([ 'route' => ['web.blog.article.update',$row->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                  @csrf
                  {!! Form::hidden('id', $row->PK_NO) !!}
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group {!! $errors->has('title') ? 'error' : '' !!}">
                           <label>Title<span class="text-danger">*</span></label>
                           <div class="controls">
                              {!! Form::text('title', $row->TITLE, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Title', 'tabindex' => 1]) !!}
                              {!! $errors->first('title', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     {{--
                     <div class="col-md-6">
                        <div class="form-group {!! $errors->has('url_slug') ? 'error' : '' !!}">
                           <label>Url Slug</label>
                           <div class="controls">
                              {!! Form::text('url_slug', null, [ 'class' => 'form-control mb-1','placeholder' => 'Enter url_slug', 'tabindex' => 2]) !!}
                              {!! $errors->first('url_slug', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     --}}
                  </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group {!! $errors->has('summary') ? 'error' : '' !!}">
                           <label>Summary</label>
                           <div class="controls">
                              {!! Form::textarea('summary', $row->summary, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter short description about the article', 'tabindex' => 4, 'rows' => 3,'id' =>'text-editor' ]) !!}
                              {!! $errors->first('summary', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group {!! $errors->has('body') ? 'error' : '' !!}">
                           <label>Article Body</label>
                           <div class="controls">
                              {!! Form::textarea('body', $row->BODY, [ 'class' => 'form-control mb-1 summernote', 'placeholder' => 'Enter short description about the article', 'tabindex' => 4, 'rows' => 3,'id' =>'text-editor' ]) !!}
                              {!! $errors->first('body', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                           <label>Category<span class="text-danger">*</span></label>
                           <div class="controls">
                              {!! Form::select('category', $category, $row->ARTICLE_CATEGORY, ['class'=>'form-control mb-1', 'id' => 'category', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Select category', 'tabindex' => 5]) !!}
                              {!! $errors->first('category', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group {!! $errors->has('tags') ? 'error' : '' !!}">
                           <label>Tags<span class="text-danger">*</span></label>
                           <div class="controls">
                              {!! Form::text('tags', $row->TAGS, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Lifestyle,', 'tabindex' => 1]) !!}
                              {!! $errors->first('tags', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group {!! $errors->has('author') ? 'error' : '' !!}">
                           <label>Author<span class="text-danger">*</span></label>
                           <div class="controls">
                              {!! Form::text('author', $row->AUTHOR_NAME, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Lifestyle,', 'tabindex' => 6]) !!}
                              {!! $errors->first('author', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group {!! $errors->has('is_feature') ? 'error' : '' !!}">
                           <label>Is Feature <span class="text-danger">*</span></label>
                           <div class="controls">
                              {!! Form::select('is_feature', ['1'=> 'YES','0'=> 'NO'], $row->IS_FEATURE,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'IS FEATURE', 'tabindex' => 6]) !!}
                              {!! $errors->first('is_feature', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group {!! $errors->has('is_active') ? 'error' : '' !!}">
                           <label>Is Active <span class="text-danger">*</span></label>
                           <div class="controls">
                              {!! Form::select('is_active', ['1'=> 'YES','0'=> 'NO'], $row->IS_ACTIVE,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'IS ACTIVE', 'tabindex' => 7]) !!}
                              {!! $errors->first('is_active', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group {!! $errors->has('feature_image') ? 'error' : '' !!}">
                           <label class="active">Feature Image <span class="text-danger">*</span></label>
                           <div class="controls">
                              <div class="fileupload @if(!empty($row->THUMBNAIL_IMAGE))  {{'fileupload-exists'}} @else {{'fileupload-new'}} @endif " data-provides="fileupload" >
                                 <span class="fileupload-preview fileupload-exists thumbnail" style="max-width: 150px; max-height: 120px;">
                                 @if(!empty($row->THUMBNAIL_IMAGE))
                                 <img src="{{asset($row->THUMBNAIL_IMAGE)}}" alt="Photo" class="img-fluid" height="150px" width="120px"/>
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
                                 {!! Form::file('feature_image', Null,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'tabindex' => 8]) !!}
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
                           <a href="{{route('web.blog.article')}}" class="btn btn-warning mr-1"><i class="ft-x"></i> {{ trans('form.btn_cancle') }}</a>
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
<script src="{{ asset('app-assets/vendors/js/editors/summernote/summernote.js') }}"></script>
{{-- <script src="{{ asset('app-assets/js/scripts/editors/editor-summernote.js') }}"></script> --}}
<script>
   $(document).ready(function() {
       $('.summernote').summernote({
        callbacks: {
           onImageUpload: function(image) {
               uploadImage(image[0]);
           }
       },
       toolbar: [
           ['style', ['bold', 'italic', 'underline', 'clear']],
           ['font', ['strikethrough', 'superscript', 'subscript']],
           ['fontsize', ['fontsize']],
           ['color', ['color']],
           ['para', ['ul', 'ol', 'paragraph', 'style']],
           ['height', ['height']],
           ['Insert', ['picture', 'link', 'video', 'table', 'hr']],
           ['Misc', ['fullscreen', 'codeview', 'help']],
           ['mybutton', ['highlight']]
       ],
       imageAttributes: {
           icon: '<i class="note-icon-pencil"/>',
           figureClass: 'figureClass',
           figcaptionClass: 'captionClass',
           captionText: 'Caption Goes Here.',
           manageAspectRatio: true // true = Lock the Image Width/Height, Default to true
       },
       lang: 'en-US',
       popover: {
           image: [
               ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],,
               ['float', ['floatLeft', 'floatRight', 'floatNone']],
               ['remove', ['removeMedia']],
               ['custom', ['imageAttributes']],
           ],
       },
       imageTitle: {
         specificAltField: true,
       },
       height: 300,
       minHeight: null,
       maxHeight: null,
       focus: false
   });
   });

   $.ajaxSetup({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
       });
       function uploadImage(image) {
           var data = new FormData();
           data.append("image", image);
           $.ajax({
               url: '{{URL("ajax/text-editor/image-upload/")}}',
               cache: false,
               contentType: false,
               processData: false,
               data: data,
               type: "post",
               success: function(url) {
                   var image = $('<img>').attr('src', url).attr('class', 'img-fluid');
                   $('.summernote').summernote("insertNode", image[0]);
               },
               error: function(data) {
                   console.log(data);
               }
           });
       }

</script>
@endpush('custom_js')
