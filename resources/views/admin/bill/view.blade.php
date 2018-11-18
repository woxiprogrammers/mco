@extends('layout.master')
@section('title','Constro | View Bill')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
<!-- END PAGE LEVEL PLUGINS -->
@endsection
@section('content')
<input type="hidden" value="{{$remainingAmount}}" id="remainingAmount">
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
                                <h1>View Bill - {{$quotation->project_site->name}} - {{$quotation->billType->name}}wise</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container" style="width: 100%">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/bill/manage/project-site">Manage Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">View Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="/bill/create/{{$bill->quotation->project_site_id}}">Create Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body">
                                        <input type="hidden" id="billId" value="{{$selectedBillId}}">
                                        @if($bill->bill_status->slug == 'approved')
                                            <ul class="nav nav-tabs nav-tabs-lg">
                                                <li class="active">
                                                    <a href="#billViewTab" data-toggle="tab"> Bill View </a>
                                                </li>
                                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-billing-transaction') || $user->customHasPermission('view-billing-transaction') || $user->customHasPermission('edit-billing-transaction') || $user->customHasPermission('approve-billing-transaction'))
                                                    <li>
                                                        <a href="#billTransactionTab" data-toggle="tab"> Transactions </a>
                                                    </li>
                                                    <li>
                                                        <a href="#reconcileTab" data-toggle="tab"> Reconcile </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        @endif
                                        <div class="tab-content">
                                            <div class="tab-pane fade in active" id="billViewTab">
                                            @if($bills != NULL)
                                                <div class="col-md-12 table-actions-wrapper" style="margin-bottom: 20px;">
                                                    <select class="table-group-action-input form-control input-inline input-small input-sm" name="change_bill" id="change_bill" style="margin-left: 1%">
                                                        @for($i = 0 ; $i < count($bills); $i++)
                                                            <option value="{{$bills[$i]['id']}}">R.A Bill {{$i+1}}</option>
                                                        @endfor
                                                    </select>

                                                    <label class="control-label" for="date" style="margin-left: 1%">Bill Date : {{date('m/d/Y',strtotime($bill['date']))}}</label>

                                                    <label class="control-label" for="date" style="margin-left: 1%"> Proforma Invoice Date : {{date('m/d/Y',strtotime($bill['performa_invoice_date']))}}</label>

                                                    <a href="/bill/cumulative/invoice/{{$selectedBillId}}" class="btn btn-info btn-icon" style="margin-left: 10px">
                                                        <i class="fa fa-download"></i> Cumulative Bill
                                                    </a>

                                                    <a href="/bill/cumulative/excel-sheet/{{$selectedBillId}}" class="btn btn-info btn-icon" style="margin-left: 10px">
                                                        <i class="fa fa-download"></i>Export Cumulative Bill
                                                    </a>
                                                    <a href="/bill/current/invoice/{{$selectedBillId}}" class="btn btn-info btn-icon" style="margin-left: 10px">
                                                        <i class="fa fa-download"></i>
                                                        Current Bill
                                                    </a>

                                                    <a href="/bill/current/performa-invoice/{{$selectedBillId}}" class="btn btn-info btn-icon" style="margin-left: 10px">
                                                        <i class="fa fa-download"></i>
                                                        Proforma Invoice Bill
                                                    </a>
                                                    @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('edit-billing'))
                                                        <a href="/bill/edit/{{$selectedBillId}}" class="btn btn-info btn-icon" style="margin-left: 10px">
                                                            <i class="fa fa-edit"></i>
                                                            Bill
                                                        </a>
                                                    @endif
                                                    @if($bill->bank_info_id != null)
                                                        <label for="bank" class="control-label" style="margin-left: 10px">Assigned Bank : {!! $bill->bankInfo->bank_name !!} - {!! $bill->bankInfo->account_number !!}</label>
                                                    @endif

                                                    @if(($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-billing')) && $bill->bill_status->slug == 'draft')
                                                        <a class="btn green-meadow" id="approve" data-toggle="tab" href="#billApproveTab" style="margin-left: 10px">
                                                            <i class="fa fa-check-square-o"></i> Approve
                                                        </a>

                                                        <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#cancel-form" style="margin-left: 10px">
                                                            <i class="fa fa-remove"></i> Cancel
                                                        </a>
                                                    @endif

                                                    {{--<div class="col-md-12" style="margin-top: 1%">
                                                        @if($bill->bank_info_id != null)
                                                            <label for="bank" class="control-label" style="padding-left: 13%">Assigned Bank : {!! $bill->bankInfo->bank_name !!} - {!! $bill->bankInfo->account_number !!}</label>
                                                        @endif
                                                        @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('edit-billing'))
                                                            <a href="/bill/edit/{{$selectedBillId}}" class="btn btn-info btn-icon" style="margin-left: 11%">
                                                                <i class="fa fa-edit"></i>
                                                                Bill
                                                            </a>
                                                        @endif
                                                        @if(($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-billing')) && $bill->bill_status->slug == 'draft')
                                                                <a class="btn green-meadow" id="approve" data-toggle="tab" href="#billApproveTab" style="margin-left: 10px">
                                                                    <i class="fa fa-check-square-o"></i> Approve
                                                                </a>

                                                                <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#cancel-form" style="margin-left: 10px">
                                                                    <i class="fa fa-remove"></i> Cancel
                                                                </a>
                                                        @endif

                                                    </div>--}}
                                                </div>
                                            @endif
                                            <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="createBillTable">
                                                <tr style="text-align: center">
                                                    <th width="3%" style="text-align: center"> Item no </th>
                                                    <th width="15%" style="text-align: center"> Item Description </th>
                                                    <th width="6%" class="numeric" style="text-align: center"> UOM </th>
                                                    @if($bill->quotation->billType->slug == 'sqft')
                                                        <th width="7%" class="numeric" style="text-align: center"> Slab Area </th>
                                                    @else
                                                        <th width="7%" class="numeric" style="text-align: center"> BOQ Quantity </th>
                                                    @endif
                                                    <th width="6%" class="numeric" style="text-align: center"> Rate </th>
                                                    <th width="10%" class="numeric" style="text-align: center"> W.O Amount </th>
                                                    <th width="7%" class="numeric" style="text-align: center"> Previous Quantity </th>
                                                    <th width="7%" class="numeric" style="text-align: center"> Current Quantity </th>
                                                    <th width="10%" class="numeric" style="text-align: center"> Cumulative Quantity </th>
                                                    <th width="10%" class="numeric" style="text-align: center"> Current Bill Amount </th>
                                                </tr>
                                                @if($bill->quotation->billType->slug == 'sqft')
                                                    @for($iterator = 0; $iterator < count($billQuotationSummaries); $iterator++)
                                                        <tr>
                                                            <td>
                                                                <span id="quotation_product_id">{{$iterator + 1}}</span>
                                                            </td>

                                                            <td>
                                                                <span>{{$billQuotationSummaries[$iterator]['summaryDetail']['name']}} - {{$billQuotationSummaries[$iterator]['product_description']['description']}}</span>
                                                            </td>

                                                            <td>
                                                                <span>{{$billQuotationSummaries[$iterator]['unit']}}</span>
                                                            </td>

                                                            <td>
                                                                <span>{{$quotation['built_up_area']}}</span>
                                                            </td>

                                                            <td>
                                                                <span id="rate_per_unit_{{$billQuotationSummaries[$iterator]['id']}}">{{$billQuotationSummaries[$iterator]['rate_per_sqft']}}</span>
                                                            </td>

                                                            <td>
                                                                <span>{!! round(($billQuotationSummaries[$iterator]['rate_per_sqft'] * $quotation['built_up_area']),3) !!}</span>
                                                            </td>

                                                            <td>
                                                                <span id="previous_quantity_{{$billQuotationSummaries[$iterator]['id']}}">{{$billQuotationSummaries[$iterator]['previous_quantity']}}</span>
                                                            </td>

                                                            <td>
                                                                <span id="current_quantity_{{$billQuotationSummaries[$iterator]['id']}}">{{$billQuotationSummaries[$iterator]['quantity']}}</span>
                                                            </td>

                                                            <td>
                                                                <span id="cumulative_quantity_{{$billQuotationSummaries[$iterator]['id']}}">{{$billQuotationSummaries[$iterator]['cumulative_quantity']}}</span>
                                                            </td>

                                                            <td>
                                                                <span class="current_bill_amount" id="current_bill_amount_{{$billQuotationSummaries[$iterator]['id']}}">{{$billQuotationSummaries[$iterator]['current_bill_subtotal']}}</span>
                                                            </td>

                                                        </tr>
                                                    @endfor
                                                @else
                                                    @for($iterator = 0; $iterator < count($billQuotationProducts); $iterator++)
                                                        <tr>
                                                            <td>
                                                                <span id="quotation_product_id">{{$iterator + 1}}</span>
                                                            </td>

                                                            <td>
                                                                <span>{{$billQuotationProducts[$iterator]['productDetail']['name']}} - {{$billQuotationProducts[$iterator]['product_description']['description']}}</span>
                                                            </td>

                                                            <td>
                                                                <span>{{$billQuotationProducts[$iterator]['unit']}}</span>
                                                            </td>

                                                            <td>
                                                                <span>{{$billQuotationProducts[$iterator]['quotationProducts']['quantity']}}</span>
                                                            </td>

                                                            <td>
                                                                <span id="rate_per_unit_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['rate']}}</span>
                                                            </td>

                                                            <td>
                                                                <span>{!! round(($billQuotationProducts[$iterator]['rate'] * $billQuotationProducts[$iterator]['quotationProducts']['quantity']),3) !!}</span>
                                                            </td>

                                                            <td>
                                                                <span id="previous_quantity_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['previous_quantity']}}</span>
                                                            </td>

                                                            <td>
                                                                <span id="current_quantity_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['quantity']}}</span>
                                                            </td>

                                                            <td>
                                                                <span id="cumulative_quantity_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['cumulative_quantity']}}</span>
                                                            </td>

                                                            <td>
                                                                <span class="current_bill_amount" id="current_bill_amount_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['current_bill_subtotal']}}</span>
                                                            </td>

                                                        </tr>
                                                    @endfor
                                                @endif

                                                <tr>
                                                    <td colspan="11" style="background-color: #F5F5F5">&nbsp; </td>
                                                </tr>
                                                @if(count($extraItems) > 0)
                                                    <tr>
                                                        <td colspan="4"><b>Extra Items</b></td>
                                                        <td colspan="2"><b>Total amount approved</b></td>
                                                        <td colspan="2"><b>Previous amount</b></td>
                                                        <td colspan="2"><b>Current amount</b></td>
                                                    </tr>
                                                    @for($iterator = 0; $iterator < count($extraItems); $iterator++)
                                                        <tr>
                                                            <td colspan="4">
                                                                <span>
                                                                    {{$extraItems[$iterator]->quotationExtraItems->extraItem->name}} - {{$extraItems[$iterator]->description}}
                                                                </span>
                                                            </td>
                                                            <td colspan="2">
                                                                <span id="total_extra_item_rate">{{$extraItems[$iterator]->quotationExtraItems->rate}}</span>
                                                            </td>
                                                            <td colspan="2">
                                                                <span id="previous_rates_{{$extraItems[$iterator]->id}}">{!! $extraItems[$iterator]->previous_rate !!}</span>
                                                            </td>
                                                            <td colspan="2" class="form-group">
                                                                <span id="current_rates_{{$extraItems[$iterator]->id}}">{{$extraItems[$iterator]->rate}}</span>
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                @endif

                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;"><b>Sub Total</b></td>
                                                    <td>
                                                        <span id="sub_total_current_bill_amount">{{$total['current_bill_subtotal']}}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;"><b>Sub Total Round</b></td>
                                                    <td>
                                                        <span id="rounded_off_current_bill_sub_total">{{$total_rounded['current_bill_subtotal']}}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;"><b>Discount Amount</b></td>
                                                    <td>
                                                        <span id="discountAmount">{{$bill['discount_amount']}}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;"><b>Discount Description</b></td>
                                                    <td>
                                                        <span id="discountDescription">{{$bill['discount_description']}}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;"><b>Total Round</b></td>
                                                    <td>
                                                        <span id="rounded_off_current_bill_amount">{{$total_rounded['current_bill_amount']}}</span>
                                                    </td>
                                                </tr>
                                                @if($taxes != null)
                                                    <tr>
                                                        <td colspan="6">
                                                            <b>Tax Name</b>
                                                        </td>
                                                        <td colspan="3">
                                                            <b>Tax Rate</b>
                                                        </td>
                                                        <td colspan="1">

                                                        </td>
                                                    </tr>
                                                    @foreach($taxes as $tax)
                                                    <tr>
                                                        <td colspan="6" style="text-align: center">
                                                            {{$tax['tax_name']}}
                                                        </td>
                                                        <td colspan="3" style="text-align: center">
                                                            <span id="percentage">{{abs($tax['percentage'])}}</span>
                                                        </td>
                                                        <td>
                                                            <span id="tax_current_bill_amount_{{$tax['id']}}">{{$tax['current_bill_amount']}}</span>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @endif
                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;">
                                                        <b>Final Total</b>
                                                    </td>
                                                    <td>
                                                        <span id="final_current_bill_total">{{$final['current_bill_amount']}}</span>
                                                    </td>
                                                </tr>
                                                @if(!empty($specialTaxes))
                                                    @foreach($specialTaxes as $specialTax)
                                                <tr>
                                                    <td colspan="7" style="text-align: right; padding-right: 30px;"><b>{{$specialTax['tax_name']}}<i class="fa fa-at"></i>{{$specialTax['percentage']}}%</b><input type="hidden" class="special-tax" name="special_tax[]" value="{{$specialTax['id']}}"> </td>
                                                    <td colspan="2">
                                                        <a class="btn green sbold uppercase btn-outline btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> Applied On
                                                            <i class="fa fa-angle-down"></i>
                                                        </a>
                                                        <ul class="dropdown-menu" style="position: relative">
                                                            <li>
                                                                @if(in_array(0,$specialTax['applied_on']))
                                                                    <input type="checkbox" class="tax-applied-on special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="0" checked="checked" disabled> Total Round
                                                                @else
                                                                    <input type="checkbox" class="tax-applied-on special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="0" disabled> Total Round
                                                                @endif
                                                            </li>
                                                            @foreach($taxes as $tax)
                                                            <li>
                                                                @if(in_array($tax['tax_id'],$specialTax['applied_on']))
                                                                    <input type="checkbox" class="tax-applied-on special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="{{$tax['id']}}" checked="checked" disabled> {{$tax['tax_name']}}
                                                                @else
                                                                    <input type="checkbox" class="tax-applied-on special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="{{$tax['id']}}" disabled> {{$tax['tax_name']}}
                                                                @endif

                                                            </li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>
                                                        <span id="tax_current_bill_amount_{{$specialTax['id']}}" class="special-tax-amount">{{$specialTax['current_bill_amount']}}</span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @endif
                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;"><b> Rounded Amount By</b></td>
                                                    <td>
                                                        <span id="rounded_amount_by">{{$bill['rounded_amount_by']}}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;"><b> Grand Total</b></td>
                                                    <td>
                                                        <span id="grand_current_bill_total">{{$final['current_bill_gross_total_amount']}}</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                            <div class="tab-pane fade in" id="billApproveTab">
                                                <form id="approve" action="/bill/approve" method="post">
                                                {!! csrf_field() !!}
                                                <input type="hidden" name="bill_id" value="{{$selectedBillId}}">
                                                <div class="col-md-offset-2">
                                                    <div class="form-group">
                                                        <div class="col-md-3">
                                                            <label for="remark" class="control-form pull-right">
                                                                Remark:
                                                            </label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <textarea class="form-control" name="remark" id="remark"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div id="tab_images_uploader_filelist" class="col-md-6 col-sm-12"> </div>
                                                        </div>
                                                        <div id="tab_images_uploader_container" class="col-md-offset-5">
                                                            <a id="tab_images_uploader_pickfiles" href="javascript:;" class="btn green-meadow">
                                                                Browse</a>
                                                            <a id="tab_images_uploader_uploadfiles" href="javascript:;" class="btn btn-primary">
                                                                <i class="fa fa-share"></i> Upload Files </a>
                                                        </div>
                                                        <table class="table table-bordered table-hover" style="width: 700px">
                                                            <thead>
                                                            <tr role="row" class="heading">
                                                                <th> Image </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="show-product-images">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-2 col-md-offset-4">
                                                            <button type="submit" class="btn btn-success">
                                                                Submit
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            </div>
                                            <div class="tab-pane fade in" id="billTransactionTab">
                                                <div class="tab-content">
                                                <div class="tab-pane fade in active" id="billTransactionListingTab">
                                                    <div class="table-toolbar">
                                                        <div class="row" style="text-align: right">
                                                            <div class="col-md-12">
                                                                @if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-billing-transaction'))
                                                                    <div class="btn-group">
                                                                        <div id="sample_editable_1_new" class="btn yellow" >
                                                                            <a href="#paymentModal" style="color: white" id="billTransactionCreateButton" data-toggle="modal"> Transaction
                                                                                <i class="fa fa-plus"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="table-scrollable">
                                                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="transactionListingTable">
                                                            <thead>
                                                            <tr>
                                                                <th style="width: 5%"> Sr. No. </th>
                                                                <th> Date </th>
                                                                <th> Paid From </th>
                                                                <th> Amount </th>
                                                                <th> Debit </th>
                                                                <th> Hold </th>
                                                                <th> Retention </th>
                                                                <th> TDS </th>
                                                                <th> Other Recovery Value </th>
                                                                <th> Total </th>
                                                                <th> Status </th>
                                                                <th> Action </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
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
                                                            @if($remainingHoldAmount < 0 && ($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-billing-transaction')))
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
                                                            @if($remainingRetentionAmount < 0 && ($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-billing-transaction')))
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
                <input type="hidden" id="path" name="path" value="">
                <input type="hidden" id="max_files_count" name="max_files_count" value="20">
                <div id="cancel-form" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Cancel Bill</h4>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal" id="add_enquiry" action="/bill/cancel/{{$selectedBillId}}" method="POST">
                                {!! csrf_field() !!}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Remark</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="remark" id="remark">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary pull-right">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="paymentModal" role="dialog">
    <div class="modal-dialog" style="width: 60%">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:10px">
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4"> <h3><b>Add Payment</b></h3></div>
                    <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                </div>
            </div>
            <div class="modal-body" style="padding:40px 50px;">
                <form id="paymentCreateForm" method="post" action="/bill/transaction/create">
                    {!! csrf_field() !!}
                    <input type="hidden" name="bill_id" value="{{$selectedBillId}}">
                    <input type="hidden" name="cancelled_bill_transaction_balance" value="{{$balanceCancelledTransactionAmount}}">
                    <div class="form-group row" id="paymentSelect">
                        <div class="col-md-4">
                            <label class="pull-right control-label">
                                Paid By:
                            </label>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control" name="paid_from_advanced" id="paid_from_advanced" onchange="showBankData()">
                                <option value="bank"> Bank </option>
                                <option value="advance"> Advance Payments </option>
                                <option value="cash"> Cash </option>
                                <option value="cancelled_bill_advance"> Cancelled Bill Advance </option>
                            </select>
                        </div>
                    </div>
                    <div id="bankData">
                        <div class="form-group row" id="bankSelect">
                            <div class="col-md-4">
                                <label class="pull-right control-label">
                                    Bank:
                                </label>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" id="bank_id" name="bank_id" onchange="checkAmount()">
                                    @foreach($banks as $bank)
                                        <option value="{{$bank['id']}}">{{$bank['bank_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="bankSelect">
                            <div class="col-md-4">
                                <label class="pull-right control-label">
                                    Payment Type:
                                </label>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" name="payment_type_id">
                                    @foreach($paymentTypes as $type)
                                        <option value="{{$type['id']}}">{{$type['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="pull-right control-label">
                                Amount:
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" class="form-control calculatable-field" name="amount" placeholder="Enter Amount"  value="{{$remainingAmount}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="pull-right control-label">
                                Debit:
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" class="form-control calculatable-field" id="debit" name="debit" placeholder="Enter Debit Amount">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="pull-right control-label">
                                Hold:
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" class="form-control calculatable-field" id="hold" name="hold" placeholder="Enter Hold Amount">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="pull-right control-label">
                                Retention:
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" class="form-control calculatable-field" id='retention_amount' name="retention_amount" placeholder="Retention Amount">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="pull-right control-label">
                                TDS:
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" class="form-control calculatable-field" id="tds_amount" name="tds_amount" placeholder="TDS Amount">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="pull-right control-label">
                                Other Recovery Value:
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" name="other_recovery_value" id="other_recovery_value" class="form-control calculatable-field">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="pull-right control-label">
                                Total:
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" name="total" class="form-control calculatable-field" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label class="pull-right control-label">
                                Remark:
                            </label>
                        </div>
                        <div class="col-md-6">
                            <textarea name="remark" class="form-control"></textarea>
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
<div class="modal fade " id="reconcilePaymentModal"  role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="add_payment_form" action="/bill/reconcile/add-transaction" method="post">
                {!! csrf_field() !!}
                <input type="hidden" name="bill_id" value="{{$selectedBillId}}">
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
                        <div class="form-group row">
                            <select class="form-control" id="bank_id" name="bank_id">
                                <option value="">--- Select Bank ---</option>
                                @foreach($banks as $bank)
                                    <option value="{{$bank['id']}}">{{$bank['bank_name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" id="allowedAmount">
                        @foreach($banks as $bank)
                            <input type="hidden" id="balance_amount_{{$bank['id']}}" value="{{$bank['balance_amount']}}">
                        @endforeach

                        <div class="form-group row">
                            <select class="form-control" name="payment_type_id">
                                <option value="">--- Select Payment Type ---</option>
                                @foreach($paymentTypes as $type)
                                    <option value="{{$type['id']}}">{{$type['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <input type="number" class="form-control" id="bilAmount" name="amount" placeholder="Enter Amount">
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
                <form id="changeStatusForm" method="post" action="/bill/transaction/change-status">
                    {!! csrf_field() !!}
                    <input type="hidden" name="bill_transaction_id" id="bill_transaction_id">
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
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
<script src="/assets/global/plugins/plupload/js/plupload.full.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
<script src="/assets/custom/bill/image-datatable.js"></script>
<script src="/assets/custom/bill/transaction-datatable.js"></script>
<script src="/assets/custom/bill/image-upload.js"></script>
<script type="text/javascript" src="/assets/global/plugins/ckeditor/ckeditor.js"></script>
<script src="/assets/custom/bill/bill-view.js"></script>
<script src="/assets/custom/bill/validation.js" type="text/javascript"></script>
<script src="/assets/custom/bill/hold-reconcile-datatable.js" type="text/javascript"></script>
<script src="/assets/custom/bill/retention-reconcile-datatable.js" type="text/javascript"></script>
<script>
    function openDetails(billTransactionId){
        console.log(billTransactionId);
        $('#bill_transaction_id').val(billTransactionId);
        $("#changeStatusModel").modal('show');
    }
    $(document).ready(function(){
        CreateBillPayment.init();
        CreateBillReconcilePayment.init();
        $("#change_bill").on('change', function(){
            var bill_id = $(this).val();
            window.location.href = "/bill/view/"+bill_id;
        });
        $('select[name="change_bill"]').find('option[value={{$selectedBillId}}]').attr("selected",true);
    });

    function openReconcilePaymentModal(transactionSlug){
        $("#reconcileTransactionSlug").val(transactionSlug);
        $("#reconcilePaymentModal").modal('show');

    }

    function changePaidFrom(element){
        var paidFromSlug = $(element).val();
        if(paidFromSlug == 'cash'){
            $(element).closest('.modal-body').find('.bankData').hide();
        }else{
            $(element).closest('.modal-body').find('.bankData').show();
        }
    }

    function showBankData(){
        var isAdvanceOption = $('#paid_from_advanced').val();
        if(isAdvanceOption == 'advance'){
            $('#bankData').hide();
            $('#debit').prop('readonly',true).val(0);
            $('#hold').prop('readonly',true).val(0);
            $('#retention_amount').prop('readonly',true).val(0);
            $('#tds_amount').prop('readonly',true).val(0);
            $('#other_recovery_value').prop('readonly',true).val(0);
        }else if(isAdvanceOption == 'cancelled_bill_advance'){
            $('#bankData').hide();
            $('#debit').prop('readonly',false);
            $('#hold').prop('readonly',false);
            $('#retention_amount').prop('readonly',false);
            $('#tds_amount').prop('readonly',false);
            $('#other_recovery_value').prop('readonly',false);
        }else{
            $('#bankData').show();
            $('#debit').prop('readonly',false);
            $('#hold').prop('readonly',false);
            $('#retention_amount').prop('readonly',false);
            $('#tds_amount').prop('readonly',false);
            $('#other_recovery_value').prop('readonly',false);
        }
    }
</script>
@endsection



