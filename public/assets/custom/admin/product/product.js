$(document).ready(function(){

    calculateSubTotal();
    $("#next_btn").on('click',function(){
        if($("#material_id input:checkbox:checked").length > 0){
            getMaterialDetails();
            $(".materials-table-div").show();
        }
    });
    $("#category_name").on('change', function(){
        if(!($("#materials-table-div").is(':visible'))){
            $("#productMaterialTable tr").each(function(){
                $(this).remove();
            });
            $(".materials-table-div").hide();
        }
        getMaterials($("#category_name").val());
    });
});

function getMaterials(category){
    $.ajax({
        url: '/product/get-materials/'+category,
        type: 'GET',
        async: false,
        success: function(data, textStatus, xhr){
            if(xhr.status == 200){
                $("#material_id").html(data);
                $("#productMaterialTable input[type='number']").each(function(){
                    $(this).rules('add',{
                        required: true
                    });
                });

            }else{

            }
        },
        error: function(errorStatus,xhr){

        }
    });
}

function getMaterialDetails(){
    var material_ids = [];
    $("#material_id input:checkbox:checked").each(function(i){
        material_ids[i] = $(this).val();
    });
    if($(".product-material-id").length > 0){
        var productMaterialId = [];
        $(".product-material-id").each(function(i){
            productMaterialId[i] = $(this).val();
        });

    }else{
        var productMaterialId = null;
    }
    $.ajax({
        url: '/product/material/listing',
        type: "POST",
        data :{
            '_token' : $("input[name='_token']").val(),
            'material_ids' : material_ids,
            'product_material_id': productMaterialId
        },
        async: false,
        success: function(data,textStatus, xhr){
            $("#productMaterialTable").html(data);
            calculateSubTotal();
        },
        error: function(errorStatus, xhr){

        }
    });
}
function changedQuantity(materialId){
    var rate = $("#material_"+materialId+"_rate").val();
    var quantity = $("#material_"+materialId+"_quantity").val();
    var amount = rate*quantity;
    $("#material_"+materialId+"_amount").val(Math.round(amount * 1000) / 1000);
    calculateSubTotal();
}

function calculateSubTotal(){
    var amount = 0;
    $(".material_amount").each(function(){
        amount = amount+parseFloat($(this).val());
    });
    if(isNaN(amount)){
        amount = 0;
    }

    $("#subtotal,#productViewSubtotal").text(Math.round(amount * 1000) / 1000);
    calculateProfitMargin();
}

function calculateProfitMargin(){
    var amount = parseFloat($("#subtotal,#productViewSubtotal").text());
    var total = amount;
    $(".profit-margin").each(function(){
        var profitMarginAmount = amount * ($(this).val() / 100);
        total = total + profitMarginAmount;
        $(this).parent().next().text(Math.round(profitMarginAmount * 1000) / 1000);
    });
    $("#total,#productViewTotal").text(Math.round(total * 1000) / 1000);
}

function convertUnits(materialId){
    var newUnit = $("#material_"+materialId+"_unit").val();
    var url = window.location.href;
    if(url.indexOf("edit") > 0){
        var materialVersionUnitId = $("input[name='unit_"+materialId+"']").val();
        var materialVersionRate = $("input[name='rate_"+materialId+"']").val();
        var data = {
                current_unit: materialVersionUnitId,
                rate: materialVersionRate,
                new_unit: newUnit,
                material_id:materialId,
                _token: $("input[name='_token']").val()
            };
    }else{
        var data = {
                new_unit: newUnit,
                material_id:materialId,
                _token: $("input[name='_token']").val()
            };
    }
    $.ajax({
        url: '/units/convert',
        type: 'POST',
        async: false,
        data: data,
        success: function(data,textStatus,xhr){
            if(xhr.status == 200){
                $("#material_"+materialId+"_rate").val(Math.round(data.rate * 1000) / 1000);
            }else{
                $("#material_"+materialId+"_unit option[value='"+data.unit+"']").prop('selected', true);
                $("#material_"+materialId+"_rate").val(Math.round(data.rate * 1000) / 1000);
            }
            changedQuantity(materialId);
        },
        error: function(data, textStatus, xhr){

        }
    });
}