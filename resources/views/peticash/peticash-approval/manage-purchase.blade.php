@extends('layout.master')
@section('title','Constro | Peticash Request Purchase Approval')
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
                                <h1>Peticash Purchase Request Approval</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                    <div class="portlet light ">
                                        {!! csrf_field() !!}
                                        <div class="portlet-body">
                                            <div class="row" style="text-align: right">
                                                <div class="col-md-4">
                                                    <select class="form-control" id="client_id" name="client_id">
                                                        <option value="1">Please Select Client</option>
                                                        <option value="1">Site1</option>
                                                        <option value="1">Site2</option>
                                                        <option value="1">Site3</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <select class="form-control" id="project_id" name="project_id">
                                                        <option value="1">Please Select Project</option>
                                                        <option value="1">Site2</option>
                                                        <option value="1">Site3</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <select class="form-control" id="site_id" name="site_id">
                                                        <option value="1">Please Select Site</option>
                                                        <option value="1">Site2</option>
                                                        <option value="1">Site3</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="btn-group">
                                                        <div id="sample_editable_1_new" class="btn yellow" >
                                                            <a href="#" style="color: white"> Submit
                                                                <i class="fa fa-plus"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr/>
                                            <!--<div class="table-toolbar">
                                                <div class="row" style="text-align: right">
                                                    <div class="col-md-12">
                                                        <div class="btn-group">
                                                            <div id="sample_editable_1_new" class="btn yellow" ><a href="create" style="color: white"> ALLOCATE
                                                                    <i class="fa fa-plus"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>-->
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="approvalPeticashTable">
                                                <thead>
                                                <tr>
                                                    <th> Txn ID </th>
                                                    <th> Material Name </th>
                                                    <th> Qty </th>
                                                    <th> Unit </th>
                                                    <th> Requested By </th>
                                                    <th style="width: 20%"> Status </th>
                                                    <th> Date </th>
                                                    <th> Site Details </th>
                                                    <th> Action </th>


                                                </tr>
                                                <!--<tr class="filter">
                                                    <th style="width: 30%"> <input type="text" class="form-control form-filter" name="search_name"> </th>
                                                    <th> <input type="text" class="form-control form-filter" name="search_status" readonly> </th>
                                                    <th> <input type="text" class="form-control form-filter" name="search_created_on" readonly> </th>
                                                    <th>
                                                        <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                        <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                    </th>
                                                </tr>-->
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form class="modal-content" method="post">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="component_id[]" id="componentId">
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

                                        <div class="modal fade" id="editRequestApprovalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form class="modal-content" method="post">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="component_id[]" id="componentId">
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
@endsection

@section('javascript')
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/custom/peticash/peticash.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        peticashPurchaseApprovalListing.init();
        $('#approvalPeticashTable').DataTable();

        $(".approve-modal-footer-buttons").on('click',function(){
            var buttonType = $(this).text();
            if(buttonType == 'Approve'){
                var action = "/purchase/material-request/change-status/admin-approved";
            }else{
                if(buttonType == 'Disapprove'){
                    var action = "/purchase/material-request/change-status/admin-disapproved"
                }
            }
            $(this).closest('form').attr('action',action);
            $(this).closest('form').submit();
        });
    });

    function openApproveModal(componentId){
        $("#remarkModal #componentId").val(componentId);
        $("#remarkModal").modal('show');
    }

    function openEditRequestApprovalModal(componentId){
        $("#editRequestApprovalForm #componentId").val(componentId);
        $("#editRequestApprovalForm").modal('show');
    }


</script>
@endsection
