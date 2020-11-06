<div class="row form-group">
    <div class="col-md-3">
        <label class="control-label pull-right">Site Details : </label>
    </div>
    <div class="col-md-6">
        <input class="form-control" type="text" id="siteDetails" value="{{$challan['from_site']}}" readonly>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-3">
        <label class="control-label pull-right">Transportation Amount : </label>
    </div>
    <div class="col-md-6">
        <input class="form-control" type="text" id="transportation_amount" value="{{$challan['other_data']['transportation_amount']}}" readonly>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-3">
        <label class="control-label pull-right">Transportation Tax Amount : </label>
    </div>
    <div class="col-md-6">
        <input class="form-control" type="text" id="transportation_tax_amount" value="{{$challan['other_data']['transportation_tax_total']}}" readonly>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-3">
        <label class="control-label pull-right">Vendor name : </label>
    </div>
    <div class="col-md-6">
        <input class="form-control" type="text" id="vendor_name" value="{{$challan['other_data']['vendor_name']}}" readonly>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-3">
        <label class="control-label pull-right">Driver Name : </label>
    </div>
    <div class="col-md-6">
        <input class="form-control" type="text" id="driver_name" value="{{$challan['other_data']['driver_name']}}" readonly>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-3">
        <label class="control-label pull-right">Mobile : </label>
    </div>
    <div class="col-md-6">
        <input class="form-control" type="text" id="mobile" value="{{$challan['other_data']['mobile']}}" readonly>
    </div>
</div>
<div class="row form-group">
    <div class="col-md-3">
        <label class="control-label pull-right">Vehicle Number : </label>
    </div>
    <div class="col-md-6">
        <input class="form-control" type="text" id="vehicle_number" value="{{$challan['other_data']['vehicle_number']}}" readonly>
    </div>
</div>
<div class="row form-group col-md-offset-2 col-md-10">
    <table class="table table-striped table-bordered table-hover" id="challanComponentTable">
        <thead>
            <tr>
                <th> Material/Asset Name </th>
                <th> Site In GRN </th>
                <th> Quantity </th>
                <th> Unit </th>
            </tr>
        </thead>
        <tbody>
            @foreach($components as $component)
            <tr class="component-row" id="componentRow-{{$component['reference_id']}}">
                <input type="hidden" id="componentRow-{{$component['reference_id']}}-site-out-id" name="component[{{$component['reference_id']}}][site_out_transfer_id]" value="{{$component['site_out_transfer_id']}}">
                <input type="hidden" id="site_out_quantity" name="component[{{$component['reference_id']}}][site_out_quantity]" value="{{$component['site_out_quantity']}}">
                <input type="hidden" id="componentRow-{{$component['reference_id']}}-site-in-id" name="component[{{$component['reference_id']}}][site_in_transfer_id]">
                <td><span> {{$component['name']}}</span></td>
                <td><span id="componentRow-{{$component['reference_id']}}-site-in-grn"> - </span></td>
                <td><input type="number" id="componentRow-{{$component['reference_id']}}-site-in-quantity" name="component[{{$component['reference_id']}}][site_in_quantity]" value="{{$component['site_out_quantity']}}"></td>
                <td><span> {{$component['unit']}} </span></td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>
<!-- <div class="row form-group">
    <div class="col-md-3">
        <label class="control-label pull-right">Remark : </label>
    </div>
    <div class="col-md-6">
        <input class="form-control" type="text" id="remark" name="remark" placeholder="Enter Remark">
    </div>
</div> -->