@extends('layout.master')
@section('title','Constro | Create Subcontractor Structure Bill')
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
    <input type="hidden" id="subcontractorStructureSlug" value="{{$subcontractorStructure->contractType->slug}}">
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
                                    <h1>Create Subcontractor Structure Bill</h1>
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
                                        <a href="javascript:void(0);">Create Subcontractor Structure Bill</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <div class="form-body">
                                                <form role="form" id="createStructureBill" class="form-horizontal" action="/subcontractor/bill/create/{!! $subcontractorStructure['id'] !!}" method="post">
                                                    {!! csrf_field() !!}
                                                    <div class="row">
                                                        <div class="col-md-6 date date-picker" data-date-end-date="0d" data-date-format="dd/mm/yyyy">
                                                            <label class="control-label" for="date">Bill Date : </label>
                                                            <input type="text" style="width: 30%" name="bill_date" placeholder="Select Bill Date" value="" id="date" required>
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </div>
                                                        <div class="col-md-6 date date-picker" data-date-end-date="0d" data-date-format="dd/mm/yyyy">
                                                            <label class="control-label" for="performa_invoice_date" style="margin-left: 9%">Proforma Invoice Date : </label>
                                                            <input type="text" style="width: 32%" name="performa_invoice_date" value="" placeholder="Select Proforma Invoice Date" id="performa_invoice_date" required/>
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll;align-content: center; " id="parentBillTable">
                                                        <thead>
                                                        <tr id="tableHeader">
                                                            <th width="10%" style="text-align: center"><b> Bill No  </b></th>
                                                            @if($subcontractorStructure->contractType->slug == 'itemwise' || $subcontractorStructure->contractType->slug == 'amountwise')
                                                                <th width="5%">
                                                                    <b>Select</b>
                                                                </th>
                                                            @endif
                                                            <th width="10%">
                                                                <b>Summary</b>
                                                            </th>
                                                            <th width="10%" style="text-align: center"><b> Description </b></th>
                                                            <th width="10%"><b>Total Work Area</b></th>
                                                            <th width="10%" class="numeric" style="text-align: center"><b> Rate </b></th>
                                                            <th width="10%" class="numeric" style="text-align: center"><b> Unit </b></th>
                                                            <th width="10%" class="numeric" style="text-align: center"><b> Amount </b></th>
                                                            <th width="8%" class="numeric" style="text-align: center"><b> Previous Quantity </b></th>
                                                            <th width="8%" class="numeric" style="text-align: center"><b> Current Quantity </b></th>
                                                            <th width="10%" class="numeric" style="text-align: center"><b> Cummulative Quantity </b></th>
                                                            <th width="15%" class="numeric" style="text-align: center"><b> Current Bill Amount </b></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($subcontractorStructureSummaries as $index => $structureSummary)
                                                            <tr>
                                                                @if ($index == 0)
                                                                    <td rowspan="{!! count($subcontractorStructure->summaries) !!}"> {{ $billName }}</td>
                                                                @endif
                                                                @if($subcontractorStructure->contractType->slug == 'itemwise')
                                                                    <td>
                                                                        <div class="form-group" > <input type="checkbox" class="checkbox-inline structure-summary" name="structure_summaries[]" value="{{$structureSummary['id']}}" onclick="structureSummarySelected(this)"></div>
                                                                    </td>
                                                                    <td>
                                                                        <label class="control-label"> {{$structureSummary['summary_name']}}</label>
                                                                    </td>
                                                                    <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><textarea class="form-control description" readonly></textarea></div></td>
                                                                    <td >{{$structureSummary['total_work_area']}} <input type="hidden" name="total_work_area[{{$structureSummary['id']}}]" value="{{$structureSummary['total_work_area']}}"></td>
                                                                    <td ><label class="control-label rate">{{$structureSummary['rate']}}</label></td>
                                                                    <td ><label class="control-label">{!! $structureSummary['unit'] !!}</label></td>
                                                                    <td >{!! $structureSummary['total_work_area'] * $structureSummary['rate'] !!}</td>
                                                                    <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><input type="text" class="prev-qty form-control" value="{{$structureSummary['prev_quantity']}}" readonly></div></td>
                                                                    <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><input type="text" class="form-control quantity" max="{{$structureSummary['allowed_quantity']}}" onkeyup="calculateAmount(this)" readonly> </div></td>
                                                                    <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><input type="text" class="cummulative-qty form-control" value="{{$structureSummary['prev_quantity']}}" readonly></div></td>
                                                                    <td > <label class="control-label bill-amount"> 0 </label> </td>
                                                                @elseif($subcontractorStructure->contractType->slug == 'amountwise')
                                                                        <td>
                                                                            <div class="form-group" > <input type="checkbox" class="checkbox-inline structure-summary" name="structure_summaries[]" value="{{$structureSummary['id']}}" onclick="structureSummarySelected(this)"></div>
                                                                        </td>
                                                                        <td>
                                                                            <label class="control-label"> {{$structureSummary['summary_name']}}</label>
                                                                        </td>
                                                                        <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><textarea class="form-control description" readonly></textarea></div></td>
                                                                        <td >{{$structureSummary['total_work_area']}} <input type="hidden" name="total_work_area[{{$structureSummary['id']}}]" value="{{$structureSummary['total_work_area']}}"></td>
                                                                        <td ><label class="control-label rate">{{$structureSummary['rate']}}</label></td>
                                                                        <td ><label class="control-label">{!! $structureSummary['unit'] !!}</label></td>
                                                                        <td ><span class="total_amount">{!! $structureSummary['total_work_area'] * $structureSummary['rate'] !!}</span></td>
                                                                        <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><input type="text" class="prev-qty form-control" value="{{$structureSummary['prev_quantity']}}" readonly></div></td>
                                                                        <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><input type="text" class="form-control quantity" max="{{$structureSummary['allowed_quantity']}}" min="0.000001" onkeyup="calculateAmount(this)" {{--name="quantity[{{$structureSummary['id']}}]"--}} readonly> </div></td>
                                                                        <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><input type="text" class="cummulative-qty form-control" value="{{$structureSummary['prev_quantity']}}" readonly></div></td>
                                                                        <td > <label class="control-label bill-amount"> 0 </label> </td>
                                                                @else
                                                                    <td>
                                                                        <label class="control-label"> {{$structureSummary['summary_name']}}</label>
                                                                        <input type="hidden" name="structure_summaries[]" value="{{$structureSummary['id']}}">
                                                                    </td>
                                                                    <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><textarea class="form-control description" name="description[{{$structureSummary['id']}}]"></textarea></div></td>
                                                                    <td >{{$structureSummary['total_work_area']}} <input type="hidden" name="total_work_area[{{$structureSummary['id']}}]" value="{{$structureSummary['total_work_area']}}"></td>
                                                                    <td ><label class="control-label rate">{{$structureSummary['rate']}}</label></td>
                                                                    <td ><label class="control-label">{!! $structureSummary['unit'] !!}</label></td>
                                                                    <td ><span class="total_amount">{!! $structureSummary['total_work_area'] * $structureSummary['rate'] !!}</span></td>
                                                                    <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><input type="text" class="prev-qty form-control" value="{{$structureSummary['prev_quantity']}}" readonly></div></td>
                                                                    <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><input type="text" class="form-control quantity" max="{{$structureSummary['allowed_quantity']}}" min="0.000001" onkeyup="calculateAmount(this)" name="quantity[{{$structureSummary['id']}}]" required> </div></td>
                                                                    <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><input type="text" class="cummulative-qty form-control" value="{{$structureSummary['prev_quantity']}}" readonly></div></td>
                                                                    <td > <label class="control-label bill-amount"> 0 </label> </td>
                                                                @endif

                                                            </tr>
                                                        @endforeach

                                                        <tr>
                                                            @if($subcontractorStructure->contractType->slug == 'itemwise')
                                                                <td colspan="12">
                                                            @else
                                                                <td colspan="11">
                                                            @endif
                                                                    <label class="control-label"> <b> Extra Items</b></label>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="2" style="text-align: center;">
                                                                Action
                                                            </th>
                                                            @if($subcontractorStructure->contractType->slug == 'itemwise')
                                                                <th colspan="3" style="text-align: center;">
                                                            @else
                                                                <th colspan="2" style="text-align: center;">
                                                            @endif
                                                                Name
                                                            </th>
                                                            <th colspan="3" style="text-align: center;">
                                                                Description
                                                            </th>
                                                            <th colspan="2" style="text-align: center;">
                                                                Rate
                                                            </th>
                                                            <th colspan="3" style="text-align: center;">
                                                                Current rate
                                                            </th>
                                                        </tr>
                                                        @foreach($structureExtraItems as $structureExtraItem)
                                                            <tr>
                                                                <td colspan="2">
                                                                    <input type="checkbox" name="structure_extra_item_ids[]" value="{{$structureExtraItem['subcontractor_structure_extra_item_id']}}" onclick="extraItemClick(this)">
                                                                </td>
                                                                @if($subcontractorStructure->contractType->slug == 'itemwise')
                                                                    <td colspan="3">

                                                                @else
                                                                    <td colspan="2">

                                                                @endif
                                                                    {{$structureExtraItem['name']}}
                                                                </td>
                                                                <td colspan="3">
                                                                    <div class="form-group" style="margin: 1%;">
                                                                        <input type="text" class="form-control extra-item-description"  readonly>
                                                                    </div>
                                                                </td>

                                                                <td colspan="2">
                                                                    {{$structureExtraItem['rate']}}
                                                                </td>
                                                                <td colspan="3">
                                                                    <div class="form-group" style="margin: 1%;">
                                                                        <input type="text" class="form-control extra-item"  value="0" onkeyup="calculateSubtotal()" readonly>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        <tr>
                                                            @if($subcontractorStructure->contractType->slug == 'itemwise' || $subcontractorStructure->contractType->slug == 'amountwise')
                                                                <td colspan="10">
                                                            @else
                                                                <td colspan="9">
                                                            @endif
                                                                    <label class="control-label pull-right" style="margin-right: 3%; margin-bottom: 1%;"> <b>Subtotal</b> </label>
                                                                </td>
                                                                <td colspan="2">
                                                                    <label class="control-label" id="subtotal" style="margin-right: 3%; margin-bottom: 1%;">  </label>
                                                                    <input type="hidden" name="subtotal">
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                            @if($subcontractorStructure->contractType->slug == 'itemwise' || $subcontractorStructure->contractType->slug == 'amountwise')
                                                                <td colspan="6">
                                                            @else
                                                                <td colspan="5">
                                                            @endif
                                                                    <b>Discount</b>
                                                            </td>
                                                            <td colspan="4">
                                                                <div class="form-group" style="margin: 1%">
                                                                    <textarea class="form-control" name="discount_description" placeholder="Discount Description"></textarea>
                                                                </div>
                                                            </td>
                                                            <td colspan="2">
                                                                <div class="form-group" style="margin: 1%">
                                                                    <input type="text" class="form-control" name="discount" placeholder="Discount Amount" id="discount" onkeyup="calculateDiscount()">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            @if($subcontractorStructure->contractType->slug == 'itemwise' || $subcontractorStructure->contractType->slug == 'amountwise')
                                                                <td colspan="10">
                                                            @else
                                                                <td colspan="9">
                                                            @endif
                                                                <label class="control-label pull-right" style="margin-right: 3%; margin-bottom: 1%;"> <b>Discounted Amount</b> </label>
                                                            </td>
                                                            <td colspan="2">
                                                                <label class="control-label" id="discountedTotal" style="margin-right: 3%; margin-bottom: 1%;">  </label>
                                                            </td>
                                                        </tr>
                                                        @if(count($taxes) > 0)
                                                            <tr>
                                                                @if($subcontractorStructure->contractType->slug == 'itemwise' || $subcontractorStructure->contractType->slug == 'amountwise')
                                                                    <td colspan="12">
                                                                @else
                                                                    <td colspan="11">
                                                                        @endif
                                                                        <label class="control-label"> <b> Taxes</b></label>
                                                                    </td>
                                                            </tr>
                                                            <tr>
                                                                @if($subcontractorStructure->contractType->slug == 'itemwise' || $subcontractorStructure->contractType->slug == 'amountwise')
                                                                    <td colspan="6">
                                                                @else
                                                                    <td colspan="5">
                                                                @endif
                                                                    <b>Tax Name</b>
                                                                </td>
                                                                <td colspan="4">
                                                                    <b>Tax Rate (%)</b>
                                                                </td>
                                                                <td colspan="2">

                                                                </td>
                                                            </tr>
                                                            @foreach($taxes as $key => $taxData)
                                                                <tr>
                                                                    @if($subcontractorStructure->contractType->slug == 'itemwise' || $subcontractorStructure->contractType->slug == 'amountwise')
                                                                        <td colspan="6">
                                                                    @else
                                                                        <td colspan="5">
                                                                    @endif
                                                                        {!! $taxData->name !!}
                                                                    </td>
                                                                    <td colspan="4">
                                                                        <input type="text" class="form-control percentage" name="taxes[{!! $taxData->id !!}]" id="percentage_{!! $taxData->id !!}" value="{!! $taxData->base_percentage !!}" onkeyup="calculateTaxAmount(this)">
                                                                    </td>
                                                                    <td colspan="2">
                                                                        <label class="control-label tax-amount" id="tax_current_bill_amount_{{$taxData['id']}}"></label>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        @if(count($specialTaxes) > 0)
                                                            <tr>
                                                                @if($subcontractorStructure->contractType->slug == 'itemwise' || $subcontractorStructure->contractType->slug == 'amountwise')
                                                                    <td colspan="12">
                                                                @else
                                                                    <td colspan="11">
                                                                        @endif
                                                                        <label class="control-label"> <b>Special Taxes</b></label>
                                                                    </td>
                                                            </tr>
                                                            @foreach($specialTaxes as $specialTax)
                                                                <tr>
                                                                    <td colspan="6" style="text-align: right; padding-right: 30px;"><b>{{$specialTax['name']}}</b><input type="hidden" class="special-tax" name="special_tax[]" value="{{$specialTax['id']}}"> </td>
                                                                    <td colspan="2"><input class="form-control" name="applied_on[{{$specialTax['id']}}][percentage]" value="{{$specialTax['base_percentage']}}" id="tax_percentage_{{$specialTax['id']}}" onchange="calculateSpecialTax()()" onkeyup="calculateSpecialTax()()"> </td>
                                                                    <td colspan="2">
                                                                        <a class="btn green sbold uppercase btn-outline btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> Applied On
                                                                            <i class="fa fa-angle-down"></i>
                                                                        </a>
                                                                        <ul class="dropdown-menu" style="position: relative">
                                                                            {{--<li>
                                                                                <input type="checkbox" class="tax-applied-on special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="0"> Total Round
                                                                            </li>--}}
                                                                            @foreach($taxes as $tax)
                                                                                <li>
                                                                                    <input type="checkbox" class="tax-applied-on" id="special_tax_{{$specialTax['id']}}_on" name="applied_on[{{$specialTax['id']}}][on][]" value="{{$tax['id']}}" onclick="calculateSpecialTax()"> {{$tax['name']}}
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
                                                            @if($subcontractorStructure->contractType->slug == 'itemwise' || $subcontractorStructure->contractType->slug == 'amountwise')
                                                                <td colspan="10">
                                                            @else
                                                                <td colspan="9">
                                                            @endif
                                                                    <label class="control-label pull-right"> <b>Final Total</b></label>
                                                            </td>
                                                            <td colspan="2">
                                                                <label class="control-label" id="finalTotal"></label>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            @if($subcontractorStructure->contractType->slug == 'itemwise' || $subcontractorStructure->contractType->slug == 'amountwise')
                                                                <td colspan="10">
                                                            @else
                                                                <td colspan="9">
                                                            @endif
                                                                    <label class="control-label pull-right"> <b>Round off amount</b></label>
                                                            </td>
                                                            <td colspan="2">
                                                                <div class="form-group" style="margin: 1%">
                                                                    <input type="text" class="form-control" name="round_off_amount" id="roundOffAmount" value="0" onkeyup="calculateGrandTotal()">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            @if($subcontractorStructure->contractType->slug == 'itemwise' || $subcontractorStructure->contractType->slug == 'amountwise')
                                                                <td colspan="10">
                                                            @else
                                                                <td colspan="9">
                                                            @endif
                                                                    <label class="control-label pull-right"> <b>Grand Total</b></label>
                                                            </td>
                                                            <td colspan="2">
                                                                <label class="control-label" id="grandTotal" ></label>
                                                                <input type="hidden" name="grand_total">
                                                            </td>
                                                        </tr>
                                                        </tbody>
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
    <link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
    <script  src="/assets/global/plugins/datatables/datatables.min.js"></script>
    <script src="/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/custom/subcontractor/validations.js"></script>
    <script>
        subcontractorStructureSlug = $("#subcontractorStructureSlug").val();
        function structureSummarySelected(element){
            var structureSummaryId = $(element).val();
            if ($(element).prop("checked")){
                $(element).closest('tr').find('.description').attr('readonly', false);
                $(element).closest('tr').find('.description').attr('name', 'description['+structureSummaryId+']');
                $(element).closest('tr').find('.quantity').attr('readonly', false);
                $(element).closest('tr').find('.quantity').attr('name', 'quantity['+structureSummaryId+']');
                $(element).closest('tr').find('.quantity').rules('add', {
                    required: true,
                    min: 0.000001
                });
            } else {
                $(element).closest('tr').find('.description').attr('readonly', true);
                $(element).closest('tr').find('.description').removeAttr('name');
                $(element).closest('tr').find('.quantity').attr('readonly', true);
                $(element).closest('tr').find('.quantity').removeAttr('name');
                $(element).closest('tr').find('.quantity').rules('remove');
                $(element).closest('tr').find('.quantity').val(0);
                calculateAmount($(element).closest('tr').find('.quantity'));
            }
        }

        function calculateAmount(element){
            var quantity = parseFloat($(element).val());
            if(isNaN(quantity)){
                quantity = 0;
            }
            var rate = 0;
            if(subcontractorStructureSlug == 'amountwise'){
                rate = parseFloat($(element).closest('tr').find('.total_amount').text());
            }else{
                rate = parseFloat($(element).closest('tr').find('.rate').text());
            }
            var amount = (quantity * rate).toFixed(3);
            $(element).closest('tr').find('.bill-amount').text(amount);
            var cummulativeQty = (parseFloat( $(element).closest('tr').find('.prev-qty').val()) + quantity);
            $(element).closest('tr').find('.cummulative-qty').val(cummulativeQty);
            calculateSubtotal();
        }

        function calculateSubtotal() {
            var subtotal = 0;
            $(".bill-amount").each(function () {
                subtotal += parseFloat($(this).text());
            });
            $(".extra-item").each(function () {
                var extraItemAmount = parseFloat($(this).val());
                if (isNaN(extraItemAmount)) {
                    extraItemAmount = 0;
                }
                subtotal += extraItemAmount;
            });
            $("#subtotal").text(subtotal.toFixed(3));
            $("input[name='subtotal']").val(subtotal.toFixed(3));
            calculateDiscount();
        }

        function calculateDiscount(){
            var subtotal = parseFloat($("#subtotal").text());
            var discount = parseFloat($("#discount").val());
            if(isNaN(discount)){
                discount = 0;
            }
            if(isNaN(subtotal)){
                subtotal = 0;
            }
            var discountedTotal = parseFloat(subtotal - discount).toFixed(3);
            $("#discountedTotal").text(discountedTotal);
            calculateTaxAmount();
        }

        function calculateTaxAmount(){
            $(".percentage").each(function(){
                var percentage = parseFloat($(this).val());
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

        function calculateFinalTotal(){
            var finalTotal = parseFloat($('#discountedTotal').text());
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
            calculateGrandTotal();
        }

        function calculateGrandTotal(){
            var total = parseFloat($("#finalTotal").text());
            var roundOffAmount  = parseFloat($("#roundOffAmount").val());
            if(isNaN(total)){
                total = 0;
            }
            if(isNaN(roundOffAmount)){
                roundOffAmount = 0;
               // $("#roundOffAmount").val(0);
            }
            var grandTotal = parseFloat((total+roundOffAmount));
            $("#grandTotal").text(grandTotal);
            $("input[name='grand_total']").val(grandTotal);
        }

        function extraItemClick(element){
            var structureExtraItemId = $(element).val();
            if($(element).prop('checked')){
                $(element).closest('tr').find('.extra-item').attr('readonly', false);
                $(element).closest('tr').find('.extra-item').attr('name', 'structure_extra_item_rate['+structureExtraItemId+']');
                $(element).closest('tr').find('.extra-item-description').attr('readonly', false);
                $(element).closest('tr').find('.extra-item-description').attr('name', 'structure_extra_item_description['+structureExtraItemId+']');
            }else{
                $(element).closest('tr').find('.extra-item').attr('readonly', true);
                $(element).closest('tr').find('.extra-item').removeAttr('name');
                $(element).closest('tr').find('.extra-item').val(0);
                $(element).closest('tr').find('.extra-item-description').attr('readonly', true);
                $(element).closest('tr').find('.extra-item-description').removeAttr('name');
                $(element).closest('tr').find('.extra-item-description').val('');
                calculateSubtotal();
            }
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
                        var taxPercentage = $("#tax_percentage_"+specialTaxId).val();
                        taxAmount += parseFloat((taxOnAmount * (taxPercentage / 100)).toFixed(3));
                    });
                    $("#tax_current_bill_amount_"+specialTaxId).text(parseFloat(taxAmount).toFixed(3));
                });
            }
            calculateFinalTotal();
        }

        $(document).ready(function(){
            CreateSubcontractorBills.init();
        });
    </script>
@endsection
