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
    <input type="hidden" id="balanceAdvanceAmount" value="{{$subcontractorBill->subcontractorStructure->subcontractor->balance_advance_amount}}">
    <input type="hidden" id="pendingAmount" value="{{$pendingAmount}}">
    <input type="hidden" id="cashAllowedLimit" value="{{$cashAllowedLimit}}">
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
                                        <a href="/subcontractor/bill/manage/{!! $subcontractorStructure['id'] !!}">Manage Subcontractor Bills</a>
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
                                            <input type="hidden" id="subcontractorBillId" value="{!! $subcontractorBill['id'] !!}">
                                            @if($subcontractorBill->subcontractorBillStatus->slug == 'approved')
                                                <ul class="nav nav-tabs nav-tabs-lg">
                                                    <li class="active">
                                                        <a href="#billViewTab" data-toggle="tab"> Bill View </a>
                                                    </li>
                                                    <li>
                                                        <a href="#billTransactionTab" data-toggle="tab"> Transactions </a>
                                                    </li>
                                                    <li>
                                                        <a href="#reconcileTab" data-toggle="tab"> Reconcile </a>
                                                    </li>
                                                </ul>
                                            @endif
                                            <div class="tab-content">
                                                <div class="tab-pane fade in active" id="billViewTab">
                                                    <label class="control-label" for="date">Bill Date : {{date('m/d/Y',strtotime($subcontractorBill['created_at']))}}</label>
                                                    @if($subcontractorBill->subcontractorBillStatus->slug == 'draft')
                                                        <a href="/subcontractor/bill/edit/{{$subcontractorBill['id']}}" class="btn btn-xs blue" style="margin-left: 10px">
                                                            <i class="fa fa-edit"></i>
                                                            Bill
                                                        </a>
                                                        @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-subcontractor-billing'))
                                                            <a class="btn btn-xs green" id="approve" href="/subcontractor/bill/change-status/approved/{{$subcontractorBill['id']}}" style="margin-left: 10px">
                                                                <i class="fa fa-check-square-o"></i> Approve
                                                            </a>

                                                            <a href="/subcontractor/bill/change-status/disapproved/{{$subcontractorBill['id']}}" class="btn btn-xs btn-danger" id="disapprove">
                                                                <i class="fa fa-remove"></i> Disapprove
                                                            </a>
                                                        @endif
                                                    @endif

                                                    <div class="form-body">
                                                        <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll;align-content: center; " id="parentBillTable">
                                                            <thead>
                                                            <tr id="tableHeader">
                                                                <th width="10%" style="text-align: center"><b> Bill No  </b></th>
                                                                <th width="10%">
                                                                    <b>Summary</b>
                                                                </th>
                                                                <th width="10%" style="text-align: center"><b> Description </b></th>
                                                                <th width="10%"><b>Total Work Area</b></th>
                                                                <th width="10%" class="numeric" style="text-align: center"><b> Rate </b></th>
                                                                <th width="10%" class="numeric" style="text-align: center"><b> Quantity </b></th>
                                                                <th width="15%" class="numeric" style="text-align: center"><b> Amount </b></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($subcontractorBill->subcontractorBillSummaries as $index => $billSummary)
                                                                <tr>
                                                                    @if ($index == 0)
                                                                        <td rowspan="{!! count($subcontractorBill->subcontractorBillSummaries) !!}"> {{ $billName }}</td>
                                                                    @endif
                                                                    <td>
                                                                        <label class="control-label"> {{ $billSummary->subcontractorStructureSummary->summary->name }}</label>
                                                                    </td>
                                                                    <td >{{$billSummary['description']}}</td>
                                                                    <td >{{$billSummary['total_work_area']}}</td>
                                                                    <td ><label class="control-label rate">{{$billSummary->subcontractorStructureSummary['rate']}}</label></td>
                                                                    <td >{{$billSummary['quantity']}}</td>
                                                                    <td > {!! $billSummary->subcontractorStructureSummary['rate'] * $billSummary['quantity'] !!} </td>
                                                                </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td colspan="7">
                                                                    <label class="control-label"> <b> Extra Items</b></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th colspan="3" style="text-align: center;">
                                                                    Name
                                                                </th>
                                                                <th colspan="3" style="text-align: center;">
                                                                    Description
                                                                </th>
                                                                <th style="text-align: center;">
                                                                    Rate
                                                                </th>
                                                            </tr>
                                                            @foreach($subcontractorBill->subcontractorBillExtraItems as $subcontractorBillExtraItem)
                                                                <tr>
                                                                    <td colspan="3">
                                                                        {{ $subcontractorBillExtraItem->subcontractorStructureExtraItem->extraItem->name }}
                                                                    </td>
                                                                    <td colspan="3">
                                                                        {{ $subcontractorBillExtraItem->description }}
                                                                    </td>
                                                                    <td colspan="1">
                                                                        {{$subcontractorBillExtraItem['rate']}}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td colspan="6">
                                                                    <label class="control-label pull-right" style="margin-right: 3%; margin-bottom: 1%;"> <b>Subtotal</b> </label>
                                                                </td>
                                                                <td colspan="1">
                                                                    <label class="control-label" id="subtotal" style="margin-right: 3%; margin-bottom: 1%;"> {{$subcontractorBill['subtotal']}} </label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3">
                                                                    <b> Discount </b>
                                                                </td>
                                                                <td colspan="3">
                                                                    {{$subcontractorBill['discount_description']}}
                                                                </td>
                                                                <td colspan="1">
                                                                    <label class="control-label" id="discount">{{$subcontractorBill['discount']}}</label>
                                                                </td>
                                                                {{--<td colspan="1">
                                                                    <label class="control-label" id="discountAmount"></label>
                                                                </td>--}}
                                                            </tr>
                                                            <tr>
                                                                <td colspan="6">
                                                                    <label class="control-label pull-right" style="margin-right: 3%; margin-bottom: 1%;"> <b>Discounted Amount</b> </label>
                                                                </td>
                                                                <td colspan="1">
                                                                    <label class="control-label" id="discountedTotal" style="margin-right: 3%; margin-bottom: 1%;">  </label>
                                                                </td>
                                                            </tr>
                                                            @if(count($subcontractorBill->subcontractorBillTaxes) > 0)
                                                                <tr>
                                                                    <td colspan="7">
                                                                        <label class="control-label"> <b> Taxes</b></label>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4">
                                                                        <b>Tax Name</b>
                                                                    </td>
                                                                    <td colspan="2">
                                                                        <b>Tax Rate (%)</b>
                                                                    </td>
                                                                    <td colspan="1">

                                                                    </td>
                                                                </tr>
                                                                @foreach($taxes as $tax)
                                                                    <tr>
                                                                        <td colspan="4">
                                                                            {!! $tax['name'] !!}
                                                                        </td>
                                                                        <td colspan="2">
                                                                            <label class="control-label percentage">{{$tax['percentage']}}</label>
                                                                        </td>
                                                                        <td colspan="1">
                                                                            <label class="control-label tax-amount" id="tax_current_bill_amount_{{$tax['id']}}"></label>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                            @if(count($specialTaxes) > 0)
                                                                <tr>
                                                                    <td colspan="7">
                                                                        <label class="control-label"> <b>Special Taxes</b></label>
                                                                    </td>
                                                                </tr>
                                                                @foreach($specialTaxes as $specialTax)
                                                                    <tr>
                                                                        <td colspan="3" style="text-align: right; padding-right: 30px;">
                                                                            <b>{{$specialTax['name']}}<input type="hidden" class="special-tax" value="{{$specialTax['id']}}"> </b>
                                                                        </td>
                                                                        <td colspan="2">
                                                                            <label class="control-label special-tax-percentage"> {{$specialTax['percentage']}}</label>
                                                                        </td>
                                                                        <td colspan="1">
                                                                            <a class="btn green sbold uppercase btn-outline btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> Applied On
                                                                                <i class="fa fa-angle-down"></i>
                                                                            </a>
                                                                            <ul class="dropdown-menu" style="position: relative">
                                                                                {{--<li>
                                                                                    <input type="checkbox" class="tax-applied-on special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="0"> Total Round
                                                                                </li>--}}
                                                                                @if(in_array($specialTax['id'], $appliedSpecialTaxIds))
                                                                                    @foreach($taxes as $tax)
                                                                                        <li>
                                                                                            @if(in_array($tax['id'], $specialTax['applied_on']))
                                                                                                <input type="checkbox" class="tax-applied-on" id="special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="{{$tax['id']}}" checked disabled="disabled"> {{$tax['name']}}
                                                                                            @else
                                                                                                <input type="checkbox" class="tax-applied-on" id="special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="{{$tax['id']}}" disabled="disabled"> {{$tax['name']}}
                                                                                            @endif

                                                                                        </li>
                                                                                    @endforeach
                                                                                @else
                                                                                    @foreach($taxes as $tax)
                                                                                        <li>
                                                                                            <input type="checkbox" class="tax-applied-on" id="special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="{{$tax['id']}}" onclick="calculateSpecialTax()"> {{$tax['name']}}
                                                                                        </li>
                                                                                    @endforeach
                                                                                @endif

                                                                            </ul>
                                                                        </td>
                                                                        <td>
                                                                            <span id="tax_current_bill_amount_{{$specialTax['id']}}" class="special-tax-amount"></span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                            <tr>
                                                                <td colspan="6">
                                                                    <label class="control-label pull-right"> <b>Final Total</b></label>
                                                                </td>
                                                                <td colspan="1">
                                                                    <label class="control-label" id="finalTotal"></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="6">
                                                                    <label class="control-label pull-right"> <b>Round off amount</b></label>
                                                                </td>
                                                                <td colspan="1">
                                                                    <div class="form-group" style="margin: 1%">
                                                                        <label class="control-label">{{$subcontractorBill['round_off_amount']}}</label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="6">
                                                                    <label class="control-label pull-right"> <b>Grand Total</b></label>
                                                                </td>
                                                                <td colspan="2">
                                                                    <label class="control-label" id="grandTotal" >{{$subcontractorBill['grand_total']}}</label>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade in" id="billTransactionTab">
                                                    <div class="tab-content">
                                                        <div class="tab-pane fade in active" id="billTransactionListingTab">
                                                            <div class="table-toolbar">
                                                                <div class="row" style="text-align: right">
                                                                    <div class="col-md-12">
                                                                        <div class="btn-group">
                                                                            <div id="sample_editable_1_new" class="btn yellow" >
                                                                                <a href="##billTransactionCreateModel" data-toggle="modal" style="color: white" id="billTransactionCreateButton"> Transaction
                                                                                    <i class="fa fa-plus"></i>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="transactionListingTable">
                                                                <thead>
                                                                <tr>
                                                                    <th style="width: 5%"> Sr. No. </th>
                                                                    <th> Subtotal </th>
                                                                    <th> Debit
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                    <th> Hold
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                    <th> Retention
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                    <th> TDS
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                    <th> Other Recovery
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                    <th> Total
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                    <th> Created At
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                    <th> Status </th>
                                                                    <th> Action </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="modal fade" id="billTransactionCreateModel" role="dialog">
                                                            <div class="modal-dialog">
                                                                <!-- Modal content-->
                                                                <div class="modal-content">
                                                                    <div class="modal-header" style="padding:0px !important;">
                                                                        <div class="row">
                                                                            <div class="col-md-4"></div>
                                                                            <div class="col-md-4"> <h3><b>Transaction</b></h3> </div>
                                                                            <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form role="form" id="createTransactionForm" class="form-horizontal" method="post" action="/subcontractor/bill/transaction/create">
                                                                            {!! csrf_field() !!}
                                                                            <input type="hidden" value="{{$subcontractorBill['id']}}" name="subcontractor_bills_id">
                                                                            <input type="hidden" id="remainingTotal" name="remainingTotal" >
                                                                            <div class="form-body">
                                                                                <div class="form=group row">
                                                                                    <div class="col-md-8 col-md-offset-2">
                                                                                        <span style="font-size: 15px; font-weight: bold">
                                                                                            Total Advance Amount Paid : {{$subcontractorBill->subcontractorStructure->subcontractor->total_advance_amount}}
                                                                                        </span><br>
                                                                                        <span style="font-size: 15px; font-weight: bold">
                                                                                            Balance Advance Amount : {{$subcontractorBill->subcontractorStructure->subcontractor->balance_advance_amount}}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-3">

                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="checkbox" name="is_advance" id="isAdvanceCheckbox">
                                                                                        <label class="control-label">
                                                                                            Is advance to be deducted from bill?
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row transactionPaidFromSlug" id="paidFromSlug">
                                                                                    <div class="col-md-3">
                                                                                        <label class="pull-right control-label">
                                                                                            Paid From :
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <select class="form-control" id="paid_from_slug" name="paid_from_slug" onchange="changePaidFrom(this)">
                                                                                                <option value="bank">Bank</option>
                                                                                                <option value="cash">Cash</option>
                                                                                                <option value="cancel_transaction_advance">Cancel Transaction Advance</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row bankData" id="paymentSelect">
                                                                                    <div class="form-group row" id="bankSelect">
                                                                                        <div class="col-md-3">
                                                                                            <label class="pull-right control-label">
                                                                                                Bank:
                                                                                            </label>
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <select class="form-control" id="transaction_bank_id" name="bank_id" >
                                                                                                <option value="default">Select Bank</option>
                                                                                                @foreach($banks as $bank)
                                                                                                    <option value="{{$bank['id']}}">{{$bank['bank_name']}}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                    <input type="hidden" id="allowedAmount">
                                                                                    @foreach($banks as $bank)
                                                                                        <input type="hidden" id="transaction_balance_amount_{{$bank['id']}}" value="{{$bank['balance_amount']}}">
                                                                                    @endforeach
                                                                                    <div class="col-md-3">
                                                                                        <label class="pull-right control-label">
                                                                                            Payment Mode:
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <select class="form-control" name="payment_id" >
                                                                                            <option value="">--- Select Payment Type ---</option>
                                                                                            @foreach($paymentTypes as $paymentType)
                                                                                                <option value="{{$paymentType['id']}}">{{$paymentType['name']}}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-3" style="text-align: right">
                                                                                        <label for="name" class="control-label"> Debit </label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="text" class="form-control calculate-amount" id="debit" name="debit">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-3" style="text-align: right">
                                                                                        <label for="name" class="control-label"> Hold </label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="text" class="form-control calculate-amount" id="hold" name="hold">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row form-group">
                                                                                    <div class="col-md-3">
                                                                                        <label class="control-label pull-right">Retention</label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="text" class="form-control tax_amount calculate-amount" id="retention_tax_amount" name="retention_amount">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row form-group">
                                                                                    <div class="col-md-3">
                                                                                        <label class="control-label pull-right">TDS</label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="text" class="form-control tax_amount calculate-amount" id="tds_tax_amount" name="tds_amount">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <div class="col-md-3" style="text-align: right">
                                                                                        <label for="name" class="control-label"> Other Recovery </label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="text" class="form-control calculate-amount" id="other_recovery" name="other_recovery">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-3" style="text-align: right">
                                                                                        <label for="name" class="control-label"> Payable Amount </label>
                                                                                        <span>*</span>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="hidden" class="form-control" id="originalPayableAmount" value="{{$pendingAmount}}">
                                                                                        <input type="text" class="form-control" id="payableAmount" value="{{$pendingAmount}}" readonly>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-3" style="text-align: right">
                                                                                        <label for="name" class="control-label"> Subtotal </label>
                                                                                        <span>*</span>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="text" class="form-control calculate-amount" id="subtotalAmount" name="subtotal">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-3" style="text-align: right">
                                                                                        <label for="name" class="control-label"> Total </label>
                                                                                        <span>*</span>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="text" class="form-control" id="transactionTotal" name="total" readonly>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <div class="col-md-3" style="text-align: right">
                                                                                        <label for="name" class="control-label"> Remark </label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <textarea class="form-control" name="remark" id="remark"></textarea>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-actions noborder row">
                                                                                <button type="submit" class="btn red pull-right" id="transactionSubmit"> Submit</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade in" id="reconcileTab">
                                                    <ul class="nav nav-tabs nav-tabs-lg">
                                                        <li class="active">
                                                            <a href="#holdReconcileTab" data-toggle="tab"> Hold </a>
                                                        </li>
                                                        <li>
                                                            <a href="#retentionReconcileTab" data-toggle="tab"> Retention </a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content">
                                                        <div class="tab-pane fade in active" id="holdReconcileTab">
                                                            <div class="form-group row">
                                                                <div class="col-md-3">
                                                                    <label class="pull-right control-label">
                                                                        Reconcile Hold Amount :
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control" readonly value="{{$remainingHoldAmount}}">
                                                                </div>
                                                                @if($remainingHoldAmount < 0)
                                                                    <div class="col-md-6">
                                                                        <a class="btn yellow pull-right" href="javascript:void(0);" onclick="openReconcilePaymentModal('hold')">
                                                                            <i class="fa fa-plus"></i>Reconcile Hold
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="table-scrollable">
                                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="holdReconcileTable">
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
                                                        </div>
                                                        <div class="tab-pane fade in" id="retentionReconcileTab">
                                                            <div class="form-group row">
                                                                <div class="col-md-3">
                                                                    <label class="pull-right control-label">
                                                                        Reconcile Retention Amount :
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control" readonly value="{{$remainingRetentionAmount}}">
                                                                </div>
                                                                @if($remainingRetentionAmount < 0)
                                                                    <div class="col-md-6">
                                                                        <a class="btn yellow pull-right" href="javascript:void(0);" onclick="openReconcilePaymentModal('retention')">
                                                                            <i class="fa fa-plus"></i>Reconcile Retention
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                <div class="table-scrollable">
                                                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="retentionReconcileTable">
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
                                                            </div>
                                                        </div>
                                                        <div class="modal fade " id="reconcilePaymentModal"  role="dialog">
                                                            <div class="modal-dialog">
                                                                <!-- Modal content-->
                                                                <div class="modal-content">
                                                                    <form id="add_payment_form" action="/subcontractor/subcontractor-bills/reconcile/add-transaction" method="post">
                                                                        {!! csrf_field() !!}
                                                                        <input type="hidden" name="subcontractor_bill_id" value="{{$subcontractorBill['id']}}">
                                                                        <input name="transaction_slug" id="reconcileTransactionSlug" type="hidden">
                                                                        <div class="modal-header">
                                                                            <div class="row">
                                                                                <div class="col-md-4"></div>
                                                                                <div class="col-md-4" style="font-size: 18px"> Payment</div>
                                                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-body" style="padding:40px 50px;">
                                                                            <div class="form-group row">
                                                                                <select class="form-control" id="paid_from_slug" name="paid_from_slug" onchange="changePaidFrom(this)">
                                                                                    <option value="bank">Bank</option>
                                                                                    <option value="cash">Cash</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="bankData">
                                                                                <div class="form-group row" id="bankSelect">
                                                                                    <select class="form-control" id="bank_id" name="bank_id" onchange="checkAmount()">
                                                                                        <option value="">--- Select Bank ---</option>
                                                                                        @foreach($banks as $bank)
                                                                                            <option value="{{$bank['id']}}">{{$bank['bank_name']}}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <select class="form-control" name="payment_type_id">
                                                                                        <option value="">--- Select Payment Type ---</option>
                                                                                        @foreach($paymentTypes as $type)
                                                                                            <option value="{{$type['id']}}">{{$type['name']}}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>


                                                                            <input type="hidden" id="allowedAmount">


                                                                            @foreach($banks as $bank)
                                                                                <input type="hidden" id="balance_amount_{{$bank['id']}}" value="{{$bank['balance_amount']}}">
                                                                            @endforeach


                                                                            <div class="form-group row">
                                                                                <input type="number" class="form-control" id="bilAmount" name="amount" placeholder="Enter Amount" onchange="checkAmount()">
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <input type="text" class="form-control"  name="reference_number" placeholder="Enter Reference Number" >
                                                                            </div>
                                                                            <button class="btn btn-set red pull-right" type="submit">
                                                                                <i class="fa fa-check" style="font-size: large"></i>
                                                                                Add &nbsp; &nbsp; &nbsp;
                                                                            </button>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="changeStatusModel" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="padding-bottom:10px">
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4"><center><h4 class="modal-title" id="exampleModalLongTitle">Change Status</h4></center></div>
                        <div class="col-md-4"><button type="button" class="close" data-dismiss="modal"><i class="fa fa-close" style="font-size: medium"></i></button></div>
                    </div>
                </div>
                <div class="modal-body" style="padding:40px 50px; font-size: 15px">
                    <form id="changeStatusForm" method="post" action="/subcontractor/bill/transaction/change-status">
                        {!! csrf_field() !!}
                        <input type="hidden" name="bill_transaction_id" id="bill_transaction_id">
                        <input type="hidden" name="status-slug" id="status_slug">
                        <div class="form-group row">
                            <div class="col-md-4" style="text-align: right">
                                <label for="company" class="control-label">Remark</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="remark" name="remark">
                            </div>
                        </div>
                        <div class="form-group row">
                            <i> Note : Cancellation of the bill will add transaction amount to advance amount</i>
                        </div>
                        <button class="btn btn-set red pull-right" type="submit">
                            <i class="fa fa-check" style="font-size: large"></i>
                            Change &nbsp; &nbsp; &nbsp;
                        </button>
                    </form>
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
    <script src="/assets/custom/subcontractor/bill-transaction-manage-datatable.js" type="text/javascript"></script>
    <script src="/assets/custom/subcontractor/hold-reconcile-datatable.js" type="text/javascript"></script>
    <script src="/assets/custom/subcontractor/retention-reconcile-datatable.js" type="text/javascript"></script>
    <script>
        function openDetails(changeStatusTo,billTransactionId){
            $('#bill_transaction_id').val(billTransactionId);
            $('#status_slug').val(changeStatusTo);
            $("#changeStatusModel").modal('show');
        }
        $(document).ready(function(){
            CreateTransaction.init();
            CreatePayment.init();
            $('#isAdvanceCheckbox').on('change', function(){
                var pendingAmount = parseFloat($("#pendingAmount").val());
                var balanceAdvanceAmount = parseFloat($("#balanceAdvanceAmount").val());
                if($(this).prop('checked') == true){
                    if(balanceAdvanceAmount >= pendingAmount){
                        $("#subtotalAmount").val(pendingAmount);
                    }else{
                        $("#subtotalAmount").val(balanceAdvanceAmount);
                    }
                    $("#debit").val(0);
                    $("#debit").prop('readonly', true);
                    $("#hold").val(0);
                    $("#hold").prop('readonly', true);
                    $("#retention_tax_amount").val(0);
                    $("#retention_tax_amount").prop('readonly', true);
                    $("#tds_tax_amount").val(0);
                    $("#tds_tax_amount").prop('readonly', true);
                    $("#other_recovery").val(0);
                    $("#other_recovery").prop('readonly', true);
                    $('#paidFromSlug').hide();
                    $('#paymentSelect').hide();
                    $("#transactionTotal").rules('add',{
                        max: balanceAdvanceAmount
                    });
                }else{
                    $('#paidFromSlug').show();
                    $('#paymentSelect').show();
                    $("#debit").prop('readonly', false);
                    $("#hold").prop('readonly', false);
                    $("#retention_tax_amount").prop('readonly', false);
                    $("#tds_tax_amount").prop('readonly', false);
                    $("#other_recovery").prop('readonly', false);
                    $("#transactionTotal").rules('add',{
                        max: pendingAmount
                    });
                }
                $(".calculate-amount").trigger('keyup');
            });

            $(".calculate-amount").on('keyup', function(){
                var total = parseFloat(0);
                var originalPayablemount = $('#originalPayableAmount').val();
                    $(".calculate-amount").each(function(){
                    var amount = parseFloat($(this).val());
                    if(isNaN(amount)){
                        amount = 0;
                        $(this).val(amount);
                    }
                    total = parseFloat(total);
                    total += parseFloat(amount);
                });
                var changedPayableAmount = originalPayablemount - (total - parseFloat($('#subtotalAmount').val()));
                changedPayableAmount = changedPayableAmount.toFixed(3);
                console.log('pay amount type', typeof changedPayableAmount);
                $('#payableAmount').val(changedPayableAmount);
                $("#transactionTotal").val(total.toFixed(3));
                $("#subtotalAmount").rules('add',{
                    max: parseFloat(changedPayableAmount)
                });
                var remainingBillAmount = parseFloat($("#pendingAmount").val());
                if(remainingBillAmount == null || typeof remainingBillAmount == 'undefined' || isNaN(remainingBillAmount)){
                    remainingBillAmount = 0;
                }
                if($("#isAdvanceCheckbox").is(':checked') == true){
                    var balanceAdvanceAmount = parseFloat($("#balanceAdvanceAmount").val());
                    if(balanceAdvanceAmount == null || typeof balanceAdvanceAmount == 'undefined' || isNaN(balanceAdvanceAmount)){
                        balanceAdvanceAmount = 0;
                    }
                    if(balanceAdvanceAmount < remainingBillAmount){
                        $("#transactionTotal").rules('add',{
                            max: balanceAdvanceAmount
                        });
                    }else{
                        $("#transactionTotal").rules('add',{
                            max: remainingBillAmount
                        });
                    }

                }else{
                    var amount = parseFloat($('#transactionTotal').val());
                    if(typeof amount == '' || amount == 'undefined' || isNaN(amount)){
                        amount = 0;
                    }
                    var paid_from_slug = $('.transactionPaidFromSlug').val();
                    if(paid_from_slug == 'cash'){
                        var allowedCashAmount = parseFloat($('#cashAllowedLimit').val());
                        if(allowedCashAmount < remainingBillAmount){
                            $("#transactionTotal").rules('add',{
                                max: allowedCashAmount
                            });
                        }else{
                            $("#transactionTotal").rules('add',{
                                max: remainingBillAmount
                            });
                        }
                    }else{
                        var selectedBankId = $('#transaction_bank_id').val();
                        if(selectedBankId == ''){
                            alert('Please select Bank');
                        }else{

                            var allowedBankAmount = parseFloat($('#transaction_balance_amount_'+selectedBankId).val());
                            if(allowedBankAmount < remainingBillAmount){
                                $("#transactionTotal").rules('add',{
                                    max: allowedBankAmount
                                });
                            }else{
                                $("#transactionTotal").rules('add',{
                                    max: remainingBillAmount
                                });
                            }

                        }
                    }

                }
            });
            calculateDiscount();
        });
        function openReconcilePaymentModal(transactionSlug){
            $("#reconcileTransactionSlug").val(transactionSlug);
            $("#reconcilePaymentModal").modal('show');
        }

        function checkAmount(){
            var paidFromSlug = $('#add_payment_form #paid_from_slug').val();
            if(paidFromSlug == 'bank'){
                var selectedBankId = $('#bank_id').val();
                if(selectedBankId == ''){
                    alert('Please select Bank');
                }else{
                    var amount = parseFloat($('#bilAmount').val());
                    if(typeof amount == '' || amount == 'undefined' || isNaN(amount)){
                        amount = 0;
                    }
                    var allowedAmount = parseFloat($('#balance_amount_'+selectedBankId).val());
                    $('#bilAmount').rules('add',{
                        max: allowedAmount
                    });
                }
            }else{
                var cashAllowedLimit = parseFloat($('#cashAllowedLimit').val());
                $('#bilAmount').rules('add',{
                    max: cashAllowedLimit
                });
            }

        }

        function changePaidFrom(element){
            var paidFromSlug = $(element).val();
            if(paidFromSlug == 'cash'){
                $(element).closest('.modal-body').find('.bankData').hide();
            }else if(paidFromSlug == 'cancel_transaction_advance'){
                $(element).closest('.modal-body').find('.bankData').hide();
            }else{
                $(element).closest('.modal-body').find('.bankData').show();
            }
        }

        function calculateDiscount(){
            var subtotal = parseFloat($("#subtotal").text());
            var discount = parseFloat($("#discount").text());
            if(isNaN(discount)){
                discount = 0;
            }
            if(isNaN(subtotal)){
                subtotal = 0;
            }
            /*var discountAmount = parseFloat(((discount / 100) * subtotal).toFixed(3));
            $("#discountAmount").text(discountAmount);*/
            var discountedTotal = parseFloat(subtotal - discount).toFixed(3);
            $("#discountedTotal").text(discountedTotal);
            calculateTaxAmount();
        }

        function calculateTaxAmount(){
            $(".percentage").each(function(){
                var percentage = parseFloat($(this).text());
                var discountedTotal = parseFloat($('#discountedTotal').text());
                var tax_amount = (percentage * discountedTotal) / 100;
                if(isNaN(tax_amount)){
                    $(this).closest('tr').find(".tax-amount").text(0)
                }else{
                    $(this).closest('tr').find(".tax-amount").text(tax_amount.toFixed(3));
                }
            });
            calculateSpecialTax();
        }

        function calculateSpecialTax(){
            if($(".special-tax").length > 0){
                $(".special-tax").each(function(){
                    var specialTaxId = $(this).val();
                    var taxAmount = 0;
                    $(this).closest('tr').find('.tax-applied-on:checked').each(function(){
                        var taxId = $(this).val();
                        var taxOnAmount = 0;
                        if(taxId == 0 || taxId == '0'){
                            taxOnAmount = parseFloat($("#rounded_off_current_bill_amount").val());
                        }else{
                            taxOnAmount = parseFloat($("#tax_current_bill_amount_"+taxId).text());
                        }
                        var taxPercentage = parseFloat($(this).closest('tr').find('.special-tax-percentage').text());
                        if(isNaN(taxPercentage)){
                            taxPercentage = 0;
                        }
                        taxAmount += parseFloat((taxOnAmount * (taxPercentage / 100)).toFixed(3));
                    });
                    $("#tax_current_bill_amount_"+specialTaxId).text(parseFloat(taxAmount).toFixed(3));
                });
            }
            calculateFinalTotal();
        }

        function calculateFinalTotal(){
            var finalTotal = parseFloat($('#subtotal').text());
            $('.tax-amount, .special-tax-amount').each(function(){
                var taxAmount = parseFloat($(this).text());
                if(isNaN(taxAmount)){
                    taxAmount = 0;
                }
                finalTotal += taxAmount;
            });
            if(isNaN(finalTotal)){
                $('#finalTotal').text(0);
            }else{
                $('#finalTotal').text(finalTotal.toFixed(3));
            }
        }
    </script>
@endsection
