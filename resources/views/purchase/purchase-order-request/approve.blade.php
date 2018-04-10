<?php
/**
 * Created by Ameya Joshi.
 * Date: 10/1/18
 * Time: 5:01 PM
 */
?>
@extends('layout.master')
@section('title','Constro | Edit Purchase Order Request')
@include('partials.common.navbar')
@section('css')

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
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <lable class="control-label">
                                                            Client
                                                        </lable>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <lable class="control-label">
                                                            Project
                                                        </lable>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <lable class="control-label">
                                                            Project Site
                                                        </lable>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <lable class="control-label">
                                                            Purchase Request
                                                        </lable>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <input type="text" value="{{$purchaseOrderRequest->purchaseRequest->projectSite->project->client->company}}" class="form-control" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" value="{{$purchaseOrderRequest->purchaseRequest->projectSite->project->name}}" class="form-control" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" value="{{$purchaseOrderRequest->purchaseRequest->projectSite->name}}" class="form-control" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" value="{{$purchaseOrderRequest->purchaseRequest->format_id}}" class="form-control" readonly>
                                                    </div>
                                                </div>
                                                @if(count($purchaseOrderRequestComponents) > 0)
                                                    @foreach($purchaseOrderRequestComponents as $purchaseOrderRequestComponentId => $purchaseOrderRequestComponentData)
                                                        <div class="panel-group accordion" id="accordion_{{$purchaseOrderRequestComponentId}}">
                                                            <div class="panel panel-default">
                                                                <div class="panel-heading row" style="background-color: cornflowerblue">
                                                                    <div class="pull-left" style="padding: 1% 1% 1%">
                                                                        <input type="checkbox" onclick="accordionTitleSelect(this)">
                                                                    </div>
                                                                    <h4 class="panel-title" style="margin-left: 2%">
                                                                        <a class="accordion-toggle accordion-toggle-styled" data-parent="#accordion3" href="#collapse_{{$purchaseOrderRequestComponentId}}" style="font-size: 16px;color: white">
                                                                            <b> {{$purchaseOrderRequestComponentData['name']}} </b><br>
                                                                            {{$purchaseOrderRequestComponentData['quantity']}} {{$purchaseOrderRequestComponentData['unit']}}
                                                                        </a>
                                                                    </h4>
                                                                </div>
                                                                <div id="collapse_{{$purchaseOrderRequestComponentId}}" class="panel-collapse collapse">
                                                                    <div class="panel-body" style="overflow:auto;">
                                                                        <div class="form-group">
                                                                            <table class="table table-striped table-bordered table-hover">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th>

                                                                                    </th>
                                                                                    <th>
                                                                                        Vendor Name
                                                                                    </th>
                                                                                    <th>
                                                                                        Rate w/o Tax
                                                                                    </th>
                                                                                    <th>
                                                                                        Rate w/ Tax
                                                                                    </th>
                                                                                    <th>
                                                                                        Total w/ Tax
                                                                                    </th>
                                                                                    <th>
                                                                                        Transportation w/o Tax
                                                                                    </th>
                                                                                    <th>
                                                                                        Transportation w/ Tax
                                                                                    </th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                @foreach($purchaseOrderRequestComponentData['vendor_relations'] as $vendorRelation)
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input type="checkbox" class="radio-buttons-{{$purchaseOrderRequestComponentId}}" name="approved_purchase_order_request_relation[{{$vendorRelation['vendor_id']}}][]" value="{{$vendorRelation['purchase_order_request_component_id']}}" onclick="radioClickEvent(this)">
                                                                                        </td>
                                                                                        <td>
                                                                                            {{$vendorRelation['vendor_name']}}
                                                                                        </td>
                                                                                        <td>
                                                                                            {{$vendorRelation['rate_without_tax']}}
                                                                                        </td>
                                                                                        <td>
                                                                                            {{$vendorRelation['rate_with_tax']}}
                                                                                        </td>
                                                                                        <td>
                                                                                            {{$vendorRelation['total_with_tax']}}
                                                                                        </td>
                                                                                        <td>
                                                                                            {{$vendorRelation['transportation_without_tax']}}
                                                                                        </td>
                                                                                        <td>
                                                                                            {{$vendorRelation['transportation_with_tax']}}
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-purchase-order-request'))
                                                    <div class="form-actions noborder row">
                                                        <div class="col-md-offset-3" style="margin-left: 26%">
                                                            <button type="submit" id="submitPOApproveForm" class="btn red"><i class="fa fa-check"></i> Submit</button>
                                                        </div>
                                                    </div>
                                                @endif

                                                @else
                                                    <div class="row" style="margin-top: 10%; margin-bottom: 10%">
                                                        <div class="col-md-8 col-md-offset-3">
                                                            <span style="font-size: 20px; font-weight: bold"> Purchase Orders are created for all the materials and assets . </span>
                                                        </div>
                                                    </div>
                                                @endif
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
    <script>

        $(document).ready(function(){
            $('#submitPOApproveForm').click(function(){
                $("button[type='submit']").prop('disabled', true);
                $('#editPurchaseOrderRequest').submit();
            })
        });
        function accordionTitleSelect(element){
            if($(element).is(":checked") == true){
                $(element).closest('.panel-heading').find('.accordion-toggle').attr('data-toggle','collapse');
            }else{
                $(element).closest('.panel-heading').find('.accordion-toggle').removeAttr('data-toggle');
                $(element).closest('.panel-default').find('.panel-collapse').removeClass('in');
                $(element).closest('.panel-default').find('.panel-collapse input[type="checkbox"]').each(function(){
                    $(this).prop('checked', false);
                });
            }
        }
        function radioClickEvent(element){
            if($(element).prop('checked') == true){
                var name = $(element).attr('name');
                var classname = $(element).attr('class');
                $("."+classname+":not([name='"+name+"'])").each(function(){
                    $(this).attr('checked', false)
                });
            }
        }
    </script>
@endsection

