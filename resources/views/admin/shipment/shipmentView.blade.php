@extends('admin.layout.master')

@section('Shipping','open')
@section('processing_shipping','active')

@section('title') Box Details @endsection
@section('page-name') Box Details @endsection

<?PHP
    $roles = userRolePermissionArray();
?>

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('admin.shipment.list') }}"> Box details in the shipment </a>
    </li>
@endsection

@push('custom_css')
 <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
 <link rel="stylesheet" href="{{ asset('app-assets/file_upload/image-uploader.min.css')}}">
 <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
 <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
 <link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">

@endpush('custom_css')
@section('content')
    <div class="card card-success min-height">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> View
            shipment</h4>
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
                                        <h5><b>Shipment No:</b>     {{ $shipment['data']->CODE }}</h5>
                                        <h5><b>Shipping Agent:</b> {{ $shipment['data']->shipment_address_set_agent->NAME ?? '' }}</h5>
                                        <h5><b>Sender Address:</b> {{ $shipment['data']->shipment_address_set_from->NAME ?? '' }}</h5>
                                        <h5><b>From Warehouse:</b> {{ $shipment['data']->from_warehouse->NAME }}</h5>
                                        <h5><b>Scheduled Departure:</b> {{ date('d-m-Y', strtotime($shipment['data']->SCH_DEPARTING_DATE)) }}</h5>
                                        <h5><b>Actual Departure:</b> {{ date('d-m-Y', strtotime($shipment['data']->ACT_DEPARTING_DATE)) }}</h5>
                                    </div>
                                    <div class="col-md-4 pb-1">
                                        <h5><br></h5>
                                        <h5><b>Receiving Agent:</b> {{ $shipment['data']->shipment_address_set_receiver->NAME ?? '' }}</h5>
                                        <h5><b>Receiving Address:</b> {{ $shipment['data']->shipment_address_set_ship_to->NAME ?? '' }}</h5>
                                        <h5><b>To Warehouse:</b> {{ $shipment['data']->to_warehouse->NAME }}</h5>
                                        <h5><b>Scheduled Arrival:</b> {{ date('d-m-Y', strtotime($shipment['data']->SCH_ARRIVAL_DATE)) }}</h5>
                                        <h5><b>Actual Arrival:</b> {{ date('d-m-Y', strtotime($shipment['data']->ACT_ARRIAVAL_DATE)) }}</h5>
                                    </div>
                                    <div class="col-md-4 pb-1">
                                        <h5><br></h5>
                                        <h5><b>Waybill:</b> {{ $shipment['data']->WAYBILL }}</h5>
                                        <h5><b>Freight GBP:</b> {{ $shipment['data']->FREIGHT_GBP }}</h5>
                                        <h5><b>Freight RM:</b> {{ $shipment['data']->FREIGHT_RM }}</h5>
                                        <h5><b>Receiver Box Count:</b> {{ $shipment['data']->RECIVER_BOX_COUNT }}</h5>
                                        <h5><b>Shipment Type:</b> {{ $shipment['data']->IS_AIR_SHIPMENT == 0 ? 'Ship' : 'Air' }}</h5>
                                        <h5><b>Quantity:</b> {{ $shipment['count'] }}</h5>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive text-center">
                            <table class="table table-striped table-bordered alt-pagination50 table-sm" id="process_data_table_">
                                <thead>
                                    <tr>
                                        <th>SL.</th>
                                        <th>Box No.</th>
                                        <th>Box Label</th>
                                        <th>Recent Accessed By </th>
                                        <th>Product Count</th>
                                        <th>Warehouse</th>
                                        <th style="width: 11%">@lang('tablehead.tbl_head_action')</th>
                                    </tr>
                                </thead>
                                <tbody id="append_tr">
                                    @if ($shipment['box'] != null)
                                    @foreach ($shipment['box'] as $row)
                                    <tr>
                                        <td class="text-center">{{ $loop->index + 1 }}</td>
                                        <td>{{ $row->box_serial->BOX_SERIAL }}</td>
                                        <td>{{ $row->BOX_NO }}</td>
                                        <td>{{ $row->USER_NAME }}</td>
                                        <td title="UNBOXED-{{ $row->unboxed }} TOTAL-{{ $row->ITEM_COUNT }}">{{ $row->unboxed }}/{{ $row->ITEM_COUNT }}</td>
                                        <?php
                                            $status = \Config::get('static_array.box_status');
                                        ?>
                                        <td>{{ $status[$row->BOX_STATUS] }}</td>
                                        <td class="text-center">
                                            @if(hasAccessAbility('view_box', $roles))
                                            <a href="{{ route('admin.box.view',$row->PK_NO) }}" class="btn btn-xs btn-success mr-1" title="VIEW BOX"><i class="la la-eye"></i></a>

                                            @endif
                                            @if(hasAccessAbility('delete_shipment_box', $roles))
                                            @if ($row->BOX_STATUS <= 20)
                                            <a href="javascript:void(0)" class="btn btn-xs btn-danger mr-1" id="delete_prd{{ $row->box_serial->BOX_SERIAL }}" title="DELETE" data-shipment_id={{ $row->box_serial->PK_NO }}><i class="la la-trash"></i></a>
                                            @endif
                                            @endif
                                            @if(hasAccessAbility('view_faulty', $roles))
                                            @if ($row->faulty_count > 0)
                                            <a href="{{ route('admin.faulty.list',['box',$row->PK_NO]) }}" class="btn btn-xs btn-azura mr-1" title="FAULTY ITEM EXISTS"><i class="la la-wrench"></i></a>
                                            @else
                                            <a href="{{ route('admin.faulty.list',['box',$row->PK_NO]) }}" class="btn btn-xs btn-warning mr-1" title="MARK FAULTY"><i class="la la-warning"></i></a>
                                            @endif
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
@endsection
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/shipment.js')}}"></script>
