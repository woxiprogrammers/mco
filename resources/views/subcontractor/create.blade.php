@extends('layout.master')
@section('title','Constro | Create Subcontractor')
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
                                    <h1>Create Subcontractor</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                                <div class="container" style="width: 100%">
                                    <ul class="page-breadcrumb breadcrumb">
                                        <li>
                                            <a href="/subcontractor/manage">Manage Subcontractor</a>
                                            <i class="fa fa-circle"></i>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);">Create Subcontractor</a>
                                            <i class="fa fa-circle"></i>
                                        </li>
                                    </ul>
                                    <div class="col-md-12">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="portlet light ">
                                            <div class="portlet-body form">
                                                <form id="createSubcontractor" class="form-horizontal" action="/subcontractor/create" method="post">
                                                    {!! csrf_field() !!}
                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="subcontractor_name" class="control-label">Subcontractor Name</label>
                                                                    <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="subcontractor_name" name="subcontractor_name" required="required">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="company_name" class="control-label">Company Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="company_name" name="company_name" required="required">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="category" class="control-label">Category Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="category" name="category">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="subcategory" class="control-label">Subcategory Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="subcategory" name="subcategory">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="desc_prod_service" class="control-label">Description of Service</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="desc_prod_service" name="desc_prod_service">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="nature_of_work" class="control-label">Nature Of Work</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="nature_of_work" name="nature_of_work">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="sc_turnover_pre_yr" class="control-label">Turnover Per Year</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="sc_turnover_pre_yr" name="sc_turnover_pre_yr">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="sc_turnover_two_fy_ago" class="control-label">Turnover Two FY Ago</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="sc_turnover_two_fy_ago" name="sc_turnover_two_fy_ago">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="primary_cont_person_name" class="control-label">Primary Contact Person Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="primary_cont_person_name" name="primary_cont_person_name">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="primary_cont_person_mob_number" class="control-label">Primary Contact Person Mobile No</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="primary_cont_person_mob_number" name="primary_cont_person_mob_number">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="primary_cont_person_email" class="control-label">Primary Contact Person Email</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="primary_cont_person_email" name="primary_cont_person_email">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="escalation_cont_person_name" class="control-label">Escalation Contact Person Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="escalation_cont_person_name" name="escalation_cont_person_name">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="escalation_cont_person_mob_number" class="control-label">Escalation Contact Person Mobile No</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="escalation_cont_person_mob_number" name="escalation_cont_person_mob_number">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="sc_pancard_no" class="control-label">PAN Card Number</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="sc_pancard_no" name="sc_pancard_no">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="sc_service_no" class="control-label">Service Tax Number</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="sc_service_no" name="sc_service_no">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="sc_vat_no" class="control-label">VAT Number</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="sc_vat_no" name="sc_vat_no">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="gstin" class="control-label">GSTIN number</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" id="gstin" name="gstin">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-actions noborder row">
                                                        <div class="col-md-offset-3" style="margin-left: 26%">
                                                            <button type="submit" class="btn red" id="labour_submit"><i class="fa fa-check"></i> Submit</button>
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
<script src="/assets/custom/subcontractor/subcontractor.js" type="application/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script><script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        CreateSubcontractor.init();
    });
</script>
@endsection