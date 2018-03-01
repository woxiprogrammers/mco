@extends('layout.master')
@section('title','Constro | Create Bill')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
<link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet"  href="/assets/global/plugins/typeahead/typeahead.css"/>
<link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
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
                                <h1>Create Bill</h1>
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
                                    <a href="javascript:void(0);">Create Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                        <div class="portlet-body flip-scroll">
                                           <form role="form" id="new_bill" class="form-horizontal" action="/bill/create" method="post">
                                               <div class="row">
                                                   <div class="form-group">
                                                       <div class="col-md-3 date date-picker" data-date-end-date="0d">
                                                           <label class="control-label" for="date">Bill Date : </label>
                                                               <input type="text" style="width: 30%" name="date" placeholder="Select Bill Date" id="date"/>
                                                               <button class="btn btn-sm default" type="button">
                                                                   <i class="fa fa-calendar"></i>
                                                               </button>
                                                       </div>
                                                       <div class="col-md-4 date date-picker" data-date-end-date="0d">
                                                           <label class="control-label" for="performa_invoice_date" style="margin-left: 9%">Performa Invoice Date : </label>
                                                           <input type="text" style="width: 32%" name="performa_invoice_date" placeholder="Select Performa Invoice Date" id="performa_invoice_date"/>
                                                           <button class="btn btn-sm default" type="button">
                                                               <i class="fa fa-calendar"></i>
                                                           </button>
                                                       </div>
                                                       <div class="col-md-2" style="margin-left: 6%">
                                                           <select class="table-group-action-input form-control input-inline input-small input-sm" name="assign_bank" id="assign_bank">
                                                               <option value="default">Select Bank</option>
                                                               @foreach($banksAssigned as $bankId)
                                                                   <option value="{{$bankId['bank_info_id']}}">{{$bankId->bankInfo->bank_name}} - {{$bankId->bankInfo->account_number}} </option>
                                                                @endforeach
                                                           </select>
                                                       </div>
                                                       @if($bills != NULL)
                                                           <div class="col-md-offset-8 table-actions-wrapper" style="margin-bottom: 20px; margin-left: 86%">
                                                               <select class="table-group-action-input form-control input-inline input-small input-sm" name="change_bill" id="change_bill">
                                                                   <option value="default">Select Bill</option>
                                                                   @for($i = 0 ; $i < count($bills); $i++)
                                                                       <option value="{{$bills[$i]['id']}}">R.A Bill {{$i+1}}</option>
                                                                   @endfor
                                                               </select>
                                                           </div>
                                                       @endif
                                                   </div>
                                               </div>
                                           <input type="hidden" id="project_site_id" name="project_site_id" value="{{$project_site['id']}}">
                                           <input type="hidden" id="quotation_id" name="quotation_id" value="{{$quotation['id']}}">

                                            <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="createBillTable">
                                                <tr>
                                                    <th width="1%">
                                                        <input type="checkbox" class="group-checkable" disabled="disabled" >
                                                    </th>
                                                    <th width="5%" style="text-align: center"> Item no </th>
                                                    <th width="30%" style="text-align: center"> Item Description </th>
                                                    <th width="6%" class="numeric" style="text-align: center"> UOM </th>
                                                    <th width="6%" class="numeric" style="text-align: center"> BOQ Quantity </th>
                                                    <th width="6%" class="numeric" style="text-align: center"> Rate </th>
                                                    <th width="7%" class="numeric" style="text-align: center"> W.O Amount </th>
                                                    <th width="5%" class="numeric" style="text-align: center"> Previous Quantity </th>
                                                    <th width="5%" class="numeric" style="text-align: center"> Current Quantity </th>
                                                    <th width="8%" class="numeric" style="text-align: center"> Cumulative Quantity </th>
                                                    <th width="8%" class="numeric" style="text-align: center"> Current Bill Amount </th>
                                                </tr>
                                                @for($iterator = 0; $iterator < count($quotationProducts); $iterator++)
                                                    <tr id="id_{{$quotationProducts[$iterator]['id']}}">
                                                        <td>
                                                            <input type="checkbox" id="id_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}]" value="{{$quotationProducts[$iterator]['id']}}" class="product-checkbox">
                                                        </td>
                                                        <td>
                                                            <span>{{$iterator + 1}}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['product_detail']['name']}}</span>
                                                            <div class="input-group form-group" id="inputGroup" style="padding-left: 10%; padding-right: 10%">
                                                                <input type="hidden" class="product-description-id" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}][product_description_id]" id="product_description_id_{{$quotationProducts[$iterator]['id']}}" disabled>
                                                                <input class="product_description form-control" type="text" id="product_description_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}][product_description]" disabled>
                                                                <span class="input-group-addon product_description_create" style="font-size: 12px">C</span>
                                                                <span class="input-group-addon product_description_update" style="font-size: 12px">U</span>
                                                                <span class="input-group-addon product_description_delete" style="font-size: 12px">D</span>
                                                            </div>

                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['unit']}}</span>
                                                        </td>
                                                        <td>
                                                            <span id="boq_quantity_{{$quotationProducts[$iterator]['id']}}">{{$quotationProducts[$iterator]['quantity']}}</span>
                                                        </td>
                                                        <td>
                                                            <span id="rate_per_unit_{{$quotationProducts[$iterator]['id']}}">{{$quotationProducts[$iterator]['rate']}}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['rate'] * $quotationProducts[$iterator]['quantity']}}</span>
                                                        </td>
                                                        <td>
                                                            <span id="previous_quantity_{{$quotationProducts[$iterator]['id']}}">{{$quotationProducts[$iterator]['previous_quantity']}}</span>
                                                        </td>
                                                        <td class="form-group">
                                                                <input class="form-control current_quantity" type="text" id="current_quantity_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}][current_quantity]" disabled>
                                                        </td>
                                                        <td>
                                                            <span id="cumulative_quantity_{{$quotationProducts[$iterator]['id']}}"></span>
                                                        </td>

                                                        <td>
                                                            <span id="current_bill_amount_{{$quotationProducts[$iterator]['id']}}"></span>
                                                        </td>

                                                    </tr>
                                                @endfor
                                                <tr>
                                                    <td colspan="11" style="background-color: #F5F5F5">&nbsp; </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5"><b>Extra Items</b></td>
                                                    <td colspan="2"><b>Total Amount Approved</b></td>
                                                    <td colspan="2"><b>Previous Amount</b></td>
                                                    <td colspan="2"><b>Current Amount</b></td>
                                                </tr>
                                                @for($iterator = 0; $iterator < count($extraItems); $iterator++)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" id="id_{{$extraItems[$iterator]->id}}" name="extra_item[{{$extraItems[$iterator]->id}}]" value="{{$extraItems[$iterator]->id}}" class="extra-item-checkbox">
                                                        </td>
                                                        <td colspan="4">
                                                            <span>
                                                                {{$extraItems[$iterator]->extraItem->name}}
                                                                <input class="extra_item_description form-control" type="text" id="extra_item_description_{{$extraItems[$iterator]->id}}" name="extra_item[{{$extraItems[$iterator]->id}}][description]" disabled>
                                                            </span>
                                                        </td>
                                                        <td colspan="2">
                                                            <span id="total_extra_item_rate_{{$extraItems[$iterator]->id}}">{{$extraItems[$iterator]->rate}}</span>
                                                        </td>
                                                        <td colspan="2">
                                                            <span id="previous_rates_{{$extraItems[$iterator]->id}}">{{$extraItems[$iterator]->previous_rate}}</span>
                                                        </td>
                                                        <td colspan="2" class="form-group">
                                                            <input class="form-control" type="text" id="extra_item_rate_{{$extraItems[$iterator]->id}}" name="extra_item[{{$extraItems[$iterator]->id}}][rate]" disabled>
                                                        </td>
                                                    </tr>
                                                @endfor
                                                <tr>
                                                    <td colspan="10" style="text-align: right; padding-right: 30px;"><b>Sub Total</b></td>
                                                    <td>
                                                        <span id="sub_total_current_bill_amount"></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="10" style="text-align: right; padding-right: 30px;"><b>Sub Total Round</b></td>
                                                    <td>
                                                        <span id="rounded_off_current_bill_sub_total"></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="10" style="text-align: right; padding-right: 30px;"><b>Discount Amount</b></td>
                                                    <td>
                                                        <input name="discount_amount" id="discountAmount" class="form-control" type="text" value="0">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="10" style="text-align: right; padding-right: 30px;"><b>Discount Description</b></td>
                                                    <td>
                                                        <input name="discount_description" id="discountDescription" class="form-control" type="text">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="10" style="text-align: right; padding-right: 30px;"><b>Total Round</b></td>
                                                    <td>
                                                        <span id="rounded_off_current_bill_amount"></span>
                                                    </td>
                                                </tr>
                                                @if($taxes != null)
                                                    <tr>
                                                        <td colspan="6"><b>Tax Name</b></td>
                                                        <td colspan="4"><b>Tax Rate</b></td>
                                                        <td colspan="1"></td>
                                                    </tr>
                                                    @for($j = 0 ; $j < count($taxes); $j++)
                                                         <tr>
                                                            <!-- <input class="tax_slug" type="hidden" id="tax_slug_{{$taxes[$j]['id']}}" name="tax_slug_{{$taxes[$j]['slug']}}" value="{{$taxes[$j]['slug']}}">-->
                                                             <td colspan="6" style="text-align: center">{{$taxes[$j]['name']}}</td>
                                                             <td colspan="4" style="text-align: right"><input class="tax form-control" step="any" type="number" id="tax_percentage_{{$taxes[$j]['id']}}" name="tax_percentage[{{$taxes[$j]['id']}}]" value="{{$taxes[$j]['base_percentage']}}" onchange="calculateTax()" onkeyup="calculateTax()"></td>

                                                             <td>
                                                                 <span id="tax_current_bill_amount_{{$taxes[$j]['id']}}"></span>
                                                             </td>


                                                         </tr>
                                                    @endfor
                                                @endif
                                                <tr>
                                                    <td colspan="10" style="text-align: right; padding-right: 30px;"><b>Final Total</b></td>

                                                    <td>
                                                        <span id="final_current_bill_total"></span>
                                                    </td>


                                                </tr>
                                                @if(!empty($specialTaxes))
                                                    @foreach($specialTaxes as $specialTax)
                                                        <tr>
                                                            <td colspan="7" style="text-align: right; padding-right: 30px;"><b>{{$specialTax['name']}}</b><input type="hidden" class="special-tax" name="special_tax[]" value="{{$specialTax['id']}}"> </td>
                                                            <td><input class="form-control" name="applied_on[{{$specialTax['id']}}][percentage]" value="{{$specialTax['base_percentage']}}" id="tax_percentage_{{$specialTax['id']}}" onchange="calculateTax()" onkeyup="calculateTax()"> </td>
                                                            <td colspan="2">
                                                                <a class="btn green sbold uppercase btn-outline btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> Applied On
                                                                    <i class="fa fa-angle-down"></i>
                                                                </a>
                                                                <ul class="dropdown-menu" style="position: relative">
                                                                    <li>
                                                                        <input type="checkbox" class="tax-applied-on special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="0"> Total Round
                                                                    </li>
                                                                    @foreach($taxes as $tax)
                                                                        <li>
                                                                            <input type="checkbox" class="tax-applied-on special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="{{$tax['id']}}"> {{$tax['name']}}
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </td>
                                                            <td>
                                                                <span id="tax_current_bill_amount_{{$specialTax['id']}}" class="special-tax-amount"></span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                <tr>
                                                    <td colspan="10" style="text-align: right; padding-right: 30px;"><b> Grand Total</b></td>
                                                    <td>
                                                        <span id="grand_current_bill_total"></span>
                                                    </td>
                                                </tr>

                                            </table>
                                            <div class="form-group">
                                                <div class="col-md-offset-11" style="margin-left: 91%">
                                                    <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit </button>
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
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script><script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/custom/bill/bill.js" type="text/javascript"></script>
<script src="/assets/custom/bill/validation.js" type="text/javascript"></script>
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<script src="/assets/custom/bill/bill-typeahead.js" type="text/javascript"></script>
<script>
    var date=new Date();
    $('#date').val((date.getMonth()+1)+"/"+date.getDate()+"/"+date.getFullYear());
    $('#performa_invoice_date').val((date.getMonth()+1)+"/"+date.getDate()+"/"+date.getFullYear());
</script>
@endsection