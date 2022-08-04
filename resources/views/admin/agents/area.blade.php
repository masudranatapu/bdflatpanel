@extends('admin.layout.master')

@section('title')
    Agent Area
@endsection

@section('page-name')
    Agent Area
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Set Agent Area </li>
@endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{asset('/custom/css/custom.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{asset('/assets/css/forms/datepicker/bootstrap-datetimepicker.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/assets/css/image_upload/image-uploader.min.css')}}">
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        .bootstrap-datetimepicker-widget {
            background: #404040;
            /*color: #f4f4f4;*/
        }
        .bootstrap-datetimepicker-widget .fa {
            color: #f4f4f4;
        }
        a.ui-state-default{background-color:red!important}
        #scrollable-dropdown-menu2 .tt-menu{max-height:260px;overflow-y:auto;width:100%;border:1px solid #333;border-radius:5px}.twitter-typeahead{display:block!important}.tt-hint{color:#999!important}
    </style>
@endpush

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
            @if($ss_agent_areas)
                <div class="card-body">
                    <form action="{{ route('admin.agentarea.update', $ss_agent_areas->PK_NO) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4"><h3>Agent Name</h3></div>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="{{$usersname}}">
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-4"><h3>Select Agent Area</h3></div>
                            <div class="col-md-8">
                                <select name="F_AREA_NO[]" class="form-control select2" multiple>
                                    @foreach($ss_areas as $ss_area)
                                        <option
                                            @if ($ss_agent_areas->F_AREA_NO)
                                                @php
                                                    $FAREANO = explode(',', $ss_agent_areas->F_AREA_NO);
                                                @endphp
                                                @foreach($FAREANO as $FAREA)
                                                    @if($ss_area->PK_NO == $FAREA) selected @endif
                                                @endforeach
                                            @endif
                                            value="{{$ss_area->PK_NO}}">
                                            {{$ss_area->AREA_NAME}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4"></div>
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-info">Update Area</button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <div class="card-body">
                    <form action="{{ route('admin.agentarea.store') }}" method="POST">
                        @csrf
                        <input type="hidden" value="{{$users}}" name="user_id">
                        <div class="row">
                            <div class="col-md-4"><h3>Agent Name</h3></div>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="{{$usersname}}">
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-4"><h3>Select Agent Area</h3></div>
                            <div class="col-md-8">
                                <select name="F_AREA_NO[]" class="form-control select2" multiple>
                                    @foreach($ss_areas as $ss_area)
                                        <option value="{{$ss_area->PK_NO }}">{{$ss_area->AREA_NAME}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4"></div>
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-info">Submit Area</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
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
            format: 'hh:mm',
            useCurrent: false,
            showTodayButton: true,
            showClear: true,
            toolbarPlacement: 'bottom',
            sideBySide: true,
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-chevron-left",
                next: "fa fa-chevron-right",
                today: "fa fa-clock",
                clear: "fa fa-trash"
            }
        });
    </script>
@endpush
