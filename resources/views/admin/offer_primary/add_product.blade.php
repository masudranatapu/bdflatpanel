@extends('admin.layout.master')

@section('offer_primary_list','active')
@section('offer_management','open')

@section('title')
Add new product to primary list
@endsection
@section('page-name')
Add new product to primary list
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('payment.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">@lang('payment.breadcrumb_sub_title')    </li>
@endsection

@push('custom_css')
 <style type="text/css">
       .twitter-typeahead{
         display: block !important;
     }

 </style>

@endpush

@php
$roles = userRolePermissionArray();
@endphp


@section('content')

<section id="basic-form-layouts">
    <div class="row match-height">
        <div class="col-md-12">
            <div class="card card-success min-height">
                <div class="card-content collapse show">
                    <div class="card-body">
                        {!! Form::open([ 'route' => ['admin.offer_primary.store_product'], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                        <input type="hidden" value="{{ $row->PK_NO }}" name="master_pk_no" />
                            <div class="row">
                                <div class="col-md-4 ">

                                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                                        <label>@lang('form.name')</label>
                                        <div class="controls">
                                            {!! Form::text('name', $row->PRIMARY_SET_NAME, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter account source', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1,'readonly' ]) !!}
                                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {!! $errors->has('comment') ? 'error' : '' !!}">
                                        <label>@lang('form.description')</label>
                                        <div class="controls">
                                            {!! Form::text('comment', $row->COMMENTS, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter comments', 'data-validation-required-message' => 'This field is required', 'tabindex' => 1, 'rows' => 3,'readonly' ]) !!}
                                            {!! $errors->first('comment', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>


                            <div class="col-md-2">
                                <div class="form-group with-border">
                                    <div class="controls" id="scrollable-dropdown-menu2">
                                        <button type="button" class="btn btn-primary  btn-sm search_mother_btn mt-2" data-toggle="modal" data-backdrop="true" data-target="#variant-modal" data-url="{{ route('admin.offer_primary.add_product',['id' => $row->PK_NO]) }}" data-multiple="1" >Product Finder</button>
                                    </div>
                                </div>
                            </div>


                                <div class="col-md-12 mt-2">
                                    <div id="productList">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-sm" id="product_list_tbl" >
                                               <thead>
                                                  <tr>
                                                     <th >Photo</th>
                                                     <th >Name</th>
                                                     <th >IG code</th>
                                                     <th style="width: 30px;">Action</th>
                                                  </tr>
                                               </thead>
                                               <tbody>

                                               </tbody>

                                            </table>
                                         </div>
                                    </div>
                                </div>


                            </div>
                                <div class="form-actions text-center mt-3">
                                    <a href="{{ route('admin.offer_primary.list') }}">
                                        <button type="button" class="btn btn-warning mr-1">
                                            <i class="ft-x"></i>@lang('form.btn_cancle')
                                        </button>
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="la la-check-square-o"></i>@lang('form.btn_save')
                                    </button>


                                </div>
                                {!! Form::close() !!}
                                @if($row->primaryDetails && count($row->primaryDetails) > 0 )
                                <hr>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-striped table-bordered table-sm" >
                                                <thead>
                                                    <tr>
                                                        <th colspan="7" class="text-center text-success">Existing Product List</th>
                                                    </tr>
                                                <tr>
                                                    <th class="text-center" style="width: 40px;">Sl.</th>
                                                    <th class="text-left" style="width:100px;">Photo</th>
                                                    <th class="text-left">Product Name</th>
                                                    <th class="text-left">Price (Option 1) </th>
                                                    <th class="text-left">Price (Option 2) </th>
                                                    <th class="text-left">IG code</th>
                                                    <th class="text-center" style="width: 40px;">Action</th>

                                                </tr>
                                                </thead>
                                                <tbody>

                                                    @foreach($row->primaryDetails as $key => $crow)
                                                        <tr>
                                                            <td class="text-center">{{ $key+1 }}</td>
                                                            <td  style="width:100px;">
                                                                <img src="{{ asset($crow->variant->PRIMARY_IMG_RELATIVE_PATH) }}" style="width : 40px; " />
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
        @include('admin.components._product_search_modal')
@endsection

@push('custom_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script>
$(document).on('click', '#bulkCheck', function(e){
var pageurl = `{{ route('admin.offer_primary.productlist') }}`;
        $.ajax({
            type    : 'POST',
            url     : pageurl,
            async   : true,
            data    : $('#variantSearchItem').serialize(),
            beforeSend: function () {
                $("body").css("cursor", "progress");
            },
            success: function (data) {
                console.log(data);
               // $('#advanceSearchResult tbody').html('').append(data.html);
               // $(".lightgallery").lightGallery();
                if(data.status == true ){
                    $('#product_list_tbl tbody').html('').append(data.data);
                    $('#variant-modal').modal('toggle');
                } else {
                    alert('something wrong please you should reload the page');
                    $('#variant-modal').modal('toggle');
                }

            },
            complete: function (data) {
                $("body").css("cursor", "default");
                //$.unblockUI();
            }
        });
})


</script>

@endpush
