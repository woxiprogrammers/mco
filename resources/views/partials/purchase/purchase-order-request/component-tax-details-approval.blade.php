<?php
/**
 * Created by PhpStorm.
 * User: Ameya Joshi
 * Date: 1/6/18
 * Time: 3:15 PM
 */
?>

<div class="row" style="height: 1000px">
    <div class="col-md-6" style="border-right: 1px solid grey;height: 100%;padding-top: 3%">
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Category :</label>
            </div>
            <div class="col-md-6">
                <select class="form-control" readonly="readonly" style="pointer-events: none">
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
                <input type="text" class="form-control tax-modal-quantity" name="quantity" value="{{$purchaseOrderRequestComponentData['quantity']}}" onkeyup="calculateTaxes(this)" readonly>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Unit</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" value="{{$purchaseOrderRequestComponentData['unit']}}" readonly>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Rate</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control tax-modal-rate" name="rate_per_unit" value="{{$purchaseOrderRequestComponentData['rate']}}" readonly>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Subtotal</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control tax-modal-subtotal" name="subtotal" readonly value="{!! round(($purchaseOrderRequestComponentData['quantity'] * $purchaseOrderRequestComponentData['rate']),3)!!}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">HSN Code :</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control tax-modal-hsn-code" value="{{$purchaseOrderRequestComponentData['hsn_code']}}" name="hsn_code" readonly>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">CGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    @if($purchaseOrderRequestComponentData['is_client'] == true)
                        <input type="text" class="form-control tax-modal-cgst-percentage" name="cgst_percentage" value="0" readonly>
                    @else
                        <input type="text" class="form-control tax-modal-cgst-percentage" name="cgst_percentage" value="{{$purchaseOrderRequestComponent['cgst_percentage']}}" readonly>
                    @endif
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control tax-modal-cgst-amount" readonly value="{{$purchaseOrderRequestComponent['cgst_amount']}}">
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
                        <input type="text" class="form-control tax-modal-sgst-percentage" name="sgst_percentage" value="{{$purchaseOrderRequestComponent['sgst_percentage']}}" readonly>
                    @endif
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control tax-modal-sgst-amount" readonly value="{{$purchaseOrderRequestComponent['sgst_amount']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">IGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    @if($purchaseOrderRequestComponentData['is_client'] == true)
                        <input type="text" class="form-control tax-modal-igst-percentage" name="igst_percentage" value="0" readonly>
                    @else
                        <input type="text" class="form-control tax-modal-igst-percentage" name="igst_percentage" value="{{$purchaseOrderRequestComponent['igst_percentage']}}" readonly>
                    @endif
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control tax-modal-igst-amount" readonly value="{{$purchaseOrderRequestComponent['igst_amount']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Total</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control tax-modal-total" readonly value="{{$purchaseOrderRequestComponent['total']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Expected Delivery Date</label>
            </div>
            <div class="col-md-6 date date-picker" data-date-start-date="0d">
                <input type="hidden" id="expected_delivery" value="{{$purchaseOrderRequestComponent['expected_delivery_date']}}">
                <input type="text" style="width: 40%" class="tax-modal-delivery-date  form-control" id="expected_delivery_date" name="expected_delivery_date" value="{{$purchaseOrderRequestComponent['expected_delivery_date']}}"/>
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
                <input type="text" class="form-control calculate-transportation-amount" name="transportation_amount" value="{{$purchaseOrderRequestComponent['transportation_amount']}}" readonly>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">CGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    <input type="text" class="form-control calculate-transportation-cgst-percentage" name="transportation_cgst_percentage" value="{{$purchaseOrderRequestComponent['transportation_cgst_percentage']}}" readonly>
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control calculate-transportation-cgst-amount" readonly value="{{$purchaseOrderRequestComponentData['transportation_cgst_amount']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">SGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    <input type="text" class="form-control calculate-transportation-sgst-percentage" name="transportation_sgst_percentage" value="{{$purchaseOrderRequestComponent['transportation_sgst_percentage']}}" readonly>
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
                    <input type="text" class="form-control calculate-transportation-igst-percentage"  value="{{$purchaseOrderRequestComponent['transportation_igst_percentage']}}" >
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control calculate-transportation-igst-amount" readonly  value="{{$purchaseOrderRequestComponentData['transportation_igst_amount']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Total</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control calculate-transportation-total" readonly value="{{$purchaseOrderRequestComponentData['transportation_total']}}">
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
                            <table class="table table-bordered table-hover" style="width: 700px">
                                <thead>
                                <tr role="row" class="heading">
                                    <th> File </th>
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
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
                            <table class="table table-bordered table-hover" style="width: 700px">
                                <thead>
                                <tr role="row" class="heading">
                                    <th> File </th>
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
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
    });
</script>
