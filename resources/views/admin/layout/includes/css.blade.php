
<!-- BEGIN: Vendor CSS-->
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/vendors.min.css')}}">
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}"> --}}
<!-- END: Vendor CSS-->
{{-- Font Awsome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />
<!-- BEGIN: Theme CSS-->
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/css/bootstrap.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/css/bootstrap-extended.css')}}">
{{-- <link rel="stylesheet" type="text/css" href="{{asset('/app-assets/css/colors.css')}}"> --}}
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/css/components.css')}}">
{{-- <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}"> --}}
{{-- <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/colors/palette-gradient.css')}}"> --}}
<!-- END: Theme CSS-->
<!-- BEGIN: Page CSS-->
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/css/core/menu/menu-types/vertical-menu-modern.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/css/core/colors/palette-gradient.css')}}">
{{-- <link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/charts/jquery-jvectormap-2.0.3.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/charts/morris.css')}}"> --}}
{{-- Simple Line Icons --}}
{{-- <link rel="stylesheet" type="text/css" href="{{asset('/app-assets/fonts/simple-line-icons/style.css')}}"> --}}
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/css/plugins/forms/validation/form-validation.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/css/plugins/forms/checkboxes-radios.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('/custom/css/custom.css') }}">
<!-- END: Page CSS-->
{{-- <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script> --}}
{{-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> --}}
<script src="{{ asset('app-assets/jquery-3.2.1.min.js') }}"></script>
<!-- BEGIN: Custom CSS-->
{{--    <link rel="stylesheet" type="text/css" href="../../../assets/css/style.css">--}}
<!-- END: Custom CSS-->
@stack('custom_css')
