@extends('layout.master')
@section('title','Constro | Manage Tax')
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
                                <h1>Manage Tax</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                    <div class="portlet light ">
                                        <div class="portlet-title">
                                            <div class="caption font-dark">
                                                <i class="icon-settings font-dark"></i>
                                                <span class="caption-subject bold uppercase"> Managed Table</span>
                                            </div>
                                        </div>
                                        {!! csrf_field() !!}
                                        <div class="portlet-body">
                                            <div class="table-toolbar">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="btn-group">
                                                            <div id="sample_editable_1_new" class="btn sbold green"><a href="/tax/create"> Add New Tax
                                                                    <i class="fa fa-plus"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="taxTable">
                                                <thead>
                                                <tr>
                                                    <th> Name </th>
                                                    <th> Percentage </th>
                                                    <th> Status </th>
                                                    <th> Created On </th>
                                                    <th> Actions </th>
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
<script src="/assets/custom/admin/tax.js" type="application/javascript"></script>
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/custom/admin/tax/manage-datatable.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#taxTable').DataTable();
    });
</script>
@endsection
