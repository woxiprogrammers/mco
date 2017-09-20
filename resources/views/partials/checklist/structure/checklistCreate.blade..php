<div class="form-body"  id="example">
    <div class="tab-content">
        <div class="form-group row">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-5" style="text-align: right">
                        <label for="main_cat" class="control-label">Select Main Category Here</label>
                        <span>*</span>
                    </div>
                    <div class="col-md-6">
                        <select class="form-control" id="main_cat" name="main_cat">

                        </select>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <div class="tab-content">
                            <div class="col-md-5" style="text-align: right">
                                <label for="sub_cat" class="control-label">Select Sub Category Here</label>
                                <span>*</span>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" id="sub_cat" name="sub_cat">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3" style="text-align: right">

        <label for="title" class="control-label">Title</label>
        <span>*</span>
    </div>
    <div class="col-md-5">
        <input type="text" class="form-control" id="titlename" placeholder="Enter Title Here">
        <div id="sample_editable_1_new" class="btn yellow" style="margin-top: -8%; margin-left: 105%"><button style="color: white" id="add"><i class="fa fa-plus"></i> </button>
        </div>
    </div>
    <div class="form-body">
        <br>
        <br>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right">
                <label for="description" class="control-label">Description</label>
                <span>*</span>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description Here">
            </div>
        </div>

        <div class="form-body">
            <div class="form-group row">
                <div class="col-md-3" style="text-align: right">
                    <label for="no_images" class="control-label">Compulsory Number Of Images</label>
                    <span>*</span>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="no_images" name="no_images" placeholder="Enter Compulsory Number Of Images Here">
                </div>
            </div>
            <div class="form-group row">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3" style="text-align: right ;margin-top: -2%">
                <label for="is_special" class="control-label">Is Remark Mandatory ?</label>
                <span>*</span>
            </div>
            <div class="col-md-6" style="margin-top: -2%">
                <input type="checkbox" class="make-switch" data-on-text="Yes" data-off-text="No" name="is_special">
            </div>
        </div>
    </div>
</div>