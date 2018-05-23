/**
 * Created by Ameya Joshi on 23/3/18.
 */


var  CreateAssetMaintenanceBillPayment = function () {
    var handleCreate = function() {
        var form = $('#createBillPaymentForm');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        var maxAmount = parseFloat($("#pendingAmount").val());
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                amount:{
                    required: true,
                    min: 0.000001,
                    max: maxAmount
                },
                bank_id: {
                  required : true
                },
                payment_id:{
                    required : true
                }
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