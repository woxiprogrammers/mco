<?php
/**
 * Created by Ameya Joshi.
 * Date: 5/12/17
 * Time: 11:37 AM
 */
?>
@extends('layout.master')
@section('title','Constro | Manage Peticash Purchase')
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
                                    <h1>Manage Peticash Purchase</h1>
                                </div>
                                <div class="btn-group" style="float: right;margin-top:1%">
                                    <div id="sample_editable_1_new" class="btn yellow"><a href="/peticash/peticash-management/purchase/transaction/create" style="color: white">                                         <i class="fa fa-plus"></i>
                                            Transaction
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
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <label>Select Year :</label>
                                                            <select class="form-control" id="year" name="year">
                                                                <option value="0">ALL</option>
                                                                <option value="2017">2017</option>
                                                                <option value="2018">2018</option>
                                                                <option value="2019">2019</option>
                                                                <option value="2020">2020</option>
                                                                <option value="2021">2021</option>
                                                                <option value="2022">2022</option>
                                                                <option value="2023">2023</option>
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
                                                    </div>
                                                    <div class="table-container">
                                                        <table class="table table-striped table-bordered table-hover order-column" id="purchaseManageTable">
                                                            <thead>
                                                            <tr>
                                                                <th> ID </th>
                                                                <th> Material Name </th>
                                                                <th> Quantity</th>
                                                                <th> Unit </th>
                                                                <th> Amount  </th>
                                                                <th> Created By </th>
                                                                <th> Date </th>
                                                                <th> Site </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th> {{--<input type="text" class="form-control form-filter" name="search_id" hidden>--}} </th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_name"> </th>
                                                                <th> {{--<input type="text" class="form-control form-filter" name="search_name" hidden>--}} </th>
                                                                <th> {{--<input type="text" class="form-control form-filter" name="search_type" hidden>--}} </th>
                                                                <th> {{--<input type="text" class="form-control form-filter" name="search_amount" hidden>--}} </th>
                                                                <th> {{--<input type="text" class="form-control form-filter" name="search_payable_amount" hidden>--}} </th>
                                                                <th> {{--<input type="text" class="form-control form-filter" name="search_created_by" hidden>--}} </th>
                                                                <th> {{--<input type="text" class="form-control form-filter" name="search_created_on" hidden>--}} </th>
                                                                <th>
                                                                    <input type="hidden" class="form-control form-filter" name="postdata" id="postdata">
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
    <script src="/assets/custom/peticash/purchase-manage-datatable.js"></script>
    <script>
        $(document).ready(function(){
            peticashManagementListing.init();
            $("#search-withfilter").on('click',function(){
                var client_id = $('#client_id').val();
                var project_id = $('#project_id').val();
                var site_id = $('#site_id').val();
                var year = $('#year').val();
                var month = $('#month').val();
                var status_id = $('#status_id').val();
                var search_name = $('#search_name').val();
                var emp_id = $('#emp_id').val();

                var postData =
                    'year=>'+year+','+
                    'month=>'+month;

                $("input[name='postdata']").val(postData);
                $("input[name='search_name']").val(search_name);
                $("input[name='search_employee_id']").val(emp_id);
                $(".filter-submit").trigger('click');
            });
        });
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
    </script>
@endsection

