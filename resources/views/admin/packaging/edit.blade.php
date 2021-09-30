@extends('admin.layout.master')

@section('Shipping','open')
@section('processing_shipping','active')

@section('page-name') {{ 'Edit Packinging' }} @endsection
@section('title') {{ 'Packinging | Edit' }} @endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('shipping.breadcrumb_dashboard_title')</a></li>
<li class="breadcrumb-item active">Edit Packaging</li>
@endsection


@push('custom_css')
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">


<style type="text/css">
.btn-info, .bg-blue {background-color: #56a3ab !important; border-color: #56a3ab !important;}
    #scrollable-dropdown-menu .tt-menu {
      max-height: 260px;
      overflow-y: auto;
      width: 100%;
      border: 1px solid #333;
      border-radius: 5px;

  }
  #scrollable-dropdown-menu2 .tt-menu {
      max-height: 260px;
      overflow-y: auto;
      width: 100%;
      border: 1px solid #333;
      border-radius: 5px;

  }
  .twitter-typeahead{
    display: block !important;
}

.btn-sm2{
    padding: 0.68rem 0.75rem;
    border-radius: 0px;
    width: 80px;
}

.box-table .form-control {
    height: 25px !important;
    font-size: 14px !important;
    padding: 0 5px 0 8px !important;
    border-radius: 0px;
}
tr.htr > th{
    padding: 8px !important; font-size: 16px !important;
}
.box-table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th{
    padding: 2px;  font-size: 14px; vertical-align: middle;
}

form.packing_list_frm .form-group {
    margin-bottom: 0.5rem;
}
form.packing_list_frm .label {

    margin-bottom: 0.1rem;
}
.item-amt{pointer-events: none;}

</style>

@endpush('custom_css')

<?php

$categories_combo       = $data['category_combo'] ?? [];
$roles                  = userRolePermissionArray();
$box_combo              = $data['box_combo'] ?? [];
$nex_box_serial_no             = 1;


?>

