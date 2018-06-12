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
                    <form role="form" id="create-image" class="form-horizontal" method="post" action="/dpr/dpr-edit">
                        <!-- BEGIN CONTENT -->
                        <div class="page-content-wrapper">
                            <div class="page-head">
                                <div class="container">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1>Edit DPR Detail</h1>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 12px;float: right">
                                        <button type="submit" class="btn btn-set red pull-right">
                                            <i class="fa fa-check"></i>
                                            Edit
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="page-content">
                                @include('partials.common.messages')
                                <div class="container">
                                    <ul class="page-breadcrumb breadcrumb">
                                        <li>
                                            <a href="/dpr/manage_dpr">Manage DPR</a>
                                            <i class="fa fa-circle"></i>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);">Edit DPR</a>
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
                                                            <label for="name" class="control-label">Sub Contractor</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" value="{{$subcontractorName}}" class="form-control" readonly>
                                                        </div>
                                                    </div>
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
                                                                    @foreach($subcontractorDprDetailData as $subcontractorDprDetail)
                                                                        <tr>
                                                                            <td>
                                                                                {{$subcontractorDprDetail['category_name']}}
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control" name="number_of_users[{{$subcontractorDprDetail['dpr_detail_id']}}]" value="{{$subcontractorDprDetail['number_of_users']}}">
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <input type="hidden" id="path" name="path" value="">
                    <input type="hidden" id="max_files_count" name="max_files_count" value="20">
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script src="/assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
    <script src="/assets/custom/dpr/dpr.js" type="application/javascript"></script>
    <script src="/assets/custom/dpr/file-datatable.js" type="application/javascript"></script>
    <script src="/assets/custom/dpr/upload-file.js" type="application/javascript"></script>
    <script>
        jQuery(document).ready(function() {
            QuotationImageUpload.init()
        });
    </script>
@endsection
