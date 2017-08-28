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
                                    <a href="/units/manage">Manage Units Conversions</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Edit Unit Conversion</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <form role="form" id="edit-conversion" class="form-horizontal" method="post" action="/units/conversion/edit/{{$conversion['id']}}">
                                            {!! csrf_field() !!}
                                            <input type="hidden" name="_method" value="put">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">From Unit</label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="from_unit" name="from_unit">
                                                            @foreach($units as $unit)
                                                                @if($unit['id'] == $conversion['unit_1_id'])
                                                                    <option value="{{$unit['id']}}" selected> {{$unit['name']}}</option>
                                                                @else
                                                                    <option value="{{$unit['id']}}"> {{$unit['name']}}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
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
                                                        <select class="form-control" id="to_unit" name="to_unit">
                                                            @foreach($units as $unit)
                                                                @if($unit['id'] == $conversion['unit_2_id'])
                                                                    <option value="{{$unit['id']}}" selected> {{$unit['name']}}</option>
                                                                @else
                                                                    <option value="{{$unit['id']}}"> {{$unit['name']}}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
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
                                                <div class="col-md-offset-3" style="margin-left: 26%">
                                                    <button type="submit" class="btn red btn-md" id="submit"><i class="fa fa-check"></i> Submit</button>
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

        $('#submit').css("padding-left",'6px');

    });
</script>
@endsection
