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
                                <h1>Create Purchase Order Request</h1>
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
                                    <a href="javascript:void(0);">Create Purchase Order Request</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                            <!-- BEGIN VALIDATION STATES-->
                            <div class="portlet light ">
                                <div class="portlet-body form">
                                    <form role="form" id="createPurchaseOrderRequest" class="form-horizontal" method="post" action="/purchase/purchase-order-request/create">
                                        {!! csrf_field() !!}
                                        <div class="form-actions noborder row">
                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <label class="control-label pull-right">
                                                        Purchase Request
                                                    </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control typeahead">
                                                </div>
                                            </div>
                                            <div class="table-scrollable" style="overflow: scroll !important;">
                                                <table class="table table-striped table-bordered table-hover" id="purchaseRequest" style="overflow: scroll; table-layout: fixed">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 12%"> Vendor </th>
                                                        <th style="width: 15%"> Material Name </th>
                                                        <th style="width: 10%"> Quantity </th>
                                                        <th style="width: 10%;"> Unit </th>
                                                        <th style="width: 10%"> Rate w/o Tax </th>
                                                        <th style="width: 10%"> Rate w/ Tax </th>
                                                        <th style="width: 10%"> Tax Amount </th>
                                                        <th style="width: 10%">
                                                            Action
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td style="width: 12%"> Manisha Construction </td>
                                                            <td style="width: 15%"> Cement </td>
                                                            <td style="width: 10%"> 10 </td>
                                                            <td style="width: 10%;"> Bags </td>
                                                            <td style="width: 10%"> 100 </td>
                                                            <td style="width: 10%"> 120 </td>
                                                            <td style="width: 10%"> 200 </td>
                                                            <td style="width: 10%">
                                                                <a class="btn blue" href="javascript:void(0);">
                                                                    Add Details
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 12%"> Karia </td>
                                                            <td style="width: 15%"> Cement </td>
                                                            <td style="width: 10%"> 10 </td>
                                                            <td style="width: 10%;"> Bags </td>
                                                            <td style="width: 10%"> 110 </td>
                                                            <td style="width: 10%"> 140 </td>
                                                            <td style="width: 10%"> 300 </td>
                                                            <td style="width: 10%">
                                                                <a class="btn blue" href="javascript:void(0);">
                                                                    Add Details
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-md-offset-3" style="margin-left: 26%">
                                                <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
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
