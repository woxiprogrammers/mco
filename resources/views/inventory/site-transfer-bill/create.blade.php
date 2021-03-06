<?php

/**
 * Created by Ameya Joshi.
 * Date: 20/3/18
 * Time: 4:42 PM
 */
?>
@extends('layout.master')
@section('title','Constro | Create Site Transfer Bill')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<style>
    .thumbimage {
        float: left;
        width: 100%;
        height: 200px;
        position: relative;
        padding: 5px;
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
                                            <form action="/inventory/transfer/billing/create" method="POST" id="siteTransferBillCreateForm">
                                                {!! csrf_field() !!}
                                                <fieldset>
                                                    <legend> Site Transfer (Transportation Bill) </legend>
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">
                                                                Search Challan
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control challan-typeahead" placeholder="Enter Challan number" name="challan_number">
                                                            <input type="hidden" name="challan_id" id="challanId">
                                                        </div>
                                                    </div>
                                                </fieldset>
                                                <div id="billData" hidden>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <table class="table table-striped table-bordered table-hover order-column" id="challans" style="margin-top: 1%;">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 4%;"></th>
                                                                        <th> Challan No </th>
                                                                        <th> Vendor Name</th>
                                                                        <th> Subtotal </th>
                                                                        <th> Tax Amount </th>
                                                                        <th> Action </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Bill Number</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="bill_number" id="billNumber">
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
                                                            <input type="text" class="form-control calculate-amount" name="subtotal" id="subTotal" readonly>
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
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control calculate-amount" id="extra_amount" name="extra_amount">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Extra Amount CGST</label>
                                                        </div>
                                                        <div class="col-md-6 row">
                                                            <div class="col-md-6 input-group" id="inputGroup" style="float: inherit; !important;">
                                                                <input type="number" class="form-control calculate-amount" id="extra_amount_cgst_percentage" name="extra_amount_cgst_percentage" value="0">
                                                                <span class="input-group-addon" style="font-size: 18px">&nbsp;&nbsp; % &nbsp; &nbsp;</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" name="extra_amount_cgst_amount" id="extra_amount_cgst_amount" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Extra Amount SGST</label>
                                                        </div>
                                                        <div class="col-md-6 row">
                                                            <div class="col-md-6 input-group" id="inputGroup" style="float: inherit; !important;">
                                                                <input type="number" class="form-control calculate-amount" id="extra_amount_sgst_percentage" name="extra_amount_sgst_percentage" value="0">
                                                                <span class="input-group-addon" style="font-size: 18px">&nbsp;&nbsp; % &nbsp; &nbsp;</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" name="extra_amount_sgst_amount" id="extra_amount_sgst_amount" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Extra Amount IGST</label>
                                                        </div>
                                                        <div class="col-md-6 row">
                                                            <div class="col-md-6 input-group" id="inputGroup" style="float: inherit; !important;">
                                                                <input type="number" class="form-control calculate-amount" id="extra_amount_igst_percentage" name="extra_amount_igst_percentage" value="0">
                                                                <span class="input-group-addon" style="font-size: 18px">&nbsp;&nbsp; % &nbsp; &nbsp;</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" name="extra_amount_igst_amount" id="extra_amount_igst_amount" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Total Amount</label>
                                                        </div>
                                                        <div class="col-md-6">
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
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="ConfirmTotalAmount" name="total">
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
@endsection
@section('javascript')
<link rel="stylesheet" href="/assets/global/plugins/datatables/datatables.min.css" />
<link rel="stylesheet" href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" />
<link rel="stylesheet" href="/assets/global/css/app.css" />
<script src="/assets/global/plugins/moment.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<script src="/assets/custom/inventory/site-transfer-validations.js"></script>
<script>
    $(document).ready(function() {
        CreateSiteTransferBill.init();
        var transferGrnList = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: "/inventory/transfer/billing/get-approved-transaction?keyword=%QUERY",
                filter: function(x) {
                    if ($(window).width() < 420) {
                        $("#header").addClass("fixed");
                    }
                    return $.map(x, function(data) {
                        return {
                            challan_id: data.challan_id,
                            challan_number: data.challan_number,
                            tr_data: data.tr_data
                        };
                    });
                },
                wildcard: "%QUERY"
            }
        });
        transferGrnList.initialize();
        $('.challan-typeahead').typeahead(null, {
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
                    suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{challan_number}}</strong></div>')
                },
            })
            .on('typeahead:selected', function(obj, datum) {
                var POData = $.parseJSON(JSON.stringify(datum));
                //$(".challan-typeahead").typeahead('val', POData.challan_number);
                tableBody = $("table tbody");
                tableBody.append(POData.tr_data);
                calculateSubtotal();
                $("#billData").show();
            })
            .on('typeahead:open', function(obj, datum) {
                $("#billData").hide();
                $("#challanId").removeAttr('value');
            });

        $(".calculate-amount").on('keyup', function() {
            var extraAmount = parseFloat($("#extra_amount").val());
            if (isNaN(extraAmount)) {
                extraAmount = 0;
            }
            var cgstPercent = parseFloat($("#extra_amount_cgst_percentage").val());
            if (isNaN(cgstPercent)) {
                cgstPercent = 0;
            }
            var sgstPercent = parseFloat($("#extra_amount_sgst_percentage").val());
            if (isNaN(sgstPercent)) {
                sgstPercent = 0;
            }
            var igstPercent = parseFloat($("#extra_amount_igst_percentage").val());
            if (isNaN(igstPercent)) {
                igstPercent = 0;
            }
            var cgstAmount = parseFloat(extraAmount * (cgstPercent / 100)).toFixed(3);
            var sgstAmount = parseFloat(extraAmount * (sgstPercent / 100)).toFixed(3);
            var igstAmount = parseFloat(extraAmount * (igstPercent / 100)).toFixed(3);
            $("#extra_amount_cgst_amount").val(cgstAmount);
            $("#extra_amount_sgst_amount").val(sgstAmount);
            $("#extra_amount_igst_amount").val(igstAmount);
            var subtotal = parseFloat($("#subTotal").val());
            if (isNaN(subtotal)) {
                subtotal = 0;
            }
            var taxAmount = parseFloat($("#taxAmount").val());
            if (isNaN(taxAmount)) {
                taxAmount = 0;
            }
            var totalAmount = parseFloat(parseFloat(subtotal) + parseFloat(taxAmount) + parseFloat(extraAmount) + parseFloat(cgstAmount) + parseFloat(sgstAmount) + parseFloat(igstAmount)).toFixed(3);
            if (isNaN(totalAmount)) {
                totalAmount = 0;
            }
            $("#totalAmount").val(totalAmount);
            //$(".calculate-amount").trigger('keyup');
        });

        $("#imageupload").on('change', function() {
            var countFiles = $(this)[0].files.length;
            var imgPath = $(this)[0].value;
            var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
            var image_holder = $("#preview-image");
            image_holder.empty();
            if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                if (typeof(FileReader) != "undefined") {
                    for (var i = 0; i < countFiles; i++) {
                        var reader = new FileReader()
                        reader.onload = function(e) {
                            var imagePreview = '<div class="col-md-2"><input type="hidden" name="bill_images[]" value="' + e.target.result + '"><img src="' + e.target.result + '" class="thumbimage" /></div>';
                            image_holder.append(imagePreview);
                        };
                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[i]);
                    }
                } else {
                    alert("It doesn't supports");
                }
            } else {
                alert("Select Only images");
            }
        });
    });

    function calculateSubtotal() {
        let subtotal = 0;
        let taxAmount = 0;
        $(".challan-select:checkbox:checked").each(function() {
            challan_id = $(this).val();
            subtotal += parseFloat($("#subtotal_" + challan_id).val());
            taxAmount += parseFloat($("#taxAmount" + challan_id).val());
        });
        $("#subTotal").val(subtotal);
        $("#taxAmount").val(taxAmount);
        $(".calculate-amount").trigger('keyup');
    }


    function removeTableRow(element) {
        $(element).closest('tr').remove();
        if ($(".challan-select:checkbox:checked").length <= 0) {
            $("#billData").hide();
        }
        calculateSubtotal();
    }
</script>
@endsection