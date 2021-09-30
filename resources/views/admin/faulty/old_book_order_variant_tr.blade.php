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
    {!! Form::hidden('products[]', $item['info'][0]->IG_CODE ?? '' ) !!}
    <td style="width: 10%;"><img style="width: 150px !important; height: 150px;" src="{{ asset($item['img']['PRIMARY_IMG_RELATIVE_PATH'] ?? '' ) }}" alt="PICTURE"></td>
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

    <label>{!! Form::radio('price_type-'.$item['info'][0]->IG_CODE.'', 'regular', isset($item['price_type']) && $item['price_type'] == 0 ? false : true,['id'=>'price_type','onclick'=>'javascript: return false;']) !!} Regular</label> &nbsp;&nbsp;
    <label>{!! Form::radio('price_type-'.$item['info'][0]->IG_CODE.'', 'installment', isset($item['price_type']) && $item['price_type'] == 0 ? true : false,['id'=>'price_type','onclick'=>'javascript: return false;']) !!} Installment</label>

    @if ($item['info'][0]->F_INV_WAREHOUSE_NO == 1 && $item['info'][0]->SHIPMENT_TYPE == null && $item['info'][0]->F_BOX_NO == null )
    <div>
        <label>{!! Form::radio('is_air'.$item['info'][0]->SKUID.'', number_format($item['info'][0]->AIR_FREIGHT,2, '.', ''),$preferred_method == 'AIR' ? true : false,['id'=>'customer_preferred1','onclick'=>'javascript: return false;']) !!} Air-{{ number_format($item['info'][0]->AIR_FREIGHT,2, '.', '') }}</label> &nbsp;&nbsp;
        <label>{!! Form::radio('is_air'.$item['info'][0]->SKUID.'', number_format($item['info'][0]->SEA_FREIGHT,2, '.', ''),$preferred_method == 'SEA' ? true : false,['id'=>'customer_preferred2','onclick'=>'javascript: return false;']) !!} Sea-{{ number_format($item['info'][0]->SEA_FREIGHT,2, '.', '') }}</label>
    </div>
    @else
    <p style="opacity: .7">Air- <span class="danger"> {{number_format($item['info'][0]->AIR_FREIGHT,2, '.', '') }}</span> Sea- <span class="danger"> {{number_format($item['info'][0]->SEA_FREIGHT,2, '.', '') }}</span></p>
    @endif
    <p>SM- <span class="danger"> {{number_format($item['info'][0]->SM_COST,2, '.', '') }}</span> SS- <span class="danger"> {{number_format($item['info'][0]->SS_COST,2, '.', '') }}</span></p>
