@extends('layout.master')
@section('title','Constro | Edit Role')
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
                                    <h1>Edit Role</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/role/manage">Manage Role</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Edit Role</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <input type="hidden" id="role_id" value="{{$role['id']}}">
                                            <form role="form" id="edit-role" class="form-horizontal" method="post" action="/role/edit/{{$role['id']}}">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="name" name="name" value="{{$role['name']}}">
                                                        </div>
                                                    </div>
                                                         <div class="form-group row">
                                                             <div class="col-md-3" style="text-align: right">
                                                                 <label for="name" class="control-label">Type</label>
                                                                 <span>*</span>
                                                             </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" id="type" name="type" value="{{$role['type']}}">
                                                                    <option value="active">Active</option>
                                                                    <option value="not-active">Not-Active</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Modules</label>
                                                        <div class="col-md-7">
                                                            <div class="form-control product-material-select" >
                                                                <ul id="module_id" class="list-group">
                                                                    @foreach($modules as $module)
                                                                        <li class="list-group-item">
                                                                            @if(in_array($module['id'],$moduleIds))
                                                                                <input type="checkbox" name="module_id" value="{{$module->id}}" checked> {{$module->name}}
                                                                            @else
                                                                            <input type="checkbox" name="module_id" value="{{$module->id}}"> {{$module->name}}
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-offset-9">
                                                            <a class="btn btn-success btn-md" id="next_btn"> Add </a>
                                                        </div>
                                                    </div>

                                                    <div class="submodules-table-div">
                                                        <fieldset>
                                                            <legend> ACL Assignments</legend>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="SubModulesTable">
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
                                                                                        @if(in_array($subModule['permissions'][$permissionType['id']],$roleWebPermissions))
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
                                                                                            @if(in_array($subModule['permissions'][$permissionType['id']],$roleMobilePermissions))
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
                                                        </fieldset>
                                                    </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3">
                                                        <button type="submit" class="btn blue pull-right">Submit</button>
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
    <script src="/assets/custom/admin/role/role7.js" type="application/javascript"></script>
    <script>
        $(document).ready(function() {
            EditRole.init();
        });
    </script>
@endsection
