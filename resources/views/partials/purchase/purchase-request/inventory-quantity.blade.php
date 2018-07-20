<?php
/**
 * Created by PhpStorm.
 * User: ganesh
 * Date: 8/6/18
 * Time: 3:53 PM
 */
?>

<table class="table table-hover table-light" style="overflow-y: scroll; margin-left: 20%; width: 60%">
    <tr style="text-align: center">
        <th style="width: 20%"> Sr. No. </th>
        <th> Project</th>
        <th>Quantity</th>
    </tr>
    @for($iterator = 0; $iterator < count($projectSiteInfo); $iterator++ )
        <tr style="text-align: center">
            <td>{{$iterator + 1}}</td>
            <td> {{$projectSiteInfo[$iterator]['project']}} - {{$projectSiteInfo[$iterator]['project_site']}}</td>
            <td> {{$projectSiteInfo[$iterator]['quantity']}} </td>
        </tr>
    @endfor
</table>