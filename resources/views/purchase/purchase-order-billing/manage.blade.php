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
    <style>
        #purchaseOrderBillTable tr th, #purchaseOrderBillTable tr td,#purchaseOrderBillTable tr td input{
            font-size: 13px !important;
        }
    </style>
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
                                    <h1>Manage Purchase Order Bill</h1>
                                </div>
                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-purchase-bill'))
                                    <div class="btn-group pull-right margin-top-15">
                                        <div id="sample_editable_1_new" class="btn yellow" ><a href="/purchase/purchase-order-bill/create" style="color: white"> <i class="fa fa-plus"></i>  &nbsp; Purchase Order Bill
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
                                                        <div class="row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label>Date Range :  </label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="input-group input-large date-picker input-daterange" data-date-format="yyyy-mm-dd">
                                                                    <input type="text" class="form-control" name="start_date" id="start_date" required="required">
                                                                    <span class="input-group-addon"> to </span>
                                                                    <input type="text" class="form-control" name="end_date" id="end_date" required="required"> </div>
                                                                <!-- /input-group -->
                                                                <span class="help-block"> Select date range </span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="btn-group">
                                                                    <div id="search-withfilter" class="btn blue" >
                                                                        <a href="#" style="color: white"> Submit
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="table-scrollable profit-margin-table" style="overflow: scroll !important;">
                                                            <table class="table table-striped table-bordered table-hover order-column" id="purchaseOrderBillTable">
                                                                <thead>
                                                                <tr>
                                                                    <th style="width: 11%">Project</th>
                                                                    <th style="width: 11%">Sr.No.</th>
                                                                    <th style="width: 11%">Entry Date</th>
                                                                    <th style="width: 11%">Bill Date</th>
                                                                    <th style="width: 11%">Bill Number</th>
                                                                    <th style="width: 11%">Vendor Name</th>
                                                                    <th style="width: 11%">Basic Amount</th>
                                                                    <th style="width: 11%">Tax Amount</th>
                                                                    <th style="width: 11%">Total</th>
                                                                    <th style="width: 11%">Pending Amount</th>
                                                                    <th style="width: 11%">Paid Amount</th>
                                                                    <th style="width: 11%">Action</th>
                                                                </tr>
                                                                <tr class="filter">
                                                                    <th style="width: 11%"></th>
                                                                    <th style="width: 11%"></th>
                                                                    <th style="width: 11%"> <input type="hidden" class="form-control form-filter" name="postdata" id="postdata"></th>
                                                                    <th style="width: 11%">
                                                                        <div class="input-group date date-picker" data-date-format="yyyy-mm-dd" data-date-end-date="+0d">
                                                                            <input type="text" class="form-control form-filter" name="bill_date" style="font-size: 9px" readonly>
                                                                            <span class="input-group-btn">
                                                                                <button class="btn default" type="button">
                                                                                    <i class="fa fa-calendar"></i>
                                                                                </button>
                                                                            </span>
                                                                        </div>
                                                                    </th>
                                                                    <th style="width: 11%"> </th>
                                                                    <th style="width: 11%"> <input style="width: 90%; margin-left: 5%" type="text" name="vendor_name" class="form-control form-filter"> </th>
                                                                    <th style="width: 11%"> </th>
                                                                    <th style="width: 11%"> </th>
                                                                    <th style="width: 11%"> </th>
                                                                    <th style="width: 11%"> </th>
                                                                    <th style="width: 11%"> </th>
                                                                    <th style="width: 11%">
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
    <script src="/assets/custom/purchase/purchase-order-billing/manage-datatables.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $("#search-withfilter").on('click',function(){
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                var postData =
                    'start_date=>'+start_date+','+
                    'end_date=>'+end_date;
                $("input[name='postdata']").val(postData);
                $(".filter-submit").trigger('click');
            });
        });
    </script>
@endsection

