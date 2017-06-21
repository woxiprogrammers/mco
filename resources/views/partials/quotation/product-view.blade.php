<?php
/**
 * Created by Ameya Joshi.
 * Date: 20/6/17
 * Time: 6:13 PM
 */
?>

<fieldset>
    <legend> General Information </legend>
    <div class="form-group">
        <label class="col-md-3 control-label">Product Title</label>
        <div class="col-md-6">
            <input type="text" id="name" name="name" class="form-control" value="{{$product['name']}}">
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Description</label>
        <div class="col-md-6">
            <textarea class="form-control" rows="2" id="description" name="description">{{$product['description']}}</textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Unit</label>
        <div class="col-md-6">
            <select class="form-control" id="unit_id" name="unit_id">
                @foreach($units as $unit)
                @if($unit['id'] == $product['unit_id'])
                <option value="{{$unit['id']}}" selected>{{$unit['name']}}</option>
                @else
                <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                @endif
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Category</label>
        <div class="col-md-6">
            <input type="hidden" name="category_id" value="{{$product['category_id']}}">
            <input class="form-control" value="{{$product['category']}}" readonly>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Material</label>
        <div class="col-md-6">
            <select class="form-control" id="material_id" multiple="true" style="overflow: scroll">
                @foreach($materials as $material)
                @if(in_array($material['id'],$productMaterialIds))
                <option value="{{$material['id']}}" selected> {{$material['name']}}</option>
                @else
                <option value="{{$material['id']}}"> {{$material['name']}}</option>
                @endif
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-9">
            <a class="btn btn-success btn-md" id="next_btn"> Add </a>
        </div>
    </div>
    <input type="hidden" name="material_version_ids" value="{{$materialVersionIds}}">
</fieldset>
<div class="materials-table-div">
    <fieldset>
        <legend> Materials</legend>
        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="productMaterialTable">
            <tr>
                <th style="width: 25%"> Name </th>
                <th> Unit </th>
                <th> Rate </th>
                <th> Quantity </th>
                <th> Amount </th>
            </tr>
            @foreach($productMaterialVersions as $version)
            <tr>
                <td>
                    <label>
                        {{$version['name']}}
                    </label>
                </td>
                <td>
                    <div class="form-group">
                        <input type="hidden" name="unit_{{$version['material_id']}}" value="{{$version['unit_id']}}">
                        <select class="form-control material-table-input" name="material_version[{{$version['id']}}][unit_id]"  id="material_{{$version['material_id']}}_unit" onchange="convertUnits({{$version['material_id']}})">
                            @foreach($units as $unit)
                            @if($unit['id'] == $version['unit_id'])
                            <option value="{{$unit['id']}}" selected>{{$unit['name']}}</option>
                            @else
                            <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input name="rate_{{$version['material_id']}}" value="{{round($version['rate_per_unit'],3)}}" type="hidden">
                        <input class="form-control material-table-input" step="any" type="number" id="material_{{$version['material_id']}}_rate" name="material_version[{{$version['id']}}][rate_per_unit]" value="{{round($version['rate_per_unit'],3)}}" onkeyup="changedQuantity({{$version['material_id']}})" onchange="changedQuantity({{$version['material_id']}})">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input type="number" step="any" class="form-control material-table-input" id="material_{{$version['material_id']}}_quantity" name="material_quantity[{{$version['id']}}]" onkeyup="changedQuantity({{$version['material_id']}})" onchange="changedQuantity({{$version['material_id']}})" value="{{round($version['quantity'],3)}}" required>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input type="text" class="form-control material_amount material-table-input" id="material_{{$version['material_id']}}_amount" name="material_amount[{{$version['id']}}]" value="{!! round(($version['quantity']*$version['rate_per_unit']),3) !!}">
                    </div>
                </td>
            </tr>
            @endforeach
        </table>
        <div class="col-md-offset-7">
            <div class="col-md-3 col-md-offset-3">
                <label class="control-label" style="font-weight: bold">
                    Sub Total:
                </label>
            </div>
            <div class="col-md-2">
                <label class="control-label" style="font-weight: bold" id="subtotal">

                </label>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend> Profit Margins </legend>
        <div class="form-body">
            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="productMaterialTable">
                <tr>
                    <th style="width: 33%"> Profit Margin Name </th>
                    <th style="width: 46%"> Percentage </th>
                    <th style="width: 33%"> Amount </th>
                </tr>
                @foreach($profitMargins as $profitMargin)
                <tr>
                    <td>
                        {{$profitMargin['name']}}
                    </td>
                    <td>
                        @if(isset($productProfitMargins[$profitMargin['id']]))
                        <input class="profit-margin form-control" step="any" type="number" id="profit_margin_{{$profitMargin['id']}}" name="profit_margin[{{$profitMargin['id']}}]" class="form-control" value="{{$productProfitMargins[$profitMargin['id']]}}" onchange="calculateProfitMargin()" onkeyup="calculateProfitMargin()"required>
                        @else
                        <input class="profit-margin form-control" step="any" type="number" id="profit_margin_{{$profitMargin['id']}}" name="profit_margin[{{$profitMargin['id']}}]" class="form-control" value="{{$profitMargin['base_percentage']}}" onchange="calculateProfitMargin()" onkeyup="calculateProfitMargin()"required>
                        @endif
                    </td>
                    <td class="profit-margin-amount">

                    </td>
                </tr>
                @endforeach
            </table>
            <div class="col-md-offset-7">
                <div class="col-md-3 col-md-offset-3" style="align-items: ">
                    <label class="control-label" style="font-weight: bold; text-align: right">
                        Total:
                    </label>
                </div>
                <div class="col-md-2">
                    <label class="control-label" style="font-weight: bold; margin-left: 1%" id="total">

                    </label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-3 col-md-offset-4">
                    <button type="submit" class="btn btn-success"> Submit </button>
                </div>
            </div>
        </div>
    </fieldset>
</div>