<?php
use Carbon\Carbon;
$order =  $book->getOrder;
$available_amount = ($order->ORDER_ACTUAL_TOPUP ?? 0) - ($order->ORDER_BALANCE_RETURN ?? 0) - ($order->ORDER_BALANCE_USED ?? 0);
?>

@if($bundle && count($bundle) > 0 )
    @foreach($bundle as $key => $offer)
    <?php
        $Xbundle_total_price    = 0;
        $bundle_total_price     = 0;
        $bundle_total_price_with_cost = 0;
        $postage_bundle         = 0;
        $freight_bundle         = 0;
    ?>

        @if($bookingDetails)
            @foreach( $bookingDetails as $key => $book_detail )
                @if($book_detail->F_BUNDLE_NO == $offer->bundle->PK_NO && $offer->BUNDLE_SEQUENC == $book_detail->BUNDLE_SEQUENC )
                    <?php
                    $freight    = ($book_detail->CURRENT_IS_FREIGHT == 1) ? $book_detail->CURRENT_AIR_FREIGHT : (($book_detail->CURRENT_IS_FREIGHT == 2) ? $book_detail->CURRENT_SEA_FREIGHT : 0) ;
                    $freight_bundle += $freight;
                    $freight_old    = ($book_detail->IS_FREIGHT == 1) ? $book_detail->stock->AIR_FREIGHT_COST : (($book_detail->IS_FREIGHT == 2) ? $book_detail->stock->SEA_FREIGHT_COST : 0) ;
                    $postage    = $book_detail->CURRENT_IS_SM == 1 ? $book_detail->CURRENT_SM_COST : $book_detail->CURRENT_SS_COST;
                    $postage_bundle    += $postage;
                    $postage_old    = $book_detail->CURRENT_IS_SM == 1 ? $book_detail->stock->SM_COST : $book_detail->stock->SS_COST;
                    $unit_price_old = $book_detail->CURRENT_IS_REGULAR == 1 ? $book_detail->stock->REGULAR_PRICE : $book_detail->stock->INSTALLMENT_PRICE;
                    $Xbundle_total_price += $unit_price_old;
                    $unit_price = $book_detail->CURRENT_IS_REGULAR == 1 ? $book_detail->CURRENT_REGULAR_PRICE : $book_detail->CURRENT_INSTALLMENT_PRICE;
                    $bundle_total_price += $unit_price;
                    $line_total = $freight+$postage+$unit_price;
                    $line_total_old = $unit_price_old;
                    $bundle_total_price_with_cost += $line_total;
                    ?>
                    <tr class="bg-bundle-item ">
                        <td class="img_td">
                            @php $img_count = 0; @endphp
                            @if(isset($book_detail->stock->productVariant->allVariantPhotos) && count($book_detail->stock->productVariant->allVariantPhotos) > 0 )
                                <div class="lightgallery" style="margin:0px  auto; text-align: center; ">
                                    @php $img_count = $book_detail->stock->productVariant->allVariantPhotos->count(); @endphp
                                    @for($i = 0; $i < $img_count; $i++ )
                                        @php $vphoto = $book_detail->stock->productVariant->allVariantPhotos[$i]; @endphp
                                        <a class="img_popup " href="{{ asset($vphoto->RELATIVE_PATH)}}" style="{{ $i>0 ? 'display: none' : ''}}" title=""><img style="width: 40px !important; height: 40px;" data-src="{{ asset($vphoto->RELATIVE_PATH)}}" alt="" src="{{asset($vphoto->RELATIVE_PATH)}}" class="unveil"></a>
                                    @endfor
                                </div>
                            @else
                            <div class="lightgallery" style="margin:0px  auto; text-align: center; ">
                                <a class="img_popup " href="{{ asset('app-assets/images/no_image.jpg') }}"  title=""><img style="width: 40px !important; height: 40px;" data-src="{{ asset('app-assets/images/no_image.jpg') }}" alt="" src="{{asset($vphoto->RELATIVE_PATH)}}" class="unveil"></a>
                            </div>
                            @endif
                            <span class="badge badge-pill badge-primary badge-square img_c" title="Total {{$img_count}} photos for the product">{{$img_count}}</span>
                        </td>
                        <td id="postage_costs_th">
                            <div id="postage_costs">
                            {{ $book_detail->stock->PRD_VARINAT_NAME ?? '' }}
                            {!! Form::hidden('INV_PK_NO[]', $book_detail->F_INV_STOCK_NO) !!}
                            {!! Form::hidden('customer_address[]', $book_detail->CURRENT_F_DELIVERY_ADDRESS) !!}
                            {!! Form::hidden('is_freight[]', $book_detail->CURRENT_IS_FREIGHT,['id' => 'is_freight']) !!}
                            {!! Form::hidden('is_sm[]', $book_detail->CURRENT_IS_SM,['id' => 'is_sm']) !!}
                            {!! Form::hidden('is_regular[]', $book_detail->CURRENT_IS_REGULAR,['id' => 'is_regular']) !!}
                            {!! Form::hidden('', $book_detail->ORDER_STATUS ?? 0, ['id' => 'order_status']) !!}
                            {!! Form::hidden('', $book_detail->CURRENT_REGULAR_PRICE ?? 0, ['id' => 'regular_price']) !!}
                            {!! Form::hidden('', $book_detail->CURRENT_INSTALLMENT_PRICE ?? 0, ['id' => 'installment_price']) !!}
                            {!! Form::hidden('', $book_detail->CURRENT_SS_COST ?? 0, ['id' => 'ss_price']) !!}
                            {!! Form::hidden('', $book_detail->CURRENT_SM_COST ?? 0, ['id' => 'sm_price']) !!}
                            </div>
                        </td>
                        <td>{{ $book_detail->stock->warehouse->NAME ?? '' }}</td>
                        <td><input class="form-control form-control-sm input-sm max_val_check text-center"  type="number" min="1" max="1" value="1" readonly></td>
                        <td>
                            <input name="postage_costs[]" type="hidden"  class="single_postage_value form-control form-control-sm input-sm " readonly value="{{  number_format($postage,3,'.','') }}" >
                        </td>
                        <td>
                            <input name="freight_costs[]"  type="hidden" class="single_freight_value form-control form-control-sm input-sm "  readonly value="{{  number_format($freight,3,'.','') }}">
                        </td>
                        <td>
                            <input name="unit_costs[]"  type="hidden" class="single_unit_value form-control form-control-sm input-sm " readonly value="{{ $unit_price }}">
                            <input name="unit_costs_old[]"  type="number" class="form-control form-control-sm input-sm text-right" readonly value="{{ $unit_price_old }}" step="any">
                        </td>
                        <td>
                            <input name="line_total_costs[]"  type="hidden"  class="single_line_value form-control form-control-sm input-sm " readonly value="{{ $line_total}}">
                            <input name="line_total_costs_old[]"  type="hidden"  class="form-control form-control-sm input-sm text-right" readonly value="{{ $line_total_old }}">
                        </td>
                        <td>
                            <input name="checkbox_value_{{ $book_detail->F_INV_STOCK_NO }}" type="checkbox"  style="pointer-events: none; display:none;" class="form-control c_check  bundle_payment_status_{{ $offer->bundle->PK_NO.$book_detail->BUNDLE_SEQUENC }}" {{ $book_detail->ORDER_STATUS == 60 ? 'checked': '' }}   >

                        </td>
                        <td>
                            <input name="selfpickup_value_{{ $book_detail->F_INV_STOCK_NO }}" style="pointer-events: none; display:none;" type="checkbox" class="form-control c_check selfpickup_status bundle_selfpickup_{{ $offer->bundle->PK_NO.$book_detail->BUNDLE_SEQUENC }}" {{ $book_detail->IS_SELF_PICKUP == 1 ? 'checked': '' }}  >
                        </td>
                        <td></td>
                    </tr>

                @endif
            @endforeach
        @endif
        <?php
