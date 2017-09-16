@extends('layout.master')
@section('title','Constro | View Bill')
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
                                <h1>View Bill</h1>
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
                                                <li>
                                                    <a href="#billTransactionTab" data-toggle="tab"> Transactions </a>
                                                </li>
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
                                                @if($bill->bank_info_id != null)
                                                    <label for="bank" class="control-label" style="padding-left: 5%">Assigned Bill : {!! $bill->bankInfo->bank_name !!} - {!! $bill->bankInfo->account_number !!}</label>
                                                @endif
                                                @if($bill->bill_status->slug == 'draft')
                                                    <a href="/bill/edit/{{$selectedBillId}}" class="btn btn-info btn-icon" style="margin-left: 30%">
                                                        <i class="fa fa-edit"></i>
                                                        Bill
                                                    </a>

                                                    <a class="btn green-meadow" id="approve" data-toggle="tab" href="#billApproveTab" style="margin-left: 10px">
                                                        <i class="fa fa-check-square-o"></i> Approve
                                                    </a>

                                                    <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#cancel-form" style="margin-left: 10px">
                                                        <i class="fa fa-remove"></i> Cancel
                                                    </a>
                                                @endif
                                                <a href="/bill/cumulative/invoice/{{$selectedBillId}}" class="btn btn-info btn-icon" style="margin-left: 10px">
                                                    <i class="fa fa-download"></i> Cumulative Bill
                                                </a>

                                                <a href="/bill/cumulative/excel-sheet/{{$selectedBillId}}" class="btn btn-info btn-icon" style="margin-left: 10px">
                                                    <i class="fa fa-download"></i>Export Cumulative Bill
                                                </a>
                                                <div class="col-md-12" style="margin-top: 1%">
                                                <label class="control-label" for="date">Bill Date : {{date('m/d/Y',strtotime($bill['date']))}}</label>

                                                <a href="/bill/current/invoice/{{$selectedBillId}}" class="btn btn-info btn-icon" style="margin-left: 10px">
                                                    <i class="fa fa-download"></i>
                                                    Current Bill
                                                </a>
                                                <label class="control-label" for="date" style="margin-left: 38%"> Performa Invoice Date : {{date('m/d/Y',strtotime($bill['performa_invoice_date']))}}</label>

                                                <a href="/bill/current/performa-invoice/{{$selectedBillId}}" class="btn btn-info btn-icon" style="margin-left: 10px">
                                                    <i class="fa fa-download"></i>
                                                    Performa Invoice Bill
                                                </a>
                                                </div>

                                            </div>
                                            @endif
                                            <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="createBillTable">
                                                <tr style="text-align: center">
                                                    <th width="3%" style="text-align: center"> Item no </th>
                                                    <th width="15%" style="text-align: center"> Item Description </th>
                                                    <th width="6%" class="numeric" style="text-align: center"> UOM </th>
                                                    <th width="7%" class="numeric" style="text-align: center"> BOQ Quantity </th>
                                                    <th width="6%" class="numeric" style="text-align: center"> Rate </th>
                                                    <th width="10%" class="numeric" style="text-align: center"> W.O Amount </th>
                                                    <th width="7%" class="numeric" style="text-align: center"> Previous Quantity </th>
                                                    <th width="7%" class="numeric" style="text-align: center"> Current Quantity </th>
                                                    <th width="10%" class="numeric" style="text-align: center"> Cumulative Quantity </th>
                                                    <th width="10%" class="numeric" style="text-align: center"> Current Bill Amount </th>
                                                </tr>
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
                                                        <span>{!! round($billQuotationProducts[$iterator]['rate'] * $billQuotationProducts[$iterator]['quotationProducts']['quantity']) !!}</span>
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
                                                                <div class="btn-group">
                                                                    <div id="sample_editable_1_new" class="btn yellow" ><a href="javascript:void(0);" style="color: white" id="billTransactionCreateButton"> Transaction
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
                                                            @foreach($taxes as $tax)
                                                                <th> {{$tax['tax_name']}} </th>
                                                            @endforeach
                                                            @foreach($specialTaxes as $specialTax)
                                                                <th> {{$specialTax['tax_name']}} </th>
                                                            @endforeach
                                                            <th> Total
                                                                <input type="hidden" class="filter-submit">
                                                            </th>
                                                            <th>
                                                                Action
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="tab-pane fade in" id="billTransactionCreateTab">
                                                    <form role="form" id="createTransactionForm" class="form-horizontal" method="post" action="/bill/transaction/create">
                                                        {!! csrf_field() !!}
                                                        <input type="hidden" name="bill_id" value="{{$selectedBillId}}">
                                                        <input type="hidden" id="remainingTotal" name="remainingTotal" value="{{$remainingAmount}}">
                                                        <div class="form-body">
                                                            <div class="form-group row">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="name" class="control-label"> Total </label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="number" class="form-control" id="transactionTotal" name="total" onchange="calculateTransactionDetails()">
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="name" class="control-label"> Subtotal </label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control" id="transactionSubTotal" name="subtotal" readonly>
                                                                </div>
                                                            </div>
                                                            @foreach($taxes as $tax)
                                                                <input type="hidden" name="tax_info[{{$tax['tax_id']}}][percent]" value="{{$tax['percentage']}}">
                                                                <input type="hidden" name="tax_info[{{$tax['tax_id']}}][applied_on]" value="{{$tax['applied_on']}}">
                                                                <div class="form-group">
                                                                    <div class="col-md-3" style="text-align: right">
                                                                        <label for="name" class="control-label"> {{$tax['tax_name']}} </label>
                                                                        <span>*</span>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control" id="TaxAmount_{{$tax['tax_id']}}" name="tax_amount[{{$tax['id']}}]" readonly>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            @foreach($specialTaxes as $specialTax)
                                                                <input type="hidden" name="tax_info[{{$specialTax['tax_id']}}][percent]" value="{{$specialTax['percentage']}}">
                                                                <input type="hidden" name="tax_info[{{$specialTax['tax_id']}}][applied_on]" value="{{json_encode($specialTax['applied_on'])}}">
                                                                <div class="form-group">
                                                                    <div class="col-md-3" style="text-align: right">
                                                                        <label for="name" class="control-label"> {{$specialTax['tax_name']}} </label>
                                                                        <span>*</span>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="text" class="form-control" id="TaxAmount_{{$specialTax['tax_id']}}" name="tax_amount[{{$specialTax['tax_id']}}]" readonly>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            <div class="form-group">
                                                                <div class="col-md-3" style="text-align: right">
                                                                    <label for="name" class="control-label"> Remark </label>
                                                                    <span>*</span>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <textarea class="form-control" name="remark" id="transactionRemark"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-actions noborder row">
                                                            <div class="col-md-offset-3">
                                                                <a class="btn blue" id="transactionSubmit">Submit</a>
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
        <div id="transactionModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Modal Header</h4>
                    </div>
                    <div class="modal-body">
                        <p>Some text in the modal.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

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
<script>
    $(document).ready(function(){

        $("#change_bill").on('change', function(){
            var bill_id = $(this).val();
            window.location.href = "/bill/view/"+bill_id;
        });
        $('select[name="change_bill"]').find('option[value={{$selectedBillId}}]').attr("selected",true);
    });

</script>
@endsection



