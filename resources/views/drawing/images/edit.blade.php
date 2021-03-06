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
                    <form action="/drawing/images/edit" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="project_site_id" value="{{$site['id']}}">
                        <input type="hidden" name="main_category_id" value="{{$main_category['id']}}">
                        <input type="hidden" name="sub_category_id" value="{{$sub_category['id']}}">
                        <div class="page-content-wrapper">
                            <div class="page-head">
                                <div class="container">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1>Edit Image</h1>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 12px;float: right">
                                        <button type="submit" class="btn btn-set red pull-right">
                                            <i class="fa fa-check"></i>
                                            Submit
                                        </button>
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
                                                                    @if(pathinfo($file['encoded_name'], PATHINFO_EXTENSION) == 'dwg' || pathinfo($file['encoded_name'], PATHINFO_EXTENSION) == 'DWG')
                                                                        <a href="javascript:void(0);" onclick="showVersions({{$file['id']}})">
                                                                            <img class="img-responsive" src="/assets/global/img/dwg_thumbnail.jpg" alt="" style="width:100px; height:100px;">
                                                                        </a>
                                                                    @else
                                                                        <a href="javascript:void(0);" onclick="showVersions({{$file['id']}})">
                                                                            <img class="img-responsive" src="{{$file['encoded_name']}}" alt="" style="width:100px; height:100px;">
                                                                        </a>
                                                                    @endif
                                                                    <input type="hidden" class="product-image-name" id="product-image-name-{{$file['id']}}" value="{{$file['encoded_name']}}"/><br>
                                                                    <input type="text"  class="form-control" value="{{$file['title']}}" />
                                                                </td>
                                                                <td>
                                                                    {{--<a href="javascript:;" class="btn btn-default btn-sm" onclick='removeProductImages("#image-{{$file['id']}}","{{$file['encoded_name']}}",0);'>
                                                                        <i class="fa fa-times"></i> Remove </a>--}}
                                                                    <a  class="btn btn-default btn-sm myBtn"  href="javascript:void(0);" onclick="openVersionModal({{$file['original_id']}})">
                                                                        <i class="fa fa-plus-square"></i> Add Version </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
                        @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('edit-add-drawing')|| $user->customHasPermission('create-add-drawing'))
                            <input type="submit" class="btn red pull-right" >
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="versionModal" role="dialog">
        <div class="modal-dialog" style="width: 70%">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4"> <h3><b>Drawing Image Versions</b></h3> </div>
                        <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                    </div>
                </div>
                <div class="modal-body" style="padding:40px 50px;">

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
        });
        function openVersionModal(drawingImageId){
            $('#drawing-images-id').val(drawingImageId);
            $("#myModal").modal();
        }
        function showVersions(drawingVersionId){
            $("#versionModal").modal('show');
            $.ajax({
                url: '/drawing/images/get-version-images?_token='+$("input[name='_token']").val(),
                type: "POST",
                data:{
                    image_version_id: drawingVersionId
                },
                success: function(data,textStatus,xhr){
                    $("#versionModal .modal-body").html(data);
                },
                error: function(errorData){

                }
            })
        }
    </script>

@endsection