/**
 * Created by Ameya Joshi on 18/1/18.
 */

$(document).ready(function(){
    var citiList = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: "/purchase/purchase-order-request/purchase-request-auto-suggest/%QUERY",
            filter: function(x) {
                if($(window).width()<420){
                    $("#header").addClass("fixed");
                }
                return $.map(x, function (data) {
                    return {
                        id:data.id,
                        format_id: data.format_id
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
            suggestion: Handlebars.compile('<div class="autosuggest"><strong>{{format_id}}</strong></div>')
        },
    }).on('typeahead:selected', function (obj, datum) {
        var POData = $.parseJSON(JSON.stringify(datum));
        $('.typeahead').typeahead('val',POData.format_id);
        var purchaseRequestId = POData.id;
        $("#purchaseRequestId").val(purchaseRequestId);
        $.ajax({
            url: '/purchase/purchase-order-request/get-purchase-request-component-details',
            type:'POST',
            data:{
                _token: $("input[name='_token']").val(),
                purchase_request_id: purchaseRequestId
            },
            success: function(data, textStatus, xhr){
                $("#purchaseRequestComponentTable tbody").html(data);
            },
            error: function(errorData){

            }
        });
    })
        .on('typeahead:open', function (obj, datum) {

        });
});
function componentTaxDetailSubmit(){
    var formData = $("#componentDetailForm").serializeArray();
    var componentRelationId = $("#modalComponentID").val();
    $("#componentRow-"+componentRelationId+" #hiddenInputs").remove();
    $("<div id='hiddenInputs'></div>").insertAfter("#componentRow-"+componentRelationId+" .component-vendor-relation");
    $.each(formData, function(key, value){
        if(value.name != 'vendor_images[]' && value.name != 'client_images[]'){
            $("#componentRow-"+componentRelationId+" #hiddenInputs").append("<input type='hidden' value='"+value.value+"' name='data["+componentRelationId+"]["+value.name+"]'>");
        }else{
            if(value.name != 'vendor_images[]'){
                $("#componentRow-"+componentRelationId+" #hiddenInputs").append("<input type='hidden' value='"+value.value+"' name='data["+componentRelationId+"][vendor_images][]'>");
            }else{
                $("#componentRow-"+componentRelationId+" #hiddenInputs").append("<input type='hidden' value='"+value.value+"' name='data["+componentRelationId+"][client_images][]'>");
            }
        }
    });
    var rate = $("input[name='data["+componentRelationId+"][rate_per_unit]'").val();
    var cgst_percentage = $("input[name='data["+componentRelationId+"][cgst_percentage]'").val();
    var sgst_percentage = $("input[name='data["+componentRelationId+"][sgst_percentage]'").val();
    var igst_percentage = $("input[name='data["+componentRelationId+"][igst_percentage]'").val();
    var rate_with_tax = parseFloat(rate) + parseFloat(rate * (cgst_percentage/100)) + parseFloat(rate * (sgst_percentage/100)) + parseFloat(rate * (igst_percentage/100));
    $("#componentRow-"+componentRelationId+" .rate-without-tax").text(rate);
    $("#componentRow-"+componentRelationId+" .rate-with-tax").text(rate_with_tax);
    $("#componentRow-"+componentRelationId+" .total-with-tax").text($("input[name='data["+componentRelationId+"][total]'").val());
    $('#detailsModal').modal('toggle');
}
function openDetailsModal(element, purchaseRequestComponentVendorRelationId){
    $("#modalComponentID").val(purchaseRequestComponentVendorRelationId);
    var rate = $(element).closest('tr').find('.rate-without-tax').text();
    $.ajax({
        url: '/purchase/purchase-order-request/get-component-tax-details/'+purchaseRequestComponentVendorRelationId+'?_token='+$("input[name='_token']").val(),
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
    var cgstAmount = subtotal * (cgstPercentage / 100);
    var sgstAmount = subtotal * (sgstPercentage / 100);
    var igstAmount = subtotal * (igstPercentage / 100);
    $(element).closest('.modal-body').find('.tax-modal-cgst-amount').val(cgstAmount);
    $(element).closest('.modal-body').find('.tax-modal-sgst-amount').val(sgstAmount);
    $(element).closest('.modal-body').find('.tax-modal-igst-amount').val(igstAmount);
    var total = subtotal + cgstAmount + sgstAmount + igstAmount;
    $(element).closest('.modal-body').find('.tax-modal-total').val(total);
}