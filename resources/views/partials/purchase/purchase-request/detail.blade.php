<?php
    /**
     * Created by Harsha.
     * User: Harsha
     * Date: 3/3/18
     * Time: 11:29 AM
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
        @foreach($purchaseRequest->purchaseRequestComponents as $key => $purchaseRequestComponent)
            <tr>
                <td>
                    <span>{{$purchaseRequestComponent->materialRequestComponent->name}}</span>
                </td>
                <td>
                    <span>{{$purchaseRequestComponent->materialRequestComponent->quantity}}</span>
                </td>
                <td>
                    <span>{{$purchaseRequestComponent->materialRequestComponent->unit->name}}</span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>


