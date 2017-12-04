x=1;
$(document).ready(function() {
    var wrapper         = $(".input_fields_wrap");
    var add_button      = $(".add_field_button");
    $(add_button).click(function(e){
        x++;
        e.preventDefault();
        $(wrapper).append(
            '<div class="checkpoint" style="margin-top: 5%">\n' +
                '<fieldset>'+
                    '<legend style="margin-left: 15%"> Checkpoint -'+x+'</legend>'+
                    '<div class="form-group row">\n' +
                        '<div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">\n' +
                            '<label for="title" class="control-label">Description</label>\n' +
                                '<span>*</span>\n' +
                        '</div>\n' +
                        '<div class="col-md-7">\n' +
                            '<input type="text" class="form-control" name="checkpoints[0][description]"  placeholder="Enter Description" style="width: 85%;">\n' +
                            '<a class="btn blue" href="javascript:void(0);" id="add" style="margin-left: 87%; margin-top: -8%" onclick="return createCheckpoint()">\n' +
                                '<i class="fa fa-plus"></i>\n' +
                            '</a>\n' +
                            '<a class="btn blue" id="add" href="javascript:void(0);" style=" margin-top: -8%" onclick="return removeCheckpoint(this)">\n' +
                                '<i class="fa fa-minus"></i>\n' +
                            '</a>\n' +
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
                '</fieldset>'+
            '</div>');

    });

    $(".remove_field").click(function(e) {
        e.preventDefault();
        console.log('in remove');
        $(this).closest('.checkpoint').remove();
        x--;
    });

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
function createCheckpoint(){
    $(".add_field_button").trigger('click');
}
function removeCheckpoint(element){
    console.log(' in remove funciton');
    $(element).closest('.checkpoint').remove();
    x--;
}