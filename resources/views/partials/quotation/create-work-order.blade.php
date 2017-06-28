<form id="WorkOrderCreateForm" action="/quotation/work-order/create" method="post">
    {!! csrf_field() !!}
    <input type="hidden" name="quotation_id" value="{{$quotationId}}">
    <div class="col-md-offset-2">
        <div class="form-group">
            <div class="col-md-3">
                <label for="work_order_number" class="control-form pull-right">
                    Remark:
                </label>
            </div>
            <div class="col-md-3">
                <textarea class="form-control" name="remark" id="remark"></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-3">
                <label for="work_order_number" class="control-form pull-right">
                    Work Order Number:
                </label>
            </div>
            <div class="col-md-3">
                <input class="form-control" name="work_order_number" id="workOrderNumber" type="text">
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-3">
                <label for="description" class="control-form pull-right">
                    Description:
                </label>
            </div>
            <div class="col-md-3">
                <textarea class="form-control" name="description" id="workOrderDescription">

                </textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-3">
                <label for="scope" class="control-form pull-right">
                    Scope:
                </label>
            </div>
            <div class="col-md-3">
                <input class="form-control" name="scope" id="scope" type="text">
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-3">
                <label for="order_value" class="control-form pull-right">
                    Order Value:
                </label>
            </div>
            <div class="col-md-3">
                <input class="form-control" name="order_value" id="orderValue" type="number" step="any">
            </div>
        </div>
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
                    <th> Image </th>
                    <th> Action </th>
                </tr>
                </thead>
                <tbody id="show-product-images">

                </tbody>
            </table>
        </div>
        <div class="form-group">
            <div class="col-md-2 col-md-offset-4">
                <button type="submit" class="btn btn-success">
                    Submit
                </button>
            </div>
        </div>

</form>