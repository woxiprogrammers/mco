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
                                    <a href="/user/manage">Manage Users</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Edit User</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-11">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">

                                    <div class="portlet-body form">
                                        <input type="hidden" id="user_id" value="{{$user['id']}}">
                                        <ul class="nav nav-tabs nav-tabs-lg">
                                            <li class="active">
                                                <a href="#generalInfoTab" data-toggle="tab"> General Information </a>
                                            </li>
                                            {{--<li>--}}
                                                {{--<a href="#projectSiteAssignmentTab" data-toggle="tab"> Assign Project Sites </a>--}}
                                            {{--</li>--}}
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane fade in active" id="generalInfoTab">
                                                <form role="form" id="edit-user" class="form-horizontal" method="post" action="/user/edit/{{$user['id']}}">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="role_id" class="control-label">Role</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" id="role" value="{{$user->roles[0]->role->name}}" class="form-control" disabled>
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
                                                                @if($user['gender'] == 'M' || $user['gender'] == 'm')
                                                                    <option value="F">Female</option>
                                                                    <option value="M" selected>Male</option>
                                                                @else
                                                                    <option value="F" selected>Female</option>
                                                                    <option value="M">Male</option>
                                                                @endif
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
                                                    <div class="form-group">
                                                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="aclTable">
                                                            <tr>
                                                                <th style="width: 25%"> Name </th>
                                                                @foreach($permissionTypes as $permissionType)
                                                                    <th>{{$permissionType['name']}}</th>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <th style="font-size:150%;" colspan="{!! count($permissionTypes) + 1!!}">WEB</th>
                                                            </tr>
                                                            @foreach($webModuleResponse as $data)
                                                                <tr>
                                                                    <td colspan="{!! count($permissionTypes) + 1!!}">
                                                                        {{$data['module_name']}}
                                                                    </td>
                                                                </tr>
                                                                @foreach($data['submodules'] as $subModule)
                                                                    <tr>
                                                                        <td>
                                                                            {{$subModule['submodule_name']}}
                                                                        </td>
                                                                        @foreach($permissionTypes as $permissionType)
                                                                            <td style="text-align: center">
                                                                                @if(array_key_exists($permissionType['id'],$subModule['permissions']))
                                                                                    @if(in_array($subModule['permissions'][$permissionType['id']],$userWebPermissions))
                                                                                        <input type="checkbox" name="web_permissions[]" value="{{$subModule['permissions'][$permissionType['id']]}}" checked>
                                                                                    @else
                                                                                        <input type="checkbox" name="web_permissions[]" value="{{$subModule['permissions'][$permissionType['id']]}}">
                                                                                    @endif
                                                                                @else
                                                                                    <span>-</span>
                                                                                @endif
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                @endforeach
                                                            @endforeach
                                                            @if(count($mobileModuleResponse) > 0)
                                                                <tr>
                                                                    <th style="font-size:150%;" colspan="{!! count($permissionTypes) + 1!!}">MOBILE</th>
                                                                </tr>
                                                                @foreach($mobileModuleResponse as $data)
                                                                    <tr>
                                                                        <td colspan="{!! count($permissionTypes) + 1!!}">
                                                                            {{$data['module_name']}}
                                                                        </td>
                                                                    </tr>
                                                                    @foreach($data['submodules'] as $subModule)
                                                                        <tr>
                                                                            <td>
                                                                                {{$subModule['submodule_name']}}
                                                                            </td>
                                                                            @foreach($permissionTypes as $permissionType)
                                                                                <td style="text-align: center">
                                                                                    @if(array_key_exists($permissionType['id'],$subModule['permissions']))
                                                                                        @if(in_array($subModule['permissions'][$permissionType['id']],$userMobilePermissions))
                                                                                            <input type="checkbox" name="mobile_permissions[]" value="{{$subModule['permissions'][$permissionType['id']]}}" checked>
                                                                                        @else
                                                                                            <input type="checkbox" name="mobile_permissions[]" value="{{$subModule['permissions'][$permissionType['id']]}}">
                                                                                        @endif
                                                                                    @else
                                                                                        <span>-</span>
                                                                                    @endif
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>
                                                                    @endforeach
                                                                @endforeach
                                                            @endif
                                                        </table>
                                                    </div>
                                                    <div class="form-actions noborder row">
                                                        <div class="col-md-offset-3" style="margin-left: 26%">
                                                            <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            {{--<div class="tab-pane fade in" id="projectSiteAssignmentTab">--}}
                                                {{--Assign project sites here--}}
                                            {{--</div>--}}
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
</div>
@endsection

@section('javascript')
<script src="/assets/custom/user/user.js" type="application/javascript"></script>
<script>
    $(document).ready(function() {
        $('#email').css('pointer-events',"none");
        EditUser.init();
    });
</script>
@endsection
