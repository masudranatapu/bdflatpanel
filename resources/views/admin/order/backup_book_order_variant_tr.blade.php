
<table class="table mb-0">
    <thead>
        <tr>
            <th colspan="4" style="background: aliceblue;">Customer Information (Customer No: {{ $data->getCustomer->CUSTOMER_NO ?? $data->getReseller->RESELLER_NO ?? '' }})</th>
        </tr>
        <tr>
            <th><small><strong>Name</strong></small></th>
            <th><small><strong>Phone</strong></small></th>
            <th><small><strong>Email</strong></small></th>
            <th><small><strong>Post Code</strong></small></th>
        </tr>
    </thead>
    <tbody id="append_cus">
    <tr>
        <td><small id="book_customer">{{ $data->getCustomer->NAME ?? $data->getReseller->NAME ?? '' }}</small></td>
        <td><small id="mobile_no_">{{ $data->getCustomer->MOBILE_NO ?? $data->getReseller->MOBILE_NO ?? '' }}</small></td>
        <td><small>{{ $data->getCustomer->EMAIL ?? $data->getReseller->EMAIL ?? '' }}</small></td>
        <td><small id="postage_cost_main_customer">{{ $customer_postcode->POST_CODE ?? '' }}</small></td>
    </tr>
    @if (isset($data->getCustomer->PK_NO))
    <?php
    $address2 = $data->getCustomerAddress($customer_id,2);
    ?>
    @if (!empty($address2[0]))
    <tr>
        <td colspan="4"><small>Billing :
            {{ is_null($address2[0]->ADDRESS_LINE_1) ? '' : $address2[0]->ADDRESS_LINE_1 }}
            {{ is_null($address2[0]->ADDRESS_LINE_2) ? '' : ','.$address2[0]->ADDRESS_LINE_2 }}
            {{ is_null($address2[0]->ADDRESS_LINE_3) ? '' : ','.$address2[0]->ADDRESS_LINE_3 }}
            {{ is_null($address2[0]->ADDRESS_LINE_4) ? '' : ','.$address2[0]->ADDRESS_LINE_4 }}
            {{ is_null($address2[0]->STATE) ? '' : ','.$address2[0]->STATE }}
            {{ is_null($address2[0]->CITY) ? '' : ','.$address2[0]->CITY }}
            {{ is_null($address2[0]->POST_CODE) ? '' : ','.$address2[0]->POST_CODE }}
            {{ is_null($address2[0]->COUNTRY) ? '' : ','.$address2[0]->COUNTRY }}
        </small>
        <a href="javascript:void(0)" id="edit_address{{ $address2[0]->PK_NO ?? '' }}" class="btn btn-xs btn-info mr-1" data-toggle="modal" data-target="#UpdateCustomerAddress" data-post_code="{{ $address2[0]->POST_CODE ?? '' }}" data-customeraddress="{{ $address2[0]->NAME ?? '' }}" data-address_no="{{ $address2[0]->PK_NO ?? '' }}" data-pk_no="{{ $data['pk_no'] ?? '' }}" data-addresstype="{{ $address2[0]->F_ADDRESS_TYPE_NO ?? '' }}" data-mobilenoadd="{{ $address2[0]->TEL_NO ?? '' }}" data-ad_1="{{ $address2[0]->ADDRESS_LINE_1 ?? '' }}" data-ad_2="{{ $address2[0]->ADDRESS_LINE_2 ?? '' }}" data-ad_3="{{ $address2[0]->ADDRESS_LINE_3 ?? '' }}" data-ad_4="{{ $address2[0]->ADDRESS_LINE_4 ?? '' }}" data-location="{{ $address2[0]->LOCATION ?? '' }}" data-country="{{ $address2[0]->country->PK_NO ?? '' }}" data-state="{{ $address2[0]->STATE ?? '' }}" data-city="{{ $address2[0]->CITY ?? '' }}" style="float: right;" title="EDIT"><i class="la la-edit"></i>
        </a>
    </td>
    </tr>
    @endif
    <?php
    $address1 = $data->getCustomerAddress($customer_id,1);
    ?>
    @if (!empty($address1))
    @foreach ($address1 as $item1)
    <tr>
        <td colspan="4">
            <small>
            <strong style="color: #666ee8">{{ is_null($item1->PK_NO) ? '' : $item1->PK_NO.' >>' }}</strong>
            Dellivery :
            {{ is_null($item1->ADDRESS_LINE_1) ? '' : $item1->ADDRESS_LINE_1 }}
            {{ is_null($item1->ADDRESS_LINE_2) ? '' : ','.$item1->ADDRESS_LINE_2 }}
            {{ is_null($item1->ADDRESS_LINE_3) ? '' : ','.$item1->ADDRESS_LINE_3 }}
            {{ is_null($item1->ADDRESS_LINE_4) ? '' : ','.$item1->ADDRESS_LINE_4 }}
            {{ is_null($item1->STATE) ? '' : ','.$item1->STATE }}
            {{ is_null($item1->CITY) ? '' : ','.$item1->CITY }}
            {{ is_null($item1->POST_CODE) ? '' : ','.$item1->POST_CODE }}
            {{ is_null($item1->COUNTRY) ? '' : ','.$item1->COUNTRY }}
            </small>
        </td>
    </tr>
    @endforeach
    @endif
    @else
    @if (!empty($data->getResellerAddress($customer_id,2)))
    <?php
    $address3 = $data->getResellerAddress($customer_id);
    ?>
    <tr>
        <td colspan="4"><small>Billing :
            {{ is_null($address3->ADDRESS_LINE_1) ? '' : $address3->ADDRESS_LINE_1 }}
            {{ is_null($address3->ADDRESS_LINE_2) ? '' : ','.$address3->ADDRESS_LINE_2 }}
            {{ is_null($address3->ADDRESS_LINE_3) ? '' : ','.$address3->ADDRESS_LINE_3 }}
            {{ is_null($address3->ADDRESS_LINE_4) ? '' : ','.$address3->ADDRESS_LINE_4 }}
            {{ is_null($address3->STATE) ? '' : ','.$address3->STATE }}
            {{ is_null($address3->CITY) ? '' : ','.$address3->CITY }}
            {{ is_null($address3->POST_CODE) ? '' : ','.$address3->POST_CODE }}
            {{ is_null($address3->COUNTRY) ? '' : ','.$address3->COUNTRY }}
        </small></td>
    </tr>
    @endif
    @endif
    </tbody>
</table><?php
use Carbon\Carbon;
// echo '<pre>';
// echo '======================<br>';
// print_r($item);
// echo '<br>======================<br>';
// exit();
$preferred_method = $item['info'][0]->CUSTOMER_PREFFERED_SHIPPING_METHOD ?? $item['info'][0]->FINAL_PREFFERED_SHIPPING_METHOD;
?>
<?php
use Carbon\Carbon;
// echo '<pre>';
// echo '======================<br>';
// print_r($item);
// echo '<br>======================<br>';
// exit();
$preferred_method = $item['info'][0]->CUSTOMER_PREFFERED_SHIPPING_METHOD ?? $item['info'][0]->FINAL_PREFFERED_SHIPPING_METHOD;
?>
<tr>
    <td colspan="11" style="text-align: center;">
        <?php
        // $address1 = \App\Models\Booking::getCustomerAddressOne($item['delivery_address'],1);
            // echo '<pre>';
            // echo '======================<br>';
            // print_r($address1);
            // echo '<br>======================<br>';
        // exit();
        ?>
        {!! is_null($address1->TEL_NO) ? '' : $address1->TEL_NO.'<br>' !!}
        {!! is_null($address1->ADDRESS_LINE_1) ? '' : $address1->ADDRESS_LINE_1.'<br>' !!}
        {!! is_null($address1->ADDRESS_LINE_2) ? '' : $address1->ADDRESS_LINE_2.'<br>' !!}
        {!! is_null($address1->ADDRESS_LINE_3) ? '' : $address1->ADDRESS_LINE_3.'<br>' !!}
        {!! is_null($address1->ADDRESS_LINE_4) ? '' : $address1->ADDRESS_LINE_4.'<br>' !!}
        {!! is_null($address1->STATE) ? '' : $address1->STATE.'<br>' !!}
        {!! is_null($address1->CITY) ? '' : $address1->CITY.'<br>' !!}
        {!! is_null($address1->POST_CODE) ? '' : $address1->POST_CODE.'<br>' !!}
        {!! is_null($address1->COUNTRY) ? '' : $address1->COUNTRY.'<br>' !!}
        {{ is_null($address1->TEL_NO) ? '' : $address1->TEL_NO }}
    </td>
