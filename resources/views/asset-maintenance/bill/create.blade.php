<?php
    /**
     * Created by PhpStorm.
     * User: Harsha
     * Date: 1/2/18
     * Time: 12:32 PM
     */
?>

@extends('layout.master')
@section('title','Constro | Create Asset Maintenance Bill')
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
                        <form action="/asset/maintenance/request/bill/create" method="POST" id="assetMaintenanceBillCreateForm" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="page-head">
                                <div class="container">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1>Create Asset Maintenance Bill</h1>
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
                                                    <fieldset>
                                                        <legend>Asset Maintenance</legend>
                                                        <div class="row" style="margin-bottom: 20px;">
                                                            <div class="col-md-4">
                                                                Transaction GRN
                                                            </div>
                                                        </div>
                                                        <div class="row" >
                                                            <div class="col-md-4 form-group">
                                                                <input type="hidden" name="asset_maintenance_id" id="assetMaintenanceId">
                                                                <input type="text" class="form-control transaction-grn-typeahead" name="transaction_grn">
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="form-group" id="grnSelectionDiv" hidden>
                                                        <label class="control-label" style="font-size: 18px"> Select GRN for Creating Bill </label>
                                                        <div class="form-control product-material-select" style="font-size: 14px; margin-left: 1%; height: 200px !important;" >
                                                            <ul class="list-group">

                                                            </ul>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3 col-md-offset-3" style="margin-top: 1%">
                                                                <a class="pull-right btn blue" href="javascript:void(0);" id="transactionSelectButton"> Select </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="billData" hidden>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Bill Number</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" name="bill_number" id="bill_number">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Sub-Total</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control calculatable-field calculate-amount" name="sub_total" id="subTotal">
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">CGST</label>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="input-group" >
                                                                    <input type="text" class="form-control calculatable-field cgst-percentage" name="cgst_percentage">
                                                                    <span class="input-group-addon">%</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control calculatable-field cgst-amount" name="cgst_amount" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">SGST</label>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="input-group" >
                                                                    <input type="text" class="form-control calculatable-field sgst-percentage" name="sgst_percentage">
                                                                    <span class="input-group-addon">%</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control calculatable-field sgst-amount" name="sgst_amount" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">IGST</label>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="input-group" >
                                                                    <input type="text" class="form-control calculatable-field igst-percentage" name="igst_percentage">
                                                                    <span class="input-group-addon">%</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control calculatable-field igst-amount" name="igst_amount" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Extra Amount</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control calculatable-field" name="extra_amount">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Total Amount</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control calculatable-field" id="totalAmount" name="total" readonly>
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
                        <div class="modal fade" id="viewTransactionModal" role="dialog">
                            <div class="modal-dialog transaction-modal" style="width: 90%; ">
                                <!-- Modal content-->
                                <div class="modal-content" style="overflow: scroll !important;">
                                    <div class="modal-header">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4" style="font-size: 18px"> Asset Maintenance Transaction</div>
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
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/purchase/purchase-order-billing/validations.js"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script>
        $(document).ready(function(){
            CreatePurchaseOrderBill.init();
            $(".calculatable-field").on('keyup', function(){
                var subTotal = $(".calculatable-field:input[name='sub_total']").val();
                if(typeof subTotal == 'undefined' || subTotal == ''){
                    subTotal = 0;
                    $(".calculatable-field:input[name='sub_total']").val(0);
                }
                var cgst_percentage = $(".calculatable-field:input[name='cgst_percentage']").val();
                if(typeof cgst_percentage == 'undefined' || cgst_percentage == ''){
                    cgst_percentage = 0;
                    $(".calculatable-field:input[name='cgst_percentage']").val(0);
                }
                var cgst_amount = (parseFloat(subTotal)) * (parseFloat(cgst_percentage)/100);
                $(".calculatable-field:input[name='cgst_amount']").val(cgst_amount);

                var sgst_percentage = $(".calculatable-field:input[name='sgst_percentage']").val();
                if(typeof sgst_percentage == 'undefined' || sgst_percentage == ''){
                    sgst_percentage = 0;
                    $(".calculatable-field:input[name='sgst_percentage']").val(0);
                }
                var sgst_amount = (parseFloat(subTotal)) * (parseFloat(sgst_percentage)/100);
                $(".calculatable-field:input[name='sgst_amount']").val(sgst_amount);

                var igst_percentage = $(".calculatable-field:input[name='igst_percentage']").val();
                if(typeof igst_percentage == 'undefined' || igst_percentage == ''){
                    igst_percentage = 0;
                    $(".calculatable-field:input[name='igst_percentage']").val(0);
                }
                var igst_amount = (parseFloat(subTotal)) * (parseFloat(igst_percentage)/100);
                $(".calculatable-field:input[name='igst_amount']").val(igst_amount);

                var extra_amount = $(".calculatable-field:input[name='extra_amount']").val();
                if(typeof extra_amount == 'undefined' || extra_amount == ''){
                    extra_amount = 0;
                    $(".calculatable-field:input[name='extra_amount']").val(0);
                }
                var total = (parseFloat(subTotal) + parseFloat(cgst_amount) +parseFloat(sgst_amount) +parseFloat(igst_amount) +parseFloat(extra_amount));
                $(".calculatable-field:input[name='total']").val(total)
            });

            $("#transactionSelectButton").on('click', function(event){
                event.stopPropagation();
                if($(".transaction-select:checkbox:checked").length > 0 ){
                    var transaction_id = [];
                    $(".transaction-select:checkbox:checked").each(function(){
                        transaction_id.push($(this).val());
                    });
                }
            });

            $("#imageupload").on('change', function () {
                var countFiles = $(this)[0].files.length;
                var imgPath = $(this)[0].value;
                var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
                var image_holder = $("#preview-image");
                image_holder.empty();
                if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                    if (typeof (FileReader) != "undefined") {
                        for (var i = 0; i < countFiles; i++) {
                            var reader = new FileReader()
                            reader.onload = function (e) {
                                var imagePreview = '<div class="col-md-2"><input type="hidden" name="bill_images[]" value="'+e.target.result+'"><img src="'+e.target.result+'" class="thumbimage" /></div>';
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

            var transactionGrnList = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: "/asset/maintenance/request/bill/get-bill-pending-transactions?keyword=%QUERY",
                    filter: function(x) {
                        if($(window).width()<420){
                            $("#header").addClass("fixed");
                        }
                        return $.map(x, function (data) {
                            return {
                                id: data.id,
                                list: data.list,
                                grn: data.grn
                            };
                        });
                    },
                    wildcard: "%QUERY"
                }
            });
            transactionGrnList.initialize();

            $('.transaction-grn-typeahead').typeahead(null, {
                displayKey: 'name',
                engine: Handlebars,
                source: transactionGrnList.ttAdapter(),
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
                var POData = $.parseJSON(JSON.stringify(datum));
                $("input[name='transaction_grn']").val(POData.grn);
                $("#grnSelectionDiv .list-group").html(POData.list);
                $("#assetMaintenanceId").val(POData.id);
                $("#grnSelectionDiv").show();
                $("#grnSelectionDiv .list-group input:checkbox").each(function(){
                    $(this).attr('checked', true);
                });
                $("#transactionSelectButton").trigger('click');
                $('#billData').show();
            })
                .on('typeahead:open', function (obj, datum) {
                    $(".transaction-grn-typeahead").val('');
                    $(".asset-maintenance-typeahead").val('');
                    $("#grnSelectionDiv .list-group").html('');
                    $("#grnSelectionDiv").hide();
                    $("#billData").hide();
                });
        });

        function viewTransactionDetails(transactionId){
            $.ajax({
                url:'/asset/maintenance/request/transaction/view/'+transactionId+"?_token="+$('input[name="_token"]').val()+"&isShowTax=true",
                type: 'GET',
                success: function(data,textStatus,xhr){
                    $("#viewTransactionModal .modal-body").html(data);
                    $("#viewTransactionModal").modal('show');
                },
                error: function(errorStatus){

                }
            });
        }
    </script>
@endsection

