/**
 * Created by Ameya Joshi on 26/12/17.
 */

var  CreateMaterialRequest = function () {
    var handleCreate = function() {
        var form = $('#new_material_request');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        $.validator.addMethod("regex", function(value, element, regexpr) {
            return regexpr.test(value);
        });
        $.validator.addMethod("customPattern", function(value, element) {
            return (this.optional(element) ||  /^[^$!@#]*$/ .test(value));
        }, "The field may not contain special characters like $ ! @ #");
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                user_name: {
                    required: true
                },
                user_id:{
                    requried: true
                }
            },
            messages: {
                user_name:{
                    required: 'Please select the user.'
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
                if($("#Materialrows tr").length > 0 || $("#Assetrows tr").length > 0 ){
                    $("button[type='submit']").prop('disabled', true);
                    success.show();
                    error.hide();
                    form.submit();
                }else{
                    alert('Please enter valid data');
                }
            }
        });
    }
    return {
        init: function () {
            handleCreate();
        }
    };
}();

var  CreatePurchaseRequest = function () {
    var handleCreate = function() {
        var form = $('#new_purchase_request');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                user_name: {
                    required: true
                },
                user_id:{
                    requried: true
                }
            },
            messages: {
                user_name:{
                    required: 'Please select the user.'
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
                if($("#Materialrows tr").length > 0 || $("#Assetrows tr").length > 0 || $("#indentRows tr").length > 0){
                    $("button[type='submit']").prop('disabled', true);
                    success.show();
                    error.hide();
                    form.submit();
                }else{
                    alert('Please enter valid data');
                }
            }
        });
    }
    return {
        init: function () {
            handleCreate();
        }
    };
}();