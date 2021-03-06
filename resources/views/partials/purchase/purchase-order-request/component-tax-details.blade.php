<?php
/**
 * Created by Ameya Joshi.
 * Date: 17/1/18
 * Time: 11:44 AM
 */
?>
<div class="row" style="height: 1000px">
    <div class="col-md-6" style="border-right: 1px solid grey;height: 100%;padding-top: 3%">
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Category :</label>
            </div>
            <input type="hidden" name="is_client" value="{{$purchaseRequestComponentData['is_client']}}">
            <input type="hidden" id="purchaseRequestComponentId" name="purchaseRequestComponentId" value="{{$purchaseRequestComponentData['id']}}">
            <div class="col-md-6">
                <select class="form-control" name="category_id">
                    @foreach($purchaseRequestComponentData['categories'] as $category)
                        <option value="{{$category['id']}}">
                            {{$category['name']}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Name :</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" value="{{$purchaseRequestComponentData['name']}}" readonly>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Quantity</label>
            </div>
            <div class="col-md-6">
                @if($purchaseRequestComponentData['is_client'] == true)
                    <input type="text" class="form-control tax-modal-quantity" name="quantity" value="{{$purchaseRequestComponentData['quantity']}}" onkeyup="calculateTaxes(this)" readonly>
                @else
                    <input type="text" class="form-control tax-modal-quantity" name="quantity" value="{{$purchaseRequestComponentData['quantity']}}" onkeyup="calculateTaxes(this)">
                @endif
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Unit</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" value="{{$purchaseRequestComponentData['unit']}}" readonly><input type="hidden" name="unit_id" value="{{$purchaseRequestComponentData['unit_id']}}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Rate</label>
            </div>
            <div class="col-md-6">
                @if($purchaseRequestComponentData['is_client'] == true)
                    <input type="text" class="form-control tax-modal-rate" name="rate_per_unit" value="{{$purchaseRequestComponentData['rate']}}" onkeyup="calculateTaxes(this)" readonly>
                @else
                    <input type="text" class="form-control tax-modal-rate" name="rate_per_unit" value="{{$purchaseRequestComponentData['rate']}}" onkeyup="calculateTaxes(this)">
                @endif
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Subtotal</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control tax-modal-subtotal" name="subtotal" readonly value="{!!$purchaseRequestComponentData['quantity'] * $purchaseRequestComponentData['rate']!!}">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">HSN Code :</label>
            </div>
            <div class="col-md-6">
                @if($purchaseRequestComponentData['is_client'] == true)
                    <input type="text" class="form-control tax-modal-hsn-code" value="{{$purchaseRequestComponentData['hsn_code']}}" name="hsn_code" readonly>
                @else
                    <input type="text" class="form-control tax-modal-hsn-code" value="{{$purchaseRequestComponentData['hsn_code']}}" name="hsn_code">
                @endif
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">CGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    @if($purchaseRequestComponentData['is_client'] == true)
                        <input type="text" class="form-control tax-modal-cgst-percentage" name="cgst_percentage" value="0" onkeyup="calculateTaxes(this)" readonly>
                    @else
                        <input type="text" class="form-control tax-modal-cgst-percentage" name="cgst_percentage" value="0" onkeyup="calculateTaxes(this)">
                    @endif
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
                    @if($purchaseRequestComponentData['is_client'] == true)
                        <input type="text" class="form-control tax-modal-sgst-percentage" name="sgst_percentage" value="0" onkeyup="calculateTaxes(this)" readonly>
                    @else
                        <input type="text" class="form-control tax-modal-sgst-percentage" name="sgst_percentage" value="0" onkeyup="calculateTaxes(this)">
                    @endif
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
                    @if($purchaseRequestComponentData['is_client'] == true)
                        <input type="text" class="form-control tax-modal-igst-percentage" name="igst_percentage" value="0" onkeyup="calculateTaxes(this)" readonly>
                    @else
                        <input type="text" class="form-control tax-modal-igst-percentage" name="igst_percentage" value="0" onkeyup="calculateTaxes(this)">
                    @endif
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
            <div class="col-md-6 date date-picker" data-date-start-date="0d" >
                <input type="text" style="width: 40%" class="tax-modal-delivery-date" id="expected_delivery_date" name="expected_delivery_date" dateFormat= 'd-m-Y'/>
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
                @if($purchaseRequestComponentData['is_client'] == true)
                    <input type="text" class="form-control calculate-transportation-amount" name="transportation_amount" value="0" onkeyup="calculateTransportationTaxes(this)" readonly>
                @else
                    <input type="text" class="form-control calculate-transportation-amount" name="transportation_amount" value="0" onkeyup="calculateTransportationTaxes(this)">
                @endif
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">CGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    @if($purchaseRequestComponentData['is_client'] == true)
                        <input type="text" class="form-control calculate-transportation-cgst-percentage" name="transportation_cgst_percentage" value="0" onkeyup="calculateTransportationTaxes(this)" readonly>
                        <span class="input-group-addon">%</span>
                    @else
                        <input type="text" class="form-control calculate-transportation-cgst-percentage" name="transportation_cgst_percentage" value="0" onkeyup="calculateTransportationTaxes(this)">
                        <span class="input-group-addon">%</span>
                    @endif
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control calculate-transportation-cgst-amount" readonly name="transportation_cgst_amount">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">SGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    @if($purchaseRequestComponentData['is_client'] == true)
                        <input type="text" class="form-control calculate-transportation-sgst-percentage" name="transportation_sgst_percentage" value="0" onkeyup="calculateTransportationTaxes(this)" readonly>
                        <span class="input-group-addon">%</span>
                    @else
                        <input type="text" class="form-control calculate-transportation-sgst-percentage" name="transportation_sgst_percentage" value="0" onkeyup="calculateTransportationTaxes(this)">
                        <span class="input-group-addon">%</span>
                    @endif
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control calculate-transportation-sgst-amount" readonly name="transportation_sgst_amount">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">IGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    @if($purchaseRequestComponentData['is_client'] == true)
                        <input type="text" class="form-control calculate-transportation-igst-percentage" name="transportation_igst_percentage" value="0" onkeyup="calculateTransportationTaxes(this)" readonly>
                        <span class="input-group-addon">%</span>
                    @else
                        <input type="text" class="form-control calculate-transportation-igst-percentage" name="transportation_igst_percentage" value="0" onkeyup="calculateTransportationTaxes(this)">
                        <span class="input-group-addon">%</span>
                    @endif
                </div>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control calculate-transportation-igst-amount" readonly name="transportation_igst_amount">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">Total</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control calculate-transportation-total" readonly name="transportation_total">
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
        $("#expected_delivery_date").datepicker({dateFormat: 'd-m-Y'});
        $('#expected_delivery_date').attr("readonly", "readonly");
        var date = new Date();
        //$('#expected_delivery_date').val((date.getMonth()+1)+"/"+date.getDate()+"/"+date.getFullYear());
        $('#expected_delivery_date').val((date.getDate())+"-"+(date.getMonth()+1)+"-"+date.getFullYear());

        $(".tax-modal-quantity").each(function(){
            calculateTaxes(this);
        });
        
    });
</script>
