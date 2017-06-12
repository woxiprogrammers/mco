@extends('layout.master')
@section('title','Constro | Edit Conversion')
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
                                <h1>Edit Conversion</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/units/manage">Back</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-11">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <form role="form" id="edit-conversion" class="form-horizontal" method="post" action="/units/conversion/edit/{{$conversion['unit_1_id']}}-{{$conversion['unit_2_id']}}">
                                            {!! csrf_field() !!}
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">From Unit</label>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control" name="from_unit_text" value="{{$units[$conversion['unit_1_id']]}}" readonly>
                                                        <input type="hidden" class="form-control" id="from_unit" name="from_unit" value="{{$conversion['unit_1_id']}}">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Value</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="number" class="form-control" id="from_value" name="from_value" value="{{$conversion['unit_1_value']}}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">To Unit</label>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control" name="from_unit_text" value="{{$units[$conversion['unit_2_id']]}}"  readonly>
                                                        <input type="hidden" class="form-control" id="from_unit" name="to_unit" value="{{$conversion['unit_2_id']}}">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Value</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="number" class="form-control" id="to_value" name="to_value" value="{{$conversion['unit_2_value']}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions noborder row">
                                                <div class="col-md-offset-3">
                                                    <button type="submit" class="btn btn-success btn-md" style="width:25%">Submit</button>
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
<script src="/assets/custom/admin/units/units.js" type="application/javascript"></script>
<script>
    $(document).ready(function() {
        UnitsConversionEdit.init();
    });
</script>
@endsection
