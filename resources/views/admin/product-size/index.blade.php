@extends('admin.layout.master')
@section('product size','active')
@section('title')
Product Size | Create
@endsection
@section('page-name')
Product Size
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
</li>
<li class="breadcrumb-item active">Product Size
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
    $roles = userRolePermissionArray()
@endphp
@section('content')
<!-- Alternative pagination table -->
<div class="content-body min-height">
    <section id="pagination">
        <div class="row">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <div class="form-group">
                            @if(hasAccessAbility('new_size', $roles))
                            <a class="text-white" href="{{ route('admin.product-size.new') }}">
                                <button type="button" class="btn btn-round btn-sm btn-primary">
                                    <i class="ft-user-plus"></i> Create Product Size
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
                                            <th>Sl.</th>
                                            <th>Size Code</th>
                                            <th>Size Name</th>
                                            <th>Brand</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($size as $row)
                                            <tr>
                                                <td>{{$loop->index+1}}</td>
                                                <td>{{$row->NAME}}</td>
                                                <td>{{$row->CODE}}</td>
                                              <td>{{$row->brand->NAME ?? ''}}</td>

                                                <td>
                                                    @if(hasAccessAbility('edit_size', $roles))
                                                    <a href="{{ route('admin.product-size.edit', [$row->PK_NO]) }}" class="btn btn-xs btn-primary mr-1"><i class="la la-edit"></i></a>
                                                    @endif
                                                    @if(hasAccessAbility('delete_size', $roles))
                                                    <a href="{{ route('admin.product-size.delete', [$row->PK_NO]) }}" class="btn btn-xs btn-danger mr-1"  onclick="return confirm('Are you sure you want to delete?')"><i class="la la-trash"></i></a>
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
