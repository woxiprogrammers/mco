var  AddPayment = function () {
    var add_payment = function() {
        var form = $('#add_payment_form');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                bank_id: {
                    required: true
                },
                amount: {
                    required: true
                },
                reference_number: {
                    required: true
                },
                remark: {
                    required: true,
                }
            },

            messages: {
                amount: {
                    required: "Amount is required."
                },
                reference_number: {
                    required: "Reference number is required."
                },
                remark: {
                    required: "Remark is required."
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
    };
    return {
        init: function () {
            add_payment();
        }
    };
    var add_transaction = function() {
        var form = $('#add_transaction_form');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                material: {
                    required: true
                },
                quantity: {
                    required: true
                },
                vendor_name: {
                    required: true,
                },
                bill_number: {
                    required: true,
                },
                bill_amount: {
                    required: true,
                },
                vehicle_number: {
                    required: true,
                }
            },

            messages: {
                amount: {
                    material: "Material is required."
                },
                quantity: {
                    required: "Quantity is required."
                },
                vendor_name: {
                    required: "Vendor Name is required."
                },
                bill_number: {
                    required: "Bill number is required."
                },
                bill_amount: {
                    required: "Bill amount is required."
                },
                vehicle_number: {
                    required: "Vehicle is required."
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
    };
    return {
        init: function () {
            add_transaction();
        }
    }
}();
AddPayment.init();

var  EditPurchaseOrder = function () {
    var editPurchaseOrder = function() {
        var form = $('#PurchaseOrderComponentEditForm');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {

            },
            messages: {

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
    };
    return {
        init: function () {
            editPurchaseOrder();
        }
    };
}();
