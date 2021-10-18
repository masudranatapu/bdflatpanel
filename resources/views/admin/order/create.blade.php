@extends('admin.layout.master')
@section('add_order','active')
@section('title')
    Order | Create
@endsection
@section('page-name')
    Create Order
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_dashboard_title')</a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('admin.admin-user') }}">@lang('order.breadcrumb_dashboard_order_text')</a>
    </li>
    <li class="breadcrumb-item active">@lang('order.breadcrumb_create_order_text')
    </li>
@endsection
<!--push from page-->
@push('custom_css')

@endpush('custom_css')
<style>
    #scrollable-dropdown-menu .tt-menu {
      max-height: 260px;
      overflow-y: auto;
      width: 100%;
      border: 1px solid #333;
      border-radius: 5px;

    }
    .twitter-typeahead{
        display: block !important;
    }
</style>
@section('content')
    <div class="card card-success min-height">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-colored-form-control">
                <i class="ft-plus text-primary"></i> Add New
                Order</h4>
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
                {!! Form::open([ 'route' => 'admin.order.store', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                @csrf
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {!! $errors->has('customer') ? 'error' : '' !!}">
                                <div class="controls">
                                    <label>@lang('order.customer')<span class="text-danger">*</span></label>
                                    <div class="controls" id="scrollable-dropdown-menu">
                                        {{-- {!! Form::select('customer', $customer, null, ['class'=>'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Select Customer','id' => 'customer','tabindex' => 9]) !!} --}}
                                        <input type="search" name="q" id="book_customer" class="form-control search-input" placeholder="Enter Customer Name" autocomplete="off">
                                        {!! $errors->first('customer', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group {!! $errors->has('shipping_type') ? 'error' : '' !!}">
                                <label>@lang('order.shipping_type')<span class="text-danger">*</span></label>
                                <div class="controls">
                                    <div>
                                        <label>{!! Form::radio('transport', 'air_freight', true) !!} Air Freight</label>&nbsp;&nbsp;
                                        <label>{!! Form::radio('transport', 'sea_freight') !!} Sea Freight</label>&nbsp;&nbsp;
                                        <label>{!! Form::radio('transport', 'direct_freight') !!} Direct Sale</label>&nbsp;&nbsp;
                                        <label>{!! Form::radio('transport', 'ready_stock') !!} Ready Stock</label>&nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {!! $errors->has('from_address') ? 'error' : '' !!}">
                                <label>@lang('order.from_address')<span class="text-danger">*</span></label>
                                <div class="controls">
                                    {!! Form::textarea('from_address', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter short from address description about the order', 'tabindex' => 1, 'rows' => 3 ]) !!}
                                    {!! $errors->first('from_address', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group {!! $errors->has('delivery_address') ? 'error' : '' !!}">
                                <label>@lang('order.delivery_address')<span class="text-danger">*</span></label>
                                <div class="controls">
                                    {!! Form::textarea('delivery_address', null, [ 'class' => 'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Enter delivery address about the order', 'tabindex' => 1, 'rows' => 3 ]) !!}
                                    {!! $errors->first('delivery_address', '<label class="help-block text-danger">:message</label>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {!! $errors->has('sales_agent') ? 'error' : '' !!}">
                                <div class="controls">
                                    <label>@lang('order.sales_agent')<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::select('sales_agent', $agent, null, ['class'=>'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Select Agent','id' => 'sales_agent','tabindex' => 9]) !!}
                                        {!! $errors->first('sales_agent', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">


                            <div class="form-group {!! $errors->has('shipping_location') ? 'error' : '' !!}">
                                <div class="controls">
                                    <label>@lang('order.shipping_location')<span class="text-danger">*</span></label>
                                    <div class="controls">
                                        {!! Form::select('shipping_location', $country, null, ['class'=>'form-control mb-1', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Select Country','id' => 'shipping_location','tabindex' => 9]) !!}
                                        {!! $errors->first('shipping_location', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="order_date">@lang('order.order_date')</label>
                                <input type="date" id="order_date" class="form-control" name="order_date" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Date Opened" data-original-title="" title="">
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="delivery_date">@lang('order.delivery_date')</label>
                                <input type="date" id="delivery_date" class="form-control" name="delivery_date" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Date Opened" data-original-title="" title="">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions mt-10 text-center">
                    <a href="{{ route('admin.order.list')}}" class="btn btn-warning mr-1">

                            <i class="ft-x"></i> @lang('order.order_frm_button_cancel_label')

                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="la la-check-square-o"></i> @lang('order.order_frm_button_save_label')
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

<!--push from page-->
@push('custom_js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <!-- Typeahead.js Bundle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            var get_url = $('#base_url').val();
            // Set the Options for "Bloodhound" suggestion engine
            var engine = new Bloodhound({
                remote: {
                    url: get_url+'/autocomplete_booking?q=%QUERY%&type=SLS_CUSTOMERS',
                    wildcard: '%QUERY%'
                },
                datumTokenizer: Bloodhound.tokenizers.whitespace('q'),
                queryTokenizer: Bloodhound.tokenizers.whitespace
            });

            $(".search-input").typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                source: engine.ttAdapter(),
                // This will be appended to "tt-dataset-" to form the class name of the suggestion menu.
                display: 'NAME',
                limit: 20,
                // the key from the array we want to display (name,id,email,etc...)
                templates: {
                    empty: [
                        '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                    ],
                    header: [
                        '<div class="list-group search-results-dropdown">'
                    ],
                    suggestion: function (data) {
                        return '<span class="list-group-item" style="cursor: pointer;">' + data.NAME + '</span>'
              }
                }
            });
        });
    </script>
@endpush('custom_js')
