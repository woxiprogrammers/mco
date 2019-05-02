@extends('layout.master')
@section('title','Constro | Manage Salary Distribution')
@include('partials.common.navbar')
@section('css')
<link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
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
                                <h1>Manage Salary Distribution</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        <div class="container">
                            <div class="row">
                                <fieldset>
                                    <legend style="padding-left: 30px"> Selection Criteria </legend>
                                    <div class="col-md-2 form-group">
                                        <select class="form-control" id="year_slug" name="year_slug" style="width: 80%;">
                                            <option value="all"> -- All Year -- </option>
                                            @foreach($years as $year)
                                            <option value="{{$year['id']}}"> {{$year['slug']}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <select name="month_slug" id="month_slug" class="form-control" style="width: 80%;">
                                            <option value="all"> -- All Month -- </option>
                                            @foreach($months as $month)
                                            <option value="{{$month['id']}}"> {{ucfirst($month['slug'])}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5 form-group">
                                        <select class="form-control input-lg select2-multiple" id="project_site_id" name="project_site_id" multiple="multiple" style="overflow:hidden" data-placeholder="Select Project Site">
                                            @foreach($projectSiteData as $projectSite)
                                            <option value="{{$projectSite['project_site_id']}}"> {{$projectSite['project_site_name']}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-1 form-group">
                                        <label>&nbsp;</label>
                                        <div class="btn-group">
                                            <div id="search-withfilter" class="btn blue" >
                                                <a href="#" style="color: white"> Submit
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </fieldset>

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                    <div class="portlet light ">
                                        {!! csrf_field() !!}
                                        <div class="portlet-body">
                                            <div class="table-toolbar">
                                                <div class="row" style="text-align: right">
                                                    <div class="col-md-12">
                                                        <div class="btn-group">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="salaryDistributionTable">
                                                <thead>
                                                <tr>
                                                    <th> Sr. no </th>
                                                    <th> Site Name </th>
                                                    <!--<th> Site Expenses </th>
                                                    <th> Total Expenses </th>
                                                    <th> Office Expenses </th>-->
                                                    <th> Salary Distribution </th>
                                                </tr>
                                                <tr>
                                                    <!--<th></th>
                                                    <th></th>
                                                    <th></th>-->
                                                    <th>
                                                        <input type="hidden" class="form-control form-filter" name="sd_month_id" id="sd_month_id">
                                                    </th>
                                                    <th>
                                                        <input type="hidden" class="form-control form-filter" name="sd_project_site_id" id="sd_project_site_id"></th>
                                                    <th>
                                                        <input type="hidden" class="form-control form-filter" name="sd_year_id" id="sd_year_id">
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
@endsection

@section('javascript')
<script src="/public/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<!--<script src="/assets/custom/admin/tax.js" type="application/javascript"></script>-->
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
<link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/assets/global/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
<script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="/assets/global/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css" type="text/css"/>
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/custom/admin/salary-distribution/manage-datatables.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#salaryDistributionTable').DataTable();

        $("#search-withfilter").on('click',function(e){
            var year_slug = $('#year_slug').val();
            var month_slug = $('#month_slug').val();
            var project_site_id = $('#project_site_id').val();
            $('#sd_year_id').val(year_slug);
            $('#sd_month_id').val(month_slug);
            $('#sd_project_site_id').val(project_site_id);
            $(".filter-submit").trigger('click');
        });
    });
</script>
@endsection
