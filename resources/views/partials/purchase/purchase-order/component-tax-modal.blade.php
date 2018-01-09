<?php
/**
 * Created by Ameya Joshi.
 * Date: 29/12/17
 * Time: 5:42 PM
 */
?>

<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">Material / Asset</label>
    </div>
    <div class="col-md-6">
        <input type="text" class="form-control" value="{{$data['name']}}" readonly>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">Rate</label>
    </div>
    <div class="col-md-6">
        <input type="text" class="form-control tax-modal-rate" value="{{$data['rate']}}" onkeyup="calculateTaxes(this)">
    </div>
</div>
<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">Quantity</label>
    </div>
    <div class="col-md-6">
        <input type="text" class="form-control tax-modal-quantity" value="{{$data['quantity']}}" onkeyup="calculateTaxes(this)">
    </div>
</div>
<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">Subtotal</label>
    </div>
    <div class="col-md-6">
        <input type="text" class="form-control tax-modal-subtotal" value="{!! $data['rate'] * $data['quantity'] !!}" readonly>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">CGST</label>
    </div>
    <div class="col-md-5">
        <div class="input-group" >
            <input type="text" class="form-control tax-modal-cgst-percentage" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][cgst_percentage]" onkeyup="calculateTaxes(this)" value="{{$data['cgst_percentage']}}">
            <span class="input-group-addon">%</span>
        </div>
    </div>
    <div class="col-md-5">
        <input type="text" class="form-control tax-modal-cgst-amount" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][cgst_amount]" readonly>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">SGST</label>
    </div>
    <div class="col-md-5">
        <div class="input-group" >
            <input type="text" class="form-control tax-modal-sgst-percentage" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][sgst_percentage]" onkeyup="calculateTaxes(this)" value="{{$data['sgst_percentage']}}">
            <span class="input-group-addon">%</span>
        </div>
    </div>
    <div class="col-md-5">
        <input type="text" class="form-control tax-modal-sgst-amount" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][sgst_amount]" readonly>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">IGST</label>
    </div>
    <div class="col-md-5">
        <div class="input-group" >
            <input type="text" class="form-control tax-modal-igst-percentage" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][igst_percentage]" onkeyup="calculateTaxes(this)" value="{{$data['igst_percentage']}}">
            <span class="input-group-addon">%</span>
        </div>
    </div>
    <div class="col-md-5">
        <input type="text" class="form-control tax-modal-igst-amount" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][igst_amount]" readonly>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">Total</label>
    </div>
    <div class="col-md-6">
        <input type="text" class="form-control tax-modal-total" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][total]" readonly>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-5">
        <a href="javascript:void(0)" class="btn red pull-right" onclick="submitTaxForm({{$data['purchase_request_component_id']}})">Submit</a>
    </div>
</div>


<script>
    $(document).ready(function(){
        $(".tax-modal-quantity").each(function(){
           calculateTaxes(this);
        });
    });
</script>