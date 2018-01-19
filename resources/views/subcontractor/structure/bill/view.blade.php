@extends('layout.master')
@section('title','Constro | View Subcontractor Structure Bill')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="/assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css" />
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
                                    <h1>View Subcontractor Structure Bill</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container" style="width: 100%">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/subcontractor/subcontractor-bills/manage/{!! $subcontractorStructure['id'] !!}">Manage Subcontractor Bills</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">View Subcontractor Structure Bill</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <label class="control-label" for="date">Bill Date : {{date('m/d/Y',strtotime($subcontractorBill['created_at']))}}</label>
                                            @if($subcontractorBill->subcontractorBillStatus->slug == 'draft')
                                                <a href="/subcontractor/subcontractor-bills/edit/{{$subcontractorBill['id']}}" class="btn btn-xs blue" style="margin-left: 10px">
                                                    <i class="fa fa-edit"></i>
                                                    Bill
                                                </a>
                                                <a class="btn btn-xs green" id="approve" href="/subcontractor/subcontractor-bills/change-status/approved/{{$subcontractorBill['id']}}" style="margin-left: 10px">
                                                    <i class="fa fa-check-square-o"></i> Approve
                                                </a>

                                                <a href="/subcontractor/subcontractor-bills/change-status/disapproved/{{$subcontractorBill['id']}}" class="btn btn-xs btn-danger" id="disapprove">
                                                    <i class="fa fa-remove"></i> Disapprove
                                                </a>
                                            @endif

                                            <div class="form-body">
                                                <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="parentBillTable">
                                                    <thead>
                                                    <tr id="tableHeader">
                                                        <th width="10%" style="text-align: center"><b> Bill No  </b></th>
                                                        <th width="30%" style="text-align: center"><b> Description </b></th>
                                                        <th width="15%" class="numeric" style="text-align: center"><b> Quantity </b></th>
                                                        <th width="15%" class="numeric" style="text-align: center"><b> Rate </b></th>
                                                        <th width="15%" class="numeric" style="text-align: center"><b> Amount </b></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            {!! $billName !!}
                                                        </td>
                                                        <td>
                                                            {!! $subcontractorBill['description'] !!}
                                                        </td>
                                                        <td>
                                                           {!! $subcontractorBill['qty'] !!}
                                                        </td>
                                                        <td>
                                                            {!! $rate !!}
                                                        </td>
                                                        <td>
                                                            {!! $subTotal!!}
                                                        </td>
                                                    </tr>
                                                    @if(count($subcontractorBillTaxes) > 0)
                                                        <tr>
                                                            <td colspan="2">
                                                                <b>Tax Name</b>
                                                            </td>
                                                            <td colspan="2">
                                                                <b>Tax Rate</b>
                                                            </td>
                                                            <td colspan="1">

                                                            </td>
                                                        </tr>
                                                        @foreach($subcontractorBillTaxes as $key => $billTaxData)
                                                            <tr>
                                                                <td colspan="2">
                                                                    {!! $billTaxData->taxes->name !!}
                                                                </td>
                                                                <td colspan="2">
                                                                    {!! $billTaxData->percentage !!} %
                                                                </td>
                                                                <td colspan="1">
                                                                    {!! ($billTaxData->percentage * $subTotal) / 100 !!}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    <tr>
                                                        <td colspan="4">
                                                            <b>Final Total</b>
                                                        </td>
                                                        <td colspan="1">
                                                            {!! $finalTotal !!}
                                                        </td>
                                                    </tr>

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
@endsection
@section('javascript')
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script><script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
@endsection
