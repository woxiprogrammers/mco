@extends('layout.master')
@section('title','Constro | Create Salary Request')
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
                                    <h1>Create Salary Request</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/client/manage">Manage Salary Request</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Salary Request</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">

                                        <div class="portlet-body form">
                                            <form role="form" id="create-client" class="form-horizontal" method="post" action="/peticash/salary-request/create">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="row" style="margin-left: 5%">
                                                        <div class="col-md-4 form-group">
                                                            <select class="form-control" id="clientId" style="width: 80%;">
                                                                <option value=""> -- Select Client -- </option>
                                                                @foreach($clients as $client)
                                                                    <option value="{{$client['id']}}"> {{$client['company']}} </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <select id="projectId" class="form-control" style="width: 80%;">
                                                                <option value=""> -- Select Project -- </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <select name="project_site_id" id="projectSiteId" class="form-control" style="width: 80%;">
                                                                <option value=""> -- Select Project site -- </option>
                                                            </select>
                                                        </div>

                                                    </div>
                                                    <div class="table-scrollable" id="employeeTableDiv" hidden>

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
        $(document).ready(function(){
            $("#clientId").on('change', function(){
                var clientId = $(this).val();
                if(clientId == ""){
                    $('#projectId').prop('disabled', false);
                    $('#projectId').html('');
                    $('#projectSiteId').prop('disabled', false);
                    $('#projectSiteId').html('');
                }else{
                    $.ajax({
                        url: '/peticash/projects/'+clientId,
                        type: 'GET',
                        async: true,
                        success: function(data,textStatus,xhr){
                            $('#projectId').html(data);
                            $('#projectId').prop('disabled', false);
                            var projectId = $("#projectId").val();
                            $("#projectId").trigger('change');
                        },
                        error: function(){

                        }
                    });
                }

            });
            $("#projectId").on('change', function(){
                var project_id = $("#projectId").val();
                $.ajax({
                    url: '/peticash/project-sites/'+project_id,
                    type: 'GET',
                    async: true,
                    success: function(data,textStatus,xhr){
                        if(data.length > 0){
                            $('#projectSiteId').html(data);
                            $('#projectSiteId').prop('disabled', false);
                            $("#projectSiteId").trigger('change');
                        }else{
                            $('#projectSiteId').html("");
                            $('#projectSiteId').prop('disabled', false);
                        }
                    },
                    error: function(){

                    }
                });
            });
            $('#projectSiteId').on('change',function(){
                $.ajax({
                    url: '/peticash/salary-request/get-labours',
                    type: 'POST',
                    async: false,
                    data :{
                        'project_site_id' : $('#projectSiteId').val()
                    },
                    success: function(data,textStatus,xhr){
                        $("#employeeTableDiv").html(data);
                        $("#employeeTableDiv").show();
                    },
                    error: function(data, textStatus, xhr){

                    }
                });
            });
        });
    </script>
@endsection
