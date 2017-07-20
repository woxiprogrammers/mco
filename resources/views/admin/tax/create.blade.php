@extends('layout.master')
@section('title','Constro | Create Tax')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="/assets/global/plugins/bootstrap-toggle/bootstrap-toggle.min.css" rel="stylesheet">
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
                                <h1>Create Tax</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/tax/manage">Manage Tax</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Create Tax</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">

                                    <div class="portlet-body form">
                                        <form role="form" id="create-tax" class="form-horizontal" method="post" action="/tax/create">
                                            {!! csrf_field() !!}
                                            <div class="form-body">
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Name</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control" id="name" name="name">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="base_percentage" class="control-label">Percentage</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6 input-group" id="inputGroup">
                                                        <input type="text" class="form-control" id="percentage" name="base_percentage">
                                                        <span class="input-group-addon" style="font-size: 18px">&nbsp;&nbsp; % &nbsp; &nbsp;</span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="is_special" class="control-label">Is Special Tax ?</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="checkbox" class="make-switch" data-on-text="Yes" data-off-text="No" name="is_special">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions noborder row">
                                                <div class="col-md-offset-3">
                                                    <button type="submit" class="btn blue">Submit</button>
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
<script src="/assets/global/plugins/bootstrap-toggle/bootstrap-toggle.min.js" type="application/javascript"></script>
<script src="/assets/custom/admin/tax/tax.js" type="application/javascript"></script>
<script>
    $(document).ready(function() {
        CreateTax.init();
        console.log($("#isSpecial").next().find('.toggle-off'));
        $("#isSpecial").next().find('.toggle-off').removeProp('position');
    });
</script>
@endsection
