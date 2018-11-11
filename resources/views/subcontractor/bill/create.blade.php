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
                                                <form role="form" id="create_bill" class="form-horizontal" action="/subcontractor/bill/create/{!! $subcontractorStructure['id'] !!}" method="post">
                                                    {!! csrf_field() !!}
                                                    <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="parentBillTable">
                                                        <thead>
                                                        <tr id="tableHeader">
                                                            <th width="10%" style="text-align: center"><b> Bill No  </b></th>
                                                            @if($subcontractorStructure->contractType->slug == 'itemwise')
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
                                                                        <div class="form-group" > <input type="checkbox" class="checkbox-inline"></div>
                                                                    </td>
                                                                @endif
                                                                <td>
                                                                    <label class="control-label"> {{$structureSummary['summary_name']}}</label>
                                                                </td>
                                                                    <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><textarea class="form-control description" name="description[{{$structureSummary['id']}}]"></textarea></div></td>
                                                                <td >{{$structureSummary['total_work_area']}}</td>
                                                                <td >{{$structureSummary['rate']}}</td>
                                                                <td >{!! $structureSummary['total_work_area'] * $structureSummary['rate'] !!}</td>
                                                                <td >{{$structureSummary['prev_quantity']}}</td>
                                                                    <td ><div class="form-group" style="margin-left: 1%; margin-right: 1%"><input type="text" class="form-control quantity" max="{{$structureSummary['allowed_quantity']}}" name="quantity[{{$structureSummary['id']}}]" required> </div></td>
                                                                <td >{{$structureSummary['prev_quantity']}}</td>
                                                                <td > <label class="control-label bill-amount"> 0 </label> </td>
                                                            </tr>
                                                        @endforeach
                                                        @if(count($taxes) > 0)
                                                            <tr>
                                                                @if($subcontractorStructure->contractType->slug == 'itemwise')
                                                                    <td colspan="5">
                                                                @else
                                                                    <td colspan="4">
                                                                @endif
                                                                    <b>Tax Name</b>
                                                                </td>
                                                                <td colspan="5">
                                                                    <b>Tax Rate (%)</b>
                                                                </td>
                                                                <td colspan="1">

                                                                </td>
                                                            </tr>
                                                            @foreach($taxes as $key => $taxData)
                                                                <tr>
                                                                    @if($subcontractorStructure->contractType->slug == 'itemwise')
                                                                        <td colspan="5">
                                                                    @else
                                                                        <td colspan="4">
                                                                    @endif
                                                                        {!! $taxData->name !!}
                                                                    </td>
                                                                    <td colspan="5">
                                                                        <input type="text" class="form-control percentage" name="taxes[{!! $taxData->id !!}]" id="percentage_{!! $taxData->id !!}" value="{!! $taxData->base_percentage !!}" onkeyup="calculateTaxAmount(this)">
                                                                    </td>
                                                                    <td colspan="1">
                                                                        <span class="tax_amount" id="tax_amount_{!! $taxData->id !!}"></span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        <tr>
                                                            @if($subcontractorStructure->contractType->slug == 'itemwise')
                                                                <td colspan="10">
                                                            @else
                                                                <td colspan="9">
                                                            @endif
                                                                <b>Final Total</b>
                                                            </td>
                                                            <td colspan="1">
                                                                <span id="finalTotal"></span>
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
        $(document).ready(function(){
            CreateSubcontractorBill.init();
            var subcontractorStructureSlug = $("#subcontractorStructureSlug").val();
            if(subcontractorStructureSlug == 'amountwise'){
                $("#number_of_floors").rules('add',{
                    required: true
                });
            }
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
        function calculateSubTotal(element){
            var quantity = parseFloat($(element).val());
            var rate = parseFloat($('#rate').text());
            var subTotal = (quantity * rate);
            if(isNaN(subTotal)){
                $("#subtotal").text(0);
            }else{
                $('#subtotal').text(subTotal.toFixed(3));
            }
            $('.percentage').each(function(){
                calculateTaxAmount($(this));
            });
        }


        function calculateTaxAmount(element){
            var percentage = $(element).val();
            var taxId = $(element).attr('id').match(/\d+/)[0];
            var subtotal = $('#subtotal').text();
            var tax_amount = (percentage * subtotal) / 100;
            if(isNaN(tax_amount)){
                $('#tax_amount_'+taxId).text(0);
            }else{
                $('#tax_amount_'+taxId).text(tax_amount.toFixed(3));
            }
            calulateFinalTotal();
        }

        function calulateFinalTotal(){
            var finalTotal = parseFloat($('#subtotal').text());
            $('.tax_amount').each(function(){
                var taxAmount = parseFloat($(this).text());
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
