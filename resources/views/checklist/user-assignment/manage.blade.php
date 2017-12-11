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
                                        <div class="panel panel-primary">
                                            <div class="panel-heading">Jules Verne - Around the World in 80 Days</div>
                                            <div class="panel-body">
                                                <p>"Sir," said Mr. Fogg to the captain</p>
                                            </div>
                                        </div>
                                        <div class="panel panel-primary">
                                            <div class="panel-heading">Jules Verne - Around the World in 80 Days</div>
                                            <div class="panel-body">
                                                <p>"Sir," said Mr. Fogg to the captain</p>
                                            </div>
                                        </div>
                                        <div class="panel panel-primary">
                                            <div class="panel-heading">Jules Verne - Around the World in 80 Days</div>
                                            <div class="panel-body">
                                                <p>"Sir," said Mr. Fogg to the captain</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="taskColumn" id="doing">
                                        <div class="colHdr"><strong  style="font-size: large">In Process</strong></div>
                                    </div>
                                    <div class="taskColumn" id="done">
                                        <div class="colHdr"><strong  style="font-size: large">Review</strong></div>
                                    </div>
                                    <div class="taskColumn" id="donee">
                                        <div class="colHdr"><strong  style="font-size: large">Complete</strong></div>
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
