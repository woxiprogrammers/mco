/**
 * Created by Ameya Joshi on 5/6/17.
 */


$(document).ready(function(){
    var category_id = $("#category_select_1").val();
    getProducts(category_id,1);
    $(".quotation-category").change(function(){
        var category_id = $(this).val();
        var categoryIdField = $(this).attr('id');
        var idArray = categoryIdField.split('_');
        var rowNumber = idArray[2];
        getProducts(category_id, rowNumber);
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