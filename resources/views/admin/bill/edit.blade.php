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
                                            <div class="portlet-body">
                                                <div class="tab-content">
                                                    <form role="form" id="edit_bill" class="form-horizontal" action="/bill/edit/{{$bill->id}}" method="post">
                                                    <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="createBillTable">
                                                        <tr style="text-align: center">
                                                            <th width="1%">
                                                                <input type="checkbox" class="group-checkable">
                                                            </th>
                                                            <th width="3%"> Item no </th>
                                                            <th width="15%"> Item Description </th>
                                                            <th width="6%" class="numeric"> UOM </th>
                                                            <th width="6%" class="numeric"> Rate </th>
                                                            <th width="7%" class="numeric"> BOQ Quantity </th>
                                                            <th width="10%" class="numeric"> W.O Amount </th>
                                                            <th width="7%" class="numeric"> Previous Quantity </th>
                                                            <th width="7%" class="numeric"> Current Quantity </th>
                                                            <th width="10%" class="numeric"> Cumulative Quantity </th>
                                                            <th width="10%" class="numeric"> Previous. Bill Amount </th>
                                                            <th width="10%" class="numeric"> Current Bill Amount </th>
                                                            <th width="10%" class="numeric"> Cumulative Bill Amount </th>
                                                        </tr>
                                                        @for($iterator = 0; $iterator < count($quotationProducts); $iterator++)
                                                            <tr id="id_{{$quotationProducts[$iterator]['id']}}">
                                                                <td>
                                                                    @if(array_key_exists('current_quantity',$quotationProducts[$iterator]->toArray()))
                                                                    <input type="checkbox" id="id_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}]" value="{{$quotationProducts[$iterator]['id']}}" checked>
                                                                    @else
                                                                    <input type="checkbox" id="id_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}]" value="{{$quotationProducts[$iterator]['id']}}">
                                                                    @endif
                                                                </td>

                                                                <td>
                                                                    <span>{{$iterator + 1}}</span>
                                                                </td>

                                                                <td>
                                                                    <span>{{$quotationProducts[$iterator]->product->name}}</span>
                                                                    @if(array_key_exists('bill_description',$quotationProducts[$iterator]->toArray()))
                                                                        <input class="form-control" type="text" id="product_description_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}][product_description]" value="{{$quotationProducts[$iterator]['bill_description']}}">
                                                                    @else
                                                                        <input class="form-control" type="text" id="product_description_{{$quotationProducts[$iterator]['id']}}" name="quotation_product_id[{{$quotationProducts[$iterator]['id']}}][product_description]" disabled>
                                                                    @endif
                                                                </td>

                                                                <td>
                                                                    <span>{{$quotationProducts[$iterator]->product->unit->name}}</span>
                                                                </td>

                                                                <td>
                                                                    <span id="rate_per_unit_{{$quotationProducts[$iterator]['id']}}">{{$quotationProducts[$iterator]['discounted_rate']}}</span>
                                                                </td>

                                                                <td>
                                                                    <span id="boq_quantity_{{$quotationProducts[$iterator]['id']}}">{{$quotationProducts[$iterator]['quantity']}}</span>
                                                                </td>

                                                                <td>
                                                                    <span>{{$quotationProducts[$iterator]['discounted_rate'] * $quotationProducts[$iterator]['quantity']}}</span>
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
                                                            <td colspan="10" style="text-align: right; padding-right: 30px;">
                                                                <b>Total</b>
                                                            </td>

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
                                                            <td colspan="10" style="text-align: right; padding-right: 30px;">
                                                                <b>Total Round</b>
                                                            </td>

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
                                                        @if($taxes != null)
                                                            <tr>
                                                                <td colspan="6">
                                                                    <b>Tax Name</b>
                                                                </td>
                                                                <td colspan="4">
                                                                    <b>Tax Rate</b>
                                                                </td>
                                                                <td colspan="3">

                                                                </td>
                                                            </tr>
                                                            @for($j = 0 ; $j < count($taxes); $j++)
                                                                <tr>
                                                                    <input class="tax_slug" type="hidden" id="tax_slug_{{$taxes[$j]['id']}}" name="tax_slug_{{$taxes[$j]['slug']}}" value="{{$taxes[$j]['slug']}}">
                                                                    <td colspan="6" style="text-align: center">
                                                                        {{$taxes[$j]['name']}}
                                                                    </td>

                                                                    <td colspan="4" style="text-align: right">
                                                                        <input class="tax form-control" step="any" type="number" id="tax_percentage_{{$taxes[$j]['id']}}" name="tax_percentage[{{$taxes[$j]['id']}}]" value="{{$taxes[$j]['percentage']}}" onchange="calculateTax()" onkeyup="calculateTax()">
                                                                    </td>

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
                                                        @endif

                                                        <tr>
                                                            <td colspan="10" style="text-align: right; padding-right: 30px;">
                                                                <b>Final Total</b>
                                                            </td>

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
<script src="/assets/custom/bill/validation.js" type="text/javascript"></script>
@endsection



