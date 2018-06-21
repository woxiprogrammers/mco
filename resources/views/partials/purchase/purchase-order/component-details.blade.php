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
            <input type="text" class="form-control" id="rate" value="{{$purchaseOrderComponentData['rate_per_unit']}}" readonly>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-3">
            <label class="pull-right control-label"> PO Quantity :</label>
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control quantity" id="po_quantity" name="quantity" value="{{$purchaseOrderComponentData['quantity']}}" onchange="calculateTotal()">
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
            <input type="text" id="subtotal" class="form-control" value="{!! $purchaseOrderComponentData['rate_per_unit'] * $purchaseOrderComponentData['quantity'] !!}" readonly>
        </div>
    </div>
    @if($purchaseOrderComponent['cgst_percentage'] != null && $purchaseOrderComponent['cgst_amount'] != null)
        <div class="form-group">
            <div class="col-md-3">
                <label class="pull-right control-label"> CGST  :</label>
            </div>
            <div class="col-md-3">
                <div class="input-group" >
                    <input type="text" id="cgst_percentage" class="form-control" value="{{$purchaseOrderComponent['cgst_percentage']}}" readonly>
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-3">
                <input type="text" id="cgst_amount" class="form-control" value="{{$purchaseOrderComponent['cgst_amount']}}" readonly>
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
                    <input type="text" id="sgst_percentage" class="form-control" value="{{$purchaseOrderComponent['sgst_percentage']}}" readonly>
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-3">
                <input type="text" id="sgst_amount" class="form-control" value="{{$purchaseOrderComponent['sgst_amount']}}" readonly>
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
                    <input type="text" id="igst_percentage" class="form-control" value="{{$purchaseOrderComponent['igst_percentage']}}" readonly>
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="col-md-3">
                <input type="text" id="igst_amount" class="form-control" value="{{$purchaseOrderComponent['igst_amount']}}" readonly>
            </div>
        </div>
    @endif
    @if($purchaseOrderComponent['total'] != null)
        <div class="form-group">
            <div class="col-md-3">
                <label class="pull-right control-label"> Total :</label>
            </div>
            <div class="col-md-6">
                <input type="text" id="total" name="total" class="form-control" value="{{$purchaseOrderComponent['total']}}" readonly>
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
                    <table class="table table-bordered table-hover" style="width: 50px">
                        <thead>

                        </thead>
                        <tbody id="show-product-images-vendor">
                            <tr role="row" class="heading">
                                @foreach($purchaseOrderComponentData['material_component_images'] as $vendorImages)
                                    <td>
                                        @if($vendorImages['extension'] == 'pdf')
                                            <span class="imageTag"><img src="/assets/global/img/pdf.png" height="30px" width="30px" onclick="openPdf(this,'{{$vendorImages['name']}}')"></span>
                                            <iframe id="myFrame" style="display:none" width="600" height="170"></iframe>
                                            <a href="javascript:void(0);"  class="btn btn-sm zoomOutButton" id="zoomOutButton" onclick = "closePdf(this,'{{$vendorImages['name']}}')">Close PDF</a>
                                        @else
                                            <span class="imageTag"><img src="/assets/global/img/image.png" height="30px" width="30px" onclick="openImage(this)"></span>
                                            <a href="javascript:void(0);"  class="btn btn-sm zoomOutButton" id="zoomOutButton" onclick = "closeImage(this)">Close Image</a>
                                            <div id="imageDiv" hidden>
                                                <a href="{{$vendorImages['name']}}">
                                                    <img id="image" src="{{$vendorImages['name']}}" style="text-align:left;height: 170px">
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
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

                    <table class="table table-bordered table-hover" style="width: 50px">
                        <thead>

                        </thead>
                        <tbody id="show-product-images-vendor">
                        <tr role="row" class="heading">
                            @foreach($purchaseOrderComponentData['client_approval_images'] as $clientImages)
                                <td>
                                    @if($clientImages['extension'] == 'pdf')
                                        <span class="imageTag"><img src="/assets/global/img/pdf.png" height="30px" width="30px" onclick="openPdf(this,'{{$clientImages['name']}}')"></span>
                                        <iframe id="myFrame" style="display:none" width="600" height="170"></iframe>
                                        <a href="javascript:void(0);"  class="btn btn-sm zoomOutButton" id="zoomOutButton" onclick = "closePdf(this,'{{$clientImages['name']}}')">Close PDF</a>
                                    @else
                                        <span class="imageTag"><img src="/assets/global/img/image.png" height="30px" width="30px" onclick="openImage(this)"></span>
                                        <a href="javascript:void(0);"  class="btn btn-sm zoomOutButton" id="zoomOutButton" onclick = "closeImage(this)">Close Image</a>
                                        <div id="imageDiv" hidden>
                                            <a href="{{$clientImages['name']}}">
                                                <img id="image" src="{{$clientImages['name']}}" style="text-align:left;height: 170px">
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        </tbody>
                    </table>
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

<script>

    function openPdf(element,vendorImage){
        var myFrameElement = $(element).closest('tr').find('#myFrame');
        $(myFrameElement).attr('src',vendorImage);
        $(myFrameElement).show();
    }
    function closePdf(element){
        var myFrameElement = $(element).closest('tr').find('#myFrame');
        $(myFrameElement).hide();
    }

    function openImage(element){
        $(element).closest('tr').find('#imageDiv').show();
    }

    function closeImage(element){
        $(element).closest('tr').find('#imageDiv').hide();
    }

    function calculateTotal(){
        var po_quantity = parseFloat($('#po_quantity').val());
        var rate = parseFloat($('#rate').val());
        var subtotal = po_quantity * rate;
        $('#subtotal').val(subtotal);
        var cgst_percentage = parseFloat($('#cgst_percentage').val());
        var cgst_amount = parseFloat((cgst_percentage * subtotal) / 100);
        $('#cgst_amount').val(cgst_amount);
        var sgst_percentage = parseFloat($('#sgst_percentage').val());
        var sgst_amount = parseFloat((sgst_percentage * subtotal) / 100);
        $('#sgst_amount').val(sgst_amount);
        var igst_percentage = parseFloat($('#igst_percentage').val());
        var igst_amount = parseFloat((igst_percentage * subtotal) / 100);
        $('#igst_amount').val(igst_amount);
        var total = parseFloat(subtotal + cgst_amount + sgst_amount + igst_amount);
        $('#total').val(total);
    }

</script>