@extends('layout.master')
@section('title','Constro | Manage Materials')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <style>

    </style>
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
                                    <h1>Edit Purchase Order</h1>
                                </div>
                                @if($purchaseOrderStatusSlug != 'close')
                                    <div class="form-group " style="text-align: center">
                                        <button id="poCloseBtn" type="submit" class="btn red pull-right margin-top-15">
                                            <i class="fa fa-close" style="font-size: large"></i>
                                            Close
                                        </button>
                                    </div>
                                @elseif($purchaseOrderStatusSlug == 'close' && $userRole == 'superadmin')
                                    <div class="form-group " style="text-align: center">
                                        <button id="poReopenBtn" type="submit" class="btn red pull-right margin-top-15">
                                            <i class="fa fa-open" style="font-size: large"></i>
                                            Reopen
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/purchase/purchase-order/manage">Manage Purchase Order</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Edit Purchase Order</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <input type="hidden" id="po_id" value="{{$purchaseOrderList['purchase_order_id']}}">
                                <input type="hidden" id="vendor_id" value="{{$purchaseOrderList['vendor_id']}}">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="portlet light ">
                                            <div class="portlet-body form">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                                <label style="color: darkblue;">Purchase Order Id</label>
                                                                <input type="text" class="form-control" name="po_id" value="{{$purchaseOrderList['purchase_order_format_id']}}" readonly tabindex="-1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label style="color: darkblue;">Purchase Request Id</label>
                                                            <input type="text" class="form-control" name="client_name"  value="{{$purchaseOrderList['purchase_request_format_id']}}" readonly tabindex="-1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            @if($purchaseOrderList['is_client_order'] == true)
                                                                <label style="color: darkblue;">Client Name</label>
                                                            @else
                                                                <label style="color: darkblue;">Vendor Name</label>
                                                            @endif
                                                            <input type="text" class="form-control" name="client_name"  value="{{$purchaseOrderList['vendor_name']}}" readonly tabindex="-1">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- BEGIN VALIDATION STATES-->
                                            <div class="portlet light ">
                                                <div class="portlet-body">
                                                    @if($purchaseOrderList['is_client_order'] == false)
                                                        <ul class="nav nav-tabs nav-tabs-lg">
                                                            <li class="active">
                                                                <a href="#generalInfoTab" data-toggle="tab"> General Information </a>
                                                            </li>
                                                            <li>
                                                                <a href="#advancePaymentTab" data-toggle="tab"> Advance Payment </a>
                                                            </li>
                                                        </ul>
                                                    @endif
                                                    <div class="tab-content">
                                                        <div class="tab-pane fade in active" id="generalInfoTab">
                                                            <div class="table-container">
                                                                @if(($purchaseOrderStatusSlug == 'open' || $purchaseOrderStatusSlug == 're-open') && ($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-purchase-bill')))
                                                                    <div class="row">
                                                                        <div class="col-md-offset-9 col-md-3 ">
                                                                            <a class="btn red pull-right" href="javascript:void(0);" id="transactionButton">
                                                                                <i class="fa fa-plus" style="font-size: large"></i>&nbsp;
                                                                                Transaction
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <table class="table table-striped table-bordered table-hover order-column" id="purchaseRequest" style="margin-top: 1%;">
                                                                    <thead>
                                                                    <tr>
                                                                        <th> Material Name </th>
                                                                        <th> Consumed Quantity</th>
                                                                        <th> Quantity</th>
                                                                        <th> Unit </th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @foreach($materialList as $key => $materialData)
                                                                        <tr>
                                                                            <td> {{$materialData['material_component_name']}} </td>
                                                                            <td> {{$materialData['consumed_quantity']}} </td>
                                                                            <td> {{$materialData['material_component_quantity']}} </td>
                                                                            <td> {{$materialData['material_component_unit_name']}} </td>
                                                                            <td><button class="component-view" value="{{$materialData['purchase_order_component_id']}}">View</button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="container">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <!-- BEGIN VALIDATION STATES-->
                                                                        <div class="portlet light ">
                                                                            <div class="portlet-body form">
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        @if(count($purchaseOrderTransactionListing) > 0)
                                                                                            <table class="table table-striped table-bordered table-hover order-column" id="purchaseRequest">
                                                                                                <thead>
                                                                                                <tr>
                                                                                                    <th style="width: 40%"> GRN</th>
                                                                                                    <th>Status</th>
                                                                                                    <th>Action</th>
                                                                                                </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                @foreach($purchaseOrderTransactionListing as $purchaseOrderTransaction)
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <input type="hidden" value="{{$purchaseOrderTransaction['purchase_order_transaction_id']}}">
                                                                                                            {{$purchaseOrderTransaction['grn']}}
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            {!! $purchaseOrderTransaction['status'] !!}
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <a href="javascript:void(0);" class="btn blue transaction-edit-btn">
                                                                                                                Edit
                                                                                                            </a>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                @endforeach
                                                                                                </tbody>
                                                                                            </table>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade in" id="advancePaymentTab">
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Total Advance Paid Amount</label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control" value="{{$purchaseOrderList['total_advance_amount']}}" readonly>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="control-label pull-right">Balance Advance Paid Amount</label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control" value="{{$purchaseOrderList['balance_advance_amount']}}" readonly>
                                                                </div>
                                                            </div>

                                                            <div class="btn-group pull-right margin-top-15">
                                                                <a id="sample_editable_1_new" class="btn yellow" href="#paymentModal" data-toggle="modal" >
                                                                    <i class="fa fa-plus"></i>  &nbsp; Advance Payment
                                                                </a>
                                                            </div>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="purchaseOrderAdvancePaymentTable">
                                                                <thead>
                                                                <tr>
                                                                    <th style="width: 25%"> Date </th>
                                                                    <th style="width: 25%"> Amount </th>
                                                                    <th style="width: 25%"> Payment Method </th>
                                                                    <th style="width: 25%"> Reference Number </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="ImageUpload" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                        <div class="modal-dialog transaction-modal">
                                            <div class="modal-content">

                                                <div class="modal-header" >
                                                    <div class="row">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4"> Material</div>
                                                        <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                    </div>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" class="form-horizontal" id="PurchaseOrderComponentEditForm" method="post" action="/purchase/purchase-order/edit/{{$purchaseOrderList['purchase_order_id']}}">

                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade " id="paymentModal"  role="dialog">
                                        <div class="modal-dialog">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <form id="add_payment_form" action="/purchase/purchase-order/add-advance-payment" method="post">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="purchase_order_id" value="{{$purchaseOrderList['purchase_order_id']}}">
                                                    <div class="modal-header">
                                                        <div class="row">
                                                            <div class="col-md-4"></div>
                                                            <div class="col-md-4" style="font-size: 18px"> Payment</div>
                                                            <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-body" style="padding:40px 50px;">
                                                        <div class="form-group row">
                                                            <select class="form-control" id="paid_from_slug" name="paid_from_slug" onchange="changePaidFrom(this)">
                                                                <option value="bank">Bank</option>
                                                                <option value="cash">Cash</option>
                                                            </select>
                                                        </div>
                                                        <div id="bankData">
                                                            <div class="form-group row" id="bankSelect">
                                                                <select class="form-control" id="bank_id" name="bank_id" onchange="checkAmount()">
                                                                    <option value="">--- Select Bank ---</option>
                                                                    @foreach($banks as $bank)
                                                                        <option value="{{$bank['id']}}">{{$bank['bank_name']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group row">
                                                                <select class="form-control" name="payment_id">
                                                                    <option value="">--- Select Payment Type ---</option>
                                                                    @foreach($transaction_types as $type)
                                                                        <option value="{{$type['id']}}">{{$type['name']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>


                                                        <input type="hidden" id="allowedAmount">
                                                        <input type="hidden" id="cashAllowedAmount" value="{{$cashAllowedLimit}}">

                                                        @foreach($banks as $bank)
                                                            <input type="hidden" id="balance_amount_{{$bank['id']}}" value="{{$bank['balance_amount']}}">
                                                        @endforeach


                                                        <div class="form-group row">
                                                            <input type="number" class="form-control" id="bilAmount" name="amount" placeholder="Enter Amount" onkeyup="checkAmount()">
                                                        </div>
                                                        <div class="form-group row">
                                                            <input type="number" class="form-control"  name="reference_number" placeholder="Enter Reference Number" >
                                                        </div>
                                                        <button class="btn btn-set red pull-right" type="submit">
                                                            <i class="fa fa-check" style="font-size: large"></i>
                                                            Add &nbsp; &nbsp; &nbsp;
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="transactionModal" role="dialog">
                                        <div class="modal-dialog transaction-modal" style="width: 90%; ">
                                            <!-- Modal content-->
                                            <div class="modal-content" style="overflow: scroll !important;">
                                                <div class="modal-header">
                                                    <div class="row">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4" style="font-size: 18px"> Purchase Order Transaction</div>
                                                        <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                    </div>
                                                </div>
                                                <div class="modal-body" style="padding:40px 50px;">
                                                    <form id="transactionForm" action="/purchase/purchase-order/transaction/create" method="POST">
                                                        {!! csrf_field() !!}
                                                        <input type="hidden" name="purchase_order_id" value="{{$purchaseOrderList['purchase_order_id']}}">
                                                        <input type="hidden" id="purchaseOrderTransactionId" name="purchase_order_transaction_id">
                                                        <input type="hidden" id="type" value="upload_bill">
                                                        <div class="form-body">
                                                            <div class="form-group">
                                                                <label class="control-label">Select Images For Generating GRN :</label>
                                                                <input id="imageupload" type="file" class="btn blue" multiple />
                                                                <br />
                                                                <div class="row">
                                                                    <div id="preview-image" class="row">

                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-3" id="grnImageUplaodButton" style="margin-top: 1%;" hidden>
                                                                        <a href="javascript:void(0);" class="btn blue" > Upload Images</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="afterImageUploadDiv" hidden>

                                                                <div class="form-group">
                                                                    <div class="col-md-3">
                                                                        <label class="control-label pull-right"> GRN :</label>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input class="form-control" name="grn" readonly>
                                                                    </div>
                                                                </div>
                                                                <div id="componentSelectDiv" style="margin-top: 5%;">
                                                                    <div class="form-control product-material-select" style="font-size: 14px; height: 200px !important;" >
                                                                        <ul id="material_id" class="list-group">
                                                                            @foreach($materialList as $key => $materialData)
                                                                                @if($materialData['material_component_remaining_quantity'] != 0.0)
                                                                                    <li><input type="checkbox" class="component-select" value="{{$materialData['purchase_order_component_id']}}"><label class="control-label">{{$materialData['material_component_name']}} </label></li>
                                                                                @else
                                                                                    <li><input type="checkbox" class="component-select" value="{{$materialData['purchase_order_component_id']}}" disabled="disabled"><label class="control-label">{{$materialData['material_component_name']}} </label>&nbsp;&nbsp;&nbsp;(PO Complete)</li>
                                                                                @endif
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-md-3 col-md-offset-3" style="margin-top: 1%">
                                                                        <a class="pull-right btn blue" href="javascript:void(0);" id="componentSelectButton"> Select </a>
                                                                    </div>

                                                                </div>
                                                                <div id="componentDetailsDiv" hidden style="margin-top: 5%;">

                                                                </div>
                                                                <div id="transactionCommonFieldDiv" hidden>
                                                                    <div class="form-group row">
                                                                        <label>Vendor Name</label>
                                                                        <input type="text" class="form-control" id="vendor" name="vendor_name" placeholder="Enter Vendor Name" value="{{$vendorName}}" readonly>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <input type="text" class="form-control" name="bill_number" placeholder="Enter Bill Number">
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <input type="text" class="form-control" name="bill_amount" placeholder="Enter Bill Amount">
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <input type="text" class="form-control" name="vehicle_number" placeholder="Enter Vehicle Number">
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <input type="text" class="form-control" name="remark" placeholder="Enter Remark">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="control-label">Select Images :</label>
                                                                        <input id="postImageUpload" type="file" class="btn blue" multiple />
                                                                        <br />
                                                                        <div class="row">
                                                                            <div id="postPreviewImage" class="row">

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <button type="submit" class="btn btn-set red pull-right">
                                                                        <i class="fa fa-check" style="font-size: large"></i>
                                                                        Save&nbsp; &nbsp; &nbsp;
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="editTransactionModal" role="dialog">
                                        <div class="modal-dialog transaction-modal" style="width: 90%; ">
                                            <!-- Modal content-->
                                            <div class="modal-content" style="overflow: scroll !important;">
                                                <div class="modal-header">
                                                    <div class="row">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4" style="font-size: 18px"> Purchase Order Transaction</div>
                                                        <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                    </div>
                                                </div>
                                                <div class="modal-body" style="padding:40px 50px;">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade " id="amendmentModal"  role="dialog">
                                        <div class="modal-dialog">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <form id="approve_bill" action="/purchase/purchase-order/change-status" method="post">
                                                    <div class="modal-header">
                                                        <div class="row">
                                                            <div class="col-md-4"></div>
                                                            <div class="col-md-4" style="font-size: 18px"> Approve</div>
                                                            <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-body" style="padding:40px 50px;">
                                                        <div class="form-group row">
                                                            <input type="text" class="form-control" name="remark">
                                                            <input type="hidden" class="form-control" name="purchase_order_bill_id" id="purchase_order_bill_id">
                                                        </div>
                                                        <button class="btn btn-set red pull-right" type="submit">
                                                            <i class="fa fa-check" style="font-size: large"></i>
                                                            Approve &nbsp; &nbsp; &nbsp;
                                                        </button>
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
</div>
@endsection
@section('javascript')
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <link rel="stylesheet"  href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css"/>
    <link rel="stylesheet"  href="/assets/global/css/app.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/custom/purchase/purchase-order/purchase-order.js" type="text/javascript"></script>
    <script src="/assets/custom/purchase/purchase-order/purchase-order-advance-payment-datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script src="/assets/custom/purchase/purchase-order/purchase-order-validations.js"></script>
    <style>
        @-webkit-keyframes zoom {
            from {
                -webkit-transform: scale(1, 1);
            }
            to {
                -webkit-transform: scale(3.5, 3.5);
            }
        }

        @keyframes zoom {
            from {
                transform: scale(1, 1);
            }
            to {
                transform: scale(1.7, 1.7);
            }
        }
        .carousel-inner .item > img {
            -webkit-animation: zoom 20s;
            animation: zoom 20s;
        }
    </style>
    <script>
        $(document).ready(function(){
            EditPurchaseOrder.init();
            $("#componentSelectButton").on('click',function(){
                if($(".component-select:checkbox:checked").length > 0){
                    var componentIds = [];
                    $(".component-select:checkbox:checked").each(function(){
                        componentIds.push($(this).val());
                    });
                    $.ajax({
                        url:'/purchase/purchase-order/get-component-details?_token='+$("input[name='_token']").val(),
                        type: "POST",
                        data:{
                            purchase_order_component_id: componentIds
                        },
                        success:function (data,textStatus,xhr) {
                            $("#componentDetailsDiv").html(data);
                            $("#componentDetailsDiv").show();
                            $("#transactionCommonFieldDiv").show();
                        },
                        error:function(errorData){
                            alert('Something went wrong.');
                        }
                    })
                }
            });
            $("#imageupload").on('change', function () {
                var countFiles = $(this)[0].files.length;
                var imgPath = $(this)[0].value;
                var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
                var image_holder = $("#preview-image");
                image_holder.empty();
                if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                    if (typeof (FileReader) != "undefined") {
                        for (var i = 0; i < countFiles; i++) {
                            var reader = new FileReader()
                            reader.onload = function (e) {
                                var imagePreview = '<div class="col-md-2"><input type="hidden" name="pre_grn_image[]" value="'+e.target.result+'"><img src="'+e.target.result+'" class="thumbimage" /></div>';
                                image_holder.append(imagePreview);
                            };
                            image_holder.show();
                            reader.readAsDataURL($(this)[0].files[i]);
                            $("#grnImageUplaodButton").show();
                        }
                    } else {
                        alert("It doesn't supports");
                    }
                } else {
                    alert("Select Only images");
                }
            });

            $("#postImageUpload").on('change', function () {
                var countFiles = $(this)[0].files.length;
                var imgPath = $(this)[0].value;
                var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
                var image_holder = $("#postPreviewImage");
                image_holder.empty();
                if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                    if (typeof (FileReader) != "undefined") {
                        for (var i = 0; i < countFiles; i++) {
                            var reader = new FileReader()
                            reader.onload = function (e) {
                                var imagePreview = '<div class="col-md-2"><input type="hidden" name="post_grn_image[]" value="'+e.target.result+'"><img src="'+e.target.result+'" class="thumbimage" /></div>';
                                image_holder.append(imagePreview);
                            };
                            image_holder.show();
                            reader.readAsDataURL($(this)[0].files[i]);
                        }
                    } else {
                        alert("It doesn't supports");
                    }
                } else {
                    alert("Select Only images");
                }
            });

            $("#grnImageUplaodButton a").on('click',function(){
                var imageArray = $("#transactionForm").serializeArray();
                $.ajax({
                    url: '/purchase/purchase-order/transaction/upload-pre-grn-images',
                    type: 'POST',
                    data: imageArray,
                    success: function(data, textStatus, xhr){
                        $("#imageupload").hide();
                        $("#grnImageUplaodButton").hide();
                        $("#purchaseOrderTransactionId").val(data.purchase_order_transaction_id);
                        $("#transactionForm input[name='grn']").val(data.grn);
                        $("#afterImageUploadDiv").show();
                    },
                    error: function(errorData){

                    }
                });
            });

            $(".transaction-edit-btn").on('click', function(){
               var transactionId = $(this).closest('tr').find('input[type="hidden"]').val();
               $.ajax({
                    url:'/purchase/purchase-order/transaction/edit/'+transactionId+"?_token="+$('input[name="_token"]').val()+"&isShowTax=false",
                    type: 'GET',
                    success: function(data,textStatus,xhr){
                        $("#editTransactionModal .modal-body").html(data);
                        $("#editTransactionModal").modal('show');
                    },
                    error: function(errorStatus){

                    }
               });
            });

            $("#transactionButton").on('click',function(){
                var purchaseOrderId = $("#po_id").val();
                $.ajax({
                    url:'/purchase/purchase-order/transaction/check-generated-grn/'+purchaseOrderId+'?_token='+$("input[name='_token']").val(),
                    type: 'GET',
                    success: function(data,textStatus,xhr){
                        console.log(data);
                        if(xhr.status == 200){
                            $.each(data.images, function(k ,v){
                                var imagePreview = '<div class="col-md-2"><img src="'+v+'" class="thumbimage" /></div>';
                                $("#preview-image").append(imagePreview);
                            });
                            $("#imageupload").hide();
                            $("#grnImageUplaodButton").hide();
                            $("#purchaseOrderTransactionId").val(data.purchase_order_transaction_id);
                            $("#transactionForm input[name='grn']").val(data.grn);
                            $("#afterImageUploadDiv").show();
                        }
                        $("#transactionModal").modal('show');
                    },
                    error: function(errorData){

                    }
                });
            });
        });

        function submitComponentForm(){
            var minQuantity = $("#ImageUpload .modal-body #minQuantity").val();
            var quantity = $("#ImageUpload .modal-body .quantity").val();
            if($.isNumeric(quantity) == true){
                minQuantity = parseFloat(minQuantity);
                quantity = parseFloat(quantity);
                if(minQuantity > quantity){
                    $("#ImageUpload .modal-body .quantity").closest('.form-group').addClass('has-error').removeClass('has-success');
                    alert('Minimum allowed quantity is '+ minQuantity);
                }else{
                    $("#ImageUpload .modal-body .quantity").closest('.form-group').removeClass('has-error').addClass('has-success');
                    $("#PurchaseOrderComponentEditForm").submit();
                }
            }else{
                $("#ImageUpload .modal-body .quantity").closest('.form-group').addClass('has-error').removeClass('has-success');
                alert('Please Enter digit only.');
            }
        }

        function checkAmount(){
            var paidFromSlug = $('#paid_from_slug').val();
            if(paidFromSlug == 'bank'){
                var selectedBankId = $('#bank_id').val();
                if(selectedBankId == ''){
                    alert('Please select Bank');
                }else{
                    var amount = parseFloat($('#amount').val());
                    if(typeof amount == '' || amount == 'undefined' || isNaN(amount)){
                        amount = 0;
                    }
                    var allowedAmount = parseFloat($('#balance_amount_'+selectedBankId).val());
                    $("input[name='amount']").rules('add',{
                        max: allowedAmount
                    });
                }
            }else{
                var cashAllowedAmount = parseFloat($('#cashAllowedAmount').val());
                $("input[name='amount']").rules('add',{
                    max: cashAllowedAmount
                });
            }
        }

        function changePaidFrom(element){
            var paidFromSlug = $(element).val();
            if(paidFromSlug == 'cash'){
                $('#bankData').hide();
            }else{
                $('#bankData').show();
            }

        }
    </script>
@endsection
