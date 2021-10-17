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
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                <tr>
                    <td class="text-center">
                        <input type="radio" id="address_no_{{ $row->PK_NO }}" name="change_address" class="flat-red" data-customer_post_code="{{ $row->POST_CODE }}" data-name="{{ $row->NAME }}" data-address_no="{{ $row->PK_NO }}" data-pk_no="{{ $data['pk_no'] }}" {{ $row->PK_NO == $data['address_id'] ? 'checked' : '' }}>
                    </td>
                    <td>{{ $row->NAME }}</td>
                    <td style="width: 50%">{{ $row->ADDRESS_LINE_1 }}, {{ $row->ADDRESS_LINE_2 }}, {{ $row->ADDRESS_LINE_3 }}, {{ $row->ADDRESS_LINE_4 }}, {{ $row->STATE }}, {{ $row->CITY }}, {{ isset($row->country) ? $row->country->NAME : '' }}-{{ $row->POST_CODE }}</td>
                    <td>{{ $row->POST_CODE }} ({{ $row->POST_CODE >= 87000 ? 'SS' : 'SM' }})</td>
                    <td>{{ $row->TEL_NO }}</td>
                    <td>{{ $row->addressType->NAME }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="add_new_address_table" style="display: none;">
        {!! Form::open([ 'class' => 'form-horizontal', 'id' => 'add_new_customer_form' , 'files' => true , 'novalidate']) !!}
        {!! Form::hidden('customer_id',0,['id'=>'customer_id_modal']) !!}
        {!! Form::hidden('is_modal',1) !!}
        {!! Form::hidden('',$data['pk_no'],['id'=>'single_prd_pk']) !!}

        {{-- <div class="row">
            <div class="col-md-6">
                <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                    <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::text('customername', null, ['class'=>'form-control mb-1', 'id' => 'customername',
                        'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Your Name', 'tabindex' => 1 ]) !!}
                        {!! $errors->first('customername', '<label class="help-block text-danger">:message</label>')
                        !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                    <label>{{trans('form.mobile_no')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::text('mobilenoadd', null, [ 'class' => 'form-control mb-1',
                        'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Mobile No', 'tabindex' => 7]) !!}
                        {!! $errors->first('mobilenoadd', '<label class="help-block text-danger">:message</label>')
                        !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                    <label>{{trans('form.address_1')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_1', null, ['class'=>'form-control mb-1', 'id' => 'ad1', 'placeholder' =>
                        'Enter Your Address', 'tabindex' => 2, ]) !!}
                        {!! $errors->first('ad_1', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                    <label>{{trans('form.location')}}</label>
                    <div class="controls">
                        {!! Form::text('location', null, ['class'=>'form-control mb-1', 'id' => 'location',
                        'placeholder' => 'Enter Your Location', 'tabindex' => 8, ]) !!}
                        {!! $errors->first('location', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                    <label>{{trans('form.address_2')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_2', null, ['class'=>'form-control mb-1', 'id' => 'ad3', 'placeholder' =>
                        'Enter Your Address', 'tabindex' => 3, ]) !!}
                        {!! $errors->first('ad_2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {!! $errors->has('country') ? 'error' : '' !!}">
                    <label>{{trans('form.country')}}</label>
                    <div class="controls">
                        {!! Form::select('country', $data['country'], null, ['class'=>'form-control mb-1 select2',
                        'data-validation-required-message' => 'EX- Malaysia', 'id' => 'addressCombo','tabindex' =>
                        9,]) !!}
                        {!! $errors->first('country', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                    <label>{{trans('form.address_3')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_3', null, ['class'=>'form-control mb-1', 'id' => 'ad3', 'placeholder' =>
                        'Enter Your Address', 'tabindex' => 4, ]) !!}
                        {!! $errors->first('ad_3', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                    <label>{{trans('form.state')}}</label>
                    <div class="controls">
                        {!! Form::text('state', null, ['class'=>'form-control mb-1', 'id' => 'state', 'placeholder'
                        => 'State', 'tabindex' => 10, ]) !!}
                        {!! $errors->first('state', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {!! $errors->has('ad_4') ? 'error' : '' !!}">
                    <label>{{trans('form.address_4')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_4', null, ['class'=>'form-control mb-1', 'id' => 'state', 'placeholder'
                        => 'Enter Your Address', 'tabindex' => 5, ]) !!}
                        {!! $errors->first('ad_4', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                    <label>{{trans('form.city')}}</label>
                    <div class="controls">
                        {!! Form::text('city', null, ['class'=>'form-control mb-1', 'id' => 'city', 'placeholder' =>
                        'EX- Kualalampur', 'tabindex' => 11, ]) !!}
                        {!! $errors->first('city', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {!! $errors->has('category') ? 'error' : '' !!}">
                    <label>{{trans('form.post_code')}}</label>
                    <div class="controls">
                        {!! Form::text('post_code', null, ['class'=>'form-control mb-1', 'id' => 'post_c',
                        'placeholder' => 'EX- 2350', 'tabindex' => 6, ]) !!}
                        {!! $errors->first('post_code', '<label class="help-block text-danger">:message</label>')
                        !!}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <a href="javascript:void(0)" id="address_book" class="ml-1">Open Address Book</a>
            </div>
            <div class="col-md-12">
                <div class="form-actions text-center">
                    <button type="button" id="add_new_address_modal" onclick="add_address({{ $data['order_status'] }})" class="btn bg-primary bg-darken-1 text-white">
                        <i class="la la-check-square-o"></i> Save Address </button>
                </div>
            </div>
        </div> --}}
        {{-- {!! Form::close() !!} --}}

            {{-- {!! Form::open([ 'route' => 'admin.customer-address.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!} --}}

        <div class="row mb-3">
            <div class="col-md-12">
                <h3><strong>Address</strong></h3>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <br>
                    <div class="controls">
                        {{Form::hidden('same_as_add',0)}}
                        <label><input type="checkbox" name="same_as_add" id="checkbox1" checked>  {{ trans('form.same_as_add') }}</label>
                        {!! $errors->first('same_as_add', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('addresstype') ? 'error' : '' !!}">
                    <label>{{trans('form.address_type')}}</label>
                    <div class="controls">

                    {!! Form::select('addresstype', $address, null, ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'This field is required', 'id' => 'addressCombo']) !!}
                    {!! $errors->first('addresstype', '<label class="help-block text-danger">:message</label>') !!}
                </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('customeraddress') ? 'error' : '' !!}">
                    <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::text('customeraddress',  null, ['class'=>'form-control mb-1', 'id' => 'customeraddress', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Name', 'tabindex' => 3, 'required' ]) !!}
                        {!! $errors->first('customeraddress', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('country') ? 'error' : '' !!}">
                    <label>{{trans('form.country')}}</label>
                    <div class="controls">
                        {!! Form::select('country', $data['country'], null, ['class'=>'form-control mb-1 select2',
                        'data-validation-required-message' => 'EX- Malaysia', 'placeholder' => 'Select Country', 'id' => 'country','tabindex' =>
                        1, 'data-url' => URL::to('customer_state' )]) !!}
                        {!! $errors->first('country', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>

            {{-- STATE 1 --}}

            <div class="col-md-4">
                <div class="form-group {!! $errors->has('state') ? 'error' : '' !!}">
                    <label>{{trans('form.state')}}</label>
                    <div class="controls">
                    {!! Form::text('state',  null, ['class'=>'form-control mb-1', 'id' => 'state', 'placeholder' => 'Enter State','data-validation-required-message' => 'Select State' ]) !!}
                        {!! $errors->first('state', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('city') ? 'error' : '' !!}">
                    <label>{{trans('form.city')}}</label>
                    <div class="controls">
                    {!! Form::text('city',  null, ['class'=>'form-control mb-1', 'id' => 'city', 'placeholder' => 'Select City','data-validation-required-message' => 'Select City' ]) !!}

                        {!! $errors->first('city', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {!! $errors->has('post_code') ? 'error' : '' !!}">
                    <label>{{trans('form.post_code')}}</label>
                    <div class="controls" id="scrollable-dropdown-menu2">
                        {!! Form::text('post_code',  null, ['class'=>'form-control mb-1', 'id' => 'post_code', 'placeholder' => 'Post Code','data-validation-required-message' => 'This field is required' ]) !!}

                        {!! $errors->first('post_c', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                    {{-- <div class="controls">
                        {!! Form::select('post_code', array(),  null, ['class'=>'form-control mb-1', 'id' => 'post_c',  'placeholder' => 'Select Post Code', 'tabindex' => 8,  ]) !!}
                        {!! $errors->first('post_code', '<label class="help-block text-danger">:message</label>') !!}
                    </div> --}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                    <label>{{trans('form.mobile_no')}}</label>
                    <div class="controls">
                        {!! Form::text('mobilenoadd', null, [ 'class' => 'form-control mb-1','placeholder' => 'Enter Mobile No', 'tabindex' => 2]) !!}
                        {!! $errors->first('mobilenoadd', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_1') ? 'error' : '' !!}">
                    <label>{{trans('form.address_1')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_1',  null, ['class'=>'form-control mb-1', 'id' => 'ad1',  'placeholder' => 'Enter Address', 'tabindex' => 4,  ]) !!}
                        {!! $errors->first('ad_1', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_2') ? 'error' : '' !!}">
                    <label>{{trans('form.address_2')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_2',  null, ['class'=>'form-control mb-1', 'id' => 'ad3',  'placeholder' => 'Enter Address', 'tabindex' => 5,  ]) !!}
                        {!! $errors->first('ad_2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_3') ? 'error' : '' !!}">
                    <label>{{trans('form.address_3')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_3',  null, ['class'=>'form-control mb-1', 'id' => 'ad3',  'placeholder' => 'Enter Address', 'tabindex' => 6,  ]) !!}
                        {!! $errors->first('ad_3', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_4') ? 'error' : '' !!}">
                    <label>{{trans('form.address_4')}}</label>
                    <div class="controls">
                        {!! Form::text('ad_4',  null, ['class'=>'form-control mb-1', 'id' => 'ad3',  'placeholder' => 'Enter Address', 'tabindex' => 7,  ]) !!}
                        {!! $errors->first('ad_4', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>




            <div class="col-md-4">
                <div class="form-group {!! $errors->has('location') ? 'error' : '' !!}">
                    <label>{{trans('form.location')}}</label>
                    <div class="controls">
                        {!! Form::text('location',  null, ['class'=>'form-control mb-1', 'id' => 'location',  'placeholder' => 'Enter Location', 'tabindex' => 11,  ]) !!}
                        {!! $errors->first('location', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
        </div>

        {{-- Billing Address --}}

        <div class="row" id="display_none" style="display: none">
            <div class="col-md-12">
                <h3><strong>Billing Address</strong></h3>
                <br>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('addresstype2') ? 'error' : '' !!}">
                    <label>{{trans('form.address_type')}}</label>
                    <div class="controls">
                    {!! Form::select('addresstype2', $address, 2, ['class'=>'form-control mb-1 select2', 'id' => 'addresstype2']) !!}
                    {!! $errors->first('addresstype2', '<label class="help-block text-danger">:message</label>') !!}
                </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('customeraddress2') ? 'error' : '' !!}">
                    <label>{{trans('form.name')}}<span class="text-danger">*</span></label>
                    <div class="controls">
                        {!! Form::text('customeraddress2',  null, ['class'=>'form-control mb-1', 'id' => 'customeraddress2', 'placeholder' => 'Enter Name', 'tabindex' => 3, 'data-validation-required-message' => 'EX- 123' ]) !!}
                        {!! $errors->first('customeraddress2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('country2') ? 'error' : '' !!}">
                    <label>{{trans('form.country')}}</label>
                    <div class="controls">
                    {!! Form::text('country2',  null, ['class'=>'form-control mb-1', 'id' => 'country2', 'placeholder' => 'Enter Country', 'data-validation-required-message' => 'EX- Malaysia' ]) !!}
                        {!! $errors->first('country2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('state2') ? 'error' : '' !!}">
                    <label>{{trans('form.state')}}</label>
                    <div class="controls">
                    {!! Form::text('state2',  null, ['class'=>'form-control mb-1', 'id' => 'state2', 'placeholder' => 'Enter State', 'data-validation-required-message' => 'EX- Kualalampur', ]) !!}

                        {!! $errors->first('state2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {!! $errors->has('city2') ? 'error' : '' !!}">
                    <label>{{trans('form.city')}}</label>
                    <div class="controls">
                    {!! Form::text('city2',  null, ['class'=>'form-control mb-1', 'id' => 'city2', 'placeholder' => 'Enter City', 'data-validation-required-message' => 'EX- Kualalampur', ]) !!}

                        {!! $errors->first('city', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {!! $errors->has('post_code2') ? 'error' : '' !!}">
                    <label>{{trans('form.post_code')}}</label>
                    <div class="controls">
                    {!! Form::text('post_code2',  null, ['class'=>'form-control mb-1', 'id' => 'post_code2', 'placeholder' => 'Enter Post Code', 'data-validation-required-message' => 'EX- 1234', ]) !!}

                        {!! $errors->first('post_code2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {!! $errors->has('mobilenoadd2') ? 'error' : '' !!}">
                    <label>{{trans('form.mobile_no')}}</label>
                    <div class="controls">
                        {!! Form::text('mobilenoadd2', null, [ 'class' => 'form-control mb-1','placeholder' => 'Enter Mobile No', 'tabindex' => 2]) !!}
                        {!! $errors->first('mobilenoadd2', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_12') ? 'error' : '' !!}">
                    <label>{{trans('form.address_1')}}</label>
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
        <div class="col-md-12">
            <a href="javascript:void(0)" id="address_book" class="ml-1">Open Address Book</a>
        </div>
        <div class="col-md-12 mt-2 mb-2">
            <div class="form-actions text-center">
                <button type="button" onclick="add_address(0)" class="btn bg-primary bg-darken-1 text-white">
                <i class="la la-check-square-o"></i> Add Address </button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
<script>
    $('#checkbox1').change(function() {
        if(this.checked) {
            $('#display_none').fadeOut();
        }else{
            $('#display_none').fadeIn();
        }
    });
</script>
