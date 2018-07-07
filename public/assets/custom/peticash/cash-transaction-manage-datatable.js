/**
 * Created by Harsha on 6/7/18.
 */

var cashTransactionListing = function () {
    var handleOrders = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#cashTransactionManageTable"),
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
               /* "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    var client_id = $('#client_id').val();
                    var project_id = $('#project_id').val();
                    var site_id = $('#site_id').val();
                    var year = $('#year').val();
                    var month = $('#month').val();
                    var search_name = $('#search_name').val();

                    var postData =
                        'client_id=>'+client_id+','+
                        'project_id=>'+project_id+','+
                        'site_id=>'+site_id+','+
                        'year=>'+year+','+
                        'month=>'+month;


                    // Total over all pages
                    $.ajax({
                        url: "/peticash/peticash-management/purchase/listing?_token="+$("input[name='_token']").val(),
                        type: 'POST',
                        data :{
                            "get_total" : true,
                            "search_name" : search_name,
                            "postdata" : postData
                        },
                        success: function(result){
                            total = result['total'];

                            // Total over this page
                            pageTotal = api
                                .column( 4, { page: 'current'} )
                                .data()
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0 );

                            // Update footer
                            $( api.column( 4 ).footer() ).html(
                                pageTotal +' ( '+ total +' total)'
                            );
                        }});
                },*/

                "lengthMenu": [
                    [50, 100, 150],
                    [50, 100, 150] // change per page values here
                ],
                "pageLength": 50, // default record count per page
                "ajax": {
                    "url": "/peticash/peticash-management/cash-transaction/listing?_token="+$("input[name='_token']").val() // ajax source
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
