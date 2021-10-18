@extends('admin.layout.master')

@section('invoice','active')
@section('Procurement','open')
@section('title')
    @lang('invoice_details.new_page_title')
@endsection
@section('page-name')
    @lang('invoice_details.list_page_sub_title')
@endsection

<?PHP
    $roles              = userRolePermissionArray();
    $invoice_info       = $data['invoice_info'] ?? null;
    $variant_info       = $data['variant_info'] ?? null;
    $old_variant_info   = $data['old_data'] ?? null;

    $gtotal_qty                     = 0;
    $gtotal_receipt                 = 0;
    $gtotal_flty                    = 0;
    $gtotal_sub_total_gbp_receipt   = 0;
    $gtotal_line_total              = 0;
    $gtotal_line_total_vat_gbp      = 0;
    $grec_total                     = 0;
    $grec_total_vat_gbp             = 0;
    $gbp_equivalent                 = 0;

?>

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('admin.invoice') }}"> Invoice </a>
    </li>
    <li class="breadcrumb-item active">Create invoice details
    </li>
@endsection

@push('custom_css')
 <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
 <link rel="stylesheet" href="{{ asset('app-assets/file_upload/image-uploader.min.css')}}">
 <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
 <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
 <link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
 <style type="text/css">
    .twitter-typeahead{display: block !important;}
    .list-group-item.tt-suggestion.tt-selectable{padding: .25rem 1.25rem;}
    #scrollable-dropdown-menu2 .tt-menu {max-height: 260px;overflow-y: auto;width: 100%;border: 1px solid #333;border-radius: 5px;}
 </style>
@endpush('custom_css')

