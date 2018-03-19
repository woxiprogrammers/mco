<?php
    /**
     * Created by Harsha.
     * User: harsha
     * Date: 19/3/18
     * Time: 10:15 AM
     */?>
<form id="transactionForm" role="form" action="/inventory/component/add-transfer/{{$inventoryComponentTransfer->inventoryComponent->id}}" method="POST" id="addTransferForm">
    {!! csrf_field() !!}
    <input type="hidden" name="transfer_type" value="site">
    <input type="hidden" name="in_or_out" value="on">
    <input type="hidden" name="inventory_component_transfer_id" value="{{$inventoryComponentTransfer->id}}">
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label pull-right">Selected GRN : </label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" value="{{$relatedTransferGRN}}" readonly>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label">Images Used For Generating GRN :</label>
        <div class="row">
            <div id="preview-image" class="row">
                @foreach($inventoryComponentTransferImages as $preGrnImagePath)
                    <div class="col-md-2">
                        <img src="{{$preGrnImagePath}}" class="thumbimage" />
                    </div>
                @endforeach
            </div>
        </div>

    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label pull-right">Site Details : </label>
        </div>
        <div class="col-md-8">
            <input class="form-control" type="text" id="siteDetails" value="{{$inventoryComponentTransfer['source_name']}}" readonly>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label pull-right">Quantity : </label>
        </div>
        <div class="col-md-8">
            <input class="form-control" type="text" id="quantity" name="quantity" value="{{$inventoryComponentTransfer['quantity']}}">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label pull-right">Unit : </label>
        </div>
        <div class="col-md-8">
            <input class="form-control" type="text" id="unit" value="{{$unit}}" readonly>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label pull-right">Transportation Amount : </label>
        </div>
        <div class="col-md-8">
            <input class="form-control" type="text" id="transportation_amount" value="{{$inventoryComponentTransfer['transportation_amount']}}" readonly>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label pull-right">Transportation Tax Amount : </label>
        </div>
        <div class="col-md-8">
            <input class="form-control" type="text" id="transportation_tax_amount" value="{{$inventoryComponentTransfer['transportation_tax_amount']}}" readonly>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label pull-right">Company name : </label>
        </div>
        <div class="col-md-8">
            <input class="form-control" type="text" id="company_name" value="{{$inventoryComponentTransfer['company_name']}}" readonly>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label pull-right">Driver Name : </label>
        </div>
        <div class="col-md-8">
            <input class="form-control" type="text" id="driver_name" value="{{$inventoryComponentTransfer['driver_name']}}" readonly>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label pull-right">Mobile : </label>
        </div>
        <div class="col-md-8">
            <input class="form-control" type="text" id="mobile" value="{{$inventoryComponentTransfer['mobile']}}" readonly>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label pull-right">Vehicle Number : </label>
        </div>
        <div class="col-md-8">
            <input class="form-control" type="text" id="vehicle_name" value="{{$inventoryComponentTransfer['vehicle_number']}}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <input type="text" class="form-control" name="remark" placeholder="Enter Remark">
    </div>
    <div class="form-group">
        <label class="control-label">Select Images :</label>
        <input id="imageUpload" type="file" class="btn blue" multiple />
        <br />
        <div class="row">
            <div id="previewImage" class="row">

            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-set red pull-right">
        <i class="fa fa-check" style="font-size: large"></i>
        Save&nbsp; &nbsp; &nbsp;
    </button>
</form>

<script>
    $(document).ready(function(){
        $("#imageUpload").on('change', function () {
            var countFiles = $(this)[0].files.length;
            var imgPath = $(this)[0].value;
            var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
            var image_holder = $("#previewImage");
            image_holder.empty();
            if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                if (typeof (FileReader) != "undefined") {
                    for (var i = 0; i < countFiles; i++) {
                        var reader = new FileReader()
                        reader.onload = function (e) {
                            var imagePreview = '<div class="col-md-2"><input type="hidden" name="post_grn_image[]" value="'+e.target.result+'"><img src="'+e.target.result+'" class="thumbimage" /></div>';
                            image_holder.append(imagePreview);
                        };
                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[i]);
                    }
                } else {
                    alert("It doesn't supports");
                }
            } else {
                alert("Select Only images");
            }
        });
    });

</script>
