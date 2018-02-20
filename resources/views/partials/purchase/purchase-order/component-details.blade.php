<?php
/**
 * Created by Ameya Joshi.
 * Date: 3/1/18
 * Time: 12:23 PM
 */
?>

<div class="form-body">
    {!! csrf_field() !!}
    <input type="hidden" id="minQuantity" value="{{$purchaseOrderComponentData['transaction_quantity']}}">
    <div class="form-group">
        <div class="col-md-3">
            <label class="pull-right control-label"> Name :</label>
        </div>
        <div class="col-md-6">
            <input type="hidden" name="purchase_order_component_id" value="{{$purchaseOrderComponentData['purchase_order_component_id']}}">
            <input type="text" class="form-control" value="{{$purchaseOrderComponentData['name']}}" readonly>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-3">
            <label class="pull-right control-label"> Rate :</label>
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" value="{{$purchaseOrderComponentData['rate_per_unit']}}" readonly>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-3">
            <label class="pull-right control-label"> Quantity :</label>
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control quantity" name="quantity" value="{{$purchaseOrderComponentData['quantity']}}">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-3">
            <label class="pull-right control-label"> Unit :</label>
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" value="{{$purchaseOrderComponentData['unit_name']}}" readonly>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-3">
            <label class="pull-right control-label"> HSN code :</label>
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" value="{!! $purchaseOrderComponentData['hsn_code']!!}" readonly>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-3">
            <label class="pull-right control-label"> Subtotal :</label>
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" value="{!! $purchaseOrderComponentData['rate_per_unit'] * $purchaseOrderComponentData['quantity'] !!}" readonly>
        </div>
    </div>
    @if($purchaseOrderComponent['cgst_percentage'] != null && $purchaseOrderComponent['cgst_amount'] != null)
        <div class="form-group">
            <div class="col-md-3">
                <label class="pull-right control-label"> CGST  :</label>
            </div>
            <div class="col-md-3">
                <div class="input-group" >
                    <input type="text" class="form-control" value="{{$purchaseOrderComponent['cgst_percentage']}}" readonly>
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" value="{{$purchaseOrderComponent['cgst_amount']}}" readonly>
            </div>
        </div>
    @endif
    @if($purchaseOrderComponent['sgst_percentage'] != null && $purchaseOrderComponent['sgst_amount'] != null)
        <div class="form-group">
            <div class="col-md-3">
                <label class="pull-right control-label"> SGST  :</label>
            </div>
            <div class="col-md-3">
                <div class="input-group" >
                    <input type="text" class="form-control" value="{{$purchaseOrderComponent['sgst_percentage']}}" readonly>
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" value="{{$purchaseOrderComponent['sgst_amount']}}" readonly>
            </div>
        </div>
    @endif
    @if($purchaseOrderComponent['igst_percentage'] != null && $purchaseOrderComponent['igst_amount'] != null)
        <div class="form-group">
            <div class="col-md-3">
                <label class="pull-right control-label"> IGST  :</label>
            </div>
            <div class="col-md-3">
                <div class="input-group" >
                    <input type="text" class="form-control" value="{{$purchaseOrderComponent['igst_percentage']}}" readonly>
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" value="{{$purchaseOrderComponent['igst_amount']}}" readonly>
            </div>
        </div>
    @endif
    @if($purchaseOrderComponent['total'] != null)
        <div class="form-group">
            <div class="col-md-3">
                <label class="pull-right control-label"> Total :</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" value="{{$purchaseOrderComponent['total']}}" readonly>
            </div>
        </div>
    @else
        <div class="form-group">
            <div class="col-md-3">
                <label class="pull-right control-label"> Total :</label>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" value="{!! $purchaseOrderComponentData['rate_per_unit'] * $purchaseOrderComponentData['quantity'] !!}" readonly>
            </div>
        </div>
    @endif
    @if(array_key_exists('material_component_images',$purchaseOrderComponentData))
        <div class="form-group">
            <div class="col-md-12">
                Vendor Quotation Image
                <!-- Wrapper for slides -->
                <div id ="imagecorousel">
                    @foreach($purchaseOrderComponentData['material_component_images'] as $vendorImages)
                        <a href="{{$vendorImages['name']}}">
                            <img id="image" src="{{$vendorImages['name']}}" style="text-align:left;height: 170px">
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    @if(array_key_exists('client_approval_images',$purchaseOrderComponentData))
        <br>
        <div class="form-group">
            <div class="col-md-12">
                Client Approval Note Image
                <div id ="imagecorouselForClientApproval">
                    @foreach($purchaseOrderComponentData['client_approval_images'] as $clientImages)
                        <a href="{{$clientImages['name']}}">
                            <img id="image" src="{{$clientImages['name']}}" style="text-align:left;height: 170px">
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    <div class="form-group">
        <div class="col-md-offset-3 col-md-3">
            <a class="btn red" href="javascript:void(0)" onclick="submitComponentForm()">Submit</a>
        </div>
    </div>
</div>