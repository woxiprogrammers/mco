/**
 * Created by Ameya Joshi on 28/6/17.
 */

/* Function To Display Uploaded image */
var $hello= $('#path');
$hello.on("change", function(event, path,count){ //bind() for older jquery version
    if (typeof path !== "undefined") {
        /*console.log('In HTML');
         console.log("Length:"+$('#length').val());
         console.log("max_file:"+$('#max_files').val());
         console.log("max_file_id:"+$('#max_files_count').val());*/
        var quotationId = $("#quotationId").val();
        $.ajax({
            url: "/quotation/display-images/"+quotationId,
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
/* Function To Remove Product Images */

function removeProductImages(imageId,path,originalId){
    /*console.log('In Remove');
     console.log("Length:"+$('#length').val());
     console.log("max_file:"+$('#max_files').val());
     console.log("max_file:"+$('#max_files').val());*/
    var maxCount = parseInt($('#max_files_count').val());
    maxCount = maxCount +  1;
    $('#max_files_count').val(maxCount);
    $.ajax({
        url: "/quotation/delete-temp-product-image",
        data: {'path':path,'id':originalId},
        async:false,
        error: function(data) {
            alert('something went wrong');
        },
        success: function(data, textStatus, xhr) {
            console.log(xhr.status);
            if(xhr.status==200){
                $(imageId).remove();
            }else{
                alert('something went wrong');
            }
        },
        type: 'POST'
    });
}
