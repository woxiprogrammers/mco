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
                                                                <div class="col-md-3 col-md-offset-0">
                                                                    Client Name
                                                                </div>
                                                                <div class="col-md-3">
                                                                    Project Name
                                                                </div>
                                                                <div class="col-md-3">
                                                                    Project Site Name
                                                                </div>
                                                                <div class="col-md-3">
                                                                    Purchase Order
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-3 form-group">
                                                                    <input type="text" class="form-control" readonly value="{{$purchaseOrderBill->purchaseOrder->purchaseRequest->projectSite->project->client->company}}">
                                                                </div>
                                                                <div class="col-md-3 form-group">
                                                                    <input readonly type="text" class="form-control" value="{{$purchaseOrderBill->purchaseOrder->purchaseRequest->projectSite->project->name}}">
                                                                </div>
                                                                <div class="col-md-3 form-group">
                                                                    <input type="text" readonly class="form-control" value="{{$purchaseOrderBill->purchaseOrder->purchaseRequest->projectSite->name}}">
                                                                </div>
                                                                <div class="col-md-3 form-group">
                                                                    <input type="text" readonly class="form-control" value="{{$purchaseOrderBill->purchaseOrder->format_id}}">
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <fieldset style="margin-top: 2%">
                                                            <legend> Bill Details </legend>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Sub-Total</label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control calculate-amount" name="sub_total" id="subTotal" value="{{$subTotalAmount}}" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">CGST</label>
                                                                </div>
                                                                <div class="col-md-3" id="inputGroup">
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control tax" id="cgstPercentage" name="cgst_percentage" value="{{$purchaseOrderBill->cgst_percentage}}">
                                                                        <span class="input-group-addon" style="font-size: 18px">&nbsp;&nbsp; % &nbsp; &nbsp;</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control calculate-amount" placeholder="CGST Amount" name="cgst_amount" value="{{$purchaseOrderBill->cgst_amount}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">SGST</label>
                                                                </div>
                                                                <div class="col-md-3" id="inputGroup">
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control tax" id="cgstPercentage" name="sgst_percentage" value="{{$purchaseOrderBill->sgst_percentage}}">
                                                                        <span class="input-group-addon" style="font-size: 18px">&nbsp;&nbsp; % &nbsp; &nbsp;</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control calculate-amount" placeholder="SGST Amount" name="sgst_amount" value="{{$purchaseOrderBill->sgst_amount}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">IGST</label>
                                                                </div>
                                                                <div class="col-md-3" id="inputGroup">
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control tax" id="cgstPercentage" name="igst_percentage" value="{{$purchaseOrderBill->igst_percentage}}">
                                                                        <span class="input-group-addon" style="font-size: 18px">&nbsp;&nbsp; % &nbsp; &nbsp;</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control calculate-amount" name="igst_amount" placeholder="IGST Amount" value="{{$purchaseOrderBill->igst_amount}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Extra Amount</label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control calculate-amount" name="extra_amount" value="{{$purchaseOrderBill->extra_amount}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Total Amount</label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control" id="totalAmount" value="{{$purchaseOrderBill->amount}}" readonly>
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
                                                                                <img src="{{$paths}}" class="thumbimage" />
                                                                            </div>
                                                                        @endforeach

                                                                    </div>
                                                                </div>
                                                                {{--<div class="row" style="margin-top: 2%">
                                                                    <div class="col-md-2 col-md-offset-1">
                                                                        <input id="imageupload" type="file" class="btn blue" multiple />
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-offset-1">
                                                                        <div id="preview-image" class="row">

                                                                        </div>
                                                                    </div>
                                                                </div>--}}
                                                            </div>
                                                            {{--<div class="form-group row">
                                                                <div class="col-md-3">
                                                                    <button type="submit" class="btn red pull-right">
                                                                        <i class="fa fa-check" style="font-size: large"></i>
                                                                        Submit
                                                                    </button>
                                                                </div>
                                                            </div>--}}
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
                                                                    <th> Amount </th>
                                                                    <th> Payment Method </th>
                                                                    <th> Reference Number </th>
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
                                                                        <div class="col-md-4">
                                                                            <label class="pull-right control-label">
                                                                                Amount
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control" name="amount" placeholder="Enter Amount">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <label class="pull-right control-label">
                                                                                Payment Mode:
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control" name="payment_id">
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
    <script src="/assets/custom/purchase/purchase-order-billing/payment-manage-datatable.js"></script>
@endsection

