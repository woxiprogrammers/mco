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
                            @if(($user->roles[0]->role->slug == 'admin') || ($user->roles[0]->role->slug == 'superadmin'))
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12" style="width: 29%">
                                        <div class="dashboard-stat2 ">
                                            <div class="display">
                                                <div class="number">
                                                    <h3 class="font-green-sharp">
                                                        <span data-counter="counterup" data-value="7800">  69,870,261.30 </span>
                                                        <small class="font-green-sharp"> ₹ </small>
                                                    </h3>
                                                    <hr>
                                                    <span class="caption-subject bold uppercase" style="font-size: large"> SALE P/L </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="width: 29%">
                                        <div class="dashboard-stat2 ">
                                            <div class="display">
                                                <div class="number">
                                                    <h3 class="font-red-haze">
                                                        <span data-counter="counterup" data-value="1349">  11,569,848.49 </span>
                                                        <small class="font-red-haze"> ₹ </small>

                                                    </h3>
                                                    <hr>

                                                    <span class="caption-subject bold uppercase" style="font-size: large"> RECEIPT P/L </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12" style="width: 29%">
                                        <div class="dashboard-stat2">
                                            <div class="display">
                                                <div class="number">
                                                    <h3 class="font-blue-sharp">
                                                        <span data-counter="counterup" data-value="1349">  2,377,534.00 </span>
                                                        <small class="font-blue-sharp"> ₹ </small>

                                                    </h3>
                                                    <hr>
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
                                                    <span class="caption-subject bold uppercase"> Sitewise PnL Report</span>
                                                </div>
                                            </div>
                                            <div class="portlet-body">
                                                {!! csrf_field() !!}
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="unitsTable">
                                                    <thead>
                                                    <tr style="background-color: #eaf285">
                                                        <th style="width:10%">Site Name</th>
                                                        <th style="width:10%">Sales</th>
                                                        <th style="width:10%"> Receipt </th>
                                                        <th style="width:10%"> Outstanding </th>
                                                        <th style="width:10%"> Total Expenses </th>
                                                        <th style="width:10%"> Outstanding Mobilization </th>
                                                        <th style="width:10%"> Sitewise P/L </th>
                                                        <th style="width:10%"> Receipt P/L </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <th >Tata</th>
                                                        <th > 49,157,387.76 </th>
                                                        <th > 41,986,940.43  </th>
                                                        <th > 1,189,881.39 </th>
                                                        <th > 41,847,242.56 </th>
                                                        <th > 1,928,211.00 </th>
                                                        <th > 7,310,145.20 </th>
                                                        <th > 139,697.87 </th>
                                                    </tr>
                                                    <tr>
                                                        <th style="width:10%"> MLCP </th>
                                                        <th style="width:10%">218,047,142.98</th>
                                                        <th style="width:10%"> 166,917,177.51 </th>
                                                        <th style="width:10%"> 26,366,044.18 </th>
                                                        <th style="width:10%"> 155,487,026.89 </th>
                                                        <th style="width:10%"> 449,323.00 </th>
                                                        <th style="width:10%"> 62,560,116.10 </th>
                                                        <th style="width:10%"> 11,430,150.62 </th>
                                                    </tr>
                                                    <tr>
                                                        <th style="width:10%"> TOTAL </th>
                                                        <th style="width:10%"> 267,204,530.75 </th>
                                                        <th style="width:10%"> 208,904,117.94  </th>
                                                        <th style="width:10%">   27,555,925.57  </th>
                                                        <th style="width:10%">   197,334,269.45  </th>
                                                        <th style="width:10%">   2,377,534.00  </th>
                                                        <th style="width:10%">   69,870,261.30  </th>
                                                        <th style="width:10%">   11,569,848.49  </th>
                                                    </tr>
                                                    </tbody>
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
                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="unitConversionTable">
                                                <thead>
                                                <tr style="background-color: #eaf285">
                                                    <th style="width:10%">Site Name</th>
                                                    <th style="width:10%"> Purchase </th>
                                                    <th style="width:10%"> Salary </th>
                                                    <th style="width:10%"> Asset Rent </th>
                                                    <th style="width:10%"> Subcontractor </th>
                                                    <th style="width:10%"> Misc. Purchase </th>
                                                    <th style="width:10%"> Indirect Expenses </th>
                                                    <th style="width:10%"> Total Expenses </th>
                                                </tr>

                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <th style="width:10%"> Tata </th>
                                                    <th style="width:10%">   17,276,805.98  </th>
                                                    <th style="width:10%">   75,846.00   </th>
                                                    <th style="width:10%">   1,132,336.00  </th>
                                                    <th style="width:10%">   11,834,194.32 </th>
                                                    <th style="width:10%">   518,678.26  </th>
                                                    <th style="width:10%">   11,009,382.00  </th>
                                                    <th style="width:10%">   41,847,242.56  </th>
                                                </tr>
                                                <tr>
                                                    <th style="width:10%"> MLCP </th>
                                                    <th style="width:10%">  104,022,582.58 </th>
                                                    <th style="width:10%">   75,846.00   </th>
                                                    <th style="width:10%">   1,132,336.00  </th>
                                                    <th style="width:10%">  38,728,202.05  </th>
                                                    <th style="width:10%">   518,678.26  </th>
                                                    <th style="width:10%"> 11,009,382.00 </th>
                                                    <th style="width:10%">   155,487,026.89  </th>
                                                </tr>
                                                <tr>
                                                    <th style="width:10%"> TOTAL </th>
                                                    <th style="width:10%">   121,299,388.56  </th>
                                                    <th style="width:10%">   151,692.00   </th>
                                                    <th style="width:10%">     2,264,672.00   </th>
                                                    <th style="width:10%">     50,562,396.37   </th>
                                                    <th style="width:10%">     1,037,356.52   </th>
                                                    <th style="width:10%">     22,018,764.00   </th>
                                                    <th style="width:10%">     197,334,269.45   </th>
                                                </tr>
                                                </tbody>
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
