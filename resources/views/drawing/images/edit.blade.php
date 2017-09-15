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
                                    <h1>Add Image</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/drawing/Image/manage">Manage Image</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Add Image</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">

                                        <div class="portlet-body form">
                                            <form role="form" id="edit-image" class="form-horizontal" method="post" action="">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Client Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="client_name" name="client_name">
                                                                <option value="">Select Client name from here </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Project Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="project_name" name="project_name">
                                                                <option value="">Select Project Name from here</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Site Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="site_name" name="site_name">
                                                                <option value="">Select Site Name from here</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Main Category</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="main_category" name="main_category">
                                                                <option value="">Select Main Category from here</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Sub Category</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="sub_category" name="sub_category">
                                                                <option value="">Select Sub Category from here</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Add Image :</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Image Title</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="image_title" name="image_title">
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
                                                            <tr id="image-">
                                                                <td>
                                                                    <a href="" target="_blank" class="fancybox-button" data-rel="fancybox-button">
                                                                        <img class="img-responsive" src="" alt="" style="width:100px; height:100px;"> </a>
                                                                    <input type="hidden" class="work-order-image-name" name="work_order_images[$image->id][image_name]" id="work-order-image-" value=""/>
                                                                </td>
                                                                <td>
                                                                    <a href="javascript:;" class="btn btn-default btn-sm" onclick='removeProductImages("#image");'>
                                                                        <i class="fa fa-times"></i> Remove </a>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                        {{-- <div class="form-group">
                                                             <button type="submit" class="btn btn-success" style="margin-left: 40%; margin-top:3%">
                                                                 Submit
                                                             </button>
                                                         </div>--}}
                                                        <button id="version" style="margin-left: 26%"><a href="#"><i class="fa fa-plus"></i> New Version</a>
                                                        </button>
                                                        <div class="modal fade" id="modal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content" style="height: 35%">
                                                                    <div class="modal-header" style="padding-bottom: 10%">
                                                                        <div class="row">
                                                                            <div class="col-md-4"></div>
                                                                            <div class="col-md-7" style="margin-left:-11%"> Add New Version</div>
                                                                            <div class="col-md-1" style="margin-left:9%"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form role="form" id="new_version">
                                                                            <div class="form-group">
                                                                                <div class="col-md-3" style="text-align: right">
                                                                                    <label for="name" class="control-label">Image Title</label>
                                                                                    <span>*</span>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="text" class="form-control" id="image_title" name="image_title">
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12"> </div>
                                                                            </div>
                                                                            <div id="tab_images_uploader_container" class="col-md-offset-5" style="margin-left: 57%">
                                                                                <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow" style="margin-left: -70%">
                                                                                    Browse</a>
                                                                                <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                                                    <i class="fa fa-share"></i> Upload Files </a>
                                                                            </div>
                                                                            <button type="submit" id="createNewVersion" class="btn red pull-right"><i class="fa fa-check"></i> Create </button>
                                                                        </form>
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
    <script src="/assets/custom/admin/drawing/image/image-upload.js"></script>
    <script src="/assets/custom/admin/drawing/image/validation.js" type="application/javascript"></script>
    <script>
        $(document).ready(function(){
            $("#version").click(function(){
                $("#modal").modal('toggle');
            });
            EditImage.init();
        });
    </script>

@endsection
