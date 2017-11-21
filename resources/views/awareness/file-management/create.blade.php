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
                    <form action="/awareness/file-management/create-awareness" method="POST">
                    <!-- BEGIN CONTENT -->
                    <div class="page-content-wrapper">
                        <div class="page-head">
                            <div class="container">
                                <!-- BEGIN PAGE TITLE -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="page-title">
                                            <h1>Create</h1>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 12px;float: right">
                                        <div class="btn-group"  style="float: right;margin-top:1%">
                                            <input id="sample_editable_1_new" type="submit" class="btn yellow" ><i class="fa fa-check"></i>
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
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet light ">
                                            {!! csrf_field() !!}
                                            <div class="portlet-body">
                                                <div class="portlet-body">
                                                    <div class="form-body">
                                                        <div class="form-group row has-success">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="name" class="control-label">Select Main category</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select name="main_category_id" id="main_category_id" class="form-control" aria-required="true" aria-invalid="false" >
                                                                   <option>Select</option>
                                                                    @foreach($main_categories as $main_category)
                                                                    <option value="{{$main_category['id']}}"> {{$main_category['name']}} </option>
                                                                   @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row has-success">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="name" class="control-label">Select Sub category</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select name="sub_category_sub" id="sub_category_sub" class="form-control" aria-required="true" aria-invalid="false" >

                                                                </select>
                                                            </div>
                                                        </div>
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

                                                                </tbody>
                                                            </table>
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
                </form>
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
            <script src="/assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
            <script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
            <script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
            <script src="/assets/custom/awareness/file-management/file-datatable.js"></script>
            <script src="/assets/custom/awareness/file-management/upload-file.js"></script>

            <script>
                $('#main_category_id').change(function(){
                    var main_category_id = $(this).val();
                    $.ajax({
                        url:'/awareness/file-management/get-sub-categories/'+main_category_id+'?_token='+$('input[name="_token"]').val(),
                        type: 'GET',
                        success: function(data,textStatus,xhr){
                            $('#sub_category_sub').html(data);
                        },
                        error: function(errorData){
                        }
                    });
                })

            </script>
@endsection