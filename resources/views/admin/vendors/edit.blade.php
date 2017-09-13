@extends('layout.master')
@section('title','Constro | Edit Vendor')
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
                                    <h1>Edit Vendor

                                    </h1>
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
                                        <a href="javascript:void(0);">Edit Vendor</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">

                                        <div class="portlet-body form">
                                            <input type="hidden" id="vendors_id" value="{{$vendor['id']}}">
                                            <form role="form" id="edit-vendor" class="form-horizontal" method="post" action="/vendors/edit/{{$vendor['id']}}">
                                                {!! csrf_field() !!}
                                                <input name="_method" value="put" type="hidden">
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="name" name="name" value="{{$vendor['name']}}">
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
                                                            <input type="text" class="form-control" id="company" name="company" value="{{$vendor['company']}}">
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
                                                            <input type="text" class="form-control" id="mobile" name="mobile" value="{{$vendor['mobile']}}">
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
                                                            <input type="text" class="form-control" id="email" name="email" value="{{$vendor['email']}}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="gstin" class="control-label">GSTIN</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="gstin" name="gstin" value="{{$vendor['gstin']}}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="alternate_contact" class="control-label">Alternate Contact Number</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="alternate_contact" name="alternate_contact" value="{{$vendor['alternate_contact']}}">
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
                                                            <select class="form-control" id="cityId" name="city">
                                                                <option value=""> -- Select City -- </option>
                                                                @foreach($cities as $city)
                                                                    <option value="{{$city['id']}}"> {{$city['name']}} </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label class="control-label">State Name</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="state" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label class="control-label">Country Name</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="country" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3">
                                                        <button type="submit" class="btn blue">Submit</button>
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
    <script src="/assets/custom/admin/vendor/vendors1.js" type="application/javascript"></script>
    <script>
        $(document).ready(function() {
            EditVendor.init();
        });
    </script>
@endsection
