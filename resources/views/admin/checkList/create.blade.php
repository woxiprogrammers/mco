@extends('layout.master')
@section('title','Constro | Create CheckList')
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
                                    <h1>Create CheckList</h1>
                                </div>
                                <div class="form-group " style="float: right;margin-top:1%">
                                    <a href="#" class="btn btn-set red pull-right">
                                        <i class="fa fa-check"></i>
                                        Submit
                                    </a>
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
                                            <form role="form" id="create-user" class="form-horizontal" method="post" action="/checkList/create">
                                                {!! csrf_field() !!}
                                                <div class="form-body"  id="example">

                                                    <div class="form-group row">
                                                        <div class="row">

                                                                <div class="col-md-5" style="text-align: right; margin-left: -6%">
                                                                    <label for="main_cat" class="control-label">Select Main Category Here</label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <select class="form-control" id="main_cat" name="main_cat">

                                                                    </select>
                                                                </div>

                                                                    <br>
                                                            <br>
                                                                        <div class="col-md-5" style="text-align: right ; margin-left: -6%">
                                                                            <label for="sub_cat" class="control-label">Select Sub Category Here</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control" id="sub_cat" name="sub_cat">
                                                                            </select>
                                                                        </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-5" style="text-align: right ; margin-left: -6.6%">

                                                        <label for="title" class="control-label">Title</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control" id="titlename" placeholder="Enter Title Here">
                                                        <div id="sample_editable_1_new" class="btn yellow" style="margin-top: -7%; margin-left: 105%"><button style="color: white" id="add"><i class="fa fa-plus"></i> </button>
                                                        </div>
                                                    </div>
                                                    <div class="form-body">
                                                        <br>

                                                        <div class="form-group row">
                                                            <div class="col-md-5" style="text-align: right ; margin-left: -6%">
                                                                <label for="description" class="control-label">Description</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description Here">
                                                            </div>
                                                        </div>

                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-5" style="text-align: right ; margin-left: -6% ; margin-top: -2%">
                                                                <label for="no_images" class="control-label">Compulsory Number Of Images</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6" style="margin-top: -2%">
                                                                <input type="text" class="form-control" id="no_images" name="no_images" placeholder="Enter Compulsory Number Of Images Here">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <br>
                                                        <div class="col-md-5" style="text-align: right ;margin-top: -5%;margin-left: -6%">
                                                            <label for="is_special" class="control-label" style="text-align: right ;margin-top: -5%;margin-left: 1%">Is Remark Mandatory ?</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6" style="margin-top: -5%;margin-left: 16%">
                                                            <input type="checkbox" class="make-switch" data-on-text="Yes" data-off-text="No" name="is_special">
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div id="appendHere" >

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
    <script src="/assets/custom/purchase/manage-datatable.js" type="text/javascript"></script>

    <script>
        $('#add').click(function(){
            alert("S");
            ($('#example').clone()).appendTo('#appendHere');
            $("form").submit(function(e){
                e.preventDefault();
            });
        })
    </script>
@endsection
