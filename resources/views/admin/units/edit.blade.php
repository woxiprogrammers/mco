<?php
/**
 * Created by PhpStorm.
 * User: Shubham Chaudhari
 * Date: 26/5/17
 * Time: 11:08 AM
 */

?>



@extends('layout.master')
@section('title','Constro | Edit Unit')
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
                                <h1>Edit Unit</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/units/manage">Manage Units</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Edit Unit</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <input type="hidden" id="unit_id" value="{{$unit['id']}}">
                                        <form role="form" id="edit-unit" class="form-horizontal" method="post" action="/units/edit/{{$unit['id']}}">
                                            {!! csrf_field() !!}
                                            <input type="hidden" name="_method" value="put">
                                            <div class="form-body">
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Name</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control" id="name" name="name" value="{{$unit['name']}}">
                                                    </div>
                                                </div>
                                            </div>
                                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('edit-units'))
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red btn-md"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                </div>
                                            @endif

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
<script src="/assets/custom/admin/units/units.js" type="application/javascript"></script>
<script>
    $(document).ready(function() {
        UnitsEdit.init();
    });
</script>
@endsection

