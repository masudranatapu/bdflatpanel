@extends('admin.layout.master')

@section('Dispatch Management','open')
@section('list_dispatch','active')

@section('title') Order for Dispatch @endsection
@section('page-name') Order for Dispatch @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('order.breadcrumb_sub_title')
    </li>
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
    a:not([href]):not([tabindex]) {color: #fff;}
    .c-btn.active{color: #fff !important;}
</style>
@endpush



@php
    $roles = userRolePermissionArray();
    $order_type = 'all';
@endphp


@section('content')

<div class="card card-success min-height">
    <div class="card-content collapse show">
        <div class="card-body" style="padding: 15px 5px;">
            <div class="row">

                <div class="col-md-6 col-sm-6">
                    <a href="{{ route('admin.dispatch.list',[ 'dispatch' => 'rts' ]) }}" class="btn btn-xs btn-success  c-btn {{ request()->get('dispatch') == 'rts' ? 'active' : ''}} " style="min-width:90px;" title="Ready to Shipped">RTS</a>
                    <a href="{{ route('admin.dispatch.list',[ 'dispatch' => 'cod_rtc' ]) }}" class="btn btn-xs btn-success c-btn {{ request()->get('dispatch') == 'cod_rtc' ? 'active' : ''}} " style="min-width:90px;" title="Cash on delivery & Ready to collect">COD & RTC</a>
                    @if (request()->get('dispatch') == 'rts' && hasAccessAbility('view_rts_collect_btn', $roles))
                    <a href="javascript:void(0)" class="btn btn-xs btn-info c-btn" id="mark_pickup" style="min-width:90px;" title="Collect">Collect</a>
                    @endif
                  </div>
            </div>
            <hr>
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
                        <th style="width:50px;">Variations</th>
                        <th style="width:50px;" class=" text-right">Order value</th>
                        <th style="width:50px;">Payment</th>
                        <th style="width:50px;">Ready?</th>
                        <th class=" text-center">Status</th>
                        {{-- <th class=" text-center" title="IS HOLD BY ADMIN">Hold</th> --}}
                        <th class=" text-center" title="SELF PICKUP/ COD or RTC">SP</th>
                        <th class=" text-center" style="width:100px;">Action
                            @if (request()->get('dispatch') == 'rts')
                            <label class="c-p">
                                <input type="checkbox" id="bulk_check" class="c-p ml-1">
                            </label>
                            @endif
                        </th>
                        <th class=" text-center">DEFAULT</th>
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
<!--ASSIGN COD RTC DISPATCH USER MODAL-->
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
                    <input type="reset" class="btn btn-outline-secondary btn-sm" data-dismiss="modal" value="Close">
                    <input type="submit" class="btn btn-outline-primary btn-sm submit-btn" value="Send">
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection




@push('custom_js')
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).on('click','.self_pick', function(){
    var booking_id = $(this).data('booking_id');
    var rtc_no = $(this).data('rtc_no');
    if(rtc_no){
        $('#payment_acc_no').val(rtc_no);
    }

    $("#f_booking_no").val(booking_id);
})
$(document).on("click", "#bulk_check", function(e){
    $('#process_data_table tbody :checkbox').prop('checked', $(this).is(':checked'));
});
$(document).on("click", "#mark_pickup", function(event){
    var pickup_array = [];
    $("input:checkbox[name=record_check]:checked").each(function(){
        pickup_array.push($(this).val());
    });
    var url = get_url + '/mark-pickup-list';
    if (pickup_array != '') {
        if(confirm('Are you sure?')) {
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: {'pickup_array' : pickup_array},
            success: function(data) {
                if(data == 1){
                    location.reload();
                }else{
                    toastr.info('Please try again','Info');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {}
        });
        }else{
            $("input:checkbox[name=record_check]:checked").prop('checked', false);
        }
    }else{
        toastr.info('Please check at least one record','Info');
    }
});
var get_url = $('#base_url').val();

    $("#process_data_table").on("change", ".is_admin_hold", function () {
        var id = $(this).data("booking_id");
        var type = null;

        if ($(this).is(':checked')) {
            var type = 'checked';
        }else{
            var type = 'unchecked';
        }
        var is_admin_hold = get_url + '/order_admin_hold';

        if(confirm('Are you sure?')) {
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
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ajax: {
                    url: 'order/all_order',
                    type: 'POST',
                    data: function(d) {
                        d._token    = "{{ csrf_token() }}";
                        d.id        = id;
                        d.type      = type;
                        d.dispatch  = dispatch;
                    }
                },
                "columnDefs": [
                { "orderable": false, "targets": 12 },
                { visible: false, targets: 13 },
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
                        searchable: false
                    },
                    {
                        data: 'order_id',
                        name: 'SLS_ORDER.F_BOOKING_NO',
                        searchable: true,

                    },
                    {
                        data: 'customer_name',
                        name: 'SLS_BOOKING.CUSTOMER_NAME',
                        searchable: true,
                        className:'text-uppercase'
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
                        searchable: false
                    },
                    /* {
                        data: 'admin_hold',
                        name: 'admin_hold',
                        className: 'text-center',
                        searchable: false
                    }, */
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
                        data: 'IS_DEFAULT',
                        name: 'IS_DEFAULT',
                        searchable: true,
                    }
                ],
                rowCallback: function (row, data, index) {
                    if (data['IS_DEFAULT'] == 1) {
                        $(row).css('background-color','#ffd945')
                    }
                },
            });
        });
    </script>
@if (request()->get('dispatch') == 'cod_rtc')
{{-- <script type="text/javascript">
    $('[class*=self_pick]').attr('data-target', '');
    $(document).on('click','.self_pick', function(){
        $('#self_pick_modal').modal('hide');
    })
</script> --}}
@endif
@endpush

