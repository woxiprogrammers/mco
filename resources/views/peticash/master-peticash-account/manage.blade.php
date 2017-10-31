@extends('layout.master')
@section('title','Constro | Manage Master Peticash Account')
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
                                <h1>Manage Master Peticash Account</h1>
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
                                                <div class="row" style="text-align: center">
                                                    <div class="col-md-4" style="background-color: #c2c2c2">
                                                        Total Peticash Amount : {{$masteraccountAmount}}
                                                    </div>
                                                    <div class="col-md-3" style="background-color: #e2e2e2">
                                                        Allocated Peticash : {{$sitewiseaccountAmount}}
                                                    </div>
                                                    <div class="col-md-3" style="background-color: #c2c2c2">
                                                        Balance : {{$balance}}
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="btn-group">
                                                            <div id="sample_editable_1_new" class="btn yellow" ><a href="createpage" style="color: white"> ADD
                                                                    <!-- here we need to handle create transaction for master account-->
                                                                    <i class="fa fa-plus"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="masterPeticashTable">
                                                <thead>
                                                <tr>
                                                    <th> Transaction Id </th>
                                                    <th> From </th>
                                                    <th> To </th>
                                                    <th> Amount </th>
                                                    <th> Type </th>
                                                    <th style="width: 30%"> Remark </th>
                                                    <th> Created On </th>
                                                    <th> Status </th>
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
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/custom/peticash/peticash.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        masterAccountListing.init();
        $('#masterPeticashTable').DataTable();
    });
</script>
@endsection
