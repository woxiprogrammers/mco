@extends('layout.master')
@section('title','Constro | Create Bill')
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
                                <h1>Create Bill</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/bill/manage">Manage Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Create Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-11">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                        <div class="portlet-body flip-scroll">
                                            <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll" id="createBillTable">
                                                <thead class="flip-content">
                                                <tr>
                                                    <th> BOQ Item no </th>
                                                    <th width="90%"> Item Description </th>
                                                    <th width="40%" class="numeric"> UOM </th>
                                                    <th width="40%" class="numeric"> Rate </th>
                                                    <th width="40%" class="numeric"> BOQ Quantity </th>
                                                    <th width="40%" class="numeric"> W.O Amount </th>
                                                    <th width="40%" class="numeric"> Prev. Quantity </th>
                                                    <th width="40%" class="numeric"> Current Quantity </th>
                                                    <th width="40%" class="numeric"> Cumulative Quantity </th>
                                                    <th width="40%" class="numeric"> Prev. Bill Amount </th>
                                                    <th width="40%" class="numeric"> Current Bill Amount </th>
                                                    <th width="40%" class="numeric"> Cumulative Bill Amount </th>

                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td> AAC </td>
                                                    <td> AUSTRALIAN AGRICULTURAL COMPANY LIMITED. </td>
                                                    <td class="numeric"> &nbsp; </td>
                                                    <td class="numeric"> -0.01 </td>
                                                    <td class="numeric"> -0.36% </td>
                                                    <td class="numeric"> $1.39 </td>
                                                    <td class="numeric"> $1.39 </td>
                                                    <td class="numeric"> &nbsp; </td>
                                                    <td class="numeric"> 9,395 </td>
                                                    <td class="numeric"> 9,395 </td>
                                                    <td class="numeric"> 9,395 </td>
                                                    <td class="numeric"> 9,395 </td>
                                                </tr>
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
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/custom/bill/bill-manage-datatable.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
    console.log({{$project_site['id']}});
    $('#createBillTable').DataTable();
});
</script>
@endsection




