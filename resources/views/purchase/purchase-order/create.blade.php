@extends('layout.master')
@section('title','Constro | Manage Materials')
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
                        <form action="/purchase/purchase-order/create" method="POST" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="page-head">
                                <div class="container">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1>Create Purchase Order</h1>
                                    </div>
                                    <div class="form-group " style="text-align: center">
                                        <button type="submit" class="btn red pull-right margin-top-15">
                                            <i class="fa fa-check" style="font-size: large"></i>
                                            Submit
                                        </button>
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
                                                <div class="portlet-body form">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="btn-group " style="width: 100%;">
                                                                <button type="button" class="multiselect dropdown-toggle mt-multiselect btn btn-default" data-toggle="dropdown"  aria-expanded="true" style="width: 100%; overflow: hidden; text-overflow: ellipsis;">
                                                                    <div id="po_id_div">Select Purchase Request</div>
                                                                    <b class="caret"></b>
                                                                    <div id="purchaseRequestID">

                                                                    </div>
                                                                </button>
                                                                <ul class="multiselect-container dropdown-menu" style="height: 1000%;overflow-y: scroll">
                                                                    @foreach($purchaseRequests as $purchaseRequestId =>$purchaseRequestFormat)
                                                                        <li style="height: 10%"><a tabindex="0"><label class="checkbox"><button type="button" onclick="getDetails({{$purchaseRequestId}})">View Detail</button>&nbsp;&nbsp;<b onclick="getData('{{$purchaseRequestId}}','{{$purchaseRequestFormat}}')">{{$purchaseRequestFormat}}</b> </label></a></li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" id="client"readonly value="{{$globalProjectSite->project->client->company}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" value="{{$globalProjectSite->project->name}} - {{$globalProjectSite->name}}" id="project" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">

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
                                                    <div class="table-scrollable" style="overflow: scroll !important;">
                                                        <table class="table table-striped table-bordered table-hover" id="purchaseRequest" style="overflow: scroll; table-layout: fixed">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:15%">Category</th>
                                                                    <th style="width: 15%"> Material Name </th>
                                                                    <th style="width: 10%"> Quantity </th>
                                                                    <th style="width: 10%;"> Unit </th>
                                                                    <th style="width: 12%"> Vendor </th>
                                                                    <th style="width: 10%"> Rate </th>
                                                                    <th style="width: 10%"> HSN code </th>
                                                                    <th style="width: 10%"> Tax </th>
                                                                    <th style="width: 18%"> Expected Delivery Date </th>
                                                                    <th style="width: 22%;"> Vendor quotation images </th>
                                                                    <th style="width: 22%;"> Client Approval images </th>
                                                                    <th style="width: 10%;"> Status </th>
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
                            </div>
                        </form>
                        <div class="modal fade" id="myModal1" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header" style="padding-bottom:10px">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"> Assign Category</div>
                                            <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                        </div>
                                    </div>
                                    <div class="modal-body" style="padding:40px 50px;">
                                        <form id="materialCreateForm" method="post">
                                            <input type="hidden" id="purchaseRequestComponentId">
                                            {!! csrf_field() !!}
                                            <input type="hidden" id="vendor_id" name="vendor_id">
                                            <input type="hidden" id="name" name="name">
                                            <input type="hidden" id="rate_per_unit" name="rate_per_unit">
                                            <input type="hidden" id="unit_id" name="unit_id">
                                            <input type="hidden" id="hsn_code" name="hsn_code">
                                            <br>
                                            <select class="form-control" id="select_material_type" name="category" required>
                                                <option>Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                @endforeach
                                            </select>
                                            <div class="row">
                                                <div class="col-md-3 col-md-offset-4">
                                                    <a class="btn red " id="materialCreateSubmit"> Submit </a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="myModal2" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header" style="padding-bottom:10px">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"> Assign Category</div>
                                            <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                        </div>
                                    </div>
                                    <div class="modal-body" style="padding:40px 50px;">
                                        <form  id="assetCreateForm" method="post">
                                            <input type="hidden" id="purchaseRequestComponentId">
                                            {!! csrf_field() !!}
                                            <div class="row">
                                                <div class="col-md-12 form-group">
                                                    <input type="text" id="assetName" name="name" class="form-control" readonly tabindex="-1">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 form-group">
                                                    <input type="text" id="asset_rate_per_unit" name="rate_per_unit" class="form-control" readonly tabindex="-1">
                                                </div>
                                            </div>
                                            <input class="form-control" type="hidden" id="asset_vendor_id" name="vendor_id">
                                            <input type="hidden" id="asset_unit_id" name="unit_id">
                                            <input type="hidden" id="asset_hsn_code" name="hsn_code">
                                            <br>
                                            <div class="row">
                                                <div class="col-md-3 col-md-offset-4">
                                                    <a class="btn red" id="assetCreateSubmit"> Submit </a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="mymodal4" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header" style="padding-bottom:10px">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4" style="font-size: larger"> Details</div>
                                            <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                        </div>
                                    </div>
                                    <div class="modal-body" >
                                            <div id="detailsId">

                                            </div>
                                            <br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="taxModal" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header" style="padding-bottom:10px">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">Add Taxes</div>
                                            <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                        </div>
                                    </div>
                                    <form id="addTaxForm">
                                        <div class="modal-body" >


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
            $("#materialCreateSubmit").on('click',function(e) {
                e.stopPropagation();
                var purchaseRequestComponentId = $("#materialCreateForm #purchaseRequestComponentId").val();
                var vendorId = $("#materialCreateForm #vendor_id").val();
                var url = "/purchase/purchase-order/create-material"; // the script where you handle the form input.
                $.ajax({
                    type: "POST",
                    url: url,
                    data: $("#materialCreateForm").serialize(), // serializes the form's elements.
                    success: function(data)
                    {
                        var selectedFlag = $("#is_approve_"+purchaseRequestComponentId+"_"+vendorId+" option:selected").val();
                        $("#is_approve_"+purchaseRequestComponentId+"_"+vendorId+" option:not([value='"+selectedFlag+"'])").each(function(){
                            $(this).prop('disabled', true);
                        });
                        $('#myModal1').modal('toggle');
                        alert('New Material Created Successfully');
                    }
                });
            });
            $("#assetCreateSubmit").on('click',function(e) {
                e.stopPropagation();
                var purchaseRequestComponentId = $("#assetCreateForm #purchaseRequestComponentId").val();
                var vendorId = $("#assetCreateForm #asset_vendor_id").val();
                var url = "/purchase/purchase-order/create-asset"; // the script where you handle the form input.
                $.ajax({
                    type: "POST",
                    url: url,
                    data: $("#assetCreateForm").serialize(), // serializes the form's elements.
                    success: function(data)
                    {
                        var selectedFlag = $("#is_approve_"+purchaseRequestComponentId+"_"+vendorId+" option:selected").val();
                        $("#is_approve_"+purchaseRequestComponentId+"_"+vendorId+" option:not([value='"+selectedFlag+"'])").each(function(){
                            $(this).prop('disabled', true);
                        });
                        $('#myModal2').modal('toggle');
                        alert('New Asset Created Successfully');
                    }
                });
            });
        });
        function getDetails(purchaseRequestId){
            var data = '<input type="hidden" name="purchase_request_id" value="'+purchaseRequestId+'">';
            $('#purchaseRequestID').html(data);
            $.ajax({
                url: '/purchase/purchase-order/get-purchase-order-details/' + purchaseRequestId + '?_token=' + $('input[name="_token"]').val(),
                type: 'GET',
                async: true,
                success: function (data, textStatus, xhr) {
                    if (xhr.status == 203) {
                        alert(data.message);
                    } else {
                        $('#mymodal4').modal();
                        $("#detailsId").html(data);
                    }

                },
                error: function (errorData) {
                    alert('Something went wrong');
                }
            });
        };
        function getData (purchaseRequestId,po_id_div) {
            $('#po_id_div').html(po_id_div);
            if (typeof purchaseRequestId == 'undefined' || purchaseRequestId == '') {
                $('#client').val('');
                $('#project').val('');
                $('#purchaseRequest').hide();
            } else {
                $.ajax({
                    url: '/purchase/purchase-order/get-client-project/' + purchaseRequestId + '?_token=' + $('input[name="_token"]').val(),
                    type: 'GET',
                    success: function (data, textStatus, xhr) {
                        $('#client').val(data.client);
                        $('#project').val(data.project);
                    },
                    error: function (errorData) {
                    }
                });
                $.ajax({
                    url: '/purchase/purchase-order/get-purchase-request-component/' + purchaseRequestId + '?_token=' + $('input[name="_token"]').val(),
                    type: 'GET',
                    async: true,
                    success: function (data, textStatus, xhr) {
                        if (xhr.status == 203) {
                            alert(data.message);
                        } else {
                            $("#purchaseRequest tbody").html(data);
                            $('#purchaseRequest').show();
                        }

                    },
                    error: function (errorData) {
                        alert('Something went wrong');
                    }
                });
            }
        };
        function addTax(element){
            var purchaseRequestComponentId = $(element).closest('tr').find('.component-id').val();
            var vendorId = $(element).closest('tr').find('.component-vendor').val();
            var rate = $(element).closest('tr').find('.component-rate').val()
            var quantity = $(element).closest('tr').find('.component-quantity').val();
            var cgstPercentage = $(element).closest('tr').find('.tax-modal-cgst-percentage').val();
            var sgstPercentage = $(element).closest('tr').find('.tax-modal-sgst-percentage').val();
            var igstPercentage = $(element).closest('tr').find('.tax-modal-igst-percentage').val();
            $.ajax({
                url: '/purchase/purchase-order/get-tax-details/'+purchaseRequestComponentId+'?_token='+$("input[name='_token']").val(),
                type: 'POST',
                data:{
                    _token : $('input[name="_token"]').val(),
                    vendor_id: vendorId,
                    rate: rate,
                    quantity: quantity,
                    cgst_percentage : cgstPercentage,
                    sgst_percentage : sgstPercentage,
                    igst_percentage : igstPercentage,
                },
                success: function(data,textStatus, xhr){
                    $("#taxModal .modal-body").html(data);
                    $("#taxModal").modal('show');
                },
                error: function(errorData){

                }
            });
        }
        function submitTaxForm(purchaseRequestComponentId){
            var row = $(".row-component-"+purchaseRequestComponentId);
            var formData = $("#addTaxForm").serializeArray();
            var rate = $("#addTaxForm .tax-modal-rate").val();
            var quantity = $("#addTaxForm .tax-modal-quantity").val();
            row.find('.component-rate').val(rate);
            row.find('.component-quantity').val(quantity);
            $.each(formData, function(key, value){
                var className = $("input[name='"+ value.name +"']").attr('class');
                var inputData = '<input type="hidden" name="'+value.name+'" class="'+className+'" value="'+value.value+'">'
                row.append(inputData);
            });
            $("#taxModal").modal('toggle');
        }

        function calculateTaxes(element){
            var rate = parseFloat($(element).closest('.modal-body').find('.tax-modal-rate').val());
            if(typeof rate == 'undefined' || rate == '' || isNaN(rate)){
                rate = 0;
            }
            var quantity = parseFloat($(element).closest('.modal-body').find('.tax-modal-quantity').val());
            if(typeof quantity == 'undefined' || quantity == '' || isNaN(quantity)){
                quantity = 0;
            }
            var subtotal = rate * quantity;
            var cgstPercentage = parseFloat($(element).closest('.modal-body').find('.tax-modal-cgst-percentage').val());
            if(typeof cgstPercentage == 'undefined' || cgstPercentage == '' || isNaN(cgstPercentage)){
                cgstPercentage = 0;
            }
            var sgstPercentage = parseFloat($(element).closest('.modal-body').find('.tax-modal-sgst-percentage').val());
            if(typeof sgstPercentage == 'undefined' || sgstPercentage == '' || isNaN(sgstPercentage)){
                sgstPercentage = 0;
            }
            var igstPercentage = parseFloat($(element).closest('.modal-body').find('.tax-modal-igst-percentage').val());
            if(typeof igstPercentage == 'undefined' || igstPercentage == '' || isNaN(igstPercentage)){
                igstPercentage = 0;
            }
            var cgstAmount = subtotal * (cgstPercentage / 100);
            var sgstAmount = subtotal * (sgstPercentage / 100);
            var igstAmount = subtotal * (igstPercentage / 100);
            $(element).closest('.modal-body').find('.tax-modal-cgst-amount').val(cgstAmount);
            $(element).closest('.modal-body').find('.tax-modal-sgst-amount').val(sgstAmount);
            $(element).closest('.modal-body').find('.tax-modal-igst-amount').val(igstAmount);
            var total = subtotal + cgstAmount + sgstAmount + igstAmount;
            $(element).closest('.modal-body').find('.tax-modal-total').val(total);
        }
    </script>
@endsection
