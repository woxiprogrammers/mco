<?php
/**
 * Created by PhpStorm.
 * User: Ameya Joshi
 * Date: 29/5/18
 * Time: 5:10 PM
 */
?>
<div class="row" style="height: 1000px">
    <div class="col-md-6" style="border-right: 1px solid grey;height: 100%;padding-top: 3%">
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Category :</label>
            </div>
            <input type="hidden" name="is_client" value="{{$purchaseOrderRequestComponentData['is_client']}}">
            <input type="hidden" id="purchaseRequestComponentId" name="purchase_order_request_component_id" value="{{$purchaseOrderRequestComponentData['id']}}">
            <div class="col-md-6">
                <select class="form-control" name="category_id">
                    @foreach($purchaseOrderRequestComponentData['categories'] as $category)
                        @if($category['id'] == $purchaseOrderRequestComponent['category_id'])
                            <option value="{{$category['id']}}" selected> {{$category['name']}} </option>
                        @else
                            <option value="{{$category['id']}}"> {{$category['name']}} </option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Name :</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" value="{{$purchaseOrderRequestComponentData['name']}}" readonly>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Quantity</label>
            </div>
            <div class="col-md-6">
                @if($purchaseOrderRequestComponentData['is_client'] == true)
                    <input type="text" class="form-control tax-modal-quantity" name="quantity" value="{{$purchaseOrderRequestComponentData['quantity']}}" onkeyup="calculateTaxes(this)" readonly>
                @else
                    <input type="text" class="form-control tax-modal-quantity" name="quantity" value="{{$purchaseOrderRequestComponentData['quantity']}}" onkeyup="calculateTaxes(this)">
                @endif
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Unit</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" value="{{$purchaseOrderRequestComponentData['unit']}}" readonly><input type="hidden" name="unit_id" value="{{$purchaseOrderRequestComponentData['unit_id']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Rate</label>
            </div>
            <div class="col-md-6">
                @if($purchaseOrderRequestComponentData['is_client'] == true)
                    <input type="text" class="form-control tax-modal-rate" name="rate_per_unit" value="{{$purchaseOrderRequestComponentData['rate']}}" onkeyup="calculateTaxes(this)" readonly>
                @else
                    <input type="text" class="form-control tax-modal-rate" name="rate_per_unit" value="{{$purchaseOrderRequestComponentData['rate']}}" onkeyup="calculateTaxes(this)">
                @endif
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Subtotal</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control tax-modal-subtotal" name="subtotal" readonly value="{!!$purchaseOrderRequestComponentData['quantity'] * $purchaseOrderRequestComponentData['rate']!!}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">HSN Code :</label>
            </div>
            <div class="col-md-6">
                @if($purchaseOrderRequestComponentData['is_client'] == true)
                    <input type="text" class="form-control tax-modal-hsn-code" value="{{$purchaseOrderRequestComponentData['hsn_code']}}" name="hsn_code" readonly>
                @else
                    <input type="text" class="form-control tax-modal-hsn-code" value="{{$purchaseOrderRequestComponentData['hsn_code']}}" name="hsn_code">
                @endif
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">CGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    @if($purchaseOrderRequestComponentData['is_client'] == true)
                        <input type="text" class="form-control tax-modal-cgst-percentage" name="cgst_percentage" value="0" onkeyup="calculateTaxes(this)" readonly>
                    @else
                        <input type="text" class="form-control tax-modal-cgst-percentage" name="cgst_percentage" value="{{$purchaseOrderRequestComponent['cgst_percentage']}}" onkeyup="calculateTaxes(this)">
                    @endif
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control tax-modal-cgst-amount" readonly name="cgst_amount" value="{{$purchaseOrderRequestComponent['cgst_amount']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">SGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    @if($purchaseOrderRequestComponentData['is_client'] == true)
                        <input type="text" class="form-control tax-modal-sgst-percentage" name="sgst_percentage" value="0" onkeyup="calculateTaxes(this)" readonly>
                    @else
                        <input type="text" class="form-control tax-modal-sgst-percentage" name="sgst_percentage" value="{{$purchaseOrderRequestComponent['sgst_percentage']}}" onkeyup="calculateTaxes(this)">
                    @endif
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control tax-modal-sgst-amount" readonly name="sgst_amount" value="{{$purchaseOrderRequestComponent['sgst_amount']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">IGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    @if($purchaseOrderRequestComponentData['is_client'] == true)
                        <input type="text" class="form-control tax-modal-igst-percentage" name="igst_percentage" value="0" onkeyup="calculateTaxes(this)" readonly>
                    @else
                        <input type="text" class="form-control tax-modal-igst-percentage" name="igst_percentage" value="{{$purchaseOrderRequestComponent['igst_percentage']}}" onkeyup="calculateTaxes(this)">
                    @endif
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control tax-modal-igst-amount" readonly name="igst_amount" value="{{$purchaseOrderRequestComponent['igst_amount']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Total</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control tax-modal-total" readonly name="total" value="{{$purchaseOrderRequestComponent['total']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Expected Delivery Date</label>
            </div>
            <div class="col-md-6 date date-picker" data-date-start-date="0d">
                <input type="hidden" id="expected_delivery" value="{{$purchaseOrderRequestComponent['expected_delivery_date']}}">
                <input type="text" style="width: 40%" class="tax-modal-delivery-date" id="expected_delivery_date" name="expected_delivery_date" value="{{$purchaseOrderRequestComponent['expected_delivery_date']}}"/>
                <button class="btn btn-sm default" type="button">
                    <i class="fa fa-calendar"></i>
                </button>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Transportation Amount</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control calculate-transportation-amount" name="transportation_amount" value="{{$purchaseOrderRequestComponent['transportation_amount']}}" onkeyup="calculateTransportationTaxes(this)">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">CGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    <input type="text" class="form-control calculate-transportation-cgst-percentage" name="transportation_cgst_percentage" value="{{$purchaseOrderRequestComponent['transportation_cgst_percentage']}}" onkeyup="calculateTransportationTaxes(this)">
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control calculate-transportation-cgst-amount" readonly name="transportation_cgst_amount" value="{{$purchaseOrderRequestComponentData['transportation_cgst_amount']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">SGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    <input type="text" class="form-control calculate-transportation-sgst-percentage" name="transportation_sgst_percentage" value="{{$purchaseOrderRequestComponent['transportation_sgst_percentage']}}" onkeyup="calculateTransportationTaxes(this)">
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control calculate-transportation-sgst-amount" readonly name="transportation_sgst_amount" value="{{$purchaseOrderRequestComponentData['transportation_sgst_amount']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">IGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    <input type="text" class="form-control calculate-transportation-igst-percentage" name="transportation_igst_percentage" value="{{$purchaseOrderRequestComponent['transportation_igst_percentage']}}" onkeyup="calculateTransportationTaxes(this)">
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control calculate-transportation-igst-amount" readonly name="transportation_igst_amount" value="{{$purchaseOrderRequestComponentData['transportation_igst_amount']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Total</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control calculate-transportation-total" readonly name="transportation_total" value="{{$purchaseOrderRequestComponentData['transportation_total']}}">
            </div>
        </div>


        <div class="row form-group">
            <div class="col-md-5">
                <a href="javascript:void(0)" class="btn red pull-right" id="detailModalSubmit" onclick="componentTaxDetailSubmit()">Submit</a>
            </div>
        </div>
    </div>
    <div class="col-md-6" style="height: 100%; padding-top: 1%">
        <div class="panel-group accordion" id="accordion3" style="margin-top: 3%">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: cornflowerblue">
                    <h4 class="panel-title">
                        <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2" style="font-size: 16px;color: white">
                            <b> Client Approval File </b>
                        </a>
                    </h4>
                </div>
                <div id="collapse_3_2" class="panel-collapse in">
                    <div class="panel-body" style="overflow-y:auto;">
                        <div class="form-group">
                            <div class="row">
                                <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12"> </div>
                            </div>
                            <div id="tab_images_uploader_container" class="col-md-offset-5">
                                <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow">
                                    Browse</a>
                                <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                    <i class="fa fa-share"></i> Upload Files </a>
                            </div>
                            <table class="table table-bordered table-hover" style="width: 700px">
                                <thead>
                                <tr role="row" class="heading">
                                    <th> File </th>
                                    <th> Action </th>
                                </tr>
                                </thead>
                                <tbody id="show-product-images">
                                    @foreach($purchaseOrderRequestComponentData['client_approval'] as $clientApprovalFile)
                                        @if($clientApprovalFile['path'] !=null)
                                            <tr id="image-{{$clientApprovalFile['random']}}">
                                                <td>
                                                    @if($clientApprovalFile['isPdf'] == true)
                                                        <span style="padding-right: 100px"><img src="/assets/global/img/pdf.png" height="30px" width="30px"></span>
                                                    @else
                                                        <span style="padding-right: 100px"><img src="/assets/global/img/image.png" height="30px" width="30px"></span>
                                                    @endif

                                                    <iframe id="myFrame" style="display:none" width="600" height="300"></iframe>
                                                    <a href="javascript:void(0);" class="btn btn-sm" onclick = "openPdf('{{$clientApprovalFile['random']}}','{{$clientApprovalFile['fullPath']}}')">Zoom In</a>
                                                    <input type="hidden" class="product-image-name" name="existing_client_file[{{$purchaseOrderRequestComponentData['id']}}][]" id="product-image-name-{{$clientApprovalFile['random']}}" value="{{$clientApprovalFile['path']}}"/>
                                                    <a href="javascript:void(0);"  class="btn btn-sm" onclick = "closePdf('{{$clientApprovalFile['random']}}','{{$clientApprovalFile['fullPath']}}')">Zoom Out</a>
                                                </td>
                                                <td>
                                                    <a href="javascript:;" class="btn btn-default btn-sm" onclick='removeProductImages("#image-{{$clientApprovalFile['random']}}","{{$clientApprovalFile['path']}}",0);'>
                                                        <i class="fa fa-times"></i> Remove
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="path" name="path" value="">
                <input type="hidden" id="max_files_count" name="max_files_count" value="20">
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: cornflowerblue">
                    <h4 class="panel-title">
                        <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2" style="font-size: 16px;color: white">
                            <b> Vendor Approval File </b>
                        </a>
                    </h4>
                </div>
                <div id="collapse_3_2" class="panel-collapse in">
                    <div class="panel-body" style="overflow-y:auto;">
                        <div class="form-group">
                            <div class="row">
                                <div id="tab_images_uploader_filelist_vendor" class="col-md-6 col-sm-12"> </div>
                            </div>
                            <div id="tab_images_uploader_container_vendor" class="col-md-offset-5">
                                <a id="tab_images_uploader_pickfiles_vendor" href="javascript:;" class="btn green-meadow">
                                    Browse</a>
                                <a id="tab_images_uploader_uploadfiles_vendor" href="javascript:;" class="btn btn-primary">
                                    <i class="fa fa-share"></i> Upload Files </a>
                            </div>
                            <table class="table table-bordered table-hover" style="width: 700px">
                                <thead>
                                <tr role="row" class="heading">
                                    <th> File </th>
                                    <th> Action </th>
                                </tr>
                                </thead>
                                <tbody id="show-product-images-vendor">
                                    @foreach($purchaseOrderRequestComponentData['vendor_quotation'] as $clientApprovalFile)
                                        @if($clientApprovalFile['path'] !=null)
                                            <tr id="image-{{$clientApprovalFile['random']}}">
                                                <td>
                                                    @if($clientApprovalFile['isPdf'] == true)
                                                        <span style="padding-right: 100px"><img src="/assets/global/img/pdf.png" height="30px" width="30px"></span>
                                                    @else
                                                        <span style="padding-right: 100px"><img src="/assets/global/img/image.png" height="30px" width="30px"></span>
                                                    @endif

                                                    <iframe id="myFrame" style="display:none" width="600" height="300"></iframe>
                                                    <a href="javascript:void(0);" class="btn btn-sm" onclick = "openPdf('{{$clientApprovalFile['random']}}','{{$clientApprovalFile['fullPath']}}')">Zoom In</a>
                                                    <input type="hidden" class="product-image-name" name="existing_vendor_file[{{$purchaseOrderRequestComponentData['id']}}][]"  id="product-image-name-{{$clientApprovalFile['random']}}" value="{{$clientApprovalFile['path']}}"/>
                                                    <a href="javascript:void(0);"  class="btn btn-sm" onclick = "closePdf('{{$clientApprovalFile['random']}}','{{$clientApprovalFile['fullPath']}}')">Zoom Out</a>
                                                </td>
                                                <td>
                                                    <a href="javascript:;" class="btn btn-default btn-sm" onclick='removeProductImages("#image-{{$clientApprovalFile['random']}}","{{$clientApprovalFile['path']}}",0);'>
                                                        <i class="fa fa-times"></i> Remove
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="vendorPath" name="path" value="">
                <input type="hidden" id="max_files_count" name="max_files_count" value="20">
            </div>
        </div>
    </div>
</div>

</div>
<script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
<script src="/assets/custom/purchase/purchase-order-request/file-datatable.js"></script>
<script src="/assets/custom/purchase/purchase-order-request/file-upload.js"></script>
<link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script><script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $('#expected_delivery_date').attr("readonly", "readonly");
        var date = new Date($('#expected_delivery').val());
        $('#expected_delivery_date').val(date.getDate()+"/"+(date.getMonth()+1)+"/"+date.getFullYear());

        $(".tax-modal-quantity").each(function(){
            calculateTaxes(this);
        });

    });
</script>

