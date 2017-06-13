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
                },
                'project_site': {
                    required: true,
                    /*remote: {
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
                    }*/
                },
                'product_rate[]': {
                    required: true
                },
                'product_quantity[]': {
                    required: true
                }
            },

            messages: {
                'client_id': {
                    required: "Please select Client."
                },
                'project': {
                    required: "Project name is required.",
                },
                'project_site': {
                    required: "Project Site name is required.",
//                    remote: "Quotation for this site is already created."
                },
                'product_rate[]': {
                    required: "Please enter Rate."
                },
                'product_quantity[]': {
                    required: "Please enter quantity."
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit
                success.hide();
                error.show();
//                $("#next1").removeAttr('onclick');
            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
//                $("#next1").removeAttr('onclick');
            },

            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
//                $("#next1").attr('onclick',"getAllMaterials()");

            },

            success: function (label) {
                label
                    .closest('.form-group').addClass('has-success');
//                $("#next1").attr('onclick',"getAllMaterials()");
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