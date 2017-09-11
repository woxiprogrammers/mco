var  CreateBank = function () {
    var handleCreate = function() {
        var form = $('#create-bank');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                bank_name: {
                    required: true
                },
                account_number: {
                    required: true
                },
                ifs_code: {
                    required: true
                },
                branch_id: {
                    required: true
                },
                branch_name: {
                    required: true
                }
            },

            messages: {
                bank_name: {
                    required: "Bank name is required."
                },
                account_number: {
                    required: "Account number is required."
                },
                ifs_code: {
                    required: "IFS code is required."
                },
                branch_id: {
                    required: "Branch ID is required."
                },
                branch_name: {
                    required: "Branch name is required."
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

var  EditBank = function () {
    var handleCreate = function() {
        var form = $('#create-bank');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                bank_name: {
                    required: true
                },
                account_number: {
                    required: true
                },
                ifs_code: {
                    required: true
                },
                branch_id: {
                    required: true
                },
                branch_name: {
                    required: true
                }
            },

            messages: {
                bank_name: {
                    required: "Bank name is required."
                },
                account_number: {
                    required: "Account number is required."
                },
                ifs_code: {
                    required: "IFS code is required."
                },
                branch_id: {
                    required: "Branch ID is required."
                },
                branch_name: {
                    required: "Branch name is required."
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

