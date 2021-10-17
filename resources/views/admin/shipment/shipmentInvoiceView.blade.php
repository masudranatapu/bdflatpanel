@extends('admin.layout.master')

@section('Shipping','open')
@section('processing_shipping','active')

@section('title') Invoice Details @endsection
@section('page-name') Invoice Details  @endsection

<?PHP
    $roles = userRolePermissionArray();
?>

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.shipment.list') }}"> Invoice details </a></li>
@endsection

@push('custom_css')
 <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
 <link rel="stylesheet" href="{{ asset('app-assets/file_upload/image-uploader.min.css')}}">
 <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
 <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endpush('custom_css')
@section('content')
    <div class="card card-success min-height">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> View
            shipment Invoices</h4>
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
                                    <div class="col-md-4 pb-1">
                                        <h5><b>Shipment No:</b> {{ $shipment['data']->CODE }}</h5>
                                        <h5><b>Shipping Agent:</b> {{ $shipment['data']->shippingAddress->NAME ?? '' }}</h5>
                                        <h5><b>Sender Address:</b> {{ $shipment['data']->SENDER_ADDRESS }}</h5>
                                        <h5><b>From Warehouse:</b> {{ $shipment['data']->from_warehouse->NAME }}</h5>
                                        <h5><b>Scheduled Departure:</b> {{ date('d-m-Y', strtotime($shipment['data']->SCH_DEPARTING_DATE)) }}</h5>
                                        <h5><b>Actual Departure:</b> @if($shipment['data']->ACT_DEPARTING_DATE) {{ date('d-m-Y', strtotime($shipment['data']->ACT_DEPARTING_DATE)) }} @endif</h5>
                                    </div>
                                    <div class="col-md-4 pb-1">
                                        <h5><br></h5>
                                        <h5><b>Receiving Agent:</b> {{ $shipment['data']->receivingAddress->NAME ?? '' }}</h5>
                                        <h5><b>Receiving Address:</b> {{ $shipment['data']->RECIVER_ADDRESS }}</h5>
                                        <h5><b>To Warehouse:</b> {{ $shipment['data']->to_warehouse->NAME }}</h5>
                                        <h5><b>Scheduled Arrival:</b> {{ date('d-m-Y', strtotime($shipment['data']->SCH_ARRIVAL_DATE)) }}</h5>
                                        <h5><b>Actual Arrival:</b> @if($shipment['data']->ACT_ARRIAVAL_DATE){{ date('d-m-Y', strtotime($shipment['data']->ACT_ARRIAVAL_DATE)) }}@endif</h5>
                                    </div>
                                    <div class="col-md-4 pb-1">
                                        <h5><br></h5>
                                        <h5><b>Waybill:</b> {{ $shipment['data']->WAYBILL }}</h5>
                                        <h5><b>Freight GBP:</b> {{ $shipment['data']->FREIGHT_GBP }}</h5>
                                        <h5><b>Freight RM:</b> {{ $shipment['data']->FREIGHT_RM }}</h5>
                                        <h5><b>Receiver Box Count:</b> {{ $shipment['data']->RECIVER_BOX_COUNT }}</h5>
                                        <h5><b>Shipment Type:</b> {{ $shipment['data']->IS_AIR_SHIPMENT == 0 ? 'Ship' : 'Air' }}</h5>
                                        <h5><b>Quantity:</b> {{ $shipment['data']->SENDER_BOX_COUNT }}</h5>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive text-center">
                            <table class="table table-striped table-bordered alt-pagination table-sm" id="process_data_table_">
                                <thead>
                                    <tr>
                                        <th>SL.</th>
                                        <th>Date</th>
                                        <th>Vendor Name</th>
                                        <th>Invoice No.</th>
                                        <th>Exact Value</th>
                                        <th>Product Qty</th>
                                        <th>Invoice</th>
                                    </tr>
                                    </thead>
                                    <tbody id="append_tr">
                                        @if ($shipment['invoice'] != null)
                                        @foreach ($shipment['invoice'] as $row)
                                        <tr>
                                            <td class="text-center">{{ $loop->index + 1 }}</td>
                                            <td>{{ $row->INVOICE_DATE }}</td>
                                            <td>{{ $row->VENDOR_NAME }}</td>
                                            <td>{{ $row->INVOICE_NO }}</td>
                                            <td>{{ number_format($row->INVOICE_EXACT_VALUE,2) }}</td>
                                            <td><a href="javascript:void(0)" style="text-decoration: underline;" id="popup_product_modal" data-toggle="modal" data-target="#popup_product_modal_" title="See Details" data-url="product-details-modal" data-shipment_no="{{ $shipment['data']->PK_NO }}" data-invoice_no="{{ $row->pkno }}" data-type="shipped_invoice">{{ $row->qty }}</a>/{{ $row->total }}</td>
                                            <td class="text-center">
                                                @if(hasAccessAbility('view_box', $roles))
                                                <?php
                                                // print_r($row->allPhotosShipInvoice);
                                                    // $all_photo = \DB::table('PRC_IMG_LIBRARY')->select(DB::raw('RELATIVE_PATH as RELATIVE_PATH'))->where('F_PRC_STOCK_IN_NO',$row->F_PRC_STOCK_IN_NO)->get();
                                                    foreach ($row->allPhotosShipInvoice as $key => $value) {
                                                    ?>
                                                        <a href="{{ asset($value->RELATIVE_PATH)}}" target="_blank" class="btn btn-xs btn-success" title="VIEW INVOICE"><i class="la la-eye"></i></a>
                                                    <?php
                                                    }
                                                    ?>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.shipment._product_modal')
@endsection
@push('custom_js')
{{-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> --}}
<script type="text/javascript" src="{{ asset('app-assets/pages/shipment.js')}}"></script>
<script>
    $(document).on('click','#popup_product_modal', function(){
    var get_url = $('#base_url').val();
    var url         = $(this).data('url');
    var shipment_no = $(this).data('shipment_no');
    var invoice_no  = $(this).data('invoice_no');
    var type        = $(this).data('type');

    var pageurl = get_url+'/'+url+'/'+type;
    $.ajax({
        type:'post',
        url:pageurl,
        dataType: "json",
        data: {
            shipment_no: shipment_no,
            invoice_no: invoice_no,
            type: type,
            "_token": $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            $("body").css("cursor", "progress");
        },
        success: function (data) {
            var label = ''
            if (type == 'boxed') {
                var label = 'Product In Box'
            }else if (type == 'shipped') {
                var label = 'Product In Shipment'
            }else if (type == 'shelved') {
                var label = 'Product In Shelve'
            }else if (type == 'shipped_invoice') {
                var label = 'Product In Invoice'
            }
            $('#modal_title').text(label);
            $('#append_view').html('');
            $('#append_view').html(data);
        },
        complete: function (data) {
            $("body").css("cursor", "default");
        }
    });
});
</script>
@endpush('custom_js')
