@extends('admin.layout.master')

@section('Property Owner','open')
@section('owner_list','active')

@section('title') Owner | Update @endsection
@section('page-name') Update Owner @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Update Owner</li>
@endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{asset('/custom/css/custom.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{asset('/assets/css/forms/datepicker/bootstrap-datetimepicker.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/assets/css/image_upload/image-uploader.min.css')}}">
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        a.ui-state-default{background-color:red!important}
        #scrollable-dropdown-menu2 .tt-menu{max-height:260px;overflow-y:auto;width:100%;border:1px solid #333;border-radius:5px}.twitter-typeahead{display:block!important}.tt-hint{color:#999!important}
    </style>
@endpush

@php
    $roles          = userRolePermissionArray();
    $user_type      = Config::get('static_array.user_type');
    $user_status    = Config::get('static_array.user_status');
    $owner          = $data['owner'] ?? [];
    $tabIndex = 0;
    $days = [
        0 => 'Sun',
        1 => 'Mon',
        2 => 'Tue',
        3 => 'Wed',
        4 => 'Thu',
        5 => 'Fri',
        6 => 'Sat'
    ];
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
                {!! Form::open([ 'route' => ['admin.owner.update', $owner->PK_NO], 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                @if($owner->USER_TYPE == 2)
                    @include('admin.owner._owner_edit');
                    @elseif($owner->USER_TYPE == 3 || $owner->USER_TYPE == 4)
                    @include('admin.owner._developer_edit')
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <a href="{{ route('admin.owner.list')}}">
                            <button type="button" class="btn btn-warning mr-1">
                                <i class="ft-x"></i> Cancel
                            </button>
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="la la-check-square-o"></i> Save
                        </button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

<!--push from page-->
@push('custom_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
    <script type="text/javascript" src="{{ asset('app-assets/pages/country.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
    <script src="{{asset('/assets/css/image_upload/image-uploader.min.js')}}"></script>
    <script src="{{asset('/assets/js/forms/datepicker/moment.min.js')}}"></script>
    <script src="{{asset('/assets/js/forms/datepicker/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        $('#imageFile').imageUploader();
        $('#bannerFile').imageUploader();
        $('#logoFile').imageUploader();

        $('.time').datetimepicker({
            format: 'hh:mm'
        });
    </script>
@endpush
