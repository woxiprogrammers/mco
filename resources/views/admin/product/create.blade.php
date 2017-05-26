@extends('layout.master')
@section('title','Constro | Create Category')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
<div class="page-wrapper">
    <div class="page-wrapper-row full-height">
        <div class="page-wrapper-middle">
            <!-- BEGIN CONTAINER -->
            <div class="page-container">
                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <div class="page-head">
                        <div class="container">
                            <!-- BEGIN PAGE TITLE -->
                            <div class="page-title">
                                <h1>Create Product</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        <div class="container">
                            <div class="col-md-11">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <form role="form" id="create-material" class="form-horizontal">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Product Title</label>
                                                    <div class="col-md-6">
                                                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter Material Name">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Description</label>
                                                    <div class="col-md-6">
                                                            <textarea class="form-control" rows="2" id="description" name="description"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Category</label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="category_name" name="category_name">
                                                            <option>Category 1</option>
                                                            <option>Option 2</option>
                                                            <option>Option 3</option>
                                                            <option>Option 4</option>
                                                            <option>Option 5</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Material</label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="material_name" name="material_name" multiple="true">
                                                            <option>material 1</option>
                                                            <option>Option 2</option>
                                                            <option>Option 3</option>
                                                            <option>Option 4</option>
                                                            <option>Option 5</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                            <button type="submit" class="btn btn-dark btn-md">Add Material</button>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Material Name</label>
                                                    <div class="col-md-6">
                                                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter Material Name">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Rate</label>
                                                    <div class="col-md-6">
                                                        <input type="number" id="rate" name="rate" class="form-control" placeholder="Enter Rate">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Unit</label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="unit" name="unit">
                                                            <option>Option 1</option>
                                                            <option>Option 2</option>
                                                            <option>Option 3</option>
                                                            <option>Option 4</option>
                                                            <option>Option 5</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions noborder row">
                                                <div class="col-md-offset-3">
                                                    <button type="submit" class="btn btn-success btn-md" style="width:25%">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script src="/assets/custom/admin/material.js" type="application/javascript"></script>
<script>
    $(document).ready(function() {
       CreateMaterial.init();
    });
</script>
@endsection
