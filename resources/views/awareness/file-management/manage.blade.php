@extends('layout.master')
@section('title','Constro | Manage Materials')
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="page-title">
                                            <h1>Manage Files</h1>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 12px;float: right">
                                        <div class="btn-group"  style="float: right;margin-top:1%">
                                            <div id="sample_editable_1_new" class="btn yellow" ><a href="/awareness/file-management/create" style="color: white">                                         <i class="fa fa-plus"></i>
                                                    Create
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <form action="/awareness/file-management/edit-awareness" method="post">
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet light ">
                                            {!! csrf_field() !!}
                                            <div class="portlet-body">
                                                <div class="portlet-body">
                                                    <div class="row">
                                                         <div class="col-md-6">
                                                             <select class="bs-select form-control" id="mainCategoryId" name="main_category_id" required>
                                                                 <option value="">Select Main Category</option>
                                                                @foreach($main_categories as $category)
                                                                 <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                                @endforeach
                                                             </select>
                                                         </div>
                                                        <div class="col-md-6">
                                                            <select class="bs-select form-control" id="subCategoryId" name="sub_category_id" required>
                                                                <option value="">Select Sub Category</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <br><br>
                                                </div>
                                            </div>
                                                    <div class="row">
                                                        <div class="col-md-12" id="imagesTable">

                                                        </div>
                                                        <div  style="margin-top: 12px;float: right">
                                                            <button type="submit" class="btn btn-set red pull-right">
                                                                <i class="fa fa-check"></i>
                                                                Edit
                                                            </button>
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
        @endsection
        @section('javascript')
            <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
            <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
            <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
            <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
            <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
            <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
            <script src="/assets/custom/awareness/edit.js" type="text/javascript"></script>
            <script src="/assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
            <script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
            <script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
            <script src="/assets/custom/awareness/file-management/file-datatable.js"></script>
            <script src="/assets/custom/awareness/file-management/upload-file.js"></script>
@endsection