@extends('layout.master')
@section('title','Constro | Generate Challan')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
<form role="form" id="generate_challan" class="form-horizontal" action="/inventory/transfer/challan/create" method="post">
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
                                                            <input type="hidden" id="project_site_id" name="out_project_site_id" value="{{$globalProjectSite->id}}">
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
                                                    <a href="#" class="btn btn-set yellow pull-right" id="cart-submit" style="margin-left: 10px;" id="assetBtn">
                                                        Save
                                                    </a>
                                                    <a href="#" class="btn btn-set yellow pull-right" id="cart-delete" style="margin-left: 10px;" id="assetBtn">
                                                        Delete
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
                                                                        <th style="width: 4%;"><input type="checkbox" id="all_material_checkbox" name="all_material_checkbox" class="material-component-checkbox" onchange="changeCheckboxStatus(this, 'material')"></th>
                                                                        <th style="width: 12%;"> Name </th>
                                                                        <th style="width: 12%;"> Available Quantity </th>
                                                                        <th style="width: 12%;"> Quantity </th>
                                                                        <th style="width: 8%;"> Unit </th>
                                                                        <th style="width: 8%;"> Rate </th>
                                                                        <th style="width: 8%;"> GST % </th>
                                                                        <th style="width: 12%;"> CGST Amount </th>
                                                                        <th style="width: 12%;"> SGST Amount </th>
                                                                        <th style="width: 12%;"> Total </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="materialRows">
                                                                    @foreach ($materials as $material)
                                                                    <tr class="cart-materials">
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input type="hidden" id="inventory_component_id_{{$material['id']}}" name="inventory_cart[{{$material['id']}}][inventory_component_id]" class="cart-inventory-component-id" value="{{$material['inventory_component_id']}}">
                                                                                <input type="hidden" id="{{$material['id']}}" name="cart_id" class="cart-id" value="{{$material['id']}}">
                                                                                <input type="checkbox" id="id_{{$material['id']}}" name="inventory_cart[{{$material['id']}}][checkbox]" value="{{$material['id']}}" class="component-checkbox">
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <span>{{$material['inventory_component']['name']}}</span>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input type="number" class="form-control cart-available-quantity" name="inventory_cart[{{$material['id']}}][available_quantity]" id="available_quantity_{{$material['id']}}" value="{{$material['available_quantity']}}" readonly />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input type="number" class="form-control cart-quantity" name="inventory_cart[{{$material['id']}}][quantity]" id="current_quantity_{{$material['id']}}" value="{{$material['quantity']}}" onchange="checkAllowedQuantity(event, 'material')" />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input class="form-control unit" value="{{$material['unit']['name']}}" readonly />
                                                                                <input type="hidden" class="form-control unit" id="unit_id_{{$material['id']}}" name="inventory_cart[{{$material['id']}}][unit_id]" value="{{$material['unit_id']}}" readonly />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input type="number" class="form-control cart-rate" id="rate_per_unit_{{$material['id']}}" name="inventory_cart[{{$material['id']}}][rate_per_unit]" value="{{$material['inventory_component']['material']['rate_per_unit']}}" readonly />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input class="form-control cart-gst" type="number" id="gst_{{$material['id']}}" name="inventory_cart[{{$material['id']}}][gst_percent]" value="{{($material['inventory_component']['material']['gst'] != null) ? $material['inventory_component']['material']['gst'] : 0}}" readonly>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input class="form-control cart-cgst_amount" type="number" id="cgst_amount_{{$material['id']}}" name="inventory_cart[{{$material['id']}}][cgst_amount]" readonly>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input class="form-control cart-sgst_amount" type="number" id="sgst_amount_{{$material['id']}}" name="inventory_cart[{{$material['id']}}][sgst_amount]" readonly>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input class="form-control cart-total" type="number" id="total_{{$material['id']}}" name="inventory_cart[{{$material['id']}}][total]" readonly>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
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
                                                                        <th style="width: 4%;"><input type="checkbox" id="all_asset_checkbox" name="all_asset_checkbox" class="asset-component-checkbox" onchange="changeCheckboxStatus(this, 'asset')"></th>
                                                                        <th style="width: 12%;"> Name </th>
                                                                        <th style="width: 12%;"> Available Quantity </th>
                                                                        <th style="width: 12%;"> Quantity </th>
                                                                        <th style="width: 8%;"> Unit </th>
                                                                        <th style="width: 8%;"> Rate </th>
                                                                        <th style="width: 8%;"> GST % </th>
                                                                        <th style="width: 12%;"> CGST Amount </th>
                                                                        <th style="width: 12%;"> SGST Amount </th>
                                                                        <th style="width: 12%;"> Total </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="assetrows">
                                                                    @foreach ($assets as $asset)
                                                                    <tr class="cart-assets">
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input type="hidden" id="inventory_component_id_{{$asset['id']}}" name="inventory_cart[{{$asset['id']}}][inventory_component_id]" class="cart-inventory-component-id" value="{{$asset['inventory_component_id']}}">
                                                                                <input type="hidden" id="{{$asset['id']}}" name="cart_id" class="cart-id" value="{{$asset['id']}}">
                                                                                <input type="checkbox" id="id_{{$asset['id']}}" name="inventory_cart[{{$asset['id']}}][checkbox]" value="{{$asset['id']}}" class="component-checkbox">
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <span>{{$asset['inventory_component']['name']}}</span>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input type="number" class="form-control cart-asset-available-quantity" name="inventory_cart[{{$asset['id']}}][available_quantity]" id="available_quantity_{{$asset['id']}}" value="{{$asset['available_quantity']}}" readonly />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input type="number" class="form-control cart-asset-quantity" name="inventory_cart[{{$asset['id']}}][quantity]" id="current_quantity_{{$asset['id']}}" value="{{$asset['quantity']}}" onchange="checkAllowedQuantity(event, 'asset')" />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input class="form-control unit" value="{{$nosUnit['name']}}" readonly />
                                                                                <input type="hidden" class="form-control unit" id="unit_id_{{$asset['id']}}" name="inventory_cart[{{$asset['id']}}][unit_id]" value="{{$nosUnit['id']}}" readonly />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input type="number" class="form-control cart-asset-rate" id="rate_per_unit_{{$asset['id']}}" name="inventory_cart[{{$asset['id']}}][rate_per_unit]" value="{{$asset['inventory_component']['asset']['rent_per_day']}}" readonly />
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input class="form-control cart-asset-gst" type="number" id="gst_{{$asset['id']}}" name="inventory_cart[{{$asset['id']}}][gst_percent]" value="0" readonly>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input class="form-control cart-cgst_amount" type="number" id="cgst_amount_{{$asset['id']}}" name="inventory_cart[{{$asset['id']}}][cgst_amount]" readonly>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input class="form-control cart-sgst_amount" type="number" id="sgst_amount_{{$asset['id']}}" name="inventory_cart[{{$asset['id']}}][sgst_amount]" readonly>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group" style="width: 80%; margin-left: 10%">
                                                                                <input class="form-control cart-total" type="number" id="total_{{$asset['id']}}" name="inventory_cart[{{$asset['id']}}][total]" readonly>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
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
                                                            <select name="in_client_id" class="form-control clientSelect" onchange="clientChange(this)" id="client_id">
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
                                                            <select name="in_project_id" class="form-control projectSelect" onchange="projectChange(this)" id="project_id">
                                                                <option value="">--Select Project Name--</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-md-2">
                                                            <label class="control-label pull-right">Project Site</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <select name="in_project_site_id" class="form-control projectSiteSelect" id="inv_project_site_id">
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

