@extends('layout.master')
@section('title','Constro | Create Main Category')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="page-wrapper-row full-height">
            <div class="page-wrapper-middle">
                <!-- BEGIN CONTAINER -->
                <div class="page-container">
                    <!-- BEGIN CONTENT -->
                    <div class="page-content-wrapper">
                        <div class="page-head">
                            <div class="container">
                                <!-- BEGIN PAGE TITLE -->
                                <div class="page-title">
                                    <h1>Edit Image</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/drawing/images/manage">Manage Image</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Edit Image</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">

                                        <div class="portlet-body form">
                                            {!! csrf_field() !!}
                                            <div class="form-body">
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Main Category</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" value="{{$main_category['name']}}" class="form-control" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Sub Category</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" value="{{$sub_category['name']}}" class="form-control" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Add Image :</label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12"> </div>
                                                    </div>
                                                    <div id="tab_images_uploader_container" class="col-md-offset-5" style="margin-left: 57%">
                                                        <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow">
                                                            Browse</a>
                                                        <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                            <i class="fa fa-share"></i> Upload Files </a>
                                                    </div>
                                                    <table class="table table-bordered table-hover" style="width: 613px; margin-top: 1%; margin-left: 26%">
                                                        <thead>
                                                        <tr role="row" class="heading">
                                                            <th> Image </th>
                                                            <th> Action </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="show-product-images">
                                                        @foreach($drawing_image_latest_version as $file)
                                                            <tr id="image-{{$file['id']}}">
                                                                <td>
                                                                    <a href="{{$file['encoded_name']}}" target="_blank" class="fancybox-button" data-rel="fancybox-button">
                                                                        <img class="img-responsive" src="{{$file['encoded_name']}}" alt="" style="width:100px; height:100px;"> </a>

                                                                    <input type="hidden" class="product-image-name" name="work_order_images[{{$file['id']}}][image_name]" id="product-image-name-{{$file['id']}}" value="{{$file['encoded_name']}}"/>
                                                                    <input type="text"  name="work_order_images[{{$file['id']}}][title]" value="{{$file['title']}}" required/>
                                                                </td>
                                                                <td>
                                                                    <a href="javascript:;" class="btn btn-default btn-sm" onclick='removeProductImages("#image-{{$file['id']}}","{{$file['encoded_name']}}",0);'>
                                                                        <i class="fa fa-times"></i> Remove </a>
                                                                    <button  class="btn btn-default btn-sm myBtn"  value="{{$file['original_id']}}">
                                                                        <i class="fa fa-plus-square"></i> Add Version </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <input type="hidden" id="path" name="path" value="">
                                            <input type="hidden" id="max_files_count" name="max_files_count" value="20">
                                            <div class="modal fade" id="myModal" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <div class="row">
                                                                <div class="col-md-4"></div>
                                                                <div class="col-md-4"> Material</div>
                                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body" style="padding:40px 50px;">
                                                            <form action="/drawing/images/add-version" method="POST" enctype="multipart/form-data">
                                                                {!! csrf_field() !!}
                                                                <input type="hidden" id="drawing-images-id" name="drawing_images_id">
                                                                <input type="hidden"  name="site_id" value="{{$site_id}}">
                                                                <input type="hidden"  name="sub_category_id" value="{{$id}}">
                                                                <div class="form-group row">
                                                                    <div class="col-md-3" style="text-align: right">
                                                                        <label for="name" class="control-label">Browse File</label>
                                                                        <span>*</span>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="file" name="file" required>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-md-3" style="text-align: right">
                                                                        <label for="name" class="control-label">Title</label>
                                                                        <span>*</span>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" name="title" required>
                                                                    </div>
                                                                </div>
                                                                <input type="submit" class="btn red pull-right" >
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
    <script src="/assets/custom/admin/drawing/image/image-datatable.js"></script>
    {{--<script src="/assets/custom/admin/drawing/image/image-version-datatable.js"></script>--}}
    <script src="/assets/custom/admin/drawing/image/image-upload.js"></script>
    {{--<script src="/assets/custom/admin/drawing/image/image-version-upload.js"></script>--}}
    <script src="/assets/custom/admin/drawing/image/validation.js" type="application/javascript"></script>
    <script src="/assets/custom/Drawing/drawing.js" type="application/javascript"></script>
    <script>
        $(document).ready(function(){
            EditImage.init();
            $(".myBtn").click(function(){
                var id = $(this).val();
                $('#drawing-images-id').val(id);
                $("#myModal").modal();
            });
        });
    </script>

@endsection