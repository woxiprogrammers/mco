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
                                            <form role="form" id="create-user" class="form-horizontal" method="post" action="/checkList/create">
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
                                                                </select>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="input_fields_wrap">

                                                        {{--<div id="example">--}}
                                                        <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">

                                                            <label for="title" class="control-label">Title</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="mytext[]"  id="title_name" placeholder="Enter Title Here">
                                                            <div id="sample_editable_1_new" class="btn yellow" style="margin-top: -7%; margin-left: 105%"><button style="color: white" class="add_field_button" id="add"><i class="fa fa-plus"></i> </button>
                                                            </div>
                                                            <div style="margin-top: -5%; margin-left: 118% ; font-size: 14px" ><input type="reset" value="Reset"></div>

                                                            <div id="removeBtn"  style="margin-top: -5%; margin-left: 118%" >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5" style="text-align: right ; margin-left: -91.5% ;margin-top: 9% ; font-size: 14px">

                                                            <label for="title" class="control-label">Is Remark Mandatory</label>
                                                            <span>*</span>
                                                        </div>

                                                        <div class="col-md-5" style="text-align: right ; margin-top: 9% ; margin-left: -81% ">
                                                            <input type="checkbox" class="make-switch" id="is_special" data-on-text="Yes" data-off-text="No" name="is_special">
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
                                                                        <label for="is_special" class="control-label" style="text-align: right ">Is Mandatory ?</label>
                                                                        <span>*</span>
                                                                        <label for="description" class="control-label" style=" font-size: 14px ;text-align: left ; margin-left: 23%">Image Caption</label>
                                                                        <span>*</span>

                                                                    </div>
                                                                    <div id="extra">
                                                                        <div class="row">
                                                                            <div class="col-md-6" >
                                                                                <input type="checkbox" class="make-switch" id="is_special" data-on-text="Yes" data-off-text="No" name="is_special">
                                                                            </div>
                                                                            <div class="col-md-6">

                                                                                <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description Here ">

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
                                                    <div class="form-group row">
                                                        <div id="appendHere">
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
    <script src="/assets/custom/admin/checkliststructure/manage-datatable.js" type="text/javascript"></script>
    <script src="/assets/custom/admin/checkliststructure/checklist3.js" type="application/javascript"></script>

    <script>
        $(document).ready(function() {
            CreateCheckListStructure.init();
        });
    </script>
    <script>
        $('document').ready(function(){

            $('#extra').hide();
        })
    </script>
    <script>

        $(document).ready(function() {
            var max_fields      = 10; //maximum input boxes allowed
            var wrapper         = $(".input_fields_wrap"); //Fields wrapper
            var add_button      = $(".add_field_button"); //Add button ID

            var x = 1; //initlal text box count
            $(add_button).click(function(e){ //on add input button click
                e.preventDefault();
                if(x < max_fields){ //max input box allowe
                    x++; //text box increment
                    $(wrapper).append('  <form><div class="form-body"><div class="form-group row">  <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">\n' +
                        '\n' +
                        '                                                            <label for="title" class="control-label">Title</label>\n' +
                        '                                                            <span>*</span>\n' +
                        '                                                        </div>\n' +
                        '                                                        <div class="col-md-6">\n' +
                        '                                                            <input type="text" class="form-control" name="mytext[]"  id="title_name" placeholder="Enter Title Here"><div id="removeBtn"  style="margin-top: -5%; margin-left: 118%" >\n' +
                        '                                                            </div>\n' +
                        '                                                        </div>\n' +
                        '                                                        <div class="col-md-5" style="text-align: right ; margin-left: -91.5% ;margin-top: 9% ; font-size: 14px">\n' +
                        '\n' +
                        '                                                            <label for="title" class="control-label">Is Remark Mandatory</label>\n' +
                        '                                                            <span>*</span>\n' +
                        '                                                        </div>\n' +
                        '\n' +
                        '                                                        <div class="col-md-5" style="text-align: right ; margin-top: 9% ; margin-left: -81% "><div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-id-is_special bootstrap-switch-animate bootstrap-switch-on" style="width: 92px;"><div class="bootstrap-switch-container" style="width: 135px; margin-left: 0px;"><span class="bootstrap-switch-handle-on bootstrap-switch-primary" style="width: 45px;">Yes</span><span class="bootstrap-switch-label" style="width: 45px;">&nbsp;</span><span class="bootstrap-switch-handle-off bootstrap-switch-default" style="width: 45px;"></span><input type="checkbox" class="make-switch" id="is_special" data-on-text="Yes" data-off-text="No" name="is_special"></div></div> </div><div class="col-md-5" style="text-align: right ; margin-left: -91.5% ;margin-top: 4% ; font-size: 14px">\n' +
                        '\n' +
                        '                                                            <label for="title" class="control-label"> Description </label>\n' +
                        '                                                            <span>*</span>\n' +
                        '                                                        </div>\n' +
                        '                                                        <div class="col-md-6" style="text-align: right ; margin-top: 4.5% ; margin-left: -50% ">\n' +
                        '                                                            <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description Here"  >\n' +
                        '                                                        </div><div class="col-md-5" style="text-align: right ; margin-left: -91.5% ; margin-top: 13% ; font-size: 14px"> <label for="no_images" class="control-label"> Number Of Images</label><span>*</span></div><div class="col-md-6" style="margin-top: -2% ;margin-left: 31.5% ; font-size: 14px"><div class="col-md-6"><input type="text" id="nochapter" ></div></div></div></form><input type="button" class="remove_field" style="margin-top: -150px ; margin-left: 77%" value="Remove"></div>'); //add input box
                }
            });

            $(wrapper).on("click",".remove_field", function(e) { //user click on remove text

                e.preventDefault();
                $(this).parent('div').remove();
                x--;
            })

        });
    </script>
@endsection
