<div class="form-group">
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
            @foreach($awareness_files as $file)
                <tr id="image-{{$file['id']}}">
                    <td>
                        <a href="{{$file['path']}}" target="_blank" class="fancybox-button" data-rel="fancybox-button">
                            <img class="img-responsive" src="{{$file['path']}}" alt="" style="width:100px; height:100px;"> </a>
                        <input type="hidden" class="product-image-name" name="awareness_files[{{$file['id']}}][image_name]" id="product-image-name-{{$file['id']}}" value="{{$file['path']}}"/>
                    </td>
                    <td>
                        <a href="javascript:;" class="btn btn-default btn-sm" onclick='removeProductImages("#image-{{$file['id']}}","{{$file['path']}}",0);'>
                            <i class="fa fa-times"></i> Remove </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <input type="hidden" id="path" name="path" value="">
        <input type="hidden" id="max_files_count" name="max_files_count" value="20">
    </table>
</div>
<script src="/assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
<script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
<script src="/assets/custom/awareness/file-management/file-datatable.js"></script>
<script src="/assets/custom/awareness/file-management/upload-file.js"></script>