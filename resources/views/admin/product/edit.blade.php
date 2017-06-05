@extends('layout.master')
@section('title','Constro | Edit Product')
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
                                <h1>Edit Product</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/product/manage">Back</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-11">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <form role="form" id="edit-product" class="form-horizontal" action="/product/edit/{{$product['id']}}" method="post">
                                            {!! csrf_field() !!}
                                            <div>
                                                <fieldset>
                                                    <legend> General Information </legend>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Product Title</label>
                                                        <div class="col-md-6">
                                                            <input type="text" id="name" name="name" class="form-control" value="{{$product['name']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Description</label>
                                                        <div class="col-md-6">
                                                            <textarea class="form-control" rows="2" id="description" name="description">{{$product['description']}}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Unit</label>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="unit_id" name="unit_id">
                                                                @foreach($units as $unit)
                                                                @if($unit['id'] == $product['unit_id'])
                                                                <option value="{{$unit['id']}}" selected>{{$unit['name']}}</option>
                                                                @else
                                                                <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                                                                @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Category</label>
                                                        <div class="col-md-6">
                                                            <input type="hidden" name="category_id" value="{{$product['category_id']}}">
                                                            <input class="form-control" value="{{$product['category']}}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Material</label>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="material_id" multiple="true">
                                                                @foreach($materials as $material)
                                                                @if(in_array($material['id'],$productMaterialIds))
                                                                <option value="{{$material['id']}}" selected> {{$material['name']}}</option>
                                                                @else
                                                                <option value="{{$material['id']}}"> {{$material['name']}}</option>
                                                                @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-offset-9">
                                                            <a class="btn btn-success btn-md" id="next_btn"> Add </a>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="material_version_ids" value="{{$materialVersionIds}}">
                                                </fieldset>
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
                                                            @foreach($productMaterialVersions as $version)
                                                                <tr>
                                                                    <td>
                                                                        <label>
                                                                            {{$version['name']}}
                                                                        </label>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input class="form-control material-table-input" step="any" type="number" id="material_version_{{$version['id']}}_rate" name="material_version[{{$version['id']}}][rate_per_unit]" value="{{$version['rate_per_unit']}}" onkeyup="changedQuantity({{$version['id']}})" onchange="changedQuantity({{$version['id']}})">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <select class="form-control material-table-input" name="material_version[{{$version['id']}}][unit_id]"  id="material_version_{{$version['id']}}_unit" onchange="convertUnits({{$version['id']}})">
                                                                                @foreach($units as $unit)
                                                                                    @if($unit['id'] == $version['unit_id'])
                                                                                        <option value="{{$unit['id']}}" selected>{{$unit['name']}}</option>
                                                                                    @else
                                                                                        <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                                                                                    @endif
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input type="number" step="any" class="form-control material-table-input" id="material_version_{{$version['id']}}_quantity" name="material_quantity[{{$version['id']}}]" onkeyup="changedQuantity({{$version['id']}})" onchange="changedQuantity({{$version['id']}})" value="{{$version['quantity']}}" required>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input type="text" class="form-control material_amount material-table-input" id="material_version_{{$version['id']}}_amount" name="material_amount[{{$version['id']}}]" value="{!! $version['quantity']*$version['rate_per_unit'] !!}">
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                        <div class="col-md-offset-7">
                                                            <div class="col-md-3 col-md-offset-3">
                                                                <label class="control-label" style="font-weight: bold">
                                                                    Sub Total:
                                                                </label>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="control-label" style="font-weight: bold" id="subtotal">

                                                                </label>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset>
                                                        <legend> Profit Margins </legend>
                                                        <div class="form-body">
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="productMaterialTable">
                                                                <tr>
                                                                    <th style="width: 33%"> Profit Margin Name </th>
                                                                    <th style="width: 46%"> Percentage </th>
                                                                    <th style="width: 33%"> Amount </th>
                                                                </tr>
                                                                @foreach($profitMargins as $profitMargin)
                                                                <tr>
                                                                    <td>
                                                                        {{$profitMargin['name']}}
                                                                    </td>
                                                                    <td>
                                                                        <input class="profit-margin form-control" step="any" type="number" id="profit_margin_{{$profitMargin['id']}}" name="profit_margin[{{$profitMargin['id']}}]" class="form-control" value="{{$productProfitMargins[$profitMargin['id']]}}" onchange="calculateProfitMargin()" onkeyup="calculateProfitMargin()"required>
                                                                    </td>
                                                                    <td class="profit-margin-amount">

                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </table>
                                                            <div class="col-md-offset-7">
                                                                <div class="col-md-3 col-md-offset-3" style="align-items: ">
                                                                    <label class="control-label" style="font-weight: bold; text-align: right">
                                                                        Total:
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-2">
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
    $(document).ready(function(){
        EditProduct.init();
    });
</script>
@endsection

