@extends('layout.master')
@section('title','Constro | Create Bill')
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
                                <h1>Create Bill</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/bill/manage">Manage Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Create Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-11">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">

                                    <div class="portlet-body form">
                                        <ul class="nav nav-tabs">
                                                <li class="active">
                                                    <a href="#tab_general_1" data-toggle="tab" id="tab_general_a">1st tab </a>
                                                </li>
                                            <li class="active">
                                                <a href="#tab_general_2" data-toggle="tab" id="tab_general_a">2nd tab </a>
                                            </li>
                                            <li class="active">
                                                <a href="#tab_general_3" data-toggle="tab" id="tab_general_a">3rd tab </a>
                                            </li>
                                        </ul>
                                        <form role="form" id="create-bill" class="form-horizontal" method="post" action="/bill/create">
                                            {!! csrf_field() !!}
                                            <div class="tab-content">
                                                <div class="tab-pane fade in active" id="tab_general_1">
                                                    fnsjkfb
                                                </div>
                                                <div class="tab-pane fade in active" id="tab_general_2">
                                                    fnsjkfb jsfhnj
                                                </div>
                                                <div class="tab-pane fade in active" id="tab_general_3">
                                                    fnsjkfb
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
        console.log('here');
        console.log($('#company').val());
        //getProjects($('#company').val());

    function getProjects(client_id){
        $.ajax({
            url: '/bill/get-projects/'+client_id,
            type: 'GET',
            async : false,
            success: function(data,textStatus,xhr){
                if(xhr.status == 200){
                    console.log(data);
                }else{

                }
            },
            error: function(errorStatus,xhr){

            }
        });
    }
    });
</script>
@endsection
