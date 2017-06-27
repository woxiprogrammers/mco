@extends('layout.master')
@section('title','Constro | Create Bill')
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
                                <h1>Create Bill</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container" style="width: 100%">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/bill/manage">Manage Bill</a>
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
                                            @if($bills != NULL)
                                                <div class="col-md-offset-8 table-actions-wrapper" style="margin-bottom: 20px">
                                                    <select class="table-group-action-input form-control input-inline input-small input-sm" name="change_bill" id="change_bill">
                                                        <option value="default">Select Bill</option>
                                                        @for($i = 0 ; $i < count($bills); $i++)
                                                            <option value="{{$bills[$i]['id']}}">Bill Array {{$i+1}}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            @endif
                                           <input type="hidden" id="project_site_id" name="project_site_id" value="{{$project_site['id']}}">
                                           <input type="hidden" id="quotation_id" name="quotation_id" value="{{$quotation['id']}}">

                                            <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="createBillTable">
                                                <tr>
                                                    <th width="1%">
                                                        <input type="checkbox" class="group-checkable" disabled="disabled" >
                                                    </th>
                                                    <th width="5%"> Item no </th>
                                                    <th width="15%"> Item Description </th>
                                                    <th width="8%" class="numeric"> UOM </th>
                                                    <th width="8%" class="numeric"> Rate </th>
                                                    <th width="9%" class="numeric"> BOQ Quantity </th>
                                                    <th width="10%" class="numeric"> W.O Amount </th>
                                                    <th width="8%" class="numeric"> Previous Quantity </th>
                                                    <th width="8%" class="numeric"> Current Quantity </th>
                                                    <th width="8%" class="numeric"> Cumulative Quantity </th>
                                                    <th width="7%" class="numeric"> Previous. Bill Amount </th>
                                                    <th width="7%" class="numeric"> Current Bill Amount </th>
                                                    <th width="7%" class="numeric"> Cumulative Bill Amount </th>
                                                </tr>
                                                @for($iterator = 0; $iterator < count($quotationProducts); $iterator++)
                                                    <tr id="id_{{$quotationProducts[$iterator]['id']}}">
                                                        <td>
                                                            <input type="checkbox" id="id_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}]" value="{{$quotationProducts[$iterator]['id']}}">
                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['id']}}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['product_detail']['name']}}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['unit']}}</span>
                                                        </td>
                                                        <td>
                                                            <span id="rate_per_unit_{{$quotationProducts[$iterator]['id']}}">{{$quotationProducts[$iterator]['rate']}}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['quantity']}}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['rate_per_unit'] * $quotationProducts[$iterator]['quantity']}}</span>
                                                        </td>
                                                        <td>
                                                            <span id="previous_quantity_{{$quotationProducts[$iterator]['id']}}">{{$quotationProducts[$iterator]['previous_quantity']}}</span>
                                                        </td>
                                                        <td>
                                                            <input class="form-control" type="text" id="current_quantity_{{$quotationProducts[$iterator]['id']}}" name="current_quantity[{{$quotationProducts[$iterator]['id']}}]" disabled>
                                                        </td>
                                                        <td>
                                                            <span id="cumulative_quantity_{{$quotationProducts[$iterator]['id']}}"></span>
                                                        </td>
                                                        <td>
                                                            <span id="previous_bill_amount_{{$quotationProducts[$iterator]['id']}}"></span>
                                                        </td>
                                                        <td>
                                                            <span id="current_bill_amount_{{$quotationProducts[$iterator]['id']}}"></span>
                                                        </td>
                                                        <td>
                                                            <span id="cumulative_bill_amount_{{$quotationProducts[$iterator]['id']}}"></span>
                                                        </td>
                                                    </tr>
                                                @endfor
                                                    <tr>
                                                        <td colspan="10" style="text-align: right; padding-right: 30px;"><b>Total</b></td>
                                                        <td>
                                                            <span id="total_previous_bill_amount"></span>
                                                        </td>
                                                        <td>
                                                            <span id="total_current_bill_amount"></span>
                                                        </td>
                                                        <td>
                                                            <span id="total_cumulative_bill_amount"></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="10" style="text-align: right; padding-right: 30px;"><b>Total Round</b></td>
                                                        <td>
                                                            <span id="rounded_off_previous_bill_amount"></span>
                                                        </td>
                                                        <td>
                                                            <span id="rounded_off_current_bill_amount"></span>
                                                        </td>
                                                        <td>
                                                            <span id="rounded_off_cumulative_bill_amount"></span>
                                                        </td>
                                                    </tr>
                                                <tr>
                                                    <td colspan="6"><b>Tax Name</b></td>
                                                    <td colspan="4"><b>Tax Rate</b></td>
                                                    <td colspan="3"></td>
                                                </tr>
                                                @for($j = 0 ; $j < count($taxes); $j++)
                                                     <tr>
                                                         <input class="tax_slug" type="hidden" id="tax_slug_{{$taxes[$j]['id']}}" name="tax_slug_{{$taxes[$j]['slug']}}" value="{{$taxes[$j]['slug']}}">
                                                         <td colspan="6" style="text-align: center">{{$taxes[$j]['name']}}</td>
                                                         <td colspan="4" style="text-align: right"><input class="tax form-control" step="any" type="number" id="tax_percentage_{{$taxes[$j]['id']}}" name="tax_percentage[{{$taxes[$j]['id']}}]" value="{{$taxes[$j]['base_percentage']}}" onchange="calculateTax()" onkeyup="calculateTax()"></td>
                                                         <td>
                                                             <span id="tax_previous_bill_amount_{{$taxes[$j]['id']}}"></span>
                                                         </td>
                                                         <td>
                                                             <span id="tax_current_bill_amount_{{$taxes[$j]['id']}}"></span>
                                                         </td>
                                                         <td>
                                                             <span id="tax_cumulative_bill_amount_{{$taxes[$j]['id']}}"></span>
                                                         </td>

                                                     </tr>
                                                @endfor
                                                <tr>
                                                    <td colspan="10" style="text-align: right; padding-right: 30px;"><b>Final Total</b></td>
                                                    <td>
                                                        <span id="final_previous_bill_total"></span>
                                                    </td>
                                                    <td>
                                                        <span id="final_current_bill_total"></span>
                                                    </td>
                                                    <td>
                                                        <span id="final_cumulative_bill_total"></span>
                                                    </td>

                                                </tr>

                                            </table>
                                            <div class="form-group">
                                                <div class="col-md-offset-11">
                                                    <button type="submit" class="btn btn-success"> Submit </button>
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
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/custom/bill/bill.js" type="text/javascript"></script>
@endsection