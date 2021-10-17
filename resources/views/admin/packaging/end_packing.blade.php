@extends('admin.layout.master')

@section('Shipping','open')
@section('processing_shipping','active')

@section('page-name') {{ 'End Packinging' }} @endsection
@section('title') {{ 'Packinging | End' }} @endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('shipping.breadcrumb_dashboard_title')</a></li>
<li class="breadcrumb-item active">End Packaging</li>
@endsection


@push('custom_css')
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">

<style type="text/css">
    .btn-info, .bg-blue {background-color: #56a3ab !important; border-color: #56a3ab !important;}
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

.btn-sm2{
    padding: 0.68rem 0.75rem;
    border-radius: 0px;
    width: 80px;
}

.box-table .form-control {
    height: 25px !important;
    font-size: 14px !important;
    padding: 0 5px 0 8px !important;
    border-radius: 0px;
}
tr.htr > th{
    padding: 8px !important; font-size: 16px !important;
}
.box-table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th{
    padding: 2px;  font-size: 14px; vertical-align: middle;
}

form.packing_list_frm .form-group {
    margin-bottom: 0.5rem;
}
form.packing_list_frm .label {

    margin-bottom: 0.1rem;
}
.item-amt{pointer-events: none;}
.picker--opened .picker__holder{min-width: 250px !important;}

</style>

@endpush('custom_css')

<?php

$categories_combo       = $data['category_combo'] ?? [];
$roles                  = userRolePermissionArray();
$box_combo              = $data['box_combo'] ?? [];
$shipment_address       = $data['shipment_address'] ?? [];
$shipment_sign          = $data['shipment_sign'] ?? [];
// echo "<pre>";
// print_r($data['shipment_address']);
// die();

?>

@section('content')
<div class="card card-success min-height" >
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i>{{ 'End Packaging' }}  </h4>
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
                {{--
                <div class="col-md-4">
                    {!! Form::open([ 'route' => ['admin.shipping_info.update',$data['rows'][0]->F_SHIPMENT_NO], 'method' => 'post', 'class' => 'form-horizontal packing_list_frm', 'files' => true, 'novalidate' ]) !!}
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class=""  style="padding: 20px; background-color: #eee;">
                                <div class="with-border bg-grey">
                                    <div class="form-group with-border" style="margin-bottom: 5px;">
                                        <div class="input-group" style="margin-bottom: 5px;">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-sm btn-info btn-sm2">Ship No</button>
                                            </div>
                                            <input type="hidden" class="form-control number-only" id="shipment_no" name="shipment_no" placeholder="Enter Shipment No" value="{{$data['rows'][0]->F_SHIPMENT_NO}}" readonly="">
                                            <input type="text" class="form-control number-only"  value="{{$data['rows'][0]->SHIPMENT_NAME}}" readonly="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6" style="padding-right: 5px;">
                                        <div class="form-group {!! $errors->has('date') ? 'error' : '' !!}">
                                            <label>{{trans('form.date')}}<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::text('date', date('d-m-Y',strtotime($data['shipment_info']->SCH_DEPARTING_DATE)), [ 'class' => 'form-control pickadate', 'placeholder' => 'Enter quantity', 'tabindex' => 2, 'id' => 'date', 'required',   'data-validation-required-message' => 'This field is required'] ) !!}
                                                {!! $errors->first('date', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6" style="padding-left: 5px;">
                                        <div class="form-group {!! $errors->has('awb') ? 'error' : '' !!}">
                                            <label>{{trans('form.awb')}}<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::text('awb', $data['shipment_info']->WAYBILL, [ 'class' => 'form-control', 'placeholder' => 'Enter awb', 'tabindex' => 8, 'id' => 'awb',  'required',  'data-validation-required-message' => 'This field is required'] ) !!}
                                                {!! $errors->first('awb', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="bg-primary">
                                <div class="form-group row {!! $errors->has('signature') ? 'error' : '' !!}">
                                    <label class="col-md-4 label-control" for="projectinput5">Signatory</label>
                                    <div class="col-md-8 mx-auto">
                                        <div class="controls">
                                            <select class="form-control" name="signature">
                                                @if(isset($shipment_sign) && count($shipment_sign) > 0)
                                                @foreach($shipment_sign as $key => $sign)

                                                 <option value="{{ $sign->PK_NO }}" data-img_path="{{ $sign->IMG_PATH }}" {{ $data['shipment_info']->F_SIGNATURE == $sign->PK_NO ? 'selected' : ''  }}>
                                                 {{ $sign->NAME}}
                                                 </option>

                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row {!! $errors->has('form_address') ? 'error' : '' !!}">
                                    <label class="col-md-4 label-control" for="projectinput5">From Address</label>
                                    <div class="col-md-8 mx-auto">
                                        <div class="controls">
                                            <select class="form-control" name="form_address">
                                                @if(isset($shipment_address) && count($shipment_address) > 0)
                                                @foreach($shipment_address as $key => $addr)
                                                @if($addr->ADDRESS_TYPE == 'From')
                                                 <option
                                                 data-company="{{ $addr->NAME}}"
                                                 data-addr1="{{ $addr->ADDRESS_LINE_1 }}"
                                                 data-addr2="{{ $addr->ADDRESS_LINE_2 }}"
                                                 data-addr3="{{ $addr->ADDRESS_LINE_3 }}"
                                                 data-addr3="{{ $addr->ADDRESS_LINE_4 }}"
                                                 data-phone="{{ $addr->TEL_NO }}"
                                                 data-vat="{{ $addr->VAT_EORI_NO }}"
                                                 value="{{ $addr->PK_NO }}"
                                                 {{ $data['shipment_info']->F_FROM_ADDRESS == $addr->PK_NO ? 'selected' : ''  }} >
                                                 {{ $addr->NAME}}
                                                 </option>
                                                @endif
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row {!! $errors->has('ship_to') ? 'error' : '' !!}">
                                    <label class="col-md-4 label-control" for="projectinput5">Ship To</label>
                                    <div class="col-md-8 mx-auto">
                                        <div class="controls">
                                            <select class="form-control" name="ship_to">
                                                @if(isset($shipment_address) && count($shipment_address) > 0)
                                                 @foreach($shipment_address as $key => $addr)
                                                 @if($addr->ADDRESS_TYPE == 'Ship_to')
                                                 <option
                                                 data-company="{{ $addr->NAME}}"
                                                 data-addr1="{{ $addr->ADDRESS_LINE_1 }}"
                                                 data-addr2="{{ $addr->ADDRESS_LINE_2 }}"
                                                 data-addr3="{{ $addr->ADDRESS_LINE_3 }}"
                                                 data-addr3="{{ $addr->ADDRESS_LINE_4 }}"
                                                 data-phone="{{ $addr->TEL_NO }}"
                                                 data-vat="{{ $addr->VAT_EORI_NO }}"
                                                 value="{{ $addr->PK_NO }}"
                                                 {{ $data['shipment_info']->F_SHIP_TO_ADDRESS == $addr->PK_NO ? 'selected' : ''  }}
                                                 >
                                                 {{ $addr->NAME}}
                                                 </option>
                                                 @endif
                                                 @endforeach
                                                 @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row {!! $errors->has('bill_to') ? 'error' : '' !!}">
                                    <label class="col-md-4 label-control" for="projectinput5">Bill TO</label>
                                    <div class="col-md-8 mx-auto">
                                        <div class="controls">
                                            <select class="form-control" name="bill_to">
                                                @if(isset($shipment_address) && count($shipment_address) > 0)
                                                 @foreach($shipment_address as $key => $addr)
                                                 @if($addr->ADDRESS_TYPE == 'Bill_to')
                                                 <option
                                                 data-company="{{ $addr->NAME}}"
                                                 data-addr1="{{ $addr->ADDRESS_LINE_1 }}"
                                                 data-addr2="{{ $addr->ADDRESS_LINE_2 }}"
                                                 data-addr3="{{ $addr->ADDRESS_LINE_3 }}"
                                                 data-addr3="{{ $addr->ADDRESS_LINE_4 }}"
                                                 data-phone="{{ $addr->TEL_NO }}"
                                                 data-vat="{{ $addr->VAT_EORI_NO }}"
                                                 value="{{ $addr->PK_NO }}"
                                                 {{ $data['shipment_info']->F_BILL_TO_ADDRESS == $addr->PK_NO ? 'selected' : ''  }}
                                                 >
                                                 {{ $addr->NAME}}
                                                 </option>
                                                 @endif
                                                 @endforeach
                                                 @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row {!! $errors->has('destination') ? 'error' : '' !!}">
                                    <label class="col-md-4 label-control" for="projectinput5">Destination Agent</label>
                                    <div class="col-md-8 mx-auto">
                                        <div class="controls">
                                            <select class="form-control" name="destination">
                                                @if(isset($shipment_address) && count($shipment_address) > 0)
                                                 @foreach($shipment_address as $key => $addr)
                                                 @if($addr->ADDRESS_TYPE == 'Destination')
                                                 <option
                                                 data-company="{{ $addr->NAME}}"
                                                 data-addr1="{{ $addr->ADDRESS_LINE_1 }}"
                                                 data-addr2="{{ $addr->ADDRESS_LINE_2 }}"
                                                 data-addr3="{{ $addr->ADDRESS_LINE_3 }}"
                                                 data-addr3="{{ $addr->ADDRESS_LINE_4 }}"
                                                 data-phone="{{ $addr->TEL_NO }}"
                                                 data-vat="{{ $addr->VAT_EORI_NO }}"
                                                 value="{{ $addr->PK_NO }}"
                                                 {{ $data['shipment_info']->F_DESTINATION_ADDRESS == $addr->PK_NO ? 'selected' : ''  }}
                                                 >
                                                 {{ $addr->NAME}}
                                                 </option>
                                                 @endif
                                                 @endforeach
                                                 @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary btn-sm col-sm-12" id="btn-add-item"><i class="fa fa-plus"></i> Save</button>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary btn-sm col-sm-12" id="btn-add-item"><i class="fa fa-plus"></i> Save and Send</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div> --}}

                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-12" style="">
                        <div class=""  style="padding: 20px; background-color: #eee;">
                            <div class="with-border">
                                <h4 class="box-title col-md-12 text-info pl-0">Packing Product List <a href="{{route('admin.packaginglist.pdf',['shipment_no' => $data['rows'][0]->F_SHIPMENT_NO])}}" class="btn btn-info pull-right btn-xs" style="padding-left: 10px; padding-right: 10px;" title="DOWNLOAD PDF">PDF</a></h4>

                            </div>
                            <div class="box-body table-responsive" >
                                <div id="packingItem">
                                    @include('admin.packaging._packing_item', $data)
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
</div>


@endsection
<!--push from page-->
@push('custom_js')

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script>
    // Use datepicker on the date inputs
    $('.pickadate').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
        max:!0,
    });
</script>


@endpush('custom_js')
