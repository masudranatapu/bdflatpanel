@extends('admin.layout.master')

@section('view_bank_collection','active')

@section('title') @lang('paymentc.new_page_title') @endsection
@section('page-name') @lang('paymentc.list_page_sub_title') @endsection
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush


@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
</li>
<li class="breadcrumb-item"><a href="{{ route('admin.invoice') }}"> Payment </a>
</li>
<li class="breadcrumb-item active">Payment List
</li>
@endsection

<!--push from page-->
@push('custom_css')

@endpush('custom_css')
<style>
    #scrollable-dropdown-menu .tt-menu {
        max-height: 260px;
        overflow-y: auto;
        width: 100%;
        border: 1px solid #333;
        border-radius: 5px;

    }

    .twitter-typeahead {
        display: block !important;
    }
    #indextable td{vertical-align: middle}

/* .all_cod{color: #fff !important;} */
</style>
<?php
$key = 0;
$roles = userRolePermissionArray();
?>

@section('content')
<div class="card card-success min-height">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-eye text-primary" style="padding-right: 5px; "></i>Payment of <span style="color: #FFA702">{{ $data['name'] ?? ''}}</span></h4>

        @if($errors->any())
        {{ implode($errors->all(':message')) }}
        @endif
        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                <li><a data-action="close"><i class="ft-x"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse show">
        <div class="card-body">
            <div class="table-responsive p-1">
            <table class="table table-striped table-bordered table-sm" id="indextable">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">Sl.</th>
                        <th class="" style="width: 80px;">Entry Date</th>
                        <th class="" style="width:80px;">Entry By</th>
                        {{-- <th style="width: 70px;">Pay Date</th> --}}
                        <th>Transaction</th>
                        {{-- <th style="width: 100px;" >Ref</th> --}}
                        {{-- <th style="width: 100px;" class="text-center">Image</th> --}}
                        <th style="width: 80px;">Tx Mode</th>
                        <th>Party</th>
                        <th style="width: 80px;">Recieve</th>
                        <th style="width: 80px;">Payment</th>
                        <th style="width: 80px;">Balance</th>
                        <th style="width: 60px;" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($data['rows']) && count($data['rows']) > 0 )
                        @foreach($data['rows'] as $key => $row )
                        @php
                            if($row->TYPE == 'CUSTOMER'){
                                $type = 'customer';
                            }
                            if($row->TYPE == 'RESELLER'){
                                $type = 'reseller';
                            }

                        @endphp

                            {{-- <tr @if($row->PAYMENT_CONFIRMED_STATUS == 0 )  @endif>
                                <td class="text-center">{{ $key+1 }}</td>
                                <td style="width: 80px;" class="text-center">
                                   <div style="font-size:12px; font-weight: 600;">
                                    {{ date('d-m-Y',strtotime($row->SS_CREATED_ON)) }}
                                    </div>

                                    <div style="font-size:12px;">
                                    {{ date('h:i A',strtotime($row->SS_CREATED_ON)) }}
                                   </div>
                                   @if( ($row->IS_MATCHED == 1) && ($row->MATCHED_ON))
                                   <div style="font-size:12px; font-weight: 600; border-top: 1px solid #000;">
                                    {{ date('d-m-Y',strtotime($row->MATCHED_ON)) }}
                                    </div>

                                    <div style="font-size:12px;">
                                    {{ date('h:i A',strtotime($row->MATCHED_ON)) }}
                                   </div>
                                   @endif
                                </td>
                                <td style="width:100px; text-transform: uppercase;">{{ $row->entryBy->USERNAME ?? '' }}</td>
                                <td>{{ date('d-m-Y', strtotime($row->PAYMENT_DATE) ) }}</td>
                                <td class="text-uppercase">
                                    <a href="" class="font-bold">
                                        {{ $row->CUSTOMER_NAME ?? ''}}
                                    </a>
                                </td>
                                <td class="text-uppercase">
                                     {{ $row->PAID_BY  ?? $row->CUSTOMER_NAME }} </td>
                                <td style="width:100px;">
                                    {{ $row->SLIP_NUMBER }} <br>
                                    <a href="{{ route('admin.payment.details',['id' =>  $row->PK_NO ]) }}" class="font-bold">{{ 'PAYID-'.$row->CODE ?? '' }}</a>
                                </td>
                                <td class="text-center" >
                                    @if($row->ATTACHMENT_PATH)
                                    <a href="{{ asset($row->ATTACHMENT_PATH) }}" target="_blank"  >
                                        <img src="{{ asset($row->ATTACHMENT_PATH) }}"  style="width: 60px;" >
                                    </a>
                                    @endif
                                </td>
                                <td >
                                    {{ $row->PAYMENT_BANK_NAME }}
                                    <br>
                                    <span style="font-size:10px;">{{ $row->PAYMENT_ACCOUNT_NAME }}</span>
                                </td>
                                <td class="text-right">COD-RTC</td>
                                <td class="text-right">{{ number_format($row->MR_AMOUNT,2) }}</td>
                                <td class="text-right">----</td>
                                <td class="text-right">----</td>

                                <td class="text-center" style="width: 5%;">
                                    @if(hasAccessAbility('view_payment', $roles))
                                    <a href="{{ route('admin.payment.details',['id' =>  $row->PK_NO ]) }}" class="btn btn-xs btn-success mr-05" title="VIEW PAYMENT"><i class="la la-eye"></i></a>
                                    @endif
                                    @if(hasAccessAbility('delete_payment', $roles))
                                    @if($row->IS_COD == 0)
                                    <a href="{{ route('admin.payment.delete',['id' =>  $row->PK_NO]) }}" class="btn btn-xs btn-danger mr-05" title="DELETE PAYMENT" onclick="return confirm('Are you sure want to delete this?');"><i class="la la-trash"></i></a>
                                    @endif
                                    @endif

                                </td>
                            </tr> --}}


                        @endforeach
                    @endif
                    {{-- @if (isset($data['inter_from_ixfer']) &&count($data['inter_from_ixfer']) > 0)
                    @foreach($data['inter_from_ixfer'] as $key => $row )
                    <tr>
                        <td class="text-center">{{ $key+=1 }}</td>
                        <td>{{ date('d-m-Y', strtotime($row->SS_CREATED_ON) ) }}</td>
                        <td>{{ $row->entryBy->USERNAME }}</td>
                        <td>----</td>
                        <td>----</td>
                        <td>----</td>
                        <td>----</td>
                        <td>----</td>
                        <td>{{ $row->ACC_CUSTOMER_PAYMENT_METHOD }}</td>
                        <td>----</td>
                        <td>----</td>
                        <td class="text-right">{{ number_format($row->ENTERED_MR_AMOUNT,2) }}</td>
                        <td>----</td>
                        <td class="text-center" style="width: 5%;">
                        <a href="{{ route('admin.account_to_bank.details',['id' =>  $row->PK_NO ]) }}" class="btn btn-xs btn-success mr-05" title="VIEW"><i class="la la-eye"></i></a>
                        </td>
                    </tr>
                    @endforeach
                    @endif


                    @if (isset($data['inter_to_ixfer']) && count($data['inter_to_ixfer']) > 0)
                    @foreach($data['inter_to_ixfer'] as $key2 => $row )
                    @php
                     $key2 = $key ?? 0
                     @endphp
                    <tr>
                        <td class="text-center">{{ $key2+=1 }}</td>
                        <td>{{ date('d-m-Y', strtotime($row->SS_CREATED_ON) ) }}</td>
                        <td>{{ $row->entryBy->USERNAME }}</td>
                        <td>----</td>
                        <td>----</td>
                        <td>----</td>
                        <td>----</td>
                        <td>----</td>
                        <td>{{ $row->ACC_CUSTOMER_PAYMENT_METHOD }}</td>
                        <td>----</td>
                        <td class="text-right">{{ number_format($row->ENTERED_MR_AMOUNT,2) }}</td>
                        <td>----</td>
                        <td>----</td>
                        <td class="text-center" style="width: 5%;">
                        <a href="{{ route('admin.account_to_bank.details',['id' =>  $row->PK_NO ]) }}" class="btn btn-xs btn-success mr-05" title="VIEW"><i class="la la-eye"></i></a>
                        </td>
                    </tr>
                    @endforeach
                    @endif

                    @if (isset($data['party_exfer']) && count($data['party_exfer']) > 0)
                    @foreach($data['party_exfer'] as $key3 => $row )
                    @php
                    $key3 = $key2 ?? 0
                    @endphp
                    <tr>
                        <td class="text-center">{{ $key3+=1 }}</td>
                        <td>{{ date('d-m-Y', strtotime($row->SS_CREATED_ON) ) }}</td>
                        <td>{{ $row->entryBy->USERNAME }}</td>
                        <td>----</td>
                        <td>----</td>
                        <td>----</td>
                        <td>----</td>
                        <td>----</td>
                        <td>{{ $row->ACC_PARTY_PAYMENT_METHOD }}</td>
                        <td>----</td>
                        <td class="text-right">{{ $row->IS_IN == 1 ? number_format($row->ENTERED_MR_AMOUNT,2) : '----' }}</td>
                        <td class="text-right">{{ $row->IS_IN == 0 ? number_format($row->ENTERED_MR_AMOUNT,2) : '----' }}</td>
                        <td>----</td>
                        <td class="text-center" style="width: 5%;">
                        <a href="{{ route('admin.account_to_other.details',['id' =>  $row->PK_NO ]) }}" class="btn btn-xs btn-success mr-05" title="VIEW"><i class="la la-eye"></i></a>
                        </td>
                    </tr>
                    @endforeach
                    @endif --}}



                    @if(isset($result) && count($result) > 0 )
                    @php
                        $balance        = 0;
                        $html           = array();
                    @endphp
                    @foreach($result as $k => $row)

