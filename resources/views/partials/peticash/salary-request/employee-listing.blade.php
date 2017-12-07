<?php
/**
 * Created by Ameya Joshi.
 * Date: 4/12/17
 * Time: 3:33 PM
 */
?>
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
                        <select class="form-control employee-payment-type" name="employee[{{$employee['id']}}][payment_type]" disabled>
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
                        <input type="text" class="form-control employee-days" value="{{$employee['days']}}" name="employee[{{$employee['id']}}][days]" readonly style="width: 90%">
                    </td>
                    <td style="text-align: center">
                        <input type="text" class="form-control employee-amount" value="{{$employee['amount']}}" name="employee[{{$employee['id']}}][amount]" readonly style="width: 90%">
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

    <script>
        $(document).ready(function(){
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
                    var amount = days*wages;
                }else{
                    var amount = 0;
                }
                $(this).closest('tr').find('.employee-amount').val(amount);
            });
        });
    </script>
