<?php
/**
 * Created by Ameya Joshi.
 * Date: 21/6/17
 * Time: 12:38 PM
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
                                <h1>Edit Quotation</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/quotation/manage">Manage Quotations</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Edit Quotation</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <input type="hidden" id="productRowCount" value="1">
                                        <input type="hidden" id="quotationId" value="{{$quotation->id}}">
                                        <form role="form" id="QuotationEditForm" class="form-horizontal" action="/quotation/edit/{{$quotation->id}}" method="post">
                                            {!! csrf_field() !!}
                                            <input type="hidden" name="_method" value="put">
                                            <div class="tab-content">
                                                <div class="tab-pane fade in active" id="GeneralTab">
                                                    <fieldset class="row">
                                                        <a class="col-md-offset-1 btn green-meadow" id="approve">
                                                            Approve
                                                        </a>
                                                        <a class="col-md-offset-1 btn btn-danger" id="disapprove">
                                                            Disaaprove
                                                        </a>
                                                        <a class="col-md-offset-1 btn btn-primary" id="materialCosts">
                                                            Change Material Cost
                                                        </a>
                                                        <a class="col-md-offset-1 btn btn-wide btn-primary" href="javascript:void(0)" onclick="showProfitMargins()" id="profitMargins">
                                                            Change Profit Margins
                                                        </a>
                                                    </fieldset>
                                                    <div class="panel-group accordion" id="accordion3" style="margin-top: 3%">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h4 class="panel-title">
                                                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_1" style="font-size: 16px"><b> Project Details </b></a>
                                                                </h4>
                                                            </div>
                                                            <div id="collapse_3_1" class="panel-collapse in">
                                                                <div class="panel-body" style="font-size: 15px">
                                                                    <div class="row" style="margin-left: 2%">
                                                                        <div class="col-md-3">
                                                                            <label class="control-label"><b>Client Name:</b></label>
                                                                            <label class="control-label"> {{$quotation->project_site->project->client->company}} </label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label class="control-label"><b>Project Name:</b></label>
                                                                            <label class="control-label">{{$quotation->project_site->project->name}}</label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label class="control-label"><b>Project Site Name:</b></label>
                                                                            <label class="control-label">{{$quotation->project_site->name}}</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row" style="margin-left: 3%">
                                                                        <div>
                                                                            <label class="control-label"><b>Project Site Address:</b></label>
                                                                            <label class="control-label">{{$quotation->project_site->address}}</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h4 class="panel-title">
                                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2" style="font-size: 16px"><b> Product Details </b></a>
                                                                </h4>
                                                            </div>
                                                            <div id="collapse_3_2" class="panel-collapse collapse">
                                                                <div class="panel-body" style="height:400px; overflow-y:auto;">
                                                                    <a class="btn btn-success btn-md col-md-offset-11" id="addProduct">Add Product</a>
                                                                    <div class="table-scrollable">
                                                                        <table class="table table-bordered" id="productTable" style="overflow: scroll;overflow-x: auto; overflow-y: auto">
                                                                            <tr>
                                                                                <th style="width: 10%"> Category </th>
                                                                                <th style="width: 10%"> Product </th>
                                                                                <th style="width: 18%"> Description</th>
                                                                                <th style="width: 8%"> Unit</th>
                                                                                <th style="width: 8%"> Rate</th>
                                                                                <th style="width: 5%"> Quantity </th>
                                                                                <th  style="width: 8%"> Amount </th>
                                                                                <th  style="width: 8%"> Discounted Amount </th>
                                                                                <th  style="width: 15%"> Summary </th>
                                                                                <th> Action </th>
                                                                            </tr>
                                                                            @for($iterator=0; $iterator < count($quotation->quotation_products); $iterator++)
                                                                                <tr id="Row{{$iterator}}">
                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            {{$quotation->quotation_products[$iterator]->product->category->name}}
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <input type="hidden" class="quotation-product" value="{{$quotation->quotation_products[$iterator]->product_id}}" id="productSelect{{$iterator}}" name="product_id[]">
                                                                                            {{$quotation->quotation_products[$iterator]->product->name}}
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <input name="product_description[{{$quotation->quotation_products[$iterator]->product_id}}]" class="form-control quotation-product-table" onclick="replaceEditor({{$iterator}})" id="productDescription{{$iterator}}" type="text" value="{{$quotation->quotation_products[$iterator]->description}}" readonly>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <input name="product_unit[{{$quotation->quotation_products[$iterator]->product_id}}]" class="form-control quotation-product-table" id="productUnit{{$iterator}}" type="text" value="{{$quotation->quotation_products[$iterator]->product->unit->name}}" readonly>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <input name="product_rate[{{$quotation->quotation_products[$iterator]->product_id}}]" class="form-control quotation-product-table" id="productRate{{$iterator}}" type="text" value="{{$quotation->quotation_products[$iterator]->rate_per_unit}}" readonly>
                                                                                        </div>
                                                                                    </td>

                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <input type="number" step="any" class="form-control quotation-product-table" name="product_quantity[{{$quotation->quotation_products[$iterator]->product_id}}]" id="productQuantity{{$iterator}}" onchange="calculateAmount({{$iterator}})" onkeyup="calculateAmount({{$iterator}})"  value="{{$quotation->quotation_products[$iterator]->quantity}}" readonly>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <input type="text" name="product_amount[{{$quotation->quotation_products[$iterator]->product_id}}]" class="form-control quotation-product-table product-amount" id="productAmount{{$iterator}}" value="{!!$quotation->quotation_products[$iterator]->rate_per_unit*$quotation->quotation_products[$iterator]->quantity!!}" readonly>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <input type="text" name="product_discount_amount[{{$quotation->quotation_products[$iterator]->product_id}}]" class="form-control quotation-product-table product-discount-amount" id="productDiscountAmount{{$iterator}}" value="{!!($quotation->quotation_products[$iterator]->rate_per_unit*$quotation->quotation_products[$iterator]->quantity)-($quotation->quotation_products[$iterator]->rate_per_unit*$quotation->quotation_products[$iterator]->quantity*($quotation->discount/100))!!}" readonly>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <select class="form-control" name="product_summary[{{$quotation->quotation_products[$iterator]->product_id}}]" style="width: 80%; margin-left: 10%; font-size: 13px">
                                                                                                @if($quotation->quotation_products[$iterator]->summary_id == null)
                                                                                                    <option value="" selected>Select Summary</option>
                                                                                                    @foreach($summaries as $summary)
                                                                                                        <option value="{{$summary['id']}}">{{$summary['name']}}</option>
                                                                                                    @endforeach
                                                                                                @else
                                                                                                    <option value="">Select Summary</option>
                                                                                                    @foreach($summaries as $summary)
                                                                                                        @if($quotation->quotation_products[$iterator]->summary_id == $summary['id'])
                                                                                                            <option value="{{$summary['id']}}" selected>{{$summary['name']}}</option>
                                                                                                        @else
                                                                                                            <option value="{{$summary['id']}}">{{$summary['name']}}</option>
                                                                                                        @endif
                                                                                                    @endforeach
                                                                                                @endif
                                                                                            </select>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <table>
                                                                                            <tr style="border-bottom: 1px solid black">
                                                                                                <td>
                                                                                                    <a href="javascript:void(0);" onclick="viewProduct({{$iterator}})">
                                                                                                        View
                                                                                                    </a>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <a href="javascript:void(0);" onclick="removeRow({{$iterator}})">
                                                                                                        Remove
                                                                                                    </a>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            @endfor
                                                                        </table>

                                                                        <input type="hidden" id="productRowCount" value="{{$iterator}}">
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-3 col-md-offset-9">
                                                                            <label class="control-label" style="font-weight: bold; margin-left: 15%; font-size: 15px">
                                                                                Subtotal:
                                                                            </label>
                                                                            <label class="control-label" style="font-weight: bold; margin-left: 15%; font-size: 14px" id="subtotal">

                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-2 col-md-offset-7">
                                                                            <label class="control-label" style="font-weight: bold;float: right; margin-left: 15%; font-size: 15px">
                                                                                Discount:
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <input class="form-control" id="discount" name="discount" type="number" value="{{$quotation->discount}}" min="0">
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <label class="control-label  pull-right">
                                                                                Carpet Area:
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <input class="form-control" type="number" name="carpet_area" value="{{$quotation->carpet_area}}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <label class="control-label  pull-right">
                                                                                Built up area:
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <input class="form-control" type="number" name="built_up_area" value="{{$quotation->built_up_area}}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h4 class="panel-title">
                                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_4" style="font-size: 16px"><b> Tax </b></a>
                                                                </h4>
                                                            </div>
                                                            <div id="collapse_3_4" class="panel-collapse collapse">
                                                                <div class="panel-body">
                                                                    @foreach($taxes as $tax)
                                                                        <div class="row">
                                                                            <div class="col-md-3">
                                                                                <label class="control-label" style="float: right;"> {{$tax['name']}}: </label>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <input type="number" step="any" class="form-control" name="tax[{{$tax['id']}}]" id="Tax{{$tax['id']}}" value="{{$tax['base_percentage']}}">
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade in" id="MaterialsTab">

                                                </div>
                                                <div class="tab-pane fade in" id="ProfitMarginsTab">


                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div id="productView" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Product Analysis.</h4>
                                            </div>
                                            <div class="modal-body">

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
<script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>

<script>
    $(document).ready(function(){
        //CreateQuotation.init();
        calculateSubtotal();
    });

</script>
@endsection
