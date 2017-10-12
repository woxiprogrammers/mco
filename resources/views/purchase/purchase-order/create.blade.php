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
                        <div class="page-head">
                            <div class="container">
                                <!-- BEGIN PAGE TITLE -->
                                <div class="page-title">
                                    <h1>Create Purchase Order</h1>
                                </div>
                                <div class="form-group " style="text-align: center">
                                    <a href="#" class="btn red pull-right margin-top-15">
                                        <i class="fa fa-check" style="font-size: large"></i>
                                        Submit
                                    </a>
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
                                                {!! csrf_field() !!}
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
                                                            <input type="text" class="form-control" value="Client Name" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" value="Project-Project Site" readonly>
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
                                                    <table class="table table-striped table-bordered table-hover order-column" id="purchaseRequest">
                                                        <thead>
                                                        <tr>
                                                            <th> Material Name </th>
                                                            <th style="width: 10%"> Quantity </th>
                                                            <th style="width: 8%;"> Unit </th>
                                                            <th style="width: 12%"> Vendor </th>
                                                            <th> Rate </th>
                                                            <th> HSN code </th>
                                                            <th style="width: 10%;"> Vendor quotation images </th>
                                                            <th style="width: 10%;"> Client Approval images </th>
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
            /*$("#image").click(function(){
                $("#ImageUpload").modal();
            })*/
            $("#purchaseRequestId").on('click',function(){
                var purchaseRequestId = $(this).val();
                if(typeof purchaseRequestId == 'undefined' || purchaseRequestId == ''){
                    $('#client').val('');
                    $('#project').val('');
                    $('#purchaseRequest').hide();
                }else{
                    $.ajax({
                        url: '/purchase/purchase-order/get-purchase-request-component/'+purchaseRequestId+'?_token='+$('input[name="_token"]').val(),
                        type: 'GET',
                        async: true,
                        success: function(data,textStatus,xhr){
                            $("#purchaseRequest tbody").html(data);
                            $('#purchaseRequest').show();
                        },
                        error: function(errorData){
                            alert('Something went wrong');
                        }
                    });
                }
            });
        });
    </script>
@endsection
