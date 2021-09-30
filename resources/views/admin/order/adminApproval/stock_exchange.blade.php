<?php
use Carbon\Carbon;
?>
<style>
    #exchange_modal tr:hover{
        color: #F78902;
    }
    #exchange_modal th{
        border-top: none;
    }
</style>
<table class="table table-striped table-borde table-sm" id="exchange_modal">
@foreach ($rows as $data)
@if ($data->F_INV_WAREHOUSE_NO == 1)
<tr id="warehouse_delete{{ $data->PK_NO }}" data-inv_pk="{{ $data->PK_NO }}" style="cursor: pointer;">
    <th title="{{ isset($data->BOX_TYPE) ? "BOX TYPE-($data->BOX_TYPE)" : '' }} {{ isset($data->SHIPMENT_TYPE) ? "SHIPMENT TYPE-($data->SHIPMENT_TYPE)" : '' }}" style="{{ $data->ORDER_STATUS >= 80 ? 'opacity:.5' : '' }}" id="exchange_with" data-warehouse="{{ $data->F_INV_WAREHOUSE_NO }}"  data-box_type="{{ $data->BOX_TYPE }}" data-shipment_type="{{ $data->SHIPMENT_TYPE }}" data-shipment_no="{{ $data->F_SHIPPMENT_NO }}" data-skuid="{{ $data->SKUID }}" data-inv_pk="{{ $invpk }}">
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
    <th style="{{ $data->ORDER_STATUS >= 80 ? 'opacity:.5' : '' }}" id="exchange_with" data-warehouse="{{ $data->F_INV_WAREHOUSE_NO }}"  data-box_type="{{ $data->BOX_TYPE }}" data-shipment_type="{{ $data->SHIPMENT_TYPE }}" data-shipment_no="{{ $data->F_SHIPPMENT_NO }}" data-skuid="{{ $data->SKUID }}" data-inv_pk="{{ $invpk }}">{{ $data->INV_WAREHOUSE_NAME }}<strong> (Ready Stock)</strong>
    </th>
</tr>
@endif
@endforeach
</table>
