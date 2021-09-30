<?php
if ($type == 'boxed'){
    $label = 'Box Label';
}else if($type == 'shipped'){
    $label = 'Shipment Label';
}else if($type == 'shelved'){
    $label = 'Shelve Label';
}else if($type == 'booked'){
    $label = 'Order Label';
}else if($type == 'dispatched'){
    $label = 'Order Label';
}
?>
@foreach ($data as $item)
@if ($type != 'booked' && $type != 'dispatched')
    <strong>{{ $label }} :</strong> {{ $item->label }} <br>
    @if (isset($item->DESCRIPTION))
        <strong>Description :</strong> {{ $item->DESCRIPTION }} <br>
    @endif
    <strong>Product Count :</strong> {{ $item->qty }} <br> <br>
    @if ($type == 'shipped')
        <hr style="margin-top: 0;border-top: 1px solid #1e9ff2;">
        <?php
        $box_details = $item->get_box_details($item->shipment_no,$skuid,$warehouse,$invoiceid ?? null,$invoicetype ?? null);
        ?>
        @foreach ($box_details as $box)

        <strong class="ml-3">Box No :</strong> {{ $box->BOX_BARCODE }} ({{ $item->label }} - {{ $box->box_serial }}) <br>
        <strong class="ml-3">Product Count :</strong> {{ $box->boxed }} <br> <br>

        @endforeach
        <br>
    @endif
@elseif($type == 'booked')
    <strong>{{ $label }} :</strong>
    @if ($item->BOOKING_STATUS < 60)
    <a href="{{ route('admin.booking.view',$item->f_booking_no1) }}" target="_blank">BOOK-{{ $item->BOOKING_NO }}</a> <br>
    @else
    <a href="{{ route('admin.booking_to_order.book-order-view',[$item->f_booking_no1]) }}" target="_blank">ORD-{{ $item->BOOKING_NO }}</a> <br>
    @endif
    <strong>Product Count :</strong> {{ $item->order_qty }} <br>
    <strong>Customer :</strong> {{ $item->CUSTOMER_NAME ?? $item->RESELLER_NAME }} ({{ !empty($item->CUSTOMER_NAME) ? 'Customer' : 'Reseller' }}) <br> <br>
@elseif($type == 'dispatched')
    <strong>{{ $label }} :</strong>
    <a href="{{ route('admin.booking_to_order.book-order-view',[$item->f_booking_no1]) }}" target="_blank">ORD-{{ $item->BOOKING_NO }}</a> <br>
    <strong>Product Count :</strong> {{ $item->order_qty }} <br>
    <strong>Customer :</strong> {{ $item->CUSTOMER_NAME ?? $item->RESELLER_NAME }} ({{ !empty($item->CUSTOMER_NAME) ? 'Customer' : 'Reseller' }}) <br> <br>
@endif
@endforeach
