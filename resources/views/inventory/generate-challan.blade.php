@extends('layout.master')
@section('title','Constro | Generate Challan')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
<input id="nosUnitId" type="hidden" value="{{$nosUnitId}}">
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
                                                    <a href="#" class="btn btn-set yellow pull-right" id="cart-submit" style="margin-left: 10px;" id="assetBtn">
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
                                                                    @foreach ($materials as $material)
                                                                    <tr class="cart-materials">
                                                                        <td>
                                                                            <input type="hidden" id="{{$material['id']}}" name="cart_id" class="cart-id" value="{{$material['id']}}">
                                                                            <input type="checkbox" id="id_{{$material['id']}}" name="inventory_cart[{{$material['id']}}]" value="{{$material['id']}}" class="component-checkbox">
                                                                        </td>
                                                                        <td>
                                                                            <span>{{$material['inventory_component']['name']}}</span>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" max="${element.availQuantity}" class="form-control cart-quantity" name="inventory_cart[{{$material['id']}}][quantity]" id="current_quantity_{{$material['id']}}" value="{{$material['quantity']}}" />
                                                                        </td>
                                                                        <td>
                                                                            <select name="inventory_cart[{{$material['id']}}][unit_id]" class="form-control unit" id="unit_id">
                                                                                @foreach($material['units'] as $unit)
                                                                                @if($material['unit_id'] == $unit['id'])
                                                                                <option value="{{$unit['id']}}" selected>{{$unit['name']}}</option>
                                                                                @else
                                                                                <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                                                                                @endif
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" class="form-control cart-rate" id="rate_per_unit_{{$material['id']}}" name="inventory_cart[{{$material['id']}}][rate_per_unit]" disabled />
                                                                        </td>
                                                                        <td>
                                                                            <input class="form-control cart-gst" type="number" id="gst_{{$material['id']}}" name="inventory_cart[{{$material['id']}}][gst_percent]" disabled>
                                                                        </td>
                                                                        <td>
                                                                            <span class="cart-cgst_amount" id="cgst_amount_{{$material['id']}}"></span>
                                                                        </td>
                                                                        <td>
                                                                            <span class="cart-sgst_amount" id="sgst_amount_{{$material['id']}}"></span>
                                                                        </td>
                                                                        <td>
                                                                            <span class="cart-total" id="total_{{$material['id']}}"></span>
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
<!-- <script src="/assets/custom/inventory/generate-challan.js" type="text/javascript"></script> -->
<script>
    $(document).ready(function() {
        $('.cart-quantity, .cart-rate, .cart-gst').on('change', function() {
            var id = $(this).closest('.cart-materials').find('.cart-id').attr('id');
            calculateTax(id);
        });

        $('input:checkbox.component-checkbox').click(function() {
            var id = $(this).val();
            var quantity = $('#current_quantity_' + id);
            if ($(this).prop("checked") == false) {
                $('#gst_' + id + ',#rate_per_unit_' + id).prop('disabled', true);
                $('#cgst_amount_' + id + ',#sgst_amount_' + id + ',#total_' + id).text('');
                $('#rate_per_unit_' + id + ',#gst_' + id).val('');
            } else {
                $('#gst_' + id + ',#rate_per_unit_' + id).prop('disabled', false);
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
        $.ajax({
            url: '/inventory/transfer/challan/cart',
            type: 'POST',
            async: true,
            data: {
                _token: $("input[name='_token']").val(),
                'materials': materials,
                'assets': assets
            },
            success: function(data, textStatus, xhr) {
                if (xhr.status == 200) {
                    alert("Products saved successfully");
                }
            },
            error: function(data, textStatus, xhr) {

            }
        });
        console.log(arr);
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
        var total = rate + tax_amount + tax_amount;
        $('#cgst_amount_' + id).text(tax_amount.toFixed(2));
        $('#sgst_amount_' + id).text(tax_amount.toFixed(2));
        $('#total_' + id).text(total.toFixed(2));
    }
</script>
@endsection