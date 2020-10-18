@extends('layout.master')
@section('title','Constro | Generate Challan')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
<input type="hidden" id="unitOptions" value="{{$unitOptions}}">
<input id="nosUnitId" type="hidden" value="{{$nosUnitId}}">
<form role="form" id="generate_challan" class="form-horizontal" action="/purchase/material-request/create" method="post">
    <input type="hidden" id="component_id">
    <input type="hidden" id="iterator">
    {!! csrf_field() !!}
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
                                    <h1>Generate Challan</h1>
                                </div>
                                <div class="pull-right">
                                    <a href="/inventory/manage" class="btn btn-secondary-outline margin-top-15">
                                        < Back</a> <button type="submit" class="btn red margin-top-15">
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
                                                            <label>Client Name : </label>
                                                            <input type="text" class="form-control empty" id="clientSearchbox" name="client_name" value="{{$globalProjectSite->project->client->company}}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Site Name : </label>
                                                            <input type="text" class="form-control empty" id="projectSearchbox" value="{{$globalProjectSite->project->name}} - {{$globalProjectSite->name}}" readonly>
                                                            <input type="hidden" id="project_site_id" name="project_site_id" value="{{$globalProjectSite->id}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>User Name : </label>
                                                            <!--<input type="text" class="form-control empty" id="userSearchbox"  placeholder="Enter user name" name="user_name">-->
                                                            <input type="text" class="form-control empty" value="{{$userData['username']}}" readonly name="user_name">
                                                            <input type="hidden" name="user_id" id="user_id_" value="{{$userData['id']}}">
                                                            <div id="user-suggesstion-box"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet light ">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <a href="#" class="btn btn-set yellow pull-right" style="margin-left: 10px;" id="assetBtn">
                                                        Save
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="portlet-body form">
                                                <div class="portlet light ">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="fa fa-bars font-red"></i>&nbsp
                                                            <span class="caption-subject font-red sbold uppercase">Material List</span>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="table-scrollable">
                                                            <table class="table table-hover table-light">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 4%;"></th>
                                                                        <th style="width: 12%;"> Name </th>
                                                                        <th style="width: 12%;"> Quantity </th>
                                                                        <th style="width: 12%;"> Unit </th>
                                                                        <th style="width: 12%;">Rate</th>
                                                                        <th style="width: 12%;">GST % </th>
                                                                        <th style="width: 12%;">CGST Amount</th>
                                                                        <th style="width: 12%;">SGST Amount</th>
                                                                        <th style="width: 12%;">Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="materialRows">

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="fa fa-bars font-red"></i>&nbsp
                                                            <span class="caption-subject font-red sbold uppercase">Asset List</span>
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="table-scrollable">
                                                            <table class="table table-hover table-light">
                                                                <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        <th> Name </th>
                                                                        <th> Quantity </th>
                                                                        <th> Unit </th>
                                                                        <th>Rate</th>
                                                                        <th>GST % </th>
                                                                        <th>CGST Amount</th>
                                                                        <th>SGST Amount</th>
                                                                        <th>Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="Assetrows">
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet light ">
                                            <div class="portlet-body form">
                                                <form>
                                                    <div class="row form-group">
                                                        <div class="col-md-2">
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
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Project Name</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <select name="project_id" class="form-control projectSelect" onchange="projectChange(this)" id="project_id">
                                                                <option value="">--Select Project Name--</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Project Site</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <select name="project_site_id" class="form-control projectSiteSelect" id="inv_project_site_id">
                                                                <option value="">--Select Project Site Name--</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row form-group">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Select Vendor</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <select class="form-control" id="vendor_id" name="vendor_id">
                                                                <option value="">--Select a vendor--</option>
                                                                @foreach($transportationVendors as $vendor)
                                                                <option value="{{$vendor['id']}}">{{$vendor['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group" id="transportation_amount">
                                                        <div class="col-md-2">
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
                                                        <div class="col-md-4">
                                                            <div class="input-group">
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
                                                        <div class="col-md-4">
                                                            <div class="input-group">
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
                                                        <div class="col-md-4">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control transportation-igst-percentage" name="transportation_igst_percent" onkeyup="calculateTransportationTaxes(this)">
                                                                <span class="input-group-addon">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control transportation-igst-amount" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group" id="transportation_total">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Transportation Total</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control transportation-total" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Driver Name</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control" name="driver_name" id="driver_name">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Mobile No</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control" name="mobile" id="mobile_no">
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Vehicle No</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control" name="vehicle_number" id="vehicle_number">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Remark</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <textarea name="remark" class="form-control" id="remark" placeholder="Remark..."></textarea>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="myModal" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4"> Material</div>
                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                            </div>
                                        </div>
                                        <div class="modal-body" style="padding:40px 50px;">
                                            <input type="hidden" id="materialModalComponentSlug">
                                            <div class="form-group">
                                                <label for="name" class="control-label">Material Name:</label>
                                                <input type="text" class="form-control empty" id="searchbox" placeholder="Enter material name">
                                            </div>
                                            <div class="form-group">
                                                <label for="name" class="control-label">Quantity:</label>
                                                <input type="number" class="form-control empty" id="qty" placeholder="Enter quantity">
                                            </div>
                                            <div class="form-group" id="unitDrpdn">
                                                <label for="name" class="control-label">Unit:</label>
                                                <select id="materialUnit" class="form-control">
                                                    @foreach($units as $unit)
                                                    <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <article>
                                                <label for="files">Select multiple files:</label>
                                                <input id="files" type="file" multiple="multiple" />
                                                <output id="result" />
                                            </article>
                                            <div class="btn red pull-right" id="createMaterial"> Create</div>
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
                                            <div class="form-group">
                                                <label for="name" class="control-label">Asset Name :</label>
                                                <input type="text" class="form-control empty" id="Assetsearchbox" placeholder="Enter asset name">
                                                <div id="asset_suggesstion-box"></div>
                                            </div>
                                            <div class="form-group">
                                                <label for="name" class="control-label">Quantity :</label>
                                                <input type="number" class="form-control empty" id="Assetqty" value="1" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="name" class="control-label">Unit :</label>
                                                <input type="text" class="form-control empty" id="AssetUnitsearchbox" value="Nos" readonly>
                                            </div>
                                            <article>
                                                <label for="filesAsset">Select multiple files:</label>
                                                <input id="filesAsset" type="file" multiple="multiple" />
                                                <output id="resultAsset" />
                                            </article>
                                            <div class="btn red pull-right" id="createAsset"> Create</div>
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
</form>
@endsection
@section('javascript')
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<link rel="stylesheet" href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" />
<link rel="stylesheet" href="/assets/global/css/app.css" />
<!-- <link rel="stylesheet" href="/assets/custom/purchase/material-request/material-request.css" />
<script src="/assets/custom/purchase/material-request/material-request.js" type="text/javascript"></script>-->
<script src="/assets/custom/inventory/generate-challan.js" type="text/javascript"></script>
<script>

</script>
@endsection