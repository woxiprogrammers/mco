@extends('layout.master')
@section('title','Constro | Create Purchase Order Bill')
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
                        <form action="/purchase/purchase-order-bill/create" method="POST" role="form" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="page-head">
                                <div class="container">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1>Create Purchase Order Bill</h1>
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
                                                <div class="portlet-body">
                                                    <div class="form-group">
                                                        <label class="control-label" style="font-size: 18px"> Select GRN for Creating Bill </label>
                                                        <div class="form-control product-material-select" style="font-size: 14px; margin-left: 1%; height: 200px !important;" >
                                                            <ul id="material_id" class="list-group">
                                                                @foreach($purchaseOrderTransactionDetails as $key => $purchaseOrderTransaction)
                                                                    <li><input type="checkbox" class="transaction-select" value="{{$purchaseOrderTransaction['id']}}"><label class="control-label" style="margin-left: 0.5%;"> {{$purchaseOrderTransaction['grn']}} </label></li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-3 col-md-offset-3" style="margin-top: 1%">
                                                                <a class="pull-right btn blue" href="javascript:void(0);" id="componentSelectButton"> Select </a>
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
    </script>
@endsection
