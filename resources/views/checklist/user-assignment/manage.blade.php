<?php
/**
 * Created by Ameya Joshi.
 * Date: 11/12/17
 * Time: 10:22 AM
 */
?>

@extends('layout.master')
@section('title','Constro | Manage Checklist User Assignment')
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
                        <div class="page-content" >
                            @include('partials.common.messages')
                            <div class="container">
                                <div class="col-md-12" style="background-image: url("../assets/global/img/construction.jpg")">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="hdr"><span ><strong>Manage Checklist</strong></span></div>
                                <div class="board">
                                    <div class="taskColumn" id="todo"  style="padding: 5px">
                                        <div class="colHdr" dragabble="true"><strong  style="font-size: large">Assigned</strong></div>
                                        <div class="btn-group">
                                            <div id="sample_editable_1_new" >
                                                <a  style="color: black" class="btn yellow" href="/checklist/user-assignment/create">
                                                    <i class="fa fa-plus"></i>
                                                    Assign checklist
                                                </a>
                                            </div>
                                        </div>
                                        <br><br>
                                        @if($assignedChecklists != null)
                                            @foreach($assignedChecklists as $assignedChecklist)
                                                <div class="panel panel-primary">
                                                    <div class="panel-heading">{{$assignedChecklist->projectSiteChecklist->title}}</div>
                                                    <div class="panel-body">
                                                        <p>{{$assignedChecklist->projectSiteChecklist->detail}}</p>
                                                        <p>{{$assignedChecklist->projectSiteChecklist->quotationFloor->name}}</p>
                                                        <p>{{$assignedChecklist->projectSiteChecklist->projectSite->project->name}} - {{$assignedChecklist->projectSiteChecklist->projectSite->name}}</p>
                                                        <p>{{$assignedChecklist->assignedToUser->first_name}} {{$assignedChecklist->assignedToUser->last_name}}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="taskColumn" id="doing">
                                        <div class="colHdr"><strong  style="font-size: large">In Process</strong></div>
                                        @if($inProgressChecklists != null)
                                            @foreach($inProgressChecklists as $inProgressChecklist)
                                                <div class="panel panel-primary">
                                                    <div class="panel-heading">{{$inProgressChecklist->projectSiteChecklist->title}}</div>
                                                    <div class="panel-body">
                                                        <p>{{$inProgressChecklist->projectSiteChecklist->detail}}</p>
                                                        <p>{{$inProgressChecklist->projectSiteChecklist->quotationFloor->name}}</p>
                                                        <p>{{$inProgressChecklist->projectSiteChecklist->projectSite->project->name}} - {{$assignedChecklist->projectSiteChecklist->projectSite->name}}</p>
                                                        <p>{{$inProgressChecklist->assignedToUser->first_name}} {{$assignedChecklist->assignedToUser->last_name}}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="taskColumn" id="done">
                                        <div class="colHdr"><strong  style="font-size: large">Review</strong></div>
                                        @if($reviewChecklists != null)
                                            @foreach($reviewChecklists as $reviewChecklist)
                                                <div class="panel panel-primary">
                                                    <div class="panel-heading">{{$reviewChecklist->projectSiteChecklist->title}}</div>
                                                    <div class="panel-body">
                                                        <p>{{$reviewChecklist->projectSiteChecklist->detail}}</p>
                                                        <p>{{$reviewChecklist->projectSiteChecklist->quotationFloor->name}}</p>
                                                        <p>{{$reviewChecklist->projectSiteChecklist->projectSite->project->name}} - {{$assignedChecklist->projectSiteChecklist->projectSite->name}}</p>
                                                        <p>{{$reviewChecklist->assignedToUser->first_name}} {{$assignedChecklist->assignedToUser->last_name}}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="taskColumn" id="donee">
                                        <div class="colHdr"><strong  style="font-size: large">Complete</strong></div>
                                        @if($completedChecklists != null)
                                            @foreach($completedChecklists as $completedChecklist)
                                                <div class="panel panel-primary">
                                                    <div class="panel-heading">{{$completedChecklist->projectSiteChecklist->title}}</div>
                                                    <div class="panel-body">
                                                        <p>{{$completedChecklist->projectSiteChecklist->detail}}</p>
                                                        <p>{{$completedChecklist->projectSiteChecklist->quotationFloor->name}}</p>
                                                        <p>{{$completedChecklist->projectSiteChecklist->projectSite->project->name}} - {{$assignedChecklist->projectSiteChecklist->projectSite->name}}</p>
                                                        <p>{{$completedChecklist->assignedToUser->first_name}} {{$assignedChecklist->assignedToUser->last_name}}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
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
    <script src="/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    {{--<script src="/assets/custom/user/user.js" type="application/javascript"></script>--}}
    <link rel="stylesheet"  href="/assets/global/css/app.css"/>
    <script>
        $(document).ready(function(){
            $('#add_checklist_btn').click(function(){
                $('#add_checklist_model').modal();
            })
        });
    </script>
@endsection
