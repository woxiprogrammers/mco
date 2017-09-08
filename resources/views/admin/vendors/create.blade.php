@extends('layout.master')
@section('title','Constro | Create Vendor')
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
                                    <h1>Create Vendor</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/vendors/manage">Manage Vendor</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Vendor</a>
                                    </li>
                                </ul>
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#">General</a>
                                    </li>
                                    <li>
                                        <a href="/vendors/material">Material Assign </a>
                                    </li>

                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">

                                        <div class="portlet-body form">
                                            <form role="form" id="create-vendor" class="form-horizontal" method="post" action="/vendors/create">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="name" name="name">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="company" class="control-label">Company</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="company" name="company">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="mobile" class="control-label">Mobile Number</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="mobile" name="mobile">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="email" class="control-label">Email Address</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="email" name="email">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                    </div>
                                                    <div class="form-body">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="gstin" class="control-label">GSTIN</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="gstin" name="gstin">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="alternate_contact" class="control-label">Alternate Contact Number</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="alternate_contact" name="alternate_contact">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label class="control-label">City Name</label>
                                                            <span>*</span>
                                                        </div>
                                                    <div class="col-md-6">
                                                        <div class="form-control product-material-select" style="height: 150px;" >
                                                            <ul id="material_id" class="list-group">
                                                                @foreach($cityArray as $city)
                                                                    <li><input type="checkbox" name="cities[{{$city['id']}}]" > {{$city['name']}} </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red" style=" padding-left: 6px"><i class="fa fa-check"></i> Submit</button>
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
    <script src="/assets/custom/admin/vendors/vendor112.js" type="application/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#cityId').trigger('change');
            CreateVendor.init();
        });
    </script>
@endsection
