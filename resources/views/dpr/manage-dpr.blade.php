@extends('layout.master')
@section('title','Constro | Manage Category')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />

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
                                <div id="sample_editable_1_new" class="btn yellow pull-right" style="margin-top: 1%; ">
                                    <a href="/dpr/create-dpr-view" style="color: white">
                                        <i class="fa fa-plus"></i> DPR
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
                                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="dprTable">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 10%"> No. </th>
                                                            <th> Subcontractor </th>
                                                            <th style="width:30%;"> Date </th>
                                                            <th> Action </th>
                                                        </tr>
                                                        <tr class="filter">
                                                            <th style="width: 10%">  </th>
                                                            <th> <input type="text" class="form-control form-filter" name="search_main_category" readonly> </th>
                                                            <th>
                                                                <div class="date date-picker">
                                                                    <input type="text"  class="form-filter" name="search_date" placeholder="Select Date" id="date" readonly/>
                                                                    <button class="btn btn-sm default" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>
                                                                </div>
                                                            </th>
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
    </div>
@endsection
@section('javascript')
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/dpr/dpr-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#dprTable').DataTable();
        });
        function submitEditForm(element){
            $(element).closest("form").submit();
        }
    </script>
@endsection
