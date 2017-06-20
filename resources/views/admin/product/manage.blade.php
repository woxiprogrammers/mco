@extends('layout.master')
@section('title','Constro | Manage Product')
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
                                <h1>Manage Product</h1>
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
                                        <div class="portlet-body">
                                            <div class="table-toolbar">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="btn-group">
                                                            <div id="sample_editable_1_new" class="btn sbold green"><a href="/product/create"> Add New Product
                                                                    <i class="fa fa-plus"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {!! csrf_field() !!}
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="productTable">
                                                <thead>
                                                <tr>
                                                    <th style="width: 25%"> Product Name </th>
                                                    <th style="width: 25%"> Category Name </th>
                                                    <th> Unit </th>
                                                    <th> Rate </th>
                                                    <th> Status </th>
                                                    <th> Actions </th>
                                                </tr>
                                                <tr>
                                                    <th style="width: 25%"> <input class="form-control form-filter" name="search_product_name"></th>
                                                    <th style="width: 25%"> <input class="form-control form-filter" name="search_category_name" readonly> </th>
                                                    <th> <input class="form-control form-filter" name="search_unit" readonly> </th>
                                                    <th> <input class="form-control form-filter" name="search_rate" readonly> </th>
                                                    <th> <input class="form-control form-filter" name="search_status" readonly> </th>
                                                    <th>
                                                        <button class="btn-primary btn-sm filter-submit"> Search </button> <br>
                                                        <button class="btn-default filter-cancel"> Reset </button>
                                                    </th>
                                                </tr>
                                                </thead>
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
</div>
@endsection

@section('javascript')
<script src="/assets/custom/product/product.js" type="application/javascript"></script>
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/custom/admin/product/manage-datatable.js" type="text/javascript"></script>

@endsection
