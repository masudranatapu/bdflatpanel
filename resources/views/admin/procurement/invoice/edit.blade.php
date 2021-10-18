@extends('admin.layout.master')

@section('Procurement','open')
@section('invoice','active')

@section('title') Invoice | Edit @endsection
@section('page-name') @lang('invoice.list_page_sub_title') @endsection


@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.invoice') }}">Edit Invoice </a></li>
<li class="breadcrumb-item active">Edit invoice</li>

@endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/file_upload/image-uploader.min.css')}}">
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">
    <style type="text/css">
        .notForGBP{display: none;}
    </style>
@endpush('custom_css')

<?php
        $parent_vendor              = $parentInvoice->F_VENDOR_NO ?? null;
        $parent_invoice_currency    = $invoice->F_SS_CURRENCY_NO ?? null;

        $roles = userRolePermissionArray();
        $gtotal_qty                     = 0;
        $gtotal_receipt                 = 0;
        $gtotal_flty                    = 0;
        $gtotal_sub_total_gbp_receipt   = 0;
        $gtotal_line_total              = 0;
        $gtotal_line_total_vat_gbp      = 0;
        $gbp_equivalent                 = 0;
        $rec_total_vat_gbp              = 0;
        $gbp_equivalent                 = 0;

// dd($vendors);
?>

