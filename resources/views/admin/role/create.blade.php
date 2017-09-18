@extends('layout.master')
@section('title','Constro | Create Role')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
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
                                    <h1>Create Role</h1>
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
                                        <a href="javascript:void(0);">Create Role</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <form role="form" id="create-role" class="form-horizontal" method="post" action="/role/create">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <fieldset>
                                                        <legend> General Information </legend>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">

                                                            <label for="name" class="control-label">Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="name" name="name">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="type" class="control-label">Type</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="type" name="type">
                                                                <option value="active">Active</option>
                                                                <option value="not-active">Not-Active</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    </fieldset>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Modules</label>
                                                    <div class="col-md-7">
                                                        <div class="form-control product-material-select" >
                                                            <ul id="module_id" class="list-group">
                                                                @foreach($modules as $module)
                                                                    <li  class="list-group-item"><input type="checkbox" name="module_id" value="{{$module->id}}"> {{$module->name}}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-offset-9">
                                                        <a class="btn btn-success btn-md" id="next_btn">Next >></a>
                                                    </div>
                                                </div>
                                        <div class="submodules-table-div" hidden>
                                            <fieldset>
                                                <legend> ACL Assignments</legend>
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="SubModulesTable">

                                                </table>
                                            </fieldset>
                                               <div class="form-group">
                                                        <div class="col-md-3 col-md-offset-4">
                                                            <button type="submit" class="btn btn-success"> Submit </button>
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
    <script src="/assets/custom/admin/role/role9.js" type="application/javascript"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script>
        $(document).ready(function() {
            CreateRole.init();
        });
    </script>
@endsection
