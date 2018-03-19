@extends('layout.master')
@section('title','Constro | View Subcontractor Structure Bill')
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
                                    <h1>View Subcontractor Structure Bill</h1>
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
                                        <a href="javascript:void(0);">View Subcontractor Structure Bill</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">
                                        <div class="portlet-body form">
                                            <input type="hidden" id="subcontractorBillId" value="{!! $subcontractorBill['id'] !!}">
                                            @if($subcontractorBill->subcontractorBillStatus->slug == 'approved')
                                                <ul class="nav nav-tabs nav-tabs-lg">
                                                    <li class="active">
                                                        <a href="#billViewTab" data-toggle="tab"> Bill View </a>
                                                    </li>
                                                    <li>
                                                        <a href="#billTransactionTab" data-toggle="tab"> Transactions </a>
                                                    </li>
                                                    <li>
                                                        <a href="#reconcileTab" data-toggle="tab"> Reconcile </a>
                                                    </li>
                                                </ul>
                                            @endif
                                            <div class="tab-content">
                                                <div class="tab-pane fade in active" id="billViewTab">
                                                    <label class="control-label" for="date">Bill Date : {{date('m/d/Y',strtotime($subcontractorBill['created_at']))}}</label>
                                                    @if($subcontractorBill->subcontractorBillStatus->slug == 'draft')
                                                        <a href="/subcontractor/subcontractor-bills/edit/{{$subcontractorBill['id']}}" class="btn btn-xs blue" style="margin-left: 10px">
                                                            <i class="fa fa-edit"></i>
                                                            Bill
                                                        </a>
                                                        <a class="btn btn-xs green" id="approve" href="/subcontractor/subcontractor-bills/change-status/approved/{{$subcontractorBill['id']}}" style="margin-left: 10px">
                                                            <i class="fa fa-check-square-o"></i> Approve
                                                        </a>

                                                        <a href="/subcontractor/subcontractor-bills/change-status/disapproved/{{$subcontractorBill['id']}}" class="btn btn-xs btn-danger" id="disapprove">
                                                            <i class="fa fa-remove"></i> Disapprove
                                                        </a>
                                                    @endif

                                                    <div class="form-body">
                                                        <table class="table table-bordered table-striped table-condensed flip-content" style="width:100%;overflow: scroll; " id="parentBillTable">
                                                            <thead>
                                                            <tr id="tableHeader">
                                                                <th width="10%" style="text-align: center"><b> Bill No  </b></th>
                                                                <th width="30%" style="text-align: center"><b> Description </b></th>
                                                                @if($subcontractorStructure->contractType->slug == 'amountwise')
                                                                    <th width="15%" class="numeric" style="text-align: center"><b> Number of Floors </b></th>
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
                                                                    {!! $subcontractorBill['description'] !!}
                                                                </td>
                                                                @if($subcontractorStructure->contractType->slug == 'amountwise')
                                                                    <td>
                                                                        {!! $subcontractorBill['number_of_floors'] !!}
                                                                    </td>
                                                                @endif
                                                                <td>
                                                                    {!! $subcontractorBill['qty'] !!}
                                                                </td>
                                                                <td>
                                                                    {!! $rate !!}
                                                                </td>
                                                                <td>
                                                                    {!! $subTotal!!}
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
                                                                        <b>Tax Rate</b>
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
                                                                            {!! $billTaxData->percentage !!} %
                                                                        </td>
                                                                        <td colspan="1">
                                                                            {!! ($billTaxData->percentage * $subTotal) / 100 !!}
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
                                                                    {!! $finalTotal !!}
                                                                </td>
                                                            </tr>

                                                            </tbody>

                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade in" id="billTransactionTab">
                                                    <div class="tab-content">
                                                        <div class="tab-pane fade in active" id="billTransactionListingTab">
                                                            <div class="table-toolbar">
                                                                <div class="row" style="text-align: right">
                                                                    <div class="col-md-12">
                                                                        <div class="btn-group">
                                                                            <div id="sample_editable_1_new" class="btn yellow" >
                                                                                <a href="##billTransactionCreateModel" data-toggle="modal" style="color: white" id="billTransactionCreateButton"> Transaction
                                                                                    <i class="fa fa-plus"></i>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="transactionListingTable">
                                                                <thead>
                                                                <tr>
                                                                    <th style="width: 5%"> Sr. No. </th>
                                                                    <th> Subtotal </th>
                                                                    <th> Debit
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                    <th> Hold
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                    <th> Retention
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                    <th> TDS
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                    <th> Other Recovery
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                    <th> Total
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                    <th> Created At
                                                                        <input type="hidden" class="filter-submit">
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="modal fade" id="billTransactionCreateModel" role="dialog">
                                                            <div class="modal-dialog">
                                                                <!-- Modal content-->
                                                                <div class="modal-content">
                                                                    <div class="modal-header" style="padding:0px !important;">
                                                                        <div class="row">
                                                                            <div class="col-md-4"></div>
                                                                            <div class="col-md-4"> <h3><b>Transaction</b></h3> </div>
                                                                            <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <input type="hidden" id="balanceAdvanceAmount" value="{{$subcontractorBill->subcontractorStructure->subcontractor->balance_advance_amount}}">
                                                                        <form role="form" id="createTransactionForm" class="form-horizontal" method="post" action="/subcontractor/subcontractor-bills/transaction/create">
                                                                            {!! csrf_field() !!}
                                                                            <input type="hidden" value="{{$subcontractorBill['id']}}" name="subcontractor_bills_id">
                                                                            <input type="hidden" id="remainingTotal" name="remainingTotal" >
                                                                            <div class="form-body">
                                                                                <div class="form=group row">
                                                                                    <div class="col-md-8 col-md-offset-2">
                                                                                        <span style="font-size: 15px; font-weight: bold">
                                                                                            Total Advance Amount Paid : {{$subcontractorBill->subcontractorStructure->subcontractor->total_advance_amount}}
                                                                                        </span><br>
                                                                                        <span style="font-size: 15px; font-weight: bold">
                                                                                            Balance Advance Amount : {{$subcontractorBill->subcontractorStructure->subcontractor->balance_advance_amount}}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-4">

                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="checkbox" name="is_advance" id="isAdvanceCheckbox">
                                                                                        <label class="control-label" style="margin-left: 1%">
                                                                                            Is Advance Payment
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-3" style="text-align: right">
                                                                                        <label for="name" class="control-label"> Total </label>
                                                                                        <span>*</span>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="number" class="form-control" id="transactionTotal" name="total">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-3" style="text-align: right">
                                                                                        <label for="name" class="control-label"> Debit </label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="number" class="form-control" id="debit" name="debit">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-3" style="text-align: right">
                                                                                        <label for="name" class="control-label"> Hold </label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="number" class="form-control" id="hold" name="hold">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row form-group">
                                                                                    <div class="col-md-3">
                                                                                        <label class="control-label pull-right">Retention</label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="number" class="form-control tax_amount" id="retention_tax_amount" name="retention_tax_amount">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row form-group">
                                                                                    <div class="col-md-3">
                                                                                        <label class="control-label pull-right">TDS</label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="number" class="form-control tax_amount" id="tds_tax_amount" name="tds_tax_amount">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <div class="col-md-3" style="text-align: right">
                                                                                        <label for="name" class="control-label"> Other Recovery </label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <input type="number" class="form-control" id="other_recovery" name="other_recovery">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <div class="col-md-3" style="text-align: right">
                                                                                        <label for="name" class="control-label"> Remark </label>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <textarea class="form-control" name="remark" id="remark"></textarea>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-actions noborder row">
                                                                                    <button type="submit" class="btn red pull-right" id="transactionSubmit"> Submit</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade in" id="reconcileTab">
                                                    <ul class="nav nav-tabs nav-tabs-lg">
                                                        <li class="active">
                                                            <a href="#holdReconcileTab" data-toggle="tab"> Hold </a>
                                                        </li>
                                                        <li>
                                                            <a href="#retentionReconcileTab" data-toggle="tab"> Retention </a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content">
                                                        <div class="tab-pane fade in active" id="holdReconcileTab">
                                                            <div class="form-group row">
                                                                <div class="col-md-3">
                                                                    <label class="pull-right control-label">
                                                                        Reconcile Hold Amount :
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control" readonly value="{{$remainingHoldAmount}}">
                                                                </div>
                                                                @if($remainingHoldAmount < 0)
                                                                    <div class="col-md-6">
                                                                        <a class="btn yellow pull-right" href="javascript:void(0);" onclick="openReconcilePaymentModal('hold')">
                                                                            <i class="fa fa-plus"></i>Reconcile Hold
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="table-scrollable">
                                                                <table class="table table-striped table-bordered table-hover table-checkable order-column" id="holdReconcileTable">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width: 25%"> Date </th>
                                                                            <th style="width: 25%"> Amount </th>
                                                                            <th style="width: 25%"> Payment Method </th>
                                                                            <th style="width: 25%"> Reference Number </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade in" id="retentionReconcileTab">
                                                            <div class="form-group row">
                                                                <div class="col-md-3">
                                                                    <label class="pull-right control-label">
                                                                        Reconcile Retention Amount :
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control" readonly value="{{$remainingRetentionAmount}}">
                                                                </div>
                                                                @if($remainingRetentionAmount < 0)
                                                                    <div class="col-md-6">
                                                                        <a class="btn yellow pull-right" href="javascript:void(0);" onclick="openReconcilePaymentModal('retention')">
                                                                            <i class="fa fa-plus"></i>Reconcile Retention
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                <div class="table-scrollable">
                                                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="retentionReconcileTable">
                                                                        <thead>
                                                                        <tr>
                                                                            <th style="width: 25%"> Date </th>
                                                                            <th style="width: 25%"> Amount </th>
                                                                            <th style="width: 25%"> Payment Method </th>
                                                                            <th style="width: 25%"> Reference Number </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal fade " id="reconcilePaymentModal"  role="dialog">
                                                            <div class="modal-dialog">
                                                                <!-- Modal content-->
                                                                <div class="modal-content">
                                                                    <form id="add_payment_form" action="/subcontractor/subcontractor-bills/reconcile/add-transaction" method="post">
                                                                        {!! csrf_field() !!}
                                                                        <input type="hidden" name="subcontractor_bill_id" value="{{$subcontractorBill['id']}}">
                                                                        <input name="transaction_slug" id="reconcileTransactionSlug" type="hidden">
                                                                        <div class="modal-header">
                                                                            <div class="row">
                                                                                <div class="col-md-4"></div>
                                                                                <div class="col-md-4" style="font-size: 18px"> Payment</div>
                                                                                <div class="col-md-4"><button type="button" class="close" data-dismiss="modal">X</button></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-body" style="padding:40px 50px;">
                                                                            <div class="form-group row">
                                                                                <select class="form-control" name="payment_type_id">
                                                                                    @foreach($paymentTypes as $type)
                                                                                        <option value="{{$type['id']}}">{{$type['name']}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <input type="number" class="form-control" id="bilAmount" name="amount" placeholder="Enter Amount">
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <input type="text" class="form-control"  name="reference_number" placeholder="Enter Reference Number" >
                                                                            </div>
                                                                            <button class="btn btn-set red pull-right" type="submit">
                                                                                <i class="fa fa-check" style="font-size: large"></i>
                                                                                Add &nbsp; &nbsp; &nbsp;
                                                                            </button>
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
    <script src="/assets/custom/subcontractor/bill-transaction-manage-datatable.js" type="text/javascript"></script>
    <script src="/assets/custom/subcontractor/hold-reconcile-datatable.js" type="text/javascript"></script>
    <script src="/assets/custom/subcontractor/retention-reconcile-datatable.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $("#isAdvanceCheckbox").on('change', function(){
                if($(this).prop('checked') == true){
                    var balanceAdvanceAmount = $("#balanceAdvanceAmount").val();
                    console.log(balanceAdvanceAmount);
//                    $("#transactionTotal").rules('add',{
//                        max: balanceAdvanceAmount
//                    });
                }else{
//                    $("#transactionTotal").rules('remove','max');
                }
            });
            $("#transactionTotal").on('keyup', function(){
                console.log($(this).val());
            });
        });
        /*function calculateTaxAmount(element){
            var percentage = parseFloat($(element).val());
            var total = parseFloat($('#transactionTotal').val());
            if(isNaN(total)){
                total = 0;
            }
            if(isNaN(percentage)){
                percentage = 0;
            }
            var taxAmount = (percentage * total) / 100;
            $(element).closest('.form-group').find('.tax_amount').val(taxAmount);
        }
        function changeTaxAmounts(){
            $('.tax_percent').each(function(){
                calculateTaxAmount($(this));
            });
        }*/

        function openReconcilePaymentModal(transactionSlug){
            $("#reconcileTransactionSlug").val(transactionSlug);
            $("#reconcilePaymentModal").modal('show');
        }
    </script>
@endsection
