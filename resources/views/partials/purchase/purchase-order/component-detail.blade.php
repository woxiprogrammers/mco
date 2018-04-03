<?php
    /**
     * Created by Harsha.
     * User: Harsha
     * Date: 3/3/18
     * Time: 12:15 PM
     */
?>

<table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="purchaseComponentTable">
    <thead>
    <tr id="tableHeader">
        <th width="30%" style="text-align: center"> Material Name </th>
        <th width="15%" class="numeric" style="text-align: center"> Quantity </th>
        <th width="15%" class="numeric" style="text-align: center"> Unit </th>
    </tr>
    </thead>
    <tbody>
    @foreach($purchaseOrder->purchaseOrderComponent as $key => $purchaseOrderComponent)
    <tr>
        <td>
            <span>{{$purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name}}</span>
        </td>
        <td>
            <span>{{$purchaseOrderComponent->quantity}}</span>
        </td>
        <td>
            <span>{{$purchaseOrderComponent->unit->name}}</span>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
