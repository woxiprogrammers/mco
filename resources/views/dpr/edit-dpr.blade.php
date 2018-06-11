@extends('layout.master')
@section('title','Constro | Create Main Category')
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
                    <form role="form" id="create-image" class="form-horizontal" method="post" action="/dpr/dpr-edit">
                        <!-- BEGIN CONTENT -->
                        <div class="page-content-wrapper">
                            <div class="page-head">
                                <div class="container">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1>Edit DPR Detail</h1>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 12px;float: right">
                                        <button type="submit" class="btn btn-set red pull-right">
                                            <i class="fa fa-check"></i>
                                            Edit
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="page-content">
                                @include('partials.common.messages')
                                <div class="container">
                                    <ul class="page-breadcrumb breadcrumb">
                                        <li>
                                            <a href="/dpr/manage_dpr">Manage DPR</a>
                                            <i class="fa fa-circle"></i>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);">Edit DPR</a>
                                            <i class="fa fa-circle"></i>
                                        </li>
                                    </ul>
                                    <div class="col-md-12">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="portlet light ">

                                            <div class="portlet-body form">
                                                {!! csrf_field() !!}
                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Sub Contractor</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" value="{{$subcontractorName}}" class="form-control" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-6 col-md-offset-3" style="text-align: right">
                                                            <table class="table table-bordered" id="categoryTable">
                                                                <thead>
                                                                <tr>
                                                                    <th style="width: 50%">
                                                                        Category
                                                                    </th>
                                                                    <th>
                                                                        Number of labours
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($subcontractorDprDetailData as $subcontractorDprDetail)
                                                                        <tr>
                                                                            <td>
                                                                                {{$subcontractorDprDetail['category_name']}}
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control" name="number_of_users[{{$subcontractorDprDetail['dpr_detail_id']}}]" value="{{$subcontractorDprDetail['number_of_users']}}">
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script src="/assets/custom/dpr/dpr.js" type="application/javascript"></script>
@endsection
