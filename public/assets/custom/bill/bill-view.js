$(document).ready(function (){

    $("#billTransactionCreateButton").on('click',function(e){
        e.stopPropagation();
        $("#billTransactionListingTab").removeClass('active');
        $("#billTransactionCreateTab").addClass('active');
    });

    $("#transactionSubmit").on('click',function(e){
        e.stopPropagation();
        var formData = $("#createTransactionForm").serializeArray();
        $.ajax({
            url: '/bill/transaction/create',
            type: 'POST',
            async: false,
            data: formData,
            success: function(data,textStatus,xhr){
                $("#billTransactionCreateTab").removeClass('active');
                $("#billTransactionListingTab").addClass('active');
                $("#billTransactionListingTab .filter-submit").trigger('click');
            },
            error: function(data){

            }
        });
    });

    typingTimer = 0;
    doneTypingInterval = 1000;
    $("#transactionTotal").on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(calculateTransactionDetails, doneTypingInterval);
    });
    $("#transactionTotal").on('keydown', function () {
        clearTimeout(typingTimer);
    });
});



function calculateTransactionDetails(){
    var billId = $("#billId").val();
    var total = $("#transactionTotal").val();
    $.ajax({
        url: '/bill/calculate-tax-amounts',
        type: 'POST',
        async: false,
        data:{
            _token: $("input[name='_token']").val(),
            bill_id: billId,
            total: total
        },
        success: function(data,textStatus,xhr){
            $("#transactionSubTotal").val(data.subtotal);
            $.each(data.taxes, function(i,v){
                $("#TaxAmount_"+ v.tax_id).val(v.tax_amount);
            })
        },
        error: function(data){

        }
    });
}