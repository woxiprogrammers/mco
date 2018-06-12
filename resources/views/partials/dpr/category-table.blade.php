<?php
    /**
     * Created by PhpStorm.
     * User: sagar
     * Date: 16/1/18
     * Time: 1:58 AM
     */
?>

<div class="form-group row">
    <div class="col-md-6 col-md-offset-3" style="text-align: right">
        <table class="table table-bordered" id="categoryTable">
            <thead>
            <tr>
                <th style="width: 50%">
                    Category
                </th>
                <th>
                    Number of labours
                </th>
            </tr>
            </thead>
            <tbody>
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
            </tbody>
        </table>
    </div>
</div>
<div class="form-group" id="imageUploadDiv">
    <div class="row">
        <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12"> </div>
    </div>
    <div id="tab_images_uploader_container" class="col-md-offset-5">
        <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow">
            Browse</a>
        <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
            <i class="fa fa-share"></i> Upload Files </a>
    </div><br>
    <table class="table table-bordered table-hover col-md-offset-3" style="width: 700px">
        <thead>
        <tr role="row" class="heading">
            <th> Image </th>
            <th> Action </th>
        </tr>
        </thead>
        <tbody id="show-product-images">
            @foreach($subcontractorCategoryImages as $subcontractorCategoryImage)
                <tr id="image-{{$subcontractorCategoryImage['random']}}">
                    <td>
                        <a href="{{$subcontractorCategoryImage['path']}}" target="_blank" class="fancybox-button" data-rel="fancybox-button">
                            <img class="img-responsive" src="{{$subcontractorCategoryImage['path']}}" alt="" style="width:100px; height:100px;"> </a>
                    </td>
                    <td>
                        <a href="javascript:;" class="btn btn-default btn-sm" onclick='removeDprImages("#image-{{$subcontractorCategoryImage['random']}}","{{$subcontractorCategoryImage['path']}}",{{$subcontractorCategoryImage['dpr_image_id']}});'>
                            <i class="fa fa-times"></i> Remove </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

