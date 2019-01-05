/**
 * Created by manoj on 31/10/18.
 */
$(document).ready(function (){
    CreateBill.init();
    $('#submit').css("padding-left",'6px');
    $('#submit').prop('disabled',true);
    var typingTimer;
    var doneTypingInterval = 500;
    $("#change_bill").on('change', function(){
        var bill_id = $(this).val();
        if(bill_id == 'default'){
            var project_site_id = $('#project_site_id').val();
            window.location.href = "/bill/create/"+project_site_id;
        }else{
            window.location.href = "/bill/view/"+bill_id;
        }
    });
    $('input:checkbox.extra-item-checkbox').click(function () {
        var id = $(this).val();
        if($(this).prop("checked") == false){
            $('#extra_item_description_'+id).prop('disabled',true);
            $('#extra_item_rate_'+id).prop('disabled',true);
            $('#extra_item_rate_'+id).val('');
        }else{
            $('#extra_item_description_'+id).prop('disabled',false);
            $('#extra_item_rate_'+id).prop('disabled',false);
            var enteredRate = $('#extra_item_rate_'+id);
            $('#extra_item_rate_'+id).val(0);

            enteredRate.on('keyup', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(doneTyping, doneTypingInterval);
            });
            enteredRate.on('keydown', function () {
                clearTimeout(typingTimer);
            });
            function doneTyping () {
                getTotals();
            }
        }

    });

    $('input:checkbox.product-checkbox').click(function(){
        var id = $(this).val();
        var quantity = $('#current_quantity_'+id);
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
            $('#current_quantity_'+id+',#rate_per_unit_'+id).prop('disabled',true);
            $('#product_description_'+id).prop('disabled',true);
            $('#product_description_'+id).rules('remove');
            $('#product_description_id_'+id).rules('remove');
            $('#product_description_id_'+id).prop('disabled',true);
            $('#current_quantity_'+id+',#rate_per_unit_'+id).rules('remove');
            $('#current_quantity_'+id+',#rate_per_unit_'+id).closest('form-group').removeClass('has-error');
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
                $('#product_description_'+id).rules('add',{
                    required: true
                });
                $('#product_description_id_'+id).rules('add',{
                    required: true
                });
                $('#product_description_id_'+id).prop('disabled',false);
                $('.product_description_create').click(function (){
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
                                alert("Product Description created.");
                                $('#product_description_id_'+id).val(data.id);
                            }
                        },
                        error: function(data, textStatus, xhr){

                        }
                    });
                });

                $('.product_description_update').click(function (){
                    var productDescription = $('#product_description_'+id).val();
                    var descriptionId = $('#product_description_id_'+id).val();
                    if(productDescription !="" && descriptionId !=""){
                        $.ajax({
                            url: '/bill/product_description/update',
                            type: 'POST',
                            async: false,
                            data: {
                                'description' : productDescription,
                                'description_id' : descriptionId
                            },
                            success: function(data,textStatus,xhr){
                                alert("Product Description updated.");
                            },
                            error: function(data, textStatus, xhr){

                            }
                        });
                    }
                });

                $('.product_description_delete').click(function (){
                    $('#product_description_'+id).val("");
                    $('#previous_quantity_'+id).val("");
                });

                $('#current_quantity_'+id+',#rate_per_unit_'+id).prop('disabled',false);
                $('#current_quantity_'+id).val(0);
                $("#id_"+id).css('background-color',"#e1e1e1");
                var typingTimer;
                var doneTypingInterval = 500;
                /*$('#current_quantity_'+id).rules('add',{
                    required: true,
                    min: 0.000001,
                    max: diff
                });*/
                $('#current_quantity_'+id+',#rate_per_unit_'+id).on('keyup', function () {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(doneTyping, doneTypingInterval);
                });
                $('#current_quantity_'+id+',#rate_per_unit_'+id).on('keydown', function () {
                    clearTimeout(typingTimer);
                });
                function doneTyping () {
                    calculateQuantityAmount(quantity.val(),id);
                }
                calculateQuantityAmount(quantity.val(),id);
            }
            if($('input:checked:not(".tax-applied-on")').length > 0){
                $('#submit').prop('disabled',false);
            }else{
                $('#submit').prop('disabled',true);
            }
        }
    });

    $(".tax-applied-on").on('click',function(){
        calculateTax();
    });

    $('#discountAmount,#roundAmountBy').on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(calculateDiscount, doneTypingInterval);
    });
    $('#discountAmount').on('keydown', function () {
        clearTimeout(typingTimer);
    });
});

