<?php
/**
 * Created by Ameya Joshi.
 * Date: 15/6/17
 * Time: 12:46 PM
 */
?>
@extends('layout.master')
@section('title','Constro | Edit Project')
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
                                <h1>Edit Projects</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/project/manage">Manage Projects</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Edit Project</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <ul class="nav nav-tabs nav-tabs-lg">
                                            <li class="active">
                                                <a href="#generalInfoTab" data-toggle="tab"> General Information </a>
                                            </li>
                                            <li>
                                                <a href="#advancePaymentTab" data-toggle="tab"> Advance Payments </a>
                                            </li>
                                            <li>
                                                <a href="#indirectExpenseTab" data-toggle="tab"> Indirect Expenses </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane fade in active" id="generalInfoTab">
                                                <form role="form" id="createProject" class="form-horizontal" method="post" action="/project/edit/{{$projectData['id']}}">
                                                    <input type="hidden" name="project_id" id="projectId" value="{{$projectData['id']}}">
                                                    <input type="hidden" name="_method" value="put">
                                                    {!! csrf_field() !!}
                                                    <div class="form-body">
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="name" class="control-label">Client</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input name="client" class="form-control" id="client" value="{{$projectData['client']}}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="name" class="control-label">Project Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" name="project_name" class="form-control" id="projectName" value="{{$projectData['project']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="name" class="control-label">Project Site Name</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" id="projectSiteName" name="project_site_name" class="form-control" value="{{$projectData['project_site']}}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="name" class="control-label">Project Site address</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <textarea id="siteAddress" name="address" class="form-control">{{$projectData['project_site_address']}}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="name" class="control-label">HSN code</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <select class="form-control" name="hsn_code" id="hsnCode">
                                                                    @foreach($hsnCodes as $hsnCode)
                                                                        @if($projectData['project_hsn_code'] == $hsnCode['id'])
                                                                            <option value="{{$hsnCode['id']}}" selected>{{$hsnCode['code']}}</option>
                                                                        @else
                                                                            <option value="{{$hsnCode['id']}}">{{$hsnCode['code']}}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                @foreach($hsnCodes as $hsnCode)
                                                                    <span class="hsn-description" id="hsnCodeDescription-{{$hsnCode['id']}}" hidden>
                                                                {{$hsnCode['description']}}
                                                            </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="form-body">
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label class="control-label">City Name</label>
                                                                    <span>*</span>
                                                                </div>

                                                                <div class="col-md-4">

                                                                    <select class="form-control" name="city_id" id="city_id">
                                                                        @foreach($cityArray as $city)
                                                                            @if($projectData['project_city_id'] == $city['id'])
                                                                                <option value="{{$city['id']}}" selected>{{$city['name']}}</option>
                                                                            @else
                                                                                <option value="{{$city['id']}}">{{$city['name']}}</option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>


                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('edit-manage-sites'))
                                                            <div class="form-actions noborder row">
                                                                <div class="col-md-offset-3" style="margin-left: 26%">
                                                                    <button type="submit" class="btn red"><i class="fa fa-check"></i> Submit</button>
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane fade in" id="advancePaymentTab">
                                                <div class="btn-group pull-right margin-top-15">
                                                    <a id="sample_editable_1_new" class="btn yellow" href="#paymentModal" data-toggle="modal" >
                                                        <i class="fa fa-plus"></i>  &nbsp; Advance Payment
                                                    </a>
                                                </div>
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="advancePaymentTable">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 25%"> Date </th>
                                                        <th style="width: 25%"> Amount </th>
                                                        <th style="width: 25%"> Payment Method </th>
                                                        <th style="width: 25%"> Reference Number </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade in" id="indirectExpenseTab">
                                                <div class="btn-group pull-right margin-top-15">
                                                    <a id="sample_editable_1_new" class="btn yellow" href="#indirectExpenseModal" data-toggle="modal" >
                                                        <i class="fa fa-plus"></i>  &nbsp; Indirect Expense
                                                    </a>
                                                </div>
                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="indirectExpenseTable">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 33%"> Date </th>
                                                        <th style="width: 33%"> GST </th>
                                                        <th style="width: 33%"> TDS </th>
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
        </div>
    </div>
