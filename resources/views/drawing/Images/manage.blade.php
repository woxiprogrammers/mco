@extends('layout.master')
@section('title','Constro | Manage Category')
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
                                    <h1>Image Management</h1>
                                </div>
                                <div id="sample_editable_1_new" class="btn yellow" style="margin-top: 1%; margin-left: 77%">
                                    <a href="/drawing/Images/create" style="color: white">
                                        <i class="fa fa-plus"></i> Image
                                    </a>
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
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="manageImageTable">
                                                                <thead>
                                                                <tr>
                                                                    <th style="width: 30%"> Sr. No. </th>
                                                                    <th> Name of Main Category </th>
                                                                    <th> Name of Sub Category </th>
                                                                    <th> Actions </th>
                                                                </tr>
                                                                <tr class="filter">
                                                                    <th style="width: 30%"> <input type="text" class="form-control form-filter" name="search_sr"> </th>
                                                                    <th> <input type="text" class="form-control form-filter" name="search_main" readonly> </th>
                                                                    <th> <input type="text" class="form-control form-filter" name="search_sub" readonly> </th>
                                                                    <th>
                                                                        <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                                        <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td><a href="/drawing/category-management/edit-main">Edit</a></td>
                                                                </tr>
                                                                <tbody>

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
            </div>
        </div>
        @endsection
        @section('javascript')

            <script>
                $(document).ready(function() {

                });
            </script>
@endsection
