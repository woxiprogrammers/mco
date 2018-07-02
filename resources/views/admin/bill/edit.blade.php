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
                                            <a href="javascript:void(0);">Edit Bill</a>
                                            <i class="fa fa-circle"></i>
                                        </li>
                                    </ul>
                                    <div class="col-md-12">
                                        <!-- BEGIN VALIDATION STATES-->
                                        <div class="portlet light ">
                                            <div class="portlet-body">
                                                <div class="tab-content">
                                                    <input type="hidden" id="quotation_id" value="{{$bill->quotation_id}}">
                                                    <input type="hidden" id="bank_info_id" value="{{$bill->bank_info_id}}">
                                                    <form role="form" id="edit_bill" class="form-horizontal" action="/bill/edit/{{$bill->id}}" method="post">
                                                        <div class="col-md-12 form-group">
                                                            <div class="col-md-4 date date-picker" data-date-end-date="0d">
                                                                <label class="control-label" for="date">Select Bill Date : </label>
                                                                @if(!empty($bill['date']))
                                                                    <input type="text"  name="date" value="{{date('m/d/Y',strtotime($bill['date']))}}" id="date" readonly>
                                                                    <button class="btn btn-sm default" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>
                                                                @else
                                                                    <input type="text" class="form-control" name="date" id="date" readonly/>
                                                                    <button class="btn btn-sm default" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>


                                                                @endif
                                                            </div>
                                                            <div class="col-md-4 date date-picker" data-date-end-date="0d" style="margin-left: 20%">
                                                                <label class="control-label" for="performa_invoice_date" style="margin-left: -60%">Select Proforma Invoice Date : </label>
                                                                @if(!empty($bill['performa_invoice_date']))
                                                                    <input type="text"  name="performa_invoice_date" value="{{date('m/d/Y',strtotime($bill['performa_invoice_date']))}}" id="performa_invoice_date" readonly>
                                                                    <button class="btn btn-sm default" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>
                                                                @else
                                                                    <input type="text" class="form-control" name="performa_invoice_date" id="performa_invoice_date" readonly/>
                                                                    <button class="btn btn-sm default" type="button">
                                                                        <i class="fa fa-calendar"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-2" style="margin-left: -4%">
                                                                <select class="table-group-action-input form-control input-inline input-small input-sm" name="assign_bank" id="assign_bank">
                                                                    <option value="">Select Bank</option>
                                                                    @foreach($allbankInfoIds as $bank)
                                                                        <option value="{{$bank['bank_info_id']}}"> {!! $bank->bankInfo->bank_name !!} - {!! $bank->bankInfo->account_number !!} </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                        </div>

                                                        <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="editBillTable">
                                                        <tr style="text-align: center">
                                                            <th width="1%">
                                                                <input type="checkbox" class="group-checkable">
                                                            </th>
                                                            <th width="5%"> Item no </th>
                                                            <th width="30%"> Item Description </th>
                                                            <th width="6%" class="numeric"> UOM </th>
                                                            <th width="6%" class="numeric"> BOQ Quantity </th>
                                                            <th width="6%" class="numeric"> Rate </th>
                                                            <th width="7%" class="numeric"> W.O Amount </th>
                                                            <th width="5%" class="numeric"> Previous Quantity </th>
                                                            <th width="5%" class="numeric"> Current Quantity </th>
                                                            <th width="8%" class="numeric"> Cumulative Quantity </th>
                                                            <th width="8%" class="numeric"> Current Bill Amount </th>
                                                        </tr>
                                                        @for($iterator = 0; $iterator < count($quotationProducts); $iterator++)
                                                            <tr id="id_{{$quotationProducts[$iterator]['id']}}">
                                                                <td>
                                                                    @if(array_key_exists('current_quantity',$quotationProducts[$iterator]->toArray()))
                                                                        <input class="product-checkbox" type="checkbox" id="id_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}]" value="{{$quotationProducts[$iterator]['id']}}" checked onchange="getTotals()">
                                                                    @else
                                                                        <input class="product-checkbox" type="checkbox" id="id_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}]" value="{{$quotationProducts[$iterator]['id']}}">
                                                                    @endif
                                                                </td>

                                                                <td>
                                                                    <span>{{$iterator + 1}}</span>
                                                                </td>

                                                                <td>
                                                                    <span>{{$quotationProducts[$iterator]->product->name}}</span>
                                                                    <div class="input-group form-group" id="inputGroup">
                                                                        @if(array_key_exists('bill_description',$quotationProducts[$iterator]->toArray()))
                                                                            <input type="hidden" class="product-description-id" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}][product_description_id]" id="product_description_id_{{$quotationProducts[$iterator]['id']}}" value="{{$quotationProducts[$iterator]['bill_product_description_id']}}">
                                                                            <input class="form-control product_description" type="text" id="product_description_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}][product_description]" value="{{$quotationProducts[$iterator]['bill_description']}}">
                                                                        @else
                                                                            <input type="hidden" class="product-description-id" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}][product_description_id]" id="product_description_id_{{$quotationProducts[$iterator]['id']}}">
                                                                            <input class="form-control product_description" type="text" id="product_description_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}][product_description]" disabled>
                                                                        @endif
                                                                        <span class="input-group-addon product_description_create" style="font-size: 12px">C</span>
                                                                        <span class="input-group-addon product_description_update" style="font-size: 12px">U</span>
                                                                        <span class="input-group-addon product_description_delete" style="font-size: 12px">D</span>
                                                                    </div>

                                                                </td>

                                                                <td>
                                                                    <span>{{$quotationProducts[$iterator]->product->unit->name}}</span>
                                                                </td>

                                                                <td>
                                                                    <span id="boq_quantity_{{$quotationProducts[$iterator]['id']}}">{{$quotationProducts[$iterator]['quantity']}}</span>
                                                                </td>

                                                                <td>
                                                                    <span id="rate_per_unit_{{$quotationProducts[$iterator]['id']}}">{{$quotationProducts[$iterator]['discounted_rate']}}</span>
                                                                </td>

                                                                <td>
                                                                    <span>{{round(($quotationProducts[$iterator]['discounted_rate'] * $quotationProducts[$iterator]['quantity']),3)}}</span>
                                                                </td>

                                                                <td>
                                                                    <span id="previous_quantity_{{$quotationProducts[$iterator]['id']}}">{{$quotationProducts[$iterator]['previous_quantity']}}</span>
                                                                </td>

                                                                <td class="form-group">
                                                                    @if(array_key_exists('current_quantity',$quotationProducts[$iterator]->toArray()))
                                                                        <input class="form-control current_quantity" type="text" id="current_quantity_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}][current_quantity]" value="{{$quotationProducts[$iterator]['current_quantity']}}">
                                                                    @else
                                                                        <input class="form-control current_quantity" type="text" id="current_quantity_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}][current_quantity]" disabled>
                                                                    @endif
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
                                                        @for($iterator = 0; $iterator < count($quotationExtraItems); $iterator++)
                                                            <tr>
                                                                <td>
                                                                    @if(array_key_exists('current_rate',$quotationExtraItems[$iterator]->toArray()))
                                                                        <input type="checkbox" id="id_{{$quotationExtraItems[$iterator]->id}}" name="extra_item[{{$quotationExtraItems[$iterator]->id}}]" value="{{$quotationExtraItems[$iterator]->id}}" class="extra-item-checkbox" checked>
                                                                    @else
                                                                        <input type="checkbox" id="id_{{$quotationExtraItems[$iterator]->id}}" name="extra_item[{{$quotationExtraItems[$iterator]->id}}]" value="{{$quotationExtraItems[$iterator]->id}}" class="extra-item-checkbox">
                                                                    @endif
                                                                </td>
                                                                <td colspan="4">
                                                            <span>
                                                                {{$quotationExtraItems[$iterator]->extraItem->name}}
                                                                @if(array_key_exists('description',$quotationExtraItems[$iterator]->toArray()))
                                                                    <input class="form-control extra_item_description" type="text" id="extra_item_description_{{$quotationExtraItems[$iterator]->id}}" name="extra_item[{{$quotationExtraItems[$iterator]->id}}][description]" value="{{$quotationExtraItems[$iterator]['description']}}">
                                                                @else
                                                                    <input class="form-control extra_item_description" type="text" id="extra_item_description_{{$quotationExtraItems[$iterator]->id}}" name="extra_item[{{$quotationExtraItems[$iterator]->id}}][description]" disabled>
                                                                @endif
                                                            </span>
                                                                </td>
                                                                <td colspan="2">
                                                                    <span id="total_extra_item_rate_{{$quotationExtraItems[$iterator]->id}}">{{$quotationExtraItems[$iterator]->rate}}</span>
                                                                </td>
                                                                <td colspan="2">
                                                                    <span id="previous_rates_{{$quotationExtraItems[$iterator]->id}}">{{$quotationExtraItems[$iterator]->prev_amount}}</span>
                                                                </td>
                                                                <td colspan="2" class="form-group">
                                                                    @if(array_key_exists('current_rate',$quotationExtraItems[$iterator]->toArray()))
                                                                        <input class="form-control" type="text" id="extra_item_rate_{{$quotationExtraItems[$iterator]->id}}" name="extra_item[{{$quotationExtraItems[$iterator]->id}}][rate]" value="{{$quotationExtraItems[$iterator]->current_rate}}" onchange="checkExtraItemRate({{$quotationExtraItems[$iterator]->id}})">
                                                                    @else
                                                                        <input class="form-control" type="text" id="extra_item_rate_{{$quotationExtraItems[$iterator]->id}}" name="extra_item[{{$quotationExtraItems[$iterator]->id}}][rate]" disabled>
                                                                    @endif

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
                                                                    <input name="discount_amount" id="discountAmount" class="form-control" type="text" value="{{$bill->discount_amount}}">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="10" style="text-align: right; padding-right: 30px;"><b>Discount Description</b></td>
                                                                <td>
                                                                    <input name="discount_description" id="discountDescription" class="form-control" type="text" value="{{$bill->discount_description}}">
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
                                                                <td colspan="6">
                                                                    <b>Tax Name</b>
                                                                </td>
                                                                <td colspan="4">
                                                                    <b>Tax Rate</b>
                                                                </td>
                                                                <td colspan="1">

                                                                </td>
                                                            </tr>
                                                            @for($j = 0 ; $j < count($taxes); $j++)
                                                                <tr>
                                                                    <input type="hidden" id="is_already_applied" name="tax_data[{{$taxes[$j]['id']}}][is_already_applied]" value="{{$taxes[$j]['already_applied']}}">
                                                                    @if($taxes[$j]['already_applied'] == 0)
                                                                        <td colspan="6" style="text-align: center">
                                                                            {{$taxes[$j]['name']}} ("Below tax are newly added, if you don't want then enter 0 in rate field.")
                                                                        </td>
                                                                    @else
                                                                        <td colspan="6" style="text-align: center">
                                                                            {{$taxes[$j]['name']}}
                                                                        </td>
                                                                    @endif

                                                                    <td colspan="4" style="text-align: right">
                                                                        <input class="tax form-control" step="any" type="number" id="tax_percentage_{{$taxes[$j]['id']}}" name="tax_data[{{$taxes[$j]['id']}}][percentage]" value="{{$taxes[$j]['percentage']}}" onchange="calculateTax()" onkeyup="calculateTax()">
                                                                    </td>

                                                                    <td>
                                                                        <span id="tax_current_bill_amount_{{$taxes[$j]['id']}}"></span>
                                                                    </td>
                                                                </tr>
                                                            @endfor
                                                        @endif

                                                        <tr>
                                                            <td colspan="10" style="text-align: right; padding-right: 30px;">
                                                                <b>Final Total</b>
                                                            </td>

                                                            <td>
                                                                <span id="final_current_bill_total"></span>
                                                            </td>
                                                        </tr>
                                                        @if(!empty($specialTaxes))
                                                        @foreach($specialTaxes as $specialTax)
                                                        <tr>
                                                            <td colspan="7" style="text-align: right; padding-right: 30px;"><b>{{$specialTax['name']}}</b><input type="hidden" class="special-tax" name="special_tax[]" value="{{$specialTax['id']}}"> </td>
                                                            <input type="hidden" id="is_already_applied" name="applied_on[{{$specialTax['id']}}][is_already_applied]" value="{{$specialTax['already_applied']}}">
                                                            <td><input class="form-control" name="applied_on[{{$specialTax['id']}}][percentage]" value="{{$specialTax['percentage']}}" id="tax_percentage_{{$specialTax['id']}}" onchange="calculateTax()" onkeyup="calculateTax()"> </td>
                                                            <td colspan="2">
                                                                <a class="btn green sbold uppercase btn-outline btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> Applied On
                                                                    <i class="fa fa-angle-down"></i>
                                                                </a>
                                                                <ul class="dropdown-menu" style="position: relative">
                                                                    <li>
                                                                        @if(in_array(0,$specialTax['applied_on']))
                                                                            <input type="checkbox" class="tax-applied-on special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="0" checked> Total Round
                                                                        @else
                                                                            <input type="checkbox" class="tax-applied-on special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="0"> Total Round
                                                                        @endif
                                                                    </li>
                                                                    @foreach($taxes as $tax)
                                                                    <li>
                                                                        @if(in_array($tax['id'],$specialTax['applied_on']))
                                                                        <input type="checkbox" class="tax-applied-on special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="{{$tax['id']}}" checked> {{$tax['name']}}
                                                                        @else
                                                                        <input type="checkbox" class="tax-applied-on special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="{{$tax['id']}}"> {{$tax['name']}}
                                                                        @endif
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
                                                        <div class="col-md-offset-11">
                                                            <button type="submit" class="btn btn-success" id="submit"> Submit </button>
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

@endsection
@section('javascript')
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/custom/bill/bill-edit.js" type="text/javascript"></script>
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<script src="/assets/custom/bill/bill-typeahead.js" type="text/javascript"></script>
<script src="/assets/custom/bill/validation.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>

<script>
    var bank_info_id = $('#bank_info_id').val();
    $('select[name="assign_bank"]').find('option[value="' + bank_info_id + '"]').attr("selected",true);
</script>
@endsection



