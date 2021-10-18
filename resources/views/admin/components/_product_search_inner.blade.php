@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
<!--for image gallery-->
<link rel="stylesheet" href="{{ asset('app-assets/lightgallery/dist/css/lightgallery.min.css') }}">
<style>
    #bulkCheck{display: none;}
    #advanceSearchResult td{vertical-align: middle;}
    .variant_name{position: relative;}
    .variant_name div{position: absolute; right: 0; bottom: 0;}
    table.table-sm th, .table.table-sm td {
        padding: 0.5rem .1rem;
    }
</style>

@endpush


<?php
$category       = request()->get('category') ?? '';
$sub_category   = request()->get('sub_category') ?? '';
$brand          = request()->get('brand') ?? '';
$prod_model     = request()->get('prod_model') ?? '';
$name           = request()->get('name') ?? '';
$vat_class      = request()->get('vat_class') ?? '';
$hs_code        = request()->get('hs_code') ?? '';
$ig_code        = request()->get('ig_code') ?? '';
$sku_id         = request()->get('sku_id') ?? '';
$barcode        = request()->get('barcode') ?? '';
$preferred_shipping_method        = request()->get('preferred_shipping_method') ?? '';


$categories_combo       = getCategorCombo() ?? [];
$subcategories_combo    = getSubCategorCombo($category) ?? [];
$vat_class_combo        = getVatClassCombo() ?? [];
$brand_combo            = getBrandCombo() ?? [];
$model_combo            = getModelCombo($brand) ?? [];
$hscode_combo           = getHScodeCombo($sub_category) ?? [];

$rows = $data['rows'] ?? null;

//$current_route = request()->route()->getName();

$list_type = \Session::get('list_type');

// dd($current_route);

// echo "<pre>";
//    print_r($subcategories_combo) ;
//    die();

?>

