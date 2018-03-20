<?php
/**
 * Created by Ameya Joshi.
 * Date: 20/3/18
 * Time: 4:42 PM
 */
?>
@extends('layout.master')
@section('title','Constro | Create Purchase Order Bill')
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
    <link href="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/clockface/css/clockface.css" rel="stylesheet" type="text/css" />
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
                        <form action="/purchase/purchase-order-bill/create" method="POST" id="purchaseOrderBillCreateForm" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="page-head">
                                <div class="container">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1>Create Site Transfer Bill</h1>
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
                                            <a href="javascript:void(0);">Create Site Transfer Bill</a>
                                            <i class="fa fa-circle"></i>
                                        </li>
                                    </ul>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- BEGIN VALIDATION STATES-->
                                            <div class="portlet light ">
                                                <div class="portlet-body">
                                                    <fieldset>
                                                        <legend> Site Transfer </legend>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">
                                                                    Enter Transfer GRN
                                                                </label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control transfer-grn-typeahead" name="transfer_grn">
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div id="billData" hidden>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Bill Number</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" name="vendor_bill_number" id="vendorBillNumber">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Bill Date</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="input-group input-medium date date-picker" data-date-format="yyyy-mm-dd" data-date-end-date="+0d">
                                                                    <input type="text" class="form-control" name="bill_date" readonly>
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
                                                                <input type="text" class="form-control calculate-amount" name="sub_total" id="subTotal" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Tax Amount</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" class="form-control tax calculate-amount" id="taxAmount" name="tax_amount" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Extra Amount</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control calculate-amount" id="extra_amount" name="extra_amount">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="extra_tax_div" hidden>
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Extra Tax Amount</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="hidden" id="extra_amount_tax_value">
                                                                <input type="text" class="form-control calculate-amount" name="extra_tax_amount" id="extra_tax_amount" value="0" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Total Amount</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="totalAmount" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Remark</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="remark" name="remark">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Confirm Total Amount</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="ConfirmTotalAmount" name="amount">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Select Images :</label>
                                                            <input id="imageupload" type="file" class="btn blue" multiple />
                                                            <br />
                                                            <div class="row">
                                                                <div id="preview-image" class="row">

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3">
                                                                <button type="submit" class="btn red pull-right">
                                                                    <i class="fa fa-check" style="font-size: large"></i>
                                                                    Submit
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="modal fade" id="editTransactionModal" role="dialog">
                            <div class="modal-dialog transaction-modal" style="width: 90%; ">
                                <!-- Modal content-->
                                <div class="modal-content" style="overflow: scroll !important;">
                                    <div class="modal-header">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4" style="font-size: 18px"> Purchase Order Transaction</div>
                                            <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                        </div>
                                    </div>
                                    <div class="modal-body" style="padding:40px 50px;">

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
    <script src="/assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script>
        $(document).ready(function(){
            var transferGrnList = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: "/inventory/transfer/billing/get-approved-transaction?keyword=%QUERY",
                    filter: function(x) {
                        if($(window).width()<420){
                            $("#header").addClass("fixed");
                        }
                        return $.map(x, function (data) {
                            return {

                            };
                        });
                    },
                    wildcard: "%QUERY"
                }
            });
            transferGrnList.initialize();
            $('.transfer-grn-typeahead').typeahead(null, {
                displayKey: 'name',
                engine: Handlebars,
                source: transferGrnList.ttAdapter(),
                limit: 30,
                templates: {
                    empty: [
                        '<div class="empty-suggest">',
                        'Unable to find any Result that match the current query',
                        '</div>'
                    ].join('\n'),
                    suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{grn}}</strong></div>')
                },
            }).on('typeahead:selected', function (obj, datum) {
                $("input[name='purchase_order_format']").attr('readonly', true);
                var POData = $.parseJSON(JSON.stringify(datum));
                $("input[name='transaction_grn']").val(POData.grn);
                $("#grnSelectionDiv .list-group").html(POData.list);
                $("#purchaseOrderId").val(POData.id);
                $("#grnSelectionDiv").show();
                $("#grnSelectionDiv .list-group input:checkbox").each(function(){
                    $(this).attr('checked', true);
                });
                $("#transactionSelectButton").trigger('click');
            })
                .on('typeahead:open', function (obj, datum) {
                    $("input[name='purchase_order_format']").attr('readonly', false);
                    $(".transaction-grn-typeahead").val('');
                    $(".purchase-order-typeahead").val('');
                    $("#grnSelectionDiv .list-group").html('');
                    $("#grnSelectionDiv").hide();
                    $("#billData").hide();
                });
        });
    </script>
@endsection

