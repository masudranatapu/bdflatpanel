@extends('admin.layout.master')
@section('product inventory','active')
@section('title')
    Inventory
@endsection
@section('page-name')
    Inventory List
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Inventory
    </li>
@endsection
@section('content')
    <!-- Alternative pagination table -->
    <div class="content-body min-height">
        <section id="pagination">
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <div class="form-group">
                                <a class="text-white" href="{{ route('product.inventory.create')}}">
                                    <button type="button" class="btn btn-sm btn-primary">
                                        <i class="ft-user-plus"></i> Create Inventory
                                    </button>
                                </a>
                            </div>
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
                            <div class="card-body card-dashboard text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered alt-pagination" id="indextable">
                                        <thead>
                                        <tr>
                                            <th>Sl.</th>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Location</th>
                                            <th>Address</th>
                                            <th>Manager</th>
                                            <th>Phone No</th>
                                            <th>Country</th>
                                            <th>Country No</th>

                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                            <tr>
                                                <td>1</td>
                                                <td>Samsung</td>
                                                <td>2564</td>
                                                <td>USA</td>
                                                <td>Demo/11/A Banely Street.</td>
                                                <td>Kamrul Haque</td>
                                                <td>016456789</td>
                                                <td>USA</td>
                                                <td>256</td>
                                                <td>
                                                    <a href="#">
                                                        <button type="button"
                                                                class="btn btn-sm btn-primary mr-1"><i
                                                                class="la la-edit"></i>
                                                        </button>
                                                    </a>
                                                    <a href="#">
                                                        <button type="button"
                                                                class="btn btn-sm btn-danger mr-1"><i
                                                                class="la la-trash"></i>
                                                        </button>
                                                    </a>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!--/ Alternative pagination table -->
@endsection
