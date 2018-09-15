<?php
    /**
     * Created by Harsha.
     * User: harsha
     * Date: 14/9/18
     * Time: 5:13 PM
     */?>

@extends('layout.master')
@section('title','Constro | Manage Units')
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
                                    <h1>Manage Manisha Construction</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            <div class="container">
                                <div class="row">
                                    @include('partials.common.messages')
                                    <div class="col-md-12">
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet light ">
                                            <div class="portlet-title">
                                                <div class="caption font-dark">
                                                    <i class="icon-settings font-dark"></i>
                                                    <span class="caption-subject bold uppercase"> Manage Sitewise PnL Report</span>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                <div class="table-toolbar">
                                                    <div class="row" style="text-align: right">
                                                        <div class="col-md-12">
                                                            <div class="btn-group">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {!! csrf_field() !!}
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="unitsTable">
                                                    <thead>
                                                    <tr>
                                                        <th style="width:10%">Site Name</th>
                                                        <th style="width:10%">Sales</th>
                                                        <th style="width:10%"> Receipt </th>
                                                        <th style="width:10%"> Outstanding </th>
                                                        <th style="width:10%"> Total Expenses </th>
                                                        <th style="width:10%"> Outstanding Mobilization </th>
                                                        <th style="width:10%"> Sitewise P/L </th>
                                                        <th style="width:10%"> Receipt P/L </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet light ">
                                            <div class="portlet-title">
                                                <div class="caption font-dark">
                                                    <i class="icon-settings font-dark"></i>
                                                    <span class="caption-subject bold uppercase"> Manage Expenses </span>
                                                </div>
                                            </div>
                                                {!! csrf_field() !!}
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="unitConversionTable">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:10%">Site Name</th>
                                                            <th style="width:10%"> Purchase </th>
                                                            <th style="width:10%"> Salary </th>
                                                            <th style="width:10%"> Asset Rent </th>
                                                            <th style="width:10%"> Subcontractor </th>
                                                            <th style="width:10%"> Misc. Purchase </th>
                                                            <th style="width:10%"> Indirect Expenses </th>
                                                            <th style="width:10%"> Total Expenses </th>
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
    <script src="/public/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {

        });
    </script>
@endsection

