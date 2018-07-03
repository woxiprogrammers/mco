<?php
/**
 * Created by Ameya Joshi.
 * Date: 28/11/17
 * Time: 5:51 PM
 */
?>
@extends('layout.master')
@section('title','Constro | Manage Purchase Order')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
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
                                    <h1>Manage Pending PO Bills</h1>
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
                                            <span style="color: red">(Note : All Sites data displayed)</span>
                                            {!! csrf_field() !!}
                                                <div class="portlet-body">
                                                    <div class="table-container">
                                                        <div class="table-scrollable profit-margin-table">
                                                            <table class="table table-striped table-bordered table-hover order-column" id="pendingPOBillTable">
                                                                <thead>
                                                                <tr>
                                                                    <th>Project</th>
                                                                    <th>PO Number</th>
                                                                    <th>GRN Number</th>
                                                                    <th>Vendor Name</th>
                                                                    <th>First Material Name</th>
                                                                    <th>Mobile Number</th>
                                                                </tr>
                                                                <tr class="filter">
                                                                    <th><input style="width: 90%; margin-left: 5%" type="text" name="project_name" id="project_name" class="form-control form-filter search_filter"></th>
                                                                    <th><input style="width: 90%; margin-left: 5%" type="text" name="po_number" id="po_number" class="form-control form-filter search_filter"></th>
                                                                    <th><input style="width: 90%; margin-left: 5%" type="text" name="grn_number" id="grn_number" class="form-control form-filter search_filter"></th>
                                                                    <th> <input style="width: 90%; margin-left: 5%" type="text" name="vendor_name" id="vendor_name" class="form-control form-filter search_filter"> </th>
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
                                                    </form>
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
    <script src="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
    <script src="/assets/custom/purchase/pending-po-bills/manage-datatables.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('.search_filter').on('keyup',function(){
                $(".filter-submit").trigger('click');
            });
        });
    </script>
@endsection

