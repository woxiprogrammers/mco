<?php
/**
 * Created by PhpStorm.
 * User: ameya
 * Date: 12/6/18
 * Time: 6:03 PM
 */
?>

@if(count($imageVersionData) > 0)<table class="table table-bordered table-hover">
    <thead>
    <tr role="row" class="heading">
        <th style="width: 50% !important;"> Image </th>
        <th> Title </th>
    </tr>
    </thead>
    <tbody id="show-product-images">
        @foreach($imageVersionData as $imageVersion)
            <tr>
                <td>
                    @if(pathinfo($imageVersion['image_path'], PATHINFO_EXTENSION) == 'dwg' || pathinfo($imageVersion['image_path'], PATHINFO_EXTENSION) == 'DWG')
                        <a href="{{$imageVersion['image_path']}}">
                            <img class="img-responsive" src="/assets/global/img/dwg_thumbnail.jpg" alt="" style="width:100px; height:100px;">
                        </a>
                    @else
                        <a href="{{$imageVersion['image_path']}}">
                            <img src="{{$imageVersion['image_path']}}" style="width: 250px; height: 150px">
                        </a>
                    @endif
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control" value="{{$imageVersion['title']}}">
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endif
