<?php
/**
 * Created by Ameya Joshi.
 * Date: 5/6/17
 * Time: 4:10 PM
 */
?>

@extends('layout.master')
@section('title','Constro | Create Quotation')
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
                                    <h1>Create Quotation</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/quotation/manage/state#2">Manage Quotations</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Quotation</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <input type="hidden" id="productRowCount" value="1">
                                            <form role="form" id="QuotationCreateForm" class="form-horizontal" action="/quotation/create" method="post">
                                                {!! csrf_field() !!}
                                                <div class="tab-content">
                                                    <div class="tab-pane fade in active" id="GeneralTab">
                                                        <fieldset>
                                                            <legend>Project</legend>


                                                            <div class="row">

                                                                <div class="col-md-4 col-md-offset-0">
                                                                    Client Name
                                                                </div>
                                                                <div class="col-md-4">
                                                                    Project Name
                                                                </div>
                                                                <div class="col-md-4">
                                                                    Project Site Name
                                                                </div>

                                                            </div>


                                                            <div class="row">

                                                                <div class="col-md-4">
                                                                    <select class="form-control" id="clientId" name="client_id">
                                                                        <option value=""> -- Select Client -- </option>
                                                                        @foreach($clients as $client)
                                                                            <option value="{{$client['id']}}"> {{$client['company']}} </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <select name="project_id" id="projectId" class="form-control" disabled>

                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <select name="project_site_id" id="projectSiteId" class="form-control" disabled>

                                                                    </select>
                                                                </div>

                                                            </div>
                                                        </fieldset>


                                                        <fieldset style="margin-top: 1%; margin-bottom: 1%">
                                                            <legend> Products
                                                                <a class="btn btn-wide btn-primary  col-md-offset-8" id="materialCosts" >
                                                                    <i class="fa fa-pencil-square-o"></i>
                                                                    Material Rate
                                                                </a>
                                                                <a class="btn btn-success btn-md" id="addProduct">
                                                                    <i class="fa fa-plus"></i>
                                                                    Product
                                                                </a>
                                                            </legend>
                                                            <div class="table-scrollable">
                                                                <table class="table table-bordered" id="productTable" style="overflow: scroll;overflow-x: auto; overflow-y: auto">
                                                                    <tr>
                                                                        <th style="width: 18%"> Category </th>
                                                                        <th style="width: 18%"> Product </th>
                                                                        <th style="width: 25%"> Description</th>
                                                                        <th style="width: 8%"> Unit</th>
                                                                        <th style="width: 10%"> Rate</th>
                                                                        <th style="width: 10%"> Quantity </th>
                                                                        <th  style="width: 10%"> Amount </th>
                                                                        <th> Action </th>
                                                                    </tr>
                                                                    <tr id="Row1">
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <select class="form-control quotation-product-table quotation-category" id="categorySelect1" name="category_id[]">
                                                                                    @foreach($categories as $category)
                                                                                    <option value="{{$category['id']}}"> {{$category['name']}} </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <select class="form-control quotation-product-table quotation-product" name="product_id[]" id="productSelect1" disabled>

                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <input name="product_description[]" class="form-control quotation-product-table" onclick="replaceEditor(1)" id="productDescription1" type="text" readonly>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <input name="product_unit[]" class="form-control quotation-product-table" id="productUnit1" type="text" readonly>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <input name="product_rate[]" class="form-control quotation-product-table" id="productRate1" type="text" readonly>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <input type="number" step="any" class="form-control quotation-product-table" name="product_quantity[]" id="productQuantity1" onchange="calculateAmount(1)" onkeyup="calculateAmount(1)" readonly>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <input type="text" name="product_amount[]" class="form-control quotation-product-table" id="productAmount1" readonly>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <table>
                                                                                <tr style="border-bottom: 1px solid black">
                                                                                    <td>
                                                                                        <a href="javascript:void(0);" onclick="viewProduct(1)">
                                                                                            View
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <a href="javascript:void(0);" onclick="removeRow(1)">
                                                                                            Remove
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="tab-pane fade in" id="MaterialsTab">

                                                    </div>
                                                    <div class="tab-pane fade in" id="ProfitMarginsTab">
                                                        <fieldset class="row">
                                                            <a class="btn btn-primary" onclick="backToMaterials()" href="javascript:void(0);">
                                                                Back
                                                            </a>
                                                            <button type="submit" class="btn btn-success pull-right" id="formSubmit" hidden>
                                                                Submit
                                                            </button>
                                                        </fieldset>
                                                        <div id="profitMarginTable">

                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="productView" class="modal fade" role="dialog">
                                        <div class="modal-dialog product-view-modal">

                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">Product Analysis.</h4>
                                                </div>
                                                <div class="modal-body">

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
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>
<script src="/assets/custom/admin/quotation/quotation.js"></script>
<script src="/assets/custom/admin/quotation/validations.js"></script>
<script src="/assets/global/plugins/jquery-form.min.js"></script>

<script>
    $(document).ready(function(){
        CreateQuotation.init();
        var category_id = $("#categorySelect1").val();
        getProducts(category_id,1);
        var selectedProduct = $("#productSelect1").val();
        getProductDetails(selectedProduct, 1);
    });
</script>
@endsection
