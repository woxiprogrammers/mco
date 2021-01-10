@extends('layout.master')
@section('title','Constro | Edit Challan')
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
                                @if($challan['project_site_in_date'] == null && $challan->inventoryComponentTransferStatus->slug == 'open')
                                <h1>Edit Challan <label style="color: darkred;">(Note: Site In pending)</label></h1>
                                @elseif ($isbillGenerated === 'true')
                                <h1>Edit Challan <label style="color: darkred;">(Note: Bill is already generated)</label></h1>
                                @else
                                <h1>Edit Challan</h1>
                                @endif
                            </div>
                            @if($challan->inventoryComponentTransferStatus->slug == 'open' || $challan->inventoryComponentTransferStatus->slug == 're-open' && $userRole == 'superadmin')
                            <div class="form-group " style="text-align: center">
                                <a class="btn red pull-right margin-top-15" data-toggle="modal" href="#closeChallan">
                                    <i class="fa fa-close" style="font-size: large"></i>
                                    Close Challan
                                </a>
                            </div>
                            @elseif($challan->inventoryComponentTransferStatus->slug == 'requested' && $userRole == 'superadmin')
                            <div class="form-group " style="text-align: center">
                                <button style="width:130px" id="disapproveChallan" type="submit" value="disapproved" class="btn red pull-right margin-top-15 approveDisapproveChallan">
                                    <i class="fa fas fa-ban" style="font-size: large"></i>
                                    Disapprove
                                </button>
                                <button style="width:130px" id="approveChallan" type="submit" value="approved" class="btn green pull-right margin-top-15 approveDisapproveChallan">
                                    <i class="fa fa-check-square-o" style="font-size: large"></i>
                                    Approve
                                </button>
                            </div>
                            @elseif($challan->inventoryComponentTransferStatus->slug == 'close' && $userRole == 'superadmin')
                            <div class="form-group " style="text-align: center">
                                <input type="hidden" id="is_bill_generated" name="bill_generated_status" value="{!! $isbillGenerated !!}">
                                <a class="btn red pull-right margin-top-15" data-toggle="modal" onclick="reopenChallan(this);">
                                    <i class=" fa fa-close" style="font-size: large"></i>
                                    Reopen Challan
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/inventory/transfer/challan/manage">Manage Challan</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">View Challan</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>

                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label style="color: darkblue;">Challan Number</label>
                                                        <input type="text" class="form-control" value="{{$challan['challan_number']}}" readonly tabindex="-1">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label style="color: darkblue;">From Site</label>
                                                        <input type="text" class="form-control" name="client_name" value="{{$challan->projectSiteOut->project->name}}" readonly tabindex="-1">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label style="color: darkblue;">To Site</label>
                                                        <input type="text" class="form-control" name="client_name" value="{{$challan->projectSiteIn->project->name ?? '-'}}" readonly tabindex="-1">
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

                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <!-- BEGIN VALIDATION STATES-->
                                                            <div class="portlet light ">
                                                                <div class="portlet-body form">
                                                                    <form role="form" id="edit-challan" class="form-horizontal" method="post" action="/inventory/transfer/challan/edit/{{$challan['id']}}">
                                                                        {!! csrf_field() !!}
                                                                        <input type="hidden" id="challan_id" name="challan_id" value="{{$challan['id']}}">
                                                                        <div class="form-body">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <table class="table table-striped table-bordered table-hover order-column" id="challanMaterials" style="margin-top: 1%;">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th> Material Name </th>
                                                                                                <th> Site Out Quantity</th>
                                                                                                @if ($isSiteInDone)
                                                                                                <th> Site In Quantity</th>
                                                                                                @endif
                                                                                                <th> Unit </th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            @foreach($components as $key => $materialData)
                                                                                            <tr>
                                                                                                <input type="hidden" id="componentRow-{{$materialData['out_transfer_id']}}-component-id" value="{{$materialData['out_inventory_component_id']}}">
                                                                                                <input type="hidden" class="out-transfer" id="{{$materialData['out_transfer_id']}}" value="{{$materialData['out_transfer_id']}}">
                                                                                                <td> {{$materialData['name']}} </td>
                                                                                                @if ($challan->inventoryComponentTransferStatus->slug == 'requested')
                                                                                                <td>
                                                                                                    <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                                        <input type="number" class="form-control site-out-transfer" id="componentRow-{{$materialData['out_transfer_id']}}-site-out-quantity" name="component[{{$materialData['out_transfer_id']}}][site_out_quantity]" value="{{$materialData['site_out_quantity']}}" onchange="checkQuantity(this, 'out-transfer')">
                                                                                                    </div>
                                                                                                </td>
                                                                                                @else
                                                                                                <td>
                                                                                                    <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                                        <input type="number" class="form-control site-out-transfer" id="componentRow-{{$materialData['out_transfer_id']}}-site-out-quantity" name="component[{{$materialData['out_transfer_id']}}][site_out_quantity]" value="{{$materialData['site_out_quantity']}}" readonly>
                                                                                                    </div>
                                                                                                </td>
                                                                                                @endif

                                                                                                @if ($isSiteInDone)
                                                                                                <td>
                                                                                                    <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                                        <input type="number" class="form-control site-in-transfer" id="componentRow-{{$materialData['out_transfer_id']}}-site-in-quantity" name="component[{{$materialData['out_transfer_id']}}][site_in_quantity]" value="{{$materialData['site_in_quantity']}}" onchange="checkQuantity(this, 'in-transfer')" readonly>
                                                                                                    </div>
                                                                                                </td>
                                                                                                @else
                                                                                                <td hidden>
                                                                                                    <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                                        <input type="number" class="form-control site-in-transfer" id="componentRow-{{$materialData['out_transfer_id']}}-site-in-quantity" name="component[{{$materialData['out_transfer_id']}}][site_in_quantity]" value="0">
                                                                                                    </div>
                                                                                                </td>
                                                                                                @endif
                                                                                                <td> {{$materialData['unit']}} </td>
                                                                                                <input type="hidden" id="componentRow-{{$materialData['out_transfer_id']}}-unit-id" value="{{$materialData['unit_id']}}">
                                                                                            </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row form-group" id="transportation_amount">
                                                                                <div class="col-md-3">
                                                                                    <label class="control-label pull-right">Transportation Amount</label>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control transportation-amount" name="transportation_amount" value="{{$challan['other_data']['transportation_amount']}}" onkeyup="calculateTransportationTaxes()">
                                                                                </div>
                                                                            </div>
                                                                            <div class=" row form-group" id="transportation_cgst">
                                                                                <div class="col-md-3">
                                                                                    <label class="control-label pull-right">CGST</label>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <div class="input-group">
                                                                                        <input type="text" class="form-control transportation-cgst-percentage" name="transportation_cgst_percent" value="{{$challan['other_data']['transportation_cgst_percent']}}" onkeyup="calculateTransportationTaxes()">
                                                                                        <span class="input-group-addon">%</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <input type="text" class="form-control transportation-cgst-amount" readonly>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row form-group" id="transportation_sgst">
                                                                                <div class="col-md-3">
                                                                                    <label class="control-label pull-right">SGST</label>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <div class="input-group">
                                                                                        <input type="text" class="form-control transportation-sgst-percentage" name="transportation_sgst_percent" value="{{$challan['other_data']['transportation_sgst_percent']}}" onkeyup="calculateTransportationTaxes()">
                                                                                        <span class="input-group-addon">%</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <input type="text" class="form-control transportation-sgst-amount" readonly>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row form-group" id="transportation_igst">
                                                                                <div class="col-md-3">
                                                                                    <label class="control-label pull-right">IGST</label>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <div class="input-group">
                                                                                        <input type="text" class="form-control transportation-igst-percentage" name="transportation_igst_percent" value="{{$challan['other_data']['transportation_igst_percent']}}" onkeyup="calculateTransportationTaxes()">
                                                                                        <span class="input-group-addon">%</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <input type="text" class="form-control transportation-igst-amount" readonly>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row form-group" id="transportation_total">
                                                                                <div class="col-md-3">
                                                                                    <label class="control-label pull-right">Transportation Total</label>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control transportation-total" value="{{$challan['other_data']['transportation_total']}}" readonly>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <div class="col-md-3" style="text-align: right">
                                                                                    <label class="control-label">Vendor</label>
                                                                                    <span>*</span>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control" id="vendor" name="vendor" value="{{$challan['other_data']['vendor_name']}}" disabled>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <div class="col-md-3" style="text-align: right">
                                                                                    <label class="control-label">Driver Name</label>
                                                                                    <span>*</span>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control" id="driver_name" name="driver_name" value="{{$challan['other_data']['driver_name']}}">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <div class="col-md-3" style="text-align: right">
                                                                                    <label class="control-label">Mobile</label>
                                                                                    <span>*</span>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control" id="mobile" name="mobile" value="{{$challan['other_data']['mobile']}}">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <div class="col-md-3" style="text-align: right">
                                                                                    <label class="control-label">Site Out Remark</label>
                                                                                    <span>*</span>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control" name="out_remark" value="{{$out_remark}}">
                                                                                </div>
                                                                            </div>
                                                                            @if($isSiteInDone)
                                                                            <div class="form-group row">
                                                                                <div class="col-md-3" style="text-align: right">
                                                                                    <label class="control-label">Site In Remark</label>
                                                                                    <span>*</span>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control" name="in_remark" value="{{$in_remark}}">
                                                                                </div>
                                                                            </div>
                                                                            @endif
                                                                            <div class="form-actions noborder row">
                                                                                <div class="col-md-offset-11">
                                                                                    <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit</button>
                                                                                </div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal fade " id="closeChallan" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4" style="font-size: 18px"> Close Challan</div>
                    <div class="col-md-4 "><button type="button" class="close" data-dismiss="modal">X</button></div>
                </div>
            </div>
            <div class="modal-body" style="padding:40px 50px;">
                <div class="form-group row">
                    <div class="col-md-4">
                        <label class="control-label pull-right">
                            Enter Password :
                        </label>
                    </div>
                    <div class="col-md-6">
                        <input type="password" id="challanClosePassword" class="form-control" name="password">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-3 col-md-offset-4">
                        <a class="btn btn-set red" href="javascript:void(0);" onclick="submitChallanPassword()">
                            <i class="fa fa-check" style="font-size: large"></i>
                            Submit &nbsp; &nbsp; &nbsp;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade " id="reopenChallan" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4" style="font-size: 18px"> Reopen Challan</div>
                    <div class="col-md-4 "><button type="button" class="close" data-dismiss="modal">X</button></div>
                </div>
            </div>
            <div class="modal-body" style="padding:40px 50px;">
                <div class="form-group row">
                    <div class="col-md-4">
                        <label class="control-label pull-right">
                            Enter Password :
                        </label>
                    </div>
                    <div class="col-md-6">
                        <input type="password" id="ReopenPassword" class="form-control" name="password">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-3 col-md-offset-4">
                        <a class="btn btn-set red" href="javascript:void(0);" onclick="submitReopenChallanPassword()">
                            <i class="fa fa-check" style="font-size: large"></i>
                            Submit &nbsp; &nbsp; &nbsp;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<link rel="stylesheet" href="/assets/global/plugins/datatables/datatables.min.css" />