$postage_bundle = ceil( $postage_bundle / 5 ) * 5;

?>
        <tr class="bg-bundle" >
            <td colspan="1"></td>
            <td colspan="6">
                <p class="htext ">Sub Total <span class="float-right">{{ number_format($Xbundle_total_price,2,'.','') }}</span></p>
                <p class="htext ">Bundle Discount (You saved RM{{ number_format($Xbundle_total_price-($bundle_total_price),2,'.','') }})  <span class="float-right">- {{ number_format($Xbundle_total_price-($bundle_total_price),2,'.','') }}</span></p>
            </td>
            <td colspan="5"></td>
        </tr>
        <tr class="bg-bundle bundle-summary">
            <td class="img_tr">
                <div class="lightgallery" style="margin:0px  auto; text-align: center; ">
                    @if($offer->bundle->IMAGE)

                        <a class="img_popup " href="{{ asset($offer->bundle->IMAGE) }}"  title=""><img style="width: 40px !important; height: 40px;" data-src="{{ asset($offer->bundle->IMAGE) }}" alt="" src="{{ asset($offer->bundle->IMAGE) }}" class="unveil"></a>
                    @else
                        <img src="{{ asset('app-assets/images/no_image.jpg') }}"  width="50"/>
                    @endif
                </div>

            </td>
            <td colspan="2" class="text-left">
                <h4 class="text-azura">{{ $offer->bundle->BUNDLE_NAME ?? '' }}</h4>
            </td>
            <td class="text-center">1</td>
            <td class="text-right">{{ number_format($postage_bundle,2,'.','') }}</td>
            <td class="text-right">{{ number_format($freight_bundle,2,'.','') }}</td>
            <td class="text-right">{{ number_format($bundle_total_price,2,'.','') }}</td>
            <td class="text-right text-azura" style="font-size: 15px !important; font-weight:700;">{{ number_format($postage_bundle+$freight_bundle+$bundle_total_price,2,'.','') }}</td>
            <td>
                <input type="checkbox" data-bundle="{{ $offer->bundle->PK_NO }}" data-bundle-sequenc="{{ $offer->BUNDLE_SEQUENC }}" class="form-control payment_status payment_status_bundle c_check @if($bookingDetails[0]->ORDER_STATUS < 60){{ $available_amount < $bundle_total_price_with_cost ? 'readonly-check' : '' }} @endif" {{ $bookingDetails[0]->ORDER_STATUS == 60 ? 'checked': '' }} data-line_price="{{ $bundle_total_price_with_cost }}">
            </td>
            <td>
                <input  type="checkbox" data-bundle="{{ $offer->bundle->PK_NO }}" data-bundle-sequenc="{{ $offer->BUNDLE_SEQUENC }}" class="form-control selfpickup_status_bundle c_check" {{ $bookingDetails[0]->IS_SELF_PICKUP == 1 ? 'checked': '' }}>

            </td>
            <td>
                @if ($book->getOrder->DISPATCH_STATUS < 35 )
                <a href="javascript:void(0)" id="delete_single_prd{{ $offer->bundle->PK_NO }}" class="btn btn-xs btn-danger mr-1" data-delete_id="{{ $offer->bundle->PK_NO }}" title="DELETE" data-type="bundle" data-booking-no="{{ $book->PK_NO }}"><i class="la la-trash"></i></a>
                @endif
            </td>
        </tr>
    @endforeach
