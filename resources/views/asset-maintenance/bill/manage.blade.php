<?php
    /**
     * User: Harsha
     * Date: 1/2/18
     * Time: 12:15 PM
     */
?>


@extends('layout.master')
@section('title','Constro | Manage Asset Maintenance Bill')
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
                                    <h1>Manage Asset Maintenance Bill</h1>
                                </div>
                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin')
                                    <div class="btn-group pull-right margin-top-15">
                                        <div id="sample_editable_1_new" class="btn yellow" ><a href="/asset/maintenance/request/bill/create" style="color: white"> <i class="fa fa-plus"></i>  Asset Maintenance Bill
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
                                                        <table class="table table-striped table-bordered table-hover order-column" id="assetMaintenanceBillTable">
                                                            <thead>
                                                            <tr>
                                                                <th>Asset Maintenance Id</th>
                                                                <th>Vendor Company Name</th>
                                                                <th>Bill Number</th>
                                                                <th>Final Amount</th>
                                                                <th>Pending Amount</th>
                                                                <th>Action</th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th> </th>
                                                                <th> <input type="text" class="form-control form-filter" name="vendor_name" id ="vendor_name"> </th>
                                                                <th> </th>
                                                                <th> </th>
                                                                <th> </th>
                                                                <th>
                                                                    <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                                    <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th colspan="3" style="text-align:right">Total Page Wise:</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
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
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/admin/asset-maintenance/bill/manage-datatable.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $("input[name='vendor_name']").on('keyup',function(){
            $(".filter-submit").trigger('click');
        });
    });
</script>
@endsection


