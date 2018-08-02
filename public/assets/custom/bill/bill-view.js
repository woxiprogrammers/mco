$(document).ready(function (){
    ViewBill.init();
    typingTimer = 0;
    doneTypingInterval = 1000;
    var total = $('#remainingTotal').val();
    $(".calculatable-field").on('keyup', function(){
        var amount = $(".calculatable-field:input[name='amount']").val();
        if(typeof amount == 'undefined' || amount == ''){
            amount = 0;
            $(".calculatable-field:input[name='amount']").val(0);
        }
        var debit = $(".calculatable-field:input[name='debit']").val();
        if(typeof debit == 'undefined' || debit == ''){
            debit = 0;
            $(".calculatable-field:input[name='debit']").val(0);
        }
        var hold = $(".calculatable-field:input[name='hold']").val();
        if(typeof hold == 'undefined' || hold == ''){
            hold = 0;
            $(".calculatable-field:input[name='hold']").val(0);
        }
        /*var retention_percent = $(".calculatable-field:input[name='retention_percent']").val();
        if(typeof retention_percent == 'undefined' || retention_percent == ''){
            retention_percent = 0;
            $(".calculatable-field:input[name='retention_percent']").val(0);
        }
        var retentionAmount = (parseFloat(amount)) * (parseFloat(retention_percent)/100);
        $(".calculatable-field:input[name='retention_amount']").val(retentionAmount);*/

        var retentionAmount = $(".calculatable-field:input[name='retention_amount']").val();
        if(typeof retentionAmount == 'undefined' || retentionAmount == ''){
            retentionAmount = 0;
            $(".calculatable-field:input[name='retention_amount']").val(0);
        }

        /*var tds_percent = $(".calculatable-field:input[name='tds_percent']").val();
        if(typeof tds_percent == 'undefined' || tds_percent == ''){
            tds_percent = 0;
            $(".calculatable-field:input[name='tds_percent']").val(0);
        }
        var tdsAmount = (parseFloat(amount)) * (parseFloat(tds_percent)/100);
        $(".calculatable-field:input[name='tds_amount']").val(tdsAmount);*/

        var tdsAmount = $(".calculatable-field:input[name='tds_amount']").val();
        if(typeof tdsAmount == 'undefined' || tdsAmount == ''){
            tdsAmount = 0;
            $(".calculatable-field:input[name='tds_amount']").val(0);
        }

        var other_recovery_value = $(".calculatable-field:input[name='other_recovery_value']").val();
        if(typeof other_recovery_value == 'undefined' || other_recovery_value == ''){
            other_recovery_value = 0;
            $(".calculatable-field:input[name='other_recovery_value']").val(0);
        }
        var total = parseFloat(amount) - ((parseFloat(hold)) + (parseFloat(debit)) + (parseFloat(retentionAmount)) + (parseFloat(tdsAmount)) + (parseFloat(other_recovery_value)));
        $(".calculatable-field:input[name='total']").val(total)
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