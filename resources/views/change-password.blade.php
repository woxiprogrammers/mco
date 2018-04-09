<?php
    /**
     * Created by PhpStorm.
     * User: harsha
     * Date: 9/4/18
     * Time: 10:59 AM
     */
    ?>

@extends('layout.master')
@section('title','Constro | Create User')
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
                                    <h1>Change Password</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="javascript:void(0);">Change Password</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">

                                        <div class="portlet-body form">
                                            <form role="form" id="change-password" class="form-horizontal" method="post" action="/user/change-password">
                                                {!! csrf_field() !!}
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="old_password" class="control-label">Old Password</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="password" class="form-control" id="old_password" name="old_password">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="new_password" class="control-label">New Password</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="password" class="form-control" id="new_password" name="new_password" autocomplete="off" placeholder="New Password">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="confirm_password" class="control-label">Confirm Password</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" autocomplete="off" placeholder="Confirm Password">
                                                    </div>
                                                </div>

                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
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
    <script>
        $(document).ready(function(){
            ChangePassword.init();
        });

        var  ChangePassword = function () {
            var handleCreate = function() {
                var form = $('#change-password');
                var error = $('.alert-danger', form);
                var success = $('.alert-success', form);
                form.validate({
                    errorElement: 'span', //default input error message container
                    errorClass: 'help-block', // default input error message class
                    focusInvalid: false, // do not focus the last invalid input
                    rules: {
                        old_password: {
                            required: true
                        },
                        new_password: {
                            required: true,
                            minlength: 6,
                            maxlength:20
                        },
                        confirm_password: {
                            required: true,
                            minlength: 6,
                            maxlength:20,
                            equalTo: "#new_password"
                        }
                    },

                    messages: {
                        old_password: {
                            required: "Old Password is required."
                        },

                        new_password:{
                            required: "Password is required"
                        },
                        confirm_password: {
                            required: "Confirm password is required.",
                            equalTo: "Please re-enter the same password again."
                        }
                    },

                    invalidHandler: function (event, validator) { //display error alert on form submit
                        success.hide();
                        error.show();
                    },

                    highlight: function (element) { // hightlight error inputs
                        $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                    },

                    unhighlight: function (element) { // revert the change done by hightlight
                        $(element)
                            .closest('.form-group').removeClass('has-error'); // set error class to the control group
                    },

                    success: function (label) {
                        label
                            .closest('.form-group').addClass('has-success');
                    },

                    submitHandler: function (form) {
                        $("button[type='submit']").prop('disabled', true);
                        success.show();
                        error.hide();
                        form.submit();
                    }
                });
            };

            return {
                init: function () {
                    handleCreate();
                }
            };
        }();

    </script>
@endsection

