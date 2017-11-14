
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
               var abc = [];
               $.each(data.material_component_images ,function(key,value){
                console.log(key);
                console.log(value);
                 abc += '<div class="item"><img src="'+ value.name + '"alt="New york" style="width:100%;height: 170px"></div>';
                });
               console.log(abc)
                $('#imagecorousel').html(abc);
                $.each(data.client_approval_images ,function(key,value){
                    console.log(key);
                    console.log(value);
                    abc += '<div class="item"><img id="image" src="'+ value.name + '"alt="New york" style="width:100%;height: 170px"></div>';
                });
                $('#imagecorouselForClientApproval').html(abc);

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

});