@extends('admin.layout.master')

@section('hscode','active')
@section('Products','open')
@section('title')
@lang('product.hscode_title')
@endsection
@section('page-name')
@lang('product.hscode_title')
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('product.breadcrumb_title')    </a>
</li>
<li class="breadcrumb-item active">@lang('product.breadcrumb_hscode_title')
</li>
@endsection
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush
@php
$roles = userRolePermissionArray();
@endphp
@section('content')
<!-- Alternative pagination table -->
<div class="content-body min-height">
    <section id="pagination">
        <div class="row">
            <div class="col-12">
                <div class="card card-sm card-success">
                    <div class="card-header pl-2">
                        @if(hasAccessAbility('new_hscode', $roles))
                        <a href="javascript:void(0)" class="btn btn-round btn-sm btn-primary text-white"><i class="ft-plus text-white"></i> Create New

                        </a>
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
                                            <th>Sl.</th>
                                            <th class="text-left" >@lang('tablehead.category')</th>
                                            <th class="text-left" >@lang('tablehead.subcategory_name')</th>
                                            <th class="text-left" >@lang('tablehead.hs_code')</th>
                                            <th class="text-left" >@lang('tablehead.narration')</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($rows as $row)

                                    <tr>
                                        <td>{{$loop->index + 1}}</td>
                                        <td class="text-left">{{ $row->CAT_NAME }}</td>
                                        <td class="text-left">{{ $row->SCAT_NAME }}</td>
                                        <td class="text-left">{{ $row->HS_CODE }}</td>
                                        <td class="text-left">{{ $row->HS_NARRATION }}</td>
                                        <td>
                                            @if(hasAccessAbility('delete_hscode', $roles))
                                            <a href="{{route('admin.hscode.delete',$row->HS_PK_NO)}}" onclick="return confirm('Are you sure you want to delete?')" class="btn btn-xs btn-danger mr-1" title="DELETE">
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
