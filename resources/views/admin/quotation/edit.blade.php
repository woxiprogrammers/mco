<?php
/**
 * Created by Ameya Joshi.
 * Date: 21/6/17
 * Time: 12:38 PM
 */
?>
@extends('layout.master')
@section('title','Constro | Edit Quotation')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
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
                                    <a href="/quotation/manage/state#2">Manage Quotations</a>
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
                                        <input type="hidden" id="quotationId" value="{{$quotation->id}}">
                                        <input type="hidden" id="userRole" value="{{$userRole}}">
                                        <input type="hidden" id="quotationStatus" value="{{$quotation->quotation_status->slug}}">
                                        <input type="hidden" id="quotationProducts" value="{{$quotationProducts}}">
                                        <form role="form" id="QuotationEditForm" class="form-horizontal" action="/quotation/edit/{{$quotation->id}}" method="post">
                                            {!! csrf_field() !!}
                                            <input type="hidden" name="_method" value="put">
                                            <div class="tab-content">
                                                <div class="tab-pane fade in active" id="GeneralTab">
                                                    <fieldset class="row" style="text-align: right">
                                                        @if($quotation->quotation_status->slug == 'draft')
                                                            <a class="btn green-meadow btn-xs" id="approve" href="javascript:void(0);" onclick="openApproveTab()">
                                                                <i class="fa fa-check-square-o"></i> Approve
                                                            </a>
                                                            <a class="btn btn-danger btn-xs" id="disapprove" onclick="openDisapproveModal()">
                                                                <i class="fa fa-remove"></i> Disapprove
                                                            </a>
                                                        @elseif($quotation->quotation_status->slug == 'approved')
                                                            <a class="btn btn-info btn-xs" id="workOrderDetails" data-toggle="tab" href="#workOrderTab">
                                                                <i class="fa fa-server"></i> Work Order Detail
                                                            </a>
                                                        @endif
                                                        <a class="btn btn-info btn-xs" id="materialCosts">
                                                            <i class="fa fa-edit"></i> Material Rate
                                                        </a>
                                                        <a class="btn btn-info btn-xs" href="javascript:void(0)" onclick="showProfitMargins()" id="profitMargins">
                                                            <i class="fa fa-edit"></i> Profit Margins
                                                        </a>
                                                        @if($quotation->is_tax_applied == true)
                                                            <a href="/quotation/invoice/{{$quotation->id}}/with-tax/without-summary" class="btn btn-info btn-xs">
                                                                <i class="fa fa-download"></i>Q. w/ Tax w/o Summary
                                                            </a>
                                                        @endif
                                                        <a href="/quotation/invoice/{{$quotation->id}}/without-tax/without-summary" class="btn btn-info btn-xs">
                                                                <i class="fa fa-download"></i>Q. w/o Tax w/o Summary
                                                        </a>

                                                        @if($quotation->built_up_area != null)
                                                            @if($quotation->is_tax_applied == true)
                                                                <a href="/quotation/invoice/{{$quotation->id}}/with-tax/with-summary" class="btn btn-info btn-xs">
                                                                    <i class="fa fa-download"></i>Q. w/ Tax
                                                                </a>
                                                            @endif
                                                            <a href="/quotation/invoice/{{$quotation->id}}/without-tax/with-summary" class="btn btn-info btn-xs">
                                                                <i class="fa fa-download"></i>Q. w/o Tax
                                                            </a>
                                                            @if($quotation->is_summary_applied == true)
                                                                <a href="/quotation/summary/{{$quotation->id}}" class="btn btn-info btn-xs">
                                                                    <i class="fa fa-download"></i>Summary
                                                                </a>
                                                            @endif
                                                        @endif
                                                        @if($quotation->quotation_status->slug == 'draft')
                                                            <a class="btn btn-success btn-xs" id="generalTabSubmit">
                                                                Save
                                                            </a>
                                                        @endif
                                                    </fieldset>

                                                    <div class="panel-group accordion" id="accordion3" style="margin-top: 3%">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h4 class="panel-title">
                                                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_1" style="font-size: 16px"><b> Project Details </b></a>
                                                                </h4>
                                                            </div>
                                                            <div id="collapse_3_1" class="panel-collapse in">
                                                                <input type="hidden" name="project_site_id" id="projectSiteId" value="{{$quotation->project_site_id}}">
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
                                                                    <div class="row" style="background-color: beige">
                                                                        <div class="col-md-4">
                                                                            <div class="col-md-6">
                                                                                <label class="control-label pull-right" style="font-weight: bold; font-size: 15px; margin-right: 17%">
                                                                                    Enter Discount:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-6 input-group form-group">
                                                                                <input class="form-control" id="discount" name="discount" type="number" value="{{$quotation->discount}}" style="width: 113%">
                                                                                <span class="input-group-addon">&nbsp; % </span>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-4">
                                                                            <div class="col-md-6">
                                                                                <label class="control-label pull-right" style="font-weight: bold; font-size: 15px">
                                                                                    Enter Slab Area:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-6 input-group form-group">
                                                                                <input class="form-control" type="number" name="built_up_area" value="{{$quotation->built_up_area}}">
                                                                                <span class="input-group-addon">Sq.Ft</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="col-md-3">
                                                                                <label class="control-label pull-right" style="font-weight: bold; font-size: 15px">
                                                                                    Subtotal:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-9" style="padding-right: 34px; padding-left:30px ">
                                                                                    <input type="text" class="form-control" id="subtotal" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-1">
                                                                            <a class="btn yellow btn-md pull-right" id="addProduct"><i class="fa fa-plus"></i> Product</a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row" style="height: 4%">

                                                                    </div>
                                                                    <div class="table-scrollable" style="margin-top: 5%">
                                                                        <table class="table table-bordered" id="productTable" style="overflow: scroll;overflow-x: auto; overflow-y: auto">
                                                                            <tr>
                                                                                <th style="width: 10%; text-align: center" > Category </th>
                                                                                <th style="width: 10%; text-align: center"> Product </th>
                                                                                <th style="width: 18%; text-align: center"> Description</th>
                                                                                <th style="width: 8%; text-align: center"> Unit</th>
                                                                                <th style="width: 8%; text-align: center"> Rate</th>
                                                                                <th style="width: 5%; text-align: center"> Quantity </th>
                                                                                <th  style="width: 8%; text-align: center"> Amount </th>
                                                                                <th  style="width: 8%; text-align: center"> Discounted Amount </th>
                                                                                <th  style="width: 15%; text-align: center"> Summary </th>
                                                                                <th style="text-align: center"> Action </th>
                                                                            </tr>
                                                                            @for($iterator=0; $iterator < count($quotation->quotation_products); $iterator++)
                                                                                {{--@php
                                                                                    $rate = \App\Helper\MaterialProductHelper::customRound($quotation->quotation_products[$iterator]->rate_per_unit);
                                                                                    $amount = \App\Helper\MaterialProductHelper::customRound($rate * $quotation->quotation_products[$iterator]->quantity);
                                                                                    $discountedAmount = \App\Helper\MaterialProductHelper::customRound($amount - ($amount*($quotation->discount/100)));
                                                                                @endphp--}}
                                                                                @php
                                                                                    $rate = round($quotation->quotation_products[$iterator]->rate_per_unit,3);
                                                                                    $amount = round($rate * $quotation->quotation_products[$iterator]->quantity,3);
                                                                                    $discountedAmount = round(($amount - ($amount*($quotation->discount/100))),3);
                                                                                @endphp
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
                                                                                            <input name="product_description[{{$quotation->quotation_products[$iterator]->product_id}}]" class="form-control quotation-product-table" onclick="replaceEditor({{$iterator}})" id="productDescription{{$iterator}}" type="text" value="{{$quotation->quotation_products[$iterator]->description}}" title="{{$quotation->quotation_products[$iterator]->description}}" readonly>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <input name="product_unit[{{$quotation->quotation_products[$iterator]->product_id}}]" class="form-control quotation-product-table" id="productUnit{{$iterator}}" type="text" value="{{$quotation->quotation_products[$iterator]->product->unit->name}}" title="{{$quotation->quotation_products[$iterator]->product->unit->name}}" readonly>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <input name="product_rate[{{$quotation->quotation_products[$iterator]->product_id}}]" class="form-control quotation-product-table" id="productRate{{$iterator}}" type="text" value="{{$rate}}" title="{{$rate}}" readonly>
                                                                                        </div>
                                                                                    </td>

                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <input type="number" step="any" class="form-control quotation-product-table" name="product_quantity[{{$quotation->quotation_products[$iterator]->product_id}}]" id="productQuantity{{$iterator}}" onchange="calculateAmount({{$iterator}})" onkeyup="calculateAmount({{$iterator}})"  value="{{$quotation->quotation_products[$iterator]->quantity}}" title="{{$quotation->quotation_products[$iterator]->quantity}}">
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <input type="text" name="product_amount[{{$quotation->quotation_products[$iterator]->product_id}}]" class="form-control quotation-product-table product-amount" id="productAmount{{$iterator}}" value="{{$amount}}" title="{{$amount}}" readonly>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <input type="text" name="product_discount_amount[{{$quotation->quotation_products[$iterator]->product_id}}]" class="form-control quotation-product-table product-discount-amount" id="productDiscountAmount{{$iterator}}" value="{{$discountedAmount}}" title="{{$discountedAmount}}" readonly>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="form-group">
                                                                                            <select class="form-control product-summary" name="product_summary[{{$quotation->quotation_products[$iterator]->product_id}}]" style="width: 80%; margin-left: 10%; font-size: 13px">
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

                                                                        <input type="hidden" id="productRowCount" value="{{count($quotation->quotation_products)}}">
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="row">

                                                                        </div>
                                                                        <div class="row">

                                                                        </div>
                                                                        <div class="row">

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
                                                                            <div class="col-md-11">
                                                                                <label class="control-label" style="font-weight: bold; font-size: 15px; float: right"> {{$tax['name']}}: </label>
                                                                            </div>
                                                                            <div class="col-md-1">
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
                                                    <fieldset class="row">
                                                        <a class="btn btn-primary" onclick="backToMaterials()" href="javascript:void(0);">
                                                            Back
                                                        </a>
                                                        @if($quotation->quotation_status->slug == 'draft' || $userRole == 'superadmin')

                                                            <button type="submit" class="btn red pull-right" id="formSubmit" hidden style="padding-left: 6px">
                                                                <i class="fa fa-check"></i> Submit
                                                            </button>
                                                        @endif
                                                    </fieldset>
                                                    <div id="profitMarginTable">

                                                    </div>
                                                </div>
                                        </form>
                                                <div class="tab-pane fade in" id="workOrderTab">
                                                    @if($quotation->quotation_status->slug == 'approved')
                                                        <form id="WorkOrderForm" action="/quotation/work-order/edit/{{$quotation->work_order->id}}" method="post">
                                                    @elseif($quotation->quotation_status->slug == 'draft')
                                                        <form id="WorkOrderForm" action="/quotation/approve/{{$quotation->id}}" method="post">
                                                    @endif
                                                    {!! csrf_field() !!}
                                                    <fieldset class="row" style="text-align: right">
                                                        <a class="btn btn-info" href="#GeneralTab" data-toggle="tab">
                                                            Back
                                                        </a>
                                                        @if(($quotation->quotation_status->slug == 'approved' || $quotation->quotation_status->slug == 'disapproved')&& $userRole == 'superadmin')
                                                        <button type="submit" class="btn red" style="padding-left: 6px">
                                                            <i class="fa fa-check"></i> Submit
                                                        </button>
                                                        @elseif($quotation->quotation_status->slug == 'draft')
                                                            <button type="submit" class="btn btn-success">
                                                                Approve
                                                            </button>
                                                        @endif
                                                    </fieldset>
                                                    <ul class="nav nav-tabs nav-tabs-lg">
                                                        <li class="active">
                                                            <a href="#workOrderFormTab" data-toggle="tab"> Work Order </a>
                                                        </li>
                                                        <li>
                                                            <a href="#extraItemFormTab" data-toggle="tab"> Extra Items </a>
                                                        </li>
                                                        <li>
                                                            <a href="#bankassignFormTab" data-toggle="tab"> Assign Bank </a>
                                                        </li>
                                                        <li>
                                                            <a href="#miscellaneousFormTab" data-toggle="tab"> Miscellaneous Category </a>
                                                        </li>
                                                        <li>
                                                            <a href="#floorFormTab" data-toggle="tab"> Floors </a>
                                                        </li>
                                                        @if($quotation->quotation_status->slug == 'approved'&& $userRole == 'superadmin')
                                                            <li>
                                                                <a href="#openingExpensesFormTab" data-toggle="tab"> Opening Expenses </a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                    <div class="tab-content">
                                                        <div class="tab-pane fade in active" id="workOrderFormTab">
                                                            @if($quotation->quotation_status->slug == 'approved')
                                                                    {!! csrf_field() !!}
                                                                    <input type="hidden" name="quotation_id" value="{{$quotation->id}}">
                                                                    <div class="col-md-offset-2">
                                                                        <div class="form-group">
                                                                            <div class="col-md-3">
                                                                                <label for="work_order_number" class="control-form pull-right">
                                                                                    Remark:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <textarea class="form-control" name="remark" id="remark">
                                                                                    {{$quotation->remark}}
                                                                                </textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-3">
                                                                                <label for="work_order_number" class="control-form pull-right">
                                                                                    Work Order Number:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <input class="form-control" value="{{$quotation->work_order->work_order_number}}" name="work_order_number" id="workOrderNumber" type="text">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-3">
                                                                                <label for="description" class="control-form pull-right">
                                                                                    Description:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <textarea class="form-control" name="description" id="workOrderDescription">
                                                                                    {{$quotation->work_order->description}}
                                                                                </textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-3">
                                                                                <label for="scope" class="control-form pull-right">
                                                                                    Scope:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <input class="form-control" value="{{$quotation->work_order->scope}}" name="scope" id="scope" type="text">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-3">
                                                                                <label for="order_value" class="control-form pull-right">
                                                                                    Order Value Without Tax:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <input class="form-control" value="{{$beforeTaxOrderValue}}" name="order_value" id="OrderValueWithoutTax" type="text" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-3">
                                                                                <label for="order_value" class="control-form pull-right">
                                                                                    Order Value With Tax:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <input class="form-control" value="{{$orderValue}}" name="order_value" id="OrderValue" type="text" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-3">
                                                                                <label for="bill_type" class="control-form pull-right">
                                                                                    Bill Type:
                                                                                </label>
                                                                            </div>
                                                                            @if($billCount == 0)
                                                                                <div class="col-md-3" id="billType">
                                                                                    <select class="form-control" name="bill_type_id">
                                                                                        <option value="">Select Bill Type</option>
                                                                                        @foreach($billTypes as $billType)
                                                                                            @if($quotation['bill_type_id'] != null && $billType['id'] == $quotation['bill_type_id'])
                                                                                                <option value="{{$billType['id']}}" selected>{{$billType['name']}}</option>
                                                                                            @else
                                                                                                <option value="{{$billType['id']}}">{{$billType['name']}}</option>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            @else
                                                                                <div class="col-md-3">
                                                                                    <input type="text" class="form-control" value="{{$quotation->billType->name}}" readonly>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="row">
                                                                                <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12"> </div>
                                                                            </div>
                                                                            <div id="tab_images_uploader_container" class="col-md-offset-5">
                                                                                <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow">
                                                                                    Browse</a>
                                                                                <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                                                    <i class="fa fa-share"></i> Upload Files </a>
                                                                            </div>
                                                                            <table class="table table-bordered table-hover" style="width: 700px">
                                                                                <thead>
                                                                                <tr role="row" class="heading">
                                                                                    <th> Image </th>
                                                                                    <th> Action </th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody id="show-product-images">
                                                                                @foreach($quotation->work_order->images as $image)
                                                                                    <tr id="image-{{$image->id}}">
                                                                                        <td>
                                                                                            <a href="{{$image->path}}" target="_blank" class="fancybox-button" data-rel="fancybox-button">
                                                                                                <img class="img-responsive" src="{{$image->path}}" alt="" style="width:100px; height:100px;"> </a>
                                                                                            <input type="hidden" class="work-order-image-name" name="work_order_images[$image->id][image_name]" id="work-order-image-{{$image->id}}" value="{{$image->path}}"/>
                                                                                        </td>
                                                                                        <td>
                                                                                            <a href="javascript:;" class="btn btn-default btn-sm" onclick='removeProductImages("#image-{{$image->id}}","{{$image->path}}",0);'>
                                                                                                <i class="fa fa-times"></i> Remove </a>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                            @elseif($quotation->quotation_status->slug == 'draft')
                                                                    <input type="hidden" name="quotation_id" value="{{$quotation->id}}">
                                                                    <div class="col-md-offset-2">
                                                                        <div class="form-group">
                                                                            <div class="col-md-3">
                                                                                <label for="work_order_number" class="control-form pull-right">
                                                                                    Remark:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <textarea class="form-control" name="remark" id="remark"></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-3">
                                                                                <label for="work_order_number" class="control-form pull-right">
                                                                                    Work Order Number:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <input class="form-control" name="work_order_number" id="workOrderNumber" type="text">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-3">
                                                                                <label for="description" class="control-form pull-right">
                                                                                    Description:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <textarea class="form-control" name="description" id="workOrderDescription">
                                                                                </textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-3">
                                                                                <label for="scope" class="control-form pull-right">
                                                                                    Scope:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <input class="form-control" name="scope" id="scope" type="text">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-3">
                                                                                <label for="order_value" class="control-form pull-right">
                                                                                    Order Value:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <input class="form-control" value="{{$orderValue}}" name="order_value" id="OrderValue" type="text" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group" id="billType">
                                                                            <div class="col-md-3">
                                                                                <label for="bill_type" class="control-form pull-right">
                                                                                    Bill Type:
                                                                                </label>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <select class="form-control" name="bill_type_id">
                                                                                    <option value="">Select Bill Type</option>
                                                                                    @foreach($billTypes as $billType)
                                                                                        @if($quotation['bill_type_id'] != null && $billType['id'] == $quotation['bill_type_id'])
                                                                                            <option value="{{$billType['id']}}" selected>{{$billType['name']}}</option>
                                                                                        @else
                                                                                            <option value="{{$billType['id']}}">{{$billType['name']}}</option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="row">
                                                                                <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12"> </div>
                                                                            </div>
                                                                            <div id="tab_images_uploader_container" class="col-md-offset-5">
                                                                                <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow">
                                                                                    Browse</a>
                                                                                <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                                                    <i class="fa fa-share"></i> Upload Files </a>
                                                                            </div>
                                                                            <table class="table table-bordered table-hover" style="width: 700px">
                                                                                <thead>
                                                                                <tr role="row" class="heading">
                                                                                    <th> Image </th>
                                                                                    <th> Action </th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody id="show-product-images">

                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                            @endif
                                                        </div>
                                                        <div class="tab-pane fade in" id="extraItemFormTab">
                                                            <fieldset>
                                                                <legend>
                                                                    <span>
                                                                        <a href="#extraItemModal" data-toggle="modal" class="btn yellow pull-right">
                                                                            <i class="fa fa-plus"> </i>
                                                                            Extra Item
                                                                        </a>
                                                                    </span>
                                                                </legend>
                                                                @foreach($extraItems as $extraItem)
                                                                    <div class="form-group">
                                                                        <div class="col-md-3">
                                                                            <label class="control-form pull-right">
                                                                                {{$extraItem['name']}}
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <input class="form-control" name="extra_item[{{$extraItem['id']}}]" value="{{round($extraItem['rate'],3)}}">
                                                                            @if(array_key_exists('slug',$extraItem))
                                                                                <i> Note : This is newly created item and not added to quotation yet</i>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                <div id="newExtraItemSection">

                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="tab-pane fade in" id="bankassignFormTab">
                                                            <div class="form-group" style="margin-top: 3%">
                                                                <div class="col-md-3" style="margin-left: 2%">
                                                                    <label class="control-form pull-right">
                                                                        Select Bank :
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-control product-material-select" style="height: 150px;" >
                                                                        <ul id="bank_id" class="list-group">
                                                                            @foreach($bankInfo as $bank)
                                                                                @if(in_array($bank['id'],$checkBank))
                                                                                    <li> <input type="checkbox" name="bank[]" value="{{$bank['id']}}" checked> {{$bank['bank_name']}} ({{$bank['account_number']}}) </li>
                                                                                @else
                                                                                    <li> <input type="checkbox" name="bank[]" value="{{$bank['id']}}"> {{$bank['bank_name']}} ({{$bank['account_number']}}) </li>
                                                                                @endif
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade in" id="miscellaneousFormTab">
                                                            <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="createBillTable">
                                                                <tr>
                                                                    <th style="width: 13%; text-align: center" >
                                                                        Is Client supplied?
                                                                    </th>
                                                                    <th  style="text-align: center"> Category name </th>
                                                                    <th style="text-align: center"> Material name </th>
                                                                    <th  style="text-align: center"> Rate per unit </th>
                                                                    <th  style="text-align: center"> Unit </th>
                                                                    <th  style="text-align: center"> Quantity </th>
                                                                </tr>
                                                                @foreach($quotationMiscellaneousMaterials as $key => $miscellaneousMaterial)
                                                                    <tr id = "id_{{$miscellaneousMaterial['material_id']}}">
                                                                        <td>
                                                                            @if(array_key_exists('is_client_supplied',$miscellaneousMaterial) && $miscellaneousMaterial['is_client_supplied'] == true)
                                                                                <input type="checkbox" name="miscellaneous_material_id[{{$miscellaneousMaterial['material_id']}}][is_client_supplied]" checked>
                                                                            @else
                                                                                <input type="checkbox" name="miscellaneous_material_id[{{$miscellaneousMaterial['material_id']}}][is_client_supplied]">
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <span>{{$miscellaneousMaterial['category_name']}}</span>
                                                                        </td>
                                                                        <td>
                                                                            <span>{{$miscellaneousMaterial['material_name']}}</span>
                                                                        </td>
                                                                        <td>
                                                                            <input class="form-control" type="text" id="rate_{{$miscellaneousMaterial['material_id']}}" name="miscellaneous_material_id[{{$miscellaneousMaterial['material_id']}}][rate_per_unit]" value="{{$miscellaneousMaterial['rate_per_unit']}}">
                                                                        </td>
                                                                        <td>
                                                                            <span>{{$miscellaneousMaterial['unit_name']}}</span>
                                                                            <input type="hidden" name="miscellaneous_material_id[{{$miscellaneousMaterial['material_id']}}][unit_id]" value="{{$miscellaneousMaterial['unit_id']}}">
                                                                        </td>
                                                                        @if(array_key_exists('quantity',$miscellaneousMaterial) && $miscellaneousMaterial['quantity'] != null)
                                                                            <td class="form-group">
                                                                                <input class="form-control current_quantity " type="text" id="current_quantity_{{$miscellaneousMaterial['material_id']}}" name="miscellaneous_material_id[{{$miscellaneousMaterial['material_id']}}][quantity]" value="{{$miscellaneousMaterial['quantity']}}">
                                                                                <input type="hidden" name="miscellaneous_material_id[{{$miscellaneousMaterial['material_id']}}][quotation_material_id]" value="{{$miscellaneousMaterial['quotation_material_id']}}">
                                                                            </td>
                                                                        @else
                                                                            <td class="form-group">
                                                                                <input class="form-control current_quantity" type="text" id="current_quantity_{{$miscellaneousMaterial['material_id']}}" name="miscellaneous_material_id[{{$miscellaneousMaterial['material_id']}}][quantity]" value="0">
                                                                            </td>
                                                                        @endif
                                                                    </tr>
                                                                @endforeach
                                                            </table>
                                                        </div>
                                                        <div class="tab-pane fade in" id="openingExpensesFormTab">
                                                            <div class="form-group">
                                                                <div class="col-md-3">
                                                                    <label class="control-form pull-right">
                                                                        Opening Expenses
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input class="form-control" name="open_expenses" id="open_expenses" value="{{$quotation->opening_expenses}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade in" id="floorFormTab">
                                                            <div class="form-group">
                                                                @if(count($quotation->quotation_floor) > 0)
                                                                    <div class="form-group">
                                                                        <div class="col-md-3">
                                                                            <label for="work_order_number" class="control-form pull-right">
                                                                                Number of floors:
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <input class="form-control" name="number_of_floors" id="numberOfFloors" type="text" value="{!! count($quotation->quotation_floor) !!}">
                                                                        </div>
                                                                    </div>
                                                                    <div id="floorNameDiv">
                                                                        <table class="table table-bordered table-striped table-condensed flip-content" style="width:50%;overflow: scroll; margin-left: 10%;">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th style="width: 10%"> No. </th>
                                                                                    <th> Name </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @for($iterator = 0; $iterator < count($quotation->quotation_floor); $iterator++)
                                                                                    <tr>
                                                                                        <td>{!! $iterator+1 !!}</td>
                                                                                        <td>
                                                                                            <div class="form-group" style="width:80%;margin-left: 10%">
                                                                                                <input type="text" name="quotation_floor[{{$iterator}}]" class="form-control quotation-floor" value="{{$quotation->quotation_floor[$iterator]->name}}">
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endfor
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                @else
                                                                    <div class="form-group">
                                                                        <div class="col-md-3">
                                                                            <label for="work_order_number" class="control-form pull-right">
                                                                                Number of floors:
                                                                            </label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <input class="form-control" name="number_of_floors" id="numberOfFloors" type="text">
                                                                        </div>
                                                                    </div>
                                                                    <div id="floorNameDiv">

                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
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
                                            <div class="modal-footer">

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                @if($quotation->quotation_status->slug == 'draft')
                                    <div id="disapproveModal" class="modal fade" role="dialog">
                                        <div class="modal-dialog">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">Disapprove Quotation</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="disapproveForm" method="post" action="/quotation/disapprove/{{$quotation->id}}">
                                                        {!! csrf_field() !!}
                                                        <div class="form-group">
                                                            <div class="col-md-3">
                                                                <lable for="remark" class="control-label pull-right">
                                                                    Remark
                                                                </lable>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <textarea name="remark" id="disapproveRemark" class="form-control">

                                                                </textarea>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <button type="submit" class="btn btn-success" style="margin-left: 40%; margin-top:3%">
                                                                Submit
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    <input type="hidden" id="path" name="path" value="">
                    <input type="hidden" id="max_files_count" name="max_files_count" value="20">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div id="extraItemModal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 70%;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="text-align: center"> <b>Create extra item </b> </h4>
            </div>
            <div class="modal-body form">
                <form role="form" id="create-extra-item" class="form-horizontal" method="post">
                    {!! csrf_field() !!}
                    <div class="form-body">
                        <div class="form-group row">
                            <div class="col-md-3" style="text-align: right">
                                <label for="name" class="control-label">Name</label>
                                <span>*</span>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Rate</label>
                        <div class="col-md-6">
                            <input type="number" id="rate" name="rate" class="form-control" placeholder="Enter Rate">
                        </div>
                    </div>
                    <div class="form-actions noborder row">
                        <div class="col-md-offset-3" style="margin-left: 26%">
                            <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit</button>
                        </div>
                    </div>
                </form>
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
<script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

