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
        <div class="page-wrapper-middle">
            <!-- BEGIN CONTAINER -->
            <div class="page-container">
                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    @if(($user->roles[0]->role->slug == 'admin') || ($user->roles[0]->role->slug == 'superadmin'))
                    <div class="page-head">
                        <div class="container">
                            <!-- BEGIN PAGE TITLE -->
                            <div class="page-title">
                                <h1>Manisha Construction Profit & Loss</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        <div class="container">
                                {{--<div class="row">
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
                                </div>--}}
                                <div class="row">
                                    <fieldset>
                                        <legend style="padding-left: 30px"> Selection Criteria </legend>
                                        <div class="col-md-2 form-group">
                                            <select class="form-control" id="year_slug" name="year_slug" style="width: 80%;">
                                                <option value="all"> -- All Year -- </option>
                                                @foreach($years as $year)
                                                    <option value="{{$year['id']}}"> {{$year['slug']}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="month_slug" id="month_slug" class="form-control" style="width: 80%;">
                                                <option value="all"> -- All Month -- </option>
                                                @foreach($months as $month)
                                                    <option value="{{$month['id']}}"> {{ucfirst($month['slug'])}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5 form-group">
                                            <select class="form-control input-lg select2-multiple" id="project_site_id" name="project_site_id" multiple="multiple" style="overflow:hidden" data-placeholder="Select Project Site">
                                                @foreach($projectSiteData as $projectSite)
                                                    <option value="{{$projectSite['project_site_id']}}"> {{$projectSite['project_site_name']}} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-1 form-group">
                                            <label>&nbsp;</label>
                                            <div class="btn-group">
                                                <div id="search-withfilter" class="btn blue" >
                                                    <a href="#" style="color: white"> Submit
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                    </fieldset>

                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat2 ">
                                            <div class="display">
                                                <div class="number">
                                                    <h3 class="font-green-sharp">
                                                        <span data-counter="counterup" id="salesValue"> 0.000 </span>
                                                        <small class="font-green-sharp"> ₹ </small>
                                                    </h3>
                                                    <hr>
                                                    <h4 class="font-green-sharp">
                                                        <span data-counter="counterup" id="salesValueWords" style="text-transform: capitalize;"></span>
                                                    </h4>
                                                    <span class="caption-subject bold uppercase" style="font-size: large"> SALE P/L </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat2 ">
                                            <div class="display">
                                                <div class="number">
                                                    <h3 class="font-red-haze">
                                                        <span data-counter="counterup" id="receiptValue">  0.000 </span>
                                                        <small class="font-red-haze"> ₹ </small>
                                                    </h3>
                                                    <hr>
                                                    <h4 class="font-red-haze">
                                                        <span data-counter="counterup" id="receiptValueWords" style="text-transform: capitalize;"></span>
                                                    </h4>
                                                    <span class="caption-subject bold uppercase" style="font-size: large"> RECEIPT P/L </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="dashboard-stat2">
                                            <div class="display">
                                                <div class="number">
                                                    <h3>
                                                        <span data-counter="counterup" id="advReceiptValue">  0.000 </span>
                                                        <small> ₹ </small>
                                                    </h3>
                                                    <hr>
                                                    <h4>
                                                        <span data-counter="counterup" id="advReceiptValueWords" style="text-transform: capitalize;"></span>
                                                    </h4>
                                                    <span class="caption-subject bold uppercase" style="font-size: large"> Advanced/RECEIPT P/L </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" >
                                        <div class="dashboard-stat2">
                                            <div class="display">
                                                <div class="number">
                                                    <h3 class="font-blue-sharp">
                                                        <span data-counter="counterup" id="outstandingMobilization">  0.000 </span>
                                                        <small class="font-blue-sharp"> ₹ </small>
                                                    </h3>
                                                    <hr>
                                                    <h4 class="font-blue-sharp">
                                                        <span data-counter="counterup" id="outstandingMobilizationWords" style="text-transform: capitalize;"></span>
                                                    </h4>
                                                    <span class="caption-subject bold uppercase" style="font-size: large"> OUTSTANDING MOBILIZATION </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    @include('partials.common.messages')
                                    <div class="col-md-12">
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet light ">
                                            <div class="portlet-title">
                                                <div class="caption font-dark">
                                                    <i class="icon-settings font-dark"></i>
                                                    <span class="caption-subject bold uppercase"> Salewise PnL Report</span>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                {!! csrf_field() !!}
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="salesTable">
                                                    <thead>
                                                        <tr style="background-color: #eaf285">
                                                            <th style="width:10%">Site Name</th>
                                                            <th style="width:10%">Sales</th>
                                                            <th style="width:10%"> Receipt </th>
                                                            <th style="width:10%"> Outstanding </th>
                                                            <th style="width:10%"> Total Expenses </th>
                                                            <th style="width:10%"> Outstanding Mobilization </th>
                                                            <th style="width:10%"> Saleswise P/L </th>
                                                            <th style="width:10%"> Receipt P/L </th>
                                                            <th style="width:10%"> Adv/Receipt P/L </th>
                                                        </tr>
                                                        <tr class="filter">
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th>
                                                                <input type="hidden" class="form-control form-filter" name="sales_month_id" id="sales_month_id">
                                                            </th>
                                                            <th>
                                                                <input type="hidden" class="form-control form-filter" name="sales_project_site_id" id="sales_project_site_id"></th>
                                                            <th>
                                                                <input type="hidden" class="form-control form-filter" name="sales_year_id" id="sales_year_id">
                                                            </th>
                                                            <th >
                                                                <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                                <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>


                                                    </tbody>
                                                    <tfoot>
                                                    <tr >
                                                        <th>Total Page Wise: </th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet light ">
                                            <div class="portlet-title">
                                                <div class="caption font-dark">
                                                    <i class="icon-settings font-dark"></i>
                                                    <span class="caption-subject bold uppercase"> Expenses </span>
                                                </div>
                                            </div>
                                            {!! csrf_field() !!}
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="expensesTable">
                                                <thead>
                                                    <tr style="background-color: #eaf285">
                                                    <th style="width:10%">Site Name</th>
                                                    <th style="width:10%"> Purchase </th>
                                                    <th style="width:10%"> Salary </th>
                                                    <th style="width:10%"> Asset Rent </th>
                                                    <th style="width:10%"> Asset Rent Opening Expense </th>
                                                    <th style="width:10%"> Subcontractor </th>
                                                    <th style="width:10%"> Misc. Purchase </th>
                                                    <th style="width:10%"> Office Expenses </th>
                                                    <th style="width:10%"> Opening Balance </th>
                                                    <th style="width:10%"> Indirect Expenses </th>
                                                    <th style="width:10%"> Total Expenses </th>
                                                </tr>
                                                    <tr class="filter"><th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th>
                                                            <input type="hidden" class="form-control form-filter" name="expense_month_id" id="expense_month_id">
                                                        </th>
                                                        <th>
                                                            <input type="hidden" class="form-control form-filter" name="expense_project_site_id" id="expense_project_site_id"></th>
                                                        <th>
                                                            <input type="hidden" class="form-control form-filter" name="expense_year_id" id="expense_year_id">
                                                        </th>
                                                        <th></th>
                                                        <th>
                                                            <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                            <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>


                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Total Page Wise: </th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                        <th style="text-align: center"></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                        <div class="portlet light ">
                                            <div class="portlet-title">
                                                <div class="caption font-dark">
                                                    <i class="icon-settings font-dark"></i>
                                                    <span class="caption-subject bold uppercase"> Advanced + Expenses </span>
                                                </div>
                                            </div>
                                            {!! csrf_field() !!}
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="advExpensesTable">
                                                <thead>
                                                <tr style="background-color: #eaf285">
                                                    <th width="15%">Site Name</th>
                                                    <th > Purchase </th>
                                                    <th > Salary </th>
                                                    <th > Asset Rent </th>
                                                    <th > Asset Rent Opening Expense </th>
                                                    <th > Subcontractor </th>
                                                    <th > Misc. Purchase </th>
                                                    <th > Office Expenses </th>
                                                    <th > Opening Balance </th>
                                                    <th > Subcontractor Advance </th>
                                                    <th > Purchase Advance </th>
                                                    <th > Indirect Expenses </th>
                                                    <th > Total Expenses </th>
                                                </tr>
                                                <tr class="filter"><th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th>
                                                        <input type="hidden" class="form-control form-filter" name="expense_month_id" id="expense_month_id">
                                                    </th>
                                                    <th>
                                                        <input type="hidden" class="form-control form-filter" name="expense_project_site_id" id="expense_project_site_id"></th>
                                                    <th>
                                                        <input type="hidden" class="form-control form-filter" name="expense_year_id" id="expense_year_id">
                                                    </th>
                                                    <th></th>
                                                    <th>
                                                        <button class="btn btn-xs blue filter-submit"> Search <i class="fa fa-search"></i> </button>
                                                        <button class="btn btn-xs default filter-cancel"> Reset <i class="fa fa-undo"></i> </button>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>


                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <th>Total Page Wise: </th>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center"></th>
                                                    <th style="text-align: center"></th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
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

                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- END CONTAINER -->
@endsection
@section('javascript')
<script src="/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

<link href="/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/assets/global/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
<script src="/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="/assets/global/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css" type="text/css"/>
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/custom/admin/dashboard/manage-datatable.js" type="text/javascript"></script>

<script>
    $(document).ready(function(){
         $("#search-withfilter").on('click',function(){
            var year_slug = $('#year_slug').val();
            var month_slug = $('#month_slug').val();
            var project_site_id = $('#project_site_id').val();
            $('#expense_year_id,#sales_year_id').val(year_slug);
            $('#expense_month_id,#sales_month_id').val(month_slug);
            $('#expense_project_site_id,#sales_project_site_id').val(project_site_id);
            /*var postData =
                'year_slug=>'+year_slug+','+
                'month_slug=>'+month_slug+','+
                'project_site_id=>'+project_site_id;
                $("input[name='sales_post_data']").val(postData);
                $("input[name='expense_post_data']").val(postData);*/
                $(".filter-submit").trigger('click');
        });
    });

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
