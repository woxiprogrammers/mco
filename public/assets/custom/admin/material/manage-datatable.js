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
                    [ 20, 100, 150],
                    [ 20, 100, 150]
                ],
                "pageLength": 20,
                "ajax": {
                    "url": "/material/listing",
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
    $("input[name='search_name']").on('keyup',function(){
        if ($("input[name='search_name']").val().length > 3) {
            $(".filter-submit").trigger('click');
        }
    });

    $("input[name='search_rate']").on('keyup',function(){
        if ($("input[name='search_rate']").val().length > 0) {
            $(".filter-submit").trigger('click');
        }
    });

    $("input[name='search_name_cat']").on('keyup',function(){
        if ($("input[name='search_name_cat']").val().length > 3) {
            $(".filter-submit").trigger('click');
        }
    });

    $("#changeStatusButton").on('click',function(){
        var materialIds = [];
        $("input:checkbox:checked").each(function(i){
            materialIds[i] = $(this).val();
        });
        $.ajax({
            url:'/material/change-status',
            type: "POST",
            data: {
                _token: $("input[name='_token']").val(),
                material_ids: materialIds
            },
            success: function(data, textStatus, xhr){
                $(".filter-submit").trigger('click');
            },
            error: function(data){

            }
        });
    });
});