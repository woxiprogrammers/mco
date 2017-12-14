@extends('layout.master')
@section('title','Constro | Create Main Category')
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
                    <form role="form" id="create-category" class="form-horizontal" method="post" action="/dpr/category-edit">
                     <input type="hidden" value="{{$category['id']}}" name="id">
                        <!-- BEGIN CONTENT -->
                        <div class="page-content-wrapper">
                            <div class="page-head">
                                <div class="container">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1>Create Category</h1>
                                    </div>

                                </div>
                            </div>
                            <div class="page-content">
                                @include('partials.common.messages')
                                <div class="container">
                                    <div class="col-md-12">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="portlet light ">

                                            <div class="portlet-body form">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Category Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="category" value="{{$category['name']}}" style="width: 100%" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" style="margin-top: 12px;float: right">
                                                        <button type="submit" class="btn btn-set red pull-right">
                                                            <i class="fa fa-check"></i>
                                                            Edit
                                                        </button>
                                                    </div>
                                                    <br>
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
@endsection