@if($quotation->quotation_status->slug != 'disapproved')
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
    <script src="/assets/custom/admin/quotation/image-datatable.js"></script>
    <script src="/assets/custom/admin/quotation/image-upload.js"></script>

@endif
<script>
    $(document).ready(function(){
        EditQuotation.init();
        WorkOrderFrom.init();
        CreateExtraItem.init();
        calculateSubtotal();
        applyValidation("QuotationEditForm");
        var typingTimer;
        var doneTypingInterval = 500;
        var quotationProducts = $("#quotationProducts").val();
        quotationProducts = jQuery.parseJSON(quotationProducts);
        $.each(quotationProducts, function(index, value){
            $("input[name='product_quantity["+value.product_id+"]']").rules('add',{
                min: parseInt(value.product_bill_count)
            });
        });
        $("#numberOfFloors").on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });
        $("#numberOfFloors").on('keydown', function () {
            clearTimeout(typingTimer);
        });
        function doneTyping () {
            var number = parseInt($("#numberOfFloors").val());
            if(!isNaN(number)){
                var floorString = '<table class="table table-bordered table-striped table-condensed flip-content" style="width:50%;overflow: scroll; margin-left: 10%;">' +
                    '<thead>' +
                    '<tr>' +
                    '<th style="width: 10%"> No. </th>'+
                    '<th> Name </th>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody>';
                for(var iterator = 0; iterator < number; iterator++){
                    var no = iterator+1;
                    floorString += '<tr>' +
                        '<td>'+no+'</td>' +
                        '<td><div class="form-group" style="width:80%;margin-left: 10%"><input type="text" name="quotation_floor['+iterator+']" class="form-control quotation-floor"></div></td>' +
                        '</tr>';
                }
                floorString += '</tbody></table>';
                $("#floorNameDiv").html(floorString);
                $(".quotation-floor").each(function(){
                    $(this).rules('add',{
                        required: true
                    });
                });
            }else{
                $("#floorNameDiv").html('');
            }
        }
    });
</script>
@endsection
