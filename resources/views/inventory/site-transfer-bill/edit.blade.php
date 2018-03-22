<?php
/**
 * Created by Ameya Joshi.
 * Date: 21/3/18
 * Time: 4:37 PM
 */
?>

@extends('layout.master')
@section('title','Constro | Edit Site Transfer Bill')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <style>
        .thumbimage {
            float:left;
            width:100%;
            height: 200px;
            position:relative;
            padding:5px;
        }
    </style>
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
                                    <h1>Edit Site Transfer Bill</h1>
                                </div>

                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/inventory/transfer/billing/manage">Manage Site Transfer Bill</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Edit Site Transfer Bill</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="portlet light ">
                                            <div class="portlet-body">
                                                <ul class="nav nav-tabs nav-tabs-lg">
                                                    <li class="active">
                                                        <a href="#generalTab" data-toggle="tab"> General Info </a>
                                                    </li>
                                                    <li>
                                                        <a href="#paymentTab" data-toggle="tab"> Payments </a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div class="tab-pane fade in active" id="generalTab">
                                                        <fieldset>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">
                                                                        Enter Transfer GRN
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control transfer-grn-typeahead" name="transfer_grn" value="{{$siteTransferBill->inventoryComponentTransfer->grn}}" readonly>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <div id="billData">
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Bill Number</label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control" name="bill_number" id="billNumber" value="{{$siteTransferBill->bill_number}}" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Bill Date</label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="input-group input-medium date date-picker" data-date-format="yyyy-mm-dd" data-date-end-date="+0d">
                                                                        <input type="text" class="form-control" name="bill_date" value="{{$siteTransferBill->bill_date}}" readonly>
                                                                        <span class="input-group-btn">
                                                                        <button class="btn default" type="button">
                                                                            <i class="fa fa-calendar"></i>
                                                                        </button>
                                                                    </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Sub-Total</label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control calculate-amount" name="subtotal" id="subTotal" value="{{$siteTransferBill->subtotal}}" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Tax Amount</label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="number" class="form-control tax calculate-amount" id="taxAmount" name="tax_amount" value="{{$siteTransferBill->tax_amount}}" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Extra Amount</label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control calculate-amount" id="extra_amount" name="extra_amount" value="{{$siteTransferBill->extra_amount}}" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Extra Amount CGST</label>
                                                                </div>
                                                                <div class="col-md-6 row">
                                                                    <div class="col-md-6 input-group" id="inputGroup" style="float: inherit; !important;">
                                                                        <input type="number" class="form-control calculate-amount" id="extra_amount_cgst_percentage" name="extra_amount_cgst_percentage" value="{{$siteTransferBill->extra_amount_cgst_percentage}}" readonly>
                                                                        <span class="input-group-addon" style="font-size: 18px">&nbsp;&nbsp; % &nbsp; &nbsp;</span>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" class="form-control" name="extra_amount_cgst_amount" id="extra_amount_cgst_amount" value="{{$siteTransferBill->extra_amount_csgst_amount}}" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Extra Amount SGST</label>
                                                                </div>
                                                                <div class="col-md-6 row">
                                                                    <div class="col-md-6 input-group" id="inputGroup" style="float: inherit; !important;">
                                                                        <input type="number" class="form-control calculate-amount" id="extra_amount_sgst_percentage" name="extra_amount_sgst_percentage" value="{{$siteTransferBill->extra_amount_sgst_percentage}}" readonly>
                                                                        <span class="input-group-addon" style="font-size: 18px">&nbsp;&nbsp; % &nbsp; &nbsp;</span>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" class="form-control" name="extra_amount_sgst_amount" id="extra_amount_sgst_amount" value="{{$siteTransferBill->extra_amount_sgst_amount}}" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Extra Amount IGST</label>
                                                                </div>
                                                                <div class="col-md-6 row">
                                                                    <div class="col-md-6 input-group" id="inputGroup" style="float: inherit; !important;">
                                                                        <input type="number" class="form-control calculate-amount" id="extra_amount_igst_percentage" name="extra_amount_igst_percentage" value="{{$siteTransferBill->extra_amount_igst_percentage}}" readonly>
                                                                        <span class="input-group-addon" style="font-size: 18px">&nbsp;&nbsp; % &nbsp; &nbsp;</span>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" class="form-control" name="extra_amount_igst_amount" id="extra_amount_igst_amount" value="{{$siteTransferBill->extra_amount_igst_amount}}" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Total Amount</label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control" id="totalAmount" value="{{$siteTransferBill->total}}" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Remark</label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control" id="remark" name="remark" value="{{$siteTransferBill->remark}}" readonly>
                                                                </div>
                                                            </div>
                                                            @if(count($siteTransferBill->siteTransferBillImages) > 0)
                                                                <div class="form-group">
                                                                    <label class="control-label">Images :</label>
                                                                    {{--<input id="imageupload" type="file" class="btn blue" multiple />--}}
                                                                    {{--<br />--}}
                                                                    <div class="row">
                                                                        <div id="preview-image" class="row">
                                                                            @foreach($siteTransferBill->siteTransferBillImages as $siteTransferBillImage)
                                                                                <div class="col-md-2">
                                                                                    <img src="{{$imageUploadPath}}{{$siteTransferBillImage->name}}" class="thumbimage" />
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            {{--<div class="form-group row">
                                                                <div class="col-md-3">
                                                                    <button type="submit" class="btn red pull-right">
                                                                        <i class="fa fa-check" style="font-size: large"></i>
                                                                        Submit
                                                                    </button>
                                                                </div>
                                                            </div>--}}
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade in" id="paymentTab">
                                                        <div class="btn-group pull-right margin-top-15">
                                                            <a id="sample_editable_1_new" class="btn yellow" href="#paymentModal" data-toggle="modal" >
                                                                <i class="fa fa-plus"></i>  &nbsp; Site Transfer Bill Payment
                                                            </a>
                                                        </div>
                                                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="siteTransferBillPaymentTable">
                                                            <thead>
                                                            <tr>
                                                                <th style="width: 25%"> Date </th>
                                                                <th style="width: 25%"> Amount </th>
                                                                <th style="width: 25%"> Payment Method </th>
                                                                <th style="width: 25%"> Reference Number </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="paymentModal" role="dialog">
                                                    <div class="modal-dialog">
                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header" style="padding-bottom:10px; font-size: 25px !important;">
                                                                <div class="row">
                                                                    <div class="col-md-4"></div>
                                                                    <div class="col-md-4"> <b>Add Payment</b></div>
                                                                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body" style="padding:40px 50px;">
                                                                <form id="materialCreateForm" method="post" action="/inventory/transfer/billing/payment/create">
                                                                    {!! csrf_field() !!}
                                                                    <input type="hidden" id="siteTransferBillId" name="site_transfer_bill_id" value="{{$siteTransferBill->id}}">
                                                                    <br>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-3">

                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <label><b>Total payment done till today - Rs. {{$totalPaidAmount}}</b></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <label class="pull-right control-label">
                                                                                Amount
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" name="amount" placeholder="Enter Amount">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row"id="paymentSelect">
                                                                        <div class="col-md-4">
                                                                            <label class="pull-right control-label">
                                                                                Payment Mode:
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control" name="payment_type_id" >
                                                                                @foreach($paymentTypes as $paymentType)
                                                                                    <option value="{{$paymentType['id']}}">{{$paymentType['name']}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <label class="pull-right control-label">
                                                                                Reference Number
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" name="reference_number" placeholder="Enter Reference Number">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row" style="margin-top: 5%">
                                                                        <div class="col-md-6">
                                                                            <button type="submit" class="btn red pull-right">
                                                                                <i class="fa fa-check" style="font-size: large"></i>
                                                                                Submit
                                                                            </button>
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
        </div>
    </div>
@endsection
@section('javascript')
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <link rel="stylesheet"  href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css"/>
    <link rel="stylesheet"  href="/assets/global/css/app.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/inventory/site-transfer-payment-manage-datatable.js"></script>
    <script>
        $(document).ready(function(){
            SiteTransferBillPaymentListing.init();
            $("#isAdvanceCheckbox").on('click', function(){
                if($(this).is(':checked') == true){
                    $("#paymentSelect").hide();
                }else{
                    $("#paymentSelect").show();
                }
            });
        });
    </script>
@endsection
