
<tr>
    {{-- {!! Form::hidden('products[]', $item['info'][0]['IG_CODE'] ?? '' ) !!} --}}
    <td style="width: 50px;"><input type="number" id="serial_no" style="width: 50px;border: none;"></td>
    <td id="box_serial">{{ $serial ?? '' }}</td>
    <td id="box_name">{{ $item->BOX_NO ?? '' }}</td>
    <td>
        {{ $item->ITEM_COUNT ?? '' }}
    </td>
    <td>
        <a href="javascript:void(0)" class="btn btn-xs btn-danger" id="delete_prd{{ $serial }}" title="DELETE" data-shipment_id={{ $boxid }}><i class="la la-trash"></i></a>
    </td>
</tr>
<script>
    var last_row = $('table tr:last');
    var last_serial = last_row.closest('tr').prev();
    if (last_serial.length != 0) {
        var last_id = $('#serial_no').val();
        var last_id2 = parseInt(last_id);
        last_id2++;
        last_row.closest('tr').find('#serial_no').val(last_id2);
    }else{
        last_row.closest('tr').find('#serial_no').val('1');
    }
</script>
