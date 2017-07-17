var BillListing = function () {
    var handleOrders = function () {

        var grid = new Datatable();
        console.log($('#projectSiteId').val());
        var project_site_id = $('#projectSiteId').val();
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
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "/bill/listing/"+project_site_id+"/"+bill_status, // ajax source
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
        var s = $('<select />',{id:"bill_status",class:"form-control", name:"bill_status"});
        if (hash == "#draft") { //DRAFT Status
            $('<option />', {value:'draft',text:'Draft'}).appendTo(s);
            $('<option />', {value:'approved',text:'Approved'}).appendTo(s);
            $('<option />', {value:'cancelled',text:'Cancelled'}).appendTo(s);
        } else if (hash == "#cancelled") { // CANCELLED STATUS
            $('<option />', {value:'cancelled',text:'Cancelled'}).appendTo(s);
            $('<option />', {value:'approved',text:'Approved'}).appendTo(s);
            $('<option />', {value:'draft',text:'Draft'}).appendTo(s);
        } else { //Approved Status by default
            $('<option />', {value:'approved',text:'Approved'}).appendTo(s);
            $('<option />', {value:'draft',text:'Draft'}).appendTo(s);
            $('<option />', {value:'cancelled',text:'Cancelled'}).appendTo(s);
        }
        s.appendTo('#bill_status_dropdown');

    BillListing.init();

        $('#bill_status').on('change',function(e) {
            window.location.href = "#" + $('#bill_status').val();
            location.reload();
        });
    });