@section('content')
<div class="card card-success min-height" >
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i>{{ 'Edit Packaging' }}  </h4>
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
            <div class="row">
                <div class="col-md-4">
                    {!! Form::open([ 'route' => 'admin.packagingitem.store', 'method' => 'post', 'class' => 'form-horizontal packing_list_frm', 'files' => true, 'novalidate' ]) !!}
                    @csrf
                    <input type="hidden" name="product_variant_no" value="" id="product_variant_no" />


                    <div class="row">
                        <div class="col-md-12">
                            <div class=""  style="padding: 20px; background-color: #eee;">
                                <div class="with-border bg-grey">
                                    <div class="row">
                                        <div class="col-sm-6" style="padding-right: 2px;">
                                            <div class="input-group" style="margin-bottom: 5px;">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-sm btn-info btn-sm2">Ship No</button>
                                                </div>
                                                <input type="hidden" class="form-control number-only" id="shipment_no" name="shipment_no" placeholder="Enter Shipment No" value="{{$data['rows'][0]->F_SHIPMENT_NO}}" readonly="">
                                                <input type="text" class="form-control number-only"  value="{{$data['rows'][0]->SHIPMENT_NAME}}" readonly="">
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="padding-left: 2px;">
                                            <div class="form-group  {!! $errors->has('box_no') ? 'error' : '' !!}">
                                                <div class="controls">
                                                    <select name="box_no" id="box_no" class="form-control select2" data-validation-required-message="This field is required" tabindex="1" >
                                                        @if(isset($box_combo) && count($box_combo) >0 )
                                                        @foreach($box_combo as $key => $val )
                                                        <option value="{{ $val->F_BOX_NO ?? $val->PK_NO  }}">{{ 'BOX NO '.$val->BOX_SERIAL_NO }}</option>
                                                        @php $nex_box_serial_no++ @endphp
                                                        @endforeach
                                                        @endif
                                                    </select>

                                                    {!! $errors->first('box_no', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group with-border" style="margin-bottom: 5px;">
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-sm btn-danger btn-sm2">Barcode</button>
                                        </div>
                                        <input type="text" class="form-control" name="barcode_no" id="barcode_no" placeholder="Scan/Write Barcode" autocomplete="off" tabindex="1">
                                    </div>
                                </div>
                                <div class="form-group with-border">
                                    <div class="controls" id="scrollable-dropdown-menu2">
                                        <input type="search" name="q" id="key_search" class="form-control search-input2" placeholder="Search by Keywords" autocomplete="off" tabindex="2">
                                    </div>
                                </div>
                                 <div class="form-group with-border">
                                    <h4 id="product_variant_name" class="text-success" style="font-style: italic;"></h4>
                                 </div>
                                <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                                    <label>{{trans('form.category')}}<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::select('category', $categories_combo, null, ['class'=>'form-control select2', 'id' => 'category', 'data-validation-required-message' => 'This field is required', 'tabindex' => 3, 'data-url' => URL::to('prod_subcategory') ]) !!}
                                        {!! $errors->first('category', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                                <div class="form-group {!! $errors->has('sub_category') ? 'error' : '' !!}">
                                    <label>{{trans('form.sub_category')}}<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::select('sub_category', array(), null, ['class'=>'form-control select2', 'id' => 'sub_category', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Select sub category', 'data-url' => URL::to('get_hscode_by_scat'), 'tabindex' => 3] ) !!}
                                        {!! $errors->first('sub_category', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                                <div class="form-group {!! $errors->has('hs_code') ? 'error' : '' !!}">
                                    <label>{{trans('form.hs_code')}}<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::select('hs_code', array(), null, [ 'class' => 'form-control', 'placeholder' => 'Enter product HS code', 'data-validation-required-message' => 'This field is required', 'tabindex' => 4, 'id' => 'hs_code'] ) !!}
                                        {!! $errors->first('hs_code', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                                <div class="form-group {!! $errors->has('description') ? 'error' : '' !!}">
                                    <label>{{trans('form.description')}}<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::text('description', null, [ 'class' => 'form-control', 'placeholder' => 'Enter short note', 'tabindex' => 5, 'id' => 'description', 'data-validation-required-message' => 'This field is required' ] ) !!}
                                        {!! $errors->first('description', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6" style="padding-right: 5px;">
                                        <div class="form-group {!! $errors->has('qty') ? 'error' : '' !!}">
                                            <label>{{trans('form.quantity')}}<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::number('qty', null, [ 'class' => 'form-control', 'placeholder' => 'Enter quantity', 'tabindex' => 6, 'id' => 'qty', 'required', 'min' => '1',  'data-validation-required-message' => 'This field is required'] ) !!}
                                                {!! $errors->first('qty', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6" style="padding-left: 5px;">
                                        <div class="form-group {!! $errors->has('price') ? 'error' : '' !!}">
                                            <label>{{trans('form.price')}}<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::number('price', null, [ 'class' => 'form-control', 'placeholder' => 'Enter price', 'tabindex' => 7, 'id' => 'price', 'step' => '0.01', 'required',  'data-validation-required-message' => 'This field is required'] ) !!}
                                                {!! $errors->first('price', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>




                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary btn-sm col-sm-12" id="btn-add-item"><i class="fa fa-plus"></i> Add Item</button>
                                </div>

                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}

                </div>

                <div class="col-md-8">
                  <div class="row">
                    <div class="col-md-12" style="">
                        <div class=""  style="padding: 20px; background-color: #eee;">
                            <div class="with-border">
                                <h4 class="box-title col-md-12 text-info pl-0">Packing Product List <button type="button" class="btn btn-info btn-xs" id="showAllBox" title="Show All Boxes" style="padding-left: 10px; padding-right: 10px;">Show All Boxes</button> <button type="button" class="btn btn-xs btn-primary" title="ADD NEW BOX" data-toggle="modal" data-target="#addNewBoxModal"><i class="la la-plus"></i></button> <a href="{{route('admin.packaginglist.pdf',['shipment_no' => $data['rows'][0]->F_SHIPMENT_NO])}}" class="btn btn-info pull-right btn-xs" style="padding-left: 10px; padding-right: 10px;" title="DOWNLOAD PDF">PDF</a></h4>



                            </div>
                            <div class="box-body table-responsive" id="box_product_list" style="height: 900px; width: 100%; overflow-y: scroll;">
                                <div id="packingItem">
                                    @include('admin.packaging._packing_item', $data)
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
</div>

<!--Edit Product Subcategory  html-->
<div class="modal fade text-left" id="addNewBoxModal" tabindex="-1" role="dialog" aria-labelledby="add_box" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" >Add New Box</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                {!! Form::open([ 'route' => 'admin.packagingbox.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                <input class="form-control mb-1"  name="shippment_no" type="hidden" value="{{ $data['rows'][0]->F_SHIPMENT_NO }}">

                <div class="modal-body">
                    <div class="form-group {!! $errors->has('box_serial_no') ? 'error' : '' !!}">
                        <label>BOX NO <span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('box_serial_no', $nex_box_serial_no , [ 'class' => 'form-control mb-1',  'data-validation-required-message' => 'This field is required', 'tabindex' => 1 , 'readonly' => 'readonly']) !!}
                            {!! $errors->first('box_serial_no', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                    <div class="form-group {!! $errors->has('box_width') ? 'error' : '' !!}">
                        <label>Width<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('box_width', 46 , [ 'class' => 'form-control mb-1',  'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('box_width', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                    <div class="form-group {!! $errors->has('box_length') ? 'error' : '' !!}">
                        <label>Length <span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('box_length', 46 , [ 'class' => 'form-control mb-1',  'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('box_length', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                    <div class="form-group {!! $errors->has('box_height') ? 'error' : '' !!}">
                        <label>Height <span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('box_height', 78 , [ 'class' => 'form-control mb-1',  'data-validation-required-message' => 'This field is required', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('box_height', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-secondary btn-sm" data-dismiss="modal" value="Close">
                    <input type="submit" class="btn btn-primary btn-sm submit-btn" value="Save">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('custom_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/product.js') }}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('change','.box_type', function(e){
        var box_no      = $(this).data('box_no');
        var width_cm    = $('option:selected', this).attr('data-width_cm');
        var length_cm   = $('option:selected', this).attr('data-length_cm');
        var height_cm   = $('option:selected', this).attr('data-height_cm');
        $('.box_w_'+box_no).val(width_cm);
        $('.box_l_'+box_no).val(length_cm);
        $('.box_h_'+box_no).val(height_cm);

    })

    $(document).on('submit', 'form.box_updateFrm', function(e){
        e.preventDefault();
        if(confirm('Do you really want to update the box? ')) {
            e.currentTarget.submit();
        }

    })




    //unlock full table
    $(document).on('click', '.unlock_box', function(e){

        $(this).css('display','none');
        var box_no              = $(this).data('box_no');
        var box_qty_input       = 'box_'+box_no+'_qnty';
        var box_price_input     = 'box_'+box_no+'_price';
        var box_scat_input      = 'box_'+box_no+'_scat_name';
        var box_name_input      = 'box_'+box_no+'_prc_name';
        var invoice_details_input      = 'box_'+box_no+'_invoice_details';

        $('.lock_box_'+box_no).css('display','inline-block');
        $('input.'+box_qty_input).removeAttr('readonly');
        $('input.'+box_scat_input).removeAttr('readonly');
        $('input.'+box_name_input).removeAttr('readonly');
        $('input.'+box_price_input).removeAttr('readonly');
        $('input.'+invoice_details_input).removeAttr('readonly');
        $('input[id="box_weight_'+box_no+'"]').removeAttr('readonly');
        $('.invoice_id_'+box_no).removeAttr('disabled');
        $('.box_type_'+box_no).removeAttr('disabled');
        $('.box_size_'+box_no).removeAttr('disabled');


    })


    //item delete fro, box
    $(document).on('change', '.bulk_action', function(e){
        var bulk_action     =   $(this).val();
        var box_no          =   $(this).attr('data-box_no');
        var delete_url      =   $('option:selected', this).attr('data-url');
        var shipment_no     =   $('#shipment_no').val();
        var records         =   new Array();

        $("#box_"+box_no+" input:checkbox[name=list_id]:checked").each(function() {
            records.push($(this).val());
         });


         if (records == '') {
            alert('You do not checked any record');
            return false;
        }else{
            if (bulk_action == 'delete') {
                if(confirm('Do you really want to delete the item(s) ? ')) {
                    $.ajax({
                        type: "post",
                        data:{ shipment_no:shipment_no, records:records },
                        url: delete_url,
                        beforeSend:function(){},
                        success: function (data) {
                            if (data.status == true) {
                                $('#packingItem').html(data.html);
                                showHideBoxItem();
                                itemFrmReset();
                            }
                        },
                        complete: function (data){}
                    });
                }else{
                    $('.bulk_action').val(0);
                }
            }
            if (bulk_action == 'move') {
                $('#moveRecords').modal({backdrop: 'static', keyboard: false});
                $('.move_box_no').show();
                $('.move_box_'+box_no).hide();
                $('.move_records').val(records);
            }
        }
    })


    /*booking customer radio button events*/

    jQuery(document).ready(function($) {
        typeahead('igcode');
    });

    function typeahead(type) {
        var get_url = $('#base_url').val();
        var engine = new Bloodhound({
            remote: {
                url: get_url+'/product/get-variant-info-like?q=%QUERY%&type='+type,
                wildcard: '%QUERY%',
                cache: false,
            },
            datumTokenizer: Bloodhound.tokenizers.whitespace('q'),
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });
        $('.search-input2').on('typeahead:selected', function (e, datum) {


            if (datum != '') {
                var pageurl = `{{ URL::to('get-packaginglist-info/`+datum.IG_CODE+`/igcode')}}`;
                $.ajax({
                    type:'get',
                    url:pageurl,
                    async :true,
                    beforeSend: function () {
                        $("body").css("cursor", "progress");
                    },
                    success: function (data) {
                        console.log(data.row.product);

                        if(data != ''){
                            $('#product_variant_no').val(data.row.product.PK_NO);
                            $("#category").select2().val(data.row.product.CATEGORY_PK_NO).trigger("change");
                            setTimeout(function(){

                                $("#sub_category").select2().val(data.row.product.SUB_CATEGORY_PK_NO).trigger("change");
                            },500);
                            setTimeout(function(){

                                $("#hs_code").select2().val(data.row.product.HS_CODE).trigger("change");
                            },1000);
                            $('#description').val(data.row.product.VARIANT_NAME);


                        }else{
                            alert('Data not found');
                        }

                    },
                    complete: function (data) {
                        $("body").css("cursor", "default");
                    }
                });
            }
        });
        $(".search-input2").typeahead({
            hint: true,
            highlight: true,
            minLength: 1,
        }, {
            source: engine.ttAdapter(),
            name: 'NAME',
            displayKey: 'IG_CODE',
            limit: 20,

            // the key from the array we want to display (name,id,email,etc...)
            templates: {
                empty: [
                '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                ],
                header: [
                '<div class="list-group search-results-dropdown">'
                ],
                suggestion: function (data) {
                    return '<span class="list-group-item" style="cursor: pointer;">' + data.NAME + '</span>'
                }
            }
        });
    }
</script>

<script type="text/javascript">

    $(function () {
        showHideBoxItem();

        $('form.packing_list_frm').on('submit', function (e) {

          e.preventDefault();
          var url = $(this).attr('action');

          $.ajax({
            type: 'post',
            url: url,
            data: $('form.packing_list_frm').serialize(),
            success: function (data) {

              if (data.status == true) {
                $('#packingItem').html(data.html);
                showHideBoxItem();
                $('#barcode_no').val('');
                $('#key_search').val('');
                $('#description').val('');
                $('#qty').val('');
                $('#price').val('');
              }
          }
  });

      });

    });

</script>

<script type="text/javascript">


    // Get variant by barcode
    $(document).on('keypress','#barcode_no', function(e) {
        if(e.which == 13) {
            var bar_code = $(this).val();

            if (bar_code != '') {
                var pageurl = `{{ URL::to('get-packaginglist-info/`+bar_code+`/barcode')}}`;
                $.ajax({
                    type:'get',
                    url:pageurl,
                    async :true,
                    beforeSend: function () {
                        $("body").css("cursor", "progress");
                    },
                    success: function (data) {

                        if(data != ''){

                            $('#product_variant_no').val(data.row.product.PK_NO);
                            $('#description').val(data.row.product.VARIANT_NAME);
                            $("#category").select2().val(data.row.product.CATEGORY_PK_NO).trigger("change");
                            setTimeout(function(){

                                $("#sub_category").select2().val(data.row.product.SUB_CATEGORY_PK_NO).trigger("change");
                            },500);
                            setTimeout(function(){

                                $("#hs_code").select2().val(data.row.product.HS_CODE).trigger("change");
                            },1000);


                        }else{
                            alert('Data not found');
                        }

                    },
                    complete: function (data) {
                        $("body").css("cursor", "default");
                    }
                });
            }


           e.preventDefault();
       }
   });



    jQuery.fn.scrollTo = function(elem, speed) {
        $(this).animate({
          scrollTop:  $(this).scrollTop() - $(this).offset().top + $(elem).offset().top
      }, speed == undefined ? 1000 : speed);
        return this;
    };


    $(document).on("change","#box_no",function(){
       showHideBoxItem();

    });
    $(document).on('click', '#showAllBox', function(e){
        $('.box_updateFrm').show();
    })
    function showHideBoxItem()
    {
        var box_no = $('#box_no').val()*1;
       // alert(box_no);
        $('.box_updateFrm').hide();
        $('.'+box_no).show();
    }



</script>
@endpush('custom_js')
