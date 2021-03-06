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
                            <div class="btn-group" style="float: right;margin-top:1%">
                                <div id="sample_editable_1_new" class="btn red" >
                                    <a id="statsBtn" style="color: white">Statistics</a>
                                </div>
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
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <label>Select Client :</label>
                                                    <select class="form-control" id="client_id" name="client_id">
                                                        <option value="0">ALL</option>
                                                        @foreach($clients as $client)
                                                        <option value="{{$client['id']}}">{{$client['company']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Select Project :</label>
                                                    <select class="form-control" id="project_id" name="project_id">
                                                        <option value="0">ALL</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Select Site :</label>
                                                    <select class="form-control" id="site_id" name="site_id">
                                                        <option value="0">ALL</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Select Year :</label>
                                                    <select class="form-control" id="year" name="year">
                                                        <option value="0">ALL</option>
                                                        <option value="2017">2017</option>
                                                        <option value="2018">2018</option>
                                                        <option value="2019">2019</option>
                                                        <option value="2020">2020</option>
                                                        <option value="2021">2021</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Select Month :</label>
                                                    <select class="form-control" id="month" name="month">
                                                        <option value="0">ALL</option>
                                                        <option value="01">Jan</option>
                                                        <option value="02">Feb</option>
                                                        <option value="03">Mar</option>
                                                        <option value="04">Apr</option>
                                                        <option value="05">May</option>
                                                        <option value="06">Jun</option>
                                                        <option value="07">Jul</option>
                                                        <option value="08">Aug</option>
                                                        <option value="09">Sep</option>
                                                        <option value="10">Oct</option>
                                                        <option value="11">Nov</option>
                                                        <option value="12">Dec</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-1">
                                                    <label>&nbsp;</label>
                                                    <div class="btn-group">
                                                        <div id="search-withfilter" class="btn blue" >
                                                            <a href="#" style="color: white"> Submit
                                                                <i class="fa fa-plus"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                </div>
                                            </div>
                                            <hr/>
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="approvalPeticashTable">
                                                <thead>
                                                <tr>
                                                    <th> Txn ID </th>
                                                    <th> Material Name </th>
                                                    <th> Qty </th>
                                                    <th> Unit </th>
                                                    <th> Amount </th>
                                                    <th> Requested By </th>
                                                    <th> Date </th>
                                                    <th> Site Details </th>
                                                    <th> Status </th>
                                                    <th> Action </th>


                                                </tr>
                                                <tr class="filter">
                                                    <th></th>
                                                    <th> <input type="text" class="form-control form-filter" name="material_name" id="material_name"> </th>
                                                    <th> <input type="hidden" class="form-control form-filter" name="search_name" id="search_name"> </th>
                                                    <th> <input type="hidden" class="form-control form-filter" name="postdata" id="postdata"></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th>  <select class="form-control" id="status_id" name="status_id">
                                                            <option value="0">ALL</option>
                                                            <option value="1">GRN Generated</option>
                                                            <option value="2">Approved</option>
                                                            <option value="3">Disapproved</option>
                                                            <option value="4">Pending</option>
                                                        </select>
                                                        <input type="hidden" class="form-control form-filter" name="status" id="status"></th>
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
                                        <div class="modal fade" id="remarkApproveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form class="modal-content" method="post">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="component_id[]" id="componentId">
                                                    <div class="modal-header">
                                                        <div class="row">
                                                            <div class="col-md-8"><center><h4 class="modal-title" id="exampleModalLongTitle">Disapproved Purchase Transaction</h4></center></div>
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
                                                                    <input type="text" class="form-control" id="remark" name="remark" required="required">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <div class="btn-group" style="float: right;margin-top:1%">
                                                            <div id="sample_editable_1_new" class="btn red" >
                                                                <a id="changeStatusButtonDisapprove" style="color: white">Disapprove</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="statsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form class="modal-content" method="post">
                                                    {!! csrf_field() !!}
                                                    <div class="modal-header">
                                                        <div class="row">
                                                            <div class="col-md-8"><center><h4 class="modal-title" id="exampleModalLongTitle">
                                                                        STATS For - <input type="text" id="site_name" name="site_name" readonly>
                                                                    </h4></center></div>
                                                            <div class="col-md-4"><button type="button" class="btn btn-warning pull-right" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button></div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-body">
                                                            <div class="form-group row">
                                                                <div class="col-md-12" style="text-align: right;">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Allocated Peticash Amount : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="allocated_amt" name="allocated_amt" value="0" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Peticash Salary Amount : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="salary_amt" name="salary_amt" value="0" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Peticash Advance Amount : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="advance_amt" name="advance_amt" value="0" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Peticash Purchase Amount : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="purchase_amt" name="purchase_amt" value="0" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Peticash Available Amount : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="pending_amt" name="pending_amt" value="0" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="detailsPurchaseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form class="modal-content" method="post">
                                                    {!! csrf_field() !!}
                                                    <div class="modal-header">
                                                        <div class="row">
                                                            <div class="col-md-8"><center><h4 class="modal-title" id="exampleModalLongTitle">Purchase Transaction Details : </h4></center></div>
                                                            <div class="col-md-4"><button type="button" class="btn btn-warning pull-right" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button></div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-body">
                                                            <div class="form-group row">
                                                                <div class="col-md-12" style="text-align: right;">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Material/Asset Name : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="mat_name" name="mat_name" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Project Site Name : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="project_site_name" name="project_site_name" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Transaction Type  : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="txn_type" name="txn_type" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>GRN : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="txn_grn" name="txn_grn" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Source Name : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="src_name" name="src_name" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Component Type : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="cmp_type" name="cmp_type" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Quantity : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="purchase_qty" name="purchase_qty" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Unit Name : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="unit_name" name="unit_name" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Bill Amount : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="bill_amt" name="bill_amt" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Bill Number : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="bill_number" name="bill_number" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>vehicle Number : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="veh_number" name="veh_number" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>In Time : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="in_time" name="in_time" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Out Time : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="out_time" name="out_time" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Reference Number : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="reference_number" name="reference_number" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Payment Type : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="pay_type" name="pay_type" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Date  : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="txn_date" name="txn_date" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Remark  : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="txn_remark" name="txn_remark" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Admin Remark  : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="admin_remark" name="admin_remark" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="editPurchaseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
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
                                                                <div class="col-md-12" style="text-align: right;">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Material/Asset Name : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="edit_mat_name" name="edit_mat_name" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Rate : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="edit_rate" name="edit_rate" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Quantity : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="edit_purchase_qty" name="edit_purchase_qty" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Unit Name : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="edit_unit_name" name="edit_unit_name" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <label>Bill Amount : </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="edit_bill_amt" name="edit_bill_amt" value="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row" id="selCategory" style="display: none;">
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-6">
                                                                            <label for="company" class="control-label">Admin Remark</label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="edit_remark" name="edit_remark" required="required">
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" id="component_type_slug" name="component_type_slug" value="0">
                                                                    <input type="hidden" id="edit_unit_id" name="edit_unit_id" value="0">
                                                                    <input type="hidden" id="editTxnId" name="editTxnId" value="0">
                                                                    <div class="modal-footer">
                                                                        <div class="btn-group" style="float: right;margin-top:1%">
                                                                            <div id="sample_editable_1_new" class="btn red" >
                                                                                <a id="editPurchaseSubmit" style="color: white">Approve</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
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

        $("#client_id").on('change', function(){
            getProjects($('#client_id').val());
        });

        $("#project_id").on('change', function(){
            getProjectSites($('#project_id').val());
        });

        $("#statsBtn").on('click',function(e){
            e.stopPropagation();
            var siteId = $("#site_id").val();
            $.ajax({
                url:'/peticash/stats-salary',
                type: "POST",
                data: {
                    _token : $("input[name='_token']").val(),
                    site_id : siteId
                },
                success: function(data, textStatus, xhr){
                    $("#allocated_amt").val(data.allocated_amt);
                    $("#salary_amt").val(data.salary_amt);
                    $("#advance_amt").val(data.advance_amt);
                    $("#purchase_amt").val(data.purchase_amt);
                    $("#pending_amt").val(data.pending_amt);
                    $("#site_name").val(data.site_name);
                    $("#statsModal").modal('show');
                },
                error: function(data){

                }
            });

        });

        $("input[name='material_name']").on('keyup',function(){
            var client_id = $('#client_id').val();
            var project_id = $('#project_id').val();
            var site_id = $('#site_id').val();
            var year = $('#year').val();
            var month = $('#month').val();
            var status_id = $('#status_id').val();
            var search_name = $('#search_name').val();
            var emp_id = $('#material_name').val();

            var postData =
                'client_id=>'+client_id+','+
                    'project_id=>'+project_id+','+
                    'site_id=>'+site_id+','+
                    'year=>'+year+','+
                    'month=>'+month;

            $("input[name='postdata']").val(postData);
            $("input[name='search_name']").val(search_name);
            $("input[name='material_name']").val(emp_id);
            $("input[name='status']").val(status_id);
            $(".filter-submit").trigger('click');
        });

        $("#status_id").on('change',function(){
            var client_id = $('#client_id').val();
            var project_id = $('#project_id').val();
            var site_id = $('#site_id').val();
            var year = $('#year').val();
            var month = $('#month').val();
            var status_id = $('#status_id').val();
            var search_name = $('#search_name').val();
            var emp_id = $('#material_name').val();

            var postData =
                'client_id=>'+client_id+','+
                    'project_id=>'+project_id+','+
                    'site_id=>'+site_id+','+
                    'year=>'+year+','+
                    'month=>'+month;

            $("input[name='postdata']").val(postData);
            $("input[name='search_name']").val(search_name);
            $("input[name='material_name']").val(emp_id);
            $("input[name='status']").val(status_id);
            $(".filter-submit").trigger('click');
        });

        $("#search-withfilter").on('click',function(){
            var client_id = $('#client_id').val();
            var project_id = $('#project_id').val();
            var site_id = $('#site_id').val();
            var year = $('#year').val();
            var month = $('#month').val();
            var status_id = $('#status_id').val();
            var search_name = $('#search_name').val();
            var emp_id = $('#material_name').val();

            var postData =
                'client_id=>'+client_id+','+
                    'project_id=>'+project_id+','+
                    'site_id=>'+site_id+','+
                    'year=>'+year+','+
                    'month=>'+month;

            $("input[name='postdata']").val(postData);
            $("input[name='search_name']").val(search_name);
            $("input[name='material_name']").val(emp_id);
            $("input[name='status']").val(status_id);
            $(".filter-submit").trigger('click');
        });

        $("#changeStatusButtonDisapprove").on('click',function(e){
            e.stopPropagation();
            var txnIds = [];
            txnIds[0] = $("#remarkApproveModal #componentId").val();
            var remark = $("#remark").val();
            if (remark != "") {
                $.ajax({
                    url:'/peticash/change-status-purchase-disapproved',
                    type: "POST",
                    data: {
                        _token: $("input[name='_token']").val(),
                        txn_ids: txnIds,
                        status : "disapproved",
                        remark : remark
                    },
                    success: function(data, textStatus, xhr){
                        $("#remarkApproveModal").modal('hide');
                        alert(data);
                        $(".filter-submit").trigger('click');
                    },
                    error: function(data){
                    }
                });
            } else {
                alert("Remark should not be empty.");
            }
        });

        $("#editPurchaseSubmit").on('click',function(e){
            e.stopPropagation();
            if ($("#edit_remark").val() != "") {
                var con = confirm("Are you Sure?");
                if (con) {
                    var mat_name = $("#edit_mat_name").val();
                    var qty = $("#edit_purchase_qty").val();
                    var unit_name = $("#edit_unit_name").val();
                    var unit_id = $("#edit_unit_id").val();
                    var amount = $("#edit_bill_amt").val();
                    var rate = $("#edit_rate").val();
                    var comp_type = $("#component_type_slug").val();
                    var edit_category_id = $("#edit_category").val();
                    var admin_remark = $("#edit_remark").val();
                    var txn_id = $("#editTxnId").val();
                    $.ajax({
                        url :'/peticash/peticash-approval-request/approve-purchase-ajax',
                        type: "POST",
                        data: {
                            _token: $("input[name='_token']").val(),
                            mat_name : mat_name,
                            qty : qty,
                            unit_name : unit_name,
                            amount : amount,
                            rate_per_unit : rate,
                            comp_type : comp_type,
                            category_id : edit_category_id,
                            unit_id : unit_id,
                            admin_remark : admin_remark,
                            txn_id : txn_id,
                            status : 'approved'
                        },
                        success: function(data, textStatus, xhr) {
                            $("#selCategory").empty();
                            $("#editPurchaseModal").modal('hide');
                            alert(data);
                            $(".filter-submit").trigger('click');
                        },
                        error: function(data) {
                        }
                    });
                } else {
                    $("#selCategory").empty();
                    $("#editPurchaseModal").modal('hide');
                }

            } else {
                alert("Remark should not be empty.");
            }
        });

    });

    function openApproveModal(componentId){
        $("#remarkApproveModal #componentId").val(componentId);
        $("#remarkApproveModal").modal('show');
    }

    function getProjects(client_id){
        $.ajax({
            url: '/peticash/projects/'+client_id,
            type: 'GET',
            async : false,
            success: function(data,textStatus,xhr){
                if(xhr.status == 200){
                    $('#project_id').html(data);
                    $('#project_id').prop('disabled',false);
                    getProjectSites($('#project_id').val());
                }
            },
            error: function(errorStatus,xhr){

            }
        });
    }

    function getProjectSites(project_id){
        $.ajax({
            url: '/peticash/project-sites/'+project_id,
            type: 'GET',
            async : false,
            success: function(data,textStatus,xhr){
                if(xhr.status == 200){
                    $('#site_id').html(data);
                    $('#site_id').prop('disabled',false);
                }
            },
            error: function(errorStatus,xhr){

            }
        });
    }

    function detailsPurchaseModal(txnId) {
        $.ajax({
            url:'/peticash/peticash-approval-request/manage-purchase-details-ajax',
            type: "POST",
            data: {
                _token : $("input[name='_token']").val(),
                txn_id : txnId
            },
            success: function(data, textStatus, xhr){
                $("#mat_name").val(data.name);
                $("#project_site_name").val(data.project_site_name);
                $("#txn_type").val(data.peticash_transaction_type);
                $("#txn_grn").val(data.grn);
                $("#src_name").val(data.source_name);
                $("#cmp_type").val(data.component_type);
                $("#purchase_qty").val(data.quantity);
                $("#unit_name").val(data.unit_name);
                $("#bill_amt").val(data.bill_amount);
                $("#bill_number").val(data.bill_number);
                $("#veh_number").val(data.vehicle_number);
                $("#in_time").val(data.in_time);
                $("#out_time").val(data.out_time);
                $("#reference_number").val(data.reference_number);
                $("#pay_type").val(data.payment_type);
                $("#txn_date").val(data.date);
                $("#txn_remark").val(data.remark);
                $("#admin_remark").val(data.admin_remark);
                $("#detailsPurchaseModal").modal('show');
            },
            error: function(data){

            }
        });
    }

    function editPurchaseModal(txnId) {
        $.ajax({
            url:'/peticash/peticash-approval-request/manage-purchase-details-ajax',
            type: "POST",
            data: {
                _token : $("input[name='_token']").val(),
                txn_id : txnId
            },
            success: function(data, textStatus, xhr){
                $("#selCategory").empty();
                var strdata = '';
                $("#edit_mat_name").val(data.name);
                $("#edit_purchase_qty").val(data.quantity);
                $("#edit_unit_name").val(data.unit_name);
                $("#edit_unit_id").val(data.unit_id);
                $("#edit_bill_amt").val(data.bill_amount);
                $("#edit_rate").val(data.bill_amount/data.quantity);
                $("#component_type_slug").val(data.component_type_slug);
                $("#editTxnId").val(data.peticash_transaction_id);
                if (data.categorydata != "") {
                    strdata = '<div class="col-md-6"><label>Select Category : </label></div>'
                        + '<div class="col-md-6">'+data.categorydata+'</div>';
                    $("#selCategory").append(strdata);
                } else {
                    $("#selCategory").empty();
                }
                $("#selCategory").show();
                $("#editPurchaseModal").modal('show');
            },
            error: function(data){

            }
        });
    }


</script>
@endsection
