@extends('layout.master')
@section('title','Constro | View Subcontractor Structure')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="/assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css" />
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
                                    <h1>View Subcontractor Structure</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container" style="width: 100%">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/subcontractor/subcontractor-structure/manage">Manage Subcontractor Structure</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">View Subcontractor Structure</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <div class="form-body">
                                                <div class="row form-group">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="description" class="control-label">Subcontractor : </label>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span>{!! $subcontractorStructure->subcontractor->subcontractor_name !!}</span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="description" class="control-label">Description : </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <span>{!! $subcontractorStructure->description !!}</span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="summary_id" class="control-label">Summary : </label>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span>{!! $subcontractorStructure->summary->name !!}</span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="rate" class="control-label">Rate :</label>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span>{!! $subcontractorStructure->rate !!}</span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="total_work_area" class="control-label">Total Work Area (Sq.Ft) :</label>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span>{!! $subcontractorStructure->total_work_area !!}</span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="total_amount" class="control-label">Total Amount : </label>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span>{!! $subcontractorStructure->total_work_area * $subcontractorStructure->rate !!}</span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="structure_type" class="control-label">Structure Type :</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <span>{!! $subcontractorStructure->contractType->name !!}</span>
                                                    </div>
                                                </div>
                                                @if($subcontractorStructure->contractType->slug == 'amountwise')
                                                    <div class="form-group row" id="no_of_floor">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="no_of_floors" class="control-label">No of Floors : </label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <span>{!! $noOfFloors !!}</span>
                                                        </div>
                                                    </div>
                                                @endif
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
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script><script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
@endsection
