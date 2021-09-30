@extends('admin.layout.master')

@section('web_ads','open')
@section('ads','active')

@section('title') Ads @endsection
@section('page-name') Ads @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('agent.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active">Ads</li>
@endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{asset('/custom/css/custom.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@push('custom_js')

    <!-- BEGIN: Data Table-->
    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
    <!-- END: Data Table-->
@endpush

@php
    $roles = userRolePermissionArray()
@endphp

@section('content')
    <div class="content-body min-height">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-header">
                        @if(hasAccessAbility('add_ads', $roles))
                            <a href="{{ route('web.ads.create') }}" class="btn btn-primary btn-sm">Add New</a>
                        @endif
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                <li><a data-action="close"><i class="ft-x"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row  mb-2">
                                <div class="col-12">

                                    <div class="table-responsive ">
                                        <table class="table table-striped table-bordered table-sm text-center" {{--id="process_data_table"--}}>
                                            <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Position</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Photo Count</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($data['rows']) && count($data['rows']) > 0 )
                                                    @foreach( $data['rows'] as $key => $row )
                                                        <tr>
                                                            <td>{{ $key+1 }}</td>
                                                            <td>{{ $row->position->NAME ?? '' }}</td>
                                                            <td>{{ date('d-m-Y', strtotime($row->AVAILABLE_FROM)) }}</td>
                                                            <td>{{ date('d-m-Y', strtotime($row->AVAILABLE_TO)) }}</td>
                                                            <td>{{ $row->images->count() }}</td>
                                                            <td>{{ $row->STATUS ? 'Published' : 'Pending' }}</td>
                                                            <td>
                                                                @if(hasAccessAbility('view_ads_image', $roles))
                                                                    <a class="btn btn-sm btn-success text-white"
                                                                       href="{{ route('web.ads.image', $row->PK_NO) }}"
                                                                       title="Images">
                                                                        <i class="la la-image"></i>
                                                                    </a>
                                                                @endif
                                                                @if(hasAccessAbility('edit_ads', $roles))
                                                                    <a class="btn btn-sm btn-warning text-white"
                                                                       href="{{ route('web.ads.edit', $row->PK_NO) }}"
                                                                       title="Edit">
                                                                        <i class="la la-edit"></i>
                                                                    </a>
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
            </div>
        </div>
    </div>
@endsection
