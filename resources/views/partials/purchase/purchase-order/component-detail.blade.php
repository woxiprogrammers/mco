<?php
    /**
     * Created by Harsha.
     * User: Harsha
     * Date: 3/3/18
     * Time: 12:15 PM
     */
?>
<p style="text-align: center">Purchase order ID : <b>{{$purchaseOrder['format_id']}}</b></p>
<table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="purchaseComponentTable">
    <thead>
    <tr id="tableHeader" style="background: #e2e2e2">
        <th width="30%" style="text-align: center"> Material Name </th>
        <th width="15%" class="numeric" style="text-align: center"> Quantity </th>
        <th width="15%" class="numeric" style="text-align: center"> Unit </th>
    </tr>
    </thead>
    <tbody>
    <?php $cnt = 0; $totQty = 0;?>
    @foreach($purchaseOrder->purchaseOrderComponent as $key => $purchaseOrderComponent)
        @if ($cnt%2 == 0)
            <tr style="background: #feff9c">
        @else
            <tr>
        @endif
                <td>
                    <span style="text-transform: capitalize">{{$purchaseOrderComponent->purchaseRequestComponent->materialRequestComponent->name}}</span>
                </td>
                <td>
                    <span>{{$purchaseOrderComponent->quantity}}</span>
                </td>
                <td>
                    <span>{{$purchaseOrderComponent->unit->name}}</span>
                </td>
            </tr>
            <?php $totQty += $purchaseOrderComponent->quantity;$cnt++;?>
    @endforeach
        <tr style="background: #addfff">
            <td style="text-align: right"><b>Total Quantity : </b></td>
            <td>{{$totQty}}</td>
            <td></td>
        </tr>
    </tbody>
</table>
