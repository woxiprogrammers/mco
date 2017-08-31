var  CreateRole = function () {
    var handleCreate = function() {
        var form = $('#create-role');
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
                        url: "/role/check-name",
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
                }
            },

            messages: {
                name: {
                    required: "Name is required.",
                    remote: "Name already exists."
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



var  EditRole = function () {
    var handleEdit = function() {
        var form = $('#edit-role');
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
                        url: "/role/check-name",
                        type: "POST",
                        data: {
                            category_id: function(){
                                return $("#role_id").val();
                            },
                            _token: function(){
                                return $("input[name='_token']").val();
                            },
                            name: function() {
                                return $( "#name" ).val();
                            }
                        }
                    }
                }
            },

            messages: {
                name: {
                    required: "Name is required.",
                    remote: "Name already exists."
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

function getModules(role) {
    $.ajax({
        url: '/role/get-modules/' + role,
        type: 'GET',
        async: false,
        success: function (data, textStatus, xhr) {
            if (xhr.status == 200) {
                $("#material_id").html(data);
                $("#roleModulesTable input[type='number']").each(function () {
                    $(this).rules('add', {
                        required: true
                    });
                });

            } else {

            }
        },
        error: function (errorStatus, xhr) {

        }
    });
}

$("#next_btn").on('click',function(){
    if($("#module_id input:checkbox:checked").length > 0){
        getSubModules();
        $(".submodules-table-div").show();
    }
});

function getSubModules() {
    var module_id = [];
    var formData = {};
    formData['_token'] = $("input[name='_token']").val();
    $("#module_id input:checkbox:checked").each(function(i){
        module_id[i] = $(this).val();
    });
    formData['module_id'] = module_id;
    if($(".product-material-id").length > 0){
        formData['modules'] = {};
        $(".product-material-id").each(function(i){
            var module_id = $(this).val();
            formData['modules'][module_id] = {};
            formData['modules'][module_id]['id'] = module_id;
            formData['modules'][module_id]['module_id'] = $("#module_"+module_id+"_module_id").val();
            formData['modules'][module_id]['is_web'] = $("#module_"+module_id+"_is_web").val();
            formData['modules'][module_id]['is_mobile'] = $("#module_"+module_id+"_is_mobile").val();

        });
    }
    $.ajax({
        type: "POST",
        url: "/role/module/listing",
        data :formData,
        async: false,

        success: function(data,textStatus, xhr){
            $("#SubModulesTable").html(data);
        },
        error : function(errorStatus, xhr){
            alert('error');
        }
    });
}



