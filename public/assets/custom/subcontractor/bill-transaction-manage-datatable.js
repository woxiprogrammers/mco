var BillTransactionListing = function () {
    var handleInventory = function () {
        var grid = new Datatable();
        grid.init({
            src: $("#transactionListingTable"),
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
                   // Total over all pages
                    $.ajax({
                        url: "/subcontractor/bill/transaction/listing/"+$("#subcontractorBillId").val(),
                        type: 'POST',
                        data :{
                            "_token": $("input[name='_token']").val(),
                            "get_total" : true,
                        },
                        success: function(result){
                            var subtotal = result['subtotal'];
                            var pageSubtotal = api
                                .column( 3, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );
                            $( api.column( 3 ).footer() ).html(
                                pageSubtotal.toFixed(3) +' ( '+ subtotal.toFixed(3) +' total)'
                            );

                            var debit = result['debit'];
                            var pageDebit = api
                                .column( 4, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    console.log(a+' '+b);
                                    return intVal(a) + intVal(b);
                                }, 0 );
                            $( api.column( 4 ).footer() ).html(
                                pageDebit.toFixed(3) +' ( '+ debit.toFixed(3) +' total)'
                            );

                            var hold = result['hold'];
                            var pageHold = api
                                .column( 5, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    console.log(a+' '+b);
                                    return intVal(a) + intVal(b);
                                }, 0 );
                            $( api.column( 5 ).footer() ).html(
                                pageHold.toFixed(3) +' ( '+ hold.toFixed(3) +' total)'
                            );

                            var retention = result['retention_amount'];
                            var pageRetention = api
                                .column( 6, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    console.log(a+' '+b);
                                    return intVal(a) + intVal(b);
                                }, 0 );
                            $( api.column( 6 ).footer() ).html(
                                pageRetention.toFixed(3) +' ( '+ retention.toFixed(3) +' total)'
                            );

                            var tds = result['tds'];
                            var pageTds = api
                                .column( 7, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    console.log(a+' '+b);
                                    return intVal(a) + intVal(b);
                                }, 0 );
                            $( api.column( 7 ).footer() ).html(
                                pageTds.toFixed(3) +' ( '+ tds.toFixed(3) +' total)'
                            );

                            var otherRecovery = result['other_recovery'];
                            var pageOtherRecovery = api
                                .column( 8, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    console.log(a+' '+b);
                                    return intVal(a) + intVal(b);
                                }, 0 );
                            $( api.column( 8 ).footer() ).html(
                                pageOtherRecovery.toFixed(3) +' ( '+ otherRecovery.toFixed(3) +' total)'
                            );

                            var total = result['total'];
                            var pageTotal = api
                                .column( 9, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    console.log(a+' '+b);
                                    return intVal(a) + intVal(b);
                                }, 0 );
                            $( api.column( 9 ).footer() ).html(
                                pageTotal.toFixed(3) +' ( '+ total.toFixed(3) +' total)'
                            );
                        }});
                },
                "lengthMenu": [
                    [50, 100, 150],
                    [50, 100, 150] // change per page values here
                ],
                "pageLength": 50, // default record count per page
                "ajax": {
                    "url": "/subcontractor/bill/transaction/listing/"+$("#subcontractorBillId").val(),
                    "data" :{
                        '_token' : $("input[name='_token']").val()
                    }
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
            handleInventory();
        }
    };
}();

var  CreateTransaction = function () {
    var handleCreate = function() {
        var form = $('#createTransactionForm');
        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);
        var pendingAmount = parseFloat($("#pendingAmount").val());
        form.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                total : {
                    required: true,
                    min : 1,
                    max: pendingAmount
                }
            },
            messages: {
                total : {
                    required: 'Please enter total.'
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

$(document).ready(function(){
    BillTransactionListing.init();
});
