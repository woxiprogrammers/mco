@extends('layout.master')
@section('title','Constro | Edit Subcontractor Structure Bill')
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
                                    <h1>Edit Subcontractor Structure Bill</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container" style="width: 100%">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/subcontractor/subcontractor-bills/manage/{!! $subcontractorStructure['id'] !!}">Manage Subcontractor Bills</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Edit Subcontractor Structure Bill</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <div class="form-body">
                                                <form role="form" id="edit_bill" class="form-horizontal" action="/subcontractor/subcontractor-bills/edit/{!! $subcontractorBill['id'] !!}" method="post">
                                                    {!! csrf_field() !!}
                                                    <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="parentBillTable">
                                                        <thead>
                                                        <tr id="tableHeader">
                                                            <th width="10%" style="text-align: center"><b> Bill No  </b></th>
                                                            <th width="30%" style="text-align: center"><b> Description </b></th>
                                                            @if($subcontractorStructure->contractType->slug == 'amountwise')
                                                                <th width="15%" class="numeric" style="text-align: center"><b> Number of floors </b></th>
                                                            @endif
                                                            <th width="15%" class="numeric" style="text-align: center"><b> Quantity </b></th>
                                                            <th width="15%" class="numeric" style="text-align: center"><b> Rate </b></th>
                                                            <th width="15%" class="numeric" style="text-align: center"><b> Amount </b></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>
                                                                {!! $billName !!}
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name="description" id="description" value="{!! $subcontractorBill['description'] !!}">
                                                            </td>
                                                            @if($subcontractorStructure->contractType->slug == 'amountwise')
                                                                <td>
                                                                    <div class="form-group" style="width: 90% !important;margin-left: 5%">
                                                                        <input type="text" class="form-control" name="number_of_floors" id="number_of_floors" value="{{$subcontractorBill['number_of_floors']}}">
                                                                    </div>
                                                                </td>
                                                            @endif
                                                            <td>
                                                                {!! $subcontractorBill['qty'] !!}
                                                            </td>
                                                            <td>
                                                                {!! $rate !!}
                                                            </td>
                                                            <td>
                                                                <span id="subtotal">{!! $subTotal!!}</span>
                                                            </td>
                                                        </tr>
                                                        @if(count($subcontractorBillTaxes) > 0)
                                                            <tr>
                                                                @if($subcontractorStructure->contractType->slug == 'amountwise')
                                                                    <td colspan="3">
                                                                @else
                                                                    <td colspan="2">
                                                                @endif
                                                                    <b>Tax Name</b>
                                                                </td>
                                                                <td colspan="2">
                                                                    <b>Tax Rate (%)</b>
                                                                </td>
                                                                <td colspan="1">

                                                                </td>
                                                            </tr>
                                                            @foreach($subcontractorBillTaxes as $key => $billTaxData)
                                                                <tr>
                                                                    @if($subcontractorStructure->contractType->slug == 'amountwise')
                                                                        <td colspan="3">
                                                                    @else
                                                                        <td colspan="2">
                                                                    @endif
                                                                        {!! $billTaxData->taxes->name !!}
                                                                    </td>
                                                                    <td colspan="2">
                                                                        <input type="text" class="form-control percentage" name="taxes[{!! $billTaxData->id !!}]" id="percentage_{!! $billTaxData->id !!}" value="{!! $billTaxData->percentage !!}" onkeyup="calculateTaxAmount(this)">
                                                                    </td>
                                                                    <td colspan="1">
                                                                        <span class="tax_amount" id="tax_amount_{!! $billTaxData->id !!}">{!! round((($billTaxData->percentage * $subTotal) / 100),3) !!}</span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        <tr>
                                                            @if($subcontractorStructure->contractType->slug == 'amountwise')
                                                                <td colspan="5">
                                                            @else
                                                                <td colspan="4">
                                                            @endif
                                                                <b>Final Total</b>
                                                            </td>
                                                            <td colspan="1">
                                                                <span id="finalTotal">{!! $finalTotal !!}</span>
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
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script><script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="/assets/custom/subcontractor/validations.js"></script>
    <script>
        $(document).ready(function(){
            EditSubcontractorBill.init();
            var subcontractorStructureSlug = $("#subcontractorStructureSlug").val();
            if(subcontractorStructureSlug == 'amountwise'){
                $("#number_of_floors").rules('add',{
                    required: true
                });
            }
        });
        function calculateTaxAmount(element){
            var percentage = parseFloat($(element).val());
            var taxId = $(element).attr('id').match(/\d+/)[0];
            var subtotal = parseFloat($('#subtotal').text());
            var tax_amount = parseFloat((percentage * subtotal) / 100).toFixed(3);
            $('#tax_amount_'+taxId).text(tax_amount);
            calulateFinalTotal();
        }

        function calulateFinalTotal(){
            var finalTotal = parseFloat($('#subtotal').text());
            $('.tax_amount').each(function(){
                var taxAmount = parseFloat($(this).text());
                finalTotal += taxAmount;
            });
            $('#finalTotal').text(parseFloat(finalTotal).toFixed(3));
        }
    </script>
@endsection
