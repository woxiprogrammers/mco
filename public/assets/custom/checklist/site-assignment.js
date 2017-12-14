/**
 * Created by Ameya Joshi on 9/12/17.
 */

$(document).ready(function(){
    $("#sub_cat").on('change', function(){
        var subCategoryId = $(this).val();
        if(typeof subCategoryId != 'undefined' && subCategoryId != ''){
            $.ajax({
                url: '/checklist/site-assignment/get-checkpoints/'+subCategoryId+'?_token='+$("input[name='_token']").val(),
                type: "GET",
                success: function (data,textStatus,xhr) {
                    $(".input_fields_wrap").html(data);
                },
                error: function (errorData) {
                    
                }
            });
        }else{
            $(".input_fields_wrap").html('');
        }
    });

    $("#projectSiteId").change(function(){
        var projectSiteId = $(this).val();
        if(typeof projectSiteId != 'undefined' && projectSiteId != ''){
            $.ajax({
                url: '/checklist/get-quotation-floors',
                type: "POST",
                data: {
                    _token: $("input[name='_token']").val(),
                    project_site_id: projectSiteId
                },
                success: function(data,textStatus,xhr){
                    $("#quotationFloorId").html(data);
                },
                error: function (errorData) {
                    
                }
            });
        }else{
            $("#quotationFloorId").html('<option value="">--Select Quotation Floor --</option>');
        }
    });
});
