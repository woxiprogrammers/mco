<?php
/**
 * Created by Ameya Joshi.
 * Date: 20/3/18
 * Time: 4:10 PM
 */
?>

@extends('layout.master')
@section('title','Constro | Manage Site Transfer Bills')
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
                                    <h1>Manage Site Transfer Bills</h1>
                                </div>
                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-asset-maintenance-billing'))
                                    <div id="sample_editable_1_new" class="btn yellow pull-right" style="margin-left: 70%">
                                        <a href="/inventory/transfer/billing/create" style="color: white" id="createSiteTransferBill">
                                            <i class="fa fa-plus"></i> Site Transfer Bill
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
                                            <span style="color: red">(Note : All Sites data displayed)</span>
                                            {!! csrf_field() !!}

                                                <div class="portlet-body">
                                                    <div class="table-container">
                                                        <table class="table table-striped table-bordered table-hover order-column" id="siteTransferBillListingTable">
                                                            <thead>
                                                                <tr>
                                                                    <th >Project</th>
                                                                    <th >Entry Date</th>
                                                                    <th >Bill Date</th>
                                                                    <th >Bill Number</th>
                                                                    <th >Vendor Name</th>
                                                                    <th >Basic Amount</th>
                                                                    <th >Tax Amount</th>
                                                                    <th >Total</th>
                                                                    <th >Paid Amount</th>
                                                                    <th >Pending Amount</th>
                                                                    <th >Action</th>
                                                                </tr>
                                                                <tr class="filter">
                                                                    <th><input type="text" class="form-control form-filter"  name="project_name" id="project_name"></th>
                                                                    <th> <input type="hidden" class="form-control form-filter" name="postdata" id="postdata"></th>
                                                                    <th>
                                                                        <div class="input-group date date-picker" data-date-format="yyyy-mm-dd" data-date-end-date="+0d">
                                                                            <input type="text" class="form-control form-filter" name="bill_date" style="font-size: 9px" readonly>
                                                                            <span class="input-group-btn">
                                                                                    <button class="btn default" type="button">
                                                                                        <i class="fa fa-calendar"></i>
                                                                                    </button>
                                                                                </span>
                                                                        </div>
                                                                    </th>
                                                                    <th> <input type="text" class="form-control form-filter"  name="bill_number" id="bill_number"></th>
                                                                    <th> <input type="text" class="form-control form-filter"  name="vendor_name" id="vendor_name"></th>
                                                                    <th><input type="text" class="form-control form-filter"  name="basic_amt" id="basic_amt"> </th>
                                                                    <th> </th>
                                                                    <th> <input type="text" class="form-control form-filter"  name="total_amt" id="total_amt"></th>
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
                                                                    <th colspan="7" style="text-align:right">Total Page Wise: </th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="inventoryComponentModal" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header" style="padding-bottom:10px">
                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="col-md-4"> Inventory Component</div>
                                                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                </div>
                                            </div>
                                            <div class="modal-body" style="padding:40px 50px;">
                                                <form role="form" action="/inventory/component/create" method="POST" id="addTransferForm">
                                                    {!! csrf_field() !!}
                                                    <div class="form-group row">
                                                        <div class="col-md-4" style="text-align: right">
                                                            <label for="name" class="control-label">Inventory Type: </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select class="form-control" id="inventory_type" name="inventory_type">
                                                                <option value="">Select Inventory Type</option>
                                                                <option value="material">Material</option>
                                                                <option value="asset">Asset</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-4" style="text-align: right">
                                                            <label for="name" class="control-label">Name : </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" id="name" name="name">
                                                        </div>
                                                    </div>
                                                    <input type="hidden" class="form-control" id="reference_id" name="reference_id">
                                                    <div class="form-group row">
                                                        <div class="col-md-4" style="text-align: right">
                                                            <label for="name" class="control-label">Opening Stock : </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" id="opening_stock" name="opening_stock">
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn red pull-right" id="createComponentButton" hidden> Create</button>
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
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
    <script src="/assets/custom/inventory/site-transfer-bill-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            SiteTransferBillListing.init();
            $(".form-filter:input[name='vendor_name']").on('keyup', function(){
                if ($(".form-filter:input[name='vendor_name']").val().length > 3) {
                    var bill_number = $('#bill_number').val();
                    var project_name = $('#project_name').val();
                    var basic_amt = $('#basic_amt').val();
                    var total_amt = $('#total_amt').val();
                    var vendor_name = $('#vendor_name').val();
                    var bill_date = $('#bill_date').val();
                    $("input[name='bill_number']").val(bill_number);
                    $("input[name='project_name']").val(project_name);
                    $("input[name='basic_amt']").val(basic_amt);
                    $("input[name='total_amt']").val(total_amt);
                    $("input[name='vendor_name']").val(vendor_name);
                    $("input[name='bill_date']").val(bill_date);
                    $(".filter-submit").trigger('click');
                }
            });
            $(".form-filter:input[name='project_name']").on('keyup', function(){
                if ($(".form-filter:input[name='project_name']").val().length > 3) {
                    var bill_number = $('#bill_number').val();
                    var project_name = $('#project_name').val();
                    var basic_amt = $('#basic_amt').val();
                    var total_amt = $('#total_amt').val();
                    var vendor_name = $('#vendor_name').val();
                    var bill_date = $('#bill_date').val();
                    $("input[name='bill_number']").val(bill_number);
                    $("input[name='project_name']").val(project_name);
                    $("input[name='basic_amt']").val(basic_amt);
                    $("input[name='total_amt']").val(total_amt);
                    $("input[name='vendor_name']").val(vendor_name);
                    $("input[name='bill_date']").val(bill_date);
                    $(".filter-submit").trigger('click');
                }
            });
            $(".form-filter:input[name='bill_number']").on('keyup', function(){
                if ($(".form-filter:input[name='bill_number']").val().length > 0) {
                    var bill_number = $('#bill_number').val();
                    var project_name = $('#project_name').val();
                    var basic_amt = $('#basic_amt').val();
                    var total_amt = $('#total_amt').val();
                    var vendor_name = $('#vendor_name').val();
                    var bill_date = $('#bill_date').val();
                    $("input[name='bill_number']").val(bill_number);
                    $("input[name='project_name']").val(project_name);
                    $("input[name='basic_amt']").val(basic_amt);
                    $("input[name='total_amt']").val(total_amt);
                    $("input[name='vendor_name']").val(vendor_name);
                    $("input[name='bill_date']").val(bill_date);
                    $(".filter-submit").trigger('click');
                }
            });
            $(".form-filter:input[name='basic_amt']").on('keyup', function(){
                var bill_number = $('#bill_number').val();
                var project_name = $('#project_name').val();
                var basic_amt = $('#basic_amt').val();
                var total_amt = $('#total_amt').val();
                var vendor_name = $('#vendor_name').val();
                var bill_date = $('#bill_date').val();
                $("input[name='bill_number']").val(bill_number);
                $("input[name='project_name']").val(project_name);
                $("input[name='basic_amt']").val(basic_amt);
                $("input[name='total_amt']").val(total_amt);
                $("input[name='vendor_name']").val(vendor_name);
                $("input[name='bill_date']").val(bill_date);
                $(".filter-submit").trigger('click');
            });
            $(".form-filter:input[name='total_amt']").on('keyup', function(){
                var bill_number = $('#bill_number').val();
                var project_name = $('#project_name').val();
                var basic_amt = $('#basic_amt').val();
                var total_amt = $('#total_amt').val();
                var vendor_name = $('#vendor_name').val();
                var bill_date = $('#bill_date').val();
                $("input[name='bill_number']").val(bill_number);
                $("input[name='project_name']").val(project_name);
                $("input[name='basic_amt']").val(basic_amt);
                $("input[name='total_amt']").val(total_amt);
                $("input[name='vendor_name']").val(vendor_name);
                $("input[name='bill_date']").val(bill_date);
                $(".filter-submit").trigger('click');
            });
        });
    </script>
@endsection

