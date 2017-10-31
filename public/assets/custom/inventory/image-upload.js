/**
 * Created by Ameya Joshi on 30/10/17.
 */

/* Function To Display Uploaded image */
var $hello= $('#path');
$hello.on("change", function(event, path,count){
    if (typeof path !== "undefined") {
        var componentId = $("#inventoryComponentId").val();
        $.ajax({
            url: "/inventory/component/display-images/"+componentId+'?_token='+$("input[name='_token']").val(),
            data: {'path':path,'count':count},
            async:false,
            error: function(data) {
                alert('something went wrong');
            },
            success: function(data, textStatus, xhr) {
                $('#show-product-images').append(data);
            },
            type: 'POST'
        });
    }

}).triggerHandler('change');

function removeProductImages(imageId,path,originalId){
    var maxCount = parseInt($('#max_files_count').val());
    maxCount = maxCount +  1;
    $('#max_files_count').val(maxCount);
    $.ajax({
        url: "/inventory/component/delete-temp-inventory-image?_token="+$("input[name='_token']").val(),
        data: {'path':path,'id':originalId},
        async:false,
        error: function(data) {
            alert('something went wrong');
        },
        success: function(data, textStatus, xhr) {
            if(xhr.status==200){
                $(imageId).remove();
            }else{
                alert('something went wrong');
            }
        },
        type: 'POST'
    });
}
