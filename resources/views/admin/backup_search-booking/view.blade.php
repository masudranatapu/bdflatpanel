@extends('admin.layout.master')

@section('Product Management','open')
@section('booking_list','active')

@section('title') @lang('booking.view_booking') @endsection
@section('page-name') @lang('booking.view_booking') @endsection



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


// print_r($data->EXPIERY_DATE_TIME_DIF);
// die();

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
                    {!! Form::open([ 'route' => ['admin.booking.put', $data->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                    {{-- {!! Form::hidden('post_code',$data->getCustomerPostCode($data->F_CUSTOMER_NO,$data->F_RESELLER_NO,$data->IS_RESELLER) ?? 0, ['id'=>'post_code']) !!} --}}

                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <div id="cus_details" style="border: 1px solid #c4c4c4;border-radius: 5px;">
                                 <div class="form-group {!! $errors->has('book_customer') ? 'error' : '' !!}">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th colspan="5" style="background: aliceblue;">Customer Information</th>
                                                </tr>
                                                <tr>
                                                    <th><small><strong>Name</strong></small></th>
                                                    <th><small><strong>Phone</strong></small></th>
                                                    <th><small><strong>Email</strong></small></th>
                                                    <th><small><strong>Customer Type</strong></small></th>
                                                    <th><small><strong>{{trans('form.sales_agent')}}</strong></small></th>
                                                </tr>
                                            </thead>
                                            <tbody id="append_cus">
                                                <td><small>{{ $data->getCustomer->NAME ?? $data->getReseller->NAME ?? '' }}</small></td>
                                                <td><small>{{ $data->getCustomer->MOBILE_NO ?? $data->getReseller->MOBILE_NO ?? '' }}</small></td>
                                                <td><small>{{ $data->getCustomer->EMAIL ?? $data->getReseller->EMAIL ?? '' }}</small></td>
                                                <td>
                                                    <small>{{$data->IS_RESELLER == 0 ? 'Customer' : 'Reseller' }}</small>

                                                </td>
                                                <td><small>{{$data->BOOKING_SALES_AGENT_NAME }}</small></td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm" id="invoicetable">
                                    <thead>
                                        <tr>
                                            <th style="width: 200px">@lang('tablehead.image')</th>
                                            <th style="width: 200px">@lang('tablehead.product_name')</th>
                                            <th class="" style="width: 70px;">@lang('tablehead.warehouse')</th>
                                            <th class="" style="width: 70px;">@lang('tablehead.available_qty')</th>
                                            <th class="" style="width: 70px;">@lang('tablehead.booking_qty')</th>
                                            <th class="" style="width: 70px;">Price</th>
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
                                            {{-- <td id="sm_amount_final"></td> --}}
                                            <td></td>
                                        </tr>
                                        <tr style="text-align: center">
                                            <td></td>
                                            <td>Total Freight Cost</td>
                                            <td>
                                                <div class="controls">
                                                    <select name="customer_preferred_all" id="customer_preferred_all" class="select2" disabled>

                                                        <option value="0">Select Air/Sea</option>
                                                        <option value="air">AIR</option>
                                                        <option value="sea">SEA</option>

                                                    </select>
                                                </div>
                                            </td>
                                            <td></td>
                                            <td><span id="given_freight_td">Given Freight: <span id="given_freight">{{ number_format($data->FREIGHT_COST,2, '.', '') }}</span></span></td>
                                            {{-- <td></td> --}}
                                            <td><input type="number" name="freight_cost_total" style="width: 60px;text-align: center;" class="form-control input-sm ml-2" id="amount_freight" disabled></td>
                                            {{-- <td><input type="number" name="freight_cost_total_ins" style="width: 60px;text-align: center;" class="form-control input-sm ml-2" id="amount_freight2"></td> --}}
                                            <td></td>
                                        </tr>
                                        <tr style="text-align: center">
                                            <td></td>
                                            <td>Postage Cost</td>
                                            <td>
                                                <label>{!! Form::radio('postage',1,true,['id'=>'is_sm_cost1', 'disabled' => true]) !!} SM COST</label>&nbsp;
                                                <label>{!! Form::radio('postage',0,false,['id'=>'is_sm_cost2', 'disabled' => true]) !!} SS COST</label>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td><input type="number" name="postage_regular_cost_final" style="width: 60px;text-align: center;" class="form-control input-sm ml-2" id="postage_cost" readonly></td>
                                            {{-- <td><input type="number" name="postage_ins_cost_final" style="width: 60px;text-align: center;" class="form-control input-sm ml-2" id="postage_cost2"></td> --}}
                                            <td></td>
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

                                    {!! Form::textarea('booking_note', null, [ 'class' => 'form-control mb-1 summernote', 'placeholder' => 'Enter short booking note', 'tabindex' => 16, 'rows' => 3, 'disabled' => true ]) !!}
                                    {!! $errors->first('booking_note', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="controls">
                                    <label>{{trans('form.booking_validity')}}<span class="text-danger">*</span></label>
                                    {!! Form::select('booking_validity', $booking_validity, $data->EXPIERY_DATE_TIME_DIF, ['class'=>'form-control mb-1 select2', 'data-validation-required-message' => 'This field is required', 'id' => 'booking_validity', 'disabled' => true]) !!}
                                    {!! $errors->first('booking_validity', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions mt-10 text-center">
                    <a href="{{ route('admin.booking.list')}}" class="btn btn-warning mr-1"><i class="ft-x"></i> @lang('form.btn_cancle')</a>

                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
    </div>
</div>
</div>

@endsection
<!--push from page-->
@push('custom_js')

{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> --}}
<!-- Bootstrap JS -->
{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> --}}
<!-- Typeahead.js Bundle -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/cus_pro_search.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/book_order.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/js/common.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
@endpush('custom_js')
