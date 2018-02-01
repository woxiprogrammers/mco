<?php
    /**
     * Created by Harsha.
     * Date: 29/1/18
     * Time: 5:29 PM
     */

    ?>

@extends('layout.master')
@section('title','Constro | View Asset Maintenance Request')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />

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
                                    <h1>View Asset Maintenance Request</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/asset/maintenance/request/manage">Manage Asset Maintenance Request</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">View Asset Maintenance Request </a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <input type="hidden" id="assetMaintenanceId" value="{{$assetMaintenance->id}}">
                                            <ul class="nav nav-tabs nav-tabs-lg">
                                                <li class="active">
                                                    <a href="#viewInfoTab" data-toggle="tab"> View Asset Maintenance </a>
                                                </li>
                                                <li>
                                                    <a href="#vendorAssignmentTab" data-toggle="tab"> Assign Vendors</a>
                                                </li>
                                                <li>
                                                    <a href="#transactionTab" data-toggle="tab"> Transactions </a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane fade in active" id="viewInfoTab">
                                                    <div class="form-body">
                                                        @if($assetMaintenance->assetMaintenanceStatus->slug == 'vendor-approved')
                                                            <div class="row">
                                                                <div class="col-md-offset-9 col-md-3 ">
                                                                    <a class="btn red pull-right" href="javascript:void(0);" id="transactionButton">
                                                                        <i class="fa fa-plus" style="font-size: large"></i>&nbsp;
                                                                        Transaction
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="asset_name" class="control-label">Asset</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <span>{!! $assetMaintenance->asset->name !!}</span>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="remark" class="control-label">Remark</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <span>{!! $assetMaintenance->remark !!}</span>
                                                            </div>
                                                        </div>
                                                        @if(count($assetMaintenance->assetMaintenanceImage) > 0)
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="remark" class="control-label">Images</label>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div id ="imagecorouselForAssetMaintenanceRequest">
                                                                        @foreach($imageData as $key => $image)
                                                                            <a href="{{$image['upload_path']}}">
                                                                                <img id="image" src="{{$image['upload_path']}}" style="text-align:left;height: 170px">
                                                                            </a>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade in" id="vendorAssignmentTab">
                                                    @if($assetMaintenance->assetMaintenanceStatus->slug == 'maintenance-requested' || $assetMaintenance->assetMaintenanceStatus->slug == 'vendor-assigned')
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right" for="project_site">Search Vendor</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control empty typeahead" id="vendorList" name="vendorList" placeholder="Enter vendor name">
                                                            </div>
                                                        </div>
                                                        <div class="row"  style="margin-top: 2%">
                                                            <div class="col-md-3">
                                                                <a class="btn blue pull-right" id="removeButton" >Remove</a>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="row"  style="margin-top: 0.5%">
                                                        <div class="col-md-8 col-md-offset-2">
                                                            <form role="form" id="assignVendorForm" action="/asset/maintenance/request/vendor/assign/{{$assetMaintenance['id']}}" method="POST">
                                                                {{csrf_field()}}
                                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="assignVendorTable">
                                                                    <thead>
                                                                    <tr>
                                                                        <th style="width: 10%;">Remove</th>
                                                                        <th> Vendor Information</th>
                                                                        <th> Quotation Amount</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @foreach($assetMaintenance->assetMaintenanceVendorRelation as  $key => $assetMaintenanceVendorRelation)
                                                                        <tr>
                                                                            <td style="width: 10%;">
                                                                                <input type="checkbox" class="vendor-row-checkbox" disabled>
                                                                            </td>
                                                                            <td>
                                                                                <input name="vendors[]" type="hidden" value="{{$assetMaintenanceVendorRelation->vendor_id}}">
                                                                                <div class="row">
                                                                                    <div class="col-md-9"  style="text-align: left">
                                                                                        <label class="control-label">{{$assetMaintenanceVendorRelation->vendor->name}}</label>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="row">
                                                                                    <div class="col-md-9"  style="text-align: left">
                                                                                        <label class="control-label">{{$assetMaintenanceVendorRelation->quotation_amount}}</label>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                                @if($assetMaintenance->assetMaintenanceStatus->slug == 'maintenance-requested' || $assetMaintenance->assetMaintenanceStatus->slug == 'vendor-assigned')
                                                                    <div class="form-actions noborder row">
                                                                        <div class="col-md-offset-3" style="margin-left: 26%">
                                                                            <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade in" id="transactionTab">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            @if(count($assetMaintenance->assetMaintenanceTransaction) > 0)
                                                                <table class="table table-striped table-bordered table-hover order-column" id="assetMaintenanceTransaction">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Vendor Name</th>
                                                                        <th style="width: 40%"> GRN</th>
                                                                        <th>Status</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @foreach($assetMaintenance->assetMaintenanceTransaction as $assetMaintenanceTransaction)
                                                                        <tr>
                                                                            <td>
                                                                                {!! $vendorApproved->vendor->name !!}
                                                                            </td>
                                                                            <td>
                                                                                <input type="hidden" value="{{$assetMaintenanceTransaction['id']}}">
                                                                                {{$assetMaintenanceTransaction['grn']}}
                                                                            </td>
                                                                            <td>
                                                                                {!! $assetMaintenanceTransaction->assetMaintenanceTransactionStatus->name !!}
                                                                            </td>
                                                                            <td>
                                                                                <a href="javascript:void(0);" class="btn blue transaction-view-btn">
                                                                                    View
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
                                                <div class="modal fade" id="transactionModal" role="dialog">
                                                    <div class="modal-dialog transaction-modal" style="width: 90%; ">
                                                        <!-- Modal content-->
                                                        <div class="modal-content" style="overflow: scroll !important;">
                                                            <div class="modal-header">
                                                                <div class="row">
                                                                    <div class="col-md-4"></div>
                                                                    <div class="col-md-4" style="font-size: 18px"> Asset Maintenance Transaction</div>
                                                                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body" style="padding:40px 50px;">
                                                                <form id="transactionForm" action="/asset/maintenance/request/transaction/create" method="POST">
                                                                    {!! csrf_field() !!}
                                                                    <input type="hidden" name="assetMaintenanceId" value="{{$assetMaintenance->id}}">
                                                                    <input type="hidden" id="assetMaintenanceTransactionId" name="asset_maintenance_transaction_id">
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
                                                                            <div id="transactionCommonFieldDiv" >
                                                                                <div class="form-group row">
                                                                                    <input type="text" class="form-control" name="bill_number" placeholder="Enter Bill Number">
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <input type="text" class="form-control" name="bill_amount" placeholder="Enter Bill Amount">
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
                                                <div class="modal fade" id="viewTransactionModal" role="dialog">
                                                    <div class="modal-dialog transaction-modal" style="width: 90%; ">
                                                        <!-- Modal content-->
                                                        <div class="modal-content" style="overflow: scroll !important;">
                                                            <div class="modal-header">
                                                                <div class="row">
                                                                    <div class="col-md-4"></div>
                                                                    <div class="col-md-4" style="font-size: 18px"> Asset Maintenance Transaction Detail</div>
                                                                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body" style="padding:40px 50px;">

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
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script><script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
    {{--<script src="/assets/custom/admin/asset/image-datatable.js"></script>
    <script src="/assets/custom/admin/asset/image-upload.js"></script>--}}
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script>
        $('#vendorList').addClass('typeahead');
        var assetMaintenanceId = $('#assetMaintenanceId').val();
        var citiList = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: "/asset/maintenance/request/vendor/auto-suggest/%QUERY/"+assetMaintenanceId,
                filter: function(x) {
                    if($(window).width()<420){
                        $("#header").addClass("fixed");
                    }
                    return $.map(x, function (data) {
                        return {
                            vendorList:data.name,
                            tr_view:data.tr_view,
                            vendor_id:data.vendor_id
                        };
                    });
                },
                wildcard: "%QUERY"
            }
        });
        citiList.initialize();
        $('.typeahead').typeahead(null, {
            displayKey: 'name',
            engine: Handlebars,
            source: citiList.ttAdapter(),
            limit: 30,
            templates: {
                empty: [
                    '<div class="empty-suggest">',
                    'Unable to find any Result that match the current query',
                    '</div>'
                ].join('\n'),
                suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{vendorList}}</strong></div>')
            },
        }).on('typeahead:selected', function (obj, datum) {
            var POData = $.parseJSON(JSON.stringify(datum));
            console.log(POData);
            var trString = '<tr>' +
                '           <th style="width: 10%;"><input type="checkbox" class="vendor-row-checkbox"></th>\n' +
                '           <th>'+POData.tr_view+'</th>'+
                '           <th><input type="text" class="form-control amount" name="vendor_data['+POData.vendor_id+']"></th>\n</th></tr>';
            $("#assignVendorTable tbody").append(trString);
            $("#removeButton").closest('.row').show();
            $("#assignVendorTable").show();
        }).on('typeahead:open', function (obj, datum) {

        });

        $(document).ready(function() {

            $("#removeButton").on('click', function () {
                if ($("#assignVendorTable tbody input:checkbox:checked").length > 0) {
                    $("#assignVendorTable tbody input:checkbox:checked").each(function () {
                        $(this).closest('tr').remove();
                    });
                }
                if ($("#assignVendorTable tbody input:checkbox").length <= 0) {
                    $("#removeButton").closest('.row').hide();
                    $("#assignVendorTable").hide();
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

            $("#grnImageUplaodButton a").on('click',function(){
                var imageArray = $("#transactionForm").serializeArray();
                $.ajax({
                    url: '/asset/maintenance/request/transaction/upload-pre-grn-images',
                    type: 'POST',
                    data: imageArray,
                    success: function(data, textStatus, xhr){
                        $("#imageupload").hide();
                        $("#grnImageUplaodButton").hide();
                        $("#assetMaintenanceTransactionId").val(data.asset_maintenance_transaction_id);
                        $("#transactionForm input[name='grn']").val(data.grn);
                        $("#afterImageUploadDiv").show();
                    },
                    error: function(errorData){

                    }
                });
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

            $("#transactionButton").on('click',function(){
                var assetMaintenanceId = $("#assetMaintenanceId").val();
                $.ajax({
                    url:'/asset/maintenance/request/transaction/check-generated-grn/'+assetMaintenanceId+'?_token='+$("input[name='_token']").val(),
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
                            $("#assetMaintenanceTransactionId").val(data.asset_maintenance_transaction_id);
                            $("#transactionForm input[name='grn']").val(data.grn);
                            $("#afterImageUploadDiv").show();
                        }
                        $("#transactionModal").modal('show');
                    },
                    error: function(errorData){

                    }
                });
            });

            $(".transaction-view-btn").on('click', function(){
                var transactionId = $(this).closest('tr').find('input[type="hidden"]').val();
                console.log(transactionId);
                $.ajax({
                    url:'/asset/maintenance/request/transaction/view/'+transactionId+"?_token="+$('input[name="_token"]').val(),
                    type: 'GET',
                    success: function(data,textStatus,xhr){
                        $("#viewTransactionModal .modal-body").html(data);
                        $("#viewTransactionModal").modal('show');
                    },
                    error: function(errorStatus){

                    }
                });
            });

        });

    </script>
@endsection

