<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Welcome to Azuramart">
    <meta name="keywords" content="Welcome to Azuramart">
    <meta name="author" content="PIXINVENT">
    <title>Admin | Register</title>
    <link rel="apple-touch-icon" href="{{asset('app-assets/images/ico/apple-icon-120.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('/app-assets/images/ico/favicon.ico')}}">
    @include('admin.layout.includes.css');
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700" rel="stylesheet">
</head>
<!-- END: Head-->
@push('custom_css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}">
@endpush
<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern 1-column  bg-cyan bg-lighten-2 blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section class="row flexbox-container">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-lg-4 col-md-8 col-10 box-shadow-2 p-0">
                        <div class="card border-grey border-lighten-3 px-1 py-1 m-0">
                            <div class="card-header border-0 pb-0">
                                <div class="card-title text-center">
                                    <img src="{{asset('/app-assets/images/logo/logo-dark.png')}}" alt="branding logo">
                                </div>
                            </div>
                            <div class="card-content">
                                <p class="card-subtitle line-on-side text-muted text-center font-small-3 mx-2 my-1"><span>Using
                                            Email</span></p>
                                <div class="card-body pt-0">
                                    <form class="form-horizontal" action="index.html">
                                        <fieldset class="form-group floating-label-form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <fieldset class="mt-1">
                                                        <h5>Fast Name</h5>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control card-expiry" name="first_name" id="card-expiry" placeholder="Card Expiry Date">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-6">
                                                    <fieldset class="mt-1">
                                                        <h5>Last Name</h5>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control card-cvc" name="first_name"   id="card-cvc" maxlength="16" placeholder="Card CVC Number">
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset class="form-group floating-label-form-group">
                                            <label for="user-email">Designation</label>
                                            <input type="text" class="form-control"  placeholder="Your Designantion" name="designation">
                                        </fieldset>
                                        <fieldset class="form-group floating-label-form-group mb-1">
                                            <label for="user-password">Enter Password</label>
                                            <input type="password" class="form-control" id="user-password" placeholder="Enter Password">
                                        </fieldset>
                                        <div class="form-group row">
                                            <div class="col-sm-6 col-12 text-center text-sm-left pr-0">
                                                <fieldset>
                                                    <input type="checkbox" id="remember-me" class="chk-remember">
                                                    <label for="remember-me"> Remember Me</label>
                                                </fieldset>
                                            </div>
                                            <div class="col-sm-6 col-12 float-sm-left text-center text-sm-right"><a href="recover-password.html" class="card-link">Forgot Password?</a></div>
                                        </div>
                                        <button type="submit" class="btn btn-info btn-block"><i class="la la-user"></i> Register</button>
                                    </form>
                                </div>
                                <div class="card-body pt-0">
                                    <a href="login-with-bg.html" class="btn btn-danger btn-block"><i class="ft-unlock"></i> Login</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>
<!-- END: Content-->


<!-- BEGIN: Vendor JS-->
<script src="{{asset('/app-assets/vendors/js/vendors.min.js')}}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{asset('/app-assets/vendors/js/forms/icheck/icheck.min.js')}}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{asset('/app-assets/js/core/app-menu.js')}}"></script>
<script src="{{asset('/app-assets/js/core/app.js')}}"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<script src="{{asset('/app-assets/js/scripts/forms/form-login-register.js')}}"></script>
<!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>
