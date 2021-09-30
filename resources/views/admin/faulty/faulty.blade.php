@extends('admin.layout.master')

{{-- @section('Product Management','open')
@section('list_order','active') --}}


@section('title') Lost Product @endsection
@section('page-name') Lost Product @endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('order.breadcrumb_dashboard_title') </a></li>
<li class="breadcrumb-item active">Lost Product </li>
@endsection

@push('custom_css')
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/custom.css')}}">

<style>
#warehouse td {
    border: none;
    border-bottom: 1px solid #333;
    font-size: 12px;
    font-weight: normal;
    padding-bottom: 7px;
    padding-bottom: 15px;
}
#warehouse tr{
    margin-bottom: 2px;
    display: block;
}
#book_qty th {
    border: none;
    /* border-bottom: 1px solid #333; */
    font-size: 12px;
    font-weight: normal;
    padding-bottom: 5px;
    padding-top: 0;
}
#faulty_table td{
    border-top: none
}
</style>

@endpush('custom_css')

@section('content')
<div class="card card-success min-height">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-plus text-primary"></i> Lost Product</h4>
        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
        </div>
    </div>
    <div class="card-content collapse show">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-body" id="order_form">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-borde alt-pagination table-sm" id="faulty_table">
                                <thead>
                                    <tr>
                                        <th style="width: 200px">@lang('tablehead.image')</th>
                                        <th style="width: 200px">@lang('tablehead.product_name')</th>
                                        <th class="" style="width: 70px;">@lang('tablehead.warehouse')</th>
                                        <th class="" style="width: 70px;">SS Cost</th>
                                        <th class="" style="width: 70px;">SM Cost</th>
                                        <th class="" style="width: 30px;text-align: center">Ait Freight</th>
                                        <th class="" style="width: 30px;text-align: center">Sea Freight</th>
                                        <th class="" style="width: 10px;">Regular Price</th>
                                        <th class="" style="width: 10px;">Ins Price</th>
                                        <th class="checkBox" style="width: 10px;">Invoice</th>
                                        <th class="checkBox" style="width: 10px;">Faulty</th>
                                    </tr>
                                </thead>
                                <tbody id="append_tr">
                                    @if($data && count($data) > 0 )
                                    @foreach($data as $key => $val)

                                        <?= $val->product_info; ?>

                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
{{-- @include('admin.order._modal_html') --}}
@endsection
<!--push from page-->
@push('custom_js')
<script type="text/javascript" src="{{ asset('app-assets/js/common.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script>
    // $('[id*=checkbox_of_faulty]').on('ifChanged', function() {
$(document).on('click','[id*=checkbox_of_faulty]', function(){
    if(confirm('Are you sure ?')){
        var pk_no        = $(this).data('pk_no');
        var get_url      = $('#base_url').val();
        if($(this).is(":checked")) {
            var booking_no = $(this).data('booking_no');
            if (booking_no > 0) {
                alert('There is a booking/order in this product !');
                // $(this).iCheck('uncheck');
                $(this).prop("checked", false);
            }else{
                $.ajax({
                    type:'get',
                    url:get_url+'/faulty-checker/'+pk_no,

                    async :true,
                    dataType: 'json',
                    beforeSend: function () {
                        $("body").css("cursor", "progress");
                    },
                    success: function (data) {
                        if (data == 1) {
                            toastr.success('Product Is Marked As Faulty !', 'Product Faulty Marked !');
                            $('[id=checkbox_of_faulty'+pk_no+']').prop("disabled", true);
                        }else{
                            toastr.danger('Something is wrong !', 'Please Try Again !');
                        }
                    },
                    complete: function (data) {
                        $("body").css("cursor", "default");
                    }
                });
            }
        }else{
            // $(this).iCheck('uncheck');
            $(this).prop("checked", false);
        }
    }else{
        // $(this).iCheck('uncheck');
        $(this).prop("checked", false);
    }
});
</script>
@endpush('custom_js')
