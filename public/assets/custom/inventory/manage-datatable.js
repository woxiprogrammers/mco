/**
 * Created by Ameya Joshi on 27/10/17.
 */
var InventoryListing = function () {
    var handleInventory = function () {
        var grid = new Datatable();
        var url = "/inventory/listing?_token=" + $("input[name='_token']").val();
        grid.init({
            src: $("#inventoryListingTable"),
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
                    [30, 100, 150],
                    [30, 100, 150] // change per page values here
                ],
                "pageLength": 30, // default record count per page
                "ajax": {
                    "url": url, // ajax source
                },
                "order": [
                    [1, "asc"]
                ], // set first column as a default sort by asc
                "aoColumns": [
                    { "sClass": "inventory-checkbox" },
                    { "sClass": "inventory-mat-name" },
                    null,
                    null,
                    { "sClass": "inventory-avail-quant" },
                    { "sClass": "inventory-type" },
                    null
                ]
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

var CreateInventoryComponent = function () {
    var handleCreate = function () {
        var form = $('#createComponentForm');
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
                opening_stock: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Name is required."
                },
                opening_stock: {
                    required: 'Opening stock required'
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

function getSelectedItem() {
    var componentIDs = '';
    $("#inventoryListingTable input[type='checkbox']:checked").each((index, elemnt) => {
        componentIDs += elemnt.value
        if (index != ($("#inventoryListingTable input[type='checkbox']:checked").length - 1)) {
            componentIDs += ','
        }
    });
    $("#search_component_id").val(componentIDs)
}

$(document).ready(function () {
    InventoryListing.init();
    CreateInventoryComponent.init();

    $("#createInventoryComponent").click(function () {
        $("#inventoryComponentModal").modal();
    });

    $('#search_name').on('keyup', function () {
        if ($("#search_name").val().length > 3) {
            getSelectedItem();
            $(".filter-submit").trigger('click');
        }
    });

    $('.search_filter').on('keyup', function () {
        if ($("#search_name").val().length > 3) {
            getSelectedItem();
            $(".filter-submit").trigger('click');
        }
    });

    $('#createComponentButton').click(function () {
        var referenceId = $("#reference_id").val();
        if (typeof referenceId != 'undefined' && referenceId != '' && referenceId != null) {
            $('#createComponentForm').submit();
        } else {
            alert('Please select from drop down');
        }
    });

    $("#inventory_type").on('change', function () {
        var componentType = $("#inventory_type").val();
        var project_site_id = $('#project_site').val();
        if (typeof componentType != 'undefined' && componentType != '') {
            $('#name').removeClass('typeahead');
            $('#name').typeahead('destroy');
            $('#name').addClass('typeahead');
            var citiList = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: "/inventory/transfer/auto-suggest/" + componentType + "/%QUERY",
                    filter: function (x) {
                        if ($(window).width() < 420) {
                            $("#header").addClass("fixed");
                        }
                        return $.map(x, function (data) {
                            return {
                                name: data.name,
                                reference_id: data.reference_id
                            };
                        });
                    },
                    wildcard: "%QUERY"
                }
            });
            citiList.initialize();
            $('.typeahead').typeahead(null, {
                displayKey: 'name',
                engine: Handlebars,
                source: citiList.ttAdapter(),
                limit: 30,
                templates: {
                    empty: [
                        '<div class="empty-suggest">',
                        'Unable to find any Result that match the current query',
                        '</div>'
                    ].join('\n'),
                    suggestion: Handlebars.compile('<div class="autosuggest"><strong>@{{name}}</strong></div>')
                },

            }).on('typeahead:selected', function (obj, datum) {
                var POData = $.parseJSON(JSON.stringify(datum));
                POData.name = POData.name.replace(/\&/g, '%26');
                $("#reference_id").val(POData.reference_id);
                $("#name").val(POData.name);
            })
                .on('typeahead:open', function (obj, datum) {

                });
        } else {
            $('#name').removeClass('typeahead');
            $('#name').typeahead('destroy');
        }
    });


    $('#generateChallan').click(function () {

        var itemData = {
            'material': [],
            'asset': []
        }
        $("#inventoryListingTable input[type='checkbox']:checked").each(function () {

            var data = {
                id: this.value,
                name: $(this).closest('tr').find(".inventory-mat-name").html(),
                availQuantity: parseFloat($(this).closest('tr').find(".inventory-avail-quant").html())
            }
            if ($(this).closest('tr').find(".inventory-type").html().toLowerCase() == 'material') {

                itemData['material'].push(data)
            } else if ($(this).closest('tr').find(".inventory-type").html().toLowerCase() == 'asset') {
                itemData['asset'].push(data);
            }
        })
        localStorage.setItem('inventoryData', JSON.stringify(itemData))
        window.location = "/inventory/challan";
    });
});