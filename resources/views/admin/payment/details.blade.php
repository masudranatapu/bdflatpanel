@extends('admin.layout.master')

@section('Payment','open')
@section('payment_list','active')

@section('title') Payment details @endsection
@section('page-name') Payment details @endsection


@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
</li>
<li class="breadcrumb-item"><a href="{{ route('admin.invoice') }}"> Payment </a>
</li>
<li class="breadcrumb-item active">Payment details
</li>
@endsection

<!--push from page-->
@push('custom_css')

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">
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
    .table-responsive {
    overflow: visible !important
    }
    .picker__holder{max-width: 320px !important;}
    .picker__holder table td{padding: 0px !important;}
    /* .paymentEditBtn
    .paymentUpdatefrm */
    .paymentUpdatefrm{display: none; max-width: 320px !important;}

</style>
<?php
$key = 0;
$roles = userRolePermissionArray();

?>

@section('content')
<div class="card card-success min-height">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-eye text-primary"></i>Payment</h4>




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
            <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm">

                <tbody>
                    <tr>

                        <td stye="width:33%">Payment ID : {{ 'PAYID-'.$data['txn']->CODE }}</td>
                        <td stye="width:33%">Received Via : {{ $data['txn']->bank->BANK_NAME }}</td>

                        <td stye="width:33%" class="text-right">Entered By : <span style="text-transform: uppercase">{{ $data['txn']->createdBy->USERNAME ?? '' }}</span></td>
                    </tr>
                    <tr>
                        <td stye="width:33%">Deposit Amount : {{ number_format($data['txn']->AMOUNT_BUFFER,2) }}</td>
                        <td stye="width:33%">
                            <div class="paydate">Pay Date : <span>{{ date('d-m-Y',strtotime($data['txn']->TXN_DATE)) }}</span>  <button type="button" class="btn paymentEditBtn" style="padding: 0px;"><i class="la la-edit"></i></button></div>
                            <div class="paymentUpdatefrm">

                                {!! Form::open([ 'route' => 'admin.payment.updatepartial', 'method' => 'post', 'class' => 'form-horizontal', 'files'
                                    => true , 'novalidate']) !!}
                                    <input type="hidden" value="{{ $data['txn']->PK_NO }}" name="txn_pk_no" />

                                    <div class="input-group" >
                                        <input type="text" class="form-control pickadate " placeholder="payment date" value="{{ date('d-m-Y',strtotime($data['txn']->TXN_DATE)) }}" required name="payment_date">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary btn-sm payment_date_sv" type="submit" >Save</button>
                                            <button class="btn btn-warning btn-sm payment_date_close" type="button" >Close</button>
                                        </div>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </td>
                        <td stye="width:33%" class="text-right">Entered At : {{ date('d-m-Y, h:i A',strtotime($data['txn']->SS_CREATED_ON) ) }} </td>
                    </tr>

                    <tr>
                        <td stye="width:33%">Order Assigned :

                            @if($data['txn']->F_CUSTOMER_PAYMENT_NO)
                            {{ number_format($data['txn']->AMOUNT_BUFFER - $data['txn']->customerPayment->PAYMENT_REMAINING_MR,2) }}
                            @elseif($data['txn']->F_RESELLER_PAYMENT_NO)
                            {{ number_format($data['txn']->AMOUNT_BUFFER - $data['txn']->resellerPayment->PAYMENT_REMAINING_MR,2) }}
                            @endif

                        </td>
                        <td stye="width:33%">Paid By :
                            @if($data['txn']->F_CUSTOMER_PAYMENT_NO)
                                {{ $data['txn']->customerPayment->PAID_BY }}
                            @elseif($data['txn']->F_RESELLER_PAYMENT_NO)
                                {{ $data['txn']->resellerPayment->PAID_BY }}
                            @endif

                        </td>
                        <td stye="width:33%" class="text-right">Status :
                            @if($data['txn']->IS_MATCHED == 1)
                            <div class="badge badge-success " title=" VERIFIED">Verified</div>
                            @elseif($data['txn']->IS_MATCHED == 0)
                            <div class="badge badge-warning " title="NOT VERIFIED">Not Verified</div>
                            @endif

                            </td>

                    </tr>

                    <tr>
                        <td stye="width:33%">Credit Amount :
                            @if($data['txn']->F_CUSTOMER_PAYMENT_NO)
                            {{ number_format($data['txn']->customerPayment->PAYMENT_REMAINING_MR,2) }}
                            @elseif($data['txn']->F_RESELLER_PAYMENT_NO)
                            {{ number_format($data['txn']->resellerPayment->PAYMENT_REMAINING_MR,2) }}
                            @endif

                        </td>


                        <td stye="width:33%" >Tx Ref :
                            @if($data['txn']->F_CUSTOMER_PAYMENT_NO)
                                {{ $data['txn']->customerPayment->SLIP_NUMBER }}
                            @elseif($data['txn']->F_RESELLER_PAYMENT_NO)
                                {{ $data['txn']->resellerPayment->SLIP_NUMBER }}
                            @endif

                            </td>

                        <td stye="width:33%" class="text-right">
                            @if($data['txn']->F_CUSTOMER_PAYMENT_NO)
                            Customer :
                            {{ $data['txn']->customerPayment->customer->NAME }} ({{ $data['txn']->customerPayment->customer->CUSTOMER_NO }})
                            @elseif($data['txn']->F_RESELLER_PAYMENT_NO)
                            Reseller  :
                            {{ $data['txn']->resellerPayment->reseller->NAME }} ({{ $data['txn']->resellerPayment->reseller->RESELLER_NO }})
                            @endif


                        </td>

                    </tr>





                </tbody>
                </table>
            </div>

            <hr>
            <div class="table-responsive">
                <table class="table table-striped table-bordered  table-sm" >
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Order Number</th>
                            <th>Order Note</th>
                            <th>Order Date</th>
                            <th>Original amount</th>
                            <th>Penalty Amount</th>
                            <th>Due Amount</th>
                            <th>Payment</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($data['order_payments']) && count($data['order_payments']) > 0 )
                            @foreach($data['order_payments'] as $key =>  $row)
                                <tr title="order_pk : {{ $row->order->PK_NO }}">
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        <a href="{{ route('admin.booking_to_order.book-order', [ 'id' => $row->order->booking->PK_NO ]) }}?type=view" class="" title="VIEW ORDER">{{ 'ORD-'.$row->order->booking->BOOKING_NO ?? '' }}</a>

                                    </td>
                                    <td>
                                        {{ $row->order->booking->BOOKING_NOTES ?? '' }}
                                    </td>
                                    <td>
                                        {{ date('d-m-Y h:i A',strtotime($row->order->booking->RECONFIRM_TIME)) }}
                                    </td>
                                    <td>
                                        {{ number_format($row->order->booking->TOTAL_PRICE - $row->order->booking->DISCOUNT,2) }}
                                    </td>
                                    <td>
                                        0
                                    </td>
                                    <td>
                                        {{ number_format(($row->order->booking->TOTAL_PRICE - $row->order->booking->DISCOUNT) - $row->order->ORDER_BUFFER_TOPUP,2 )  }}
                                    </td>
                                    <td>
                                        {{ number_format($row->PAYMENT_AMOUNT,2)  }}
                                    </td>
                                    <td class="text-center">
                                        @if(hasAccessAbility('delete_payment', $roles))
                                            <a href="{{route('admin.orderpayment.delete',$row->PK_NO)}}"
                                                onclick="return confirm('Are you sure you want to delete the payment ?')" class="btn btn-xs btn-danger mr-1" title="DELETE">
                                            <i class="la la-trash"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                        @else
                                <tr>
                                    <td colspan="9" class="text-center">NO DATA</td>
                                </tr>
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


<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>

<script>

    $('.pickadate').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
    });

    $(document).on('click','.paymentEditBtn', function(e){

        $('.paydate').hide();
        $('.paymentUpdatefrm').show();

    })

    $(document).on('click','.payment_date_close',function(e){
        $('.paymentUpdatefrm').hide();
        $('.paydate').show();

    })

    // .paymentEditBtn



</script>


@endpush('custom_js')
