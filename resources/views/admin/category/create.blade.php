@extends('layout.master')
@section('title','Constro | Create Category')
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
                                <h1>Create Category

                                </h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        <div class="container">
                            <div class="col-md-11">
                            <!-- BEGIN VALIDATION STATES-->
                            <div class="portlet light ">

                                <div class="portlet-body form">
                                    <form role="form" id="create-category" class="form-horizontal">
                                        <div class="form-body">
                                            <div class="form-group row">
                                                <div class="col-md-3" style="text-align: right">
                                                    <label for="name" class="control-label">Name</label>
                                                    <span>*</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="name" name="name">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions noborder row">
                                            <div class="col-md-offset-3">
                                                <button type="submit" class="btn blue">Submit</button>
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
<script src="/assets/custom/admin/category.js" type="application/javascript"></script>
<script>
    $(document).ready(function() {
        CreateCategory.init();
    });
</script>
@endsection
