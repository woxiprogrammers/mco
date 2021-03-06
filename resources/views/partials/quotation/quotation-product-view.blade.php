<?php
/**
 * Created by Ameya Joshi.
 * Date: 17/7/17
 * Time: 3:19 PM
 */ ?>
<input type="hidden" name="quotation_product_id" id="quotationProductViewId" value="{{$quotationProduct['product_id']}}">
<form role="form" id="editProductForm" class="form-horizontal" action="/quotation/create" method="post">
    {!! csrf_field() !!}
    <input type="hidden" name="product_id[]" value="{{$quotationProduct['product_id']}}">
    <input type="hidden" name="project_site_id" id="productViewProjectSiteId">
    <input type="hidden" name="product_quantity[{{$quotationProduct->product_id}}]" id="quotationProductQuantity">
    <div>
        <fieldset>
            <legend> General Information </legend>
            <div class="form-group">

                <label class="col-md-3 control-label">Product Title</label>
                <div class="col-md-6">
                    <input type="text" id="name" name="name" class="form-control" value="{{$quotationProduct->product->name}}">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">Description</label>
                <div class="col-md-6">

                    <textarea class="form-control" rows="2" id="description" name="product_description[{{$quotationProduct->product_id}}]">{{$quotationProduct->description}}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">Unit</label>
                <div class="col-md-6">
                    <input type="text" name="product_unit" value="{{$quotationProduct->product->unit->name}}" class="form-control" readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">Category</label>
                <div class="col-md-6">
                    <input class="form-control" value="{{$quotationProduct->product->category->name}}" readonly>
                </div>
            </div>
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
                        @foreach($quotationProduct->quotation_profit_margins as $profitMargin)
                            <tr>
                                <td>
                                    {{$profitMargin->profit_margin->name}}
                                </td>
                                <td>
                                    <input class="profit-margin form-control" step="any" type="number" id="profit_margin_{{$profitMargin['id']}}" name="profit_margins[{{$quotationProduct->product_id}}][{{$profitMargin['profit_margin_id']}}]" value="{{$profitMargin->percentage}}" onchange="calculateProfitMargin()" onkeyup="calculateProfitMargin()"required>
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
                            @if($canUpdateProduct == true)
                                <button type="button" class="btn red" onclick="submitProductEdit()" id="submit"><i class="fa fa-check"></i> Submit </button>
                            @endif
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    @if($canUpdateProduct == false)
                        <div class="form-group">
                            <div class="col-md-7 col-md-offset-2">
                                <label class="control-label" style="color: red">You can not edit this product. Either you are unauthorised or already a bill is created for this product.</label>
                            </div>
                        </div>
                    @endif
                </div>
            </fieldset>
        </div>
    </div>

</form>