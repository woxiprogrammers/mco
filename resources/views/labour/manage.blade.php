<?php
    /**
     * Created by Ameya Joshi.
     * Date: 14/6/17
     * Time: 5:54 PM
     */
?>
@extends('layout.master')
@section('title','Constro | Manage Labour')
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
                                    <h1>Manage Labours</h1>
                                </div>
                                {{--@if($user->hasPermissionTo('create-manage-sites'))--}}
                                    <div id="sample_editable_1_new" class="btn yellow" style="margin-left: 78%; margin-top: 1%">
                                        <a href="/labour/create" style="color: white">
                                            <i class="fa fa-plus"></i>
                                            Labour
                                        </a>
                                    </div>
                                {{--@endif--}}
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
                                                <div class="table-toolbar">
                                                    <div class="row" style="text-align: right">
                                                        <div class="col-md-12">
                                                            <div class="btn-group">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-striped table-bordered table-hover" id="labourTable">
                                                    <thead>
                                                    <tr>
                                                        <th> Labour Id </th>
                                                        <th> Labour Name </th>
                                                        <th style="width: 30%"> Contact No </th>
                                                        <th> Per Day wages </th>
                                                        <th> Project Site </th>
                                                        <th> Status </th>
                                                        <th> Actions </th>
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
    <script src="/assets/custom/labour/manage-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#labourTable').DataTable();
        });
    </script>
@endsection