<div>
   {!! Form::open([ 'route' => 'admin.product_search', 'method' => 'get', 'class' => 'form-horizontal', 'files' => true , 'novalidate', 'id' => 'advanceSearch']) !!}
   @csrf

   <input type="hidden" name="parent_url" value="" id="serach_parent_url">
   <input type="hidden" name="multiple_select" value="" id="multiple_select">
   <input type="hidden" name="mother_url" value="{{request()->get('parent_url') ?? request()->get('mother_url')}}" />


   <div class="row">
      <div class="col-md-3">
          <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
              <label>{{trans('form.category')}}</label>
              <div class="controls">
                  {!! Form::select('category', $categories_combo, $category, ['class'=>'form-control mb-1 select2', 'id' => 'category', 'placeholder' => 'Select category', 'tabindex' => 1, 'data-url' => URL::to('prod_subcategory') ]) !!}
                  {!! $errors->first('category', '<label class="help-block text-danger">:message</label>') !!}
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="form-group {!! $errors->has('sub_category') ? 'error' : '' !!}">
              <label>{{trans('form.sub_category')}}</label>
              <div class="controls">
                  {!! Form::select('sub_category', $subcategories_combo, $sub_category, ['class'=>'form-control mb-1 select2', 'id' => 'sub_category',  'placeholder' => 'Select sub category', 'data-url' => URL::to('get_hscode_by_scat'), 'tabindex' => 2] ) !!}
                  {!! $errors->first('sub_category', '<label class="help-block text-danger">:message</label>') !!}
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="form-group {!! $errors->has('brand') ? 'error' : '' !!}">
              <label>{{trans('form.brand')}}</label>
              <div class="controls">
                  {!! Form::select('brand', $brand_combo, $brand, ['class'=>'form-control mb-1 select2', 'id' => 'brand', 'placeholder' => 'Select brand', 'tabindex' => 3, 'data-url' => URL::to('prod_model')]) !!}
                  {!! $errors->first('brand', '<label class="help-block text-danger">:message</label>') !!}
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="form-group {!! $errors->has('prod_model') ? 'error' : '' !!}">
              <label>{{trans('form.model')}}</label>
              <div class="controls">
                  {!! Form::select('prod_model', $model_combo, $prod_model, ['class'=>'form-control mb-1 select2 prod_model_add', 'id' => 'prod_model', 'placeholder' => 'Select model', 'tabindex' => 4]) !!}
                  {!! $errors->first('prod_model', '<label class="help-block text-danger">:message</label>') !!}
              </div>
          </div>
      </div>

      <div class="col-md-3">
          <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
              <label>{{trans('form.search_key')}}</label>
              <div class="controls">
                  {!! Form::text('name', $name, [ 'class' => 'form-control mb-1', 'placeholder' => 'Search by keywords', 'tabindex' => 5]) !!}
                  {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
              </div>
          </div>
      </div>
      <div class="col-md-3">
        <div class="form-group {!! $errors->has('ig_code') ? 'error' : '' !!}">
            <label>IG Code</label>
            <div class="controls">
                {!! Form::text('ig_code', $ig_code, [ 'class' => 'form-control mb-1', 'placeholder' => 'Search by IG code', 'tabindex' => 5]) !!}
                {!! $errors->first('ig_code', '<label class="help-block text-danger">:message</label>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group {!! $errors->has('sku_id') ? 'error' : '' !!}">
            <label>SKU </label>
            <div class="controls">
                {!! Form::text('sku_id', $sku_id, [ 'class' => 'form-control mb-1', 'placeholder' => 'Search by SKU', 'tabindex' => 5]) !!}
                {!! $errors->first('sku_id', '<label class="help-block text-danger">:message</label>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group {!! $errors->has('barcode') ? 'error' : '' !!}">
            <label>{{trans('form.barcode')}}</label>
            <div class="controls">
                {!! Form::text('barcode', $barcode, [ 'class' => 'form-control mb-1', 'placeholder' => 'Search by barcode', 'tabindex' => 5]) !!}
                {!! $errors->first('barcode', '<label class="help-block text-danger">:message</label>') !!}
            </div>
        </div>
    </div>
      <div class="col-md-3">
        <div class="form-group {!! $errors->has('preferred_shipping_method') ? 'error' : '' !!}">
            <label>{{trans('form.preferred_shipping_method')}}</label>
            <div class="controls">
                {!! Form::select('preferred_shipping_method', array('AIR' => 'AIR','SEA' => 'SEA'), $preferred_shipping_method, [ 'class' => 'form-control mb-1', 'placeholder' => 'Select Shipping Method', 'tabindex' => 6]) !!}
                {!! $errors->first('preferred_shipping_method', '<label class="help-block text-danger">:message</label>') !!}
            </div>
        </div>
    </div>
      {{--<div class="col-md-3">
          <div class="form-group {!! $errors->has('vat_class') ? 'error' : '' !!}">
              <label>{{trans('form.vat_class')}}</label>
              <div class="controls">
                  {!! Form::select('vat_class', $vat_class_combo, $vat_class, ['class'=>'form-control mb-1 ', 'placeholder' => 'Select vat class', 'tabindex' => 7]) !!}
                  {!! $errors->first('vat_class', '<label class="help-block text-danger">:message</label>') !!}
              </div>
          </div>
      </div> --}}
      {{--<div class="col-md-3">
          <div class="form-group {!! $errors->has('hs_code') ? 'error' : '' !!}">
              <label>{{trans('form.hs_code')}}</label>
              <div class="controls">
                  {!! Form::select('hs_code', $hscode_combo, $hs_code, [ 'class' => 'form-control mb-1 select2-input', 'placeholder' => 'Enter product HS code', 'tabindex' => 8, 'id' => 'hs_code']) !!}
                  {!! $errors->first('hs_code', '<label class="help-block text-danger">:message</label>') !!}
              </div>
          </div>
      </div> --}}
  </div>
  <div class="col-md-12">
      <div class="form-actions text-center">
          <button type="submit" class="btn bg-primary bg-darken-1 text-white" title="Search"><i class="la la-search"></i> {{ trans('form.btn_search') }} </button>
          </div>
      </div>

      {!! Form::close() !!}
  </div>


  <div>
    {!! Form::open([ 'route' => 'admin.add_to_mother_page', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate', 'id' => 'variantSearchItem']) !!}

    <input type="hidden" name="parent_url" value="{{request()->get('parent_url') ?? request()->get('mother_url')}}" />
    <div class="row">
        <div class="col-md-12">
            <table class="table table-sm table-bordered table-hover" id="advanceSearchResult" style="font-size: 12px;line-height: 18px;">
                <thead>
                    <tr>
                        <th colspan=" @if(request()->get('parent_url') ?? request()->get('mother_url')) 8 @else 10 @endif" class="text-center text-danger">SEARCH RESULT </th>

                          @if(request()->get('parent_url') ?? request()->get('mother_url'))
                          <th colspan="2">
                             <button type="submit" class="btn btn-sm btn-primary pull-right">Go back with result set or empty</button>
                          </th>
                            @endif

                    </tr>
                    <tr>
                        <th class="text-center" style="width:20px;">SL</th>
                        <th class="text-center" style="width: 60px;">Photos</th>
                        <th style="">Variant Name</th>
                        <th style="width:200px;">Code</th>

                        <th style="width:80px;">Size/Color</th>
                        <th style="width:120px;">Brand/Model</th>
                        <th style="width:120px;">Category</th>
                        <th title="Postage Cost / Shippimg Cost" style="width: 110px;">SM/SS(RM)<br>AIR/SEA(RM)</th>
                        <th title="Unit Variant Price" style="width: 110px;">Unit Price <br>Reg/Ins (RM)</th>

                        <th style="width: 60px;" class="text-center">
                            Action
                            @if($list_type != 'searchlist')
                                <button type="button" class="btn btn-xs btn-info" id="bulkCheck">Submit Select</button>
                            @endif
                        </th>

                    </tr>
                </thead>
                <tbody>
                    {{-- @if($rows &&  $rows->count() > 0)

                    @else
                    <tr>
                        <td colspan="9" class="text-center">Data not found</td>

                    </tr>
                    @endif --}}
                </tbody>
            </table>
        </div>
    </div>

    {!! Form::close() !!}

</div>








@push('custom_js')
<script src="{{ asset('app-assets/js/scripts/tooltip/tooltip.js')}}"></script>
<!--for image gallery-->
<script src="{{ asset('app-assets/lightgallery/dist/js/lightgallery.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/product.js')}}"></script>

<script>
//for image gallery
$(".lightgallery").lightGallery();

$(document).ready(function(){
    $('#advanceSearch').submit(function(){

        var pageurl = `{{ route('admin.searchlist.view.post') }}`;
        $.ajax({
            type    : 'POST',
            url     : pageurl,
            async   : true,
            data    : $(this).serialize(),
            beforeSend: function () {
                $("body").css("cursor", "progress");
            },
            success: function (data) {
                console.log(data);
                $('#advanceSearchResult tbody').html('').append(data.html);
                $(".lightgallery").lightGallery();
                // if(data.status == true ){
                //     $('#photo_div_'+id).hide();
                // } else {
                //     alert('something wrong please you should reload the page');
                // }

            },
            complete: function (data) {
                $("body").css("cursor", "default");
                //$.unblockUI();
            }
        });




        return false;

    });
});

// $(document).on('click', '.variant_select', function(e){
//     var barcode = $(this).data('barcode');
//     $('#bar-code').val(barcode);

// })


$(document).on('click', '#advanceSearchResult tbody tr', function() {
    var barcode     = $(this).find(".variant_select").data("barcode");
    var multiple    = $(this).find(".variant_select").data("multiple");
    if(multiple == 0){
        $('#bar-code').val(barcode).trigger('enterPress');
        getItemList(barcode);
        $('#variant-modal').modal('toggle');
    }

    if(multiple == 1){

    }

});


// data-multiple

</script>

@endpush('custom_js')
