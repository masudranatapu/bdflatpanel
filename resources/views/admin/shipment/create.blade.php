@extends('admin.layout.master')

@section('Shipping','open')
@section('list_shipping','active')

@section('title')
{{ $data['data'] != null ? 'Shipping | Edit' : 'Shipping | Create' }}
@endsection
@section('page-name')
{{ $data['data'] != null ? 'Edit Shipping' : 'Create Shipping' }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('shipping.breadcrumb_dashboard_title')</a>
    </li>
    <li class="breadcrumb-item active">{{ $data['data'] != null ? trans('shipping.breadcrumb_edit_shipping_text') : trans('shipping.breadcrumb_create_shipping_text')}}
    </li>
@endsection

<?php

//$logistics_carrier_arr = Config::get('static_array.logistics_carrier');
// dd($data['data']);

?>
<!--push from page-->
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
@endpush('custom_css')
@section('content')
    <div class="card card-success min-height">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-colored-form-control">
            <i class="ft-plus text-primary"></i>{{ $data['data'] != null ? 'Edit Shipping' : 'Create Shipping' }}</h4>
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
                {!! Form::open([ 'route' => 'admin.shipment.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                @csrf
                {!! Form::hidden('is_update', $data['data'] != null ? $data['data']->PK_NO : 0) !!}
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {!! $errors->has('shipping_agent') ? 'error' : '' !!}">
                                <label>C&F Agent (UK)<span class="text-danger">*</span></label>
                                <div class="controls">
                                    <select class="form-control" name="shipping_agent">
                                        @if(isset($address) && count($address) > 0)
                                        @foreach($address as $key => $addr)
                                        @if($addr->ADDRESS_TYPE == 'Shipping_agent')
                                        <option
                                        data-company="{{ $addr->NAME}}"
                                        data-addr1="{{ $addr->ADDRESS_LINE_1 }}"
                                        data-addr2="{{ $addr->ADDRESS_LINE_2 }}"
                                        data-addr3="{{ $addr->ADDRESS_LINE_3 }}"
                                        data-addr3="{{ $addr->ADDRESS_LINE_4 }}"
                                        data-phone="{{ $addr->TEL_NO }}"
                                        data-vat="{{ $addr->VAT_EORI_NO }}"
                                        value="{{ $addr->PK_NO }}"

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
                        <div class="col-md-4">
                            <div class="form-group {!! $errors->has('receiving_agent') ? 'error' : '' !!}">
                                <label>C&F Agent (Malaysia)<span class="text-danger">*</span></label>
                                <div class="controls">
                                    <select class="form-control" name="receiving_agent">
                                        @if(isset($address) && count($address) > 0)
                                         @foreach($address as $key => $addr)
                                         @if($addr->ADDRESS_TYPE == 'Receiving_agent')
                                         <option
                                         data-company="{{ $addr->NAME}}"
                                         data-addr1="{{ $addr->ADDRESS_LINE_1 }}"
                                         data-addr2="{{ $addr->ADDRESS_LINE_2 }}"
                                         data-addr3="{{ $addr->ADDRESS_LINE_3 }}"
                                         data-addr3="{{ $addr->ADDRESS_LINE_4 }}"
                                         data-phone="{{ $addr->TEL_NO }}"
                                         data-vat="{{ $addr->VAT_EORI_NO }}"
                                         value="{{ $addr->PK_NO }}"
                                         {{ ($data['data'] != null)  ? ( $data['data']->F_DESTINATION_ADDRESS == $addr->PK_NO ? 'selected' : '') : ''  }}

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
                        <div class="col-md-4">
                            <div class="form-group {!! $errors->has('from_address') ? 'error' : '' !!}">
                                <label>@lang('shipping.from_address')<span class="text-danger">*</span></label>

                                <div class="controls">
                                    <select class="form-control" name="form_address">
                                        @if(isset($address) && count($address) > 0)
                                        @foreach($address as $key => $addr)
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
                                         {{ ($data['data'] != null)  ? ( $data['data']->F_FROM_ADDRESS == $addr->PK_NO ? 'selected' : '') : ''  }}
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

                        <div class="col-md-4">
                            <div class="form-group {!! $errors->has('delivery_address') ? 'error' : '' !!}">
                                <label>@lang('shipping.delivery_address')<span class="text-danger">*</span></label>
                                <div class="controls">
                                    <select class="form-control" name="ship_to">

                                        @if(isset($address) && count($address) > 0)
                                         @foreach($address as $key => $addr)
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
                                         {{ ($data['data'] != null)  ? ( $data['data']->F_SHIP_TO_ADDRESS == $addr->PK_NO ? 'selected' : '') : ''  }}

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
                        <div class="col-md-4">
                            <div class="form-group {!! $errors->has('bill_to') ? 'error' : '' !!}">
                            <label  for="">Billing Address<span class="text-danger">*</span></label>

                                <div class="controls">
                                    <select class="form-control" name="bill_to">
                                        @if(isset($address) && count($address) > 0)
                                         @foreach($address as $key => $addr)
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
                                         {{ ($data['data'] != null)  ? ( $data['data']->F_BILL_TO_ADDRESS == $addr->PK_NO ? 'selected' : '') : ''  }}

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
                        <div class="col-md-4">
                            <div class="form-group {!! $errors->has('signature') ? 'error' : '' !!}">
                                <label >Signatory<span class="text-danger">*</span></label>
                                <div class="controls">
                                    <select class="form-control" name="signature">
                                        @if(isset($signature) && count($signature) > 0)
                                        @foreach($signature as $key => $sign)

                                         <option value="{{ $sign->PK_NO }}" data-img_path="{{ $sign->IMG_PATH }}" {{ ($data['data'] != null)  ? ( $data['data']->F_SIGNATURE == $sign->PK_NO ? 'selected' : '') : ''  }} >
                                         {{ $sign->NAME}}
                                         </option>

                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                                <div class="col-md-4">
                                    <div class="form-group {!! $errors->has('from_warehouse') ? 'error' : '' !!}">
                                        <div class="controls">
                                            <label>@lang('shipping.from_warehouse')<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::select('from_warehouse', $data['warehouse'], $data['data'] != null ? $data['data']->F_FROM_INV_WAREHOUSE_NO : null, ['class'=>'form-control mb-1', 'data-validation-required-message' => 'This field is required', $data['data'] != null ? '' : 'placeholder' => 'From Warehouse', 'id' => 'from_warehouse']) !!}
                                                {!! $errors->first('from_warehouse', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group {!! $errors->has('to_warehouse') ? 'error' : '' !!}">
                                        <div class="controls">
                                            <label>@lang('shipping.to_warehouse')<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::select('to_warehouse', $data['warehouse'], $data['data'] != null ? $data['data']->F_TO_INV_WAREHOUSE_NO : null, ['class'=>'form-control mb-1','id' => 'to_warehouse', 'data-validation-required-message' => 'This field is required', $data['data'] != null ? '' : 'placeholder' => 'To Warehouse']) !!}
                                                {!! $errors->first('to_warehouse', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-md-4">
                                    <div class="form-group {!! $errors->has('box_count') ? 'error' : '' !!}">
                                        <label>@lang('shipping.box_count')</label>
                                        <div class="controls">
                                            {!! Form::number('box_count',  $data['data'] != null ? $data['data']->SENDER_BOX_COUNT : null, ['class'=>'form-control mb-1', 'id' => 'box_count',  'placeholder' => 'Enter Box Count','data-validation-required-message' => 'This field is required',]) !!}
                                            {!! $errors->first('box_count', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div> --}}


                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="shipping_date">@lang('shipping.shipment_date')</label>
                                <input type="text" id="shipping_date" class="form-control pickadate" name="shipping_date" data-toggle="tooltip" title="" value="{{ $data['data'] != null ? date('Y-m-d', strtotime($data['data']->SCH_DEPARTING_DATE)) : '' }}" data-validation-required-message ="This field is required">
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="delivery_date">@lang('shipping.shipment_arrival_date')</label>
                                <input type="text" id="delivery_date" class="form-control pickadate" name="delivery_date" data-toggle="tooltip" title="" value="{{ $data['data'] != null ? date('Y-m-d', strtotime($data['data']->SCH_ARRIVAL_DATE)) : '' }}" data-validation-required-message ="This field is required">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="packing_process_date">Packaging Date</label>
                                <input type="text" id="packing_process_date" class="form-control pickadate" name="packing_process_date"  title="" value="{{ $data['data'] != null ? date('Y-m-d', strtotime($data['data']->PACKING_PROCESS_DATE)) : '' }}" data-validation-required-message ="This field is required">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {!! $errors->has('waybill') ? 'error' : '' !!}">
                                <label>@lang('shipping.waybill')</label>
                                <div class="controls">
                                    {!! Form::number('waybill',  $data['data'] != null ? $data['data']->WAYBILL : null, ['class'=>'form-control mb-1', 'id' => 'waybill',  'placeholder' => 'Enter waybill']) !!}
                                    {!! $errors->first('waybill', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {!! $errors->has('freight_gbp') ? 'error' : '' !!}">
                                <label>@lang('shipping.freight_gbp')</label>
                                <div class="controls">
                                    {!! Form::number('freight_gbp',  $data['data'] != null ? $data['data']->FREIGHT_GBP : null, ['class'=>'form-control mb-1', 'id' => 'freight_gbp',  'placeholder' => 'Enter Freight GBP', 'step' => '0.01']) !!}
                                    {!! $errors->first('freight_gbp', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {!! $errors->has('shipping_type') ? 'error' : '' !!}">
                                <label>@lang('shipping.shipping_type')<span class="text-danger">*</span></label>
                                <div class="controls">
                                    <div>
                                        <label>{!! Form::radio('transport', 'air_freight', true) !!} Air Freight</label>&nbsp;&nbsp;
                                        <label>{!! Form::radio('transport', 'sea_freight', $data['data'] != null && $data['data']->IS_AIR_SHIPMENT==0 ? true : false) !!} Sea Freight</label>&nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {!! $errors->has('logistics_carrier') ? 'error' : '' !!}">
                                <div class="controls">
                                    <label>@lang('shipping.logistics_carrier')<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::select('logistics_carrier', $carrier ?? [], $data['data'] != null ? $data['data']->F_LOGISTICS_CARRIER : null, ['class'=>'form-control mb-1','id' => 'logistics_carrier', 'data-validation-required-message' => 'This field is required']) !!}
                                        {!! $errors->first('logistics_carrier', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" title="ADD CUSTOMER" class="btn btn-primary btn-sm search_mother_btn mt-2" id="carrier_input" data-target="#logistics_carrier_modal" data-toggle="modal">
                                <i class="la la-edit"></i>
                            </button>
                        </div>
                    </div>
                <div class="form-actions mt-10 text-center">
                    <a href="{{ route('admin.shipment.list')}}" class="btn btn-warning mr-1">
                            <i class="ft-x"></i> @lang('shipping.shipping_frm_button_cancel_label')
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="la la-check-square-o"></i> {{ $data['data'] != null ? trans('shipping.shipping_frm_button_update_label') : trans('shipping.shipping_frm_button_save_label')}}
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>


    <div class="modal animated zoomIn text-left" tabindex="-1" role="dialog" aria-labelledby="logistics_carrier_modal" aria-hidden="true" id="logistics_carrier_modal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-content">
                {!! Form::open([ 'route' => 'admin.shipment.carrier', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate','id' => 'balanceTransFrm']) !!}
                {!! Form::hidden('logistic_pk', null,['id' => 'logistic_pk']) !!}
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel23"> Logistics Carrier </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="form-group {!! $errors->has('logistic_name') ? 'error' : '' !!}">
                                <label id="header_label">Logistics Carrier</label>
                                <div class="controls">
                                    {!! Form::text('logistic_name', null, ['class'=>'form-control mb-1 ', 'data-validation-required-message' => 'This field is required','placeholder' => 'Enter Carrier', 'id' => 'logistic_name']) !!}
                                    {!! $errors->first('logistic_name', '<label class="help-block text-danger">:message</label>') !!}

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn grey btn-secondary" data-dismiss="modal" title="Close"><i class="ft-x"></i> Close</button>
                        <button type="button" class="btn btn-info" id="new_carrier" title="ADD NEW LOGISTICS CARRER"><i class="la la-plus"></i> Add New</button>
                        <button type="submit" class="btn btn-primary" title="Save"><i class="la la-save"></i> Save changes</button>
                    </div>
                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
<!--push from page-->
@push('custom_js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
    <script>
        // Use datepicker on the date inputs
        $('.pickadate').pickadate({
            format: 'dd-mm-yyyy',
            formatSubmit: 'dd-mm-yyyy',
            //max:!0,
        });
    </script>
    <script>
        $(document).on('click', '#carrier_input', function(e){
            var carrier_no = $('#logistics_carrier').val();
            var carrier = $('#logistics_carrier option:selected').text();
            $('#myModalLabel23').text('Logistics Carrier');
            $('#header_label').text('Logistics Carrier');
            $('#logistic_name').attr('placeholder','Enter Logistics Carrier');

            $('#logistic_name').val(carrier);
            $('#logistic_pk').val(carrier_no);
            $('#new_carrier').fadeIn();

        })
        $(document).on('click', '#new_carrier', function(e){
            $('#logistic_name').val('');
            $('#logistic_pk').val(0);
            $('#myModalLabel23').text('New Logistics Carrier');
            $('#header_label').text('New Logistics Carrier');
            $('#logistic_name').attr('placeholder','Enter New Logistics Carrier');
            $(this).fadeOut();
        })
    </script>
@endpush('custom_js')
