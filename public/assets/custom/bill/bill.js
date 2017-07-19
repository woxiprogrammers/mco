$(document).ready(function (){
    CreateBill.init();
    $('#submit').prop('disabled',true);
    $("#change_bill").on('change', function(){
        var bill_id = $(this).val();
        if(bill_id == 'default'){
            var project_site_id = $('#project_site_id').val();
            window.location.href = "/bill/create/"+project_site_id;
        }else{
            window.location.href = "/bill/view/"+bill_id;
        }
    });

    $('input[type="checkbox"]').click(function(){
        var id = $(this).val();
        var input = $('#current_quantity_'+id);
        var boq = $('#boq_quantity_'+id).text();
        var previous_quantity = $('#previous_quantity_'+id).text();
        var diff = parseFloat(boq - previous_quantity);
        if($(this).prop("checked") == false){
            if($('input:checked').length > 0){
                $('#submit').prop('disabled',false);
            }else{
                $('#submit').prop('disabled',true);
            }
            $("#id_"+id).css('background-color',"");
            $('#current_quantity_'+id).prop('disabled',true);
            $('#product_description_'+id).prop('disabled',true);
            $('#product_description_id_'+id).prop('disabled',true);
            $('#current_quantity_'+id).rules('remove');
            $('#current_quantity_'+id).closest('form-group').removeClass('has-error');
            $('#current_quantity_'+id).val('');
            $('#cumulative_quantity_'+id).text("");
            $('#current_bill_amount_'+id).text("");
            getTotals();
        }else{
            if(diff == 0){
                $(this).attr('checked',false);
                $('#boq_quantity_'+id).css('background-color',"ff8884");
                $('#previous_quantity_'+id).css('background-color',"ff8884");
            }else{
                $('#product_description_'+id).prop('disabled',false);
                $('#product_description_id_'+id).prop('disabled',false);
                $('#product_description_create').click(function (){
                    $.ajax({
                        url: '/bill/product_description/create',
                        type: 'POST',
                        async: false,
                        data :{
                            'description' : $('#product_description_'+id).val(),
                            'quotation_id' : $('#quotation_id').val()
                        },
                        success: function(data,textStatus,xhr){
                            if(xhr.status == 200){
                                $('#product_description_id_'+id).val(data.id);
                            }
                        },
                        error: function(data, textStatus, xhr){

                        }
                    });
                });
                $('#product_description_update').click(function (){
                    $.ajax({
                        url: '/bill/product_description/update',
                        type: 'POST',
                        async: false,
                        data: {
                            'description' : $('.product_description').val(),
                            'quotation_id' : $('#quotation_id').val()
                        },
                        success: function(data,textStatus,xhr){
                            if(xhr.status == 200){
                            }else{
                            }
                        },
                        error: function(data, textStatus, xhr){

                        }
                    });
                });
                $('#product_description_delete').click(function (){
                    $('.product_description').val("");
                    /*$.ajax({
                        url: '/bill/product_description/delete',
                        type: 'POST',
                        async: false,
                        data: {

                        },
                        success: function(data,textStatus,xhr){
                            if(xhr.status == 200){
                            }else{
                            }
                        },
                        error: function(data, textStatus, xhr){

                        }
                    });*/
                });
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
            if($('input:checked').length > 0){
                $('#submit').prop('disabled',false);
            }else{
                $('#submit').prop('disabled',true);
            }
        }
    });
});

function calculateQuantityAmount(current_quantity,id){
    if(current_quantity == ""){
        current_quantity = 0;
    }
    var cumulative_quantity = parseFloat($('#previous_quantity_'+id).text()) + parseFloat(current_quantity);
    var current_bill_amount = parseFloat(current_quantity) * parseFloat($('#rate_per_unit_'+id).text());
    $('#cumulative_quantity_'+id).text(cumulative_quantity.toFixed(3));
    $('#current_bill_amount_'+id).text(current_bill_amount.toFixed(3));
    getTotals();
}

function getTotals(){
    var total_current_bill_amount = 0.0;
    var selected_product_length = $('input:checked').length;
    if(selected_product_length > 0){
        $('input:checked').each(function(){
            var id = $(this).val();
            var current_bill_amount = parseFloat($('#current_bill_amount_'+id).text());
            total_current_bill_amount = total_current_bill_amount + current_bill_amount;
        });
    }
    $('#total_current_bill_amount').text(total_current_bill_amount.toFixed(3));
    $('#rounded_off_current_bill_amount').text(Math.round(total_current_bill_amount));
    calculateTax();
}

function calculateTax(){
    var total_rounded_current_bill = parseFloat($("#rounded_off_current_bill_amount").text());
    var final_total_current_bill = total_rounded_current_bill;
    $(".tax").each(function(){
        var tax_amount_current_bill = total_rounded_current_bill * ($(this).val() / 100);
        final_total_current_bill = final_total_current_bill + tax_amount_current_bill;
        $(this).parent().next().text(tax_amount_current_bill.toFixed(3));
    });
    $("#final_current_bill_total").text(Math.round(final_total_current_bill));
}
