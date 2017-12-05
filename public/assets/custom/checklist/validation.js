var  CreateCheckListStructure = function () {
    var handleCreate = function() {
        var form = $('#createChecklistStructure');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                main_cat: {
                    required: true
                },
                sub_cat:{
                    required: true
                },
                title_name:{
                    required: true
                },
                nochapter:{
                    required: true,
                    email: true
                },
                description:{
                    required: true
                }
            },
            messages: {
                main_cat: {
                    required: "Main Category Name is required."
                },
                sub_cat:{
                    required: "Sub Category Name is required."
                },
                title_name:{
                    required: "Title Name is required."
                },
                nochapter:{
                    required: "Number of Images is required."
                },
                description:{
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

var  EditCheckListStructure = function () {
    var handleEdit = function() {
        var form = $('#edit-vendor');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                main_cat: {
                    required: true
                },
                sub_cat:{
                    required: true
                },
                title_name:{
                    required: true
                },
                nochapter:{
                    required: true,
                    email: true
                },
                description:{
                    required: true
                }
            },
            messages: {
                main_cat: {
                    required: "Main Category Name is required."
                },
                sub_cat:{
                    required: "Sub Category Name is required."
                },
                title_name:{
                    required: "Title Name is required."
                },
                nochapter:{
                    required: "Number of Images is required."
                },
                description:{
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
    }
};