@section('content')
<div class="card card-success min-height">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> Edit
        Invoice</h4>
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
            {!! Form::open([ 'route' => ['admin.invoice.update', $invoice->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
            @csrf
            <input type="hidden" name="parent_invoice" value="{{ $invoice->F_PARENT_PRC_STOCK_IN }}">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('vendor') ? 'error' : '' !!}">
                            <label>Vendor<span class="text-danger">*</span></label>
                            <div class="controls">
                                  <select class="form-control mb-1 select2"  data-validation-required-message="This field is required" tabindex="1" id="vendor" name="vendor">
                                    <option> Please select vendor </option>
                                        @if($vendors && count($vendors) > 0 )
                                            @foreach($vendors as $key => $vendor)
                                                <option value="{{$vendor->PK_NO}}" data-loyality="{{$vendor->HAS_LOYALITY}}"
                                            {{ $invoice->F_VENDOR_NO == $vendor->PK_NO ? 'selected' : '' }} >{{$vendor->NAME}}</option>
                                            @endforeach
                                        @endif
                                </select>
                                {!! $errors->first('vendor', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('invoice_no') ? 'error' : '' !!}">
                            <label>Invoice Number<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::text('invoice_no', $invoice->INVOICE_NO,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Please scan or enter', 'tabindex' => 2 ]) !!}
                                {!! $errors->first('invoice_no', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('invoice_date') ? 'error' : '' !!}">
                            <label>Invoice Date<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                </div>
                                <input type='text' class="form-control pickadate" placeholder="Invoice Date" value="{{date('d-m-Y', strtotime($invoice->INVOICE_DATE))}}" name="invoice_date" tabindex="3" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('payment_source') ? 'error' : '' !!}">
                            <label>Account Source<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::select('payment_source', $accSource, $invoice->F_PAYMENT_SOURCE_NO, [ 'class' => 'form-control mb-1 select2', 'placeholder' => 'Please select', 'data-validation-required-message' => 'This field is required', 'tabindex' => 4, 'id' => 'acc_source', 'data-url' => URL::to('bank_acc') ]) !!}
                                {!! $errors->first('payment_source', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('acc_bank') ? 'error' : '' !!}">
                            <label>Account Name<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::select('acc_bank', $bankAcc, $invoice->F_PAYMENT_ACC_NO, [ 'class' => 'form-control mb-1 select2', 'placeholder' => 'Please select', 'data-validation-required-message' => 'This field is required', 'tabindex' => 5, 'id' => 'bank_acc']) !!}
                                {!! $errors->first('acc_bank', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('payment_methods') ? 'error' : '' !!}">
                            <label>Payment Method<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::select('payment_methods', $paymentMethod, $invoice->F_PAYMENT_METHOD_NO, [ 'class' => 'form-control mb-1 select2', 'placeholder' => 'Please select', 'data-validation-required-message' => 'This field is required', 'tabindex' => 6, 'id' => 'payment_method']) !!}
                                {!! $errors->first('payment_methods', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('discount_percentage') ? 'error' : '' !!}">
                            <label>Primary Discount Percentage</label>
                            <div class="controls">
                                {!! Form::number('discount_percentage', $invoice->DISCOUNT_PERCENTAGE,[ 'class' => 'form-control mb-1', 'placeholder' => 'Enter primary discount percentage', 'tabindex' => 7, 'step' => '0.01','min' => 0, 'data-validation-number-message' => 'Please enter max 2 decimal point']) !!}
                                {!! $errors->first('discount_percentage', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('discount_percentage') ? 'error' : '' !!}">
                            <label>Secondary Discount Percentage</label>
                            <div class="controls">
                                {!! Form::number('discount_percentage2', $invoice->DISCOUNT2_PERCENTAGE, [ 'class' => 'form-control mb-1', 'placeholder' => 'Enter secondary discount percentage', 'tabindex' => 8, 'step' => '0.01','min' => 0, 'data-validation-number-message' => 'Please enter max 2 decimal point']) !!}
                                {!! $errors->first('discount_percentage', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('invoice_exact_value') ? 'error' : '' !!}">
                            <label>Invoice Exact Value<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::text('invoice_exact_value', $invoice->INVOICE_EXACT_VALUE, [ 'class' => 'form-control', 'placeholder' => 'Enter invoice exact value', 'data-validation-required-message' => 'This field is required', 'tabindex' => 9]) !!}
                                {!! $errors->first('invoice_exact_value', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('exact_vat') ? 'error' : '' !!}">
                            <label>Exact Vat</label>
                            <div class="controls">
                                {!! Form::number('exact_vat', $invoice->INVOICE_EXACT_VAT, [ 'class' => 'form-control mb-1 ', 'placeholder' => 'Enter exact vat amount', 'tabindex' => 10, 'id' => 'exact_vat', 'step' => '0.01' ]) !!}
                                {!! $errors->first('exact_vat', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('postage') ? 'error' : '' !!}">
                            <label>Local Postage</label>
                            <div class="controls">
                                {!! Form::number('postage', $invoice->INVOICE_EXACT_POSTAGE, [ 'class' => 'form-control mb-1 ', 'placeholder' => 'Enter local postage', 'tabindex' => 11, 'id' => 'postage', 'step' => '0.01' ]) !!}
                                {!! $errors->first('postage', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('purchaser') ? 'error' : '' !!}">
                            <label>Purchaser<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::select('purchaser', $user, $invoice->F_PURCHASER_USER_NO, [ 'class' => 'form-control mb-1 select2', 'placeholder' => 'Please select purchaser', 'data-validation-required-message' => 'This field is required', 'tabindex' => 12]) !!}
                                {!! $errors->first('purchaser', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('currency') ? 'error' : '' !!}">
                            <label>Purchase Currency<span class="text-danger">*</span></label>
                            <div class="controls">
                                <select class="form-control mb-1" data-validation-required-message="This field is required" tabindex="13" name="currency" id="purchase_currency">
                                    @if($currency)
                                    @foreach($currency as $key => $val)
                                    <option value="{{$val->PK_NO}}" data-rate="{{$val->EXCHANGE_RATE_GB}}" data-code="{{$val->CODE}}" {{ $invoice->F_SS_CURRENCY_NO == $val->PK_NO ? 'selected' : '' }}>{{$val->NAME}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                {!! $errors->first('currency', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('has_loyality') ? 'error' : '' !!}">
                            <label>Has Loyality Scheme<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::select('has_loyality', ['1' => 'Yes', '0' => 'No'], $invoice->HAS_LOYALTY, [ 'class' => 'form-control mb-1 select2', 'placeholder' => 'Please select', 'data-validation-required-message' => 'This field is required', 'tabindex' => 14, 'id' => 'has_loyality']) !!}
                                {!! $errors->first('has_loyality', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {!! $errors->has('gbp_to_mr') ? 'error' : '' !!}">
                            <label>GBP To MR Rate<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::number('gbp_to_mr', $invoice->GBP_TO_MR_RATE, [ 'class' => 'form-control mb-1','data-validation-required-message' => 'This field is required', 'step' => '0.001', 'placeholder' => 'Please enter rate', 'tabindex' => 15, 'min' => 0, 'data-validation-number-message' => 'Please enter max 3 decimal point']) !!}
                                {!! $errors->first('gbp_to_mr', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 notForGBP">
                        <div class="form-group {!! $errors->has('gbp_to_ac') ? 'error' : '' !!}">
                            <label> GBP To <span id="alian_currency">GBP</span> Rate</label>
                            <div class="controls">
                                {!! Form::number('gbp_to_ac', $invoice->GBP_TO_AC_RATE, [ 'class' => 'form-control mb-1', 'placeholder' => 'Please enter rate', 'tabindex' => 16, 'step' => '0.001', 'min' => 0, 'data-validation-number-message' => 'Please enter max 3 decimal point','id' => 'gbp_to_ac' ]) !!}
                                {!! $errors->first('gbp_to_ac', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group {!! $errors->has('description') ? 'error' : '' !!}">
                            <label>{{trans('form.notes')}}</label>
                            <div class="controls">

                                {!! Form::textarea('description', $invoice->DESCRIPTION, [ 'class' => 'form-control mb-1 summernote', 'placeholder' => 'Enter short notes or parent invoice reference', 'tabindex' => 16, 'rows' => 3 ]) !!}
                                {!! $errors->first('description', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($items) && count($items) > 0)
                <hr>
                <div class="row">
                    <div class="col-md-12">

                        <div class="form-body">
                            <h4 style="display: inline-block; border-bottom: 2px solid #D58711">All Items in Invoice</h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-sm" id="indextable" style="font-size: 13px;">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">SL.</th>
                                                    <th class="text-center" title="Product variant name">Item Name</th>
                                                    <th class="text-center" title="Receipt title">Receipt Title</th>
                                                    <th class="text-center" title="Product variant barcode" >Bar Code</th>

                                                    <th class="text-center" title="Product received quantity">Rec<br>Qty</th>
                                                    <th class="text-center" title="Product faulty quantity">Flt <br>Qty</th>
                                                    <th class="text-center" title="">Line Total<br>(Receipt)</th>
                                                    <th title="Primary Discount">PD</th>
                                                    <th title="Secondary Discount">SD</th>
                                                    <th class="text-center" title="Unit price without actual price in GBP">Unit Price <br>W/V</th>
                                                    <th class="text-center" title="Unit actual vat in GBP ">Unit <br> Vat </th>
                                                    <th class="text-center" title="Unit total quanty">Unit <br> Total </th>
                                                    <th class="text-center" title="Line total quantity">Line<br>Qty</th>
                                                    <th class="text-center" title="Line Total Actual GBP">Line Total </th>
                                                    <th class="text-center" title="Line Total Actual Vat GBP">Line Vat </th>
                                                    <th class="text-center" title="Received TotalActual GBP">Rec Total</th>
                                                    <th class="text-center" title="Received Total Actual Vat GBP">Rec Vat</th>

                                                    <th class="text-center" title="Line total actual vat in GBP">Vat</th>
                                                    <th class="text-center">@lang('tablehead.tbl_head_action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach($items as $row)
                                                <?php

                                                $gtotal_qty          += $row->QTY;
                                                $gtotal_receipt      += $row->RECIEVED_QTY;
                                                $gtotal_flty         += $row->FAULTY_QTY;
                                                $gtotal_sub_total_gbp_receipt += $row->SUB_TOTAL_GBP_RECEIPT;
                                                $gtotal_line_total += ($row->SUB_TOTAL_GBP_EV + $row->LINE_TOTAL_VAT_GBP);
                                                $gtotal_line_total_vat_gbp += $row->LINE_TOTAL_VAT_GBP;


                                                ?>
                                                <tr>
                                                    <td>{{$loop->index + 1}}</td>
                                                    <td>{{ $row->PRD_VARIANT_NAME }}</td>
                                                    <td>{{ $row->INVOICE_NAME }}</td>
                                                    <td>{{ $row->BAR_CODE }}</td>


                                                    <td class="text-center">{{ $row->RECIEVED_QTY }}</td>
                                                    <td class="text-center">{{ $row->FAULTY_QTY }}</td>
                                                    <td class="text-right">{{ number_format($row->SUB_TOTAL_GBP_RECEIPT,2) }}</td>
                                                    <td>{{ $invoice->DISCOUNT_PERCENTAGE }} %</td>
                                                    <td>{{ $invoice->DISCOUNT2_PERCENTAGE }} %</td>

                                                    <td class="text-right">{{ number_format($row->UNIT_PRICE_GBP_EV,2) }}</td>
                                                    <td class="text-right">{{ number_format($row->UNIT_VAT_GBP,2) }}</td>
                                                    <td class="text-right">{{ number_format(($row->UNIT_PRICE_GBP_EV +$row->UNIT_VAT_GBP),2) }}</td>
                                                    <td class="text-center">{{ $row->QTY }}</td>
                                                    <td class="text-right">{{ number_format(($row->SUB_TOTAL_GBP_EV + $row->LINE_TOTAL_VAT_GBP),2) }}</td>
                                                    <td class="text-right">{{ number_format($row->LINE_TOTAL_VAT_GBP,2) }}</td>
                                                    <td class="text-right">{{ number_format($row->REC_TOTAL_GBP_WITH_VAT,2) }}</td>
                                                    <td class="text-right">{{ number_format($row->REC_TOTAL_GBP_ONLY_VAT,2) }}</td>
                                                    <td>{{ $row->VAT_RATE }}%</td>
                                                    <td class="text-center">
                                                        @if(hasAccessAbility('delete_invoice_details', $roles))
                                                        <a href="{{ route('admin.invoice-details.delete', [$row->PK_NO]) }}" onclick="return confirm('Are You Sure?')" title="INVOICE DELETE"  class="btn btn-xs btn-danger mr-05"><i class="la la-trash"></i></a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach()


                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-center">Total</td>

                                                    <td></td>
                                                    <td class="text-center">{{$gtotal_receipt ?? ''}}</td>
                                                    <td class="text-center">{{$gtotal_flty ?? '' }}</td>
                                                    <td class="text-right">{{number_format($gtotal_sub_total_gbp_receipt ?? 0,2)}}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>

                                                    <td class="text-center">{{ $gtotal_qty ?? '' }}</td>

                                                    <td class="text-right">{{number_format($gtotal_line_total ?? 0,2)}}</td>
                                                    <td class="text-right">{{number_format($gtotal_line_total_vat_gbp ?? 0,2)}}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>
                </div>
                @endif


                @if($invoice->allPhotos && $invoice->allPhotos->count() > 0)
                <hr>
                 <p style="margin-left: 15px;">Images</p>

                    <div class="row">
                        @foreach($invoice->allPhotos as $photo)
                        <div class="col-md-3" id="photo_div_{{$photo->PK_NO}}">
                            <div class="form-group">
                                <div class="img-box" style="border: 2px solid #ccc; display: inline-block;">
                                    <img src="{{asset($photo->RELATIVE_PATH)}}" class="img-fluid" style="width: 200px; height: 200px;">
                                    <div class="img-box-child">
                                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-danger photo-delete" data-id="{{$photo->PK_NO}}"><i class="la la-smile-o"></i>
                                            Delete</button>

                                    </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="input-field">
                                <label class="active">Image<span class="text-danger">*</span></label>
                                <div class="file_upload" style="padding-top: .5rem;" title="Click for photo upload">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions mt-10 text-center">
                <a href="{{ route('admin.invoice')}}" class="btn btn-warning mr-1" title="Cancel" ><i class="ft-x"></i> Cancel </a>
                <button type="submit" class="btn btn-primary" title="Cancel"><i class="la la-check-square-o"></i> Save change </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection

<!--push from page-->
@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/file_upload/image-uploader.min.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>

<script type="text/javascript" src="{{ asset('app-assets/pages/invoice.js')}}"></script>
<script>


   //product photo delete
   $(document).on('click','.photo-delete', function(e){
    var id = $(this).attr('data-id');
    if (!confirm('Are you sure you want to delete the photo')) {
        return false;
    }
    if ('' != id) {
        var pageurl = `{{ URL::to('imvoice_img_delete')}}/`+id;
        $.ajax({
            type:'get',
            url:pageurl,
            async :true,
            beforeSend: function () {
                $("body").css("cursor", "progress");
                //blockUI();
            },
            success: function (data) {
                // console.log(data.status);
                if(data.status == true ){
                    $('#photo_div_'+id).hide();
                } else {
                    alert('something wrong please you should reload the page');
                }

            },
            complete: function (data) {
                $("body").css("cursor", "default");
                //$.unblockUI();
            }
        });
    }


})

function setLoyality(loyality){
    if (loyality == 1) {
        $("#has_loyality").select2().val(1).trigger("change");

   }else{
        $("#has_loyality").select2().val(0).trigger("change");
    }
}

function setPurchaseCurrency(invoice_currency)
{
    $('#purchase_currency').val(invoice_currency);
}

function showHideConversionRate(currency){
    if (currency == 1 || currency == 2) {
        $('.notForGBP').hide();
    }else{
        $('.notForGBP').show();
    }
}

$(document).on('change','#purchase_currency', function(e){
    var currency = $(this).val();
    showHideConversionRate(currency);
})




$(document).on('change', '#vendor', function(e){
    var loyality = $(this).select2().find(":selected").data("loyality");

    setLoyality(loyality);
})


$(document).ready(function () {

    var loyality = $('#vendor').select2().find(":selected").data("loyality");
    setLoyality(loyality);

    var parent_invoice_currency = `{{$parent_invoice_currency}}`;
    setPurchaseCurrency(parent_invoice_currency);
    showHideConversionRate(parent_invoice_currency);


    var yesterday = new Date((new Date()).valueOf()-1000*60*60*24);
    $('.pickadate').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
        max:!0,
    });
})


    $(function () {
        $('.file_upload').imageUploader({
            extensions:[".jpg",".jpeg",".png",".gif",".svg", ".pdf",".JPG",".JPEG",".PNG",".GIF",".SVG", ".PDF"],
            mimes:["image/jpeg","image/png","image/gif","image/svg+xml","application/pdf"]
        });
    });

</script>
@endpush('custom_js')
