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
                            <div class="col-md-11">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <input type="hidden" id="categoryId" value="{{$materialData['category_id']}}">
                                        <input type="hidden" id="unitId" value="{{$materialData['unit']}}">
                                        <input type="hidden" id="materialId" value="{{$materialData['id']}}">
                                        <form role="form" id="edit-material" class="form-horizontal" action="/material/edit/{{$materialData['id']}}" method="post">
                                            {!! csrf_field() !!}
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Category Name</label>
                                                    <div class="col-md-6 category">
                                                        <select class="form-control" id="category_id" name="category_id">
                                                            @foreach($categories as $category)
                                                            <option value="{{$category['id']}}"> {{$category['name']}} </option>
                                                            @endforeach
                                                        </select>
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
                                                        <select class="form-control" id="unit" name="unit">
                                                            @foreach($units as $unit)
                                                            <option value="{{$unit['id']}}"> {{$unit['name']}} </option>
                                                            @endforeach
                                                        </select>
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
        var category = $('#categoryId').val();
        var unit = $('#unitId').val();
        $(".category option[value='"+ category +"']").prop('selected', true);
        $(".units option[value='"+ unit +"']").prop('selected', true);
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
