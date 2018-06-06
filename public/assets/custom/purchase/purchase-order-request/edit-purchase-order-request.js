function componentTaxDetailSubmit(){
    var formData = $("#componentDetailForm").serializeArray();
    var componentRelationId = $("#modalComponentID").val();
    $("#componentRow-"+componentRelationId+" #hiddenInputs").remove();
    $("<div id='hiddenInputs'></div>").insertAfter("#componentRow-"+componentRelationId+" .component-vendor-relation");
    $.each(formData, function(key, value){
        if(value.name != 'vendor_images[]' && value.name != 'client_images[]'){
            $("#componentRow-"+componentRelationId+" #hiddenInputs").append("<input type='hidden' value='"+value.value+"' name='data["+componentRelationId+"]["+value.name+"]'>");
        }else{
            if(value.name == 'vendor_images[]'){
                $("#componentRow-"+componentRelationId+" #hiddenInputs").append("<input type='hidden' value='"+value.value+"' name='data["+componentRelationId+"][vendor_images][]'>");
            }else{
                $("#componentRow-"+componentRelationId+" #hiddenInputs").append("<input type='hidden' value='"+value.value+"' name='data["+componentRelationId+"][client_images][]'>");
            }
        }
    });
    var rate = $("input[name='data["+componentRelationId+"][rate_per_unit]'").val();
    if(rate == '-'){
        $("#componentRow-"+componentRelationId+" .rate-without-tax").text('-');
        $("#componentRow-"+componentRelationId+" .rate-with-tax").text('-');
        $("#componentRow-"+componentRelationId+" .total-with-tax").text('-');
    }else{
        var cgst_percentage = $("input[name='data["+componentRelationId+"][cgst_percentage]'").val();
        var sgst_percentage = $("input[name='data["+componentRelationId+"][sgst_percentage]'").val();
        var igst_percentage = $("input[name='data["+componentRelationId+"][igst_percentage]'").val();
        var rate_with_tax = parseFloat(rate) + parseFloat(rate * (cgst_percentage/100)) + parseFloat(rate * (sgst_percentage/100)) + parseFloat(rate * (igst_percentage/100));
        $("#componentRow-"+componentRelationId+" .rate-without-tax").text(customRound(rate));
        $("#componentRow-"+componentRelationId+" .rate-with-tax").text(customRound(rate_with_tax));
        $("#componentRow-"+componentRelationId+" .total-with-tax").text(customRound($("input[name='data["+componentRelationId+"][total]'").val()));
    }
    var quantity = $(".tax-modal-quantity").val();
    $("#componentRow-"+componentRelationId+" .quantity").text(quantity);
    $('#detailsModal').modal('toggle');
}
function openDetailsModal(element, purchaseOrderRequestComponentId){
    $("#modalComponentID").val(purchaseOrderRequestComponentId);
    var rate = $(element).closest('tr').find('.rate-without-tax').text();
    $.ajax({
        url: '/purchase/purchase-order-request/get-purchase-order-request-component-tax-details/'+purchaseOrderRequestComponentId+'?_token='+$("input[name='_token']").val(),
        type: 'POST',
        data:{
            _token: $("input[name='_token']").val(),
            rate: rate
        },
        success: function(data, textStatus, xhr){
            $("#detailsModal .modal-body").html(data);
            $("#detailsModal").modal('show');
        },
        error: function(errorData){

        }
    });
}
function calculateTaxes(element){
    var rate = parseFloat($(element).closest('.modal-body').find('.tax-modal-rate').val());
    if(typeof rate == 'undefined' || rate == '' || isNaN(rate)){
        rate = 0;
    }
    var quantity = parseFloat($(element).closest('.modal-body').find('.tax-modal-quantity').val());
    if(typeof quantity == 'undefined' || quantity == '' || isNaN(quantity)){
        quantity = 0;
    }
    var subtotal = rate * quantity;
    $(element).closest('.modal-body').find('.tax-modal-subtotal').val(subtotal);
    var cgstPercentage = parseFloat($(element).closest('.modal-body').find('.tax-modal-cgst-percentage').val());
    if(typeof cgstPercentage == 'undefined' || cgstPercentage == '' || isNaN(cgstPercentage)){
        cgstPercentage = 0;
    }
    var sgstPercentage = parseFloat($(element).closest('.modal-body').find('.tax-modal-sgst-percentage').val());
    if(typeof sgstPercentage == 'undefined' || sgstPercentage == '' || isNaN(sgstPercentage)){
        sgstPercentage = 0;
    }
    var igstPercentage = parseFloat($(element).closest('.modal-body').find('.tax-modal-igst-percentage').val());
    if(typeof igstPercentage == 'undefined' || igstPercentage == '' || isNaN(igstPercentage)){
        igstPercentage = 0;
    }
    var cgstAmount = customRound(subtotal * (cgstPercentage / 100));
    var sgstAmount = customRound(subtotal * (sgstPercentage / 100));
    var igstAmount = customRound(subtotal * (igstPercentage / 100));
    $(element).closest('.modal-body').find('.tax-modal-cgst-amount').val(cgstAmount);
    $(element).closest('.modal-body').find('.tax-modal-sgst-amount').val(sgstAmount);
    $(element).closest('.modal-body').find('.tax-modal-igst-amount').val(igstAmount);
    var total = customRound(subtotal + cgstAmount + sgstAmount + igstAmount);
    $(element).closest('.modal-body').find('.tax-modal-total').val(total);
}

function calculateTransportationTaxes(element){
    var transportation_amount = parseFloat($(element).closest('.modal-body').find('.calculate-transportation-amount').val());
    if(typeof transportation_amount == 'undefined' || transportation_amount == '' || isNaN(transportation_amount)){
        transportation_amount = 0;
    }
    var quantity = parseFloat($(element).closest('.modal-body').find('.tax-modal-quantity').val());
    if(typeof quantity == 'undefined' || quantity == '' || isNaN(quantity)){
        quantity = 0;
    }
    var cgstPercentage = parseFloat($(element).closest('.modal-body').find('.calculate-transportation-cgst-percentage').val());
    if(typeof cgstPercentage == 'undefined' || cgstPercentage == '' || isNaN(cgstPercentage)){
        cgstPercentage = 0;
    }
    var sgstPercentage = parseFloat($(element).closest('.modal-body').find('.calculate-transportation-sgst-percentage').val());
    if(typeof sgstPercentage == 'undefined' || sgstPercentage == '' || isNaN(sgstPercentage)){
        sgstPercentage = 0;
    }
    var igstPercentage = parseFloat($(element).closest('.modal-body').find('.calculate-transportation-igst-percentage').val());
    if(typeof igstPercentage == 'undefined' || igstPercentage == '' || isNaN(igstPercentage)){
        igstPercentage = 0;
    }
    var cgstAmount = customRound(transportation_amount * (cgstPercentage / 100));
    var sgstAmount = customRound(transportation_amount * (sgstPercentage / 100));
    var igstAmount = customRound(transportation_amount * (igstPercentage / 100));
    $(element).closest('.modal-body').find('.calculate-transportation-cgst-amount').val(cgstAmount);
    $(element).closest('.modal-body').find('.calculate-transportation-sgst-amount').val(sgstAmount);
    $(element).closest('.modal-body').find('.calculate-transportation-igst-amount').val(igstAmount);
    var total = customRound(transportation_amount + cgstAmount + sgstAmount + igstAmount);
    $(element).closest('.modal-body').find('.calculate-transportation-total').val(customRound(total));
}