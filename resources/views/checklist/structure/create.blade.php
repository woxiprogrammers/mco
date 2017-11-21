@extends('layout.master')
@section('title','Constro | Create CheckList')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

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
                                    <h1>Create CheckList</h1>
                                </div>
                                <div class="form-group " style="float: right;margin-top:1%">
                                    <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit </button>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <div class="col-md-11">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">

                                        <div class="portlet-body form">
                                            <form role="form" id="create-user" class="form-horizontal" method="post" action="/checklist/checkList/create">
                                                {!! csrf_field() !!}
                                                <div class="form-body">

                                                    <div class="form-group row">
                                                            <div class="row">

                                                            <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                <label for="main_cat" class="control-label">Select Main Category Here</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" id="main_cat" name="main_cat">
                                                                    <option value="volvo">Select Main Category From Here</option>
                                                                    <option value="volvo">Cat 1</option>
                                                                    <option value="saab">Cat 2</option>
                                                                    <option value="opel">Cat 3</option>
                                                                    <option value="audi">Cat 4</option>
                                                                </select>
                                                            </div>

                                                            <br>
                                                            <br>
                                                            <div class="col-md-5" style="text-align: right ; margin-top: 2%; margin-left: -6% ; font-size: 14px">
                                                                <label for="sub_cat" class="control-label">Select Sub Category Here</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6" style=" margin-top: 2%">
                                                                <select class="form-control" id="sub_cat" name="sub_cat">
                                                                    <option value="volvo">Select Sub Category From Here</option>
                                                                    <option value="volvo">Sub Cat 1</option>
                                                                    <option value="saab">Sub Cat 2</option>
                                                                    <option value="opel">Sub Cat 3</option>
                                                                    <option value="audi">Sub Cat 4</option>
                                                                </select>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="input_fields_wrap">

                                                        <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">

                                                            <label for="title" class="control-label">Title</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="mytext[]"  id="title_name" placeholder="Enter Title Here">
                                                            <div id="sample_editable_1_new" class="btn yellow" style="margin-top: -7%; margin-left: 105%"><button style="color: white" class="add_field_button" id="add"><i class="fa fa-plus"></i> </button>
                                                            </div>
                                                            <div style="margin-top: -6%; margin-left: 118% ; font-size: 14px" ><input type="reset" value="Reset"></div>

                                                            <div id="removeBtn"  style="margin-top: -5%; margin-left: 118%" >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5" style="text-align: right ; margin-left: -91.5% ;margin-top: 9% ; font-size: 14px">

                                                            <label for="title" class="control-label">Is Remark Mandatory</label>
                                                            <span>*</span>
                                                        </div>

                                                        <div class="col-md-2" style="text-align: right ; margin-top: 9% ; margin-left: -50% ">
                                                            <select class="form-control" id="sub_cat" name="sub_cat">
                                                                <option value="">Select Option</option>
                                                                <option value="True">Yes</option>
                                                                <option value="False">No</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-5" style="text-align: right ; margin-left: -91.5% ;margin-top: 4% ; font-size: 14px">

                                                            <label for="title" class="control-label"> Description </label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6" style="text-align: right ; margin-top: 4.5% ; margin-left: -50% ">
                                                            <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description Here"  >
                                                        </div>


                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-5" style="text-align: right ; margin-left: -90% ; margin-top: 11% ; font-size: 14px">
                                                                <label for="no_images" class="control-label"> Number Of Images</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6" style="margin-top: -2% ;margin-left: 31.5% ; font-size: 14px">
                                                                <div class="col-md-6">
                                                                    <input type="text" id="nochapter" >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6" style="margin-top:-2% ; margin-left: 31%">
                                                                <input type="button" id="setValue" value="Set" onclick="generate()" >
                                                            </div>
                                                            <div class="form-group row">
                                                            </div>
                                                        </div>
                                                        <div id="description">
                                                            <div class="form-body">
                                                                <br>
                                                                <div class="form-group row">
                                                                    <div class="col-md-6" style="text-align: right ; margin-left: 12% ; margin-top: -5% ; font-size: 14px" >
                                                                        <label for="is_special" class="control-label" style="text-align: right ">Is Mandatory ?</label>
                                                                        <span>*</span>
                                                                        <label for="description" class="control-label" style=" font-size: 14px ;text-align: left ; margin-left: 23%">Image Caption</label>
                                                                        <span>*</span>

                                                                    </div>
                                                                    <div id="extra" >
                                                                        <div class="row" style="margin-left: 21%">
                                                                            <div class="col-md-4" >
                                                                                <select class="form-control" id="sub_cat" name="sub_cat">
                                                                                    <option value="">Select Option</option>
                                                                                    <option value="True">Yes</option>
                                                                                    <option value="False">No</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-6">

                                                                                <input type="text" class="form-control" id="description" name="description" placeholder="Enter Image Description Here ">

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div id="append">

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
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <link rel="stylesheet"  href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css"/>
    <link rel="stylesheet"  href="/assets/global/css/app.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/custom/admin/checkliststructure/checklist.js" type="application/javascript"></script>
    <script src="/assets/custom/admin/checkliststructure/validation.js" type="application/javascript"></script>


    <script>
        $(document).ready(function() {
            CreateCheckListStructure.init();
        });
    </script>

@endsection
