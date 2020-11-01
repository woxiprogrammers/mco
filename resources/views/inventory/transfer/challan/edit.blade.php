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
                                <h1>Edit Challan</h1>
                            </div>
                            @if($challan->inventoryComponentTransferStatus->slug == 'open')
                            <div class="form-group " style="text-align: center">
                                <a class="btn red pull-right margin-top-15" data-toggle="modal" href="#closeChallan">
                                    <i class="fa fa-close" style="font-size: large"></i>
                                    Close Challan
                                </a>
                            </div>
                            @elseif($challan->inventoryComponentTransferStatus->slug == 'requested')
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
                            @elseif($challan->inventoryComponentTransferStatus->slug == 'close')
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
                                    <a href="/inventory/transfer/challan/manage">Manage Challan</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">View Challan</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <input type="hidden" id="challan_id" value="{{$challan['id']}}">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label style="color: darkblue;">Challan Number</label>
                                                        <input type="text" class="form-control" name="challan_id" value="{{$challan['challan_number']}}" readonly tabindex="-1">
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
                                                                        <div class="form-body">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <table class="table table-striped table-bordered table-hover order-column" id="purchaseRequest" style="margin-top: 1%;">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th> Material Name </th>
                                                                                                <th> Site Out Quantity</th>
                                                                                                <th> Site In Quantity</th>
                                                                                                <th> Unit </th>
                                                                                                <th> Action</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            @foreach($components as $key => $materialData)
                                                                                            <tr>
                                                                                                <td> {{$materialData['name']}} </td>
                                                                                                <td> {{$materialData['site_out_quantity']}} </td>
                                                                                                <td> {{$materialData['site_in_quantity']}} </td>
                                                                                                <td> {{$materialData['unit']}} </td>
                                                                                                <td><button class="component-view" value="{{$materialData['name']}}">View</button>
                                                                                                </td>
                                                                                            </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <div class="col-md-3" style="text-align: right">
                                                                                    <label class="control-label">Transportation Amount</label>
                                                                                    <span>*</span>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control" id="transportation_amount" name="transportation_amount" value="{{$challan['other_data']['transportation_amount']}}">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <div class="col-md-3" style="text-align: right">
                                                                                    <label class="control-label">Transportation Tax Amount</label>
                                                                                    <span>*</span>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control" id="transportation_tax_total" name="transportation_tax_total" value="{{$challan['other_data']['transportation_tax_total']}}" disabled>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <div class="col-md-3" style="text-align: right">
                                                                                    <label class="control-label">Transportation Total</label>
                                                                                    <span>*</span>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control" id="transportation_total" name="transportation_total" value="{{$challan['other_data']['transportation_total']}}" disabled>
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
                                                                                    <input type="text" class="form-control" id="driver_name" name="driver_name" value="{{$challan['other_data']['driver_name']}}" disabled>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <div class="col-md-3" style="text-align: right">
                                                                                    <label class="control-label">Mobile</label>
                                                                                    <span>*</span>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control" id="mobile" name="mobile" value="{{$challan['other_data']['mobile']}}" disabled>
                                                                                </div>
                                                                            </div>

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
                        <input type="password" id="POPassword" class="form-control" name="password">
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
@endsection
@section('javascript')
<link rel="stylesheet" href="/assets/global/plugins/datatables/datatables.min.css" />
<link rel="stylesheet" href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" />
<link rel="stylesheet" href="/assets/global/css/app.css" />
<script src="/assets/global/plugins/datatables/datatables.min.js"></script>
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

    .carousel-inner .item>img {
        -webkit-animation: zoom 20s;
        animation: zoom 20s;
    }
