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
                    required: true,
                    remote: {
                        url: "/quotation/check-project-name",
                        type: "POST",
                        data: {
                            _token: function(){
                                return $("input[name='_token']").val();
                            },
                            name: function() {
                                return $( "#project" ).val();
                            }
                        }
                    }
                },
                'project_site': {
                    required: true,
                    remote: {
                        url: "/quotation/check-project-site-name",
                        type: "POST",
                        data: {
                            _token: function(){
                                return $("input[name='_token']").val();
                            },
                            name: function() {
                                return $( "#project_site" ).val();
                            }
                        }
                    }
                }
            },

            messages: {
                'client_id': {
                    required: "Please select Client."
                },
                'project': {
                    required: "Project name is required.",
                    remote: "This project already exists."
                },
                'project_site': {
                    required: "Project Site name is required.",
                    remote: "Quotation for this site is already created."
                },
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