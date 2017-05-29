@extends('layout.master')
@section('title','Constro | Create Category')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
<div class="page-wrapper" xmlns="http://www.w3.org/1999/html">
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
                                        <ul class="nav nav-tabs">
                                            <li class="active">
                                                <a href="#tab_general" data-toggle="tab" id="tab_general_a"> General Information </a>
                                            </li>
                                            <li>
                                                <a  id="tab_meta_a"> Materials </a>
                                            </li>
                                            <li>
                                                <a href="#tab_profit_margin" data-toggle="tab" id="tab_price_a"> Profit Margins </a>
                                            </li>
                                        </ul>
                                        <form role="form" id="create-material" class="form-horizontal">
                                        <div class="tab-content">
                                            <div class="tab-pane fade in active" id="tab_general">

                                                    <div class="form-body">
                                                        <div class="form-group">
                                                            <label class="col-md-3 control-label">Product Title</label>
                                                            <div class="col-md-6">
                                                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Material Name">
                                                            </div>
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
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-offset-9">
                                                            <a class="btn btn-success btn-md" href="#tab_material" data-toggle="tab">Next >></a>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab_material">
                                                <fieldset>
                                                        <legend>Material Name</legend>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Rate</label>
                                                        <div class="col-md-6">
                                                            <input type="number" id="rate" name="rate" class="form-control" placeholder="Enter Rate">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Unit</label>
                                                        <div class="col-md-6">
                                                            <input type="number" id="unit" name="unit" class="form-control" placeholder="Unit" readonly>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="tab-pane fade" id="tab_profit_margin">

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

@endsection
