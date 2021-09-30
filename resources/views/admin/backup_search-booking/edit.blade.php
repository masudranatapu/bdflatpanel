@extends('admin.layout.master')

@section('Product Management','open')
@section('booking_list','active')


@section('title') @lang('booking.update_booking') @endsection
@section('page-name') @lang('booking.update_booking') @endsection

@section('title') @lang('booking.add_new_booking') @endsection
@section('page-name') @lang('booking.add_new_booking') @endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin_role.breadcrumb_title') </a></li>
<li class="breadcrumb-item active">@lang('booking.breadcrumb_sub_title') </li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('app-assets/file_upload/image-uploader.min.css')}}">
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/summernote.css')}}">

<style>
    #scrollable-dropdown-menu .tt-menu {
      max-height: 260px;
      overflow-y: auto;
      width: 100%;
      border: 1px solid #333;
      border-radius: 5px;

  }
  #scrollable-dropdown-menu2 .tt-menu {
      max-height: 260px;
      overflow-y: auto;
      width: 100%;
      border: 1px solid #333;
      border-radius: 5px;

  }
  .twitter-typeahead{
    display: block !important;
}
#warehouse th, #availble_qty th {
    border: none;
    border-bottom: 1px solid #333;
    font-size: 12px;
    font-weight: normal;
    padding-bottom: 7px;
    padding-bottom: 11px;
}
#book_qty th {
    border: none;
    /* border-bottom: 1px solid #333; */
    font-size: 12px;
    font-weight: normal;
    padding-bottom: 5px;
    padding-top: 0;
}
.tt-hint {
        color: #999 !important;
    }
    #offerTbl tr td{padding: 5px !important}
</style>
@endpush('custom_css')
<?php
use Carbon\Carbon;
$booking_validity   = Config::get('static_array.booking_validity') ?? array();
$booking_details    = $data['booking_details'];
$booking            = $data['booking'];
$bundle             = $data['bundle'] ?? array();
$customer_id        = $booking->getCustomer->PK_NO ?? $booking->getReseller->PK_NO;

$total_price_regular = 0;
$total_price_installment = 0;
$is_freight     = $booking_details[0]->IS_FREIGHT;
$is_sm          = $booking_details[0]->IS_SM;
$is_regular     = $booking_details[0]->IS_REGULAR;

