@extends('admin.layout.master')

@section('Shipping','open')
@section('list_shipping','active')

@section('title')
    @lang('shipping.list_page_title')
@endsection
@section('page-name')
    @lang('shipping.list_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('shipping.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('shipping.breadcrumb_sub_title')
    </li>
@endsection
@php
    $roles = userRolePermissionArray();
    $shipment_status = Config::get('static_array.shipping_status2') ?? array();
@endphp
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush
@section('content')
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            @if(hasAccessAbility('new_shipment', $roles))
                            <a class="text-white" href="{{route('admin.shipment.create')}}">
                                <button type="button" class="btn btn-round btn-sm btn-primary">
                                    <i class="ft-plus text-white"></i> New Shipment
                                </button>
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
                                <div class="table-responsive text-center p-1">
                                    <table class="table table-striped table-bordered alt-pagination table-sm" id="indextable">
                                        <thead>
                                        <tr>
                                            <th>SL.</th>
                                            <th>Shipment No.</th>
                                            <th>AWB/Docket No.</th>
                                            <th>Shipping Agent</th>
                                            <th>Box Count</th>
                                            <th>Departure Date</th>
                                            <th>Arrival Date</th>
                                            <th>Status</th>
                                            <th style="width: 11%">@lang('tablehead.tbl_head_action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($shipment as $row)
                                            <tr>
                                                <td>{{ $loop->index+1 }}</td>
                                                <td>{{ $row->CODE }}</td>
                                                <td>{{ $row->WAYBILL }}</td>
                                                <td>{{ $row->shippingAddress->NAME ?? '' }}</td>
                                                {{-- <td>{{ $row->SENDER_BOX_COUNT }}</td> --}}
                                                <td title="UNBOXED-{{ $row->received }} TOTAL-{{ $row->SENDER_BOX_COUNT }}">{{ $row->received }}/{{ $row->SENDER_BOX_COUNT }}</td>

                                                <td>{{ date('d-m-Y', strtotime($row->SCH_DEPARTING_DATE)) }}</td>
                                                <td>{{ date('d-m-Y', strtotime($row->SCH_ARRIVAL_DATE)) }}</td>
                                                <td>
                                                    {{ $shipment_status[$row->SHIPMENT_STATUS] ?? '' }}

                                                </td>
                                                {{-- <td class="text-center">
                                                <div class="controls">
                                                    {!! Form::select('shipment_status', $shipment_status, $row->SHIPMENT_STATUS, ['class'=>'custom-select', 'id' => 'shipment_status','data-shipment_id' => $row->PK_NO]) !!}
                                                    {!! $errors->first('shipment_status', '<label class="help-block text-danger">:message</label>') !!}
                                                </div>
                                                </td> --}}
                                                <td>
                                                    @if(hasAccessAbility('new_shipment', $roles))
                                                    {{-- @if ($row->SHIPMENT_STATUS < 20) --}}
                                                    <a href="{{ route('admin.shipment.create',['id'=>$row->PK_NO]) }}" title="Edit Shipment">
                                                        <button type="button"
                                                        class="btn btn-xs btn-info"><i
                                                        class="la la-edit"></i>
                                                    </button>
                                                    </a>
                                                    {{-- @endif --}}
                                                    @endif
                                                    @if(hasAccessAbility('view_shipment', $roles))
                                                    <a href="{{ route('admin.shipment.view',['id'=>$row->PK_NO]) }}" title="View Shipment">
                                                        <button type="button"
                                                        class="btn btn-xs btn-success"><i
                                                        class="la la-eye"></i>
                                                    </button>
                                                    </a>
                                                    @endif
                                                    @if(hasAccessAbility('new_shipment_box', $roles))
                                                    <a href="{{ route('admin.shipment.new',['id'=>$row->PK_NO]) }}" title="Add Shipment Box">
                                                        <button type="button"
                                                                class="btn btn-xs btn-primary"><i
                                                                class="la la-plus"></i>
                                                        </button>
                                                    </a>
                                                    @endif
                                                    {{-- @if(hasAccessAbility('delete_vendor', $roles))
                                                    <a href="#!" onclick="return confirm('Are You Sure?')" title="Delete Shipment">
                                                        <button type="button"
                                                                class="btn btn-xs btn-danger"><i
                                                                class="la la-trash"></i>
                                                        </button>
                                                    </a>
                                                    @endif --}}
                                                </td>
                                            </tr>
                                            @endforeach
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
 <script type="text/javascript" src="{{ asset('app-assets/pages/shipment.js')}}"></script>
@endpush('custom_js')