</tr>
<tr>
    <td style="width: 10%;"><img style="width: 150px !important; height: 150px;" src="{{ asset($item['info'][0]->PRIMARY_IMG_RELATIVE_PATH ?? '' ) }}" alt="PICTURE"></td>
    <td style="width: 20%">
    {{ $item['info'][0]->PRD_VARINAT_NAME ?? '' }}
    <br>
    <p>IG CODE : <span id="prd_ig_code">{{$item['info'][0]->IG_CODE ?? ''}}</span></p>
    <p>Regular: <span id="ss_price" class="danger" >{{ number_format($item['info'][0]->REGULAR_PRICE,2, '.', '') }}</span> RM</p>

    <p>Installment:<span id="sm_price" class="danger"> {{number_format($item['info'][0]->INSTALLMENT_PRICE,2, '.', '') }}</span> RM</p>

    {{-- {!! Form::hidden("ss_postage", number_format($item['info'][0]->SS_COST,2, '.', ''), ['id'=>'ss_postage_cost']) !!}
    {!! Form::hidden("sm_postage", number_format($item['info'][0]->SM_COST,2, '.', ''), ['id'=>'sm_postage_cost']) !!} --}}
    {!! Form::hidden("price-".$item['info'][0]->IG_CODE."", 0, ['id'=>'price']) !!}
    {!! Form::hidden("product_freight_type-".$item['info'][0]->IG_CODE."", $preferred_method , ['id'=>'product_freight_type-'.$item['info'][0]->IG_CODE.'']) !!}

    {{-- <label>{!! Form::radio('price_type-'.$item['info'][0]->IG_CODE.'', 'regular', isset($item['price_type']) && $item['price_type'] == 0 ? false : true,['id'=>'price_type','onclick'=>'javascript: return false;','disabled']) !!} Regular</label> &nbsp;&nbsp;
    <label>{!! Form::radio('price_type-'.$item['info'][0]->IG_CODE.'', 'installment', isset($item['price_type']) && $item['price_type'] == 0 ? true : false,['id'=>'price_type','onclick'=>'javascript: return false;','disabled']) !!} Installment</label> --}}

    @if ($item['info'][0]->F_INV_WAREHOUSE_NO == 1 && $item['info'][0]->SHIPMENT_TYPE == null && $item['info'][0]->F_BOX_NO == null )
    {{-- <div>
        <label>{!! Form::radio('is_air'.$item['info'][0]->SKUID.'', number_format($item['info'][0]->CURRENT_AIR_FREIGHT,2, '.', ''),$preferred_method == 'AIR' ? true : false,['id'=>'customer_preferred1','onclick'=>'javascript: return false;','disabled']) !!} Air-{{ number_format($item['info'][0]->CURRENT_AIR_FREIGHT,2, '.', '') }}</label> &nbsp;&nbsp;
        <label>{!! Form::radio('is_air'.$item['info'][0]->SKUID.'', number_format($item['info'][0]->CURRENT_SEA_FREIGHT,2, '.', ''),$preferred_method == 'SEA' ? true : false,['id'=>'customer_preferred2','onclick'=>'javascript: return false;','disabled']) !!} Sea-{{ number_format($item['info'][0]->CURRENT_SEA_FREIGHT,2, '.', '') }}</label>
    </div> --}}
    <p>Air- <span class="danger"> {{number_format($item['info'][0]->CURRENT_AIR_FREIGHT,2, '.', '') }}</span> Sea- <span class="danger"> {{number_format($item['info'][0]->CURRENT_SEA_FREIGHT,2, '.', '') }}</span></p>
    @else
    <p style="opacity: .7">Air- <span class="danger"> {{number_format($item['info'][0]->CURRENT_AIR_FREIGHT,2, '.', '') }}</span> Sea- <span class="danger"> {{number_format($item['info'][0]->CURRENT_SEA_FREIGHT,2, '.', '') }}</span></p>
    @endif
    <p>SM- <span class="danger"> {{number_format($item['info'][0]->CURRENT_SM_COST,2, '.', '') }}</span> SS- <span class="danger"> {{number_format($item['info'][0]->CURRENT_SS_COST,2, '.', '') }}</span></p>
