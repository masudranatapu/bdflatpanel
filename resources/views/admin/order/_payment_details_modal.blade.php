@foreach ($data as $item)
<strong>Payment ID :</strong>
<a href="{{ route('admin.payment.details',$item->PK_NO) }}" target="_blank">PAYID-{{ $item->CODE }}</a> <br>
<strong>Amount :</strong> {{ $item->PAYMENT_AMOUNT }} <br><br>
@endforeach
