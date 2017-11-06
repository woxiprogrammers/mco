
$(document).ready(function(){
    $(".image").click(function(){
        var component_id = $(this).val();
        $.ajax({
            type: "POST",
            url: "/purchase/purchase-order/get-details",
            data:{po_id : $('#po_id').val() ,component_id : component_id},
            beforeSend: function(){
                $.LoadingOverlay("hide");
            },
            success: function(data){
               $('#material_name').val(data.name);
               $('#qty').val(data.quantity);
               $('#unit').val(data.unit_name);
               $('#hsn_code').val(data.hsn_code);
            }
        });
        $("#ImageUpload").modal();
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

});