</td>
    <th style="width: 10%">
        <table id="warehouse">
            @if(!empty($item['info']))
            @foreach ($item['info'] as $data)
            @if ($data->F_INV_WAREHOUSE_NO == 1)
            <tr id="warehouse_delete{{ $data->PK_NO }}">
                <th title="{{ isset($data->BOX_TYPE) ? "BOX TYPE-($data->BOX_TYPE)" : '' }} {{ isset($data->SHIPMENT_TYPE) ? "SHIPMENT TYPE-($data->SHIPMENT_TYPE)" : '' }}" style="{{ $data->ORDER_STATUS >= 80 ? 'opacity:.5' : '' }}">{{ $data->INV_WAREHOUSE_NAME }}<strong> ({{ Carbon::now() < $data->SCH_ARRIVAL_DATE ? Carbon::parse(Carbon::now())->diffInDays($data->SCH_ARRIVAL_DATE) : '90' }} Days)</strong>
                    @if (isset($data->BOX_TYPE) && $data->BOX_TYPE == 'AIR')
                    <span> <i class='ft-box' style="color: red"></i></span>
                    @endif
                    @if (isset($data->BOX_TYPE) && $data->BOX_TYPE == 'SEA')
                    <span> <i class='ft-box' style="color: blue"></i></span>
                    @endif
                    @if (isset($data->SHIPMENT_TYPE) && $data->SHIPMENT_TYPE == 'SEA')
                    <span> <i class='la la-ship' style="color: blue"></i></span>
                    @endif
                    @if (isset($data->SHIPMENT_TYPE) && $data->SHIPMENT_TYPE == 'AIR')
                    <span> <i class='icon-plane' style="color: red"></i></span>
                    @endif
                </th>
            </tr>
            @elseif ($data->F_INV_WAREHOUSE_NO > 1)
            <tr id="warehouse_delete{{ $data->PK_NO }}">
                <th style="{{ $data->ORDER_STATUS >= 80 ? 'opacity:.5' : '' }}">{{ $data->INV_WAREHOUSE_NAME }}<strong> (Ready Stock)</strong>
                </th>
            </tr>
            @endif
            @endforeach
            @endif
        </table>
    </th>
    <th id="th_book_qty" style="width: 5%">
        <table id="book_qty"  style="width: 100%;">
            @if(!empty($item['info']))
            @foreach ($item['info'] as $data)
            @if ($data->F_INV_WAREHOUSE_NO == 1)
            <tr id="book_qty_delete{{ $data->PK_NO }}">
                {{-- @if (isset($data->SHIPMENT_TYPE)) --}}
                <th><input class="form-control input-sm max_val_check" id="booking_qty" type="number" min="1" max="1" value="1" data-type="house-{{ $data->F_INV_WAREHOUSE_NO }}-ship-{{ $data->SHIPMENT_TYPE ?? 0 }}-box-{{ $data->BOX_TYPE ?? 0 }}" readonly>
                </th>
                {{-- @endif --}}
            </tr>
            @elseif ($data->F_INV_WAREHOUSE_NO > 1)
            <tr id="book_qty_delete{{ $data->PK_NO }}">
                <th><input class="form-control input-sm max_val_check" id="booking_qty" type="number" min="1" max="1" value="1" readonly>
                </th>
            </tr>
            @endif
            @endforeach
            @endif
        </table>
    </th>
    <th id="postage_costs_th" style="width: 3%">
        @foreach ($item['info'] as $data)
        <div id="postage_delete{{ $data->PK_NO }}">
            <div style="margin-bottom: 5px" id="postage_costs">
                {!! Form::hidden('customer_address[]', $data->f_customer_address , ['id'=>'customer_address']) !!}
                {!! Form::hidden('', $data->IS_SELF_PICKUP , ['id'=>'is_self_pickup']) !!}
                {!! Form::hidden('is_sm[]', $data->CURRENT_IS_SM , ['id'=>'is_sm']) !!}
                {!! Form::hidden('', $data->customer_post_code, ['id' => 'customer_postage']) !!}
                {!! Form::hidden('', number_format($data->CURRENT_SM_COST,2, '.', ''), ['id' => 'single_sm_cost']) !!}
                {!! Form::hidden('', number_format($data->CURRENT_SS_COST,2, '.', ''), ['id' => 'single_ss_cost']) !!}
                <span id="single_postage" style="font-weight: normal;font-size: 12px; {{ $data->ORDER_STATUS >= 80 ? 'opacity:.5' : '' }}" ></span>
                <input name="postage_costs[]" id="single_postage_value" type="number" style="font-weight: normal;font-size: 12px;display: inline-block;text-align:right;" class="form-control input-sm" {{ $data->ORDER_STATUS >= 80 ? 'readonly' : '' }}>
            </div>
        </div>
        @endforeach
    </th>
    <th id="freight_costs_th" style="width: 7%">
        @foreach ($item['info'] as $data)
        <div id="freight_delete{{ $data->PK_NO }}" style="width: 70%">
            <div style="margin-bottom: 5px;text-align: center" id="freight_costs">
                {!! Form::hidden('is_freight[]', $data->CURRENT_IS_FREIGHT, ['id' => 'is_freight']) !!}
                {!! Form::hidden('', $data->CURRENT_AIR_FREIGHT, ['id' => 'single_air_cost']) !!}
                {!! Form::hidden('', $data->CURRENT_SEA_FREIGHT, ['id' => 'single_sea_cost']) !!}
                {{-- <span id="per_product_freight_value" style="font-weight: normal;font-size: 12px;" class="ml-1"></span> --}}
                <input name="freight_costs[]" style="text-align:right;" type="number" class="form-control input-sm ml-2" id="per_product_freight_value" {{ $data->ORDER_STATUS >= 80 ? 'readonly' : '' }}>
            </div>
        </div>
        @endforeach
    </th>
    <th id="per_product_costs_th" style="width: 1%;">
        @foreach ($item['info'] as $data)
        <div id="per_product_costs_delete{{ $data->PK_NO }}" style="width: 100%;">
            <div style="margin-bottom: 5px" id="per_product_costs">
                {{-- <span id="per_product" style="font-weight: normal;font-size: 12px;"></span> --}}
                {!! Form::hidden('is_regular[]', $data->CURRENT_IS_REGULAR, ['id' => 'is_regular']) !!}
                {!! Form::hidden('', $data->CURRENT_REGULAR_PRICE, ['id' => 'regular_price']) !!}
                {!! Form::hidden('', $data->CURRENT_INSTALLMENT_PRICE, ['id' => 'installment_price']) !!}

                <input name="unit_costs[]" id="per_product_value" type="number" style="font-weight: normal;font-size: 12px;text-align:right;" class="form-control input-sm" {{ $data->ORDER_STATUS >= 80 ? 'readonly' : '' }}>
            </div>
        </div>
        @endforeach
    </th>
    <th id="line_subtotal_costs_th" style="width: 5%;">
        @foreach ($item['info'] as $data)
        <div id="line_subtotal_delete{{ $data->PK_NO }}" style="width: 100%;">
            <div style="margin-bottom: 5px;display: inline-block" id="line_subtotal_costs">
                <span id="per_product" style="font-weight: normal;font-size: 12px;"></span>
                {!! Form::hidden('', $data->ORDER_STATUS ?? 0, ['id' => 'order_status']) !!}
                {!! Form::hidden('', $data->PK_NO, ['id'=>'product_inv_pk']) !!}
                {!! Form::hidden('', 0, ['id'=>'line_subtotal_value_hidden']) !!}
                <input name="line_total_costs[]" id="line_subtotal_value" type="number" style="font-weight: normal;font-size: 12px;text-align:right;" class="form-control input-sm" readonly>
            </div>

        </div>
        @endforeach
    </th>
    <th id="view_mode_add_pk" style="width: 1%;display: none">
        <div style="display: none;display: inline-block">
            <a href="javascript:void(0)"  class="btn btn-xs btn-primary" style="float: left;font-size: 12px" title="{{ $data->customer_name ?? 'NO ADDRESS ASSIGNED' }}" readonly>{{ $data->f_customer_address }}</a>
        </div>
    </th>
    <th id="checkbox_th" style="width: 1%;">
        @foreach ($item['info'] as $data)
        <div id="checkbox_delete{{ $data->PK_NO }}" style="width: 5%">
            <div style="margin-bottom: 11.5px" id="checkbox_div" class="skin skin-square">
                <fieldset>
                    {!! Form::hidden('', $data->ORDER_STATUS ?? 0, ['id' => 'order_status']) !!}
                    {!! Form::hidden('', 1, ['id' => 'if_change_value'.$data->PK_NO]) !!}
                    {!! Form::hidden('', 0, ['id' => 'per_item_checkbox_price'.$data->PK_NO]) !!}
                    {!! Form::hidden('', 0, ['id' => 'checked_item_price'.$data->PK_NO]) !!}
                    {!! Form::hidden('', $data->PK_NO, ['id' => 'product_pk']) !!}
                    {{-- {{Form::hidden('checkbox_value[]','off')}} --}}
                    <input name="checkbox_value_{{ $data->PK_NO }}" type="checkbox" data-pk_no="{{ $data->PK_NO }}" id="checkbox_of_order" {{ $data->ORDER_STATUS >= 80 ? 'disabled' : '' }}>
                </fieldset>
            </div>
        </div>
        @endforeach
    </th>
    <th id="selfpickup_th" style="width: 1%;">
        @foreach ($item['info'] as $data)
        <div id="selfpickup_delete{{ $data->PK_NO }}" style="width: 5%">
            <div style="margin-bottom: 11.5px" id="selfpickup_div" class="skin skin-flat">
                <fieldset>
                    {{-- {{Form::hidden('checkbox_value[]','off')}} --}}
                    <input name="selfpickup_value_{{ $data->PK_NO }}" type="checkbox" data-pk_no="{{ $data->PK_NO }}" id="selfpickup_of_order" {{ $data->ORDER_STATUS >= 80 ? 'disabled' : '' }}{{ $data->IS_SELF_PICKUP == 1 ? 'checked' : '' }}>
                </fieldset>
            </div>
        </div>
        @endforeach
    </th>
    {{-- <th class="text-center">
        <input id="amount_ss" class="form-control" type="number" style="width: 100px;" value="00.00" readonly >
        <span id="freight_cost_section" style="font-weight: normal;font-size: 12px;">Freight : <span id="freight_cost"></span></span><br>
        <span id="postage_cost_section" style="font-weight: normal;font-size: 12px;">Postage : <span id="postage_cost_"></span></span><br>
    </th> --}}
    <th class="text-center" style="width: 5%" id="action_col">
        @foreach ($item['info'] as $data)
        <div style="display: flex;margin-bottom: 11px;width: 50%" id="action_column{{ $data->PK_NO}}">
            {!! Form::hidden('INV_PK_NO[]', $data->F_INV_STOCK_NO ) !!}
            @if ($data->ORDER_STATUS <= 60 || $data->ORDER_STATUS == null)
            <a href="javascript:void(0)" id="delete_single_prd{{ $data->PK_NO }}" class="btn btn-xs btn-danger mr-1" data-delete_id="{{ $data->PK_NO }}" style="float: left;{{ $data->ORDER_STATUS == 60 ? 'display:none' : 'display:block' }}" title="DELETE"><i class="la la-trash"></i></a>
            @endif
            <div id="address_btn">
                <?php
                // $customer_name = $data->customer_name;
                // $customer_name = isset($customer_name) ? (\Str::limit($data->customer_name, 5, $end='...')) : 'No Address';
                ?>
                <a href="javascript:void(0)" id="update_btn{{ $data->PK_NO }}" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#UpdateCustomerAddress" data-customer_address_id="{{$data->f_customer_address}}" data-pk_no="{{ $data->PK_NO }}" data-order_status="{{ $data->ORDER_STATUS }}" style="float: left;font-size: 12px" title="{{ $data->customer_name ?? 'NO ADDRESS ASSIGNED' }}">{{ $data->f_customer_address }}</a>
            </div>
        </div>
        @endforeach
    </th>
