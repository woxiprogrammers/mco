@extends('layout.master')
@section('title','Constro | Edit Sub Category')
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
                                    <h1>Edit Sub Category</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/drawing/category-management/manage">Manage Sub Category</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Edit Sub Category</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <form role="form" id="create-sub" class="form-horizontal" method="POST" action="/drawing/category-management/edit-sub-category">
                                                {!! csrf_field() !!}
                                                <input type="hidden" name="id" value="{{$name['id']}}">
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Name of Main Category</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="main_category" name="main_category_id" required>
                                                                <option value="">Select Main Category from here</option>
                                                                @foreach($categories as $category)
                                                                    @if($drawing_category_id == $category['id'])
                                                                        <option value="{{$category['id']}}" selected>{{$category['name']}}</option>
                                                                    @else
                                                                        <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Name of Sub Category</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="sub_category" name="sub_category" value="{{$name['name']}}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-drawing-category') || $user->customHasPermission('edit-drawing-category'))
                                                    <div class="form-actions noborder row">
                                                        <div class="col-md-offset-3" style="margin-left: 26%">
                                                            <button type="submit" class="btn red" id="submit" style="padding-left: 6px"><i class="fa fa-check"></i> Submit</button>
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
    <script>
        $(document).ready(function(){
            EditSub.init();
        });
    </script>

@endsection
