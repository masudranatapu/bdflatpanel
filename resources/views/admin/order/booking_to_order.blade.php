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
</style>

@endpush('custom_css')


<?php

$booking_validity = Config::get('static_array.booking_validity') ?? array();
$booking_details    = $data['booking_details'];
$data               = $data['booking'];

$total_price_regular = 0;
$total_price_installment =0;

?>


@section('content')
<div class="card card-success ">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> Order Details</h4>
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
                        $customer_postcode = $data->getCustomerPostCode($data->F_CUSTOMER_NO,$data->F_RESELLER_NO,$data->IS_RESELLER);
                        // echo '<pre>';
                        // echo '======================<br>';
                        // print_r($data->getCustomerPostCode($data->F_CUSTOMER_NO,$data->IS_RESELLER));
                        // echo '<br>======================<br>';
                        // exit();
                        ?>


                    {!! Form::hidden('customer_id',$data->getCustomer->PK_NO ?? null, ['id'=>'customer_id']) !!}
                    {!! Form::hidden('post_code',$customer_postcode->POST_CODE ?? 0, ['id'=>'post_code']) !!}
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group {!! $errors->has('ig_code') ? 'error' : '' !!}">
                                            <label>IG Code<span class="text-danger">*</span></label>
                                            <div class="controls" id="scrollable-dropdown-menu"><input type="search" name="q" id="product" class="form-control search-input" placeholder="Enter IG Code" autocomplete="off" value="">{!! $errors->first('ig_code', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="form-group {!! $errors->has('agent') ? 'error' : '' !!}">

                                                <div class="controls">
                                                     <label>{{trans('form.sales_agent')}}<span class="text-danger">*</span></label>
                                                    {!! Form::select('agent', $agent, $data->F_BOOKING_SALES_AGENT_NO ?? '', ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'This field is required', 'id' => 'booking_under']) !!}
                                                    {!! $errors->first('agent', '<label class="help-block text-danger">:message</label>') !!}
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
                                                        <label>{!! Form::radio('booking_radio', 'customer', $data->IS_RESELLER == 0 ? true : false, [ 'id' => 'radio_btn']) !!} {{trans('form.customer')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>{!! Form::radio('booking_radio','reseller', $data->IS_RESELLER == 1 ? true : false,[ 'id' => 'radio_btn2']) !!} {{trans('form.reseller')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="cus_details" style="border: 1px solid #c4c4c4;border-radius: 5px;">
                                    <div class="form-group {!! $errors->has('book_customer') ? 'error' : '' !!}">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th colspan="3" style="background: aliceblue;">Customer Information</th>
                                                    </tr>
                                                    <tr>
                                                        <th><small><strong>Name</strong></small></th>
                                                        <th><small><strong>Phone</strong></small></th>
                                                        <th><small><strong>Email</strong></small></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="append_cus">
                                                <td><small>{{ $data->getCustomer->NAME ?? $data->getReseller->NAME ?? '' }}</small></td>
                                                <td><small>{{ $data->getCustomer->MOBILE_NO ?? $data->getReseller->MOBILE_NO ?? '' }}</small></td>
                                                <td><small>{{ $data->getCustomer->EMAIL ?? $data->getReseller->EMAIL ?? '' }}</small></td>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group {!! $errors->has('customer') ? 'error' : '' !!}">

                                    <label>Customer Name</label>
                                    <div class="controls" id="scrollable-dropdown-menu2">
                                        <input type="search" name="q" id="book_customer" class="form-control search-input2" placeholder="Enter Customer Name" autocomplete="off" required value="{{$data->getCustomer->NAME ?? $data->getReseller->NAME }}">
                                        {!! $errors->first('book_customer', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                            <div id="cus_mobile" class="col-md-2" style="display: none">
                                <div class="form-group {!! $errors->has('custom_mobile') ? 'error' : '' !!}">
                                    <label>Customer Mobile</label>
                                    <div class="controls">
                                        {!! Form::number('custom_mobile', '017',[ 'class' => 'form-control', 'placeholder' => 'Enter Customer Mobile', 'id' => 'cus_mobile_input','data-validation-required-message' => 'This field is required']) !!}
                                        {!! $errors->first('custom_mobile', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                            <div id="cus_no" class="col-md-2" style="display: none">
                                 <!-- <div class="form-group {!! $errors->has('custom_no') ? 'error' : '' !!}">
                                    <label>Customer No.</label>
                                    <?php
                                    // $maxcustomerno = $maxcustomerno ?? '123456';
                                    ?>
                                    <div class="controls">
                                        {{-- {!! Form::number('custom_no', $maxcustomerno,[ 'class' => 'form-control', 'placeholder' => 'Enter Customer Mobile', 'id' => 'cus_no_input','data-validation-required-message' => 'This field is required' ]) !!}
                                        {!! $errors->first('custom_no', '<label class="help-block text-danger">:message</label>') !!} --}}
                                    </div>
                                </div> -->
                                <button type="button" title="ADD CUSTOMER" class="btn btn-primary btn-sm search_mother_btn mt-2" id="cus_no_input" data-url={{ route('admin.customer.store.booking')}}>
                                    <i class="la la-plus"></i>
                                 </button>
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
                                        <th class="" style="width: 70px;">Regular Price</th>
                                        <th class="" style="width: 70px;">Installment Price</th>
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
                                        <td></td>
                                        <td></td>
                                        <td id="final_qty"></td>
                                        <td id="ss_amount_final"></td>
                                        <td id="sm_amount_final"></td>
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
                                        <td></td>
                                        <td></td>
                                        <td><input type="number" name="freight_cost_total_regular" style="width: 60px;text-align: center;" class="form-control input-sm ml-2" id="amount_freight"></td>
                                        <td><input type="number" name="freight_cost_total_ins" style="width: 60px;text-align: center;" class="form-control input-sm ml-2" id="amount_freight2"></td>
                                        <td></td>
                                    </tr>
                                    <tr style="text-align: center">
                                        <td></td>
                                        <td>Postage Cost</td>
                                        <td>
                                            <label>{!! Form::radio('postage',1,true,['id'=>'is_sm_cost1']) !!} SM COST</label>&nbsp;
                                            <label>{!! Form::radio('postage',0,false,['id'=>'is_sm_cost2']) !!} SS COST</label>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td><input type="number" name="postage_regular_cost_final" style="width: 60px;text-align: center;" class="form-control input-sm ml-2" id="postage_cost"></td>
                                        <td><input type="number" name="postage_ins_cost_final" style="width: 60px;text-align: center;" class="form-control input-sm ml-2" id="postage_cost2"></td>
                                        <td></td>
                                    </tr>
                                    <tr style="text-align: center">
                                        <th></th>
                                        <th>Grand Total</th>
                                        <th></th>
                                        <th></th>
                                        <th id="final_qty"></th>
                                        <th id="grand_total_ss"></th>
                                        <th id="grand_total_sm"></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group {!! $errors->has('booking_note') ? 'error' : '' !!}">
                            <label>{{trans('form.booking_note')}}</label>
                            <div class="controls">

                                {!! Form::textarea('booking_note', null, [ 'class' => 'form-control mb-1 summernote', 'placeholder' => 'Enter short dbooking note', 'tabindex' => 16, 'rows' => 3 ]) !!}
                                {!! $errors->first('booking_note', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-4">
                        <div class="form-group">
                            <div class="controls">
                                <label>{{trans('form.booking_validity_extend')}}</label>
                                {!! Form::select('booking_validity', $booking_validity, $data->EXPIERY_DATE_TIME_DIF, ['class'=>'form-control mb-1 select2', 'id' => 'booking_validity', 'placeholder' => 'Select Validation Extend Time']) !!}
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
                                use Carbon\Carbon;
                                $startTime = Carbon::now();

                                $endTime = Carbon::parse($data->EXPIERY_DATE_TIME);


                                $totalDuration =  $startTime->diff($endTime)->format('%H:%I:%S')." Minutes";
                                // $totalDuration =  $startTime->diffInHours($endTime)." Minutes";

                                ?>
                                <p class="danger">{{  date('d-m-Y h:i a', strtotime($data->EXPIERY_DATE_TIME))  }}</p>
                                {{-- <p class="danger"><strong>{{ $totalDuration }}</strong></p>
                                <p class="danger"><strong>{{ $startTime }}</strong></p>
                                <p class="danger"><strong>{{ $endTime }}</strong></p> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr style="background-color: green;">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered " >
                            <thead>
                            <tr>
                                <th style="width: 5%">SL.</th>
                                <th style="width: 50%">Details</th>
                                <th style="width: 15%">Regular Price</th>
                                <th style="width: 15%">Installment Price</th>
                                <th style="width: 15%">Payment</th>
                                <th style="width: 15%">Action</th>

                            </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td style="width: 5%">0</td>
                                    <td style="width: 50%">Default Price</td>
                                    <td style="width: 15%">{{ number_format($total_price_regular,2) }}</td>
                                    <td style="width: 15%">{{ number_format($total_price_installment,2) }}</td>
                                    <td style="width: 15%">
                                        <div class="form-group {!! $errors->has('payment') ? 'error' : '' !!}">

                                            <div class="controls">

                                                {!! Form::number('payment', null, [ 'class' => 'form-control mb-1 summernote', 'placeholder' => 'Enter payment amount', 'tabindex' => 1, 'step' => '0.01' ]) !!}
                                                {!! $errors->first('payment', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </td>
                                    <td style="width: 15%">
                                        <a class="btn btn-warning btn-sm">Save</a>
                                    </td>
                                </tr>

                                @if($bundle && count($bundle))
                                @foreach($bundle as $key => $row)
                                @php
                                $total_price_regular = $row->NON_BUNDLE_REGULAR_PRICE + $row->TOTAL_REGULAR_BUNDLE_PRICE ?? 0;
                                $total_price_installment = $row->NON_BUNDLE_INSTALLMENT_PRICE + $row->TOTAL_INSTALLMENT_BUNDLE_PRICE ?? 0;
                                @endphp
                                <tr>
                                    <td style="width: 5%">{{ $key+1 }}</td>
                                    <td style="width: 50%">{{ $row->BUNDLE_NAME_PUBLIC }} ({{ $row->F_BUNDLE_NO }}) ({{ $row->INV_COUNT }})</td>
                                    <td style="width: 15%">{{ number_format($total_price_regular,2) }}</td>
                                    <td style="width: 15%">{{ number_format($total_price_installment,2) }}</td>
                                    <td style="width: 15%">
                                        <div class="form-group {!! $errors->has('payment') ? 'error' : '' !!}">

                                            <div class="controls">

                                                {!! Form::number('payment', null, [ 'class' => 'form-control mb-1 summernote', 'placeholder' => 'Enter payment amount', 'tabindex' => 1, 'step' => '0.01' ]) !!}
                                                {!! $errors->first('payment', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </td>
                                    <td style="width: 15%">
                                        <a class="btn btn-warning btn-sm">Save</a>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
</div>
</div>
</div>

@endsection
<!--push from page-->
@push('custom_js')


<!-- Typeahead.js Bundle -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/book.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/book_order.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/js/common.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
@endpush('custom_js')
