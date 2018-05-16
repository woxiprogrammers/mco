@extends('layout.master')
@section('title','Constro | Add Amount to Master Peticash Account')
@include('partials.common.navbar')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link rel="stylesheet"  href="/assets/global/plugins/datatables/datatables.min.css"/>
<link href="/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
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
                                <h1>Add Amount to Master Peticash Account</h1>
                            </div>
                        </div>
                    </div>
                    <div class="page-content">
                        @include('partials.common.messages')
                        <div class="container">
                            <ul class="page-breadcrumb breadcrumb">
                                <li>
                                    <a href="/peticash/master-peticash-account/manage">Manage Master Peticash Account</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Add Amount to Master Peticash Account</a>
                                </li>
                            </ul>
                            <div class="col-md-12">
                                <!-- BEGIN VALIDATION STATES-->
                                <div class="portlet light ">
                                    <div class="portlet-body form">
                                        <form role="form" id="create-master-account" class="form-horizontal" method="post" action="/peticash/master-peticash-account/create">
                                            {!! csrf_field() !!}
                                            <div class="form-body">
                                                <fieldset>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="type" class="control-label">Assign To</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="to_userid" name="to_userid">
                                                                @foreach($users as $user)
                                                                <option value="{{$user['id']}}">{{$user['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">
                                                            <label for="type" class="control-label">Paid From</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-control" id="paid_from_slug" name="paid_from_slug" onchange="changePaidFrom()">
                                                                    <option value="bank">Bank</option>
                                                                    <option value="cash">Cash</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div id="bankData">
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="type" class="control-label">Bank</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" id="bank_id" name="bank_id" onchange="checkAmount()">
                                                                    <option value="">--- Select Bank ---</option>
                                                                    @foreach($banks as $bank)
                                                                        <option value="{{$bank['id']}}">{{$bank['bank_name']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        @foreach($banks as $bank)
                                                            <input type="hidden" id="balance_amount_{{$bank['id']}}" value="{{$bank['balance_amount']}}">
                                                        @endforeach
                                                        <div class="form-group row">
                                                            <div class="col-md-3" style="text-align: right">
                                                                <label for="type" class="control-label">Payment Type</label>
                                                                <span>*</span>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" id="payment_type" name="payment_type">
                                                                    @foreach($paymenttypes as $type)
                                                                        <option value="{{$type['id']}}">{{$type['name']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">

                                                            <label for="name" class="control-label">Amount</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="amount" name="amount" required="required" onkeyup="checkAmount()">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">

                                                            <label for="name" class="control-label">Remark</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" id="remark" name="remark" required="required">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-3" style="text-align: right">

                                                            <label for="name" class="control-label">Transaction Date</label>
                                                            <span>*</span>
                                                        </div>
                                                        <div class="col-md-3 date date-picker" data-date-end-date="0d">
                                                            <input type="text" name="date" id="date"/>
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-md-offset-8">
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
<script src="/assets/custom/peticash/peticash.js" type="text/javascript"></script>
<script src="/assets/global/plugins/typeahead/typeahead.bundle.min.js"></script>
<script src="/assets/global/plugins/typeahead/handlebars.min.js"></script>
<script src="/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script><script src="/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        var date=new Date();
        $('#date').val((date.getMonth()+1)+"/"+date.getDate()+"/"+date.getFullYear());
        AddAmtToAccount.init();
    });

    function changePaidFrom(){
        var paidFromSlug = $('#paid_from_slug').val();
        if(paidFromSlug == 'cash'){
            $('#bankData').hide();
        }else{
            $('#bankData').show();
        }
    }

    function checkAmount(){
        var paidFromSlug = $('#paid_from_slug').val();
        if(paidFromSlug == 'bank'){
            var selectedBankId = $('#bank_id').val();
            if(selectedBankId == ''){
                alert('Please select Bank');
            }else{
                var amount = parseFloat($('#amount').val());
                if(typeof amount == '' || amount == 'undefined' || isNaN(amount)){
                    amount = 0;
                }
                var allowedAmount = parseFloat($('#balance_amount_'+selectedBankId).val());
                $("input[name='amount']").rules('add',{
                    max: allowedAmount
                });
            }
        }
    }
</script>
@endsection
