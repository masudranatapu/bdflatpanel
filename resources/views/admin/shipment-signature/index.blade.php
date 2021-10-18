
@extends('admin.layout.master')

@section('Shipping','open')
@section('shipment_sign','active')

@section('title') Shipment Sign @endsection
@section('page-name') Shipment Sign List @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard  </a></li>
    <li class="breadcrumb-item active">Shipment Sign</li>
@endsection

@php
    $roles = userRolePermissionArray()
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
                            {{-- @if(hasAccessAbility('new_role', $roles)) --}}
                            <a class="text-white btn btn-round btn-sm btn-primary" href="{{ route('admin.shipment-signature.create') }}" title="Add new"> <i class="ft-plus text-white"></i> New Signature</a>
                            {{-- @endif --}}
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
                                    <table class="table table-striped table-bordered alt-pagination table-sm" id="indextable">
                                        <thead>
                                        <tr>
                                            <th class="text-center" style="width: 8%">SL</th>
                                            <th class="text-center">Name</th>
                                            <th style="width: 40%" class="text-center">Signature</th>
                                            <th style="width: 80px;" class="text-center">@lang('tablehead.tbl_head_action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($rows as $row)
                                            <tr>
                                                <td class="text-center">{{$loop->index + 1}}</td>
                                                <td class="text-center">{{ $row->NAME  }}</td>
                                                <td class="text-center"><img style="height: 80px" src="{{ asset('/') }}{{ $row->IMG_PATH }}"></td>
                                                <td style="width: 80px;" class="text-center">
                                                    <a href="{{ route('admin.shipment-signature.edit',$row->PK_NO) }}">
                                                        <button type="button"
                                                                class="btn btn-xs btn-success mr-1"><i
                                                                class="la la-edit"></i>
                                                        </button>
                                                    </a>
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
