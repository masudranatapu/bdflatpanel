@extends('admin.layout.master')

@section('Web Info','active')

@section('title') General Web Info @endsection
@section('page-name') General Web Info @endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('payment.breadcrumb_title')  </a></li>
    <li class="breadcrumb-item active">@lang('general.general_sub_title')</li>
@endsection

@push('custom_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.css') }}">
    <link rel="stylesheet" type="text/css" href="{{asset('/assets/css/image_upload/image-uploader.min.css')}}">
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endpush

@push('custom_js')
    <!-- BEGIN: Data Table-->
    <script src="{{asset('/app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
    <script src="{{asset('/app-assets/js/scripts/tables/datatables/datatable-basic.js')}}"></script>
    <!-- END: Data Table-->
    <script src="{{asset('/assets/css/image_upload/image-uploader.min.js')}}"></script>
    <script>
        $('#headerLogo').imageUploader();
        $('#footerLogo').imageUploader();
        $('#appLogo').imageUploader();
        $('#metaImage').imageUploader();
        $('#favicon').imageUploader();
    </script>
@endpush

@php
    $webInfo = $data['webInfo'] ?? [];
@endphp

@section('content')
    <section id="basic-form-layouts" class="min-height">
        <div class="row match-height">
            <div class="col-md-12">
                <div class="card card-success min-height">
                    <div class="card-header">
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
                            {!! Form::open([ 'route' => 'admin.generalinfo.update', 'method' => 'post', 'class' => 'form-horizontal', 'files' => true , 'novalidate']) !!}
                            <div class="row">
                                <div class="col-md-12">
                                    <h2>General Information</h2>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('title', 'Title <span>*</span>', ['class' => 'label-title'], false) }}
                                        <div class="controls">
                                            {!! Form::text('title', old('title', $webInfo->TITLE ?? ''), ['id' => 'title', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Title']) !!}
                                            {!! $errors->first('title', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('meta_title', 'Meta Title <span>*</span>', ['class' => 'label-title'], false) }}
                                        <div class="controls">
                                            {!! Form::text('meta_title', old('meta_title', $webInfo->META_TITLE ?? '' ), ['id' => 'meta_title', 'class' => 'form-control', 'placeholder' => 'Meta Title']) !!}
                                            {!! $errors->first('meta_title', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('email_one', 'Email <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::email('email_one', old('email_one', $webInfo->EMAIL_1 ?? ''), ['id' => 'email_one', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Email']) !!}
                                            {!! $errors->first('email_one', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('email_two', 'Secondary Email', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::email('email_two', old('email_two', $webInfo->EMAIL_2 ?? ''), ['id' => 'email_two', 'class' => 'form-control', 'placeholder' => 'Email']) !!}
                                            {!! $errors->first('email_two', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('phone_one', 'Phone <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('phone_one', old('phone_one', $webInfo->PHONE_1 ?? ''), ['id' => 'phone_one', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Phone Number']) !!}
                                            {!! $errors->first('phone_one', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('phone_two', 'Secondary Phone', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('phone_two', old('phone_two', $webInfo->PHONE_2 ?? ''), ['id' => 'phone_two', 'class' => 'form-control', 'placeholder' => 'Phone Number']) !!}
                                            {!! $errors->first('phone_two', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('url', 'URL <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('url', old('url', $webInfo->URL ?? '' ), ['id' => 'url', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Phone Number']) !!}
                                            {!! $errors->first('url', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('meta_keywords', 'Meta Keywords', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('meta_keywords', old('meta_keywords', $webInfo->META_KEYWARDS ?? ''), ['id' => 'meta_keywords', 'class' => 'form-control', 'placeholder' => 'Meta Keywords']) !!}
                                            {!! $errors->first('meta_keywords', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {!! Form::label('meta_description', 'Meta Description', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('meta_description', old('meta_description', $webInfo->META_DESCRIPTION ?? ''), ['id' => 'meta_description', 'class' => 'form-control', 'placeholder' => 'Meta Description']) !!}
                                            {!! $errors->first('meta_description', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {{ Form::label('description', 'Description', ['class' => 'label-title']) }}
                                        <div class="controls">
                                            {!! Form::textarea('description', old('description', $webInfo->DESCRIPTION ?? ''), ['id' => 'description', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Title']) !!}
                                            {!! $errors->first('description', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {!! Form::label('hq_address', 'HQ Address <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('hq_address', old('hq_address', $webInfo->HQ_ADDRESS ?? ''), ['id' => 'hq_address', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'HQ Address']) !!}
                                            {!! $errors->first('hq_address', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {!! Form::label('copyright_text', 'Copyright Text <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('copyright_text', old('copyright_text', $webInfo->COPYRIGHT_TEXT ?? ''), ['id' => 'copyright_text', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Copyright Text']) !!}
                                            {!! $errors->first('copyright_text', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('feature_property_limit', 'Feature Property Limit <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('feature_property_limit', old('feature_property_limit', $webInfo->FEATURE_PROPERTY_LIMIT ?? ''), ['id' => 'feature_property_limit', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Feature Property Limit']) !!}
                                            {!! $errors->first('feature_property_limit', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('verified_property_limit', 'Verified Property Limit <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('verified_property_limit', old('verified_property_limit', $webInfo->VERIFIED_PROPERTY_LIMIT ?? ''), ['id' => 'verified_property_limit', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Verified Property Limit']) !!}
                                            {!! $errors->first('verified_property_limit', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('sale_property_limit', 'Sale Property Limit <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('sale_property_limit', old('sale_property_limit', $webInfo->SALE_PROPERTY_LIMIT ?? ''), ['id' => 'sale_property_limit', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Sale Property Limit']) !!}
                                            {!! $errors->first('sale_property_limit', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('rent_property_limit', 'Rent Property Limit <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('rent_property_limit', old('rent_property_limit', $webInfo->RENT_PROPERTY_LIMIT ?? ''), ['id' => 'rent_property_limit', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Rent Property Limit']) !!}
                                            {!! $errors->first('rent_property_limit', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('roommate_property_limit', 'Roommate Property Limit <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('roommate_property_limit', old('roommate_property_limit', $webInfo->ROOMMATE_PROPERTY_LIMIT ?? ''), ['id' => 'roommate_property_limit', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Roommate Property Limit']) !!}
                                            {!! $errors->first('roommate_property_limit', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('similar_property_limit', 'Similar Property Limit <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('similar_property_limit', old('similar_property_limit', $webInfo->SIMILAR_PROPERTY_LIMIT ?? ''), ['id' => 'similar_property_limit', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Similar Property Limit']) !!}
                                            {!! $errors->first('similar_property_limit', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('listing_lead_claimed_time', 'Listing Lead Claimed Time (Hours) <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('listing_lead_claimed_time', old('listing_lead_claimed_time', $webInfo->LISTING_LEAD_CLAIMED_TIME ?? ''), ['id' => 'listing_lead_claimed_time', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Listing Lead Claimed Time']) !!}
                                            {!! $errors->first('listing_lead_claimed_time', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('default_ci_price', 'Contact View Deafult Price <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('default_ci_price', old('default_ci_price', $webInfo->DEFAULT_CI_PRICE ?? ''), ['id' => 'default_ci_price', 'class' => 'form-control', 'data-validation-required-message' => 'This field is required', 'placeholder' => 'Listing Lead Claimed Time']) !!}
                                            {!! $errors->first('default_ci_price', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h2>Registration Bonus Amount</h2>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('seeker_bonus_amount', 'Seeker Bonus Amount <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::number('seeker_bonus_amount', old('seeker_bonus_amount', $webInfo->SEEKER_BONUS_BALANCE ?? ''), ['id' => 'seeker_bonus_amount', 'class' => 'form-control', 'placeholder' => 'Initial Bonus Balance For Seeker']) !!}
                                            {!! $errors->first('seeker_bonus_amount', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('owner_bonus_amount', 'Owner/Builder/Agency Bonus Amount <span>*</span>', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::number('owner_bonus_amount', old('owner_bonus_amount', $webInfo->OWNER_BONUS_BALANCE ?? ''), ['id' => 'owner_bonus_amount', 'class' => 'form-control', 'placeholder' => 'Initial Bonus Balance For Owner/Builder/Agency']) !!}
                                            {!! $errors->first('owner_bonus_amount', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h2>Social Media Links</h2>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('facebook_url', 'Facebook URL', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('facebook_url', old('facebook_url', $webInfo->FACEBOOK_URL ?? ''), ['id' => 'facebook_url', 'class' => 'form-control', 'placeholder' => 'Facebook URL']) !!}
                                            {!! $errors->first('facebook_url', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('twitter_url', 'Twitter URL', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('twitter_url', old('twitter_url', $webInfo->TWITTER_URL ?? ''), ['id' => 'twitter_url', 'class' => 'form-control', 'placeholder' => 'Twitter URL']) !!}
                                            {!! $errors->first('twitter_url', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('instagram_url', 'Instagram URL', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('instagram_url', old('instagram_url', $webInfo->INSTAGRAM_URL ?? ''), ['id' => 'instagram_url', 'class' => 'form-control', 'placeholder' => 'Instagram URL']) !!}
                                            {!! $errors->first('instagram_url', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('youtube_url', 'YouTube URL', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('youtube_url', old('youtube_url', $webInfo->YOUTUBE_URL ?? ''), ['id' => 'youtube_url', 'class' => 'form-control', 'placeholder' => 'YouTube URL']) !!}
                                            {!! $errors->first('youtube_url', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('pinterest_url', 'Pinterst URL', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('pinterest_url', old('pinterest_url', $webInfo->PINTEREST_URL ?? ''), ['id' => 'pinterest_url', 'class' => 'form-control', 'placeholder' => 'Pinterest URL']) !!}
                                            {!! $errors->first('pinterest_url', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('whatsapp', 'WhatsApp No.', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('whatsapp', old('whatsapp', $webInfo->WHATS_APP ?? ''), ['id' => 'whatsapp', 'class' => 'form-control', 'placeholder' => 'WhatsApp Number']) !!}
                                            {!! $errors->first('whatsapp', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h2>Apps Information</h2>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('facebook_app_id', 'FB App ID', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('facebook_app_id', old('facebook_app_id', $webInfo->FB_APP_ID ?? ''), ['id' => 'facebook_app_id', 'class' => 'form-control', 'placeholder' => 'FB App ID']) !!}
                                            {!! $errors->first('facebook_app_id', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('facebook_secret_id', 'FB Secret ID', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('facebook_secret_id', old('facebook_secret_id', $webInfo->FACEBOOK_SECRET_ID ?? ''), ['id' => 'facebook_secret_id', 'class' => 'form-control', 'placeholder' => 'FB Secret ID']) !!}
                                            {!! $errors->first('facebook_secret_id', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('google_app_id', 'Google App ID', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('google_app_id', old('google_app_id', $webInfo->GOOGLE_APP_ID ?? ''), ['id' => 'google_app_id', 'class' => 'form-control', 'placeholder' => 'Google App ID']) !!}
                                            {!! $errors->first('google_app_id', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('google_client_id', 'Google Client ID', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('google_client_id', old('google_client_id', $webInfo->GOOGLE_CLIENT_ID ?? '' ), ['id' => 'google_client_id', 'class' => 'form-control', 'placeholder' => 'Google Client ID']) !!}
                                            {!! $errors->first('google_client_id', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('google_client_secret', 'Google Client Secret', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('google_client_secret', old('google_client_secret', $webInfo->GOOGLE_CLIENT_SECRET ?? ''), ['id' => 'google_client_secret', 'class' => 'form-control', 'placeholder' => 'Google Client Secret']) !!}
                                            {!! $errors->first('google_client_secret', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('analytic_id', 'Analytics ID', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('analytic_id', old('analytic_id', $webInfo->ANALYTIC_ID ?? ''), ['id' => 'analytic_id', 'class' => 'form-control', 'placeholder' => 'Analytics ID']) !!}
                                            {!! $errors->first('analytic_id', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('language_id', 'Language ID', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::number('language_id', old('language_id', $webInfo->ANALYTIC_ID ?? ''), ['id' => 'language_id', 'class' => 'form-control', 'placeholder' => 'Language ID']) !!}
                                            {!! $errors->first('language_id', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h2>App Links</h2>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('android_app_link', 'Android App Link', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('android_app_link', old('android_app_link', $webInfo->ANDROID_APP_LINK ?? ''), ['id' => 'android_app_link', 'class' => 'form-control', 'placeholder' => 'Android App Link']) !!}
                                            {!! $errors->first('android_app_link', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('android_app_version', 'Android App Version', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('android_app_version', old('android_app_version', $webInfo->ANDROID_APP_VERSION ?? ''), ['id' => 'android_app_version', 'class' => 'form-control', 'placeholder' => 'Android App Version']) !!}
                                            {!! $errors->first('android_app_version', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('ios_app_link', 'iOS App Link', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('ios_app_link', old('ios_app_link', $webInfo->IPHONE_APP_LINK ?? ''), ['id' => 'ios_app_link', 'class' => 'form-control', 'placeholder' => 'iOS App Link']) !!}
                                            {!! $errors->first('ios_app_link', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('ios_app_version', 'iOS App Version', ['class' => 'label-title'], false) !!}
                                        <div class="controls">
                                            {!! Form::text('ios_app_version', old('ios_app_version', $webInfo->IPHONE_APP_VERSION ?? ''), ['id' => 'ios_app_version', 'class' => 'form-control', 'placeholder' => 'iOS App Version']) !!}
                                            {!! $errors->first('ios_app_version', '<label class="help-block text-danger">:message</label>') !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h2>Logo & Images</h2>
                                </div>
                                <div class="col-md-4">
                                    {{ Form::label('headerLogo', 'Header Logo', ['class' => 'label-title'], false) }}
                                    <div class="form-group">
                                        @if(isset($webInfo->HEADER_LOGO) && $webInfo->HEADER_LOGO != '' )
                                            <img src="{{ asset($webInfo->HEADER_LOGO) }}" alt=""
                                                 style="max-height: 150px;max-width: 100%">
                                        @endif
                                        <div class="controls">
                                            <div id="headerLogo" style="padding-top: .5rem;"></div>
                                        </div>
                                        {!! $errors->first('header_logo', '<label class="help-block text-danger">:message</label>') !!}
                                        {!! $errors->first('header_logo.0', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {{ Form::label('footerLogo', 'Footer Logo', ['class' => 'label-title'], false) }}
                                    <div class="form-group">
                                        @if(isset($webInfo->FOOTER_LOGO) && $webInfo->FOOTER_LOGO != '')
                                            <img src="{{ asset($webInfo->FOOTER_LOGO) }}" alt=""
                                                 style="max-height: 150px;max-width: 100%">
                                        @endif
                                        <div class="controls">
                                            <div id="footerLogo" style="padding-top: .5rem;"></div>
                                        </div>
                                        {!! $errors->first('footer_logo', '<label class="help-block text-danger">:message</label>') !!}
                                        {!! $errors->first('footer_logo.0', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {{ Form::label('appLogo', 'App Logo', ['class' => 'label-title'], false) }}
                                    <div class="form-group">
                                        @if(isset($webInfo->APP_LOGO) && $webInfo->APP_LOGO != '')
                                            <img src="{{ asset($webInfo->APP_LOGO) }}" alt=""
                                                 style="max-height: 150px;max-width: 100%">
                                        @endif
                                        <div class="controls">
                                            <div id="appLogo" style="padding-top: .5rem;"></div>
                                        </div>
                                        {!! $errors->first('app_logo', '<label class="help-block text-danger">:message</label>') !!}
                                        {!! $errors->first('app_logo.0', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {{ Form::label('metaImage', 'Meta Image', ['class' => 'label-title'], false) }}
                                    <div class="form-group">
                                        @if(isset($webInfo->META_IMAGE) && $webInfo->META_IMAGE != '')
                                            <img src="{{ asset($webInfo->META_IMAGE) }}" alt=""
                                                 style="max-height: 150px;max-width: 100%">
                                        @endif
                                        <div class="controls">
                                            <div id="metaImage" style="padding-top: .5rem;"></div>
                                        </div>
                                        {!! $errors->first('metaImage', '<label class="help-block text-danger">:message</label>') !!}
                                        {!! $errors->first('metaImage.0', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {{ Form::label('favicon', 'Favicon Icon', ['class' => 'label-title'], false) }}
                                    <div class="form-group">
                                        @if(isset($webInfo->FAVICON) && $webInfo->FAVICON != '')
                                            <img src="{{ asset($webInfo->FAVICON) }}" alt=""
                                                 style="max-height: 150px;max-width: 100%">
                                        @endif
                                        <div class="controls">
                                            <div id="favicon" style="padding-top: .5rem;"></div>
                                        </div>
                                        {!! $errors->first('favicon', '<label class="help-block text-danger">:message</label>') !!}
                                        {!! $errors->first('favicon.0', '<label class="help-block text-danger">:message</label>') !!}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h2>SMS Verification</h2>
                                    <label><input type="checkbox" value="1" name="owner_sms_verification" {{ $webInfo->OWNER_SMS_VERIFICATION == 1 ? 'checked' : '' }} /> For all property owner registration</label>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-actions text-center mt-3">
                                    <a href="{{route('admin.generalinfo')}}" class="btn btn-warning mr-1">
                                    <i class="ft-x"></i>@lang('form.btn_cancle')</a>
                                    <button type="submit" class="btn btn-primary"><i class="la la-check-square-o"></i>@lang('form.btn_save')</button>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('custom_js')
    <script type="text/javascript"
            src="{{ asset('app-assets/vendors/fileupload/bootstrap-fileupload.min.js') }}"></script>
@endpush('custom_js')
