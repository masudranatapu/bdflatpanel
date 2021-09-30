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


    <td style="width: 10%;" class="img_td">
        {{-- <img style="width: 150px !important; height: 150px;" src="{{ asset($item['info'][0]->PRIMARY_IMG_RELATIVE_PATH ?? '' ) }}" alt="PICTURE"> --}}
        @php $img_count = 0; @endphp
        @if(isset($item['info'][0]->productVariant->allVariantPhotos) && count($item['info'][0]->productVariant->allVariantPhotos) > 0 )
            <div class="lightgallery" style="margin:0px  auto; text-align: center; ">
                @php $img_count = $item['info'][0]->productVariant->allVariantPhotos->count(); @endphp
                @for($i = 0; $i < $img_count; $i++ )
                    @php $vphoto = $item['info'][0]->productVariant->allVariantPhotos[$i]; @endphp
                    <a class="img_popup " href="{{ asset($vphoto->RELATIVE_PATH)}}" style="{{ $i>0 ? 'display: none' : ''}}" title=""><img style="width: 150px !important; height: 150px;" data-src="{{ asset($vphoto->RELATIVE_PATH)}}" alt="" src="{{asset($vphoto->RELATIVE_PATH)}}" class="unveil"></a>
                @endfor
            </div>
        @else
        <div class="lightgallery" style="margin:0px  auto; text-align: center; ">
            <a class="img_popup " href="{{ asset('app-assets/images/no_image.jpg') }}"  title=""><img style="width: 150px !important; height: 150px;" data-src="{{ asset('app-assets/images/no_image.jpg') }}" alt="" src="{{asset($item['info'][0]->PRIMARY_IMG_RELATIVE_PATH)}}" class="unveil"></a>
        </div>
        @endif
        <span class="badge badge-pill badge-primary badge-square img_c" title="Total {{$img_count}} photos for the product">{{$img_count}}</span>
    </td>

    <td style="width: 40%">
    {{ $item['info'][0]->PRD_VARINAT_NAME ?? '' }}
    <br>
    <p>AM CODE : <span id="prd_ig_code">{{$item['info'][0]->IG_CODE ?? ''}}</span></p>
    <p>BARCODE : <span id="prd_barcode">{{$item['info'][0]->BARCODE ?? ''}}</span></p>
    <p>Regular: <span id="ss_price" class="danger" >{{ number_format($item['info'][0]->REGULAR_PRICE,2, '.', '') }}</span> RM</p>

    <p>Installment:<span id="sm_price" class="danger"> {{number_format($item['info'][0]->INSTALLMENT_PRICE,2, '.', '') }}</span> RM</p>

    {!! Form::hidden("ss_postage", number_format($item['info'][0]->SS_COST,2, '.', ''), ['id'=>'ss_postage_cost']) !!}
    {!! Form::hidden("sm_postage", number_format($item['info'][0]->SM_COST,2, '.', ''), ['id'=>'sm_postage_cost']) !!}
    {!! Form::hidden("product_freight_type-".$item['info'][0]->IG_CODE."", $preferred_method , ['id'=>'product_freight_type-'.$item['info'][0]->IG_CODE.'']) !!}
    @if ($item['info'][0]->F_INV_WAREHOUSE_NO == 1 && $item['info'][0]->SHIPMENT_TYPE == null && $item['info'][0]->F_BOX_NO == null )
    <div>
        <p>Air- <span class="danger"> {{number_format($item['info'][0]->AIR_FREIGHT_COST,2, '.', '') }}</span> Sea- <span class="danger"> {{number_format($item['info'][0]->SEA_FREIGHT_COST,2, '.', '') }}</span></p>
    </div>
    @else
    <p style="opacity: .7">Air- <span class="danger"> {{number_format($item['info'][0]->AIR_FREIGHT_COST,2, '.', '') }}</span> Sea- <span class="danger"> {{number_format($item['info'][0]->SEA_FREIGHT_COST,2, '.', '') }}</span></p>
    @endif
    <p>SM- <span class="danger"> {{number_format($item['info'][0]->SM_COST,2, '.', '') }}</span> SS- <span class="danger"> {{number_format($item['info'][0]->SS_COST,2, '.', '') }}</span></p>
