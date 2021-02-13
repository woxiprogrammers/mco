var MaterialListing = function () {
    var handleMaterials = function () {
        var grid = new Datatable();

        grid.init({
            src: $("#materialTable"),
            onSuccess: function (grid) {

            },
            onError: function (grid) {

            },
            loadingMessage: 'Loading...',
            dataTable: {
                "lengthMenu": [
                    [ 30, 100, 150],
                    [ 30, 100, 150]
                ],
                "pageLength": 30,
                "ajax": {
                    "url": "/grn/delete/listing",
                    "method": "GET"
                },
                "order": [
                    [1, "asc"]
                ]
            }
        });


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
            handleMaterials();
        }

    };

}();

jQuery(document).ready(function() {
    MaterialListing.init();

    $("#delete-grn").on('click',function(){
        var grnIds = [];
        $("input:checkbox:checked").each(function(i){
            grnIds[i] = $(this).val();
        });

        if(grnIds.length > 0) {
            $.ajax({
                url:'/grn/delete',
                type: "POST",
                data: {
                    _token: $("input[name='_token']").val(),
                    grnIds: grnIds
                },
                success: function(data, textStatus, xhr){
                    $('#search_grn').val('');
                    $(".filter-submit").trigger('click');
                },
                error: function(data){

                }
            });
        }else{
            alert('Please select atleast one checkbox!');
        }
    });
});