x=1;
$(document).ready(function() {
    var wrapper         = $(".input_fields_wrap");
    var add_button      = $(".add_field_button");
    $(add_button).click(function(e){
        e.preventDefault();
        var noOfCheckpoint = $("#numberOfCheckpoints").val();
        $.ajax({
            url: '/checklist/structure/get-checkpoint-partial-view',
            type: 'POST',
            data:{
                _token: $("input[name='_token']").val(),
                number_of_checkpoints: noOfCheckpoint
            },
            success: function (data,textStatus,xhr) {
                $(".input_fields_wrap").append(data);
                $("#numberOfCheckpoints").val(parseInt(noOfCheckpoint)+1);
            },
            error:function (errorStatus) {
                alert("Something went wrong.");
            }
        });
    });
});

function addCheckpoint(){
    $(".add_field_button").trigger('click');
}
function removeCheckpoint(element){
    console.log(' in remove funciton');
    $(element).closest('.checkpoint').remove();
    x--;
}
function getImageTable(element, index){
    var noOfImage = $(element).closest('.form-group').find('.number-of-image').val();
    if(typeof noOfImage != 'undefined' && noOfImage != '' && $.isNumeric(noOfImage)){
        $.ajax({
            url: '/checklist/structure/get-checkpoint-image-partial-view',
            type: 'POST',
            data:{
                _token: $('input[name="_token"]').val(),
                index: index,
                number_of_images: noOfImage
            },
            success: function (data,textStatus,xhr) {
                $(element).closest('.form-group').next().find('.image-table-section').html(data);
            },
            error: function (errorData) {

            }
        });
    }

}