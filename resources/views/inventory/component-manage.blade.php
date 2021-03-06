@extends('layout.master')
@section('title','Constro | Manage Inventory Transfers')
@section('nav-bar')
    @include('partials.common.navbar')
@endsection
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
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
                                    <h1>{!!  $inventoryComponent->name !!}</h1>
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
                                                <div class="portlet light ">
                                                    <div class="portlet-body form">
                                                        <div class="portlet light ">
                                                            @if($isReadingApplicable)
                                                                <ul class="nav nav-tabs nav-tabs-lg">
                                                                    <li class="active">
                                                                        <a href="#transferTab" data-toggle="tab"> Transfers </a>
                                                                    </li>
                                                                    @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-asset-reading')|| $user->customHasPermission('view-asset-reading')|| $user->customHasPermission('edit-asset-reading'))
                                                                        <li>
                                                                            <a href="#readingTab" data-toggle="tab"> Readings </a>
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            @endif
                                                            <div class="tab-content">
                                                                <div id="transferTab" class="tab-pane fade in active">
                                                                    <div class="pull-right">
                                                                        <div class="form-group " style="text-align: center">
                                                                            <a href="javascript:void(0);" class="btn yellow" id="stockButton" >
                                                                                Opening Stock
                                                                            </a>
                                                                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-inventory-in-out-transfer'))
                                                                                <a href="javascript:void(0);" class="btn yellow" style="margin: 20px" id="transaction">
                                                                                    <i class="fa fa-plus" style="font-size: large"></i>&nbsp;
                                                                                    Transaction
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="portlet-body">
                                                                        <div class="table-scrollable">
                                                                            <table class="table table-striped table-bordered table-hover order-column" id="inventoryComponentListingTable">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th> GRN </th>
                                                                                    <th> Quantity </th>
                                                                                    <th> Unit </th>
                                                                                    <th> Rate per Unit </th>
                                                                                    <th> Date </th>
                                                                                    <th> Status </th>
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

                                                                @if($isReadingApplicable)
                                                                    <div id="readingTab" class="tab-pane fade">
                                                                        @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-asset-reading'))
                                                                            <div class="pull-right">
                                                                                <div class="form-group " style="text-align: center">
                                                                                    <a href="#readingFormModel" class="btn yellow" id="readingButton" data-toggle="modal">
                                                                                        <i class="fa fa-plus" style="font-size: large"></i>&nbsp;
                                                                                        Reading
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        @endif

                                                                        <div class="portlet-body">
                                                                            <div class="table-scrollable">
                                                                                <table class="table table-striped table-bordered table-hover order-column" id="inventoryFuelReadingListings">
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th> Start Reading </th>
                                                                                        <th> End Reading </th>
                                                                                        <th> Start Time </th>
                                                                                        <th> End Time </th>
                                                                                        <th> Units Used </th>
                                                                                        <th> Litre Per Unit </th>
                                                                                        <th> Electricity Per Unit </th>
                                                                                        <th> Fuel Consumed </th>
                                                                                        <th> Electricity Consumed </th>
                                                                                        <th> Top Up </th>
                                                                                        <th> Top Time </th>
                                                                                    </tr>
                                                                                    </thead>
                                                                                    <tbody>

                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="transactionModal" role="dialog">
                                    <div class="modal-dialog" style="width: 90%; ">
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
                                                <form id="transactionForm" role="form" action="/inventory/component/add-transfer/{{$inventoryComponent['id']}}" method="POST" id="addTransferForm">
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
                                                                <option value="{{$unit['id']}}">{{$unit['name']}}</option>
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
                                                                <option value="{{$unit['id']}}">{{$unit['name']}}</option>
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
                                                                <option value="{{$unit['id']}}">{{$unit['name']}}</option>
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
                                                                <option value="{{$unit['id']}}">{{$unit['name']}}</option>
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
                                                </div>
                                                <div id="labour_form" hidden>
                                                    <div class="form-group">
                                                        <input type="text" name="source_name" id="sourceName" class="form-control typeahead" placeholder="Enter User's Name">
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Quantity</label>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <input type="text" id="user_quantity" name="quantity" class="form-control" placeholder="Enter Quantity" onkeyup="checkUserAllowedQuantity()">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        @if($isReadingApplicable)
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Unit</label>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <select class="form-control" id="user_unit" name="unit_id" onchange="checkUserAllowedQuantity()">
                                                                    <option value="{{$nosUnitId}}">Nos</option>
                                                                </select>
                                                            </div>
                                                        @else
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Unit</label>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <select class="form-control" id="user_unit" name="unit_id" onchange="checkUserAllowedQuantity()">
                                                                    <option value=""> -- Unit -- </option>
                                                                    @foreach($units as $unit)
                                                                        <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    {{--@if($isReadingApplicable)
                                                        <div class="row form-group" id="rent">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Rent</label>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="text" name="rent" id="rent" class="form-control" placeholder="Enter Rent" value="{!! $amount !!}" hidden>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="row form-group" id="rent">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Rent</label>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="text" name="rate" id="rate" class="form-control" placeholder="Enter Rate" value="{!! $amount['rate_per_unit'] !!}" hidden>
                                                            </div>
                                                        </div>
                                                    @endif--}}
                                                    <div class="form-group">
                                                        <textarea name="remark" class="form-control" id="remark" placeholder="Remark"></textarea>
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
                                                                <option value="{{$unit['id']}}">{{$unit['name']}}</option>
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
                                                        <label class="control-label"> Client: {{$projectInfo['client']}}</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label"> Project: {{$projectInfo['project']}}</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label"> Project: {{$projectInfo['project_site']}}</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="hidden" name="unit_id" value="{{$nosUnitId}}">
                                                        <label class="control-label"> Unit: Nos</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="hidden" name="quantity" value="1">
                                                        <label class="control-label"> Quantity: 1</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea class="form-control" placeholder="Remark" name="remark" id="remark"></textarea>
                                                    </div>
                                                </div>
                                                <div id="site_form" hidden>

                                                    <div class="row form-group">
                                                        <div class="col-md-3">
                                                            <label class="control-label pull-right">Client Name</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <select name="client_id" class="form-control clientSelect" onchange="clientChange(this)" id="client_id">
                                                                <option value="">--Select Client Name--</option>
                                                                @foreach($clients as $client)
                                                                    <option value="{{$client['id']}}">{{$client['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-3">
                                                            <label class="control-label pull-right">Project Name</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <select name="project_id" class="form-control projectSelect" onchange="projectChange(this)" id="project_id">
                                                                <option value="">--Select Project Name--</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-3">
                                                            <label class="control-label pull-right">Project Site</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <select name="project_site_id" class="form-control projectSiteSelect" id="project_site_id">
                                                                <option value="">--Select Project Site Name--</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    @if($isReadingApplicable)
                                                        <div class="row form-group" id="rent">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Asset Type</label>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <input type="text" id="asset_type" name="asset_type_id" class="form-control" value="{!! $asset_type['name'] !!}" readonly>
                                                            </div>
                                                        </div>

                                                    @endif

                                                    @if($isReadingApplicable || (array_key_exists('asset',$inventoryComponent->toArray()) && $inventoryComponent->asset->assetTypes->slug == 'other'))
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Unit</label>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <select class="form-control" id="unit" name="unit_id">
                                                                    <option value="{{$nosUnitId}}">Nos</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group" id="rent">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Rent</label>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <input type="text" name="rate_per_unit" id="rent_id" class="form-control" placeholder="Enter Rent" value="{!! $amount['rate_per_unit'] !!}">
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Quantity</label>
                                                            </div>
                                                            @if($asset_type['slug'] == 'other')
                                                                <div class="col-md-9">
                                                                    <input type="text" id="site_form_quantity" name="quantity" class="form-control tax-modal-quantity" placeholder="Enter Quantity">
                                                                </div>
                                                            @else
                                                                <div class="col-md-9">
                                                                    <input type="text" id="site_form_quantity" name="quantity" class="form-control tax-modal-quantity" placeholder="Enter Quantity" value="1" readonly>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Unit</label>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <select class="form-control" id="unit" name="unit_id" onchange="checkAllowedQuantity()">
                                                                    <option value=""> -- Unit -- </option>
                                                                    @foreach($units as $unit)
                                                                        <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Quantity</label>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <input type="text" id="site_form_quantity" name="quantity" class="form-control tax-modal-quantity" placeholder="Enter Quantity" onkeyup="calculateTaxes(this)" onchange="checkAllowedQuantity()">
                                                            </div>
                                                        </div>

                                                        <div class="row form-group" id="site_out_rate">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Rate</label>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <input type="text" name="rate_per_unit" id="rate" class="form-control tax-modal-rate" placeholder="Enter Rate" value="{!! $amount['rate_per_unit'] !!}" onkeyup="calculateTaxes(this)">
                                                            </div>
                                                        </div>

                                                        <div class="row form-group" id="site_cgst">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">CGST</label>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="input-group" >
                                                                    @if($amount['cgst_percentage'] != null)
                                                                        <input type="text" class="form-control tax-modal-cgst-percentage" name="cgst_percentage" onkeyup="calculateTaxes(this)" value="{{$amount['cgst_percentage']}}">
                                                                    @else
                                                                        <input type="text" class="form-control tax-modal-cgst-percentage" name="cgst_percentage" onkeyup="calculateTaxes(this)" value="0">
                                                                    @endif
                                                                    <span class="input-group-addon">%</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control tax-modal-cgst-amount" name="cgst_amount" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group" id="site_sgst">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">SGST</label>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="input-group" >
                                                                    @if($amount['sgst_percentage'] != null)
                                                                        <input type="text" class="form-control tax-modal-sgst-percentage" name="sgst_percentage" onkeyup="calculateTaxes(this)" value="{{$amount['sgst_percentage']}}">
                                                                    @else
                                                                        <input type="text" class="form-control tax-modal-sgst-percentage" name="sgst_percentage" onkeyup="calculateTaxes(this)" value="0">
                                                                    @endif
                                                                    <span class="input-group-addon">%</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control tax-modal-sgst-amount" name="sgst_amount" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group" id="site_igst">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">IGST</label>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="input-group" >
                                                                    @if($amount['igst_percentage'] != null)
                                                                        <input type="text" class="form-control tax-modal-igst-percentage" name="igst_percentage" onkeyup="calculateTaxes(this)" value="{{$amount['igst_percentage']}}">
                                                                    @else
                                                                        <input type="text" class="form-control tax-modal-igst-percentage" name="igst_percentage" onkeyup="calculateTaxes(this)" value="0">
                                                                    @endif
                                                                    <span class="input-group-addon">%</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control tax-modal-igst-amount" name="igst_amount" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group" id="total">
                                                            <div class="col-md-2">
                                                                <label class="control-label pull-right">Total</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control tax-modal-total" name="total" readonly>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="row form-group">
                                                        <div class="col-md-3">
                                                            <label class="control-label pull-right">Select Vendor</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <select class="form-control" id="vendor_id" name="vendor_id">
                                                                @foreach($transportationVendors as $vendor)
                                                                    <option value="{{$vendor['id']}}">{{$vendor['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group" id="transportation_amount">
                                                        <div class="col-md-3">
                                                            <label class="control-label pull-right">Transportation Amount</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control transportation-amount" name="transportation_amount">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group" id="transportation_cgst">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">CGST</label>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="input-group" >
                                                               <input type="text" class="form-control transportation-cgst-percentage" name="transportation_cgst_percent" onkeyup="calculateTransportationTaxes(this)">
                                                                <span class="input-group-addon">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control transportation-cgst-amount" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group" id="transportation_sgst">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">SGST</label>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="input-group" >
                                                                <input type="text" class="form-control transportation-sgst-percentage" name="transportation_sgst_percent" onkeyup="calculateTransportationTaxes(this)">
                                                                <span class="input-group-addon">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control transportation-sgst-amount" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group" id="transportation_igst">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">IGST</label>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="input-group" >
                                                                <input type="text" class="form-control transportation-igst-percentage" name="transportation_igst_percent" onkeyup="calculateTransportationTaxes(this)">
                                                                <span class="input-group-addon">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control transportation-igst-amount" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group" id="transportation_total">
                                                        <div class="col-md-3">
                                                            <label class="control-label pull-right">Transportation Total</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control transportation-total" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-3">
                                                            <label class="control-label pull-right">Driver Name</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control" name="driver_name" id="driver_name">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-3">
                                                            <label class="control-label pull-right">Mobile No</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control" name="mobile" id="mobile_no">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-3">
                                                            <label class="control-label pull-right">Vehicle No</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control" name="vehicle_number" id="vehicle_number">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea name="remark" class="form-control" id="remark" placeholder="Remark..."></textarea>
                                                    </div>
                                                </div>
                                                <div id="site_in_form" hidden>
                                                    <div class="row form-group">
                                                        <div class="col-md-3">
                                                            <label class="control-label pull-right">Select GRN : </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="siteOutGrn" name="siteOutId" onchange="getGRNDetails()">
                                                                <option value="default">Select Site Out Grn</option>
                                                                @foreach($siteOutGrns as $grn)
                                                                    <option value="{{$grn['id']}}">{{$grn['grn']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="grnDetail" hidden>
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Site Details : </label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input class="form-control" type="text" id="siteDetails" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Quantity : </label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input class="form-control" type="text" id="quantity" name="quantity">
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Unit : </label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input class="form-control" type="text" id="unit" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Transportation Amount : </label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input class="form-control" type="text" id="transportation_amount" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Transportation Tax Amount : </label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input class="form-control" type="text" id="transportation_tax_amount" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Company name : </label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input class="form-control" type="text" id="company_name" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Driver Name : </label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input class="form-control" type="text" id="driver_name" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Mobile : </label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input class="form-control" type="text" id="mobile" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">Vehicle Number : </label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input class="form-control" type="text" id="vehicle_name" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label">Select Images For Generating GRN :</label>
                                                        <input id="imageupload" type="file" class="btn blue"/>
                                                        <br>
                                                        <div class="row">
                                                            <div id="preview-image" class="row">

                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3" id="grnImageUplaodButton" style="margin-top: 1%;" hidden>
                                                                <a href="javascript:void(0);" class="btn blue" > Upload Images</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="afterImageUploadDiv" hidden>
                                                        <input type="hidden" name="inventory_component_transfer_id" id="inventoryComponentTransferId">
                                                        <div class="form-group row">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right"> GRN :</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input class="form-control" name="grn" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="control-label">Select Images :</label>
                                                            <input id="postImageUpload" type="file" class="btn blue" multiple />
                                                            <br />
                                                            <div class="row">
                                                                <div id="postPreviewImage" class="row">

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <input type="text" class="form-control" name="remark" placeholder="Enter Remark">
                                                        </div>
                                                        {{--<button type="submit" class="btn btn-set red pull-right">
                                                            <i class="fa fa-check" style="font-size: large"></i>
                                                            Save&nbsp; &nbsp; &nbsp;
                                                        </button>--}}
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
                                <div class="modal fade" id="transferApproveModel" role="dialog">
                                    <div class="modal-dialog" style="width: 90%;" >
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header" style="padding-bottom:10px">
                                                <div class="row">
                                                    <div class="col-md-7 col-md-offset-2"> Approve Transfer </div>
                                                    <div class="col-md-3"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                </div>
                                            </div>
                                            <div class="modal-body" style="padding:40px 50px; font-size: 15px">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($isReadingApplicable)
                                    <input type="hidden" name="asset_type" id="assetType" value="{{$inventoryComponent->asset->assetTypes->slug}}">
                                    <div class="modal fade" id="readingFormModel" role="dialog">
                                        <div class="modal-dialog">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header" style="padding-bottom:10px">
                                                    <div class="row">
                                                        <div class="col-md-7 col-md-offset-2"> Readings </div>
                                                        <div class="col-md-3"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                    </div>
                                                </div>
                                                <form action="/inventory/component/readings/add/{{$inventoryComponent->id}}" method="POST">
                                                    {!! csrf_field() !!}
                                                    <div class="modal-body" style="padding:40px 50px; font-size: 15px">
                                                        <div class="form-group">
                                                            <input type="radio" name="is_fuel" value="true"> Fuel Used
                                                            <input type="radio" name="is_fuel" value="false"> Electricity Used
                                                        </div>
                                                        <div id="formBody"></div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div id="fuelForm" hidden>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-1">
                                                            <label class="control-label pull-right">
                                                                Start Time :
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group date form_datetime form_datetime bs-datetime" data-date-format="yyyy-mm-dd hh:ii">
                                                                <input type="text" size="16" class="form-control" name="start_time" id="startTime">
                                                                <span class="input-group-addon">
                                                                    <button class="btn default date-set" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-1">
                                                            <label class="control-label pull-right">
                                                                Start Reading :
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="start_reading" id="startReading">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-1">
                                                            <label class="control-label pull-right">
                                                                Stop Time :
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group date form_datetime form_datetime bs-datetime">
                                                                <input type="text" size="16" class="form-control" name="stop_time" id="stopTime">
                                                                <span class="input-group-addon">
                                                                    <button class="btn default date-set" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-1">
                                                            <label class="control-label pull-right">
                                                                Stop Reading :
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="stop_reading" id="stopReading">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-1">
                                                            <label class="control-label pull-right">
                                                                Top-up :
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="top_up" id="topUp">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-1">
                                                            <label class="control-label pull-right">
                                                                Top-up Time :
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group date form_datetime form_datetime bs-datetime">
                                                                <input type="text" size="16" class="form-control" name="top_up_time" id="topUpTime">
                                                                <span class="input-group-addon">
                                                                    <button class="btn default date-set" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-1">
                                                            <label class="control-label pull-right">
                                                                Fuel Per Unit :
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="fuel_per_unit" id="fuelPerUnit">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-4">
                                                            <button type="submit" class="btn red"> Submit </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="electricityForm" hidden>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-1">
                                                            <label class="control-label pull-right">
                                                                Start Time :
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group date form_datetime form_datetime bs-datetime" data-date-format="yyyy-mm-dd hh:ii">
                                                                <input type="text" size="16" class="form-control" name="start_time" id="startTime">
                                                                <span class="input-group-addon">
                                                                    <button class="btn default date-set" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-1">
                                                            <label class="control-label pull-right">
                                                                Start Reading :
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="start_reading" id="startReading">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-1">
                                                            <label class="control-label pull-right">
                                                                Stop Time :
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group date form_datetime form_datetime bs-datetime">
                                                                <input type="text" size="16" class="form-control" name="stop_time" id="stopTime">
                                                                <span class="input-group-addon">
                                                                    <button class="btn default date-set" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-1">
                                                            <label class="control-label pull-right">
                                                                Stop Reading :
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="stop_reading" id="stopReading">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-1">
                                                            <label class="control-label pull-right">
                                                                Electricity Per Unit :
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="electricity_per_unit" id="electricityPerUnit">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 2%;">
                                                    <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-4">
                                                            <button type="submit" class="btn red"> Submit </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
    <script src="/assets/custom/inventory/component-reading-manage-datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script src="/assets/custom/inventory/image-datatable.js"></script>
    <script src="/assets/custom/inventory/image-upload.js"></script>
    <script>
        $(document).ready(function(){
            InventoryComponentListing.init();
            changeType();
            $("#transaction").click(function(){
                CreateInventoryComponentTransfer.init();
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
                        $("#openingStock").val(data.opening_stock);
                        alert(data.message);
                    },
                    error: function(errorData){
                        alert('Something went wrong');
                    }
                });
            });

            $('#inOutCheckbox').on('switchChange.bootstrapSwitch', function(event, state) {
                changeType();
            });
            
            if(typeof ($("#assetType").val()) != 'undefined'){
                var assetType = $("#assetType").val();
                switch(assetType){
                    case 'fuel_dependent':
                        $("#readingFormModel .modal-body").html($("#fuelForm").clone().show());
                        initializeDateTimePicker();
                        break;

                    case 'electricity_dependent':
                        $("#readingFormModel .modal-body").html($("#electricityForm").clone().show());
                        initializeDateTimePicker();
                        break;
                }
                $('input[name="is_fuel"]').on('change', function(){
                    if($(this).val() == 'true'){
                        $("#formBody").html($("#fuelForm").clone().show());
                        initializeDateTimePicker();
                    }else if($(this).val() == 'false'){
                        $("#formBody").html($("#electricityForm").clone().show());
                        initializeDateTimePicker();
                    }else{
                        $("#formBody").html();
                    }
                });
            }

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
                        var projectId = $("#dynamicForm .projectSelect").val();
                        getProjectSites(projectId);
                    },
                    error: function(){

                    }
                });
            }
        }

        function initializeDateTimePicker(){
            $("#readingFormModel form .date").each(function(){
                $(this).datetimepicker({
                    format: 'yyyy-mm-dd hh:ii'
                });
            });
        }

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
                url: '/inventory/component/detail/'+componentTransferId+'/for-detail?_token='+$("input[name='_token']").val(),
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

        function changeStatus(componentTransferId){
            $.ajax({
                url: '/inventory/component/detail/'+componentTransferId+'/for-approval?_token='+$("input[name='_token']").val(),
                type: 'GET',
                async: true,
                success: function(data,textStatus,xhr){
                    $("#transferApproveModel .modal-body").html(data);
                    $("#transferApproveModel").modal('show');
                },
                error:function(errorData){
                    alert('Something went wrong');
                }

            });
        }

        var  CreateInventoryComponentTransfer = function () {
            var handleCreate = function() {
                var form = $('#transactionForm');
                var error = $('.alert-danger', form);
                var success = $('.alert-success', form);
                form.validate({
                    errorElement: 'span', //default input error message container
                    errorClass: 'help-block', // default input error message class
                    focusInvalid: false, // do not focus the last invalid input
                    rules: {
                        unit_id:{
                            required: true
                        },
                        project_site_id:{
                            required: true
                        },
                        quantity: {
                            required: true
                        }
                    },
                    messages: {
                        quantity: {
                            required: "Quantity is required."
                        },
                        unit_id:{
                            required: "Unit is required."
                        }
                    },
                    invalidHandler: function (event, validator) { //display error alert on form submit
                        success.hide();
                        error.show();
                    },
                    highlight: function (element) { // hightlight error inputs
                        $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                    },
                    unhighlight: function (element) { // revert the change done by hightlight
                        $(element)
                            .closest('.form-group').removeClass('has-error'); // set error class to the control group
                    },
                    success: function (label) {
                        label
                            .closest('.form-group').addClass('has-success');
                    },
                    submitHandler: function (form) {
                        $("button[type='submit']").prop('disabled', true);
                        success.show();
                        error.hide();
                        form.submit();
                    }
                });
            }
            return {
                init: function () {
                    handleCreate();
                }
            };
        }();
    </script>
    <script>

        function checkUserAllowedQuantity(){
            $('#dynamicForm #user_quantity').rules('remove');
            $('#dynamicForm #user_quantity').closest('form-group').removeClass('has-error');
            var quantity = $('#user_quantity').val();
            var unitId =  $('#user_unit').val();
            if(typeof quantity != 'undefined' && quantity != '' && !(isNaN(quantity)) && typeof unitId != 'undefined' && unitId != '' && !(isNaN(unitId))){
                $.ajax({
                    url: '/inventory/transfer/check-quantity',
                    type: 'POST',
                    async: true,
                    data: {
                        _token: $("input[name='_token']").val(),
                        inventoryComponentId : $('#inventoryComponentId').val(),
                        quantity: quantity,
                        unitId: unitId
                    },
                    success: function(data,textStatus,xhr){
                        if(data.show_validation == true){
                            $('#user_quantity').rules('add',{
                                max: data.available_quantity,
                                messages: {
                                    max  : "Available quantity is "+data.available_quantity
                                }
                            });

                        }else{
                            $('#user_quantity').rules('remove');
                            $('#user_quantity').closest('form-group').removeClass('has-error');
                        }
                    },
                    error: function(){

                    }
                });
            }
        }

        $('#transfer_type').change(function(){
            if($(this).val() == 'user'){
                $("#dynamicForm").html($('#labour_form').clone().show(500));
                $("#dynamicForm .custom-file-list").attr('id','tab_images_uploader_filelist');
                $("#dynamicForm .custom-file-container").attr('id','tab_images_uploader_container');
                $("#dynamicForm .custom-file-browse").attr('id','tab_images_uploader_pickfiles');
                $("#dynamicForm .custom-upload-file").attr('id','tab_images_uploader_uploadfiles');
                $("#inOutSubmit").show();
                InventoryComponentImageUpload.init();
                CreateInventoryComponentTransfer.init();
                var citiList = new Bloodhound({
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    remote: {
                        url: "/inventory/transfer/employee-auto-suggest/%QUERY",
                        filter: function(x) {
                            if($(window).width()<420){
                                $("#header").addClass("fixed");
                            }
                            return $.map(x, function (data) {
                                return {
                                    employee_name: data.employee_name
                                };
                            });
                        },
                        wildcard: "%QUERY"
                    }
                });
                citiList.initialize();
                $('#dynamicForm .typeahead').typeahead(null, {
                    displayKey: 'name',
                    engine: Handlebars,
                    source: citiList.ttAdapter(),
                    limit: 30,
                    templates: {
                        empty: [
                            '<div class="empty-suggest">',
                            'Unable to find any Result that match the current query',
                            '</div>'
                        ].join('\n'),
                        suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{employee_name}}</strong></div>')
                    },
                }).on('typeahead:selected', function (obj, datum) {
                    var POData = $.parseJSON(JSON.stringify(datum));
                    $('.typeahead').typeahead('val',POData.employee_name);
                })
                    .on('typeahead:open', function (obj, datum) {

                    });

            }else if($(this).val() == 'site'){
                CreateInventoryComponentTransfer.init();
                    if($('#inOutCheckbox').is(':checked') == true){
                        $("#dynamicForm").html($('#site_in_form').clone().removeAttr('hidden').show(500));
                        $("#imageupload").on('change',function () {
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
                                            var imagePreview = '<div class="col-md-2"><input class="grn-images" type="hidden" name="pre_grn_image[]" value="'+e.target.result+'"><img src="'+e.target.result+'" class="thumbimage" /></div>';
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
                        $("#grnImageUplaodButton a").on('click',function(){
                            var imageArray = $("#transactionForm .grn-images").val();
                            $.ajax({
                                url: '/inventory/transfer/upload-pre-grn-images',
                                type: 'POST',
                                data: {
                                    'imageArray' : imageArray,
                                    'inventory_component_id' : $('#inventoryComponentId').val(),
                                    'related_inventory_component_transfer_id' : $('#siteOutGrn').val(),
                                    'quantity' : $('#quantity').val()
                                },
                                success: function(data, textStatus, xhr){
                                    console.log('in sucess');
                                    $("#imageupload").hide();
                                    $("#grnImageUplaodButton").hide();
                                    $("#inventoryComponentTransferId").val(data.inventory_component_transfer_id);
                                    $("#afterImageUploadDiv").show();
                                    $("#afterImageUploadDiv input[name='grn']").val(data.grn);
                                },
                                error: function(errorData){

                                }
                            });
                        });

                        $("#postImageUpload").on('change', function () {
                            var countFiles = $(this)[0].files.length;
                            var imgPath = $(this)[0].value;
                            var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
                            var image_holder = $("#postPreviewImage");
                            image_holder.empty();
                            if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                                if (typeof (FileReader) != "undefined") {
                                    for (var i = 0; i < countFiles; i++) {
                                        var reader = new FileReader()
                                        reader.onload = function (e) {
                                            var imagePreview = '<div class="col-md-2"><input type="hidden" name="post_grn_image[]" value="'+e.target.result+'"><img src="'+e.target.result+'" class="thumbimage" /></div>';
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
                        CreateInventoryComponentTransfer.init();
                        $("#inOutSubmit").hide();
                    }else{
                        $("#dynamicForm").html($('#site_form').clone().removeAttr('hidden').show(500));
                        $("#imageupload").unbind('change');
                    }

                $("#inOutSubmit").show();
            }else{
                $("#dynamicForm").html('');
                $("#inOutSubmit").hide();
            }

        });

        function calculateTaxes(element){
            var rate = parseFloat($(element).closest('.modal-body').find('.tax-modal-rate').val());
            if(typeof rate == 'undefined' || rate == '' || isNaN(rate)){
                rate = 0;
            }
            var quantity = parseFloat($(element).closest('.modal-body').find('.tax-modal-quantity').val());
            if(typeof quantity == 'undefined' || quantity == '' || isNaN(quantity)){
                quantity = 0;
            }
            var subtotal = parseFloat(parseFloat(rate) * parseFloat(quantity)).toFixed(3);
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
            var cgstAmount = (subtotal * (parseFloat(cgstPercentage) / 100)).toFixed(3);
            var sgstAmount = (subtotal * (parseFloat(sgstPercentage) / 100)).toFixed(3);
            var igstAmount = (subtotal * (parseFloat(igstPercentage) / 100)).toFixed(3);
            $(element).closest('.modal-body').find('.tax-modal-cgst-amount').val(cgstAmount);
            $(element).closest('.modal-body').find('.tax-modal-sgst-amount').val(sgstAmount);
            $(element).closest('.modal-body').find('.tax-modal-igst-amount').val(igstAmount);
            var total = parseFloat((parseFloat(subtotal) + parseFloat(cgstAmount) + parseFloat(sgstAmount) + parseFloat(igstAmount))).toFixed(3)
            $(element).closest('.modal-body').find('.tax-modal-total').val(total);
        }

        function calculateTransportationTaxes(element){
            var transportationAmount = parseFloat($(element).closest('.modal-body').find('.transportation-amount').val());
            if(typeof transportationAmount == 'undefined' || transportationAmount == '' || isNaN(transportationAmount)){
                transportationAmount = 0;
            }

            var transportationCGSTPercent = parseFloat($(element).closest('.modal-body').find('.transportation-cgst-percentage').val());
            if(typeof transportationCGSTPercent == 'undefined' || transportationCGSTPercent == '' || isNaN(transportationCGSTPercent)){
                transportationCGSTPercent = 0;
            }

            var transportationSGSTPercent = parseFloat($(element).closest('.modal-body').find('.transportation-sgst-percentage').val());
            if(typeof transportationSGSTPercent == 'undefined' || transportationSGSTPercent == '' || isNaN(transportationSGSTPercent)){
                transportationSGSTPercent = 0;
            }

            var transportationIGSTPercent = parseFloat($(element).closest('.modal-body').find('.transportation-igst-percentage').val());
            if(typeof transportationIGSTPercent == 'undefined' || transportationIGSTPercent == '' || isNaN(transportationIGSTPercent)){
                transportationIGSTPercent = 0;
            }

            var transportationTotalAmount = parseFloat($(element).closest('.modal-body').find('.transportation-total').val());
            if(typeof transportationTotalAmount == 'undefined' || transportationTotalAmount == '' || isNaN(transportationTotalAmount)){
                transportationTotalAmount = 0;
            }

            var cgstAmount = ((parseFloat(transportationCGSTPercent) * parseFloat(transportationAmount)) / 100).toFixed(3);
            var sgstAmount = ((parseFloat(transportationSGSTPercent) * parseFloat(transportationAmount)) / 100).toFixed(3);
            var igstAmount = ((parseFloat(transportationIGSTPercent) * parseFloat(transportationAmount)) / 100).toFixed(3);
            $(element).closest('.modal-body').find('.transportation-cgst-amount').val(cgstAmount);
            $(element).closest('.modal-body').find('.transportation-sgst-amount').val(sgstAmount);
            $(element).closest('.modal-body').find('.transportation-igst-amount').val(igstAmount);
            var transportationTotal = parseFloat(parseFloat(transportationAmount) + parseFloat(cgstAmount) + parseFloat(sgstAmount) + parseFloat(igstAmount)).toFixed(3);
            $(element).closest('.modal-body').find('.transportation-total').val(transportationTotal);
        }

        function getGRNDetails(){
            var inventoryComponentTransferId = $('#siteOutGrn').val();
            if(inventoryComponentTransferId == 'default'){
                $('.grnDetail').hide();
            }else{
                $.ajax({
                    url: '/inventory/component/get-detail',
                    type: 'POST',
                    async: true,
                    data: {
                        _token: $("input[name='_token']").val(),
                        inventory_component_transfer_id : inventoryComponentTransferId
                    },
                    success: function(data,textStatus,xhr){
                        $('.grnDetail').show();
                        $('#siteDetails').val(data.inventory_component_transfer['source_name']);
                        $('#quantity').val(data.inventory_component_transfer['quantity']);
                        $('#unit').val(data.inventory_component_transfer['unit']);
                        $('#transportation_amount').val(data.inventory_component_transfer['transportation_amount']);
                        $('#transportation_tax_amount').val(data.inventory_component_transfer['transportation_tax_amount']);
                        $('#company_name').val(data.inventory_component_transfer['company_name']);
                        $('#driver_name').val(data.inventory_component_transfer['driver_name']);
                        $('#mobile').val(data.inventory_component_transfer['mobile']);
                        $('#vehicle_name').val(data.inventory_component_transfer['vehicle_number']);

                    },
                    error: function(errorData){
                        alert('Something went wrong');
                    }
                });
            }


        }

        function checkAllowedQuantity(){
            $('#dynamicForm #site_form_quantity').rules('remove');
            $('#dynamicForm #site_form_quantity').closest('form-group').removeClass('has-error');
            var quantity = $('#site_form_quantity').val();
            var unitId =  $('#unit').val();
            if(typeof quantity != 'undefined' && quantity != '' && !(isNaN(quantity)) && typeof unitId != 'undefined' && unitId != '' && !(isNaN(unitId))){
                $.ajax({
                    url: '/inventory/transfer/check-quantity',
                    type: 'POST',
                    async: true,
                    data: {
                        _token: $("input[name='_token']").val(),
                        inventoryComponentId : $('#inventoryComponentId').val(),
                        quantity: quantity,
                        unitId: unitId
                    },
                    success: function(data,textStatus,xhr){
                        if(data.show_validation == true){
                            $('#site_form_quantity').rules('add',{
                                max: data.available_quantity,
                                messages: {
                                    max  : "Available quantity is "+data.available_quantity
                                }
                            });
                        }else{
                            $('#site_form_quantity').rules('remove');
                            $('#site_form_quantity').closest('form-group').removeClass('has-error');
                        }
                    },
                    error: function(){

                    }
                });
            }else{
                $('#site_form_quantity').rules('add',{
                    required: true
                });
            }
        }

    </script>
@endsection
