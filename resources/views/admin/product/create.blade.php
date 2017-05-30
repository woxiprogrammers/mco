@extends('layout.master')
@section('title','Constro | Create Product')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
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
                                                <a href="#tab_profit_margin" data-toggle="tab" id="tab_price_a"> Profit Margins </a>
                                            </li>
                                        </ul>
                                        <form role="form" id="create-product" class="form-horizontal" action="/product/create" method="post">
                                            {!! csrf_field() !!}
                                            <div class="tab-content">
                                                <div class="tab-pane fade in active" id="tab_general">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Product Title</label>
                                                        <div class="col-md-6">
                                                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter Product Name">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Description</label>
                                                        <div class="col-md-6">
                                                            <textarea class="form-control" rows="2" id="description" name="description"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Unit</label>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="unit_id" name="unit_id">
                                                                @foreach($units as $unit)
                                                                <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Category</label>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="category_name" name="category_id">
                                                                @foreach($categories as $category)
                                                                    <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Material</label>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="material_name" name="material_id" multiple="true">

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-offset-9">
                                                            <a class="btn btn-success btn-md" id="next_btn">Next >></a>
                                                        </div>
                                                    </div>
                                                    <div class="materials-table-div">
                                                        <fieldset>
                                                            <legend> Materials</legend>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="productMaterialTable">
                                                                <tr>
                                                                    <th style="width: 25%"> Name </th>
                                                                    <th> Rate </th>
                                                                    <th> Unit </th>
                                                                    <th> Quantity </th>
                                                                    <th> Amount </th>
                                                                </tr>
                                                            </table>
                                                            <div class="col-md-offset-7">
                                                                <div class="col-md-3 col-md-offset-2">
                                                                    <label class="control-label" style="font-weight: bold">
                                                                        Sub Total
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="control-label">

                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="tab_profit_margin">
                                                    <div class="form-body">
                                                        @foreach($profitMargins as $profitMargin)
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label">{{$profitMargin['name']}}</label>
                                                                <div class="col-md-6">
                                                                    <input type="text" id="profit_margin_{{$profitMargin['id']}}" name="profit_margin[{{$profitMargin['id']}}]" class="form-control" value="{{$profitMargin['base_percentage']}}" required>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        <div class="form-group">
                                                            <div class="col-md-3 col-md-offset-4">
                                                                <button type="submit" class="btn btn-success"> Submit </button>
                                                            </div>
                                                        </div>
                                                    </div>
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
<script src="/assets/custom/admin/product/product.js"></script>
<script src="/assets/custom/admin/product/validations.js"></script>
<script>

</script>
@endsection
