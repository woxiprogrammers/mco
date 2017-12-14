@extends('layout.master')
@section('title','Constro | Create CheckList')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    {{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>--}}

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
                        <form role="form" id="createChecklistStructureForm" class="form-horizontal" method="post" action="/checklist/structure/create">
                            {!! csrf_field() !!}
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
                                            <input type="hidden" id="numberOfCheckpoints" value="1">
                                            <div class="portlet-body form">
                                                <div class="form-body">
                                                    <div class="form-group">
                                                        <div class="col-md-5" style="text-align: right; margin-left: -6% ; font-size: 14px">
                                                            <label for="main_cat" class="control-label">Select Main Category Here</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="main_cat" name="main_category_id">
                                                                <option value="">--Select Main Category --</option>
                                                                @foreach($mainCategories as $mainCategory)
                                                                    <option value="{{$mainCategory['id']}}">{{$mainCategory['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-5" style="text-align: right ; margin-left: -6% ; font-size: 14px">
                                                            <label for="sub_cat" class="control-label">Select Sub Category Here</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="sub_cat" name="sub_category_id">

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="input_fields_wrap">
                                                        <div class="checkpoint">
                                                            <fieldset>
                                                                <legend style="margin-left: 15%">Checkpoint</legend>
                                                                <div class="form-group">
                                                                    <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">
                                                                        <label for="title" class="control-label">Description</label>
                                                                        <span>*</span>
                                                                    </div>
                                                                    <div class="col-md-7">
                                                                        <textarea class="form-control checkpoint-description" name="checkpoints[0][description]"  placeholder="Enter Description" style="width: 85%"></textarea>
                                                                        <a class="add_field_button btn blue" id="add" style="margin-left: 87%; margin-top: -4.5%">
                                                                            <i class="fa fa-plus"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">
                                                                        <label for="title" class="control-label">Is Remark Mandatory</label>
                                                                        <span>*</span>
                                                                    </div>

                                                                    <div class="col-md-2">
                                                                        <select class="form-control" id="isMandatory" name="checkpoints[0][is_mandatory]">
                                                                            <option value="false" selected>No</option>
                                                                            <option value="true">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">
                                                                        <label for="title" class="control-label"> No. of Images </label>
                                                                        <span>*</span>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control number-of-image" name="checkpoints[0][number_of_images]">
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <a class="btn blue" href="javascript:void(0);" onclick="getImageTable(this,0)">Set</a>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="col-md-7 col-md-offset-3 image-table-section">

                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                </div>
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
    <script src="/assets/custom/checklist/checklist.js" type="application/javascript"></script>
    <script src="/assets/custom/checklist/validation.js" type="application/javascript"></script>


    <script>
        $(document).ready(function() {
            CreateCheckListStructure.init();
            applyCheckpointValidation();
        });
        function applyCheckpointValidation(){
            $(".checkpoint-description, .number-of-image").each(function(){
                $(this).rules('add',{
                    required : true
                })
            });
        }
    </script>
@endsection
