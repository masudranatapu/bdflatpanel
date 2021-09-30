@extends('admin.layout.master')

@section('Payment','open')

@section('title') Payment details @endsection
@section('page-name') Payment details @endsection


@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
</li>
<li class="breadcrumb-item"><a href="{{ route('admin.invoice') }}"> Payment </a>
</li>
<li class="breadcrumb-item active">Payment details
</li>
@endsection

<!--push from page-->
@push('custom_css')

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.css')}}">
@endpush('custom_css')
<style>
    #scrollable-dropdown-menu .tt-menu {
        max-height: 260px;
        overflow-y: auto;
        width: 100%;
        border: 1px solid #333;
        border-radius: 5px;

    }

    .twitter-typeahead {
        display: block !important;
    }
    #indextable td{vertical-align: middle}
    .table-responsive {
    overflow: visible !important
    }
    .picker__holder{max-width: 320px !important;}
    .picker__holder table td{padding: 0px !important;}
    /* .paymentEditBtn
    .paymentUpdatefrm */
    .paymentUpdatefrm{display: none; max-width: 320px !important;}

</style>
<?php
$key = 0;
$roles = userRolePermissionArray();

?>

@section('content')
<div class="card card-success min-height">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-eye text-primary"></i>Payment</h4>

        @if($errors->any())
        {{ implode($errors->all(':message')) }}
        @endif
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
            <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm">
                <tbody>
                    <tr>

                        <td stye="width:33%">Form Account : {{ $data['data']->from_ix_account->BANK_ACC_NAME.' ('.$data['data']->from_ix_account->BANK_NAME.')' ?? '' }}</td>
                        <td stye="width:33%">Request At : {{ date('d-m-Y',strtotime($data['data']->SS_CREATED_ON)) ?? '' }}</td>
                    </tr>
                    <tr>

                        <td stye="width:33%">To Account : {{ $data['data']->to_ix_account->BANK_ACC_NAME.' ('.$data['data']->to_ix_account->BANK_NAME.')' ?? '' }}</td>
                        <td stye="width:33%">Accept By : {{ $data['data']->approveBy->USERNAME ?? '' }}</td>
                    </tr>
                    <tr>

                        <td stye="width:33%">Request Amount : {{ $data['data']->ENTERED_MR_AMOUNT ?? '' }}</td>
                        <td stye="width:33%">Approved Amount : {{ $data['data']->ACK_MR_AMOUNT ?? '' }}</td>

                    </tr>
                    <tr>

                        <td stye="width:33%">Request By : <span style="text-transform: uppercase">{{ $data['data']->entryBy->USERNAME ?? '' }}</span></td>
                        <td stye="width:33%">Status
                            @if($data['data']->IS_VERIFIED == 0)
                            <div class="badge badge-warning " title=" Pending">Not Verified</div>
                            @elseif($data['data']->IS_VERIFIED == 1)
                            <div class="badge badge-success " title="VERIFIED">Verified</div>
                            @elseif($data['data']->IS_VERIFIED == 2)
                            <div class="badge badge-info " title="CANCELLED">Cancelled</div>
                            @endif</td>

                    </tr>
                    <tr>
                        <td stye="width:33%">
                            Payment Method : {{ $data['data']->ACC_CUSTOMER_PAYMENT_METHOD ?? '' }}
                        </td>
                        <td stye="width:33%">
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>
            <label for="">NOTE</label>
            {!! Form::textarea('', $data['data']->NARRATION ?? '',[ 'class' => 'form-control mb-1', 'placeholder' =>
            'Note', 'id' => 'payment_note','rows' => 3,'readonly']) !!}
            @if(!empty($data['data']->ATTACHMENT_PATH))
            <?php
            $extension = pathinfo(storage_path($data['data']->ATTACHMENT_PATH), PATHINFO_EXTENSION);
            if ($extension == 'pdf') {
            ?>
            <a href="{{asset('/').$data['data']->ATTACHMENT_PATH}}" target="_blank">SHOW PDF</a>
            <?php }else{   ?>
            <a href="{{asset('/').$data['data']->ATTACHMENT_PATH}}" target="_blank"><img src="{{asset('/').$data['data']->ATTACHMENT_PATH}}" alt="Photo" class="img-fluid" height="150px" width="120px"/></a>
            <?php } ?>
            @endif
            <hr>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-success btn-min-width mr-1 mb-1"><i class="la la-backward" ></i> Back</a>
        </div>
    </div>

</div>
@endsection

<!--push from page-->
@push('custom_js')


<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>

<script>

    $('.pickadate').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
    });

    $(document).on('click','.paymentEditBtn', function(e){

        $('.paydate').hide();
        $('.paymentUpdatefrm').show();

    })

    $(document).on('click','.payment_date_close',function(e){
        $('.paymentUpdatefrm').hide();
        $('.paydate').show();

    })

    // .paymentEditBtn



</script>


@endpush('custom_js')
