@extends('layout.master')
@section('title','Constro | Create Category')
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
                                        <form role="form" id="reports-download" class="form-horizontal" method="post" action="/reports/download">
                                            {!! csrf_field() !!}
                                            <input type="hidden" value="false" name="is_miscellaneous" id="is_miscellaneous" >
                                            <div class="row">
                                                <label class="control-label col-md-3">Date Range : </label>
                                                <div class="col-md-9">
                                                    <div class="input-group input-large date-picker input-daterange" data-date-format="dd/mm/yyyy">
                                                        <input type="text" class="form-control" name="start_date" id="start_date" value="{{$start_date}}" required="required">
                                                        <span class="input-group-addon"> to </span>
                                                        <input type="text" class="form-control" name="end_date" id="end_date" value="{{$end_date}}" required="required"> </div>
                                                    <!-- /input-group -->
                                                    <span class="help-block"> Select date range </span>
                                                </div>
                                            </div>
                                            <div class="form-body">
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Report Type : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="report_type" name="report_type">
                                                            <option value="materialwise_purchase_report">Materialwise Purchase</option>
                                                            <option value="receiptwise_p_and_l_report">Receiptwise P & L</option>
                                                            <option value="subcontractor_report">Subcontractor</option>
                                                            <option value="labour_specific_report">Labour Specific Report</option>
                                                            <option value="purchase_bill_tax_report">Purchase Bill Tax Report</option>
                                                            <option value="sales_bill_tax_report">Sales Bill Tax Reports</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" name="materialwise_purchase_report" id="materialwise_purchase_report">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h3> Materialwise Report</h3>
                                                        <hr/>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Project Site : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="materialwise_purchase_report_site_id" name="materialwise_purchase_report_site_id">
                                                            @foreach($sites as $site)
                                                            <option value="{{$site['id']}}">{{$site['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Category : </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <select class="form-control" id="category_id" name="category_id" data-placeholder="">
                                                                <option value="0">ALL</option>
                                                                @foreach($categories as $category)
                                                                <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Materials : </label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group select2-bootstrap-prepend">
                                                            <!--<span class="input-group-btn">
                                                                <button class="btn btn-default" type="button" data-select2-open="material_id"> Materials </button>
                                                            </span>-->
                                                            <select class="form-control select2" multiple id="material_id" name="material_id[]" data-placeholder="">
                                                                <option value="0">ALL</option>
                                                                @foreach($materials as $material)
                                                                <option value="{{$material['id']}}">{{$material['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" name="receiptwise_p_and_l_report" id="receiptwise_p_and_l_report">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h3> Receiptwise P & L Report</h3>
                                                        <hr/>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Project Site : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="receiptwise_p_and_l_report_site_id" name="receiptwise_p_and_l_report_site_id">
                                                            @foreach($sites as $site)
                                                            <option value="{{$site['id']}}">{{$site['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" name="subcontractor_report" id="subcontractor_report">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h3> Subcontractor Report</h3>
                                                        <hr/>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Project Site : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="subcontractor_report_site_id" name="subcontractor_report_site_id">
                                                            @foreach($sites as $site)
                                                            <option value="{{$site['id']}}">{{$site['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Subcontractor : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="subcontractor_id" name="subcontractor_id">
                                                            @foreach($subcontractors as $sc)
                                                            <option value="{{$sc['id']}}">{{$sc['company_name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" name="labour_specific_report" id="labour_specific_report">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h3> Labour Report</h3>
                                                        <hr/>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Project Site : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="labour_specific_report_site_id" name="labour_specific_report_site_id">
                                                            <option value="all">All</option>
                                                            @foreach($sites as $site)
                                                            <option value="{{$site['id']}}">{{$site['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-actions noborder row">
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Labour : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group select2-bootstrap-prepend">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-default" type="button" data-select2-open="labour_id">
                                                                    <span class="glyphicon glyphicon-search"></span>
                                                                </button>
                                                            </span>
                                                            <select class="form-control select2" id="labour_id" name="labour_id">
                                                                @foreach($employees as $employee)
                                                                <option value="{{$employee['id']}}">{{$employee['employee_id']}} - {{$employee['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="row" name="purchase_bill_tax_report" id="purchase_bill_tax_report">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h3> Purchase Bill Tax Vendorwise Report</h3>
                                                        <hr/>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Project Site : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="purchase_bill_tax_report_site_id" name="purchase_bill_tax_report_site_id">
                                                            @foreach($sites as $site)
                                                            <option value="{{$site['id']}}">{{$site['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Vendor : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group select2-bootstrap-prepend">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-default" type="button" data-select2-open="vendor_id">
                                                                    <span class="glyphicon glyphicon-search"></span>
                                                                </button>
                                                            </span>
                                                            <select class="form-control select2" id="vendor_id" name="vendor_id">
                                                                @foreach($vendors as $vendor)
                                                                <option value="{{$vendor['id']}}">{{$vendor['name']}} - {{$vendor['company']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" name="sales_bill_tax_report" id="sales_bill_tax_report">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h3> Sales Bill Tax Report</h3>
                                                        <hr/>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label>Select Project Site : </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-control" id="sales_bill_tax_report_site_id" name="sales_bill_tax_report_site_id">
                                                            @foreach($billProjectSites as $site)
                                                            <option value="{{$site['id']}}">{{$site['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
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
<script type="text/javascript">
    $(document).ready(function(){
        toggleReportTypeContent();
        disabledMaterial();
        $('#report_type').on('change',function() {
           toggleReportTypeContent();
        });

        $('#category_id').on('change', function(){
            disabledMaterial();
        });

    });

    function disabledMaterial() {
        var category_val = $('#category_id').val();
        if (category_val == 0) {
            $('#material_id').prop("disabled", true);
        } else {
            $('#material_id').prop("disabled", false);
        }
    }

    function toggleReportTypeContent() {
        var report_type = $('#report_type').val();
        var report_type_array = [ 'sales_bill_tax_report','materialwise_purchase_report',
            'receiptwise_p_and_l_report','subcontractor_report',
            'labour_specific_report','purchase_bill_tax_report'];
        $.each(report_type_array, function( index, value ) {
            if (report_type === value) {
                $('#'+ value).show();
            } else {
                $('#'+ value).hide();
            }
        });
    }
</script>

@endsection
