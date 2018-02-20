@extends('layout.master')
@section('title','Constro | Add Amount to Master Peticash Account')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
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
                                <h1>Edit Amount to Master Peticash Account</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/peticash/master-peticash-account/manage">Manage Master Peticash Account</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Edit Amount to Master Peticash Account</a>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <form role="form" id="edit-master-account" class="form-horizontal" method="post" action="/peticash/master-peticash-account/edit">
                                            {!! csrf_field() !!}
                                            <div class="form-body">
                                                <fieldset>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="type" class="control-label">Allocate From</label>
                                                            <span>:</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="type" class="control-label">{{$from_id}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="type" class="control-label">Allocate To</label>
                                                            <span>:</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="type" class="control-label">{{$to_id}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="type" class="control-label">On Date</label>
                                                            <span>:</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="type" class="control-label">{{$date}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="type" class="control-label">Payment Type</label>
                                                            <span>:</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="type" class="control-label">{{$payment_id}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="type" class="control-label">Remark</label>
                                                            <span>:</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="type" class="control-label">{{$remark}}</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="type" class="control-label">Amount</label>
                                                            <span>:</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="amount" name="amount" value="{{$amount}}" required="required">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="type" class="control-label">Created On</label>
                                                            <span>:</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="type" class="control-label">{{$created_on}}</label>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-md-offset-2">
                                                    <input type="hidden" name="txn_id" id="txn_id" value="{{$txn_id}}">
                                                    <button type="submit" class="btn btn-success"> Update </button>
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
<script src="/assets/custom/peticash/peticash.js" type="text/javascript"></script>
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<script>
    $(document).ready(function() {
        EditAmtToAccount.init();
    });
</script>
@endsection
