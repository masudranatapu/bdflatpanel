@extends('admin.layout.master')

{{--@section('Earnings','open')--}}
@section('listing_price','active')

@section('title') Listing Price @endsection
@section('page-name') Listing Price @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('agent.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active">Listing Price</li>
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
    $roles = userRolePermissionArray();
    $data = $data ?? [];
    $data2 = $data2 ?? [];
@endphp


@section('content')
    <div class="card card-success min-height">
        <div class="card-header">
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
            <div class="card-body">
                {!! Form::open([ 'route' => 'admin.listing_price.update', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                <div class="form-body">
                    @foreach($data as $key => $item)
                        @php
                            $price = $item->listingPrice;
                        @endphp
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {!! $errors->has('gl_sale_price') ? 'error' : '' !!}">
                                    <div class="controls">
                                        {!! Form::text('gl_sale_name'.$key, $item->NAME,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('gl_sale_price', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Listing For Sale (BDT):</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group {!! $errors->has('gl_sale_price') ? 'error' : '' !!}">
                                    <div class="controls">
                                        {!! Form::text('gl_sale_price'.$key, $price->SELL_PRICE,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('gl_sale_price', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Listing For Rent (BDT):</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group {!! $errors->has('gl_rent_price') ? 'error' : '' !!}">
                                    <div class="controls">
                                        {!! Form::text('gl_rent_price'.$key, $price->RENT_PRICE,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('gl_rent_price', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Listing For Roommate (BDT):</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group {!! $errors->has('gl_roommate_price') ? 'error' : '' !!}">
                                    <div class="controls">
                                        {!! Form::text('gl_roommate_price'.$key, $price->ROOMMAT_PRICE,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('gl_roommate_price', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Duration (Days):</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group {!! $errors->has('gl_duration') ? 'error' : '' !!}">
                                    <div class="controls">
                                        {!! Form::text('gl_duration'.$key, $item->DURATION,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                        {!! $errors->first('gl_duration', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-actions mt-10 mb-3 text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="la la-check-square-o"></i> Save
                </button>
            </div>
            {!! Form::close() !!}
        </div>


        <div class="card-content collapse show">
            <div class="card-body">
                {!! Form::open([ 'route' => 'admin.listing_lead_price.update', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                <div class="form-body">
                    <h2 class="mb-2 mt-2">Agent Properties Contact View</h2>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Listing For Sale:</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group {!! $errors->has('apv_sale_price') ? 'error' : '' !!}">
                                <div class="controls">
                                    {!! Form::text('apv_sale_price', $data2->AGENT_PROP_VIEW_SALES_PRICE ?? 0,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                    {!! $errors->first('apv_sale_price', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Listing For Rent:</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group {!! $errors->has('apv_rent_price') ? 'error' : '' !!}">
                                <div class="controls">
                                    {!! Form::text('apv_rent_price', $data2->AGENT_PROP_VIEW_RENT_PRICE ?? 0,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                    {!! $errors->first('apv_rent_price', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Listing For Roommate:</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group {!! $errors->has('apv_roommate_price') ? 'error' : '' !!}">
                                <div class="controls">
                                    {!! Form::text('apv_roommate_price', $data2->AGENT_PROP_VIEW_ROOMMATE_PRICE ?? 0,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                    {!! $errors->first('apv_roommate_price', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <h2 class="mb-2 mt-2">Agent Commission</h2>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Listing For Sale:</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group {!! $errors->has('ac_sale_price') ? 'error' : '' !!}">
                                <div class="controls">
                                    {!! Form::text('ac_sale_price', $data2->AGENT_COMM_SALES_PRICE ?? 0,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                    {!! $errors->first('ac_sale_price', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Listing For Rent:</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group {!! $errors->has('ac_rent_price') ? 'error' : '' !!}">
                                <div class="controls">
                                    {!! Form::text('ac_rent_price', $data2->AGENT_COMM_RENT_PRICE ?? 0,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                    {!! $errors->first('ac_rent_price', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Listing For Roommate:</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group {!! $errors->has('ac_roommate_price') ? 'error' : '' !!}">
                                <div class="controls">
                                    {!! Form::text('ac_roommate_price', $data2->AGENT_COMM_ROOMMATE_PRICE ?? 0,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                    {!! $errors->first('ac_roommate_price', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>


                    <h2 class="mb-2 mt-2">Lead View</h2>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Listing For Sale:</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group {!! $errors->has('lv_sale_price') ? 'error' : '' !!}">
                                <div class="controls">
                                    {!! Form::text('lv_sale_price', $data2->LEAD_VIEW_SALES_PRICE ?? 0,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                    {!! $errors->first('lv_sale_price', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Listing For Rent:</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group {!! $errors->has('lv_rent_price') ? 'error' : '' !!}">
                                <div class="controls">
                                    {!! Form::text('lv_rent_price', $data2->LEAD_VIEW_RENT_PRICE ?? 0,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                    {!! $errors->first('lv_rent_price', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Listing For Roommate:</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group {!! $errors->has('lv_roommate_price') ? 'error' : '' !!}">
                                <div class="controls">
                                    {!! Form::text('lv_roommate_price', $data2->LEAD_VIEW_ROOMMATE_PRICE ?? 0 ,[ 'class' => 'form-control mb-1', 'placeholder' => 'Price', 'tabindex' => 1 ]) !!}
                                    {!! $errors->first('lv_roommate_price', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions mt-10 mb-3 text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="la la-check-square-o"></i> Save
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection
