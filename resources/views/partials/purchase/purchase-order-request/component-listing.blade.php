<?php
/**
 * Created by Ameya Joshi.
 * Date: 16/1/18
 * Time: 2:30 PM
 */
?>

@foreach($purchaseRequestComponentData as $purchaseRequestComponent)
    <tr class="component-row" id="componentRow-{{$purchaseRequestComponent['vendor_relation_id']}}">
        <td style="width: 12%"><input type="hidden" name="component_vendor_relations[{{$purchaseRequestComponent['purchase_request_component_id']}}][]" class="component-vendor-relation" value="{{$purchaseRequestComponent['vendor_relation_id']}}"><span> {{$purchaseRequestComponent['vendor_name']}} </span></td>
        <td style="width: 15%"><span> {{$purchaseRequestComponent['name']}} </span></td>
        <td style="width: 10%"><span class="quantity"> {{$purchaseRequestComponent['quantity']}} </span></td>
        <td style="width: 10%;"><span > {{$purchaseRequestComponent['unit']}} </span></td>
        <td style="width: 10%"><span class="rate-without-tax">{!!  \App\Helper\MaterialProductHelper::customRound($purchaseRequestComponent['rate_per_unit']) !!} </span></td>
        <td style="width: 10%"><span class="rate-with-tax"> {!!  \App\Helper\MaterialProductHelper::customRound($purchaseRequestComponent['rate_per_unit']) !!} </span></td>
        @if($purchaseRequestComponent['is_client'] == true)
            <td style="width: 10%"><span class="total-with-tax"> - </span></td>
        @else
            <td style="width: 10%"><span class="total-with-tax"> {!! \App\Helper\MaterialProductHelper::customRound($purchaseRequestComponent['quantity'] * $purchaseRequestComponent['rate_per_unit']) !!} </span></td>
        @endif
        <td style="width: 10%">
            <a class="btn blue" href="javascript:void(0);" onclick="openDetailsModal(this,{{$purchaseRequestComponent['vendor_relation_id']}})">
                Add Details
            </a>
        </td>
    </tr>
@endforeach
