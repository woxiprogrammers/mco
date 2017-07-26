$(document).ready(function (){
    ViewBill.init();
    $("#billTransactionCreateButton").on('click',function(e){
        e.stopPropagation();
        $("#billTransactionListingTab").removeClass('active');
        $("#billTransactionCreateTab").addClass('active');
        CKEDITOR.replace("transactionRemark",{
            extraPlugins:"imageuploader"
        });
    });

    $("#transactionSubmit").on('click',function(e){
        e.stopPropagation();
        var remark = CKEDITOR.instances["transactionRemark"].getData();
        $("#transactionRemark").val(remark);
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
    var total = $('#remainingTotal').val();
    console.log(total);
    $('#transactionTotal').rules('add',{
        max: total
    });
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
    $("#transactionTotal").prop('disabled', true);
    var remainingTotal = $("#remainingTotal").val();
    if(total <= remainingTotal){
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
                $("#transactionTotal").prop('disabled', false);
            },
            error: function(data){

            }
        });
    }else{
        $("#transactionTotal").prop('disabled', false);
    }

}

function getTransactionDetails(id){
    var url = "/bill/transaction/detail/"+id;
    $.ajax({
        url: url,
        type: "GET",
        async: false,
        success: function (data,textStatus, xhr) {
            $("#transactionModal .modal-body").html(data);
            $("#transactionModal").modal('show');
        },
        error: function (data) {

        }
    });
}