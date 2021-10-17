@extends('admin.layout.master')

@section('Testimonial','active')

@section('title') Testimonial @endsection
@section('page-name') Testimonial @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('agent.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active">Testimonial</li>
@endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{asset('/custom/css/custom.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@push('custom_js')

    <!-- BEGIN: Data Table-->
    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
    <!-- END: Data Table-->
@endpush

@php
    $roles = userRolePermissionArray();
    $testimonials  = $data['testimonials'] ?? [];
@endphp

@section('content')
    <div class="content-body min-height">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-header">
                        <a href="{{ route('web.testimonial.create') }}" class="btn btn-primary btn-sm">Add New</a>
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
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-striped table-bordered text-center">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Designation</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php($i=1)
                                        @if(isset($testimonials) && count($testimonials))
                                            @foreach($testimonials as $item)
                                                <tr>
                                                    <td>
                                                        {{$i++}}
                                                    </td>
                                                    <td>
                                                        <img width="80" src="{{asset($item->IMAGE)}}" alt="">
                                                    </td>
                                                    <td>{{ $item->NAME }}</td>
                                                    <td>{{ $item->DESIGNATION }}</td>
                                                    <td>{{ $item->DESCRIPTION }}</td>
                                                    <td>
                                                        @if($item->IS_ACTIVE == 1 )
                                                            <span class='text-success'>Active</span>
                                                        @else
                                                            <span class='text-danger'>Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{route('web.testimonial.edit',$item->PK_NO)}}">Edit</a> |
                                                        <a onclick="return confirm('Are You Sure To Delete This')" href="{{route('web.testimonial.delete',$item->PK_NO)}}">Delete</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7" class="text-danger text-center font-weight-bold">No Data Found!</td>
                                            </tr>
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
@endsection
