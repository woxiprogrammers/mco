/**
 * Created by Ameya Joshi on 5/6/17.
 */


$(document).ready(function(){
    $(".quotation-category").change(function(){
        var category_id = $(this).val();
        debugger;
        $.ajax({
            url: '/quotation/get-products',
            type: 'POST',
            data: {
                _token: $("input[name='_token']").val(),
                category_id: category_id
            },
            async: false,
            success: function(data, textStatus, xhr){
                
            },
            error: function(errorStatus, xhr){

            }
        });
    });
});