@extends('admin.layout.master')
@push('custom_css')
@endpush('custom_css')
@section('mail Management','open')
@section('mail','active')
@section('title') Mail Configuring @endsection
@section('page-name') Mail Configuring @endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"> Mail </a></li>
<li class="breadcrumb-item active">Mail Configuring </li>
@endsection
<?php
   $roles   = userRolePermissionArray();
   $mail    = $mail ?? [];
   ?>
@section('content')
<div class="content-body">
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">SMTP Settings</h5>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('env_key_update.update') }}" method="POST">
                    @csrf
                    <div class="form-group row">
                        <input type="hidden" name="types[]" value="MAIL_DRIVER">
                        <label class="col-md-3 col-form-label">Type</label>
                        <div class="col-md-7">
                            <select class="form-control aiz-selectpicker mb-2 mb-md-0" name="MAIL_DRIVER" onchange="checkMailDriver()">
                                <option value="sendmail" @if (env('MAIL_DRIVER') == "sendmail") selected @endif>Sendmail</option>
                                <option value="smtp" @if (env('MAIL_DRIVER') == "smtp") selected @endif>SMTP</option>
                                <option value="mailgun" @if (env('MAIL_DRIVER') == "mailgun") selected @endif>Mailgun</option>
                            </select>
                        </div>
                    </div>
                    <div id="smtp">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MAIL_HOST">
                            <div class="col-md-3">
                                <label class="col-from-label">MAIL HOST</label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="MAIL_HOST" value="{{  env('MAIL_HOST') }}" placeholder="MAIL HOST">
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MAIL_PORT">
                            <div class="col-md-3">
                                <label class="col-from-label">MAIL PORT</label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="MAIL_PORT" value="{{  env('MAIL_PORT') }}" placeholder="MAIL PORT">
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MAIL_USERNAME">
                            <div class="col-md-3">
                                <label class="col-from-label">MAIL USERNAME</label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="MAIL_USERNAME" value="{{  env('MAIL_USERNAME') }}" placeholder="MAIL USERNAME">
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MAIL_PASSWORD">
                            <div class="col-md-3">
                                <label class="col-from-label">MAIL PASSWORD</label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="MAIL_PASSWORD" value="{{  env('MAIL_PASSWORD') }}" placeholder="MAIL PASSWORD">
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MAIL_ENCRYPTION">
                            <div class="col-md-3">
                                <label class="col-from-label">MAIL ENCRYPTION</label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="MAIL_ENCRYPTION" value="{{  env('MAIL_ENCRYPTION') }}" placeholder="MAIL ENCRYPTION">
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MAIL_FROM_ADDRESS">
                            <div class="col-md-3">
                                <label class="col-from-label">MAIL FROM ADDRESS</label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="MAIL_FROM_ADDRESS" value="{{  env('MAIL_FROM_ADDRESS') }}" placeholder="MAIL FROM ADDRESS">
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MAIL_FROM_NAME">
                            <div class="col-md-3">
                                <label class="col-from-label">MAIL FROM NAME</label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="MAIL_FROM_NAME" value="{{  env('MAIL_FROM_NAME') }}" placeholder="MAIL FROM NAME">
                            </div>
                        </div>
                    </div>
                    <div id="mailgun">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MAILGUN_DOMAIN">
                            <div class="col-md-3">
                                <label class="col-from-label">MAILGUN DOMAIN</label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="MAILGUN_DOMAIN" value="{{  env('MAILGUN_DOMAIN') }}" placeholder="MAILGUN DOMAIN">
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MAILGUN_SECRET">
                            <div class="col-md-3">
                                <label class="col-from-label">MAILGUN SECRET</label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control" name="MAILGUN_SECRET" value="{{  env('MAILGUN_SECRET') }}" placeholder="MAILGUN SECRET">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-gray-light">
            <div class="card-header">
                <h5 class="mb-0 h6">Instruction</h5>
            </div>
            <div class="card-body">
                <p class="text-danger">Please be carefull when you are configuring SMTP.</p>
                <h6 class="text-muted">For Non-SSL</h6>
                <ul class="list-group">
                    <li class="list-group-item text-dark">Select sendmail for Mail Driver if you face any issue after configuring smtp as Mail Driver </li>
                    <li class="list-group-item text-dark">Set Mail Host according to your server Mail Client Manual Settings</li>
                    <li class="list-group-item text-dark">Set Mail port as 587</li>
                    <li class="list-group-item text-dark">Set Mail Encryption as ssl if you face issue with tls</li>
                </ul>
                <br>
                <h6 class="text-muted">For SSL</h6>
                <ul class="list-group mar-no">
                    <li class="list-group-item text-dark">Select sendmail for Mail Driver if you face any issue after configuring smtp as Mail Driver </li>
                    <li class="list-group-item text-dark">Set Mail Host according to your server Mail Client Manual Settings</li>
                    <li class="list-group-item text-dark">Set Mail port as 465</li>
                    <li class="list-group-item text-dark">Set Mail Encryption as ssl</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
@endsection
@push('custom_js')
@endpush('custom_js')
