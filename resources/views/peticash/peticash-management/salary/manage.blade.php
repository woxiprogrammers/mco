<?php
/**
 * Created by Ameya Joshi.
 * Date: 5/12/17
 * Time: 11:38 AM
 */
?>
@extends('layout.master')
@section('title','Constro | Manage Peticash Purchase')
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
                                    <h1>Manage Inventory</h1>
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
                                                    <div class="table-container">
                                                        <div class="table-actions-wrapper right">
                                                            <span> </span>
                                                            <select class="table-group-action-input form-control input-inline input-small input-sm">
                                                                <option value="">Select...</option>
                                                                <option value="Cancel">Approve</option>
                                                                <option value="Cancel">Disapprove</option>
                                                            </select>
                                                            <button class="btn btn-sm green table-group-action-submit">
                                                                <i class="fa fa-check"></i> Submit</button>
                                                        </div>
                                                        <table class="table table-striped table-bordered table-hover order-column" id="inventoryListingTable">
                                                            <thead>
                                                            <tr>
                                                                <th> Project </th>
                                                                <th> Material Name </th>
                                                                <th> In</th>
                                                                <th> Out </th>
                                                                <th> Available  </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th> <input type="text" class="form-control form-filter" name="search_project" readonly> </th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_name" readonly> </th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_status" readonly> </th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_created_on" readonly> </th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_created_on" readonly> </th>
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
@endsection


