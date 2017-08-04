/**
 * Created by Ameya Joshi on 5/6/17.
 */


$(document).ready(function(){
    $.getScript('/assets/custom/admin/product/product.js');
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
        $(this).css('pointer-events','none');
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
                $("#productTable tr:first").after(data);
                $('#productRowCount').val(parseInt(rowCount)+1);
                $("#addProduct").css('pointer-events','');
            },
            error: function(errorStatus, xhr){

            }
        });
    });

    $("#materialCosts").on('click', function(e){
        e.stopPropagation();
        if($(".quotation-product").length > 0){
            var productIds = [];
            $(".quotation-product").each(function(){
                productIds.push($(this).val());
            });
            var validForm = true;
            var url = window.location.href;
            if(url.indexOf("edit") <= 0){
                var formFields = $("#QuotationCreateForm").serializeArray();
                $.each(formFields, function(i){
                    if(($.trim(formFields[i].value)) == ""){
                        $("[name='"+formFields[i].name+"']").closest(".form-group").addClass("has-error");
                        validForm = false;
                    }else{
                        $("[name='"+formFields[i].name+"']").closest(".form-group").removeClass("has-error");
                    }
                });
            }else{
                var formFields = $("#productTable :input").serializeArray();
                $.each(formFields, function(i){
                    if(($.trim(formFields[i].value)) == ""){
                        $("[name='"+formFields[i].name+"']").closest(".form-group").addClass("has-error");
                        validForm = false;
                    }else{
                        $("[name='"+formFields[i].name+"']").closest(".form-group").removeClass("has-error");
                    }
                });
            }
            if(validForm == true){
                var ajaxData = {};
                ajaxData['productIds'] = productIds;
                if($("#quotationMaterialTable").length > 0){
                    $("#quotationMaterialTable input:not([type='checkbox']),#quotationMaterialTable select").each(function(){
                        ajaxData[$(this).attr('name')] = $(this).val();
                    });
                    var clientSuppliedMaterials = [];
                    $("#quotationMaterialTable input:checkbox:checked").each(function(){
                        clientSuppliedMaterials.push($(this).val());
                    });
                    ajaxData['clientSuppliedMaterial'] = clientSuppliedMaterials;
                }
                if(url.indexOf("edit") > 0){
                    ajaxData['quotation_id'] = $("#quotationId").val();
                }
                var quotationId = $("#quotationId").val();
                if(typeof quotationId != 'undefined'){
                    ajaxData['quotation_id'] = $("#quotationId").val();
                }
                $.ajax({
                    url: '/quotation/get-materials',
                    async: false,
                    type: "POST",
                    data: ajaxData,
                    success: function(data, textStatus, xhr){
                        $("#MaterialsTab").html(data);
                        setTimeout(function () {
                            $("#GeneralTab").removeClass('active');
                            $("#ProfitMarginsTab").removeClass('active');
                            $("#MaterialsTab").addClass('active');
                        },2000);
                    },
                    error: function(errorStatus, data){

                    }
                });
            }
        }else{
            alert("Please add atleast one product");
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

    $("#generalTabSubmit").on('click',function(e){
        e.stopPropagation();
        $("#materialCosts").trigger('click');
    });

    $("#formSubmit").on('click',function(e) {
        $(this).prop('disabled', true);
        $("#QuotationEditForm, #QuotationCreateForm").ajaxSubmit(function () {
            window.location.reload();
        });
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
            if(xhr.status == 201){
                location.reload();
            }
            $.each(data.amount, function(id,value){
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
            }else{
                $('#projectSiteId').html("");
                $('#projectSiteId').prop('disabled', true);
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
            $("#productRate"+rowNumber).val(Math.round(data.rate_per_unit * 1000) / 1000);
            $("#productRate"+rowNumber).attr('name','product_rate['+data.id+']');
            $("#productQuantity"+rowNumber).prop('readonly', false);
            $("#productUnit"+rowNumber).val(data.unit);
            $("#productUnit"+rowNumber).attr('name','product_unit['+data.id+']');
            $("#productQuantity"+rowNumber).attr('name','product_quantity['+data.id+']');
            $("#productAmount"+rowNumber).attr('name','product_amount['+data.id+']');
            var url = window.location.href;
            if(url.indexOf('edit') > 0){
                $("#productSummary"+rowNumber).attr('name','product_summary['+data.id+']');
                $("#productDiscountAmount"+rowNumber).attr('name','product_discount_amount['+data.id+']');
            }
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
        $("#productAmount"+row).val(Math.round(amount * 1000) / 1000);
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
    $("#formSubmit").hide();
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
        var quotationId = $("#quotationId").val();
        if(typeof quotationId != 'undefined'){
            data['quotation_id'] = $("#quotationId").val();
        }
        $.ajax({
            url: '/quotation/get-profit-margins',
            async: false,
            type: "POST",
            data: data,
            success: function(data, textStatus, xhr){
                $("#profitMarginTable").html(data);
                setTimeout(function(){
                    $("#formSubmit").show();
                    $("#GeneralTab").removeClass('active');
                    $("#MaterialsTab").removeClass('active');
                    $("#ProfitMarginsTab").addClass('active');
                },2000);
            },
            error: function(errorStatus, data){

            }
        });
    }


    /*$("#QuotationEditForm,#QuotationCreateForm").on('submit',function (e) {
        e.stopPropagation();
        $("#QuotationEditForm button[type='submit'],#QuotationCreateForm button[type='submit']").css('display','none');
        $(this).ajaxSubmit();
    });*/
}

/*function onSubmitClick(){
    console.log('in ob click');
    $("#next2").css('display','none');
}*/
function viewProduct(row){
    var productId = $('#productSelect'+row).val();
    var quotationId = $("#quotationId").val();
    if(typeof quotationId != 'undefined'){
        $.ajax({
            url:'/quotation/get-quotation-product-view',
            type: "POST",
            async: false,
            data: {
                _token: $('input[name="_token"]').val(),
                quotation_id: quotationId,
                product_id: productId
            },
            success: function(data, textStatus, xhr){
                $("#productView .modal-body").html(data);
                $("#productView").modal('show');
                calucalateProductViewTotal();
            },
            error: function(){

            }
        });
    }else{
        $.ajax({
            url:'/product/edit/'+productId,
            type: "GET",
            async: false,
            success: function(data, textStatus, xhr){
                $("#productView .modal-body").html(data);
                $("#productView").modal('show');
                calucalateProductViewTotal();
            },
            error: function(){

            }
        });
    }


}

function calculateProductSubtotal(){
    var subtotal = 0;
    $(".product-discount-amount").each(function(){
        subtotal = subtotal + parseFloat($(this).val());
    });
    $("#subtotal").val(Math.round(subtotal * 1000) / 1000);

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
        $("#subtotal").val(Math.round(subtotal * 1000) / 1000);
    }
}

function calucalateProductViewTotal(){
    var subtotal = 0;
    $(".material_amount").each(function(){
        subtotal = subtotal + parseFloat($(this).val());
    });
    $("#productViewSubtotal").text(Math.round(subtotal * 1000) / 1000);
    var total = subtotal;
    $(".profit-margin").each(function(){
        var profitMarginAmount = subtotal * ($(this).val() / 100);
        total = total + profitMarginAmount;
        $(this).parent().next().text(Math.round(profitMarginAmount * 1000) / 1000);
    });
    $("#productViewTotal").text(Math.round(total * 1000) / 1000);
}

function convertUnit(materialId,fromUnit){
    var newUnit = $("#materialUnit"+materialId).val();
    var rate = $("#materialRate"+materialId).val();
    var data = {
        current_unit: fromUnit,
        rate: rate,
        new_unit: newUnit,
        material_id:materialId,
        _token: $("input[name='_token']").val()
    };
    $.ajax({
        url: '/units/convert',
        type: 'POST',
        async: false,
        data: data,
        success: function(data,textStatus,xhr){
            if(xhr.status == 200){
                $("#materialRate"+materialId).val(Math.round(data.rate * 1000) / 1000);
            }else{
                $("#materialUnit"+materialId+" option[value='"+data.unit+"']").prop('selected', true);
                $("#materialRate"+materialId).val(Math.round(data.rate * 1000) / 1000);
            }
        },
        error: function(data, textStatus, xhr){
            alert("Something went wrong");
        }
    });

}

function openDisapproveModal(){
    $("#disapproveModal").modal('show');
}

function submitProductEdit(){
    $("#productViewProjectSiteId").val($('#projectSiteId').val());
    var productId = $("#quotationProductViewId").val();
    var productQuantity = $("input[name='product_quantity["+productId+"]']").val();
    if(productQuantity == ""){
        productQuantity = 0;
    }
    $("#quotationProductQuantity").val(productQuantity);
    var formData = $("#editProductForm").serialize();
    var url = window.location.href;
    var quotationId = $("#quotationId").val();
    if(typeof quotationId != 'undefined'){
        formData = formData + '&quotation_id=' + quotationId;
    }
    $.ajax({
        url: '/quotation/create',
        async: false,
        type: 'POST',
        data: formData,
        success: function(data,textStatus, xhr){
            $("input[name='product_description["+data.product_id+"]']").val(data.product_description);
            $("input[name='product_rate["+data.product_id+"]']").val(Math.round(data.product_amount * 1000) / 1000);
            var rowId = $("input[name='product_rate["+data.product_id+"]']").closest('tr').attr('id');
            var rowNumber = rowId.match(/\d+/)[0];
            calculateAmount(rowNumber);
            var quotationId = $("#quotationId").val();
            if(typeof quotationId == 'undefined'){
                $("<input/>",{
                    name: 'quotation_id',
                    id:'quotationId',
                    value: data.quotation_id,
                    type: 'hidden'
                }).appendTo("#QuotationCreateForm")
            }else{
                $("#quotationId").val(data.quotation_id);
            }
            alert('Product Edited Successfully');
        },
        error: function(data){
            alert('Something went wrong');
        }
    });
}