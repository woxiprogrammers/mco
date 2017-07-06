$(document).ready(function (){
    CreateBill.init();
    $("#change_bill").on('change', function(){
        var bill_id = $(this).val();
        window.location.href = "/bill/view/"+bill_id;
    });

    $('input[type="checkbox"]').click(function(){
        var id = $(this).val();
        var input = $('#current_quantity_'+id);
        var boq = $('#boq_quantity_'+id).text();
        var previous_quantity = $('#previous_quantity_'+id).text();
        var diff = parseFloat(boq - previous_quantity);
        if($(this).prop("checked") == false){
            $("#id_"+id).css('background-color',"");
            $('#current_quantity_'+id).prop('disabled',true);
            $('#product_description_'+id).prop('disabled',true);
            $('#current_quantity_'+id).rules('remove');
            $('#current_quantity_'+id).closest('form-group').removeClass('has-error');
            $('#current_quantity_'+id).val('');
            $('#cumulative_quantity_'+id).text("");
            $('#previous_bill_amount_'+id).text("");
            $('#current_bill_amount_'+id).text("");
            $('#cumulative_bill_amount_'+id).text("");
            getTotals();
        }else{
            if(diff == 0){
                $(this).attr('checked',false);
                $('#boq_quantity_'+id).css('background-color',"ff8884");
                $('#previous_quantity_'+id).css('color',"ce0e08");

            }else{
                $('#product_description_'+id).prop('disabled',false);
                $('#current_quantity_'+id).prop('disabled',false);
                $('#current_quantity_'+id).val(0);
                $("#id_"+id).css('background-color',"#e1e1e1");
                var typingTimer;
                var doneTypingInterval = 500;
                $('#current_quantity_'+id).rules('add',{
                    required: true,
                    min: 0.000001,
                    max: diff
                });
                input.on('keyup', function () {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(doneTyping, doneTypingInterval);
                });
                input.on('keydown', function () {
                    clearTimeout(typingTimer);
                });
                function doneTyping () {
                    calculateQuantityAmount(input.val(),id);
                }
                calculateQuantityAmount(input.val(),id);
            }
        }
    });
});

function calculateQuantityAmount(current_quantity,id){
    if(current_quantity == ""){
        current_quantity = 0;
    }
    var cumulative_quantity = parseFloat($('#previous_quantity_'+id).text()) + parseFloat(current_quantity);
    var prev_bill_amount = parseFloat($('#previous_quantity_'+id).text()) * parseFloat($('#rate_per_unit_'+id).text());
    var current_bill_amount = parseFloat(current_quantity) * parseFloat($('#rate_per_unit_'+id).text());
    var cumulative_bill_amount = prev_bill_amount + current_bill_amount;
    $('#cumulative_quantity_'+id).text(cumulative_quantity.toFixed(3));
    $('#previous_bill_amount_'+id).text(prev_bill_amount.toFixed(3));
    $('#current_bill_amount_'+id).text(current_bill_amount.toFixed(3));
    $('#cumulative_bill_amount_'+id).text(cumulative_bill_amount.toFixed(3));
    getTotals();
}

function getTotals(){
    var total_previous_bill_amount = 0.0;
    var total_current_bill_amount = 0.0;
    var total_cumulative_bill_amount = 0.0;
    var selected_product_length = $('input:checked').length;
    if(selected_product_length > 0){
        $('input:checked').each(function(){
            var id = $(this).val();
            var previous_bill_amount = parseFloat($('#previous_bill_amount_'+id).text());
            total_previous_bill_amount = total_previous_bill_amount + previous_bill_amount;
            var current_bill_amount = parseFloat($('#current_bill_amount_'+id).text());
            total_current_bill_amount = total_current_bill_amount + current_bill_amount;
            var cumulative_bill_amount = parseFloat($('#cumulative_bill_amount_'+id).text());
            total_cumulative_bill_amount = total_cumulative_bill_amount + cumulative_bill_amount;
        });
    }
    $('#total_previous_bill_amount').text(total_previous_bill_amount.toFixed(3));
    $('#rounded_off_previous_bill_amount').text(Math.round(total_previous_bill_amount));
    $('#total_current_bill_amount').text(total_current_bill_amount.toFixed(3));
    $('#rounded_off_current_bill_amount').text(Math.round(total_current_bill_amount));
    $('#total_cumulative_bill_amount').text(total_cumulative_bill_amount.toFixed(3));
    $('#rounded_off_cumulative_bill_amount').text(Math.round(total_cumulative_bill_amount));
    calculateTax();
}

function calculateTax(){
    var total_rounded_previous_bill = parseFloat($("#rounded_off_previous_bill_amount").text());
    var final_total_previous_bill = total_rounded_previous_bill;
    var total_rounded_current_bill = parseFloat($("#rounded_off_current_bill_amount").text());
    var final_total_current_bill = total_rounded_current_bill;
    var total_rounded_cumulative_bill = parseFloat($("#rounded_off_cumulative_bill_amount").text());
    var final_total_cumulative_bill = total_rounded_cumulative_bill;
    $(".tax").each(function(){
        var tax_amount_previous_bill = total_rounded_previous_bill * ($(this).val() / 100);
        var tax_amount_current_bill = total_rounded_current_bill * ($(this).val() / 100);
        var tax_amount_cumulative_bill = total_rounded_cumulative_bill * ($(this).val() / 100);
        final_total_previous_bill = final_total_previous_bill + tax_amount_previous_bill;
        $(this).parent().next().text(tax_amount_previous_bill.toFixed(3));
        final_total_current_bill = final_total_current_bill + tax_amount_current_bill;
        $(this).parent().next().next().text(tax_amount_current_bill.toFixed(3));
        final_total_cumulative_bill = final_total_cumulative_bill + tax_amount_cumulative_bill;
        $(this).parent().next().next().next().text(tax_amount_cumulative_bill.toFixed(3));
    });
    $("#final_previous_bill_total").text(Math.round(final_total_previous_bill));
    $("#final_current_bill_total").text(Math.round(final_total_current_bill));
    $("#final_cumulative_bill_total").text(Math.round(final_total_cumulative_bill));
}
