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
                                            <input type="hidden" name="purchase_request_id" id="purchaseRequestId">
                                            <div class="form-actions noborder row">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label class="control-label pull-right">
                                                            Purchase Request :
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control typeahead" id="purchaseRequest" name="purchaseRequest">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label class="control-label pull-right">
                                                            Delivery Address :
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="delivery_address" name="delivery_address">
                                                            @foreach($deliveryAddresses as $deliveryAddress)
                                                                <option value="{{$deliveryAddress}}">{{$deliveryAddress}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label class="control-label pull-right">
                                                            Remarks :
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <textarea class="form-control" id="por_remarks" name="por_remarks" ></textarea>
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
    <script src="/assets/custom/purchase/purchase-order-request/purchase-order-request.js"></script>
    <script>
        $(document).ready(function(){
            var  CreatePurchaseOrderRequest = function () {
                var handleCreate = function() {
                    var form = $('#createPurchaseOrderRequest');
                    var error = $('.alert-danger', form);
                    var success = $('.alert-success', form);
                    form.validate({
                        errorElement: 'span', //default input error message container
                        errorClass: 'help-block', // default input error message class
                        focusInvalid: false, // do not focus the last invalid input
                        rules: {
                            purchase_request_id: {
                                required: true
                            }
                        },

                        messages: {
                            purchase_request_id: {
                                required: "Purchase Request required"
                            }
                        },

                        invalidHandler: function (event, validator) { //display error alert on form submit
                            success.hide();
                            error.show();
                            alert('Please fill valid data');
                        },

                        highlight: function (element) { // hightlight error inputs
                            $(element)
                                .closest('.form-group').addClass('has-error'); // set error class to the control group
                        },

                        unhighlight: function (element) { // revert the change done by hightlight
                            $(element)
                                .closest('.form-group').removeClass('has-error'); // set error class to the control group
                        },

                        success: function (label) {
                            label
                                .closest('.form-group').addClass('has-success');
                        },

                        submitHandler: function (form) {
                            var purchaseRequestId = $("#purchaseRequestId").val();
                            if($("#hiddenInputs").length > 0 && !(typeof purchaseRequestId == 'undefined' || purchaseRequestId == '')){
                                $("button[type='submit']").prop('disabled', true);
                                success.show();
                                error.hide();
                                form.submit();
                            }else{
                                alert('Please fill valid data');
                            }

                        }
                    });
                };

                return {
                    init: function () {
                        handleCreate();
                    }
                };
            }();
            CreatePurchaseOrderRequest.init();
        });


    </script>
@endsection
