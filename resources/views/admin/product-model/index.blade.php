@extends('admin.layout.master')
@section('product model','active')
@section('title')
    @lang('user_group.product_model_page_title')
@endsection
@section('page-name')
    @lang('user_group.product_model_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('user_group.breadcrumb_title')</a>
    </li>
    <li class="breadcrumb-item active">@lang('user_group.product_model_page_model')
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

<?php
$roles = userRolePermissionArray();
?>
@section('content')
    <!-- Alternative pagination table -->
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <div class="form-group">
                                @if(hasAccessAbility('new_model', $roles))
                                <a class="text-white" href="{{ route('admin.product-model.new')}}">
                                    <button type="button" class="btn btn-round btn-sm btn-primary">
                                        <i class="ft-user-plus"></i> @lang('user_group.product_model_page_create_model')
                                    </button>
                                </a>
                                @endif
                            </div>
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
                            <div class="card-body card-dashboard text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered alt-pagination table-sm" id="indextable">
                                        <thead>
                                        <tr>
                                            <th>@lang('tablehead.sl')</th>
                                            <th class="text-left">@lang('tablehead.name')</th>
                                            <th class="text-left">@lang('tablehead.model_name')</th>
                                            <th class="text-left">@lang('tablehead.model_code')</th>
                                            <th class="text-left">@lang('tablehead.composite_code')</th>
                                            <th style="width: 120px;">@lang('tablehead.action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($model as $row)
                                            <tr>
                                                <td>{{$loop->index+1}}</td>
                                                <td class="text-left">{{$row->brand->NAME ?? ''}}</td>
                                                <td class="text-left">{{$row->NAME}}</td>
                                                <td class="text-left">{{$row->CODE}}</td>
                                                <td class="text-left">{{$row->COMPOSITE_CODE}}</td>

                                                <td>
                                                    @if(hasAccessAbility('edit_model', $roles))
                                                    <a href="{{ route('admin.product-model.edit', [$row->PK_NO]) }}" class="btn btn-xs btn-primary mr-1" title="EDIT"><i
                                                                class="la la-edit"></i></a>
                                                    @endif
                                                    @if(hasAccessAbility('delete_model', $roles))
                                                    <a href="{{route('admin.product-model.delete', [$row->PK_NO])}}" class="btn btn-xs btn-danger mr-1" title="DELETE" onclick="return confirm('Are you sure you want to delete?')"><i class="la la-trash"></i>
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