</tr>



<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    jQuery(document).ready(function($) {
        $('[id*=single_postage_value],[id*=per_product_freight_value],[id*=per_product_value],[id*=line_subtotal_value],[id*=discount_amount]').on('wheel', function(e){
            return false;
        });
        // count_total_final();
        count_qty_final();
        put_values();
        count_amount();
        grand_total();
        put_checkbox_values();
        ///// LOOP THROUGH EACH ROW /////
        $('#append_tr > tr').each(function (i, row) {
            var rows = $(row);
            rows.find('#line_subtotal_costs_th #line_subtotal_costs').each(function (i, row9) {
                var rows9 = $(row9);
                var line_cost = rows9.find('#line_subtotal_value_hidden').val();
                line_cost = parseFloat(line_cost);
                line_cost = line_cost.toFixed(2);
                rows9.find('#line_subtotal_value').val(line_cost);
            });
        });
        $(document).on('keyup','[id*=single_postage_value],[id*=per_product_freight_value],[id*=per_product_value]', function(){
            count_amount();
            grand_total();
            update_checkbox_and_balance();
        });
        $('[id*=line_subtotal_value]').change(function() {
            count_line_total();
            update_checkbox_and_balance();
        });
        $(document).on('keyup','[id*=line_subtotal_value]', function(){
            count_line_total();
            update_checkbox_and_balance();
        });
        $(document).on('keyup','[id*=single_postage_value],[id*=per_product_freight_value],[id*=per_product_value]', function(){
            count_amount();
            grand_total();
            update_checkbox_and_balance();
        });
        /*CHANGE DISCOUNT AMOUNT*/
        $("#discount_amount").keyup(function(){
            grand_total();
        });
        $("#discount_amount").change(function(){
            grand_total();
        });
        ////////// CHECKBOX CHANGED /////////
        $('[id*=checkbox_of_order]').on('ifChanged', function() {
            var order_outstanding = $('#order_outstanding_hidden').val();
                order_outstanding = parseFloat(order_outstanding);
            var order_balance_used = $('#balance_used').val();
                order_balance_used = parseFloat(order_balance_used);
            var pk_no = $(this).data('pk_no');
            var order_price = $('[id*=per_item_checkbox_price'+pk_no+']').val();
            order_price = parseFloat(order_price);
            if($(this).is(":checked")) {
                order_outstanding = order_outstanding - order_price;
                order_outstanding = parseFloat(order_outstanding);
                $('#order_outstanding').text(order_outstanding);
                $('#order_outstanding_hidden').val(order_outstanding);

                order_balance_used = order_balance_used + order_price;
                order_balance_used = parseFloat(order_balance_used);
                $('#order_balance_used').text(order_balance_used);
                $('#balance_used').val(order_balance_used);
                $('[id*=checked_item_price'+pk_no+']').val(order_price);

                $('[id=delete_single_prd'+pk_no+']').css('display','none');
            }else{
                if ($('[id=if_change_value'+pk_no+']').val() == 1) {
                    order_outstanding = order_outstanding + order_price;
                    order_outstanding = parseFloat(order_outstanding);
                    $('#order_outstanding').text(order_outstanding);
                    $('#order_outstanding_hidden').val(order_outstanding);

                    order_balance_used = order_balance_used - order_price;
                    order_balance_used = parseFloat(order_balance_used);
                    $('#order_balance_used').text(order_balance_used);
                    $('#balance_used').val(order_balance_used);
                $('[id=delete_single_prd'+pk_no+']').css('display','block');

                }
                $('[id=if_change_value'+pk_no+']').val(1);
            }
            // console.log(order_balance_used);
            var get_url = $('#base_url').val();
            var pageurl = get_url+'/update_order_payment';
            payment_checkbox_action();
        });
        var customer_id = $('#customer_id').val();
        checkIfAddress(customer_id);
    });
    function checkIfAddress(customer_id) {
        var get_url = $('#base_url').val();
        var is_reseller     = $('#is_reseller').val();
        var page_is_view    = $('#page_is_view').val();
        if (is_reseller == 0 && page_is_view == 1) {
            var customer_id = $('#customer_id').val();
            $.ajax({
                type:'get',
                url:get_url+'/checkifCustomerAddressexists/'+customer_id+'/customer',
                async :true,
                dataType: 'json',
                beforeSend: function () {
                    $("body").css("cursor", "progress");
                },
                success: function (data) {
                    // console.log(data);
                    if (data['response'] == 1) {
                        $('#cus_add_modal').html('').append(data.html);
                        destroycreatetypeahead(2);
                        destroycreatetypeahead2(2);
                        $('#customeraddress').val($('#book_customer').text());
                        $('#customeraddress2').val($('#book_customer').text());
                        $('#mobilenoadd').val($('#mobile_no_').text());
                        $('#mobilenoadd2').val($('#mobile_no_').text());
                        if (data['billing'] === null && data['delivery'] === null) {
                            $('#checkbox1').prop('checked', true);
                            $('#same_as_label').css('display','block');
                            $('#booking_create').val(1);
                        }else if(data['billing'] === null || data['delivery'] === null){
                            if (data['delivery'] === null) {
                                $('#addresstype option[value=1]').attr('selected','selected');
                            }else if(data['billing'] === null) {
                                $('#addresstype option[value=2]').attr('selected','selected');
                            }
                            $('#addresstype').css("pointer-events", 'none');
                            $('#checkbox1').prop('checked', false);
                            $('#same_as_label').css('display','none');
                            $('#booking_create').val(0);
                        }
                        $('#UpdateCustomerAddress').modal('show');
                        $('#add_new_btn').css('display','none');
                        $('#customer_id_modal_').val(customer_id);
                        return false;
                    }
                    // else{
                    //     var url = $('#post_form').attr('action');
                    //     $('#post_form').attr('action', url+"/order").submit();
                    // }
                },
                complete: function (data) {
                    $("body").css("cursor", "default");
                }
            });
        }
    }
    function add_address_bookorder() {
        var get_url = $('#base_url').val();
        var order_date_ = $('input[name=order_date]').val();
        $('input[name=order_date_]').val(order_date_);
        $.ajax({
            type:'POST',
            url:get_url+'/postCustomerAddress',
            data: $('#add_new_customer_form').serialize(),
            beforeSend: function () {
                $("body").css("cursor", "progress");
            },
            success: function (data) {
                if (data['status'] == 1) {
                    $('#UpdateCustomerAddress').modal('hide');
                    $('[id*=customer_address]').val(data['final_address']);
                    $('#booktoorderform').submit();
                }else{
                    alert('Please Try Again !');
                }
            },
            complete: function (data) {
                $("body").css("cursor", "default");
            }
        });
    }
    function count_line_total() {
        var sub_total_only_line = 0;
        var subtotal_with_exta_costs = 0;
        ///// LOOP THROUGH EACH ROW /////
        $('#append_tr > tr').each(function (i, row) {
            var rows = $(row);
            ///// LOOP THROUGH EACH LINE SUB-TOTAL /////
            rows.find('#line_subtotal_costs_th #line_subtotal_costs').each(function (i, row2) {
            var rows2 = $(row2);
            var product_inv_pk = rows2.find('#product_inv_pk').val();

            var line_total_cost = rows2.find('[id=line_subtotal_value]').val();
            sub_total_only_line += line_total_cost;
            line_total_cost = parseFloat(line_total_cost);
            line_total_cost = line_total_cost.toFixed(2);
            $('#per_item_checkbox_price'+product_inv_pk).val(line_total_cost);
            });
        });
        $('#append_tfoot tr').find('#total_with_extra_costs').html(sub_total_only_line.toFixed(2));
        grand_total();
    }
    function payment_checkbox_action() {
        var order_outstanding = $('#order_outstanding_hidden').val();
        order_outstanding = parseFloat(order_outstanding);
        $('#append_tr > tr').each(function (i, row) {
            var rows = $(row);
            rows.find('#checkbox_th #checkbox_div').each(function (i, row2) {
                var rows2 = $(row2);
                var item_price = rows2.find('[id*=per_item_checkbox_price]').val();
                item_price = parseFloat(item_price);
                var order_status = rows2.find('[id*=order_status]').val();

                if (order_status < 80) {
                    if(rows2.find('input[id="checkbox_of_order"]').is(":checked")) {
                        rows2.find('#checkbox_of_order').prop("disabled", false);
                    }else{
                        if (item_price > order_outstanding) {
                            rows2.find('#checkbox_of_order').prop("disabled", true);
                        }else{
                            rows2.find('#checkbox_of_order').prop("disabled", false);
                        }
                    }
                }
            });
        });
    }
    function count_qty_final() {
        var booking_qty = 0;
        $('#append_tr tr').each(function (i, row) {
            var rows = $(row);
            rows.find('#th_book_qty tr').each(function (i, row2) {
                var rows2 = $(row2);
                var qty = rows2.find('#booking_qty').val();
                if (qty > 0) {
                    booking_qty += parseInt(qty);
                }
            });
        });
        $('#append_tfoot tr').find('#final_qty').html(booking_qty);
        $('#append_tfoot tr').find('#grand_final_qty').html(booking_qty);
        $('#append_tfoot tr').find('#final_qty_').val(booking_qty);
    }
    function update_checkbox_and_balance() {
        var order_outstanding = $('#order_outstanding_hidden').val();
            order_outstanding = parseFloat(order_outstanding);
        var order_balance_used = $('#balance_used').val();
            order_balance_used = parseFloat(order_balance_used);
        ///// LOOP THROUGH EACH ROW /////
        $('#append_tr > tr').each(function (i, row) {
            var rows = $(row);
            rows.find('#checkbox_th #checkbox_div').each(function (j, row5) {
                var rows5 = $(row5);
                var product_inv_pk = rows5.find('#product_pk').val();
                var prev_value = rows5.find('[id=checked_item_price'+product_inv_pk+']').val();
                prev_value = parseFloat(prev_value);
                var new_value = rows5.find('[id=per_item_checkbox_price'+product_inv_pk+']').val();
                new_value = parseFloat(new_value);
                if (rows5.find('[id=checkbox_of_order]').is(":checked")) {

                    if (new_value != prev_value) {

                        order_balance_used = order_balance_used - prev_value;
                        order_outstanding = order_outstanding + prev_value;
                        rows5.find('[id=if_change_value]').val(0);
                        rows5.find('#checkbox_of_order').iCheck('uncheck');
                        $('#order_outstanding').text(order_outstanding);
                        $('#order_balance_used').text(order_balance_used);
                        $('#balance_used').val(order_balance_used);
                        $('#order_outstanding_hidden').val(order_outstanding);
                    }
                }
            });
        });
    }
    /////// ONLOAD PUT INPUT FIELD VALUES ///////
    function put_values() {
        ///// LOOP THROUGH EACH ROW /////
        $('#append_tr > tr').each(function (i, row) {
            var rows = $(row);
            ///// LOOP THROUGH EACH POSTAGE COSTS /////
            rows.find('#postage_costs_th #postage_costs').each(function (i, row2) {
                var rows2           = $(row2);
                var post_code       = rows2.find('#customer_postage').val();
                var is_self_pickup  = rows2.find('#is_self_pickup').val();
                if (is_self_pickup == 1 ) {

                    var single_ss_cost = rows2.find('#single_ss_cost').val();
                    rows2.find('#single_postage').html('');
                    rows2.find('#single_postage_value').val(0.00);
                }else if (post_code >= 87000 ) {

                    var single_ss_cost = rows2.find('#single_ss_cost').val();
                    rows2.find('#single_postage').html('SS : ');
                    rows2.find('#single_postage_value').val(single_ss_cost);
                }else{
                    var single_sm_cost = rows2.find('#single_sm_cost').val();
                    rows2.find('#single_postage').html('SM :');
                    rows2.find('#single_postage_value').val(single_sm_cost);
                }
            });
            ///// LOOP THROUGH EACH FREIGHT COSTS /////
            rows.find('#freight_costs_th #freight_costs').each(function (i, row3) {
                var rows3 = $(row3);
                var single_freight = rows3.find('#is_freight').val();
                // single_freight = parseFloat(single_freight);
                // single_freight = single_freight.toFixed(2);
                if (single_freight == 1) {
                    single_air_freight = rows3.find('#single_air_cost').val();
                    single_air_freight = parseFloat(single_air_freight);
                    single_air_freight = single_air_freight.toFixed(2);
                    rows3.find('#per_product_freight_value').val(single_air_freight);
                }else if(single_freight == 2){
                    single_sea_freight = rows3.find('#single_sea_cost').val();
                    single_sea_freight = parseFloat(single_sea_freight);
                    single_sea_freight = single_sea_freight.toFixed(2);
                    rows3.find('#per_product_freight_value').val(single_sea_freight);
                }else{
                    rows3.find('#per_product_freight_value').val(0);
                }
            });
            ///// LOOP THROUGH EACH PRODUCT COSTS /////
            rows.find('#per_product_costs_th #per_product_costs').each(function (i, row3) {
                var rows3 = $(row3);
                var price_type = rows3.find("#is_regular").val();
                if (price_type == 1) {
                    var is_regular_price = rows3.find('#regular_price').val();
                    is_regular_price = parseFloat(is_regular_price);
                    is_regular_price = is_regular_price.toFixed(2);
                    rows3.find('#per_product_value').val(is_regular_price);
                }else{
                    var is_regular_price = rows3.find('#installment_price').val();
                    is_regular_price = parseFloat(is_regular_price);
                    is_regular_price = is_regular_price.toFixed(2);
                    rows3.find('#per_product_value').val(is_regular_price);
                }
            });
        });
    }
    function put_checkbox_values() {
        var order_outstanding = $('#order_outstanding_hidden').val();
        order_outstanding = parseFloat(order_outstanding);
        ///// LOOP THROUGH EACH ROW /////
        $('#append_tr > tr').each(function (i, row) {
            var rows = $(row);
            rows.find('#checkbox_th #checkbox_div').each(function (j, row5) {
                var rows5 = $(row5);
                var pk_no = rows5.find('[id*=product_pk]').data('pk_no');
                var per_product_cost = rows5.find('[id*=per_item_checkbox_price]').val();
                per_product_cost = parseFloat(per_product_cost);
                rows5.find('[id*=checked_item_price]').val(per_product_cost);
                var order_status = rows5.find('[id*=order_status]').val();
                if (order_status >= 60) {
                    rows5.find('#checkbox_of_order').iCheck('check');
                }else if (order_status >= 80) {
                    rows5.find('#checkbox_of_order').prop("disabled", true);
                }else{
                    if (per_product_cost > order_outstanding) {
                        rows5.find('#checkbox_of_order').prop("disabled", true);
                    }else{
                        rows5.find('#checkbox_of_order').prop("disabled", false);
                    }
                }
            });
        });
    }
    function count_amount() {
        var total_freight = 0;
        var total_postage = 0;
        var total_subtotal = 0;
        var subtotal_with_exta_costs = 0;
        ///// LOOP THROUGH EACH ROW /////
        $('#append_tr > tr').each(function (i, row) {
            var rows = $(row);
            ig_code = $(this).find('#prd_ig_code').text();
            if ($(this).find("input[type=radio][id=customer_preferred1]:checked").val()) {
                $(this).find("input[type=hidden][id=product_freight_type-"+ig_code+"]").val("AIR")
            }else{
                $(this).find("input[type=hidden][id=product_freight_type-"+ig_code+"]").val("SEA")
            }
            var per_product_cost = 0;
            var per_item_freight_cost = 0;
            var product_inv_pk = 0;
            ///// LOOP THROUGH EACH LINE SUB-TOTAL /////
            rows.find('#line_subtotal_costs_th #line_subtotal_costs').each(function (i, row3) {
                var rows3 = $(row3);
                var line_total = 0;
                product_inv_pk = rows3.find('#product_inv_pk').val();
                single_postage_value    = rows.find('#postage_costs_th #postage_delete'+product_inv_pk+' #single_postage_value').val();
                single_postage_value = parseFloat(single_postage_value);
                // single_postage_value = single_postage_value.toFixed(2);
                per_item_freight_cost   = rows.find('#freight_costs_th #freight_delete'+product_inv_pk+' #per_product_freight_value').val();
                per_item_freight_cost = parseFloat(per_item_freight_cost);
                // per_item_freight_cost = per_item_freight_cost.toFixed(2);
                per_product_cost        = rows.find('#per_product_costs_th #per_product_costs_delete'+product_inv_pk+' #per_product_value').val();
                per_product_cost = parseFloat(per_product_cost);
                // per_product_cost = per_product_cost.toFixed(2);
                total_postage += single_postage_value;
                total_freight += per_item_freight_cost;
                total_subtotal += per_product_cost;
                line_total = single_postage_value + per_item_freight_cost + per_product_cost;
                subtotal_with_exta_costs += line_total;
                $('#per_item_checkbox_price'+product_inv_pk).val(line_total);
                rows3.find('#line_subtotal_value_hidden').val(line_total);
                line_total = parseFloat(line_total);
                line_total = line_total.toFixed(2);
                rows3.find('#line_subtotal_value').val(line_total);
            });
        });
        $('#append_tfoot tr').find('#ss_amount_final').html(total_subtotal.toFixed(2));
        $('#append_tfoot tr').find('#freight_cost_total').html(total_freight.toFixed(2));
        $('#append_tfoot tr').find('#postage_cost_final').html(total_postage.toFixed(2));
        $('#append_tfoot tr').find('#total_with_extra_costs').html(subtotal_with_exta_costs.toFixed(2));
    }
    function grand_total() {
        var total_cost      = '';
        var discount_amount = 0;
        var grand_total     = 0;
        total_cost          = $('#append_tfoot tr').find('#total_with_extra_costs').text();
        total_cost          = parseFloat(total_cost);
        buffer_amount       = $('#buffer_amount').val();
        buffer_amount       = parseFloat(buffer_amount);
        balance_return      = $('#balance_return').val();
        balance_return      = parseFloat(balance_return);
        $('#append_tfoot tr').find('#discount_amount').attr({
            "max" : total_cost,
            "min" : 0
         });
        discount_amount     = $('#append_tfoot tr').find('#discount_amount').val();
        discount_amount     = parseFloat(discount_amount);
        if (discount_amount > total_cost) {
            $('#append_tfoot tr').find('#discount_amount').val(total_cost);
            discount_amount = total_cost;
        }
        if (discount_amount < 0 || !discount_amount) {
            $('#append_tfoot tr').find('#discount_amount').val(0);
            discount_amount = 0;
        }
        grand_total     = total_cost - discount_amount;
        buffer_amount   = grand_total - buffer_amount - balance_return;
        if (buffer_amount < 0) {
            buffer_amount = 0;
        }
        $('#append_tfoot tr').find('#grand_total_ss').html(grand_total.toFixed(2));
        $('#append_tfoot tr').find('#grand_total').val(total_cost.toFixed(2));
        $('#order_value').text(grand_total.toFixed(2));
        $('#due_amount').text(buffer_amount.toFixed(2));
    }
    //////////////// ORDER PAGE //////////////////
    /*delete single product row method for order*/
    $(document).on('click','[id*=delete_single_prd]', function(){
        if(confirm('Are you sure you want to delete?')){
            var prd_id = $(this).data('delete_id');
            var row = $(this).closest("tr");
            var warehouse_delete            = row.find('#warehouse_delete'+prd_id);
            var book_qty_delete             = row.find('#book_qty_delete'+prd_id);
            var postage_delete              = row.find('#postage_delete'+prd_id);
            var freight_delete              = row.find('#freight_delete'+prd_id);
            var per_product_costs_delete    = row.find('#per_product_costs_delete'+prd_id);
            var line_subtotal_delete        = row.find('#line_subtotal_delete'+prd_id);
            var checkbox_delete             = row.find('#checkbox_delete'+prd_id);
            var selfpickup_delete           = row.find('#selfpickup_delete'+prd_id);
            var delete_btn                  = row.find('#action_column'+prd_id);
            var get_url = $('#base_url').val();
            var pageurl = get_url+'/delete_book_to_order_item/'+prd_id;
            $.ajax({
                type:'get',
                url:pageurl,
                async :true,
                beforeSend: function () {
                    $("body").css("cursor", "progress");
                },
                success: function (data) {
                    if (data == 1) {
                        $(book_qty_delete).fadeOut();
                        $(warehouse_delete).fadeOut();
                        $(postage_delete).fadeOut();
                        $(freight_delete).fadeOut();
                        $(per_product_costs_delete).fadeOut();
                        $(line_subtotal_delete).fadeOut();
                        $(checkbox_delete).fadeOut();
                        $(selfpickup_delete).fadeOut();
                        $(delete_btn).fadeOut();
                        // $(update_btn).fadeOut();
                        $(book_qty_delete).remove();
                        $(warehouse_delete).remove();
                        $(postage_delete).remove();
                        $(freight_delete).remove();
                        $(per_product_costs_delete).remove();
                        $(line_subtotal_delete).remove();
                        $(checkbox_delete).remove();
                        $(selfpickup_delete).remove();
                        $(delete_btn).remove();
                        // $(update_btn).remove();
                        // count_total_final();
                        count_qty_final();
                        count_amount();
                    }else{
                        alert(data)
                    }
                },
                complete: function (data) {
                    $("body").css("cursor", "default");
                }
            });
        }
    });
    /*Get Customer Address For Rach ROw*/
    $(document).on('click','[id*=update_btn]', function(){
        var customer_id         = $('#customer_id').val();
        var pk_no               = $(this).data('pk_no');
        var address_id          = $(this).data('customer_address_id');
        var order_status        = $(this).data('order_status');
        var get_url             = $('#base_url').val();
        $('#add_new_btn').css('display','block');

        $.ajax({
            type:'get',
            url:get_url+'/booking/getCustomerAddress/'+customer_id+'/'+pk_no+'/'+address_id,
            async :true,
            dataType: 'json',
            beforeSend: function () {
                $("body").css("cursor", "progress");
            },
            success: function (data) {
                $('#cus_add_modal').html('').append(data.html);
                if (order_status >=80) {
                    $('#view_address_table tbody [id*=address_no_]').prop('disabled',true);
                }
            },
            complete: function (data) {
                $("body").css("cursor", "default");
            }
        });
    });
    $(document).on('click','[id=addCustomerAddressAll]', function(){
        var customer_id         = $('#customer_id').val();
        var pk_no               = $(this).data('pk_no');
        var address_id          = $(this).data('customer_address_id');
        var get_url             = $('#base_url').val();
        $.ajax({
            type:'get',
            url:get_url+'/booking/getCustomerAddress/'+customer_id+'/'+pk_no+'/'+address_id,
            async :true,
            dataType: 'json',
            beforeSend: function () {
                $("body").css("cursor", "progress");
            },
            success: function (data) {
                $('#cus_add_modal').html('').append(data.html);
            },
            complete: function (data) {
                $("body").css("cursor", "default");
            }
        });
    });
    /*Change Customer Address For Rach ROw*/
    $(document).on('click','[id*=address_no_]', function(){
        var customer_post_code  = $(this).data('customer_post_code');
        var address_no          = $(this).data('address_no');
        var pk_no               = $(this).data('pk_no');
        var name                = $(this).data('name');
        var get_url             = $('#base_url').val();
        var postage_section = $('#append_tr tr').find('#postage_delete'+pk_no);
        if (customer_post_code >= 87000) {
            var is_sm = 0;
            postage_section.find('#single_postage').text('SS : ');
            var single_ss_cost = postage_section.find('#single_ss_cost').val();
            postage_section.find('#single_postage_value').val(single_ss_cost);
            var address_type = '"SS"';
        }else{
            var is_sm = 1;
            postage_section.find('#single_postage').text('SM :');
            var single_sm_cost = postage_section.find('#single_sm_cost').val();
            postage_section.find('#single_postage_value').val(single_sm_cost);
            var address_type = '"SM"';
        }

        console.log(pk_no);
        console.log(address_no);
        postage_section.find('#customer_address').val(address_no);
        postage_section.find('#customer_postage').val(customer_post_code);
        postage_section.find('#is_sm').val(is_sm);
        count_amount();
        grand_total();
        update_checkbox_and_balance();
        $('#append_tr tr').find('#update_btn'+pk_no).text(address_no);
        $('#append_tr tr').find('#update_btn'+pk_no).data('customer_address_id',address_no);
        toastr.success('Address Updated to '+address_type+'!', 'Product Delivery Address Updated');
    });
    /*GET NEW ADDRESS FORM*/
    $(document).on('click','[id=add_new_btn]', function(){
        $('#view_address_table').css('display','none');
        $('#checkbox1').prop('checked', false);
        $('#same_as_label').css('display','none');
        $('#add_new_address_table').fadeIn();
        var customer_pk         = $('#customer_id').val();
        $('#customer_id_modal').val(customer_pk);
        destroycreatetypeahead(2);
        $('#addresstype option[value=1]').attr('selected','selected');
        $('#addresstype').css("pointer-events", 'none');
        $('#customeraddress').val('');
        $('#country').val(2);
        $('#state').val('');
        $('#city').val('');
        $('#post_code_').val('');
        $('#mobilenoadd').val('');
        $('#ad_1_').val('');
        $('#ad_2_').val('');
        $('#ad_3_').val('');
        $('#ad_4_').val('');
        $('#location').val('');
        $('#action_btn').text(' Add Address');
    });
    $(document).on('click','[id=address_book]', function(){
        $('#add_new_address_table').css('display','none');
        $('#view_address_table').fadeIn();
    });
    $(document).on('click','[id*=edit_address]', function(){
        var customer_pk     = $('#customer_id').val();
        var pk_no           = $(this).data('pk_no');
        var address_id      = $(this).data('address_no');
        var post_code       = $(this).data('post_code');
        var addresstype     = $(this).data('addresstype');
        var customeraddress = $(this).data('customeraddress');
        var mobilenoadd     = $(this).data('mobilenoadd');
        var ad_1            = $(this).data('ad_1');
        var ad_2            = $(this).data('ad_2');
        var ad_3            = $(this).data('ad_3');
        var ad_4            = $(this).data('ad_4');
        var location        = $(this).data('location');
        var country         = $(this).data('country');
        var state           = $(this).data('state');
        var city            = $(this).data('city');

        // getEditData(customer_pk,pk_no);
        var get_url = $('#base_url').val();
        $.ajax({
            type:'get',
            url:get_url+'/getCustomerAddressEdit/'+customer_pk+'/'+address_id,
            async :true,
            dataType: 'json',
            beforeSend: function () {
                $("body").css("cursor", "progress");
            },
            success: function (data) {
                $('#cus_add_modal').html('').append(data.html);
                destroycreatetypeahead(country);
                $('#checkbox1').prop('checked',false);
                $('#same_as_label').fadeOut();
                if (addresstype == 1) {
                    $('#addresstype option[value=1]').attr('selected','selected');
                }else{
                    $('#addresstype option[value=2]').attr('selected','selected');
                }
                $('#addresstype').css("pointer-events", 'none');
                $('#location').val(location);
                $('#address_pk_').val(address_id);
                $('#action_btn').text(' Update Address')
                $('#view_address_table').css('display','none');
                $('#add_new_address_table').fadeIn();
                $('#post_code_').val(post_code);
                $('#post_code_hidden').val(post_code);
                $('#customer_id_modal').val(customer_pk);
            },
            complete: function (data) {
                $("body").css("cursor", "default");
            }
        });
        // destroycreatetypeahead(country);
        // console.log('after');
        // $('#checkbox1').prop('checked',false);
        // $('#same_as_label').fadeOut();
        // $('#addresstype').val(addresstype);
        // $('#customeraddress').val(customeraddress);
        // $('#country').val(country);
        // $('#state').val(state);
        // $('#city').val(city);
        // $('#mobilenoadd').val(mobilenoadd);
        // $('#ad_1_').val(ad_1);
        // $('#ad_2_').val(ad_2);
        // $('#ad_3_').val(ad_3);
        // $('#ad_4_').val(ad_4);
        // $('#location').val(location);
        // $('#address_pk_').val(pk_no);
        // $('#action_btn').text(' Update Address')
        // $('#view_address_table').css('display','none');
        // $('#add_new_address_table').fadeIn();
        // $('#post_code_').val(post_code);
        // $('#post_code_hidden').val(post_code);
    });
    function add_address(order_status,inv_pk) {
        var get_url = $('#base_url').val();
        $.ajax({
            type:'POST',
            url:get_url+'/postCustomerAddress',
            data: $('#add_new_customer_form').serialize(),

            beforeSend: function () {
                $("body").css("cursor", "progress");
            },
            success: function (data) {
                if (data['status'] == 1) {
                    if ($('#address_pk_').val() > 0) {
                        toastr.success('Address Updated Successfully', 'Address Updated !');
                        $('#append_tr > tr').each(function (i, row) {
                            var rows = $(row);
                            ///// LOOP THROUGH EACH POSTAGE COSTS /////
                            rows.find('#postage_costs_th #postage_costs').each(function (i, row2) {
                                var rows2 = $(row2);
                                var is_self_pickup  = rows2.find('#is_self_pickup').val();
                                var address_pk      = rows2.find('#customer_address').val();

                                if ( data['address_pk'] == address_pk ) {

                                    if ( is_self_pickup == 1 ) {
                                        rows2.find('#single_postage').html('');
                                        rows2.find('#single_postage_value').val(0.00);
                                        rows2.find('#is_sm').val(0);
                                    }else if ( data['post_code'] >= 87000 ) {
                                        var single_ss_cost = rows2.find('#single_ss_cost').val();
                                        rows2.find('#single_postage').html('SS : ');
                                        rows2.find('#single_postage_value').val(single_ss_cost);
                                        rows2.find('#is_sm').val(0);
                                    }else{
                                        var single_sm_cost = rows2.find('#single_sm_cost').val();
                                        rows2.find('#single_postage').html('SM :');
                                        rows2.find('#single_postage_value').val(single_sm_cost);
                                        rows2.find('#is_sm').val(1);
                                    }
                                    rows2.find('#customer_postage').val(data['post_code']);
                                }
                            });
                        });
                        count_amount();
                        grand_total();
                        update_checkbox_and_balance();
                    }else{
                        toastr.success('New Address Added Successfully', 'Address Added !');
                    }
                    var customer_id         = $('#customer_id').val();
                    // var pk_no               = $('#single_prd_pk').val();
                    var address_id          = $('#address_pk_').val();
                    $.ajax({
                        type:'get',
                        url:get_url+'/booking/getCustomerAddress/'+customer_id+'/'+inv_pk+'/'+address_id,
                        // data: {
                        //     customer_id : customer_id,
                        //     customer_address_id : customer_address_id,
                        // },
                        async :true,
                        dataType: 'json',
                        beforeSend: function () {
                            $("body").css("cursor", "progress");
                        },
                        success: function (data) {
                            $('#cus_add_modal').html('').append(data.html);
                            if (order_status >= 80) {
                                $('#view_address_table tbody [id*=address_no_]').prop('disabled',true);
                            }
                        },
                        complete: function (data) {
                            $("body").css("cursor", "default");
                        }
                    });
                }else{
                    toastr.warning('Error !', 'Something went wrong, Please try again');
                }
            },
            complete: function (data) {
                $("body").css("cursor", "default");
            }
        })
    }
    /*SELF PICKUP BUTTON ACTIONS*/
    $('[id*=selfpickup_of_order]').on('ifChanged', function() {
        var pk_no               = $(this).data('pk_no');
        var postage_section     = $('#append_tr tr').find('#postage_delete'+pk_no);
        if($(this).is(":checked")) {
            postage_section.find('#is_self_pickup').val(1);
            postage_section.find('#single_postage').text('');
            postage_section.find('#single_postage_value').val(0.00);
        }else{
            postage_section.find('#is_self_pickup').val(0);
            var customer_post_code  = postage_section.find('#customer_postage').val();
            if (customer_post_code >= 87000) {
                postage_section.find('#single_postage').text('SS : ');
                var single_ss_cost = postage_section.find('#single_ss_cost').val();
                postage_section.find('#single_postage_value').val(single_ss_cost);
            }else{
                postage_section.find('#single_postage').text('SM :');
                var single_sm_cost = postage_section.find('#single_sm_cost').val();
                postage_section.find('#single_postage_value').val(single_sm_cost);
            }
        }
    });
    function destroycreatetypeahead(country) {
        $(".search-input4").typeahead("destroy");
        // $('#scrollable-dropdown-menu2').html('');
        // $('<input>').attr('type','search').attr('id','post_code_').attr('required',true).attr('name','post_code').addClass('form-control search-input4').attr('placeholder', 'Post Code').attr('autocomplete','off').appendTo('#scrollable-dropdown-menu2');
        call_typeahead(country);
    }
    function destroycreatetypeahead2(country) {
        $(".search-input8").typeahead("destroy");
        // $('#scrollable-dropdown-menu4').html('');
        // $('<input>').attr('type','search').attr('id','post_code_').attr('required',true).attr('name','post_code').addClass('form-control search-input4').attr('placeholder', 'Post Code').attr('autocomplete','off').appendTo('#scrollable-dropdown-menu2');
        call_typeahead2(country);
    }
    $(document).on('click','[id=payidbookorder]', function(){
        var get_url         = $('#base_url').val();
        var order_id        = $(this).data('order_id');
        var is_reseller     = $(this).data('is_reseller');
        $.ajax({
            type:'get',
            url:get_url+'/bookorder/getPayInfo/'+order_id+'/'+is_reseller,
            // data: {
            //     customer_id : customer_id,
            //     customer_address_id : customer_address_id,
            // },
            async :true,
            dataType: 'json',
            beforeSend: function () {
                $("body").css("cursor", "progress");
            },
            success: function (data) {
                console.log(data.html);
                $('#cus_add_modal2').html('').html(data.html);
            },
            complete: function (data) {
                $("body").css("cursor", "default");
            }
        });
    });

