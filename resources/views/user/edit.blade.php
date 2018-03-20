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
                                <h1>Edit User {{$userEdit['first_name']}} {{$userEdit['last_name']}}

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
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <input type="hidden" id="user_id" value="{{$userEdit['id']}}">
                                        <ul class="nav nav-tabs nav-tabs-lg">
                                            <li class="active">
                                                <a href="#generalInfoTab" data-toggle="tab"> General Information </a>
                                            </li>
                                            <li>
                                                <a href="#projectSiteAssignmentTab" data-toggle="tab"> Assign Project Sites </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane fade in active" id="generalInfoTab">
                                                <form role="form" id="edit-user" class="form-horizontal" method="post" action="/user/edit/{{$userEdit['id']}}">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="role_id" class="control-label">Role</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" id="role" value="{{$userEdit->roles[0]->role->name}}" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="first_name" class="control-label">First Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{$userEdit['first_name']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="last_name" class="control-label">Last Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{$userEdit['last_name']}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="dob" class="control-label">DOB</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="date" class="form-control" name="dob" id="datepicker" value="{{$userEdit['dob']}}">
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
                                                                @if($userEdit['gender'] == 'M' || $userEdit['gender'] == 'm')
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
                                                            <input type="email" class="form-control" id="email" name="email" value="{{$userEdit['email']}}" tabindex="-1">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="mobile" class="control-label">Contact Number</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="mobile" name="mobile" value="{{$userEdit['mobile']}}">
                                                        </div>
                                                    </div>
                                                    @if($purchaseOrderCreatePermission > 0)
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="purchase_order_amount_limit" class="control-label">Purchase Order Amount Limit</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="purchase_order_amount_limit" name="purchase_order_amount_limit" value="{{$userEdit['purchase_order_amount_limit']}}">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($peticashManagementPermission > 0)
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="purchase_order_amount_limit" class="control-label">Peticash Purchase Amount Limit</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="purchase_peticash_amount_limit" name="purchase_peticash_amount_limit" value="{{$userEdit['purchase_peticash_amount_limit']}}">
                                                            </div>
                                                        </div>
                                                    @endif
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
                                            <div class="tab-pane fade in" id="projectSiteAssignmentTab">
                                                <div class="row" style="margin-top: 2%">
                                                    <div class="col-md-3">
                                                        <label class="control-label pull-right">Project Site Name</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control typeahead" id="siteTypeahead">
                                                    </div>

                                                </div>
                                                @if($showSiteTable == true)
                                                    <div class="row"  style="margin-top: 2%">
                                                @else
                                                    <div class="row"  style="margin-top: 2%" hidden>
                                                @endif
                                                        <div class="col-md-3">
                                                            <a class="btn blue pull-right" id="removeButton" >Remove Project Site</a>
                                                        </div>
                                                    </div>
                                                <div class="row"  style="margin-top: 0.5%">
                                                    <div class="col-md-8 col-md-offset-2">
                                                        <form role="form" id="assignSiteForm" action="/user/project-site/assign/{{$userEdit->id}}" method="POST">
                                                            {{ csrf_field() }}
                                                            @if($showSiteTable == true)
                                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="assignSiteTable">
                                                            @else
                                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="assignSiteTable" hidden>
                                                            @endif
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 10%;">Remove</th>
                                                                        <th> Project Site Information</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($projectSites as $projectData)
                                                                        <tr>
                                                                            <td style="width: 10%;">
                                                                                <input type="checkbox" class="project-row-checkbox">
                                                                            </td>
                                                                            <td>
                                                                                <input name="project_sites[]" type="hidden" value="{{$projectData['project_site_id']}}">
                                                                                <div class="row">
                                                                                    <div class="col-md-3">
                                                                                        <label class="control-label pull-right">
                                                                                            <b>Client</b>
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="col-md-9"  style="text-align: left">
                                                                                        <label class="control-label">{{$projectData['client_company']}}</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-md-3"  style="text-align: left">
                                                                                        <label class="control-label pull-right">
                                                                                            <b>Project</b>
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="col-md-9"  style="text-align: left">
                                                                                        <label class="control-label">{{$projectData['project_name']}}</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-md-3">
                                                                                        <label class="control-label pull-right">
                                                                                            <b>Project Site</b>
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="col-md-9"  style="text-align: left">
                                                                                        <label class="control-label" style="text-align: left">{{$projectData['project_site_name']}}</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-md-3">
                                                                                        <label class="control-label pull-right">
                                                                                            <b>Project Site Address</b>
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="col-md-9"  style="text-align: left">
                                                                                        <label class="control-label">{{$projectData['address']}}</label>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
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
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="/assets/custom/user/user.js" type="application/javascript"></script>
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<script>
    $(document).ready(function() {
        EditUser.init();
        var citiList = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: "/user/project-site/auto-suggest/%QUERY",
                filter: function(x) {
                    if($(window).width()<420){
                        $("#header").addClass("fixed");
                    }
                    return $.map(x, function (data) {
                        return {
                            id:data.project_site_id,
                            client_company:data.client_company,
                            project_name:data.project_name,
                            project_site_name:data.project_site_name,
                            tr_view:data.tr_view
                        };
                    });
                },
                wildcard: "%QUERY"
            }
        });
        citiList.initialize();
        $('.typeahead').typeahead(null, {
            displayKey: 'name',
            engine: Handlebars,
            source: citiList.ttAdapter(),
            limit: 30,
            templates: {
                empty: [
                    '<div class="empty-suggest">',
                    'Unable to find any Result that match the current query',
                    '</div>'
                ].join('\n'),
                suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{project_name}}</strong></div>')
            },
        })
        .on('typeahead:selected', function (obj, datum) {
            var POData = $.parseJSON(JSON.stringify(datum));
            var trString = '<tr>' +
                '           <th style="width: 10%;"><input type="checkbox" class="project-row-checkbox"></th>\n' +
                '           <th>'+POData.tr_view+'</th></tr>';
            $("#assignSiteTable tbody").append(trString);
            $("#removeButton").closest('.row').show();
            $("#assignSiteTable").show();
        })
        .on('typeahead:open', function (obj, datum) {

        });

        $("#removeButton").on('click',function(){
            if($("#assignSiteTable tbody input:checkbox:checked").length > 0){
                $("#assignSiteTable tbody input:checkbox:checked").each(function(){
                    $(this).closest('tr').remove();
                });
            }
            if($("#assignSiteTable tbody input:checkbox").length <= 0){
                $("#removeButton").closest('.row').hide();
                $("#assignSiteTable").hide();
            }
        });
    });
</script>
@endsection
