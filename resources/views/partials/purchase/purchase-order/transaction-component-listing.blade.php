<?php
/**
 * Created by PhpStorm.
 * User: ameya
 * Date: 19/11/17
 * Time: 2:13 PM
 */
?>

<table class="table table-striped table-bordered table-hover" style="margin-top:1%">
    <tr style="text-align: center">
        <th style="width: 40%">
            Name
        </th>
        <th>
            Unit
        </th>
        <th>
            Quantity
        </th>
    </tr>
    @foreach($purchaseOrderComponentData as $purchaseOrderComponent)
        <tr style="text-align: center">
            <td style="width: 40%">
                <input type="text" class="form-control" readonly name="component_data[{{$purchaseOrderComponent['purchase_order_component_id']}}][name]" value="{{$purchaseOrderComponent['name']}}">
            </td>
            <td>
                <select class="form-control" name="component_data[{{$purchaseOrderComponent['purchase_order_component_id']}}][unit_id]">
                    <option value="">-- Select Unit --</option>
                    @foreach($purchaseOrderComponent['units'] as $unit)
                        @if($purchaseOrderComponent['unit_id'] == $unit['id'])
                            <option value="{{$unit['id']}}" selected>{{$unit['name']}}</option>
                        @else
                            <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                        @endif
                    @endforeach
                </select>
            </td>
            <td>
                @if($quantityIsFixed == true)
                    <input type="text" class="form-control" name="component_data[{{$purchaseOrderComponent['purchase_order_component_id']}}][quantity]" value="1" readonly>
                @else
                    <input type="text" class="form-control" name="component_data[{{$purchaseOrderComponent['purchase_order_component_id']}}][quantity]">
                @endif
            </td>
        </tr>
    @endforeach
</table>
