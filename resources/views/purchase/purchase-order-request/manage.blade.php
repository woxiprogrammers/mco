<?php
/**
 * Created by Ameya Joshi.
 * Date: 10/1/18
 * Time: 4:21 PM
 */
?>
@extends('layout.master')
@section('title','Constro | Manage Purchase Order')
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
                                    <h1>Manage Purchase Order Request</h1>
                                </div>
                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' ||
                                        $user->customHasPermission('create-purchase-order-request') || $user->customHasPermission('approve-purchase-order-request'))
                                    <div class="btn-group pull-right margin-top-15">
                                        <div id="sample_editable_1_new" class="btn yellow" ><a href="/purchase/purchase-order-request/create" style="color: white">
                                                <i class="fa fa-plus"></i> Purchase Order Request
                                            </a>
                                        </div>
                                    </div>
                                @endif
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
                                                        <table class="table table-striped table-bordered table-hover order-column" id="purchaseOrderRequestTable">
                                                            <thead>
                                                            <tr>
                                                                <th style="width: 10%;"> ID. </th>
                                                                <th> Purchase Request ID </th>
                                                                <th> Status</th>
                                                                <th> Created By </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th></th>
                                                                <th><input type="text" class="form-control form-filter" name="purchase_request_format"></th>
                                                                <th>
                                                                    <select class="table-group-action-input form-control input-inline input-small input-sm status-select" id="por_status" name="por_status">

                                                                        <option value="por_created">Pending for Ready to Approve</option>
                                                                        <option value="pending_for_approval">Pending for Director Approval</option>
                                                                        <option value="po_created">PO Created</option>
                                                                    </select>
                                                                </th>
                                                                <th><input type="hidden" class="form-control form-filter" name="por_status_id" id="por_status_id"/></th>
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
    <script src="/assets/custom/purchase/purchase-order-request/manage-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $(".form-filter").on('keyup', function(){
                    $(".filter-submit").trigger('click');
            });

            $("#por_status").change(function(){
                var por_status = $("#por_status").val();
                $("#por_status_id").val(por_status);
                $(".filter-submit").trigger('click');
            });
        });
    </script>
@endsection

