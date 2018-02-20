var  CreateLabour = function () {
    var handleCreate = function() {
        var form = $('#create-labour');
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
                employee_id: {
                    required: true
                },
                per_day_wages: {
                    required: true
                },
                email: {
                    email: true
                }
            },

            messages: {
                name: {
                    required: "Name is required."
                },
                employee_id:{
                    required: "Labour ID is required."
                },
                per_day_wages:{
                    required: "Per day wages is required."
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
    };

    return {
        init: function () {
            handleCreate();
        }
    };
}();

var  EditLabour = function () {
    var handleEdit = function() {
        var form = $('#editLabour');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                name: {
                    required: true,
                },
                employee_id: {
                    required: true
                },
                per_day_wages: {
                    required: true
                },
                email: {
                    email: true
                }
            },

            messages: {
                name: {
                    required: "Name is required."
                },
                employee_id:{
                    required: "Labour ID is required."
                },
                per_day_wages:{
                    required: "Per day wages is required."
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
    };

    return {
        init: function () {
            handleEdit();
        }
    };
}();