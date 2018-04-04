@extends('layout.master')
@section('title','Constro | Manage Units')
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
                                <h1>Manage Units</h1>
                            </div>

                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-units'))
                                <div id="sample_editable_1_new" class="btn yellow" style="margin-top: 1%; margin-left: 82%">
                                    <a href="/units/create" style="color: white">
                                        <i class="fa fa-plus"></i>Unit
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="page-content">
                        <div class="container">
                            <div class="row">
                                @include('partials.common.messages')
                                <div class="col-md-12">
                                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                    <div class="portlet light ">
                                        <div class="portlet-title">
                                            <div class="caption font-dark">
                                                <i class="icon-settings font-dark"></i>
                                                <span class="caption-subject bold uppercase"> Manage Units</span>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="table-toolbar">
                                                <div class="row" style="text-align: right">
                                                    <div class="col-md-12">
                                                        <div class="btn-group">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {!! csrf_field() !!}
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="unitsTable">
                                                <thead>
                                                <tr>
                                                    <th style="width:30%"> Name </th>
                                                    <th> Status </th>
                                                    <th> Created On </th>
                                                    <th> Actions </th>
                                                </tr>
                                                <tr>
                                                    <th style="width:30%"> <input type="text" class="form-control form-filter" name="search_name"> </th>
                                                    <th> <input type="text" class="form-control form-filter" name="search_status" readonly> </th>
                                                    <th> <input type="text" class="form-control form-filter" name="search_created_on" readonly> </th>
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
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                    <div class="portlet light ">
                                        <div class="portlet-title">
                                            <div class="caption font-dark">
                                                <i class="icon-settings font-dark"></i>
                                                <span class="caption-subject bold uppercase"> Manage Unit Conversions </span>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="table-toolbar">
                                                <div class="row" style="text-align: right">
                                                    <div class="col-md-12">
                                                        <div class="btn-group">
                                                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-units'))
                                                                <div id="sample_editable_1_new" class="btn yellow">
                                                                    <a href="/units/conversion/create" style="color: white"> New
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {!! csrf_field() !!}
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="unitConversionTable">
                                                <thead>
                                                <tr>
                                                    <th style="width:30%"> Unit  </th>
                                                    <th> Value </th>
                                                    <th style="width:30%"> Unit </th>
                                                    <th> Value </th>
                                                    <th style="width: 20%"> Action </th>
                                                </tr>
                                                <tr>
                                                    <th style="width:30%"> <input type="text" class="form-control form-filter" name="search_unit_1_name"> </th>
                                                    <th> <input type="text" class="form-control form-filter" name="search_unit_1_value" readonly> </th>
                                                    <th style="width:30%"> <input type="text" class="form-control form-filter" name="search_unit_2_name"> </th>
                                                    <th> <input type="text" class="form-control form-filter" name="search_unit_2_value" readonly> </th>
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
<script src="/public/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/custom/admin/units/manage-datatable.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#unitsTable').DataTable();
        $('#conversionsTable').DataTable();
    });
</script>
@endsection
