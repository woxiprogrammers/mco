@extends('layout.master')
@section('title','Constro | Manage Vendor')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>

    <!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="page-wrapper-row full-height">
            <div class="page-wrapper-middle">
                <!-- BEGIN CONTAINER -->
                <div class="page-container">
                    <!-- BEGIN CONTENT -->
                    <div class="page-content-wrapper">
                        <div class="page-head">
                            <div class="container">
                                <!-- BEGIN PAGE TITLE -->
                                <div class="page-title">
                                    <h1>Manage Vendors</h1>
                                </div>
                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-manage-user'))
                                    <div id="sample_editable_1_new" class="btn yellow" style="margin-top: 1%; margin-left: 78%">
                                        <a href="/vendors/create" style="color: white">
                                            <i class="fa fa-plus"></i> Vendor
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="page-content">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet light ">
                                            {!! csrf_field() !!}
                                            <div class="portlet-body">
                                                <div class="table-toolbar">
                                                    <div class="row" style="text-align: right">
                                                        <div class="col-md-12">
                                                            <div class="btn-group">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="vendorTable">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 30%"> Vendor Comapny Name</th>
                                                        <th > Vendor Name </th>
                                                        <th> Mobile Number </th>
                                                        <th> Alt Mobile Number </th>
                                                        <th> GST Number</th>
                                                        <th> Status </th>
                                                        <th> Actions </th>
                                                    </tr>
                                                    <tr class="filter">
                                                        <th style="width: 30%"> <input type="text" class="form-control form-filter" name="search_company"> </th>
                                                        <th> <input type="text" class="form-control form-filter" name="search_name"> </th>
                                                        <th> {{--<input type="text" class="form-control form-filter" name="search_mobile" readonly>--}} </th>
                                                        <th> {{--<input type="text" class="form-control form-filter" name="search_status" readonly>--}} </th>
                                                        <th></th>
                                                        <th></th>
                                                        <th>
                                                            <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                            <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

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
        </div>
    </div>
@endsection

@section('javascript')
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/admin/vendor/manage-datatable.js" type="text/javascript"></script>
   <script>
        $(document).ready(function() {
            $('#vendorTable').DataTable();
            $(".form-filter").on('keyup', function(){
                $(".filter-submit").trigger('click');
            });
        });
    </script>
@endsection
