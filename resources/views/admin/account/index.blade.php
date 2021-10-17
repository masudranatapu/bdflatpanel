@extends('admin.layout.master')
@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
@endpush

@section('Payment Management','active')
@section('Accounts','open')

@section('title')
    @lang('payment.list_page_title')
@endsection
@section('page-name')
    @lang('payment.list_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('payment.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('payment.breadcrumb_sub_title')
    </li>
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
                    <div class="card card-success">
                        <div class="card-header">
                            <div class="form-group">
                                @if(hasAccessAbility('new_account_source', $roles))
                                    <a class="text-white addsourceModal" href="javascript:void(0)" data-toggle="modal" data-target="#addSourceModal"
                                       title="Add Payment Source" data-url="{{ route('account.store')}}" data-type="add">
                                        <button type="button" class="btn btn-sm btn-primary">
                                            <i class="ft-plus text-white"></i> Add Banks
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
                                            <th style="width: 40px;">Sl.</th>
                                            <th class="text-left" style="width: 420px;">Bank Name</th>
                                            <th class="text-left" style="max-width: 420px;">Bank Account Name</th>
                                            <th class="text-left">Bank Acc No.</th>
                                            <th style="width: 200px;">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($rows as $row)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td class="text-left">
                                                    <span title="Account Bank Name">{{ $row->BANK_NAME }}</span>
                                                </td>
                                                <td class="text-left" style="max-width: 200px;">
                                                    <span title="Account Bank Name">{{ $row->BANK_ACC_NAME }}</span>
                                                </td>
                                                <td class="text-left" style="max-width: 200px;">
                                                    <span title="Account Bank Name">{{ $row->BANK_ACC_NO }}</span>
                                                </td>
                                                <td style="width: 200px;">
                                                    @if(hasAccessAbility('edit_account_source', $roles))
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-info mr-0 mb-1 editSourceModal" data-toggle="modal"
                                                           data-target="#editSourceModal" title="Edit Payment Source"
                                                           data-url="{{ route('account.source.update', [$row->PK_NO]) }}" data-id="{{$row->PK_NO}}"
                                                           data-bank_name="{{$row->BANK_NAME}}" data-bank_acc_name="{{$row->BANK_ACC_NAME}}" data-bank_acc_no="{{$row->BANK_ACC_NO}}" data-type="edit"><i class="la la-edit"></i></a>
                                                    @endif
                                                    {{--@if(hasAccessAbility('delete_account_source', $roles))
                                                        <a href="{{ route('account.source.delete', [$row->PK_NO]) }}"
                                                           onclick="return confirm('Are you sure you want to delete?')" class="btn btn-xs btn-danger mr-0 mb-1"
                                                           title="Delete Payment Source"><i class="la la-trash"></i>
                                                        </a>
                                                    @endif
                                                    @if(hasAccessAbility('new_account_name', $roles))
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-success mr-0 mb-1 addBankModal" title="ADD ACCOUNT NAME"
                                                           data-toggle="modal" data-target="#EditAccountname" data-url="{{ route('account.bank.store.single') }}"
                                                           data-source_id="{{$row->PK_NO}}" data-source_name="{{$row->NAME}}" data-type="add">&nbsp;+ A&nbsp;</a>
                                                    @endif
                                                    @if(hasAccessAbility('new_payment_method', $roles))
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-warning mr-0 mb-1 addMethodModal" title="ADD METHOD"
                                                           data-toggle="modal" data-target="#addEditMethodModal" data-url="{{ route('account.method.store') }}"
                                                           data-source_id="{{$row->PK_NO}}" data-source_name="{{$row->NAME}}" data-type="add">&nbsp;+ P&nbsp;</a>
                                                    @endif--}}
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


    @include('admin.account._account_edit_modal')

    <!--/ Alternative pagination table -->
@endsection
@push('custom_js')

    <!--script only for brand page-->
    <script type="text/javascript" src="{{ asset('app-assets/pages/account.js')}}"></script>


@endpush('custom_js')
