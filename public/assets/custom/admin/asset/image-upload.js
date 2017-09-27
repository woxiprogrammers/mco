/**
 * Created by Ameya Joshi on 28/6/17.
 */

/* Function To Display Uploaded image */
var $hello= $('#path');
$hello.on("change", function(event, path,count){
    if (typeof path !== "undefined") {
        $.ajax({
            url: "/asset/display-images",
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

function removeAssetImage(imageId,path,originalId){
    var maxCount = parseInt($('#max_files_count').val());
    maxCount = maxCount +  1;
    $('#max_files_count').val(maxCount);
    $.ajax({
        url: "/asset/delete-temp-product-image",
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
