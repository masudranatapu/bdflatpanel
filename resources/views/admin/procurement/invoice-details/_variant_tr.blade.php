<?php
$vatclass_combo = getVatClassCombo() ?? [];
?>
<tr class="row_{{$item->PK_NO}}">
    <td class=" hidden">
        <input type="hidden" name="variant_id[]" value="{{ $item->PK_NO }}">
        <input type="hidden" name="variant_name[]" value="{{ $item->VARIANT_NAME }}">
        <input type="hidden" name="barcode[]" value="{{ $item->BARCODE }}" class="barcode">
        <input type="hidden" name="hs_code[]" value="{{ $item->HS_CODE }}">
        <input type="hidden" class="unit_vat{{ $item->PK_NO }}" name="unit_vat[]">
        <input type="hidden" class="unit_vat2{{ $item->PK_NO }}" name="unit_vat2[]">
    </td>
    <td></td>
    <td class="">
        @php
            $remove_item = $item->remove_item;

        @endphp
        {{-- <span class="text-danger" onclick="removeItem('{{ $remove_item }}')" style="margin-right: 20px;cursor: pointer;">
            <i class="la la-close"></i>{{ $remove_item }}
        </span> --}}
        {{ $item->VARIANT_NAME }}</br>

        <span>BC : {{ $item->BARCODE }}</span>
    </td>

    <td class="">
        <input class="form-control input-sm mb-1" placeholder="Enter invoice name" value="{{ $item->VARIANT_CUSTOMS_NAME }}" tabindex="1" name="invoice_name[]" type="text" data-validation-required-message = "This field is required" required data-row_id="{{ $item->PK_NO }}">
    </td>

    <td class="">
        <input class="form-control input-sm qty_event row_rec_qty rec_qty{{ $item->PK_NO }} no-wheel" tabindex="2" name="recieved_qty[]" type="number" required min="0" data-row_id="{{ $item->PK_NO }}">
    </td>
    <td class="">
        <input class="form-control input-sm qty_event row_flt_qty flt_qty{{ $item->PK_NO }} no-wheel" tabindex="3" name="faulty_qty[]" type="number" min="0" data-row_id="{{ $item->PK_NO }}">
    </td>
    <td class="">
        {!! Form::select('vat_class_rate[]', $vatclass_combo ?? array(), $item->VAT_AMOUNT_PERCENT ?? 20, [ 'class' => 'form-control input-sm vat_class_rate'.$item->PK_NO, 'placeholder' => '-- Select --', 'id'=>'vat_class_rate', 'required' => 'required', 'tabindex' => 5, 'onchange'=> "unitPriceEvent('$item->PK_NO')" ]) !!}
    </td>

    <td class="">
        <input onkeyup="unitPriceEvent({{ $item->PK_NO }}, 'rec_qty')"  class="form-control input-sm row_line_qty line_qty{{ $item->PK_NO }} no-wheel" tabindex="1" name="total_line_qty[]" type="number" required min="0" data-row_id="{{ $item->PK_NO }}">
    </td>
    <td class="">
        <input onkeyup="unitPriceEvent({{ $item->PK_NO }})"  class="form-control input-sm row_line_unit line_unit{{ $item->PK_NO }} no-wheel" placeholder="line unit" tabindex="1" name="line_unit[]" type="number" required id="line_unit" min="0" data-row_id="{{ $item->PK_NO }}" step="any" >
    </td>

    <td class="">
        <input onkeyup="unitPriceEvent({{ $item->PK_NO }})" class="form-control input-sm row_line_total line_total{{ $item->PK_NO }} no-wheel" placeholder="line total" tabindex="1" name="line_total[]" type="number" required min="0" data-row_id="{{ $item->PK_NO }}" step="any">
    </td>

    <td class="">
        <input class="form-control input-sm text-right unit_price_ev{{ $item->PK_NO }} no-wheel" tabindex="4" name="unit_price_ev[]" type="number" required readonly data-row_id="{{ $item->PK_NO }}">

        <input class="form-control input-sm text-right unit_price_ev2{{ $item->PK_NO }}" tabindex="4" name="unit_price_ev2[]" type="hidden" required readonly data-row_id="{{ $item->PK_NO }}">
    </td>

    <td class="">
        <input class="form-control input-sm text-right row_line_total_exvat_actual line_total_exvat_actual{{ $item->PK_NO }} no-wheel" tabindex="4" name="line_total_exvat_actual[]" type="number" required readonly data-row_id="{{ $item->PK_NO }}">

        <input class="form-control input-sm text-right row_line_total_exvat_actual2 line_total_exvat_actual2{{ $item->PK_NO }} no-wheel" tabindex="4" name="line_total_exvat_actual2[]" type="hidden" required readonly data-row_id="{{ $item->PK_NO }}">
    </td>

    <td class="">
        <input class="form-control input-sm text-right row_line_vat_actual line_vat_actual{{ $item->PK_NO }} no-wheel" tabindex="1" name="line_vat_actual[]" type="number" required readonly>

        <input class="form-control input-sm text-right row_line_vat_actual2 line_vat_actual2{{ $item->PK_NO }}" tabindex="1" name="line_vat_actual2[]" type="hidden" required readonly>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-xs btn-danger mr-1 delete-row" data-barcode="{{ $item->BARCODE }}" data-pk="{{ $item->PK_NO }}"><i class="la la-trash"></i></button>

    </td>
</tr>


