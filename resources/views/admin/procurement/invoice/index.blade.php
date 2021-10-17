@extends('admin.layout.master')

@section('invoice','active')
@section('Procurement','open')
@section('title')
    @lang('invoice.list_page_title')
@endsection
@section('page-name')
    @lang('invoice.list_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('invoice.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('invoice.breadcrumb_sub_title')
    </li>
@endsection
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush
@php
    $roles = userRolePermissionArray()
@endphp
@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            @if(hasAccessAbility('new_invoice', $roles))
                            <a class="text-white btn btn-round btn-sm btn-primary" href="{{route('admin.invoice.new')}}"  title="Add new invoice">
                                <i class="ft-plus text-white"></i> @lang('invoice.create_btn')
                            </a>
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
                            <div class="card-body card-dashboard">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="indextable">
                                        <thead>
                                        <tr>
                                            <th class="text-center">SL.</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Vendor</th>
                                            <th class="text-center">Reciept No</th>
                                            <th class="text-center">Currency</th>
                                            <th class="text-center" title="Calculated/Reciept Value">Cal/Rec </th>
                                            <th class="text-center">Rec(Faulty)/Total</th>
                                            <th class="text-center">VAT</th>
                                            <th class="text-center" style="width: 220px;">@lang('tablehead.tbl_head_action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($rows as $row)
                                            <tr>
                                                <td class="text-center">{{ $loop->index + 1 }}</td>
                                                <td class="text-center">{{ date('d-m-Y',strtotime($row->INVOICE_DATE)) }}</td>
                                                <td>{{ $row->VENDOR_NAME }}</td>
                                                <td>{{ $row->INVOICE_NO }}</td>
                                                <td class="text-center">{{$row->INVOICE_CURRENCY}}</td>
                                                <td class="text-right" >
                                                    @if($row->INVOICE_CURRENCY == 'RM')
                                                        @php
                                                        $cal_val = $row->INVOICE_REC_TOTAL_ACTUAL_MR_WITH_VAT + $row->INVOICE_POSTAGE_ACTUAL_MR
                                                        @endphp

                                                    @elseif($row->INVOICE_CURRENCY == 'GBP')
                                                        @php
                                                        $cal_val = $row->INVOICE_REC_TOTAL_ACTUAL_GBP_WITH_VAT + $row->INVOICE_POSTAGE_ACTUAL_GBP
                                                        @endphp

                                                    @else
                                                        @php
                                                        $cal_val = $row->INVOICE_REC_TOTAL_ACTUAL_AC_WITH_VAT + $row->INVOICE_POSTAGE_ACTUAL_AC
                                                        @endphp

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
                                                <td class="text-right">
                                                    @if($row->INVOICE_CURRENCY == 'RM')
                                                        {{number_format($row->INVOICE_REC_TOTAL_ACTUAL_MR_ONLY_VAT,2)}}
                                                    @elseif($row->INVOICE_CURRENCY == 'GBP')
                                                        {{number_format($row->INVOICE_REC_TOTAL_ACTUAL_GBP_ONLY_VAT,2)}}
                                                    @else
                                                        {{number_format($row->INVOICE_REC_TOTAL_ACTUAL_AC_ONLY_VAT,2)}}
                                                    @endif
                                                </td>

                                                <td class="text-center" style="widows: 220px;">

                                                    @if($row->INV_STOCK_RECORD_GENERATED != 1)
                                                        @if(hasAccessAbility('new_invoice', $roles))
                                                        <a href="{{ route('admin.invoice-details.new', [$row->PK_NO]) }}" title="ADD LINE ITEM">
                                                            <button type="button"
                                                                    class="btn btn-xs btn-info mr-05"><i
                                                                    class="la la-plus" ></i>
                                                            </button>
                                                        </a>
                                                        @endif
                                                    @endif

                                                    @if($row->INV_STOCK_RECORD_GENERATED != 1 )
                                                        @if(hasAccessAbility('edit_invoice', $roles))
                                                        <a href="{{ route('admin.invoice.edit', [$row->PK_NO]) }}" title="INVOICE EDIT">
                                                            <button type="button"
                                                                    class="btn btn-xs btn-info mr-05">
                                                                    <i class="la la-pencil"></i>
                                                            </button>
                                                        </a>
                                                        @endif
                                                    @endif

                                                    @if(hasAccessAbility('view_invoice', $roles))
                                                    <a href="{{ route('admin.invoice-details', [$row->PK_NO]) }}" title="VIEW INVOICE DETAILS">
                                                        <button type="button"
                                                                class="btn btn-xs btn-success mr-05">
                                                                <i class="la la-eye"></i>
                                                        </button>
                                                    </a>
                                                    @endif


                                                    @if($row->F_CHILD_PRC_STOCK_IN > 0 )


                                                        @if(hasAccessAbility('view_invoice', $roles))
                                                        <a href="{{ route('admin.invoice-details', [$row->  F_CHILD_PRC_STOCK_IN]) }}" title="VIEW CHILD INVOICE" class="btn btn-xs btn-success mr-05">&nbsp;C&nbsp;</a>
                                                        @endif

                                                    @else
                                                        @if(hasAccessAbility('new_invoice', $roles))
                                                            <a href="{{ route('admin.invoice.new', ['type' => 'child', 'parent' => $row->PK_NO]) }}" title="ADD CHILD INVOICE UNDER THE INVOICE" class="btn btn-xs btn-primary mr-05">
                                                               C<i class="la la-plus" ></i>

                                                            </a>
                                                        @endif
                                                    @endif

                                                    @if($row->F_PARENT_PRC_STOCK_IN > 0 )

                                                        @if(hasAccessAbility('view_invoice', $roles))
                                                            <a href="{{ route('admin.invoice-details', [$row->  F_PARENT_PRC_STOCK_IN]) }}" title="VIEW PARENT INVOICE" class="btn btn-xs btn-success mr-05">&nbsp;P&nbsp;</a>
                                                        @endif


                                                    @endif

                                                    @if($row->INV_STOCK_RECORD_GENERATED != 1 )
                                                        @if(hasAccessAbility('delete_invoice', $roles))
                                                            <a href="{{ route('admin.invoice.delete', [$row->PK_NO]) }}" onclick="return confirm('Are You Sure?')" title="INVOICE DELETE">
                                                            <button type="button"

                                                                class="btn btn-xs btn-danger mr-05"><i
                                                                class="la la-trash"></i>
                                                            </button>
                                                            </a>
                                                        @endif
                                                    @endif

                                                </td>
                                            </tr>
                                        @endforeach()
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
@endsection
@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
<script type="text/javascript" src="{{ asset('app-assets/pages/invoice.js')}}"></script>
<script>
    $(document).on('click','.page-link', function(){
        var pageNum = $(this).text();
        setCookie('invoice',pageNum);
    });
    var value = getCookie('invoice');
    var table = $('#indextable').dataTable({
        'pageLength' : 50,
    });

    if (value !== null) {
        var value = value-1
        table.fnPageChange(value,true);
    }
</script>
@endpush('custom_js')
