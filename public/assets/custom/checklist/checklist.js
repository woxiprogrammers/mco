$(document).ready(function() {
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

    $("#main_cat").on('change', function(){
        var mainCategoryId = $(this).val();
        if(typeof mainCategoryId == 'undefined' || mainCategoryId == ''){
            $("#sub_cat").html('<option value="">--Select Sub Category --</option>');
        }else{
            $.ajax({
                url: '/checklist/structure/get-sub-category',
                type: 'POST',
                data: {
                    _token: $('input[name="_token"]').val(),
                    category_id: mainCategoryId
                },
                success: function(data,textStatus,xhr){
                    $("#sub_cat").html(data);
                },
                error: function(errorData){
                    alert('Something went wrong.');
                }
            });
        }
    });

    $("#clientId").on('change', function(){
        var clientId = $(this).val();
        if(typeof clientId != 'undefined'  && clientId != ''){
            $.ajax({
                url: '/checklist/get-projects',
                type: "POST",
                data: {
                    _token : $("input[name='_token']").val(),
                    client_id: clientId
                },
                success: function (data,textStatus,xhr) {
                    $("#projectId").html(data);
                },
                error: function (errorData) {

                }
            });
        }else{
            $("#projectId").html('<option value="">--Select Project--</option>')
        }

    });
    $("#projectId").on('change', function(){
        var projectId = $(this).val();
        if(typeof projectId != 'undefined' && projectId != ''){
            $.ajax({
                url: '/checklist/get-project-sites',
                type: "POST",
                data: {
                    _token : $("input[name='_token']").val(),
                    project_id: projectId
                },
                success: function (data,textStatus,xhr) {
                    $("#projectSiteId").html(data);
                },
                error: function (errorData) {

                }
            });
        }else{
            $("#projectSiteId").html('<option value="">--Select Project Site--</option>')
        }
    });
});

function addCheckpoint(){
    $(".add_field_button").trigger('click');
}
function removeCheckpoint(element){
    $(element).closest('.checkpoint').remove();
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