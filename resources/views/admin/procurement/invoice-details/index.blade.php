@extends('admin.layout.master')

@section('invoice','active')
@section('Procurement','open')
@section('title')
@lang('invoice_details.list_page_title')
@endsection
@section('page-name')
@lang('invoice_details.list_page_sub_title')
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('invoice_details.breadcrumb_title')    </a>
</li>
<li class="breadcrumb-item active">@lang('invoice_details.breadcrumb_sub_title')
</li>
@endsection
<?php

    $roles                          = userRolePermissionArray();
    $rows                           = $data['rows'] ?? null;
    $invoice                        = $data['invoice'] ?? null;
    $gtotal_qty                     = 0;
    $gtotal_receipt                 = 0;
    $gtotal_flty                    = 0;
    $gtotal_sub_total_gbp_receipt   = 0;
    $gtotal_line_total              = 0;
    $gtotal_line_total_vat_gbp      = 0;
    $gbp_equivalent                 = 0;
    $grec_total                     = 0;
    $grec_total_vat_gbp             = 0;
    // dd($data);

    if($invoice->INVOICE_CURRENCY == 'GBP' ){
    $gbp_equivalent = $invoice->INVOICE_EXACT_VALUE;
    }elseif($invoice->INVOICE_CURRENCY == 'RM' ){
    $gbp_equivalent = $invoice->INVOICE_EXACT_VALUE/$invoice->GBP_TO_MR_RATE;
    }else{
        $gbp_equivalent = $invoice->INVOICE_EXACT_VALUE/$invoice->GBP_TO_AC_RATE;
    }


    $invoice_img =  $invoice->allPhotos ?? array();
    // echo "<pre>";
    // print_r($invoice->parentInvoice);
    // die();

?>


@push('custom_css')
<!--for image gallery-->
<link rel="stylesheet" href="{{ asset('app-assets/lightgallery/dist/css/lightgallery.min.css') }}">

@endpush('custom_css')


