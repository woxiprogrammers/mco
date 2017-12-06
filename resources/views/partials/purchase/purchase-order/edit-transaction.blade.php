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
                            <img src="{{$preGrnImagePath}}" class="thumbimage" />
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
        <div id="afterImageUploadDiv">

            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label pull-right"> GRN :</label>
                </div>
                <div class="col-md-6">
                    <input class="form-control" name="grn" value="{{$purchaseOrderTransaction->grn}}" readonly>
                </div>
            </div>
            @if($purchaseOrderTransaction->purchaseOrderTransactionStatus->slug == 'grn-generated')
                <div id="componentSelectDiv" style="margin-top: 5%;">
                    <div class="form-control product-material-select" style="font-size: 14px; height: 200px !important;" >
                        <ul id="material_id" class="list-group">
                            @foreach($materialList as $key => $materialData)
                                <li><input type="checkbox" class="component-select" value="{{$materialData['purchase_order_component_id']}}"><label class="control-label">{{$materialData['name']}} </label></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-3 col-md-offset-3" style="margin-top: 1%">
                        <a class="pull-right btn blue" href="javascript:void(0);" id="componentSelectButton"> Select </a>
                    </div>
                </div>
            @endif
            @if($purchaseOrderTransaction->purchaseOrderTransactionStatus->slug != 'grn-generated')
                <div id="componentDetailsDiv" style="margin-top: 5%;">
                    <table class="table table-striped table-bordered table-hover" style="margin-top:1%">
                        <tr style="text-align: center">
                            <th style="width: 40%">
                                Name
                            </th>
                            <th>
                                Unit
                            </th>
                            <th>
                                Quantity
                            </th>
                        </tr>
                        @foreach($materialList as $material)
                            <tr style="text-align: center">
                                <td style="width: 40%">
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
                                    @if($quantityIsFixed == true)
                                        <input type="text" class="form-control" name="component_data[{{$material['purchase_order_component_id']}}][quantity]" value="1" readonly>
                                    @else
                                        <input type="text" class="form-control" name="component_data[{{$material['purchase_order_component_id']}}][quantity]">
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                <div id="componentDetailsDiv" hidden style="margin-top: 5%;">

                </div>
            @endif
            <div id="transactionCommonFieldDiv" hidden>
                <div class="form-group row">
                    <label>Vendor Name</label>
                    <input type="text" class="form-control" id="vendor" name="vendor_name" placeholder="Enter Vendor Name" value="{{$vendorName}}" readonly>
                </div>
                <div class="form-group row">
                    <input type="text" class="form-control" name="bill_number" placeholder="Enter Bill Number" value="{{$purchaseOrderTransaction->bill_number}}">
                </div>
                <div class="form-group row">
                    <input type="text" class="form-control" name="bill_amount" placeholder="Enter Bill Amount"  value="{{$purchaseOrderTransaction->bill_number}}">
                </div>
                <div class="form-group row">
                    <input type="text" class="form-control" name="vehicle_number" placeholder="Enter Vehicle Number"  value="{{$purchaseOrderTransaction->bill_number}}">
                </div>
                <div class="form-group row">
                    <input type="datetime-local"   class="form-control" name="in_time" placeholder="Enter In Time"  value="{{$purchaseOrderTransaction->bill_number}}">
                </div>
                <div class="form-group row">
                    <input type="datetime-local" class="form-control" name="out_time" placeholder="Enter Out Time"  value="{{$purchaseOrderTransaction->bill_number}}">
                </div>
                <div class="form-group row">
                    <input type="text" class="form-control" name="remark" placeholder="Enter Remark"  value="{{$purchaseOrderTransaction->bill_number}}">
                </div>
                <div class="form-group">
                    <label class="control-label">Select Images :</label>
                    <br />
                    <div class="row">
                        <div id="postPreviewImage" class="row">
                            @foreach($postGrnImagePaths as $postGrnImagePath)
                                <div class="col-md-2">
                                    <img src="{{$postGrnImagePath}}" class="thumbimage" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-set red pull-right">
                    <i class="fa fa-check" style="font-size: large"></i>
                    Save&nbsp; &nbsp; &nbsp;
                </button>
            </div>
        </div>
    </div>
</form>
