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


                    // Total over all pages
                    $.ajax({
                        url: "/subcontractor/structure/listing",
                        type: 'POST',
                        data :{
                            "_token": $("input[name='_token']").val(),
                            "get_total" : true,
                            "subcontractor_name" : subcontractor_name,
                            "project_name" : project_name
                        },
                        success: function(result){
                            // total = result['total'];
                            //
                            // // Total over this page
                            // pageTotal = api
                            //     .column( 6, { page: 'current'} )
                            //     .data()
                            //     .reduce( function (a, b) {
                            //         return intVal(a) + intVal(b);
                            //     }, 0 );
                            //
                            // // Update footer
                            // $( api.column( 6 ).footer() ).html(
                            //     pageTotal.toFixed(3) +' ( '+ total +' total)'
                            // );
                            totalRate = result['totalRate'];
                            pageTotalRate = api
                                .column( 3, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            $( api.column( 3 ).footer() ).html(
                                pageTotalRate.toFixed(3) +' ( '+ totalRate.toFixed(3) +' total)'
                            );
                            totalWorkArea = result['totalWorkArea'];
                            pageTotalWorkArea = api
                                .column( 4, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            $( api.column( 4 ).footer() ).html(
                                pageTotalWorkArea.toFixed(3) +' ( '+ totalWorkArea.toFixed(3) +' total)'
                            );
                            totalAmount = result['totalAmount'];
                            pageTotalAmount = api
                                .column( 5, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            $( api.column( 5 ).footer() ).html(
                                pageTotalAmount.toFixed(3) +' ( '+ totalAmount.toFixed(3) +' total)'
                            );
                            billtotal = result['billtotal'];

                            // Total over this page
                            pageBillTotal = api
                                .column( 6, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            // Update footer
                            $( api.column( 6 ).footer() ).html(
                                pageBillTotal.toFixed(3) +' ( '+ billtotal.toFixed(3) +' total)'
                            );

                            paidtotal = result['paidtotal'];

                            // Total over this page
                            pagePaidTotal = api
                                .column( 7, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            // Update footer
                            $( api.column( 7 ).footer() ).html(
                                pagePaidTotal.toFixed(3) +' ( '+ paidtotal.toFixed(3) +' total)'
                            );

                            balancetotal = result['balancetotal'];

                            // Total over this page
                            pageBalanceTotal = api
                                .column( 8, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            // Update footer
                            $( api.column( 8 ).footer() ).html(
                                pageBalanceTotal.toFixed(3) +' ( '+ balancetotal.toFixed(3) +' total)'
                            );

                        }});
                },
                "lengthMenu": [
                    [50, 100, 150],
                    [50, 100, 150] // change per page values here
                ],
                "pageLength": 50, // default record count per page
                "ajax": {
                    "url": "/subcontractor/structure/listing?_token="+$("input[name='_token']").val() // ajax source
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