/**
 * Created by Ameya Joshi on 16/3/18.
 */

var  CreateSubcontractorBill = function () {
    var handleCreate = function() {
        var form = $('#create_bill');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        var allowedQuantity = parseFloat($("#allowedQuantity").val());
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                qty:{
                    min: 0.000001,
                    max: allowedQuantity,
                    required: true
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

var  EditSubcontractorBill = function () {
    var handleCreate = function() {
        var form = $('#edit_bill');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        var allowedQuantity = $("#allowedQuantity").val();
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

var  CreateSubcontractorStructure = function () {
    var handleCreate = function() {
        var form = $('#createSubcontractorStructure');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                subcontractor_id:{
                    required: true
                },
                structure_type:{
                    required: true
                }
            },

            messages: {
                subcontractor_id:{
                    required: "Please select subcontractor."
                },
                structure_type:{
                    required: "Please select structure type."
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

var  EditSubcontractorStructure = function () {
    var handleCreate = function() {
        var form = $('#editSubcontractorStructure');
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

var CreateSubcontractorBills = function () {
    var handleCreate = function() {
        var form = $('#createStructureBill');
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
                var structureTypeSlug = $("#subcontractorStructureSlug").val();
                if (structureTypeSlug == 'itemwise' || structureTypeSlug == 'amountwise'){
                    if ($(".structure-summary:checked").length > 0){
                        $("button[type='submit']").prop('disabled', true);
                        success.show();
                        error.hide();
                        form.submit();
                    }else{
                        alert('Select atleast one structure summary.');
                    }
                }else{
                    $("button[type='submit']").prop('disabled', true);
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

var EditSubcontractorBills = function () {
    var handleCreate = function() {
        var form = $('#editStructureBill');
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
                var structureTypeSlug = $("#subcontractorStructureSlug").val();
                if (structureTypeSlug == 'itemwise' || structureTypeSlug == 'amountwise'){
                    if ($(".structure-summary:checked").length > 0){
                        $("button[type='submit']").prop('disabled', true);
                        success.show();
                        error.hide();
                        form.submit();
                    }else{
                        alert('Select atleast one structure summary.');
                    }
                }else{
                    $("button[type='submit']").prop('disabled', true);
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
                        '<div class="col-md-6">\n' +
                        '      <input type="text" class="form-control extra_items" name="new_extra_item['+count+'][rate]" value="'+rate+'">\n' +
                        '</div>\n' +
                        '<div class="">\n' +
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


