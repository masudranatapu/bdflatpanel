@extends('admin.layout.master')

@section('Shipping','open')

@section('title')
    @lang('shipping.new_page_title')
@endsection
@section('page-name')
    @lang('shipping.list_page_sub_title')
@endsection

<?PHP
    $roles = userRolePermissionArray();
?>

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('admin.shipment.list') }}"> shipment </a>
    </li>
    <li class="breadcrumb-item active">Create shipment
    </li>
@endsection

@push('custom_css')
 <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
 <link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
@endpush('custom_css')
@section('content')
    <div class="card card-success min-height">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> New
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
                        {{-- {!! Form::open([ 'route' => 'admin.shipment.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'id' => 'save-inv-details']) !!} --}}
                            {{-- @csrf --}}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6 pb-1">
                                        <h5><b>Shipment No:</b> {{ $shipmentInfo->CODE }}</h5>
                                        <h5><b>Shipment box count:</b> {{ $shipmentInfo->SENDER_BOX_COUNT }}</h5>
                                        <h5><b>Shipping Agent:</b> {{ $shipmentInfo->SHIPPING_AGENT }}</h5>
                                        <h5><b>Sender Address:</b> {{ $shipmentInfo->SENDER_ADDRESS }}</h5>
                                        <h5><b>From Warehouse:</b> {{ $shipmentInfo->from_warehouse->NAME }}</h5>
                                        <h5><b>Scheduled Departure:</b> {{ date('d-m-Y', strtotime($shipmentInfo->SCH_DEPARTING_DATE)) }}</h5>
                                    </div>
                                    <div class="col-md-6 pb-1">
                                        <h5><br></h5>
                                        <h5><br></h5>
                                        <h5><b>Receiving Agent:</b> {{ $shipmentInfo->RECIEVING_AGENT }}</h5>
                                        <h5><b>Receiving Address:</b> {{ $shipmentInfo->RECIVER_ADDRESS }}</h5>
                                        <h5><b>To Warehouse:</b> {{ $shipmentInfo->to_warehouse->NAME }}</h5>
                                        <h5><b>Scheduled Arrival:</b> {{ date('d-m-Y', strtotime($shipmentInfo->SCH_DEPARTING_DATE)) }}</h5>
                                        <h5><b>Shipment Type:</b> {{ $shipmentInfo->IS_AIR_SHIPMENT == 1 ? 'AIR' : 'SEA' }}</h5>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group {!! $errors->has('bar_code1') ? 'error' : '' !!}">
                                            <label>Shipping Label<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::number('bar_code1', null,[ 'class' => 'form-control mb-1', 'placeholder' => 'Enter barcode of shipping label', 'id'=>'bar_code1', 'autofocus', $shipmentInfo->SHIPMENT_STATUS > 10 ? 'disabled' : '' ]) !!}
                                                {!! $errors->first('bar_code1', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group {!! $errors->has('bar_code2') ? 'error' : '' !!}">
                                            <label>Box Label<span class="text-danger">*</span></label>
                                            <div class="controls">
                                                {!! Form::number('bar_code2', null,[ 'class' => 'form-control mb-1', 'placeholder' => 'Enter barcode of box label', 'id'=>'bar_code2', 'autofocus', $shipmentInfo->SHIPMENT_STATUS > 10 ? 'disabled' : '' ]) !!}
                                                {!! $errors->first('bar_code2', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 26px;">
                                        <button type="button" class="btn btn-primary  btn-sm" id="shipment_submit" {{ $shipmentInfo->SHIPMENT_STATUS > 10 ? 'disabled' : '' }}>
                                           Add Box to Shipment
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-sm" id="invoicetable">
                                                <thead>
                                                    <tr>
                                                        <th>SL.</th>
                                                        <th>Box Serial</th>
                                                        <th>Box Label</th>
                                                        <th>Item Count</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot id="append_tr">
                                                    @if ($shipments != null)
                                                        @foreach ($shipments as $shipment)
                                                        <tr>
                                                            <td style="width: 50px;"><input type="number" id="serial_no" value="{{ $loop->index+1 }}" style="width: 50px;border: none;"></td>
                                                            <td id="box_serial">{{ $shipment->BOX_SERIAL }}</td>
                                                            <td id="box_name">{{ $shipment->SC_BOX->BOX_NO }}</td>
                                                            <td>{{ $shipment->PRODUCT_COUNT }}</td>
                                                            @if ($shipmentInfo->SHIPMENT_STATUS <= 10)
                                                            <td>
                                                                @if(hasAccessAbility('delete_shipment_box', $roles))
                                                                <a href="javascript:void(0)" class="btn btn-xs btn-danger" id="delete_prd{{ $shipment->BOX_SERIAL }}" title="DELETE" data-shipment_id={{ $shipment->PK_NO }}><i class="la la-trash"></i></a></td>
                                                                @endif
                                                            @endif
                                                        </tr>
                                                        @endforeach
                                                    @endif
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions mt-10 text-center">
                                <a href="{{ route('admin.shipment.list')}}">
                                    <button type="button" class="btn btn-warning mr-1">
                                        <i class="ft-x"></i> Cancel
                                    </button>
                                </a>
                                {{-- <button type="button" class="btn btn-primary save-inv-details">
                                    <i class="la la-check-square-o"></i> Save
                                </button> --}}
                            </div>
                        {{-- {!! Form::close() !!} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom_js')
 <script type="text/javascript" src="{{ asset('app-assets/pages/shipment.js')}}"></script>
 <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
@endpush('custom_js')
