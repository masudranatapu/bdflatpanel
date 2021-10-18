@extends('admin.layout.master')
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

@section('System Settings','open')
@section('currency','active')

@section('title')
    Currency
@endsection
@section('page-name')
Currency
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('payment.breadcrumb_title')    </a>
    </li>
    <li class="breadcrumb-item active">Currency
    </li>
@endsection
@push('custom_js')
<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
<!-- END: Data Table-->
@endpush
@section('content')
    <!-- Alternative pagination table -->
    <div class="content-body min-height">
        <section id="pagination">
          <div class="row">
            <div class="col-12">
              <div class="card card-success">
                <div class="card-header">
                  <div class="form-group">
                    <a class="text-white addCurrencyModal" href="javascript:void(0)" data-toggle="modal" data-target="#AddCurrency" title="Add New Currency" data-url="{{ route('admin.currency.store')}}" data-type="add" data-value="">
                      <button type="button" class="btn btn-sm btn-primary">
                        <i class="ft-plus text-white"></i> Add Currency
                      </button>
                    </a>
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
                    <div class="table-responsive p-1">
                      <table class="table table-striped table-bordered alt-pagination table-sm" id="indextable">
                        <thead>
                          <tr>
                            <th style="width: 40px;">Sl.</th>
                            <th class="text-left" style="width: 420px;">Currency Code</th>
                            <th class="text-left" style="max-width: 420px;">Currency Name</th>
                            <th class="text-left">Exchange Rate GB</th>
                            <th style="width: 200px;">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                        @foreach($rows as $row)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td class="text-left">
                                    <ul class="list-group" style="max-width: 300px;">
                                        <li class="list-group-item list-group-item-sm list-group-parent">
                                          <div class="float-right" style="display: inline-block; min-width: 50px;">
                                            <span class="float-right child-action">
                                                <button class="btn btn-xs btn-primary mr-0 editCodeModal" data-toggle="modal" data-target="#EditCode" title="EDIT CODE" data-url="{{ route('admin.currency.update', [$row->PK_NO]) }}" data-type="code" data-value="{{$row->CODE}}"><i
                                                    class="la la-edit"></i></button>
                                            </span>
                                          </div>
                                          <span title="Bank name">{{$row->CODE}}</span> &nbsp;
                                        </li>
                                    </ul>
                                </td>
                                <td class="text-left" style="max-width: 200px;">
                                    <ul class="list-group" style="max-width: 300px;">
                                        <li class="list-group-item list-group-item-sm list-group-parent">
                                          <div class="float-right" style="display: inline-block; min-width: 50px;">
                                            <span class="float-right child-action">
                                             <button class="btn btn-xs btn-primary mr-0 editCodeModal" data-toggle="modal" data-target="#EditCode" title="EDIT NAME" data-url="{{ route('admin.currency.update', [$row->PK_NO]) }}" data-type="name" data-value="{{$row->NAME}}"><i
                                              class="la la-edit"></i></button>
                                            </span>
                                          </div>
                                          <span title="Bank name">{{$row->NAME}}</span> &nbsp;
                                        </li>
                                    </ul>
                                </td>
                                <td class="text-left" style="max-width: 200px;">
                                    <ul class="list-group" style="max-width: 300px;">
                                        <li class="list-group-item list-group-item-sm list-group-parent">
                                          <div class="float-right" style="display: inline-block; min-width: 50px;">
                                            <span class="float-right child-action">
                                                <button class="btn btn-xs btn-primary mr-0 editCodeModal" data-toggle="modal" data-target="#EditCode" title="EDIT EXCHANGE RATE" data-url="{{ route('admin.currency.update', [$row->PK_NO]) }}" data-type="rate" data-value="{{$row->EXCHANGE_RATE_GB}}"><i
                                                    class="la la-edit"></i></button>
                                            </span>
                                          </div>
                                          <span title="Bank name">{{$row->EXCHANGE_RATE_GB}}</span> &nbsp;
                                        </li>
                                    </ul>
                                </td>
                                <td style="width: 200px;">
                                    <a href="{{ route('admin.currency.delete', [$row->PK_NO]) }}" onclick="return confirm('Are you sure you want to delete?')" class="btn btn-xs btn-danger mr-0 mb-1" title="Delete Currency"><i class="la la-trash"></i>
                                    </a>
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


      @include('admin.currency._modal')

    <!--/ Alternative pagination table -->
@endsection
@push('custom_js')

<!--script only for brand page-->
<script type="text/javascript" src="{{ asset('app-assets/pages/currency.js')}}"></script>


@endpush('custom_js')
