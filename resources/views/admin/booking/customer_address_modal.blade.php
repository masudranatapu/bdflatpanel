<div id="add_new_address_table_wrap" style="display: block;">
    <div id="add_new_address_table">
        {!! Form::open([ 'class' => 'form-horizontal', 'id' => 'add_new_customer_form' , 'files' => true , 'novalidate']) !!}
        {!! Form::hidden('customer_id_',0,['id'=>'customer_id_modal_']) !!}
        {!! Form::hidden('booking_create',1,['id'=>'booking_create']) !!}
        {!! Form::hidden('booking_id',$booking ?? '',['id'=>'booking_id']) !!}
        {!! Form::hidden('is_reseller',$isreseller ?? 0,['id'=>'is_reseller']) !!}
        <div class="row">
            <div class="col-md-6 offset-md-3" id="order_date_section">
                <div class="form-group {!! $errors->has('order_date') ? 'error' : '' !!}">
                    <label>Order Date<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <span class="la la-calendar-o"></span>
                            </span>
                        </div>
                        <input type='text' class="form-control pickadate" placeholder="Order Date" value="{{date('d-m-Y')}}" name="order_date" />
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <h3><strong>Address</strong></h3>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('addresstype') ? 'error' : '' !!}">
                    <label>{{trans('form.address_type')}}</label>
                    <div class="controls">

                    {!! Form::select('addresstype', $address, null, ['class'=>'form-control mb-1 ', 'data-validation-required-message' => 'This field is required', 'id' => 'addresstype']) !!}
                    {!! $errors->first('addresstype', '<label class="help-block text-danger">:message</label>') !!}
                </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('customeraddress') ? 'error' : '' !!}">
                    <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::text('customeraddress', null, ['class'=>'form-control mb-1', 'id' => 'customeraddress', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Name', 'tabindex' => 3, 'required' ]) !!}
                        {!! $errors->first('customeraddress', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <label>{{trans('form.mobile_no')}}<span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="phonecode2">+60</span>
                    </div>
                    {!! Form::text('mobilenoadd',null,[ 'class' => 'form-control', 'placeholder' => 'Enter Mobile No.', 'id' => 'mobilenoadd']) !!}
                    {!! $errors->first('mobilenoadd', '<label class="help-block text-danger">:message</label>') !!}

                </div>
            </div>
            <div class="col-md-4">

                <div class="form-group {!! $errors->has('country') ? 'error' : '' !!}">
                    <label>{{trans('form.country')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        <select name="country" id="country" class="form-control mb-1 ">
                            @foreach ($country as $item)
                                <option value="{{ $item->PK_NO }}" data-dial_code="{{ $item->DIAL_CODE }}" {{ $item->PK_NO == 2 ? "selected='selected'" : '' }}>{{ $item->NAME }} ({{ $item->DIAL_CODE }})</option>
                            @endforeach
                        </select>
                        {!! $errors->first('country', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('post_code') ? 'error' : '' !!}">
                    <label>{{trans('form.post_code')}}<span class="text-danger">*</span></label>
                    <div class="controls" id="scrollable-dropdown-menu2">
                        <input type="search" name="post_code" id="post_code_" class="form-control search-input4" placeholder="Post Code" autocomplete="off" required>

                        {!! $errors->first('post_code', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                    <div id="post_code_appended_div">
                        {!! Form::hidden('post_code', null, ['id'=>'post_code_hidden']) !!}
                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('city') ? 'error' : '' !!}">
                    <label>{{trans('form.city')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                    {!! Form::select('city', array(), null, ['class'=>'form-control mb-1 ',
                    'data-validation-required-message' => 'Select City', 'id' => 'city','tabindex' =>
                    1, 'placeholder' =>'Select City' ]) !!}
                        {!! $errors->first('city', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            {{-- STATE 1 --}}
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('state') ? 'error' : '' !!}">
                    <label>{{trans('form.state')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::select('state', array(), null, ['class'=>'form-control mb-1 ',
                        'data-validation-required-message' => 'Select State', 'placeholder' => 'Select State', 'id' => 'state','tabindex' => 1 ]) !!}
                        {!! $errors->first('state', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_1') ? 'error' : '' !!}">
                    <label>{{trans('form.address_1')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::text('ad_1', null, ['class'=>'form-control mb-1', 'id' => 'ad_1_',  'placeholder' => 'Enter Address', 'tabindex' => 4  ]) !!}
                        {!! $errors->first('ad_1', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_2') ? 'error' : '' !!}">
                    <label>{{trans('form.address_2')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_2', null, ['class'=>'form-control mb-1', 'id' => 'ad_2_',  'placeholder' => 'Enter Address', 'tabindex' => 5,  ]) !!}
                        {!! $errors->first('ad_2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_3') ? 'error' : '' !!}">
                    <label>{{trans('form.address_3')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_3', null, ['class'=>'form-control mb-1', 'id' => 'ad_3_',  'placeholder' => 'Enter Address', 'tabindex' => 6  ]) !!}
                        {!! $errors->first('ad_3', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_4') ? 'error' : '' !!}">
                    <label>{{trans('form.address_4')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_4', null, ['class'=>'form-control mb-1', 'id' => 'ad_4_',  'placeholder' => 'Enter Address', 'tabindex' => 7,  ]) !!}
                        {!! $errors->first('ad_4', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('location') ? 'error' : '' !!}">
                    <label>{{trans('form.location')}}</label>
                    <div class="controls">
                        {!! Form::text('location', null, ['class'=>'form-control mb-1', 'id' => 'location',  'placeholder' => 'Enter Location', 'tabindex' => 11,  ]) !!}
                        {!! $errors->first('location', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <br>
                    <div class="controls">
                        {{Form::hidden('same_as_add',0)}}
                        <label id="same_as_label" style="float: right"><input type="checkbox" name="same_as_add" id="checkbox1">  {{ trans('form.same_as_add') }}</label>
                        {!! $errors->first('same_as_add', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
        </div>
        {{-- Billing Address --}}
        <div class="row" id="billing_address_section" style="display: none">
            <div class="col-md-12">
                <h3><strong>Billing Address</strong></h3>
                <br>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('addresstype2') ? 'error' : '' !!}">
                    <label>{{trans('form.address_type')}}</label>
                    <div class="controls">
                    {!! Form::select('addresstype2', $address, 2, ['class'=>'form-control mb-1', 'id' => 'addresstype2']) !!}
                    {!! $errors->first('addresstype2', '<label class="help-block text-danger">:message</label>') !!}
                </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('customeraddress2') ? 'error' : '' !!}">
                    <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::text('customeraddress2',  null, ['class'=>'form-control mb-1', 'id' => 'customeraddress2', 'placeholder' => 'Enter Name', 'tabindex' => 3, '' ]) !!}
                        {!! $errors->first('customeraddress2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <label>{{trans('form.mobile_no')}}<span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="phonecode3">+60</span>
                    </div>
                    {!! Form::text('mobilenoadd2',null,[ 'class' => 'form-control', 'placeholder' => 'Enter Mobile No.', 'id' => 'mobilenoadd2']) !!}
                    {!! $errors->first('mobilenoadd2', '<label class="help-block text-danger">:message</label>') !!}
                    {{-- <input type="text" class="form-control" placeholder="Addon to Left" aria-describedby="basic-addon1"> --}}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {!! $errors->has('country2') ? 'error' : '' !!}">
                    <label>{{trans('form.country')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        <select name="country2" id="country2" class="form-control mb-1">
                            @foreach ($country as $item)
                                <option value="{{ $item->PK_NO }}" data-dial_code="{{ $item->DIAL_CODE }}" {{ $item->PK_NO == 2 ? "selected='selected'" : '' }}>{{ $item->NAME }} ({{ $item->DIAL_CODE }})</option>
                            @endforeach
                        </select>
                        {!! $errors->first('country2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('post_code2') ? 'error' : '' !!}">
                    <label>{{trans('form.post_code')}}<span class="text-danger">*</span></label>
                    <div class="controls" id="scrollable-dropdown-menu4">
                        <input type="search" name="post_code2" id="post_code_2" class="form-control search-input8" placeholder="Post Code" autocomplete="off" required>
                        {!! $errors->first('post_code2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                    <div id="post_code2_appended_div">
                        {!! Form::hidden('post_code2', null, ['id'=>'post_code2']) !!}
                    </div>
                </div>

            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('city2') ? 'error' : '' !!}">
                    <label>{{trans('form.city')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::select('city2', array(), null, ['class'=>'form-control mb-1',
                        'data-validation-required-message' => 'Select City', 'id' => 'city2','tabindex' =>
                        1, 'placeholder' => 'Select City', 'data-url' => URL::to('customer_pCode') ]) !!}
                        {!! $errors->first('city', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('state2') ? 'error' : '' !!}">
                    <label>{{trans('form.state')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::select('state2', array(), null, ['class'=>'form-control mb-1 ',
                        'data-validation-required-message' => 'Select City', 'placeholder' => 'Select State', 'id' => 'state2','tabindex' => 1, 'data-url' => URL::to('customer_city') ]) !!}
                        {!! $errors->first('state2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_12') ? 'error' : '' !!}">
                    <label>{{trans('form.address_1')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::text('ad_12',  null, ['class'=>'form-control mb-1', 'id' => 'ad1',  'placeholder' => 'Enter Address', 'tabindex' => 4,  ]) !!}
                        {!! $errors->first('ad_12', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_22') ? 'error' : '' !!}">
                    <label>{{trans('form.address_2')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_22',  null, ['class'=>'form-control mb-1', 'id' => 'ad3',  'placeholder' => 'Enter Address', 'tabindex' => 5,  ]) !!}
                        {!! $errors->first('ad_22', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_32') ? 'error' : '' !!}">
                    <label>{{trans('form.address_3')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_32',  null, ['class'=>'form-control mb-1', 'id' => 'ad3',  'placeholder' => 'Enter Address', 'tabindex' => 6,  ]) !!}
                        {!! $errors->first('ad_32', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_42') ? 'error' : '' !!}">
                    <label>{{trans('form.address_4')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_42',  null, ['class'=>'form-control mb-1', 'id' => 'ad3',  'placeholder' => 'Enter Address', 'tabindex' => 7,  ]) !!}
                        {!! $errors->first('ad_42', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('location2') ? 'error' : '' !!}">
                    <label>{{trans('form.location')}}</label>
                    <div class="controls">
                        {!! Form::text('location2',  null, ['class'=>'form-control mb-1', 'id' => 'location',  'placeholder' => 'Enter Location', 'tabindex' => 11,  ]) !!}
                        {!! $errors->first('location2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mt-2 mb-2">
            <div class="form-actions text-center">
                <button type="button" onclick="add_address_bookorder()" class="btn bg-primary bg-darken-1 text-white">
                <i class="la la-check-square-o"></i><span id="action_btn"> Add Address </span></button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
<script>
    $('#checkbox1').change(function() {
        if(this.checked) {
            $('#billing_address_section').fadeIn();
        }else{
            $('#billing_address_section').fadeOut();
        }
    });
    $('.pickadate').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
        max:"<?php echo date('d-m-Y'); ?>",
    });
</script>
