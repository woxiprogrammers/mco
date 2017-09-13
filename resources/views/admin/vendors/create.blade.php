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

                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->



                                                <div class="portlet light ">
                                                    <div class="table-toolbar">
                                                        <ul class="nav nav-tabs nav-tabs-lg">
                                                            <li class="active">
                                                                <a href="#generaltab" data-toggle="tab"> General </a>
                                                            </li>
                                                            <li>
                                                                <a href="#materialassigntab" data-toggle="tab"> Material Assign </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="portlet-body form">
                                                    <form role="form" id="create-vendor" class="form-horizontal" method="post" action="/vendors/create">
                                                        {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="tab-content">
                                                        <div class="tab-pane fade in active" id="generaltab">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="name" name="name">
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
                                                            <label for="alternate_email" class="control-label">Alternate Email Address </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="alternate_email" name="alternate_email">
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
                                                            <ul id="cityList" class="list-group">
                                                                @foreach($cityArray as $city)
                                                                    <li><input type="checkbox" value={{$city['name']}} name="cities[{{$city['id']}}]" > {{$city['name']}} </li>
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
                                                        </div>

                                                        <div class="tab-pane fade in" id="materialassigntab">
                                                            <fieldset>
                                                                    <div class="form-group">
                                                                        <label class="col-md-3 control-label">Category</label>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control" id="category_name" name="category_id">
                                                                                @foreach($categories as $category)
                                                                                    <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div id="dem1">
                                                                        <div class="form-group">
                                                                            <label class="col-md-3 control-label">Material</label>
                                                                            <div class="col-md-7">
                                                                                <div class="form-control product-material-select" >
                                                                                    <ul id="material_id" class="list-group">

                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="col-md-offset-9">
                                                                            <input type="button" value="Next" id="next">
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                                <div id="demo">
                                                                    <div class="materials-table-div" hidden>
                                                                        <fieldset>
                                                                            <legend> Materials</legend>
                                                                            <span>*</span>

                                                                        </fieldset>
                                                                        <fieldset>
                                                                            <div class="form-body">
                                                                                <div class="form-group">
                                                                                    <div class="col-md-3 col-md-offset-4" style="margin-left: 78%">
                                                                                        <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </fieldset>
                                                                    </div>
                                                                </div>
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
    <script src="/assets/custom/admin/vendor/vendors1.js" type="application/javascript"></script>
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#cityId').trigger('change');
            CreateVendor.init();
        });
    </script>
    <script>
        $(document).ready(function(){
            getMaterials($("#category_name").val());
            CreateVendor.init();
            $('#submit').css("padding-left",'6px');

            $("#next").on('click', function(){
                var selectMaterialIds = [];
                $("#material_id input:checkbox:checked").each(function () {
                    selectMaterialIds.push($(this).val());
                    console.log($(this).next().text());
                });
                var selectCityIds = [];
                $("#cityList input:checkbox:checked").each(function () {
                    selectCityIds.push($(this).val());
                    console.log($(this).next().text());
                });
                console.log(selectMaterialIds , selectCityIds);
            });
        });
    </script>
@endsection
