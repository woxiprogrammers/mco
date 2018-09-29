<?php
    /**
     * Created by Harsha.
     * User: harsha
     * Date: 5/9/18
     * Time: 6:00 PM
     */?>

@extends('layout.master')
@section('title','Constro | Reports')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />

    <link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

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
                                    <h1>Reports Management</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            {!! csrf_field() !!}
                                            <div class="form-body">
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Report Type : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="report_type" name="report_type" onchange="getDivData()">
                                                            <option value="sitewise_indirect_expenses_report">Indirect Expenses Report</option>
                                                            <option value="sitewise_mis_purchase_report">Mis. Purchase Report</option>
                                                            <option value="sitewise_purchase_report">Purchase Report</option>
                                                            <option value="sitewise_salary_report">Salary Report</option>
                                                            <option value="sitewise_sales_receipt_report">Sales & Receipt Report</option>
                                                            <option value="sitewise_subcontractor_report">Subcontractor Report</option>
                                                            <option value="sitewise_subcontractor_summary_report">Subcontractor Summary Report</option>
                                                            <option value="sitewise_pNl_report">PnL Report</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row" id="dateDiv">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Date Range : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group input-large date-picker input-daterange" data-date-format="dd/mm/yyyy">
                                                            <input type="text" class="form-control" name="start_date" id="start_date" value="{{$startDate}}" required="required">
                                                                <span class="input-group-addon"> to </span>
                                                            <input type="text" class="form-control" name="end_date" id="end_date" value="{{$endDate}}" required="required">
                                                        </div>
                                                        <span class="help-block"> Select date range </span>
                                                    </div>
                                                </div>
                                                <div class="form-group row" id="year" hidden>
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Year : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="year_id" name="year_id" onchange="getData()">
                                                            @foreach($yearData as $year)
                                                                <option value="{{$year['id']}}">{{$year['slug']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row" id="month" hidden>
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Month Range: </label>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="col-md-2">
                                                            <select class="form-control" id="month_from_id" name="month_from_id" onchange="getData()">
                                                                @foreach($monthData as $month)
                                                                    <option value="{{$month['id']}}">{{ucwords($month['slug'])}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-1" style="text-align: center;padding-top: 0.9%">
                                                            <p>TO</p>
                                                        </div>
                                                        <div class="col-md-2" style="text-align: left">
                                                            <select class="form-control" id="month_to_id" name="month_to_id" onchange="getData()">
                                                                @foreach($monthData as $month)
                                                                    <option value="{{$month['id']}}">{{ucwords($month['slug'])}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="form-group row" id="project_sites">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Project Site : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="project_site_id" name="project_site_id" onchange="getData()">
                                                            @foreach($projectSites as $projectSite)
                                                                <option value="{{$projectSite['id']}}">{{$projectSite['project_name']}} - {{$projectSite['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row" id="bill_project_site" hidden>
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Project Site : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="bill_project_site_id" name="bill_project_site_id" {{--onchange="getData()"--}}>
                                                            @foreach($billProjectSites as $projectSite)
                                                                <option value="{{$projectSite['id']}}">{{$projectSite['project_name']}} - {{$projectSite['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row" id="subcontractor_project_site" hidden>
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Project Site : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="subcontractor_project_site_id" name="subcontractor_project_site_id" onchange="getSubcontractor()">
                                                            @foreach($subcontractorProjectSitesData as $projectSite)
                                                                <option value="{{$projectSite['id']}}">{{$projectSite['project_name']}} - {{$projectSite['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row" id="subcontractor" hidden>
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Subcontractors : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="subcontractor_id" name="subcontractor_id" onchange="getData()">

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-actions noborder row submitButton" id="submitButton">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button class="btn red" onclick="getData()"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                </div>
                                                <div class="downloadButton" id="downloadButton" hidden>

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
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="/assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>

    <script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            getDivData();
        });

        function getDivData(){
            if($('#report_type').val() == 'sitewise_sales_receipt_report'){
                $('#bill_project_site,#submitButton').show();
                $('#project_sites').hide();
                $('#subcontractor').hide();
                $('#subcontractor_project_site,#month,#year').hide();
                $('#dateDiv,#downloadButton').hide();

            }else if($('#report_type').val() == 'sitewise_subcontractor_report'){
                $('#subcontractor_project_site,#submitButton').show();
                $('#project_sites').hide();
                $('#bill_project_site,#month,#year').hide();
                $('#dateDiv,#downloadButton').hide();

                getSubcontractor();
            }else if($('#report_type').val() == 'sitewise_subcontractor_summary_report'){
                $('#subcontractor_project_site,#submitButton').show();
                $('#project_sites').hide();
                $('#bill_project_site,#month,#year').hide();
                $('#dateDiv,#downloadButton,#subcontractor').hide();
            }else if($('#report_type').val() == 'sitewise_indirect_expenses_report' || $('#report_type').val() == 'sitewise_pNl_report'){
                $('#bill_project_site,#subcontractor_project_site,#subcontractor,#downloadButton,#dateDiv').hide();
                $('#project_sites,#submitButton,#month,#year').show();
            }else{
                $('#bill_project_site').hide();
                $('#subcontractor_project_site').hide();
                $('#subcontractor,#downloadButton,#month,#year').hide();
                $('#project_sites,#submitButton').show();
                $('#dateDiv').show();
            }
        }

        function getData(){
            if($('#report_type').val() == 'sitewise_sales_receipt_report'){
                var projectSiteId = $('#bill_project_site_id').val();
            }else if($('#report_type').val() == 'sitewise_subcontractor_report' || $('#report_type').val() == 'sitewise_subcontractor_summary_report'){
                var projectSiteId = $('#subcontractor_project_site_id').val();
            }else{
                var projectSiteId = $('#project_site_id').val();
            }
            $.ajax({
                type : "POST",
                url : "/reports/detail",
                data : {
                    _token : $('input[name="_token"]').val(),
                    report_name : $('#report_type').val(),
                    start_date : $('#start_date').val(),
                    end_date : $('#end_date').val(),
                    project_site_id : projectSiteId,
                    subcontractor_id : $('#subcontractor_id').val(),
                    start_month_id : $('#month_from_id').val(),
                    end_month_id : $('#month_to_id').val(),
                    year_id : $('#year_id').val()
                },
                success : function(data,textStatus,xhr){
                    $('.submitButton').hide();
                    $(".downloadButton").html(data);
                    $('.downloadButton').show();
                },
                error : function(errorData){

                }
            });

            /*$('#start_date,#end_date').change(function(){
                getData();
            });*/
        }

        function getSubcontractor(){
            if($('#report_type').val() == 'sitewise_subcontractor_report'){
                var projectSiteId = $('#subcontractor_project_site_id').val();
                $.ajax({
                    type : "POST",
                    url : "/reports/subcontractor",
                    data : {
                        _token : $('input[name="_token"]').val(),
                        project_site_id : projectSiteId
                    },
                    success : function(data,textStatus,xhr){
                        $('#subcontractor').show();
                        $("#subcontractor_id").html(data);
                    },
                    error : function(errorData){

                    }
                });
            }else{
                getData();
            }
        }

    </script>

@endsection