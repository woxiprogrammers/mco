<?php
/**
 * Created by Ameya Joshi.
 * Date: 15/6/17
 * Time: 12:46 PM
 */
?>
@extends('layout.master')
@section('title','Constro | Edit Project')
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
                                <h1>Create Category</h1>
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
                                    <a href="javascript:void(0);">Edit Project</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <form role="form" id="createProject" class="form-horizontal" method="post" action="/project/edit/{{$projectData['id']}}">
                                            <input type="hidden" name="project_id" id="projectId" value="{{$projectData['id']}}">
                                            <input type="hidden" name="_method" value="put">
                                            {!! csrf_field() !!}
                                            <div class="form-body">
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Client</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input name="client" class="form-control" id="client" value="{{$projectData['client']}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Project Name</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" name="project_name" class="form-control" id="projectName" value="{{$projectData['project']}}">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Project Site Name</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" id="projectSiteName" name="project_site_name" class="form-control" value="{{$projectData['project_site']}}">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Project Site address</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <textarea id="siteAddress" name="address" class="form-control">{{$projectData['project_site_address']}}</textarea>
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
                                                                @if($projectData['project_hsn_code'] == $hsnCode['id'])
                                                                    <option value="{{$hsnCode['id']}}" selected>{{$hsnCode['code']}}</option>
                                                                @else
                                                                    <option value="{{$hsnCode['id']}}">{{$hsnCode['code']}}</option>
                                                                @endif
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
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit</button>
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
        EditProject.init();
        $("#hsnCode").trigger('change');

        $('#submit').css("padding-left",'6px');

    });
</script>
@endsection
