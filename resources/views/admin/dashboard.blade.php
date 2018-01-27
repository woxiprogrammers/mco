@extends('layout.master')
@section('title','Constro')
@include('partials.common.navbar')
@section('css')
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')

{!! Charts::assets() !!}

<div class="page-wrapper">
    <div class="page-wrapper-row full-height">
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <!-- BEGIN PAGE HEAD-->
        <div class="page-head">
            <div class="container">
                <!-- BEGIN PAGE TITLE -->

                <!-- END PAGE TITLE -->

            </div>
        </div>
        {!! csrf_field() !!}
        <!-- END PAGE HEAD-->
        <!-- BEGIN PAGE CONTENT BODY -->
        <div class="page-content content-full-height">
            <div class="container">
                <!-- BEGIN PAGE BREADCRUMBS -->

                <!-- END PAGE BREADCRUMBS -->
                <!-- BEGIN PAGE CONTENT INNER -->
                <div class="page-content-inner">
                    <div class="row">
                        <fieldset>
                            <legend>
                                <label style="margin-left: 1%">
                                    Notifications
                                </label>
                            </legend>
                            <div class="row">
                                <div class="col-md-3" style="padding-left: 2%;padding-right: 2%;">
                                    <div class="panel-group accordion" id="accordion1" style="margin-top: 3%">
                                        <div class="panel panel-default">
                                            <div class="panel-heading" style="background-color: cornflowerblue">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse_3_1" style="font-size: 14px;color: white">
                                                        <b> MIT COLLEGE - SOEMIT </b>
                                                        <span class="badge badge-danger" style="background-color: #ed6b75 !important; margin-left: 3%">
                                                            <b>10</b>
                                                        </span>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_1" class="panel-collapse collapse">
                                                <div class="panel-body" style="overflow:auto;">
                                                    <table class="table table-striped table-bordered table-hover">
                                                        <tr>
                                                            <td>
                                                                <label class="control-label">
                                                                    Purchase
                                                                </label>
                                                                <span class="badge badge-success" style="margin-left: 2%">
                                                                    3
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <label class="control-label">
                                                                    Inventory
                                                                </label>
                                                                <span class="badge badge-success" style="margin-left: 2%">
                                                                    3
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <label class="control-label">
                                                                    Checklist
                                                                </label>
                                                                <span class="badge badge-success" style="margin-left: 2%">
                                                                    2
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <label class="control-label">
                                                                    Peticash
                                                                </label>
                                                                <span class="badge badge-success" style="margin-left: 2%">
                                                                    2
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-left: 2%;padding-right: 2%;">
                                    <div class="panel-group accordion" id="accordion2" style="margin-top: 3%">
                                        <div class="panel panel-default">
                                            <div class="panel-heading" style="background-color: cornflowerblue">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion2" href="#collapse_3_2" style="font-size: 14px;color: white">
                                                        <b> T6 - TALEGAON </b>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_2" class="panel-collapse collapse">
                                                <div class="panel-body" style="overflow:auto;">
                                                    <div class="form-group">
                                                        <label class="control-label">Select Vendor Quotation Images  :</label>
                                                        <input id="imageupload" type="file" class="btn green" multiple />
                                                        <br />
                                                        <div class="row">
                                                            <div id="preview-image" class="row">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-left: 2%;padding-right: 2%;">
                                    <div class="panel-group accordion" id="accordion3" style="margin-top: 3%">
                                        <div class="panel panel-default">
                                            <div class="panel-heading" style="background-color: cornflowerblue">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_3" style="font-size: 14px;color: white">
                                                        <b> MOHAR PRATIMA - TALEGAON </b>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_3" class="panel-collapse collapse">
                                                <div class="panel-body" style="overflow:auto;">
                                                    <div class="form-group">
                                                        <label class="control-label">Select Vendor Quotation Images  :</label>
                                                        <input id="imageupload" type="file" class="btn green" multiple />
                                                        <br />
                                                        <div class="row">
                                                            <div id="preview-image" class="row">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-left: 2%;padding-right: 2%;">
                                    <div class="panel-group accordion" id="accordion4" style="margin-top: 3%">
                                        <div class="panel panel-default">
                                            <div class="panel-heading" style="background-color: cornflowerblue">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion4" href="#collapse_3_4" style="font-size: 14px;color: white">
                                                        <b> Konark Orchid - Konark Orchid School </b>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_4" class="panel-collapse collapse">
                                                <div class="panel-body" style="overflow:auto;">
                                                    <div class="form-group">
                                                        <label class="control-label">Select Vendor Quotation Images  :</label>
                                                        <input id="imageupload" type="file" class="btn green" multiple />
                                                        <br />
                                                        <div class="row">
                                                            <div id="preview-image" class="row">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="row" style="margin-top: 3%">
                        <div class="col-md-4">
                            {!! $quotationStatus->render() !!}
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-3">
                                    &nbsp;
                                </div>
                                <div class="col-md-3" style="background: #8fdf82;font-weight: bold;text-align: center;color: #ffffff">
                                    <span>Total Category : {{$totalCategory}}</span>
                                </div>
                                <div class="col-md-3" style="background: #00b3ee;font-weight: bold;text-align: center;color: #ffffff">
                                    <span>Total Materials : {{$totalMaterials}}</span>
                                </div>
                                <div class="col-md-3">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    {!! $categorywiseMaterialCount->render() !!}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <!-- END PAGE CONTENT INNER -->
    </div>
</div>
<!-- END PAGE CONTENT BODY -->
<!-- END CONTENT BODY -->
</div>
<!-- END CONTENT -->
<!-- BEGIN QUICK SIDEBAR -->

<!-- END QUICK SIDEBAR -->
</div>
<!-- END CONTAINER -->
@endsection
@section('javascript')
<script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
@endsection
