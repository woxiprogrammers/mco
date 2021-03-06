<?php
/**
 * Created by Ameya Joshi.
 * Date: 14/6/17
 * Time: 5:52 PM
 */
?>

@extends('layout.master')
@section('title','Constro | Create Project')
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
                                <h1>Create Project</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/project/manage">Manage Projects</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Create Project</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">

                                    <div class="portlet-body form">
                                        <form role="form" id="createProject" class="form-horizontal" method="post" action="/project/create">
                                            {!! csrf_field() !!}
                                            <div class="form-body">
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Client</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select name="client_id" class="form-control">
                                                            @foreach($clients as $client)
                                                                <option value="{{$client->id}}"> {{$client->company}} </option>
                                                            @endforeach
                                                        </select>
                                                        </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Project Name</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" name="project_name" class="form-control" id="projectName">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Location</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" id="projectSiteName" name="project_site_name" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Location address</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <textarea id="siteAddress" name="address" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">HSN code</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <select class="form-control" name="hsn_code" id="hsnCode">
                                                            @foreach($hsnCodes as $hsnCode)
                                                                <option value="{{$hsnCode['id']}}">{{$hsnCode['code']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        @foreach($hsnCodes as $hsnCode)
                                                            <span class="hsn-description" id="hsnCodeDescription-{{$hsnCode['id']}}" hidden>
                                                                {{$hsnCode['description']}}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Extra Email</label>
                                                        <span></span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <textarea class="form-control" name="cc_mail" id="cc_mail"></textarea>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <span>If multiple email id then use <strong>, (comma)</strong> to seperate it out.</span>
                                                    </div>
                                                </div>
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label class="control-label">City Name</label>
                                                            <span>*</span>
                                                        </div>

                                                        <div class="col-md-4">

                                                                    <select class="form-control" name="city_id" id="city_id">
                                                                    @foreach($cityArray as $city)
                                                                        <li><option value={{$city['id']}} name="cities[{{$city['id']}}]"</option> {{$city['name']}} </li>
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
<script src="/assets/custom/admin/project/project.js" type="application/javascript"></script>
<script>
    $(document).ready(function() {
        CreateProject.init();
        $("#hsnCode").trigger('change');
    });
</script>
@endsection
