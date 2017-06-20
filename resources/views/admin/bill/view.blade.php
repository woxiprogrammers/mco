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
                                <h1>View Bill</h1>
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
                                    <a href="javascript:void(0);">View Bill</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body flip-scroll">
                                            @if($bills != NULL)
                                            <div class="col-md-offset-8 table-actions-wrapper" style="margin-bottom: 20px">
                                                <label class="control-label">Select Bill</label>
                                                <select class="table-group-action-input form-control input-inline input-small input-sm" name="change_bill" id="change_bill">
                                                    @for($i = 0 ; $i < count($bills); $i++)
                                                        <option value="{{$bills[$i]['id']}}">Bill Array {{$i+1}}</option>
                                                    @endfor
                                                </select>
                                                <button class="btn btn-info btn-icon" style="margin-left: 50px">Download</button>
                                            </div>
                                            @endif
                                            <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="createBillTable">
                                                <tr style="text-align: center">
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
                                                @for($iterator = 0; $iterator < count($billQuotationProducts); $iterator++)
                                                <tr>
                                                    <td>
                                                        <span id="quotation_product_id">{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{$billQuotationProducts[$iterator]['productDetail']['name']}}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{$billQuotationProducts[$iterator]['unit']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="rate_per_unit_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['quotationProducts']['rate_per_unit']}}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{$billQuotationProducts[$iterator]['quotationProducts']['quantity']}}</span>
                                                    </td>
                                                    <td>
                                                        <span>{{$billQuotationProducts[$iterator]['quotationProducts']['rate_per_unit'] * $billQuotationProducts[$iterator]['quotationProducts']['quantity']}}</span>
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
                                                        <span class="previous_bill_amount" id="previous_bill_amount_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['previous_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span class="current_bill_amount" id="current_bill_amount_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['current_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span class="cumulative_bill_amount" id="cumulative_bill_amount_{{$billQuotationProducts[$iterator]['quotationProducts']['id']}}">{{$billQuotationProducts[$iterator]['cumulative_bill_amount']}}</span>
                                                    </td>
                                                </tr>
                                                @endfor
                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;"><b>Total</b></td>
                                                    <td>
                                                        <span id="total_previous_bill_amount">{{$total['previous_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="total_current_bill_amount">{{$total['current_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="total_cumulative_bill_amount">{{$total['cumulative_bill_amount']}}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;"><b>Total Round</b></td>
                                                    <td>
                                                        <span id="rounded_off_previous_bill_amount">{{$total_rounded['previous_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="rounded_off_current_bill_amount">{{$total_rounded['current_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="rounded_off_cumulative_bill_amount">{{$total_rounded['cumulative_bill_amount']}}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5"><b>Tax Name</b></td>
                                                    <td colspan="4"><b>Tax Rate</b></td>
                                                    <td colspan="3"></td>
                                                </tr>
                                                @for($j = 0 ; $j < count($taxes); $j++)
                                                <tr>
                                                    <td colspan="5" style="text-align: center">{{$taxes[$j]['taxes']['name']}}</td>
                                                    <td colspan="4" style="text-align: center"><span id="percentage">{{$taxes[$j]['percentage']}}</td>
                                                    <td>
                                                        <span id="tax_previous_bill_amount_{{$taxes[$j]['id']}}">{{$taxes[$j]['previous_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="tax_current_bill_amount_{{$taxes[$j]['id']}}">{{$taxes[$j]['current_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="tax_cumulative_bill_amount_{{$taxes[$j]['id']}}">{{$taxes[$j]['cumulative_bill_amount']}}</span>
                                                    </td>

                                                </tr>
                                                @endfor
                                                <tr>
                                                    <td colspan="9" style="text-align: right; padding-right: 30px;"><b>Final Total</b></td>
                                                    <td>
                                                        <span id="final_previous_bill_total">{{$final['previous_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="final_current_bill_total">{{$final['current_bill_amount']}}</span>
                                                    </td>
                                                    <td>
                                                        <span id="final_cumulative_bill_total">{{$final['cumulative_bill_amount']}}</span>
                                                    </td>
                                                </tr>

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
@endsection
@section('javascript')
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script>
    $(document).ready(function (){
        $("#change_bill").on('change', function(){
            var bill_id = $(this).val();
            window.location.href = "/bill/view/"+bill_id;
        });
        $('select[name="change_bill"]').find('option[value={{$selectedBillId}}]').attr("selected",true);
    });
</script>
@endsection



