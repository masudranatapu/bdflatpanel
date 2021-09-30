@extends('admin.layout.master')
@section('shipment_sign','active')
@section('title')
Signature | Update
@endsection
@section('page-name')
Update Signature
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
</li>
<li class="breadcrumb-item active">Signature
</li>
@endsection
<!--push from page-->
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('app-assets/file_upload/image-uploader.min.css')}}">
@endpush('custom_css')
@section('content')

<div class="card card-success min-height">
    <div class="card-header">

        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
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
            <div class="container">
                {!! Form::open([ 'route' => ['admin.shipment-signature.update', $row->PK_NO], 'method' => 'POST', 'id' => 'quickForm', 'autocomplete' => 'off', 'files' => true , 'novalidate' ]) !!}

                    @csrf
                    {!! Form::hidden('customer_id',Request::segment(2)) !!}


                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">@lang('form.name')</label>
                            {!! Form::text('name', $row->NAME, [ 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter name']) !!}
                            {!! $errors->first('name', '<label class="bg-danger">:message</label>') !!}

                        </div>

                            <div class="form-group">
                                <div class="input-field">
                                    <label class="active">Signature Photos</label>
                                    <div class="prod_def_photo_upload"  name="image" style="padding-top: .5rem;" title="Click for photo upload">
                                    </div>
                                </div>
                            </div>


                        <p style="margin-left: 15px;">Update Signature Photos</p>
                               <div class="row">
                                {{-- @foreach($product->allVariantPhotos as $photo) --}}
                                <div class="col-md-3" id="photo_div_{{$row->PK_NO}}">
                                    <div class="form-group">
                                        <div class="img-box" style="border: 2px solid #ccc; display: inline-block;">
                                            <img src="{{ asset('/') }}{{ $row->IMG_PATH }}" class="img-fluid" style="width: 200px; height: 200px;">
                                            <div class="img-box-child">
                                                <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                                <button type="button" class="btn btn-success"><i class="la la-search"></i>
                                                    Show</button>
                                                <button type="button" class="btn btn-danger photo-delete" data-id="{{$row->PK_NO}}"><i class="la la-smile-o"></i>
                                                    Delete</button>

                                            </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer col-md-offset-4">
                        <button type="submit" class="btn btn-success"><i class="la la-save"></i> Save</button>
                        <a href="{{ route('admin.shipment-signature.list') }}" class="btn btn-danger"><i class="ft-x"></i> Cancle</a>
                    </div>
                {!! Form::close() !!}

            </div>

        </div>
    </div>
</div>
@endsection

<!--push from page-->
@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/customer.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/file_upload/image-uploader.min.js')}}"></script>
<script src="{{ asset('app-assets/lightgallery/dist/js/lightgallery.min.js')}}"></script>

<script type="text/javascript">

    //for image gallery
    $(".lightgallery").lightGallery();

   //product photo delete
   $(document).on('click','.photo-delete', function(e){
    var id = $(this).attr('data-id');
    if (!confirm('Are you sure you want to delete the photo')) {
        return false;
    }
    if ('' != id) {
        var pageurl = `{{ URL::to('signature_img_delete')}}/`+id;
        $.ajax({
            type:'get',
            url:pageurl,
            async :true,
            beforeSend: function () {
                $("body").css("cursor", "progress");
                //blockUI();
            },
            success: function (data) {
                if(data.status == 'true' ){
                console.log(data.status);
                    $('#photo_div_'+id).hide();
                } else {
                    alert('something wrong please you should reload the page');
                }

            },
            complete: function (data) {
                $("body").css("cursor", "default");
                //$.unblockUI();
            }
        });
    }


})

</script>

<script>
    $(function () {

      $('.prod_def_photo_upload').imageUploader();


      });

  </script>

@endpush('custom_js')