</td>
    <th style="width: 10%">
        <table id="warehouse">
            @if(!empty($item['info']))
            @foreach ($item['info'] as $data)
            @if ($data->F_INV_WAREHOUSE_NO == 1)
            <tr id="warehouse_delete{{ $data->PK_NO }}">
                <th title="{{ isset($data->BOX_TYPE) ? "BOX TYPE-($data->BOX_TYPE)" : '' }} {{ isset($data->SHIPMENT_TYPE) ? "SHIPMENT TYPE-($data->SHIPMENT_TYPE)" : '' }}" >{{ $data->INV_WAREHOUSE_NAME }}<strong> ({{ Carbon::now() < $data->SCH_ARRIVAL_DATE ? Carbon::parse(Carbon::now())->diffInDays($data->SCH_ARRIVAL_DATE) : '90' }} Days)</strong>
                    @if (isset($data->BOX_TYPE))
                    <span> <i class='ft-box' style="color: red"></i></span>
                    @endif
                    @if (isset($data->SHIPMENT_TYPE) && $data->SHIPMENT_TYPE == 'SEA')
                    <span> <i class='la la-ship' style="color: blue"></i></span>
                    @endif
                    @if (isset($data->SHIPMENT_TYPE) && $data->SHIPMENT_TYPE == 'AIR')
                    <span> <i class='icon-plane' style="color: blue"></i></span>
                    @endif
                </th>
            </tr>
            @elseif ($data->F_INV_WAREHOUSE_NO > 1)
            <tr id="warehouse_delete{{ $data->PK_NO }}">
                <th>{{ $data->INV_WAREHOUSE_NAME }}<strong> (Ready Stock)</strong></th>
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
                <th><input class="form-control input-sm max_val_check" name="book-{{ $data->IG_CODE }}-house-{{ $data->F_INV_WAREHOUSE_NO }}-ship-{{ $data->SHIPMENT_TYPE ?? 0 }}-box-{{ $data->BOX_TYPE ?? 0 }}" id="booking_qty" type="number" min="1" max="1" value="1" data-type="house-{{ $data->F_INV_WAREHOUSE_NO }}-ship-{{ $data->SHIPMENT_TYPE ?? 0 }}-box-{{ $data->BOX_TYPE ?? 0 }}" readonly>
                </th>
                {{-- @endif --}}
            </tr>
            @elseif ($data->F_INV_WAREHOUSE_NO > 1)
            <tr id="book_qty_delete{{ $data->PK_NO }}">
                <th><input class="form-control input-sm max_val_check" name="book-{{ $data->IG_CODE }}-house-2-ship-0-box-0" id="booking_qty" type="number" min="1" max="1" value="1" readonly>
                </th>
            </tr>
            @endif
            @endforeach
            @endif
        </table>
    </th>
    <th id="postage_costs_th">
        @foreach ($item['info'] as $data)
        <div id="postage_delete{{ $data->PK_NO }}">
            <div style="margin-bottom: 17.5px" id="postage_costs">
                {!! Form::hidden('customer_postage', $data->customer_post_code, ['id' => 'customer_postage']) !!}
                {!! Form::hidden('single_sm_cost', number_format($item['info'][0]->SM_COST,2, '.', ''), ['id' => 'single_sm_cost']) !!}
                {!! Form::hidden('single_ss_cost', number_format($item['info'][0]->SS_COST,2, '.', ''), ['id' => 'single_ss_cost']) !!}
                <span id="single_postage" style="font-weight: normal;font-size: 12px;"></span><span id="single_postage_value"  style="font-weight: normal;font-size: 12px;"></span>
            </div>
        </div>
        @endforeach
    </th>
    <th id="freight_costs_th" style="width: 10px">
        @foreach ($item['info'] as $data)
        <div id="freight_delete{{ $data->PK_NO }}" style="width: 1%">
            <div style="margin-bottom: 17.5px;text-align: center" id="freight_costs">
                {!! Form::hidden('is_freight', $data->IS_FREIGHT, ['id' => 'is_freight']) !!}
                <span id="per_product_freight_value" style="font-weight: normal;font-size: 12px;" class="ml-1"></span>
            </div>
        </div>
        @endforeach
    </th>
    <th id="per_product_costs_th" style="width: 1%;">
        @foreach ($item['info'] as $data)
        <div id="per_product_costs_delete{{ $data->PK_NO }}" style="width: 10px;">
            <div style="margin-bottom: 17.5px" id="per_product_costs">
                {!! Form::hidden('is_freight', $data->IS_FREIGHT, ['id' => 'is_freight']) !!}
                {!! Form::hidden('single_air_cost', $data->AIR_FREIGHT, ['id' => 'single_air_cost']) !!}
                {!! Form::hidden('single_sea_cost', $data->SEA_FREIGHT, ['id' => 'single_sea_cost']) !!}
                <span id="per_product" style="font-weight: normal;font-size: 12px;"></span><span id="per_product_value"  style="font-weight: normal;font-size: 12px;"></span>
            </div>
        </div>
        @endforeach
    </th>
    {{-- <th id="per_product_costs_th" style="width: 0%;">
        @foreach ($item['info'] as $data)
        <div id="per_product_costs_delete{{ $data->PK_NO }}" style="width: 10px;">
            <div style="margin-bottom: 17.5px" id="per_product_costs">
                {!! Form::hidden('is_freight', $data->IS_FREIGHT, ['id' => 'is_freight']) !!}
                {!! Form::hidden('single_air_cost', $data->AIR_FREIGHT, ['id' => 'single_air_cost']) !!}
                {!! Form::hidden('single_sea_cost', $data->SEA_FREIGHT, ['id' => 'single_sea_cost']) !!}
                <span id="per_product" style="font-weight: normal;font-size: 12px;"></span><span id="per_product_value"  style="font-weight: normal;font-size: 12px;"></span>
            </div>
        </div>
        @endforeach
    </th> --}}
    <th id="checkbox_th">
        @foreach ($item['info'] as $data)
        <div id="checkbox_delete{{ $data->PK_NO }}">
            <div style="margin-bottom: 11.5px" id="checkbox_div" class="skin skin-square">
                <fieldset>
                    {!! Form::hidden('order_status', $data->ORDER_STATUS ?? 0, ['id' => 'order_status']) !!}
                    {!! Form::hidden('per_item_checkbox_price', 0, ['id' => 'per_item_checkbox_price'.$data->PK_NO]) !!}
                    {!! Form::hidden('product_pk', $data->PK_NO, ['id' => 'product_pk']) !!}
                    <input type="checkbox" id="checkbox_of_order" value="{{ $data->PK_NO }}" {{ $data->ORDER_STATUS >= 80 ? 'disabled' : '' }}>
                </fieldset>
            </div>
        </div>
        @endforeach
    </th>
    <th class="text-center">
        <input id="amount_ss" class="form-control" type="number" style="width: 100px;" value="00.00" readonly >
        <span id="freight_cost_section" style="font-weight: normal;font-size: 12px;">Freight : <span id="freight_cost"></span></span><br>
        <span id="postage_cost_section" style="font-weight: normal;font-size: 12px;">Postage : <span id="postage_cost_"></span></span><br>
    </th>
    <th class="text-center" style="width: 10%">
        @foreach ($item['info'] as $data)
        <div style="display: flex;margin-bottom: 11px">
            @if ($data->ORDER_STATUS <= 60 || $data->ORDER_STATUS == null)
            <a href="javascript:void(0)" id="delete_single_prd{{ $data->PK_NO }}" class="btn btn-xs btn-danger mr-1" data-delete_id="{{ $data->PK_NO }}" style="float: left;" title="DELETE"><i class="la la-trash"></i></a>
            @endif
            <a href="javascript:void(0)" id="update_btn{{ $data->PK_NO }}" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#UpdateCustomerAddress+" data-url="{{ route('account.bank.update', [$data->PK_NO]) }}" data-customer_id="{{$data->PK_NO}}" style="float: left;" title="UPDATE DELIVERY ADDRESS"><i class="la la-server"></i></a>
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
        count_total_final();
        count_qty_final();
        count_amount();
        grand_total();

        /*CHANGE POSTAGE COST FOR ALL*/
        $("#postage_cost, #amount_freight").keyup(function(){

            count_amount();
            grand_total();
        });
        $('#append_tr > tr').each(function (i, row) {
            var rows = $(row);
            rows.find('#per_product_costs_th #per_product_costs').each(function (i, row3) {
                var rows3 = $(row3);
                var per_product_cost = 0;
                per_product_cost = rows3.find('#per_product_value').text();
                console.log(per_product_cost);
                per_product_cost = parseFloat(per_product_cost);
                rows.find('#checkbox_th #checkbox_div').each(function (j, row5) {
                    var rows5 = $(row5);
                    if (i == j) {
                        rows5.find('[id*=per_item_checkbox_price]').val(per_product_cost.toFixed(2));
                        var order_status = rows5.find('[id*=order_status]').val();
                        if (order_status >= 60) {
                            rows5.find('#checkbox_of_order').iCheck('check');
                        }
                        if (order_status >= 80) {
                            rows5.find('#checkbox_of_order').prop("disabled", true);
                        }else{
                            if (per_product_cost > order_outstanding) {
                                rows5.find('#checkbox_of_order').prop("disabled", true);
                            }else{
                                rows5.find('#checkbox_of_order').prop("disabled", false);
                            }
                        }
                    }
                });
            });
        });

        $('[id*=checkbox_of_order]').on('ifChanged', function() {
            var order_outstanding = $('#order_outstanding').text();
            order_outstanding = parseFloat(order_outstanding);

            var order_balance_used = $('#order_balance_used').text();
            order_balance_used = parseFloat(order_balance_used);

            var pk_no = $(this).val();

            var order_price = $('#per_item_checkbox_price'+pk_no).val();
            order_price = parseFloat(order_price);
            if($(this).is(":checked")) {
                order_outstanding = order_outstanding - order_price;
                order_outstanding = parseFloat(order_outstanding);
                $('#order_outstanding').text(order_outstanding);

                order_balance_used = order_balance_used + order_price;
                order_balance_used = parseFloat(order_balance_used);
                $('#order_balance_used').text(order_balance_used);
                var order_price_update = 'add';
                // alert($(this).val())
            }else{
                order_outstanding = order_outstanding + order_price;
                order_outstanding = parseFloat(order_outstanding);
                $('#order_outstanding').text(order_outstanding);

                order_balance_used = order_balance_used - order_price;
                order_balance_used = parseFloat(order_balance_used);
                $('#order_balance_used').text(order_balance_used);
                var order_price_update = 'minus';
                // console.log(order_price);
                // console.log(order_balance_used);
            }

            var get_url = $('#base_url').val();
            var pageurl = get_url+'/update_order_payment';
            $.ajax({
                type:'post',
                url:pageurl,
                data:{
                    pk_no: pk_no,
                    order_balance_used: order_balance_used,
                    order_price_update: order_price_update
                },
                // dataType: 'JSON',
                async :true,
                beforeSend: function () {
                    $("body").css("cursor", "progress");
                },
                success: function (data) {
                    if (data = 1) {
                        if (order_price_update == 'add') {
                            toastr.success('Payment for this product was successfull', 'Payment is successfull');
                        }else{
                            toastr.warning('Payment for this product was successfull', 'Payment is Withdrawn');
                        }
                    }else{
                        alert('Please try again !')
                    }
                },
                complete: function (data) {
                    $("body").css("cursor", "default");
                }
            });

            payment_checkbox_action();
        });

    });

    function payment_checkbox_action() {
        var order_outstanding = $('#order_outstanding').text();
        order_outstanding = parseFloat(order_outstanding);
        $('#append_tr > tr').each(function (i, row) {
            var rows = $(row);

            rows.find('#checkbox_th #checkbox_div').each(function (i, row2) {
                var rows2 = $(row2);
                var item_price = rows2.find('[id*=per_item_checkbox_price]').val();
                item_price = parseFloat(item_price);
                var order_status = rows2.find('[id*=order_status]').val();
                // var if_checked = $(this).find('input[id*="checkbox_of_order"]').iCheck('check');
                // if (order_status >= 60) {
                //     rows2.find('#checkbox_of_order').iCheck('check');
                //     console.log('checked');
                // }
                // if (order_status >= 80) {
                //     rows2.find('#checkbox_of_order').prop("disabled", true);
                //     console.log('80');
                // }
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
                // if (item_price > order_outstanding) {
                //     rows2.on('ifChecked', function(event){
                //         rows2.find('#checkbox_of_order').prop("disabled", false);
                //     console.log('checked');
                //     });
                //     rows2.on('ifUnchecked', function(event){
                //         rows2.find('#checkbox_of_order').prop("disabled", true);
                //     console.log('unchecked');
                //     });
                // }else{
                //     rows2.find('#checkbox_of_order').prop("disabled", false);
                // }
            });
        });
    }

    function count_total_final() {
        var ss_price_count = 0;
        var sm_price_count = 0;
        $('#append_tr tr').each(function (i, row) {
            var rows = $(row);
            var ss_price = rows.find('#amount_ss').val();
            if (ss_price > 0) {
                ss_price_count += parseFloat(ss_price);
            }
        });
        $('#append_tfoot tr').find('#ss_amount_final').html(ss_price_count);
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

    function count_amount() {
        var total_ss = 0;
        var total_freight = 0;
        var grand_postage_cost = 0;
        var postage_percentage_value = 0;
        var freight_percentage_value = 0;

        var given_postage = $('#append_tfoot tr').find('#postage_cost').val();
        var total_qty = $('#append_tfoot tr').find('#grand_final_qty').text();
        given_postage = parseFloat(given_postage);
        total_qty = parseInt(total_qty);

        postage_percentage_value = (given_postage/total_qty).toFixed(2);
        postage_percentage_value = parseFloat(postage_percentage_value);

        var given_freight_cost = $('#append_tfoot tr').find('#amount_freight').val();
        var freight_qty = $('#order_form').find('#count_freight').val();
        given_freight_cost = parseFloat(given_freight_cost);
        freight_qty = parseInt(freight_qty);

        freight_percentage_value = (given_freight_cost/freight_qty).toFixed(2);
        freight_percentage_value = parseFloat(freight_percentage_value);
        // console.log(freight_percentage_value);
        // console.log(freight_qty);
        // console.log(typeof(freight_qty));
        // console.log(typeof(freight_percentage_value));

        var order_outstanding = $('#order_outstanding').text();
        order_outstanding = parseFloat(order_outstanding);
        order_outstanding = order_outstanding.toFixed(2);

        ///// LOOP THROUGH EACH ROW /////
        $('#append_tr > tr').each(function (i, row) {
            var rows = $(row);
            var ss_price = '';
            ss_price = $(this).find('#ss_price').text();
            ss_price = parseFloat(ss_price);
            var sm_price = '';
            sm_price = $(this).find('#sm_price').text();
            sm_price = parseFloat(sm_price);

            var price_type = $(this).find("input[type=radio][id=price_type]:checked").val();

            ig_code = $(this).find('#prd_ig_code').text();
            if ($(this).find("input[type=radio][id=customer_preferred1]:checked").val()) {
                $(this).find("input[type=hidden][id=product_freight_type-"+ig_code+"]").val("AIR")
                // $(this).find('#product_freight_type').val("AIR")
                // alert(0)
            }else{
                $(this).find("input[type=hidden][id=product_freight_type-"+ig_code+"]").val("SEA")
                // $(this).find('#product_freight_type').val("SEA")
                // alert(1)
            }

            // console.log(grand_ss_postage_cost);
            var index = 0;
            var booking_qty = 0;
            var freight_cost = 0;
            var uk_only_qty = 0;
            var single_freight = 0;
            var single_postage_cost = 0;
            var per_item_postage_cost = 0;
            var sub_total_ss_postage_cost = 0;
            var sub_total_sm_postage_cost = 0;

            ///// LOOP THROUGH CHILD ROW OF PARENT /////
            rows.find('#th_book_qty tr').each(function (i, row2) {
                var rows2 = $(row2);
                var qty = rows2.find('#booking_qty').val();
                var type = '';
                type = rows2.find('#booking_qty').attr('data-type');
                if (type == 'house-1-ship-0-box-0') {
                    uk_only_qty += parseInt(qty);
                    // freight_cost = $(this).find('#customer_preferred').val();
                }if (qty > 0) {
                    booking_qty += parseInt(qty);
                }
            });


            ///// LOOP THROUGH EACH POSTAGE COSTS /////
            rows.find('#postage_costs_th #postage_costs').each(function (i, row2) {
                var rows2 = $(row2);
                var post_code = rows2.find('#customer_postage').val();

                if (post_code >= 87000 ) {

                    var single_ss_cost = rows2.find('#single_ss_cost').val();
                    rows2.find('#single_postage').html('SS : ');
                    rows2.find('#single_postage_value').html(postage_percentage_value);
                }else{
                    var single_sm_cost = rows2.find('#single_sm_cost').val();
                    rows2.find('#single_postage').html('SM : ');
                    rows2.find('#single_postage_value').html(postage_percentage_value);
                }
                index++;
            });


            per_item_postage_cost = postage_percentage_value * index;
            per_item_postage_cost = per_item_postage_cost.toFixed(2);

            var single_ss = 0;
            var single_sm = 0;
            var per_product_price = 0;

            $(this).find('#postage_cost_').html(per_item_postage_cost);
            grand_postage_cost += per_item_postage_cost;

            if (price_type == 'regular') {
                single_ss = ss_price*booking_qty;
                single_sm = ss_price*booking_qty;
                per_product_price = ss_price;
            }else{
                single_ss = sm_price*booking_qty;
                single_sm = sm_price*booking_qty;
                per_product_price = sm_price;
            }
            total_ss += single_ss;
            // total_sm += single_sm;

            if (uk_only_qty > 0) {
                freight_cost = $(this).find("input[type=radio][id*=customer_preferred]:checked").val();
                total_freight += uk_only_qty*freight_cost
                single_freight += uk_only_qty*freight_cost
            }

            // $(this).find('#amount_ss').val(single_ss.toFixed(2));
            // $(this).find('#price').val(single_ss.toFixed(2));
            // $(this).find('#amount_sm').val(single_sm.toFixed(2));

            var free_freight = 0;
            var single_air_freight = 0;
            var single_sea_freight = 0;
            var per_item_total_cost = 0;
            var per_product_cost = 0;
            var per_item_freight_cost = 0;
            ///// LOOP THROUGH EACH PRODUCT COSTS /////
            rows.find('#per_product_costs_th #per_product_costs').each(function (i, row3) {
            var rows3 = $(row3);
            var single_freight = rows3.find('#is_freight').val();
            per_product_price = parseFloat(per_product_price);
            if (single_freight == 1) {
                // single_air_freight = rows3.find('#single_air_cost').val();
                // single_air_freight = parseFloat(single_air_freight);
                // single_air_freight = single_air_freight + postage_percentage_value + per_product_price;
                // single_air_freight = single_air_freight.toFixed(2);
                // per_item_total_cost = parseFloat(per_item_total_cost);
                // per_item_total_cost += single_air_freight;
                // rows3.find('#per_product_value').html(single_air_freight);
                per_product_cost = freight_percentage_value + postage_percentage_value + per_product_price;
                per_item_total_cost = parseFloat(per_item_total_cost);
                per_item_total_cost += per_product_cost;
                per_item_freight_cost += freight_percentage_value;
                rows3.find('#per_product_value').html(per_product_cost.toFixed(2));
            }else if(single_freight == 2){
                per_product_cost = freight_percentage_value + postage_percentage_value + per_product_price;
                per_item_total_cost = parseFloat(per_item_total_cost);
                per_item_total_cost += per_product_cost;
                per_item_freight_cost += freight_percentage_value;
                rows3.find('#per_product_value').html(per_product_cost.toFixed(2));

            }else{
                per_product_cost = postage_percentage_value + per_product_price;
                per_item_total_cost = parseFloat(per_item_total_cost);
                per_item_total_cost += per_product_cost;
                rows3.find('#per_product_value').html(per_product_cost.toFixed(2));
            }
            rows.find('#checkbox_th #checkbox_div').each(function (j, row5) {
                var rows5 = $(row5);
                if (i == j) {
                    rows5.find('#checkbox_of_order').prop("disabled", true);
                    // rows5.find('[id*=per_item_checkbox_price]').val(per_product_cost.toFixed(2));
                    // var order_status = rows5.find('[id*=order_status]').val();
                    // if (order_status >= 60) {
                    //     rows5.find('#checkbox_of_order').iCheck('check');
                    // }
                    // if (order_status >= 80) {
                    //     rows5.find('#checkbox_of_order').prop("disabled", true);
                    // }else{
                    //     if (per_product_cost > order_outstanding) {
                    //         rows5.find('#checkbox_of_order').prop("disabled", true);
                    //     }else{
                    //         rows5.find('#checkbox_of_order').prop("disabled", false);
                    //     }
                    // }
                }
            });
        });

        ///// LOOP THROUGH EACH FREIGHT COSTS /////
        rows.find('#freight_costs_th #freight_costs').each(function (i, row4) {
            var rows4 = $(row4);
            var single_freight = rows4.find('#is_freight').val();
            per_product_price = parseFloat(per_product_price);
            // per_product_price = per_product_price.toFixed(2);
            if (single_freight == 1) {
                rows4.find('#per_product_freight_value').html(freight_percentage_value);
            }else if(single_freight == 2){
                rows4.find('#per_product_freight_value').html(freight_percentage_value);
            }else{
                rows4.find('#per_product_freight_value').html('----');
            }
        });

        // total_ss += per_item_total_cost;
        $(this).find('#amount_ss').val(per_item_total_cost.toFixed(2));
        if (per_item_freight_cost != 0) {
            $(this).find('#freight_cost_section').fadeIn();
            $(this).find('#freight_cost').text(per_item_freight_cost.toFixed(2));
        }else{
            $(this).find('#freight_cost_section').fadeOut();
        }
        });
        $('#append_tfoot tr').find('#ss_amount_final').html(total_ss.toFixed(2));

        var given_freight = $('#append_tfoot tr').find('#given_freight').text();
        given_freight = parseFloat(given_freight);
        if (total_freight == given_freight) {
            $('#append_tfoot tr').find('#given_freight_td').remove();
        }
    }

    function grand_total() {
        var total_ss = '';
        // var total_sm = '';
        var grand_ss = 0;
        var grand_sm = 0;
        var freight_cost = 0;
        var freight_cost2 = 0;
        var postage_cost = 0;
        total_ss = $('#append_tfoot tr').find('#ss_amount_final').text();
        total_ss = parseFloat(total_ss);
        freight_cost = $('#append_tfoot tr').find('#amount_freight').val();
        freight_cost = parseFloat(freight_cost);
        postage_cost = $('#append_tfoot tr').find('#postage_cost').val();
        postage_cost = parseFloat(postage_cost);

        grand_ss = total_ss + freight_cost + postage_cost;

        $('#append_tfoot tr').find('#grand_total_ss').html(grand_ss.toFixed(2));
        $('#append_tfoot tr').find('#grand_total').val(grand_ss.toFixed(2));
        $('#append_tfoot tr').find('#grand_total_ss').html(grand_ss.toFixed(2));
    }

    //////////////// ORDER PAGE //////////////////
    /*delete single product row method for order*/
    $(document).on('click','[id*=delete_single_prd]', function(){
        if(confirm('Are you sure you want to delete?')){

            var prd_id = $(this).data('delete_id');
            var row = $(this).closest("tr");
            var warehouse_delete = row.find('#warehouse_delete'+prd_id);
            var book_qty_delete = row.find('#book_qty_delete'+prd_id);
            var postage_delete = row.find('#postage_delete'+prd_id);
            var freight_delete = row.find('#freight_delete'+prd_id);
            var per_product_costs_delete = row.find('#per_product_costs_delete'+prd_id);
            var checkbox_delete = row.find('#checkbox_delete'+prd_id);
            var delete_btn = row.find('#delete_single_prd'+prd_id);
            var update_btn = row.find('#update_btn'+prd_id);

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
                        $(checkbox_delete).fadeOut();
                        $(delete_btn).fadeOut();
                        $(update_btn).fadeOut();
                        $(book_qty_delete).remove();
                        $(warehouse_delete).remove();
                        $(postage_delete).remove();
                        $(freight_delete).remove();
                        $(per_product_costs_delete).remove();
                        $(checkbox_delete).remove();
                        $(delete_btn).remove();
                        $(update_btn).remove();
                        count_total_final();
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

</script>
