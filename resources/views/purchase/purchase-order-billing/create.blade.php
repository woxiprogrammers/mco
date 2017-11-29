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
                                                                <select class="form-control" id="clientId" name="client_id" style="width: 80%;">
                                                                    <option value=""> -- Select Client -- </option>
                                                                    @foreach($clients as $client)
                                                                        <option value="{{$client['id']}}"> {{$client['company']}} </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <select name="project_id" id="projectId" class="form-control" style="width: 80%;">
                                                                    <option value=""> -- Select Project -- </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <select name="project_site_id" id="projectSiteId" class="form-control" style="width: 80%;">
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

        });
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
                    }else{
                        $('#projectSiteId').html("");
                        $('#projectSiteId').prop('disabled', false);
                    }
                },
                error: function(){

                }
            });

            $("#projectSiteId").on('change', function(){
                var projectSiteId = $(this).val();
                $.ajax({
                    url: '/purchase/purchase-order-bill/get-bill-pending-transaction',
                    type: "POST",
                    data : {
                        _token: $('input[name="_token"]').val(),
                        project_site_id: projectSiteId
                    },
                    success: function(data,textStatus, xhr){
                        $("#purchaseOrderId").html(data);
//                        $("#grnSelectionDiv").show();
                    },
                    error: function(errorStatus){

                    }
                });
            });

            $("#purchaseOrderId").on('change',function(){

            });
        }

    </script>
@endsection
