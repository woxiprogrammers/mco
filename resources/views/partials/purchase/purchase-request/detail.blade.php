<?php
    /**
     * Created by Harsha.
     * User: Harsha
     * Date: 3/3/18
     * Time: 11:29 AM
     */
?>
<p style="text-align: center">Purchase order ID : <b>{{$purchaseRequest['format_id']}}</b></p>
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
            @foreach($purchaseRequest->purchaseRequestComponents as $key => $purchaseRequestComponent)
                @if ($cnt%2 == 0)
                    <tr style="background: #feff9c">
                @else
                    <tr>
                        @endif
                <td>
                    <span style="text-transform: capitalize">{{$purchaseRequestComponent->materialRequestComponent->name}}</span>
                </td>
                <td>
                    <span>{{$purchaseRequestComponent->materialRequestComponent->quantity}}</span>
                </td>
                <td>
                    <span>{{$purchaseRequestComponent->materialRequestComponent->unit->name}}</span>
                </td>
            </tr>
                    <?php $totQty += $purchaseRequestComponent->materialRequestComponent->quantity;$cnt++;?>
                    @endforeach
                    <tr style="background: #addfff">
                        <td style="text-align: right"><b>Total Quantity : </b></td>
                        <td>{{$totQty}}</td>
                        <td></td>
                    </tr>
        </tbody>
    </table>


