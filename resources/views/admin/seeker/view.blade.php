@extends('admin.layout.master')

@section('Seeker Management','open')
@section('seeker_list','active')

@section('title')View Property Seeker @endsection
@section('page-name')View Property Seeker @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('agent.breadcrumb_title') </a></li>
    <li class="breadcrumb-item active">View Property Seeker</li>
@endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{asset('/custom/css/custom.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 28px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 2px;
            bottom: 3px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #4FD460;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #4FD460;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(20px);
            -ms-transform: translateX(26px);
            /* transform: translateX(26px); */
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 24px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            padding: 0px 0px !important;
            left: -6px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            margin-bottom: 3px !important;
        }

        .select2-container .select2-selection--multiple .select2-selection__rendered {
            margin-bottom: 0 !important;
        }

        .bedroom-select input[type="checkbox"] {
            display: none;
        }

        .bedroom-select label {
            font-size: 14px;
            color: #555;
            position: relative;
            padding: 0 17px 0px 23px;
            cursor: pointer;
        }


        .bedroom-select .checkmark {
            display: inline-block;
            width: 15px;
            height: 15px;
            background: #ffffff;
            border: 2px solid #d9d7d7;
            position: absolute;
            left: 0;
            top: 0;
            border-radius: 2px;
            transition: all 0.1s;
        }

        .bedroom-select input:checked + .checkmark:after {
            content: "";
            position: absolute;
            height: 7px;
            width: 14px;
            border-left: 3px solid #666ee8;
            border-bottom: 3px solid #666ee8;
            top: -1px;
            left: 1px;
            transform: rotate(-45deg);
        }

        .email-alert input[type="radio"] {
            display: none;
        }

        .email-alert input[type="radio"] + label {
            font-size: 14px;
            padding: 0 17px 0px 18px;
            position: relative;
            cursor: pointer;
        }

        .email-alert input[type="radio"] + label:before,
        .email-alert input[type="radio"] + label:after {
            position: absolute;
            top: 0;
            left: 0;
            content: "";
            width: 14px;
            height: 14px;
            border-radius: 50%;
            display: inline-block;
            background-color: transparent;
        }

        .email-alert input[type="radio"] + label:before {
            border: 2px solid #666ee8;
        }

        .email-alert input[type="radio"]:checked + label:after {
            border: 5px solid #666ee8;
        }
    </style>
@endpush

@push('custom_js')

    <!-- BEGIN: Data Table-->
    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
    <!-- END: Data Table-->

@endpush

@php
    $roles          = userRolePermissionArray();
    $cities         = $city ?? [];
    $row            = $row ?? [];
    $user_status    = \Config::get('static_array.user_status');

    if (!empty($row->BEDROOM)) {
        $bedrooms = json_decode($row->BEDROOM) ?? [];
    } else {
        $bedrooms = [];
    }

    if (!empty($row->PROPERTY_CONDITION)) {
        $prop_cond = json_decode($row->PROPERTY_CONDITION) ?? [];
    } else {
        $prop_cond = [];
    }
    $tabIndex = 0;
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
                <div class="form-body">
                    <h2 class="mb-2">Buyer Information</h2>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="d-block">User ID: <strong>{{$data->CODE}}</strong></label>
                                <label>Created Date:
                                    <strong>{{date('M d, Y',strtotime($data->CREATED_AT))}}</strong></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <p><span class="font-weight-bold">Name: </span>{{ $data->NAME ?? 'N/A' }}</p>
                            <p><span class="font-weight-bold">Email: </span>{{ $data->EMAIL ?? 'N/A' }}</p>
                            <p><span class="font-weight-bold">Address: </span>{{ $data->ADDRESS ?? 'N/A' }}</p>
                            <p><span class="font-weight-bold">Mobile: </span>{{ $data->MOBILE_NO ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h2 class="mb-2 mt-2">Property Requirements</h2>
                            <p><span class="font-weight-bold">City/Division: </span>{{ $cities[$row->F_CITY_NO ?? 0] ?? 'N/A' }}</p>
                            <p><span class="font-weight-bold">Area: </span></p>
                            <ul>
                                @foreach($cities as $key => $city)
                                    <li>{{ $city }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-12">
                            <p><span class="font-weight-bold">Property Looking For: </span>{{ ucwords($row->PROPERTY_FOR ?? '') }}</p>
                            <p><span class="font-weight-bold">Property Type: </span>{{ $property_types[$row->F_PROPERTY_TYPE_NO ?? 0] ?? 'N/A' }}</p>
                            <p><span class="font-weight-bold">Property Size: </span>Min: {{ $row->MIN_SIZE ?? 'N/A' }} Max: {{ $row->MAX_SIZE ?? 'N/A' }}</p>
                            <p><span class="font-weight-bold">Property Budget: </span>Min: {{ $row->MIN_BUDGET ?? 'N/A' }} Max: {{ $row->MAX_BUDGET ?? 'N/A' }}</p>
                            <p><span class="font-weight-bold">Bedroom: </span></p>
                            <ul>
                                @foreach($bedrooms as $bedroom)
                                    <li>{{ $bedroom }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <p><span class="font-weight-bold">Bathroom: </span></p>
                            <ul>
                                @foreach($prop_cond as $cond)
                                    <li>{{ ucwords($cond) }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-12">
                            <p><span class="font-weight-bold">Requirement Details: </span></p>
                            "{!! $row->REQUIREMENT_DETAILS ?? '' !!}"
                        </div>
                        <div class="col-12">
                            <p><span class="font-weight-bold">Preferred Time to Contact: </span>{{ $row->PREP_CONT_TIME ?? 'N/A' }}</p>
                            <p><span class="font-weight-bold">Email Alert: </span>{{ ucwords($row->EMAIL_ALERT ?? 'N/A') }}</p>
                            <p><span class="font-weight-bold">Verification Status: </span>{{ $row && $row->IS_VERIFIED == 1 ? 'Valid' : ($row && $row->IS_VERIFIED == 2 ? 'Invalid' : 'Pending') }}</p>
                            <p><span class="font-weight-bold">Account Status: </span>{{ $user_status[$data->STATUS] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('custom_js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.16.1/standard/ckeditor.js"></script>
    <script>
        //ck editor
        CKEDITOR.replace('requirement_details');
    </script>
    <script>
        $('.select2').select2();
        var base_url = $('#base_url').val();
        $("#any").on('click', function () {
            if ($(this).prop("checked") == true) {
                $("#1bed").prop("checked", false);
                $("#1bed").attr("disabled", true);

                $("#2bed").prop("checked", false);
                $("#2bed").attr("disabled", true);

                $("#3bed").prop("checked", false);
                $("#3bed").attr("disabled", true);

                $("#4plus").prop("checked", false);
                $("#4plus").attr("disabled", true);
            } else {
                $("#1bed").attr("disabled", false);
                $("#2bed").attr("disabled", false);
                $("#3bed").attr("disabled", false);
                $("#4plus").attr("disabled", false);
            }

        });

        let max_size = $("#maximum_size");
        let min_size = $("#minimum_size");

        let max_budget = $("#maximum_budget");
        let min_budget = $("#minimum_budget");

        $(document).ready(function () {
            min_size.attr('max', max_size.val());
            min_budget.attr('max', max_budget.val());
        });


        min_size.on('keyup', function () {
            max_size.val('');
            min_size.attr('max', max_size.val());
        });

        min_budget.on('keyup', function () {
            max_budget.val('');
            min_budget.attr('max', max_budget.val());
        });

        $("#cities").on("change", function () {
            let dis = $(this);
            $("#f_city_id").val(dis.val());
            let area = $('#area');
            area.empty();
            $('.select2').select2();

            var baseurl = `{{\Illuminate\Support\Facades\URL::to('/')}}`;
            $.ajax({
                url: baseurl + "/seeker/get_area/" + dis.val(),
                method: "GET",
                success: function (data) {
                    if (data.status == 'success') {
                        $.each(data.html, function (key, value) {
                            let option = new Option(value, key);
                            area.append(option);
                        });
                    } else {
                        alert('Something wrong');
                    }
                }
            });
        });

        $(".city_id").on('click', function () {
            var id = $(this).data('id');
            $("#f_city_id").val(id);
            $(".city_id").removeClass('active');
            $(this).addClass('active');

            $("#show_cityArea").text($(this).text());
            $.ajax({
                url: "property-requirements/get_area/" + id,
                method: 'GET',
                success: function (data) {
                    $("#area_title").css('display', 'block');
                    $("#show_areas").html(data.html);
                }
            });
        });

        $("#all_bd").on('click', function () {
            $("#show_cityArea").text('Select location / City');
        });

        $(document).on("click", ".area_select", function (event) {
            var old_text = $("#show_cityArea").text();
            $("#show_cityArea").text(old_text + ' > ' + $(this).text());
            $(this).addClass('active');
            $("#f_area_id").val($(this).data('id'));
            $(".close").trigger("click");
        });
    </script>

    <script>
        // modal control
        $(document).on('click', '.modalcategory .nav-link', function () {
            var id = $(this).data('id');
            $('.modalcategory').hide();
            $('.modalsubcategory').show();
            $('.backcategory').show();
            $('#city_title').text($(this).text());
            $("#area").empty();
            $('.select2').select2();

            $.ajax({
                type: 'GET',
                headers: {},
                data: {},
                url: base_url + "/property-requirements/get_area/" + id,
                async: true,
                beforeSend: function () {
                    $("body").css("cursor", "progress");
                },
                success: function (data) {
                    if (data.status == 'success') {
                        $.each(data.html, function (key, value) {
                            var option = new Option(value, key);
                            $("#area").append(option);
                        });
                    } else {
                        alert('Something wrong');
                    }
                },
                complete: function (data) {

                    $("body").css("cursor", "default");


                }
            });


        });

        $(document).on('click', '.backcategory', function () {
            $('.select2').select2();
            // $('.fstChoiceRemove').trigger('click');
            $('.modalsubcategory').hide();
            $('.modalcategory').show();
        });

        // $('#requirement_form').submit(function (e) {
        //     e.preventDefault();
        //     if ($('#area').val().length && $('#f_city_id').val()) {
        //         $('#requirement_form').submit();
        //     } else {
        //         toastr.error('Location is required')
        //     }
        // });


    </script>
@endpush
