<div id="add_new_address_table_wrap" style="display: block;">
    <div class="table-responsive p-1">
        <table id="view_address_table" class="table table-striped table-bordered table-hover table-sm dataTable no-footer"
            style="font-size: 13px;">
            <thead>
                <tr>
                    <th colspan="text-center">Action</th>
                    <th>Full Name</th>
                    <th style="width: 10px">Address</th>
                    <th>Post Code</th>
                    <th>Phone Number</th>
                    <th>Address Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows ?? array() as $row)
                <tr>
                    <td class="text-center">
                        {{-- <label for="" style="color: #666ee8">{{ $row->PK_NO ?? 0 }}</label><br> --}}
                        <input type="radio" id="address_no_{{ $row->PK_NO ?? 0 }}" name="change_address" class="flat-red" data-customer_post_code="{{ $row->POST_CODE ?? '' }}" data-customeraddress="{{ $row->NAME ?? '' }}" data-address_no="{{ $row->PK_NO ?? 0 }}" data-mobilenoadd="{{ $row->TEL_NO ?? '' }}" data-ad_1="{{ $row->ADDRESS_LINE_1 ?? '' }}" data-ad_2="{{ $row->ADDRESS_LINE_2 ?? '' }}" data-ad_3="{{ $row->ADDRESS_LINE_3 ?? '' }}" data-ad_4="{{ $row->ADDRESS_LINE_4 ?? '' }}" data-location="{{ $row->LOCATION ?? '' }}" data-country="{{ $row->country->NAME ?? '' }}" data-dial_code="{{ $row->country->DIAL_CODE ?? '' }}" data-state="{{ $row->state->STATE_NAME ?? '' }}" data-city="{{ $row->city->CITY_NAME ?? '' }}" data-country_no="{{ $row->F_COUNTRY_NO ?? '' }}" data-type="receiver" data-pk_no="{{ $data['pk_no'] ?? 0 }}">
                    </td>
                    <td>{{ $row->NAME }}</td>
                    <td style="width: 50%">{{ $row->ADDRESS_LINE_1 ?? '' }}
                         {{ isset($row->ADDRESS_LINE_2) ? ','.$row->ADDRESS_LINE_2 : '' }}
                         {{ isset($row->ADDRESS_LINE_3) ? ','.$row->ADDRESS_LINE_3 : '' }}
                         {{ isset($row->ADDRESS_LINE_4) }}
                         {{ isset($row->state->STATE_NAME) ? ','.$row->state->STATE_NAME : '' }}
                         {{ isset($row->city->CITY_NAME) ? ','.$row->city->CITY_NAME : '' }}
                         {{ isset($row->country->NAME) ? ', '.$row->country->NAME : '' }}
                         {{ isset($row->POST_CODE) ? '-'.$row->POST_CODE : '' }}</td>
                    <td>{{ $row->POST_CODE }} ({{ $row->POST_CODE >= 87000 ? 'SS' : 'SM' }})</td>
                    <td>{{ $row->TEL_NO }}</td>
                    <td>{{ $row->addressType->NAME }}</td>
                    <td>
                        <a href="javascript:void(0)" id="edit_address{{ $row->PK_NO ?? 0 }}" class="btn btn-xs btn-info mr-1" data-post_code="{{ $row->POST_CODE ?? '' }}" data-customeraddress="{{ $row->NAME ?? '' }}" data-address_no="{{ $row->PK_NO ?? 0 }}" data-pk_no="{{ $data['pk_no'] ?? 0 }}" data-addresstype="{{ $row->F_ADDRESS_TYPE_NO ?? '' }}" data-mobilenoadd="{{ $row->TEL_NO ?? '' }}" data-ad_1="{{ $row->ADDRESS_LINE_1 ?? '' }}" data-ad_2="{{ $row->ADDRESS_LINE_2 ?? '' }}" data-ad_3="{{ $row->ADDRESS_LINE_3 ?? '' }}" data-ad_4="{{ $row->ADDRESS_LINE_4 ?? '' }}" data-location="{{ $row->LOCATION ?? '' }}" data-country="{{ $row->country->PK_NO ?? '' }}" data-state="{{ $row->STATE ?? '' }}" data-city="{{ $row->CITY ?? '' }}" style="float: left;" title="EDIT"><i class="la la-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="add_new_address_table" style="display: none;">
        {!! Form::open([ 'class' => 'form-horizontal' , 'id' => 'add_new_customer_form' , 'files' => true , 'novalidate']) !!}
        {!! Form::hidden('customer_id_',0,['id'=>'customer_id_modal']) !!}
        {!! Form::hidden('is_modal',1) !!}
        {!! Form::hidden('',$data['pk_no'],['id'=>'single_prd_pk']) !!}
        {!! Form::hidden('address_pk_',0,['id'=>'address_pk_']) !!}
        {!! Form::hidden('is_reseller',0,['id'=>'is_reseller_modal']) !!}

        <div class="row">
            <div class="col-md-12">
                <h3><strong>Address</strong></h3>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <br>
                    <div class="controls">
                        {{Form::hidden('same_as_add',0)}}
                        <label id="same_as_label"><input type="checkbox" name="same_as_add" id="checkbox1">  {{ trans('form.same_as_add') }}</label>
                        {!! $errors->first('same_as_add', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('addresstype') ? 'error' : '' !!}">
                    <label>{{trans('form.address_type')}}</label>
                    <div class="controls">

                    {!! Form::select('addresstype', $address, $editdata->F_ADDRESS_TYPE_NO ?? null, ['class'=>'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'id' => 'addresstype']) !!}
                    {!! $errors->first('addresstype', '<label class="help-block text-danger">:message</label>') !!}
                </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('customeraddress') ? 'error' : '' !!}">
                    <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::text('customeraddress', $editdata->NAME ?? null, ['class'=>'form-control mb-1', 'id' => 'customeraddress', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Name', 'tabindex' => 3, 'required' ]) !!}
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
                    {!! Form::text('mobilenoadd',$editdata->TEL_NO ?? $editdata->MOBILE_NO ?? null,[ 'class' => 'form-control', 'placeholder' => 'Enter Mobile No.', 'id' => 'mobilenoadd']) !!}
                    {!! $errors->first('mobilenoadd', '<label class="help-block text-danger">:message</label>') !!}
                </div>
            </div>

            <?php
            $fetched_country = isset($editdata->F_COUNTRY_NO) ? $editdata->F_COUNTRY_NO : 2;
            ?>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('country') ? 'error' : '' !!}">
                    <label>{{trans('form.country')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        <select name="country" id="country" class="form-control mb-1">
                            @foreach ($data['country'] as $item)
                                <option value="{{ $item->PK_NO }}" data-dial_code="{{ $item->DIAL_CODE }}" {{ $item->PK_NO == $fetched_country ? "selected='selected'" : '' }}>{{ $item->NAME }} ({{ $item->DIAL_CODE }})</option>
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
                        <input type="search" name="post_code" id="post_code_" class="form-control search-input4" placeholder="Post Code" autocomplete="off" value="{{ $editdata->POST_CODE ?? '' }}" required>

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
                    {!! Form::select('city', $data['city'] ?? array(),  $editdata->CITY ?? null, ['class'=>'form-control mb-1',
                    'data-validation-required-message' => 'Select City', 'id' => 'city','tabindex' =>
                    1,  isset($editdata->CITY) ? '' : 'placeholder' =>'Select City' ]) !!}
                        {!! $errors->first('city', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            {{-- STATE 1 --}}
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('state') ? 'error' : '' !!}">
                    <label>{{trans('form.state')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::select('state', $data['state'] ?? array(), $editdata->STATE ?? null, ['class'=>'form-control mb-1',
                        'data-validation-required-message' => 'Select State', isset($editdata->CITY) ? '' : 'placeholder' => 'Select State', 'id' => 'state','tabindex' => 1 ]) !!}
                        {!! $errors->first('state', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_1') ? 'error' : '' !!}">
                    <label>{{trans('form.address_1')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::text('ad_1',  $editdata->ADDRESS_LINE_1 ?? null, ['class'=>'form-control mb-1', 'id' => 'ad_1_',  'placeholder' => 'Enter Address', 'tabindex' => 4  ]) !!}
                        {!! $errors->first('ad_1', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_2') ? 'error' : '' !!}">
                    <label>{{trans('form.address_2')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_2',  $editdata->ADDRESS_LINE_2 ?? null, ['class'=>'form-control mb-1', 'id' => 'ad_2_',  'placeholder' => 'Enter Address', 'tabindex' => 5,  ]) !!}
                        {!! $errors->first('ad_2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_3') ? 'error' : '' !!}">
                    <label>{{trans('form.address_3')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_3',  $editdata->ADDRESS_LINE_3 ?? null, ['class'=>'form-control mb-1', 'id' => 'ad_3_',  'placeholder' => 'Enter Address', 'tabindex' => 6  ]) !!}
                        {!! $errors->first('ad_3', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_4') ? 'error' : '' !!}">
                    <label>{{trans('form.address_4')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_4',  $editdata->ADDRESS_LINE_4 ?? null, ['class'=>'form-control mb-1', 'id' => 'ad_4_',  'placeholder' => 'Enter Address', 'tabindex' => 7,  ]) !!}
                        {!! $errors->first('ad_4', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('location') ? 'error' : '' !!}">
                    <label>{{trans('form.location')}}</label>
                    <div class="controls">
                        {!! Form::text('location',  $editdata->LOCATION ?? null, ['class'=>'form-control mb-1', 'id' => 'location',  'placeholder' => 'Enter Location', 'tabindex' => 11,  ]) !!}
                        {!! $errors->first('location', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <a href="javascript:void(0)" id="address_book" class="ml-1">Open Address Book</a>
        </div>
        <div class="col-md-12 mt-2 mb-2">
            <div class="form-actions text-center">
                <button type="button" onclick="add_address(0, 0)" class="btn bg-primary bg-darken-1 text-white">
                <i class="la la-check-square-o"></i><span id="action_btn"> Add Address </span></button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
<script>
    $('.pickadate').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
        max:"<?php echo date('d-m-Y'); ?>",
    });
</script>
