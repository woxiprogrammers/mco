@extends('layout.master')
@section('title','Constro | Manage Materials')
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="page-title">
                                            <h1>Manage Category</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        {!! csrf_field() !!}
                                        <div class="tabbable-custom nav-justified">
                                            <ul class="nav nav-tabs nav-justified">
                                                <li class="active" >
                                                    <a href="#main-category-div" data-toggle="tab" style="font-size: 18px"><b>Main Category</b>  </a>
                                                </li>
                                                <li >
                                                    <a href="/awareness/category-management/sub-category-manage"  style="font-size: 14px"> <b>Sub Category</b>  </a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="main-category-div">
                                                    <br>
                                                    <div class="btn-group"  style="float: right;">
                                                        <div id="sample_editable_1_new" class="btn yellow" ><a href="/awareness/category-management/main-category-create" style="color: white">                                         <i class="fa fa-plus"></i>
                                                                Main Category
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="table-container" style="margin-top:5%">
                                                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="mainCategoryTable">
                                                            <thead>
                                                            <tr>
                                                                <th> Sr No </th>
                                                                <th> Main Category Name </th>
                                                                <th> Status </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th></th>
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
            <script src="/assets/custom/awareness/category-management/main-category/manage-datatables.js" type="text/javascript"></script>
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#mainCategoryTable').DataTable();
                })
            </script>
@endsection