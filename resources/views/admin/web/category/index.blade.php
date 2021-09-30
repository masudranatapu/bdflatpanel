@extends('admin.layout.master')
@section('blog-category','active')
@section('title')
Category List
@endsection
@section('page-name')
Category List
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"> Category List </a></li>
    <li class="breadcrumb-item active">Web Category</li>
@endsection
@push('custom_css')
<link href="http://arocrm.com/app-assets/icheck/square/yellow.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{asset('/app-assets/vendors/css/extensions/toastr.css')}}">
@endpush

<?php
$rows = $data['category'] ?? [];
$roles = userRolePermissionArray();
?>

@section('content')
<section id="basic-form-layouts">
    <div class="row match-height min-height">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header">
                    <a href="{{ route('web.blog.category.create') }}" class="btn btn-primary float-lg-right btn-sm"> <i class="la la-plus"></i> Create New</a>
                </div>
                <hr>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th>Title</th>
                                    <th class="text-center">Banner</th>
                                    <th class="text-center">Is Active</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($rows) && count($rows)>0)
                                @foreach($rows as $key=>$row)
                                <tr>
                                    <td class="text-center">{{$loop->index + 1}}</td>
                                    <td>{{ $row->NAME }}</td>
                                    <td class="text-center"><img src="{{ asset($row->BANNER) }}" alt="" width="100px;" class="img-fluid"></td>
                                    <td class="text-center">
                                        <input type="checkbox" class="is_feature" data-id="{{$row->PK_NO}}" @if ($row->IS_ACTIVE) checked @endif>
                                    </td>
                                      <td class="text-center" style="width: 140px;">
                                        @if(hasAccessAbility('edit_blog', $roles))
                                        <a href="{{ route('web.blog.category.edit', [$row->PK_NO]) }}" class="btn btn-xs btn-info" title="EDIT"><i class="la la-edit"></i></a>
                                        @endif
                                        @if(hasAccessAbility('delete_blog', $roles))
                                        <a href="{{ route('web.blog.category.delete', [$row->PK_NO]) }}" class="btn btn-xs btn-danger " onclick="return confirm('Are you sure you want to delete?')" title="DELETE"><i class="la la-trash"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @else

                                <tr class="alert">
                                    <td rowspan="5">Data Not Found</td>
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
    $(document).ready(function(){
        $('.is_feature').iCheck({
            checkboxClass: 'icheckbox_square-yellow',
            radioClass: 'iradio_square-yellow',
            increaseArea: '20%'
        });
        $('.is_feature').on('ifClicked', function(event){
            id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: "{{ URL('web/slider/featureStatus') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'id': id
                },
                success: function(data) {

                    toastr.success('Home Slider', 'Feature Status Updated')


                },
            });
        });
        $('.is_feature').on('ifToggled', function(event) {
            $(this).closest('tr').toggleClass('warning');
        });
    });
</script>
@endpush
@endsection
