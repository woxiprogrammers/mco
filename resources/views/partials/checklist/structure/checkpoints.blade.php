<div class="checkpoint">
    <fieldset>
        <legend style="margin-left: 15%">Checkpoint</legend>
        <div class="form-group">
            <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">
                <label for="title" class="control-label">Description</label>
                <span>*</span>
            </div>
            <div class="col-md-7">
                <textarea class="form-control" name="checkpoints[{{$index}}][description]"  placeholder="Enter Description" style="width: 80%"></textarea>
                <a class="btn blue" id="add" style="margin-left: 82%; margin-top: -10.5%" onclick="addCheckpoint()">
                    <i class="fa fa-plus"></i>
                </a>
                <a class="btn blue" style="margin-top: -10.5%" onclick="removeCheckpoint(this)">
                    <i class="fa fa-minus"></i>
                </a>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">
                <label for="title" class="control-label">Is Remark Mandatory</label>
                <span>*</span>
            </div>

            <div class="col-md-2">
                <select class="form-control" id="isMandatory" name="checkpoints[{{$index}}][is_mandatory]">
                    <option value="false" selected>No</option>
                    <option value="true">Yes</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">
                <label for="title" class="control-label"> No. of Images </label>
                <span>*</span>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control  number-of-image" name="checkpoints[{{$index}}][number_of_images]">
            </div>
            <div class="col-md-2">
                <a class="btn blue" href="javascript:void(0);" onclick="getImageTable(this,{{$index}})">Set</a>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-7 col-md-offset-3 image-table-section" >

            </div>
        </div>
    </fieldset>
</div>