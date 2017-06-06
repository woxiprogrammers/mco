/**
 * Created by Ameya Joshi on 5/6/17.
 */


$(document).ready(function(){
    var category_id = $("#category_select_1").val();
    getProducts(category_id,1);
    var selectedProduct = $("#product_select_1").val();
    getProductDetails(selectedProduct, 1);
    $(".quotation-category").change(function(){
        var category_id = $(this).val();
        var categoryIdField = $(this).attr('id');
        var idArray = categoryIdField.split('_');
        var rowNumber = idArray[2];
        getProducts(category_id, rowNumber);
        var selectedProduct = $("#product_select_"+rowNumber).val();
        getProductDetails(selectedProduct, rowNumber);
    });
});


function getProducts(category_id,rowNumber){
    $.ajax({
        url: '/quotation/get-products',
        type: 'POST',
        data: {
            _token: $("input[name='_token']").val(),
            category_id: category_id
        },
        async: false,
        success: function(data, textStatus, xhr){
            $("#product_select_"+rowNumber).html(data);
            $("#product_select_"+rowNumber).prop('disabled', false);
        },
        error: function(errorStatus, xhr){

        }
    });
}

function getProductDetails(product_id,rowNumber){
    console.log('product id');
    console.log(product_id);
    $.ajax({
        url:'/quotation/get-product-detail',
        type: 'POST',
        data: {
            _token:$('input[name="_token"]').val(),
            product_id: product_id
        },
        success: function(data,textStatus,xhr){
            $("#product_description_"+rowNumber).val(data.description);
            $("#product_rate_"+rowNumber).val(data.rate_per_unit);
            $("#product_unit_"+rowNumber).val(data.unit);
        },
        error: function(errorStatus, xhr){

        }
    });
}