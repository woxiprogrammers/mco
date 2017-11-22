@extends('layout.master')
@section('title','Constro | Manage Subcontractor Structure')
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
                                <h1>Manage Subcontractor Structure</h1>
                            </div>
                            <div class="btn-group" style="float: right;margin-top:1%">
                                <div id="sample_editable_1_new" class="btn yellow" ><a href="/subcontractor/subcontractor-structure/create" style="color: white"> Subcontractor Structure
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
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
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <label>Select Client :</label>
                                                    <select class="form-control" id="client_id" name="client_id">
                                                        <option value="0">ALL</option>
                                                        @foreach($clients as $client)
                                                        <option value="{{$client['id']}}">{{$client['company']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Select Project :</label>
                                                    <select class="form-control" id="project_id" name="project_id">
                                                        <option value="0">ALL</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Select Site :</label>
                                                    <select class="form-control" id="site_id" name="site_id">
                                                        <option value="0">ALL</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Select Subcontractor :</label>
                                                    <select class="form-control" id="year" name="subcontractor">
                                                        <option value="0">ALL</option>
                                                        <option value="2017">SC1</option>
                                                        <option value="2018">SC2</option>
                                                        <option value="2019">SC3</option>
                                                        <option value="2020">SC4</option>
                                                        <option value="2021">Sc5</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-1">
                                                    <label>&nbsp;</label>
                                                    <div class="btn-group">
                                                        <div id="search-withfilter" class="btn blue" >
                                                            <a href="#" style="color: white"> Submit
                                                                <i class="fa fa-plus"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                </div>
                                            </div>
                                            <hr/>
                                            <table class="table table-striped table-bordered table-hover" id="subcontractorStructureTable">
                                                <thead>
                                                <tr>
                                                    <th> Employee Id </th>
                                                    <th> Employee Name </th>
                                                    <th> Contact No </th>
                                                    <th> Per Day wages </th>
                                                    <th> Project Site </th>
                                                    <th> Status </th>
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
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/custom/subcontractor/subcontractor.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#subcontractorStructureTable').DataTable();
    });
</script>
@endsection
