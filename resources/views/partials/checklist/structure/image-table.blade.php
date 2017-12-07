<?php
/**
 * Created by Ameya Joshi.
 * Date: 7/12/17
 * Time: 5:51 PM
 */
?>

<table class="table table-striped table-bordered table-hover table-checkable order-column">
    <tr>
        <th>
            No.
        </th>
        <th>
            Caption
        </th>
        <th>
            Is Required
        </th>
    </tr>
    @for($iterator = 0; $iterator < $noOfImages; $iterator++)
        <tr>
            <td>
                {!! $iterator+1 !!}.
            </td>
            <td>
                <input type="text" class="form-control" name="checkpoints[{{$index}}][images][{{$iterator}}][caption]">
            </td>
            <td>
                <select class="form-control" name="checkpoints[{{$index}}][images][{{$iterator}}][is_required]">
                    <option value="true">Yes</option>
                    <option value="false">No</option>
                </select>
            </td>
        </tr>
    @endfor
</table>
