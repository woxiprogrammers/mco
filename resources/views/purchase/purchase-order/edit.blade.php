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
                                @if($isClosed != true)
                                    <div class="form-group " style="text-align: center">
                                        <button id="poCloseBtn" type="submit" class="btn red pull-right margin-top-15">
                                            <i class="fa fa-close" style="font-size: large"></i>
                                            Close this purchase order.
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
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
                                                            <label style="color: darkblue;">Client Name</label>
                                                            <input type="text" class="form-control" name="client_name" value="{{$purchaseOrderList['client_name']}}" readonly tabindex="-1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label style="color: darkblue;">Project Name</label>
                                                            <input type="text" class="form-control" name="project_name" value="{{$purchaseOrderList['project']}}" readonly tabindex="-1">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label style="color: darkblue;">Purchase Request Id</label>
                                                            <input type="text" class="form-control" name="client_name"  value="{{$purchaseOrderList['purchase_request_format_id']}}" readonly tabindex="-1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label style="color: darkblue;">Vendor Name</label>
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
                                                    <div class="table-container">
                                                        @if($isClosed != true)
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
                                                                    <th> Quantity</th>
                                                                    <th> Unit </th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($materialList as $key => $materialData)
                                                                    <tr>
                                                                        <td> {{$materialData['material_component_name']}} </td>
                                                                        <td>  {{$materialData['material_component_quantity']}} </td>
                                                                        <td> {{$materialData['material_component_unit_name']}} </td>
                                                                        <td><button class="image" value="{{$materialData['purchase_order_component_id']}}">View</button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
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
                                                                <form role="form" class="form-horizontal" method="post">
                                                                    {!! csrf_field() !!}
                                                                    <div class="form-body">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-12" style="text-align: right">
                                                                                <input type="text" class="form-control empty typeahead tt-input" id="material_name" placeholder="Enter material name" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top; background-color: transparent;" readonly>
                                                                                <br><input type="text" class="form-control empty typeahead tt-input" id="qty" placeholder="Enter Quantity" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top; background-color: transparent;" readonly>
                                                                                <br><input type="text" class="form-control empty typeahead tt-input" id="unit" placeholder="Enter Unit" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top; background-color: transparent;" readonly>
                                                                                <br><input type="text" class="form-control empty typeahead tt-input" id="rate" placeholder="Enter Rate" autocomplete="off" readonly>
                                                                                <br><input type="hidden" class="form-control empty typeahead tt-input" id="searchbox" placeholder="Enter Rate" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top; background-color: transparent;" readonly>
                                                                                <br><input type="text" class="form-control empty typeahead tt-input" id="hsn_code" placeholder="Enter HSNCODE" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top; background-color: transparent;" >
                                                                               <br>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-12">
                                                                                        Vendor Quotation Image
                                                                                            <!-- Wrapper for slides -->
                                                                                                <div id ="imagecorousel">

                                                                                                </div>
                                                                                    </div>
                                                                                </div>
                                                                                <br>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-12">
                                                                                        Client Approval Note Image
                                                                                                <div id ="imagecorouselForClientApproval">

                                                                                                </div>
                                                                                    </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade " id="paymentModal"  role="dialog">
                                                        <div class="modal-dialog">
                                                            <!-- Modal content-->
                                                            <div class="modal-content">
                                                                <form id="add_payment_form" action="/purchase/purchase-order/add-payment" method="post">
                                                                <div class="modal-header">
                                                                    <div class="row">
                                                                        <div class="col-md-4"></div>
                                                                        <div class="col-md-4" style="font-size: 18px"> Payment</div>
                                                                        <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-body" style="padding:40px 50px;">
                                                                    <div class="form-group row">
                                                                        <input type="hidden" id="po_bill_id" name="purchase_order_bill_id">
                                                                        <select class="form-control" name="user_id">
                                                                           @foreach($systemUsers as $user)
                                                                           <option value="{{$user['id']}}">{{$user['first_name']}}  {{$user['last_name']}}</option>
                                                                           @endforeach
                                                                       </select>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <select class="form-control" name="payment_slug">                                              @foreach($transaction_types as $type)
                                                                                <option value="{{$type['slug']}}">{{$type['slug']}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <input type="number" class="form-control" id="bilAmount" name="amount" placeholder="Enter Amount" readonly>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <input type="number" class="form-control"  name="reference_number" placeholder="Enter Reference Number" >
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <input type="text" class="form-control" name="remark"  placeholder="Enter Transaction details">
                                                                    </div>
                                                                    {{--<div class="form-group row">Quotation images
                                                                        <div id="myCarousel" class="carousel slide" style="height: 150px" data-ride="carousel">
                                                                            <!-- Indicators -->
                                                                            <ol class="carousel-indicators">
                                                                                <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                                                                                <li data-target="#myCarousel" data-slide-to="1"></li>
                                                                                <li data-target="#myCarousel" data-slide-to="2"></li>
                                                                            </ol>

                                                                            <!-- Wrapper for slides -->
                                                                            <div class="carousel-inner">
                                                                                <div class="item active">
                                                                                    <img src="la.jpg" alt="Los Angeles" style="width:100%;">
                                                                                </div>

                                                                                <div class="item">
                                                                                    <img src="chicago.jpg" alt="Chicago" style="width:100%;">
                                                                                </div>

                                                                                <div class="item">
                                                                                    <img src="ny.jpg" alt="New york" style="width:100%;">
                                                                                </div>
                                                                            </div>

                                                                            <!-- Left and right controls -->
                                                                            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                                                                <span class="glyphicon glyphicon-chevron-left"></span>
                                                                                <span class="sr-only">Previous</span>
                                                                            </a>
                                                                            <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                                                                <span class="glyphicon glyphicon-chevron-right"></span>
                                                                                <span class="sr-only">Next</span>
                                                                            </a>
                                                                        </div>
                                                                    </div>--}}
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
                                                                                                <li><input type="checkbox" class="component-select" value="{{$materialData['purchase_order_component_id']}}"><label class="control-label">{{$materialData['material_component_name']}} </label></li>
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
                                                                                        <input type="datetime-local"   class="form-control" name="in_time" placeholder="Enter In Time">
                                                                                    </div>
                                                                                    <div class="form-group row">
                                                                                        <input type="datetime-local" class="form-control" name="out_time" placeholder="Enter Out Time">
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
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-12">
                                               <!-- BEGIN VALIDATION STATES-->
                                                <div class="portlet light ">
                                                    <div class="portlet-body form">
                                                        <div class="row">
                                                            <div class="col-md-12">
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
    </script>
@endsection
