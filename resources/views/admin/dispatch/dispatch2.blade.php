@extends('admin.layout.master')

@section('Order Management','open')
@section('list_order','active')
<?php
$booking                     = $data['booking'];
$order                       = $data['booking']->getOrder;
$booking_details             = $data['booking']->booking_details;
$order_content_types         = Config::get('static_array.order_content_types') ?? array();
$get_parcel_sizes            = Config::get('static_array.get_parcel_sizes') ?? array();

//$dispatchInfo = $data['dispatchInfo'];

$consignment = $order->consignment;

// echo '<pre>';
// echo '======================<br>';
// print_r($consignment);
// echo '<br>======================';
// exit();



// echo '<pre>';
// echo '======================<br>';
// print_r();
// echo '<br>======================';
// exit();


?>
@section('title')
    Order | Dispatch
@endsection
@section('page-name')
    Dispatch Order
@endsection
@section('breadcrumb')


@endsection
<!--push from page-->
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css')}}">
<style>
    #scrollable-dropdown-menu .tt-menu {
      max-height: 260px;
      overflow-y: auto;
      width: 100%;
      border: 1px solid #333;
      border-radius: 5px;

    }
    .twitter-typeahead{
        display: block !important;
    }

    address h6, .label1{
    font-weight: 400;
    line-height: 1;
    color: #081510;
    font-size: 14px;
    }
    .label2{
        font-weight: 600;
        line-height: 1.1;
        font-size: 16px;
        color: #444;
    }
    #process_data_table td{vertical-align: middle;}
</style>
@endpush('custom_css')
@section('content')
<?php
$due = ($booking->TOTAL_PRICE - $booking->DISCOUNT) - $order->ORDER_BUFFER_TOPUP;
//vError($errors);
?>
<div class="" id="alert">
  @if(Session::has('error'))
    <div class="alert alert-warning">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Warning! </strong> {{ Session::get('error') }}.
    </div>
    @endif
    @if(Session::has('success'))
    <div class="alert alert-success">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Success! </strong> {{ Session::get('success') }}.
    </div>
    @endif
</div>

