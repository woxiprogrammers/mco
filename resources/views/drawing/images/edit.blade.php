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
                                                        <label for="name" class="control-label">Client Name</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="clientId">
                                                            <option value="">Select Client name from here </option>
                                                            @foreach($clients as $client)
                                                                <option value="{{$client['id']}}">{{$client['company']}} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Project Name</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="projectId" >
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
                                                        <select class="form-control" id="projectSiteId" name="site_id">
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
                                                        <select class="form-control" id="main_category_id" >
                                                            <option value="">Select Main Category from here</option>
                                                            @foreach($categories as $category)
                                                                <option value="{{$category['id']}}">{{$category['name']}} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Sub Category</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="sub_category_id" name="drawing_category_id">
                                                            <option value="">Select Sub Category from here</option>
                                                        </select>
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
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <input type="hidden" id="path" name="path" value="">
                                            <input type="hidden" id="max_files_count" name="max_files_count" value="20">
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
    <script src="/assets/custom/admin/drawing/image/image-version-datatable.js"></script>
    <script src="/assets/custom/admin/drawing/image/image-upload.js"></script>
    <script src="/assets/custom/admin/drawing/image/image-version-upload.js"></script>
    <script src="/assets/custom/admin/drawing/image/validation.js" type="application/javascript"></script>
    <script>
        $(document).ready(function(){

            EditImage.init();
        });
    </script>

@endsection