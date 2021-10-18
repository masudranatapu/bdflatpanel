@extends('admin.layout.master')

@section('Customer Management','open')
@section('shop category','active')

@section('title')
   Update Shop Category
@endsection
@section('page-name')
    Update Shop Category
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin_role.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">Update shop category</li>
@endsection
@section('content')

<section id="basic-form-layouts">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card card-success min-height">
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        {!! Form::open([ 'route' => ['admin.shop.category.update', $category->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                                        <div class="row">
                                            <div class="col-md-4">
                                               <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                                  <label class="text-uppercase">@lang('form.name')<span class="text-danger">*</span></label>
                                                  <div class="controls">
                                                     {!! Form::text('name', $category->NAME, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter category name', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                                                     {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                                  </div>
                                               </div>
                                            </div>
                                         </div>
                                         <div class="row">
                                            <div class="col-md-4">
                                               <div class="form-group {!! $errors->has('is_active') ? 'error' : '' !!}">
                                                   <label>IS ACTIVE <span class="text-danger">*</span></label>
                                                   <div class="controls">
                                                       {!! Form::select('is_active', ['1'=> 'YES','0'=> 'NO'], $category->IS_ACTIVE,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'tabindex' => 3]) !!}
                                                       {!! $errors->first('is_active', '<label class="help-block text-danger">:message</label>') !!}
                                                   </div>
                                               </div>
                                           </div>
                                         </div>
                                         <div class="form-actions text-center mt-3">
                                            <a href="{{ route('admin.shop.category.list') }}">
                                            <button type="button" class="btn btn-warning mr-1" title="Cancel">
                                            <i class="ft-x"></i> @lang('form.btn_cancle')
                                            </button>
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
                    </div>

                </section>
                @push('custom_js')
                <script type="text/javascript" src="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.min.js') }}"></script>
                @endpush('custom_js')
@endsection
