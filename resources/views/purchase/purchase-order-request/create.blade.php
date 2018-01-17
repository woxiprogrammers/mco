@extends('layout.master')
@section('title','Constro | Create Purchase Order Request')
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
                                <h1>Create Purchase Order Request</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/purchase/purchase-order-request/manage">Manage Purchase Order Request</a>
                                    <i class="fa fa-circle"></i>
                               </li>
                                <li>
                                    <a href="javascript:void(0);">Create Purchase Order Request</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <form role="form" id="createPurchaseOrderRequest" class="form-horizontal" method="post" action="/purchase/purchase-order-request/create">
                                            {!! csrf_field() !!}
                                            <input type="hidden" name="purchase_request_id" id="purchaseRequestId">
                                            <div class="form-actions noborder row">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label class="control-label pull-right">
                                                            Purchase Request
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control typeahead">
                                                    </div>
                                                </div>
                                                <div class="table-scrollable" style="overflow: scroll !important;">
                                                    <table class="table table-striped table-bordered table-hover" id="purchaseRequestComponentTable" style="overflow: scroll; table-layout: fixed">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 12%"> Vendor </th>
                                                            <th style="width: 15%"> Material Name </th>
                                                            <th style="width: 10%"> Quantity </th>
                                                            <th style="width: 10%;"> Unit </th>
                                                            <th style="width: 10%"> Rate w/o Tax </th>
                                                            <th style="width: 10%"> Rate w/ Tax </th>
                                                            <th style="width: 10%"> Total Amount w/ Tax </th>
                                                            <th style="width: 10%">
                                                                Action
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-offset-3" style="margin-left: 26%">
                                                    <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
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
<div class="modal fade " id="detailsModal"  role="dialog">
    <div class="modal-dialog" style="width: 98%; height: 800px">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4" style="font-size: 21px"> Details </div>
                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                </div>
            </div>
            <input type="hidden" id="modalComponentID">
            <form id="componentDetailForm">
                {!! csrf_field() !!}
                <div class="modal-body">

                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('javascript')
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script>
        $(document).ready(function(){
            var citiList = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: "/purchase/purchase-order-request/purchase-request-auto-suggest/%QUERY",
                    filter: function(x) {
                        if($(window).width()<420){
                            $("#header").addClass("fixed");
                        }
                        return $.map(x, function (data) {
                            return {
                                id:data.id,
                                format_id: data.format_id
                            };
                        });
                    },
                    wildcard: "%QUERY"
                }
            });
            citiList.initialize();
            $('.typeahead').typeahead(null, {
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
                    suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{format_id}}</strong></div>')
                },
            }).on('typeahead:selected', function (obj, datum) {
                var POData = $.parseJSON(JSON.stringify(datum));
                $('.typeahead').typeahead('val',POData.format_id);
                var purchaseRequestId = POData.id;
                $("#purchaseRequestId").val(purchaseRequestId);
                $.ajax({
                    url: '/purchase/purchase-order-request/get-purchase-request-component-details',
                    type:'POST',
                    data:{
                        _token: $("input[name='_token']").val(),
                        purchase_request_id: purchaseRequestId
                    },
                    success: function(data, textStatus, xhr){
                        $("#purchaseRequestComponentTable tbody").html(data);
                    },
                    error: function(errorData){

                    }
                });
            })
            .on('typeahead:open', function (obj, datum) {

            });
        });

        function componentTaxDetailSubmit(){
            var formData = $("#componentDetailForm").serializeArray();
            var componentId = $("#modalComponentID").val();
            $("#componentRow-"+componentId+" #hiddenInputs").remove();
            $("<div id='hiddenInputs'></div>").insertAfter("#componentRow-"+componentId+" .component-vendor-relation");
            $.each(formData, function(key, value){
                if(value.name != 'vendor_images[]' && value.name != 'client_images[]'){
                    $("#componentRow-"+componentId+" #hiddenInputs").append("<input type='hidden' value='"+value.value+"' name='data["+componentId+"]["+value.name+"]'>");
                }else{
                    if(value.name != 'vendor_images[]'){
                        $("#componentRow-"+componentId+" #hiddenInputs").append("<input type='hidden' value='"+value.value+"' name='data["+componentId+"][vendor_images][]'>");
                    }else{
                        $("#componentRow-"+componentId+" #hiddenInputs").append("<input type='hidden' value='"+value.value+"' name='data["+componentId+"][client_images][]'>");
                    }
                }
            });
            var rate = $("input[name='data["+componentId+"][rate_per_unit]'").val();
            var cgst_percentage = $("input[name='data["+componentId+"][cgst_percentage]'").val();
            var sgst_percentage = $("input[name='data["+componentId+"][sgst_percentage]'").val();
            var igst_percentage = $("input[name='data["+componentId+"][igst_percentage]'").val();
            var rate_with_tax = parseFloat(rate) + parseFloat(rate * (cgst_percentage/100)) + parseFloat(rate * (sgst_percentage/100)) + parseFloat(rate * (igst_percentage/100));
            $("#componentRow-"+componentId+" .rate-without-tax").text(rate);
            $("#componentRow-"+componentId+" .rate-with-tax").text(rate_with_tax);
            $("#componentRow-"+componentId+" .total-with-tax").text($("input[name='data["+componentId+"][total]'").val());
            $('#detailsModal').modal('toggle');
        }
        function openDetailsModal(element, purchaseRequestComponentId){
            $("#modalComponentID").val(purchaseRequestComponentId);
            var rate = $(element).closest('tr').find('.rate-without-tax').text();
            $.ajax({
                url: '/purchase/purchase-order-request/get-component-tax-details/'+purchaseRequestComponentId+'?_token='+$("input[name='_token']").val(),
                type: 'POST',
                data:{
                    _token: $("input[name='_token']").val(),
                    rate: rate
                },
                success: function(data, textStatus, xhr){
                    $("#detailsModal .modal-body").html(data);
                    $("#detailsModal").modal('show');
                },
                error: function(errorData){

                }
            });
        }

        function calculateTaxes(element){
            var rate = parseFloat($(element).closest('.modal-body').find('.tax-modal-rate').val());
            if(typeof rate == 'undefined' || rate == '' || isNaN(rate)){
                rate = 0;
            }
            var quantity = parseFloat($(element).closest('.modal-body').find('.tax-modal-quantity').val());
            if(typeof quantity == 'undefined' || quantity == '' || isNaN(quantity)){
                quantity = 0;
            }
            var subtotal = rate * quantity;
            $(element).closest('.modal-body').find('.tax-modal-subtotal').val(subtotal);
            var cgstPercentage = parseFloat($(element).closest('.modal-body').find('.tax-modal-cgst-percentage').val());
            if(typeof cgstPercentage == 'undefined' || cgstPercentage == '' || isNaN(cgstPercentage)){
                cgstPercentage = 0;
            }
            var sgstPercentage = parseFloat($(element).closest('.modal-body').find('.tax-modal-sgst-percentage').val());
            if(typeof sgstPercentage == 'undefined' || sgstPercentage == '' || isNaN(sgstPercentage)){
                sgstPercentage = 0;
            }
            var igstPercentage = parseFloat($(element).closest('.modal-body').find('.tax-modal-igst-percentage').val());
            if(typeof igstPercentage == 'undefined' || igstPercentage == '' || isNaN(igstPercentage)){
                igstPercentage = 0;
            }
            var cgstAmount = subtotal * (cgstPercentage / 100);
            var sgstAmount = subtotal * (sgstPercentage / 100);
            var igstAmount = subtotal * (igstPercentage / 100);
            $(element).closest('.modal-body').find('.tax-modal-cgst-amount').val(cgstAmount);
            $(element).closest('.modal-body').find('.tax-modal-sgst-amount').val(sgstAmount);
            $(element).closest('.modal-body').find('.tax-modal-igst-amount').val(igstAmount);
            var total = subtotal + cgstAmount + sgstAmount + igstAmount;
            $(element).closest('.modal-body').find('.tax-modal-total').val(total);
        }
    </script>
@endsection