</style>
<script>
    $(document).ready(function() {

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




        EditPurchaseOrder.init();


        $("#componentSelectButton").on('click', function() {
            if ($(".component-select:checkbox:checked").length > 0) {
                var componentIds = [];
                $(".component-select:checkbox:checked").each(function() {
                    componentIds.push($(this).val());
                });
                $.ajax({
                    url: '/purchase/purchase-order/get-component-details?_token=' + $("input[name='_token']").val(),
                    type: "POST",
                    data: {
                        purchase_order_component_id: componentIds
                    },
                    success: function(data, textStatus, xhr) {
                        GenerateGRN.init();
                        $("#componentDetailsDiv").html(data);
                        $("#componentDetailsDiv").show();
                        $("#transactionCommonFieldDiv").show();
                    },
                    error: function(errorData) {
                        alert('Something went wrong.');
                    }
                })
            }
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

        $("#grnImageUplaodButton a").on('click', function() {
            var imageArray = $("#transactionForm").serializeArray();
            $.ajax({
                url: '/purchase/purchase-order/transaction/upload-pre-grn-images',
                type: 'POST',
                data: imageArray,
                beforeSend: function() {
                    $("#imageupload").hide();
                    $("#grnImageUplaodButton").hide();
                },
                success: function(data, textStatus, xhr) {
                    $("#imageupload").hide();
                    $("#grnImageUplaodButton").hide();
                    $("#purchaseOrderTransactionId").val(data.purchase_order_transaction_id);
                    $("#transactionForm input[name='grn']").val(data.grn);
                    $("#afterImageUploadDiv").show();
                },
                error: function(errorData) {

                }
            });
        });

        $(".transaction-edit-btn").on('click', function() {
            var transactionId = $(this).closest('tr').find('input[type="hidden"]').val();
            $.ajax({
                url: '/purchase/purchase-order/transaction/edit/' + transactionId + "?_token=" + $('input[name="_token"]').val() + "&isShowTax=false",
                type: 'GET',
                success: function(data, textStatus, xhr) {
                    $("#editTransactionModal .modal-body").html(data);
                    $("#editTransactionModal").modal('show');
                },
                error: function(errorStatus) {

                }
            });
        });

        $("#transactionButton").on('click', function() {
            var purchaseOrderId = $("#po_id").val();
            $.ajax({
                url: '/purchase/purchase-order/transaction/check-generated-grn/' + purchaseOrderId + '?_token=' + $("input[name='_token']").val(),
                type: 'GET',
                success: function(data, textStatus, xhr) {
                    console.log(data);
                    if (xhr.status == 200) {
                        $.each(data.images, function(k, v) {
                            var imagePreview = '<div class="col-md-2"><img src="' + v + '" class="thumbimage" /></div>';
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
                error: function(errorData) {

                }
            });
        });
    });

    function submitComponentForm() {
        var minQuantity = $("#ImageUpload .modal-body #minQuantity").val();
        var quantity = $("#ImageUpload .modal-body .quantity").val();
        if ($.isNumeric(quantity) == true) {
            minQuantity = parseFloat(minQuantity);
            quantity = parseFloat(quantity);
            if (minQuantity > quantity) {
                $("#ImageUpload .modal-body .quantity").closest('.form-group').addClass('has-error').removeClass('has-success');
                alert('Minimum allowed quantity is ' + minQuantity);
            } else {
                $("#ImageUpload .modal-body .quantity").closest('.form-group').removeClass('has-error').addClass('has-success');
                $("#PurchaseOrderComponentEditForm").submit();
            }
        } else {
            $("#ImageUpload .modal-body .quantity").closest('.form-group').addClass('has-error').removeClass('has-success');
            alert('Please Enter digit only.');
        }
    }

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
        var password = $.trim($("#POPassword").val());
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
                        type: "POST",
                        url: "/inventory/transfer/challan/close",
                        data: {
                            challan_id: challan_id,
                            _token: $("input[name='_token']").val()
                        },
                        beforeSend: function() {},
                        success: function(data) {
                            location.reload();
                            alert("Challan closed successfully.");
                        }
                    });
                },
                error: function(xhr) {
                    if (xhr.status == 401) {
                        alert("You are not authorised to close this purchase order.");
                    }
                }
            });
        } else {
            alert('Please enter valid password');
        }
    }
</script>
@endsection