</script>
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
                        <label for="" style="color: #666ee8">{{ $row->PK_NO ?? 0 }}</label><br>
                        <input type="radio" id="address_no_{{ $row->PK_NO ?? 0 }}" name="change_address" class="flat-red" data-customer_post_code="{{ $row->POST_CODE ?? 0 }}" data-name="{{ $row->NAME ?? '' }}" data-address_no="{{ $row->PK_NO ?? 0 }}" data-pk_no="{{ $data['pk_no'] ?? 0 }}" {{ ($row->PK_NO ?? 0) == ($data['address_id'] ?? 0) ? 'checked' : '' }}>
                    </td>
                    <td>{{ $row->NAME }}</td>
                    <td style="width: 50%">{{ $row->ADDRESS_LINE_1 ?? '' }}
                         {{ isset($row->ADDRESS_LINE_2) ? ','.$row->ADDRESS_LINE_2 : '' }}
                         {{ isset($row->ADDRESS_LINE_3) ? ','.$row->ADDRESS_LINE_3 : '' }}
                         {{ isset($row->ADDRESS_LINE_4) }}
                         {{ isset($row->state->STATE_NAME) ? ','.$row->state->STATE_NAME : '' }}
                         {{ isset($row->city->CITY_NAME) ? ','.$row->city->CITY_NAME : '' }}
                         {{ isset($row->country->NAME) ? ','.$row->country->NAME : '' }}
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
        {!! Form::open([ 'class' => 'form-horizontal', 'id' => 'add_new_customer_form' , 'files' => true , 'novalidate']) !!}
        {!! Form::hidden('customer_id_',0,['id'=>'customer_id_modal']) !!}
        {!! Form::hidden('is_modal',1) !!}
        {!! Form::hidden('',$data['pk_no'],['id'=>'single_prd_pk']) !!}
        {!! Form::hidden('address_pk_',0,['id'=>'address_pk_']) !!}

        <div class="row">
            {{-- <div class="col-md-6 offset-md-3" id="order_date_section" style="{{ Request::segment(2) == 'getCustomerAddress' ? 'display:none;' : '' }}{{ Request::segment(2) == 'getCustomerAddressEdit' ? 'display:none;' : '' }}">
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
            </div> --}}
            <div class="col-md-12">
                <h3><strong>Delivery Address</strong></h3>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <br>
                    <div class="controls">
                        {{Form::hidden('same_as_add',0)}}
                        <label id="same_as_label"><input type="checkbox" name="same_as_add" id="checkbox1" checked>  {{ trans('form.same_as_add') }}</label>
                        {!! $errors->first('same_as_add', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('addresstype') ? 'error' : '' !!}">
                    <label>{{trans('form.address_type')}}</label>
                    <div class="controls">

                    {!! Form::select('addresstype', $address, $editdata->F_ADDRESS_TYPE_NO ?? null, ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'This field is required', 'id' => 'addresstype']) !!}
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
                <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                    <label>{{trans('form.mobile_no')}}</label>
                    <div class="controls">
                        {!! Form::text('mobilenoadd', $editdata->TEL_NO ?? null, [ 'class' => 'form-control mb-1','placeholder' => 'Enter Mobile No', 'tabindex' => 2, 'id' => 'mobilenoadd']) !!}
                        {!! $errors->first('mobilenoadd', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('country') ? 'error' : '' !!}">
                    <label>{{trans('form.country')}}</label>
                    <div class="controls">
                        {!! Form::select('country', $data['country'], $editdata->F_COUNTRY_NO ?? 2, ['class'=>'form-control mb-1 select2',
                        'data-validation-required-message' => 'Select Country', 'placeholder' => 'Select Country', 'id' => 'country','tabindex' =>
                        1, 'data-url' => URL::to('customer_state' )]) !!}
                        {!! $errors->first('country', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('post_code') ? 'error' : '' !!}">
                    <label>{{trans('form.post_code')}}</label>
                    <div class="controls" id="scrollable-dropdown-menu2">
                        <input type="search" name="post_code" id="post_code_" class="form-control search-input4" placeholder="Post Code" autocomplete="off" value="{{ $editdata->POST_CODE ?? '' }}" required>

                        {!! $errors->first('post_code', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                    <div id="post_code_appended_div">
                        {!! Form::hidden('post_code', 0, ['id'=>'post_code_hidden']) !!}
                    </div>
                    {{-- <div class="controls">
                        {!! Form::select('post_code', array(),  null, ['class'=>'form-control mb-1', 'id' => 'post_c',  'placeholder' => 'Select Post Code', 'tabindex' => 8,  ]) !!}
                        {!! $errors->first('post_code', '<label class="help-block text-danger">:message</label>') !!}
                    </div> --}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('city') ? 'error' : '' !!}">
                    <label>{{trans('form.city')}}</label>
                    <div class="controls">
                    {!! Form::select('city', $data['city'] ?? array(),  $editdata->CITY ?? null, ['class'=>'form-control mb-1 select2',
                    'data-validation-required-message' => 'Select City', 'id' => 'city','tabindex' =>
                    1,  isset($editdata->CITY) ? '' : 'placeholder' =>'Select City' ]) !!}
                        {!! $errors->first('city', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            {{-- STATE 1 --}}
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('state') ? 'error' : '' !!}">
                    <label>{{trans('form.state')}}</label>
                    <div class="controls">
                        {!! Form::select('state', $data['state'] ?? array(), $editdata->STATE ?? null, ['class'=>'form-control mb-1 select2',
                        'data-validation-required-message' => 'Select State', isset($editdata->CITY) ? '' : 'placeholder' => 'Select State', 'id' => 'state','tabindex' => 1 ]) !!}
                        {!! $errors->first('state', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group {!! $errors->has('ad_1') ? 'error' : '' !!}">
                    <label>{{trans('form.address_1')}}</label>
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
    // $('#checkbox1').change(function() {
    //     if(this.checked) {
    //         $('#display_none').fadeOut();
    //     }else{
    //         $('#display_none').fadeIn();
    //     }
    // });
    $('.pickadate').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
        max:"<?php echo date('d-m-Y'); ?>",
    });
</script>
