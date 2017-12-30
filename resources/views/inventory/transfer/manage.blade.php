@extends('layout.master')
@section('title','Constro | Manage Transfers')
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
                                    <h1>Manage Requested Transfer</h1>
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
                                                    <div class="table-container">
                                                        <table class="table table-striped table-bordered table-hover order-column" id="requestComponentListingTable">
                                                            <thead>
                                                            <tr>
                                                                <th> Project Site From</th>
                                                                <th> Project Site To</th>
                                                                <th> Material Name </th>
                                                                <th> Quantity</th>
                                                                <th> Unit </th>
                                                                <th> Status </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th> <input type="text" class="form-control form-filter" name="search_project" readonly> </th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_name" readonly> </th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_status" readonly> </th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_created_on" readonly> </th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_created_on" readonly> </th>
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
    <script src="/assets/custom/inventory/request-component-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            RequestComponentListing.init();
        });

        function changeStatus(element){
            var token = $('input[name="_token"]').val();
            $(element).next('input[name="_token"]').val(token);
            $(element).closest('form').submit();
        }
    </script>
@endsection
