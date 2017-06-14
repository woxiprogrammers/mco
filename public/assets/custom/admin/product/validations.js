var  CreateProduct = function () {
    var handleCreate = function() {
        var form = $('#create-product');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                name: {
                    required: true,
                    remote:{
                        url: "/product/check-name",
                        type: "POST",
                        data: {
                            product_name: function() {
                                return $("#name" ).val();
                            },
                            _token: function() {
                                return $("input[name='_token']").val();
                            }
                        }
                    }
                },
                description: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Product name is required.",
                    remote: "Product name Already exists."
                },
                description: {
                    required: "Description is required."
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

var  EditProduct = function () {
    var handleEdit = function() {
        var form = $('#edit-product');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                name: {
                    required: true,
                    remote:{
                        url: "/product/check-name",
                        type: "POST",
                        data: {
                            product_name: function() {
                                return $("#name" ).val();
                            },
                            product_id: function() {
                                return $("#productId").val();
                            },
                            _token: function() {
                                return $("input[name='_token']").val();
                            }


                        }
                    }
                },
                description: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Product name is required.",
                    remote: "Product name Already exists."
                },
                description: {
                    required: "Description is required."
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
