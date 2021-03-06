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
                                <h1>Create Category</h1>
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
                                    <a href="javascript:void(0);">Create Category</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                            <!-- BEGIN VALIDATION STATES-->
                            <div class="portlet light ">

                                <div class="portlet-body form">
                                    <form role="form" id="create-category" class="form-horizontal" method="post" action="/category/create">
                                        {!! csrf_field() !!}
                                        <input type="hidden" value="false" name="is_miscellaneous" id="is_miscellaneous" >
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
                                        <div class="form-body">
                                            <div class="form-group row">
                                                <div class="col-md-3" style="text-align: right">

                                                </div>
                                                <div class="col-md-6">
                                                    <div class="md-checkbox">
                                                        <input type="checkbox" value="false" id="checkbox1" class="md-check">
                                                        <label for="checkbox1">
                                                            <span class="inc"></span>
                                                            <span class="check"></span>
                                                            <span class="box"></span> Is Miscellaneous </label>
                                                     </div>
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
        CreateCategory.init();
    });
    $('#checkbox1').change(function(){
        if($('#checkbox1').is(':checked')){
            $('#is_miscellaneous').val('true');
        }else{
            $('#is_miscellaneous').val('false');
        }
    });
</script>
@endsection
