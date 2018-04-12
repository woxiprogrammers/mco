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
            <div class="col-md-6 date date-picker" data-date-start-date="0d">
                <input type="text" style="width: 40%" class="tax-modal-delivery-date" id="expected_delivery_date" name="expected_delivery_date" />
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
                <input type="text" class="form-control calculate-transportation-amount" name="transportation_amount" value="0" onkeyup="calculateTransportationTaxes(this)">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-2">
                <label class="control-label pull-right">CGST</label>
            </div>
            <div class="col-md-5">
                <div class="input-group" >
                    <input type="text" class="form-control calculate-transportation-cgst-percentage" name="transportation_cgst_percentage" value="0" onkeyup="calculateTransportationTaxes(this)">
                    <span class="input-group-addon">%</span>
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
                    <input type="text" class="form-control calculate-transportation-sgst-percentage" name="transportation_sgst_percentage" value="0" onkeyup="calculateTransportationTaxes(this)">
                    <span class="input-group-addon">%</span>
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
                    <input type="text" class="form-control calculate-transportation-igst-percentage" name="transportation_igst_percentage" value="0" onkeyup="calculateTransportationTaxes(this)">
                    <span class="input-group-addon">%</span>
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
                <div id="collapse_3_2" class="panel-collapse in">
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

<link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script><script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $('#expected_delivery_date').attr("readonly", "readonly");
        var date = new Date();
        $('#expected_delivery_date').val((date.getMonth()+1)+"/"+date.getDate()+"/"+date.getFullYear());

        $(".tax-modal-quantity").each(function(){
            calculateTaxes(this);
        });
    });
</script>
