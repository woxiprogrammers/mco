@extends('layout.master')
@section('title','Constro')
@include('partials.common.navbar')
@section('css')
    <link href="/assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')

{!! Charts::assets() !!}

<div class="page-wrapper">
    <div class="page-wrapper-row full-height">
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <!-- BEGIN PAGE HEAD-->
        <div class="page-head">
            <div class="container">
                <!-- BEGIN PAGE TITLE -->

                <!-- END PAGE TITLE -->

            </div>
        </div>
        {!! csrf_field() !!}
        <!-- END PAGE HEAD-->
        <!-- BEGIN PAGE CONTENT BODY -->
        <div class="page-content content-full-height">
            <div class="container">
                <!-- BEGIN PAGE BREADCRUMBS -->

                <!-- END PAGE BREADCRUMBS -->
                <!-- BEGIN PAGE CONTENT INNER -->
                <div class="page-content-inner">
                    <div class="row">
                        <fieldset>
                            <legend>
                                <label style="margin-left: 1%">
                                    Notifications
                                </label>
                            </legend>
                            @for($iterator = 0; $iterator < count($projectSiteData); $iterator++)
                                @if($iterator % 4 == 0)
                                    <div class="row">
                                @endif
                                <div class="col-md-3" style="padding-left: 2%;padding-right: 2%;">
                                    <div class="panel-group accordion" id="accordion1" style="margin-top: 3%">
                                        <div class="panel panel-default">
                                            <div class="panel-heading" style="background-color: cornflowerblue">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion{{$projectSiteData[$iterator]['project_site_id']}}" href="#collapse_{{$projectSiteData[$iterator]['project_site_id']}}" style="font-size: 14px;color: white">
                                                        <b> {{$projectSiteData[$iterator]['project_site_name']}} </b>
                                                        @if((array_sum(array_column($projectSiteData[$iterator]['modules'],'notification_count'))) > 0)
                                                            <span class="badge badge-danger" style="background-color: #ed6b75 !important; margin-left: 3%">
                                                                <b>{!! array_sum(array_column($projectSiteData[$iterator]['modules'],'notification_count')) !!}</b>
                                                            </span>
                                                        @endif
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_{{$projectSiteData[$iterator]['project_site_id']}}" class="panel-collapse collapse">
                                                <div class="panel-body" style="overflow:auto;">
                                                    <table class="table table-striped table-bordered table-hover">
                                                        @foreach($projectSiteData[$iterator]['modules'] as $moduleInfo)
                                                            <tr onclick="switchProjectSiteModule({{$projectSiteData[$iterator]['project_site_id']}},'{{$moduleInfo['slug']}}')">
                                                                <td>
                                                                    <label class="control-label">
                                                                        {{$moduleInfo['name']}}
                                                                    </label>
                                                                    @if($moduleInfo['notification_count'] > 0)
                                                                        <span class="badge badge-success" style="margin-left: 2%">
                                                                            {{$moduleInfo['notification_count']}}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($iterator % 4 == 3)
                                    </div>
                                @endif
                            @endfor
                        </fieldset>
                    </div>
                    <div class="row" style="margin-top: 3%">
                        <div class="col-md-4">
                            {!! $quotationStatus->render() !!}
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-3">
                                    &nbsp;
                                </div>
                                <div class="col-md-3" style="background: #8fdf82;font-weight: bold;text-align: center;color: #ffffff">
                                    <span>Total Category : {{$totalCategory}}</span>
                                </div>
                                <div class="col-md-3" style="background: #00b3ee;font-weight: bold;text-align: center;color: #ffffff">
                                    <span>Total Materials : {{$totalMaterials}}</span>
                                </div>
                                <div class="col-md-3">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    {!! $categorywiseMaterialCount->render() !!}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <!-- END PAGE CONTENT INNER -->
    </div>
</div>
<!-- END PAGE CONTENT BODY -->
<!-- END CONTENT BODY -->
</div>
<!-- END CONTENT -->
<!-- BEGIN QUICK SIDEBAR -->

<!-- END QUICK SIDEBAR -->
</div>
<!-- END CONTAINER -->
@endsection
@section('javascript')
<script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script>
    function switchProjectSiteModule(projectSiteId, moduleSlug){
        var redirectionUrl = '';
        switch(moduleSlug){
            case 'purchase':
                redirectionUrl = '/purchase/material-request/manage';
                break;

            case 'inventory':
                redirectionUrl = '/inventory/manage';
                break;

            case 'checklist':
                redirectionUrl = '/checklist/user-assignment/manage';
                break;

            case 'peticash':
                redirectionUrl = '/peticash/peticash-approval-request/manage-salary-list'
                break;

            default :
                redirectionUrl = '/dashboard';
        }
        $.ajax({
            url: '/change-project-site',
            type: 'POST',
            data: {
                project_site_id: projectSiteId
            },
            success: function(data,textStatus,xhr){
                window.location.href = redirectionUrl;
            },
            error: function(errorData){

            }
        });
    }
</script>
@endsection
