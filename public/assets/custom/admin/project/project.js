/**
 * Created by Ameya Joshi on 14/6/17.
 */
var  CreateProject = function () {
    var handleCreate = function() {
        var form = $('#createProject');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                project_name: {
                    required: true,
                    remote: {
                        url: "/project/check-name",
                        type: "POST",
                        data: {
                            _token: function(){
                                return $("input[name='_token']").val();
                            },
                            name: function() {
                                return $( "#projectName" ).val();
                            }
                        }
                    }
                },
                project_site_name: {
                    required: true
                },
                client_id: {
                    required: true
                },
                address: {
                    required: true
                }
            },

            messages: {
                project_name: {
                    required: "Project is required.",
                    remote: "Project already exists."
                },
                project_site_name:{
                    required: "Project site is required."
                },
                client_id:{
                    required: "Client is required."
                },
                address:{
                    required: "Address is required."
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

var  EditProject = function () {
    var handleEdit = function() {
        var form = $('#createProject');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                project_name: {
                    required: true,
                    remote: {
                        url: "/project/check-name",
                        type: "POST",
                        data: {
                            _token: function(){
                                return $("input[name='_token']").val();
                            },
                            project_id: function(){
                                return $("#projectId").val();
                            },
                            name: function() {
                                return $( "#projectName" ).val();
                            }
                        }
                    }
                },
                project_site_name: {
                    required: true
                },
                client_id: {
                    required: true
                },
                address: {
                    required: true
                }
            },

            messages: {
                project_name: {
                    required: "Project is required.",
                    remote: "Project already exists."
                },
                project_site_name:{
                    required: "Project site is required."
                },
                client_id:{
                    required: "Client is required."
                },
                address:{
                    required: "Address is required."
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
    };
}();