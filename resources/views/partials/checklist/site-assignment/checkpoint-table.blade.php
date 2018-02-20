<?php
/**
 * Created by Ameya Joshi.
 * Date: 9/12/17
 * Time: 1:44 PM
 */
?>
<fieldset>
    <legend>Checkpoints</legend>
    <div class="table-container">
        <table class="table table-striped table-bordered table-hover order-column" id="inventoryListingTable">
            <thead>
            <tr>
                <th></th>
                <th>Description</th>
                <th>Is Remark Required</th>
                <th>No. of Images</th>
            </tr>
            </thead>
            <tbody>
                @foreach($checklistCategory->checkpoints as $checkpoint)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{$checkpoint['id']}}" name="checkpoint_ids[]">
                        </td>
                        <td>
                            {{$checkpoint['description']}}
                        </td>
                        <td>
                            @if($checkpoint['is_remark_required'] == true)
                                    Yes
                            @else
                                    No
                            @endif
                        </td>
                        <td>
                            {!! count($checkpoint->checklistCheckpointsImages) !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</fieldset>
