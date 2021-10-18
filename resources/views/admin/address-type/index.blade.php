@extends('admin.layout.master')
@section('Customer Management','open')

@section('address_type','active')
@section('title') Customer Address Type @endsection

@section('page-name') Customer Address Type @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Customer Address</li>
@endsection
@push('custom_css')
@endpush
@push('custom_js')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush

@php
    $roles = userRolePermissionArray()
@endphp

@section('content')
    <!-- Alternative pagination table -->
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-sm card-success">
                        <div class="card-header pl-2">
                            @if(hasAccessAbility('new_address_type', $roles))
                                <a href="{{route('admin.address_type.create')}}" title="Add new" class="btn btn-round btn-sm btn-primary text-white"><i class="ft-plus text-white"></i> Create New</a>
                            @endif
                            <a class="heading-elements-toggle heading-elements-toggle-sm"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            <div class="heading-elements heading-elements-sm">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                    <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                    <li><a data-action="close"><i class="ft-x"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered alt-pagination table-sm" id="indextable">
                                        <thead>
                                        <tr>
                                            <th style="width: 50px;">Sl.</th>
                                            <th class="text-left" >@lang('tablehead.customer_address_type')</th>
                                            <th style="width: 100px;">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                       @foreach($rows as $row)

                                            <tr>
                                                <td>{{$loop->index + 1}}</td>
                                                <td class="text-left">{{ $row->NAME }}</td>
                                                <td style="width: 100px;">
                                                    @if(hasAccessAbility('edit_address_type', $roles))
                                                        <a href="{{route('admin.address_type.edit',$row->PK_NO)}}" class="btn btn-xs btn-info mr-1" title="EDIT"><i class="la la-edit"></i></a>
                                                    @endif
                                                    @if(hasAccessAbility('delete_address_type', $roles))
                                                        <a href="{{route('admin.address_type.delete',$row->PK_NO)}}" onclick="return confirm('Are you sure you want to delete?')" class="btn btn-xs btn-danger mr-1" title="DELETE">
                                                        <i class="la la-trash"></i>
                                                    </a>
                                                    @endif
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

    <!--/ Alternative pagination table -->
@endsection