{!! Form::open([ 'route' => ['admin.order.dispatchstore','id' => $booking->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}

{{-- {!! Form::open([ 'route' => ['admin.order.dispatchCourier','id' => $booking->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!} --}}
<input type="hidden" name="booking_no" value="{{ $data['booking']->PK_NO }}" />


    <div class="card">
        <div class="card-content collapse show">
            <div class="card-body">
                @if($order->DISPATCH_STATUS == 40 )
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert bg-danger alert-dismissible mb-2 text-center" role="alert">
                            <strong>DISPATCH! </strong> The order has been dispatched.
                        </div>
                    </div>
                </div>
                @else
                    @if($order->IS_SELF_PICKUP == 1)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert bg-danger alert-dismissible mb-2 text-center" role="alert">
                                @if($due > 0 )
                                <strong>Payment Due! </strong> Please collect RM {{ number_format($due,2) }} before handover.
                                @else
                                <strong>RTC! </strong> Please handover the order to the customer.
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>

                                        @if($booking->IS_RESELLER == 1)
                                        <h3><span class="label1">Reseller : </span> <span class="label2">{{ $booking->RESELLER_NAME }}  (RES -{{ $booking->getReseller->RESELLER_NO ?? '' }} )</span></h3>
                                        @else
                                        <h3><span class="label1">Customer : </span> <span class="label2">{{ $booking->CUSTOMER_NAME }}  (CUST-{{ $booking->getCustomer->CUSTOMER_NO ?? '' }} )</span></h3>
                                        @endif
                                    </td>
                                    <td><h3><span class="label1">Entry By : </span>  <span class="label2">{{ $booking->createdBy->USERNAME }}</span> </h3></td>
                                    <td><h3><span class="label1">Order ID : </span>  <span class="label2">ORD-{{ $booking->BOOKING_NO }}</span> </h3></td>
                                </tr>
                                <tr>
                                    <td><h3><span class="label1">Sales Agent : </span>  <span class="label2">{{ $booking->BOOKING_SALES_AGENT_NAME }}</span> </h3></td>
                                    <td><h3><span class="label1">Entry At : </span><span class="label2">{{ date('d-m-Y h:i A',strtotime($booking->BOOKING_TIME)) }}</span> </h3></td>
                                    <td><h3><span class="label1">Order Date : </span>  <span class="label2">{{ date('d-m-Y',strtotime($booking->RECONFIRM_TIME)) }}</span> </h3></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($order->IS_SELF_PICKUP == 0)
                <div class="row">
                    <div class="col-sm-6">
                        <address>
                            <h4 class=""><u>Send From : </u></h4>
                            <h5 class="label2">{{  $order->FROM_NAME ?? '' }}</h5>
                            @if($order->FROM_ADDRESS_LINE_1)
                                <h6>{{ $order->FROM_ADDRESS_LINE_1 }}</h6>
                            @endif
                            @if($order->FROM_ADDRESS_LINE_2)
                                <h6>{{ $order->FROM_ADDRESS_LINE_2 }}</h6>
                            @endif
                            @if($order->FROM_ADDRESS_LINE_3)
                                <h6>{{ $order->FROM_ADDRESS_LINE_3 }}</h6>
                            @endif
                            @if($order->FROM_ADDRESS_LINE_4)
                                <h6>{{ $order->FROM_ADDRESS_LINE_4 }}</h6>
                            @endif
                            @if($order->FROM_STATE)
                                <h6>{{ $order->FROM_STATE }}</h6>
                            @endif

                            @if($order->FROM_CITY)
                                <h6>{{ $order->FROM_CITY}}@if($order->FROM_POSTCODE)<span>, {{ $order->FROM_POSTCODE }}</span> @endif </h6>
                            @endif


                            @if($order->FROM_COUNTRY)
                                <h6>{{ $order->FROM_COUNTRY }}</h6>
                            @endif
                            @if($order->FROM_MOBILE)
                            <h6><b>Phone : {{ $order->from_country->DIAL_CODE}}{{$order->FROM_MOBILE }}</b></h6>
                            @endif
                        </address>
                    </div>
                    <div class="col-sm-6">
                        <address>
                            <h4 class=""><u>Send To : </u></h4>
                            <h5 class="label2">{{  $order->DELIVERY_NAME ?? '' }}</h5>
                            @if($order->DELIVERY_ADDRESS_LINE_1)
                                <h6>{{ $order->DELIVERY_ADDRESS_LINE_1 }}</h6>
                            @endif
                            @if($order->DELIVERY_ADDRESS_LINE_2)
                                <h6>{{ $order->DELIVERY_ADDRESS_LINE_2 }}</h6>
                            @endif
                            @if($order->DELIVERY_ADDRESS_LINE_3)
                                <h6>{{ $order->DELIVERY_ADDRESS_LINE_3 }}</h6>
                            @endif
                            @if($order->DELIVERY_ADDRESS_LINE_4)
                                <h6>{{ $order->DELIVERY_ADDRESS_LINE_4 }}</h6>
                            @endif
                            @if($order->DELIVERY_STATE)
                                <h6>{{ $order->DELIVERY_STATE }}</h6>
                            @endif

                            @if($order->DELIVERY_CITY)
                                <h6>{{ $order->DELIVERY_CITY}}@if($order->DELIVERY_POSTCODE)<span>, {{ $order->DELIVERY_POSTCODE }}</span> @endif </h6>
                            @endif


                            @if($order->DELIVERY_COUNTRY)
                                <h6>{{ $order->DELIVERY_COUNTRY }}</h6>
                            @endif
                            @if($order->DELIVERY_MOBILE)
                            <h6><b>Phone : {{ $order->to_country->DIAL_CODE}}{{ $order->DELIVERY_MOBILE }}</b></h6>
                            @endif

                        </address>
                    </div>
                </div>
                <hr/>

                @endif
                <br>
                        <div class="row">
                            <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm " id="process_data_table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Photo</th>
                                            <th>Name</th>
                                            <th class="text-center">Status</th>
                                            <th style="width:50px;" class="text-center">Dispatch Qty</th>
                                            <th class="text-center" style="width:120px;">Unit Price</th>
                                            <th class="text-center" style="width:120px;">Total Price</th>
                                            <th class="text-center" style="width:120px;">VIA</th>
                                            {{-- <th class="text-center" style="width:100px;">ETA</th> --}}

                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if(isset($booking_details) && count($booking_details))
                                        @foreach($booking_details as $key => $row )
                            <?php

                            $unit_price  = $row->CURRENT_IS_REGULAR == 1 ? $row->CURRENT_REGULAR_PRICE  : $row->CURRENT_INSTALLMENT_PRICE;
                            $unit_postage_cost = $row->CURRENT_IS_SM == 1 ? $row->CURRENT_SM_COST  : $row->CURRENT_SS_COST;
                            $unit_freight_cost = $row->CURRENT_IS_FREIGHT == 1 ? $row->CURRENT_AIR_FREIGHT  : $row->CURRENT_SEA_FREIGHT;
                            $unit_total_price = $unit_price + $unit_postage_cost + $unit_freight_cost;
                            ?>


                                                <tr >
                                                    <td >{{  $key+1 }} </td>
                                                    <td>
                                                        <img src="{{ asset($row->stock->PRD_VARIANT_IMAGE_PATH) }}"  style="width:60px;" />
                                                    </td>
                                                    <td>
                                                       <a href="{{ route('admin.product.view',[ 'id' => $row->stock->productVariant->F_PRD_MASTER_SETUP_NO ?? '' ]) }}" target="_blank"> {{ $row->stock->PRD_VARINAT_NAME }}</a>
                                                    </td>
                                                    <td class="text-center">{{ $row->IS_READY == 1 ? 'Ready' : 'Not Ready' }}</td>
                                                    <td style="width:50px;" class="text-center">
                                                        <input type="hidden" value="{{ $row->PK_NO }}" name="booking_details_no[]"  />
                                                        @if($row->DISPATCH_STATUS == 40)
                                                        1
                                                        @else
                                                        <input name="dispatch_qty[]" type="number"  class="form-control max_val_check " value="{{ $row->IS_READY == 1 ? 1 : 0 }}" max="1"/>
                                                        @endif
                                                    </td>
                                                    <td class="text-right" style="width:120px;">
                                                        {{ number_format($unit_price,2) }}
                                                    </td>
                                                    <td class="text-right" style="width:120px;">
                                                        {{ number_format($unit_total_price,2) }}
                                                    </td>
                                                    <td style="width:15%">
                                                        <select name="consignment_note[]" id="consignment_note" class="form-control">
                                                        @if(!empty($consignment))
                                                         @foreach($consignment as $coninfo)
                                                        <option value="{{ $coninfo->PK_NO }}">{{ $coninfo->COURIER_TRACKING_NO }}</option>

                                                        @endforeach
                                                        @endif
                                                        </select>
                                                    </td>
                                                    {{-- <td class="text-center" style="width:120px;">
                                                        @if(isset($row->stock->shippment->SCH_DEPARTING_DATE))
                                                        {{ date('d F, Y', strtotime($row->stock->shippment->SCH_DEPARTING_DATE)) }}
                                                        @endif
                                                    </td> --}}

                                                </tr>

                                            @endforeach
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                            </div>
                        </div>




                       <div class="row">
                        @if(!empty($consignment))

                        @foreach($consignment as $coninfo)

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="delivery_date">Select courier</label>
                                <div class="controls">
                                <select class="form-control" readonly name="courier">
                                    <option value="">-Select One-</option>
                                    @if(isset($data['courier']) && count($data['courier']) > 0 )
                                        @foreach($data['courier'] as $key => $val)
                                            @if($val->PK_NO != 0)
                                                <option @if($coninfo->F_COURIER_NO ==$val->PK_NO) {{ 'selected' }}@endif value="{{ $val->PK_NO }}">{{ $val->COURIER_NAME }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group {!! $errors->has('consignment_note') ? 'error' : '' !!}">
                                <label>Consignment Note<span class="text-danger">*</span></label>
                                <div class="controls">
                                    {!! Form::text(NULL, $coninfo->COURIER_TRACKING_NO ?? '', [ 'class' => 'form-control mb-1 ', 'placeholder' => 'ENTER COURIER_TRACKING_NO', 'tabindex' => 3,'readonly'=>true ]) !!}
                                </div>
                            </div>
                        </div>

                        @if(!empty($coninfo->SHIPMENT_KEY))

                        <div class="col-md-3">
                            <div class="form-group {!! $errors->has('shipment_key') ? 'error' : '' !!}">
                                <label>Shipment Key</label>
                                <div class="controls">


                                    {!! Form::text(NULL, $coninfo->SHIPMENT_KEY, [ 'class' => 'form-control mb-1 ', 'placeholder' => 'Enter dispatch date', 'tabindex' => 3,'readonly','id'=>'shipment_key' ]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group {!! $errors->has('shipment_key') ? 'error' : '' !!}">
                                <label>SHIPMENT COST</label>
                                <div class="controls">
                                    {!! Form::text(NULL, $coninfo->POSTAGE_COST ?? '', [ 'class' => 'form-control mb-1 ', 'placeholder' => 'SHIPMENT COST', 'tabindex' => 3,'readonly','id'=>'shipment_key' ]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 pt-2">
                            @if(!empty($coninfo->COURIER_TRACKING_NO))
                            <a target="_blank" href="http://sendparcel-test.ap-southeast-1.elasticbeanstalk.com/secure/print_thermal/{{ $coninfo->COURIER_TRACKING_NO ?? '' }}" type="button"  class="btn btn-success btn-sm" title="PRINT CONSIGNMENT NOTE">
                                <i class="la la-print"></i></a>
                            @endif
                            {{-- @if(!empty($coninfo->SHIPMENT_KEY) && empty($coninfo->COURIER_TRACKING_NO) ) --}}
                            <button type="button" data-id="{{ $coninfo->PK_NO }}" class="btn btn-primary btn-sm retry" title="RETRY"> <i class="la la-refresh"></i></button>
                            {{-- @endif --}}
                        </div>
                        @endif
                        @endforeach
                        @endif
                       </div>

                        <div class="form-actions mt-10 text-center">
                            <a href="{{ route('admin.order.list')}}" class="btn btn-warning mr-1"><i class="ft-x"></i> @lang('order.order_frm_button_cancel_label')</a>

                                @if(($order->IS_SELF_PICKUP == 1) && ($due > 0) )
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#collect_cod"> Collect By</button>
                                @else
                                <button type="submit" class="btn btn-primary submit-btn" title="SEND"><i class="la la-check-square-o"></i> Send</button>
                                @endif


                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#consignment" title="ADD NEW CONSIGNMENT NOTE"> <i class="la la-plus"></i>
                                    Add Consignment
                                  </button>


                            {{-- onclick="getConsignment()" --}}

                            {{-- DEMO72138097 <a href="javascript::void(0);" id="" onclick="getPrice()" class="btn btn-success">Get Price</a> --}}

                            {{-- <a href="{{ route('admin.poslazu.create-shipment') }}" class="btn btn-success">create shipment</a> --}}
                            {{-- http://sendparcel-test.ap-southeast-1.elasticbeanstalk.com/secure/print_thermal/DEMO31357105 --}}
                            {{-- <a href="{{ route('admin.poslazu.get-cart') }}" class="btn btn-success">cart</a> --}}
                            {{-- <a href="{{ route('admin.poslazu.download-pdf') }}" class="btn btn-success">download</a> --}}
                            {{-- <a href="{{ route('admin.poslazu.shipment-status') }}" class="btn btn-success">Shipment Status</a> --}}
                        </div>


                    </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}


    <!-- Consignment Modal -->
<div class="modal fade" id="consignment" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="consignmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="consignmentLabel">Get Consignment Note</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        {!! Form::open([ 'route' => ['admin.order.consignmentNote','id' => $booking->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}

        <input type="hidden" name="booking_no" value="{{ $data['booking']->PK_NO }}" />
        <div class="modal-body">


            <div class="p-2">
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('dispatch_date') ? 'error' : '' !!}">
                                <label>Dispatch Date<span class="text-danger">*</span></label>
                                <div class="controls">
                                    {!! Form::text('dispatch_date', date('d-m-Y'), [ 'class' => 'form-control mb-1 pickadate', 'placeholder' => 'Enter dispatch date', 'tabindex' => 2, 'data-validation-required-message' => 'This field is required', ]) !!}
                                    {!! $errors->first('dispatch_date', '<label class="help-block text-danger">:message</label>') !!}
                                </div>

                            </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="delivery_date">Select courier</label>
                            <div class="controls">
                            <select class="form-control" name="courier" id="courier"  data-validation-required-message="This field is required">
                                <option value="">-Select One-</option>
                                @if(isset($data['courier']) && count($data['courier']) > 0 )
                                    @foreach($data['courier'] as $key => $val)
                                        @if($val->PK_NO != 0)
                                            <option value="{{ $val->PK_NO }}">{{ $val->COURIER_NAME }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('consignment_note') ? 'error' : '' !!}">
                            <label>Consignment Note<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::text('consignment_note',NULL, [ 'class' => 'form-control mb-1 ', 'placeholder' => 'ENTER COURIER_TRACKING_NO', 'tabindex' => 3 ]) !!}
                                {!! $errors->first('consignment_note', '<label class="help-block text-danger">:message</label>') !!}
                            </div>

                        </div>
                    </div>


                    @if($order->IS_SELF_PICKUP == 1)
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('collected_by') ? 'error' : '' !!}">
                            <label>Collected By<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::text('collected_by', null, [ 'class' => 'form-control mb-1 ', 'placeholder' => 'Enter collected by', 'tabindex' => 3, 'data-validation-required-message' => 'This field is required', ]) !!}
                                {!! $errors->first('collected_by', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    @endif

                </div>




                    {{-- --}}
                    <div class="pos-laju" style="display:none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group {!! $errors->has('size') ? 'error' : '' !!}">
                                    <label for="size">Size<span class="text-danger">*</span></label>
                                    <div class="controls">
                                          {!!Form::select('size',$get_parcel_sizes,'box', ['placeholder' => 'Select One','class' => 'form-control','id'=>'size','tabindex' => 1,'data-validation-required-message' => 'This field is required'])!!}
                                        {!! $errors->first('size', '<label class="help-block text-danger">:message</label>') !!}

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {!! $errors->has('declared_weight') ? 'error' : '' !!}">
                                    <label for="declared_weight">Weight<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::text('declared_weight', 4.5, [ 'class' => 'form-control mb-1 ', 'placeholder' => 'Enter weight', 'tabindex' => 2, 'data-validation-required-message' => 'This field is required','id'=>'declared_weight' ]) !!}
                                        {!! $errors->first('declared_weight', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {!! $errors->has('length') ? 'error' : '' !!}">
                                    <label>Length<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::text('length', 40, [ 'class' => 'form-control mb-1 ', 'placeholder' => 'Enter length', 'tabindex' => 3, 'data-validation-required-message' => 'This field is required','id'=>'length']) !!}
                                        {!! $errors->first('length', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group {!! $errors->has('width') ? 'error' : '' !!}">
                                    <label>Width<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::text('width', 30, [ 'class' => 'form-control mb-1 ', 'placeholder' => 'Enter width', 'tabindex' => 4, 'data-validation-required-message' => 'This field is required','id'=>'width']) !!}
                                        {!! $errors->first('width', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {!! $errors->has('height') ? 'error' : '' !!}">
                                    <label>Height<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::text('height',25, [ 'class' => 'form-control mb-1 ', 'tabindex' => 5, 'data-validation-required-message' => 'This field is required','id'=>'height' ]) !!}
                                        {!! $errors->first('height', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {!! $errors->has('fitness') ? 'error' : '' !!}">
                                    <label>Product Type<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!!Form::select('content_type',$order_content_types,'fitness', ['placeholder' => 'Select One','class' => 'form-control','id'=>'content_type','tabindex' => 6,'data-validation-required-message' => 'This field is required'])!!}
                                        {!! $errors->first('fitness', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 sr-only">
                                <div class="form-group {!! $errors->has('content_description') ? 'error' : '' !!}">
                                    <label>Content Description<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!!Form::text('content_description','Lifestyle & Home - Health and Fitness',['placeholder' => 'Select One','class' => 'form-control','id'=>'content_description','tabindex' => 6,'data-validation-required-message' => 'This field is required'])!!}
                                        {!! $errors->first('content_description', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {!! $errors->has('sender_postcode') ? 'error' : '' !!}">
                                    <label>Sender Postcode<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::text('sender_postcode',$order->FROM_POSTCODE ?? '', [ 'class' => 'form-control mb-1 ', 'tabindex' => 5, 'data-validation-required-message' => 'This field is required','disabled'=>'true' ]) !!}
                                        {!! $errors->first('sender_postcode', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {!! $errors->has('receiver_postcode') ? 'error' : '' !!}">
                                    <label>Receiver Postcode<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::text('receiver_postcode',$order->DELIVERY_POSTCODE ?? '', [ 'class' => 'form-control mb-1 ', 'tabindex' => 5, 'data-validation-required-message' => 'This field is required','id'=>'receiver_postcode' ]) !!}

                                        {!! $errors->first('receiver_postcode', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {!! $errors->has('total') ? 'error' : '' !!}">
                                    <label>Effective Weight<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::text('total',NULL, [ 'class' => 'form-control mb-1 ', 'tabindex' => 5, 'id' => 'total','disabled'=>true ]) !!}
                                        {!! $errors->first('total', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

            </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>

        {!! Form::close() !!}


      </div>
    </div>
  </div>







@endsection

<!--push from page-->
@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script>
$( document ).ready(function() {
    setFinalWeight();
});
jQuery (document).on("change", "#length,#width,#height", function(e) {
    setFinalWeight();
});
function setFinalWeight() {
var length = $('.form-group').find('#length').val();
var width = $('.form-group').find('#width').val();
var height = $('.form-group').find('#height').val();
var amount = length*width*height/6000;
$('.form-group').find('#total').val(amount.toFixed(2));
if (amount > 5) {
    toastr.error('Box Weight More then .', '', {timeOut: 5000})
  }
}
    jQuery(document).ready(function($) {
        var get_url = $('#base_url').val();
        // Set the Options for "Bloodhound" suggestion engine
    });

    $(document).ready(function(){
        var courier = $('.form-group').find('#courier').val();
            $('#courier option').each(function() {
            if (courier == 1){
                $(".pos-laju").show();
            }
        });
        $('#courier').on('change', function() {
          if ( this.value == '1')
          {
            $(".pos-laju").show(500);
          }
          else
          {
            $(".pos-laju").hide(500);
          }
        });
    });
</script>

<script>


$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    $(document).on('click','.retry',function(e){
        var id = $(this).attr('data-id');
        if (!confirm('Are you sure you want Retry Get Consignment Note')) {
            return false;
        }
        if ('' != id) {
            var pageurl = `{{ URL::to('ajax/consignment/getTrackingId')}}/`+id;
            $.ajax({
                type:'GET',
                url:pageurl,
                async :true,
                beforeSend: function () {
                    $("body").css("cursor", "progress");
                },
                success: function (data) {
                    location.reload();
                },
                complete: function (data) {
                    $("body").css("cursor", "default");
                    //$.unblockUI();
                }
            });
        }
    })


function getConsignment() {
    var form = new FormData();
form.append("api_key", "kf0ecfaac86c2c240ea14d276cee0a4d5");
form.append("tracking_no", "DEMO30494389");
var settings = {
  ///"url": "http://sendparcel-test.ap-southeast-1.elasticbeanstalk.com/apiv1/get_consignment_note",
  "url": "http://sendparcel-test.ap-southeast-1.elasticbeanstalk.com/secure/print_thermal/DEMO41936500",
  "method": "POST",
  "timeout": 0,
  "headers": {
  },
  "processData": false,
  "mimeType": "multipart/form-data",
  "contentType": false,
  "data": form
};

$.ajax(settings).done(function (response) {
    // var blob=new Blob('');
    // var link=document.createElement('a');
    // link.href=window.URL.createObjectURL(blob);
    // link.download="aa.pdf";
    // link.click();
    var mypdf = new Blob(response, {type : 'application/pdf'});
    mypdf.download="aa.pdf";
   // window.open(response);
  //console.log(response);
});

}



</script>

@endpush('custom_js')
