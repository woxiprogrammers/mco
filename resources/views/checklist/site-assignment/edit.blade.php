<?php
/**
 * Created by Ameya Joshi.
 * Date: 9/12/17
 * Time: 4:11 PM
 */
?>

@extends('layout.master')
@section('title','Constro | Edit Checklist Project Site Assignment')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    {{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>--}}

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
                        <form role="form" id="createChecklistSiteAssignmentForm" class="form-horizontal" method="post" action="/checklist/site-assignment/edit/{{$siteChecklist->id}}">
                            {!! csrf_field() !!}
                            <div class="page-head">
                                <div class="container">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1>Edit Checklist Project Site Assignment</h1>
                                    </div>
                                    {{--<div class="form-group " style="float: right;margin-top:1%">
                                        <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit </button>
                                    </div>--}}
                                </div>
                            </div>
                            <div class="page-content">
                                @include('partials.common.messages')
                                <div class="container">
                                    <div class="col-md-11">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="portlet light ">
                                            <div class="portlet-body form">
                                                <div class="form-body">
                                                    <fieldset>
                                                        <legend>Project </legend>
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label"> Client :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" value="{{$siteChecklist->projectSite->project->client->company}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label">Project :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" value="{{$siteChecklist->projectSite->project->name}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label">Project Site :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" value="{{$siteChecklist->projectSite->name}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label">Quotation Floor :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" value="{{$siteChecklist->quotationFloor->name}}">
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <fieldset>
                                                        <legend> Checklist </legend>
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label">Checklist Title :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" name="title" id="title" value="{{$siteChecklist->title}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label">Detail :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" name="detail" id="detail" value="{{$siteChecklist->detail}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label">Main Category :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" value="{{$siteChecklist->checklistCategory->mainCategory->name}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right ; margin-left: -6% ; font-size: 14px">
                                                                <label for="sub_cat" class="control-label">Sub Category</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" value="{{$siteChecklist->checklistCategory->name}}">
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="input_fields_wrap" style="margin-top: 5%">
                                                        <fieldset>
                                                            <legend>Checkpoints</legend>
                                                            <div class="table-container">
                                                                <table class="table table-striped table-bordered table-hover order-column" id="inventoryListingTable">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Description</th>
                                                                        <th>Is Remark Required</th>
                                                                        <th>No. of Images</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @foreach($siteChecklist->projectSiteChecklistCheckpoints as $checkpoint)
                                                                        <tr>
                                                                            <td>
                                                                                {{$checkpoint['description']}}
                                                                            </td>
                                                                            <td>
                                                                                @if($checkpoint['is_remark_required'] == true)
                                                                                    Yes
                                                                                @else
                                                                                    No
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                {!! count($checkpoint->projectSiteChecklistCheckpointImages) !!}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script src="/assets/custom/checklist/checklist.js"></script>
    <script src="/assets/custom/checklist/validation.js"></script>
    <script src="/assets/custom/checklist/site-assignment.js"></script>
    <script>
        $(document).ready(function(){
            CreateChecklistSiteAssignment.init();
        });
    </script>
@endsection

