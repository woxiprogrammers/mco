@extends('layout.master')
@section('title','Constro | Manage Materials')
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
                                    <h1>Manage Asset</h1>
                                </div>
                                <div class="btn-group" style="float: right;margin-top:1%">
                                    <div id="sample_editable_1_new" class="btn yellow"><a href="/inventory/manage-asset/create" style="color: white">                                         <i class="fa fa-plus"></i>
                                            Asset
                                        </a>
                                    </div>
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
                                                        <table class="table table-striped table-bordered table-hover order-column" id="purchaseRequest">
                                                            <thead>
                                                            <tr>
                                                                <th> Asset Id </th>
                                                                <th> Model Number</th>
                                                                <th> Action </th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th> <input type="text" class="form-control form-filter" name="search_created_on" > </th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_created_on" > </th>
                                                                <th>
                                                                    <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                                    <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td> 5 </td>
                                                                <td> Otto </td>
                                                                <td>
                                                                    <div id="sample_editable_1_new" class="btn btn-xs green"><a href="/inventory/manage-asset/edit" style="color: white">                                         <i class="fa fa-plus"></i>
                                                                            Edit
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td> 6 </td>
                                                                <td> Nilson </td>
                                                                <td>
                                                                    <div id="sample_editable_1_new" class="btn btn-xs green"><a href="/inventory/manage-asset/edit" style="color: white">                                         <i class="fa fa-plus"></i>
                                                                            Edit
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form class="modal-content">
                                                                <div class="modal-header" style="background-color:#00844d">
                                                                    <center><h4 class="modal-title" id="exampleModalLongTitle">ADD REMARK</h4></center>
                                                                    <button type="button" class="btn btn-warning pull-right" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form role="form" class="form-horizontal" method="post">
                                                                        <div class="form-body">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-3" style="text-align: right">
                                                                                    <label for="company" class="control-label">Remark</label>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control" id="remark" name="remark">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                </div>
                                                                <div class="modal-footer" style="background-color:#00844d">
                                                                    <button type="submit" class="btn blue">Approve</button>
                                                                    <button type="submit" class="btn blue">disapprove</button>
                                                                </div>
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
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/purchase/manage-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#purchaseRequest').DataTable();
        });
    </script>
@endsection
