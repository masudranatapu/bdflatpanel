@extends('admin.layout.master')
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/summernote.css')}}">
@endpush('custom_css')
@section('faq','active')
@section('title') faq @endsection
@section('page-name') Create faq @endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">faq</a></li>
<li class="breadcrumb-item active">Create faq</li>
@endsection
@section('content')
<div class="content-body min-height">
   <div class="row">
      <div class="col-md-12">
         <div class="card card-sm card-success" >
            <div class="card-content">
               <div class="card-body">
                  {!! Form::open([ 'route' => 'web.faq.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group {!! $errors->has('question') ? 'error' : '' !!}">
                           <label>Question<span class="text-danger">*</span></label>
                           <div class="controls">
                              {!! Form::text('question', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Question', 'tabindex' => 1]) !!}
                              {!! $errors->first('question', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="form-group {!! $errors->has('answer') ? 'error' : '' !!}">
                           <label>Answer</label>
                           <div class="controls">
                              {!! Form::text('answer', null, [ 'class' => 'form-control mb-1','placeholder' => 'Enter Answer', 'tabindex' => 2]) !!}
                              {!! $errors->first('answer', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group {!! $errors->has('is_active') ? 'error' : '' !!}">
                           <label>Is Active <span class="text-danger">*</span></label>
                           <div class="controls">
                              {!! Form::select('is_active', ['1'=> 'YES','0'=> 'NO'], 1,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'IS ACTIVE', 'tabindex' => 3]) !!}
                              {!! $errors->first('is_active', '<label class="help-block text-danger">:message</label>') !!}
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="form-actions text-center">
                           <a href="{{route('web.faq')}}" class="btn btn-warning mr-1"><i class="ft-x"></i> {{ trans('form.btn_cancle') }}</a>
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
</div>
@endsection
@push('custom_js')
@endpush('custom_js')
