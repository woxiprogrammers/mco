
$(document).ready(function(){
    $(".component-view").click(function(){
        var component_id = $(this).val();
        $.ajax({
            type: "POST",
            url: "/purchase/purchase-order/get-details",
            data:{po_id : $('#po_id').val() ,component_id : component_id},
            beforeSend: function(){
                $.LoadingOverlay("hide");
            },
            success: function(data){
                $("#ImageUpload .modal-body form").html(data);
                $("#ImageUpload").modal();
            }
        });

    });
    $(".transaction").click(function(){
        var component_id = $(this).val();
        $.ajax({
            type: "POST",
            url: "/purchase/purchase-order/get-details",
            data:{po_id : $('#po_id').val() ,component_id : component_id},
            beforeSend: function(){
                $.LoadingOverlay("hide");
            },
            success: function(data){
                $('#material').val(data.name);
                $('#vendor').val(data.vendor_name);
                $('#quantity').val(data.quantity);
                $('#unit_name').val(data.unit_name);
                $('#hsn_code').val(data.hsn_code);
                $('#po_component_id').val(data.purchase_order_component_id);
                $('#unit_id').val(data.unit_id);
            }
        });
        $("#transactionModal").modal();
    });
    $(".payment").click(function(){
        var po_id = $(this).val();
        var bill_amount= $('#'+po_id).val();
        $("#paymentModal").modal();
        $('#po_bill_id').val(po_id);
        $('#bilAmount').val(bill_amount);
    });
    $(".amendment_status_change").click(function(){
        var po_id = $(this).val();
        $("#amendmentModal").modal();
        $('#purchase_order_bill_id').val(po_id);
    });
    $(".view_details").click(function(){
        var po_id = $(this).val();
        $.ajax({
            type: "POST",
            url: "/purchase/purchase-order/get-bill-details",
            data:{po_id : po_id},
            beforeSend: function(){
                $.LoadingOverlay("hide");
            },
            success: function(data){
                $('#grn').val(data.grn);
                $('#amount').val(data.bill_amount);
                $('#bill_quantity').val(data.quantity);
                $('#bill_unit').val(data.unit);
                $('#remark').val(data.remark);
            }
        });
        $("#viewDetailModal").modal();
    });
    $('#poReopenBtn').click(function (){
        var po_id = $('#po_id').val();
        var vendor_id = $('#vendor_id').val();
        $.ajax({
            type: "POST",
            url: "/purchase/purchase-order/reopen",
            data:{po_id : po_id , vendor_id:vendor_id},
            beforeSend: function(){

                },
            success: function(data){
                location.reload();
            }
        });
    });

});

function submitPOPassword(){
    var po_id = $('#po_id').val();
    var vendor_id = $('#vendor_id').val();
    var password = $.trim($("#POPassword").val());
    if(password.length > 0){
        $.ajax({
            type: "POST",
            url: "/purchase/purchase-order/authenticate-purchase-order-close",
            data:{password : password, _token: $("input[name='_token']").val()},
            success: function(data){
                $.ajax({
                    type: "POST",
                    url: "/purchase/purchase-order/close-purchase-order",
                    data:{po_id : po_id , vendor_id:vendor_id},
                    beforeSend: function(){
                    },
                    success: function(data){
                        location.reload();
                    }
                });
            },
            error: function(xhr){
                if(xhr.status == 401){
                    alert("You are not authorised to close this purchase order.");
                }
            }
        });
    }else{
        alert('Please enter valid password');
    }
}