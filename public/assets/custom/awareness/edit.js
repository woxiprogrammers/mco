$('#mainCategoryId').change(function(){
    var main_category_id = $(this).val();
    $.ajax({
        url: '/awareness/file-management/get-subcategories',
        type: 'POST',
        async: false,
        data: {
            'id' : main_category_id,
        },
        success: function(data,textStatus,xhr){
            $.each(data.categories, function( index, value ) {
                var option = '<option>Select Sub Category</option>'
                option += '<option value="'+value.id+'">'+value.name+'</option>';
                $('#subCategoryId').html(option);
            });
        },
        error: function(data, textStatus, xhr){

        }
    });
})
$('#subCategoryId').change(function(){
    var sub_category_id = $(this).val();
    $.ajax({
        url: '/awareness/file-management/get-subcategories-details',
        type: 'POST',
        async: false,
        data: {
            'id' : sub_category_id,
        },
        success: function(data,textStatus,xhr){
        $('#imagesTable').html(data);
        },
        error: function(data, textStatus, xhr){

        }
    });
})