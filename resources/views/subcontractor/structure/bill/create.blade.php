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
                                        <a href="/subcontractor/subcontractor-bills/manage/{!! $subcontractorStructure['id'] !!}">Manage Subcontractor Bills</a>
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
                                                <form role="form" id="create_bill" class="form-horizontal" action="/subcontractor/subcontractor-bills/create/{!! $subcontractorStructure['id'] !!}" method="post">
                                                    {!! csrf_field() !!}
                                                    <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="parentBillTable">
                                                        <thead>
                                                        <tr id="tableHeader">
                                                            <th width="10%" style="text-align: center"><b> Bill No  </b></th>
                                                            <th width="30%" style="text-align: center"><b> Description </b></th>
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
                                                                <input type="text" class="form-control description" name="description" id="description">
                                                                {{--{!! $subcontractorBill['description'] !!}--}}
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" name="qty" id="quantity" onkeyup="calculateSubTotal(this)">
                                                                {{--{!! $subcontractorBill['qty'] !!}--}}
                                                            </td>
                                                            <td>
                                                                <span id="rate">{!! $subcontractorStructure['rate'] !!}</span>
                                                            </td>
                                                            <td>
                                                                <span id="subtotal"></span>
                                                            </td>
                                                        </tr>
                                                        @if(count($taxes) > 0)
                                                            <tr>
                                                                <td colspan="2">
                                                                    <b>Tax Name</b>
                                                                </td>
                                                                <td colspan="2">
                                                                    <b>Tax Rate (%)</b>
                                                                </td>
                                                                <td colspan="1">

                                                                </td>
                                                            </tr>
                                                            @foreach($taxes as $key => $taxData)
                                                                <tr>
                                                                    <td colspan="2">
                                                                        {!! $taxData->name !!}
                                                                    </td>
                                                                    <td colspan="2">
                                                                        <input type="text" class="form-control percentage" name="taxes[{!! $taxData->id !!}]" id="percentage_{!! $taxData->id !!}" value="{!! $taxData->base_percentage !!}" onkeyup="calculateTaxAmount(this)">
                                                                    </td>
                                                                    <td colspan="1">
                                                                        <span class="tax_amount" id="tax_amount_{!! $taxData->id !!}"></span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        <tr>
                                                            <td colspan="4">
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
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script><script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script>
        function calculateSubTotal(element){
            var quantity = parseFloat($(element).val());
            var rate = parseFloat($('#rate').text());
            var subTotal = quantity * rate;
            $('#subtotal').text(subTotal);
            $('.percentage').each(function(){
                calculateTaxAmount($(this));
            });
        }


        function calculateTaxAmount(element){
            var percentage = $(element).val();
            var taxId = $(element).attr('id').match(/\d+/)[0];
            var subtotal = $('#subtotal').text();
            var tax_amount = (percentage * subtotal) / 100;
            $('#tax_amount_'+taxId).text(tax_amount);
            calulateFinalTotal();
        }

        function calulateFinalTotal(){
            var finalTotal = parseFloat($('#subtotal').text());
            $('.tax_amount').each(function(){
                var taxAmount = parseFloat($(this).text());
                finalTotal += taxAmount;
            });
            $('#finalTotal').text(finalTotal);
        }
    </script>
@endsection
