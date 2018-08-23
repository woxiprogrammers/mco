<?php
    /**
     * Created by Harsha.
     * Date: 30/1/18
     * Time: 10:13 AM
     */
?>

@extends('layout.master')
@section('title','Constro | Manage Asset Maintenance Request Approval')
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
                                    <h1>Manage Asset Maintenance Request Approval</h1>
                                </div>
                                {{--<div class="btn-group" style="float: right;margin-top:1%">
                                    <div id="sample_editable_1_new" class="btn yellow"><a href="/asset/maintenance/request/create" style="color: white">                                         <i class="fa fa-plus"></i>
                                            Asset Maintenance Request Approval
                                        </a>
                                    </div>
                                </div>--}}
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
                                                        <table class="table table-striped table-bordered table-hover order-column" id="assetMaintenanceRequestApprovalTable">
                                                            <thead>
                                                            <tr>
                                                                <th> Asset Name </th>
                                                                <th> Created At </th>
                                                                <th> Vendor name </th>
                                                                <th> Quotation Amount </th>
                                                                <th> Status</th>
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
    <script src="/assets/custom/admin/asset-maintenance/request/approval/manage-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#assetMaintenanceRequestApprovalTable').DataTable();
        });
        function changeStatus(element){
            var token = $('input[name="_token"]').val();
            $(element).next('input[name="_token"]').val(token);
            $(element).closest('form').submit();
        }
    </script>
@endsection


