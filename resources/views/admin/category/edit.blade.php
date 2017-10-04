@extends('layout.master')
@section('title','Constro | Edit Category')
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
                                <h1>Edit Category {{$category['name']}}

                                </h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/category/manage">Manage Category</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Edit Category</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">

                                    <div class="portlet-body form">
                                        <input type="hidden" id="category_id" value="{{$category['id']}}">
                                        <form role="form" id="edit-category" class="form-horizontal" method="post" action="/category/edit/{{$category['id']}}">
                                            {!! csrf_field() !!}
                                            <div class="form-body">
                                                <div class="form-group row">
                                                    <div class="col-md-3" style="text-align: right">
                                                        <label for="name" class="control-label">Name</label>
                                                        <span>*</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control" id="name" name="name" value="{{$category['name']}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions noborder row">
                                                <div class="col-md-offset-3" style="margin-left: 26%">
                                                    <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
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
<script src="/assets/custom/admin/category/category.js" type="application/javascript"></script>
<script>
    $(document).ready(function() {
        EditCategory.init();
            $('#submit').css("padding-left",'6px');
    });
</script>
@endsection
