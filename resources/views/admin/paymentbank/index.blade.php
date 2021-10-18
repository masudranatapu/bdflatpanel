@extends('admin.layout.master')
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush
@section('System Settings','open')
@section('payment_account','active')

@section('title')
    Payment account
@endsection

@section('page-name')
    Payment account
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('payment.breadcrumb_title')</a></li>
    <li class="breadcrumb-item active">Payment account</li>
@endsection

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
                                @if(hasAccessAbility('new_account_source', $roles))
                                    <a class="text-white addsourceModal btn btn-round btn-sm btn-primary" title="Add new" href="{{ route('admin.payment_acc.create') }}" ><i class="ft-plus text-white"></i> Add New Account</a>
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
                                    <table class="table table-striped table-bordered alt-pagination table-sm" id="indextable">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;"  class="text-center">Sl.</th>
                                                <th>Method</th>
                                                <th  style="">Bank Name</th>
                                                <th  style="">Account Name</th>
                                                <th >Account No</th>
                                                <th>Balance Actual</th>
                                                <th style="width: 50px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rows as $key => $row)
                                                <tr>
                                                    <td  class="text-center">{{ $key+1 }}</td>
                                                    <td>
                                                        {{ $row->method->NAME ?? '' }}
                                                    </td>
                                                    <td>{{ $row->BANK_NAME }}</td>
                                                    <td>{{ $row->BANK_ACC_NAME }}</td>
                                                    <td>{{ $row->BANK_ACC_NO }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($row->BALANCE_ACTUAL,2) }}
                                                    </td>

                                                    <td class="text-center">
                                                        <a href="{{ route('admin.payment_acc.edit',['id' => $row->PK_NO]) }}">Edit</a>
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


    <!--/ Alternative pagination table -->
@endsection
@push('custom_js')



@endpush('custom_js')
