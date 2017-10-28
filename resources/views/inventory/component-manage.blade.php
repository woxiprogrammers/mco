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
                                    <h1>{!!  $inventoryComponent->name !!} </h1>
                                    <h5 style="font-size: 15px !important;">{!!  $inventoryComponent->projectSite->project->name.' - '.$inventoryComponent->projectSite->name.' ('.$inventoryComponent->projectSite->project->client->company!!} )</h5>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <div class="row">
                                    <input type="hidden" name="opening_stock" id="openingStock" value="{{$inventoryComponent->opening_stock}}">
                                    <input type="hidden" name="inventory_component_id" id="inventoryComponentId" value="{{$inventoryComponent['id']}}">
                                    {!! csrf_field() !!}
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
                                                <form role="form">
                                                    <div class="form-group">
                                                        <div class="bootstrap-switch-container" style="height: 30px;width: 200px; margin-left: 0px;"><span class="bootstrap-switch-handle-on bootstrap-switch-primary" style="width: 88px;">&nbsp;&nbsp;&nbsp;</span><span class="bootstrap-switch-label" style="width: 88px;">&nbsp;</span><span class="bootstrap-switch-handle-off bootstrap-switch-default" style="width: 88px;">&nbsp;&nbsp;</span><input type="checkbox" class="make-switch" data-on-text="&nbsp;In&nbsp;&nbsp;" data-off-text="&nbsp;Out&nbsp;"></div>
                                                    </div><br>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="usrname" placeholder="Enter material name">
                                                    </div>
                                                    <div class="form-group">
                                                        <select class="form-control" id="transfer_type">
                                                            <option value=""> -- Select Transfer Type -- </option>
                                                            <option value="client"> Client </option>
                                                            <option value="hand"> By hand </option>
                                                            <option value="office"> Office </option>
                                                            <option value="supplier"> Supplier </option>
                                                        </select>
                                                    </div>

                                                    <div id="client_form">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" id="usrname" placeholder="Enter client name">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="number" class="form-control" id="usrname" placeholder="Enter quantity">
                                                        </div>
                                                        <div class="form-group">
                                                            <select class="form-control" id="clientId" name="client_id">
                                                                <option value=""> -- Unit -- </option>
                                                                <option value="client"> KG </option>
                                                                <option value="hand"> Ltr </option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="date" class="form-control" id="usrname" placeholder="Enter date">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" id="usrname" placeholder="Enter Remark">
                                                        </div>
                                                    </div>
                                                    <div id="hand_form">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" id="usrname" placeholder="Enter Shop Name">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="number" class="form-control" id="usrname" placeholder="Enter quantity">
                                                        </div>
                                                        <div class="form-group">
                                                            <select class="form-control" id="clientId" name="client_id">
                                                                <option value=""> -- Unit -- </option>
                                                                <option value="client"> KG </option>
                                                                <option value="hand"> Ltr </option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="date" class="form-control" id="usrname" placeholder="Enter date">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" id="usrname" placeholder="Enter Remark">
                                                        </div>
                                                    </div>
                                                    <div id="office_form">
                                                        <div class="form-group">
                                                            <input type="number" class="form-control" id="usrname" placeholder="Enter quantity">
                                                        </div>
                                                        <div class="form-group">
                                                            <select class="form-control" id="clientId" name="client_id">
                                                                <option value=""> -- Unit -- </option>
                                                                <option value="client"> KG </option>
                                                                <option value="hand"> Ltr </option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="date" class="form-control" id="usrname" placeholder="Enter date">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" id="usrname" placeholder="Enter Remark">
                                                        </div>
                                                    </div>
                                                    <div id="supplier_form">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" id="usrname" placeholder="Enter Supplier Name">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="number" class="form-control" id="usrname" placeholder="Enter quantity">
                                                        </div>
                                                        <div class="form-group">
                                                            <select class="form-control" id="clientId" name="client_id">
                                                                <option value=""> -- Unit -- </option>
                                                                <option value="client"> KG </option>
                                                                <option value="hand"> Ltr </option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="number" class="form-control" id="usrname" placeholder="Enter Bill Number">
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="row">
                                                                <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12"> </div>
                                                            </div>
                                                            <div id="tab_images_uploader_container" class="col-md-offset-5">
                                                                <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow">
                                                                    Browse</a>
                                                                <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                                    <i class="fa fa-share"></i> Upload Files </a>
                                                            </div>
                                                            <table class="table table-bordered table-hover" style="width: 200px">
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
                                                        <div class="form-group">
                                                            <input type="date" class="form-control" id="usrname" placeholder="Enter date">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" id="usrname" placeholder="Enter Vehicle Number">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" id="usrname" placeholder="Enter In Time">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" id="usrname" placeholder="Enter Out Time">
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" id="usrname" placeholder="Enter Remark">
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn red pull-right"> Create</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="myModal1" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header" style="padding-bottom:10px">
                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="col-md-4"> Asset</div>
                                                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                </div>
                                            </div>
                                            <div class="modal-body" style="padding:40px 50px;">
                                                <form role="form">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="usrname" placeholder="Enter asset name">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control" id="psw" placeholder="Enter quantity">
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="btn-group bootstrap-select bs-select form-control dropup">
                                                            <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" title="Afghanistan" aria-expanded="false"><span class="filter-option pull-left">Select Unit</span>&nbsp;<span class="caret"></span></button>
                                                            <div class="dropdown-menu open" style="max-height: 314px; overflow: hidden;"><div class="bs-searchbox"><input type="text" class="form-control" autocomplete="off"></div>
                                                                <ul class="dropdown-menu inner" role="menu" style="max-height: 272px; overflow-y: auto;"><li data-original-index="0" class="selected active"><a tabindex="0" class="" style="" data-tokens="null">
                                                                            <span class="text">Kg</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="3"><a tabindex="0" class="" style="" data-tokens="null">
                                                                            <span class="text">Ltr</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="4"><a tabindex="0" class="" style="" data-tokens="null"></a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12"> </div>
                                                        </div>
                                                        <div id="tab_images_uploader_container" class="col-md-offset-5">
                                                            <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow">
                                                                Browse</a>
                                                            <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                                <i class="fa fa-share"></i> Upload Files </a>
                                                        </div>
                                                        <table class="table table-bordered table-hover" style="width: 200px">
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
                                                    <button type="submit" class="btn red pull-right"> Create</button>
                                                </form>
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
    <script src="/assets/custom/inventory/component-manage-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            InventoryComponentListing.init();
            $("#transaction").click(function(){
                $("#transactionModal").modal();
            });
            $("#assetBtn").click(function(){
                $("#myModal1").modal();
            });
            $('#office_form').hide();
            $('#supplier_form').hide();
            $('#client_form').hide();
            $('#hand_form').hide()

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
        });

        function openDetails(componentTransferId){
            $("#transferDetailModel").modal('show');
        }
    </script>
    <script>
        $('#transfer_type').change(function(){
            if($(this).val() == "client"){
                $('#client_form').show(500);
                $('#office_form').hide();
                $('#supplier_form').hide();
                $('#hand_form').hide();
            }else if($(this).val() == "supplier"){
                $('#supplier_form').show(500);
                $('#office_form').hide();
                $('#client_form').hide();
                $('#hand_form').hide();

            }else if($(this).val() == "hand"){
                $('#hand_form').show(500);
                $('#office_form').hide();
                $('#supplier_form').hide();
                $('#client_form').hide();
            }else{
                $('#office_form').show(500);
                $('#supplier_form').hide();
                $('#client_form').hide();
                $('#hand_form').hide();
            }
        })
    </script>
@endsection
