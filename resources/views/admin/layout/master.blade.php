<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <title>@yield('title')</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/ico/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-assets/ico/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700" rel="stylesheet">
    <input type="hidden" name="base_url" id="base_url" value="{{url('/')}}">
    @include('admin.layout.includes.css')
    <link rel="stylesheet" type="text/css" href="{{ asset('/custom/css/custom_2.css') }}">
</head>
<!-- END: Head-->
<body class="vertical-layout vertical-menu-modern 2-columns   fixed-navbar" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
@php
        $roles = userRolePermissionArray()
@endphp
<!-- BEGIN: Header-->
<nav class="header-navbar navbar-expand-lg navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-dark navbar-shadow">
    <div class="navbar-wrapper">
        @include('admin.layout.top_nav')
    </div>
</nav>
<!-- END: Header-->

<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="main-menu-content">
        @include('admin.layout.left_sidebar')
    </div>
</div>

<!-- END: Main Menu-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 breadcrumb-new">
                <h3 class="content-header-title mb-0 d-inline-block">@yield('page-name')</h3>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                @include('admin.layout.flash')
            </div>
        </div>
        <!-- START: Content-->
        @yield('content')
        <!-- END: Content-->
    </div>
</div>
<!-- BEGIN: Footer-->
@include('admin.layout.footer')
<!-- END: Footer-->
@include('admin.layout.includes.js')
@include('admin.layout.includes.home_js')
</body>
<!-- END: Body-->

</html>
