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
        <input type="text" class="form-control" name="rate" value="{{$data['rate']}}">
    </div>
</div>
<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">Quantity</label>
    </div>
    <div class="col-md-6">
        <input type="text" class="form-control" name="quantity" value="{{$data['quantity']}}">
    </div>
</div>
<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">Subtotal</label>
    </div>
    <div class="col-md-6">
        <input type="text" class="form-control" name="subtotal" value="{!! $data['rate'] * $data['quantity'] !!}">
    </div>
</div>
<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">CGST</label>
    </div>
    <div class="col-md-5">
        <div class="input-group" >
            <input type="text" class="form-control" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][cgst_percentage]">
            <span class="input-group-addon">%</span>
        </div>
    </div>
    <div class="col-md-5">
        <input type="text" class="form-control" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][cgst_amount]">
    </div>
</div>
<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">SGST</label>
    </div>
    <div class="col-md-5">
        <div class="input-group" >
            <input type="text" class="form-control" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][sgst_percentage]">
            <span class="input-group-addon">%</span>
        </div>
    </div>
    <div class="col-md-5">
        <input type="text" class="form-control" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][sgst_amount]">
    </div>
</div>
<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">IGST</label>
    </div>
    <div class="col-md-5">
        <div class="input-group" >
            <input type="text" class="form-control" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][igst_percentage]">
            <span class="input-group-addon">%</span>
        </div>
    </div>
    <div class="col-md-5">
        <input type="text" class="form-control" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][igst_amount]">
    </div>
</div>
<div class="row form-group">
    <div class="col-md-2">
        <label class="control-label pull-right">Total</label>
    </div>
    <div class="col-md-6">
        <input type="text" class="form-control" name="purchase[{{$data['vendor_id']}}][{{$data['purchase_request_component_id']}}][total]">
    </div>
</div>
<div class="row form-group">
    <div class="col-md-5">
        <a href="javascript:void(0)" class="btn red pull-right" onclick="submitTaxForm()">Submit</a>
    </div>
</div>