<link rel="stylesheet" href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" />
<link rel="stylesheet" href="/assets/global/css/app.css" />
<script src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
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

    .carousel-inner .item>img {
        -webkit-animation: zoom 20s;
        animation: zoom 20s;
    }
</style>
<script>
    $(document).ready(function() {
        EditChallan.init();
        calculateTransportationTaxes();

        $(".approveDisapproveChallan").click(function() {
            var status = $(this).val();
            var challan_id = $('#challan_id').val();
            $.ajax({
                url: '/inventory/transfer/challan/' + challan_id + '/change-status?_token=' + $("input[name='_token']").val() + '&status=' + status,
                type: "GET",
                success: function(data, textStatus, xhr) {
                    if (data.success) {
                        location.reload();
                        alert("Challan " + status + " successfully");
                    } else {
                        alert('Something went wrong.');
                    }

                },
                error: function(errorData) {
                    alert('Something went wrong.');
                }
            })
        });

        $("#imageupload").on('change', function() {
            var countFiles = $(this)[0].files.length;
            var imgPath = $(this)[0].value;
            var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
            var image_holder = $("#preview-image");
            image_holder.empty();
            if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                if (typeof(FileReader) != "undefined") {
                    for (var i = 0; i < countFiles; i++) {
                        var reader = new FileReader()
                        reader.onload = function(e) {
                            var imagePreview = '<div class="col-md-2"><input type="hidden" name="pre_grn_image[]" value="' + e.target.result + '"><img src="' + e.target.result + '" class="thumbimage" /></div>';
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

        $("#postImageUpload").on('change', function() {
            var countFiles = $(this)[0].files.length;
            var imgPath = $(this)[0].value;
            var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
            var image_holder = $("#postPreviewImage");
            image_holder.empty();
            if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                if (typeof(FileReader) != "undefined") {
                    for (var i = 0; i < countFiles; i++) {
                        var reader = new FileReader()
                        reader.onload = function(e) {
                            var imagePreview = '<div class="col-md-2"><input type="hidden" name="post_grn_image[]" value="' + e.target.result + '"><img src="' + e.target.result + '" class="thumbimage" /></div>';
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
    });

    function checkAmount() {
        var paidFromSlug = $('#paid_from_slug').val();
        if (paidFromSlug == 'bank') {
            var selectedBankId = $('#bank_id').val();
            if (selectedBankId == '') {
                alert('Please select Bank');
            } else {
                var amount = parseFloat($('#amount').val());
                if (typeof amount == '' || amount == 'undefined' || isNaN(amount)) {
                    amount = 0;
                }
                var allowedAmount = parseFloat($('#balance_amount_' + selectedBankId).val());
                $("input[name='amount']").rules('add', {
                    max: allowedAmount
                });
            }
        } else {
            var cashAllowedAmount = parseFloat($('#cashAllowedAmount').val());
            $("input[name='amount']").rules('add', {
                max: cashAllowedAmount
            });
        }
    }

    function changePaidFrom(element) {
        var paidFromSlug = $(element).val();
        if (paidFromSlug == 'cash') {
            $('#bankData').hide();
        } else {
            $('#bankData').show();
        }

    }

    function submitChallanPassword() {
        var challan_id = $('#challan_id').val();
        var password = $.trim($("#challanClosePassword").val());
        if (password.length > 0) {
            $.ajax({
                type: "POST",
                url: "/inventory/transfer/challan/authenticate-challan-close",
                data: {
                    password: password,
                    challan_id: challan_id,
                    _token: $("input[name='_token']").val()
                },
                success: function(data) {
                    $.ajax({
                        type: "GET",
                        url: "/inventory/transfer/challan/close/" + challan_id + "?_token=" + $("input[name='_token']").val(),
                        success: function(data) {
                            $('#closeChallan').modal('hide');
                            alert("Challan closed successfully");
                            window.location.href = window.location.origin + '/inventory/transfer/challan/info/' + challan_id
                        }
                    });
                },
                error: function(xhr) {
                    if (xhr.status == 401) {
                        alert("You are not authorised to close this Challan.");
                    }
                }
            });
        } else {
            alert('Please enter valid password');
        }
    }

    function submitReopenChallanPassword() {
        var challan_id = $('#challan_id').val();
        var password = $.trim($("#ReopenPassword").val());
        if (password.length > 0) {
            $.ajax({
                type: "POST",
                url: "/inventory/transfer/challan/authenticate-challan-close",
                data: {
                    password: password,
                    challan_id: challan_id,
                    _token: $("input[name='_token']").val()
                },
                success: function(data) {
                    $.ajax({
                        type: "GET",
                        url: "/inventory/transfer/challan/reopen/" + challan_id + "?_token=" + $("input[name='_token']").val(),
                        success: function(data) {
                            $('#reopenChallan').modal('hide');
                            alert("Challan reopen successfully");
                            window.location.href = window.location.origin + '/inventory/transfer/challan/edit/' + challan_id
                        }
                    });
                },
                error: function(xhr) {
                    if (xhr.status == 401) {
                        alert("You are not authorised to close this Challan.");
                    }
                }
            });
        } else {
            alert('Please enter valid password');
        }
    }

    function checkQuantity(element, slug) {
        var outTransferId = $(element).closest('tr').find(".out-transfer").attr('id');
        var outQuantity = parseFloat($("#componentRow-" + outTransferId + "-site-out-quantity").val());
        var inQuantity = parseFloat($("#componentRow-" + outTransferId + "-site-in-quantity").val());
        var inventoryComponentId = parseFloat($("#componentRow-" + outTransferId + "-component-id").val());
        var unitId = parseFloat($("#componentRow-" + outTransferId + "-unit-id").val());
        var availableQuantity = 0;
        $.ajax({
            url: '/inventory/transfer/check-quantity',
            type: 'POST',
            async: false,
            data: {
                _token: $("input[name='_token']").val(),
                inventoryComponentId: inventoryComponentId,
                quantity: outQuantity,
                unitId: unitId
            },
            success: function(data, textStatus, xhr) {
                console.log('response');
                availableQuantity = data.available_quantity;
            },
            error: function() {
                alert("Something went wrong");
            }
        });
        $("#componentRow-" + outTransferId + "-site-out-quantity").rules('add', {
            min: inQuantity,
            max: availableQuantity,
            messages: {
                max: "Available quantity is " + availableQuantity,
                min: "Min quantity should be " + inQuantity
            }
        });
        $("#componentRow-" + outTransferId + "-site-in-quantity").rules('add', {
            max: outQuantity,
            min: 0,
            messages: {
                max: "Out Quantity is " + outQuantity,
                min: "Min Quantity should be 0"
            }
        });
    }

    function calculateTransportationTaxes() {
        var transportationAmount = $('.transportation-amount').val();
        if (typeof transportationAmount == 'undefined' || transportationAmount == '' || isNaN(transportationAmount)) {
            transportationAmount = 0;
        }

        var transportationCGSTPercent = $('.transportation-cgst-percentage').val();
        if (typeof transportationCGSTPercent == 'undefined' || transportationCGSTPercent == '' || isNaN(transportationCGSTPercent)) {
            transportationCGSTPercent = 0;
        }

        var transportationSGSTPercent = $('.transportation-sgst-percentage').val();
        if (typeof transportationSGSTPercent == 'undefined' || transportationSGSTPercent == '' || isNaN(transportationSGSTPercent)) {
            transportationSGSTPercent = 0;
        }

        var transportationIGSTPercent = $('.transportation-igst-percentage').val();
        if (typeof transportationIGSTPercent == 'undefined' || transportationIGSTPercent == '' || isNaN(transportationIGSTPercent)) {
            transportationIGSTPercent = 0;
        }

        var transportationTotalAmount = $('.transportation-total').val();
        if (typeof transportationTotalAmount == 'undefined' || transportationTotalAmount == '' || isNaN(transportationTotalAmount)) {
            transportationTotalAmount = 0;
        }

        var cgstAmount = ((parseFloat(transportationCGSTPercent) * parseFloat(transportationAmount)) / 100).toFixed(3);
        var sgstAmount = ((parseFloat(transportationSGSTPercent) * parseFloat(transportationAmount)) / 100).toFixed(3);
        var igstAmount = ((parseFloat(transportationIGSTPercent) * parseFloat(transportationAmount)) / 100).toFixed(3);
        $('.transportation-cgst-amount').val(cgstAmount);
        $('.transportation-sgst-amount').val(sgstAmount);
        $('.transportation-igst-amount').val(igstAmount);
        var transportationTotal = parseFloat(parseFloat(transportationAmount) + parseFloat(cgstAmount) + parseFloat(sgstAmount) + parseFloat(igstAmount)).toFixed(3);
        $('.transportation-total').val(transportationTotal);
    }

    var EditChallan = function() {
        var handleCreate = function() {
            var form = $('#edit-challan');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    transportation_amount: {
                        required: true,
                    }
                },
                messages: {

                },
                invalidHandler: function(event, validator) { //display error alert on form submit
                    success.hide();
                    error.show();
                },

                highlight: function(element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function(element) { // revert the change done by hightlight
                    $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function(label) {
                    label
                        .closest('.form-group').addClass('has-success');
                },

                submitHandler: function(form) {
                    success.show();
                    error.hide();
                    form.submit();
                }
            });
        };
        return {
            init: function() {
                handleCreate();
            }
        };
    }();

    function reopenChallan(element) {
        if ($('#is_bill_generated').val() === 'true') {
            message = "Are you sure you want to reopen this Challan? Bill is already generated for this challan.";
        } else {
            message = "Are you sure you want to reopen this Challan?";
        }
        if (confirm(message)) {
            $('#reopenChallan').modal('show');
        }
    }
</script>
@endsection