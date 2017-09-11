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
                                    <a href="/checkList/manage" class="btn btn-set red pull-right">
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

                                                                <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                                    <label for="main_cat" class="control-label">Select Main Category Here</label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <select class="form-control" id="main_cat" name="main_cat">

                                                                    </select>
                                                                </div>

                                                                    <br>
                                                            <br>
                                                                        <div class="col-md-5" style="text-align: right ; margin-left: -6% ; font-size: 14px">
                                                                            <label for="sub_cat" class="control-label">Select Sub Category Here</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <select class="form-control" id="sub_cat" name="sub_cat">
                                                                            </select>
                                                                        </div>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">

                                                        <label for="title" class="control-label">Title</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control" id="titlename" placeholder="Enter Title Here">
                                                        <div id="sample_editable_1_new" class="btn yellow" style="margin-top: -9%; margin-left: 105%"><button style="color: white" id="add"><i class="fa fa-plus"></i> </button>
                                                        </div>
                                                        <div id="removeBtn"  style="margin-top: -5%; margin-left: 118%" >
                                                        </div>
                                                    </div>

                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-5" style="text-align: right ; margin-left: -42% ; margin-top: 3% ; font-size: 14px">
                                                                <label for="no_images" class="control-label"> Number Of Images</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6" style="margin-top: 3% ;margin-left: -2% ; font-size: 14px">
                                                                <div class="col-md-6">
                                                                    <input type="text" id="nochapter" >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6" style="margin-top:-3% ; margin-left: 37%">
                                                            <input type="button" value="Set" onclick="generate()" >
                                                        </div>
                                                        <div class="form-group row">
                                                        </div>
                                                    </div>
                                                    <div id="description">
                                                        <div class="form-body">
                                                        <br>
                                                        <div class="form-group row">
                                                            <div class="col-md-6" style="text-align: right ; margin-left: 12% ; margin-top: -5% ; font-size: 14px" >
                                                                <label for="is_special" class="control-label" style="text-align: right ">Is Remark Mandatory ?</label>
                                                                <span>*</span>
                                                                <label for="description" class="control-label" style=" font-size: 14px ;text-align: left ; margin-left: 23%">Description</label>
                                                                <span>*</span>

                                                            </div>
                                                            <div id="extra">
                                                             <div class="row">
                                                                 <div class="col-md-6" >
                                                                     <input type="checkbox" class="make-switch" data-on-text="Yes" data-off-text="No" name="is_special">
                                                                 </div>
                                                                 <div class="col-md-6">
                                                                     <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description Here"  >
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
                                                <div id="appendHere" >

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
    <script src="/assets/custom/purchase/manage-datatable.js" type="text/javascript"></script>
    <script>
        $('document').ready(function(){

            $('#extra').hide();
        })
    </script>
    <script>
        $('#add').click(function(){
            ($('#example').clone()).appendTo('#appendHere');
            $('#removeBtn').append("<button style=color:white; ><i class=fa fa-minus id=removeBtn></i> </button>");
            $("form").submit(function(e){
                e.preventDefault();
            });
        })
        $('#removeBtn').click(function(){
            $('#appendHere').remove();
        })
    </script>
    <script>
        function generate() {

            var a = parseInt(document.getElementById("nochapter").value);

            for (i = 0; i < a; i++) {

                ($('#extra').clone()).appendTo('#append');
                $('#extra').show();

            }
        }

    </script>

@endsection
