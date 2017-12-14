<?php
/**
 * Created by Ameya Joshi.
 * Date: 9/12/17
 * Time: 12:02 PM
 */
?>
@extends('layout.master')
@section('title','Constro | Manage checklist')
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
                                    <h1>Manage Checklist Project Site Assignments</h1>
                                </div>
                                <div id="sample_editable_1_new" class="btn yellow pull-right" style="margin-top: 1%">
                                    <a href="/checklist/site-assignment/create" style="color: white"><i class="fa fa-plus"></i> Checklist Site Assignment</a>
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
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="checklistSiteAssignmentTable">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 10%"> ID </th>
                                                        <th>
                                                            Project Site
                                                        </th>
                                                        <th> Main-Category Name </th>
                                                        <th> Sub-Category Name </th>
                                                        <th>Title</th>
                                                        <th> No. Of Checkpoints </th>
                                                        <th> Actions </th>
                                                    </tr>
                                                    {{--<tr class="filter">
                                                        <th style="width: 10%"> <input type="text" class="form-control form-filter" name="search_id"> </th>
                                                        <th> <input type="text" class="form-control form-filter" name="search_cub_category" readonly> </th>
                                                        <th> <input type="text" class="form-control form-filter" name="search_no_of_checkpoints" readonly> </th>
                                                        <th> <input type="text" class="form-control form-filter" name="search_status" readonly> </th>
                                                        <th>
                                                            <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                            <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                        </th>
                                                    </tr>--}}
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
    <link rel="stylesheet"  href="/assets/global/css/app.css"/>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/checklist/structure-manage-datatable.js" type="application/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#checkListTable').DataTable();
            ChecklistSiteAssignmentListing.init();
        });
    </script>
@endsection

