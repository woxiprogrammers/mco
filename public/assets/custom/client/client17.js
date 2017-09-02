var  CreateClient = function () {
    var handleCreate = function() {
        var form = $('#create-client');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                company: {
                    required: true
                },
                address: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                mobile: {
                    required: true
                },
                gstin: {
                    maxlength:15,
                    minlength:15
                }
            },

            messages: {
                company: {
                    required: "Company is required."
                },
                address: {
                    required: "Address is required."
                },
                email: {
                    required: "Email is required."
                },
                mobile: {
                    required: "Contact number is required."
                },
                /*gstin:{
                    maxlength: "Your GSTIN must be at least 15 characters long"
                }*/
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

var  EditClient = function () {
    var handleEdit = function() {
        var form = $('#edit-client');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                company: {
                    required: true
                },
                address: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                mobile: {
                    required: true
                },
                gstin: {
                    maxlength:15,
                    minlength:15
                }
            },

            messages: {
                company: {
                    required: "Company is required."
                },
                address: {
                    required: "Address is required."
                },
                email: {
                    required: "Email is required."
                },
                mobile: {
                    required: "Contact number is required."
                },
                /*gstin:{
                    maxlength: "Your GSTIN must be at least 15 characters long"
                }*/
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
