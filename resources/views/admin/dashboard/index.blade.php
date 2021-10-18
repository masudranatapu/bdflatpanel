@extends('admin.layout.master')
@section('dashboard','active')
@section('title')
Dashboard
@endsection
@section('page-name')
Dashboard
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item active">Home
    </li>
@endsection

@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/css/colors.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
<style>
    #dashboard_notepad {
        font-weight: bold;
    }
</style>
@endpush
@php
    $roles = userRolePermissionArray();
@endphp
@section('content')
    <div class="content-body min-height">
        <!-- eCommerce statistic -->
        {{-- <div class="row mb-1"> --}}
            <div class="row">
                @if(hasAccessAbility('view_dashboard_cards_sales_agent', $roles) || hasAccessAbility('view_dashboard_cards_my_manager', $roles))
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-12 mb-1">
                            <div class="pull-up">
                                <div class="card-content">
                                    <div class="card-body bg-success">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <h6 class="text-white">Customers / Reseller</h6>
                                                <h3 class="text-white">{{ $data['customer_count'] }} / {{ $data['reseller_count']  }}</h3>
                                            </div>
                                            <div>
                                                <i class="icon-basket-loaded font-large-2 float-right text-white"></i>
                                            </div>
                                        </div>
                                        {{-- <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                            <div class="progress-bar bg-gradient-x-info" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-xl-3 col-lg-6 col-12 mb-1">
                            <div class="pull-up">
                                <div class="card-content">
                                    <div class="card-body bg-success">
                                        <div class="media d-flex">
                                            <div class="media-body text-left text-white">
                                                <h6 class="text-white">Total Reseller</h6>
                                                <h3 class="text-white">{{ $data['reseller_count']  }}</h3>
                                            </div>
                                            <div>
                                                <i class="icon-pie-chart font-large-2 float-right text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <div class="col-xl-6 col-lg-6 col-12 mb-1">
                            <div class="pull-up">
                                <div class="card-content">
                                    <div class="card-body bg-success">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <h6 class="text-white">Master / Variant - Brand / Model</h6>
                                                <h3 class="text-white">{{ $data['product_master']  }} / {{ $data['product_variant']  }} - {{ $data['product_brand']  }} / {{ $data['product_model']  }}</h3>
                                            </div>
                                            <div>
                                                <i class="icon-user-follow font-large-2 float-right text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-12 mb-1">
                            <div class="pull-up">
                                <div class="card-content">
                                    <div class="card-body bg-blue-grey">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <h6 class="text-white">Order today / week / month</h6>

                                                <h5 class="text-white">{{ $data['order_today'] }} / {{ $data['order_last7days'] }} / {{ $data['order_last30days'] }} Qty</h5>
                                                <h5 class="text-white">{{ number_format($data['order_RM_value_today'],2) }} / {{ number_format($data['order_RM_value_7day'],2) }} / {{ number_format($data['order_RM_value_30day'],2) }} RM</h5>

                                            </div>
                                            <div>
                                                <i class="icon-user-follow font-large-2 float-right text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-12 mb-1">
                            <div class="pull-up">
                                <div class="card-content">
                                    <div class="card-body bg-blue-grey">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <h6 class="text-white">Dispatch today / week / month</h6>
                                                <h5 class="text-white">{{ $data['order_dispatch_today'] }} / {{ $data['order_dispatch_last7days'] }} / {{ $data['order_dispatch_last30days'] }}</h5>
                                                <h5 class="text-white">{{ $data['order_dispatch_RM_value_today'] }} / {{ $data['order_dispatch_RM_value_7day'] }} / {{ $data['order_dispatch_RM_value_30day'] }} RM</h5>

                                            </div>
                                            <div>
                                                <i class="icon-user-follow font-large-2 float-right text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-12 mb-1">
                            <div class="pull-up">
                                <div class="card-content">
                                    <div class="card-body bg-cyan" style="min-height: 118px;">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <h6 class="text-white">COD/RTC - RTS</h6>
                                                <h5 class="text-white">{{ $data['cod_rtc_today'] }} - {{ $data['rts_today'] }}</h5>

                                            </div>
                                            <div>
                                                <i class="icon-user-follow font-large-2 float-right text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-12 mb-1">
                            <div class="pull-up">
                                <div class="card-content">
                                    <div class="card-body bg-cyan">
                                        <div class="media d-flex">
                                            <div class="media-body text-left">
                                                <h6 class="text-white">SMS today / week / month</h6>
                                                <h5 class="text-white">{{ $data['arrival_msg_sent_today'] }} / {{ $data['arrival_msg_sent_last7days'] }} / {{ $data['arrival_msg_sent_last30days'] }} Arrival</h5>
                                                <h5 class="text-white">{{ $data['dispatch_msg_sent_today'] }} / {{ $data['dispatch_msg_sent_last7days'] }} / {{ $data['dispatch_msg_sent_last30days'] }} Dispatch</h5>

                                            </div>
                                            <div>
                                                <i class="icon-user-follow font-large-2 float-right text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                {{-- <div class="col-xl-3 col-lg-6 col-12 mb-1">
                    <div class="pull-up">
                        <div class="card-content">
                            <div class="card-body bg-success">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h6 class="text-white">Total Brand / Model</h6>
                                        <h3 class="text-white">{{ $data['product_brand']  }} / {{ $data['product_model']  }}</h3>
                                    </div>
                                    <div>
                                        <i class="icon-heart font-large-2 float-right text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="card pull-up mb-1">
                                <div class="card-header bg-hexagons">
                                    <h4 class="card-title"><strong>NOTICE</strong></h4>
                                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                                    {{-- <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                        </ul>
                                    </div> --}}
                                </div>
                                <div class="card-content collapse show bg-hexagons">
                                    <div class="card-body pt-0">
                                        {!! Form::textarea('dashboard_notepad', $data['sticky_note']->NOTE ?? null, [ 'class' => 'form-control', 'id' => 'dashboard_notepad', 'placeholder' => 'Enter note', 'tabindex' => 16, 'rows' => 13 , $data['role_id'] != 1 ? 'readonly' : '']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="card pull-up mb-1">
                                <div class="card-content collapse show bg-gradient-directional-danger ">
                                    <div class="card-body bg-hexagons-danger">
                                        <h4 class="card-title white">Deals <span class="white">-55%</span> <span class="float-right"><span class="white">152</span><span class="red lighten-4">/200</span></span>
                                        </h4>
                                        <div class="chartjs">
                                            <canvas id="deals-doughnut" height="255"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {{-- </div>-------------------------------------------- --}}
        <!--/ eCommerce statistic -->
        <!-- Revenue, Hit Rate & Deals -->
        {{-- <div class="row">------------------------------- --}}
            {{-- @if(hasAccessAbility('view_dashboard_cards_sales_agent', $roles) || hasAccessAbility('view_dashboard_cards_my_manager', $roles)) --}}
            @if(hasAccessAbility('view_dashboard_cards_uk_manager', $roles))
                <div class="row mb-1">
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="pull-up">
                            <div class="card-content">
                                <div class="card-body bg-blue">
                                    <div class="media d-flex">
                                        <div class="media-body text-left">
                                            <h6 class="text-white">Purchase today / week / month</h6>
                                            <h5 class="text-white">{{ $data['purchase_qty_today'] }} / {{ $data['purchase_qty_last7days'] }} / {{ $data['purchase_qty_last30days'] }} Qty</h5>
                                            <h5 class="text-white">{{ number_format($data['purchase_val_today'],2) }} / {{ number_format($data['purchase_val_last7days'],2) }} / {{ number_format($data['purchase_val_last30days'],2) }} GBP</h5>

                                        </div>
                                        <div>
                                            <i class="icon-basket-loaded font-large-2 float-right text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="pull-up">
                            <div class="card-content">
                                <div class="card-body bg-blue" style="min-height: 118px;">
                                    <div class="media d-flex">
                                        <div class="media-body text-left">
                                            <h6 class="text-white">Vat Claim Pending</h6>
                                            <h3 class="text-white">{{ number_format($data['purchase_yet_to_claim_vat'],2) }} GBP</h3>
                                        </div>
                                        <div>
                                            <i class="icon-user-follow font-large-2 float-right text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="pull-up">
                            <div class="card-content">
                                <div class="card-body bg-blue" style="min-height: 118px;">
                                    <div class="media d-flex">
                                        <div class="media-body text-left">
                                            <h6 class="text-white">Shipment / Box / Product </h6>
                                            <h5 class="text-white">{{ $data['shipment_in_vessel'] }} / {{ $data['shipment_in_vessel_box_count'] }} / {{ $data['shipment_in_vessel_product_count'] }} (Vessel)
                                            </h5>
                                            <h5 class="text-white">{{ $data['shipment_not_in_vessel'] }} / {{ $data['shipment_not_in_vessel_box_count'] }} / {{ $data['shipment_not_in_vessel_product_count'] }} (Origin)</h5>
                                        </div>
                                        <div>
                                            <i class="icon-heart font-large-2 float-right text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-12">
                        <div class="pull-up">
                            <div class="card-content">
                                <div class="card-body bg-blue" style="min-height: 118px;">
                                    <div class="media d-flex">
                                        <div class="media-body text-left">
                                            <h6 class="text-white">Box Not Assigned(Product) / Yet to Box</h6>
                                            <h3 class="text-white">{{ $data['box_not_assigned'] }} ({{ $data['box_not_assigned_prd_qty'] }}) / {{ $data['yet_to_box'] }}</h3>
                                        </div>
                                        <div>
                                            <i class="icon-heart font-large-2 float-right text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-xl-6 col-12">
                {{-- <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="card pull-up mb-1">
                            <div class="card-header bg-hexagons">
                                <h4 class="card-title">NOTE PAD</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                            </div>
                            <div class="card-content collapse show bg-hexagons">
                                <div class="card-body pt-0">
                                    {!! Form::textarea('dashboard_notepad', $data['sticky_note']->NOTE ?? null, [ 'class' => 'form-control', 'id' => 'dashboard_notepad', 'placeholder' => 'Enter note', 'tabindex' => 16, 'rows' => 13 , $data['role_id'] != 1 ? 'readonly' : '']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="card pull-up mb-1">
                            <div class="card-content collapse show bg-gradient-directional-danger ">
                                <div class="card-body bg-hexagons-danger">
                                    <h4 class="card-title white">Deals <span class="white">-55%</span> <span class="float-right"><span class="white">152</span><span class="red lighten-4">/200</span></span>
                                    </h4>
                                    <div class="chartjs">
                                        <canvas id="deals-doughnut" height="255"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        {{-- </div>----------------------------------------- --}}
        <!--/ Revenue, Hit Rate & Deals -->
        <!--  -->
        {{-- @if(hasAccessAbility('view_dashboard_cards_uk_manager', $roles)) --}}
        {{-- <div class="row mb-1">-------------------------- --}}

            {{-- <div class="col-xl-3 col-lg-6 col-12 mb-1">
                <div class="pull-up">
                    <div class="card-content">
                        <div class="card-body bg-blue">
                            <div class="media d-flex">
                                <div class="media-body text-left text-white">
                                    <h6 class="text-white">Purchase Value (GBP) today / 7 Days / 30 days</h6>
                                    <h3 class="text-white">{{ number_format($data['purchase_val_today'],2) }} / {{ number_format($data['purchase_val_last7days'],2) }} / {{ number_format($data['purchase_val_last30days'],2) }}</h3>
                                </div>
                                <div>
                                    <i class="icon-pie-chart font-large-2 float-right text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}


        {{-- </div>------------------------------------------------------- --}}
        {{-- @endif --}}
        @if($data['role_id'] == 1)
        {{-- <div class="row mb-1">------------------------------------------------------- --}}
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12 mb-1">
                    <div class="pull-up">
                        <div class="card-content">
                            <div class="card-body bg-teal">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h6 class="text-white">Free Stock Qty ({{ $data['free_stock_qty'] }})</h6>
                                        <h5 class="text-white">{{ number_format($data['free_stock_purchase_price_rm'],2) }} RM (P)</h5>
                                        <h5 class="text-white">{{ number_format($data['free_stock_salses_price_rm'],2) }} RM (S)</h5>
                                    </div>
                                    <div>
                                        <i class="icon-basket-loaded font-large-2 float-right text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12 mb-1">
                    <div class="pull-up">
                        <div class="card-content">
                            <div class="card-body bg-teal">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h6 class="text-white">Verified Deposit / Unverified / Due</h6>
                                        <h5 class="text-white">
                                            @php
                                                $due = $data['booked_not_dispatched_price_rm'] - $data['sales_pipeline_varified_deposit'] - $data['payment_verfication_pending'];
                                            @endphp
                                            {{ number_format($data['sales_pipeline_varified_deposit'],2) }} / {{ number_format($data['payment_verfication_pending'],2) }} / {{ number_format($due,2) }} </h5>

                                        <h5 class="text-white">{{ number_format($data['booked_not_dispatched_price_rm'],2) }} RM (Exisiting Order)</h5>
                                    </div>
                                    <div>
                                        <i class="icon-heart font-large-2 float-right text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12 mb-1">
                    <div class="pull-up">
                        <div class="card-content">
                            <div class="card-body bg-teal" style="min-height: 118px;">
                                <div class="media d-flex">
                                    <div class="media-body text-left">
                                        <h6 class="text-white">Customer Free Credit</h6>
                                        <h3 class="text-white">{{ number_format($data['customer_free_credit'],2) }} RM</h3>
                                    </div>
                                    <div>
                                        <i class="icon-basket-loaded font-large-2 float-right text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12 mb-1">
                    <div class="pull-up">
                        <div class="card-content">
                            <div class="card-body bg-teal" style="min-height: 118px;">
                                <div class="media d-flex">
                                    <div class="media-body text-left text-white">
                                        <h6 class="text-white">COD</h6>
                                        <h5 class="text-white">{{ number_format($data['cod_balance'],2) }} RM</h5>
                                    </div>
                                    <div>
                                        <i class="icon-pie-chart font-large-2 float-right text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {{-- </div>------------------------
        <div class="row mb-1">---------------------- --}}

        {{-- </div>-------------------------------
        <div class="row mb-1">----------------------------- --}}
            <div class="row">
            <div class="col-xl-6 col-lg-6 col-12 mb-1">
                <div class="pull-up">
                    <div class="card-content">
                        <div class="card-body bg-cyan">
                            <div class="row">
                                <div class="col-3">
                                    <h4 class="text-white">Top Agent</h4>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white">Today</h4>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white">Last 7 day</h4>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white">Last 30 days/{{ \Carbon\Carbon::now()->format('M') }}</h4>
                                </div>
                            </div>
                            <hr>
                            @foreach ($data['top_agent'] as $item)
                            <div class="row">
                                <div class="col-3">
                                    <h6 class="text-white">{{ $item->BOOKING_SALES_AGENT_NAME }}</h6>
                                </div>
                                <div class="col-3">
                                    <h6 class="text-white">{{ $item->today_qty }}</h6>
                                </div>
                                <div class="col-3">
                                    <h6 class="text-white">{{ $item->last7days_qty }}</h6>
                                </div>
                                <div class="col-3">
                                    <h6 class="text-white">{{ $item->last30days_qty }} / {{ $item->this_month }}</h6>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-12 mb-1">
                <div class="pull-up">
                    <div class="card-content">
                        <div class="card-body bg-cyan" style="min-height: 195px;">
                            <div class="row">
                                <div class="col-3">
                                    <h4 class="text-white">Top Reseller</h4>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white">Today</h4>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white">Last 7 day</h4>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white">Last 30 days/{{ \Carbon\Carbon::now()->format('M') }}</h4>
                                </div>
                            </div>
                            <hr>
                            @foreach ($data['top_reseller'] as $item)
                            <div class="row">
                                <div class="col-3">
                                    <h6 class="text-white">{{ $item->RESELLER_NAME }}</h6>
                                </div>
                                <div class="col-3">
                                    <h6 class="text-white">{{ $item->today_qty }}</h6>
                                </div>
                                <div class="col-3">
                                    <h6 class="text-white">{{ $item->last7days_qty }}</h6>
                                </div>
                                <div class="col-3">
                                    <h6 class="text-white">{{ $item->last30days_qty }} / {{ $item->this_month }}</h6>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!--/  -->
        <!-- Emails Products & Avg Deals -->
        {{-- <div class="row">
            <div class="col-12 col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Emails</h4>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body pt-0">
                            <p>Open rate <span class="float-right text-bold-600">89%</span></p>
                            <div class="progress progress-sm mt-1 mb-0 box-shadow-1">
                                <div class="progress-bar bg-gradient-x-danger" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="pt-1">Sent <span class="float-right"><span class="text-bold-600">310</span>/500</span>
                            </p>
                            <div class="progress progress-sm mt-1 mb-0 box-shadow-1">
                                <div class="progress-bar bg-gradient-x-success" role="progressbar" style="width: 48%" aria-valuenow="48" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Top Products</h4>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a href="#">Show all</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="border-top-0">iPhone X</th>
                                            <td class="border-top-0 text-right">2245</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">One Plus</th>
                                            <td class="text-right">1850</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Samsung S7</th>
                                            <td class="text-right">1550</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-center">Average Deal Size</h4>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-md-6 col-12 border-right-blue-grey border-right-lighten-5 text-center">
                                    <h6 class="danger text-bold-600">-30%</h6>
                                    <h4 class="font-large-2 text-bold-400">$12,536</h4>
                                    <p class="blue-grey lighten-2 mb-0">Per rep</p>
                                </div>
                                <div class="col-md-6 col-12 text-center">
                                    <h6 class="success text-bold-600">12%</h6>
                                    <h4 class="font-large-2 text-bold-400">$18,548</h4>
                                    <p class="blue-grey lighten-2 mb-0">Per team</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        <!--/ Emails Products & Avg Deals -->

        <!-- Total earning & Recent Sales  -->
        {{-- <div class="row">

            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-content">
                        <div class="earning-chart position-relative">
                            <div class="chart-title position-absolute mt-2 ml-2">
                                <h1 class="display-4">$1,596</h1>
                                <span class="text-muted">Total Earning</span>
                            </div>
                            <canvas id="earning-chart" class="height-450"></canvas>
                            <div class="chart-stats position-absolute position-bottom-0 position-right-0 mb-1 mr-3">
                                <a href="#" class="btn round btn-danger mr-1 btn-glow">Statistics <i class="ft-bar-chart"></i></a> <span class="text-muted">for the <a href="#" class="danger darken-2">last year.</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="recent-sales" class="col-12 col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Sales</h4>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a class="btn btn-sm btn-danger box-shadow-2 round btn-min-width pull-right" href="invoice-summary.html" target="_blank">View all</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content mt-1">
                        <div class="table-responsive">
                            <table id="recent-orders" class="table table-hover table-xl mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">Product</th>
                                        <th class="border-top-0">Customers</th>
                                        <th class="border-top-0">Categories</th>
                                        <th class="border-top-0">Popularity</th>
                                        <th class="border-top-0">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-truncate">iPhone X</td>
                                        <td class="text-truncate p-1">
                                            <ul class="list-unstyled users-list m-0">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Kimberly Simmons" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-4.png" alt="Avatar">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Willie Torres" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-5.png" alt="Avatar">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Rebecca Jones" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-6.png" alt="Avatar">
                                                </li>
                                                <li class="avatar avatar-sm">
                                                    <span class="badge badge-info">+8 more</span>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger round">Mobile</button>
                                        </td>
                                        <td>
                                            <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                                <div class="progress-bar bg-gradient-x-danger" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-truncate">$ 1200.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate">iPad</td>
                                        <td class="text-truncate p-1">
                                            <ul class="list-unstyled users-list m-0">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Kimberly Simmons" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-7.png" alt="Avatar">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Willie Torres" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-8.png" alt="Avatar">
                                                </li>
                                                <li class="avatar avatar-sm">
                                                    <span class="badge badge-info">+5 more</span>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success round">Tablet</button>
                                        </td>
                                        <td>
                                            <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                                <div class="progress-bar bg-gradient-x-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-truncate">$ 1190.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate">OnePlus</td>
                                        <td class="text-truncate p-1">
                                            <ul class="list-unstyled users-list m-0">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Kimberly Simmons" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-1.png" alt="Avatar">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Willie Torres" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-2.png" alt="Avatar">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Rebecca Jones" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-3.png" alt="Avatar">
                                                </li>
                                                <li class="avatar avatar-sm">
                                                    <span class="badge badge-info">+3 more</span>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger round">Mobile</button>
                                        </td>
                                        <td>
                                            <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                                <div class="progress-bar bg-gradient-x-danger" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-truncate">$ 999.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate">ZenPad</td>
                                        <td class="text-truncate p-1">
                                            <ul class="list-unstyled users-list m-0">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Kimberly Simmons" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-11.png" alt="Avatar">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Willie Torres" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-12.png" alt="Avatar">
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success round">Tablet</button>
                                        </td>
                                        <td>
                                            <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                                <div class="progress-bar bg-gradient-x-success" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-truncate">$ 1150.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate">Pixel 2</td>
                                        <td class="text-truncate p-1">
                                            <ul class="list-unstyled users-list m-0">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Kimberly Simmons" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-6.png" alt="Avatar">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Willie Torres" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-4.png" alt="Avatar">
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger round">Mobile</button>
                                        </td>
                                        <td>
                                            <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                                <div class="progress-bar bg-gradient-x-danger" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-truncate">$ 1180.00</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        <!--/ Total earning & Recent Sales  -->

        <!-- Recent Transactions -->
        {{-- <div class="row">
            <div id="recent-transactions" class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Transactions</h4>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a class="btn btn-sm btn-danger box-shadow-2 round btn-min-width pull-right" href="invoice-summary.html" target="_blank">Invoice Summary</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table id="recent-orders" class="table table-hover table-xl mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">Status</th>
                                        <th class="border-top-0">Invoice#</th>
                                        <th class="border-top-0">Customer Name</th>
                                        <th class="border-top-0">Products</th>
                                        <th class="border-top-0">Categories</th>
                                        <th class="border-top-0">Shipping</th>
                                        <th class="border-top-0">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-truncate"><i class="la la-dot-circle-o success font-medium-1 mr-1"></i> Paid</td>
                                        <td class="text-truncate"><a href="#">INV-001001</a></td>
                                        <td class="text-truncate">
                                            <span class="avatar avatar-xs">
                                                <img class="box-shadow-2" src="../../../app-assets/images/portrait/small/avatar-s-4.png" alt="avatar">
                                            </span>
                                            <span>Elizabeth W.</span>
                                        </td>
                                        <td class="text-truncate p-1">
                                            <ul class="list-unstyled users-list m-0">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Kimberly Simmons" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius" src="../../../app-assets/images/portfolio/portfolio-1.jpg" alt="Avatar">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Willie Torres" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius" src="../../../app-assets/images/portfolio/portfolio-2.jpg" alt="Avatar">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Rebecca Jones" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius" src="../../../app-assets/images/portfolio/portfolio-4.jpg" alt="Avatar">
                                                </li>
                                                <li class="avatar avatar-sm">
                                                    <span class="badge badge-info">+1 more</span>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger round">Food</button>
                                        </td>
                                        <td>
                                            <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                                <div class="progress-bar bg-gradient-x-danger" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-truncate">$ 1200.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate"><i class="la la-dot-circle-o danger font-medium-1 mr-1"></i> Declined</td>
                                        <td class="text-truncate"><a href="#">INV-001002</a></td>
                                        <td class="text-truncate">
                                            <span class="avatar avatar-xs">
                                                <img class="box-shadow-2" src="../../../app-assets/images/portrait/small/avatar-s-5.png" alt="avatar">
                                            </span>
                                            <span>Doris R.</span>
                                        </td>
                                        <td class="text-truncate p-1">
                                            <ul class="list-unstyled users-list m-0">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Kimberly Simmons" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius" src="../../../app-assets/images/portfolio/portfolio-5.jpg" alt="Avatar">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Willie Torres" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius" src="../../../app-assets/images/portfolio/portfolio-6.jpg" alt="Avatar">
                                                </li>
                                                <li class="avatar avatar-sm">
                                                    <span class="badge badge-info">+2 more</span>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning round">Electronics</button>
                                        </td>
                                        <td>
                                            <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                                <div class="progress-bar bg-gradient-x-warning" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-truncate">$ 1850.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate"><i class="la la-dot-circle-o warning font-medium-1 mr-1"></i> Pending</td>
                                        <td class="text-truncate"><a href="#">INV-001003</a></td>
                                        <td class="text-truncate">
                                            <span class="avatar avatar-xs">
                                                <img class="box-shadow-2" src="../../../app-assets/images/portrait/small/avatar-s-6.png" alt="avatar">
                                            </span>
                                            <span>Megan S.</span>
                                        </td>
                                        <td class="text-truncate p-1">
                                            <ul class="list-unstyled users-list m-0">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Kimberly Simmons" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius" src="../../../app-assets/images/portfolio/portfolio-2.jpg" alt="Avatar">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Willie Torres" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius" src="../../../app-assets/images/portfolio/portfolio-5.jpg" alt="Avatar">
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success round">Groceries</button>
                                        </td>
                                        <td>
                                            <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                                <div class="progress-bar bg-gradient-x-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-truncate">$ 3200.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate"><i class="la la-dot-circle-o success font-medium-1 mr-1"></i> Paid</td>
                                        <td class="text-truncate"><a href="#">INV-001004</a></td>
                                        <td class="text-truncate">
                                            <span class="avatar avatar-xs">
                                                <img class="box-shadow-2" src="../../../app-assets/images/portrait/small/avatar-s-7.png" alt="avatar">
                                            </span>
                                            <span>Andrew D.</span>
                                        </td>
                                        <td class="text-truncate p-1">
                                            <ul class="list-unstyled users-list m-0">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Kimberly Simmons" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius" src="../../../app-assets/images/portfolio/portfolio-6.jpg" alt="Avatar">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Willie Torres" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius" src="../../../app-assets/images/portfolio/portfolio-1.jpg" alt="Avatar">
                                                </li>
                                                <li class="avatar avatar-sm">
                                                    <span class="badge badge-info">+1 more</span>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info round">Apparels</button>
                                        </td>
                                        <td>
                                            <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                                <div class="progress-bar bg-gradient-x-info" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-truncate">$ 4500.00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-truncate"><i class="la la-dot-circle-o success font-medium-1 mr-1"></i> Paid</td>
                                        <td class="text-truncate"><a href="#">INV-001005</a></td>
                                        <td class="text-truncate">
                                            <span class="avatar avatar-xs">
                                                <img class="box-shadow-2" src="../../../app-assets/images/portrait/small/avatar-s-9.png" alt="avatar">
                                            </span>
                                            <span>Walter R.</span>
                                        </td>
                                        <td class="text-truncate p-1">
                                            <ul class="list-unstyled users-list m-0">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Kimberly Simmons" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius" src="../../../app-assets/images/portfolio/portfolio-5.jpg" alt="Avatar">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Willie Torres" class="avatar avatar-sm pull-up">
                                                    <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius" src="../../../app-assets/images/portfolio/portfolio-3.jpg" alt="Avatar">
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger round">Food</button>
                                        </td>
                                        <td>
                                            <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                                <div class="progress-bar bg-gradient-x-danger" role="progressbar" style="width: 35%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-truncate">$ 1500.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        <!--/ Recent Transactions -->


    </div>
@endsection
@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
@if ($data['role_id'] == 1)
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function delay(callback, ms) {
      var timer = 0;
      return function() {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
          callback.apply(context, args);
        }, ms || 0);
      };
    }
    $(document).on('keyup paste change keypress','#dashboard_notepad', delay(function(e){
        var get_url = $('#base_url').val();
        $.ajax({
            type:'POST',
            url:get_url+'/postDashboardNote',
            data: {
                note : this.value
            },
            beforeSend: function () {
                $("body").css("cursor", "progress");
            },
            success: function (data) {
                if (data == 1) {
                    toastr.info('Note Updated Successfully ! ','Success');
                }else{
                    toastr.warning('Please Try Again !','Failed');
                }
            },
            complete: function (data) {
                $("body").css("cursor", "default");
            }
        });
    }, 3000));

</script>
@endif
@endpush
