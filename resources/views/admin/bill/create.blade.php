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
                                            @if($bills != NULL)
                                                <div class="col-md-offset-8 table-actions-wrapper" style="margin-bottom: 20px">
                                                    <select class="table-group-action-input form-control input-inline input-small input-sm" name="change_bill" id="change_bill">
                                                        <option value="default">Select Bill</option>
                                                        @for($i = 0 ; $i < count($bills); $i++)
                                                            <option value="{{$bills[$i]['id']}}">R.A Bill {{$i+1}}</option>
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
                                                    <th width="30%"> Item Description </th>
                                                    <th width="6%" class="numeric"> UOM </th>
                                                    <th width="6%" class="numeric"> Rate </th>
                                                    <th width="6%" class="numeric"> BOQ Quantity </th>
                                                    <th width="7%" class="numeric"> W.O Amount </th>
                                                    <th width="5%" class="numeric"> Previous Quantity </th>
                                                    <th width="5%" class="numeric"> Current Quantity </th>
                                                    <th width="8%" class="numeric"> Cumulative Quantity </th>
                                                    <th width="8%" class="numeric"> Current Bill Amount </th>
                                                </tr>
                                                @for($iterator = 0; $iterator < count($quotationProducts); $iterator++)
                                                    <tr id="id_{{$quotationProducts[$iterator]['id']}}">
                                                        <td>
                                                            <input type="checkbox" id="id_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}]" value="{{$quotationProducts[$iterator]['id']}}" class="require-one">
                                                        </td>
                                                        <td>
                                                            <span>{{$iterator + 1}}</span>
                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['product_detail']['name']}}</span>
                                                            <div class="input-group" id="inputGroup">
                                                                <input class="product_description form-control" type="text" id="product_description_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}][product_description]" disabled>
                                                                <span class="input-group-addon" id="product_description_create" style="font-size: 12px">C</span>
                                                                <span class="input-group-addon" id="product_description_update" style="font-size: 12px">U</span>
                                                                <span class="input-group-addon" id="product_description_delete" style="font-size: 12px">D</span>
                                                            </div>

                                                        </td>
                                                        <td>
                                                            <span>{{$quotationProducts[$iterator]['unit']}}</span>
                                                        </td>
                                                        <td>
                                                            <span id="rate_per_unit_{{$quotationProducts[$iterator]['id']}}">{{$quotationProducts[$iterator]['rate']}}</span>
                                                        </td>
                                                        <td>
                                                            <span id="boq_quantity_{{$quotationProducts[$iterator]['id']}}">{{$quotationProducts[$iterator]['quantity']}}</span>
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
                                                        <td colspan="10" style="text-align: right; padding-right: 30px;"><b>Total</b></td>

                                                        <td>
                                                            <span id="total_current_bill_amount"></span>
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
                                                             <input class="tax_slug" type="hidden" id="tax_slug_{{$taxes[$j]['id']}}" name="tax_slug_{{$taxes[$j]['slug']}}" value="{{$taxes[$j]['slug']}}">
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
@endsection
@section('javascript')
<script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/custom/bill/bill.js" type="text/javascript"></script>
<script src="/assets/custom/bill/validation.js" type="text/javascript"></script>
@endsection