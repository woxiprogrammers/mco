@extends('layout.master')
@section('title','Constro | Manage Materials')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/clockface/css/clockface.css" rel="stylesheet" type="text/css" />
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
                                    <h1>{!!  $inventoryComponent->name !!} </h1>
                                    <h5 style="font-size: 15px !important;">{!!  $inventoryComponent->projectSite->project->name.' - '.$inventoryComponent->projectSite->name.' ('.$inventoryComponent->projectSite->project->client->company!!} )</h5>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <div class="row">
                                    <input type="hidden" name="inventory_component_id" id="inventoryComponentId" value="{{$inventoryComponent['id']}}">
                                    <input type="hidden" id="path" name="path" value="">
                                    <input type="hidden" id="max_files_count" name="max_files_count" value="20">
                                    <input type="hidden" id="inTransferTypes" value="{{$inTransferTypes}}">
                                    <input type="hidden" id="outTransferTypes" value="{{$outTransferTypes}}">
                                    <input type="hidden" name="opening_stock" id="openingStock" value="{{$inventoryComponent->opening_stock}}">
                                    <div class="col-md-12">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="pull-right">
                                                    <div class="form-group " style="text-align: center">
                                                        <a href="javascript:void(0);" class="btn yellow" id="stockButton" >
                                                            Opening Stock
                                                        </a>
                                                        <a href="javascript:void(0);" class="btn yellow" style="margin: 20px" id="transaction">
                                                            <i class="fa fa-plus" style="font-size: large"></i>&nbsp;
                                                            Transaction
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="portlet light ">
                                                    <div class="portlet-body form">
                                                        <div class="portlet light ">
                                                            <div class="portlet-body">
                                                                <div class="table-scrollable">
                                                                    <table class="table table-striped table-bordered table-hover order-column" id="inventoryComponentListingTable">
                                                                        <thead>
                                                                        <tr>
                                                                            <th> GRN </th>
                                                                            <th> Quantity </th>
                                                                            <th> Unit </th>
                                                                            <th> Status </th>
                                                                            <th> Action </th>
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
                                <div class="modal fade" id="transactionModal" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header" style="padding-bottom:10px">
                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="col-md-4"> Transaction</div>
                                                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                </div>
                                            </div>
                                            <div class="modal-body" style="padding:40px 50px;">
                                                <form role="form" action="/inventory/component/add-transfer/{{$inventoryComponent['id']}}" method="POST">
                                                    {!! csrf_field() !!}
                                                    <div class="form-group">
                                                        <div class="bootstrap-switch-container" style="height: 30px;width: 200px; margin-left: 0px;">
                                                            <span class="bootstrap-switch-handle-on bootstrap-switch-primary" style="width: 88px;">&nbsp;&nbsp;&nbsp;</span>
                                                            <span class="bootstrap-switch-label" style="width: 88px;">&nbsp;</span>
                                                            <span class="bootstrap-switch-handle-off bootstrap-switch-default" style="width: 88px;">&nbsp;&nbsp;</span>
                                                            <input type="checkbox" class="make-switch" id="inOutCheckbox" name="in_or_out" data-on-text="&nbsp;In&nbsp;&nbsp;" data-off-text="&nbsp;Out&nbsp;">
                                                        </div>
                                                    </div><br>
                                                    <div class="form-group">
                                                        <select class="form-control" id="transfer_type" name="transfer_type">

                                                        </select>
                                                    </div>
                                                    <div id="dynamicForm">

                                                    </div>
                                                    <button type="submit" class="btn red pull-right" id="inOutSubmit" hidden> Create</button>
                                                </form>
                                                <div id="client_form" hidden>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="sourceName" name="source_name" placeholder="Enter client name">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity">
                                                    </div>
                                                    <div class="form-group">
                                                        <select class="form-control" id="unit" name="unit_id">
                                                            <option value=""> -- Unit -- </option>
                                                            @foreach($units as $unit)
                                                                <option value="{{$unit->id}}">{{$unit->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="date" class="form-control" id="date" name="date" placeholder="Enter date">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="remark" name="remark" placeholder="Enter Remark">
                                                    </div>
                                                </div>
                                                <div id="hand_form" hidden>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="sourceName" name="source_name" placeholder="Enter Shop Name">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity">
                                                    </div>
                                                    <div class="form-group">
                                                        <select class="form-control" id="unit" name="unit_id">
                                                            <option value=""> -- Unit -- </option>
                                                            @foreach($units as $unit)
                                                                <option value="{{$unit->id}}">{{$unit->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="date" class="form-control" id="date" name="date" placeholder="Enter date">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="remark" name="remark" placeholder="Enter Remark">
                                                    </div>
                                                </div>
                                                <div id="office_form" hidden>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity">
                                                    </div>
                                                    <div class="form-group">
                                                        <select class="form-control" id="unit" name="unit_id">
                                                            <option value=""> -- Unit -- </option>
                                                            @foreach($units as $unit)
                                                                <option value="{{$unit->id}}">{{$unit->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="date" class="form-control" id="date" name="date" placeholder="Enter date">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="remark" name="remark" placeholder="Enter Remark">
                                                    </div>
                                                </div>
                                                <div id="supplier_form" hidden>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="sourceName" name="source_name" placeholder="Enter Supplier Name">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity">
                                                    </div>
                                                    <div class="form-group">
                                                        <select class="form-control" id="unit" name="unit_id">
                                                            <option value=""> -- Unit -- </option>
                                                            @foreach($units as $unit)
                                                                <option value="{{$unit->id}}">{{$unit->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control" id="billNumber" name="bill_number" placeholder="Enter Bill Number">
                                                    </div>

                                                    <div class="form-group">
                                                        <input type="date" class="form-control" id="date" name="date" placeholder="Enter date">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="vehicleNumber" name="vehicle_number" placeholder="Enter Vehicle Number">
                                                    </div>
                                                    <div class="in-out-time-div">

                                                    </div>
                                                    <div class="form-group">
                                                        <div class="input-group date form_datetime form_datetime bs-datetime">
                                                            <input type="text" size="16" class="form-control" name="in_time" placeholder="Enter In Time">
                                                            <span class="input-group-addon">
                                                                <button class="btn default date-set" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="input-group date form_datetime form_datetime bs-datetime">
                                                            <input type="text" size="16" class="form-control" name="out_time" placeholder="Enter Out Time">
                                                            <span class="input-group-addon">
                                                                <button class="btn default date-set" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="remark" name="remark" placeholder="Enter Remark">
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-12 col-sm-12 custom-file-list"> </div>
                                                        </div>
                                                        <div class="col-md-offset-5 custom-file-container">
                                                            <a href="javascript:;" class="btn green-meadow custom-file-browse">
                                                                Browse</a>
                                                            <a href="javascript:;" class="btn btn-primary custom-upload-file">
                                                                <i class="fa fa-share"></i> Upload Files </a>
                                                        </div>
                                                        <table class="table table-bordered table-hover">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th> Image </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="show-product-images">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div id="labour_form" hidden>
                                                    <div class="form-group">
                                                        <input type="text" name="source_name" id="sourceName" class="form-control" placeholder="Enter Labour's Name">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" name="quantity" id="quantity" class="form-control" placeholder="Enter Quantity">
                                                    </div>
                                                    <div class="form-group">
                                                        <select name="unit_id" class="form-control" id="unit">
                                                            <option value="">--Select Unit--</option>
                                                            @foreach($units as $unit)
                                                                <option value="{{$unit->id}}">{{$unit->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" name="date" class="form-control" id="date" placeholder="Enter Date">
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea name="remark" class="form-control" id="remark" placeholder="Remark"></textarea>
                                                    </div>
                                                </div>
                                                <div id="subcontractor_form" hidden>
                                                    <div class="form-group">
                                                        <input type="text" name="source_name" id="sourceName" class="form-control" placeholder="Enter sub-contractor's Name">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" name="quantity" class="form-control" id="quantity" placeholder="Enter Quantity">
                                                    </div>
                                                    <div class="form-group">
                                                        <select name="unit_id" class="form-control" id="unit">
                                                            <option value="">--Select Unit--</option>
                                                            @foreach($units as $unit)
                                                                <option value="{{$unit->id}}">{{$unit->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" name="date" class="form-control" id="date" placeholder="Enter Date">
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea name="remark" class="form-control" id="remark" placeholder="Remark"></textarea>
                                                    </div>
                                                </div>
                                                <div id="maintenance_form" hidden>
                                                    <div class="form-group">
                                                        <label class="control-label"> Client: Client Name</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label"> Project: Project Name</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label"> Project: Project Site Name</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea class="form-control" placeholder="Remark" name="remark" id="remark"></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-12 col-sm-12 custom-file-list"> </div>
                                                        </div>
                                                        <div class="col-md-offset-5 custom-file-container">
                                                            <a href="javascript:;" class="btn green-meadow custom-file-browse">
                                                                Browse</a>
                                                            <a href="javascript:;" class="btn btn-primary custom-upload-file">
                                                                <i class="fa fa-share"></i> Upload Files </a>
                                                        </div>
                                                        <table class="table table-bordered table-hover">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th> Image </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="show-product-images">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div id="site_form" hidden>
                                                    <div class="form-group">
                                                        <select class="form-control clientSelect" onchange="clientChange(this)">
                                                            <option value="">--Select Client Name--</option>
                                                            @foreach($clients as $client)
                                                                <option value="{{$client['id']}}">{{$client['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <select class="form-control projectSelect" onchange="projectChange(this)">
                                                            <option value="">--Select Project Name--</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <select name="project_site_id" class="form-control projectSiteSelect">
                                                            <option value="">--Select Project Site Name--</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" name="quantity" class="form-control" placeholder="Enter Quantity">
                                                    </div>
                                                    <div class="form-group">
                                                        <select class="form-control" id="unit" name="unit_id">
                                                            <option value=""> -- Unit -- </option>
                                                            @foreach($units as $unit)
                                                                <option value="{{$unit->id}}">{{$unit->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="date" name="date" id="date" class="form-control" placeholder="Enter Date">
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea name="remark" class="form-control" id="remark" placeholder="Remark..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="openingStockModel" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header" style="padding-bottom:10px">
                                                <div class="row">
                                                    <div class="col-md-7 col-md-offset-2"> Opening Stock </div>
                                                    <div class="col-md-3"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                </div>
                                            </div>
                                            <div class="modal-body" style="padding:40px 50px;">
                                                <div class="form-group">
                                                    @if(!isset($inventoryComponent->opening_stock) || $inventoryComponent->opening_stock == '')
                                                        <input type="text" class="form-control" name="opening_stock" id="openingStockInput" placeholder="Enter Opening stock">
                                                    @else
                                                        <input type="text" class="form-control" name="opening_stock" id="openingStockInput" value="{{$inventoryComponent->opening_stock}}">
                                                    @endif
                                                </div>
                                                <div class="col-md-3 col-md-offset-4">
                                                    <a href="javascript:void(0);" id="openingStockSubmit" class="btn red">
                                                        Submit
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="transferDetailModel" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header" style="padding-bottom:10px">
                                                <div class="row">
                                                    <div class="col-md-7 col-md-offset-2"> Details </div>
                                                    <div class="col-md-3"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                </div>
                                            </div>
                                            <div class="modal-body" style="padding:40px 50px; font-size: 15px">

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
    <script src="/assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
    <script src="/assets/custom/inventory/component-manage-datatable.js" type="text/javascript"></script>
    <script src="/assets/custom/inventory/image-datatable.js" type="text/javascript"></script>
    <script src="/assets/custom/inventory/image-upload.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            InventoryComponentListing.init();
            changeType();
            $("#transaction").click(function(){
                $("#transactionModal").modal();
            });
            $("#stockButton").click(function(){
               $("#openingStockModel").modal('show');
            });
            $("#openingStockSubmit").click(function(){
                $.ajax({
                    url: '/inventory/component/edit-opening-stock',
                    type: 'POST',
                    async: true,
                    data: {
                        _token: $("input[name='_token']").val(),
                        inventory_component_id: $("#inventoryComponentId").val(),
                        opening_stock: $("#openingStockInput").val()
                    },
                    success: function(data,textStatus,xhr){
                        $("#openingStock").val($("#openingStockInput").val());
                        alert('Opening stock saved Successfully !!');
                    },
                    error: function(errorData){
                        alert('Something went wrong');
                    }
                });
            });

            $('#inOutCheckbox').on('switchChange.bootstrapSwitch', function(event, state) {
                changeType();
            });


        });
        function clientChange(element){
            var clientId = $(element).val();
            if(clientId == ""){
                $('#dynamicForm .projectSelect').prop('disabled', false);
                $('#dynamicForm .projectSelect').html('');
                $('#dynamicForm .projectSiteSelect').prop('disabled', false);
                $('#dynamicForm .projectSiteSelect').html('');
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
                        $('#dynamicForm .projectSelect').html(data);
                        $('#dynamicForm .projectSelect').prop('disabled', false);
                        var projectId = $("#dynamicForm .projectId").val();
                        getProjectSites(projectId);
                    },
                    error: function(){

                    }
                });
            }

        };

        function projectChange(element){
            var projectId = $(element).val();
            getProjectSites(projectId);
        };
        function getProjectSites(projectId){
            $.ajax({
                url: '/inventory/get-project-sites',
                type: 'POST',
                async: true,
                data: {
                    _token: $("input[name='_token']").val(),
                    project_id: projectId
                },
                success: function(data,textStatus,xhr){
                    if(data.length > 0){
                        $('#dynamicForm .projectSiteSelect').html(data);
                        $('#dynamicForm .projectSiteSelect').prop('disabled', false);
                    }else{
                        $('#dynamicForm .projectSiteSelect').html("");
                        $('#dynamicForm .projectSiteSelect').prop('disabled', false);
                    }
                },
                error: function(){

                }
            });
        }

        function changeType(){
            if($("#inOutCheckbox").is(':checked') == true){
                $("#transfer_type").html($("#inTransferTypes").val());
            }else{
                $("#transfer_type").html($("#outTransferTypes").val());
            }
            $("#transfer_type").trigger('change');
        }
        function openDetails(componentTransferId){
            $.ajax({
                url: '/inventory/component/detail/'+componentTransferId+'?_token='+$("input[name='_token']").val(),
                type: 'GET',
                async: true,
                success: function(data,textStatus,xhr){
                    $("#transferDetailModel .modal-body").html(data);
                    $("#transferDetailModel").modal('show');
                },
                error:function(errorData){
                    alert('Something went wrong');
                }

            });
        }
    </script>
    <script>
        $('#transfer_type').change(function(){
            if($(this).val() == "client"){
                $("#dynamicForm").html($('#client_form').clone().show(500));
                $("#inOutSubmit").show();
            }else if($(this).val() == "supplier"){
                $("#dynamicForm").html($('#supplier_form').clone().show(500));
                $("#dynamicForm .custom-file-list").attr('id','tab_images_uploader_filelist');
                $("#dynamicForm .custom-file-container").attr('id','tab_images_uploader_container');
                $("#dynamicForm .custom-file-browse").attr('id','tab_images_uploader_pickfiles');
                $("#dynamicForm .custom-upload-file").attr('id','tab_images_uploader_uploadfiles');
                $("#dynamicForm .date").each(function(){
                    $(this).datetimepicker();
                });



                $("#inOutSubmit").show();
                InventoryComponentImageUpload.init();
            }else if($(this).val() == "hand"){
                $("#dynamicForm").html($('#hand_form').clone().show(500));
                $("#inOutSubmit").show();
            }else if($(this).val() == 'office'){
                $("#dynamicForm").html($('#office_form').clone().show(500));
                $("#inOutSubmit").show();
            }else if($(this).val() == 'labour'){
                $("#dynamicForm").html($('#labour_form').clone().show(500));
                $("#inOutSubmit").show();
            }else if($(this).val() == 'sub-contractor'){
                $("#dynamicForm").html($('#subcontractor_form').clone().removeAttr('hidden').show(500));
                $("#inOutSubmit").show();
            }else if($(this).val() == 'site'){
                $("#dynamicForm").html($('#site_form').clone().removeAttr('hidden').show(500));
                $("#inOutSubmit").show();
            }else if($(this).val() == 'maintenance'){
                $("#dynamicForm").html($('#maintenance_form').clone().show(500));
                $("#dynamicForm .custom-file-list").attr('id','tab_images_uploader_filelist');
                $("#dynamicForm .custom-file-container").attr('id','tab_images_uploader_container');
                $("#dynamicForm .custom-file-browse").attr('id','tab_images_uploader_pickfiles');
                $("#dynamicForm .custom-upload-file").attr('id','tab_images_uploader_uploadfiles');
                $("#inOutSubmit").show();
                InventoryComponentImageUpload.init();
            }else{
                $("#dynamicForm").html('');
                $("#inOutSubmit").hide();
            }
        })
    </script>
@endsection
