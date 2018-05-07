<?php
/**
 * Created by Ameya Joshi.
 * Date: 1/12/17
 * Time: 11:46 AM
 */
?>

@extends('layout.master')
@section('title','Constro | Edit Purchase Order Bill')
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
    <input type="hidden" id="remainingPaymentAmount" value="{{$paymentRemainingAmount}}">
    <input type="hidden" id="balanceAdvanceAmount" value="{{$purchaseOrderBill->purchaseOrder->balance_advance_amount}}">
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
                                    <h1>Edit Purchase Order Bill</h1>
                                </div>

                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/purchase/purchase-order-bill/manage">Manage Purchase Order Bill</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Edit Purchase Order Bill</a>
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
                                                            <legend>Project</legend>
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    Purchase Order
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-3 form-group">
                                                                    <input type="text" readonly class="form-control" value="{{$purchaseOrderBill->purchaseOrder->format_id}}">
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <fieldset style="margin-top: 2%">
                                                            <legend> Bill Details </legend>

                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right"> GRN </label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control" name="grn" id="subTotal" value="{{$grn}}" readonly>
                                                                </div>
                                                            </div>
                                                            @if($transactionEditAccess)
                                                                <form method="post" action="/purchase/purchase-order-bill/edit/{{$purchaseOrderBill->id}}">
                                                                    {!! csrf_field() !!}
                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <label class="control-label pull-right"> Bill Number </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" name="vendor_bill_number" id="vendorBillNumber" value="{{$purchaseOrderBill->vendor_bill_number}}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <label class="control-label pull-right"> Bill Date </label>
                                                                        </div>
                                                                        @if(isset($purchaseOrderBill->bill_date))
                                                                            <div class="col-md-6 date date-picker" data-date-start-date="0d">
                                                                                <input type="text" style="width: 40%" class="tax-modal-delivery-date" id="expected_delivery_date" name="bill_date" value="{{date('m/d/y',strtotime($purchaseOrderBill->bill_date))}}" />
                                                                                <button class="btn btn-sm default" type="button">
                                                                                    <i class="fa fa-calendar"></i>
                                                                                </button>
                                                                            </div>
                                                                        @else
                                                                            <div class="col-md-6 date date-picker" data-date-start-date="0d">
                                                                                <input type="text" style="width: 40%" class="tax-modal-delivery-date" id="expected_delivery_date" name="bill_date" />
                                                                                <button class="btn btn-sm default" type="button">
                                                                                    <i class="fa fa-calendar"></i>
                                                                                </button>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <label class="control-label pull-right">Sub-Total</label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control calculate-amount" id="subTotal" value="{{$subTotalAmount}}" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <label class="control-label pull-right">Tax Amount</label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control calculate-amount" id="taxAmount" value="{{$purchaseOrderBill->tax_amount}}" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <label class="control-label pull-right">Transportation Sub-Total</label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control calculate-amount" id="transportation_total" value="{{$purchaseOrderBill->transportation_total_amount}}" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <label class="control-label pull-right">Transportation Tax Amount</label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="number" class="form-control calculate-amount" value="{{$purchaseOrderBill->transportation_tax_amount}}" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <label class="control-label pull-right">Extra Amount</label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <input type="text" class="form-control calculate-amount" id="extra_amount" name="extra_amount" value="{{$purchaseOrderBill->extra_amount}}" onkeyup="calculateTotal()">
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" value="{{$extraTaxPercentage}}" id="extra_tax_percentage">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <label class="control-label pull-right">Extra Tax Amount</label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <input type="text" class="form-control calculate-amount" id="extra_tax_amount" name="extra_tax_amount" value="{{$purchaseOrderBill->extra_tax_amount}}" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <label class="control-label pull-right">Total Amount</label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <input type="text" class="form-control" id="totalAmount" name="amount" value="{{$purchaseOrderBill->amount}}" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <label class="control-label pull-right">Remark</label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" id="remark" name="remark" value="{{$purchaseOrderBill->remark}}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="col-md-2">
                                                                            <label class="control-label pull-right">Selected Images :</label>
                                                                        </div>
                                                                        <br />
                                                                        <div class="row">
                                                                            <div id="uploaded-image" class="row">
                                                                                @foreach($purchaseOrderBillImagePaths as $paths)
                                                                                    <div class="col-md-2">
                                                                                        <a href="{{$paths}}"><img src="{{$paths}}" class="thumbimage"/></a>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-actions noborder row" id="submitDiv">
                                                                        <div class="col-md-offset-3" style="margin-left: 26%">
                                                                            <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            @else
                                                                <div class="form-group row">
                                                                    <div class="col-md-2">
                                                                        <label class="control-label pull-right"> Bill Number </label>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" class="form-control" name="vendor_bill_number" value="{{$purchaseOrderBill->vendor_bill_number}}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-md-2">
                                                                        <label class="control-label pull-right"> Bill Date </label>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        @if(isset($purchaseOrderBill->bill_date))
                                                                            <input type="text" class="form-control" name="grn"  value="{{date('j M Y',strtotime($purchaseOrderBill->bill_date))}}" readonly>
                                                                        @else
                                                                            <input type="text" class="form-control" name="grn" value="" readonly>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-md-2">
                                                                        <label class="control-label pull-right">Sub-Total</label>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" class="form-control" name="sub_total" value="{{$subTotalAmount}}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-md-2">
                                                                        <label class="control-label pull-right">Tax Amount</label>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" class="form-control" name="tax_amount" value="{{$purchaseOrderBill->tax_amount}}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-md-2">
                                                                        <label class="control-label pull-right">Transportation Sub-Total</label>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" class="form-control" name="transportation_total" value="{{$purchaseOrderBill->transportation_total_amount}}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-md-2">
                                                                        <label class="control-label pull-right">Transportation Tax Amount</label>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="number" class="form-control" name="transportation_tax_amount" value="{{$purchaseOrderBill->transportation_tax_amount}}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-md-2">
                                                                        <label class="control-label pull-right">Extra Amount</label>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control" name="extra_amount" value="{{$purchaseOrderBill->extra_amount}}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-md-2">
                                                                        <label class="control-label pull-right">Extra Tax Amount</label>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control" value="{{$purchaseOrderBill->extra_tax_amount}}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-md-2">
                                                                        <label class="control-label pull-right">Total Amount</label>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control" value="{{$purchaseOrderBill->amount}}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-md-2">
                                                                        <label class="control-label pull-right">Remark</label>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" class="form-control" id="remark" name="remark" value="{{$purchaseOrderBill->remark}}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="col-md-2">
                                                                        <label class="control-label pull-right">Selected Images :</label>
                                                                    </div>
                                                                    <br />
                                                                    <div class="row">
                                                                        <div id="uploaded-image" class="row">
                                                                            @foreach($purchaseOrderBillImagePaths as $paths)
                                                                                <div class="col-md-2">
                                                                                    <a href="{{$paths}}"><img src="{{$paths}}" class="thumbimage"/></a>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </fieldset>
                                                    </div>
                                                    <div class="tab-pane fade in" id="paymentTab">
                                                        <div class="btn-group pull-right margin-top-15">
                                                            <a id="sample_editable_1_new" class="btn yellow" href="#paymentModal" data-toggle="modal" >
                                                                <i class="fa fa-plus"></i>  &nbsp; Purchase Order Payment
                                                            </a>
                                                        </div>
                                                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="purchaseOrderPaymentTable">
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
                                                            <div class="modal-header" style="padding-bottom:10px">
                                                                <div class="row">
                                                                    <div class="col-md-4"></div>
                                                                    <div class="col-md-4"> Add Payment</div>
                                                                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body" style="padding:40px 50px;">
                                                                <form id="materialCreateForm" method="post" action="/purchase/purchase-order-bill/payment/create">
                                                                    {!! csrf_field() !!}
                                                                    <input type="hidden" id="purchaseOrderBillId" name="purchase_order_bill_id" value="{{$purchaseOrderBill->id}}">
                                                                    <br>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-3">

                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <label><b>Total payment done till today - Rs. {{$paymentTillToday}}</b></label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">

                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="checkbox" name="is_advance" id="isAdvanceCheckbox">
                                                                            <label class="control-label" style="margin-left: 1%">
                                                                                To be deducted from advance
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <label class="pull-right control-label">
                                                                                Amount
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" name="amount" id="amount" placeholder="Enter Amount" value="{{$paymentRemainingAmount}}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row"id="paymentSelect">
                                                                        <div class="col-md-4">
                                                                            <label class="pull-right control-label">
                                                                                Payment Mode:
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control" name="payment_id" >
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
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
    <script src="/assets/custom/purchase/purchase-order-billing/payment-manage-datatable.js"></script>
    <script src="/assets/custom/purchase/purchase-order-billing/validations.js"></script>

    <script>
        $(document).ready(function(){
            CreatePurchaseOrderPayment.init();
            var remainingBillAmount = parseFloat($("#remainingPaymentAmount").val());
            if(remainingBillAmount == null || typeof remainingBillAmount == 'undefined' || isNaN(remainingBillAmount)){
                remainingBillAmount = 0;
            }
            $("#amount").rules('add',{
                max: remainingBillAmount
            });
            $("#isAdvanceCheckbox").on('click', function(){
                if($(this).is(':checked') == true){
                    var balanceAdvanceAmount = parseFloat($("#balanceAdvanceAmount").val());
                    if(balanceAdvanceAmount == null || typeof balanceAdvanceAmount == 'undefined' || isNaN(balanceAdvanceAmount)){
                        balanceAdvanceAmount = 0;
                    }
                    $("#amount").rules('add',{
                        max: balanceAdvanceAmount
                    });
                    $("#paymentSelect").hide();
                }else{
                    $("#amount").rules('add',{
                        max: remainingBillAmount
                    });
                    $("#paymentSelect").show();
                }
            });
        });

        function calculateTotal(){
            var total = 0;
            var extra_amount  = $('#extra_amount').val();
            var extra_tax_amount_percentage = $('#extra_tax_percentage').val();
            var extra_tax_amount = parseFloat((extra_amount * extra_tax_amount_percentage) / 100);
            $('#extra_tax_amount').val(extra_tax_amount);
            $(".calculate-amount").each(function(){
                var amount = $(this).val();
                if(typeof amount != 'undefined' && amount != '' && amount != null){
                    total += parseFloat(amount);
                }
            });
            $('#totalAmount').val(total);

        }
    </script>
@endsection

