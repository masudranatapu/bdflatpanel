@extends('admin.layout.master')

@section('Product Management','open')
@section('product brand','active')

@section('title') Product Brand @endsection
@section('page-name') Product Brand @endsection

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
  <li class="breadcrumb-item active">Brand</li>
@endsection
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
@endpush
@php
  $roles = userRolePermissionArray();
  $selected_row = request()->get('row_no') ?? 0
@endphp

@section('content')
<div class="content-body min-height">
  <section id="pagination">
    <div class="row">
      <div class="col-12">
        <div class="card card-sm card-success">
          <div class="card-header">
            <div class="form-group">
              @if(hasAccessAbility('new_brand', $roles))
              <a class="text-white btn btn-sm btn-primary" href="{{ route('product.brand.create')}}" title="Add new brand"><i class="ft-plus text-white"></i> Create Brand</a>
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

                <table class="table-striped table-bordered alt-pagination table-sm" id="indextable">

                  <thead>
                    <tr>
                      <th style="width: 5%;">Sl.</th>
                      <th class="text-left" style="width: 20%;">Brand Name</th>
                      <th class="text-left" style="width: 20%;">All Models</th>
                      <th class="text-left" style="width: 20%;">All Colors</th>
                      <th class="text-left" style="width: 20%;">All Size</th>
                      <th class="text-left" style="width: 20%;">Logo</th>
                      <th style="width: 15%;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($rows as $pkey => $row)
                    <tr class="{{$row->IS_DALAL_BRAND == 1 ? 'text-danger' : ''}}">
                      <td style="width: 5%;">{{ $pkey + 1 }}</td>
                      <td class="text-left" style="width: 20%;">

                        <span title="Brand name" style="width: 20%;">{{ $row->NAME }}</span>

                        <span title="Brand code" style="width: 20%;">({{ $row->CODE }})</span>
                      </td>
                      <td class="text-left" style="width: 20%;">
                        @if($row->productModel && $row->productModel->count() > 0 )
                        <ul class="list-group" style="max-width: 300px;">
                          @foreach($row->productModel as $key => $model)
                          @if($key < 2)
                          <li class="list-group-item list-group-item-sm list-group-parent">
                            <div class="float-right" style="display: inline-block; min-width: 50px;">
                              <span class="float-right child-action">
                                @if(hasAccessAbility('edit_model', $roles))
                                <button class="btn btn-xs btn-primary mr-0 editModelModal" data-toggle="modal" data-target="#addEditModelModal" title="EDIT MODEL" data-url="{{ route('admin.product-model.update', [$model->PK_NO]) }}" data-brand_id="{{$row->PK_NO}}" data-brand_name="{{$row->NAME}}" data-model_id="{{$model->PK_NO}}" data-model_name="{{$model->NAME}}" data-model_code="{{$model->CODE}}" data-type="edit" data-row="{{$pkey + 1}}"><i
                                    class="la la-edit"></i></button>
                                @endif

                                @if(hasAccessAbility('delete_model', $roles))
                                <a href="{{route('admin.product-model.delete', [$model->PK_NO])}}" class="btn btn-xs btn-danger" title="DELETE" onclick="return confirm('Are you sure you want to delete?')"><i class="la la-trash"></i>
                                </a>
                                @endif
                              </span>
                            </div>
                            <span> {{$key+1}} . </span>

                            <span title="Model name">{{$model->NAME}}</span> &nbsp;

                            <span title="Model code">({{$model->CODE}})</span>
                          </li>
                          @endif
                          @endforeach()

                        </ul>
                        @endif

                        @if($row->productModel && $row->productModel->count() > 2 )
                        <div class="card collapse-icon default-collapse  accordion-icon-rotate card-sm" style="max-width: 300px;">
                          <a id="headingCollapse51" class="card-header border-primary primary collapsed" data-toggle="collapse" href="#collapseProdModel_{{$row->PK_NO}}" aria-expanded="false" aria-controls="collapseProdModel_{{$row->PK_NO}}" style="padding: 5px;">
                            <div class="card-title lead primary">More Models</div>
                          </a>
                          <div id="collapseProdModel_{{$row->PK_NO}}" role="tabpanel" aria-labelledby="headingCollapse51" class="card-collapse collapse" aria-expanded="true" style="">
                            <div class="card-content">
                              <div class="card-body p-0">
                                <ul class="list-group ">
                                  @foreach($row->productModel as $key => $model)
                                  @if($key > 1)
                                  <li class="list-group-item list-group-item-sm list-group-parent">
                                    <span class=" float-right child-action">
                                        @if(hasAccessAbility('edit_model', $roles))
                                        <button class="btn btn-xs btn-primary mr-0 editModelModal" data-toggle="modal" data-target="#addEditModelModal" title="EDIT MODEL" data-url="{{ route('admin.product-model.update', [$model->PK_NO]) }}" data-brand_id="{{$row->PK_NO}}" data-brand_name="{{$row->NAME}}" data-model_id="{{$model->PK_NO}}" data-model_name="{{$model->NAME}}" data-model_code="{{$model->CODE}}"><i class="la la-edit"></i></button>
                                        @endif

                                        @if(hasAccessAbility('delete_model', $roles))
                                        <a href="{{route('admin.product-model.delete', [$model->PK_NO])}}" class="btn btn-xs btn-danger mr-0" title="DELETE" onclick="return confirm('Are you sure you want to delete?')"><i class="la la-trash"></i>
                                        </a>
                                        @endif
                                      </span>
                                      <span> {{$key+1}} . </span>

                                      <span title="Model name">{{$model->NAME}}</span> &nbsp;

                                     <span title="Model code">({{$model->CODE}})</span>
                                    </li>
                                    @endif
                                    @endforeach()

                                  </ul>
                                </div>
                              </div>
                            </div>
                          </div>
                          @endif
                        </td>
                        <td class="text-left" style="width: 20%;">
                          @if($row->productColor && $row->productColor->count() > 0 )
                          <ul class="list-group" style="max-width: 300px;">
                            @foreach($row->productColor as $key => $color)
                            @if($key < 2)
                            <li class="list-group-item list-group-item-sm list-group-parent">
                              <div class="float-right" style="display: inline-block; min-width: 50px;">
                                <span class="float-right child-action">

                                  @if(hasAccessAbility('edit_color', $roles))
                                    <button class="btn btn-xs btn-primary mr-0 editColorModal" data-toggle="modal" data-target="#addEditColorModal" title="EDIT COLOR" data-url="{{ route('admin.product.color.update', [$color->PK_NO]) }}" data-brand_id="{{$row->PK_NO}}" data-brand_name="{{$row->NAME}}" data-color_id="{{$color->PK_NO}}" data-color_name="{{$color->NAME}}" data-color_code="{{$color->CODE}}" data-type="edit"><i class="la la-edit"></i></button>
                                  @endif

                                  @if(hasAccessAbility('delete_color', $roles))
                                    <a href="{{ route('admin.product.color.delete', [$color->PK_NO]) }}" class="btn btn-xs btn-danger" title="DELETE" onclick="return confirm('Are you sure you want to delete?')"><i class="la la-trash"></i>
                                    </a>
                                  @endif
                                </span>
                                </div>
                                <span> {{$key+1}} . </span>
                                <span title="Color name">{{$color->NAME}}</span> &nbsp;

                                {{-- <span title="Color code">({{$color->CODE}})</span> --}}
                              </li>
                              @endif
                              @endforeach()

                            </ul>
                            @endif

                            @if($row->productColor && $row->productColor->count() > 2 )
                            <div class="card collapse-icon default-collapse  accordion-icon-rotate card-sm" style="max-width: 300px;">
                              <a id="headingCollapse51" class="card-header border-primary primary collapsed" data-toggle="collapse" href="#collapseProdColor_{{$row->PK_NO}}" aria-expanded="false" aria-controls="collapseProdColor_{{$row->PK_NO}}" style="padding: 5px;">
                                <div class="card-title lead primary">More Colors</div>
                              </a>
                              <div id="collapseProdColor_{{$row->PK_NO}}" role="tabpanel" aria-labelledby="headingCollapse51" class="card-collapse collapse" aria-expanded="true" style="">
                                <div class="card-content">
                                  <div class="card-body p-0">
                                    <ul class="list-group ">
                                      @foreach($row->productColor as $key => $color)
                                      @if($key > 1)
                                      <li class="list-group-item list-group-item-sm list-group-parent">
                                        <span class=" float-right child-action">
                                          @if(hasAccessAbility('edit_color', $roles))
                                            <button class="btn btn-xs btn-primary mr-0 editColorModal" data-toggle="modal" data-target="#addEditColorModal" title="EDIT COLOR" data-url="{{ route('admin.product.color.update', [$color->PK_NO]) }}" data-brand_id="{{$row->PK_NO}}" data-brand_name="{{$row->NAME}}" data-color_id="{{$color->PK_NO}}" data-color_name="{{$color->NAME}}" data-color_code="{{$color->CODE}}" data-type="edit"><i class="la la-edit"></i></button>
                                            @endif
                                            @if(hasAccessAbility('delete_color', $roles))
                                            <a href="{{route('admin.product.color.delete', [$color->PK_NO])}}" class="btn btn-xs btn-danger mr-0" title="DELETE" onclick="return confirm('Are you sure you want to delete?')"><i class="la la-trash"></i>
                                            </a>
                                            @endif
                                          </span>
                                          <span> {{$key+1}} . </span>

                                          <span title="Model name">{{$color->NAME}}</span> &nbsp;

                                          {{--<span title="Model code">({{$color->CODE}})</span> --}}
                                        </li>
                                        @endif
                                        @endforeach()

                                      </ul>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              @endif
                            </td>
                            <td class="text-left" style="width: 20%;">
                              @if($row->productSize && $row->productSize->count() > 0 )
                              <ul class="list-group" style="max-width: 300px;">
                                @foreach($row->productSize as $key => $size)
                                @if($key < 2)
                                <li class="list-group-item list-group-item-sm list-group-parent">
                                  <div class="float-right" style="display: inline-block; min-width: 50px;">

                                    <span class="float-right child-action">

                                      @if(hasAccessAbility('edit_size', $roles))
                                        <button class="btn btn-xs btn-primary mr-0 editSizeModal" data-toggle="modal" data-target="#addEditSizeModal" title="EDIT SIZE" data-url="{{ route('admin.product-size.update', [$size->PK_NO]) }}" data-brand_id="{{$row->PK_NO}}" data-brand_name="{{$row->NAME}}" data-size_id="{{$size->PK_NO}}" data-size_name="{{$size->NAME}}" data-size_code="{{$size->CODE}}" data-type="edit"><i class="la la-edit"></i></button>
                                        @endif

                                        @if(hasAccessAbility('delete_size', $roles))
                                        <a href="{{ route('admin.product-size.delete', [$size->PK_NO]) }}" class="btn btn-xs btn-danger" title="DELETE" onclick="return confirm('Are you sure you want to delete?')"><i class="la la-trash"></i>
                                        </a>
                                        @endif
                                      </span>
                                    </div>
                                    <span> {{$key+1}} . </span>

                                    <span title="Size name">{{$size->NAME}}</span> &nbsp;

                                    {{-- <span title="Size code">({{$size->CODE}})</span> --}}
                                  </li>
                                  @endif
                                  @endforeach()

                                </ul>
                                @endif

                                @if($row->productSize && $row->productSize->count() > 2 )
                                <div class="card collapse-icon default-collapse  accordion-icon-rotate card-sm" style="max-width: 300px;">
                                  <a id="headingCollapse51" class="card-header border-primary primary collapsed" data-toggle="collapse" href="#collapseProdSize_{{$row->PK_NO}}" aria-expanded="false" aria-controls="collapseProdSize_{{$row->PK_NO}}" style="padding: 5px;">
                                    <div class="card-title lead primary">More Size</div>
                                  </a>
                                  <div id="collapseProdSize_{{$row->PK_NO}}" role="tabpanel" aria-labelledby="headingCollapse51" class="card-collapse collapse" aria-expanded="true" style="">
                                    <div class="card-content">
                                      <div class="card-body p-0">
                                        <ul class="list-group ">
                                          @foreach($row->productSize as $key => $size)
                                          @if($key > 1)
                                          <li class="list-group-item list-group-item-sm list-group-parent">
                                            <span class=" float-right child-action">
                                              @if(hasAccessAbility('edit_size', $roles))
                                              <button class="btn btn-xs btn-primary mr-0 editSizeModal" data-toggle="modal" data-target="#addEditSizeModal" title="EDIT SIZE" data-url="{{ route('admin.product-size.update', [$size->PK_NO]) }}" data-brand_id="{{$row->PK_NO}}" data-brand_name="{{$row->NAME}}" data-size_id="{{$size->PK_NO}}" data-size_name="{{$size->NAME}}" data-size_code="{{$size->CODE}}" data-type="edit"><i class="la la-edit"></i></button>
                                              @endif
                                              @if(hasAccessAbility('delete_size', $roles))
                                              <a href="{{ route('admin.product-size.delete', [$size->PK_NO]) }}" class="btn btn-xs btn-danger mr-0" title="DELETE" onclick="return confirm('Are you sure you want to delete?')"><i class="la la-trash"></i>
                                                </a>
                                              @endif
                                              </span>
                                              <span> {{$key+1}} . </span>

                                              <span title="Model name">{{$size->NAME}}</span> &nbsp;

                                              {{--<span title="Model code">({{$size->CODE}})</span> --}}
                                            </li>
                                            @endif
                                            @endforeach()

                                          </ul>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  @endif
                                </td>
                                <td class="" style=""><img src="{{asset($row->BRAND_LOGO ?? 'app-assets/images/no_image.jpg')}}" width="50"></td>
                                <td style="width: 15%;">

                                  @if(hasAccessAbility('edit_brand', $roles))
                                  <a href="{{ route('product.brand.edit', [$row->PK_NO]) }}" class="btn btn-xs btn-info mr-0 mb-1" title="Edit"><i class="la la-edit"></i></a>
                                  @endif

                                  @if(hasAccessAbility('delete_brand', $roles))
                                  <a href="{{ route('product.brand.delete', [$row->PK_NO]) }}" onclick="return confirm('Are you sure you want to delete?')" class="btn btn-xs btn-danger mr-0 mb-1" title="Delete"><i class="la la-trash"></i></a>
                                  @endif

                                  @if(hasAccessAbility('new_model', $roles))
                                  <a href="javascript:void(0)" class="btn btn-xs btn-primary mr-0 mb-1 addModelModal" title="ADD MODEL" data-toggle="modal" data-target="#addEditModelModal" data-url="{{ route('admin.product-model.store')}}" data-brand_id="{{$row->PK_NO}}" data-brand_name="{{$row->NAME}}" data-model_id="" data-model_name="" data-model_code="" data-type="add" >&nbsp;+ M&nbsp;</a>
                                  @endif

                                  @if(hasAccessAbility('new_color', $roles))
                                  <a href="javascript:void(0)" class="btn btn-xs btn-primary mr-0 mb-1 addColorModal" title="ADD COLOR" data-toggle="modal" data-target="#addEditColorModal"  data-url="{{ route('admin.product.color.store') }}" data-brand_id="{{$row->PK_NO}}" data-brand_name="{{$row->NAME}}" data-color_id="" data-color_name="" data-color_code="" data-type="add">&nbsp;+ C&nbsp;</a>
                                  @endif

                                  @if(hasAccessAbility('new_size', $roles))
                                  <a href="javascript:void(0)" class="btn btn-xs btn-primary mr-0 mb-1 editSizeModal" title="ADD SIZE" data-toggle="modal" data-target="#addEditSizeModal"  data-url="{{ route('admin.product-size.store') }}" data-brand_id="{{$row->PK_NO}}" data-brand_name="{{$row->NAME}}" data-size_id="" data-size_name="" data-size_code="" data-type="add">&nbsp;+ S&nbsp;</a>
                                  @endif

                                </td>
                              </tr>
                              @endforeach

                            </tbody>
                          </table>
                        </div>
                        <span class="btn btn-sm btn-default dalal-bg">Dalal Color</span>
                          <span class="btn btn-sm btn-default vendor-bg">Vendor Color</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>


@include('admin.product-brand._model_color_size_edit_modal')

@endsection

@push('custom_js')

<!--script only for brand page-->
<script type="text/javascript" src="{{ asset('app-assets/pages/brand.js')}}"></script>

<!-- BEGIN: Data Table-->
<script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>

@endpush('custom_js')
