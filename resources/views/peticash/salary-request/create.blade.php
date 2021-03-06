@extends('layout.master')
@section('title','Constro | Create Salary Request')
@include('partials.common.navbar')
@section('css')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
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
                                    <h1>Create Salary Request</h1>
                                </div>
                            </div>
                        </div>
                        <div class="page-content">
                            @include('partials.common.messages')
                            <div class="container">
                                <ul class="page-breadcrumb breadcrumb">
                                    <li>
                                        <a href="/client/manage">Manage Salary Request</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">Create Salary Request</a>
                                        <i class="fa fa-circle"></i>
                                    </li>
                                </ul>
                                <div class="col-md-12">
                                    <!-- BEGIN VALIDATION STATES-->
                                    <div class="portlet light ">

                                        <div class="portlet-body form">
                                            <form role="form" id="create-client" class="form-horizontal" method="post" action="/peticash/salary-request/create">
                                                {!! csrf_field() !!}
                                                <table class="table table-striped table-bordered table-hover" id="employeeTable"  style="table-layout: fixed">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 5%"></th>
                                                        <th>Id</th>
                                                        <th>Name</th>
                                                        <th>Type</th>
                                                        <th>Payment Type</th>
                                                        <th>Per day wages</th>
                                                        <th>Days</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(count($employees) > 0)
                                                        @foreach($employees as $employee)
                                                            <tr>
                                                                <td style="text-align: center">
                                                                    <input type="checkbox" class="employee-id" value="{{$employee['id']}}" name="employee_ids[]">
                                                                </td>
                                                                <td style="text-align: center">
                                                                    <input type="text" class="form-control employee-e-id" value="{{$employee['employee_id']}}" name="employee[{{$employee['id']}}][employee_id]" readonly style="width: 90%">
                                                                </td>
                                                                <td style="text-align: center">
                                                                    <input type="text" class="form-control employee-name" value="{{$employee['name']}}" name="employee[{{$employee['id']}}][name]" readonly style="width: 90%">
                                                                </td>
                                                                <td style="text-align: center">
                                                                    <input type="hidden" value="{{$employee->employeeType->id}}" name="employee[{{$employee['id']}}][type_id]">
                                                                    <input type="text" class="form-control employee-type" value="{{$employee->employeeType->name}}" name="employee[{{$employee['id']}}][type]" readonly style="width: 90%">
                                                                </td>
                                                                <td style="text-align: center">
                                                                    <select class="form-control employee-payment-type" name="employee[{{$employee['id']}}][payment_type]" disabled required="required">
                                                                        <option value="">--Select Payment Type--</option>
                                                                        @foreach($transactionTypes as $transactionType)
                                                                            <option value="{{$transactionType['id']}}">{{$transactionType['name']}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td style="text-align: center">
                                                                    <input type="text" class="form-control employee-wages" value="{{$employee['per_day_wages']}}" name="employee[{{$employee['id']}}][per_day_wages]" readonly style="width: 90%">
                                                                </td>
                                                                <td style="text-align: center">
                                                                    <input type="text" class="form-control employee-days" value="{{$employee['days']}}" name="employee[{{$employee['id']}}][days]" required="required" readonly style="width: 90%">
                                                                </td>
                                                                <td style="text-align: center">
                                                                    <input type="text" class="form-control employee-amount" value="{{$employee['amount']}}" name="employee[{{$employee['id']}}][amount]" required="required" readonly style="width: 90%">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="8">
                                                                No record found
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    </tbody>
                                                </table>
                                                @if(count($employees) > 0)
                                                    <div class="form-actions noborder row" id="submitDiv">
                                                        <div class="col-md-offset-3" style="margin-left: 26%">
                                                            <button type="submit" class="btn red" id="submit"><i class="fa fa-check"></i> Submit</button>
                                                        </div>
                                                    </div>
                                                @endif
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
<script>
        $(document).ready(function(){

            $("#submit").click(function(e){
                if($('input[type=checkbox]:checked').length == 0)
                {
                    alert('Please select atleast one checkbox');
                    e.preventDefault(this);
                }
            });

            $(".employee-id").on('click',function(){
                if ($(this).prop("checked") == true) {
                    $(this).closest('tr').find('.employee-payment-type').prop('disabled', false);
                }else{
                    $(this).closest('tr').find('.employee-payment-type').find('option[value=""]').prop('selected', true);
                    $(this).closest('tr').find('.employee-payment-type').prop('disabled', true);
                    $(this).closest('tr').find('.employee-days').prop('readonly', true);
                    $(this).closest('tr').find('.employee-amount').prop('readonly', true);
                }

            });
            $(".employee-payment-type").change(function(){
                var paymentType = $(this).find('option:selected').text();
                if(paymentType.toLowerCase() == 'salary'.toLowerCase()){
                    $(this).closest('tr').find('.employee-days').prop('readonly', false);
                    $(this).closest('tr').find('.employee-amount').prop('readonly', true);
                }else if(paymentType.toLowerCase() == 'advance'.toLowerCase()){
                    $(this).closest('tr').find('.employee-days').prop('readonly', true);
                    $(this).closest('tr').find('.employee-amount').prop('readonly', false);
                }else{
                    $(this).closest('tr').find('.employee-days').prop('readonly', true);
                    $(this).closest('tr').find('.employee-amount').prop('readonly', true);
                }
            });
            $(".employee-days").on('keyup', function(){
                var days = parseFloat($(this).val());
                var wages = parseFloat($(this).closest('tr').find('.employee-wages').val());
                if($.isNumeric(days)){
                    var amount = days * wages;
                }else{
                    var amount = 0;
                }
                $(this).closest('tr').find('.employee-amount').val(amount.toFixed(2));
            });
        });
    </script>
@endsection
