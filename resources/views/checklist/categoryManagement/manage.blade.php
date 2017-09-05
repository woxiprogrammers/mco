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
                                    <h1>Category Management</h1>
                                </div>
                                <div id="mainCat" class="btn yellow" style="margin-top: 1%; margin-left: 78%"><a href="#" style="color: white"><i class="fa fa-plus"></i> Main Category</a>
                                </div>
                                <div id="subCat" class="btn yellow" style="margin-top: 1%; margin-left: 87%"><a href="#" style="color: white"><i class="fa fa-plus"></i> Sub Category</a>
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
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="categoryManagementTable">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 30%"> ID </th>
                                                        <th> Category Name </th>
                                                        <th> Sub-Category Name </th>
                                                        <th> Actions </th>
                                                    </tr>
                                                    <tr class="filter">
                                                        <th style="width: 30%"> <input type="text" class="form-control form-filter" name="search_id" readonly> </th>
                                                        <th> <input type="text" class="form-control form-filter" name="search_category" readonly> </th>
                                                        <th> <input type="text" class="form-control form-filter" name="search_subcategory" readonly> </th>
                                                        <th>
                                                            <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                            <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal fade" id="modal1" tabindex="-1" role="dialog" >
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <div class="row">
                                                                <div class="col-md-4"></div>
                                                                <div class="col-md-4"> Create Sub Category</div>
                                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form role="form">
                                                             <div class="form-group">
                                                                 <div class="btn-group bootstrap-select bs-select form-control dropup">
                                                                     <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" title="Afghanistan" aria-expanded="false"><span class="filter-option pull-left">Select Main Category</span>&nbsp;<span class="caret"></span></button>
                                                                     <div class="dropdown-menu open" style="max-height: 314px; overflow: hidden;"><div class="bs-searchbox"><input type="text" class="form-control" autocomplete="off"></div>
                                                                         <ul class="dropdown-menu inner" role="menu" style="max-height: 272px; overflow-y: auto;"><li data-original-index="0" class="selected active"><a tabindex="0" class="" style="" data-tokens="null">
                                                                                     <span class="text">Category 1</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="3"><a tabindex="0" class="" style="" data-tokens="null">
                                                                                     <span class="text">Category 2</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="4"><a tabindex="0" class="" style="" data-tokens="null"></a></li>
                                                                         </ul>
                                                                     </div>
                                                                 </div>
                                                             </div>
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="sub_category" placeholder="Enter Sub Category">
                                                                </div>
                                                                <div class="form-group">
                                                                <button type="submit" class="btn red pull-right"> Create</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="modal2" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <div class="row">
                                                                <div class="col-md-4"></div>
                                                                <div class="col-md-4"> Create Category</div>
                                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body" style="padding:40px 50px;">
                                                            <form role="form">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" id="main_category" placeholder="Enter Main Category">
                                                                </div>
                                                                <button type="submit" class="btn btn-set red pull-right"> Create</button>
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
    <script src="/assets/custom/admin/category-mangement/manage-datatable.js" type="text/javascript"></script>
    <!--<script>
        $(document).ready(function() {
            $('#categoryManagementTable').DataTable();
        });
    </script> -->
    <script>
        $(document).ready(function(){
            $("#subCat").click(function(){
                $("#modal1").modal();
            });
            $("#mainCat").click(function(){
                $("#modal2").modal();
            });
        });
    </script>
@endsection
