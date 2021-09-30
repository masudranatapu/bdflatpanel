@extends('admin.layout.master')
@section('Procurement','open')
@section('stock_processing','active')
@section('title') Stock Processing @endsection
@section('page-name') Stock Processing @endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('form.dashboard')</a></li>
<li class="breadcrumb-item active">@lang('form.breadcrumb_stock_processing')</li>
@endsection
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush
<?php
$roles              = userRolePermissionArray();
$rows               = $data['rows'] ?? null;
$warehouse_combo    = $data['warehouse_combo'] ?? array();

?>

@push('custom_css')
<style type="text/css">
    .acc{width: 55px; line-height: 18px; font-style: italic; display: inline-block;}
</style>
@endpush

@section('content')
<div class="content-body min-height">
    <section id="pagination">
        <div class="row">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">

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
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm" id="indextable">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 30px;">SL.</th>
                                            <th class="text-center" style="width: 120px;">Date</th>
                                            <th class="text-center">Vendor</th>
                                            <th class="text-center" style="width: 150px;">Account Info</th>
                                            <th class="text-center">Reciept No</th>
                                            <th class="text-center" title="Calculated/Reciept Value">Cal/Rec </th>
                                            <th class="text-center" title="Receive quantity (Faulty quantity) / Reciept quantity">Rec(Faulty)/Total</th>
                                            <th class="text-center" title="loyalty claimed">Loyalty Claimed</th>
                                            {{-- <th class="text-center" title="loyalty claimed">Loyalty Claimed</th>
                                            <th class="text-center" title="Quick Books entry">QB</th> --}}
                                            <th class="text-center" title="Vat refund claimed">Rec VAT Claimed</th>
                                            <th class="text-center" title="Exact VAT">Exact VAT</th>
                                            <th class="text-center" title="STOCK GENERATING">Stock Generated</th>
                                            <th class="text-center">@lang('tablehead.tbl_head_action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($rows))
                                        @foreach($rows as $row)
                                        <?php
                                        $vat_amt = 0;

                                        if($row->INVOICE_CURRENCY == 'RM'){
                                            $vat_amt = $row->INVOICE_REC_TOTAL_ACTUAL_MR_ONLY_VAT;
                                        }elseif($row->INVOICE_CURRENCY == 'GBP'){
                                            $vat_amt = $row->INVOICE_REC_TOTAL_ACTUAL_GBP_ONLY_VAT;
                                        }else{
                                            $vat_amt = $row->INVOICE_REC_TOTAL_ACTUAL_AC_ONLY_VAT;
                                        }

                                        $vat_amt = number_format($vat_amt,2);



                                        ?>
                                        <tr>
                                            <td class="text-center" style="width: 30px;">{{ $loop->index + 1 }}
                                            </td>
                                            <td class="text-center" style="width: 120px;">{{ date('d-m-Y',strtotime($row->INVOICE_DATE)) }}</td>

                                            <td>{{ $row->VENDOR_NAME }}</td>
                                            <td style="width: 150px;">
                                                <div style="font-size: 12px;">{{ $row->PAYMENT_SOURCE_NAME }} / {{ $row->PAYMENT_ACC_NAME }} /  {{ $row->PAYMENT_METHOD_NAME }}</div>

                                            </td>
                                            <td>{{ $row->INVOICE_NO }}</td>

                                            <td class="text-right" >
                                                <span style="font-size: 10px; color: #000">({{$row->INVOICE_CURRENCY}})</span>

                                                @if($row->INVOICE_CURRENCY == 'RM')
                                                    @php $cal_val = $row->INVOICE_REC_TOTAL_ACTUAL_MR_WITH_VAT + $row->INVOICE_POSTAGE_ACTUAL_MR @endphp

                                                @elseif($row->INVOICE_CURRENCY == 'GBP')
                                                    @php $cal_val = $row->INVOICE_REC_TOTAL_ACTUAL_GBP_WITH_VAT + $row->INVOICE_POSTAGE_ACTUAL_GBP @endphp

                                                @else
                                                    @php $cal_val = $row->INVOICE_REC_TOTAL_ACTUAL_AC_WITH_VAT + $row->INVOICE_POSTAGE_ACTUAL_AC @endphp

                                                @endif
                                                @if(number_format($cal_val,2) != number_format($row->INVOICE_EXACT_VALUE,2) )
                                                    <span title="CALCULATED VALUE & RECIEPT VALUE NOT SAME">{{ number_format($cal_val,2) }} / {{ number_format($row->INVOICE_EXACT_VALUE,2) }}</span>
                                                    @else
                                                    <span title="CALCULATED VALUE & RECIEPT VALUE SAME">{{ number_format($row->INVOICE_EXACT_VALUE,2) }}</span>
                                                @endif


                                            </td>
                                            <td class="text-center">

                                                {{ $row->RECIEVED_QTY ?? 0 }}
                                                @if($row->FAULTY_QTY > 0 )
                                                ({{ $row->FAULTY_QTY ?? 0 }})
                                                @endif
                                                / {{ $row->TOTAL_QTY ?? 0 }}
                                            </td>
                                            {{--
                                            <td class="text-center">

                                                @if($row->HAS_LOYALTY == 1)
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle {{ $row->LOYALTY_CLAIMED != 1 ? 'text-danger' : ''}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $row->LOYALTY_CLAIMED == 1 ? 'YES' : 'NO' }}</button>
                                                    @if($row->LOYALTY_CLAIMED != 1)
                                                    <div class="dropdown-menu dropdown-menu-sm" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 40px, 0px);">
                                                        <a class="dropdown-item" href="{{ route('admin.loyalty-claime', [$row->PK_NO]) }}"  onclick="return confirm('Are you sure?')">YES</a>
                                                    </div>
                                                    @endif
                                                </div>
                                                @else
                                                <span>N/A</span>
                                                @endif

                                            </td>

                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle {{ $row->IS_QUICK_BOOK_ENTERED != 1 ? 'text-danger' : ''}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $row->IS_QUICK_BOOK_ENTERED == 1 ? 'YES' : 'NO' }}</button>
                                                    @if($row->IS_QUICK_BOOK_ENTERED != 1)
                                                    <div class="dropdown-menu dropdown-menu-sm" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 40px, 0px);">
                                                        <a class="dropdown-item" href="{{ route('admin.invoice-qbentry', [$row->PK_NO]) }}"  onclick="return confirm('Are you sure?')">YES</a>
                                                    </div>
                                                    @endif
                                                </div>
                                            </td> --}}

                                            <td class="text-center">

                                                @if($row->HAS_LOYALTY == 1)
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle {{ $row->LOYALTY_CLAIMED != 1 ? 'text-white' : ''}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $row->LOYALTY_CLAIMED == 1 ? 'YES' : 'NO' }}</button>
                                                    @if($row->LOYALTY_CLAIMED != 1)
                                                    <div class="dropdown-menu dropdown-menu-sm" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 40px, 0px);">
                                                        <a class="dropdown-item btn-danger" href="{{ route('admin.loyalty-claime', [$row->PK_NO]) }}"  onclick="return confirm('Are you sure?')">YES</a>
                                                    </div>
                                                    @endif
                                                </div>
                                                @else
                                                <span>N/A</span>
                                                @endif

                                            </td>

                                            <td class="text-center">


                                                @if($vat_amt != 0 )
                                                {{ $row->VAT_CLAIMED == 1 ? 'YES' : 'NO ('.$vat_amt.')' }}
                                                {{--<div class="btn-group">
                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle {{ $row->VAT_CLAIMED != 1 ? 'text-danger' : ''}}  " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $row->VAT_CLAIMED == 1 ? 'YES' : 'NO ('.$vat_amt.')' }}</button>
                                                    @if($row->VAT_CLAIMED != 1)
                                                    <div class="dropdown-menu dropdown-menu-sm" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 40px, 0px);">
                                                        <a class="dropdown-item" href="{{ route('admin.vat-claime', [$row->PK_NO]) }}"  onclick="return confirm('Are you sure?')">YES</a>
                                                    </div>
                                                    @endif
                                                </div>--}}
                                                @else
                                                <span>N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-right">{{ number_format($row->INVOICE_EXACT_VAT,2) }}</td>

                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle {{ $row->INV_STOCK_RECORD_GENERATED != 1 ? 'text-danger' : ''}}  " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $row->INV_STOCK_RECORD_GENERATED == 1 ? 'YES' : 'NO' }}</button>
                                                    @if($row->INV_STOCK_RECORD_GENERATED != 1)
                                                    <div class="dropdown-menu dropdown-menu-sm" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 40px, 0px);">
                                                        @if(hasAccessAbility('new_stock', $roles))
                                                        <button class="dropdown-item stockGenerate" data-toggle="modal" data-target="#stockGenerate" title="STOCK GENERATE"  data-invoice_id="{{$row->INVOICE_NO}}" data-pk_no="{{$row->PK_NO}}" data-vendor_no="{{ $row->F_VENDOR_NO }}"  data-vendor_country_no="{{ $row->vendor->F_COUNTRY }}">YES</button>
                                                        @endif
                                                    </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if(hasAccessAbility('edit_vendor', $roles))
                                                    <a href="{{ route('admin.invoice-details', [$row->PK_NO]) }}" title="VIEW INVOICE DETAILS" class="btn btn-xs btn-success" ><i class="la la-eye"></i></a>
                                                @endif
                                                @if(hasAccessAbility('delete_vendor', $roles))
                                                    <a href="{{ route('admin.stock.delete', [$row->PK_NO]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Are You Sure?')" title="DELETE ALL STOCKS"><i class="la la-trash"></i> </a>
                                                @endif
                                                    <a href="{{ route('admin.invoice-product-details.get-product',['id'=>$row->PK_NO,'type'=>'stock-processing']) }}" class="btn btn-xs btn-success" title="VIEW PRODUCT POSITION"><i class="la la-eye"></i> </a>
                                                </td>
                                            </tr>
                                            @endforeach()
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@include('admin.procurement.invoice_processing._select_warehouse', $warehouse_combo)


@endsection

<!--push from page-->
@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
<script type="text/javascript" src="{{ asset('app-assets/pages/invoice.js')}}"></script>
<script>
    $(document).on('click','.page-link', function(){
        var pageNum = $(this).text();
        setCookie('invoice-processing',pageNum);
    });
    var value = getCookie('invoice-processing');
    var table = $('#indextable').dataTable();

    if (value !== null) {
        var value = value-1
        table.fnPageChange(value,true);
    }
</script>
@endpush('custom_js')
