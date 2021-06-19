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
                                                    <!-- <div class="row">
                                                        <div class="col-md-3 pull-right ">
                                                            <form method="Post" action="/inventory/transfer/challen-generation" id="challan">
                                                                <input type="hidden" id="component_transfer_id" value="" name="component_transfer_id">
                                                                <div id="challan_generate" class="btn btn-small blue">
                                                                    <a href="" style="color: white">
                                                                        Challan Generate <i class="fa fa-download" aria-hidden="true"></i>
                                                                    </a>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div> -->
                                                    <table class="table table-striped table-bordered table-hover order-column" id="requestComponentListingTable">
                                                        <thead>
                                                            <tr>
                                                                <th> </th>
                                                                <th>Transaction Date</th>
                                                                <th>Challan Number</th>
                                                                <th>Site Out</th>
                                                                <th>Site In</th>
                                                                <th>Material Name</th>
                                                                <th>Quantity</th>
                                                                <th>Rate/Rent Per Day</th>
                                                                <th>With Tax Amount</th>
                                                                <th>Unit</th>
                                                                <th>Transportation Amount</th>
                                                                <th>GRN Out</th>
                                                                <th>GRN In</th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                            <tr class="filter">
                                                                <th></th>
                                                                <th></th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_challan" id="search_challan"></th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_from" id="search_from"></th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_to" id="search_to"></th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_name" id="search_name"></th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_qty" id="search_qty"></th>
                                                                <th> </th>
                                                                <th> </th>
                                                                <th>
                                                                    <select class="form-control" id="unit_id" name="unit_id">
                                                                        <option value="all">ALL</option>
                                                                        @foreach($units as $unit_status)
                                                                        <option value="{{$unit_status['id']}}">{{$unit_status['name']}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <input type="hidden" class="form-control form-filter" name="unit_status" id="unit_status">
                                                                </th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_amt" id="search_amt"></th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_grn_out" id="search_grn_out"></th>
                                                                <th> <input type="text" class="form-control form-filter" name="search_grn_in" id="search_grn_in"></th>
                                                                <th>
                                                                    <select class="form-control" id="status_id" name="status_id">
                                                                        <option value="all">ALL</option>
                                                                        @foreach($statusData as $status)
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
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/custom/inventory/request-component-datatable.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        RequestComponentListing.init();

        $("#search_challan, #search_to, #search_from, #search_name, #search_qty, #search_amt,#search_grn_out, #search_grn_in").on('keyup', function() {
            if ($("#search_to").val().length > 3 ||
                $("#search_from").val().length > 3 ||
                $("#search_name").val().length > 3 ||
                $("#search_qty").val().length > 0 ||
                $("#search_amt").val().length > 0 ||
                $("#search_grn_out").val().length > 3 ||
                $("#search_grn_in").val().length > 3 ||
                $("#search_challan").val().length > 3
            ) {
                var search_challan = $('#search_challan').val();
                var searchName = $('#search_name').val();
                var searchFrom = $('#search_from').val();
                var searchTo = $('#search_to').val();
                var searchQty = $('#search_qty').val();
                var searchAmt = $('#search_amt').val();
                var searchGrnOut = $('#search_grn_out').val();
                var searchGrnIn = $('#search_grn_in').val();
                var searchUnitStatus = $('#unit_id').val();
                var searchStatus = $('#status_id').val();
                $("input[name='search_name']").val(searchName)
                $("input[name='search_from']").val(searchFrom);
                $("input[name='search_to']").val(searchTo);
                $("input[name='search_amt']").val(searchAmt);
                $("input[name='search_qty']").val(searchQty);
                $("input[name='search_grn_out']").val(searchGrnOut);
                $("input[name='search_grn_in']").val(searchGrnIn);
                $("input[name='unit_status']").val(searchUnitStatus);
                $("input[name='status']").val(searchStatus);
                $("input[name='search_challan']").val(search_challan);
                $(".filter-submit").trigger('click');
            }
        });

        $("#status_id, #unit_id").on('change', function() {
            var search_challan = $('#search_challan').val();
            var searchName = $('#search_name').val();
            var searchFrom = $('#search_from').val();
            var searchTo = $('#search_to').val();
            var searchQty = $('#search_qty').val();
            var searchAmt = $('#search_amt').val();
            var searchGrnOut = $('#search_grn_out').val();
            var searchGrnIn = $('#search_grn_in').val();
            var searchUnitStatus = $('#unit_id').val();
            var searchStatus = $('#status_id').val();
            $("input[name='search_challan']").val(search_challan);
            $("input[name='search_name']").val(searchName)
            $("input[name='search_from']").val(searchFrom);
            $("input[name='search_to']").val(searchTo);
            $("input[name='search_amt']").val(searchAmt);
            $("input[name='search_qty']").val(searchQty);
            $("input[name='search_grn_out']").val(searchGrnOut);
            $("input[name='search_grn_in']").val(searchGrnIn);
            $("input[name='unit_status']").val(searchUnitStatus);
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

    $(document).ready(function() {
        $('#challan_generate').click(function(e) {
            e.preventDefault();
            var val = [];
            var count = 0;
            var valIn = [];
            var valOut = [];
            $(':checkbox:checked').each(function() {
                val[count] = $(this).val();
                var ids = val[count].split("_");
                valOut.push(ids[1]);
                valIn.push(ids[2]);
                count++;
            });
            var uniqueIn = valIn.filter(onlyUnique);
            var uniqueOut = valOut.filter(onlyUnique);
            if (valIn.length <= 0) {
                alert("Please select checkbox to generate challan");
            } else if (uniqueIn.length > 1) {
                alert("Site IN should be same.")
            } else if (uniqueOut.length > 1) {
                alert("Site OUT should be same.")
            } else {
                $('#component_transfer_id').val(val);
                $('#challan').submit();
            }
        });
    });
</script>
@endsection