@section('content')
<div class="content-body min-height">
    <section id="pagination">
        <div class="row">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-eye  text-primary"> </i> Invoice Details</h4>
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
                        <div class="card-body card-dashboard">
                            <div class="row">
                                 <div class="col-md-3 pb-1">
                                    <h5><b>Invoice No :</b> {{ $invoice->INVOICE_NO }}</h5>
                                    <h5><b>Invoice Date :</b> {{ date('d-m-Y',strtotime($invoice->INVOICE_DATE)) }}</h5>
                                    <h5><b>Entry Date :</b> {{ date('d-m-Y',strtotime($invoice->SS_CREATED_ON)) }}</h5>
                                    <h5><b>Entry By :</b> {{ $invoice->user->email ?? '' }}</h5>
                                </div>
                                <div class="col-md-3 pb-1">

                                    <h5><b>Vendor :</b> {{ $invoice->VENDOR_NAME }}</h5>
                                    <h5><b>Primary Discount :</b> {{ $invoice->DISCOUNT_PERCENTAGE }}%</h5>
                                    <h5><b>Secondary Discount :</b> {{ $invoice->DISCOUNT2_PERCENTAGE }}%</h5>
                                    <h5><b>Payment Source :</b> {{ $invoice->PAYMENT_SOURCE_NAME }}</h5>

                                </div>
                                <div class="col-md-3 pb-1">

                                    <h5><b>Purchase Currency :</b> {{ $invoice->INVOICE_CURRENCY }}</h5>
                                    <h5><b>GBP To RM Rate:</b> {{ $invoice->GBP_TO_MR_RATE }}</h5>

                                    @if($invoice->INVOICE_CURRENCY == 'GBP' )

                                    @elseif($invoice->INVOICE_CURRENCY == 'RM')

                                    @else
                                     <h5><b>GBP To <span>{{ $invoice->INVOICE_CURRENCY }}</span> Rate:</b> {{ $invoice->GBP_TO_AC_RATE }}</h5>

                                    @endif
                                    @if($invoice->F_PARENT_PRC_STOCK_IN > 0)
                                    <h5>
                                        <b>Parent Invoice : {{ $invoice->parentInvoice->INVOICE_NO ?? '' }}</b>
                                    </h5>
                                    @endif
                                    <h5><b>Payment Account :</b> {{ $invoice->PAYMENT_ACC_NAME }}</h5>
                                    <h5><b>Payment Method :</b> {{ $invoice->PAYMENT_METHOD_NAME }}</h5>
                                </div>
                                <div class="col-md-3 pb-1 text-right">

                                    <h5><b>Invoice Amount </b> ({{$invoice->INVOICE_CURRENCY}}) : {{ number_format($invoice->INVOICE_EXACT_VALUE,2) }}</h5>

                                    <h5><b>GBP Equivalent (GBP) :</b> {{ number_format($gbp_equivalent,2) }}</h5>
                                    {{-- <h5 class="{{number_format($gbp_equivalent,2) != number_format($invoice->INVOICE_TOTAL_ACTUAL_GBP,2) ? 'text-danger' : '' }}"><b>Invoice Amount GBP :</b>  {{ number_format($invoice->INVOICE_TOTAL_ACTUAL_GBP,2) }}</h5> --}}
                                    <h5 class=""><b>Local Postage GBP :</b>  {{ number_format($invoice->INVOICE_EXACT_POSTAGE,2) }}</h5>

                                </div>
                            </div>
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
                                            <th class="text-center" title="Line TotalActual GBP" >Line Total </th>
                                            <th class="text-center" title="Line Total Actual Vat GBP">Line Vat</th>
                                            <th class="text-center" title="Received TotalActual GBP">Rec Total</th>
                                            <th class="text-center" title="Received Total Actual Vat GBP">Rec Vat</th>
                                            <th class="text-center" title="Line total actual vat in GBP">Vat</th>
                                            <th class="text-center">@lang('tablehead.tbl_head_action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rows as $row)


                                        <?php

                                        $gtotal_qty                     += $row->QTY;
                                        $gtotal_receipt                 += $row->RECIEVED_QTY;
                                        $gtotal_flty                    += $row->FAULTY_QTY;

                                        if($invoice->INVOICE_CURRENCY == 'RM' ){
                                            $gtotal_sub_total_gbp_receipt   += $row->SUB_TOTAL_MR_RECEIPT;
                                            $gtotal_line_total              += ($row->SUB_TOTAL_MR_EV + $row->LINE_TOTAL_VAT_MR);
                                            $gtotal_line_total_vat_gbp      += $row->LINE_TOTAL_VAT_MR;
                                            $grec_total                     += $row->REC_TOTAL_MR_WITH_VAT;
                                            $grec_total_vat_gbp             += $row->REC_TOTAL_MR_ONLY_VAT;
                                            $tr_left = $row->SUB_TOTAL_MR_RECEIPT;
                                            $tr_right = $row->SUB_TOTAL_MR_EV + $row->LINE_TOTAL_VAT_MR;
                                        }else {
                                            $gtotal_sub_total_gbp_receipt   += $row->SUB_TOTAL_GBP_RECEIPT;
                                            $gtotal_line_total              += ($row->SUB_TOTAL_GBP_EV + $row->LINE_TOTAL_VAT_GBP);
                                            $gtotal_line_total_vat_gbp      += $row->LINE_TOTAL_VAT_GBP;
                                            $grec_total                     += $row->REC_TOTAL_GBP_WITH_VAT;
                                            $grec_total_vat_gbp             += $row->REC_TOTAL_GBP_ONLY_VAT;
                                            $tr_left = $row->SUB_TOTAL_GBP_RECEIPT;
                                            $tr_right = $row->SUB_TOTAL_GBP_EV + $row->LINE_TOTAL_VAT_GB;
                                        }





                                        ?>
                                        <tr class="{{ $tr_left != $tr_right ? 'text-warning' : '' }}">
                                            <td>{{$loop->index + 1}}</td>
                                            <td>{{ $row->PRD_VARIANT_NAME }}</td>
                                            <td>{{ $row->INVOICE_NAME }}</td>
                                            <td>{{ $row->BAR_CODE }}</td>


                                            <td class="text-center">{{ $row->RECIEVED_QTY }}</td>
                                            <td class="text-center">{{ $row->FAULTY_QTY }}</td>
                                            <td class="text-right">
                                                @if($invoice->INVOICE_CURRENCY == 'RM' )
                                                {{ number_format($row->SUB_TOTAL_MR_RECEIPT,2) }}
                                                @else
                                                {{ number_format($row->SUB_TOTAL_GBP_RECEIPT,2) }}
                                                @endif
                                            </td>
                                            <td>{{ $invoice->DISCOUNT_PERCENTAGE }} %</td>
                                            <td>{{ $invoice->DISCOUNT2_PERCENTAGE }} %</td>

                                            <td class="text-right">
                                                @if($invoice->INVOICE_CURRENCY == 'RM' )
                                                {{ number_format($row->UNIT_PRICE_MR_EV,2) }}
                                                @else
                                                {{ number_format($row->UNIT_PRICE_GBP_EV,2) }}
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                @if($invoice->INVOICE_CURRENCY == 'RM' )
                                                {{ number_format($row->UNIT_VAT_RM,2) }}
                                                @else
                                                {{ number_format($row->UNIT_VAT_GBP,2) }}
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                @if($invoice->INVOICE_CURRENCY == 'RM' )
                                                {{ number_format(($row->UNIT_PRICE_MR_EV +$row->UNIT_VAT_RM),2) }}
                                                @else
                                                {{ number_format(($row->UNIT_PRICE_GBP_EV +$row->UNIT_VAT_GBP),2) }}
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $row->QTY }}</td>
                                            <td class="text-right">
                                                @if($invoice->INVOICE_CURRENCY == 'RM' )
                                                {{ number_format(($row->SUB_TOTAL_MR_EV + $row->LINE_TOTAL_VAT_MR),2) }}
                                                @else
                                                {{ number_format(($row->SUB_TOTAL_GBP_EV + $row->LINE_TOTAL_VAT_GBP),2) }}
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                @if($invoice->INVOICE_CURRENCY == 'RM' )
                                                {{ number_format($row->LINE_TOTAL_VAT_MR,2) }}
                                                @else
                                                {{ number_format($row->LINE_TOTAL_VAT_GBP,2) }}
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                @if($invoice->INVOICE_CURRENCY == 'RM' )
                                                {{ number_format($row->REC_TOTAL_MR_WITH_VAT,2) }}
                                                @else
                                                {{ number_format($row->REC_TOTAL_GBP_WITH_VAT,2) }}
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                @if($invoice->INVOICE_CURRENCY == 'RM' )
                                                {{ number_format($row->REC_TOTAL_MR_ONLY_VAT,2) }}
                                                @else
                                                {{ number_format($row->REC_TOTAL_GBP_ONLY_VAT,2) }}
                                                @endif
                                            </td>
                                            <td>{{ $row->VAT_RATE }}%</td>
                                            <td></td>
                                            </tr>
                                            @endforeach()

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

                                                <td class="text-center">{{$gtotal_qty}}</td>

                                                <td class="text-right">
                                                    <span class="text-danger">{{number_format($gtotal_line_total,2)}}</span>
                                                </td>
                                                <td class="text-right"><span class="text-danger">{{number_format($gtotal_line_total_vat_gbp,2)}}</span>
                                                </td>
                                                <td>
                                                    <span class="text-success">{{number_format($grec_total,2)}}</span>
                                                </td>
                                                <td>
                                                     <span class="text-success">{{number_format($grec_total_vat_gbp,2)}}</span>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                @if($invoice->DESCRIPTION)
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3><u>Notes</u></h3>
                                        <div>{{$invoice->DESCRIPTION}}</div>
                                    </div>
                                </div>
                                @endif
                                @if(!empty($invoice_img))
                                <br>
                                <br>
                                <br>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <div>
                                            <h4 class="mb-2"><u>Invoice Photo</u></h4>
                                            <div id="aniimated-thumbnials">
                                                @foreach($invoice_img as $key => $inv_img)
                                                @if(strtolower($inv_img->FILE_EXT) != 'pdf')

                                                <a href="{{$inv_img->RELATIVE_PATH}}" >
                                                    <img src="{{asset($inv_img->RELATIVE_PATH)}}" class=" mr-1 mb-1" style="max-width: 200px;" />
                                                </a>
                                                @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <hr>
                                        <div>
                                            @php $i = 1 @endphp
                                            @foreach($invoice_img as $key => $inv_img)
                                            @if(strtolower($inv_img->FILE_EXT) == 'pdf')
                                            <a href="{{asset($inv_img->RELATIVE_PATH)}}" target="_blank">Show PDF ({{$i}})</a> ,&nbsp;

                                             @php $i++ @endphp
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @endsection


    <!--push from page-->
    @push('custom_js')
    <!--for image gallery-->
    <script src="{{ asset('app-assets/lightgallery/dist/js/lightgallery.min.js')}}"></script>

    <script type="text/javascript">

        //for image gallery
        $("#aniimated-thumbnials").lightGallery({
                thumbnail:true,




        });

        // $(this).lightGallery({
        //       thumbnail: false,
        //       zoom: false,
        //       fullScreen: false,
        //       download: false,
        //       hideBarsDelay: 1000,
        //       dynamic: true,
        //       dynamicEl: [{
        //           "iframe": "true",
        //           "src": "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf"
        //       }]
        //   });



    </script>





     @endpush('custom_js')