</td>
    <td style="width: 10%">
        <table id="warehouse">
            @if(!empty($item['info']))
            @if (isset($item['info'][0]->count_my_warehouse) && $item['info'][0]->count_my_warehouse > 0)
                <tr>
                    <th>{{ $item['my_warehouse_name'] }}<strong> (Ready Stock)</strong></th>
                </tr>
            @endif
            @foreach ($item['info'] as $data)
            @if ($data->F_INV_WAREHOUSE_NO == 1)
            <tr>
                <th title="{{ isset($data->BOX_TYPE) ? "BOX TYPE-($data->BOX_TYPE)" : '' }} {{ isset($data->SHIPMENT_TYPE) ? "SHIPMENT TYPE-($data->SHIPMENT_TYPE)" : '' }}" >
                    <?php
                        $status = \Config::get('static_array.shipping_status_short');
                    ?>
                    @if (isset($data->SHIPMENT_STATUS))
                    <strong>{{ $data->SHIPMENT_STATUS >= 20 ? $status[$data->SHIPMENT_STATUS] : $data->INV_WAREHOUSE_NAME }}
                    @else
                    <strong>{{ $data->INV_WAREHOUSE_NAME }}
                    @endif
                    </strong>
                    @if (isset($data->SHIPMENT_STATUS))
                    <strong> ({{ $data->SHIPMENT_STATUS >= 20 ? Carbon::parse(Carbon::now())->diffInDays($data->SCH_ARRIVAL_DATE,false) : '90' }} Days)
                    </strong>
                    @else
                    <strong>(90 Days)</strong>
                    @endif
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
                    @if ($data->F_INV_WAREHOUSE_NO == 1 && $data->SHIPMENT_TYPE == null && $data->F_BOX_NO == null )
                        <label class="mb-0">{!! Form::radio('is_air'.$data->SKUID.'', number_format($data->AIR_FREIGHT_COST,2, '.', ''),$preferred_method == 'AIR' ? true : false,['id'=>'customer_preferred1']) !!} Air</label>
                        <label class="mb-0">{!! Form::radio('is_air'.$data->SKUID.'', number_format($data->SEA_FREIGHT_COST,2, '.', ''),$preferred_method == 'SEA' ? true : false,['id'=>'customer_preferred2']) !!} Sea</label>
                    @endif
                </th>
            </tr>
            @endif
            @endforeach
            @endif
        </table>
    </th>
    <td class="text-center" style="width: 10%">
        <table id="availble_qty" style="width: 100%;">
            @if(!empty($item['info']))
            @if (isset($item['info'][0]->count_my_warehouse) && $item['info'][0]->count_my_warehouse > 0)
                <tr>
                    <th>{{ $item['info'][0]->count_my_warehouse }}</th>
                </tr>
            @endif
            @foreach ($item['info'] as $data)
            @if ($data->F_INV_WAREHOUSE_NO == 1)
            <tr>
                <th class="text-center">{{ $data->total }}</th>
            </tr>
            @endif
            @endforeach
            @endif
        </table>
    </td>
    <td id="th_book_qty" style="width: 10%">
        <table id="book_qty"  style="width: 100%;">
            @if(!empty($item['info']))
            @if (isset($item['info'][0]->count_my_warehouse) && $item['info'][0]->count_my_warehouse > 0)
                <tr>
                    <th><input class="form-control input-sm max_val_check remove_first_zero" name="book-{{ $data->IG_CODE }}-house-2-ship-0-box-0-ship_no-0" id="booking_qty" type="number" min="1" max="{{  $item['info'][0]->count_my_warehouse }}" value="{{ $item['count_my_booked'] ?? 0 }}" data-type="house-{{ $data->F_INV_WAREHOUSE_NO }}-ship-0-box-0-ship_no-0" data-house="2" data-ship="0" data-box="0" data-ship_no="0" @if(request()->get('checkoffer') == 1) readonly @endif>
                    </th>
                </tr>
            @endif
            @foreach ($item['info'] as $data)
            @if ($data->F_INV_WAREHOUSE_NO == 1)
            <tr>
                {{-- @if (isset($data->SHIPMENT_TYPE)) --}}
                <th><input class="form-control input-sm max_val_check remove_first_zero" name="book-{{ $data->IG_CODE }}-house-{{ $data->F_INV_WAREHOUSE_NO }}-ship-{{ $data->SHIPMENT_TYPE ?? 0 }}-box-{{ $data->BOX_TYPE ?? 0 }}-ship_no-{{ $data->F_SHIPPMENT_NO ?? 0 }}" id="booking_qty" type="number" min="1" max="{{ $data->total }}" value="{{ $data->book_qty ?? 0 }}" data-type="house-{{ $data->F_INV_WAREHOUSE_NO }}-ship-{{ $data->SHIPMENT_TYPE ?? 0 }}-box-{{ $data->BOX_TYPE ?? 0 }}-ship_no-{{ $data->F_SHIPPMENT_NO ?? 0 }}" data-house="1" data-ship="{{ $data->SHIPMENT_TYPE ?? 0 }}" data-box="{{ $data->BOX_TYPE ?? 0 }}" data-ship_no="{{ $data->F_SHIPPMENT_NO ?? 0 }}" @if(request()->get('checkoffer') == 1) readonly @endif>
                </th>
                {{-- @endif --}}
            </tr>
            @endif
            @endforeach
            @endif
        </table>
    </td>
    <td class="text-center">
        <input id="amount_ss" class="form-control text-right" type="number" style="width: 100px;" value="00.00" readonly >
        <span id="freight_cost_section" style="font-weight: normal;font-size: 12px;">Freight : <span id="freight_cost"></span></span><br>
        <span id="sm_postage_cost_section" style="font-weight: normal;font-size: 12px;">SM Cost : <span id="sm_postage_cost_"></span></span><br>
        <span id="ss_postage_cost_section" style="font-weight: normal;font-size: 12px;">SS Cost : <span id="ss_postage_cost_"></span></span><br>
    </td>
    <td class="text-center" style="width: 10%">
        @if(request()->get('checkoffer') == 1)
        <a href="javascript:void(0)" class="btn btn-sm btn-danger text-center" style="" title="Offer order delete not possible"><i class="la la-close"></i></a>
        @else
        <a href="javascript:void(0)" class="btn btn-sm btn-danger text-center" id="delete_prd" style="" title="TEMPORARY DELETE"><i class="la la-close"></i></a>
        @endif

    </td>
</tr>
<script>
    $(".lightgallery").lightGallery();
</script>
