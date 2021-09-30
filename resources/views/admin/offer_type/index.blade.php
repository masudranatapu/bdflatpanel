@extends('admin.layout.master')
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
<style>
    .table th, .table td {
    vertical-align: middle;
    }
    #offerType tr th{font-size: 12px !important;}
</style>
@endpush
@section('offer_type','active')
@section('offer_management','open')

@section('title')
    @lang('form.offer_list_page_title')
@endsection
@section('page-name')
    @lang('form.offer_type_page_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('form.dashboard')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('form.offer_type_page_title')
    </li>
@endsection
@php
    $roles = userRolePermissionArray();
@endphp

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
                    @if(hasAccessAbility('new_offer_type', $roles))
                        <a  href="{{ route('admin.offer_type.create') }}" class="btn btn-sm btn-primary "  title="Add new offer type"> <i class="ft-plus text-white"></i> Add new offer type</a>
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
                      <table class="table table-striped table-bordered alt-pagination table-sm" id="offerType">
                        <thead>
                          <tr>
                            <th>Sl.</th>
                            <th class="text-left" style="">Name</th>
                            <th class="text-left" style="">Public Name</th>
                            <th class="text-left">$P</th>
                            <th class="text-left">$P2</th>
                            <th class="text-left">$P_SS</th>
                            <th class="text-left">$P_SM</th>
                            <th class="text-left">$P_AIR</th>
                            <th class="text-left">$P_SEA</th>
                            <th style="">X1Qt</th>
                            <th style="">X2Qt</th>
                            <th style="">AZ1%</th>
                            <th style="">AZ2%</th>
                            <th style="">AZ3%</th>
                            <th style="">$R</th>
                            <th style="">$R2</th>
                            {{-- <th style="">$R_SS</th>
                            <th style="">$R_SM</th>
                            <th style="">$R_AIR</th>
                            <th style="">$R_SEA</th> --}}
                            <th style="">Y1Qt</th>
                            <th style="">Y2Qt</th>
                            <th style="">ZB1%</th>
                            <th style="">ZB2%</th>
                            <th style="">ZB3%</th>
                            <th >Active</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if($rows && (count($rows) > 0 ) )
                            @foreach($rows as $key => $row)
                            <tr>
                                <td >{{ $key+1 }}</td>
                                <td class="text-left">{{ $row->NAME }}</td>
                                <td class="text-left">{{ $row->PUBLIC_NAME }}</td>
                                <td >{{ $row->P_AMOUNT }}</td>
                                <td >{{ $row->P2_AMOUNT }}</td>
                                <td >{{ $row->P_SS }}</td>
                                <td >{{ $row->P_SM }}</td>
                                <td >{{ $row->P_AIR }}</td>
                                <td >{{ $row->P_SEA }}</td>
                                <td>{{ $row->X1_QTY }}</td>
                                <td>{{ $row->X2_QTY }}</td>
                                <td> {{ $row->ZA1 }} </td>
                                <td> {{ $row->ZA2 }} </td>
                                <td> {{ $row->ZA3 }} </td>

                                <td>{{ $row->R_AMOUNT }}</td>
                                <td>{{ $row->R2_AMOUNT }}</td>
                                {{-- <td>{{ $row->R_SS }}</td>
                                <td>{{ $row->R_SM }}</td>
                                <td>{{ $row->R_AIR }}</td>
                                <td>{{ $row->R_SEA }}</td> --}}
                                <td>{{ $row->Y1_QTY }}</td>
                                <td>{{ $row->Y2_QTY }}</td>
                                <td>{{ $row->ZB1 }}</td>
                                <td>{{ $row->ZB2 }}</td>
                                <td>{{ $row->ZB3 }}</td>
                                <td>
                                    @if(hasAccessAbility('edit_offer_type', $roles))
                                    <a href="{{ route('admin.offer_type.edit', [$row->PK_NO]) }}" class="btn btn-xxs btn-primary mr-1" title="EDIT"><i class="la la-edit"></i></a>
                                    @endif
                                </td>
                                @endforeach
                                @endif

                                    </tr>

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