<script>
    $(document).ready(function() {

        var CreateInventoryComponentTransfer = function() {
            var handleCreate = function() {
                var form = $('#generate_challan');
                var error = $('.alert-danger', form);
                var success = $('.alert-success', form);
                form.validate({
                    errorElement: 'span', //default input error message container
                    errorClass: 'help-block', // default input error message class
                    focusInvalid: false, // do not focus the last invalid input
                    rules: {
                        in_client_id: {
                            required: true
                        },
                        in_project_id: {
                            required: true
                        },
                        in_project_site_id: {
                            required: true
                        },
                        vendor_id: {
                            required: true
                        },
                        transportation_amount: {
                            required: true
                        },
                        driver_name: {
                            required: true
                        },
                        mobile: {
                            required: true
                        },
                        vehicle_number: {
                            required: true
                        }
                    },
                    messages: {
                        in_client_id: {
                            required: "Client is required"
                        },
                        in_project_id: {
                            required: "Project is required"
                        },
                        in_project_site_id: {
                            required: "Project site is required"
                        },
                        vendor_id: {
                            required: "Vendor is required"
                        },
                        transportation_amount: {
                            required: "Transportation amount is required"
                        },
                        driver_name: {
                            required: "Driver name is required"
                        },
                        mobile: {
                            required: "Mobile is required"
                        },
                        vehicle_number: {
                            required: "Vehicle number is required"
                        }
                    },
                    invalidHandler: function(event, validator) { //display error alert on form submit
                        success.hide();
                        error.show();
                    },
                    highlight: function(element) { // hightlight error inputs
                        $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                    },
                    unhighlight: function(element) { // revert the change done by hightlight
                        $(element)
                            .closest('.form-group').removeClass('has-error'); // set error class to the control group
                    },
                    success: function(label) {
                        label
                            .closest('.form-group').addClass('has-success');
                    },
                    submitHandler: function(form) {
                        if ($(".component-checkbox:checkbox:checked").length > 0) {
                            $(".component-checkbox:checkbox:checked").each(function() {

                            })
                            $("button[type='submit']").prop('disabled', true);
                            success.show();
                            error.hide();
                            form.submit();
                        } else {
                            alert("Please select material/asset to generate challan")
                        }

                    }
                });
            }
            return {
                init: function() {
                    handleCreate();
                }
            };
        }();
        CreateInventoryComponentTransfer.init();
        $('.cart-gst').each(function() {
            var id = $(this).closest('.cart-materials').find('.cart-id').attr('id');
            calculateTax(id);
        });
        $('.cart-asset-gst').each(function() {
            var id = $(this).closest('.cart-assets').find('.cart-id').attr('id');
            calculateTax(id);
        });
        $('.cart-quantity, .cart-gst').on('change', function() {
            var id = $(this).closest('.cart-materials').find('.cart-id').attr('id');
            calculateTax(id);
        });

        $('.cart-asset-quantity, .cart-asset-rate, .cart-asset-gst').on('change', function() {
            var id = $(this).closest('.cart-assets').find('.cart-id').attr('id');
            calculateTax(id);
        });

        $('input:checkbox.component-checkbox').click(function() {
            var id = $(this).val();
            var quantity = $('#current_quantity_' + id);
            if ($(this).prop("checked")) {
                $('#current_quantity_' + id).rules('add', {
                    min: 1,
                });
                $('#current_quantity_' + id).valid();

            } else {
                $('#current_quantity_' + id).closest('form-group').removeClass('has-error');
                $('#current_quantity_' + id).rules('remove');
            }
        });
    });

    $(".unit").change(function() {
        // AJAX call to fetch available quantity and then show eerorr
    })
    $('#cart-submit').click(function() {
        var materials = [];
        var assets = [];
        $(".cart-materials").each(function(index, elemnt) {
            let quantity = $(elemnt).find(".cart-quantity").val();
            let unit_id = $(elemnt).find(".unit").val();
            materials.push({
                cart_id: $(elemnt).find(".cart-id").val(),
                quantity,
                unit_id
            })
        })
        $(".cart-assets").each(function(index, elemnt) {
            let quantity = $(elemnt).find(".cart-asset-quantity").val();
            let unit_id = $(elemnt).find(".unit").val();
            assets.push({
                cart_id: $(elemnt).find(".cart-id").val(),
                quantity,
                unit_id
            })
        })
        $.ajax({
            url: '/inventory/transfer/challan/cart/update',
            type: 'POST',
            async: true,
            data: {
                _token: $("input[name='_token']").val(),
                'materials': materials,
                'assets': assets
            },
            success: function(data, textStatus, xhr) {
                if (xhr.status == 200) {
                    alert("Items saved successfully");
                }
            },
            error: function(data, textStatus, xhr) {

            }
        });
    });
    $('#cart-delete').click(function() {
        if ($(".component-checkbox:checkbox:checked").length > 0) {
            var cartIds = [];
            $(".component-checkbox:checkbox:checked").each(function() {
                cartIds.push($(this).val());
            });
            $.ajax({
                url: '/inventory/transfer/challan/cart/delete?_token=' + $("input[name='_token']").val(),
                type: "POST",
                data: {
                    cart_ids: cartIds
                },
                success: function(data, textStatus, xhr) {
                    location.reload();
                    alert("Components deleted successfully from cart");
                },
                error: function(errorData) {
                    alert('Something went wrong.');
                }
            })
        } else {
            alert("Please select components to delete from cart");
        }
    });

    function calculateTax(id) {
        var quantity = $('#current_quantity_' + id).val();
        var rate_per_unit = $('#rate_per_unit_' + id).val();
        var gst = $('#gst_' + id).val();
        if (isNaN(quantity) || quantity == "") {
            quantity = 0;
        }
        if (isNaN(gst) || gst == "") {
            gst = 0;
        }
        if (isNaN(rate_per_unit) || rate_per_unit == "") {
            rate_per_unit = 0;
        }
        var rate = parseFloat(rate_per_unit) * parseFloat(quantity);
        var tax_amount = (parseFloat(gst) * rate) / 100;
        var total = rate + tax_amount;
        $('#cgst_amount_' + id).val(tax_amount.toFixed(2) / 2);
        $('#sgst_amount_' + id).val(tax_amount.toFixed(2) / 2);
        $('#total_' + id).val(total.toFixed(2));
    }

    function clientChange(element) {
        var clientId = $(element).val();
        if (clientId == "") {
            $('.projectSelect').prop('disabled', false);
            $('.projectSelect').html('');
            $('.projectSiteSelect').prop('disabled', false);
            $('.projectSiteSelect').html('');
        } else {
            $.ajax({
                url: '/quotation/get-projects',
                type: 'POST',
                async: true,
                data: {
                    _token: $("input[name='_token']").val(),
                    client_id: clientId
                },
                success: function(data, textStatus, xhr) {
                    $('.projectSelect').html(data);
                    $('.projectSelect').prop('disabled', false);
                    var projectId = $(".projectSelect").val();
                    getProjectSites(projectId);
                },
                error: function() {

                }
            });
        }
    }

    function projectChange(element) {
        var projectId = $(element).val();
        getProjectSites(projectId);
    };

    function getProjectSites(projectId) {
        $.ajax({
            url: '/inventory/get-project-sites',
            type: 'POST',
            async: true,
            data: {
                _token: $("input[name='_token']").val(),
                project_id: projectId
            },
            success: function(data, textStatus, xhr) {
                if (data.length > 0) {
                    $('.projectSiteSelect').html(data);
                    $('.projectSiteSelect').prop('disabled', false);
                } else {
                    $('.projectSiteSelect').html("");
                    $('.projectSiteSelect').prop('disabled', false);
                }
            },
            error: function() {

            }
        });
    }

    function calculateTransportationTaxes(element) {
        var transportationAmount = $('.transportation-amount').val();
        if (typeof transportationAmount == 'undefined' || transportationAmount == '' || isNaN(transportationAmount)) {
            transportationAmount = 0;
        }

        var transportationCGSTPercent = $('.transportation-cgst-percentage').val();
        if (typeof transportationCGSTPercent == 'undefined' || transportationCGSTPercent == '' || isNaN(transportationCGSTPercent)) {
            transportationCGSTPercent = 0;
        }

        var transportationSGSTPercent = $('.transportation-sgst-percentage').val();
        if (typeof transportationSGSTPercent == 'undefined' || transportationSGSTPercent == '' || isNaN(transportationSGSTPercent)) {
            transportationSGSTPercent = 0;
        }

        var transportationIGSTPercent = $('.transportation-igst-percentage').val();
        if (typeof transportationIGSTPercent == 'undefined' || transportationIGSTPercent == '' || isNaN(transportationIGSTPercent)) {
            transportationIGSTPercent = 0;
        }

        var transportationTotalAmount = $('.transportation-total').val();
        if (typeof transportationTotalAmount == 'undefined' || transportationTotalAmount == '' || isNaN(transportationTotalAmount)) {
            transportationTotalAmount = 0;
        }

        var cgstAmount = ((parseFloat(transportationCGSTPercent) * parseFloat(transportationAmount)) / 100).toFixed(3);
        var sgstAmount = ((parseFloat(transportationSGSTPercent) * parseFloat(transportationAmount)) / 100).toFixed(3);
        var igstAmount = ((parseFloat(transportationIGSTPercent) * parseFloat(transportationAmount)) / 100).toFixed(3);
        $('.transportation-cgst-amount').val(cgstAmount);
        $('.transportation-sgst-amount').val(sgstAmount);
        $('.transportation-igst-amount').val(igstAmount);
        var transportationTotal = parseFloat(parseFloat(transportationAmount) + parseFloat(cgstAmount) + parseFloat(sgstAmount) + parseFloat(igstAmount)).toFixed(3);
        $('.transportation-total').val(transportationTotal);
    }

    function changeCheckboxStatus(element, slug) {
        if (slug == 'material') {
            $(".cart-materials .component-checkbox").prop("checked", !$(element).prop("checked")).trigger("click");
        } else {
            $(".cart-assets .component-checkbox").prop("checked", !$(element).prop("checked")).trigger("click");
        }
    }

    function checkAllowedQuantity(event, slug) {
        if (slug == 'material') {
            var id = $(event.target).closest('.cart-materials').find('.cart-id').attr('id');
        } else {
            var id = $(event.target).closest('.cart-assets').find('.cart-id').attr('id');
        }
        $('#current_quantity_' + id).rules('remove');
        $('#current_quantity_' + id).removeClass('has-error');
        var quantity = $('#current_quantity_' + id).val();
        var unitId = $('#unit_id_' + id).val()
        var inventoryComponentId = $('#inventory_component_id_' + id).val();
        if (typeof quantity != 'undefined' && quantity != '' && !(isNaN(quantity)) && typeof unitId != 'undefined' && unitId != '' && !(isNaN(unitId))) {
            $.ajax({
                url: '/inventory/transfer/check-quantity',
                type: 'POST',
                async: true,
                data: {
                    _token: $("input[name='_token']").val(),
                    inventoryComponentId: inventoryComponentId,
                    quantity: quantity,
                    unitId: unitId
                },
                success: function(data, textStatus, xhr) {
                    if (data.show_validation == true) {
                        $('#current_quantity_' + id).rules('add', {
                            max: data.available_quantity,
                            messages: {
                                max: "Available quantity is " + data.available_quantity,
                                min: 1
                            }
                        });
                    }
                    $('#current_quantity_' + id).rules('add', {
                        min: 0,
                    });
                    $('#current_quantity_' + id).valid();
                },
                error: function() {

                }
            });
        } else {
            $('#current_quantity_' + id).rules('add', {
                required: true
            });
        }
    }
</script>
@endsection
