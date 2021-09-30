@extends('admin.layout.master')

@section('Dispatch Management','open')
@section('item_collect','active')
@section('title')
   Order Item List
@endsection

@section('page-name')
Order Item List
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('order.breadcrumb_sub_title')
    </li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">

<style>
    .f12{font-size: 12px;}
    .w100{width: 100px;}
    #process_data_table td{vertical-align: middle;}
    .order-type{display: inline-block; margin-right: 10px;}
    .order-type label {cursor: pointer;}

    a:not([href]):not([tabindex]) {
        color: #fff;
    }
    .c-btn.active{color: #fff !important;}
</style>
@endpush
@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<!-- END: Data Table-->
@endpush

@php
    $roles = userRolePermissionArray();
@endphp


@section('content')

<div class="card card-success min-height">
    <div class="card-content collapse show">
        <div class="card-body" style="padding: 15px 5px;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm" id="process_data_table">
                    <thead>
                    <tr>
                        <th>SL.</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>
                            Assign

                        </th>
                        <th>
                            <label class="c-p mr-1" style="float:right">
                            <input type="checkbox" id="bulk_check" class="c-p ml-1">
                        </label>
                    </th>
                        <th style="width:50px;">Total Count</th>
                        {{-- <th class=" text-center" title="SELF PICKUP/ COD or RTC">SP</th> --}}
                        {{-- <th class=" text-center" style="width:100px;">Action --}}
                        {{-- </th> --}}
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="offset-md-7 col-md-3">
                    <label>Select Logistic Users<span class="text-danger">*</span></label>
                    <div class="controls">
                        {{-- {!! Form::select('logistic_user', (array)$user, null, ['class'=>'form-control mb-1 select2', 'id' => 'logistic_user']) !!} --}}
                        <select class="form-control select2" name="logistic_user" id="logistic_user_all" data-validation-required-message="This field is required" tabindex="1" >
                            <option value="">--select User--</option>
                            @foreach ($dropdown as $item)
                            <option value="{{ $item->PK_NO }}"> {{ $item->USERNAME }} </option>
                            @endforeach
                            <option value="0">Unassign User</option>
                        </select>
                        {!! $errors->first('logistic_user', '<label class="help-block text-danger">:message</label>') !!}
                    </div>
                </div>
                <div class="col-md-1">
                    <a href="javascript:void(0)" id="bulk_assign" class="btn btn-sm btn-info btn-min-width" style="margin-top: 20px">Assign</a>
                </div>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
        </div>
    </div>
</div>
@include('admin.dispatch.item_assign_modal')
@endsection
@push('custom_js')
{{-- <script src="https://pixinvent.com/modern-admin-clean-bootstrap-4-dashboard-html-template/app-assets/js/scripts/forms/checkbox-radio.min.js"></script> --}}
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).on("click", "#bulk_check", function(e){
    $('#process_data_table tbody :checkbox').prop('checked', $(this).is(':checked'));
});
$(document).on("click", "#bulk_assign", function(event){
    var bulk_product_array = [];
    $("input:checkbox[name=record_check]:checked").each(function(){
        bulk_product_array.push($(this).val());
    });
    var user_id = $( "#logistic_user_all option:selected").val();
    var batch_id = `{{ Request::segment(2) }}`;
    var url = get_url + '/bulk-assign-logistic-user';
    if (bulk_product_array != '') {
        if(confirm('Are you sure?')) {
            if (user_id != '') {
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        'bulk_product_array' : bulk_product_array,
                        'user_id' : user_id,
                        'batch_id' : batch_id,
                    },
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
                alert('Please select logistic user !');
            }
        }else{
            $("input:checkbox[name=record_check]:checked").prop('checked', false);
        }
    }else{
        toastr.info('Please check at list single record','Info');
    }
});
$(document).on('submit','#product-assign',function(e){
    e.preventDefault();
    $.ajax({
        type:'POST',
        url:get_url+'/assign-order-item',
        data : $('#product-assign').serialize(),
        beforeSend: function () {
            $("body").css("cursor", "progress");
        },
        success: function (data) {
            if (data['status'] == 1) {
                $('[data-sku_id='+data['skuid']+']').html(data['name']);
                $('[data-sku_id='+data['skuid']+']').data('user_id',data['id']);
                if (data['user_assigned'] == 1) {
                    $('[data-sku_id='+data['skuid']+']').removeClass('btn-warning');
                    $('[data-sku_id='+data['skuid']+']').addClass('btn-success');
                }else{
                    $('[data-sku_id='+data['skuid']+']').removeClass('btn-success');
                    $('[data-sku_id='+data['skuid']+']').addClass('btn-warning');
                }
                $('#_modal').modal('hide');
            }
            else{
                alert('Please try again !');
            }
        },
        complete: function (data) {
            $("body").css("cursor", "default");
        }
    });
});
$(document).on("click", "#assign_logistic", function(event){
    var batch_id  = $(this).data("batch_id");
    var sku_id    = $(this).data("sku_id");
    var user_id   = $(this).data('user_id');

    $('#batch_id').val(batch_id);
    $('#sku_id').val(sku_id);
    if (user_id > 0) {
        $("#logistic_user").val(user_id);
        $("#logistic_user").trigger('change');
    }else{
        $("#logistic_user").val("").change();
    }
});
var get_url = $('#base_url').val();
    $(document).ready(function() {
        var id      =  `{{ Request::segment(2) }}`;
        var table   =
            $('#process_data_table').DataTable({
                processing: false,
                serverSide: true,
                paging: false,
                pageLength: 25,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "columnDefs": [
                { "orderable": false, "targets": 5 }
                ],
                ajax: {
                    url: `{{ URL::to('collected-item-datatable') }}`,
                    type: 'POST',
                    data: function(d) {
                        d._token    = "{{ csrf_token() }}";
                        d.id        = id;
                        // d.type      = type;
                        // d.dispatch  = dispatch;
                    }
                },
                // "columnDefs": [
                // { "orderable": false, "targets": 12 }
                // ],
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
                        data: 'image',
                        name: 'image',
                        searchable: false,
                        className:'w100'
                    },
                    {
                        data: 'PRD_VARINAT_NAME',
                        name: 'INV_STOCK.PRD_VARINAT_NAME',
                        skuid: 'INV_STOCK.SKUID',
                        barcode: 'INV_STOCK.BARCODE',
                        searchable: true,
                        render: function(data, type, row) {
                            return '<p>NAME : '+row.PRD_VARINAT_NAME+'</p><p>SKUID :'+row.SKUID+'</p><p>BARCODE :'+row.BARCODE+'<p>';
                        }
                    },
                    {
                        data: 'position',
                        name: 'position',
                        searchable: false,
                    },
                    {
                        data: 'assign_user',
                        name: 'assign_user',
                        searchable: false,
                    },
                    {
                        data: 'bulk_assign',
                        name: 'bulk_assign',
                        searchable: false,
                    },
                    {
                        data: 'total_count',
                        name: 'total_count',
                        searchable: false,
                    },
                ]
            });
        });
    </script>

@endpush

