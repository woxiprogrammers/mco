@extends('layout.master')
@section('title','Constro | Create Purchase Order Bill')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <style>
        .thumbimage {
            float:left;
            width:100%;
            height: 200px;
            position:relative;
            padding:5px;
        }
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
                        <form action="/purchase/purchase-order-bill/create" method="POST" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="page-head">
                                <div class="container">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1>Create Purchase Order Bill</h1>
                                    </div>

                                </div>
                            </div>
                            <div class="page-content">
                                @include('partials.common.messages')
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- BEGIN VALIDATION STATES-->
                                            <div class="portlet light ">
                                                <div class="portlet-body">
                                                    <fieldset>
                                                        <legend>Project</legend>
                                                        <div class="row">
                                                            <div class="col-md-3 col-md-offset-0">
                                                                Client Name
                                                            </div>
                                                            <div class="col-md-3">
                                                                Project Name
                                                            </div>
                                                            <div class="col-md-3">
                                                                Project Site Name
                                                            </div>
                                                            <div class="col-md-3">
                                                                Purchase Order
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3 form-group">
                                                                <select class="form-control" id="clientId" style="width: 80%;">
                                                                    <option value=""> -- Select Client -- </option>
                                                                    @foreach($clients as $client)
                                                                        <option value="{{$client['id']}}"> {{$client['company']}} </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <select id="projectId" class="form-control" style="width: 80%;">
                                                                    <option value=""> -- Select Project -- </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <select id="projectSiteId" class="form-control" style="width: 80%;">
                                                                    <option value=""> -- Select Project site -- </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <select name="purchase_order_id" id="purchaseOrderId" class="form-control" style="width: 80%;">
                                                                    <option value=""> -- Select Purchase Order -- </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="form-group" id="grnSelectionDiv" hidden>
                                                        <label class="control-label" style="font-size: 18px"> Select GRN for Creating Bill </label>
                                                        <div class="form-control product-material-select" style="font-size: 14px; margin-left: 1%; height: 200px !important;" >
                                                            <ul class="list-group">

                                                            </ul>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3 col-md-offset-3" style="margin-top: 1%">
                                                                <a class="pull-right btn blue" href="javascript:void(0);" id="transactionSelectButton"> Select </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="billData" hidden>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Sub-Total</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control calculate-amount" name="sub_total" id="subTotal" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">CGST</label>
                                                            </div>
                                                            <div class="col-md-3" id="inputGroup">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control tax" id="cgstPercentage" name="cgst_percentage">
                                                                    <span class="input-group-addon" style="font-size: 18px">&nbsp;&nbsp; % &nbsp; &nbsp;</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control calculate-amount" placeholder="CGST Amount" name="cgst_amount">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">SGST</label>
                                                            </div>
                                                            <div class="col-md-3" id="inputGroup">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control tax" id="cgstPercentage" name="sgst_percentage">
                                                                    <span class="input-group-addon" style="font-size: 18px">&nbsp;&nbsp; % &nbsp; &nbsp;</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control calculate-amount" placeholder="SGST Amount" name="sgst_amount">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">IGST</label>
                                                            </div>
                                                            <div class="col-md-3" id="inputGroup">
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control tax" id="cgstPercentage" name="igst_percentage">
                                                                    <span class="input-group-addon" style="font-size: 18px">&nbsp;&nbsp; % &nbsp; &nbsp;</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control calculate-amount" name="igst_amount" placeholder="IGST Amount">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Extra Amount</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control calculate-amount" name="extra_amount">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Total Amount</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="totalAmount" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Confirm Total Amount</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="ConfirmTotalAmount" name="amount">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label">Select Images :</label>
                                                            <input id="imageupload" type="file" class="btn blue" multiple />
                                                            <br />
                                                            <div class="row">
                                                                <div id="preview-image" class="row">

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3">
                                                                <button type="submit" class="btn red pull-right">
                                                                    <i class="fa fa-check" style="font-size: large"></i>
                                                                    Submit
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
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
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $("#clientId").on('change', function(){
                var clientId = $(this).val();
                if(clientId == ""){
                    $('#projectId').prop('disabled', false);
                    $('#projectId').html('');
                    $('#projectSiteId').prop('disabled', false);
                    $('#projectSiteId').html('');
                }else{
                    $.ajax({
                        url: '/quotation/get-projects',
                        type: 'POST',
                        async: true,
                        data: {
                            _token: $("input[name='_token']").val(),
                            client_id: clientId
                        },
                        success: function(data,textStatus,xhr){
                            $('#projectId').html(data);
                            $('#projectId').prop('disabled', false);
                            var projectId = $("#projectId").val();
                            getProjectSites(projectId);
                        },
                        error: function(){

                        }
                    });
                }

            });

            $("#projectId").on('change', function(){
                var projectId = $(this).val();
                getProjectSites(projectId);
            });

            $("#transactionSelectButton").on('click', function(event){
                event.stopPropagation();
                if($(".transaction-select:checkbox:checked").length > 0 ){
                    var transaction_id = [];
                    $(".transaction-select:checkbox:checked").each(function(){
                        transaction_id.push($(this).val());
                    });
                    $.ajax({
                       url: '/purchase/purchase-order-bill/get-transaction-subtotal',
                       type: "POST",
                       data:{
                           _token: $("input[name='_token']").val(),
                           transaction_id: transaction_id
                       },
                        success: function(data,textStatus, xhr){
                            $("#subTotal").val(data.sub_total);
                            $("#billData").show();
                        },
                        error: function(errorData){

                        }
                    });
                }
            });

            $("#projectSiteId").on('change', function(){
                var projectSiteId = $(this).val();
                $.ajax({
                    url: '/purchase/purchase-order-bill/get-purchase-orders',
                    type: "POST",
                    data : {
                        _token: $('input[name="_token"]').val(),
                        project_site_id: projectSiteId
                    },
                    success: function(data,textStatus, xhr){
                        $("#purchaseOrderId").html(data);
                        $("#purchaseOrderId").trigger('change');
                    },
                    error: function(errorStatus){

                    }
                });
            });

            $("#purchaseOrderId").on('change',function(){
                var purchaseOrderId = $("#purchaseOrderId").val();
                $.ajax({
                    url:'/purchase/purchase-order-bill/get-bill-pending-transactions',
                    type: "POST",
                    data:{
                        _token: $("input[name='_token']").val(),
                        purchase_order_id: purchaseOrderId
                    },
                    success: function(data,textStatus, xhr){
                        if(xhr.status == 200){
                            $("#grnSelectionDiv ul").html(data);
                            $("#grnSelectionDiv").show();
                        }else{
                            $("#grnSelectionDiv ul").html('');
                            $("#grnSelectionDiv").hide();
                            $("#billData").hide();
                        }

                    },
                    error: function(errorData){

                    }
                });
            });

            $(".tax").on('keyup',function(){
               var subtotal = $("#subTotal").val();
               var percentage = $(this).val();
               var amount = subtotal * (percentage / 100);
               $(this).closest('#inputGroup').next().find("input[type='text']").val(amount);
                calculateTotal();
            });
            $(".calculate-amount").on('keyup',function(){
                calculateTotal();
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
                                var imagePreview = '<div class="col-md-2"><input type="hidden" name="bill_images[]" value="'+e.target.result+'"><img src="'+e.target.result+'" class="thumbimage" /></div>';
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
        function calculateTotal(){
            var total = 0;
            $(".calculate-amount").each(function(){
                var amount = $(this).val();
                if(typeof amount != 'undefined' && amount != '' && amount != null){
                    total += parseFloat($(this).val());
                }
            });
            $("#totalAmount").val(total);
        }
        function getProjectSites(projectId){
            $.ajax({
                url: '/purchase/purchase-order-bill/get-project-sites',
                type: 'POST',
                async: true,
                data: {
                    _token: $("input[name='_token']").val(),
                    project_id: projectId
                },
                success: function(data,textStatus,xhr){
                    if(data.length > 0){
                        $('#projectSiteId').html(data);
                        $('#projectSiteId').prop('disabled', false);
                        $("#projectSiteId").trigger('change');
                    }else{
                        $('#projectSiteId').html("");
                        $('#projectSiteId').prop('disabled', false);
                    }
                },
                error: function(){

                }
            });
        }

    </script>
@endsection
