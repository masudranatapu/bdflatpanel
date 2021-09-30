@extends('admin.layout.master')
<!--push from page-->
@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/summernote.css')}}">
@endpush('custom_css')
@section('Team Members','active')
@section('title') Team Members @endsection
@section('page-name') Team Members @endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Team Members</a></li>
    <li class="breadcrumb-item active"> Team Members</li>
@endsection
<?php
$row = $data['team_members'] ?? [];
?>
@section('content')
    <div class="content-body min-height">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-sm card-success">
                    <div class="card-content">
                        <div class="card-body">
                            {!! Form::open([ 'route' => ['web.team_members.update',$row->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                        <label>Name<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('name', $row->NAME, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Name', 'tabindex' => 1]) !!}
                                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('designation') ? 'error' : '' !!}">
                                        <label>Designation<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('designation', $row->DESIGNATION, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Designation', 'tabindex' => 2]) !!}
                                            {!! $errors->first('designation', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('fb_url') ? 'error' : '' !!}">
                                        <label>FB Url<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('fb_url', $row->FB_URL, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter FB Url', 'tabindex' => 1]) !!}
                                            {!! $errors->first('fb_url', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('twitter_url') ? 'error' : '' !!}">
                                        <label>Twitter Url<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('twitter_url', $row->TWITTER_URL, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Twitter Url', 'tabindex' => 2]) !!}
                                            {!! $errors->first('twitter_url', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('linkedin_url') ? 'error' : '' !!}">
                                        <label>Linkedin Url<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('linkedin_url', $row->LINKEDIN_URL, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Linkedin Url', 'tabindex' => 1]) !!}
                                            {!! $errors->first('linkedin_url', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('printerest_url') ? 'error' : '' !!}">
                                        <label>Printerest Url<span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::text('printerest_url', $row->PRINTEREST_URL, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Printerest Url', 'tabindex' => 2]) !!}
                                            {!! $errors->first('printerest_url', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('banner') ? 'error' : '' !!}">
                                        <label class="active">Banner</label>
                                        <div class="controls">
                                            <div class="fileupload @if(!empty($row->IMAGE))  {{'fileupload-exists'}} @else {{'fileupload-new'}} @endif "
                                                 data-provides="fileupload">
                                 <span class="fileupload-preview fileupload-exists thumbnail" style="max-width: 150px; max-height: 120px;">
                                 @if(!empty($row->IMAGE))
                                         <img src="{{asset($row->IMAGE)}}" alt="Photo" class="img-fluid" height="150px" width="120px"/>
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
                                 {!! Form::file('banner', Null,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'tabindex' => 3]) !!}
                                 </label>
                                 <a href="#" class="btn fileupload-exists btn-default btn-rounded  btn-sm" data-dismiss="fileupload" id="remove-thumbnail">
                                 <i class="la la-times"></i> Remove
                                 </a>
                                 </span>
                                                <br>
                                                <span class="MainToUpload edit-3-color" style="font-size: 12px; color: #bf4c4c;">File types jpg, png.</span>
                                            </div>
                                            {!! $errors->first('banner', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('is_active') ? 'error' : '' !!}">
                                        <label>Is Active <span class="text-danger">*</span></label>
                                        <div class="controls">
                                            {!! Form::select('is_active', ['1'=> 'YES','0'=> 'NO'], null,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'tabindex' => 13]) !!}
                                            {!! $errors->first('is_active', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-actions text-center">
                                        <a href="{{route('web.testimonial')}}" class="btn btn-warning mr-1"><i class="ft-x"></i> {{ trans('form.btn_cancle') }}</a>
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
        $(document).ready(function () {
            $('.summernote').summernote({
                callbacks: {
                    onImageUpload: function (image) {
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
                        ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']], ,
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
                success: function (url) {
                    var image = $('<img>').attr('src', url).attr('class', 'img-fluid');
                    $('.summernote').summernote("insertNode", image[0]);
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }

    </script>
@endpush('custom_js')
