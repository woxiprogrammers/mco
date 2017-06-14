$(document).ready(function(){

    calculateSubTotal();
    $("#next_btn").on('click',function(){
        if($("#material_id option:selected").length > 0){
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
    $("#material_id option:selected").each(function(i){
        material_ids[i] = $(this).val();
    });

    $.ajax({
        url: '/product/material/listing',
        type: "POST",
        data :{
            '_token' : $("input[name='_token']").val(),
            'material_ids' : material_ids
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
    $("#material_"+materialId+"_amount").val(amount);
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
    $("#subtotal").text(amount);
    calculateProfitMargin();
}

function calculateProfitMargin(){
    var amount = parseFloat($("#subtotal").text());
    var total = amount;
    $(".profit-margin").each(function(){
        var profitMarginAmount = amount * ($(this).val() / 100);
        total = total + profitMarginAmount;
        $(this).parent().next().text(profitMarginAmount);
    });
    $("#total").text(total);
}

function convertUnits(materialId){
    var newUnit = $("#material_"+materialId+"_unit").val();
    $.ajax({
        url: '/units/convert',
        type: 'POST',
        async: false,
        data: {
            new_unit: newUnit,
            material_id:materialId,
            _token: $("input[name='_token']").val()
        },
        success: function(data,textStatus,xhr){
            if(xhr.status == 200){
                $("#material_"+materialId+"_rate").val(data.rate);
            }else{
                $("#material_"+materialId+"_unit option[value='"+data.unit+"']").prop('selected', true);
                $("#material_"+materialId+"_rate").val(data.rate);
            }
            changedQuantity(materialId);
        },
        error: function(data, textStatus, xhr){

        }
    });
}