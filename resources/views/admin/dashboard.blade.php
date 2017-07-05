@extends('layout.master')
@section('title','Constro')
@include('partials.common.navbar')
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
        <!-- END PAGE HEAD-->
        <!-- BEGIN PAGE CONTENT BODY -->
        <div class="page-content content-full-height">
            <div class="container">
                <!-- BEGIN PAGE BREADCRUMBS -->

                <!-- END PAGE BREADCRUMBS -->
                <!-- BEGIN PAGE CONTENT INNER -->
                <div class="page-content-inner">
                    <div class="row">
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
