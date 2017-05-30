$(document).ready(function(){
    getMaterials($("#category_name").val());
    $("#next_btn").on('click',function(){
        if($("#material_id option:selected").length > 0){
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
                $("#material_id").html(data);
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
        },
        error: function(errorStatus, xhr){

        }
    });
}
function changedQuantity(materialVersion){
    /*material_version[{{$data['material_version']['id']}}]['rate_per_unit']
    *
    * material_version_{{$data['material_version']['id']}}_amount*/
    var rate = $("#material_version_"+materialVersion+"_rate").val();
    console.log("rate"+rate);

    var quantity = $("#material_version_"+materialVersion+"_quantity").val();
    console.log("quantity"+quantity);
    var amount = rate*quantity;
    console.log("amount"+amount);

    $("#material_version_"+materialVersion+"_amount").val(amount);
}