    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <label class="control-label pull-right">
                GRN  :
            </label>
        </div>
        <div class="col-md-6">
            <label class="control-label pull-left">
                {{$assetMaintenanceTransaction->grn}}
            </label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <label class="control-label pull-right">
                Status  :
            </label>
        </div>
        <div class="col-md-6">
            <label class="control-label pull-left">
                {{$assetMaintenanceTransaction->assetMaintenanceTransactionStatus->name}}
            </label>
        </div>
    </div>


@if(isset($assetMaintenanceTransaction->remark) && $assetMaintenanceTransaction->remark != '')
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <label class="control-label pull-right">
                Remark  :
            </label>
        </div>
        <div class="col-md-6">
            <label class="control-label pull-left">
                {{$assetMaintenanceTransaction->remark}}
            </label>
        </div>
    </div>
@endif

@if(isset($assetMaintenanceTransaction->bill_number) && $assetMaintenanceTransaction->bill_number != '')
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <label class="control-label pull-right">
                Bill number  :
            </label>
        </div>
        <div class="col-md-6">
            <label class="control-label pull-left">
                {{$assetMaintenanceTransaction->bill_number}}
            </label>
        </div>
    </div>
@endif
@if(isset($assetMaintenanceTransaction->bill_amount) && $assetMaintenanceTransaction->bill_amount != '')
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <label class="control-label pull-right">
                Bill amount  :
            </label>
        </div>
        <div class="col-md-6">
            <label class="control-label pull-left">
                {{$assetMaintenanceTransaction->bill_amount}}
            </label>
        </div>
    </div>
@endif

@if(isset($assetMaintenanceTransaction->in_time) && $assetMaintenanceTransaction->in_time != '')
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <label class="control-label pull-right">
                In Time  :
            </label>
        </div>
        <div class="col-md-6">
            <label class="control-label pull-left">
                {{$assetMaintenanceTransaction->in_time}}
            </label>
        </div>
    </div>
@endif
@if(isset($assetMaintenanceTransaction->out_time) && $assetMaintenanceTransaction->out_time != '')
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <label class="control-label pull-right">
                Out Time  :
            </label>
        </div>
        <div class="col-md-6">
            <label class="control-label pull-left">
                {{$assetMaintenanceTransaction->out_time}}
            </label>
        </div>
    </div>
@endif

@if(isset($assetMaintenanceTransaction->created_at) && $assetMaintenanceTransaction->created_at != '')
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <label class="control-label pull-right">
                Date  :
            </label>
        </div>
        <div class="col-md-6">
            <label class="control-label pull-left">
                {!! date('d M Y',strtotime($assetMaintenanceTransaction->created_at)) !!}
            </label>
        </div>
    </div>
@endif
@if(count($imageData) > 0)
    <table class="table table-bordered table-hover">
        <thead>
        <tr role="row" class="heading">
            <th> Image </th>

        </tr>
        </thead>
        <tbody id="show-product-images">
        @foreach($imageData as $imagePath)
            <tr id="image">
                <td>
                    <a href="{{$imagePath['upload_path']}}" target="_blank" class="fancybox-button" data-rel="fancybox-button">
                        <img class="img-responsive" src="{{$imagePath['upload_path']}}" alt="" style="width:400px; height:200px;"> </a>
                    <input type="hidden" class="product-image-name" name="work_order_images[{{$imagePath['upload_path']}}][image_name]" id="product-image-name" value="{{$imagePath['upload_path']}}"/>
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>
@endif
