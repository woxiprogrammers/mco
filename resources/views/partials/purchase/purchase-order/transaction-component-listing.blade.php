<?php
/**
 * Created by PhpStorm.
 * User: ameya
 * Date: 19/11/17
 * Time: 2:13 PM
 */
?>

<table class="table table-striped table-bordered table-hover asd" style="margin-top:1%">
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
        <tr style="text-align: center" id="purchaseOrderComponent">
            <td style="width: 40%">
                <input type="text" class="form-control" readonly name="component_data[{{$purchaseOrderComponent['purchase_order_component_id']}}][name]" value="{{$purchaseOrderComponent['name']}}">
            </td>
            <td>
                <select class="form-control unit-select-{{$purchaseOrderComponent['purchase_order_component_id']}}" name="component_data[{{$purchaseOrderComponent['purchase_order_component_id']}}][unit_id]" onchange="checkQuantity({{$purchaseOrderComponent['purchase_order_component_id']}})">
                    @foreach($purchaseOrderComponent['units'] as $unit)
                        <option value="{{$unit['id']}}">{{$unit['name']}}</option>
                    @endforeach
                </select>
            </td>
            <td  class="form-group">
                @if($quantityIsFixed == true)
                    <input type="text" class="form-control" name="component_data[{{$purchaseOrderComponent['purchase_order_component_id']}}][quantity]" value="1" readonly>
                @else
                    <input type="hidden" id="remainingQuantity_{{$purchaseOrderComponent['purchase_order_component_id']}}" value="{{$purchaseOrderComponent['units'][0]['quantity']}}">
                    <input type="text" class="form-control quantity_{{$purchaseOrderComponent['purchase_order_component_id']}}" id="quantity_{{$purchaseOrderComponent['purchase_order_component_id']}}" name="component_data[{{$purchaseOrderComponent['purchase_order_component_id']}}][quantity]" required="required" value="{{$purchaseOrderComponent['units'][0]['quantity']}}" onkeyup="checkQuantity({{$purchaseOrderComponent['purchase_order_component_id']}})">
                @endif
            </td>
        </tr>
    @endforeach
</table>


<script>
    function checkQuantity(purchaseOrderComponentId){
        var baseRemainingQuantity = $('#remainingQuantity_'+purchaseOrderComponentId).val();
        var quantity = $('.quantity_'+purchaseOrderComponentId).val();
        var unitId = $('.unit-select-'+purchaseOrderComponentId).val();
        $.ajax({
                url : '/purchase/purchase-order/transaction/check-quantity?_token='+$("input[name='_token']").val(),
                type : "POST",
                data : {
                    quantity: quantity,
                    unitId : unitId,
                    purchaseOrderComponentId : purchaseOrderComponentId,
                    baseRemainingQuantity : baseRemainingQuantity
                },
                success: function(data,textStatus,xhr){
                    GenerateGRN.init();
                    if(!data.isValid){
                        var name = $("input[name='component_data["+purchaseOrderComponentId+"][name]']").val();
                        $('#quantity_'+purchaseOrderComponentId).rules('add',{
                            required: true,
                            max: data.allowedQuantity,
                        });
                    }else{
                        $('#quantity_'+purchaseOrderComponentId).rules('remove');
                    }
                },
                error : function(errorData){
                    alert('Something went wrong');
                }
            });
    }
</script>