@endif

        @if($bookingDetails)

            @foreach( $bookingDetails as $key => $book_detail )
                @if($book_detail->F_BUNDLE_NO == null)
                    <?php
                    $freight    = ($book_detail->CURRENT_IS_FREIGHT == 1) ? $book_detail->CURRENT_AIR_FREIGHT : (($book_detail->CURRENT_IS_FREIGHT == 2) ? $book_detail->CURRENT_SEA_FREIGHT : 0) ;
                    $postage    = $book_detail->CURRENT_IS_SM == 1 ? $book_detail->CURRENT_SM_COST : $book_detail->CURRENT_SS_COST;
                    $unit_price = $book_detail->CURRENT_IS_REGULAR == 1 ? $book_detail->CURRENT_REGULAR_PRICE : $book_detail->CURRENT_INSTALLMENT_PRICE;
                    $line_total = $freight+$postage+$unit_price;

                    ?>
                    <tr >
                        <td class="img_td">
                            @php $img_count = 0; @endphp
                            @if(isset($book_detail->stock->productVariant->allVariantPhotos) && count($book_detail->stock->productVariant->allVariantPhotos) > 0 )
                                <div class="lightgallery" style="margin:0px  auto; text-align: center; ">
                                    @php $img_count = $book_detail->stock->productVariant->allVariantPhotos->count(); @endphp
                                    @for($i = 0; $i < $img_count; $i++ )
                                        @php $vphoto = $book_detail->stock->productVariant->allVariantPhotos[$i]; @endphp
                                        <a class="img_popup " href="{{ asset($vphoto->RELATIVE_PATH)}}" style="{{ $i>0 ? 'display: none' : ''}}" title=""><img style="width: 40px !important; height: 40px;" data-src="{{ asset($vphoto->RELATIVE_PATH)}}" alt="" src="{{asset($vphoto->RELATIVE_PATH)}}" class="unveil"></a>
                                    @endfor
                                </div>
                            @else
                            <div class="lightgallery" style="margin:0px  auto; text-align: center; ">
                                <a class="img_popup " href="{{ asset('app-assets/images/no_image.jpg') }}"  title=""><img style="width: 40px !important; height: 40px;" data-src="{{ asset('app-assets/images/no_image.jpg') }}" alt="" src="{{asset($vphoto->RELATIVE_PATH)}}" class="unveil"></a>
                            </div>
                            @endif
                            <span class="badge badge-pill badge-primary badge-square img_c" title="Total {{$img_count}} photos for the product">{{$img_count}}</span>
                        </td>


                        <td id="postage_costs_th">
                            <div id="postage_costs">
                            {{ $book_detail->stock->PRD_VARINAT_NAME ?? '' }}

                            {!! Form::hidden('INV_PK_NO[]', $book_detail->F_INV_STOCK_NO) !!}
                            {!! Form::hidden('customer_address[]', $book_detail->CURRENT_F_DELIVERY_ADDRESS) !!}
                            {!! Form::hidden('is_freight[]', $book_detail->CURRENT_IS_FREIGHT,['id' => 'is_freight']) !!}
                            {!! Form::hidden('is_sm[]', $book_detail->CURRENT_IS_SM,['id' => 'is_sm']) !!}
                            {!! Form::hidden('is_regular[]', $book_detail->IS_REGULAR,['id' => 'is_regular']) !!}
                            {!! Form::hidden('', $book_detail->ORDER_STATUS ?? 0, ['id' => 'order_status']) !!}
                            {!! Form::hidden('', $book_detail->CURRENT_REGULAR_PRICE ?? 0, ['id' => 'regular_price']) !!}
                            {!! Form::hidden('', $book_detail->CURRENT_INSTALLMENT_PRICE ?? 0, ['id' => 'installment_price']) !!}
                            {!! Form::hidden('', $book_detail->CURRENT_SS_COST ?? 0, ['id' => 'ss_price']) !!}
                            {!! Form::hidden('', $book_detail->CURRENT_SM_COST ?? 0, ['id' => 'sm_price']) !!}

                            <p class="text-info mb-0 f-11">SM : {{ number_format($book_detail->SM_COST,2,'.','') }} SS : {{ number_format($book_detail->SS_COST,2,'.','') }}</p>
                            <p class="text-info mb-0 f-11">AIR : {{ number_format($book_detail->AIR_FREIGHT,2,'.','') }} SEA : {{ number_format($book_detail->SEA_FREIGHT,2,'.','') }}</p>
                            </div>
                        </td>
                        <td><div id="warehouse_delete_{{  $book_detail->PK_NO }}" data-inv_pk="{{  $book_detail->F_INV_STOCK_NO }}">{{ $book_detail->stock->warehouse->NAME ?? '' }}</div></td>
                        <td><input class="form-control form-control-sm input-sm max_val_check text-center"  type="number" min="1" max="1" value="1" readonly></td>
                        <td>
                            <input name="postage_costs[]" type="number" id="single_postage__value" class="single_postage_value form-control form-control-sm input-sm pc_in" {{ $book_detail->ORDER_STATUS >= 80 ? 'readonly' : '' }} value="{{  number_format($postage,3,'.','') }}">
                        </td>
                        <td>
                            <input name="freight_costs[]"  type="number" class="single_freight_value form-control form-control-sm input-sm "  {{ $book_detail->ORDER_STATUS >= 80 ? 'readonly' : '' }} value="{{  number_format($freight,3,'.','') }}">
                        </td>
                        <td>
                            <input name="unit_costs[]"  type="number" class="single_unit_value form-control form-control-sm input-sm " {{ $book_detail->ORDER_STATUS >= 80 ? 'readonly' : '' }} value="{{  number_format($unit_price,2,'.','') }}">
                        </td>
                        <td>
                            <input name="line_total_costs[]"  type="number" class="single_line_value form-control form-control-sm input-sm " readonly value="{{  number_format($line_total,2,'.','') }}">
                        </td>
                        <td>
                            <input name="checkbox_value_{{ $book_detail->F_INV_STOCK_NO }}" type="checkbox" class="form-control c_check payment_status" {{ $book_detail->ORDER_STATUS == 60 ? 'checked': '' }} data-line_price="{{ $line_total }}" >

                        </td>
                        <td>
                            <input name="selfpickup_value_{{ $book_detail->F_INV_STOCK_NO }}" type="checkbox"  class="form-control c_check"  {{ $book_detail->IS_SELF_PICKUP == 1 ? 'checked': '' }}>
                        </td>
                        <td>
                            @if ($book_detail->ORDER_STATUS <= 60 || $book_detail->ORDER_STATUS == null )
                                <a href="javascript:void(0)" id="delete_single_prd{{ $book_detail->F_INV_STOCK_NO }}" class="btn btn-xs btn-danger mr-1" data-delete_id="{{ $book_detail->F_INV_STOCK_NO }}" style="float: left;{{ $book_detail->ORDER_STATUS == 60 ? 'display:none' : 'display:block' }}" title="DELETE"><i class="la la-trash"></i></a>
                            @endif
                        </td>
                    </tr>

                @endif
            @endforeach
        @endif

