@extends('admin.layout.master')
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
@endpush

@section('offer_primary_list','active')
@section('offer_management','open')

@section('title')
    @lang('form.offer_primary_list')
@endsection
@section('page-name')
    @lang('form.offer_primary_list')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('form.dashboard')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('form.offer_primary_list')
    </li>
@endsection
@php
    $roles = userRolePermissionArray();
@endphp
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush

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
              <div class="card">
                <div class="card-header">
                    <div class="form-group">
                    @if(hasAccessAbility('new_offer_primary', $roles))
                        <a href="{{ route('admin.offer_primary.create') }}" class="btn btn-sm btn-primary " href="" title="Add new primary list"> <i class="ft-plus text-white"></i> Add new primary list</a>
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
                  <div class="card-body card-dashboard ">
                    <div class="table-responsive">
                      <table class="table table-striped table-bordered alt-pagination table-sm" id="indextable">
                        <thead>
                          <tr>
                            <th style="width: 5%;">Sl.</th>
                            <th class="text-left" style="width: 20%;">Name</th>
                            <th class="text-left" >Description</th>
                            <th class="text-center" style="width: 10%;">Active</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if($rows && (count($rows)> 0) )
                            @foreach($rows as $key => $row)
                            <tr>
                                <td>{{ $key+1 }}</td>

                                <td>{{ $row->PRIMARY_SET_NAME }}</td>
                                <td> {{ $row->COMMENTS }}</td>
                                <td class="text-center" style="width:100px;">
                                    @if(hasAccessAbility('new_offer_primary', $roles))
                                     <a href="{{ route('admin.offer_primary.add_product',[ 'id' => $row->PK_NO ]) }}" class="btn btn-xs btn-primary" href="" title="Add new product"> <i class="la la-plus" ></i></a>
                                    @endif
                                    @if(hasAccessAbility('edit_offer_primary', $roles))
                                    <a href="{{ route('admin.offer_primary.edit', [$row->PK_NO]) }}" class="btn btn-xs btn-info" title="EDIT OR VIEW"><i class="la la-edit"></i></a>
                                    @endif

                                    @if(hasAccessAbility('view_offer_primary', $roles))
                                    <a href="{{ route('admin.offer_primary.view', [$row->PK_NO]) }}" class="btn btn-xs btn-success" title="VIEW"><i class="la la-eye"></i></a>
                                    @endif

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
                  </section>
                </div>


      @include('admin.account._account_edit_modal')

    <!--/ Alternative pagination table -->
@endsection
@push('custom_js')

<!--script only for brand page-->
<script type="text/javascript" src="{{ asset('app-assets/pages/account.js')}}"></script>


@endpush('custom_js')
