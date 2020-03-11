var SubcontractorListing = function () {
    var handleOrders = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#subcontractorTable"),
            onSuccess: function (grid) {
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            loadingMessage: 'Loading...',
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options
                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                // So when dropdowns used the scrollable div should be removed.
                //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",

                "lengthMenu": [
                    [20, 100, 150],
                    [20, 100, 150] // change per page values here
                ],
                "pageLength": 20, // default record count per page
                "ajax": {
                    "url": "/subcontractor/listing" // ajax source
                },
                "order": [
                    [1, "asc"]
                ] // set first column as a default sort by asc
            }
        });

        // handle group actionsubmit button click
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $(".table-group-action-input", grid.getTableWrapper());
            if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
                grid.setAjaxParam("customActionType", "group_action");
                grid.setAjaxParam("customActionName", action.val());
                grid.setAjaxParam("id", grid.getSelectedRows());
                grid.getDataTable().ajax.reload();
                grid.clearAjaxParams();
            } else if (action.val() == "") {
                alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'Please select an action',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            } else if (grid.getSelectedRowsCount() === 0) {
                alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No record selected',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });

    }

    return {

        //main function to initiate the module
        init: function () {
            handleOrders();
        }

    };

}();

jQuery(document).ready(function() {
    SubcontractorListing.init();
});

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
                name: {
                    required: true
                },
                employee_id: {
                    required: true
                },
                per_day_wages: {
                    required: true
                },
                email: {
                    email: true
                }
            },

            messages: {
                name: {
                    required: "Name is required."
                },
                employee_id:{
                    required: "Labour ID is required."
                },
                per_day_wages:{
                    required: "Per day wages is required."
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
    };

    return {
        init: function () {
            handleCreate();
        }
    };
}();

var  EditSubcontractor = function () {
    var handleEdit = function() {
        var form = $('#editSubcontractorStructure');
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
                employee_id: {
                    required: true
                },
                per_day_wages: {
                    required: true
                },
                email: {
                    email: true
                }
            },

            messages: {
                name: {
                    required: "Name is required."
                },
                employee_id:{
                    required: "Labour ID is required."
                },
                per_day_wages:{
                    required: "Per day wages is required."
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
    };

    return {
        init: function () {
            handleEdit();
        }
    };
}();

var SubcontractorStructureListing = function () {
    var handleOrders = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#subcontractorStructureTable"),
            onSuccess: function (grid) {
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            loadingMessage: 'Loading...',
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options
                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                // So when dropdowns used the scrollable div should be removed.
                //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    var subcontractor_name = $('#subcontractor_name').val();
                    var project_name = $('#project_name').val();
                    var contract_status = $('#contract_status').val();


                    // Total over all pages
                    $.ajax({
                        url: "/subcontractor/subcontractor-structure/listing",
                        type: 'POST',
                        data :{
                            "get_total" : true,
                            "subcontractor_name" : subcontractor_name,
                            "project_name" : project_name,
                            "contract_status" : contract_status
                        },
                        success: function(result){
                            total = result['total'];

                            // Total over this page
                            pageTotal = api
                                .column( 7, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            // Update footer
                            $( api.column( 7 ).footer() ).html(
                                pageTotal.toFixed(3) +' ( '+ total +' total)'
                            );

                            billtotal = result['billtotal'];

                            // Total over this page
                            pageBillTotal = api
                                .column( 8, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            // Update footer
                            $( api.column( 8 ).footer() ).html(
                                pageBillTotal.toFixed(3) +' ( '+ billtotal.toFixed(3) +' total)'
                            );

                            paidtotal = result['paidtotal'];

                            // Total over this page
                            pagePaidTotal = api
                                .column( 9, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            // Update footer
                            $( api.column( 9 ).footer() ).html(
                                pagePaidTotal.toFixed(3) +' ( '+ paidtotal.toFixed(3) +' total)'
                            );

                            balancetotal = result['balancetotal'];

                            // Total over this page
                            pageBalanceTotal = api
                                .column( 10, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            // Update footer
                            $( api.column( 10 ).footer() ).html(
                                pageBalanceTotal.toFixed(3) +' ( '+ balancetotal.toFixed(3) +' total)'
                            );

                        }});
                },
                "lengthMenu": [
                    [20, 100, 150],
                    [20, 100, 150] // change per page values here
                ],
                "pageLength": 20, // default record count per page
                "ajax": {
                    "url": "/subcontractor/subcontractor-structure/listing" // ajax source
                },
                "order": [
                    [1, "asc"]
                ] // set first column as a default sort by asc
            }
        });

        // handle group actionsubmit button click
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $(".table-group-action-input", grid.getTableWrapper());
            if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
                grid.setAjaxParam("customActionType", "group_action");
                grid.setAjaxParam("customActionName", action.val());
                grid.setAjaxParam("id", grid.getSelectedRows());
                grid.getDataTable().ajax.reload();
                grid.clearAjaxParams();
            } else if (action.val() == "") {
                alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'Please select an action',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            } else if (grid.getSelectedRowsCount() === 0) {
                alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No record selected',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });

    }

    return {

        //main function to initiate the module
        init: function () {
            handleOrders();
        }

    };

}();

var PaymentCreate = function () {
    var handleEdit = function() {
        var form = $('#add_payment_form');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                project_site_id : {
                  required : true
                },
                bank_id: {
                    required: true
                },
                payment_type_id : {
                    required : true
                },
                amount: {
                    required: true
                }
            },

            messages: {
                bank_id: {
                    required: "Please select Bank."
                },
                payment_type_id : {
                    required : "Please select Payment Type"
                },
                amount:{
                    max: "Balance Amount is less"
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
    };

    return {
        init: function () {
            handleEdit();
        }
    };
}();

jQuery(document).ready(function() {
    SubcontractorStructureListing.init();
});