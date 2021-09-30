@if($rows && count($rows) > 0 )
@foreach( $rows as $key => $row )
<tr class="row_{{ $row->PK_NO }}">
    <td>
        <img src="{{ asset($row->PRIMARY_IMG_RELATIVE_PATH) }}"  style="width : 50px;" />
    </td>
    <td>
        {{ $row->VARIANT_NAME }}
    </td>
    <td>
        {{ $row->MRK_ID_COMPOSITE_CODE }}
    </td>
    <td class="text-center">
        <input type="hidden" value="{{ $row->PK_NO }}" name="variant_no[]" />
        <input type="hidden" value="{{ $row->MRK_ID_COMPOSITE_CODE }}" name="variant_skuid[]" />
        <input type="hidden" value="{{ $row->VARIANT_NAME }}" name="variant_name[]" />
        <button type="button" class="btn btn-xs btn-danger mr-1 delete-row" data-barcode="121212" data-pk="{{ $row->PK_NO }}" onclick="return confirm('Are You Sure?')"><i class="la la-trash"></i></button>
    </td>
</tr>
@endforeach
@endif
