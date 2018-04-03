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
                                        <h1>Create Purchase Order Bill</h1>
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
                                            <a href="javascript:void(0);">Create Purchase Order Bill</a>
                                            <i class="fa fa-circle"></i>
                                        </li>
                                    </ul>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- BEGIN VALIDATION STATES-->
                                            <div class="portlet light ">
                                                <div class="portlet-body">
                                                    <fieldset>
                                                        <legend>Project</legend>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                Purchase Order
                                                            </div>
                                                            <div class="col-md-4">
                                                                Transaction GRN
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <input type="text" class="form-control purchase-order-typeahead" name="purchase_order_format">
                                                                <input type="hidden" name="purchase_order_id" id="purchaseOrderId">
                                                            </div>
                                                            <div class="col-md-4 form-group">
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

                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="checkbox" name="is_transportation" id="transportationCheckbox">
                                                                <label class="control-label">
                                                                    Add Transportation Amount
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div id="transportation_div" hidden>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Transportation Sub-Total</label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="text" class="form-control" name="transportation_total" id="transportation_total" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Transportation Tax Amount</label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="number" class="form-control" id="transportation_tax_amount" name="transportation_tax_amount" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
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
    <script src="/assets/custom/purchase/purchase-order-billing/validations.js"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#extra_amount').on('keyup', function(){
                if(parseFloat($('#extra_amount').val()) > 0){
                    $('#extra_tax_div').show();
                    var total_tax = (parseFloat($("#extra_amount_tax_value").val()) * parseFloat($(this).val())) / 100 ;
                    $('#extra_tax_amount').val(total_tax);
                }else{
                    $('#extra_tax_div').hide();
                    $('#extra_tax_amount').val(0);
                }
            });
            $("#transportationCheckbox").on('click', function(){
                if($(this).is(':checked') == true){
                    $("#transportation_div").show();
                    calculateTotal();
                }else{
                    $("#transportation_div").hide();
                    calculateTotal();
                }
            });
            CreatePurchaseOrderBill.init();
            $("#transactionSelectButton").on('click', function(event){
                event.stopPropagation();
                if($(".transaction-select:checkbox:checked").length > 0 ){
                    var transaction_id = [];
                    $(".transaction-select:checkbox:checked").each(function(){
                        transaction_id.push($(this).val());
                    });
                    $.ajax({
                       url: '/purchase/purchase-order-bill/get-transaction-subtotal',
                       type: "POST",
                       data:{
                           _token: $("input[name='_token']").val(),
                           transaction_id: transaction_id
                       },
                        success: function(data,textStatus, xhr){
                            $("#subTotal").val(data.sub_total);
                            $("#totalAmount").val(data.tax_amount + data.sub_total);
                            $("#taxAmount").val(data.tax_amount);
                            $("#transportation_total").val(data.transportation_amount);
                            $("#transportation_tax_amount").val(data.transportation_tax_amount);
                            $("#extra_amount_tax_value").val(data.extra_tax_percentage);
                            $("#billData").show();
                            $("#vendorBillNumber").rules('add',{
                                remote : {
                                    url: "/purchase/purchase-order-bill/check-bill-number",
                                    type: "POST",
                                    async: true,
                                    data: {
                                        _token: function(){
                                            return $("input[name='_token']").val();
                                        },
                                        purchase_order_id: function() {
                                            return $( "#purchaseOrderId" ).val();
                                        },
                                        vendor_bill_number: function(){
                                            return $("#vendorBillNumber").val()
                                        }
                                    }
                                },
                                messages: {
                                    remote : 'This Bill is already registered.'
                                }
                            });
                        },
                        error: function(errorData){

                        }
                    });
                }
            });

            $(".tax").on('keyup',function(){
               var subtotal = $("#subTotal").val();
               var percentage = $(this).val();
               var amount = subtotal * (percentage / 100);
               $(this).closest('#inputGroup').next().find("input[type='text']").val(amount);
                calculateTotal();
            });
            $(".calculate-amount").on('keyup',function(){
                calculateTotal();
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

            var citiList = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: "/purchase/purchase-order-bill/get-purchase-orders?keyword=%QUERY",
                    filter: function(x) {
                        if($(window).width()<420){
                            $("#header").addClass("fixed");
                        }
                        return $.map(x, function (data) {
                            return {
                                id: data.id,
                                format: data.format,
                                grns:data.grn
                            };
                        });
                    },
                    wildcard: "%QUERY"
                }
            });
            var transactionGrnList = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: "/purchase/purchase-order-bill/get-bill-pending-transactions?keyword=%QUERY",
                    filter: function(x) {
                        if($(window).width()<420){
                            $("#header").addClass("fixed");
                        }
                        return $.map(x, function (data) {
                            return {
                                id: data.purchase_order_id,
                                list: data.list,
                                grn: data.grn
                            };
                        });
                    },
                    wildcard: "%QUERY"
                }
            });
            transactionGrnList.initialize();
            citiList.initialize();
            $('.purchase-order-typeahead').typeahead(null, {
                displayKey: 'name',
                engine: Handlebars,
                source: citiList.ttAdapter(),
                limit: 30,
                templates: {
                    empty: [
                        '<div class="empty-suggest">',
                        'Unable to find any Result that match the current query',
                        '</div>'
                    ].join('\n'),
                    suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{format}}</strong></div>')
                },
            }).on('typeahead:selected', function (obj, datum) {
                $(".transaction-grn-typeahead").attr('readonly', true);
                var POData = $.parseJSON(JSON.stringify(datum));
                $("input[name='purchase_order_format']").val(POData.format);
                $("#purchaseOrderId").val(POData.id);
                $("#grnSelectionDiv .list-group").html(POData.grns);
                $("#grnSelectionDiv").show();
            })
            .on('typeahead:open', function (obj, datum) {
                $(".transaction-grn-typeahead").attr('readonly', false);
                $(".transaction-grn-typeahead").val('');
                $(".purchase-order-typeahead").val('');
                $("#grnSelectionDiv .list-group").html('');
                $("#grnSelectionDiv").hide();
                $("#billData").hide();
            });


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
        function calculateTotal(){
            var total = 0;
            $(".calculate-amount").each(function(){
                var amount = $(this).val();
                if(typeof amount != 'undefined' && amount != '' && amount != null){
                    total += parseFloat($(this).val());
                }
            });
            if($('#transportationCheckbox').is(':checked') == true){
                total = total + parseFloat($('#transportation_total').val()) + parseFloat($('#transportation_tax_amount').val()) ;
                $("#totalAmount").val(total);
            }else{
                $("#totalAmount").val(total);
            }

        }
        function viewTransactionDetails(transactionId){
            $.ajax({
                url:'/purchase/purchase-order/transaction/edit/'+transactionId+"?_token="+$('input[name="_token"]').val()+"&isShowTax=true",
                type: 'GET',
                success: function(data,textStatus,xhr){
                    $("#editTransactionModal .modal-body").html(data);
                    $("#editTransactionModal").modal('show');
                },
                error: function(errorStatus){

                }
            });
        }
    </script>
@endsection
