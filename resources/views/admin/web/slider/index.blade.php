@extends('admin.layout.master')
@section('slider','active')
@section('title')
    Slider List
@endsection
@section('page-name')
    Slider List
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"> Slider List </a></li>
    <li class="breadcrumb-item active">Home Slider</li>
@endsection
@push('custom_css')
    <link href="http://arocrm.com/app-assets/icheck/square/yellow.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
@endpush

<?php

$rows = $data['slider'] ?? [];
$roles = userRolePermissionArray();
?>


@section('content')

    <section id="basic-form-layouts">
        <div class="row match-height min-height">
            <div class="col-md-12">
                <div class="card card-success">
                    <div class="card-header">
                        <a href="{{ route('web.home.slider.create') }}" class="btn btn-primary float-lg-right btn-sm">
                            <i class="la la-plus"></i> Create New</a>
                    </div>
                    <hr>
                    <div class="card-content collapse show">
                        <div class="card-body">

                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th>TITLE</th>
                                    <th>SUB TITLE</th>
                                    <th class="text-center">DESKTOP BANNER</th>
                                    <th class="text-center">MOBILE BANNER</th>
                                    <th class="text-center">IS FEATURE</th>
                                    <th class="text-center">ACTION</th>
                                </tr>
                                </thead>

                                <tbody>
                                @if(!empty($rows) && count($rows)>0)
                                    @foreach($rows as $key=>$row)
                                        <tr>
                                            <td class="text-center">{{$loop->index + 1}}</td>
                                            <td>{{ $row->TITLE }}</td>
                                            <td>{{ $row->SUBTITLE }}</td>
                                            <td class="text-center"><img src="{{ asset($row->BANNER) }}" alt=""
                                                                         width="100px;" class="img-fluid"></td>
                                            <td class="text-center"><img src="{{ asset($row->MOBILE_BANNER) }}" alt=""
                                                                         width="100px;" class="img-fluid"></td>
                                            <td class="text-center">
                                                <input type="checkbox" class="is_feature" data-id="{{$row->PK_NO}}"
                                                       @if ($row->IS_FEATURE) checked @endif>
                                            </td>
                                            <td class="text-center" style="width: 140px;">
                                                @if(hasAccessAbility('edit_slider', $roles))
                                                    <a href="{{ route('web.home.slider.edit', [$row->PK_NO]) }}"
                                                       class="btn btn-xs btn-info" title="EDIT"><i
                                                            class="la la-edit"></i></a>
                                                @endif
                                                @if(hasAccessAbility('delete_slider', $roles))
                                                    <a href="{{ route('web.home.slider.delete', [$row->PK_NO]) }}"
                                                       class="btn btn-xs btn-danger "
                                                       onclick="return confirm('Are you sure you want to delete?')"
                                                       title="DELETE"><i class="la la-trash"></i></a>
                                                @endif

                                            </td>
                                        </tr>
                                    @endforeach
                                @else

                                    <tr class="text-center">
                                        <td colspan="6">Data Not Found</td>
                                    </tr>

                                @endif
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @push('custom_js')
        <script src="http://arocrm.com/app-assets/icheck/icheck.min.js"></script>
        <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
        <script>
            $(document).ready(function () {
                $('.is_feature').iCheck({
                    checkboxClass: 'icheckbox_square-yellow',
                    radioClass: 'iradio_square-yellow',
                    increaseArea: '20%'
                });
                $('.is_feature').on('ifClicked', function (event) {
                    id = $(this).data('id');
                    $.ajax({
                        type: 'POST',
                        url: "{{ URL('web/slider/featureStatus') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'id': id
                        },
                        success: function (data) {

                            toastr.success('Home Slider', 'Feature Status Updated')


                        },
                    });
                });
                $('.is_feature').on('ifToggled', function (event) {
                    $(this).closest('tr').toggleClass('warning');
                });
            });
        </script>
    @endpush
@endsection
