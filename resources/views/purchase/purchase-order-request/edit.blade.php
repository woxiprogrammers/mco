<?php
/**
 * Created by PhpStorm.
 * User: ganesh
 * Date: 29/5/18
 * Time: 2:36 PM
 */
?>

@extends('layout.master')
@section('title','Constro | Create Purchase Order Request')
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
                                    <h1>Edit Purchase Order Request</h1>
                                </div>
                                <div class="pull-right">
                                    <a class="btn blue" style="margin-top: 10%" onclick="readyToApprove()">
                                        Ready To Approve
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/purchase/purchase-order-request/manage">Manage Purchase Order Request</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Edit Purchase Order Request</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <form role="form" id="editPurchaseOrderRequest" class="form-horizontal" method="post" action="/purchase/purchase-order-request/edit/{{$purchaseOrderRequest->id}}">
                                                {!! csrf_field() !!}
                                                <input type="hidden" name="purchase_order_request_id" id="purchaseOrderRequestId" value="{{$purchaseOrderRequest->id}}">
                                                <div class="form-actions noborder row">
                                                    <div class="form-group">
                                                        <div class="col-md-3">
                                                            <label class="control-label pull-right">
                                                                Purchase Request
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="purchaseRequest" readonly value="{{$purchaseOrderRequest->purchaseRequest->format_id}}">
                                                        </div>
                                                    </div>
                                                    <div class="table-scrollable" style="overflow: scroll !important;">
                                                        <table class="table table-striped table-bordered table-hover" id="purchaseRequestComponentTable" style="overflow: scroll; table-layout: fixed">
                                                            <thead>
                                                            <tr>
                                                                <th style="width: 12%"> Vendor </th>
                                                                <th style="width: 15%"> Material Name </th>
                                                                <th style="width: 10%"> Quantity </th>
                                                                <th style="width: 10%;"> Unit </th>
                                                                <th style="width: 10%"> Rate w/o Tax </th>
                                                                <th style="width: 10%"> Rate w/ Tax </th>
                                                                <th style="width: 10%"> Total Amount w/ Tax </th>
                                                                <th style="width: 10%">
                                                                    Action
                                                                </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($purchaseOrderRequestComponentData as $purchaseOrderRequestComponent)
                                                                    <tr class="component-row" id="componentRow-{{$purchaseOrderRequestComponent['id']}}">
                                                                        <td style="width: 12%"><input type="hidden" name="component_vendor_relations[{{$purchaseOrderRequestComponent['purchase_request_component_id']}}][]" class="component-vendor-relation" value="{{$purchaseOrderRequestComponent['vendor_relation_id']}}"><span> {{$purchaseOrderRequestComponent['vendor_name']}} </span></td>
                                                                        <td style="width: 15%"><span> {{$purchaseOrderRequestComponent['name']}} </span></td>
                                                                        <td style="width: 10%"><span> {{$purchaseOrderRequestComponent['quantity']}} </span></td>
                                                                        <td style="width: 10%;"><span> {{$purchaseOrderRequestComponent['unit']}} </span></td>
                                                                        <td style="width: 10%"><span class="rate-without-tax">{!!  \App\Helper\MaterialProductHelper::customRound($purchaseOrderRequestComponent['rate_per_unit']) !!} </span></td>
                                                                        <td style="width: 10%"><span class="rate-with-tax"> {!!  ($purchaseOrderRequestComponent['rate_with_tax']) !!} </span></td>
                                                                        @if($purchaseOrderRequestComponent['is_client'] == true)
                                                                            <td style="width: 10%"><span class="total-with-tax"> - </span></td>
                                                                        @else
                                                                            <td style="width: 10%"><span class="total-with-tax"> {!! ($purchaseOrderRequestComponent['total']) !!} </span></td>
                                                                        @endif
                                                                        <td style="width: 10%">
                                                                            <a class="btn blue" href="javascript:void(0);" onclick="openDetailsModal(this,{{$purchaseOrderRequestComponent['id']}})">
                                                                                Add Details
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red" id="submitPORequestForm"><i class="fa fa-check"></i> Submit</button>
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
    <div class="modal fade " id="detailsModal"  role="dialog">
        <div class="modal-dialog" style="width: 98%; height: 800px">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4" style="font-size: 21px"> Details </div>
                        <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                    </div>
                </div>
                <input type="hidden" id="modalComponentID">
                <form id="componentDetailForm">
                    {!! csrf_field() !!}
                    <div class="modal-body">

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script src="/assets/custom/purchase/purchase-order-request/edit-purchase-order-request.js"></script>
    <script>
        function openPdf(random,fullPath){
            $("#image-"+random+" #myFrame").attr('src',fullPath);
            $("#image-"+random+" #myFrame").show();
        }
        function closePdf(random,fullPath){
            $("#image-"+random+" #myFrame").hide();
        }

        function readyToApprove(){
            var flag = confirm('Do you want to make Purchase Order Request ready for approval ?');
            if(flag == true){
                var purchaseOrderRequestId = $("#purchaseOrderRequestId").val();
                window.location.href = '/purchase/purchase-order-request/make-ready-to-approve/'+purchaseOrderRequestId;
            }
        }
    </script>
@endsection

