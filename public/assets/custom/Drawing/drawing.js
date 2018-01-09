$('#main_category_id').change(function(){
    var client_id = $(this).val();
    $.ajax({
        url: '/drawing/images/get-sub-categories/',
        type: 'POST',
        async: false,
        data: {
            'id' : client_id,
        },
        success: function(data,textStatus,xhr){
            var option = '<option>Select Sub Category</option>';
            $.each(data.projects, function( index, value ) {
                option += '<option value="'+value.id+'">'+value.name+'</option>';
                console.log(option);
            });
            $('#sub_category_id').html(option);
        },
        error: function(data, textStatus, xhr){
        }
    });
})