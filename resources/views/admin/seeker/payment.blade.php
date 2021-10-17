@extends('admin.layout.master')

@section('Seeker Management','open')
@section('seeker_list','active')

@section('title') Payment @endsection
@section('page-name') Payment @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('agent.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active">Payment</li>
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
    $roles      = userRolePermissionArray();
    $txn_type   =  Config::get('static_array.txn_type');
    $balance    = 0;
@endphp


@section('content')
    <div class="content-body min-height">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row  mb-2">
                                <div class="col-12">
                                    <div class="row mb-1">
                                        <div class="col-2">
                                            <p class="font-weight-bold">Balance</p>
                                            <h2 class="font-weight-bold text-success">BDT {{ number_format($data['seeker']->UNUSED_TOPUP ?? 0,2) }}</h2>
                                        </div>
                                        <div class="col-2 offset-8 text-right" style="padding-top: 10px">
                                            <a href="{{ route('admin.seeker.recharge', request()->route('id')) }}"
                                               class="btn btn-success">Recharge Balance</a>
                                        </div>
                                    </div>

                                    <h3>Transaction History</h3>
                                    <div class="table-responsive ">
                                        <table
                                            class="table table-striped table-bordered table-sm text-center" {{--id="process_data_table"--}}>
                                            <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Tran. ID</th>
                                                <th>Tran. Type</th>
                                                <th>Date</th>
                                                <th>Slip</th>
                                                <th>Note</th>
                                                <th>Amount</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($data['rows']) && count($data['rows']))
                                                @foreach($data['rows'] as $key => $row)
                                                    <tr>
                                                        <td>
                                                            <span>{{ $key + 1 }}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{ $row->CODE }}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{ $row->payment->PAYMENT_TYPE == 2 ? 'Bonus Payment' : 'Customer Payment' }}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{ $row->TRANSACTION_DATE }}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{ $row->payment->SLIP_NUMBER }}</span>
                                                        </td>

                                                        <td>
                                                            <span>{{ $row->payment->PAYMENT_NOTE }}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{ number_format($row->AMOUNT, 2) }}</span>
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


    <div class="modal fade text-left" id="recharge" tabindex="-1" role="dialog" aria-labelledby="category_name"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="category_name">Recharge Balance</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate' , 'id' => 'subcat_add_edit_frm' ]) !!}
                @csrf

                <div class="modal-body p-5">
                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                        <label>Amount<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::text('name', null, [ 'class' => 'form-control mb-1 subcat_name', 'data-validation-required-message' => 'This field is required', 'placeholder' => '0.00', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>

                    <div class="form-group {!! $errors->has('name') ? 'error' : '' !!}">
                        <label>Note<span class="text-danger">*</span></label>
                        <div class="controls">
                            {!! Form::textarea('name', null, [ 'class' => 'form-control mb-1 subcat_name', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Type Note', 'tabindex' => 1 ]) !!}
                            {!! $errors->first('name', '<label class="help-block text-danger">:message</label>') !!}
                        </div>
                    </div>

                    <div class="form-group text-center">
                        <input type="submit" class="btn btn-success submit-btn" value="Continue" title="Continue">
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
