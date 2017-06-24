/**
 * Created by Ameya Joshi on 5/6/17.
 */


$(document).ready(function(){


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
        var url = window.location.href;
        var rowCount = $('#productRowCount').val();
        if(url.indexOf("edit") > 0){
            var data = {
                _token: $("input[name='_token']").val(),
                row_count: rowCount,
                is_edit: true
            }
        }else{
            var data = {
                    _token: $("input[name='_token']").val(),
                    row_count: rowCount
                }
        }
        $.ajax({
            url: '/quotation/add-product-row',
            type: 'POST',
            async: true,
            data: data,
            success: function(data,textStatus,xhr){
                $("#productTable").append(data);
                $('#productRowCount').val(parseInt(rowCount)+1);
            },
            error: function(errorStatus, xhr){

            }
        });
    });

    $("#materialCosts").on('click', function(e){
        e.stopPropagation();
        var productIds = [];
        $(".quotation-product").each(function(){
            productIds.push($(this).val());
        });
        var url = window.location.href;
        var formFields = $("#QuotationEditForm").serializeArray();
        var validForm = true;
        $.each(formFields, function(i){
           if(($.trim(formFields[i].value)) == ""){
                $("[name='"+formFields[i].name+"']").closest(".form-group").addClass("has-error");
               validForm = false;
           }else{
               $("[name='"+formFields[i].name+"']").closest(".form-group").removeClass("has-error");
           }
        });
        if(url.indexOf("edit") > 0){
            validForm = true;
        }
        if(validForm == true){
            var ajaxData = {};
            ajaxData['productIds'] = productIds;
            if($("#quotationMaterialTable").length > 0){
                $("#quotationMaterialTable input:not([type='checkbox']),#quotationMaterialTable select").each(function(){
                    ajaxData[$(this).attr('name')] = $(this).val();
                });
            }
            if(url.indexOf("edit") > 0){
                ajaxData['quotation_id'] = $("#quotationId").val();
            }
            $.ajax({
                url: '/quotation/get-materials',
                async: false,
                type: "POST",
                data: ajaxData,
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

    $("#clientId").on('change', function(){
        var clientId = $(this).val();
        if(clientId == ""){
            $('#projectId').prop('disabled', true);
            $('#projectId').html('');
            $('#projectSiteId').prop('disabled', true);
            $('#projectSiteId').html('');
        }else{
            $.ajax({
                url: '/quotation/get-projects',
                type: 'POST',
                async: true,
                data: {
                    _token: $("input[name='_token']").val(),
                    client_id: clientId
                },
                success: function(data,textStatus,xhr){
                    $('#projectId').html(data);
                    $('#projectId').prop('disabled', false);
                    var projectId = $("#projectId").val();
                    getProjectSites(projectId);
                },
                error: function(){

                }
            });
        }

    });

    $("#projectId").on('change', function(){
        var projectId = $(this).val();
        getProjectSites(projectId);
    });

    $("#discount").on('keyup change', function(){
        var discount = $(this).val();
        $(".product-amount").each(function(){
            var discountAmount = parseFloat($(this).val())*(discount/100);
            var discountedAmount = parseFloat($(this).val())-discountAmount;
            $(this).closest("td").next().find('input[type="text"]').val(Math.round(discountedAmount * 1000) / 1000);
        });
        calculateProductSubtotal();
    });
});

function backToGeneral(){
    var productIds = [];
    $(".quotation-product").each(function(){
        productIds.push($(this).val());
    });
    var formData = {};
    formData['product_ids'] = productIds;
    if($("#quotationMaterialTable").length > 0){
        $("#quotationMaterialTable input:not([type='checkbox']),#quotationMaterialTable select").each(function(){
            formData[$(this).attr('name')] = $(this).val();
        });
        if($("#quotationMaterialTable input:checkbox:checked").length > 0){
            var clientSuppliedMaterials = [];
            $("#quotationMaterialTable input:checkbox:checked").each(function(){
                clientSuppliedMaterials.push($(this).val());
            });
            formData['clientSuppliedMaterial'] = clientSuppliedMaterials;
        }
    }
    if($(".profit-margin-table").length > 0){
        $(".profit-margin-table input").each(function(){
            formData[$(this).attr('name')] = $(this).val();
        });
    }
    var url = window.location.href;
    if(url.indexOf("edit") > 0){
        formData['quotation_id'] = $("#quotationId").val();
    }
    $.ajax({
        url: '/quotation/get-product-calculations',
        type: 'POST',
        async: false,
        data: formData,
        success: function(data,textStatus,xhr){
            $.each(data.amount, function(id,value){
                console.log("input[name='product_rate["+id+"]']");
                $("input[name='product_rate["+id+"]']").val(value);
                var row = $("input[name='product_rate["+id+"]']").closest("tr").attr('id');
                var rowNumber = row.match(/\d+/)[0];
                calculateAmount(rowNumber);
            });
            $("#ProfitMarginsTab").removeClass('active');
            $("#MaterialsTab").removeClass('active');
            $("#GeneralTab").addClass('active');
        },
        error: function(){
            alert("something went wrong!!")
        }
    });
}

function backToMaterials(){
    $("#materialCosts").trigger('click');
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

function getProjectSites(projectId){
    $.ajax({
        url: '/quotation/get-project-sites',
        type: 'POST',
        async: true,
        data: {
            _token: $("input[name='_token']").val(),
            project_id: projectId
        },
        success: function(data,textStatus,xhr){
            if(data.length > 0){
                $('#projectSiteId').html(data);
                $('#projectSiteId').prop('disabled', false);
            }
        },
        error: function(){

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
            $("#productQuantity"+rowNumber).prop('readonly', false);
            $("#productUnit"+rowNumber).val(data.unit);
            $("#productUnit"+rowNumber).attr('name','product_unit['+data.id+']');
            $("#productQuantity"+rowNumber).attr('name','product_quantity['+data.id+']');
            $("#productAmount"+rowNumber).attr('name','product_amount['+data.id+']');
            calculateAmount(rowNumber);
        },
        error: function(errorStatus, xhr){

        }
    });
}

function removeRow(row){
    $("#Row"+row).remove();
    var url = window.location.href;
    if(url.indexOf("edit") > 0){
        calculateSubtotal();
    }
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
    calculateSubtotal();
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
    var url = window.location.href;
    if(url.indexOf("edit") > 0){
        validForm = true;
    }
    if(validForm == true){
        var productIds = [];
        $(".quotation-product").each(function(){
            productIds.push($(this).val());
        });
        var data = {};
        data['product_ids'] = productIds;
        if($(".profit-margin-table").length > 0){
            $(".profit-margin-table input").each(function(){
                data[$(this).attr('name')] = $(this).val();
            });
        }

        if(url.indexOf("edit") > 0){
            data['quotation_id'] = $("#quotationId").val();
        }
        $.ajax({
            url: '/quotation/get-profit-margins',
            async: false,
            type: "POST",
            data: data,
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

function viewProduct(row){
    var productId = $('#productSelect'+row).val();
    $.ajax({
        url:'/product/edit/'+productId,
        type: "GET",
        async: false,
        success: function(data, textStatus, xhr){
            $("#productView .modal-body").html(data);
            calculateProductSubtotal();
            $("#productView").modal('show');
        },
        error: function(){

        }
    });
}

function calculateProductSubtotal(){
    var subtotal = 0;
    $(".product-discount-amount").each(function(){
        subtotal = subtotal + parseFloat($(this).val());
    });
    $("#subtotal").text(Math.round(subtotal * 1000) / 1000);

    var total = subtotal;
    $(".profit-margin-percentage").each(function(){
        var percentage = parseFloat($(this).text());
        var amount = subtotal * (percentage/100);
        $(this).next().text(Math.round(amount * 1000) / 1000);
        total = total + amount;
    });
    $("#total").text(Math.round(total * 1000) / 1000);
}

function calculateSubtotal(){
    var url = window.location.href;
    if(url.indexOf("edit") > 0){
        calculateProductSubtotal();
        $("#discount").trigger("change");
    }else{
        var subtotal = 0;
        $(".product-amount").each(function(){
            subtotal = subtotal+parseFloat($(this).val());
        });
        $("#subtotal").text(subtotal);
    }
}