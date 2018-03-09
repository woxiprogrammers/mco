var BillListing = function () {
    var handleOrders = function () {

        var grid = new Datatable();
        var subcontractorStructureId = $('#subcontractorStructureId').val();
        var bill_status = $('#bill_status').val();
        grid.init({
            src: $("#billTable"),
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
                    [50, 100, 150],
                    [50, 100, 150] // change per page values here
                ],
                "pageLength": 50, // default record count per page
                "ajax": {
                    "url": "/subcontractor/subcontractor-bills/listing/"+subcontractorStructureId+"/"+bill_status, // ajax source
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
            handleOrders();
        }

    };

}();

jQuery(document).ready(function() {
    var hash = window.location.hash;
    var selectDropDown = $('<select />',{id:"bill_status",class:"form-control", name:"bill_status"});
    if (hash == "#disapproved") { // CANCELLED STATUS
        $('<option />', {value:'disapproved',text:'Disapproved'}).appendTo(selectDropDown);
        $('<option />', {value:'approved&draft',text:'Approved & Draft'}).appendTo(selectDropDown);
    } else { //Approved Status by default
        $('<option />', {value:'approved&draft',text:'Approved & Draft'}).appendTo(selectDropDown);
        $('<option />', {value:'disapproved',text:'Disapproved'}).appendTo(selectDropDown);
    }
    selectDropDown.appendTo('#bill_status_dropdown');

    BillListing.init();

    $('#bill_status').on('change',function(e) {
        window.location.href = "#" + $('#bill_status').val();
        location.reload();
    });
});



