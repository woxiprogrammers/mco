@if($path!=null)
    <tr id="image-{{$random}}">
        <td>
            @if(pathinfo($path, PATHINFO_EXTENSION) == 'dwg' || pathinfo($path, PATHINFO_EXTENSION) == 'DWG')
                <a href="{{$path}}" target="_blank" class="fancybox-button" data-rel="fancybox-button">
                    <img class="img-responsive" src="/assets/global/img/dwg_thumbnail.jpg" alt="" style="width:100px; height:100px;"> </a>

            @else
                <a href="{{$path}}" target="_blank" class="fancybox-button" data-rel="fancybox-button">
                    <img class="img-responsive" src="{{$path}}" alt="" style="width:100px; height:100px;"> </a>
            @endif
            <input type="hidden" class="product-image-name" name="work_order_images[{{$random}}][image_name]" id="product-image-name-{{$random}}" value="{{$path}}"/><br>
            <input type="text"  name="work_order_images[{{$random}}][title]" required class="form-control"/>
        </td>
        <td>
            <a href="javascript:;" class="btn btn-default btn-sm" onclick='removeProductImages("#image-{{$random}}","{{$path}}",0);'>
                <i class="fa fa-times"></i> Remove </a>
        </td>
    </tr>
@endif
