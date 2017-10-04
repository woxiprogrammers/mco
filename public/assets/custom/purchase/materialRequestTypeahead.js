$(document).ready(function(){
    $("#myBtn").click(function(){
        $("#myModal").modal();
    });
    $("#assetBtn").click(function(){
        $("#myModal1").modal();
    });

    var quotationId = $("#quotation_id").val();
    var url = "/bill/product/get-descriptions/"+quotationId+"/%QUERY";
    var citiList = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('office_name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: url,
            filter: function(x) {
                if($(window).width()<420){
                    $("#header").addClass("fixed");
                }
                return $.map(x, function (data) {
                    return {
                        id:data.id,
                        description:data.description
                    };
                });
            },
            wildcard: "%QUERY"
        }
    });
});