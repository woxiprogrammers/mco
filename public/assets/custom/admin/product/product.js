$(document).ready(function(){
    $(".materials-table-div").hide();

    getMaterials($("#category_name").val());
    $("#next_btn").on('click',function(){
        if($("#material_name option:selected").length > 0){
            getMaterialDetails();
            $(".materials-table-div").show();
        }
    });

    $("#category_name").on('change', function(){
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
                $("#material_name").html(data);
            }else{

            }
        },
        error: function(errorStatus,xhr){

        }
    });
}

function getMaterialDetails(){
    var material_ids = [];
    $("#material_name option:selected").each(function(i){
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
            $("#productMaterialTable").html(data.data);
        },
        error: function(errorStatus, xhr){

        }
    });
}
function changedQuantity(materialVersion){
    var rate = $("input[name='material_rate["+materialVersion+"]']").val();
    console.log("rate"+rate);
    var quantity = $("input[name='material_quantity["+materialVersion+"]']").val();
    console.log("quantity"+quantity);
    var amount = rate*quantity;
    console.log("amount"+amount);

    $("input[name='material_amount["+materialVersion+"]']").val(amount);
}