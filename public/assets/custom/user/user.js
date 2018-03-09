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
                    email: true,
                    remote:{
                        url: "/user/check-email",
                        type: "POST",
                        data: {
                            _token: function() {
                                return $("input[name='_token']").val();
                            },
                            email: function() {
                                return $("#email").val();
                            }
                        }
                    }
                },
                mobile: {
                    required: true,
                    digits: true,
                    maxlength: 10,
                    minlength: 10,
                    remote: {
                        url: "/user/check-mobile",
                        type: "POST",
                        data: {
                            _token: function() {
                                return $("input[name='_token']").val();
                            },
                            mobile: function() {
                                return $( "#mobile" ).val();
                            }
                        }
                    }
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
                    required: "Email is required.",
                    remote: "Email is already registered."
                },
                mobile: {
                    required: "Contact number is required.",
                    digits: "Only numbers are valid.",
                    remote: 'This mobile is registered already'
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
                email: {
                    required: true,
                    email: true,
                    remote:{
                        url: "/user/check-email",
                        type: "POST",
                        data: {
                            _token: function() {
                                return $("input[name='_token']").val();
                            },
                            email: function() {
                                return $("#email").val();
                            }
                        }
                    }
                },
                mobile: {
                    required: true,
                    digits: true,
                    maxlength: 10,
                    minlength: 10,
                    remote: {
                        url: "/user/check-mobile",
                        type: "POST",
                        data: {
                            _token: function() {
                                return $("input[name='_token']").val();
                            },
                            mobile: function() {
                                return $( "#mobile" ).val();
                            },
                            user_id: function(){
                                return $("#user_id").val();
                            }
                        }
                    }
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
                    required: "Contact number is required.",
                    digits: "Only numbers are valid.",
                    remote: 'This mobile is registered already with other user'
                },
                email: {
                    required: "Email is required.",
                    remote: "Email is already registered."
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

$('#role_id').on('change',function () {
    $.ajax({
        url: '/user/get-permission',
        type: 'POST',
        async: false,
        data :{
            'role_id' : $('#role_id').val()
        },
        success: function(data,textStatus,xhr){
            if(xhr.status == 200){
                $('#amount_limit').html(data);
            }
        },
        error: function(data, textStatus, xhr){

        }
    });
});
