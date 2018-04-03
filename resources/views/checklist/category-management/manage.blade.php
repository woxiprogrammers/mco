@extends('layout.master')
@section('title','Constro | Checklist Category Management')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>

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
                                    <h1>Checklist Category Management</h1>
                                </div>
                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-checklist-category'))
                                    <div class="col-md-offset-8" style="margin-top: 1%">
                                        <a href="#" style="color: white" data-toggle="modal" data-target="#categoryModal" class="btn red"><i class="fa fa-plus"></i> Main Category</a>
                                        <a href="#" style="color: white" class="btn red" data-toggle="modal" data-target="#subcategoryModal"><i class="fa fa-plus"></i> Sub Category</a>
                                    </div>
                                @endif
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
                                                <div class="table-toolbar">
                                                    <ul class="nav nav-tabs nav-tabs-lg">
                                                        <li class="active">
                                                            <a href="#categorytab" data-toggle="tab"> Category </a>
                                                        </li>
                                                        <li>
                                                            <a href="#subcategorytab" data-toggle="tab"> Sub Category </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="tab-content">
                                            <div class="tab-pane fade in active" id="categorytab">
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="MainCategoryManagementTable">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 30%"> ID </th>
                                                        <th> Category Name </th>
                                                        <th> Status </th>
                                                        <th> Created On </th>
                                                        <th> Actions </th>
                                                    </tr>
                                                    <tr class="filter">
                                                        <th style="width: 30%"> <input type="text" class="form-control form-filter" name="search_id" readonly> </th>
                                                        <th> <input type="text" class="form-control form-filter" name="search_category" readonly> </th>
                                                        <th> <input type="text" class="form-control form-filter" name="search_subcategory" readonly> </th>
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

                                            <div class="tab-pane fade in" id="subcategorytab">
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="SubCategoryManagementTable">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 30%"> ID </th>
                                                        <th> Category Name </th>
                                                        <th> Sub-Category Name </th>
                                                        <th> Status </th>
                                                        <th> Created On </th>
                                                        <th> Actions </th>
                                                    </tr>
                                                    <tr class="filter">
                                                        <th style="width: 30%"> <input type="text" class="form-control form-filter" name="search_id" readonly> </th>
                                                        <th> <input type="text" class="form-control form-filter" name="search_category" readonly> </th>
                                                        <th> <input type="text" class="form-control form-filter" name="search_subcategory" readonly> </th>
                                                        <th> <input type="text" class="form-control form-filter" name="search_subcategory" readonly> </th>
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
                                        </div>
                                        </div>
                                        <div class="modal fade" id="subcategoryModal" tabindex="-1" role="dialog" >
                                                <div class="modal-dialog">
                                                    <div class="modal-content" style="height: 50%">
                                                        <div class="modal-header">
                                                            <div class="row">
                                                                <div class="col-md-4"></div>
                                                                <div class="col-md-7" style="margin-left:-11%"> Create Sub Category</div>
                                                                <div class="col-md-1" style="margin-left:9%"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form role="form" id="SubCategoryCreate" action="/checklist/category-management/create/sub-category" method="post">
                                                                {!! csrf_field() !!}
                                                             <div class="form-group">
                                                                 <select class="form-control" id="mainCategorySelect" name="category_id">
                                                                     @foreach($categories as $category)
                                                                         <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                                     @endforeach
                                                                 </select>
                                                             </div>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" id="sub_category" name="name" placeholder="Enter Sub Category">
                                                            </div>
                                                            <button type="submit" id="createSubCategory" class="btn red pull-right"><i class="fa fa-check"></i> Create </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal fade" id="categoryModal" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="modal-content" style="height: 50%">
                                                        <div class="modal-header" >
                                                            <div class="row">
                                                                <div class="col-md-4"></div>
                                                                <div class="col-md-6" style="margin-left:-8%"> Create Category</div>
                                                                <div class="col-md-2" style="margin-left:8%"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body" style="padding:40px 50px;">
                                                            <form role="form" id="MainCategoryCreate" action="/checklist/category-management/create/main-category" method="post">
                                                                {!! csrf_field() !!}
                                                                <div class="form-group">
                                                                    <input type="text" name="name" class="form-control" id="main_category" placeholder="Enter Main Category" required>
                                                                </div>
                                                                <button type="submit"  id="createMainCategory" class="btn btn-set red pull-right"><i class="fa fa-check"></i> Create </button>
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
    <script src="/assets/custom/checklist/main-category-management-datatable.js" type="text/javascript"></script>
    <script src="/assets/custom/checklist/sub-category-management-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            CreateMainCategory.init();
        });
        $(document).ready(function() {
            CreateSubCategory.init();
        });
    </script>
@endsection
