<?php
/**
 * Created by Ameya Joshi.
 * Date: 8/12/17
 * Time: 6:05 PM
 */

?>
@extends('layout.master')
@section('title','Constro | Edit Checklist Structure')
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
                        <form role="form" id="editChecklistStructureForm" class="form-horizontal" method="post" action="/checklist/structure/edit/{{$checklistCategory['id']}}">
                            {!! csrf_field() !!}
                            <div class="page-head">
                                <div class="container">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1>Edit CheckList Structure</h1>
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
                                                            <input type="text" name="main_category" class="form-control" value="{{$checklistCategory->mainCategory->name}}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-5" style="text-align: right ; margin-left: -6% ; font-size: 14px">
                                                            <label for="sub_cat" class="control-label">Select Sub Category Here</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="sub_category" class="form-control" value="{{$checklistCategory->name}}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="input_fields_wrap">
                                                        @php
                                                            $jIterator = 1;
                                                        @endphp
                                                        @foreach($checklistCategory->checkpoints as $checkpoint)
                                                            <div class="checkpoint">
                                                                <fieldset>
                                                                    <legend style="margin-left: 15%">Checkpoint - {!! $jIterator++ !!}</legend>
                                                                    <div class="form-group">
                                                                        <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">
                                                                            <label for="title" class="control-label">Description</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <textarea class="form-control checkpoint-description" name="checkpoints[{{$checkpoint['id']}}][description]" placeholder="Enter Description" style="width: 85%">
                                                                                {{$checkpoint['description']}}
                                                                            </textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">
                                                                            <label for="title" class="control-label">Is Remark Mandatory</label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <select class="form-control" id="isMandatory" name="checkpoints[{{$checkpoint['id']}}][is_remark_required]">
                                                                                @if($checkpoint['is_remark_required'] == true)
                                                                                    <option value="false">No</option>
                                                                                    <option value="true" selected>Yes</option>
                                                                                @else
                                                                                    <option value="false" selected>No</option>
                                                                                    <option value="true">Yes</option>
                                                                                @endif
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">
                                                                            <label for="title" class="control-label"> No. of Images </label>
                                                                            <span>*</span>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <input type="text" class="form-control number-of-image" name="checkpoints[{{$checkpoint['id']}}][no_of_images]" value="{!! count($checkpoint->checklistCheckpointsImages) !!}">
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <a class="btn blue" href="javascript:void(0);" onclick="getImageTable({{$checkpoint['id']}},this)">Set</a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div class="col-md-7 col-md-offset-3 image-table-section">
                                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column">
                                                                                <tr>
                                                                                    <th>
                                                                                        No.
                                                                                    </th>
                                                                                    <th>
                                                                                        Caption
                                                                                    </th>
                                                                                    <th>
                                                                                        Is Required
                                                                                    </th>
                                                                                </tr>
                                                                                @php
                                                                                    $iterator = 1;
                                                                                @endphp
                                                                                @foreach($checkpoint->checklistCheckpointsImages as $checklistCheckpointsImage)
                                                                                    <tr>
                                                                                        <td>
                                                                                            {!! $iterator++ !!}.
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" class="form-control" name="checkpoints[{{$checkpoint['id']}}][images][{{$iterator-2}}][caption]" value="{{$checklistCheckpointsImage['caption']}}">
                                                                                        </td>
                                                                                        <td>
                                                                                            <select class="form-control" name="checkpoints[{{$checkpoint['id']}}][images][{{$iterator-2}}][is_required]">
                                                                                                @if($checklistCheckpointsImage['is_required'] == true)
                                                                                                    <option value="false">No</option>
                                                                                                    <option value="true" selected>Yes</option>
                                                                                                @else
                                                                                                    <option value="false" selected>No</option>
                                                                                                    <option value="true">Yes</option>
                                                                                                @endif
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                        @endforeach
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

<script>
    function getImageTable(index,element){
        var noOfImage = $(element).closest('.form-group').find('.number-of-image').val();
        if(typeof noOfImage != 'undefined' && noOfImage != '' && $.isNumeric(noOfImage)){
            $.ajax({
                url: '/checklist/structure/get-checkpoint-image-partial-view',
                type: 'POST',
                data:{
                    _token: $('input[name="_token"]').val(),
                    index: index,
                    number_of_images: noOfImage
                },
                success: function (data,textStatus,xhr) {
                    $(element).closest('.form-group').next().find('.image-table-section').html(data);
                },
                error: function (errorData) {

                }
            });
        }

    }

</script>
