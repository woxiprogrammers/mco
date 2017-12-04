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
                                            <form role="form" id="create-client" class="form-horizontal" method="post" action="/salary-request/create">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="form-group">
                                                            <label class="col-md-3 control-label">Project Sites</label>
                                                            <div class="col-md-6">
                                                                <select class="form-control" id="project_sites" name="project_site_id">
                                                                    <option value="">Select Project Site</option>
                                                                    @foreach($projectSites as $projectSite)
                                                                        <option value="{{$projectSite['id']}}">{{$projectSite['name']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit</button>
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
        $('#project_sites').on('change',function(){
            $.ajax({
                url: '/peticash/salary-request/get-labours',
                type: 'POST',
                async: false,
                data :{
                    'project_site_id' : $('#project_sites').val()
                },
                success: function(data,textStatus,xhr){
                    if(xhr.status == 200){
                        $('#amount_limit').html(data);
                    }
                },
                error: function(data, textStatus, xhr){

                }
            });
        });
    </script>
@endsection
