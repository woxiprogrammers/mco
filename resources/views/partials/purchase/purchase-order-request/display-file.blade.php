@if($forSlug == 'forClient')
    @if($path!=null)
        <tr id="image-{{$random}}">
            <td>
                @if($isPDF == true)
                    <span style="padding-right: 100px"><img src="/assets/global/img/pdf.png" height="30px" width="30px"></span>
                @else
                    <span style="padding-right: 100px"><img src="/assets/global/img/image.png" height="30px" width="30px"></span>
                @endif

                <iframe id="myFrame" style="display:none" width="600" height="300"></iframe>
                <a href="javascript:void(0);" class="btn btn-sm" onclick = "openPdf('{{$random}}','{{$fullPath}}')">Zoom In</a>
                <input type="hidden" class="product-image-name" name="client_images[]" id="product-image-name-{{$random}}" value="{{$path}}"/>
                <a href="javascript:void(0);"  class="btn btn-sm" onclick = "closePdf('{{$random}}','{{$fullPath}}')">Zoom Out</a>
            </td>
            <td>
                <a href="javascript:;" class="btn btn-default btn-sm" onclick='removeProductImages("#image-{{$random}}","{{$path}}",0);'>
                    <i class="fa fa-times"></i> Remove </a>
            </td>
        </tr>
    @endif
@else
    @if($path!=null)
        <tr id="image-{{$random}}">
            <td>
                @if($isPDF == true)
                    <span style="padding-right: 100px"><img src="/assets/global/img/pdf.png" height="30px" width="30px"></span>
                @else
                    <span style="padding-right: 100px"><img src="/assets/global/img/image.png" height="30px" width="30px"></span>
                @endif

                <iframe id="myFrame" style="display:none" width="600" height="300"></iframe>
                <a href="javascript:void(0);" class="btn btn-sm" onclick = "openPdf('{{$random}}','{{$fullPath}}')">Zoom In</a>
                <input type="hidden" class="product-image-name" name="vendor_images[]" id="product-image-name-{{$random}}" value="{{$path}}"/>
                <a href="javascript:void(0);"  class="btn btn-sm" onclick = "closePdf('{{$random}}','{{$fullPath}}')">Zoom Out</a>
            </td>
            <td>
                <a href="javascript:;" class="btn btn-default btn-sm" onclick='removeProductImages("#image-{{$random}}","{{$path}}",0);'>
                    <i class="fa fa-times"></i> Remove </a>
            </td>
        </tr>
    @endif
@endif

<script>
    function openPdf(random,fullPath){
        $("#image-"+random+" #myFrame").attr('src',fullPath);
        $("#image-"+random+" #myFrame").show();
    }
    function closePdf(random,fullPath){
        $("#image-"+random+" #myFrame").hide();
    }

</script>