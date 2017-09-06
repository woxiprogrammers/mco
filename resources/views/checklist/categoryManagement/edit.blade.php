@extends('layout.master')
@section('title','Constro | Category Management')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <!--<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>-->

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
                                    <h1>Edit Category Management</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet light ">
                                            {!! csrf_field() !!}
                                            <div class="portlet-body">
                                                <div class="table-toolbar">
                                                    <div class="row" style="text-align: right">
                                                        <div class="col-md-12">
                                                            <div class="btn-group">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button class="btn yellow" id="mainCat" ><a href="#">Edit Main Category</a></button>
                                                <button class="btn yellow"  id="subCat" style="margin-left: 2%"><a href="#">Edit Sub Category</a></button>
                                            </div>
                                            <div class="modal fade" id="modal1" tabindex="-1" role="dialog" >
                                                <div class="modal-dialog">
                                                    <div class="modal-content" style="height: 50%">
                                                        <div class="modal-header">
                                                            <div class="row">
                                                                <div class="col-md-4"></div>
                                                                <div class="col-md-6"> Edit Sub Category</div>
                                                                <div class="col-md-2"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form role="form" id="SubCategoryEdit">
                                                                <div class="form-group">
                                                                    <input type="text" name="sname_main" class="form-control" id="main_category" placeholder="Enter Main Category">
                                                                </div>
                                                                <div class="form-group">
                                                                    <input type="text" name="sname_sub" class="form-control" id="sub_category"   placeholder="Name of Sub Category">
                                                                </div>
                                                                    <button type="submit"  id="editMainCategory" class="btn red pull-right" style="margin-right: 45%"><a href="manage"> Submit </a></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="modal2" tabindex="-1" role="dialog" >
                                                <div class="modal-dialog">
                                                    <div class="modal-content" style="height: 40%">
                                                        <div class="modal-header">
                                                            <div class="row">
                                                                <div class="col-md-4"></div>
                                                                <div class="col-md-6"> Edit Main Category</div>
                                                                <div class="col-md-2"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form role="form" id="MainCategoryEdit">
                                                                <div class="form-group">
                                                                    <input type="text" name="mname_main" class="form-control" id="main_category" placeholder="Enter Main Category">
                                                                </div>
                                                                    <button  id="editMainCategory" class="btn red pull-right" style="margin-right: 45%"> <a href="manage"> Submit</a> </button>
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
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <link rel="stylesheet"  href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css"/>
    <link rel="stylesheet"  href="/assets/global/css/app.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/checklist/categoryManagement.js"></script>
    <script>
        $(document).ready(function(){
            $("#subCat").click(function(){
                $("#modal1").modal();
            });
            $("#mainCat").click(function(){
                $("#modal2").modal();
            });
            $(document).ready(function() {
                EditMainCategory.init();
            });
            $(document).ready(function() {
                EditSubCategory.init();
            });
        });


    </script>
@endsection
