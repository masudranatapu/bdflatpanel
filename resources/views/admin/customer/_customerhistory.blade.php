@php
    $roles          = userRolePermissionArray();
    $balance        = 0;
    $html           = array();
    $cum_balance    = 0;
    $cum_order_due  = 0;
@endphp

<section id="pagination">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-sm">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right customer-info">
                                <p>{{  $data['customer']->NAME }} (CUST-{{  $data['customer']->CUSTOMER_NO }})</p>
                                <div class="customer-history-info" style="font-size: 15px;">
                                    @if(isset($data['address']))
                                    @if($data['address']->ADDRESS_LINE_1)
                                    <p>{{ $data['address']->ADDRESS_LINE_1 }}</p>
                                    @endif
                                    @if($data['address']->ADDRESS_LINE_2)
                                    <p>{{ $data['address']->ADDRESS_LINE_2 }}</p>
                                    @endif
                                    @if($data['address']->ADDRESS_LINE_3)
                                    <p>{{ $data['address']->ADDRESS_LINE_3 }}</p>
                                    @endif
                                    @if($data['address']->ADDRESS_LINE_4)
                                    <p>{{ $data['address']->ADDRESS_LINE_4 }}</p>
                                    @endif
                                    @endif

                                </div>
                                {{-- <p>Customer ID : CUST-{{  $data['customer']->CUSTOMER_NO }}</p> --}}
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-content collapse show">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive ">
                            <table class="table table-striped table-bordered table-sm" >
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 40px;">SL</th>
                                    <th class="text-center" style="width: 90px;">Date</th>
                                    <th class="text-center" style="width: 100px;">Created By</th>
                                    <th class="text-center" >Transaction</th>
                                    <th class="text-center" style="width: 80px;">Order Value</th>
                                    <th class="text-center" style="width: 80px;">Payment</th>
                                    <th class="text-center" style="width: 80px;">Balance</th>
                                    <th class="text-center" style="width: 80px;" title="Unused Payment">Unused Pay</th>
                                    <th class="text-center" style="width: 80px;">Order Due</th>
                                    <th class="text-center" style="width: 70px;">Status</th>
                                    <th class="text-center">Details</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if(isset($rows) && count($rows) > 0 )
                                        @foreach($rows as $k => $row)

<?php
$description = '';
$details = '';
$details1 = '';
$ord_amount      = '';
$pay_amount      = '';
$status     = '';
$breakdown = '';
$payment_status = '';
$refund_status = '';
$unused_pay = '';
$order_due = '';
$order_value = 0;





