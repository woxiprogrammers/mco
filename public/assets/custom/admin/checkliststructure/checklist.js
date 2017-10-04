
$('document').ready(function(){

    $('#extra').hide();
})


$(document).ready(function() {
    var max_fields      = 10;
    var wrapper         = $(".input_fields_wrap");
    var add_button      = $(".add_field_button");

    var x = 1;
    $(add_button).click(function(e){
        e.preventDefault();
        if(x < max_fields){
            x++;
            $(wrapper).append(' <div>' +
                '                        <form>' +
                '                        <div class="form-body">' +
                '                        <div class="form-group row">' +
                '                        <div class="col-md-5" style="text-align: right ; margin-left: -6.6% ; font-size: 14px">' +
                '                        <label for="title" class="control-label">Title</label>                        <span>*</span>\n' +
                '                    </div><div class="col-md-6">\n' +
                '<input type="text" class="form-control" name="mytext[]"  id="title_name" placeholder="Enter Title Here">' +
                '                      <div id="removeBtn"  style="margin-top: -5%; margin-left: 118%" >\n' +
                '</div>\n' +
                '                    </div>\n' +
                '<div class="col-md-5" style="text-align: right ; margin-left: -91.5% ;margin-top: 9% ; font-size: 14px">\n' +
                '<label for="title" class="control-label">Is Remark Mandatory</label>\n' +
                '<span>*</span>\n' +
                ' </div>\n' +
                '<div class="col-md-2" style="text-align: right ; margin-top: 9% ; margin-left: -50% ">' +
                '  <select class="form-control" id="sub_opt" name="sub_opt">\n' +
                '<option value="">Select Option</option>\n' +
                '<option value="True">Yes</option>\n' +
                '<option value="False">No</option>\n' +
                '</select>' +
                '                        </div>' +
                '                        <div class="col-md-5" style="text-align: right ; margin-left: -91.5% ;margin-top: 4% ; font-size: 14px">\n' +
                '<label for="title" class="control-label"> Description </label>\n' +
                '<span>*</span>\n' +
                '</div>\n' +
                '<div class="col-md-6" style="text-align: right ; margin-top: 4.5% ; margin-left: -50% ">' +
                '   <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description Here"  >\n' +
                '</div>' +
                '<div class="col-md-5" style="text-align: right ; margin-left: -91.5% ; margin-top: 13% ; font-size: 14px"> ' +
                '   <label for="no_images" class="control-label"> Number Of Images</label><span>*</span>' +
                '</div>' +
                '<div class="col-md-6" style="margin-top: -2% ;margin-left: 31.5% ; font-size: 14px">' +
                '   <div class="col-md-6">' +
                '  <input type="text" id="nochapterid" >' +
                ' </div>' +
                '</div>' +
                '<div class="col-md-6" style="margin-top:-2% ; margin-left: 31%">' +
                '<input type="button" value="Set" onclick="generatefun()" >' +
                '</div>' +
                '<div class="col-md-6" style="text-align: right ; margin-left: 12% ; margin-top: 2% ; font-size: 14px" >' +
                '<label for="is_special" class="control-label" style="text-align: right ">Is Mandatory ?</label>' +
                '<span>*</span>' +
                '<label for="description" class="control-label" style=" font-size: 14px ;text-align: left ; margin-left: 23%">Image Caption</label>' +
                '<span>*</span>' +
                '</div>' +
                '<div id="extradiv" hidden>\n' +
                '<div class="row">\n' +
                '<div class="col-md-6" >\n' +
                '<div class="col-md-5" style="text-align: right ; margin-top: 9% ; margin-left: 42% ">' +
                '                        <select class="form-control" id="sub_opt" name="sub_opt">\n' +
                '<option value="">Select Option</option>\n' +
                '<option value="True">Yes</option>\n' +
                '<option value="False">No</option>\n' +
                '</select>' +
                '</div>' +
                '<div class="col-md-5" style="text-align: right ; margin-left: 102.5% ;margin-top: -6% ; font-size: 14px">\n' +
                '<div class="col-md-6">\n' +
                '<input type="text" class="form-control" id="description2" name="description" placeholder="Enter Description Here " style="width:fit-content">\n' +
                '</div>\n' +
                '</div>\n' +
                '</div>' +
                '</div>' +
                '</div>'+
                '<input type="button" class="remove_field" style="margin-top: -16%; margin-left: 77%" value="Remove">' +
                '<div class="form-group row">\n' +
                '<div id="appending">\n' +
                '</div>\n' +
                '</div>' +
                '</div>' +

                '</div>' +
                '</form>' +
                '   </div>');
        }
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
        ($('#extra').clone()).appendTo('#append')
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
