<?php
/**
 * Created by Ameya Joshi.
 * Date: 5/12/17
 * Time: 7:10 PM
 */
?>
@if()
    @foreach($materialRequestList as $components)
        <tr>
            <td> <input type="checkbox"> <input type="hidden" name="material_request_component_ids[]" value="{{$components['material_request_component_id']}}"></td>
            <td> <input type="text" value="{{$components['name']}}" readonly> </td>
            <td> <input type="text" value="{{$components['quantity']}}" readonly> </td>
            <td> <input type="text" value="{{$components['unit']}}" readonly> </td>
            <td>
                <div class="btn-group open">
                    <a class="btn btn-xs green dropdown-toggle deleteRowButton" href="javascript:void(0);" onclick="removeTableRow(this)">
                        Remove
                    </a>
                </div>
            </td>
        </tr>
    @endforeach
@endif
