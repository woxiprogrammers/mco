@extends('layout.master')
@section('title','Constro | Edit Material')
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
                                <h1>Edit Material</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/material/manage">Manage Material</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Edit Material</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-11">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <input type="hidden" id="materialId" value="{{$materialData['id']}}">
                                        <form role="form" id="edit-material" class="form-horizontal" action="/material/edit/{{$materialData['id']}}" method="post">
                                            {!! csrf_field() !!}
                                            <input name="_method" value="put" type="hidden">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Category Name</label>
                                                    <div class="col-md-6 category">
                                                        <select class="form-control" id="category_id" name="category_id">
                                                            <option value=""> -- Select Category -- </option>
                                                            @foreach($categories as $category)
                                                            <option value="{{$category['id']}}"> {{$category['name']}} </option>
                                                            @endforeach
                                                        </select>
                                                        <div>
                                                            @if(isset($materialData['categories']))
                                                                <label class="col-md-6 control-label">Already Assigned Categories</label>
                                                                @foreach($materialData['categories'] as $category)
                                                                    <label class="control-label" style="font-style: italic">{{$category['name']}} ,</label>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Is Material already created</label>
                                                    <div class="col-md-6">
                                                        <div class="mt-checkbox-list">
                                                            <label class="mt-checkbox">
                                                                <input type="checkbox" id="is_present" name="is_present">
                                                                <span></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Material Name</label>
                                                    <div class="col-md-6">
                                                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter Material Name" value="{{$materialData['name']}}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Rate</label>
                                                    <div class="col-md-6">
                                                        <input type="number" id="rate_per_unit" name="rate_per_unit" class="form-control" placeholder="Enter Rate" value="{{$materialData['rate_per_unit']}}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Unit</label>
                                                    <div class="col-md-6 units">
                                                        <input type="text" class="form-control" name="unit" value="{{$unit['name']}}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions noborder row">
                                                <div class="col-md-offset-3">
                                                    <button type="submit" class="btn btn-success btn-md" style="width:25%">Submit</button>
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
<script src="/assets/custom/admin/material/material.js" type="application/javascript"></script>
<script>
    $(document).ready(function() {
        EditMaterial.init();
        $("#name").rules('add',{
            remote: {
                url: "/material/check-name",
                type: "POST",
                data: {
                    name: function() {
                        return $( "#name" ).val();
                    },
                    material_id: function(){
                        return $("#materialId").val();
                    }
                }
            }
        });
        $('#is_present').on('click',function(){
            if($(this).prop('checked') == true){
                $('#name').rules('remove', 'remote');
            }else{
                $("#name").rules('add',{
                    remote: {
                        url: "/material/check-name",
                        type: "POST",
                        data: {
                            name: function() {
                                return $("#name" ).val();
                            },
                            material_id: function(){
                                return $("#materialId").val();
                            }
                        }
                    }
                });
            }
        });
    });
</script>
@endsection
