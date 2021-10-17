@extends('admin.layout.master')

@section('Warehouse Operation','open')
@section('product_list_','active')

@section('title') @lang('unshelve.list_page_title') @endsection
@section('page-name') @lang('unshelve.list_page_sub_title') @endsection

<?PHP
    $roles = userRolePermissionArray();
?>

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"> @lang('unshelve.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active"> @lang('unshelve.breadcrumb_sub_title')</li>
@endsection

@push('custom_css')
 <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
 <link rel="stylesheet" href="{{ asset('app-assets/file_upload/image-uploader.min.css')}}">
 <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
 <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
 <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.css">
@endpush('custom_css')

@section('content')
    <div class="card card-success min-height">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> View
            Product</h4>
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
                                @foreach ($items as $item)
                                <div class="row">
                                    <div style="width: 70px;" class="p-2">
                                       <strong>SL.</strong><br><br>
                                        {{ $loop->index +1 }}
                                    </div>
                                    <div class="col-md-2 pb-1">
                                        <img style="width: 150px !important; height: 150px;" src="{{ asset('/') }}{{ $item->PRD_VARIANT_IMAGE_PATH }}" alt="">
                                    </div>
                                    <div class="col-md-5 pb-1">
                                        <h5><b>Product Name : </b> {{ $item->PRD_VARINAT_NAME }}</h5>
                                        <h5><b>IG Code:</b> {{ $item->IG_CODE }}</h5>
                                        <h5><b>SKU Id:</b> {{ $item->sku_id }}</h5>
                                        <h5><b>BARCODE:</b> {{ $item->BARCODE }}</h5>
                                        {{-- <h5><b>Product Count:</b> {{ $item->count }}</h5> --}}

                                        @if(Request::segment(4) != 'shelved')
                                        @if(hasAccessAbility('view_purchace_price', $roles))
                                        <?php
                                        $string = $item->F_PRC_STOCK_IN_NO;
                                        if ($string) {
                                            $string = explode(',',$string);
                                            $total_price = 0;
                                            $line_price  = 0;
                                            $total_av_qty = 0;
                                            $is_vat = 0;
                                            foreach ($string as $key => $value) {
                                                $data = $item->getPrcStockInDetails($value,$item->sku_id);
                                                $available_qty = $data['total'] - $data['dispatched'];
                                            ?>
                                                <h5 style="color: #ED1C24"><b><a href="{{ asset($data['info']->MASTER_INVOICE_RELATIVE_PATH)}}" target="_blank" >{{ $data['info']->INVOICE_DATE }} {{ $data['info']->INVOICE_NO }}</a>
                                                 ({{ $data['total'] }}) <span style="color: #00A65A">{{ $available_qty }}
                                                @if (!empty($data['vat']) && $data['vat']->VAT_AMOUNT_PERCENT > 0)
                                                <?php
                                                $purchase_price_with_vat = $data['info']->PRODUCT_PURCHASE_PRICE * (100+$data['vat']->VAT_AMOUNT_PERCENT) / 100;
                                                $is_vat++;
                                                $line_price = $available_qty * $purchase_price_with_vat;
                                                $total_price += $line_price;
                                                $total_av_qty += $available_qty;
                                                ?>
                                                X RM {{ number_format($purchase_price_with_vat,2) }} = {{ number_format($line_price,2) }}
                                                </span>
                                                @else
                                                <?php
                                                $line_price = $available_qty * $data['info']->PRODUCT_PURCHASE_PRICE;
                                                $total_price += $line_price;
                                                $total_av_qty += $available_qty;
                                                ?>
                                                @endif
                                                </b></h5>
                                            <?php
                                            }
                                        }
                                        ?>
                                        @endif
                                        @endif
                                    </div>
                                    <div class="col-md-4 pb-1">
                                        <h5><b>Brand Name:</b> {{ $item->BRAND_NAME }}</h5>
                                        <h5><b>Model Name:</b> {{ $item->MODEL_NAME }}</h5>
                                        <h5><b>Warehouse:</b> {{ $item->INV_WAREHOUSE_NAME }}</h5>
                                        <?php if (Request::segment(4) == 'shelved') {  ?>
                                            <h5><b>Quantity:</b> {{ $item->shelved_qty }}</h5>
                                        <?php } ?>
                                        @if(hasAccessAbility('view_purchace_price', $roles))
                                        @if ($is_vat > 0)
                                        <h5 style="color:#00A65A"><b>Price With VAT - RM</b> {{ number_format($total_price/$total_av_qty,2) }}</h5>
                                        @else
                                        <h5 style="color:red"><b>Price W/VAT - RM:</b> {{ number_format($total_price/$total_av_qty,2) }}</h5>
                                        @endif
                                        <p><small style="color:#ED1C24">*Please add VAT and Shipment Cost For Purchase Price</small></p>
                                        <h5><b>Option 1 Price : </b> {{ number_format($item->option1,2) }} RM</h5>
                                        <h5><b>Option 2 Price : </b> {{ number_format($item->option2,2) }} RM</h5>
                                        @endif
                                    </div>

                                </div>
                                <hr>
                                @endforeach
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('custom_js')
 <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
 <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.js"></script>

@endpush('custom_js')
