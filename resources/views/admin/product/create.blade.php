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
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/product/manage">Manage Product</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Create Product</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-11">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <form role="form" id="create-product" class="form-horizontal" action="/product/create" method="post">
                                            {!! csrf_field() !!}
                                            <div class="tab-content">
                                                <div class="tab-pane fade in active" id="tab_general">
                                                    <fieldset>
                                                        <legend> General Information </legend>
                                                        <div class="form-group">
                                                            <label class="col-md-3 control-label">Product Title</label>
                                                            <div class="col-md-6">
                                                                <input type="text" id="name" name="name" class="form-control typeahead" placeholder="Enter Product Name">
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
                                                                <select class="form-control" id="material_id" multiple="true" style="overflow: scroll">

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-offset-9">
                                                                <a class="btn btn-success btn-md" id="next_btn">Next >></a>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="materials-table-div" hidden>
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
                                                                    <label class="control-label" style="font-weight: bold" id="subtotal">

                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <fieldset>
                                                            <legend> Profit Margins </legend>
                                                            <div class="form-body">
                                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="productProfitMarginTable">
                                                                    <tr>
                                                                        <th style="width: 33%"> Profit Margin Name </th>
                                                                        <th style="width: 33%"> Percentage </th>
                                                                        <th style="width: 33%"> Amount </th>
                                                                    </tr>
                                                                    @foreach($profitMargins as $profitMargin)
                                                                    <tr>
                                                                        <td>
                                                                            {{$profitMargin['name']}}
                                                                        </td>
                                                                        <td>
                                                                            <input class="profit-margin form-control" step="any" type="number" id="profit_margin_{{$profitMargin['id']}}" name="profit_margin[{{$profitMargin['id']}}]" class="form-control" value="{{$profitMargin['base_percentage']}}" onchange="calculateProfitMargin()" onkeyup="calculateProfitMargin()"required>
                                                                        </td>
                                                                        <td class="profit-margin-amount">

                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                </table>
                                                                <div class="col-md-offset-6">
                                                                    <div class="col-md-5 col-md-offset-2" style="align-items: ">
                                                                        <label class="control-label" style="font-weight: bold; text-align: right">
                                                                            Total:
                                                                        </label>
                                                                        <label class="control-label" style="font-weight: bold; margin-left: 1%" id="total">

                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="col-md-3 col-md-offset-4">
                                                                        <button type="submit" class="btn btn-success"> Submit </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </fieldset>
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
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<script>
    $(document).ready(function(){
        CreateProduct.init();
        var citiList = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: "/product/auto-suggest/%QUERY",
                filter: function(x) {
                    if($(window).width()<420){
                        $("#header").addClass("fixed");
                    }
                    return $.map(x, function (data) {
                        return {
                            id:data.id,
                            name:data.name,
                        };
                    });
                },
                wildcard: "%QUERY"
            }
        });
        citiList.initialize();
        $('.typeahead').typeahead(null, {
            displayKey: 'name',
            engine: Handlebars,
            source: citiList.ttAdapter(),
            limit: 30,
            templates: {
                empty: [
                    '<div class="empty-suggest">',
                    '</div>'
                ].join('\n'),
                suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{name}}</strong></div>')
            },
        }).on('typeahead:selected', function (obj, datum) {

            }).on('typeahead:open', function (obj, datum) {

                });
    });
</script>
@endsection
