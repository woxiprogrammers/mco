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
                                        <a href="/product/manage">Manage Quotations</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Quotation</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-11">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <input type="hidden" id="productRowCount" value="1">
                                            <form role="form" id="QuotationGeneralForm" class="form-horizontal">
                                                {!! csrf_field() !!}
                                                <div class="tab-content">
                                                    <div class="tab-pane fade in active" id="GeneralTab">
                                                        <fieldset>
                                                            <legend>Project</legend>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label">Client Name</label>
                                                                <div class="col-md-6">
                                                                    <select class="form-control" id="client_id" name="client_id">

                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label">Project Name</label>
                                                                <div class="col-md-6">
                                                                    <select class="form-control" id="project_id" name="project_id" disabled>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-md-3 control-label">Project Site Name</label>
                                                                <div class="col-md-6">
                                                                    <select class="form-control" id="client_id" name="client_id" disabled>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-1 col-md-offset-1">

                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                        <fieldset>
                                                            <legend> Products <a class="btn btn-success btn-md col-md-offset-9" id="next_btn">Add Product</a></legend>
                                                            <table class="table table-bordered" id="productTable" style="overflow: scroll">
                                                                <tr>
                                                                    <th style="width: 18%"> Category </th>
                                                                    <th style="width: 18%"> Product </th>
                                                                    <th style="width: 25%"> Description</th>
                                                                    <th style="width: 10%"> Rate</th>
                                                                    <th style="width: 8%"> unit</th>
                                                                    <th style="width: 10%"> Quantity </th>
                                                                    <th  style="width: 10%"> Amount </th>
                                                                    <th> Action </th>
                                                                </tr>
                                                                <tr id="Row1">
                                                                    <td  style="background: linear-gradient(to top right, #fff 49.5%, #fff 50.5%);">
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
                                                                            <input name="product_rate[]" class="form-control quotation-product-table" id="productRate1" type="text" onchange="calculateAmount(1)" onkeyup="calculateAmount(1)" readonly>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input name="product_rate[]" class="form-control quotation-product-table" id="productUnit1" type="text" readonly>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input type="text" class="form-control quotation-product-table" name="product_quantity[]" id="productQuantity1" onchange="calculateAmount(1)" onkeyup="calculateAmount(1)" readonly>
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
                                                                                    <a href="javascript:void(0);">
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
                                                            <div class="col-md-3 col-md-offset-5">
                                                                <a class="btn btn-wide btn-primary" id="next1">
                                                                    Edit Material Cost
                                                                </a>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="tab-pane fade in" id="MaterialsTab">

                                                    </div>
                                                    <div class="tab-pane fade in" id="ProfitMarginsTab">

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
</div>
@endsection
@section('javascript')
<script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>
<script src="/assets/custom/admin/quotation/quotation.js"></script>
<script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#next_btn").on('click',function(){
            var rowCount = $('#productRowCount').val();
            $.ajax({
                url: '/quotation/add-product-row',
                type: 'POST',
                async: true,
                data: {
                    _token: $("input[name='_token']").val(),
                    row_count: rowCount
                },
                success: function(data,textStatus,xhr){
                    $("#productTable").append(data);
                    $('#productRowCount').val(parseInt(rowCount)+1);
                },
                error: function(errorStatus, xhr){

                }
            });
        });
    });


</script>
@endsection
