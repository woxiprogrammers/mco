<?php
/**
 * Created by Ameya Joshi.
 * Date: 5/6/17
 * Time: 4:53 PM
 */
?>

@extends('layout.master')
@section('title','Constro | Manage Quotation')
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
                                <h1>Manage Quotations</h1>
                            </div>

                            @if($user->hasPermissionTo('create-quotation'))
                                <div id="sample_editable_1_new" class="btn yellow" style="margin-top: 1%; margin-left: 74%">
                                    <a href="/quotation/create" style="color: white">
                                        <i class="fa fa-plus"></i> Quotation
                                    </a>
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
                                    <div class="portlet-body">
                                        <div class="table-toolbar">
                                            <div class="row" style="text-align: right">
                                                <div class="col-md-12">
                                                    <div class="btn-group">
                                                        <div id="dropdownQuotation">
                                                        </div>
                                                    </div>
                                                    <div class="btn-group">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {!! csrf_field() !!}
                                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="quotationTable">
                                            <thead>
                                            <tr>
                                                <th style="width: 25%"> Client Name </th>
                                                <th style="width: 25%"> Project Name </th>
                                                <th style="width: 25%"> Site Name </th>
                                                <th> Status </th>
                                                <th> Created At </th>
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
<script src="/assets/custom/admin/quotation/manage-datatable.js"></script>
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
@endsection
