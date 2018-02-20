$(document).ready(function(){
    $("#subcontractorId").change(function(){
        var subContractorId = $(this).val();
        if(typeof subContractorId == 'undefined' || subContractorId == ''){
            $("#categoryTable tbody").html('');
            $("#categoryTable").hide();
        }else {
            $.ajax({
                url: '/dpr/subcontractor/get-category',
                type: 'POST',
                data:{
                    _token: $("input[name='_token']").val(),
                    subcontractor_id: subContractorId
                },
                success: function (data,textStatus, xhr) {
                    $("#categoryTable tbody").html(data);
                    $("#categoryTable").show();
                },
                error: function () {

                }
            });
        }
    });
});