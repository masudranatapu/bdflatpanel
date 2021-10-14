@extends('admin.layout.master')

@section('Payment','open')
@section('transaction_list','active')

@section('title') Transaction @endsection
@section('page-name') Transaction @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('agent.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active">Transaction</li>
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
    $transaction_type = Config::get('static_array.transaction_type');
@endphp

@section('content')
    <div class="content-body min-height">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-header">
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
                                {!! Form::open(['route' => 'admin.transaction.list', 'method' => 'get']) !!}
                                <div class="col-12">
                                    <div class="row form-group">
                                        <div class="col-md-2">
                                            {!! Form::label('transaction_type', 'Transaction Type', ['class' => 'lable-title']) !!}
                                        </div>
                                        <div class="col-md-10">
                                            <div class="controls">
                                                {!! Form::radio('transaction_type','all', request()->query('transaction_type') == 'all' || null == request()->query('transaction_type'),[ 'id' => 'all']) !!}
                                                {{ Form::label('all','All') }}
                                                &emsp;
                                                {!! Form::radio('transaction_type','listing_ad', request()->query('transaction_type') == 'listing_ad',[ 'id' => 'listing_ad']) !!}
                                                {{ Form::label('listing_ad','Listing Ad') }}
                                                &emsp;
                                                {!! Form::radio('transaction_type','lead_purchase', request()->query('transaction_type') == 'lead_purchase',[ 'id' => 'lead_purchase']) !!}
                                                {{ Form::label('lead_purchase','Lead Purchase') }}
                                                &emsp;
                                                {!! Form::radio('transaction_type','contact_view', request()->query('transaction_type') == 'contact_view',[ 'id' => 'contact_view']) !!}
                                                {{ Form::label('contact_view','Contact View') }}
                                                &emsp;
                                                {!! Form::radio('transaction_type','recharge', request()->query('transaction_type') == 'recharge',[ 'id' => 'recharge']) !!}
                                                {{ Form::label('recharge','Recharge') }}
                                                &emsp;
                                                {!! Form::radio('transaction_type','commission', request()->query('transaction_type') == 'commission',[ 'id' => 'commission']) !!}
                                                {{ Form::label('commission','Commission') }}
                                                &emsp;
                                                {!! Form::radio('transaction_type','refund', request()->query('transaction_type') == 'refund',[ 'id' => 'refund']) !!}
                                                {{ Form::label('refund','Refund') }}

                                                {!! $errors->first('transaction_type', '<label class="help-block text-danger">:message</label>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group" style="align-items: center">
                                        <div class="col-md-2">Search by Date:</div>
                                        <div class="col-md-10">
                                            <div class="row" style="align-items: center">
                                                <div class="col-md-3">
                                                    {!! Form::date('from_date', request()->query->get('from_date'), ['class' => 'form-control']) !!}
                                                </div>
                                                <div class="col-md-1 text-center">
                                                    <p>To</p>
                                                </div>
                                                <div class="col-md-3">
                                                    {!! Form::date('to_date', request()->query->get('to_date'), ['class' => 'form-control']) !!}
                                                </div>
                                                <div class="col-md-3">
                                                    {!! Form::submit('Search', ['class' => 'btn btn-success']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                                <div class="col-12">
                                    <table class="table table-striped table-bordered text-center">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>User ID</th>
                                            <th>TID</th>
                                            <th>Date</th>
                                            <th>Transaction Type</th>
                                            <th>Note</th>
                                            <th>Amount</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(isset($data['rows']) && count($data['rows']))
                                            @foreach($data['rows'] as $key =>  $row)
                                                <tr>
                                                    <td>{{ $key+1 }}</td>
                                                    <td>{{ $row->CUSTOMER_NO }}</td>
                                                    <td>{{ $row->CODE }}</td>
                                                    <td>{{ date('M d, Y', strtotime($row->TRANSACTION_DATE)) }}</td>
                                                    <td>{{ $transaction_type[$row->TRANSACTION_TYPE] ?? '' }}</td>
                                                    <td>{{ $row->PAYMENT_NOTE ?? '' }}</td>
                                                    <td>{{ number_format($row->AMOUNT, 2) }}</td>
                                                    <td>
                                                        <a href="#">Edit</a> |
                                                        <a href="#">Delete</a>
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
@endsection
