@extends('admin.layout.master')

@section('Product Management','open')
@section('floor_list','active')

@section('title') Floor List @endsection
@section('page-name') Floor List @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Floor List</li>
@endsection

@php
    $roles = userRolePermissionArray()
@endphp
@push('custom_css')
    <link rel="stylesheet" type="text/css"
          href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
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
                <div class="col-6">
                    <div class="card card-sm card-success">
                        <div class="card-header pl-2">
                            <div class="form-group">
                                @if(hasAccessAbility('new_floor', $roles))
                                    <a class="text-white btn btn-sm btn-primary"
                                       href="{{ route('admin.property.floor.create')}}" title="Create new floors"><i
                                            class="ft-plus text-white"></i> Create Floor</a>
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
                            <div class="card-body card-dashboard">
                                <div class="table-responsive">
                                    <table
                                        class="table table-striped table-bordered text-center alt-pagination"
                                        id="indextable">
                                        <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;">SL.</th>
                                            <th class="" style="min-width: 100px;">Name</th>
                                            <th class="" style="">Order</th>
                                            <th class="" style="">Status</th>
                                            <th class="text-center" style="width: 200px;">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(isset($data['floors']) && count($data['floors']))
                                            @foreach($data['floors'] as $key => $floors)
                                                <tr>
                                                    <td class="text-center"
                                                        style="width: 50px;">{{ $key + 1 }}</td>
                                                    <td class="" style="">{{ $floors->NAME }}</td>
                                                    <td class="" style="">{{ $floors->ORDER_ID }}</td>
                                                    @if($floors->IS_ACTIVE)
                                                        <td class="text-success">Active</td>
                                                    @else
                                                        <td class="text-danger">Inactive</td>
                                                    @endif
                                                    <td class="text-center" style="width: 200px;">
                                                        @if(hasAccessAbility('edit_property_floor', $roles))
                                                            <a href="{{ route('admin.property.floor.edit', [$floors->PK_NO]) }}"
                                                               title="EDIT" class="btn btn-xs btn-info  mb-1"><i
                                                                    class="la la-edit"></i></a>
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
        </section>
    </div>


    @include('admin.category._subcategory_add_edit_modal')

@endsection


@push('custom_js')

    <!--script only for brand page-->
    <script type="text/javascript" src="{{ asset('app-assets/pages/category.js')}}"></script>


@endpush('custom_js')
