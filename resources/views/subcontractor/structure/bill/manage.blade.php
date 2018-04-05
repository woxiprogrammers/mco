@extends('layout.master')
@section('title','Constro | Manage Subcontractor Bill')
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
                                    <h1>Bill Listing</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/subcontractor/subcontractor-structure/manage">Back</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
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
                                                                <div id="bill_status_dropdown">

                                                                </div>
                                                            </div>
                                                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-subcontractor-billing') || $user->customHasPermission('approve-subcontractor-billing'))
                                                                <div class="btn-group">
                                                                    <div id="sample_editable_1_new" class="btn yellow">
                                                                        <a href="/subcontractor/subcontractor-bills/create/{!! $subcontractorStructureId !!}" style="color: white">
                                                                            <i class="fa fa-plus"></i> Create Bill
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="subcontractorStructureId" name="subcontractorStructureId" value={{$subcontractorStructureId}}>
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="billTable">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 10%"> Bill no. </th>
                                                        <th> Basic Amount </th>
                                                        <th> Tax Amount </th>
                                                        <th> Final Amount </th>
                                                        <th> Paid Amount </th>
                                                        <th> Pending Amount </th>
                                                        <th> Status </th>
                                                        <th> Action </th>
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
    <!--<script src="/assets/custom/bill/bill.js" type="application/javascript"></script>-->
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/subcontractor/manage-bill-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#billTable').DataTable();
        });
    </script>
@endsection
