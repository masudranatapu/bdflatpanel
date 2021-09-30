<?php
if ($type == 'boxed'){
    $label = 'Box Label';
}else if($type == 'shipped'){
    $label = 'Shipment Label';
}else if($type == 'shelved'){
    $label = 'Shelve Label';
}
?>
@foreach ($data as $item)

<strong class="ml-3">Box No :</strong>{{ $item->BOX_BARCODE }} - {{ $item->BOX_SERIAL ?? 0 }} <br>
<strong class="ml-3">Product Count :</strong> {{ $item->qty ?? 0 }} <br> <br>

@endforeach

