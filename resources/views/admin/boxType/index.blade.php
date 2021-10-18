@extends('admin.layout.master')

@section('box_type_list','active')

@section('title')
    Box Type
@endsection
@section('page-name')
Box Type
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Box Type    </a>
    </li>
    <li class="breadcrumb-item active">@lang('box.breadcrumb_sub_title')
    </li>
@endsection
@php
    $roles = userRolePermissionArray();
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
                            <a class="text-white" href="{{route('admin.box_type.add')}}">
                                <button type="button" class="btn btn-round btn-sm btn-primary">
                                    <i class="ft-plus text-white"></i> Add box type
                                </button>
                            </a>
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
                                    <table class="table table-striped table-bordered alt-pagination table-sm" id="process_data_table_">
                                        <thead>
                                        <tr>
                                            <th>SL.</th>
                                            <th>Type</th>
                                            <th>Width (CM)</th>
                                            <th>Length (CM)</th>
                                            <th>Height (CM)</th>
                                            <th style="width: 11%">@lang('tablehead.tbl_head_action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rows as $row)
                                            <tr>
                                                <td>{{ $loop->index+1 }}</td>
                                                <td>{{ $row->TYPE }}</td>
                                                <td>{{ $row->WIDTH_CM }}</td>
                                                <td>{{ $row->LENGTH_CM }}</td>
                                                <td>{{ $row->HEIGHT_CM }}</td>
                                                <td>
                                                    @if(hasAccessAbility('edit_box_type', $roles))
                                                        <a class="text-white btn btn-xs btn-primary mr-1" title="Edit" href="{{ route('admin.box_type.add', array($row->PK_NO)) }}"><i class="la la-edit"></i></a>
                                                    @endif
                                                    @if(hasAccessAbility('delete_box_type', $roles))
                                                        <a class="text-white btn btn-xs btn-danger mr-1" title="Delete" href="{{ route('admin.box_type.delete', array($row->PK_NO)) }}"  onclick="return confirm('Are you sure?')"><i class="la la-trash"></i></a>
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
@endsection
