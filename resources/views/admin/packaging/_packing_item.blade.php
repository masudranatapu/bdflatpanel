@if($data['rows'] && count($data['rows']) > 0 )
@foreach($data['rows'] as $key => $row)
<?php
$total_price = 0;
$total_qty = 0;

$cr_name = Route::currentRouteName();
$shipping_box_size_arr   =  Config::get('static_array.shipping_box_size') ?? array();

// print_r($shipping_box_size_arr);
// die();

?>
<form class="box_updateFrm {{$row->F_BOX_NO ?? $row->PK_NO}}" action="{{route('admin.packagingitemupdate')}}" data-box_no="{{$row->F_BOX_NO}}" method="post">
    @csrf

    <input type="hidden" name="shipment_no" value="{{$row->PK_NO}}">
    <input type="hidden" name="box_serial_no" value="{{$row->BOX_SERIAL_NO}}">

<?php
/*For new box , which has no F_BO_NO */

$row->F_BOX_NO = $row->F_BOX_NO ?? $row->PK_NO;

?>

    <table class="table table-hover table-bordered table-striped box-table" width="100%" style="border-collapse: collapse; margin-bottom: 15px;" cellpadding="2" cellspacing="0" data-packchd_pk_no="" id="box_{{$row->F_BOX_NO}}">
      <thead>
        @if($cr_name == 'admin.packaging.end')
        @php
            $box_size = 'Other';

            if($row->WIDTH_CM == '46' && $row->LENGTH_CM == '46' && $row->HEIGHT_CM == '51'){
                $box_size = 'Midi';
            }elseif($row->WIDTH_CM == '46' && $row->LENGTH_CM == '46' && $row->HEIGHT_CM == '78'){
                $box_size = 'Maxi';
            }

        @endphp
            <tr>
                <th colspan="7" class="text-center">
                    <span>BOX No - {{$row->BOX_SERIAL_NO}} ({{$box_size}})</span>
                </th>
            </tr>
        @else
            <tr style="background-color: #e3e3e3; border-bottom: 1px solid #ffffff;">
              <th>

                <input type="hidden" id="box_{{$row->F_BOX_NO}}" name="box" value="{{$row->F_BOX_NO}}" >
                <select style="width: 100px; padding: 2px 2px; float: left;" title="Box Type" class="box_type box_type_{{$row->F_BOX_NO}}" data-box_no="{{$row->F_BOX_NO}}" disabled="true">
                    @if($shipping_box_size_arr && count($shipping_box_size_arr) > 0 )
                    @foreach($shipping_box_size_arr as $key => $box_size)
                    <option value="{{$key}}"
                    data-width_cm="{{$box_size['WIDTH_CM']}}"
                    data-length_cm="{{$box_size['LENGTH_CM']}}"
                    data-height_cm="{{$box_size['HEIGHT_CM']}}">{{$key}}</option>

                    @endforeach
                    @endif
                </select>
            </th>
            <th><span style="margin-left: 20px;">BOX No - {{$row->BOX_SERIAL_NO}} </span></th>
            <th colspan="2" class="text-right">
                (CM)
                <input type="text" id="box_{{$row->F_BOX_NO}}_width" class="box_w_{{$row->F_BOX_NO}} box_size_{{$row->F_BOX_NO}}" autocomplete="off" disabled="disabled" name="box_width" value="{{$row->WIDTH_CM}}" style="width: 40px; text-align: center; padding:4px 4px;">
                <input type="text" id="box_{{$row->F_BOX_NO}}_length" class="box_l_{{$row->F_BOX_NO}} box_size_{{$row->F_BOX_NO}}" autocomplete="off" disabled="disabled" name="box_length" value="{{$row->LENGTH_CM}}" style="width: 40px; text-align: center; padding:4px 4px;">
                <input type="text" id="box_{{$row->F_BOX_NO}}_height" class="box_h_{{$row->F_BOX_NO}} box_size_{{$row->F_BOX_NO}}" autocomplete="off" disabled="disabled" name="box_height" value="{{$row->HEIGHT_CM}}" style="width: 40px; text-align: center; padding:4px 4px;">
            </th>
            <th>
                <button type="button" class="btn btn-xs btn-info unlock_box unlock_box_{{$row->F_BOX_NO}}" style="padding: 4px 4px; margin-bottom: 0px;" title="Unlcok For Edit" data-box_no="{{$row->F_BOX_NO}}"><i class="la la-unlock" aria-hidden="true"></i></button>

                <button type="submit" class="btn btn-xs btn-success lock_box lock_box_{{$row->F_BOX_NO}}" style="padding: 4px 4px; margin-bottom: 0px; display: none;" title="Save And Lcok The Box After Edit" data-box_no="{{$row->F_BOX_NO}}"><i class="la la-lock" aria-hidden="true"></i></button>

            </th>
            <th>
                <select style="width: 75px; padding: 2px 2px; float: right;" title="Bulk Action" class="bulk_action bulk_action_{{$row->F_BOX_NO}}" data-box_no="{{$row->F_BOX_NO}}" name="bulk_action">
                    <option value="0" data-box_no="{{$row->F_BOX_NO}}">Select</option>
                    <option value="delete" data-box_no="{{$row->F_BOX_NO}}" data-url="{{route('admin.packingitem.delete')}}">Delete</option>

                </select>
            </th>

        </tr>
        @endif

        <tr class="bg-blue htr" style="background-color: #1e9ff2; color: #fff;">
            <th  width="80">HS Code</th>
            <th width="80">Category</th>
            <th>Product Name</th>
            <th width="50">QTY</th>
            <th width="50">U.Price</th>
            <th width="100">TOTAL</th>
            <th  style="width:30px;">Ac.</th>
        </tr>
    </thead>

