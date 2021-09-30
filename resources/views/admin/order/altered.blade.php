@extends('admin.layout.master')

@section('Order Management','open')
@section('list_altered_order','active')

@section('title') Alter Order List @endsection
@section('page-name') Alter Order List @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_title')</a></li>
    <li class="breadcrumb-item active">@lang('order.breadcrumb_sub_title')</li>
    <li class="breadcrumb-item "><a href="{{ URL::to('api/update-status') }}" class="link btn btn-sm btn-success text-white">Refresh</a> </li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
<style>
    .f12{font-size: 12px;}
    .w100{width: 100px;}
    #process_data_table td{vertical-align: middle;}
    .order-type{display: inline-block; margin-right: 10px;}
    .order-type label {cursor: pointer;}
    .badge-default{ background-color: #fff; color: blue;}
    .pulse-green {animation: pulsered 2s infinite;background: #90ee90;box-shadow: 0 0 0 #e00e3f; }
</style>
@endpush



@php
    $roles = userRolePermissionArray();
@endphp


@section('content')
<div class="card card-success">
    <div class="card-content collapse show">
        <div class="card-body" style="padding: 15px 5px;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm" id="process_data_table">
                    <thead>
                    <tr>
                        <th>SL.</th>
                        <th style="width:100px;">Created</th>
                        <th>Agent</th>
                        <th>Date</th>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th>Reseller</th>
                        <th style="width:50px;">Variations</th>
                        <th style="width:50px;" class=" text-right">Order value</th>
                        <th style="width:50px;">Payment</th>
                        <th style="width:50px;">Location</th>
                        <th class=" text-center">Status</th>
                        <th class=" text-center" title="IS HOLD BY ADMIN">Hold</th>
                        <th class=" text-center" title="SELF PICKUP/ COD or RTC">SP</th>
                        <th class=" text-center" style="width:100px;">Action</th>
                        <th class=" text-center" style="width:100px;">Altered</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
        </div>

    </div>



</div>
<!--Edit Product Subcategory  html-->
<div class="modal fade text-left" id="self_pick_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">COD/RTC Transfer</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                {!! Form::open(['route' => 'admin.order.rtc_transfer', 'method' => 'post', 'class' => 'form-horizontal', 'files' => false , 'novalidate' ]) !!}
                    @csrf

                {!! Form::hidden('booking_id', null, [ 'class' => 'form-control mb-1', 'id' => 'f_booking_no' , 'data-validation-required-message' => 'This field is required' ]) !!}

                <div class="modal-body">
                    @if(Auth::user()->F_AGENT_NO > 0 )
                    <div class="form-group {!! $errors->has('payment_acc_no') ? 'error' : '' !!}">
                        <label>COD/RTC User<span class="text-danger">*</span></label>
                        <div class="controls">
                            <select class="form-control" name="payment_acc_no" id="payment_acc_no" data-validation-required-message="This field is required" tabindex="1" >
                                <option value="">--select bank--</option>
                                    @if(isset($data['payment_acc_no']) && count($data['payment_acc_no']) > 0 )
                                        @foreach($data['payment_acc_no'] as $k => $bank)
                                            @if( $bank->IS_COD == 1)
                                                @if( Auth::user()->F_AGENT_NO > 0)
                                                    <option value="{{ $bank->PK_NO }}" selected="selected">Request COD-RTC</option>

                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                <option value="0">Unassign COD-RTC </option>
                            </select>
                            {!! $errors->first('payment_acc_no', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>
                    @else
                    {!! Form::hidden('approval', 1, [ 'class' => 'form-control mb-1' , 'data-validation-required-message' => 'This field is required' ]) !!}

                    <div class="form-group {!! $errors->has('payment_acc_no') ? 'error' : '' !!}">
                        <label>COD/RTC User<span class="text-danger">*</span></label>
                        <div class="controls">
                            <select class="form-control" name="payment_acc_no" id="payment_acc_no" data-validation-required-message="This field is required" tabindex="4">
                                <option value="">--select bank--</option>
                                @if(isset($data['payment_acc_no']) && count($data['payment_acc_no']) > 0 )
                                    @foreach($data['payment_acc_no'] as $k => $bank)
                                        @if( $bank->IS_COD == 1)
                                            <option value="{{ $bank->PK_NO }}" >{{ $bank->BANK_NAME .' ('.$bank->BANK_ACC_NAME.') ('.$bank->BANK_ACC_NO.')' }}</option>
                                        @endif
                                    @endforeach
                                @endif
                                <option value="0">Unassigned RTC </option>

                            </select>

                            {!! $errors->first('payment_acc_no', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>

                    @endif

                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-secondary btn-sm" data-dismiss="modal" value="Close">
                    <input type="submit" class="btn btn-primary btn-sm submit-btn" value="Send">
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection




@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/js/scripts/forms/checkbox-radio.min.js"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var get_url = $('#base_url').val();

$(document).on('click','.self_pick', function(){
    var booking_id = $(this).data('booking_id');
    var rtc_no = $(this).data('rtc_no');
    if(rtc_no){
        $('#payment_acc_no').val(rtc_no);
    }

    $("#f_booking_no").val(booking_id);
})

    $("#process_data_table").on("change", ".is_admin_hold", function () {
        var id = $(this).data("booking_id");
        var type = null;

        if ($(this).is(':checked')) {
            var type = 'checked';
        }else{
            var type = 'unchecked';
        }
        var is_admin_hold = get_url + '/order_admin_hold';

        if(confirm('Are you sure you want to HOLD the order?')) {
        $.ajax({
            type: "post",
            data:{ type:type, id:id},
            url: is_admin_hold,
            beforeSend:function(){},
            success: function (data) {

                if (data == 'true') {
                    if( type == 'unchecked'){
                        toastr.success('Unhold the order successfully','Success');
                    }else{
                        toastr.success('Successfully hold the order','Success');
                        }
                }else{
                    toastr.info('Order status not change successfully', 'Error');
                }
            },
            complete: function (data){}
        });
        }else{
            if( type == 'unchecked'){
                $(this).prop('checked', true);

            }else{
                $(this).prop('checked', false);
            }


        }

    });

    $(document).ready(function() {
        var id      =  `{{ request()->get('id') }}`;
        var type    =  `{{ request()->get('type') }}`;
        var dispatch    =  `{{ request()->get('dispatch') }}`;
        var table   =
            $('#process_data_table').DataTable({
                processing: false,
                serverSide: true,
                paging: true,
                pageLength: 25,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                ajax: {
                    url: 'order/altered_order',
                    type: 'POST',
                    data: function(d) {
                        d._token        = "{{ csrf_token() }}";
                        d.id            = id;
                        d.type          = type;
                        d.dispatch      = dispatch;
                    }
                },
                columnDefs: [
                    { visible: false, targets: 6 },
                    { visible: false, targets: 15 }
                  ],
                columns: [
                    {
                        data: 'PK_NO',
                        name: 'PK_NO',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false,
                        className:'w100'
                    },
                    {
                        data: 'BOOKING_SALES_AGENT_NAME',
                        name: 'SLS_BOOKING.BOOKING_SALES_AGENT_NAME',
                        searchable: true,
                    },
                    {
                        data: 'order_date',
                        name: 'order_date',
                        searchable: true
                    },
                    {
                        data: 'order_id',
                        name: 'SLS_BOOKING.BOOKING_NO',
                        searchable: true,

                    },
                    {
                        data: 'customer_name',
                        name: 'SLS_BOOKING.CUSTOMER_NAME',
                        searchable: true,
                        className:'text-uppercase'
                    },
                    {
                        data: 'RESELLER_NAME',
                        name: 'SLS_ORDER.RESELLER_NAME',
                        searchable: true,
                    },
                    {
                        data: 'item_type',
                        name: 'item_type',
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'price_after_dis',
                        name: 'price_after_dis',
                        searchable: false,
                        className: 'text-right'

                    },
                    {
                        data: 'payment',
                        name: 'payment',
                        searchable: false
                    },
                    {
                        data: 'avaiable',
                        name: 'avaiable',
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        searchable: true
                    },
                    {
                        data: 'admin_hold',
                        name: 'admin_hold',
                        className: 'text-center',
                        searchable: false
                    },
                    {
                        data: 'self_pickup',
                        name: 'self_pickup',
                        className: 'text-center',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'altered',
                        name: 'altered',
                        searchable: true,
                    },
                ]
            });
        });
    </script>

@endpush

