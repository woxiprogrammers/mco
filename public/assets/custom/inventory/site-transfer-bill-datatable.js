/**
 * Created by Ameya Joshi on 21/3/18.
 */

var SiteTransferBillListing = function () {
    var handleInventory = function () {
        var grid = new Datatable();
        grid.init({
            src: $("#siteTransferBillListingTable"),
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
                        url: "/inventory/transfer/billing/listing?_token="+$("input[name='_token']").val(), // ajax source
                        type: 'POST',
                        data :{
                            "get_total" : true,
                            "bill_number" : $('#bill_number').val(),
                            "project_name" : $('#project_name').val(),
                            "basic_amt" : $('#basic_amt').val(),
                            "total_amt" : $('#total_amt').val(),
                            "vendor_name" : $('#vendor_name').val(),
                            "bill_date" : $('#bill_date').val()
                        },
                        success: function(result){
                            total = result['total'];

                            // Total over this page
                            pageTotal = api
                                .column( 8, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            // Update footer
                            $( api.column( 8 ).footer() ).html(
                                pageTotal.toFixed(3) +' ( '+ total.toFixed(3) +' total)'
                            );

                            billtotal = result['billtotal'];

                            // Total over this page
                            pageBillTotal = api
                                .column( 9, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            // Update footer
                            $( api.column( 9 ).footer() ).html(
                                pageBillTotal.toFixed(3) +' ( '+ billtotal.toFixed(3) +' total)'
                            );

                            paidtotal = result['paidtotal'];

                            // Total over this page
                            pagePaidTotal = api
                                .column( 10, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            // Update footer
                            $( api.column( 10 ).footer() ).html(
                                pagePaidTotal.toFixed(3) +' ( '+ paidtotal.toFixed(3) +' total)'
                            );
                        }});
                },
                "lengthMenu": [
                    [20, 100, 150],
                    [20, 100, 150] // change per page values here
                ],
                "pageLength": 20, // default record count per page
                "ajax": {
                    "url": "/inventory/transfer/billing/listing?_token="+$("input[name='_token']").val(), // ajax source
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