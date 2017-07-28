<?php
/**
 * Created by Ameya Joshi.
 * Date: 20/6/17
 * Time: 6:13 PM
 */
?>
    <input type="hidden" name="quotation_product_id" id="quotationProductViewId" value="{{$product['id']}}">
<form role="form" id="editProductForm" class="form-horizontal" action="/quotation/create" method="post">
    {!! csrf_field() !!}
    <input type="hidden" name="product_id[]" value="{{$product['id']}}">
    <input type="hidden" name="project_site_id" id="productViewProjectSiteId">
    <input type="hidden" name="product_quantity[{{$product['id']}}]" id="quotationProductQuantity">

    <div>
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
                    <textarea class="form-control" rows="2" id="description" name="product_description[{{$product['id']}}]">{{$product['description']}}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">Unit</label>
                <div class="col-md-6">
                    <input type="text" name="product_unit" value="{{$product->unit->name}}" class="form-control" readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">Category</label>
                <div class="col-md-6">
                    <input type="hidden" name="category_id" value="{{$product['category_id']}}">
                    <input class="form-control" value="{{$product['category']}}" readonly>
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
                                <input type="hidden" name="material[{{$version['material_id']}}][material_version_id]" value="{{$version['id']}}">
                            </label>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="hidden" name="material_unit[{{$version['material_id']}}]" value="{{$version['unit_id']}}" readonly>
                                <input type="text" class="form-control" name="material_unit_name[{{$version['material_id']}}]" value="{{$version['unit']}}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input class="form-control material-table-input" step="any" type="number" id="material_{{$version['material_id']}}_rate" name="material_rate[{{$version['material_id']}}]" value="{{round($version['rate_per_unit'],3)}}" onkeyup="changedQuantity({{$version['material_id']}})" onchange="changedQuantity({{$version['material_id']}})">
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="number" step="any" class="form-control material-table-input" id="material_{{$version['material_id']}}_quantity" name="material_quantity[{{$version['material_id']}}]" onkeyup="changedQuantity({{$version['material_id']}})" onchange="changedQuantity({{$version['material_id']}})" value="{{round($version['quantity'],3)}}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control material_amount material-table-input" id="material_{{$version['material_id']}}_amount" name="material[{{$version['material_id']}}][amount]" value="{!! round(($version['quantity']*$version['rate_per_unit']),3) !!}">
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
                        <label class="control-label" style="font-weight: bold" id="productViewSubtotal">

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
                                <input class="profit-margin form-control" step="any" type="number" id="profit_margin_{{$profitMargin['id']}}" name="profit_margins[{{$product['id']}}][{{$profitMargin['id']}}]" class="form-control" value="{{$productProfitMargins[$profitMargin['id']]}}" onchange="calculateProfitMargin()" onkeyup="calculateProfitMargin()"required>
                                @else
                                <input class="profit-margin form-control" step="any" type="number" id="profit_margin_{{$profitMargin['id']}}" name="profit_margins[{{$product['id']}}][{{$profitMargin['id']}}]" class="form-control" value="{{$profitMargin['base_percentage']}}" onchange="calculateProfitMargin()" onkeyup="calculateProfitMargin()"required>
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
                            <label class="control-label" style="font-weight: bold; margin-left: 1%" id="productViewTotal">

                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3 col-md-offset-4">
                            <button type="button" class="btn btn-success" onclick="submitProductEdit()"> Submit </button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

</form>