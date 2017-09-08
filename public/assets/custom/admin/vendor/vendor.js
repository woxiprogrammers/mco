var  CreateVendor = function () {
    var handleCreate = function() {
        var form = $('#create-vendor');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                name: {
                    required: true
                },
                company:{
                    required: true
                },
                mobile:{
                    required: true
                },
                email:{
                    required: true,
                    email: true
                },
                gstin:{
                    required: true
                },
                city:{
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Vendor Name is required."
                },
                company:{
                    required: "Company Name is required."
                },
                mobile:{
                    required: "Mobile is required."
                },
                email:{
                    required: "Email is required."
                },
                gstin:{
                    required: "GSTIN is required."
                },
                city:{
                    required: "City is required."
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                success.hide();
                error.show();
            },
            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            success: function (label) {
                label
                    .closest('.form-group').addClass('has-success');
            },
            submitHandler: function (form) {
                $("button[type='submit']").prop('disabled', true);
                success.show();
                error.hide();
                form.submit();
            }
        });
    }
    return {
        init: function () {
            handleCreate();
        }
    };
}();

var  EditVendor = function () {
    var handleEdit = function() {
        var form = $('#edit-vendor');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                name: {
                    required: true
                },
                company:{
                    required: true
                },
                mobile:{
                    required: true
                },
                email:{
                    required: true,
                    email: true
                },
                gstin:{
                    required: true
                },
                city:{
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Vendor Name is required."
                },
                company:{
                    required: "Company Name is required."
                },
                mobile:{
                    required: "Mobile is required."
                },
                email:{
                    required: "Email is required."
                },
                gstin:{
                    required: "GSTIN is required."
                },
                city:{
                    required: "City is required."
                }
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                success.hide();
                error.show();
            },
            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            success: function (label) {
                label
                    .closest('.form-group').addClass('has-success');
            },
            submitHandler: function (form) {
                $("button[type='submit']").prop('disabled', true);
                success.show();
                error.hide();
                form.submit();
            }
        });
    }
    return {
        init: function () {
            handleEdit();
        }
    }
};


function getMaterials(category){
    $.ajax({
        url: '/vendors/get-materials/'+category,
        type: 'GET',
        async: false,
        success: function(data, textStatus, xhr){
            if(xhr.status == 200){
                $("#material_id").html(data);
                $("#vendorsMaterialTable input[type='number']").each(function(){
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


$(document).ready(function(){

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

function getMaterialDetails(){
    var material_ids = [];
    var formData = {};
    formData['_token'] = $("input[name='_token']").val();
    $("#material_id input:checkbox:checked").each(function(i){
        material_ids[i] = $(this).val();
    });
    formData['material_ids'] = material_ids;
    if($(".product-material-id").length > 0){
        formData['materials'] = {};
        $(".product-material-id").each(function(i){
            var materialId = $(this).val();
            formData['materials'][materialId] = {};
            formData['materials'][materialId]['id'] = materialId;
            formData['materials'][materialId]['rate_per_unit'] = $("#material_"+materialId+"_rate").val();
            formData['materials'][materialId]['unit_id'] = $("#material_"+materialId+"_unit").val();
            formData['materials'][materialId]['quantity'] = $("#material_"+materialId+"_quantity").val();
        });
    }
    $.ajax({
        url: '/vendors/material/listing',
        type: "POST",
        data :formData,
        async: false,
        success: function(data,textStatus, xhr){
            $("#vendorMaterialTable").html(data);
            calculateSubTotal();
        },
        error: function(errorStatus, xhr){

        }
    });
}