var  CreateUser = function () {
    var handleCreate = function() {
        var form = $('#create-user');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                first_name: {
                    required: true
                },
                last_name: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                mobile: {
                    required: true
                },
                password: {
                    required: true,
                    minlength: 6,
                    maxlength:20
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    maxlength:20,
                    equalTo: "#password"
                }
            },

            messages: {
                first_name: {
                    required: "First name is required."
                },
                last_name: {
                    required: "Last name is required."
                },
                email: {
                    required: "Email is required."
                },
                mobile: {
                    required: "Contact number is required."
                },
                password:{
                  required: "Password is required"
                },
                confirm_password: {
                    required: "Confirm password is required.",
                    equalTo: "Please re-enter the same password again."
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

var  EditUser = function () {
    var handleEdit = function() {
        var form = $('#edit-user');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                first_name: {
                    required: true
                },
                last_name: {
                    required: true
                },
                mobile: {
                    required: true
                }
            },

            messages: {
                first_name: {
                    required: "First name is required."
                },
                last_name: {
                    required: "Last name is required."
                },
                mobile: {
                    required: "Contact number is required."
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