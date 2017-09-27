var  CreateAsset = function () {
    var handleCreate = function() {
        var form = $('#create-asset');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                model_number: {
                    required: true
                },
                name:{
                    required: true
                },
                expiry_date:{
                    required: true,

                },
                price:{
                    required: true
                },
                is_fuel_dependent:{
                    required: true
                },
                litre_per_unit:{
                    required: true
                }
            },
            messages: {
                model_number: {
                    required: "Model Number is required."
                },
                name:{
                    required: "Asset Name is required."
                },
                expiry_date:{
                    required: "Expiry date is required."
                },
                price:{
                    required: "Price is required."
                },
                is_fuel_dependent:{
                    required: "Is it diesel is required."
                },
                litre_per_unit:{
                    required: "Litre Per Unit is required."
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

var  EditAsset = function () {
    var handleEdit = function() {
        var form = $('#edit-asset');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                model_number: {
                    required: true
                },
                name:{
                    required: true
                },
                expiry_date:{
                    required: true,

                },
                price:{
                    required: true
                },
                is_fuel_dependent:{
                    required: true
                },
                litre_per_unit:{
                    required: true
                }
            },
            messages: {
                model_number: {
                    required: "Model Number is required."
                },
                name:{
                    required: "Asset Name is required."
                },
                expiry_date:{
                    required: "Expiry date is required."
                },
                price:{
                    required: "Price is required."
                },
                is_fuel_dependent:{
                    required: "Is it diesel is required."
                },
                litre_per_unit:{
                    required: "Litre Per Unit is required."
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
    };
}();