if($row->TYPE == 'Order Placed'){
$order_value = ($row->ORDER_PRICE - $row->ORDER_DISCOUNT) ?? 0;
$description .= '<div><h6><a href="'.route('admin.booking_to_order.book-order-view', ['id' => $row->BOOKING_PK_NO ]) .'" class="" target="_blank">ORD-'.$row->BOOKING_NO.'</a> <span class="">('. $row->TYPE.') - </span> <span class=""> '. number_format($order_value,2).'</span></h6></div>';

$order_value = $row->ORDER_PRICE;
$ord_amount .= '<div>'.number_format($order_value, 2).'</div>';
$balance += $row->ORDER_PRICE;
$cum_balance += $row->ORDER_PRICE;


if($row->IS_CANCEL == 1){
$status .= '<small class="badge badge-danger" title="Processing" data-oid="">Cancel</small>';
}else{
if($row->DISPATCH_STATUS == 40 ){
    $status .= '<small class="badge badge-success" title="Dispatched" data-oid="">Dispatched</small>';
}elseif($row->DISPATCH_STATUS == 35 ){
    $status .= '<small class="badge badge-warning" title="Partial Dispatched" data-oid="">Partial Dispatched</small>';
}else{
    $status .= '<small class="badge badge-info" title="Processing" data-oid="">Processing</small>';
}
}


if($row->allPaymentForTheOrder && count($row->allPaymentForTheOrder) > 0 ){
    $details = '';
    foreach ($row->allPaymentForTheOrder as $item) {
        $details .= '<li><span class="f-15 fw-b"><a href="'.route('admin.payment.details',[ 'id' => $item->customerPayment->bankTxn->PK_NO ]) .'" class="" target="_balank" title="Txn Date : '.date('d-m-Y',strtotime($item->customerPayment->bankTxn->TXN_DATE)).'">PAY-'. $item->customerPayment->bankTxn->CODE .'</a> - '.number_format($item->PAYMENT_AMOUNT,2).'</span></li>';
    }
    $breakdown .= '<ul class="list-unstyled">'.$details.'</ul>';
}
$order_due = number_format($row->ORDER_DUE,2);
$cum_order_due += $row->ORDER_DUE;


}elseif($row->TYPE == 'Order Return'){
$order_value = $row->RETURN_PRICE;
$ord_amount .= '<div>'.number_format($order_value, 2).'</div>';
// $balance += $row->ORDER_PRICE;
// $cum_balance += $row->ORDER_PRICE;

// $order_due = number_format($row->ORDER_DUE,2);
// $cum_order_due += $row->ORDER_DUE;
$order_value = $row->RETURN_PRICE ?? 0;
$description .= '<div><h6><a href="'.route('admin.booking_to_order.book-order-view', ['id' => $row->BOOKING_PK_NO ]) .'" class="" target="_blank">ORD-'.$row->BOOKING_NO.'</a> <span class="">('. $row->TYPE.') - </span> <span class=""> '. number_format($order_value,2).'</span></h6></div>';


}elseif($row->TYPE == 'Penalty'){
$order_value = $row->PENALTY_FEE;
$ord_amount .= '<div>'.number_format($order_value, 2).'</div>';
$balance += $row->PENALTY_FEE;
$cum_balance += $row->PENALTY_FEE;

// $order_due = number_format($row->ORDER_DUE,2);
// $cum_order_due += $row->ORDER_DUE;
$order_value = $row->PENALTY_FEE ?? 0;
$description .= '<div><h6><a href="'.route('admin.booking_to_order.book-order-view', ['id' => $row->BOOKING_PK_NO ]) .'" class="" target="_blank">ORD-'.$row->BOOKING_NO.'</a> <span class="">('. $row->TYPE.') - </span> <span class=""> '. number_format($order_value,2).'</span></h6></div>';


}elseif($row->TYPE == 'Payment'){
$order_value = $row->PAY_AMOUNT;
if($row->PAYMENT_VERIFY == 1){ $payment_status = ' text-success'; }else{$payment_status = ' text-warning';}
$description .= '<div><h6 class="'.$payment_status.'"><a href="'.route('admin.payment.details',[ 'id' => $row->TX_PK_NO ]).'" class="" target="_blank">PAY-'.$row->PAYMENT_NO.'</a><span class=""> ('.$row->TYPE.') </span><span class="">'.number_format($order_value,2).'</span></h6></div>';

$order_value = $row->PAY_AMOUNT;
$pay_amount .= '<div>'.number_format($order_value, 2).'</div>';

if($row->PAYMENT_VERIFY == 1){
$balance -= $row->PAY_AMOUNT;
}
$cum_balance -= $row->PAY_AMOUNT;
$unused_pay = number_format($row->PAYMENT_REMAINING_MR,2);

if($row->allOrderPayments && count($row->allOrderPayments) > 0 ){
    $details = '';
    foreach ($row->allOrderPayments as $item) {
        $details .= '<li><span class="f-15 fw-b"><a href="'.route('admin.booking_to_order.book-order-view', ['id' => $item->order->F_BOOKING_NO ?? '' ]) .'" class="" target="_balank" title="Order Date : '.date('d-m-Y',strtotime($item->order->booking->RECONFIRM_TIME)).'">ORD-'. $item->order->booking->BOOKING_NO .'</a> '.number_format($item->PAYMENT_AMOUNT,2).'</span></li>';
    }
    $breakdown .= '<ul class="list-unstyled">'.$details.'</ul>';
}
if(isset($row->allRefunds) && count($row->allRefunds) > 0 ){
    $details1 = '';
    foreach ($row->allRefunds as $refund) {
        if($refund->bankTxn->IS_MATCHED == 1){ $refund_status = ' text-success'; }else{$refund_status = ' text-warning';}

        $details1 .= '<li><div><h6 class="'.$refund_status.'"><a href="'.route('admin.payment.details',[ 'id' => 12 ]).'" class="" target="_blank">PAY-'.$refund->bankTxn->CODE.'</a><span class=""> (Refund) </span><span class="">'.number_format($refund->MR_AMOUNT,2).'</span></h6></div></li>';
    }
    if($details1){
        $breakdown .= '<ul class="list-unstyled">'.$details1.'</ul>';
    }
}

}elseif($row->TYPE == 'AM payment'){
$order_value = $row->PAY_AMOUNT;
if($row->PAYMENT_VERIFY == 1){$payment_status = ' text-success'; }else{  $payment_status = ' text-warning';}
// $description .= '<div><h6 class="'.$payment_status.'"><a href="'.route('admin.payment.details',[ 'id' => $row->TX_PK_NO ]).'" class="" target="_blank">PAY-'.$row->PAYMENT_NO.'</a><span class=""> ('.$row->TYPE.') </span><span class="">'.number_format($order_value,2).'</span></h6></div>';

$description .= '<div><h6 class="'.$payment_status.'"><a href="'.route('admin.booking_to_order.book-order-view',[ 'id' => $row->F_BOOKING_NO_FOR_PAYMENT_TYPE3 ]).'" class="" target="_blank" title="Payment by AM for order item returned">Partial Payment ORD-'.$row->BOOKING_NO.'</a><span class=""></span><span class=""> - '.number_format($order_value,2).'</span></h6></div>';

$order_value = $row->PAY_AMOUNT;
$pay_amount .= '<div>'.number_format($order_value, 2).'</div>';

if($row->PAYMENT_VERIFY == 1){
   // $balance -= $row->PAY_AMOUNT;
}
//$cum_balance -= $row->PAY_AMOUNT;
$unused_pay = number_format($row->PAYMENT_REMAINING_MR,2);

if($row->amPaymentForOrder ){
    $details = '';
    $item = $row->amPaymentForOrder;
        $details .= '<li><span class="f-15 fw-b"><a href="'.route('admin.booking_to_order.book-order-view', ['id' => $item->PK_NO ?? '' ]) .'" class="" target="_balank" title="Order Date : '.date('d-m-Y',strtotime($item->RECONFIRM_TIME)).'">ORD-'. $item->BOOKING_NO .'</a> '.number_format($row->PAY_AMOUNT,2).'</span></li>';

    $breakdown .= '<ul class="list-unstyled">'.$details.'</ul>';
}


}elseif($row->TYPE == 'Refund'){
$order_value = $row->PAY_AMOUNT;
if($row->PAYMENT_VERIFY == 1){ $payment_status .= 'text-success'; }else{$payment_status .= 'text-warning';}
$description .= '<div><h6 class="'.$payment_status.'"><a href="'.route('admin.payment.details',[ 'id' => $row->TX_PK_NO ]).'" class="" target="_blank">PAY-'.$row->PAYMENT_NO.'</a><span class=""> ('.$row->TYPE.') </span><span class="">'.number_format($order_value,2).'</span></h6></div>';

$order_value = $row->PAY_AMOUNT;
$pay_amount .= '<div>'.number_format($order_value, 2).'</div>';

// if($row->PAYMENT_VERIFY == 1){
$balance -= $row->PAY_AMOUNT;
// }
$cum_balance -= $row->PAY_AMOUNT;
//$unused_pay = number_format($row->PAYMENT_REMAINING_MR,2);
}



