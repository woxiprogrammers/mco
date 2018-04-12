
var $hello= $('#path');
$hello.on("change", function(event, path,count){
    if (typeof path !== "undefined") {
        var purchaseRequestComponentId = $("#purchaseRequestComponentId").val();
        $.ajax({
            url: "/purchase/purchase-order-request/display-files/forClient/"+purchaseRequestComponentId,
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
        url: "/purchase/purchase-order-request/delete-temp-file",
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

var $vendorHello= $('#vendorPath');
$vendorHello.on("change", function(event, path,count){
    if (typeof path !== "undefined") {
        var purchaseRequestComponentId = $("#purchaseRequestComponentId").val();
        $.ajax({
            url: "/purchase/purchase-order-request/display-files/forVendor/"+purchaseRequestComponentId,
            data: {'path':path,'count':count},
            async:false,
            error: function(data) {
                alert('something went wrong');
            },
            success: function(data, textStatus, xhr) {
                $('#show-product-images-vendor').append(data);
            },
            type: 'POST'
        });
    }

}).triggerHandler('change');

