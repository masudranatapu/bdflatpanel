<?php
use Carbon\Carbon;
// echo '<pre>';
// echo '======================<br>';
// print_r($item);
// echo '<br>======================<br>';
// exit();
?>
<tr>
    <td style="width: 10%;"><img style="width: 150px !important; height: 150px;" src="{{ asset($item['info'][0]->PRD_VARIANT_IMAGE_PATH ?? '' ) }}" alt="PICTURE"></td>
    <td style="width: 20%">
        {{ $item['info'][0]->PRD_VARINAT_NAME ?? '' }}
        <br>
        <p>IG CODE : <span id="prd_ig_code">{{$item['info'][0]->IG_CODE ?? ''}}</span></p>
        <p>Regular: <span id="ss_price" class="danger" >{{ number_format($item['info'][0]->REGULAR_PRICE,2, '.', '') }}</span> RM</p>

        <p>Installment:<span id="sm_price" class="danger"> {{number_format($item['info'][0]->INSTALLMENT_PRICE,2, '.', '') }}</span> RM</p>

        @if ($item['info'][0]->F_INV_WAREHOUSE_NO == 1 && $item['info'][0]->SHIPMENT_TYPE == null && $item['info'][0]->F_BOX_NO == null )
        <p>Air- <span class="danger"> {{number_format($item['info'][0]->AIR_FREIGHT_COST,2, '.', '') }}</span> Sea- <span class="danger"> {{number_format($item['info'][0]->SEA_FREIGHT_COST,2, '.', '') }}</span></p>
        @else
        <p style="opacity: .7">Air- <span class="danger"> {{number_format($item['info'][0]->AIR_FREIGHT_COST,2, '.', '') }}</span> Sea- <span class="danger"> {{number_format($item['info'][0]->SEA_FREIGHT_COST,2, '.', '') }}</span></p>
        @endif
        <p>SM- <span class="danger"> {{number_format($item['info'][0]->SM_COST,2, '.', '') }}</span> SS- <span class="danger"> {{number_format($item['info'][0]->SS_COST,2, '.', '') }}</span></p>
    </td>
    <th style="width: 10%">
        <table id="warehouse">
            @if(!empty($item['info']))
            @foreach ($item['info'] as $data)
            @if ($data->F_INV_WAREHOUSE_NO == 1)
            <tr id="warehouse_delete{{ $data->PK_NO }}">
                <td title="{{ isset($data->BOX_TYPE) ? "BOX TYPE-($data->BOX_TYPE)" : '' }} {{ isset($data->SHIPMENT_TYPE) ? "SHIPMENT TYPE-($data->SHIPMENT_TYPE)" : '' }}">
                    @if ($data->F_ORDER_NO != null || $data->F_ORDER_NO != 0)
                    <a href="{{ route('admin.booking_to_order.book-order',$data->F_ORDER_NO) }}" target="_blank">
                    @elseif($data->F_BOOKING_NO != null || $data->F_BOOKING_NO != 0)
                    <a href="{{ route('admin.booking.edit',$data->F_BOOKING_NO) }}" target="_blank">
                    @endif
                    {{ $data->INV_WAREHOUSE_NAME }} ({{ Carbon::now() < $data->SCH_ARRIVAL_DATE ? Carbon::parse(Carbon::now())->diffInDays($data->SCH_ARRIVAL_DATE) : '90' }} Days)
                    {{-- @if (isset($data->PRODUCT_STATUS) && $data->PRODUCT_STATUS <= 60)
                    <span> <i class='ft-box' style="color: red"></i></span>
                    @endif --}}
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
                    <span> <i class='la la-plane' style="color: red;font-weight: 700"></i></span>
                    @endif
                    @if ($data->F_BOOKING_NO != null || $data->F_BOOKING_NO != 0)
                    </a>
                    @endif
                </td>
            </tr>
            @elseif ($data->F_INV_WAREHOUSE_NO > 1)
            <tr id="warehouse_delete{{ $data->PK_NO }}">
                <td>
                    @if ($data->F_ORDER_NO != null || $data->F_ORDER_NO != 0)
                    <a href="{{ route('admin.booking_to_order.book-order',$data->F_ORDER_NO) }}" target="_blank">
                    @elseif($data->F_BOOKING_NO != null || $data->F_BOOKING_NO != 0)
                    <a href="{{ route('admin.booking.edit',$data->F_BOOKING_NO) }}" target="_blank">
                    @endif
                    {{ $data->INV_WAREHOUSE_NAME }} (Ready Stock)
                    @if ($data->F_BOOKING_NO != null || $data->F_BOOKING_NO != 0 || $data->F_ORDER_NO != null || $data->F_ORDER_NO != 0)
                    </a>
                    @endif
                </td>
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
                <input name="postage_costs[]" value="{{ number_format($data->SS_COST,2, '.', '') }}" id="single_postage_value" type="number" style="font-weight: normal;font-size: 12px;display: inline-block;text-align:right;" class="form-control input-sm" readonly>
            </div>
        </div>
        @endforeach
    </th>
    <th id="postage_costs_th" style="width: 3%">
        @foreach ($item['info'] as $data)
        <div id="postage_delete{{ $data->PK_NO }}">
            <div style="margin-bottom: 5px" id="postage_costs">

                <input name="postage_costs[]" value="{{ number_format($data->SM_COST,2, '.', '') }}" id="single_postage_value" type="number" style="font-weight: normal;font-size: 12px;display: inline-block;text-align:right;" class="form-control input-sm" readonly>
            </div>
        </div>
        @endforeach
    </th>
    <th id="freight_costs_th" style="width: 7%">
        @foreach ($item['info'] as $data)
        <div id="freight_delete{{ $data->PK_NO }}" style="width: 70%">
            <div style="margin-bottom: 5px;text-align: center" id="freight_costs">
                <input name="freight_costs[]" value="{{ number_format($data->AIR_FREIGHT_COST,2, '.', '') }}" style="text-align:right;" type="number" class="form-control input-sm ml-2" id="per_product_freight_value" readonly>
            </div>
        </div>
        @endforeach
    </th>
    <th id="freight_costs_th" style="width: 7%">
        @foreach ($item['info'] as $data)
        <div id="freight_delete{{ $data->PK_NO }}" style="width: 70%">
            <div style="margin-bottom: 5px;text-align: center" id="freight_costs">
                <input name="freight_costs[]" value="{{ number_format($data->SEA_FREIGHT_COST,2, '.', '') }}" style="text-align:right;" type="number" class="form-control input-sm ml-2" id="per_product_freight_value" readonly>
            </div>
        </div>
        @endforeach
    </th>
    <th id="per_product_costs_th" style="width: 1%;">
        @foreach ($item['info'] as $data)
        <div id="per_product_costs_delete{{ $data->PK_NO }}" style="width: 100%;">
            <div style="margin-bottom: 5px" id="per_product_costs">
                <input name="unit_costs[]" value="{{ number_format($data->REGULAR_PRICE,2, '.', '') }}" id="per_product_value" type="number" style="font-weight: normal;font-size: 12px;text-align:right;" class="form-control input-sm" readonly>
            </div>
        </div>
        @endforeach
    </th>
    <th id="per_product_costs_th" style="width: 1%;">
        @foreach ($item['info'] as $data)
        <div id="per_product_costs_delete{{ $data->PK_NO }}" style="width: 100%;">
            <div style="margin-bottom: 5px" id="per_product_costs">
                <input name="unit_costs[]" value="{{ number_format($data->INSTALLMENT_PRICE,2, '.', '') }}" id="per_product_value" type="number" style="font-weight: normal;font-size: 12px;text-align:right;" class="form-control input-sm" readonly>
            </div>
        </div>
        @endforeach
    </th>
    <th id="per_product_costs_th" style="width: 1%;">
        @foreach ($item['info'] as $data)
        <div id="per_product_costs_delete{{ $data->PK_NO }}" style="width: 100%;">
            <div style="margin-bottom: 8.2px" id="per_product_costs">
                <a href="{{ asset($data->PRC_IN_IMAGE_PATH) }}" class="btn btn-xs btn-info" style="margin-bottom: 6px;" target="_blank" title="VIEW INVOICE"><i class="ft-file-text"></i></a>
            </div>
        </div>
        @endforeach
    </th>
    <th id="checkbox_th" style="width: 1%;">
        @foreach ($item['info'] as $data)
        <div id="checkbox_delete{{ $data->PK_NO }}" style="width: 100%">
            <div style="margin-bottom: 24px" id="checkbox_div" class="">
                <fieldset>
                    <input name="checkbox_value_{{ $data->PK_NO }}" type="checkbox" data-pk_no="{{ $data->PK_NO }}" data-booking_no="{{ $data->F_BOOKING_NO ?? 0 }}" id="checkbox_of_faulty{{ $data->PK_NO }}" {{ $data->PRODUCT_STATUS == 420 ? 'disabled checked' : '' }}>
                </fieldset>
            </div>
        </div>
        @endforeach
    </th>
</tr>
