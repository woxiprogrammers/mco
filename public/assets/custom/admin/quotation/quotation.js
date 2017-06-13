/**
 * Created by Ameya Joshi on 5/6/17.
 */


$(document).ready(function(){
    var category_id = $("#categorySelect1").val();
    getProducts(category_id,1);
    var selectedProduct = $("#productSelect1").val();
    getProductDetails(selectedProduct, 1);

    $(".quotation-category").change(function(){
        var category_id = $(this).val();
        var categoryIdField = $(this).attr('id');
        var rowNumber = categoryIdField.match(/\d+/)[0];
        getProducts(category_id, rowNumber);
        var selectedProduct = $("#productSelect"+rowNumber).val();
        getProductDetails(selectedProduct, rowNumber);
    });

    $(".quotation-product").on('change',function(){
        var productId = $(this).val();
        var productRowId = $(this).attr('id');
        var rowNumber = productRowId.match(/\d+/)[0];
        getProductDetails(productId,rowNumber);
    });

    $("#addProduct").on('click',function(){
        var rowCount = $('#productRowCount').val();
        $.ajax({
            url: '/quotation/add-product-row',
            type: 'POST',
            async: true,
            data: {
                _token: $("input[name='_token']").val(),
                row_count: rowCount
            },
            success: function(data,textStatus,xhr){
                $("#productTable").append(data);
                $('#productRowCount').val(parseInt(rowCount)+1);
            },
            error: function(errorStatus, xhr){

            }
        });
    });

    $("#next1").on('click', function(e){
        e.stopPropagation();
        var productIds = [];
        $(".quotation-product").each(function(){
            productIds.push($(this).val());
        });
        var formFields = $('#QuotationCreateForm').serializeArray();
        var validForm = true;
       $.each(formFields, function(i){
            console.log(formFields[i]);
           if(($.trim(formFields[i].value)) == ""){
                $("[name='"+formFields[i].name+"']").closest(".form-group").addClass("has-error");
               validForm = false;
           }else{
               $("[name='"+formFields[i].name+"']").closest(".form-group").removeClass("has-error");
           }
        });
        if(validForm == true){
            $.ajax({
                url: '/quotation/get-materials',
                async: false,
                type: "POST",
                data:{
                    //_token: $("input[name='_token']").val(),
                    product_ids: productIds
                },
                success: function(data, textStatus, xhr){
                    $("#GeneralTab").removeClass('active');
                    $("#ProfitMarginsTab").removeClass('active');
                    $("#MaterialsTab").addClass('active');
                    $("#MaterialsTab").html(data);
                },
                error: function(errorStatus, data){

                }
            });
        }
    });
});

function backToGeneral(){
    $("#MaterialsTab").removeClass('active');
    $("#ProfitMarginsTab").removeClass('active');
    $("#GeneralTab").addClass('active');
}

function backToMaterials(){
    $("#ProfitMarginsTab").removeClass('active');
    $("#GeneralTab").removeClass('active');
    $("#MaterialsTab").addClass('active');
}

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
            $("#productSelect"+rowNumber).html(data);
            $("#productSelect"+rowNumber).prop('disabled', false);
        },
        error: function(errorStatus, xhr){

        }
    });
}

function getProductDetails(product_id,rowNumber){
    $.ajax({
        url:'/quotation/get-product-detail',
        type: 'POST',
        data: {
            _token:$('input[name="_token"]').val(),
            product_id: product_id
        },
        success: function(data,textStatus,xhr){
            $("#productDescription"+rowNumber).val(data.description);
            $("#productDescription"+rowNumber).attr('name','product_description['+data.id+']');
            $("#productRate"+rowNumber).val(data.rate_per_unit);
            $("#productRate"+rowNumber).attr('name','product_rate['+data.id+']');
            $("#productRate"+rowNumber).prop('readonly', false);
            $("#productQuantity"+rowNumber).prop('readonly', false);
            $("#productUnit"+rowNumber).val(data.unit);
            $("#productUnit"+rowNumber).attr('name','product_unit['+data.id+']');
            $("#productQuantity"+rowNumber).attr('name','product_quantity['+data.id+']');
            $("#productAmount"+rowNumber).attr('name','product_amount['+data.id+']');
        },
        error: function(errorStatus, xhr){

        }
    });
}

function removeRow(row){
    $("#Row"+row).remove();
}

function calculateAmount(row){
    var rate = parseFloat($("#productRate"+row).val());
    var quantity = parseFloat($("#productQuantity"+row).val());
    var amount = rate * quantity;
    if(isNaN(amount)){
        $("#productAmount"+row).val(0);
    }else{
        $("#productAmount"+row).val(amount);
    }
}

function replaceEditor(row){
    if(CKEDITOR.instances["ckeditor"+row]){
        var description = CKEDITOR.instances["ckeditor"+row].getData();
        $("#productDescription"+row).val(description);
        CKEDITOR.instances["ckeditor"+row].destroy();
        $("#TempRow"+row).remove();
    }else{
        var description = $("#productDescription"+row).val();
        $( "<tr id='TempRow"+row+"'><td colspan='8'><textarea id='ckeditor"+row+"'>"+description+"</textarea></td></tr>" ).insertAfter("#Row"+row);
        CKEDITOR.replace('ckeditor'+row,{
            extraPlugins:"imageuploader"
        });
    }
}

function showProfitMargins(){
    var validForm = true;
    $(".quotation-material-rate").each(function(){
        if(($.trim($(this).val())) == ''){
            $(this).closest('.form-group').addClass('has-error');
            validForm = false;
        }else{
            $(this).closest('.form-group').removeClass('has-error');
        }
    });

    if(validForm == true){
        var productIds = [];
        $(".quotation-product").each(function(){
            productIds.push($(this).val());
        });
        $.ajax({
            url: '/quotation/get-profit-margins',
            async: false,
            type: "POST",
            data:{
                //_token: $("input[name='_token']").val(),
                product_ids: productIds
            },
            success: function(data, textStatus, xhr){
                $("#GeneralTab").removeClass('active');
                $("#MaterialsTab").removeClass('active');
                $("#ProfitMarginsTab").addClass('active');
                $("#ProfitMarginsTab").html(data);
            },
            error: function(errorStatus, data){

            }
        });
    }

}