<?php
    /**
     * Created by PhpStorm.
     * User: sagar
     * Date: 16/1/18
     * Time: 1:58 AM
     */
?>

@foreach($subcontractorDprCategoryRelations as $subcontractorDprCategoryRelation)
    <tr>
        <td>
            {{$subcontractorDprCategoryRelation->dprMainCategory->name}}
        </td>
        <td>
            <input type="text" class="form-control" name="number_of_users[{{$subcontractorDprCategoryRelation->id}}]">
        </td>
    </tr>
@endforeach