@php
$description = '';
$details = '';
$recieved_amount      = '';
$pay_amount      = '';
$breakdown = '';
$payment_status = '';
$order_value = 0;
$route = '';
$status = '';
$customer = '';
$head = '';
$payment_method = isset($row->PAYMENT_METHOD) ? ' '.$row->PAYMENT_METHOD : '';


if($row->TYPE == 'Customer' || $row->TYPE == 'Reseller'){
    $status = 'COD';
    $order_value = ($row->ORDER_PRICE - $row->ORDER_DISCOUNT) ?? 0;
    $description .= '<div><h4><a href="'.route('admin.booking_to_order.book-order-view', ['id' => $row->BOOKING_PK_NO ]) .'" class="link" target="_blank">ORD-'.$row->BOOKING_NO.'</a> <span class="text-danger">('. $row->TYPE.' Order) - </span> <span class="text-success">(RM) '. number_format($order_value,2).'</span></h4></div>';
    $description .= '<div><h4><a href="'.route('admin.payment.details', ['id' => $row->TX_PK_NO ]) .'" class="link" target="_blank">PAY-'.$row->PAYMENT_NO.'</a> <span class="text-danger">(Agent Recived) - </span> <span class="text-success">(RM) '. number_format($row->PAY_AMOUNT,2).'</span></h4></div>';
}else{
    $order_value = $row->PAY_AMOUNT;

    if($row->PAYMENT_VERIFY == 1){
        $payment_status .= '<small class="badge badge-success" title="Payment already verified" >Verified</small>';
    }else{
        $payment_status .= '<small class="badge badge-warning" title="Payment not verified" >Pending</small>';
    }
    if($row->TYPE == 'party_payment'){
        $status = 'EX';
        $route .= "admin.account_to_other.details";
    }else{
        $status = 'IX';
        $route .= "admin.account_to_bank.details";
    }

    $description .= '<div><h5><a href="'.route($route,[ 'id' => $row->XFER_PK ]).'" class="link" target="_blank">PAY-'.$row->PAYMENT_NO.'</a><span class="text-danger"> (Payment) </span><span class="text-success">(RM) '.number_format($order_value,2).'</span><span>&nbsp;&nbsp;&nbsp;'.$payment_status.'</span></h5></div>';
}

