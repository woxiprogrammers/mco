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
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <input type="hidden" id="path" name="path" value="">
                                        <input type="hidden" id="max_files_count" name="max_files_count" value="20">
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
                                                        <select class="form-control" name="unit">
                                                            @foreach($units as $unit)
                                                                @if($unit['id'] == $materialData['unit'])
                                                                    <option value="{{$unit['id']}}" selected> {{$unit['name']}} </option>
                                                                @else
                                                                    <option value="{{$unit['id']}}"> {{$unit['name']}} </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">GST</label>
                                                    <div class="col-md-6">
                                                        <input type="number" id="gst" name="gst" value="{{$materialData['gst']}}" class="form-control" placeholder="Enter GST">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">HSN Code</label>
                                                    <div class="col-md-6">
                                                        <input type="text" id="hsn_code" name="hsn_code" value="{{$materialData['hsn_code']}}" class="form-control" placeholder="Enter HSN Code">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12" style="margin-left: 20%"> </div>
                                                </div>
                                                <div id="tab_images_uploader_container" class="col-md-offset-5">
                                                    <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow" style="margin-left: 26%">
                                                        Browse</a>
                                                    <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                        <i class="fa fa-share"></i> Upload Files </a>
                                                </div>
                                                <table class="table table-bordered table-hover" style="width: 554px; margin-left: 26%; margin-top: 1%">
                                                    <thead>
                                                    <tr role="row" class="heading">
                                                        <th> Image </th>
                                                        <th> Action </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="show-product-images">
                                                    @foreach($materialImage as $image)
                                                    <tr id="image-{{$image['id']}}">
                                                        <td>
                                                            <a href="{{$image['path']}}" target="_blank" class="fancybox-button" data-rel="fancybox-button">
                                                                <img class="img-responsive" src="{{$image['path']}}" alt="" style="width:100px; height:100px;"> </a>
                                                            <input type="hidden" class="work-order-image-name" name="material_images[{{$image['id']}}][image_name]" id="work-order-image-{{$image['id']}}" value="{{$image['path']}}"/>
                                                        </td>
                                                        <td>
                                                            <a href="javascript:;" class="btn btn-default btn-sm" onclick='removeAssetImage("#image-{{$image['id']}}","{{$image['path']}}",0);'>
                                                            <i class="fa fa-times"></i> Remove </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('edit-material'))
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red btn-md"><i class="fa fa-check"></i> Submit</button>
                                                    </div>
                                                </div>
                                            @endif

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
<script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
<script src="/assets/custom/admin/material/image-datatable.js"></script>
<script src="/assets/custom/admin/material/image-upload.js"></script>
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
