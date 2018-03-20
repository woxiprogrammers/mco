@extends('layout.master')
@section('title','Constro | Create New Bill for Project Site')
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
                                <h1>Create New Bill</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/bill/manage/project-site">Manage Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Create New Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">

                                    <div class="portlet-body form">
                                        <form role="form" id="create-bill" class="form-horizontal">
                                            {!! csrf_field() !!}
                                            <div class="form-body">
                                                <div class="form-group row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Client</label>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="company" name="client_id">
                                                                    <option value="">Select Client</option>
                                                                @foreach($clients as $client)
                                                                    <option value="{{$client['id']}}">{{$client['company']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Projects</label>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="project" name="project_id" disabled>

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label">Project Sites</label>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="project_sites" name="project_site_id" disabled>

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions noborder row">
                                                <div class="col-md-offset-3" style="margin-left: 26%">
                                                    <a class="btn red" id="submit"><i class="fa fa-check"></i> Submit</a>
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
<script>
    $(document).ready(function() {
        $('#submit').css("padding-left",'6px');
        $("#company").on('change', function(){
            getProjects($('#company').val());
        });
        $("#project").on('change', function(){
            getProjectSites($('#project').val());
        });
        $('#submit').on('click',function(){
            var project_site= $('#project_sites').val();
            window.location.href = "/bill/create/"+project_site;
        });
    });

    function getProjects(client_id){
        $.ajax({
            url: '/bill/projects/'+client_id,
            type: 'GET',
            async : false,
            success: function(data,textStatus,xhr){
                if(xhr.status == 200){
                    $('#project').html(data);
                    $('#project').prop('disabled',false);
                    getProjectSites($('#project').val());
                }
            },
            error: function(errorStatus,xhr){

            }
        });
    }

    function getProjectSites(project_id){
        $.ajax({
            url: '/bill/project-sites/'+project_id,
            type: 'GET',
            async : false,
            success: function(data,textStatus,xhr){
                if(xhr.status == 200){
                    $('#project_sites').html(data);
                    $('#project_sites').prop('disabled',false);
                }
            },
            error: function(errorStatus,xhr){

            }
        });
    }
</script>
@endsection
