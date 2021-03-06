@extends('layout.master')
@section('title','Constro | Manage Challan')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->

<link href="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
<style>
    #icon-text {
        display: none;
        position: absolute;
    }

    .icon-button:hover+#icon-text {
        position: relative;
        display: inline-block;
        border-bottom: 1px dotted black;
    }
</style>
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
                                <h1>Manage Challan</h1>
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
                                                <div class="row">
                                                    <div class="col-md-offset-11 col-md-2">
                                                        <div class="btn-group">
                                                            <a class="btn red pull-right" href="/inventory/transfer/challan/site/in" id="siteIn">
                                                                <i class="fa fa-check"></i>Site In
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr />
                                                <div class="table-container">
                                                    <table class="table table-striped table-bordered table-hover order-column" id="challanListTable">
                                                        <thead>
                                                            <tr>
                                                                <th width="15%">Transaction Date</th>
                                                                <th width="15%">Challan Number</th>
                                                                <th width="25%">Site Out</th>
                                                                <th width="20%">Site In</th>
                                                                <th width="5%">Transportation Amount</th>
                                                                <th width="5%">Transportation Tax Amount</th>
                                                                <th width="5%">Status</th>
                                                                <th width="10%">Action</th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th>
                                                                    <div class="input-group input-large date-picker input-daterange" data-date-format="yyyy-mm-dd">
                                                                        <input type="text" style="margin-left: 30%;width: 70%" class="form-control form-filter" name="start_date" id="start_date" required="required">
                                                                        <span class="input-group-addon" style="margin-left: 0%;width: 10%"> to </span>
                                                                        <input type="text" style="margin-left: 0%;width: 70%" class="form-control form-filter" name="end_date" id="end_date" required="required"> </div>
                                                                </th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_challan" id="search_challan"></th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_site_from" id="search_site_from"></th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_site_to" id="search_site_to"></th>
                                                                <th> </th>
                                                                <th> </th>
                                                                <th>
                                                                    <select class="form-control" id="status_id" name="status_id">
                                                                        <option value="all">ALL</option>
                                                                        @foreach($challanStatus as $status)
                                                                        <option value="{{$status['id']}}">{{$status['name']}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <input type="hidden" class="form-control form-filter" name="status" id="status">
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
</div>
@endsection
@section('javascript')
<link rel="stylesheet" href="/assets/global/plugins/datatables/datatables.min.css" />
<script src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<script src="/assets/custom/inventory/challan/manage-datatable.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        ChallanList.init();

        $("#search_site_to, #search_site_from, #search_challan").on('keyup', function() {
            if ($("#search_site_to").val().length > 3 ||
                $("#search_site_from").val().length > 3 ||
                $("#search_challan").val().length > 3
            ) {
                var searchChallan = $('#search_challan').val();
                var searchFrom = $('#search_site_from').val();
                var searchTo = $('#search_site_to').val();
                var searchStatus = $('#status_id').val();
                $("input[name='search_challan']").val(searchChallan)
                $("input[name='search_site_from']").val(searchFrom);
                $("input[name='search_site_to']").val(searchTo);

                $("input[name='status']").val(searchStatus);
                $(".filter-submit").trigger('click');
            }
        });

        $("#status_id").on('change', function() {
            var searchChallan = $('#search_challan').val();
            var searchFrom = $('#search_site_from').val();
            var searchTo = $('#search_site_to').val();
            var searchStatus = $('#status_id').val();
            $("input[name='search_challan']").val(searchChallan)
            $("input[name='search_site_from']").val(searchFrom);
            $("input[name='search_site_to']").val(searchTo);
            $("input[name='status']").val(searchStatus);
            $(".filter-submit").trigger('click');
        });

    });

    function changeStatus(element) {
        var token = $('input[name="_token"]').val();
        $(element).next('input[name="_token"]').val(token);
        $(element).closest('form').submit();
    }

    function onlyUnique(value, index, self) {
        return self.indexOf(value) === index;
    }
</script>
@endsection