if($row->TYPE == 'Customer' || $row->TYPE == 'Reseller'){
    $head .= '<a href="'.route('admin.customer.view',[ 'id' => $row->CUS_PK ]).'" class="link" target="_blank">'.$row->CUSTOMER_NAME.'</a>';
}

$head .= $row->PARTY_HEAD;
if($row->TYPE == 'to_inernal' || $row->TYPE == 'from_inernal'){
    $head .= $row->BANK_ACC_NAME.' ('.$row->BANK_NAME.')';
}

if($row->TYPE == 'Customer' || $row->TYPE == 'Reseller'){
    $order_value = $row->PAY_AMOUNT;
    $recieved_amount .= '<div>'.number_format($order_value, 2).'</div>';
}elseif($row->TYPE == 'to_inernal' || ($row->TYPE == 'party_payment' && $row->IS_IN == 1)){
    $order_value = $row->PAY_AMOUNT;
    $recieved_amount .= '<div>'.number_format($order_value, 2).'</div>';
}

if($row->TYPE == 'from_inernal' || ($row->TYPE == 'party_payment' && $row->IS_IN == 0)){
    $order_value = $row->PAY_AMOUNT;
    $pay_amount .= '<div>'.number_format($order_value, 2).'</div>';
}

if($row->TYPE == 'Customer' || $row->TYPE == 'Reseller'){
    $balance += ($row->PAY_AMOUNT);
}elseif($row->TYPE == 'to_inernal' || ($row->TYPE == 'party_payment' && $row->IS_IN == 1)){
    if($row->PAYMENT_VERIFY == 1){
        $balance += $row->PAY_AMOUNT;
    }
}elseif($row->TYPE == 'from_inernal' || ($row->TYPE == 'party_payment' && $row->IS_IN == 0)){
    if($row->PAYMENT_VERIFY == 1){
        $balance -= $row->PAY_AMOUNT;
    }
}
$action = '<a href="'.route('admin.payment.details',['id' =>  $row->TX_PK_NO ]).'" class="btn btn-xs btn-success mr-05" title="VIEW"><i class="la la-eye"></i></a>';

$html[$k] = '<tr><td class="">'.($k+1).'</td><td class=""><p style="margin-bottom: 0px; font-weight:600;">'.date('d-m-Y',strtotime($row->DATE_AT)) .'</p></td><td class=""><span class="text-upercase">'.$row->ENTRY_BY_NAME.'</span></td><td>'.$description.'</td><td>'.$status.$payment_method.'</td><td>'.$head.'</td><td class="text-right">'.$recieved_amount.'</td><td class="text-right">'.$pay_amount.'</td><td class="text-right"><div>'.number_format($balance, 2).'</div></td><td>'.$action.'</td></tr>';

@endphp
                    @endforeach
                    @php
                    // rsort($html);

                    if(count($html)> 0){
                        for ($i=0; $i < count($html); $i++) {
                        echo $html[$i];
                        }
                    }
                    @endphp
                    @endif

                </tbody>
                </table>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
        </div>
    </div>

</div>
@endsection

<!--push from page-->
@push('custom_js')
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<script>
    $('#indextable').DataTable({
    paging: true,
    pageLength: 25,
    lengthChange: true,
    searching: true,
    ordering: true,
    info: true,
    autoWidth: false,
    "order": [[ 0, "desc" ]],
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    })
</script>
@endpush('custom_js')
