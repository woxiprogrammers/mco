<?php
/**
 * Created by Ameya Joshi.
 * Date: 9/12/17
 * Time: 12:02 PM
 */
?>
@extends('layout.master')
@section('title','Constro | Checklist Project Site Assignment')
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
                        <form role="form" id="createChecklistSiteAssignmentForm" class="form-horizontal" method="post" action="/checklist/site-assignment/create">
                            {!! csrf_field() !!}
                            <div class="page-head">
                                <div class="container">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1>Checklist Project Site Assignment</h1>
                                    </div>
                                    <div class="form-group " style="float: right;margin-top:1%">
                                        <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit </button>
                                    </div>
                                </div>
                            </div>
                            <div class="page-content">
                                @include('partials.common.messages')
                                <div class="container">
                                    <div class="col-md-11">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="portlet light ">
                                            <input type="hidden" id="numberOfCheckpoints" value="1">
                                            <div class="portlet-body form">
                                                <div class="form-body">
                                                    <fieldset>
                                                        <legend>Project </legend>
                                                      {{--  <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label"> Client :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" id="clientId" name="client_id">
                                                                    <option value="">--Select Client --</option>
                                                                    @foreach($clients as $client)
                                                                        <option value="{{$client['id']}}">{{$client['company']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label">Project :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" id="projectId" name="project_id">
                                                                    <option value="">--Select Project --</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label">Project Site :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" id="projectSiteId" name="project_site_id">
                                                                    <option value="">--Select Project Site --</option>
                                                                </select>
                                                            </div>
                                                        </div>--}}
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label">Quotation Floor :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" id="quotationFloorId" name="quotation_floor_id">
                                                                    <option value="">--Select Quotation Floor --</option>
                                                                    @foreach($quotationFloors as $quotationFloor)
                                                                        <option value="{{$quotationFloor['id']}}">{{$quotationFloor['name']}}</option>
                                                                    @endforeach
                                                                </select>
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
                                                                <input type="text" class="form-control" name="title" id="title">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label">Detail :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" name="detail" id="detail">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label">Main Category :</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" id="main_cat" name="main_category_id">
                                                                    <option value="">--Select Main Category --</option>
                                                                    @foreach($mainCategories as $mainCategory)
                                                                        <option value="{{$mainCategory['id']}}">{{$mainCategory['name']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-5" style="text-align: right ; margin-left: -6% ; font-size: 14px">
                                                                <label for="sub_cat" class="control-label">Select Sub Category Here</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" id="sub_cat" name="sub_category_id">
                                                                    <option value="">--Select Sub Category --</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="input_fields_wrap" style="margin-top: 5%">

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
