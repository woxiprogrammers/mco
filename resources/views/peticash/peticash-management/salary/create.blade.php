<?php
    /**
     * Created by Harsha.
     * User: harsha
     * Date: 24/4/18
     * Time: 2:27 PM
     */?>
@extends('layout.master')
@section('title','Constro | Create Salary')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
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
                                    <h1>Create Salary</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/client/manage">Manage Salary</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Salary</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">

                                        <div class="portlet-body form">
                                            <input type="hidden" id="approved_amount">
                                            <form role="form" id="create-salary" class="form-horizontal" method="post" action="/peticash/peticash-management/salary/create">
                                                {!! csrf_field() !!}

                                                <div class="form-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="transaction_type" class="control-label">Transaction Type</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="transaction_type" name="transaction_type">
                                                                <option value="">Select Transaction Type</option>
                                                                @foreach($transactionTypes as $transactionType)
                                                                    <option value="{{$transactionType['slug']}}">{{$transactionType['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="company" class="control-label">To be paid from</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="paid_from" name="paid_from">
                                                                <option value="peticash">Peticash</option>
                                                                <option value="bank">Bank</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" id="paymentSelect" hidden>
                                                        <div class="form-group row" id="bankSelect">
                                                            <div class="col-md-3">
                                                                <label class="pull-right control-label">
                                                                    Bank:
                                                                </label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" id="bank_id" name="bank_id" onchange="setBalanceAmount()">
                                                                    <option value="">Select Bank</option>
                                                                    @foreach($banks as $bank)
                                                                        <option value="{{$bank['id']}}">{{$bank['bank_name']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row" hidden>
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="employee_name" class="control-label">Bank Balance Amount</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="bank_balance_amount" readonly>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" id="allowedAmount">
                                                        @foreach($banks as $bank)
                                                            <input type="hidden" id="balance_amount_{{$bank['id']}}" value="{{$bank['balance_amount']}}">
                                                        @endforeach
                                                        <div class="col-md-3">
                                                            <label class="pull-right control-label">
                                                                Payment Mode:
                                                            </label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" name="payment_id" >
                                                                @foreach($paymentTypes as $paymentType)
                                                                    <option value="{{$paymentType['id']}}">{{$paymentType['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="name" class="control-label">Date:</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6 date date-picker">
                                                            <input type="text" style="width: 30%" name="date"  id="date"/>
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="employee_name" class="control-label">Employee Name</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="employee_name" name="employee_name">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="employee_name" class="control-label">Advance Balance Amount</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="advance_balance_amount" readonly>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" id="employee_id" name="employee_id">
                                                    <input type="hidden" id="balance">
                                                    <div class="form-group row" id="perDayWagesDiv">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="employee_name" class="control-label">Per Day Wages</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control calculate-payable-amount" id="per_day_wages" name="per_day_wages" readonly onchange="calculatePayableAmount()">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" id="workingDaysDiv">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="working_days" class="control-label">Working Days</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control calculate-payable-amount" id="working_days" name="working_days" onkeyup="calculateAmount()">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="amount" class="control-label">Amount</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control calculate-payable-amount" id="amount" name="amount">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="amount" class="control-label">Remark</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="remark" name="remark">
                                                        </div>
                                                    </div>
                                                    <div id="salaryExtraFields" hidden>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="email" class="control-label">PT</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control calculate-payable-amount" id="pt" name="pt" value="0" onchange="calculatePayableAmount()">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="pf" class="control-label">PF</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control calculate-payable-amount" id="pf" name="pf" value="0" onchange="calculatePayableAmount()">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="tds" class="control-label">TDS</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control calculate-payable-amount" id="tds" name="tds" value="0" onchange="calculatePayableAmount()">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="esic" class="control-label">ESIC</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control calculate-payable-amount" id="esic" name="esic" value="0" onchange="calculatePayableAmount()">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="amount" class="control-label">Payable Amount</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="payable_amount" name="payable_amount" readonly>
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>
                                                <div class="form-actions noborder row">
                                                    <div class="col-md-offset-3" style="margin-left: 26%">
                                                        <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit</button>
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
    <script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
    <script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
    <script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
    <script src="/assets/custom/peticash/salary-validation.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            CreateSalary.init();
            $('#submit').css("padding-left",'6px');

            $('#date').attr("readonly", "readonly");
            var date = new Date();
            $('#date').val((date.getMonth()+1)+"/"+date.getDate()+"/"+date.getFullYear());

            $('#paid_from').on('change',function(){
                if($(this).val() == 'peticash'){
                    $('#paymentSelect').hide();
                }else{
                    $('#paymentSelect').show();
                }
            });
            $('#transaction_type').on('change',function(){
                var transactionType = $(this).val();
                if(transactionType == 'salary'){
                    $('#perDayWagesDiv').show();
                    $('#workingDaysDiv').show();
                    $('#amount').prop('readonly',true);
                }else{
                    $('#perDayWagesDiv').hide();
                    $('#workingDaysDiv').hide();
                    $('#amount').prop('readonly',false);
                }
                if(typeof $(this).val() != 'undefined' && $(this).val() != '' && $(this).val() != null){
                    $('#employee_name').removeClass('typeahead');
                    $('#employee_name').typeahead('destroy');
                    $('#employee_name').addClass('typeahead');
                    var citiList = new Bloodhound({
                        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        remote: {
                            url: "/peticash/peticash-management/salary/auto-suggest/"+transactionType+"/%QUERY",
                            filter: function(x) {
                                if($(window).width()<420){
                                    $("#header").addClass("fixed");
                                }
                                return $.map(x, function (data) {
                                    return {
                                        name:data.employee_name,
                                        employee_id:data.employee_id,
                                        per_day_wages:data.per_day_wages,
                                        balance:data.balance,
                                        advance_balance:data.advance_after_last_salary,
                                        approved_amount : data.approved_amount
                                    };
                                });
                            },
                            wildcard: "%QUERY"
                        }
                    });
                    citiList.initialize();
                    $('.typeahead').typeahead(null, {
                        displayKey: 'name',
                        engine: Handlebars,
                        source: citiList.ttAdapter(),
                        limit: 30,
                        templates: {
                            empty: [
                                '<div class="empty-suggest">',
                                'Unable to find any Result that match the current query',
                                '</div>'
                            ].join('\n'),
                            suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{name}}</strong></div>')
                        }
                    }).on('typeahead:selected', function (obj, datum) {
                        var POData = $.parseJSON(JSON.stringify(datum));
                        POData.name = POData.name.replace(/\&/g,'%26');
                        $("#name").val(POData.name);
                        $("#per_day_wages").val(POData.per_day_wages);
                        $("#balance").val(POData.balance);
                        $("#employee_id").val(POData.employee_id);
                        $("#advance_balance_amount").val(POData.advance_balance);
                        $("#approved_amount").val(POData.approved_amount);
                        if(transactionType == 'salary'){
                            $('#salaryExtraFields').show();
                        }else{
                            $('#salaryExtraFields').hide();
                        }
                    })
                        .on('typeahead:open', function (obj, datum) {

                        });
                }else{
                    $('#employee_name').removeClass('typeahead');
                    $('#employee_name').typeahead('destroy');
                }
                calculateAmount();
            });

        });

        function setBalanceAmount(){
            var selectedBankId = $('#bank_id').val();
            if(selectedBankId == ''){
                alert('Please select Bank');
                $('#bank_balance_amount').val(0);
            }else{
                var allowedBankAmount = parseFloat($('#balance_amount_'+selectedBankId).val());
                $('#bank_balance_amount').val(allowedBankAmount);
            }
            calculatePayableAmount();
        }

        function calculateAmount(){
            var perDayWages = parseFloat($('#per_day_wages').val());
            if(typeof perDayWages == '' || perDayWages == 'undefined' || isNaN(perDayWages)){
                perDayWages = 0;
            }
            var workingDays = parseFloat($('#working_days').val());
            if(typeof workingDays == '' || workingDays == 'undefined' || isNaN(workingDays)){
                workingDays = 0;
            }
            var amount = workingDays * perDayWages;
            $('#amount').val(amount);
            calculatePayableAmount();
        }

        function calculatePayableAmount(){
            if( $('#transaction_type').val() == 'salary'){
                var perDayWages = parseFloat($('#per_day_wages').val());
                if(typeof perDayWages == '' || perDayWages == 'undefined' || isNaN(perDayWages)){
                    perDayWages = 0;
                }
                var workingDays = parseFloat($('#working_days').val());
                if(typeof workingDays == '' || workingDays == 'undefined' || isNaN(workingDays)){
                    workingDays = 0;
                }
                var balance = parseFloat($('#balance').val());
                if(typeof balance == '' || balance == 'undefined' || isNaN(balance)){
                    balance = 0;
                }
                var pt = parseFloat($('#pt').val());
                if(typeof pt == '' || pt == 'undefined' || isNaN(pt)){
                    pt = 0;
                }
                var pf = parseFloat($('#pf').val());
                if(typeof pf == '' || pf == 'undefined' || isNaN(pf)){
                    pf = 0;
                }
                var tds = parseFloat($('#tds').val());
                if(typeof tds == '' || tds == 'undefined' || isNaN(tds)){
                    tds = 0;
                }
                var esic = parseFloat($('#esic').val());
                if(typeof esic == '' || esic == 'undefined' || isNaN(esic)){
                    esic = 0;
                }
                var payableAmount = (perDayWages * workingDays) + balance - (pt + pf + tds + esic);
                if(payableAmount < 0){
                    $('#payable_amount').val(0);
                }else{
                    $('#payable_amount').val(payableAmount);

                }
                applyValidation($('#payable_amount'));
            }else{
                applyValidation($('#amount'));
            }

            function applyValidation(element){
                var approved_amount = parseFloat($('#approved_amount').val());
                if(approved_amount == null || typeof approved_amount == 'undefined' || isNaN(approved_amount)){
                    approved_amount = 0;
                }
                if($('#paid_from').val() == 'bank'){
                    var selectedBankId = $('#bank_id').val();
                    if(selectedBankId == ''){
                        alert('Please select Bank');
                    }else{
                        var allowedBankAmount = parseFloat($('#balance_amount_'+selectedBankId).val());
                        $(element).rules('add',{
                            max: allowedBankAmount
                        });
                    }
                }else{
                    $(element).rules('add',{
                        max: approved_amount
                    });
                }
            }



        }
    </script>
@endsection