function calculateQuantityAmount(current_quantity,id){
    var bill_type_slug = $('#bill_type_id').val();
    if(current_quantity == ""){
        current_quantity = 0;
    }
    var cumulative_quantity = parseFloat($('#previous_quantity_'+id).text()) + parseFloat(current_quantity);
    if(bill_type_slug == 'sqft'){
        var current_bill_amount = parseFloat(current_quantity) * parseFloat($('#rate_per_unit_'+id).val());
    }else if(bill_type_slug == 'amountwise'){
        var boq_quantity = parseFloat($('#boq_quantity_'+id).text()).toFixed(3);
        var rate = parseFloat(parseFloat($('#rate_per_unit_'+id).val()) * boq_quantity).toFixed(3);
        $('#wo_amount_'+id).text(rate);
        var current_bill_amount = parseFloat(current_quantity) * rate;
    }else{
        var current_bill_amount = parseFloat(current_quantity) * parseFloat($('#rate_per_unit_'+id).val());
    }

    $('#cumulative_quantity_'+id).text((cumulative_quantity).toFixed(3));
    $('#current_bill_amount_'+id).text((current_bill_amount).toFixed(3));
    getTotals();
}

function getTotals(){
    var total_extra_item_rate = 0;
    var total_product_current_bill_amount = 0.0;

    var selected_product_length = $('input:checked.product-checkbox').length;
    if(selected_product_length > 0){
        $('input:checked.product-checkbox').each(function(){
            var id = $(this).val();
            var current_bill_amount = parseFloat($('#current_bill_amount_'+id).text());
            total_product_current_bill_amount = total_product_current_bill_amount + current_bill_amount;
        });
    }

    var selected_extra_item_length = $('input:checked.extra-item-checkbox').length;
    if(selected_extra_item_length > 0){
        $('input:checked.extra-item-checkbox').each(function () {
            var id = $(this).val();
            var enteredRate = $('#extra_item_rate_'+id).val();
            total_extra_item_rate = total_extra_item_rate + parseFloat(enteredRate);
        })
    }

    var total_current_bill_amount = parseFloat(total_extra_item_rate) + parseFloat(total_product_current_bill_amount);
    $('#sub_total_current_bill_amount').text((total_current_bill_amount).toFixed(3));
    $('#rounded_off_current_bill_sub_total').text((total_current_bill_amount).toFixed(3));
    calculateDiscount();
}

function calculateTax(){
    var total_rounded_current_bill = parseFloat($("#rounded_off_current_bill_amount").val()).toFixed(3);
    var final_total_current_bill = total_rounded_current_bill;
    $(".tax").each(function(){
        var tax_amount_current_bill = total_rounded_current_bill * ($(this).val() / 100);
        final_total_current_bill = parseFloat(final_total_current_bill) + parseFloat(tax_amount_current_bill);
        $(this).parent().next().find('span').text(tax_amount_current_bill.toFixed(3));
    });
    $("#final_current_bill_total").val(parseFloat(final_total_current_bill).toFixed(3));
    calculateSpecialTax()
}

function calculateSpecialTax(){
    if($(".special-tax").length > 0){
        $(".special-tax").each(function(){
            var specialTaxId = $(this).val();
            if($(".special_tax_"+specialTaxId+"_on:checkbox:checked").length > 0){
                var taxAmount = 0;
                $(".special_tax_"+specialTaxId+"_on:checkbox:checked").each(function(){
                    var taxId = $(this).val();
                    var taxOnAmount = 0;
                    if(taxId == 0 || taxId == '0'){
                        taxOnAmount = taxOnAmount + parseFloat($("#rounded_off_current_bill_amount").val());
                    }else{
                        taxOnAmount = taxOnAmount + parseFloat($("#tax_current_bill_amount_"+taxId).text());
                    }
                    var taxPercentage = $("#tax_percentage_"+specialTaxId).val();
                    taxAmount = parseFloat((taxAmount + ( taxOnAmount * (taxPercentage / 100))).toFixed(3));

                });
                $("#tax_current_bill_amount_"+specialTaxId).text(parseFloat(taxAmount).toFixed(3));
            }else{
                $("#tax_current_bill_amount_"+specialTaxId).text(0);
            }
        });
        var grossTotal = parseFloat($("#final_current_bill_total").val()).toFixed(3);
        $(".special-tax-amount").each(function(){
            grossTotal = parseFloat(grossTotal) + parseFloat($(this).text());
        });
        grossTotal = grossTotal + parseFloat($('#roundAmountBy').val());
        $("#grand_current_bill_total").val(parseFloat(grossTotal).toFixed(3));
    }else{
        var grossTotal = parseFloat($("#final_current_bill_total").val());
        grossTotal = grossTotal + parseFloat($('#roundAmountBy').val());
        $("#grand_current_bill_total").val((grossTotal).toFixed(3));
    }
}

function calculateDiscount(){
    var discountAmount = parseFloat($('#discountAmount').val()).toFixed(3);
    var totalBillAmount = parseFloat($('#rounded_off_current_bill_sub_total').text()).toFixed(3);
    if((typeof discountAmount == 'undefined') || discountAmount == ''){
        $('#rounded_off_current_bill_amount').val(totalBillAmount);
    }else{
        $('#rounded_off_current_bill_amount').val((totalBillAmount-discountAmount).toFixed(3));
    }
    calculateTax();
}


