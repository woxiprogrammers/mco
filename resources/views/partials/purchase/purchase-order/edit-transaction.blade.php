<?php
/**
 * Created by Ameya Joshi.
 * Date: 6/12/17
 * Time: 3:51 PM
 */
?>

<form id="transactionForm" action="/purchase/purchase-order/transaction/edit/{{$purchaseOrderTransaction->id}}" method="POST">
    {!! csrf_field() !!}
    <input type="hidden" name="purchase_order_id" value="{{$purchaseOrderTransaction['purchase_order_id']}}">
    <input type="hidden" id="type" value="upload_bill">
    <div class="form-body">
        <div class="form-group">
            <label class="control-label">Images Used For Generating GRN :</label>
            <div class="row">
                <div id="preview-image" class="row">
                    @foreach($preGrnImagePaths as $preGrnImagePath)
                        <div class="col-md-2">
                            <a target="_blank" href="{{$preGrnImagePath}}"><img src="{{$preGrnImagePath}}" class="thumbimage" /></a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div id="afterImageUploadDiv">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-1">
                            <label class="control-label pull-right"> GRN :</label>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" name="grn" value="{{$purchaseOrderTransaction->grn}}" readonly />
                        </div>
                        <div class="col-md-7">
                           &nbsp;
                        </div>
                </div>
            </div>
            <div id="componentDetailsDiv" style="margin-top: 5%;">
                <div class="row">
                <table class="table table-striped table-bordered table-hover" style="margin-top:1%" width="100%">
                    <tr style="text-align: center">
                        <th>
                            Name
                        </th>
                        <th>
                            Unit
                        </th>
                        <th style="width: 10%">
                            Quantity
                        </th>
                        @if($isShowTaxes == true || $isShowTaxes == 'true')
                            <th>
                                Rate
                            </th>
                            <th>
                                Subtotal
                            </th>
                            <th style="width: 7%">
                                CGST
                            </th>
                            <th style="width: 7%">
                                SGST
                            </th>
                            <th style="width: 7%">
                                IGST
                            </th>
                            <th>
                                Total
                            </th>
                        @endif
                    </tr>
                    @foreach($materialList as $material)
                        <tr style="text-align: center">
                            <td style="width: 20%">
                                <input type="text" class="form-control" readonly name="component_data[{{$material['purchase_order_component_id']}}][name]" value="{{$material['name']}}">
                            </td>
                            <td>
                                <select class="form-control" name="component_data[{{$material['purchase_order_component_id']}}][unit_id]">
                                    <option value="">-- Select Unit --</option>
                                    @foreach($material['units'] as $unit)
                                        @if($material['unit_id'] == $unit['id'])
                                            <option value="{{$unit['id']}}" selected>{{$unit['name']}}</option>
                                        @else
                                            <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                @if($material['quantityIsFixed'] == true)
                                    <input type="text" class="form-control" name="component_data[{{$material['purchase_order_component_id']}}][quantity]" value="1" readonly>
                                @else
                                    <input type="text" class="form-control" name="component_data[{{$material['purchase_order_component_id']}}][quantity]" value="{{$material['quantity']}}">
                                @endif
                            </td>
                            @if($isShowTaxes == true || $isShowTaxes == 'true')
                                <td>
                                    <input type="text" class="form-control" value="{!! $material['rate_per_unit'] !!}">
                                </td>
                                <td>
                                    <input type="text" class="form-control" value="{!! round(($material['rate_per_unit'] * $material['quantity']),3)!!}">
                                </td>
                                <td>
                                    <input type="text" class="form-control" value="{!! round((($material['rate_per_unit'] * $material['quantity']) * ($material['cgst_percentage'] / 100)),3) !!}">
                                </td>
                                <td>
                                    <input type="text" class="form-control" value="{!! round((($material['rate_per_unit'] * $material['quantity']) * ($material['sgst_percentage'] / 100)),3) !!}">
                                </td>
                                <td>
                                    <input type="text" class="form-control" value="{!! round((($material['rate_per_unit'] * $material['quantity']) * ($material['igst_percentage'] / 100)),3) !!}">
                                </td>
                                <td>
                                    <input type="text" class="form-control" value="{!! round(((($material['rate_per_unit'] * $material['quantity']) + (($material['rate_per_unit'] * $material['quantity']) * ($material['cgst_percentage'] / 100)) + (($material['rate_per_unit'] * $material['quantity']) * ($material['sgst_percentage'] / 100)) + (($material['rate_per_unit'] * $material['quantity']) * ($material['igst_percentage'] / 100)))),3) !!}">
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </table>
                </div>
            </div>
            <div id="transactionCommonFieldDiv">
                <div class="form-group row">
                    <div class="col-md-4">
                        <label>Vendor Company Name : </label>
                        <input type="text" class="form-control" id="vendor" name="vendor_name" placeholder="Enter Vendor Name" value="{{$vendorName}}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Bill Number : </label>
                        <input type="text" class="form-control" name="bill_number" placeholder="Enter Bill Number" value="{{$purchaseOrderTransaction->bill_number}}">
                    </div>
                    <div class="col-md-4">
                        <label>Vehicle Number : </label>
                        <input type="text" class="form-control" name="vehicle_number" placeholder="Enter Vehicle Number"  value="{{$purchaseOrderTransaction->vehicle_number}}">
                    </div>
                </div>
                <!--<div class="form-group row">
                    <input type="text" class="form-control" name="bill_amount" placeholder="Enter Bill Amount"  value="{{$purchaseOrderTransaction->bill_amount}}">
                </div>-->
                <div class="form-group row">
                    <div class="col-md-4">
                        <label>In Time : </label>
                        <input type="text" class="form-control" name="in_time" placeholder="Enter In Time"  value="{!! date('d-m-Y H:i:s', strtotime($purchaseOrderTransaction->in_time)) !!}">
                    </div>
                    <div class="col-md-4">
                        <label>Out Time : </label>
                        <input type="text" class="form-control" name="out_time" placeholder="Enter Out Time"  value="{!! date('d-m-Y H:i:s',strtotime($purchaseOrderTransaction->out_time)) !!}">
                    </div>
                    <div class="col-md-4">
                        <label>Remark : </label>
                        <input type="text" class="form-control" name="remark" placeholder="Enter Remark"  value="{{$purchaseOrderTransaction->remark}}">
                    </div>
                </div>
                @if(count($postGrnImagePaths) > 0)
                    <div class="form-group">
                        <label class="control-label">Post GRN Images :</label>
                        <br />
                        <div class="row">
                            <div id="postPreviewImage" class="row">
                                @foreach($postGrnImagePaths as $postGrnImagePath)
                                    <div class="col-md-2">
                                        <a target="_blank" href="{{$postGrnImagePath}}">
                                            <img src="{{$postGrnImagePath}}" class="thumbimage" />
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                @if($canEdit == true)
                    <button type="submit" class="btn btn-set red pull-right">
                        <i class="fa fa-check" style="font-size: large"></i>
                        Save&nbsp; &nbsp; &nbsp;
                    </button>
                @endif
            </div>
        </div>
    </div>
</form>
