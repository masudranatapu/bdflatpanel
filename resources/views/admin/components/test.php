<div>
   {!! Form::open([ 'route' => 'admin.product_search', 'method' => 'get', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}

   <input type="hidden" name="parent_url" value="" id="serach_parent_url">


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
              <label>{{trans('form.name')}}</label>
              <div class="controls">
                  {!! Form::text('name', $name, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter product name', 'tabindex' => 5]) !!}
                  {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="form-group {!! $errors->has('vat_class') ? 'error' : '' !!}">
              <label>{{trans('form.vat_class')}}</label>
              <div class="controls">
                  {!! Form::select('vat_class', $vat_class_combo, $vat_class, ['class'=>'form-control mb-1 ', 'placeholder' => 'Select vat class', 'tabindex' => 7]) !!}
                  {!! $errors->first('vat_class', '<label class="help-block text-danger">:message</label>') !!}
              </div>
          </div>
      </div>
      <div class="col-md-3">
          <div class="form-group {!! $errors->has('hs_code') ? 'error' : '' !!}">
              <label>{{trans('form.default_hs_code')}}</label>
              <div class="controls">
                  {!! Form::select('hs_code', $hscode_combo, $hs_code, [ 'class' => 'form-control mb-1 select2-input', 'placeholder' => 'Enter product HS code', 'tabindex' => 8, 'id' => 'hs_code']) !!}
                  {!! $errors->first('hs_code', '<label class="help-block text-danger">:message</label>') !!}
              </div>
          </div>
      </div>
  </div>
  <div class="col-md-12">
      <div class="form-actions text-center">
          <button type="button" class="btn btn-warning mr-1" data-dismiss="modal">Close</button>

          <button type="submit" class="btn bg-primary bg-darken-1 text-white">
              <i class="la la-search"></i> {{ trans('form.btn_search') }} </button>
          </div>
      </div>

      {!! Form::close() !!}
  </div>