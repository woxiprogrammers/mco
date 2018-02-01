<?php
    /**
     * Created by PhpStorm.
     * User: sagar
     * Date: 16/1/18
     * Time: 1:58 AM
     */
?>

@foreach($subcontractorCategoryData as $subcontractorCategoryInfo)
    <tr>
        <td>
            {{$subcontractorCategoryInfo['dpr_main_category_name']}}
        </td>
        <td>
            @if(array_key_exists('number_of_users',$subcontractorCategoryInfo))
                <input type="text" class="form-control" name="number_of_users[{{$subcontractorCategoryInfo['subcontractor_dpr_category_relation_id']}}]" value="{{$subcontractorCategoryInfo['number_of_users']}}">
            @else
                <input type="text" class="form-control" name="number_of_users[{{$subcontractorCategoryInfo['subcontractor_dpr_category_relation_id']}}]">
            @endif
        </td>
    </tr>
@endforeach
