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
                                    <h1>Manage Purchase Request</h1>
                                </div>
                                <div class="btn-group pull-right margin-top-15">
                                    <div id="sample_editable_1_new" class="btn yellow" ><a href="/purchase/purchase-request/create" style="color: white"> Purchase Request
                                            <i class="fa fa-plus"></i>
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
                                                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="purchaseRequestTable">
                                                            <thead>
                                                                <tr>
                                                                    <th> PR Id </th>
                                                                    <th> Project Name - Site Name</th>
                                                                    <th> RM Id </th>
                                                                    <th> status  </th>
                                                                    <th> Action </th>
                                                                </tr>
                                                                <tr class="filter">
                                                                    <th><input type="text" class="form-control form-filter" name="search_name"></th>
                                                                    <th> <input type="text" class="form-control form-filter" name="search_status" > </th>
                                                                    <th> <input type="text" class="form-control form-filter" name="search_created_on" > </th>
                                                                    <th> <input type="text" class="form-control form-filter" name="search_created_on" > </th>
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
                                            <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form class="modal-content" method="post">
                                                        {!! csrf_field() !!}
                                                        <input type="hidden" name="purchaseRequestId" id="purchaseRequestId">
                                                        <div class="modal-header">
                                                            <div class="row">
                                                                <div class="col-md-4"></div>
                                                                <div class="col-md-4"><center><h4 class="modal-title" id="exampleModalLongTitle">REMARK</h4></center></div>
                                                                <div class="col-md-4"><button type="button" class="btn btn-warning pull-right" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button></div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
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
                                                        <div class="modal-footer">
                                                            <a class="btn blue approve-modal-footer-buttons">Approve</a>
                                                            <a class="btn blue approve-modal-footer-buttons">Disapprove</a>
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
    <script src="/assets/custom/purchase/purchase-request/manage-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $(".approve-modal-footer-buttons").on('click',function(){
                var buttonType = $(this).text();
                if(buttonType == 'Approve'){
                    var action = "/purchase/purchase-request/change-status/approved";
                }else{
                    if(buttonType == 'Disapprove'){
                        var action = "/purchase/purchase-request/change-status/disapproved"
                    }
                }
                $(this).closest('form').attr('action',action);
                $(this).closest('form').submit();
            });
        });
        function openApproveModal(purchaseRequestId){
            $("#remarkModal #purchaseRequestId").val(purchaseRequestId);
            $("#remarkModal").modal('show');
        }
    </script>
@endsection
