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
                    <form role="form" id="create-image" class="form-horizontal" method="post" action="/dpr/create-dpr">
                        <!-- BEGIN CONTENT -->
                        <div class="page-content-wrapper">
                            <div class="page-head">
                                <div class="container">
                                    <!-- BEGIN PAGE TITLE -->
                                    <div class="page-title">
                                        <h1>Create DPR Details</h1>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 12px;float: right">
                                        <button type="submit" class="btn btn-set red pull-right">
                                            <i class="fa fa-check"></i>
                                            Submit
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
                                            <a href="javascript:void(0);">Create DPR</a>
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
                                                            <select class="form-control" id="subcontractorId" name="subcontractor_id" required>
                                                                <option value="">Select Sub Contractor from here</option>
                                                                @foreach($sub_contractors as $sub_contractor)
                                                                    <option value="{{$sub_contractor['id']}}">{{$sub_contractor['subcontractor_name']}} &nbsp ({{$sub_contractor['company_name']}})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-6 col-md-offset-3" style="text-align: right">
                                                            <table class="table table-bordered" id="categoryTable" hidden>
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
