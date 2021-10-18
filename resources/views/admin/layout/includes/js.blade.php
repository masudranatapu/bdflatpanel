{{--<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>--}}
<!-- BEGIN: Vendor JS-->
<script src="{{asset('/app-assets/vendors/js/vendors.js')}}"></script>
<!-- BEGIN Vendor JS-->
<!-- BEGIN: Page Vendor JS-->
{{-- <script src="{{asset('/app-assets/data/jvector/visitor-data.js')}}"></script> --}}
<script src="{{asset('/app-assets/vendors/js/forms/icheck/icheck.min.js')}}"></script>
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
<script src="{{asset('/app-assets/js/core/app-menu.js')}}"></script>
<script src="{{asset('/app-assets/js/core/app.js')}}"></script>
<!-- END: Theme JS-->
<script src="{{asset('/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
<!-- BEGIN: Page JS-->
{{-- <!--script-- src="{{asset('/app-assets/js/scripts/pages/dashboard-sales.js')}}"></!--script--> --}}
<script src="{{asset('/app-assets/js/scripts/forms/validation/form-validation.js')}}"></script>
<script src="{{asset('/app-assets/js/scripts/forms/checkbox-radio.js')}}"></script>
<script src="{{asset('/app-assets/js/common.js')}}"></script>
<!-- END: Page JS-->
<script>
    $(':input[type=number]').on('wheel',function(e){ $(this).blur(); });
</script>


