/**
 * Created by Ameya Joshi on 11/12/17.
 */
$(document).ready(function(){
    CreateChecklistUserAssignment.init();
    $("#quotationFloorId").on('change', function(){
        var quotationFloorId = $(this).val();
        var projectSiteId = $("#projectSiteId").val();
        if((typeof quotationFloorId != 'undefined') && (quotationFloorId != '') && (typeof projectSiteId != 'undefined') && (projectSiteId != '')){
            $.ajax({
                url: '/checklist/user-assignment/get-categories',
                type: 'POST',
                data: {
                    _token: $("input[name='_token']").val(),
                    quotation_floor_id: quotationFloorId,
                    slug: 'main-category',
                    project_site_id: projectSiteId
                },
                success: function(data,textStatus,xhr){
                    $("#main_category").html(data);
                },
                error: function(errorData){

                }
            });
        }else{
            $("#main_category").html('<option value="">--Select Main Category--</option>');
            $("#sub_category").html('<option value="">--Select Sub Category--</option>');
        }

    });
    $("#main_category").on('change', function(){
        var mainCategoryId = $(this).val();
        var quotationFloorId = $("#quotationFloorId").val();
        var projectSiteId = $("#projectSiteId").val();
        if((typeof quotationFloorId != 'undefined') && (quotationFloorId != '') && (typeof projectSiteId != 'undefined') && (projectSiteId != '') && (typeof mainCategoryId != 'undefined') && (mainCategoryId != '')){
            $.ajax({
                url: '/checklist/user-assignment/get-categories',
                type: 'POST',
                data: {
                    _token: $("input[name='_token']").val(),
                    quotation_floor_id: quotationFloorId,
                    slug: 'sub-category',
                    project_site_id: projectSiteId,
                    category_id: mainCategoryId
                },
                success: function(data,textStatus,xhr){
                    $("#sub_category").html(data);
                },
                error: function(errorData){

                }
            });
        }else{
            $("#sub_category").html('<option value="">--Select Sub Category--</option>');
        }

    });

    $("#sub_category").on('change', function(){
        var subCategoryId = $(this).val();
        var projectSiteId = $("#projectSiteId").val();
        if((typeof subCategoryId != 'undefined') && (subCategoryId != '') && (typeof projectSiteId != 'undefined') && (projectSiteId != '')){
            $.ajax({
                url: '/checklist/user-assignment/get-users',
                type: 'POST',
                data: {
                    _token: $("input[name='_token']").val(),
                    project_site_id: projectSiteId
                },
                success: function(data,textStatus, xhr){
                    $("#userList").html(data);
                    $("#userSection").show();
                },
                error: function(errorData){
                    $("#userSection").hide();
                }
            });
        }else{
            $("#userSection").hide();
        }
    });
});