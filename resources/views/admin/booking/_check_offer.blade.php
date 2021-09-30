<?php
$total_price_regular = 0;
$total_price_installment = 0;
$is_freight     = $data['bookingDetails'][0]->IS_FREIGHT;
$is_sm          = $data['bookingDetails'][0]->IS_SM;
$is_regular     = $data['bookingDetails'][0]->IS_REGULAR;
$grand_total = 0;

?>
<div class="card" >
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered " id="offerTbl">
                    <thead>
                    <tr>
                        <th style="width: 10%">SL.</th>
                        <th style="width: 50%">Details</th>
                        <th style="width: 10%">Item Qty</th>
                        <th style="width: 10%">Price
                            @if($is_regular == 1)
                                (Regular)
                            @else
                                (Installment)
                            @endif
                        </th>

                        <th style="width: 10%">Freight Cost</th>
                        <th style="width: 10%">Total Price</th>


                    </tr>
                    </thead>
                    <tbody>
                        @if(isset($data['bundle']) && count($data['bundle']))
                            @foreach($data['bundle'] as $key => $row)
                        @php
                        $total_bundle = 0;

                        if($is_regular == 1){
                            $total_bundle += $row->TOTAL_REGULAR_BUNDLE_PRICE;
                        }else{
                            $total_bundle += $row->TOTAL_INSTALLMENT_BUNDLE_PRICE*$row->BUNDLE_QTY;
                        }
                        if($is_freight == 1 ){
                            $total_bundle += ($row->P_AIR+$row->R_AIR)*$row->BUNDLE_QTY;
                        }elseif($is_freight == 2 ){
                            $total_bundle += ($row->P_SEA+$row->R_SEA)*$row->BUNDLE_QTY;
                        }else{
                            $total_bundle += 0;
                        }


                        @endphp
                        <tr class="bg-bundle bundle-summary">
                            <td style="width: 10%" class="text-center">
                                <img style="width: 60px !important; height: 60px;" src="{{ asset($row->IMAGE_PATH ) }}" alt="PICTURE">
                            </td>
                            <td style="width: 40%">
                                {{ $row->BUNDLE_NAME_PUBLIC }}
                            </td>
                            <td style="width: 10%" class="text-center">
                                <div style="display: block; padding-bottom:10px; ">
                                    {{ $row->BUNDLE_QTY }}
                                </div>

                            </td>

                            <td style="width: 10%" class="text-right">
                                @if($is_regular == 1)
                                    <div> {{ number_format($row->TOTAL_REGULAR_BUNDLE_PRICE/$row->BUNDLE_QTY,2) }} </div>
                                @else
                                <div>{{ number_format($row->TOTAL_INSTALLMENT_BUNDLE_PRICE/$row->BUNDLE_QTY,2) }}</div>
                                @endif

                            </td>

                            <td style="width: 10%" class="text-right">
                                @if($is_freight == 1 )
                                    <div>AIR : {{ number_format(($row->P_AIR+$row->R_AIR)*$row->BUNDLE_QTY,2) }}</div>
                                @elseif($is_freight == 2 )
                                    <div>SEA : {{ number_format(($row->P_SEA+$row->R_SEA)*$row->BUNDLE_QTY,2) }}</div>
                                @else
                                <div>0.00</div>
                                @endif
                            </td>
                            <td style="width: 10%" class="text-right">
                                @php $grand_total += $total_bundle; @endphp
                                <div>{{ number_format($total_bundle,2) }}</div>
                            </td>

                        </tr>

                        @endforeach

                        @if(isset($data['non_bundle']) && count($data['non_bundle']) > 0 )
                        @foreach($data['non_bundle'] as $b => $nrow )
                        <?php
                            $total_non_bundle = 0;

                        if($is_regular == 1){
                            $total_non_bundle += $nrow->CURRENT_REGULAR_PRICE*$nrow->ITEM_QTY;
                        }else{
                            $total_non_bundle += $nrow->CURRENT_INSTALLMENT_PRICE*$nrow->ITEM_QTY;
                        }
                        if($is_freight == 1 ){
                            $total_non_bundle += $nrow->AIR_FREIGHT*$nrow->ITEM_QTY;
                        }if($is_freight == 2 ){
                                $total_non_bundle += $nrow->SEA_FREIGHT*$nrow->ITEM_QTY;
                        }else{
                            $total_non_bundle += 0;
                        }

                        ?>
                        <tr>
                            <td style="width: 10%" class="text-center">
                                <img style="width: 60px !important; height: 60px;" src="{{ asset($nrow->PRD_VARIANT_IMAGE_PATH) }}" alt="PICTURE">
                            </td>
                            <td style="width: 40%">{{ $nrow->PRD_VARINAT_NAME }}</td>
                            <td style="width: 10%" class="text-center">{{ $nrow->ITEM_QTY }}</td>

                            <td style="width: 10%" class="text-right">
                                @if($is_regular == 1)
                                    <div>{{ number_format($nrow->CURRENT_REGULAR_PRICE*$nrow->ITEM_QTY,2) }} </div>
                                @else
                                    <div>{{ number_format($nrow->CURRENT_INSTALLMENT_PRICE*$nrow->ITEM_QTY,2) }}</div>
                                @endif
                            </td>

                            <td style="width: 10%" class="text-right">
                                @if($is_freight == 1 )
                                    <div>AIR : {{ number_format($nrow->AIR_FREIGHT*$nrow->ITEM_QTY,2) }}</div>
                                @elseif($is_freight == 1 )
                                    <div>SEA : {{ number_format($nrow->SEA_FREIGHT*$nrow->ITEM_QTY,2) }}</div>
                                @else
                                    <div>0.00</div>
                                @endif
                            </td>
                            <td style="width: 10%" class="text-right">
                                @php $grand_total += $total_non_bundle; @endphp
                                <div>{{ number_format($total_non_bundle,2) }}</div>
                            </td>

                        </tr>
                        @endforeach
                        @endif
                        <tr>
                            <td>Total Price</td>
                            <td colspan="3" class="text-right">
                                {{  number_format($grand_total,2)  }}
                                <input type="hidden" name="total_price" value="{{ $grand_total }}" />
                            </td>
                            <td  class="text-center" colspan="2">
                                <button type="button" class="btn btn-primary save-inv-details submit_button_book2"  value="book_and_check_offer"><i class="la la-check-square-o"></i> <span class="proceed_book_with_offer">Proceed to Order with Offer</span></button>

                                <button type="button" class="btn btn-primary save-inv-details mt-1 submit_button_book2 d-none"  value="book_and_order_with_offer" id="book_and_order_with_offer"><i class="la la-check-square-o"></i> <span>Proceed to Order With Offer</span></button>

                            </td>
                        </tr>

                        @else
                        <tr>
                            <td colspan="8" class="text-center">BUNDLE NOT MACHED</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
