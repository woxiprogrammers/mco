@extends('layout.master')
@section('title','Constro | Manage Category')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
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
                                    <h1>Category Management</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet light ">
                                            {!! csrf_field() !!}
                                            <div class="portlet-body">
                                                <div class="portlet-body">
                                                    <ul class="nav nav-tabs nav-tabs-lg">
                                                        <li class="active">
                                                            <a href="#mainCategoryFormTab" data-toggle="tab"> Main Category </a>
                                                        </li>
                                                        <li>
                                                            <a href="/drawing/category-management/sub-category-manage" > Sub Category </a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content">
                                                        <div class="tab-pane fade in active" id="mainCategoryFormTab">
                                                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-drawing-category'))
                                                                <div id="sample_editable_1_new" class="btn yellow" style="margin-bottom: 1%; margin-left: 89%">
                                                                    <a href="/drawing/category-management/create-main" style="color: white">
                                                                        <i class="fa fa-plus"></i> Main category
                                                                    </a>
                                                                </div>
                                                            @endif

                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="mainCategoryTable">
                                                                <thead>
                                                                <tr>
                                                                    <th style="width: 30%"> Sr. No. </th>
                                                                    <th> Name of Main Category </th>
                                                                    <th> Status </th>
                                                                    <th> Actions </th>
                                                                </tr>
                                                                <tr class="filter">
                                                                    <th style="width: 30%"> <input type="text" class="form-control form-filter" name="search_sr" readonly> </th>
                                                                    <th> <input type="text" class="form-control form-filter" name="search_main_category" readonly> </th>
                                                                    <th>  </th>
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
            </div>
        </div>
    </div>
        @endsection
        @section('javascript')
            <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
            <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
            <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
            <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
            <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
            <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
            <script src="/assets/custom/Drawing/main-category/manage-datatable.js" type="text/javascript"></script>
            <script>
                $(document).ready(function() {
                    $('#mainCategoryTable').DataTable();
                });
            </script>
        @endsection
