@extends('admin.layout.master')

@section('bank_to_bank_xfer','active')

@section('title') Request Internal Transfer @endsection
@section('page-name')Request Internal Transfer @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Request Internal Transfer</li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.css') }}">
    <style>
        #scrollable-dropdown-menu .tt-menu {max-height: 260px;overflow-y: auto;width: 100%;border: 1px solid #333;border-radius: 5px;}
        .twitter-typeahead {display: block !important;}
    </style>
@endpush('custom_css')

@section('content')
<div class="card card-success min-height">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control" style="text-transform: capitalize;">Payment Entry&nbsp;</h4>

        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
        </div>
        @if($errors->any())
        <div class="alert bg-danger alert-dismissible mt-1 text-center" role="alert">
            {{ implode($errors->all(':message')) }}
        </div>
        @endif
    </div>
    <div class="card-content collapse show">
        <div class="card-body">
            {!! Form::open([ 'route' => 'admin.account_to_bank.store', 'method' => 'post', 'class' => 'form-horizontal paymentEntryFrm prev_duplicat_frm', 'files'
            => true , 'novalidate']) !!}
            {!! Form::hidden('ixfer_id', $data['edit_data']->PK_NO ?? 0) !!}

            <div class="form-body">
                <div class="row">
                    <div class="offset-md-4 col-md-4">
                        <div class="form-group {!! $errors->has('from_payment_acc_no') ? 'error' : '' !!}">
                            <label>Select From Account<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::select('from_payment_acc_no',  $data['payment_bank'] ?? [], $data['edit_data']->F_FROM_ACC_PAYMENT_BANK_ACC_NO ?? null, [ 'class' => 'form-control mb-1 select2
                                ', 'placeholder' => 'Please select Account', 'data-validation-required-message' => 'This field is required',  'id' => 'from_payment_acc_no', isset($data['edit_data']) ? 'disabled' : '' ]) !!}

                                {!! $errors->first('from_payment_acc_no', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mt-2">
                        <a href="javascript:void(0)">Actual : <span id="from_actual">{{ isset($data['from_balance']) ? number_format($data['from_balance']->BALANCE_ACTUAL,2) : '0.00' }}</span></a>
                    </div>
                    <div class="col-md-2 mt-2">
                        <a href="javascript:void(0)">Buffer : <span id="from_buffer">{{ isset($data['from_balance']) ? number_format($data['from_balance']->BALACNE_BUFFER,2) : '0.00' }}</span></a>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="offset-md-4 col-md-4">
                        <div class="form-group {!! $errors->has('is_cash_in') ? 'error' : '' !!}">
                            <label>Select Cash Flow<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::select('is_cash_in',  [0=>'CASH From 1 to 2',1=>'CASH From 2 to 1'] ?? [],$data['edit_data']->IS_IN ??  null, [ 'class' => 'form-control mb-1 select2
                                ', 'placeholder' => 'Please select Cash Flow', 'data-validation-required-message' => 'This field is required',  'id' => 'is_cash_in' ]) !!}

                                {!! $errors->first('is_cash_in', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="offset-md-4 col-md-4">
                        <div class="form-group {!! $errors->has('to_payment_acc_no') ? 'error' : '' !!}">
                            <label>Select To Account<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::select('to_payment_acc_no',  $data['all_payment_bank'] ?? [],$data['edit_data']->F_TO_ACC_PAYMENT_BANK_ACC_NO ?? null, [ 'class' => 'form-control mb-1 select2
                                ', 'placeholder' => 'Please select ','data-validation-required-message' => 'This field is required',  'id' => 'to_payment_acc_no', isset($data['edit_data']) ? 'disabled' : '' ]) !!}

                                {!! $errors->first('to_payment_acc_no', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                    @if ($data['user']->F_ROLE_NO == 1)
                    <div class="col-md-2 mt-2">
                        <a href="javascript:void(0)">Actual : <span id="to_actual">{{ isset($data['to_balance']) ? number_format($data['to_balance']->BALANCE_ACTUAL,2) : '0.00' }}</span></a>
                    </div>
                    <div class="col-md-2 mt-2">
                        <a href="javascript:void(0)">Buffer : <span id="to_buffer">{{ isset($data['to_balance']) ? number_format($data['to_balance']->BALACNE_BUFFER,2) : '0.00' }}</span></a>
                    </div>
                    @endif
                </div>
                <div class="row">
                    <div class="offset-md-4 col-md-4">
                        <div class="form-group {!! $errors->has('payment_method') ? 'error' : '' !!}">
                            <label>Select Payment Method<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::select('payment_method',  $data['party_payment_method'] ?? [],$data['edit_data']->F_ACC_CUSTOMER_PAYMENT_METHOD_NO ?? null, [ 'class' => 'form-control mb-1 select2
                                ', 'placeholder' => 'Please select', 'data-validation-required-message' => 'This field is required',  'id' => 'payment_method' ]) !!}

                                {!! $errors->first('payment_method', '<label class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-md-4 col-md-4">
                        <div class="form-group {!! $errors->has('payment_amount') ? 'error' : '' !!}">
                            <label>Payment Amount (RM)<span class="text-danger">*</span></label>
                            <div class="controls">
                                {!! Form::number('payment_amount', $data['edit_data']->ENTERED_MR_AMOUNT ?? null,[ 'class' => 'form-control mb-1',
                                'data-validation-required-message' => 'This field is required','placeholder' => 'Payment amount (RM)','min' => 1, 'id' => 'payment_amount', 'step' => '0.01']) !!}
                                {!! $errors->first('payment_amount', '<label
                                    class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-md-4 col-md-4">
                        <div class="form-group {!! $errors->has('payment_note') ? 'error' : '' !!}">
                            <label>Paymet Note</label>
                            <div class="controls">
                                {!! Form::textarea('payment_note', $data['edit_data']->NARRATION ?? null,[ 'class' => 'form-control mb-1', 'placeholder' =>
                                'Paymet note','id' => 'payment_note','rows' => 3]) !!}
                                {!! $errors->first('payment_note', '<label
                                    class="help-block text-danger">:message</label>') !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-md-4 col-md-4">
                        <h5>Upload Photo/Pdf<span class="required"></span></h5>
                            <div class="form-group {!! $errors->has('is_active') ? 'error' : '' !!}">
                                <div class="controls">
                                    <div class="fileupload @if(!empty($data['edit_data']->ATTACHMENT_PATH))  {{'fileupload-exists'}} @else {{'fileupload-new'}} @endif " data-provides="fileupload" >
                                        <span class="fileupload-preview fileupload-exists thumbnail" style="max-width: 150px; max-height: 120px;">
                                            @if(!empty($data['edit_data']->ATTACHMENT_PATH))
                                            <?php
                                            $extension = pathinfo(storage_path($data['edit_data']->ATTACHMENT_PATH), PATHINFO_EXTENSION);
                                            if ($extension == 'pdf') {
                                            ?>
                                            <a href="{{asset('/').$data['edit_data']->ATTACHMENT_PATH}}" target="_blank">SHOW PDF</a>
                                            <?php }else{   ?>
                                            <a href="{{asset('/').$data['edit_data']->ATTACHMENT_PATH}}" target="_blank"><img src="{{asset('/').$data['edit_data']->ATTACHMENT_PATH}}" alt="Photo" class="img-fluid" height="150px" width="120px"/></a>
                                            <?php } ?>
                                            @endif
                                        </span>
                                        <span>
                                        <label class="btn btn-info text-white btn-file btn-sm">
                                        <span class="fileupload-new">
                                        <i class="la la-file-image-o"></i> Select Image
                                        </span>
                                        <span class="fileupload-exists">
                                        <i class="la la-reply"></i> Change
                                        </span>
                                        {!! Form::file('file', Null,[ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'IS ACTIVE', 'tabindex' => 5]) !!}
                                        </label>
                                        <a href="#" class="btn fileupload-exists btn-danger btn-sm" data-dismiss="fileupload" id="remove-thumbnail">
                                        <i class="la la-times"></i> Remove
                                        </a>
                                        </span>
                                        <br>
                                        <span class="MainToUpload edit-3-color" style="font-size: 12px; color: #bf4c4c;">File types jpg, png, pdf.</span>
                                    </div>
                                    @if ($errors->has('image'))
                                    <span class="alert alert-danger">
                                        <strong>{{ $errors->first('image') }}</strong>
                                    </span>
                                    @endif
                            </div>

                         </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-actions text-center">
                            <a href="{{route('admin.account_to_bank_list.view')}}" class="btn btn-warning mr-1"><i class="ft-x"></i> {{ trans('form.btn_cancle') }}</a>
                            @if (!isset($data['edit_data']) || (isset($data['edit_data']) && $data['edit_data']->IS_VERIFIED == 0))
                            <button type="submit" class="btn bg-primary bg-darken-1 text-white mr-1" name="submit" value="{{ isset($data['edit_data']) ? 'update' : 'save' }}">
                            <i class="la la-check-square-o"></i> {{ isset($data['edit_data']) ? trans('form.btn_update') : trans('form.btn_save') }} </button>
                            @endif
                            @if ((isset($data['edit_data']) && $data['edit_data']->IS_VERIFIED == 0) && $data['user']->F_ROLE_NO == 1)
                            <button type="submit" class="btn bg-success bg-darken-1 text-white mr-1" name="submit" value="accept">
                            <i class="la la-check-circle-o"></i> Accept </button>

                            <button type="submit" class="bg-danger btn text-white btn-md mr-1" name="submit" value="decline" style="font-size: 1rem">
                            <i class="ft-x"></i> Decline </button>
                             @endif
                         </div>
                     </div>
                </div>
            </div>
            {!! Form::close() !!}
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="AddPaymentType" tabindex="-1" role="dialog" aria-labelledby="brand_name" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="source_name">Enter New Payment Type</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                {!! Form::open(['route' => 'admin.account_to_other.type.store','method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                    @csrf
                <div class="modal-body">
                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                        <label>Enter Payment Type<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('name', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter Type']) !!}
                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-secondary btn-sm" data-dismiss="modal" value="Close">
                    <input type="submit" class="btn btn-primary btn-sm submit-btn" value="Save">
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection


@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.min.js') }}"></script>

<script>
    var get_url = $('#base_url').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on('change','#from_payment_acc_no',function(){
        get_balance($(this).val(),'from_ix');
    })
    $(document).on('change','#to_payment_acc_no',function(){
        get_balance($(this).val(),'to_ix');
    })
    function get_balance(id,type) {

        $.ajax({
            type:'POST',
            url:get_url+'/postAccountBalanceInfo',
            data: {
                id:id,
                type:type
            },
            beforeSend: function () {
                $("body").css("cursor", "progress");
            },
            success: function (data) {
                if (data != 0) {
                    console.log(data);
                    if (type == 'from_ix') {
                        $('#from_actual').text(parseFloat(data.BALANCE_ACTUAL).toFixed(2));
                        $('#from_buffer').text(parseFloat(data.BALACNE_BUFFER).toFixed(2));
                    }else{
                        $('#to_actual').text(parseFloat(data.BALANCE_ACTUAL).toFixed(2));
                        $('#to_buffer').text(parseFloat(data.BALACNE_BUFFER).toFixed(2));
                    }
                }else{
                    if (type == 'from_ix') {
                        $('#from_actual').text('0.00');
                        $('#from_buffer').text('0.00');
                    }else{
                        $('#to_actual').text('0.00');
                        $('#to_buffer').text('0.00');
                    }
                }
            },
            complete: function (data) {
                $("body").css("cursor", "default");
            }
        });
    }
</script>
@endpush('custom_js')
