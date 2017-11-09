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
                                                            <div class="form-group">
                                                                <select class="bs-select form-control" id="purchaseRequestId" name="purchase_request_id">
                                                                    <option value="">--Select Purchase Request--</option>
                                                                    @foreach($purchaseRequests as $purchaseRequestId =>$purchaseRequestFormat)
                                                                        <option value="{{$purchaseRequestId}}">{{$purchaseRequestFormat}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" value="Client Name" id="client"readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" value="Project-Project Site" id="project" readonly>
                                                                </div>
                                                            </div>
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
                                                        <div class="table-scrollable profit-margin-table">
                                                            <table class="table table-striped table-bordered table-hover" id="purchaseRequest" style="overflow: scroll">
                                                                <thead>
                                                                    <tr>
                                                                    <th></th>
                                                                    <th style="width: 15%"> Material Name </th>
                                                                    <th style="width: 8%"> Quantity </th>
                                                                    <th style="width: 8%;"> Unit </th>
                                                                    <th style="width: 12%"> Vendor </th>
                                                                    <th style="width: 10%"> Rate </th>
                                                                    <th style="width: 10%"> HSN code </th>
                                                                    <th style="width: 8%;"> Vendor quotation images </th>
                                                                    <th style="width: 8%;"> Client Approval images </th>
                                                                    <th style="width: 8%;"> Status </th>
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
            $("#purchaseRequestId").on('change',function(){
                var purchaseRequestId = $(this).val();
                if(typeof purchaseRequestId == 'undefined' || purchaseRequestId == ''){
                    $('#client').val('');
                    $('#project').val('');
                    $('#purchaseRequest').hide();
                }else{
                    $.ajax({
                        url:'/purchase/purchase-order/get-client-project/'+purchaseRequestId+'?_token='+$('input[name="_token"]').val(),
                        type: 'GET',
                        success: function(data,textStatus,xhr){
                            $('#client').val(data.client);
                            $('#project').val(data.project);
                        },
                        error: function(errorData){
                        }
                    });
                    $.ajax({
                        url: '/purchase/purchase-order/get-purchase-request-component/'+purchaseRequestId+'?_token='+$('input[name="_token"]').val(),
                        type: 'GET',
                        async: true,
                        success: function(data,textStatus,xhr){
                            if(xhr.status == 203){
                                alert(data.message);
                            }else{
                                $("#purchaseRequest tbody").html(data);
                                $('#purchaseRequest').show();
                            }

                        },
                        error: function(errorData){
                            alert('Something went wrong');
                        }
                    });
                }
            });
        });

    </script>
    <script>
        $("#materialCreateSubmit").on('click',function(e) {
            e.stopPropagation();
            var purchaseRequestComponentId = $("#materialCreateForm #purchaseRequestComponentId").val();
            var url = "/purchase/purchase-order/create-material"; // the script where you handle the form input.
            $.ajax({
                type: "POST",
                url: url,
                data: $("#materialCreateForm").serialize(), // serializes the form's elements.
                success: function(data)
                {
                    //alert(data); // show response from the php script.
                    var selectedFlag = $("#is_approve_"+purchaseRequestComponentId+" option:selected").val();
                    $("#is_approve_"+purchaseRequestComponentId+" option:not([value='"+selectedFlag+"'])").each(function(){
                       $(this).prop('disabled', true);
                    });
                }
            });
        });
        $("#assetCreateSubmit").on('click',function(e) {
            e.stopPropagation();
            var purchaseRequestComponentId = $("#assetCreateForm #purchaseRequestComponentId").val();
            var url = "/purchase/purchase-order/create-asset"; // the script where you handle the form input.
            $.ajax({
                type: "POST",
                url: url,
                data: $("#assetCreateForm").serialize(), // serializes the form's elements.
                success: function(data)
                {
//                    alert(data); // show response from the php script.
                    var selectedFlag = $("#is_approve_"+purchaseRequestComponentId+" option:selected").val();
                    $("#is_approve_"+purchaseRequestComponentId+" option:not([value='"+selectedFlag+"'])").each(function(){
                        $(this).prop('disabled', true);
                    });
                }
            });
        });
    </script>
@endsection