@section('content')
    <div class="card card-success min-height">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> New
            Invoice Details</h4>
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
                        {!! Form::open([ 'route' => 'admin.invoice-details.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'id' => 'save-inv-details']) !!}

                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-4 pb-1">
                                        <h5><b>Invoice No:</b> {{ $invoice_info->INVOICE_NO }}</h5>
                                        <h5><b>Currency:</b> {{ $invoice_info->INVOICE_CURRENCY }}</h5>
                                        <h5><b>Vendor:</b> {{ $invoice_info->VENDOR_NAME }}</h5>
                                        <h5><b>Primary Discount:</b> {{ $invoice_info->DISCOUNT_PERCENTAGE ?? 0 }}%</h5>
                                        <h5><b>Secondary Discount:</b> {{ $invoice_info->DISCOUNT2_PERCENTAGE ?? 0 }}%</h5>
                                    </div>
                                    <div class="col-md-4 pb-1">
                                        <h5><b>Invoice Exact Value:</b> {{ $invoice_info->INVOICE_EXACT_VALUE }}</h5>
                                        <h5><b>Invoice Exact Vat:</b> {{ $invoice_info->INVOICE_EXACT_VAT }}</h5>
                                        <h5><b>Invoice Exact Postage:</b> {{ $invoice_info->INVOICE_EXACT_POSTAGE }}</h5>
                                        <h5><b>GBP to MR:</b> {{ $invoice_info->GBP_TO_MR_RATE }}%</h5>

                                    </div>

                                    <div class="col-md-4">
                                        <div class="row">
                                            <input type="hidden" name="invoice_id" value="{{ $invoice_info->PK_NO }}">
                                            <span class="hidden submit_check" value="0"></span>
                                            <div class="col-md-12">
                                                <div class="form-group {!! $errors->has('bar_code') ? 'error' : '' !!}">
                                                    <div class="controls">
                                                        {!! Form::text('bar_code', null,[ 'class' => 'form-control mb-1', 'placeholder' => 'Enter barcode for search product', 'id'=>'bar-code', 'tabindex' => 1 ]) !!}
                                                        {!! $errors->first('bar_code', '<label class="help-block text-danger">:message</label>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group with-border">
                                                    <div class="controls" id="scrollable-dropdown-menu2">
                                                        <input type="search" name="q" id="key_search" class="form-control search-input2" placeholder="Search by Keywords" autocomplete="off" >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="pull-right">
                                                <button type="button" class="btn btn-primary  btn-sm search_mother_btn" data-toggle="modal" data-backdrop="true" data-target="#variant-modal" data-url="{{ route('admin.invoice-details.new', [$invoice_info->PK_NO]) }}" >
                                                   Barcode Finder
                                                </button>
                                                <a href="{{ route('admin.invoice-details.new', [$invoice_info->PK_NO]) }}" class="btn btn-primary  btn-sm ">Reset</a>
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
                                                        <th style="width: 30px;">SL</th>
                                                        <th style="width: 250px;">Name</th>
                                                        <th style="width: 250px;">Invoice Name</th>
                                                        <th class="" style="width: 70px;">Rec Qty</th>
                                                        <th class="" style="width: 70px;">Faulty Qty</th>
                                                        <th style="width: 110px;">Vat Class</th>
                                                        <th class="" style="width: 70px;">Line Qty</th>
                                                        <th style="width: 100px;">Unit Price</th>
                                                        <th style="width: 100px;">Line Total</th>
                                                        <th style="width: 100px;">Unit Price<br>(W/VAT)</th>
                                                        <th style="width: 100px;">Line Total <br>(W/VAT)</th>
                                                        <th style="width: 100px;">Line VAT</th>
                                                        <th style="width: 30px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(!(empty($variant_info)) && count($variant_info) > 0)
                                                    @foreach($variant_info as $key => $item)
                                                    @include('admin.procurement.invoice-details._variant_tr',$item)
                                                    @endforeach
                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th colspan="2">Total</th>
                                                        <th class="text-center">
                                                            <span id="total_row_rec_qty">0</span>
                                                        </th>
                                                        <th class="text-center">
                                                            <span id="total_row_flt_qty">0</span>
                                                        </th>
                                                        <th></th>
                                                        <th class="text-center">
                                                            <span id="total_row_line_qty">0</span>
                                                        </th>
                                                        <th class="text-center">
                                                            <span id="total_row_line_unit">0</span>
                                                        </th>
                                                        <th class="text-right">
                                                            <span id="total_row_line_total" class="text-right">0</span>
                                                        </th>
                                                        <th></th>
                                                        <th class="text-right">
                                                            <span id="total_row_line_total_exvat_actual" class="text-right">0.00</span>
                                                        </th>
                                                        <th class="text-right">
                                                            <span id="total_row_line_vat_actual" class="text-right">0.00</span>
                                                        </th>
                                                        <th class="text-center"></th>

                                                    </tr>
                                                    <tr>
                                                        <th colspan="9">Grand Total</th>
                                                        <th colspan="2" class="text-right"><span id="total_row_line_total_actual" class="text-right">0.00</span></th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions mt-10 text-center">
                                <a href="{{ route('admin.invoice')}}">
                                    <button type="button" class="btn btn-warning mr-1">
                                        <i class="ft-x"></i> Cancel
                                    </button>
                                </a>
                                <button type="submit" class="btn btn-primary save-inv-details">
                                    <i class="la la-check-square-o"></i> Save
                                </button>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-eye text-primary"></i> All Items in Invoice</h4>
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

                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered alt-pagination table-sm" id="indextable" style="font-size: 13px;">
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
                                                        <th class="text-center" title="Line Total Actual GBP">Rec Total </th>
                                                        <th class="text-center" title="Line Total Actual Vat GBP">Rec Vat </th>
                                                        <th class="text-center" title="Line total actual vat in GBP">Vat</th>

                                                        <!-- <th style="width: 200px;">Line Total</th> -->

                                                        <th class="text-center">@lang('tablehead.tbl_head_action')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if($old_variant_info && count($old_variant_info) > 0 )
                                                    @foreach($old_variant_info as $row)

                                                    <?php

                                                    $gtotal_qty          += $row->QTY;
                                                    $gtotal_receipt      += $row->RECIEVED_QTY;
                                                    $gtotal_flty         += $row->FAULTY_QTY;
                                                    $gtotal_sub_total_gbp_receipt += $row->SUB_TOTAL_GBP_RECEIPT;
                                                    $gtotal_line_total += ($row->SUB_TOTAL_GBP_EV + $row->LINE_TOTAL_VAT_GBP);
                                                    $gtotal_line_total_vat_gbp += $row->LINE_TOTAL_VAT_GBP;
                                                    $grec_total += $row->REC_TOTAL_GBP_WITH_VAT;
                                                    $grec_total_vat_gbp += $row->REC_TOTAL_GBP_ONLY_VAT;


                                                    ?>
                                                    <tr>
                                                        <td>{{$loop->index + 1}}</td>
                                                        <td>{{ $row->PRD_VARIANT_NAME }}</td>
                                                        <td>{{ $row->INVOICE_NAME }}</td>
                                                        <td>{{ $row->BAR_CODE }}</td>


                                                        <td class="text-center">{{ $row->RECIEVED_QTY }}</td>
                                                        <td class="text-center">{{ $row->FAULTY_QTY }}</td>
                                                        <td class="text-right">{{ number_format($row->SUB_TOTAL_GBP_RECEIPT,2) }}</td>
                                                        <td>{{ $invoice_info->DISCOUNT_PERCENTAGE }} %</td>
                                                        <td>{{ $invoice_info->DISCOUNT2_PERCENTAGE }} %</td>

                                                        <td class="text-right">{{ number_format($row->UNIT_PRICE_GBP_EV,2) }}</td>
                                                        <td class="text-right">{{ number_format($row->UNIT_VAT_GBP,2) }}</td>
                                                        <td class="text-right">{{ number_format(($row->UNIT_PRICE_GBP_EV +$row->UNIT_VAT_GBP),2) }}</td>
                                                        <td class="text-center">{{ $row->QTY }}</td>
                                                        <td class="text-right">{{ number_format(($row->SUB_TOTAL_GBP_EV + $row->LINE_TOTAL_VAT_GBP),2) }}</td>
                                                        <td class="text-right">{{ number_format($row->LINE_TOTAL_VAT_GBP,2) }}</td>
                                                        <td class="text-right">{{ number_format($row->REC_TOTAL_GBP_WITH_VAT,2) }}</td>
                                                        <td class="text-right">{{ number_format($row->REC_TOTAL_GBP_ONLY_VAT,2) }}</td>
                                                        <td>{{ $row->VAT_RATE }}%</td>
                                                        <td></td>
                                                        </tr>
                                                        @endforeach()
                                                        @endif

                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="3" class="text-center">Total</td>
                                                            <td></td>
                                                            <td class="text-center">{{$gtotal_receipt}}</td>
                                                            <td class="text-center">{{$gtotal_flty}}</td>
                                                            <td class="text-right">{{number_format($gtotal_sub_total_gbp_receipt,2)}}</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="text-center">{{ $gtotal_qty ?? '' }}</td>
                                                            <td class="text-right">
                                                                <span class="text-danger">{{number_format($gtotal_line_total,2)}}</span>
                                                            </td>
                                                            <td class="text-right">
                                                                <span class="text-danger">
                                                                    {{number_format($gtotal_line_total_vat_gbp,2)}}
                                                                </span>
                                                            </td>
                                                            <td class="text-right">
                                                                <span class="text-success"> {{number_format($grec_total,2)}} </span>
                                                            </td>
                                                            <td class="text-right">
                                                            <span class="text-success">
                                                                {{number_format($grec_total_vat_gbp,2)}}
                                                            </span>
                                                            </td>
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
            </div>
        </div>
    </div>

    @include('admin.components._product_search_modal')
@endsection


@push('custom_js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
 <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
 <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
 <script type="text/javascript" src="{{ asset('app-assets/file_upload/image-uploader.min.js')}}"></script>
 <script type="text/javascript" src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
 <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
 <script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery(document).ready(function($) {
        typeahead();
    });

    $('#save-inv-details').on('submit', function(event){
        $("body").css("cursor", "progress");
        toastr.info('Please Wait, do not press the Save button agin','Info');

    });


    function typeahead(type) {
        var get_url = $('#base_url').val();
        var type = 'barcode';
        var engine = new Bloodhound({
            remote: {
                url: get_url+'/product/get-variant-info-like?q=%QUERY%&type='+type,
                wildcard: '%QUERY%',
                cache: false,
            },
            datumTokenizer: Bloodhound.tokenizers.whitespace('q'),
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });
        $('.search-input2').on('typeahead:selected', function (e, datum) {
            if (datum != '') {
                var bar_code = datum.BAR_CODE;
                getItemList(bar_code);
                $('.search-input2').typeahead('val','');

            }
        });

        $(".search-input2").typeahead({
            hint: true,
            highlight: true,
            minLength: 1,
        }, {
            source: engine.ttAdapter(),
            name: 'NAME',
            displayKey: 'BAR_CODE',
            limit: 100,

            // the key from the array we want to display (name,id,email,etc...)
            templates: {
                empty: [
                '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                ],
                header: [
                '<div class="list-group search-results-dropdown">'
                ],
                suggestion: function (data) {
                    return '<span class="list-group-item" style="cursor: pointer;"><img style="width:40px;" class="mr-1" src="'+get_url+data.PRIMARY_IMG_RELATIVE_PATH+'" alt=" ">' + data.NAME + '</span>'
                }
            }
        });
    }

    $(document).on('click','.delete-row', function(e){

        if(confirm('Are you sure your want to delete?')){
            var pk = $(this).data('pk');
            var barcode = $(this).data('barcode');
            $('.row_'+pk).remove();
            removeItem(barcode);
        }
    })

//////////////////////////////
var enterPressed = false;
//////////////////////////////

    // Get variant by barcode
    $(document).on('keypress','#bar-code', function(e) {
        if(e.which == 13) {
            e.preventDefault();
            if( ! enterPressed){
                var bar_code = $(this).val();
                getItemList(bar_code);
              enterPressed = true;
              setTimeout(function(){
                enterPressed = false;
              }, 1000);
            }
        }
    });

var addSerialNumber = function () {
    var i = 1
    $('table#invoicetable tbody tr').each(function(index) {
        $(this).find('td:nth-child(2)').html(index+1);
    });

    };



    function getItemList(bar_code)
    {
            if (bar_code != '') {

                var check_dupli = check_if_product_exists(bar_code);
                if(check_dupli == 1){
                    alert('Duplicate entry not allow');
                    $("#bar-code").val('');
                    $("#key_search").val('');
                    return false ;
                }else{

                var pageurl = `{{ URL::to('invoice-details/variant/`+bar_code+`/list')}}`;
                $.ajax({
                    type:'get',
                    url:pageurl,
                    async :true,
                    beforeSend: function () {
                        $("body").css("cursor", "progress");
                    },
                    success: function (data) {

                        if(data != ''){
                            $('#invoicetable tbody').append(data);
                            $('.submit_check').attr("value", 1);
                            $("#bar-code").val('');
                            $("#key_search").val('');
                            addSerialNumber();
                        }else{
                            $("#bar-code").val('');
                            $("#key_search").val('');
                            alert('Data not found');
                        }

                    },
                    complete: function (data) {
                        $("body").css("cursor", "default");
                    }
                });
            }

        }

    }


function check_if_product_exists(product) {
    var flag = 0;
    $('#invoicetable tbody tr').each(function (i, row) {
        var rows = $(row);
        var prd = rows.find('.barcode').val();
        if (prd == product) {
            flag = 1;
        }
    });
    return flag

}



    $(document).on('input','.qty_event', function(e) {
        var line_qty = $('.line_qty').val();
        var rec_qty = $('.rec_qty').val();
        var flt_qty = $('.flt_qty').val();

        line_qty = line_qty == '' ? 0 : line_qty;
        rec_qty = rec_qty == '' ? 0 : rec_qty;
        flt_qty = flt_qty == '' ? 0 : flt_qty;

        var total_qty = parseInt(rec_qty) + parseInt(flt_qty);

        if(total_qty > line_qty){
            alert('Total invoice quantity is '+line_qty+'. Please check quantity again.');
            $('.rec_qty').val(line_qty);
            $('.flt_qty').val(0);
        }

        getTotalCol('row_rec_qty');
        getTotalCol('row_flt_qty');
    });

    $(document).on('input','.row_line_qty',function(e){
        getTotalCol('row_line_qty');
    })

    $(document).on('input','.row_line_total',function(e){
        getTotalCol('row_line_total');
    })

    // $('.save-inv-details').on('click', function (e){
    //     console.log('clicked');
    //     var submit_check = $('.submit_check').attr("value");
    //     if(submit_check == 0){
    //         // Swal.fire({
    //         //     position: 'top-end',
    //         //     title: 'Please select mimimum one variant.',
    //         //     confirmButtonClass: 'btn btn-danger',
    //         //     buttonsStyling: false,
    //         // })
    //         return  false;
    //     } else if(submit_check == 1){
    //         return  true;
    //     }
    //     e.preventDefault();
    // });

 $(document).on('input','.row_line_qty',function(e){
    var row_id = $(this).data('row_id');
    var row_line_qty = $(this).val();
    var row_line_unit   = $('.line_unit'+row_id).val();

    row_line_qty        = row_line_qty == '' ? 0 : row_line_qty;
    row_line_unit       = row_line_unit == '' ? 0 : row_line_unit;

    $('.line_total'+row_id).val(Number(row_line_unit*row_line_qty).toFixed(2));
    getTotalCol('row_line_qty');
    getTotalCol('row_line_total');

 })


 $(document).on('input','.row_line_unit',function(e){
    var row_id = $(this).data('row_id');
    var row_line_unit = $(this).val();
    var row_line_qty   = $('.line_qty'+row_id).val();

    row_line_qty        = row_line_qty == '' ? 0 : row_line_qty;
    row_line_unit       = row_line_unit == '' ? 0 : row_line_unit;

    $('.line_total'+row_id).val(Number(row_line_unit*row_line_qty).toFixed(2));
    getTotalCol('row_line_qty');
    getTotalCol('row_line_total');


 })

 $(document).on('input','.row_line_total',function(e){
    var row_id = $(this).data('row_id');
    var row_line_total = $(this).val();
    var row_line_qty   = $('.line_qty'+row_id).val();

    row_line_qty        = row_line_qty == '' ? 0 : row_line_qty;
    row_line_total       = row_line_total == '' ? 0 : row_line_total;

    $('.line_unit'+row_id).val(Number(row_line_total/row_line_qty).toFixed(2));
    getTotalCol('row_line_qty');
    getTotalCol('row_line_total');

 })

//check faulty qty is not bigger then receive qty
 $(document).on('input','.row_flt_qty',function(e){
    var row_id = $(this).data('row_id');
    var row_flt_qty = $(this).val();
    var row_rec_qty   = $('.rec_qty'+row_id).val();

    row_flt_qty       = row_flt_qty == '' ? 0 : row_flt_qty;
    row_rec_qty       = row_rec_qty == '' ? 0 : row_rec_qty;

    if(Number(row_flt_qty) > Number(row_rec_qty)){
        $(this).val(row_rec_qty);
    }
    getTotalCol('row_flt_qty');
})

$(document).on('input','.row_rec_qty',function(e){
     var row_id = $(this).data('row_id');
     $('.flt_qty'+row_id).val(0);
})


function getTotalCol(class_name){
    var total = 0;
    $('#save-inv-details .'+class_name).each(function()
    {   if($(this).val() !="" ){
            total += parseFloat($(this).val());
        }
    });
    if(Number.isInteger(total)){
        $('#total_'+class_name).text(total);
    }else{
        $('#total_'+class_name).text(total.toFixed(2));
    }

}

function getGtotal(gtotal_class)
{
    var total_row_line_total_exvat_actual   = Number($("#total_row_line_total_exvat_actual").text());
    var total_row_line_vat_actual           = Number($("#total_row_line_vat_actual").text());
    $('#'+gtotal_class).text(Number(total_row_line_total_exvat_actual+total_row_line_vat_actual).toFixed(2));
}


    function unitPriceEvent(pk_no, type = 'line'){
        var line_total = $('.line_total'+pk_no).val();
        line_total = line_total == '' ? 0 : parseFloat(line_total);
        var discount_percentage = parseInt(`{{ $invoice_info->DISCOUNT_PERCENTAGE }}`);
        var discount2_percentage = parseInt(`{{ $invoice_info->DISCOUNT2_PERCENTAGE }}`);
        var line_qty = $('.line_qty'+pk_no).val();
        line_qty = line_qty == '' ? 0 : parseInt(line_qty);

        var vat_class_rate = $('.vat_class_rate'+pk_no).val();
        vat_class_rate = vat_class_rate == '' ? 0 : parseInt(vat_class_rate);


        var line_total_exvat_actual = ((line_total*100)*(100-discount_percentage)*(100-discount2_percentage))/(100+vat_class_rate)/100/100;
        var line_total_exvat_actual2 = Number((line_total_exvat_actual*100)/100).toFixed(12);
        line_total_exvat_actual = Number(Math.round(line_total_exvat_actual*100)/100).toFixed(2);

        var unit_price_ev = ((line_total*100)*(100-discount_percentage)*(100-discount2_percentage)/line_qty)/(100+vat_class_rate)/100/100;
        var unit_price_ev2 = Number((unit_price_ev*100)/100).toFixed(12);
        unit_price_ev = Number(Math.round(unit_price_ev*100)/100).toFixed(2);

        var line_total_without_discount = line_total*(100-discount_percentage)*(100-discount2_percentage)/100/100;
        var line_vat_actual2 = Number(((line_total_without_discount-line_total_exvat_actual2)*100)/100).toFixed(12);
        var line_vat_actual = Number(Math.round((line_total_without_discount-line_total_exvat_actual)*100)/100).toFixed(2);
        var unit_vat2 = Number((unit_price_ev2*vat_class_rate)/100).toFixed(12);
        var unit_vat = Number(Math.round(unit_price_ev*vat_class_rate)/100).toFixed(2);



        $('.unit_price_ev'+pk_no).val(unit_price_ev);
        $('.line_total_exvat_actual'+pk_no).val(line_total_exvat_actual);
        $('.line_vat_actual'+pk_no).val(line_vat_actual);
        $('.unit_vat'+pk_no).val(unit_vat);

        $('.unit_price_ev2'+pk_no).val(unit_price_ev2);
        $('.line_total_exvat_actual2'+pk_no).val(line_total_exvat_actual2);
        $('.line_vat_actual2'+pk_no).val(line_vat_actual2);
        $('.unit_vat2'+pk_no).val(unit_vat2);

        getTotalCol('row_line_total_exvat_actual');
        getTotalCol('row_line_vat_actual');
        getTotalCol('row_flt_qty');



        var rec_qty = $('.rec_qty'+pk_no).val();
        var flt_qty = $('.flt_qty'+pk_no).val();


        if (type == 'rec_qty') {
           $('.rec_qty'+pk_no).val(line_qty);
           $('.flt_qty'+pk_no).val(0);
        }


        getTotalCol('row_rec_qty');
        getGtotal('total_row_line_total_actual');
    }



    function removeItem(item) {

        getTotalCol('row_line_total_exvat_actual');
        getTotalCol('row_line_vat_actual');
        getTotalCol('row_flt_qty');
        getTotalCol('row_rec_qty');
        getGtotal('total_row_line_total_actual');
        getTotalCol('row_line_total');
        getTotalCol('row_line_qty');
        addSerialNumber();

    }

 </script>
@endpush('custom_js')
