
$('document').ready(function(){

    $('#extra').hide();
})


$(document).ready(function() {
    var wrapper         = $(".input_fields_wrap");
    var add_button      = $(".add_field_button");

    var x = 1;
    $(add_button).click(function(e){
        e.preventDefault();
        $(wrapper).append(
            '<div class="checkpoint">\n' +
                '<div class="form-group">\n' +
                    '<div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">\n' +
                        '<label for="title" class="control-label">Description</label>\n' +
                            '<span>*</span>\n' +
                    '</div>\n' +
                    '<div class="col-md-6">\n' +
                        '<input type="text" class="form-control" name="checkpoints[0][description]"  placeholder="Enter Description">\n' +
                        '<div id="sample_editable_1_new" class="btn yellow" style="margin-top: -7%; margin-left: 105%">\n' +
                            '<button style="color: white" class="add_field_button" id="add">\n' +
                                '<i class="fa fa-plus"></i>\n' +
                            '</button>\n' +
                        '</div>\n' +
                        '<div style="margin-top: -6%; margin-left: 118% ; font-size: 14px" >\n' +
                            '<input type="reset" value="Reset">\n' +
                        '</div>\n' +
                        '<div id="removeBtn"  style="margin-top: -5%; margin-left: 118%" >\n' +
                        '</div>\n' +
                    '</div>\n' +
                '</div>\n' +
                '<div class="form-group">\n' +
                    '<div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">\n' +
                        '<label for="title" class="control-label">Is Remark Mandatory</label>\n' +
                        '<span>*</span>\n' +
                    '</div>\n' +
                        '\n' +
                    '<div class="col-md-2">\n' +
                        '<select class="form-control" id="sub_cat" name="sub_cat">\n' +
                            '<option value="">Select Option</option>\n' +
                                '<option value="True">Yes</option>\n' +
                                '<option value="False">No</option>\n' +
                        '</select>\n' +
                    '</div>\n' +
                '</div>\n' +
                '<div class="form-group">\n' +
                    '<div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">\n' +
                        '<label for="title" class="control-label"> No. of Images </label>\n' +
                        '<span>*</span>\n' +
                    '</div>\n' +
                    '<div class="col-md-2">\n' +
                        '<input type="text" class="form-control" >\n' +
                    '</div>\n' +
                '</div>\n' +
            '</div>');

    });

    $(wrapper).on("click",".remove_field", function(e) {

        e.preventDefault();
        $(this).parent('div').remove();
        x--;
    })

});

function generate() {
    var number = parseInt($("#nochapter").val());
    $('#append').html('');
    $('#extra').hide();
    for (max = 0; max < number; max++) {
        ($('#extra').clone()).appendTo('#append');
        $('#extra').show();
    }
}

$('document').ready(function(){

    $('#extradiv').hide();
})

function generatefun() {

    var count = parseInt($("#nochapterid").val());
    $('#appending').html('');
    $('#extradiv').hide();
    for (maximum = 0; maximum < count; maximum++) {

        ($('#extradiv').clone()).appendTo('#appending')
        $('#extradiv').show();

    }
}
