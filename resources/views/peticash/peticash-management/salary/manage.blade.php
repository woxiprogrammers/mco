<?php
/**
 * Created by Ameya Joshi.
 * Date: 5/12/17
 * Time: 11:38 AM
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
                                    <h1>Manage Peticash Salary</h1>
                                </div>
                                @if($user->hasPermissionTo('create-peticash-management') || ($user->roles[0]->role->slug == 'admin') || ($user->roles[0]->role->slug == 'superadmin'))
                                    <div class="btn-group" style="float: right;margin-top:1%">
                                        <div id="sample_editable_1_new" class="btn yellow" ><a href="/peticash/peticash-management/salary/create" style="color: white"> Salary
                                                <i class="fa fa-plus"></i>
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
                                                        <div class="col-md-1">
                                                            <label>&nbsp;</label>
                                                            <div id="save_value" name="save_value" class="btn blue" >
                                                                <a href="#" style="color: white">Voucher Received / Not Received All
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="table-container">
                                                        <table class="table table-striped table-bordered table-hover order-column" id="peticashSalaryManage">
                                                            <thead>
                                                                <tr>
                                                                    <th> ID </th>
                                                                    <th> Employee Id </th>
                                                                    <th> Name </th>
                                                                    <th> Type</th>
                                                                    <th> Amount </th>
                                                                    <th> Payable Amount  </th>
                                                                    <th> Created By  </th>
                                                                    <th> Date  </th>
                                                                    <th> Site  </th>
                                                                    <th> Voucher Created </th>
                                                                    <th style="width: 20%"> Action </th>
                                                                </tr>
                                                                <tr class="filter">
                                                                    <th> {{--<input type="text" class="form-control form-filter" name="search_id" hidden>--}} </th>
                                                                    <th> <input type="text" class="form-control form-filter" name="search_employee_id" id="search_employee_id"> </th>
                                                                    <th> <input type="text" class="form-control form-filter" name="search_name" id="search_name"> </th>
                                                                    <th>
                                                                        <select class="form-control" id="status_id" name="status_id">
                                                                            <option value="all">ALL</option>
                                                                            <option value="salary">Salary</option>
                                                                            <option value="advance">Advance</option>
                                                                        </select>
                                                                        <input type="hidden" class="form-control form-filter" name="status" id="status">
                                                                    </th>
                                                                    <th> {{--<input type="text" class="form-control form-filter" name="search_amount" hidden>--}} </th>
                                                                    <th> {{--<input type="text" class="form-control form-filter" name="search_payable_amount" hidden>--}} </th>
                                                                    <th> {{--<input type="text" class="form-control form-filter" name="search_created_by" hidden>--}} </th>
                                                                    <th> {{--<input type="text" class="form-control form-filter" name="search_created_on" hidden>--}} </th>
                                                                    <th> {{--<input type="text" class="form-control form-filter" name="search_site" hidden>--}} </th>
                                                                    <th> {{--<input type="text" class="form-control form-filter" name="search_site" hidden>--}} </th>
                                                                    <th>
                                                                        <input type="hidden" class="form-control form-filter" name="postdata" id="postdata">
                                                                        <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                                        <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                            <tfoot>
                                                            <tr>
                                                                <th colspan="4" style="text-align:right">Total Page Wise: </th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th colspan="4"></th>
                                                            </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="detailsSalaryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                    <div class="modal-dialog" style="width: 98%">
                                        <form class="modal-content" method="post">
                                            {!! csrf_field() !!}
                                            <div class="modal-header">
                                                <div class="row">
                                                    <div class="col-md-8"><center><h4 class="modal-title" id="exampleModalLongTitle">Salary Transaction Details : </h4></center></div>
                                                    <div class="col-md-4"><button type="button" class="btn btn-warning pull-right" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button></div>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-12" style="text-align: left;">
                                                            <div class="row">
                                                                <div class="col-md-1">
                                                                    <label>Employee Name:</label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" id="employee_name" name="employee_name" value="" readonly>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <label>Project Site Name:</label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" id="project_site_name" name="project_site_name" value="" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-1">
                                                                    <label>Transaction Type:</label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" id="txn_type" name="txn_type" value="" readonly>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <label>Amount:</label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" id="txn_amount" name="txn_amount" value="" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-1">
                                                                    <label>Payable Amount:</label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" id="payable_amount" name="payable_amount" value="" readonly>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <label>Requested By:</label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" id="reference_user_name" name="reference_user_name" value="" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-1">
                                                                    <label>Date:</label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" id="txn_date" name="txn_date" value="" readonly>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <label>Working Days:</label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" id="working_days" name="working_days" value="" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-1">
                                                                    <label>Remark:</label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" id="txn_remark" name="txn_remark" value="" readonly>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <label>Admin Remark:</label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" id="admin_remark" name="admin_remark" value="" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="row" style="margin-top: 10px;">
                                                                <div class="col-md-1">
                                                                    <label>Captured Images:</label>
                                                                </div>
                                                                <div class="col-md-11" id="purchase_images">

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <form method="post" action="/peticash/peticash-management/change-voucher-status" hidden>
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="salary_transaction_id" id="salary_transaction_id">
                                    <input type="hidden" name="type" id="type">
                                    <button type="submit" class="btn red voucher-submit" id="submit" style="padding-left: 6px"><i class="fa fa-check"></i> Submit</button>
                                </form>
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
    <script src="/assets/custom/peticash/salary-manage-datatable.js"></script>
    <script>
        $(document).ready(function(){


            $('#save_value').click(function(){
                var val = [];
                $(':checkbox:checked').each(function(i){
                    val[i] = $(this).val();
                });
                if(val.length <= 0) {
                    alert("Please select at least one checkbox.")
                } else {
                    var value = confirm('Are you sure to receive voucher?');
                    if(value) {
                        $.ajax({
                            url:'/peticash/change-status-purchase',
                            type: "POST",
                            data: {
                                _token: $("input[name='_token']").val(),
                                purchasetxn_ids: val,
                                type : 'salary'
                            },
                            success: function(data, textStatus, xhr){
                                $(".filter-submit").trigger('click');
                            },
                            error: function(data){

                            }
                        });
                    }

                }
            });

            peticashManagementListing.init();
            $("input[name='search_employee_id']").on('keyup',function(){
                $(".filter-submit").trigger('click');
            });

            $("input[name='search_name']").on('keyup',function(){
                $(".filter-submit").trigger('click');
            });

            $("#status_id").on('change',function(){
                var year = $('#year').val();
                var month = $('#month').val();
                var status_id = $('#status_id').val();
                var search_name = $('#search_name').val();
                var emp_id = $('#emp_id').val();

                var postData =
                    'year=>'+year+','+
                    'month=>'+month;
                $("input[name='status']").val(status_id)
                $("input[name='postdata']").val(postData);
                $("input[name='search_name']").val(search_name);
                $("input[name='search_employee_id']").val(emp_id);
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
                var emp_id = $('#emp_id').val();

                var postData =
                    'year=>'+year+','+
                    'month=>'+month;
                $("input[name='status']").val(status_id)
                $("input[name='postdata']").val(postData);
                $("input[name='search_name']").val(search_name);
                $("input[name='search_employee_id']").val(emp_id);
                $(".filter-submit").trigger('click');
            });
        });

        function changeVoucherStatus(txnId){
            var value = confirm('Are you sure to receive voucher?');
            if(value){
                $('#salary_transaction_id').val(txnId);
                $('#type').val('salary');
                $(".voucher-submit").trigger('click');
            }
        }

        function detailsSalaryModal(txnId) {
            $.ajax({
                url:'/peticash/peticash-approval-request/manage-salary-details-ajax',
                type: "POST",
                data: {
                    _token : $("input[name='_token']").val(),
                    txn_id : txnId
                },
                success: function(data, textStatus, xhr){
                    var images = "";
                    if (data.list_of_images != null) {
                        data.list_of_images.forEach(function(img) {
                            images = images + "<a href='"+img+"' target='_blank'><img src='"+img+"' width='150px' height='150px'/></a>";
                        });
                    } else {
                        images = "<span>No images Found</span>";
                    }
                    $("#employee_name").val(data.employee_name);
                    $("#project_site_name").val(data.project_site_name);
                    $("#txn_amount").val(data.amount);
                    $("#payable_amount").val(data.payable_amount);
                    $("#reference_user_name").val(data.reference_user_name);
                    $("#txn_date").val(data.date);
                    $("#working_days").val(data.days);
                    $("#txn_remark").val(data.remark);
                    $("#admin_remark").val(data.admin_remark);
                    $("#txn_type").val(data.peticash_transaction_type);
                    $("#purchase_images").html(images);
                    $("#detailsSalaryModal").modal('show');
                },
                error: function(data){

                }
            });
        }
    </script>
@endsection


