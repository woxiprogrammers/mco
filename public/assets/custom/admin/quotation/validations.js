
/**
 * Created by Ameya Joshi on 13/6/17.
 */


var  CreateQuotation = function () {
    var handleCreate = function() {
        var form = $('#QuotationCreateForm');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                'client_id': {
                    required: true
                },
                'project': {
                    required: true
                },
                'project_site': {
                    required: true
                }
            },

            messages: {
                'client_id': {
                    required: "Please select Client."
                },
                'project': {
                    required: "Project name is required."
                },
                'project_site': {
                    required: "Project Site name is required."
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
                var validForm = true;
                var formFields = $("#QuotationCreateForm").serializeArray();
                $.each(formFields, function(i) {
                    if (($.trim(formFields[i].value)) == "") {
                        $("[name='" + formFields[i].name + "']").closest(".form-group").addClass("has-error");
                        validForm = false;
                    } else {
                        $("[name='" + formFields[i].name + "']").closest(".form-group").removeClass("has-error");
                    }
                });
                if(validForm == true){

                        $("button[type='submit']").attr('disabled', true);
                        success.show();
                        error.hide();
                        form.submit();
                    }
                }

        });
    }

    return {
        init: function () {
            handleCreate();
        }
    };
}();


var  EditQuotation = function () {
    var handleEdit = function() {
        var form = $('#QuotationEditForm');
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
                $("button[type='submit']").attr('disabled', true);
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

var  WorkOrderFrom = function () {
    var handleCreate = function() {
        var form = $('#WorkOrderForm');
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
                if (confirm("Please confirm work order details and extra items' costs.") == true) {
                    $("button[type='submit']").attr('disabled', true);
                    form.submit();
                }
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

var  CreateExtraItem = function () {
    var handleCreate = function() {
        var form = $('#create-extra-item');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                name: {
                    required: true,
                    remote: {
                        url: "/extra-item/check-name",
                        type: "POST",
                        data: {
                            _token: function(){
                                return $("input[name='_token']").val();
                            },
                            name: function() {
                                return $( "#name" ).val();
                            }
                        }
                    }
                },
                rate : {
                    required : true
                }
            },

            messages: {
                name: {
                    required: "Extra Item name is required.",
                    remote: "Extra Item already exists."
                },
                rate: {
                    required: "Rate is required."
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
                var name = $(form).find("input[name='name']").val();
                var rate = $(form).find("input[name='rate']").val();
                var count = $("#newExtraItemSection").children().length;
                count++;
                $("#newExtraItemSection").append('<div class="form-group">\n' +
                    '<div class="col-md-3">\n' +
                    '    <label class="control-label pull-right"><input type="hidden" name="new_extra_item['+count+'][name]"value="'+name+'">\n' +
                    name +
                    '    </label>\n' +
                    '</div>\n' +
                    '<div class="col-md-5">\n' +
                    '      <input type="text" class="form-control extra_items" name="new_extra_item['+count+'][rate]" value="'+rate+'">\n' +
                    '</div>\n' +
                    '<div class="col-md-1">\n' +
                    '    <a class="btn red btn-xs" href="javascript:void(0);" onclick="removeExtraItem(this)">\n' +
                    '<i class="fa fa-times"></i>\n' +
                    '</a>' +
                    '</div>\n' +
                    '</div>');
                $(form).find("input[name='name']").val('');
                $(form).find("input[name='rate']").val('');
                $("#extraItemModal").modal("toggle");
            }
        });
    }

    return {
        init: function () {
            handleCreate();
        }
    };
}();

function removeExtraItem(element){
    $(element).closest('.form-group').remove();
}