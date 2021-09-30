@extends('admin.layout.master')
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-tooltip.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush
@section('offer_list','active')
@section('offer_management','open')

@section('title')
@lang('form.offer_list_page_title')
@endsection
@section('page-name')
    @lang('form.offer_list_page_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('form.dashboard')    </a>
    </li>
    <li class="breadcrumb-item active">@lang('form.offer_list_page_title')
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
                    @if(hasAccessAbility('new_offer', $roles))
                        <a class="btn btn-sm btn-primary " href="{{ route('admin.offer.create') }}" title="Add new offer"> <i class="ft-plus text-white"></i> Add new offer</a>
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
                            <th style="width: 40px;" class="text-center">Sl.</th>
                            <th class="" style="">Photo</th>
                            <th class="" style="">Name</th>
                            <th class="" style="">Public Name</th>
                            <th style="width: 200px;">Type</th>
                            <th style="width: 100px;">List A</th>
                            <th style="width: 100px;">List B</th>
                            <th style="width: 70px;" class="text-center">Start date</th>
                            <th style="width: 70px;" class="text-center">End date</th>
                            <th style="width: 80px;" class="text-center">Active</th>
                          </tr>
                        </thead>
                        <tbody>
                                @if($rows && count($rows) > 0 )
                                @foreach($rows as $key => $row )
                            <tr class="{{ $row->day_diff_with_current == 'No' ? 'text-danger' : '' }}">
                                <td class="text-center">{{ $key+1 }}</td>
                                <td>
                                    @if($row->IMAGE)
                                    <img src="{{ asset($row->IMAGE) }}"  width="50"/>
                                    @else
                                    <img src="{{ asset('app-assets/images/no_image.jpg') }}"  width="50"/>
                                    @endif
                                </td>

                                <td>{{ $row->BUNDLE_NAME }}</td>
                                <td>{{ $row->BUNDLE_NAME_PUBLIC }}</td>
                                <td style="width: 200px;"> {{ $row->offerType->NAME ?? ''}}</td>

                                <td style="width: 100px;">
                                    <a href="{{ route('admin.offer_primary.view', [$row->F_A_LIST_NO]) }}" target="_blank">
                                    {{ $row->listA->PRIMARY_SET_NAME ?? ''}}
                                    </a>
                                </td>
                                <td style="width: 100px;">
                                    <a href="{{ route('admin.offer_secondary.view', [$row->F_B_LIST_NO]) }}" target="_blank">
                                     {{ $row->listB->SECONDARY_SET_NAME ?? ''}}
                                    </a>
                                    </td>

                                <td style="width: 70px;" class="text-center"> {{ date('d-m-Y', strtotime($row->VALIDITY_FROM)) }} </td>
                                <td style="width: 70px;" class="text-center"> {{ date('d-m-Y', strtotime($row->VALIDITY_TO)) }} </td>

                                <td style="width: 80px;" class="text-center">

                                @if(hasAccessAbility('edit_offer_list', $roles))
                                    <a href="{{ route('admin.offer.edit', [$row->PK_NO]) }}" title="INVOICE EDIT" class="btn btn-xxs btn-primary mr-05"><i class="la la-pencil"></i></a>
                                @endif

                                @if(hasAccessAbility('delete_offer_list', $roles))
                                    <a href="{{ route('admin.offer.delete', [$row->PK_NO]) }}"  class="btn btn-xxs btn-danger mr-05" onclick="return confirm('Are You Sure?')" title="OFFER DELETE"><i class="la la-trash"></i></a>
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