<tbody>
  @if($data['packing_list'] && count($data['packing_list']) > 0 )
  @foreach($data['packing_list'] as $j => $crow )
  @if($crow->BOX_SERIAL_NO == $row->BOX_SERIAL_NO)

<?php
    $total_price    += $crow->TOTAL_PRICE;
    $total_qty      += $crow->QTY;

?>
  <tr id="{{$crow->PK_NO}}">
    <td width="80" >
        {{$crow->HS_CODE}}
        <input type="hidden" class="box_{{$row->F_BOX_NO}}_item_id" name="box_item_id[]" value="{{$crow->PK_NO}}">

    </td>
    <td width="80">
        <strong><input type="text" name="subcategory_name[]" value="{{$crow->SUBCATEGORY_NAME}}" class="form-control item-sact  box_{{$row->F_BOX_NO}}_scat_name" readonly="readonly"></strong>


    </td>
    <td>
         <input type="text" name="prc_inv_name[]" value="{{$crow->PRC_INV_NAME}}" class="form-control  item-name  box_{{$row->F_BOX_NO}}_prc_name" readonly="readonly">
    </td>
    <td width="50">
        <input class="form-control text-right item-qty  box_{{$row->F_BOX_NO}}_qnty" readonly="readonly" type="text" name="box_qnty[]" value="{{$crow->QTY}}" autocomplete="off">
    </td>
    <td width="50">
        <input class="form-control text-right item-unit-price  box_{{$row->F_BOX_NO}}_price" readonly="readonly" type="text" name="box_price[]" value="{{number_format($crow->UNIT_PRICE,2)}}" autocomplete="off">
    </td>
    <td width="100">
        <input class="form-control text-right item-amt box_{{$row->F_BOX_NO}}_amount" type="text" name="box_amount[]" value="{{number_format($crow->TOTAL_PRICE,2)}}">
    </td>
    <td style="width: 20" class="text-center">
        <label class="cp">
            <input type="checkbox" name="list_id" class="cp list_id_{{$crow->PK_NO}}" value="{{$crow->PK_NO}}">
        </label>
    </td>

</tr>
@endif
@endforeach
@endif

</tbody>
    <tfoot>
        <tr style="background-color: #e3e3e3;">
            <td><strong>Weight (kg)</strong></td>
            <td>
                <input type="hidden" name="total_record_box_{{$row->F_BOX_NO}}" value="29" class="total_record_box">
                <input type="text" class="form-control pull-left box_weight" name="box_weight" value="{{$row->WEIGHT_KG}}" id="box_weight_{{$row->F_BOX_NO}}" style="width: 100px !important" readonly="readonly">
            </td>
            <td><strong class="pull-right">Sub Total = </strong></td>
            <td align="right" class="padding-right15"><strong id="box_{{$row->F_BOX_NO}}_total_qnty">{{$total_qty ?? 0 }}</strong></td>
            <td align="right"></td>
            <td align="right" class="padding-right15"><strong id="box_{{$row->F_BOX_NO}}_total_amount">{{ number_format($total_price,2) }}</strong></td>
            <td></td>
            <tr style="background-color: #e3e3e3;">
                <td colspan="7">
                    <input type="text" value="{{$row->INVOICE_DETAILS}}"  class="form-control box_{{$row->F_BOX_NO}}_invoice_details" readonly="readonly" name="invoice_details"/>
                </td>
            </tr>
        </tr>

    </tfoot>
</table>

</form>
@endforeach
@endif
