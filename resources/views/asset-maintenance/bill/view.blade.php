<?php
    /**
     * Created by PhpStorm.
     * User: Harsha
     * Date: 2/2/18
     * Time: 10:54 AM
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
    <input type="hidden" id="pendingAmount" value="{{$pendingAmount}}">
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
                                    <h1>View Asset Maintenance Bill</h1>
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
                                                            <legend>Asset Maintenance Bill</legend>
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    Asset Maintenance ID
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-3 form-group">
                                                                    <input type="text" readonly class="form-control" value="{{$assetMaintenanceBill->assetMaintenance->id}}">
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
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Sub Total</label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control" value="{{$assetMaintenanceBill->amount}}" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="row form-group">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">CGST</label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <div class="input-group" >
                                                                        <input type="text" class="form-control" name="cgst_percentage" value="{{$assetMaintenanceBill['cgst_percentage']}}">
                                                                        <span class="input-group-addon">%</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" name="cgst_amount" readonly value="{{$assetMaintenanceBill['cgst_amount']}}">
                                                                </div>
                                                            </div>
                                                            <div class="row form-group">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">SGST</label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <div class="input-group" >
                                                                        <input type="text" class="form-control" name="sgst_percentage" value="{{$assetMaintenanceBill['sgst_percentage']}}">
                                                                        <span class="input-group-addon">%</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" name="sgst_amount" readonly value="{{$assetMaintenanceBill['sgst_amount']}}">
                                                                </div>
                                                            </div>
                                                            <div class="row form-group">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">IGST</label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <div class="input-group" >
                                                                        <input type="text" class="form-control" name="igst_percentage" value="{{$assetMaintenanceBill['igst_percentage']}}">
                                                                        <span class="input-group-addon">%</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control" name="igst_amount" readonly value="{{$assetMaintenanceBill['igst_amount']}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Extra Amount</label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control" name="extra_amount" value="{{$assetMaintenanceBill->extra_amount}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Total Amount</label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control" id="totalAmount" value="{{$assetMaintenanceBill->total}}" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Selected Images :</label>
                                                                </div>
                                                                <br />
                                                                <div class="row">
                                                                    <div id="uploaded-image" class="row">
                                                                        @foreach($assetMaintenanceBillImagePaths as $paths)
                                                                            <div class="col-md-2">
                                                                                <img src="{{$paths}}" class="thumbimage" />
                                                                            </div>
                                                                        @endforeach

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="tab-pane fade in" id="paymentTab">
                                                        @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin'|| $user->customHasPermission('create-asset-maintenance-billing'))
                                                            <div class="btn-group pull-right margin-top-15">
                                                                <a id="sample_editable_1_new" class="btn yellow" href="#paymentModal" data-toggle="modal" >
                                                                    <i class="fa fa-plus"></i>  &nbsp; Asset Maintenance Bill Payment
                                                                </a>
                                                            </div>
                                                        @endif
                                                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="assetMaintenancePaymentTable">
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
                                                                <form id="createBillPaymentForm" method="post" action="/asset/maintenance/request/bill/payment/create">
                                                                    {!! csrf_field() !!}
                                                                    <input type="hidden" id="assetMaintenanceBillId" name="asset_maintenance_bill_id" value="{{$assetMaintenanceBill->id}}">
                                                                    <br>
                                                                    {{--<div class="form-group row">
                                                                        <div class="col-md-4">

                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="checkbox" name="is_advance" id="isAdvanceCheckbox">
                                                                            <label class="control-label" style="margin-left: 1%">
                                                                                Is Advance Payment
                                                                            </label>
                                                                        </div>
                                                                    </div>--}}
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
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/admin/asset-maintenance/bill/payment-manage-datatable.js"></script>
    <script src="/assets/custom/admin/asset-maintenance/bill/validations.js"></script>
    <script>
        $(document).ready(function(){
            CreateAssetMaintenanceBillPayment.init();
        });
    </script>
@endsection



