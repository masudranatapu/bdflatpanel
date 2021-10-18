@extends('admin.layout.master')

@section('Warehouse Operation','open')
@section('box_list','active')

@section('title') Box View @endsection
@section('page-name') Box View @endsection

<?PHP
    $roles = userRolePermissionArray();
?>
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('admin.shipment.list') }}"> box view </a>
    </li>
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
            box</h4>
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
                                @foreach ($boxs as $box)
                                <?php
                                $string = $box->PRC_IN_IMAGE_PATH;
                                if ($string) {
                                    $string = explode(',',$string);
                                    // $string = array_unique($string);
                                }
                                ?>
                                <div class="row">
                                    <div style="width: 70px;" class="p-2">
                                       <strong>SL.</strong><br><br>
                                        {{ $loop->index +1 }}
                                    </div>
                                    <div class="col-md-2 pb-1">
                                        <img style="width: 150px !important; height: 150px;" src="{{ asset($box->PRD_VARIANT_IMAGE_PATH) }}" alt="">
                                    </div>
                                    <div class="col-md-5 pb-1">
                                        <h5><b>IG Code:</b> {{ $box->IG_CODE }}</h5>
                                        <h5><b>SKU Id:</b> {{ $box->sku_id }}</h5>
                                        <h5><b>Barcode :</b> {{ $box->BARCODE }}</h5>
                                        <h5><b>Product Name : </b> {{ $box->PRD_VARINAT_NAME }}</h5>
                                        <h5><b>Warehouse:</b> {{ $box->INV_WAREHOUSE_NAME }}</h5>
                                        @if(hasAccessAbility('view_purchace_price', $roles))
                                        <?php
                                            if ($string) {
                                                foreach ($string as $key => $value) {
                                                ?>
                                                    <h5><b><a href="{{ asset($value)}}" target="_blank" >Click here to see invoice</a></b></h5>

                                              <?php
                                                }
                                            }
                                        ?>
                                        @endif
                                    </div>
                                    <div class="col-md-4 pb-1">
                                        <h5><b>Brand Name:</b> {{ $box->BRAND_NAME }}</h5>
                                        <h5><b>Model Name:</b> {{ $box->MODEL_NAME }}</h5>
                                        <h5 title="UNBOXED-{{ $box->unboxed }} / TOTAL-{{ $box->total }}"><b>Quantity:</b> {{ $box->unboxed }}/{{ $box->total }}</h5>
                                        @if(hasAccessAbility('view_purchace_price', $roles))
                                        <h5><b>AVG Purchase Price:</b> {{ number_format($box->PRODUCT_PURCHASE_PRICE,2) }}</h5>
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