?>
@section('content')
<div class="card card-success min-height">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> Booking Details</h4>
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
            <div class="row">
                <div class="col-md-12">
                    <?php
                        $customer_postcode = $booking->getCustomerPostCode($booking->F_CUSTOMER_NO,$booking->F_RESELLER_NO,$booking->IS_RESELLER);
                        // echo '<pre>';
                        // echo '======================<br>';
                        // print_r($customer_postcode);
                        // echo '<br>======================<br>';
                        // exit();
                        ?>
                    {!! Form::open([ 'route' => ['admin.booking.put', $booking->PK_NO], 'method' => 'post', 'id' => 'post_form', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                    {!! Form::hidden('customer_id',$customer_id, ['id'=>'customer_id']) !!}
                    {!! Form::hidden('post_code',$customer_postcode->POST_CODE ?? 0, ['id'=>'post_code']) !!}
                    {!! Form::hidden('customer_address',$customer_postcode->PK_NO ?? 0,['id'=>'customer_address']) !!}
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-7">
                                       <div class="form-group {!! $errors->has('agent') ? 'error' : '' !!}">
                                            <div class="controls">
                                                <label>{{trans('form.sales_agent')}}<span class="text-danger">*</span></label>
                                                @if (Auth::user()->F_AGENT_NO == 0)
                                                {!! Form::select('agent', $agent, $booking->F_BOOKING_SALES_AGENT_NO ?? '', ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'This field is required', 'id' => 'booking_under']) !!}
                                                {!! $errors->first('agent', '<label class="help-block text-danger">:message</label>') !!}
                                                @else
                                                {!! Form::select('agent', $agent, $booking->F_BOOKING_SALES_AGENT_NO ?? '', ['class'=>'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'id' => 'booking_under','style'=>'pointer-events:none']) !!}
                                                {!! $errors->first('agent', '<label class="help-block text-danger">:message</label>') !!}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <br>
                                                <label>{{trans('form.customer_type')}}<span class="text-danger">*</span></label>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>{!! Form::radio('booking_radio', 'customer', $booking->IS_RESELLER == 0 ? true : false, [ 'id' => 'radio_btn']) !!} {{trans('form.customer')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>{!! Form::radio('booking_radio','reseller', $booking->IS_RESELLER == 1 ? true : false,[ 'id' => 'radio_btn2']) !!} {{trans('form.reseller')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="cus_details" style="border: 1px solid #c4c4c4;border-radius: 5px;">
                                    <div class="form-group mb-0 {!! $errors->has('book_customer') ? 'error' : '' !!}">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th colspan="4" style="background: aliceblue;">Customer Information</th>
                                                    </tr>
                                                    <tr>
                                                        <th><small><strong>Name</strong></small></th>
                                                        <th><small><strong>Phone</strong></small></th>
                                                        <th><small><strong>Email</strong></small></th>
                                                        <th><small><strong>Postage</strong></small></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="append_cus">
                                                <tr>
                                                    <td><small>{{ $booking->getCustomer->NAME ?? $booking->getReseller->NAME ?? '' }}</small></td>
                                                    <td><small>{{ $booking->getCustomer->MOBILE_NO ?? $booking->getReseller->MOBILE_NO ?? '' }}</small></td>
                                                    <td><small>{{ $booking->getCustomer->EMAIL ?? $booking->getReseller->EMAIL ?? '' }}</small></td>
                                                    <td><small id="postage_cost_main_customer">{{ $customer_postcode->POST_CODE ?? '' }}</small></td>
                                                </tr>
                                                @if (isset($booking->getCustomer->PK_NO))
                                                <?php
                                                $address2 = $booking->getCustomerAddress($customer_id,2);
                                                ?>
                                                @if (isset($address2[0]))
                                                <tr>
                                                    <td colspan="4"><small>Billing Address :
                                                        {{ isset($address2[0]->ADDRESS_LINE_1) ? $address2[0]->ADDRESS_LINE_1 : '' }}
                                                        {{ isset($address2[0]->ADDRESS_LINE_2) ? ','.$address2[0]->ADDRESS_LINE_2 : '' }}
                                                        {{ isset($address2[0]->ADDRESS_LINE_3) ? ','.$address2[0]->ADDRESS_LINE_3 : '' }}
                                                        {{ isset($address2[0]->ADDRESS_LINE_4) ? ','.$address2[0]->ADDRESS_LINE_4 : '' }}
                                                        {{ isset($address2[0]->STATE) ? ','.$address2[0]->STATE : '' }}
                                                        {{ isset($address2[0]->CITY) ? ','.$address2[0]->CITY : '' }}
                                                        {{ isset($address2[0]->POST_CODE) ? ','.$address2[0]->POST_CODE : '' }}
                                                        {{ isset($address2[0]->COUNTRY) ? ','.$address2[0]->COUNTRY : '' }}
                                                    </small></td>
                                                </tr>
                                                @endif
                                                <?php
                                                $address1 = $booking->getCustomerAddress($customer_id,1);
                                                ?>
                                                @if (!empty($address1))
                                                @foreach ($address1 as $item1)
                                                <tr>
                                                    <td colspan="4">
                                                        <small>Dellivery Address :
                                                        {{ isset($item1->ADDRESS_LINE_1) ? $item1->ADDRESS_LINE_1 : '' }}
                                                        {{ isset($item1->ADDRESS_LINE_2) ? ','.$item1->ADDRESS_LINE_2 : '' }}
                                                        {{ isset($item1->ADDRESS_LINE_3) ? ','.$item1->ADDRESS_LINE_3 : '' }}
                                                        {{ isset($item1->ADDRESS_LINE_4) ? ','.$item1->ADDRESS_LINE_4 : '' }}
                                                        {{ isset($item1->STATE) ? ','.$item1->STATE : '' }}
                                                        {{ isset($item1->CITY) ? ','.$item1->CITY : '' }}
                                                        {{ isset($item1->POST_CODE) ? ','.$item1->POST_CODE : '' }}
                                                        {{ isset($item1->COUNTRY) ? ','.$item1->COUNTRY : '' }}
                                                        </small>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @endif
                                                @else
                                                <?php
                                                $address3 = $booking->getResellerAddress($customer_id);
                                                ?>
                                                <tr>
                                                    <td colspan="4"><small>Billing Address :
                                                        {{ isset($address3[0]->ADDRESS_LINE_1) ? $address3[0]->ADDRESS_LINE_1 : '' }}
                                                        {{ isset($address3[0]->ADDRESS_LINE_2) ? ','.$address3[0]->ADDRESS_LINE_2 : '' }}
                                                        {{ isset($address3[0]->ADDRESS_LINE_3) ? ','.$address3[0]->ADDRESS_LINE_3 : '' }}
                                                        {{ isset($address3[0]->ADDRESS_LINE_4) ? ','.$address3[0]->ADDRESS_LINE_4 : '' }}
                                                        {{ isset($address3[0]->STATE) ? ','.$address3[0]->STATE : '' }}
                                                        {{ isset($address3[0]->CITY) ? ','.$address3[0]->CITY : '' }}
                                                        {{ isset($address3[0]->POST_CODE) ? ','.$address3[0]->POST_CODE : '' }}
                                                        {{ isset($address3[0]->COUNTRY) ? ','.$address3[0]->COUNTRY : '' }}
                                                    </small></td>
                                                </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group {!! $errors->has('customer') ? 'error' : '' !!}">
                                    <label>Customer Name</label>
                                    <div class="controls" id="scrollable-dropdown-menu2">
                                        <input type="search" name="q" id="book_customer" class="form-control search-input2" placeholder="Enter Customer Name" autocomplete="off" required value="{{$booking->getCustomer->NAME ?? $booking->getReseller->NAME ?? '' }}">
                                        {!! $errors->first('book_customer', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group {!! $errors->has('ig_code') ? 'error' : '' !!}">
                                    <label>Product Keyword<span class="text-danger">*</span></label>
                                    <div class="controls" id="scrollable-dropdown-menu"><input type="search" name="q" id="product" class="form-control search-input" placeholder="Enter Product Keyword" autocomplete="off" value="">{!! $errors->first('ig_code', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-borde table-sm" id="invoicetable">
                                <thead>
                                    <tr>
                                        <th style="width: 200px">@lang('tablehead.image')</th>
                                        <th style="width: 200px">@lang('tablehead.product_name')</th>
                                        <th class="" style="width: 70px;">@lang('tablehead.warehouse')</th>
                                        <th class="" style="width: 70px;">@lang('tablehead.available_qty')</th>
                                        <th class="" style="width: 70px;">@lang('tablehead.booking_qty')</th>
                                        <th class="" style="width: 70px;">Price</th>
                                        {{-- <th class="" style="width: 70px;">Installment Price</th> --}}
                                        <th class="" style="width: 70px;">@lang('tablehead.action')</th>
                                    </tr>
                                </thead>
                                <tbody id="append_tr">
                                    @if($booking_details && count($booking_details) > 0 )
                                    @foreach($booking_details as $key => $val)

                                        <?= $val->book_info ?>

                                    @endforeach
                                    @endif
                                </tbody>
                                <tfoot id="append_tfoot">
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td>Sub Total</td>
                                        <td colspan="1">
                                            <label>{!! Form::radio('price_type_all',1,$booking_details[0]->IS_REGULAR == 1 ? true : false,['id'=>'regular_price_all']) !!} Regular</label>&nbsp;
                                            <label>{!! Form::radio('price_type_all',0,$booking_details[0]->IS_REGULAR == 1 ? false : true,['id'=>'installmnt_price_all']) !!} Installment</label>
                                        </td>
                                        <td></td>
                                        <td id="final_qty"></td>
                                        <td id="ss_amount_final"></td>
                                        {{-- <td id="sm_amount_final"></td> --}}
                                        <td></td>
                                    </tr>
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td>Total Freight Cost</td>
                                        <td>
                                            <div class="controls">
                                                <select name="customer_preferred_all" id="customer_preferred_all" class="select2">

                                                    <option value="0">Select Air/Sea</option>
                                                    <option value="air">AIR</option>
                                                    <option value="sea">SEA</option>

                                                </select>
                                            </div>
                                        </td>
                                        <td colspan="2"><span id="given_freight_td">Given Freight: <span id="given_freight">{{ number_format($booking->FREIGHT_COST,2, '.', '') }}</span></span></td>
                                        {{-- <td></td> --}}
                                        <td><input type="number" name="freight_cost_total" style="width: 60px;text-align: center;" class="form-control input-sm ml-2" id="amount_freight"></td>
                                        {{-- <td><input type="number" name="freight_cost_total_ins" style="width: 60px;text-align: center;" class="form-control input-sm ml-2" id="amount_freight2"></td> --}}
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td>Postage Cost</td>
                                        <td style="pointer-events: none;display: none">
                                            <label>{!! Form::radio('postage',1,true,['id'=>'is_sm_cost1','readonly']) !!} SM COST</label>&nbsp;
                                            <label>{!! Form::radio('postage',0,false,['id'=>'is_sm_cost2']) !!} SS COST</label>
                                        </td>
                                        <td colspan="3">
                                            {{-- <span id="given_postage_td">Given Postage: <span id="given_postage">{{ number_format($booking->POSTAGE_COST,2, '.', '') }}</span></span> --}}
                                        </td>
                                        <td colspan="2" align="left" title="Postage cost will be set based on delivery address">
                                            <input type="number" name="postage_regular_cost_final" style="width: 60px;text-align: center;display: none" class="form-control input-sm ml-2" id="postage_cost">
                                            <div class="badge badge-pill badge-border border-info info">To Be Confirmed</div>
                                        </td>
                                        {{-- <td><input type="number" name="postage_ins_cost_final" style="width: 60px;text-align: center;" class="form-control input-sm ml-2" id="postage_cost2"></td> --}}
                                    </tr>
                                    <tr style="text-align: center">
                                        <th></th>
                                        <th>Grand Total</th>
                                        <th></th>
                                        <th></th>
                                        <th id="final_qty"></th>
                                        <th id="grand_total_ss"></th>
                                        {!! Form::hidden('grand_total', 0, ['id' => 'grand_total']) !!}
                                        {{-- <th id="grand_total_sm"></th> --}}
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    {{-- <div class="col-md-4">
                        <div class="form-group">
                            <div class="controls">
                                <label>{{trans('form.booking_validity_extend')}}</label>
                                {!! Form::select('booking_validity', $booking_validity, $booking->EXPIERY_DATE_TIME_DIF, ['class'=>'form-control mb-1 select2', 'id' => 'booking_validity', 'placeholder' => 'Select Validation Extend Time']) !!}
                            {!! $errors->first('booking_validity', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="controls">
                                <label>{{trans('form.booking_validity_extend')}}</label>
                                {!! Form::select('booking_validity', $booking_validity, null, ['class'=>'form-control mb-1 select2', 'id' => 'booking_validity', 'placeholder' => 'Select Validation Extend Time']) !!}
                            {!! $errors->first('booking_validity', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="controls">
                                <label>Booking Expires in </label>
                                <?php
                                $startTime = Carbon::now();
                                $endTime = Carbon::parse($booking->EXPIERY_DATE_TIME);
                                $totalDuration =  $startTime->diff($endTime)->format('%H:%I:%S')." Minutes";
                                // $totalDuration =  $startTime->diffInHours($endTime)." Minutes";

                                ?>
                                <p class="danger">{{  date('d-m-Y h:i a', strtotime($booking->EXPIERY_DATE_TIME))  }}</p>
                                {{-- <p class="danger"><strong>{{ $totalDuration }}</strong></p>
                                <p class="danger"><strong>{{ $startTime }}</strong></p>
                                <p class="danger"><strong>{{ $endTime }}</strong></p> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="form-actions mt-10 text-center">
                <a href="{{ route('admin.booking.list')}}" class="btn btn-danger mr-1"><i class="ft-x"></i> @lang('form.btn_cancle')</a>
                <button type="submit" class="btn btn-primary save-inv-details mr-1"><i class="la la-check-square-o"></i>Update Booking</button>
                <a href="{{ route('admin.booking.edit',['id' => $booking->PK_NO,'checkoffer' => 1 ])}}" class="btn btn-warning mr-1"><i class="la la-check-square"></i> Check Offer</a>
                <a href="javascript:void(0)" id="book_to_order">
                    <button type="button" class="btn btn-info save-inv-details"><i class="la la-check-square-o"></i> Proceed to Order </button>
                </a>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    @if( request()->get('checkoffer') == 1)
        <div class="card" >
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered " id="offerTbl">
                        <thead>
                        <tr>
                            <th style="width: 5%">SL.</th>
                            <th style="width: 30%">Details</th>
                            <th style="width: 10%">Item Qty</th>
                            <th style="width: 20%">Price</th>
                            <th style="width: 5%">Postage Cost</th>
                            <th style="width: 5%">Freight Cost</th>
                            <th style="width: 5%">Total Price</th>
                            <th style="width: 10%" class="text-center">Action</th>

                        </tr>
                        </thead>
                        <tbody>
                            @if($bundle && count($bundle))
                            @foreach($bundle as $key => $row)
                            {!! Form::open([ 'route' => ['admin.booking.offer-apply'], 'method' => 'post',  'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}

                            {!! Form::hidden('bundle_no',$row->F_BUNDLE_NO) !!}
                            {!! Form::hidden('booking_pk_no',$booking->PK_NO) !!}

                            @php
                            $total_bundle = 0;
                            $grand_total = 0;

                            if($is_regular == 1){
                                $total_bundle += $row->TOTAL_REGULAR_BUNDLE_PRICE*$row->BUNDLE_QTY;
                            }else{
                                $total_bundle += $row->TOTAL_INSTALLMENT_BUNDLE_PRICE*$row->BUNDLE_QTY;
                            }
                            if($is_freight == 1 ){
                                $total_bundle += ($row->P_AIR+$row->R_AIR)*$row->BUNDLE_QTY;
                            }else{
                                $total_bundle += ($row->P_SEA+$row->R_SEA)*$row->BUNDLE_QTY;
                            }
                            if($is_sm == 1 ){
                                $total_bundle += ($row->P_SM+$row->R_SM)*$row->BUNDLE_QTY;
                            }else{
                                $total_bundle += ($row->P_SS+$row->R_SS)*$row->BUNDLE_QTY;
                            }

                            @endphp
                            <tr>
                                <td style="width: 5%">OFFER</td>
                                <td style="width: 50%">
                                    {{ $row->BUNDLE_NAME_PUBLIC }}
                                </td>
                                <td class="text-center">
                                    <div style="display: block; padding-bottom:10px; ">
                                        {{ $row->BUNDLE_QTY }}
                                    </div>

                                </td>

                                <td style="width: 20%" class="text-right">
                                    @if($is_regular == 1)
                                        <div>Regular : {{ number_format($row->TOTAL_REGULAR_BUNDLE_PRICE,2) }} </div>
                                    @else
                                    <div>Installment : {{ number_format($row->TOTAL_INSTALLMENT_BUNDLE_PRICE,2) }}</div>
                                    @endif

                                </td>
                                <td class="text-right">
                                    @if($is_sm == 1 )
                                        <div>SM : {{ number_format($row->P_SM+$row->R_SM,2) }}</div>
                                    @else
                                        <div>SS : {{ number_format($row->P_SS+$row->R_SS,2) }}</div>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($is_freight == 1 )
                                        <div>AIR : {{ number_format($row->P_AIR+$row->R_AIR,2) }}</div>
                                    @else
                                        <div>SEA : {{ number_format($row->P_SEA+$row->R_SEA,2) }}</div>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @php $grand_total += $total_bundle; @endphp
                                    <div>{{ number_format($total_bundle,2) }}</div>
                                </td>
                                <td style="width: 15%" class="text-center">

                                </td>
                            </tr>

                            @if($data['non_bundle'] && count($data['non_bundle']) > 0 )
                            @foreach($data['non_bundle'] as $b => $nrow )
                            <?php
                                $total_non_bundle = 0;

                            if($is_regular == 1){
                                $total_non_bundle += $nrow->CURRENT_REGULAR_PRICE*$nrow->ITEM_QTY;
                            }else{
                                $total_non_bundle += $nrow->CURRENT_INSTALLMENT_PRICE*$nrow->ITEM_QTY;
                            }
                            if($is_freight == 1 ){
                                $total_non_bundle += $nrow->AIR_FREIGHT*$nrow->ITEM_QTY;
                            }else{
                                $total_non_bundle += $nrow->SEA_FREIGHT*$nrow->ITEM_QTY;
                            }
                            if($is_sm == 1 ){
                                $total_non_bundle += $nrow->SM_COST*$nrow->ITEM_QTY;
                            }else{
                                $total_non_bundle += $nrow->SS_COST*$nrow->ITEM_QTY;
                            }
                            ?>
                            <tr>
                                <td style="width: 5%">
                                    <img style="width: 150px !important; height: 150px;" src="{{ asset($nrow->PRC_IN_IMAGE_PATH) }}" alt="PICTURE">
                                </td>
                                <td style="width: 50%">{{ $nrow->PRD_VARINAT_NAME }}</td>
                                <td class="text-center">{{ $nrow->ITEM_QTY }}</td>

                                <td style="width: 20%" class="text-right">
                                    @if($is_regular == 1)
                                        <div>Regular : {{ number_format($nrow->CURRENT_REGULAR_PRICE*$nrow->ITEM_QTY,2) }} </div>
                                    @else
                                        <div>Installment : {{ number_format($nrow->CURRENT_INSTALLMENT_PRICE*$nrow->ITEM_QTY,2) }}</div>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($is_sm == 1 )
                                        <div>SM : {{ number_format($nrow->SM_COST*$nrow->ITEM_QTY,2) }}</div>
                                    @else
                                        <div>SS : {{ number_format($nrow->SS_COST*$nrow->ITEM_QTY,2) }}</div>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($is_freight == 1 )
                                        <div>AIR : {{ number_format($nrow->AIR_FREIGHT*$nrow->ITEM_QTY,2) }}</div>
                                    @else
                                        <div>SEA : {{ number_format($nrow->SEA_FREIGHT*$nrow->ITEM_QTY,2) }}</div>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @php $grand_total += $total_non_bundle; @endphp
                                    <div>{{ number_format($total_non_bundle,2) }}</div>
                                </td>
                                <td style="width: 15%" class="text-center">

                                </td>
                            </tr>
                            @endforeach
                            @endif


                            {!! Form::close() !!}
                            <tr>
                                <td>Total Price</td>
                                <td colspan="6" class="text-right">{{  number_format($grand_total,2)  }}</td>
                                <td class="text-center">
                                    <button type="submit" class="btn btn-info ">Apply</button>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="8" class="text-center">BUNDLE NOT MACHED</td>
                            </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
        @endif
    <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
</div>
</div>
</div>
@include('admin.booking._modal_html')
@endsection
<!--push from page-->
@push('custom_js')


<!-- Typeahead.js Bundle -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/country.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/cus_pro_search.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/book_order.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/js/common.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
@endpush('custom_js')
