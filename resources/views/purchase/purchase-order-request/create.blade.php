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
                <div class="row" style="height: 800px">
                    <div class="col-md-6" style="border-right: 1px solid grey;height: 100%;padding-top: 3%">
                        <div class="row form-group">
                            <div class="col-md-2">
                                <label class="control-label pull-right">Category :</label>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control">
                                    <option>
                                        --Category Name--
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2">
                                <label class="control-label pull-right">Name :</label>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2">
                                <label class="control-label pull-right">Rate</label>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control tax-modal-rate" name="rate_per_unit">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2">
                                <label class="control-label pull-right">Quantity</label>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control tax-modal-quantity" name="quantity">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2">
                                <label class="control-label pull-right">Subtotal</label>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control tax-modal-subtotal" name="subtotal" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2">
                                <label class="control-label pull-right">HSN Code :</label>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control tax-modal-subtotal" value="HSN1234" name="hsn_code">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2">
                                <label class="control-label pull-right">CGST</label>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group" >
                                    <input type="text" class="form-control tax-modal-cgst-percentage" name="cgst_percentage">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control tax-modal-cgst-amount" readonly name="cgst_amount">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2">
                                <label class="control-label pull-right">SGST</label>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group" >
                                    <input type="text" class="form-control tax-modal-sgst-percentage" name="sgst_percentage">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control tax-modal-sgst-amount" readonly name="sgst_amount">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2">
                                <label class="control-label pull-right">IGST</label>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group" >
                                    <input type="text" class="form-control tax-modal-igst-percentage" name="igst_percentage">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control tax-modal-igst-amount" readonly name="igst_amount">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2">
                                <label class="control-label pull-right">Total</label>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control tax-modal-total" readonly name="total">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2">
                                <label class="control-label pull-right">Expected Delivery Date</label>
                            </div>
                            <div class="col-md-6">
                                <input type="date" class="form-control tax-modal-total">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-5">
                                <a href="javascript:void(0)" class="btn red pull-right" id="detailModalSubmit">Submit</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" style="height: 100%; padding-top: 1%">
                        <div class="panel-group accordion" id="accordion3" style="margin-top: 3%">
                            <div class="panel panel-default">
                                <div class="panel-heading" style="background-color: cornflowerblue">
                                    <h4 class="panel-title">
                                        <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_1" style="font-size: 16px;color: white">
                                            <b> Vendor Quotation Image </b>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse_3_1" class="panel-collapse in">
                                    <div class="panel-body" style="overflow:auto;">
                                        <div class="form-group">
                                            <label class="control-label">Select Vendor Quotation Images  :</label>
                                            <input id="imageupload" type="file" class="btn green" multiple />
                                            <br />
                                            <div class="row">
                                                <div id="preview-image" class="row">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" style="background-color: cornflowerblue">
                                    <h4 class="panel-title">
                                        <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2" style="font-size: 16px;color: white">
                                            <b> Client Approval Image </b>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse_3_2" class="panel-collapse collapse">
                                    <div class="panel-body" style="overflow-y:auto;">
                                        <div class="form-group">
                                            <label class="control-label">Select Client Approval Images  :</label>
                                            <input id="clientImageUpload" type="file" class="btn green" multiple />
                                            <br />
                                            <div class="row">
                                                <div id="client-preview-image" class="row">

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
            $("#detailModalSubmit").on('click',function(){
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
                                var imagePreview = '<div class="col-md-4"><input type="hidden" value="'+e.target.result+'" name="vendor_images[]"><img src="'+e.target.result+'" style="height: 200px;width: 200px"/></div>';
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

            $("#clientImageUpload").on('change', function () {
                var countFiles = $(this)[0].files.length;
                var imgPath = $(this)[0].value;
                var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
                var image_holder = $("#client-preview-image");
                image_holder.empty();
                if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                    if (typeof (FileReader) != "undefined") {
                        for (var i = 0; i < countFiles; i++) {
                            var reader = new FileReader()
                            reader.onload = function (e) {
                                var imagePreview = '<div class="col-md-4"><input type="hidden" value="'+e.target.result+'" name="client_images[]"><img src="'+e.target.result+'" style="height: 200px;width: 200px"/></div>';
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
                console.log(' in open function');
            });
        });

        function openDetailsModal(elements, purchaseRequestComponentId){
            $("#modalComponentID").val(purchaseRequestComponentId);
            $("#detailsModal").modal('show');
        }
    </script>
@endsection