$html[$k] = '<tr><td class="">'.($k+1).'</td><td class=""><p style="margin-bottom: 0px;">'.date('d-m-Y',strtotime($row->DATE_AT)) .'</p></td><td class=""><span class="text-upercase">'.$row->ENTRY_BY_NAME.'</span></td><td>'.$description.'</td><td class="text-right">'.$ord_amount.'</td><td class="text-right">'.$pay_amount.'</td><td class="text-right"><div>'.number_format($balance, 2).'</div></td><td>'.$unused_pay.'</td><td>'.$order_due.'</td><td class="">'.$status.'</td><td>'.$breakdown.'</td></tr>';


?>



                                        @endforeach
                                        <?php
                                        rsort($html);

                                        if(count($html)> 0){
                                            for ($i=0; $i < count($html); $i++) {
                                            echo $html[$i];
                                            }
                                        }
                                        ?>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-3">Total Unused Payment (RM) : {{  number_format($data['customer']->CUM_BALANCE,2) }}</div>
                            <div class="col-md-3">Total Order Dues (RM) : {{ number_format($cum_order_due,2) }}</div>
                            <div class="col-md-3">AM Credit (RM) : </div>
                            <div class="col-md-3">Final AM Balance Position (RM) : {{ number_format($cum_balance, 2) }} </div>
                            <div class="col-md-12">
                                {{-- <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-danger">Add Payment</button>
                                    <button type="button" class="btn btn-sm btn-info">Add Order</button>
                                    <button type="button" class="btn btn-sm btn-success">Export</button>
                                </div> --}}
                                {{-- <p class="pull-right" style="color:blue;">

                                    <button type="button" class="btn btn-sm btn-success" id="balanceHistory" data-toggle="modal" data-target="#balanceHistoryModal"><i class="la la-eye"></i></button>
                                    Credit Amount (RM) : {{  number_format($data['customer']->CUM_BALANCE,2) }}
                                </p> --}}
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>
