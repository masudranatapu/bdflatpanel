
@if(isset($response['booking_details_canceled']) && count($response['booking_details_canceled']) > 0 )
    @foreach($response['booking_details_canceled'] as $row)
    <?php $cancel_total +=  $row->LINE_PRICE; ?>
        <tr>
            <td>
                <img src="{{ asset($row->PRD_VARIANT_IMAGE_PATH) }}" height="120" >
            </td>
            <td>{{ $row->PRD_VARINAT_NAME }}</td>
            <td>{{ $row->INV_WAREHOUSE_NAME }}</td>
            <td>1</td>
            <td>@if($row->CURRENT_IS_SM == 1) {{ $row->CURRENT_SM_COST }}@elseif($row->CURRENT_IS_SM == 0) {{ $row->CURRENT_SS_COST }} @endif</td>
            <td>@if($row->CURRENT_IS_FREIGHT == 1){{ $row->CURRENT_AIR_FREIGHT }}@elseif($row->CURRENT_IS_FREIGHT == 2) {{ $row->CURRENT_SEA_FREIGHT }} @else 0 @endif</td>
            <td>@if($row->CURRENT_IS_REGULAR == 1) {{ number_format($row->CURRENT_REGULAR_PRICE,2) }}@elseif($row->CURRENT_IS_REGULAR == 0) {{ number_format($row->CURRENT_INSTALLMENT_PRICE,2) }}@endif</td>
            <td>{{ number_format($row->LINE_PRICE,2) }}</td>
            <td></td>
            <td></td>
            <td></td>


        </tr>
    @endforeach
@endif

