var  ProfitMargin = function () {
  var handleCreate = function() {
        var form = $('#create-profitMargin');
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
                percentage:{
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Name is required."
                },
                percentage:{
                    required: "Percentage required."
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
            }
        });
    }
    return {
        init: function () {
            handleCreate();
        }
    };
}();
