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
    <p>Air- <span class="danger"> {{number_format($item['info'][0]->AIR_FREIGHT,2, '.', '') }}</span> Sea- <span class="danger"> {{number_format($item['info'][0]->SEA_FREIGHT,2, '.', '') }}</span></p>
    @else
    <p style="opacity: .7">Air- <span class="danger"> {{number_format($item['info'][0]->AIR_FREIGHT,2, '.', '') }}</span> Sea- <span class="danger"> {{number_format($item['info'][0]->SEA_FREIGHT,2, '.', '') }}</span></p>
    @endif
    <p>SM- <span class="danger"> {{number_format($item['info'][0]->SM_COST,2, '.', '') }}</span> SS- <span class="danger"> {{number_format($item['info'][0]->SS_COST,2, '.', '') }}</span></p>
</td>
    <th style="width: 10%">
        <table id="warehouse">
            @if(!empty($item['info']))
            @foreach ($item['info'] as $data)
            @if($data->F_INV_WAREHOUSE_NO == 1)
            <tr id="warehouse_delete{{ $data->PK_NO }}" data-inv_pk="{{ $data->PK_NO }}" style="cursor: pointer;">
                <th title="{{ isset($data->BOX_TYPE) ? "BOX TYPE-($data->BOX_TYPE)" : '' }} {{ isset($data->SHIPMENT_TYPE) ? "SHIPMENT TYPE-($data->SHIPMENT_TYPE)" : '' }}@if($data->F_BUNDLE_NO), Offer : {{ $data->BUNDLE_NAME}} @endif " style="{{ $data->ORDER_STATUS >= 80 ? 'opacity:.5' : '' }}" >
                    {{-- {{ $data->INV_WAREHOUSE_NAME }}
                    <strong> ({{ Carbon::now() < $data->SCH_ARRIVAL_DATE ? Carbon::parse(Carbon::now())->diffInDays($data->SCH_ARRIVAL_DATE) : '90' }} Days)</strong> --}}
                    <?php
                        $status = \Config::get('static_array.shipping_status_short');
                    ?>
                    <strong>{{ $data->SHIPMENT_STATUS >= 20 ? $status[$data->SHIPMENT_STATUS] : $data->INV_WAREHOUSE_NAME }}
                        {{-- ({{ Carbon::now() < $data->SCH_ARRIVAL_DATE ? Carbon::parse(Carbon::now())->diffInDays($data->SCH_ARRIVAL_DATE) : '90' }} Days) --}}
                        </strong>
                        <strong>({{ $data->SHIPMENT_STATUS >= 20 ? Carbon::parse(Carbon::now())->diffInDays($data->SCH_ARRIVAL_DATE,false) : '90' }} Days)
                        </strong>
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
            <tr id="warehouse_delete{{ $data->PK_NO }}" data-inv_pk="{{ $data->PK_NO }}" style="cursor: pointer;">
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
                <th><input class="form-control form-control-sm input-sm max_val_check" id="booking_qty" type="number" min="1" max="1" value="1" data-type="house-{{ $data->F_INV_WAREHOUSE_NO }}-ship-{{ $data->SHIPMENT_TYPE ?? 0 }}-box-{{ $data->BOX_TYPE ?? 0 }}" readonly>
                </th>
                {{-- @endif --}}
            </tr>
            @elseif ($data->F_INV_WAREHOUSE_NO > 1)
            <tr id="book_qty_delete{{ $data->PK_NO }}">
                <th><input class="form-control form-control-sm input-sm max_val_check" id="booking_qty" type="number" min="1" max="1" value="1" readonly>
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
                {!! Form::hidden('', $data->ORDER_STATUS ?? 0, ['id' => 'order_status']) !!}
                {!! Form::hidden('', $data->IS_SELF_PICKUP , ['id'=>'is_self_pickup']) !!}
                {!! Form::hidden('is_sm[]', $data->CURRENT_IS_SM , ['id'=>'is_sm']) !!}
                {!! Form::hidden('', $item['delivery_postage'], ['id' => 'customer_postage']) !!}
                {!! Form::hidden('', number_format($data->CURRENT_SM_COST,3, '.', ''), ['id' => 'single_sm_cost']) !!}
                {!! Form::hidden('', number_format($data->CURRENT_SS_COST,3, '.', ''), ['id' => 'single_ss_cost']) !!}
                <span id="single_postage" style="font-weight: normal;font-size: 12px; {{ $data->ORDER_STATUS >= 80 ? 'opacity:.5' : '' }}" ></span>
                <input name="postage_costs[]" id="single_postage_value" type="number" style="font-weight: normal;font-size: 12px;display: inline-block;text-align:right;" class="form-control form-control-sm input-sm" {{ $data->ORDER_STATUS >= 80 ? 'readonly' : '' }}>
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
                <input name="freight_costs[]" style="text-align:right;" type="number" class="form-control form-control-sm input-sm ml-2" id="per_product_freight_value" {{ $data->ORDER_STATUS >= 80 ? 'readonly' : '' }}>
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

                <input name="unit_costs[]" id="per_product_value" type="number" style="font-weight: normal;font-size: 12px;text-align:right;" class="form-control form-control-sm input-sm" {{ $data->ORDER_STATUS >= 80 ? 'readonly' : '' }}>
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
                <input name="line_total_costs[]" id="line_subtotal_value" type="number" style="font-weight: normal;font-size: 12px;text-align:right;" class="form-control form-control-sm input-sm" readonly>
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
            <div style="margin-bottom: 10px" id="checkbox_div" class="skin skin-square">
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
            <div style="height: 35px;" id="selfpickup_div" class="skin skin-flat">
                <fieldset>
                    {{-- {{Form::hidden('checkbox_value[]','off')}} --}}
                    <input name="selfpickup_value_{{ $data->PK_NO }}" type="checkbox" data-pk_no="{{ $data->PK_NO }}" id="selfpickup_of_order" {{ $data->ORDER_STATUS >= 80 ? 'disabled' : '' }}{{ $data->IS_SELF_PICKUP == 1 ? 'checked' : '' }}>
                </fieldset>
            </div>
        </div>
        @endforeach
    </th>
    {{-- <th class="text-center">
        <input id="amount_ss" class="form-control form-control-sm" type="number" style="width: 100px;" value="00.00" readonly >
        <span id="freight_cost_section" style="font-weight: normal;font-size: 12px;">Freight : <span id="freight_cost"></span></span><br>
        <span id="postage_cost_section" style="font-weight: normal;font-size: 12px;">Postage : <span id="postage_cost_"></span></span><br>
    </th> --}}
    <th class="text-center" style="width: 5%" id="action_col">
        @foreach ($item['info'] as $data)
        <div style="display: flex;margin-bottom: .3px;width: 50%;" id="action_column{{ $data->PK_NO}}">
            {!! Form::hidden('INV_PK_NO[]', $data->F_INV_STOCK_NO ) !!}
            @if (($data->ORDER_STATUS <= 60 || $data->ORDER_STATUS == null) && ($data->F_BUNDLE_NO == null))
            <a href="javascript:void(0)" id="delete_single_prd{{ $data->PK_NO }}" class="btn btn-xs btn-danger mr-1" data-delete_id="{{ $data->PK_NO }}" style="float: left;{{ $data->ORDER_STATUS == 60 ? 'display:none' : 'display:block' }}" title="DELETE"><i class="la la-trash"></i></a>
            @endif
        </div>
        @endforeach
    </th>
</tr>
