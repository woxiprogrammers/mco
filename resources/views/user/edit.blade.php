@extends('layout.master')
@section('title','Constro | Edit User')
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
                                <h1>Edit User {{$user['first_name']}} {{$user['last_name']}}

                                </h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/user/manage">Back</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-11">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">

                                    <div class="portlet-body form">
                                        <input type="hidden" id="user_id" value="{{$user['id']}}">
                                        <form role="form" id="edit-user" class="form-horizontal" method="post" action="/user/edit/{{$user['id']}}">
                                            {!! csrf_field() !!}
                                            <input type="hidden" name="_method" value="PUT">
                                            <div class="form-group row">
                                                <div class="col-md-3" style="text-align: right">
                                                    <label for="role_id" class="control-label">Select Role</label>
                                                    <span>*</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <select class="form-control" id="role_id" name="role_id">
                                                        @foreach($roles as $role)
                                                            @if($role['id'] == $user->roles[0]->role_id)
                                                                <option value="{{$role['id']}}" selected>{{$role['name']}}</option>
                                                            @else
                                                                <option value="{{$role['id']}}">{{$role['name']}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-3" style="text-align: right">
                                                    <label for="first_name" class="control-label">First Name</label>
                                                    <span>*</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{$user['first_name']}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-3" style="text-align: right">
                                                    <label for="last_name" class="control-label">Last Name</label>
                                                    <span>*</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{$user['last_name']}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-3" style="text-align: right">
                                                    <label for="dob" class="control-label">DOB</label>
                                                    <span>*</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="date" class="form-control" name="dob" id="datepicker" value="{{$user['dob']}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-3" style="text-align: right">
                                                    <label for="gender" class="control-label">Select Gender</label>
                                                    <span>*</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <select class="form-control" name="gender">
                                                        <option value="">Select Gender</option>
                                                        <option value="F">Female</option>
                                                        <option value="M">Male</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-3" style="text-align: right">
                                                    <label for="email" class="control-label">Email</label>
                                                    <span>*</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="email" class="form-control" id="email" name="email" value="{{$user['email']}}" tabindex="-1">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-3" style="text-align: right">
                                                    <label for="mobile" class="control-label">Contact Number</label>
                                                    <span>*</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="mobile" name="mobile" value="{{$user['mobile']}}">
                                                </div>
                                            </div>
                                            <div class="form-actions noborder row">
                                                <div class="col-md-offset-3" style="margin-left: 26%">
                                                    <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit</button>
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
<script src="/assets/custom/user/user.js" type="application/javascript"></script>
<script>
    $(document).ready(function() {
        $('#submit').css("padding-left",'6px');
        $('#email').css('pointer-events',"none");
        $('select[name="gender"]').find('option[value={{$user['gender']}}]').attr("selected",true);
        EditUser.init();
    });
</script>
@endsection
