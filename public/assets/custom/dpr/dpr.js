$(document).ready(function(){
    $("#subcontractorId").change(function(){
        var subContractorId = $(this).val();
        if(typeof subContractorId == 'undefined' || subContractorId == ''){
            $("#categoryImageDiv").html('');
            // $("#show-product-images").html('');
            // $("#categoryTable").hide();
            // $("#imageUploadDiv").hide();
        }else {
            $.ajax({
                url: '/dpr/subcontractor/get-category',
                type: 'POST',
                data:{
                    _token: $("input[name='_token']").val(),
                    subcontractor_id: subContractorId
                },
                success: function (data,textStatus, xhr) {
                    $("#categoryImageDiv").html(data);
                    QuotationImageUpload.init()
                },
                error: function () {

                }
            });
        }
    });
});