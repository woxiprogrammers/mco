<?php
/**
 * Created by Ameya Joshi.
 * Date: 20/6/17
 * Time: 6:13 PM
 */
?>

<fieldset>
    <legend style="font-size: 18px"> General Information </legend>
    <div class="form-group">
        <label class="col-md-3 control-label">Product Title</label>
        <label class="control-label">{{$product['name']}}</label>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Description</label>
        <label>{!!$product['description']!!} </label>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Unit</label>
        <label class="control-label"> {{$product->unit->name}} </label>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Category</label>
        <label class="control-label">{{$product['category']}} </label>
    </div>
</fieldset>
<div class="materials-table-div">
    <fieldset>
        <legend style="font-size: 18px"> Materials</legend>
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
                    <label class="control-label"> {{$product->unit->name}} </label>
                </td>
                <td>
                    <label class="control-label"> {{round($version['rate_per_unit'],3)}} </label>
                </td>
                <td>
                    <label class="control-label"> {{round($version['quantity'],3)}} </label>
                </td>
                <td>
                    <label class="control-label material-amount"> {!! round(($version['quantity']*$version['rate_per_unit']),3) !!} </label>
                </td>
            </tr>
            @endforeach
        </table>
        <div class="col-md-offset-7">
            <div class="col-md-5">
                <label class="control-label" style="font-weight: bold">
                    Sub Total:
                </label>
            </div>
            <div class="col-md-2">
                <label class="control-label" style="font-weight: bold" id="productViewSubtotal">

                </label>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend style="font-size: 18px"> Profit Margins </legend>
        <div class="form-body">
            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="productMaterialTable">
                <tr>
                    <th style="width: 33%"> Profit Margin Name </th>
                    <th style="width: 20%"> Percentage </th>
                    <th style="width: 33%"> Amount </th>
                </tr>
                @foreach($profitMargins as $profitMargin)
                <tr>
                    <td>
                        {{$profitMargin['name']}}
                    </td>
                    <td class="profit-margin-percentage">
                        {{$productProfitMargins[$profitMargin['id']]}}
                    </td>
                    <td class="profit-margin-amount">

                    </td>
                </tr>
                @endforeach
            </table>
            <div class="col-md-offset-7">
                <div class="col-md-5" style="align-items: ">
                    <label class="control-label" style="font-weight: bold; text-align: right">
                        Total:
                    </label>
                </div>
                <div class="col-md-2">
                    <label class="control-label" style="font-weight: bold; margin-left: 1%" id="total">

                    </label>
                </div>
            </div>
        </div>
    </fieldset>
</div>