</div>
<div class="modal fade" id="paymentModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:10px">
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4"> Add Advacnce Payment</div>
                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                </div>
            </div>
            <div class="modal-body" style="padding:40px 50px;">
                <form id="paymentCreateForm" method="post" action="/project/advance-payment/create">
                    {!! csrf_field() !!}
                    <input type="hidden" name="project_site_id" id="projectSiteId" value="{{$projectData['project_site_id']}}">
                    <div class="form-group row"id="bankSelect">
                        <div class="col-md-4">
                            <label class="pull-right control-label">
                                Bank:
                            </label>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control" id="bank_id" name="bank_id" onchange="checkAmount()">
                                <option value="default">Select Bank</option>
                                @foreach($banks as $bank)
                                    <option value="{{$bank['id']}}">{{$bank['bank_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="allowedAmount">
                    @foreach($banks as $bank)
                        <input type="hidden" id="balance_amount_{{$bank['id']}}" value="{{$bank['balance_amount']}}">
                    @endforeach

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="pull-right control-label">
                                Amount
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="amount" placeholder="Enter Amount" onkeyup="checkAmount()">
                        </div>
                    </div>
                    <div class="form-group row"id="paymentSelect">
                        <div class="col-md-4">
                            <label class="pull-right control-label">
                                Payment Mode:
                            </label>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control" name="payment_id" >
                                @foreach($paymentTypes as $paymentType)
                                    <option value="{{$paymentType['id']}}">{{$paymentType['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="pull-right control-label">
                                Reference Number
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="reference_number" placeholder="Enter Reference Number">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: 5%">
                        <div class="col-md-6">
                            <button type="submit" class="btn red pull-right">
                                <i class="fa fa-check" style="font-size: large"></i>
                                Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="indirectExpenseModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:10px">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-6"><h4><b>Add Indirect Expense</b></h4></div>
                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                </div>
            </div>
            <div class="modal-body" style="padding:40px 50px;">
                <form method="post" action="/project/indirect-expense/create">
                    <input type="hidden" name="project_site_id" value="{{$projectData['project_site_id']}}">
                    {!! csrf_field() !!}
                    <div class="form-body">
                        <div class="form-group row">
                            <div class="col-md-3" style="text-align: right">
                                <label for="name" class="control-label">GST</label>
                                <span>*</span>
                            </div>
                            <div class="col-md-6">
                                <input name="gst" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3" style="text-align: right">
                                <label for="name" class="control-label">TDS</label>
                                <span>*</span>
                            </div>
                            <div class="col-md-6">
                                <input name="tds" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row" style="margin-top: 5%">
                            <div class="col-md-6">
                                <button type="submit" class="btn red pull-right">
                                    <i class="fa fa-check" style="font-size: large"></i>
                                    Submit
                                </button>
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
<script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/custom/admin/project/project.js" type="application/javascript"></script>
<script src="/assets/custom/admin/project/project-site-advance-payment-datatable.js" type="application/javascript"></script>
<script src="/assets/custom/admin/project/indirect-expenses-datatable.js" type="application/javascript"></script>
<script>
    $(document).ready(function() {
        EditProject.init();
        PaymentCreate.init();
        $("#hsnCode").trigger('change');
    });

    function checkAmount(){
        var selectedBankId = $('#bank_id').val();
        if(selectedBankId == 'default'){
            alert('Please select Bank');
        }else{
            var amount = parseFloat($('#amount').val());
            if(typeof amount == '' || amount == 'undefined' || isNaN(amount)){
               amount = 0;
            }
            var allowedAmount = parseFloat($('#balance_amount_'+selectedBankId).val());
            $("input[name='amount']").rules('add',{
                max: allowedAmount
            });
        }
    }
</script>

@endsection
